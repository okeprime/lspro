<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengajuan;
use App\Support\LsproType5Workflow;
use App\Helpers\NotificationHelper;
use PhpOffice\PhpWord\TemplateProcessor;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    /**
     * Tampilkan Dashboard Admin/TU
     */
    public function index()
    {
        $user    = auth()->user();
        $subRole = strtolower($user->sub_role ?? '');
        $role    = strtolower($user->role ?? '');

        // Stats dasar untuk semua role
        $stats = [
            'total_masuk'        => \App\Models\Pengajuan::where('status', '!=', 'draft')->count(),
            'perlu_dicek'        => \App\Models\Pengajuan::whereIn('status', ['diajukan', 'verifikasi_tu'])->count(),
            'perbaikan'          => \App\Models\Pengajuan::where('status', 'perbaikan')->count(),
            'selesai'            => \App\Models\Pengajuan::whereIn('status', ['selesai', 'sppt_sni'])->count(),
            'sertifikat_terbit'  => \App\Models\Pengajuan::whereIn('status', ['selesai', 'sppt_sni'])->count(),
        ];

        // Stats khusus TU
        if ($subRole === 'tatausaha' || $role === 'superadmin') {
            $stats['baru_hari_ini'] = \App\Models\Pengajuan::whereDate('created_at', today())->where('status', '!=', 'draft')->count();
            $stats['belum_diverifikasi'] = \App\Models\Pengajuan::where('status', 'diajukan')->count();
        }

        // Stats khusus Keuangan/Layanan
        if ($subRole === 'layanan' || $role === 'superadmin') {
            $stats['invoice_pending']   = \App\Models\Invoice::where('status', 'pending_verification')->count();
            $stats['invoice_lunas']     = \App\Models\Invoice::where('status', 'paid')->count();
            $stats['banding_open']      = \App\Models\Banding::whereNotIn('status', ['selesai', 'ditolak'])->count();
            $stats['survailen_aktif']   = \App\Models\SurveilanSchedule::whereIn('status', ['scheduled', 'in_progress'])->count();
        }

        // Stats khusus Audit
        if ($subRole === 'audit' || $role === 'superadmin') {
            $stats['perlu_audit']       = \App\Models\Pengajuan::whereIn('status', ['proses_evaluasi', 'proses_audit'])->count();
            $stats['menunggu_keputusan']= \App\Models\Pengajuan::where('status', 'keputusan')->count();
        }

        // Pengajuan untuk tampilan tabel
        $pengajuans = \App\Models\Pengajuan::with(['user', 'statusHistories.actor', 'invoice'])
            ->where('status', '!=', 'draft')
            ->latest()
            ->limit(20)
            ->get();

        return view('admin.dashboard', compact('stats', 'pengajuans'));
    }

    /**
     * Tampilkan halaman Form Ceklis Kelengkapan Dokumen (7.2-4)
     */
    public function cekKelengkapan($id)
    {
        $pengajuan = Pengajuan::with('user')->find($id);

        // FALLBACK DUMMY DATA JIKA DATABASE KOSONG
        if (!$pengajuan) {
            $pengajuan = (object) [
                'id' => $id,
                'user' => (object) ['name' => 'PT Simulasi Agro Nusantara', 'email' => 'info@simulasi.com'],
                'status' => 'diajukan',
                'data_form' => json_encode([
                    'nama_perusahaan' => 'PT Simulasi Agro Nusantara',
                    'alamat_pabrik' => 'Kawasan Industri Dummy, Kav 45',
                    'jenis_sertifikasi' => 'Sertifikasi Baru SNI',
                    'nama_produk' => 'Pupuk Organik Padat',
                    'merek_dagang' => 'AGRO DUMMY SUPER'
                ]),
                'ceklis_dokumen' => null,
                'catatan' => ''
            ];
        }

        $formData = is_string($pengajuan->data_form) ? json_decode($pengajuan->data_form, true) : $pengajuan->data_form;
        $formData = $formData ?? [];

        return view('admin.ceklis', compact('pengajuan', 'formData'));
    }

    /**
     * Proses Simpan Hasil Evaluasi dari halaman Ceklis 7.2-4
     */
    public function prosesCeklis(Request $request, $id)
    {
        $request->validate([
            'kesimpulan' => 'required|in:evaluasi_724_audit,menunggu_ttd,perbaikan'
        ]);

        $pengajuan = Pengajuan::findOrFail($id);

        $pengajuan->catatan = $request->catatan;
        $pengajuan->nama_tu = $request->nama_tu;

        if ($request->has('ceklis')) {
            $pengajuan->ceklis_dokumen = json_encode($request->ceklis);
        }

        $pengajuan->save();

        $pengajuan->save();

        $isTU = str_contains(strtolower(auth()->user()->sub_role ?? ''), 'tatausaha') || strtolower(auth()->user()->role) === 'admin';
        $isAudit = str_contains(strtolower(auth()->user()->sub_role ?? ''), 'audit') || strtolower(auth()->user()->role) === 'admin';

        $statusNormalized = LsproType5Workflow::normalize($pengajuan->status);

        if ($request->kesimpulan === 'perbaikan') {
            if ($statusNormalized !== 'perbaikan') {
                $pengajuan->transitionTo(
                    'perbaikan',
                    $request->catatan ?: 'Dokumen ditandai perlu perbaikan oleh ' . ($isAudit ? 'Tim Audit.' : 'Tata Usaha.'),
                    auth()->id()
                );
                NotificationHelper::sendToUser($pengajuan->user_id, 'Perlu Perbaikan', 'Pengajuan #' . $pengajuan->id . ' perlu perbaikan dokumen. Cek catatan evaluasi.', 'warning', $pengajuan->id);
            }
            return redirect()->route('admin.dashboard')->with('success', 'Dokumen ditandai perlu perbaikan.');
        }

        // Jika ACC
        if ($statusNormalized === 'evaluasi_724_tu') {
            // TU Submit -> Lanjut ke Audit
            $pengajuan->transitionTo(
                'evaluasi_724_audit',
                $request->catatan ?: 'Evaluasi kelengkapan oleh TU selesai. Menunggu kebenaran oleh Tim Audit.',
                auth()->id()
            );
            return redirect()->route('admin.dashboard')->with('success', 'Evaluasi kelengkapan TU selesai. Berkas diteruskan ke Tim Audit.');
        } elseif ($statusNormalized === 'evaluasi_724_audit') {
            // Audit Submit -> Generate form dan Lanjut ke Menunggu TTD
            $pengajuan->refresh();
            $form723Generated = $this->generateForm723($pengajuan);
            
            $pengajuan->transitionTo(
                'menunggu_ttd',
                $request->catatan ?: 'Evaluasi Form 7.2-4 selesai. Klien dapat mengunduh dan menandatangani berkas.',
                auth()->id()
            );

            NotificationHelper::sendToUser($pengajuan->user_id, 'Evaluasi Selesai', 'Evaluasi dokumen Form 7.2-4 selesai. Silakan unduh dan unggah dokumen TTD.', 'success', $pengajuan->id);

            return redirect()->route('admin.dashboard')->with(
                'success',
                $form723Generated 
                    ? 'Audit kebenaran selesai. Form 7.2-1 & 7.2-4 siap diunduh klien.'
                    : 'Audit kebenaran selesai. (Template Form 7.2-3 belum ada).'
            );
        }

        return redirect()->route('admin.dashboard')->with('success', 'Perubahan disimpan.');
    }

    public function terimaPengecekanAwal(Request $request, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        
        if ($request->kesimpulan === 'perbaikan') {
            if ($pengajuan->status !== 'perbaikan') {
                $pengajuan->transitionTo(
                    'perbaikan',
                    $request->catatan ?: 'Formulir ditandai perlu perbaikan oleh Tata Usaha.',
                    auth()->id()
                );
            }
            NotificationHelper::sendToUser($pengajuan->user_id, 'Perlu Perbaikan', 'Pengajuan #' . $pengajuan->id . ' perlu perbaikan form. Cek catatan Tata Usaha.', 'warning', $pengajuan->id);
            return redirect()->route('admin.dashboard')->with('success', 'Formulir ditandai perlu perbaikan.');
        }

        // ACC Pengecekan Awal
        if ($pengajuan->status !== 'menunggu_lampiran') {
            $pengajuan->transitionTo(
                'menunggu_lampiran',
                'Pengecekan awal selesai. Menunggu klien mengunggah dokumen kelengkapan.',
                auth()->id()
            );
        }
        NotificationHelper::sendToUser($pengajuan->user_id, 'Pengecekan Awal Selesai', 'Pengecekan awal oleh TU selesai. Silakan unggah dokumen kelengkapan (SIUP, TDI, dll).', 'success', $pengajuan->id);

        return redirect()->route('admin.dashboard')->with('success', 'Pengecekan awal selesai. Klien diminta mengunggah dokumen kelengkapan.');
    }

    /**
     * Auto-Generate & Download File .docx Form 7.2-4
     */
    public function downloadForm724($id)
    {
        $pengajuan = Pengajuan::with('user')->findOrFail($id);
        $ceklis = json_decode($pengajuan->ceklis_dokumen, true) ?? [];

        $templatePath = storage_path('app/templates/Form 7.2-4_LS Pro_kelengkapan.docx');

        if (!file_exists($templatePath)) {
            abort(404, 'File template Form 7.2-4_LS Pro_kelengkapan.docx tidak ditemukan.');
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        $formData = $pengajuan->data_form;

        if (is_string($formData)) {
            $formData = json_decode($formData, true) ?? [];
        }

        $namaPemohon = $formData['nama_klien']
            ?? $formData['nama_pemohon']
            ?? $pengajuan->user->name
            ?? 'Klien';

        $nomorPermohonan = str_pad($pengajuan->id, 5, '0', STR_PAD_LEFT);

        $alamat = $formData['alamat']
            ?? $formData['alamat_perusahaan']
            ?? $pengajuan->user->alamat
            ?? 'Bogor';

        $jenisPupuk = $formData['nama_produk']
            ?? $formData['jenis_pupuk']
            ?? $formData['jenis_produk']
            ?? '-';

        $merek = $formData['merek']
            ?? $formData['merek_dagang']
            ?? '-';

        $noSni = $formData['nomor_sni']
            ?? $formData['no_sni']
            ?? $formData['judul_sni']
            ?? '-';

        $templateProcessor->setValue('nama_pemohon', $namaPemohon);
        $templateProcessor->setValue('nomor_permohonan', $nomorPermohonan);
        $templateProcessor->setValue('alamat_pemohon', $alamat);
        $templateProcessor->setValue('jenis_pupuk', $jenisPupuk);
        $templateProcessor->setValue('merek', $merek);
        $templateProcessor->setValue('no_sni', $noSni);

        for ($i = 0; $i < 16; $i++) {

            $evaluasi = $ceklis[$i]['evaluasi'] ?? '';
            $kebenaran = $ceklis[$i]['kebenaran'] ?? '';
            $keterangan = $ceklis[$i]['keterangan'] ?? '';

            $templateProcessor->setValue('l_'.$i, $evaluasi == 'lengkap' ? 'V' : '');
            $templateProcessor->setValue('tl_'.$i, $evaluasi == 'tidak' ? 'V' : '');
            $templateProcessor->setValue('b_'.$i, $kebenaran == 'benar' ? 'V' : '');
            $templateProcessor->setValue('tb_'.$i, $kebenaran == 'tidak' ? 'V' : '');
            $templateProcessor->setValue('ket_'.$i, $keterangan ?? '');
        }

        if (LsproType5Workflow::normalize($pengajuan->status) == 'perjanjian') {
            $templateProcessor->setValue('kesimpulan_teks', 'Lengkap');
        } elseif ($pengajuan->status == 'perbaikan') {
            $templateProcessor->setValue('kesimpulan_teks', 'Tidak Lengkap / Perbaikan');
        } else {
            $templateProcessor->setValue('kesimpulan_teks', 'Belum Dievaluasi');
        }

        Carbon::setLocale('id');

        $templateProcessor->setValue(
            'tanggal',
            Carbon::parse($pengajuan->updated_at)->translatedFormat('d F Y')
        );

        $templateProcessor->setValue(
            'nama_tu',
            $pengajuan->nama_tu ?? 'Petugas Tata Usaha'
        );

        $cleanName = preg_replace('/[^A-Za-z0-9\-]/', '_', $namaPemohon);

        $fileName = 'Form_7.2-4_Evaluasi_' . $cleanName . '.docx';

        $tempDirectory = storage_path('app/temp');

        if (!file_exists($tempDirectory)) {
            mkdir($tempDirectory, 0755, true);
        }

        $tempPath = $tempDirectory . DIRECTORY_SEPARATOR . $fileName;

        $templateProcessor->saveAs($tempPath);

        return response()
            ->download($tempPath)
            ->deleteFileAfterSend(true);
    }

    /**
     * Penerusan Berkas
     */
    public function teruskan(Request $request, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $allowedStatuses = array_column($pengajuan->workflowNextStatuses(), 'value');

        $request->validate([
            'status' => ['required', Rule::in($allowedStatuses)],
            'catatan_status' => ['nullable', 'string', 'max:1000'],
            'nominal_invoice' => [
                Rule::requiredIf($request->status === 'billing'),
                'nullable',
                'numeric',
                'min:0',
            ],
        ]);

        if ($request->status === 'billing') {
            $sequence = $pengajuan->invoices()->count() + 1;

            $pengajuan->invoices()->create([
                'invoice_number' => sprintf(
                    'INV/LSPRO/%s/%05d/%02d',
                    now()->format('Y'),
                    $pengajuan->id,
                    $sequence
                ),
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->addDays(14)->toDateString(),
                'amount_total' => $request->nominal_invoice,
                'amount_paid' => 0,
                'status' => 'unpaid',
                'notes' => 'Invoice tahap Billing Alur Sertifikasi LSPro.',
            ]);
        }

        if ($request->status === 'proses_evaluasi') {
            $invoice = $pengajuan->latestInvoice();

            if ($invoice) {
                $invoice->update([
                    'amount_paid' => $invoice->amount_total,
                    'payment_date' => now(),
                    'status' => 'paid',
                ]);
            }
        }

        $pengajuan->transitionTo(
            $request->status,
            $request->catatan_status,
            auth()->id()
        );

        // Triggers
        if ($request->status === 'billing') {
            NotificationHelper::sendToUser($pengajuan->user_id, 'Invoice Diterbitkan', 'Invoice baru telah diterbitkan untuk pengajuan #' . $pengajuan->id, 'info', $pengajuan->id);
        } elseif ($request->status === 'proses_evaluasi') {
            NotificationHelper::sendToUser($pengajuan->user_id, 'Pembayaran Diverifikasi', 'Pembayaran pengajuan #' . $pengajuan->id . ' telah diverifikasi. Lanjut ke proses evaluasi.', 'success', $pengajuan->id);
        } elseif ($request->status === 'proses_audit') {
            NotificationHelper::sendToRole('audit', 'Dokumen Baru', 'Dokumen pengajuan #' . $pengajuan->id . ' diteruskan oleh Tata Usaha untuk diaudit.', 'info', $pengajuan->id);
        }

        return back()->with(
            'success',
            'Tahap sertifikasi berhasil diperbarui sesuai alur Tipe 5.'
        );
    }

    private function generateForm723(Pengajuan $pengajuan): bool
    {
        $templateCandidates = [
            storage_path('app/templates/Form_7.2-3_Perjanjian.docx'),
            storage_path('app/templates/Form 7.2-3_Perjanjian Sertifikasi.docx'),
            storage_path('app/templates/Form_7.2-3.docx'),
        ];

        $templatePath = collect($templateCandidates)->first(fn ($path) => file_exists($path));

        if (!$templatePath) {
            return false;
        }

        $formData = is_array($pengajuan->data_form)
            ? $pengajuan->data_form
            : (json_decode($pengajuan->data_form, true) ?? []);

        $templateProcessor = new TemplateProcessor($templatePath);

        foreach ($formData as $key => $value) {
            if (is_scalar($value)) {
                $templateProcessor->setValue($key, (string) $value);
            }
        }

        $templateProcessor->setValue('nomor_permohonan', str_pad($pengajuan->id, 5, '0', STR_PAD_LEFT));
        $templateProcessor->setValue('tanggal_perjanjian', now()->translatedFormat('d F Y'));
        $templateProcessor->setValue('nama_pemohon', $formData['nama_pemohon'] ?? $pengajuan->user->name ?? '-');
        $templateProcessor->setValue('nama_perusahaan', $formData['nama_perusahaan'] ?? '-');

        $folder = storage_path('app/public/perjanjian/' . $pengajuan->id);
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $fileName = 'Form_7.2-3_Perjanjian_' . $pengajuan->id . '.docx';
        $savePath = $folder . DIRECTORY_SEPARATOR . $fileName;
        $templateProcessor->saveAs($savePath);

        $formData['file_perjanjian'] = $pengajuan->id . '/' . $fileName;
        $pengajuan->data_form = $formData;
        $pengajuan->save();

        return true;
    }

    public function surveilanIndex()
    {
        $schedules = \App\Models\SurveilanSchedule::with(['pengajuan.user', 'user'])->latest()->get();
        $users = \App\Models\User::where('role', 'client')->get();
        $pengajuans = Pengajuan::with('user')->whereNotIn('status', ['draft'])->latest()->get();
        
        return view('admin.survailen', compact('schedules', 'users', 'pengajuans'));
    }

    public function surveilanStore(Request $request)
    {
        $request->validate([
            'pengajuan_id' => 'required|exists:pengajuans,id',
            'survailen_year' => 'required|integer|min:2020|max:2099',
            'reminder_month' => 'required|integer|min:1|max:12',
            'deadline' => 'nullable|date',
            'catatan' => 'nullable|string',
        ]);

        $pengajuan = Pengajuan::findOrFail($request->pengajuan_id);

        \App\Models\SurveilanSchedule::create([
            'pengajuan_id' => $request->pengajuan_id,
            'user_id' => $pengajuan->user_id,
            'survailen_year' => $request->survailen_year,
            'reminder_month' => $request->reminder_month,
            'deadline' => $request->deadline,
            'catatan' => $request->catatan,
            'status' => 'scheduled',
            'status_dokumen' => 'menunggu',
        ]);

        return redirect()->route('admin.survailen.index')->with('success', 'Jadwal survailen baru berhasil ditambahkan.');
    }

    public function surveilanKirimNotif(Request $request, $id)
    {
        $request->validate([
            'email_klien' => 'required|email',
            'pesan' => 'required|string'
        ]);

        $schedule = \App\Models\SurveilanSchedule::findOrFail($id);
        $schedule->update([
            'reminder_sent' => true,
            'last_reminder_date' => now(),
        ]);

        NotificationHelper::sendToUser($schedule->user_id, 'Jadwal Survailen Dikirim', $request->pesan, 'survailen', $schedule->pengajuan_id);

        // (Opsional) Mengirim email riil jika SMTP dikonfigurasi:
        // \Mail::raw($request->pesan, function($message) use ($request) {
        //     $message->to($request->email_klien)->subject('Pemberitahuan Survailen Tahunan LSPro');
        // });

        return redirect()->route('admin.survailen.index')->with('success', 'Notifikasi survailen berhasil dikirim ke ' . $request->email_klien . '.');
    }

    public function surveilanProsesDokumen(Request $request, $id)
    {
        $request->validate([
            'status_dokumen' => 'required|in:menunggu,diterima,selesai',
            'catatan' => 'nullable|string',
        ]);

        $schedule = \App\Models\SurveilanSchedule::findOrFail($id);
        $schedule->update([
            'status_dokumen' => $request->status_dokumen,
            'catatan' => $request->catatan ?? $schedule->catatan,
            'status' => $request->status_dokumen === 'selesai' ? 'completed' : 'in_progress',
        ]);

        return redirect()->route('admin.survailen.index')->with('success', 'Verifikasi dokumen survailen berhasil diperbarui.');
    }

    // ==========================================
    // MODUL BARU: PANEL TATA USAHA
    // ==========================================
    public function panelTU()
    {
        // Tandai semua pengajuan yang berstatus diajukan sebagai sudah dibaca oleh TU
        Pengajuan::where('status', 'diajukan')->update(['is_read_tu' => true]);

        // Menampilkan antrean pengajuan yang perlu diproses TU
        $pengajuans = Pengajuan::with('user')
            ->whereIn('status', ['diajukan', 'perbaikan', 'menunggu_lampiran', 'evaluasi_724_tu'])
            ->latest()
            ->get();
        return view('admin.panel_tu', compact('pengajuans'));
    }

    // ==========================================
    // MODUL BARU: PANEL KEUANGAN
    // ==========================================
    public function panelKeuangan()
    {
        // Menampilkan antrean invoice yang pending
        $invoices = \App\Models\Invoice::with(['pengajuan.user'])
            ->whereIn('status', ['pending_verification', 'unpaid', 'paid'])
            ->orderByRaw("FIELD(status, 'pending_verification', 'unpaid', 'paid')")
            ->latest()
            ->get();
            
        return view('admin.panel_keuangan', compact('invoices'));
    }

    public function verifyPayment(Request $request, $id)
    {
        $invoice = \App\Models\Invoice::findOrFail($id);
        
        if ($request->action === 'terima') {
            $invoice->status = 'paid';
            $invoice->notes = 'Pembayaran telah diverifikasi oleh Keuangan.';
            
            // Lanjutkan workflow pengajuan
            $pengajuan = $invoice->pengajuan;
            if ($pengajuan && $pengajuan->status === 'billing') {
                $pengajuan->status = 'proses_evaluasi'; // Atau status selanjutnya sesuai SOP
                $pengajuan->save();
                
                \App\Helpers\NotificationHelper::sendToUser($pengajuan->user_id, 'Pembayaran Diterima', 'Pembayaran untuk invoice #' . $invoice->invoice_number . ' telah diverifikasi. Pengajuan dilanjutkan.', 'success', $pengajuan->id);
            }
            
            $msg = 'Pembayaran berhasil diverifikasi (Lunas).';
        } else {
            $invoice->status = 'unpaid';
            $invoice->notes = 'Bukti pembayaran ditolak: ' . $request->catatan;
            
            \App\Helpers\NotificationHelper::sendToUser($invoice->pengajuan->user_id, 'Pembayaran Ditolak', 'Bukti pembayaran untuk invoice #' . $invoice->invoice_number . ' ditolak. Alasan: ' . $request->catatan, 'danger', $invoice->pengajuan_id);
            
            $msg = 'Bukti pembayaran ditolak dan dikembalikan ke klien.';
        }
        
        $invoice->save();
        
        return redirect()->back()->with('success', $msg);
    }

    // ==========================================
    // MODUL BARU: CUSTOMER SERVICE
    // ==========================================
    public function customerService()
    {
        // Menampilkan daftar user client untuk di-chat, diurutkan berdasarkan chat terakhir
        $clients = \App\Models\User::where('role', 'client')->get();
        
        $clients = $clients->sortByDesc(function ($client) {
            $latestChat = \App\Models\InternalChat::where('group_name', 'cs_client_' . $client->id)
                ->orderBy('created_at', 'desc')
                ->first();
            return $latestChat ? $latestChat->created_at : $client->created_at;
        })->values();
        
        // FALLBACK DUMMY DATA
        if ($clients->isEmpty()) {
            $clients = collect([
                (object)['id' => 101, 'name' => 'PT Semesta Agro Tbk', 'email' => 'contact@semesta.co.id', 'nama_perusahaan' => 'PT Semesta Agro Tbk', 'created_at' => now()],
                (object)['id' => 102, 'name' => 'CV Bumi Indah Sejahtera', 'email' => 'admin@bumiindah.com', 'nama_perusahaan' => 'CV Bumi Indah Sejahtera', 'created_at' => now()],
                (object)['id' => 103, 'name' => 'Bapak Budi (Petani Mandiri)', 'email' => 'budi.tani@gmail.com', 'nama_perusahaan' => 'Kelompok Tani Harapan', 'created_at' => now()]
            ]);
        }
        
        return view('admin.customer_service', compact('clients'));
    }

    // ==========================================
    // MODUL BARU: AUDIT 7.2-4
    // ==========================================
    public function panelAudit724()
    {
        $pengajuans = Pengajuan::with('user')
            ->where('status', 'evaluasi_724_audit')
            ->latest()
            ->get();
            
        return view('admin.audit_724', compact('pengajuans'));
    }

    // ==========================================
    // MODUL BARU: DATA SAMPEL (DUMMY)
    // ==========================================
    public function dataSampel()
    {
        return view('admin.data_sampel');
    }

    // ==========================================
    // MODUL BARU: PENYERAHAN SERTIFIKAT
    // ==========================================
    public function penyerahanSertifikat()
    {
        // Menampilkan pengajuan yang sudah berada di tahap akhir/selesai
        $pengajuans = Pengajuan::with('user')
            ->whereIn('status', ['keputusan', 'selesai'])
            ->latest()
            ->get();
            
        // FALLBACK DUMMY DATA
        if ($pengajuans->isEmpty()) {
            $pengajuans = collect([
                (object)[
                    'id' => 881,
                    'user' => (object)['name' => 'PT Semesta Agro Tbk', 'email' => 'contact@semesta.co.id'],
                    'data_form' => json_encode(['jenis_sertifikasi' => 'Sertifikasi Baru SNI Pupuk Urea']),
                    'updated_at' => now()->subDays(2),
                ],
                (object)[
                    'id' => 882,
                    'user' => (object)['name' => 'CV Bumi Indah Sejahtera', 'email' => 'admin@bumiindah.com'],
                    'data_form' => json_encode(['jenis_sertifikasi' => 'Resertifikasi SNI NPK']),
                    'updated_at' => now()->subDays(1),
                ]
            ]);
        }

        return view('admin.penyerahan_sertifikat', compact('pengajuans'));
    }

    public function kirimSertifikat(Request $request, $id)
    {
        $request->validate(['pesan' => 'required|string']);
        
        $pengajuan = Pengajuan::findOrFail($id);
        
        NotificationHelper::sendToUser($pengajuan->user_id, 'Sertifikat Diterbitkan', $request->pesan, 'sertifikasi_selesai', $pengajuan->id);

        // Opsional: Integrasi SMTP Email
        // \Mail::raw($request->pesan, function($message) use ($pengajuan) {
        //     $message->to($pengajuan->user->email)
        //             ->subject('Sertifikat SPPT SNI Diterbitkan - LSPro');
        // });

        $pengajuan->update(['status' => 'selesai']);

        return redirect()->route('admin.penyerahan_sertifikat')->with('success', 'Notifikasi sertifikat telah dikirim ke ' . $pengajuan->user->email);
    }

    // ==========================================
    // MODUL BARU: AUDIT KESESUAIAN BERKAS
    // ==========================================
    public function auditBerkas()
    {
        $pengajuans = Pengajuan::with('user')
            ->where('status', 'proses_audit')
            ->latest()
            ->get();
            
        return view('admin.audit_berkas', compact('pengajuans'));
    }

    public function prosesAuditBerkas(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:keputusan,perbaikan',
            'catatan_status' => 'required|string',
        ]);
        
        $pengajuan = Pengajuan::findOrFail($id);
        
        $pengajuan->transitionTo(
            $request->status,
            $request->catatan_status,
            auth()->id()
        );

        if ($request->status === 'perbaikan') {
            NotificationHelper::sendToRole('tatausaha', 'Audit Dikembalikan', 'Tim Audit mengembalikan dokumen pengajuan #' . $pengajuan->id . ' untuk perbaikan.', 'warning', $pengajuan->id);
            NotificationHelper::sendToUser($pengajuan->user_id, 'Audit Selesai (Perlu Perbaikan)', 'Pemeriksaan audit telah selesai dengan catatan perbaikan.', 'warning', $pengajuan->id);
        } elseif ($request->status === 'keputusan') {
            NotificationHelper::sendToRole('tatausaha', 'Sertifikat Siap Diterbitkan', 'Tim Audit menyetujui dokumen pengajuan #' . $pengajuan->id . '. Menunggu keputusan akhir.', 'success', $pengajuan->id);
            NotificationHelper::sendToRole('layanan', 'Sertifikat Siap Dikirim', 'Pengajuan #' . $pengajuan->id . ' telah mencapai tahap keputusan.', 'info', $pengajuan->id);
            NotificationHelper::sendToUser($pengajuan->user_id, 'Audit Selesai', 'Pemeriksaan audit pengajuan #' . $pengajuan->id . ' telah selesai dan disetujui.', 'success', $pengajuan->id);
        }

        return redirect()->route('admin.audit_berkas')->with('success', 'Hasil audit kesesuaian berkas berhasil disimpan.');
    }

    public function terimaAwal(Request $request, $id)
    {
        $request->validate([
            'kesimpulan' => 'required|in:menunggu_lampiran,perbaikan',
            'catatan' => 'nullable|string'
        ]);

        $pengajuan = Pengajuan::findOrFail($id);
        
        $pengajuan->transitionTo(
            $request->kesimpulan,
            $request->catatan ?? 'Pengecekan awal telah dilakukan.',
            auth()->id()
        );

        $pengajuan->catatan_admin = $request->catatan;
        $pengajuan->save();

        if ($request->kesimpulan === 'perbaikan') {
            NotificationHelper::sendToUser($pengajuan->user_id, 'Pengecekan Awal: Revisi Diperlukan', 'Pengajuan Anda ditolak/direvisi dengan catatan: ' . $request->catatan, 'warning', $pengajuan->id);
        } else {
            NotificationHelper::sendToUser($pengajuan->user_id, 'Pengecekan Awal Selesai', 'Pengecekan awal selesai. Silakan unggah dokumen kelengkapan.', 'success', $pengajuan->id);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Pengecekan awal berhasil disimpan.');
    }
}
