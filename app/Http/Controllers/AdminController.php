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
     * Tampilkan Dashboard Admin/Administrasi
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

        // Stats khusus Administrasi
        if ($subRole === 'layanan' || $role === 'superadmin') {
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
            ->orderBy('updated_at', 'desc')
            ->limit(20)
            ->get();

        return view('admin.dashboard', compact('stats', 'pengajuans'));
    }

    /**
     * Tampilkan halaman Form Ceklis Kelengkapan Dokumen (7.2-4)
     */
    public function cekKelengkapan($id)
    {
        $pengajuan = Pengajuan::with('user')->findOrFail($id);

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
            'kesimpulan' => 'required|in:lengkap,perbaikan,menunggu_lhp,tindakan_perbaikan'
        ]);

        $pengajuan = Pengajuan::findOrFail($id);

        $isLayanan = str_contains(strtolower(auth()->user()->sub_role ?? ''), 'layanan') || strtolower(auth()->user()->role) === 'layanan';
        $statusNormalized = LsproType5Workflow::normalize($pengajuan->status);
        $isAuditPhase = in_array($statusNormalized, ['evaluasi_724_audit', 'proses_evaluasi', 'proses_audit', 'keputusan', 'selesai']);

        if ($isAuditPhase) {
            $formData = is_string($pengajuan->data_form) ? json_decode($pengajuan->data_form, true) : $pengajuan->data_form;
            $formData['catatan_audit'] = $request->catatan;
            $formData['nama_audit'] = $request->nama_tu;
            $pengajuan->data_form = json_encode($formData);
        } else {
            $pengajuan->catatan = $request->catatan;
            $pengajuan->nama_tu = $request->nama_tu;
        }

        if ($request->has('ceklis')) {
            $pengajuan->ceklis_dokumen = json_encode($request->ceklis);
        }

        $pengajuan->save();

        if ($request->kesimpulan === 'perbaikan') {
            if ($statusNormalized !== 'perbaikan') {
                $pengajuan->transitionTo(
                    'perbaikan',
                    $request->catatan ?: 'Dokumen ditandai perlu perbaikan oleh Administrasi/Layanan.',
                    auth()->id()
                );
                NotificationHelper::sendToUser($pengajuan->user_id, 'Perlu Perbaikan', 'Pengajuan #' . $pengajuan->id . ' perlu perbaikan dokumen. Cek catatan evaluasi.', 'warning', $pengajuan->id);
            }
            return redirect()->route('admin.dashboard')->with('success', 'Dokumen ditandai perlu perbaikan.');
        } elseif ($request->kesimpulan === 'tindakan_perbaikan') {
            $formData = is_array($pengajuan->data_form) ? $pengajuan->data_form : json_decode($pengajuan->data_form, true) ?? [];
            $formData['lks_iterasi'] = ($formData['lks_iterasi'] ?? 0) + 1;
            $formData['lks_deadline'] = now()->addMonth()->toDateString();
            $pengajuan->data_form = $formData;
            $pengajuan->save();

            $pengajuan->transitionTo(
                'tindakan_perbaikan',
                $request->catatan ?: 'Tim Audit menemukan ketidaksesuaian. Silakan unggah LKS sebelum ' . \Carbon\Carbon::parse($formData['lks_deadline'])->translatedFormat('d F Y') . '.',
                auth()->id()
            );
            NotificationHelper::sendToUser($pengajuan->user_id, 'Tindakan Perbaikan Diperlukan', 'Tim Audit menemukan ketidaksesuaian. Harap unggah Tindakan Perbaikan sebelum batas waktu.', 'warning', $pengajuan->id);
            return redirect()->route('admin.dashboard')->with('success', 'Hasil audit disimpan. Status dikembalikan ke Klien untuk Tindakan Perbaikan dengan deadline 1 bulan.');
        } elseif ($request->kesimpulan === 'menunggu_lhp') {
            $pengajuan->transitionTo(
                'menunggu_lhp',
                $request->catatan ?: 'Audit disetujui, menunggu Klien mengunggah Hasil Uji (LHP).',
                auth()->id()
            );
            NotificationHelper::sendToRole('layanan', 'Audit Lapangan Selesai', 'Audit lapangan #' . $pengajuan->id . ' disetujui. Sampel dikirim ke Lab.', 'info', $pengajuan->id);
            NotificationHelper::sendToUser($pengajuan->user_id, 'Audit Lapangan Selesai', 'Audit lapangan disetujui. Silakan unggah Laporan Hasil Uji (LHP) jika pengujian Lab sudah selesai.', 'success', $pengajuan->id);
            return redirect()->route('admin.dashboard')->with('success', 'Hasil audit disetujui. Lanjut menunggu LHP.');
        }

        // Jika ACC
        if ($statusNormalized === 'evaluasi_724_tu') {
            // Administrasi Submit -> Lanjut ke Menunggu TTD Klien
            $pengajuan->transitionTo(
                'menunggu_ttd',
                $request->catatan ?: 'Evaluasi kelengkapan oleh Administrasi selesai. Menunggu klien mengunggah TTD Perjanjian Sertifikasi.',
                auth()->id()
            );
            return redirect()->route('admin.dashboard')->with('success', 'Evaluasi kelengkapan Administrasi selesai. Menunggu klien mengunggah TTD.');
        } elseif ($statusNormalized === 'evaluasi_724_audit' || $statusNormalized === 'audit_kecukupan') {
            // Audit Submit -> Ceklis disetujui, tetap di status audit_kecukupan agar Admin bisa menjadwalkan
            $pengajuan->keterangan_admin = 'Evaluasi Form 7.2-4 selesai. Menunggu penjadwalan audit.';
            $pengajuan->save();

            \App\Helpers\NotificationHelper::sendToUser($pengajuan->user_id, 'Evaluasi Selesai', 'Evaluasi dokumen Form 7.2-4 selesai. Menunggu penentuan jadwal audit oleh tim terkait.', 'success', $pengajuan->id);

            return redirect()->route('admin.audit_berkas')->with(
                'success',
                'Audit kecukupan selesai. Pengajuan kini berada dalam antrean penjadwalan.'
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
                    $request->catatan ?: 'Formulir ditandai perlu perbaikan oleh Administrasi.',
                    auth()->id()
                );
            }
            NotificationHelper::sendToUser($pengajuan->user_id, 'Perlu Perbaikan', 'Pengajuan #' . $pengajuan->id . ' perlu perbaikan form. Cek catatan Administrasi.', 'warning', $pengajuan->id);
            return redirect()->route('admin.dashboard')->with('success', 'Formulir ditandai perlu perbaikan.');
        }

        // ACC Pengecekan Awal -> Terbitkan Billing 1
        $pengajuan->nama_tu = auth()->user()->name ?? 'Administrasi';
        $pengajuan->save();
        
        $pengajuan->transitionTo(
            'billing_1',
            'Pengecekan awal selesai. Menunggu Keuangan menerbitkan RAB & Billing 1.',
            auth()->id()
        );

        NotificationHelper::sendToUser(
            $pengajuan->user_id, 
            'Pengecekan Awal Selesai', 
            'Pengecekan awal selesai. Menunggu penerbitan Billing 1 oleh Keuangan LSPro.', 
            'info', 
            $pengajuan->id
        );

        return redirect()->route('admin.dashboard')->with('success', 'Pengecekan awal selesai. Silakan arahkan Keuangan untuk menerbitkan RAB & Billing 1.');
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
            $pengajuan->nama_tu ?? 'Petugas Administrasi'
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
        ]);

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
        if (in_array($request->status, ['billing_1', 'billing_2', 'billing_3', 'billing_4'])) {
            NotificationHelper::sendToUser($pengajuan->user_id, 'Invoice Diterbitkan', 'Invoice baru (' . strtoupper(str_replace('_', ' ', $request->status)) . ') telah diterbitkan untuk pengajuan #' . $pengajuan->id, 'info', $pengajuan->id);
        } elseif ($request->status === 'proses_evaluasi') {
            NotificationHelper::sendToUser($pengajuan->user_id, 'Pembayaran Diverifikasi', 'Pembayaran pengajuan #' . $pengajuan->id . ' telah diverifikasi. Lanjut ke proses evaluasi.', 'success', $pengajuan->id);
        } elseif ($request->status === 'proses_audit') {
            NotificationHelper::sendToRole('audit', 'Dokumen Baru', 'Dokumen pengajuan #' . $pengajuan->id . ' diteruskan oleh Administrasi untuk diaudit.', 'info', $pengajuan->id);
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
        $templateProcessor->setValue('hari_perjanjian', now()->translatedFormat('l'));
        $templateProcessor->setValue('tanggal_perjanjian', now()->translatedFormat('d'));
        $templateProcessor->setValue('bulan_perjanjian', now()->translatedFormat('F'));
        $templateProcessor->setValue('tahun_perjanjian', now()->translatedFormat('Y'));
        
        $templateProcessor->setValue('nama_tu', 'Anik Dwi Hastuti S.P.,M.M');
        $templateProcessor->setValue('nama_ketua_lspro', 'Anik Dwi Hastuti S.P.,M.M');
        $templateProcessor->setValue('nama_ketua', 'Anik Dwi Hastuti S.P.,M.M');
        $templateProcessor->setValue('jabatan_lspro', 'Ketua LSPro BRMP SDLP');
        $templateProcessor->setValue('nama_perusahaan', $formData['nama_perusahaan'] ?? $pengajuan->user->nama_perusahaan ?? '-');
        $templateProcessor->setValue('alamat_perusahaan', $formData['alamat_perusahaan'] ?? $formData['alamat_pabrik'] ?? $formData['alamat_kantor'] ?? '-');
        $templateProcessor->setValue('nama_pemohon', $formData['nama_pemohon'] ?? $pengajuan->user->name ?? '-');
        $templateProcessor->setValue('jabatan_pemohon', $formData['jabatan_pemohon'] ?? $formData['jabatan_penghubung'] ?? 'Pimpinan Perusahaan');
        
        $templateProcessor->setValue('nama_produk', $formData['nama_produk'] ?? $formData['jenis_pupuk'] ?? '-');
        $templateProcessor->setValue('no_sni', $formData['no_sni'] ?? $formData['nomor_sni'] ?? '-');
        $templateProcessor->setValue('judul_sni', $formData['judul_sni'] ?? '-');

        // TTD otomatis Pihak 1 (LSPro) & Pihak 2 (Pemohon)
        $templateProcessor->setValue('ttd_pihak_1', '[Telah Ditandatangani Secara Elektronik]');
        $templateProcessor->setValue('ttd_pihak_2', '[Telah Ditandatangani Secara Elektronik]');

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
        $pengajuans = Pengajuan::with('user')->where('status', 'selesai')->latest()->get();
        
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
    // MODUL BARU: PANEL Administrasi
    // ==========================================
    public function panelTU()
    {
        // Tandai pengajuan baru sebagai dibaca tanpa mengubah waktu updated_at
        Pengajuan::where('status', 'diajukan')
            ->where('is_read_tu', false)
            ->toBase()
            ->update(['is_read_tu' => true]);

        // Menampilkan antrean pengajuan yang perlu diproses Administrasi (Pengecekan Awal)
        // Hanya perbaikan awal (nama_tu IS NULL) yang masuk ke sini
        $pengajuans = Pengajuan::with('user')
            ->where('status', 'diajukan')
            ->orWhere(function($query) {
                $query->where('status', 'perbaikan')
                      ->whereNull('nama_tu');
            })
            ->orderBy('updated_at', 'desc')
            ->get();
        
        $title = "Pengajuan Masuk (Pengecekan Awal)";
        $subtitle = "Verifikasi identitas dan form awal (Form 7.2-1).";
        
        return view('admin.panel_tu', compact('pengajuans', 'title', 'subtitle'));
    }


    // ==========================================
    // MODUL BARU: PANEL KEUANGAN
    // ==========================================
    public function panelKeuangan()
    {
        // Menampilkan antrean invoice yang pending (Tagihan & Pembayaran)
        $invoices = \App\Models\Invoice::with(['pengajuan.user'])
            ->whereIn('status', ['pending_verification', 'unpaid', 'paid'])
            ->orderByRaw("FIELD(status, 'pending_verification', 'unpaid', 'paid')")
            ->latest()
            ->get();
            
        // Menampilkan antrean pengajuan untuk Perjanjian Sertifikasi
        $pengajuansPerjanjian = \App\Models\Pengajuan::with('user')
            ->whereIn('status', ['billing_1', 'billing_2', 'billing_3', 'billing_4'])
            ->latest()
            ->get();
            
        $allInvoices = \App\Models\Invoice::with(['pengajuan.user'])
            ->latest()
            ->get();
            
        return view('admin.panel_keuangan', compact('invoices', 'pengajuansPerjanjian', 'allInvoices'));
    }

    public function uploadBilling(Request $request, $id)
    {
        $request->validate([
            'file_invoice' => 'required|mimes:pdf,jpg,png,jpeg|max:5120',
        ]);

        $invoice = \App\Models\Invoice::findOrFail($id);

        if ($request->hasFile('file_invoice')) {
            $noPermohonan = str_pad($invoice->pengajuan_id, 5, '0', STR_PAD_LEFT);
            $filename = 'tagihan_billing_' . $invoice->id . '_permohonan_' . $noPermohonan . '.' . $request->file('file_invoice')->getClientOriginalExtension();
            $path = $request->file('file_invoice')->storeAs('invoices', $filename, 'public');
            $invoice->file_invoice = $path;
            $invoice->status = 'unpaid';
            $invoice->notes = 'Billing telah diterbitkan oleh Keuangan. Menunggu pembayaran klien.';
            $invoice->save();

            \App\Helpers\NotificationHelper::sendToUser($invoice->pengajuan->user_id, 'Billing Baru Diterbitkan', 'Tagihan / Billing baru (#' . $invoice->invoice_number . ') telah diterbitkan. Silakan cek menu Billing untuk melunasi pembayaran.', 'info', $invoice->pengajuan_id);

            return redirect()->back()->with('success', 'File Billing berhasil diunggah dan dikirim ke klien.');
        }

        return redirect()->back()->with('error', 'Gagal mengunggah File Billing.');
    }

    public function updateInvoice(Request $request, $id)
    {
        $invoice = \App\Models\Invoice::findOrFail($id);

        $request->validate([
            'invoice_number' => 'required|string|max:255',
            'amount_total' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'file_invoice' => 'nullable|mimes:pdf,jpg,png,jpeg|max:5120',
        ]);

        $invoice->invoice_number = $request->invoice_number;
        $invoice->amount_total = $request->amount_total;
        $invoice->due_date = $request->due_date;

        if ($request->hasFile('file_invoice')) {
            $noPermohonan = str_pad($invoice->pengajuan_id, 5, '0', STR_PAD_LEFT);
            $filename = 'tagihan_billing_' . $invoice->id . '_permohonan_' . $noPermohonan . '.' . $request->file('file_invoice')->getClientOriginalExtension();
            $path = $request->file('file_invoice')->storeAs('invoices', $filename, 'public');
            $invoice->file_invoice = $path;
        }

        $invoice->save();

        return redirect()->back()->with('success', 'Data Tagihan berhasil diperbarui.');
    }

    public function destroyInvoice($id)
    {
        $invoice = \App\Models\Invoice::findOrFail($id);
        
        // Hapus file fisik jika ada
        if ($invoice->file_invoice && \Illuminate\Support\Facades\Storage::disk('public')->exists($invoice->file_invoice)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($invoice->file_invoice);
        }
        
        $invoice->delete();

        return redirect()->back()->with('success', 'Tagihan berhasil dihapus dari sistem.');
    }

    public function uploadKwitansi(Request $request, $id)
    {
        $request->validate([
            'file_kwitansi' => 'required|mimes:pdf,jpg,png,jpeg|max:5120',
        ]);

        $invoice = \App\Models\Invoice::findOrFail($id);

        if ($request->hasFile('file_kwitansi')) {
            $noPermohonan = str_pad($invoice->pengajuan_id, 5, '0', STR_PAD_LEFT);
            $filename = 'kwitansi_pembayaran_billing_' . $invoice->id . '_permohonan_' . $noPermohonan . '.' . $request->file('file_kwitansi')->getClientOriginalExtension();
            $path = $request->file('file_kwitansi')->storeAs('kwitansi', $filename, 'public');
            $invoice->file_kwitansi = $path;
            
            // ACC pembayaran jika belum
            if ($invoice->status !== 'paid') {
                $invoice->status = 'paid';
                $invoice->notes = 'Pembayaran diverifikasi. Kwitansi telah diterbitkan.';
                
                // Lanjutkan workflow
                $pengajuan = $invoice->pengajuan;
                if ($pengajuan->status === 'billing_1') {
                    $pengajuan->transitionTo('perjanjian_lampiran', 'Pembayaran Billing 1 diverifikasi. Klien dapat mengisi Perjanjian & Lampiran.', auth()->id());
                } elseif ($pengajuan->status === 'billing_2') {
                    $pengajuan->transitionTo('audit_kecukupan', 'Pembayaran Billing 2 diverifikasi. Lanjut ke Audit Kecukupan Dokumen.', auth()->id());
                } elseif ($pengajuan->status === 'billing_3') {
                    $pengajuan->transitionTo('proses_audit', 'Pembayaran Billing 3 diverifikasi. Lanjut ke Pelaksanaan Audit Lapangan.', auth()->id());
                } elseif ($pengajuan->status === 'billing_4') {
                    $pengajuan->transitionTo('evaluasi', 'Pembayaran Billing 4 diverifikasi. Lanjut ke Sidang Komtek / Evaluasi Akhir.', auth()->id());
                }
            }
            
            $invoice->save();

            \App\Helpers\NotificationHelper::sendToUser($invoice->pengajuan->user_id, 'Kwitansi Diterbitkan', 'Kwitansi untuk pembayaran (#' . $invoice->invoice_number . ') telah diterbitkan oleh Keuangan.', 'success', $invoice->pengajuan_id);

            return redirect()->back()->with('success', 'Kwitansi berhasil diunggah dan Pembayaran di-ACC.');
        }

        return redirect()->back()->with('error', 'Gagal mengunggah Kwitansi.');
    }

    public function verifyPayment(Request $request, $id)
    {
        $invoice = \App\Models\Invoice::findOrFail($id);
        
        if ($request->action === 'terima') {
            $request->validate([
                'file_kwitansi' => 'required|mimes:pdf,jpg,png,jpeg|max:5120',
            ]);

            if ($request->hasFile('file_kwitansi')) {
                $noPermohonan = str_pad($invoice->pengajuan_id, 5, '0', STR_PAD_LEFT);
                $filename = 'kwitansi_pembayaran_billing_' . $invoice->id . '_permohonan_' . $noPermohonan . '.' . $request->file('file_kwitansi')->getClientOriginalExtension();
                $path = $request->file('file_kwitansi')->storeAs('kwitansi', $filename, 'public');
                $invoice->file_kwitansi = $path;
            }

            $invoice->status = 'paid';
            $invoice->notes = 'Pembayaran telah diverifikasi oleh Keuangan. Kwitansi telah diterbitkan.';
            
            // Lanjutkan workflow pengajuan
            $pengajuan = $invoice->pengajuan;
                if ($pengajuan->status === 'billing_1') {
                    $pengajuan->transitionTo('perjanjian_lampiran', 'Pembayaran Billing 1 diverifikasi. Klien dapat mengisi Perjanjian & Lampiran.', auth()->id());
                    \App\Helpers\NotificationHelper::sendToUser($pengajuan->user_id, 'Pembayaran Diterima', 'Pembayaran untuk invoice #' . $invoice->invoice_number . ' telah diverifikasi. Silakan isi dan unggah dokumen Perjanjian Sertifikasi & Lampiran.', 'success', $pengajuan->id);
                } elseif ($pengajuan->status === 'billing_2') {
                    $pengajuan->transitionTo('audit_kecukupan', 'Pembayaran Billing 2 diverifikasi. Lanjut ke Audit Kecukupan Dokumen.', auth()->id());
                    \App\Helpers\NotificationHelper::sendToUser($pengajuan->user_id, 'Pembayaran Diterima', 'Pembayaran untuk invoice #' . $invoice->invoice_number . ' telah diverifikasi. Tim bersiap untuk Audit Kecukupan.', 'success', $pengajuan->id);
                } elseif ($pengajuan->status === 'billing_3') {
                    $pengajuan->transitionTo('proses_audit', 'Pembayaran Billing 3 diverifikasi. Lanjut ke Pelaksanaan Audit Lapangan.', auth()->id());
                    \App\Helpers\NotificationHelper::sendToUser($pengajuan->user_id, 'Pembayaran Diterima', 'Pembayaran untuk invoice #' . $invoice->invoice_number . ' telah diverifikasi. Tim bersiap untuk Audit Lapangan.', 'success', $pengajuan->id);
                } elseif ($pengajuan->status === 'billing_4') {
                    $pengajuan->transitionTo('evaluasi', 'Pembayaran Billing 4 diverifikasi. Lanjut ke Sidang Komtek / Evaluasi Akhir.', auth()->id());
                    \App\Helpers\NotificationHelper::sendToUser($pengajuan->user_id, 'Pembayaran Diterima', 'Pembayaran untuk biaya evaluasi (invoice #' . $invoice->invoice_number . ') telah diverifikasi. Menunggu proses evaluasi.', 'success', $pengajuan->id);
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
    // MODUL BARU: DATA SAMPEL (DUMMY)
    // ==========================================
    public function dataSampel()
    {
        $pengajuans = Pengajuan::with('user')
            ->whereIn('status', ['proses_evaluasi', 'proses_audit', 'keputusan'])
            ->latest()
            ->get();
            
        return view('admin.data_sampel', compact('pengajuans'));
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
            
        return view('admin.penyerahan_sertifikat', compact('pengajuans'));
    }

    public function kirimSertifikat(Request $request, $id)
    {
        $request->validate([
            'pesan' => 'required|string',
            'file_sertifikat' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120'
        ]);
        
        $pengajuan = Pengajuan::findOrFail($id);
        
        if ($request->hasFile('file_sertifikat')) {
            $noPermohonan = str_pad($pengajuan->id, 5, '0', STR_PAD_LEFT);
            $filename = 'sertifikat_lspro_permohonan_' . $noPermohonan . '.' . $request->file('file_sertifikat')->getClientOriginalExtension();
            $path = $request->file('file_sertifikat')->storeAs('sertifikat', $filename, 'public');
            $pengajuan->file_sertifikat = $path; // Pastikan kolom ini ada atau simpan di data_form
        }

        NotificationHelper::sendToUser($pengajuan->user_id, 'Sertifikat Diterbitkan', $request->pesan, 'sertifikasi_selesai', $pengajuan->id);

        // Opsional: Integrasi SMTP Email
        // \Mail::raw($request->pesan, function($message) use ($pengajuan) {
        //     $message->to($pengajuan->user->email)
        //             ->subject('Sertifikat Sertifikat Kesesuaian SNI Diterbitkan - LSPro');
        // });

        $pengajuan->update(['status' => 'selesai']);

        return redirect()->route('admin.penyerahan_sertifikat')->with('success', 'Notifikasi sertifikat telah dikirim ke ' . $pengajuan->user->email);
    }

    // ==========================================
    // MODUL BARU: PENJADWALAN AUDIT (Administrasi)
    // ==========================================
    public function penjadwalanAudit()
    {
        $pengajuans = Pengajuan::with('user')
            ->where(function($q) {
                $q->where('status', 'audit_kecukupan')->whereNotNull('ceklis_dokumen')
                  ->orWhere('status', 'menunggu_persetujuan_jadwal');
            })
            ->latest()
            ->get();
            
        return view('admin.tu_penjadwalan', compact('pengajuans'));
    }

    // ==========================================
    // MODUL BARU: AUDIT KESESUAIAN BERKAS
    // ==========================================
    public function auditBerkas()
    {
        $pengajuans = Pengajuan::with('user')
            ->where(function($q) {
                $q->where('status', 'audit_kecukupan')->whereNull('ceklis_dokumen')
                  ->orWhere('status', 'proses_audit');
            })
            ->latest()
            ->get();
            
        return view('admin.audit_berkas', compact('pengajuans'));
    }

    public function prosesAuditBerkas(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|string',
            'catatan_status' => 'required|string',
        ]);
        
        $pengajuan = Pengajuan::findOrFail($id);
        
        $pengajuan->transitionTo(
            $request->status,
            $request->catatan_status,
            auth()->id()
        );

        if ($request->status === 'perbaikan' || $request->status === 'tindakan_perbaikan') {
            NotificationHelper::sendToRole('layanan', 'Audit Dikembalikan', 'Tim Audit mengembalikan dokumen pengajuan #' . $pengajuan->id . ' untuk perbaikan.', 'warning', $pengajuan->id);
            NotificationHelper::sendToUser($pengajuan->user_id, 'Audit Selesai (Perlu Perbaikan)', 'Pemeriksaan audit telah selesai dengan catatan perbaikan.', 'warning', $pengajuan->id);
        } elseif ($request->status === 'keputusan') {
            NotificationHelper::sendToRole('layanan', 'Sertifikat Siap Diterbitkan', 'Tim Audit menyetujui dokumen pengajuan #' . $pengajuan->id . '. Menunggu keputusan akhir.', 'success', $pengajuan->id);
            NotificationHelper::sendToRole('layanan', 'Sertifikat Siap Dikirim', 'Pengajuan #' . $pengajuan->id . ' telah mencapai tahap keputusan.', 'info', $pengajuan->id);
            NotificationHelper::sendToUser($pengajuan->user_id, 'Audit Selesai', 'Pemeriksaan audit pengajuan #' . $pengajuan->id . ' telah selesai dan disetujui.', 'success', $pengajuan->id);
        } elseif ($request->status === 'proses_audit') {
            NotificationHelper::sendToRole('layanan', 'Lanjut Audit Lapangan', 'Evaluasi dokumen #' . $pengajuan->id . ' disetujui. Lanjut ke proses Audit Lapangan.', 'info', $pengajuan->id);
            NotificationHelper::sendToUser($pengajuan->user_id, 'Evaluasi Selesai', 'Evaluasi dokumen pengajuan #' . $pengajuan->id . ' disetujui. Menunggu proses Audit Lapangan.', 'success', $pengajuan->id);
        } elseif ($request->status === 'proses_ppc') {
            NotificationHelper::sendToRole('layanan', 'Audit Lapangan Selesai', 'Audit lapangan #' . $pengajuan->id . ' disetujui. Menunggu Petugas PPC mengambil sampel.', 'info', $pengajuan->id);
            NotificationHelper::sendToUser($pengajuan->user_id, 'Audit Lapangan Selesai', 'Audit lapangan disetujui. Menunggu Petugas (PPC) untuk mengambil sampel uji.', 'success', $pengajuan->id);
        } elseif ($request->status === 'billing_3') {
            NotificationHelper::sendToRole('layanan', 'Pengambilan Sampel Selesai', 'PPC telah selesai untuk #' . $pengajuan->id . '. Silakan terbitkan Tagihan Uji Lab (Billing 3).', 'info', $pengajuan->id);
            NotificationHelper::sendToUser($pengajuan->user_id, 'Pengambilan Sampel Selesai', 'Sampel uji telah diambil. Menunggu tagihan Uji Lab.', 'success', $pengajuan->id);
        } elseif ($request->status === 'menunggu_lhp') {
            NotificationHelper::sendToRole('layanan', 'Menunggu Hasil Lab', 'Audit lapangan #' . $pengajuan->id . ' disetujui. Menunggu LHP.', 'info', $pengajuan->id);
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

        $pengajuan->keterangan_admin = $request->catatan;
        $pengajuan->save();

        if ($request->kesimpulan === 'perbaikan') {
            NotificationHelper::sendToUser($pengajuan->user_id, 'Pengecekan Awal: Revisi Diperlukan', 'Pengajuan Anda ditolak/direvisi dengan catatan: ' . $request->catatan, 'warning', $pengajuan->id);
        } else {
            NotificationHelper::sendToUser($pengajuan->user_id, 'Pengecekan Awal Selesai', 'Pengecekan awal selesai. Silakan unggah dokumen kelengkapan.', 'success', $pengajuan->id);
        }

        return redirect()->route('admin.dashboard')->with('success', 'Pengecekan awal berhasil disimpan.');
    }

    // ==========================================
    // MODUL BARU: JADWAL AUDIT & BILLING LAB
    // ==========================================
    public function setJadwalAudit(Request $request, $id)
    {
        $request->validate([
            'jadwal_audit' => 'required|date',
            'dokumen_jadwal' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $pengajuan = Pengajuan::findOrFail($id);
        $pengajuan->jadwal_audit = $request->jadwal_audit;
        
        if ($request->hasFile('dokumen_jadwal')) {
            $noPermohonan = str_pad($pengajuan->id, 5, '0', STR_PAD_LEFT);
            $filename = 'jadwal_audit_permohonan_' . $noPermohonan . '.' . $request->file('dokumen_jadwal')->getClientOriginalExtension();
            $path = $request->file('dokumen_jadwal')->storeAs('jadwal', $filename, 'public');
            $formData = is_array($pengajuan->data_form) ? $pengajuan->data_form : (json_decode($pengajuan->data_form, true) ?? []);
            $formData['dokumen_jadwal'] = $path;
            $pengajuan->data_form = $formData;
        }

        $pengajuan->is_jadwal_disetujui = null;
        $pengajuan->save();

        if ($pengajuan->status !== 'menunggu_persetujuan_jadwal') {
            $pengajuan->transitionTo(
                'menunggu_persetujuan_jadwal',
                'Jadwal Audit dan Pengambilan Contoh telah ditentukan. Menunggu persetujuan Klien.',
                auth()->id()
            );
        } else {
            $pengajuan->statusHistories()->create([
                'from_status' => $pengajuan->status,
                'to_status' => $pengajuan->status,
                'notes' => 'Jadwal Audit diperbarui. Menunggu persetujuan ulang dari Klien.',
                'user_id' => auth()->id()
            ]);
        }

        NotificationHelper::sendToUser($pengajuan->user_id, 'Persetujuan Jadwal Audit', 'Jadwal audit telah ditentukan. Silakan konfirmasi persetujuan di dashboard Anda.', 'info', $pengajuan->id);

        return redirect()->back()->with('success', 'Jadwal audit berhasil disimpan dan dikirim ke Klien.');
    }

    public function terbitkanBillingLab(Request $request, $id)
    {
        $request->validate([
            'nominal_lab' => 'required|numeric|min:1',
        ]);

        $pengajuan = Pengajuan::findOrFail($id);
        
        $sequence = $pengajuan->invoices()->count() + 1;
        $noPermohonan = str_pad($pengajuan->id, 5, '0', STR_PAD_LEFT);
        $filename = 'tagihan_billing_lab_' . $sequence . '_permohonan_' . $noPermohonan . '.' . $request->file('file_invoice')->getClientOriginalExtension();
        $path = $request->file('file_invoice')->storeAs('invoices', $filename, 'public');

        $pengajuan->invoices()->create([
            'invoice_number' => sprintf(
                'INV/LAB/%s/%05d/%02d',
                now()->format('Y'),
                $pengajuan->id,
                $sequence
            ),
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'amount_total' => $request->nominal_lab,
            'amount_paid' => 0,
            'status' => 'unpaid',
            'file_invoice' => $path,
            'notes' => 'Invoice Uji Laboratorium.',
            'jenis_tagihan' => 'uji_lab',
        ]);

        $pengajuan->transitionTo(
            'billing_3',
            'Billing Uji Lab telah diterbitkan. Menunggu pembayaran Klien.',
            auth()->id()
        );

        NotificationHelper::sendToUser($pengajuan->user_id, 'Billing Uji Lab Diterbitkan', 'Tagihan untuk uji lab telah terbit. Silakan lakukan pembayaran di menu Billing.', 'info', $pengajuan->id);

        return redirect()->back()->with('success', 'Billing Uji Lab berhasil diterbitkan.');
    }

    // ==========================================
    // PLACEHOLDER ROUTES (LAYANAN, AUDIT, SUPERADMIN)
    // ==========================================
    
    public function layananEvaluasiDokumen()
    {
        $pengajuans = Pengajuan::with('user')
            ->where('status', 'audit_kecukupan')
            ->whereNull('ceklis_dokumen')
            ->latest()->get();
        return view('admin.audit_berkas', compact('pengajuans'));
    }

    public function layananPenugasanTim()
    {
        $pengajuans = Pengajuan::with('user')->whereIn('status', ['proses_evaluasi', 'proses_audit'])->latest()->get();
        return view('admin.placeholder_table', [
            'title' => 'Penugasan Tim Audit & Lab',
            'subtitle' => 'Penunjukan personel Tim Audit, PPC, dan Laboratorium Uji.',
            'pengajuans' => $pengajuans
        ]);
    }

    public function layananEvaluasiLaporan()
    {
        $pengajuans = Pengajuan::with('user')->where('status', 'evaluasi')->latest()->get();
        return view('admin.placeholder_table', [
            'title' => 'Evaluasi Laporan Hasil Uji',
            'subtitle' => 'Evaluasi atas Laporan Hasil Audit dan Laporan Hasil Uji (LHU).',
            'pengajuans' => $pengajuans
        ]);
    }

    public function layananKomisiTeknis()
    {
        $pengajuans = Pengajuan::with('user')->where('status', 'keputusan')->latest()->get();
        return view('admin.placeholder_table', [
            'title' => 'Kajian Komisi Teknis',
            'subtitle' => 'Persiapan bahan dan hasil sidang Komisi Teknis LSPro.',
            'pengajuans' => $pengajuans
        ]);
    }

    public function auditLKS()
    {
        $pengajuans = Pengajuan::with('user')->where('status', 'proses_audit')->latest()->get();
        return view('admin.placeholder_table', [
            'title' => 'Laporan Ketidaksesuaian (LKS)',
            'subtitle' => 'Pencatatan dan verifikasi tindakan perbaikan (CAPA) dari klien.',
            'pengajuans' => $pengajuans
        ]);
    }

    public function superadminPersetujuanPenugasan()
    {
        $pengajuans = Pengajuan::with('user')->whereIn('status', ['proses_evaluasi', 'proses_audit'])->latest()->get();
        return view('admin.placeholder_table', [
            'title' => 'Persetujuan Penugasan',
            'subtitle' => 'Persetujuan Surat Tugas Tim Audit dan personel terkait.',
            'pengajuans' => $pengajuans
        ]);
    }

    public function superadminPengesahanSertifikat()
    {
        $pengajuans = Pengajuan::with('user')->where('status', 'keputusan')->latest()->get();
        return view('admin.placeholder_table', [
            'title' => 'Pengesahan Sertifikat Kesesuaian SNI',
            'subtitle' => 'Persetujuan akhir dan penandatanganan Sertifikat Kesesuaian SNI oleh Ketua LSPro.',
            'pengajuans' => $pengajuans
        ]);
    }

    public function formLhp($id)
    {
        $pengajuan = Pengajuan::with('user')->findOrFail($id);
        
        if (\App\Support\LsproType5Workflow::normalize($pengajuan->status) !== 'menunggu_lhp') {
            return redirect()->route('admin.dashboard')->with('error', 'Status pengajuan tidak sedang menunggu Laporan Hasil Uji (LHP).');
        }

        return view('admin.form_lhp', compact('pengajuan'));
    }

    public function uploadLhp(Request $request, $id)
    {
        $pengajuan = Pengajuan::with('user')->findOrFail($id);

        $request->validate([
            'file_lhp' => 'required|file|mimes:pdf|max:10240',
        ]);

        if ($request->hasFile('file_lhp')) {
            $file = $request->file('file_lhp');
            $filename = time() . '_lhp_' . $pengajuan->id . '.' . $file->getClientOriginalExtension();
            
            $folder = 'lhp/' . $pengajuan->id;
            $file->storeAs($folder, $filename, 'public');

            $formData = is_array($pengajuan->data_form) ? $pengajuan->data_form : (json_decode($pengajuan->data_form, true) ?? []);
            $formData['file_lhp'] = $pengajuan->id . '/' . $filename;
            
            $pengajuan->data_form = $formData;
            $pengajuan->transitionTo(
                'tinjauan_lhp', 
                'Laboratorium telah mengunggah Laporan Hasil Uji (LHP). Menunggu tinjauan tim audit.', 
                auth()->id()
            );
            $pengajuan->save();

            \App\Helpers\NotificationHelper::sendToRole('layanan', 'LHP Diunggah', 'Laboratorium telah mengunggah Laporan Hasil Uji (LHP) untuk pengajuan #' . $pengajuan->id . '. Silakan lakukan tinjauan LHP.', 'info', $pengajuan->id);
            \App\Helpers\NotificationHelper::sendToUser($pengajuan->user_id, 'LHP Diunggah', 'Laboratorium telah selesai menguji sampel Anda. Laporan Hasil Pengujian (LHP) telah diteruskan ke LSPro untuk ditinjau.', 'success', $pengajuan->id);
            
            return redirect()->route('admin.dashboard')->with('success', 'Laporan Hasil Uji (LHP) berhasil diunggah dan sedang menuggu tinjauan.');
        }

        return back()->with('error', 'Gagal mengunggah LHP.');
    }

    public function auditHasilLab()
    {
        $pengajuans = Pengajuan::with('user')
            ->whereIn('status', ['proses_lab', 'menunggu_lhp', 'tinjauan_lhp'])
            ->latest()
            ->get();
        return view('admin.placeholder_table', [
            'title' => 'Hasil Laboratorium Uji',
            'subtitle' => 'Daftar hasil uji laboratorium yang terintegrasi (LHU) dan menanti Tinjauan.',
            'pengajuans' => $pengajuans
        ]);
    }

    public function tinjauLhp($id)
    {
        $pengajuan = Pengajuan::with('user')->findOrFail($id);
        
        if (\App\Support\LsproType5Workflow::normalize($pengajuan->status) !== 'tinjauan_lhp') {
            return redirect()->route('admin.dashboard')->with('error', 'Status pengajuan tidak sedang dalam tahap tinjauan LHP.');
        }

        return view('admin.tinjau_lhp', compact('pengajuan'));
    }

    public function prosesTinjauLhp(Request $request, $id)
    {
        $pengajuan = Pengajuan::with('user')->findOrFail($id);

        $request->validate([
            'keputusan' => 'required|in:setuju,perbaikan',
            'catatan' => 'required_if:keputusan,perbaikan'
        ]);

        if ($request->keputusan === 'setuju') {
            $pengajuan->transitionTo(
                'billing_4',
                'Tinjauan Hasil LHP disetujui. ' . ($request->catatan ?? 'Menunggu penerbitan Billing 4 (Sidang Komtek).'),
                auth()->id()
            );
            $pengajuan->save();

            \App\Helpers\NotificationHelper::sendToRole('layanan', 'Tinjauan LHP Selesai', 'Tinjauan LHP untuk pengajuan #' . $pengajuan->id . ' disetujui. Silakan terbitkan Billing 4.', 'info', $pengajuan->id);
            \App\Helpers\NotificationHelper::sendToUser($pengajuan->user_id, 'Hasil Uji LHP Disetujui', 'Laporan Hasil Pengujian (LHP) Anda telah disetujui. Menunggu penerbitan tahap selanjutnya.', 'success', $pengajuan->id);

            return redirect()->route('admin.dashboard')->with('success', 'Tinjauan LHP disetujui, lanjut ke Billing 4.');
        } else {
            // Perbaikan -> Langsung menuju Billing 3 untuk Penagihan Ulang (Re-Audit) & BDLT
            $pengajuan->transitionTo(
                'billing_3',
                'Tinjauan Hasil LHP memerlukan perbaikan/re-audit. ' . $request->catatan,
                auth()->id()
            );
            $pengajuan->save();

            \App\Helpers\NotificationHelper::sendToRole('keuangan', 'Penagihan Re-Audit LHP', 'LHP untuk pengajuan #' . $pengajuan->id . ' ditolak. Silakan terbitkan kembali tagihan Audit Kesesuaian (Billing 3) dan BDLT untuk keperluan Re-Audit.', 'warning', $pengajuan->id);
            \App\Helpers\NotificationHelper::sendToUser($pengajuan->user_id, 'Hasil Uji LHP Ditolak (Re-Audit)', 'Laporan Hasil Pengujian (LHP) Anda memerlukan perbaikan. Anda akan menerima tagihan untuk pelaksanaan re-audit.', 'error', $pengajuan->id);

            return redirect()->route('admin.dashboard')->with('error', 'LHP dikembalikan, pengajuan dialihkan ke tahap Penagihan Re-Audit (Billing 3).');
        }
    }

    public function dummyTeruskan(Request $request)
    {
        if ($request->has('status')) {
            session(['dummy_status' => $request->status]);
        }
        return redirect()->back()->with('dummy_success', '1');
    }

    // =========================================================================
    // 📝 PENGISIAN FORM DINAMIS & AUTO-GENERATE WORD
    // =========================================================================

    /**
     * Tampilkan halaman pengisian form dinamis (berdasarkan Form Builder)
     */
    public function isiFormDinamis($id, $form_type)
    {
        $pengajuan = Pengajuan::with('user')->findOrFail($id);
        
        // Ambil struktur form (pertanyaan-pertanyaan) dari database
        $formFields = \App\Models\FormField::where('form_type', $form_type)->orderBy('order_index')->get();
        
        if ($formFields->isEmpty()) {
            return redirect()->back()->with('error', 'Formulir ini belum memiliki pertanyaan/field. Silakan atur di Form Builder.');
        }

        $formFieldsBySection = $formFields->groupBy('section');
        
        // Ambil info nama form
        $formTypes = \App\Http\Controllers\Superadmin\FormBuilderController::getDynamicFormTypes();
        $typeMeta = $formTypes[$form_type] ?? ['title' => 'Isi Dokumen', 'filename' => ''];

        return view('admin.form_dinamis.isi_form', compact('pengajuan', 'form_type', 'formFieldsBySection', 'typeMeta'));
    }

    /**
     * Simpan hasil pengisian form dinamis dan generate Word (.docx)
     */
    public function simpanFormDinamis(Request $request, $id, $form_type)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        $formFields = \App\Models\FormField::where('form_type', $form_type)->get();

        // 1. Validasi dinamis
        $validationRules = [];
        foreach ($formFields as $field) {
            $rule = $field->is_required ? 'required|' : 'nullable|';
            $rule .= match($field->type) {
                'number' => 'numeric',
                'email'  => 'email|max:255',
                'date'   => 'date',
                default  => 'string|max:1000',
            };
            $validationRules[$field->name] = rtrim($rule, '|');
        }
        $request->validate($validationRules);

        // 2. Kumpulkan data
        $inputData = $request->except(['_token']);
        
        // 3. Generate Word (.docx)
        $formTypes = \App\Http\Controllers\Superadmin\FormBuilderController::getDynamicFormTypes();
        $templateFilename = $formTypes[$form_type]['filename'] ?? null;
        
        if (!$templateFilename) {
            return redirect()->back()->with('error', 'Template file (.docx) tidak ditemukan untuk form ini.');
        }

        $templatePath = storage_path('app/templates/' . $templateFilename);
        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'File template fisik (.docx) tidak ditemukan: ' . $templateFilename);
        }

        try {
            $templateProcessor = new TemplateProcessor($templatePath);
            
            // Set value untuk setiap field dinamis
            foreach ($inputData as $key => $value) {
                $templateProcessor->setValue($key, htmlspecialchars($value));
            }
            
            // Set beberapa global variable (dari pengajuan)
            $templateProcessor->setValue('nomor_registrasi', $pengajuan->nomor_registrasi ?? $pengajuan->id);
            $templateProcessor->setValue('nama_perusahaan', $pengajuan->user->name ?? '');

            $outputFileName = 'Filled_' . str_replace('.docx', '', $templateFilename) . '_' . $pengajuan->id . '.docx';
            
            // Pastikan direktori ada
            if (!is_dir(storage_path('app/public/generated_forms'))) {
                mkdir(storage_path('app/public/generated_forms'), 0755, true);
            }

            $savePath = storage_path('app/public/generated_forms/' . $outputFileName);
            $templateProcessor->saveAs($savePath);

            // Langsung otomatis terdownload ke user
            return response()->download($savePath)->deleteFileAfterSend(false);
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses template: ' . $e->getMessage());
        }
    }

    public function createInvoice(Request $request, $id)
    {
        $request->validate([
            'file_invoice' => 'required|mimes:pdf,jpg,png,jpeg|max:5120',
        ]);

        $pengajuan = Pengajuan::with('rabItems')->findOrFail($id);
        $status = $pengajuan->status;
        
        if (!in_array($status, ['billing_1', 'billing_2', 'billing_3', 'billing_4'])) {
            return back()->with('error', 'Status pengajuan saat ini tidak mengizinkan pembuatan tagihan.');
        }
        
        // Pastikan RAB sudah dibuat
        if ($pengajuan->rabItems->isEmpty()) {
            return back()->with('error', 'Gagal menerbitkan tagihan. RAB Master belum dibuat untuk pengajuan ini.');
        }

        // Tentukan Kategori RAB berdasarkan status tagihan
        $kategoriTarget = '';
        $jenisTagihanDb = $status; // Default untuk DB (billing_1, billing_2, dll)
        $namaTagihanLabel = strtoupper(str_replace('_', ' ', $status));
        
        $isBdlt = $request->query('jenis') === 'bdlt';

        if ($status === 'billing_1') {
            $kategoriTarget = 'Permohonan';
        } elseif ($status === 'billing_2') {
            $kategoriTarget = 'Audit Kecukupan';
            $namaTagihanLabel = 'BILLING 2 (AUDIT KECUKUPAN)';
        } elseif ($status === 'billing_3') {
            if ($isBdlt) {
                $kategoriTarget = 'Biaya Di Luar Tarif (BDLT)';
                $jenisTagihanDb = 'billing_3_bdlt';
                $namaTagihanLabel = 'BILLING 3 (BDLT)';
            } else {
                $kategoriTarget = 'Audit Kesesuaian';
                $namaTagihanLabel = 'BILLING 3 (AUDIT KESESUAIAN)';
            }
        } elseif ($status === 'billing_4') {
            $kategoriTarget = 'Sidang Komisi Teknis';
            $namaTagihanLabel = 'BILLING 4 (SIDANG KOMTEK)';
        }
        
        // Hitung total dari kategori terkait
        $totalAmount = 0;
        foreach ($pengajuan->rabItems as $item) {
            // Bisa menggunakan str_contains untuk lebih fleksibel
            if (str_contains(strtolower($item->kategori), strtolower($kategoriTarget))) {
                if ($item->tarif_pnbp_total !== null) {
                    $totalAmount += $item->tarif_pnbp_total;
                }
            }
        }
        
        // Jika tidak ada komponen untuk kategori tersebut
        if ($totalAmount == 0) {
            return back()->with('error', "Gagal menerbitkan tagihan. Tidak ditemukan nominal pada RAB Master untuk tahap $kategoriTarget.");
        }

        $sequence = $pengajuan->invoices()->count() + 1;

        $extension = $request->file('file_invoice')->getClientOriginalExtension();
        $filename = 'tagihan_' . $jenisTagihanDb . '_' . $pengajuan->id . '_' . time() . '.' . $extension;
        $path = $request->file('file_invoice')->storeAs('invoices', $filename, 'public');
        $pengajuan->invoices()->create([
            'invoice_number' => sprintf(
                'INV/LSPRO/%s/%05d/%02d',
                now()->format('Y'),
                $pengajuan->id,
                $sequence
            ),
            'invoice_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'amount_total' => $totalAmount,
            'amount_paid' => 0,
            'status' => 'unpaid',
            'jenis_tagihan' => $jenisTagihanDb,
            'file_invoice' => $path,
            'notes' => 'Billing telah diterbitkan oleh Keuangan. Menunggu pembayaran klien.',
        ]);

        NotificationHelper::sendToUser($pengajuan->user_id, 'Invoice Diterbitkan', 'Invoice baru (' . $namaTagihanLabel . ') telah diterbitkan untuk pengajuan #' . $pengajuan->id, 'info', $pengajuan->id);

        return back()->with('success', 'Tagihan (Invoice) berhasil diterbitkan secara otomatis dari RAB.');
    }

    public function createRab($id)
    {
        $pengajuan = Pengajuan::with('user', 'rabItems')->findOrFail($id);
        $rabItems = $pengajuan->rabItems;

        return view('admin.rab.create', compact('pengajuan', 'rabItems'));
    }

    public function storeRab(Request $request, $id)
    {
        $pengajuan = Pengajuan::findOrFail($id);
        
        $items = $request->input('rab', []);
        
        // Hapus RAB lama
        \App\Models\RabItem::where('pengajuan_id', $pengajuan->id)->delete();
        
        foreach ($items as $kategori => $komponenList) {
            foreach ($komponenList as $index => $item) {
                // Lewati jika komponen kosong
                if (empty($item['komponen'])) continue;

                $hari = isset($item['hari']) && $item['hari'] !== '' ? (int)$item['hari'] : null;
                $orang = isset($item['orang']) && $item['orang'] !== '' ? (int)$item['orang'] : null;
                $tarifSatuan = isset($item['tarif']) && $item['tarif'] !== '' ? str_replace('.', '', $item['tarif']) : null;
                
                // Hitung total PNBP untuk row ini
                $tarifTotal = null;
                if ($tarifSatuan !== null) {
                    $tarifTotal = (int)$tarifSatuan;
                    if ($hari !== null) $tarifTotal *= $hari;
                    if ($orang !== null) $tarifTotal *= $orang;
                }

                \App\Models\RabItem::create([
                    'pengajuan_id' => $pengajuan->id,
                    'kategori' => $kategori,
                    'komponen' => $item['komponen'],
                    'hari' => $hari,
                    'orang' => $orang,
                    'tarif_pnbp_satuan' => $tarifSatuan,
                    'tarif_pnbp_total' => $tarifTotal,
                ]);
            }
        }
        
        // Update tagihan (Invoice) yang sudah diterbitkan tapi belum lunas agar nominalnya sinkron
        $unpaidInvoices = \App\Models\Invoice::where('pengajuan_id', $pengajuan->id)
                            ->where('status', 'unpaid')
                            ->get();

        $newItems = \App\Models\RabItem::where('pengajuan_id', $pengajuan->id)->get();
        foreach($unpaidInvoices as $inv) {
            $kategoriTarget = '';
            if ($inv->jenis_tagihan === 'billing_1') {
                $kategoriTarget = 'Permohonan';
            } elseif ($inv->jenis_tagihan === 'billing_2') {
                $kategoriTarget = 'Audit Kecukupan';
            } elseif ($inv->jenis_tagihan === 'billing_2_bdlt') {
                $kategoriTarget = 'Biaya Di Luar Tarif (BDLT)';
            } elseif ($inv->jenis_tagihan === 'billing_3') {
                $kategoriTarget = 'Audit Kesesuaian';
            } elseif ($inv->jenis_tagihan === 'billing_3_bdlt') {
                $kategoriTarget = 'Biaya Di Luar Tarif (BDLT)';
            } elseif ($inv->jenis_tagihan === 'billing_4') {
                $kategoriTarget = 'Evaluasi / Sidang Komtek';
            }
            
            if ($kategoriTarget) {
                $totalAmount = 0;
                foreach ($newItems as $i) {
                    if (str_contains(strtolower($i->kategori), strtolower($kategoriTarget))) {
                        if ($i->tarif_pnbp_total !== null) {
                            $totalAmount += $i->tarif_pnbp_total;
                        }
                    }
                }
                $inv->update(['amount_total' => $totalAmount]);
            }
        }
        
        return redirect()->route('admin.panel_keuangan')->with('success', 'RAB Master berhasil disimpan & Tagihan otomatis disinkronisasi.');
    }
}

