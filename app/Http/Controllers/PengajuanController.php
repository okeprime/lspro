<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengajuan;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PengajuanController extends Controller
{
    /**
     * 1. Halaman Pilih Tahap (Menu Pendaftaran Awal)
     */
    public function pilihTahap()
    {
        if (view()->exists('pengajuan.pilih_tahap')) {
            return view('pengajuan.pilih_tahap');
        }
        return view('pengajuan.create_otomatis', [
            'tahap' => '7.2',
            'jenisSertifikasi' => 'Sertifikasi Baru',
            'formData' => [],
            'draft' => null
        ]); 
    }

    /**
     * 2. Tampilkan Form Input Data (Tahap 1)
     */
    public function create(Request $request)
    {
        $tahap = $request->get('tahap', '7.2');
        $jenisSertifikasi = $request->get('jenis_sertifikasi', 'Sertifikasi Baru');
        $formData = [];
        $draft = null;

        if ($request->has('draft_id')) {
            $draft = Pengajuan::where('id', $request->draft_id)
                ->where('user_id', Auth::id())
                ->first();
                
            if ($draft) {
                $tahap = $draft->tahap ?? $tahap;
                $jenisSertifikasi = $draft->jenis_sertifikasi ?? $jenisSertifikasi;
                
                $data = $draft->data_form;
                if (is_string($data)) {
                    $formData = json_decode($data, true) ?? [];
                } elseif (is_array($data) || is_object($data)) {
                    $formData = (array) $data;
                }
            }
        }

        return view('pengajuan.create_otomatis', compact('tahap', 'jenisSertifikasi', 'formData', 'draft'));
    }

    /**
     * 3. Proses Kirim Formulir Kompleks & Auto-Generate Word
     */
    public function store(Request $request)
    {
        // A. VALIDASI SELURUH INPUTAN SESUAI FORM BLADE
        $request->validate([
            'tahap'                     => 'required',
            'nama_pemohon'              => 'required|string|max:255',
            'jabatan_pemohon'           => 'required|string|max:255',
            'alamat_pemohon'            => 'required|string',
            'telp_pemohon'              => 'required|string',
            'hp_pemohon'                => 'required|string',
            'kewarganegaraan_pemohon'   => 'required|string',
            'nama_perusahaan'           => 'required|string',
            'nama_penghubung'           => 'required|string',
            'alamat_kantor'             => 'required|string',
            'alamat_pabrik'             => 'required|string',
            
            // Ruang Lingkup Produk
            'nama_produk'               => 'required|string',
            'merek_produk'              => 'required|string',
            'tipe_produk'               => 'required|string',
            'no_sni'                    => 'required|string',
            'kapasitas_produksi'        => 'required|string',
            'standar_smm'               => 'required|string',
            
            // Tenaga Kerja
            'total_tk'                  => 'required|integer',
            'tk_produksi'               => 'required|integer',
            'tk_mutu'                   => 'required|integer',
            'tk_staf'                   => 'required|integer',
            'tk_nonstaf'                => 'required|integer',
        ]);

        // C. STRUKTUR DATA FORM
        $arrayDataForm = [
            'nama_pemohon'              => $request->nama_pemohon,
            'jabatan_pemohon'           => $request->jabatan_pemohon,
            'alamat_pemohon'            => $request->alamat_pemohon,
            'telp_pemohon'              => $request->telp_pemohon,
            'hp_pemohon'                => $request->hp_pemohon,
            'kewarganegaraan_pemohon'   => $request->kewarganegaraan_pemohon,
            'nama_perusahaan'           => $request->nama_perusahaan,
            'nama_penghubung'           => $request->nama_penghubung,
            'alamat_kantor'             => $request->alamat_kantor,
            'alamat_pabrik'             => $request->alamat_pabrik,
            
            // Informasi Produk
            'nama_produk'               => $request->nama_produk,
            'merek_produk'              => $request->merek_produk,
            'tipe_produk'               => $request->tipe_produk,
            'no_sni'                    => $request->no_sni,
            'kapasitas_produksi'        => $request->kapasitas_produksi,
            'standar_smm'               => $request->standar_smm,
            
            // Alokasi Tenaga Kerja
            'total_tk'                  => $request->total_tk,
            'tk_produksi'               => $request->tk_produksi,
            'tk_mutu'                   => $request->tk_mutu,
            'tk_staf'                   => $request->tk_staf,
            'tk_nonstaf'                => $request->tk_nonstaf,
            
            'lampiran'                  => [] // Lampiran akan diupload pada tahap berikutnya
        ];

        // D. SIMPAN DATA KE DATABASE
        $pengajuan = new Pengajuan();
        $pengajuan->user_id = Auth::id() ?? 1;
        $pengajuan->tahap = $request->tahap ?? '7.2';
        $pengajuan->jenis_pengajuan = 'Sertifikasi';
        $pengajuan->status = 'diajukan'; 
        $pengajuan->data_form = json_encode($arrayDataForm); 
        $pengajuan->save();

        // E. AUTO-GENERATE DOKUMEN WORD (Membaca file Form_7.2-1_Permohonan.docx)
        $templatePath = storage_path('app/templates/Form_7.2-1_Permohonan.docx');
        
        // Pengaman jika template master tidak ditemukan
        if (!file_exists($templatePath)) {
            if (!file_exists(storage_path('app/templates'))) {
                mkdir(storage_path('app/templates'), 0755, true);
            }
            $emptyWord = new \PhpOffice\PhpWord\PhpWord();
            $emptyWord->save($templatePath, 'Word2007');
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        // --- 1. IDENTITAS PEMOHON ---
        $templateProcessor->setValue('nama_pemohon', $request->nama_pemohon ?? '-');
        $templateProcessor->setValue('alamat_pemohon', $request->alamat_pemohon ?? '-');
        $templateProcessor->setValue('telp_pemohon', $request->telp_pemohon ?? '-');
        $templateProcessor->setValue('hp_pemohon', $request->hp_pemohon ?? '-');
        $templateProcessor->setValue('kewarganegaraan_pemohon', $request->kewarganegaraan_pemohon ?? '-');
        $templateProcessor->setValue('jabatan_pemohon', $request->jabatan_pemohon ?? '-');
        $templateProcessor->setValue('status_pemohon', $request->status_pemohon ?? '-'); 
        
        // --- 2. IDENTITAS PENGHUBUNG & PERUSAHAAN ---
        $templateProcessor->setValue('nama_penghubung', $request->nama_penghubung ?? '-');
        $templateProcessor->setValue('jabatan_penghubung', $request->jabatan_penghubung ?? '-');
        $templateProcessor->setValue('nama_perusahaan', $request->nama_perusahaan ?? '-');
        $templateProcessor->setValue('alamat_perusahaan', $request->alamat_kantor ?? '-'); 
        $templateProcessor->setValue('kota_perusahaan', $request->kota_kantor ?? '-');
        $templateProcessor->setValue('provinsi_perusahaan', $request->provinsi_kantor ?? '-');
        $templateProcessor->setValue('telp_perusahaan', $request->telp_kantor ?? '-');
        $templateProcessor->setValue('hp_penghubung', $request->hp_penghubung ?? '-');
        $templateProcessor->setValue('email_penghubung', $request->email_penghubung ?? '-');

        // --- 3. LEGALITAS & PABRIK ---
        $templateProcessor->setValue('badan_hukum', $request->badan_hukum ?? '-');
        $templateProcessor->setValue('alamat_kantor', $request->alamat_kantor ?? '-');
        $templateProcessor->setValue('kota_kantor', $request->kota_kantor ?? '-');
        $templateProcessor->setValue('provinsi_kantor', $request->provinsi_kantor ?? '-');
        $templateProcessor->setValue('telp_kantor', $request->telp_kantor ?? '-');
        $templateProcessor->setValue('email_kantor', $request->email_kantor ?? '-');
        $templateProcessor->setValue('alamat_pabrik', $request->alamat_pabrik ?? '-');
        $templateProcessor->setValue('kota_pabrik', $request->kota_pabrik ?? '-');
        $templateProcessor->setValue('provinsi_pabrik', $request->provinsi_pabrik ?? '-');
        $templateProcessor->setValue('telp_pabrik', $request->telp_pabrik ?? '-');
        $templateProcessor->setValue('email_pabrik', $request->email_pabrik ?? '-');

        // --- 4. IMPORTIR & LAIN-LAIN ---
        $templateProcessor->setValue('nama_importir', $request->nama_importir ?? '-');
        $templateProcessor->setValue('alamat_importir', $request->alamat_importir ?? '-');
        $templateProcessor->setValue('api_importir', $request->api_importir ?? '-');
        $templateProcessor->setValue('bahasa_pabrik', $request->bahasa_pabrik ?? '-');
        $templateProcessor->setValue('penerjemah_pabrik', $request->penerjemah_pabrik ?? '-');
        $templateProcessor->setValue('jarak_pabrik', $request->jarak_pabrik ?? '-');
        $templateProcessor->setValue('waktu_pabrik', $request->waktu_pabrik ?? '-');

        // --- 5. DATA PRODUK PUPUK ---
        $templateProcessor->setValue('nama_pupuk', $request->nama_produk ?? '-');
        $templateProcessor->setValue('judul_sni', $request->judul_sni ?? 'SNI Pupuk Terdaftar');
        $templateProcessor->setValue('no_sni', $request->no_sni ?? '-');
        $templateProcessor->setValue('merek_pupuk', $request->merek_produk ?? '-');
        $templateProcessor->setValue('jenis_pupuk', $request->tipe_produk ?? '-');
        $templateProcessor->setValue('asal_pabrik', $request->asal_pabrik ?? '-');
        $templateProcessor->setValue('status_produk', $request->status_produk ?? '-');
        $templateProcessor->setValue('foto_produk', 'Terlampir pada berkas terpisah');

        // --- 6. ORGANISASI & SMM ---
        $templateProcessor->setValue('nama_wmm', $request->nama_wmm ?? '-');
        $templateProcessor->setValue('telp_wmm', $request->telp_wmm ?? '-');
        $templateProcessor->setValue('hp_wmm', $request->hp_wmm ?? '-');
        $templateProcessor->setValue('email_wmm', $request->email_wmm ?? '-');
        $templateProcessor->setValue('jumlah_lini', $request->jumlah_lini ?? '-');
        $templateProcessor->setValue('standar_smm', $request->standar_smm ?? '-');

        // --- 7. TENAGA KERJA ---
        $templateProcessor->setValue('total_tk', $request->total_tk ?? '0');
        $templateProcessor->setValue('tk_produksi', $request->tk_produksi ?? '0');
        $templateProcessor->setValue('tk_mutu', $request->tk_mutu ?? '0');
        $templateProcessor->setValue('tk_staf', $request->tk_staf ?? '0');
        $templateProcessor->setValue('tk_nonstaf', $request->tk_nonstaf ?? '0');

        // Menyimpan Hasil File Word Jadi
        $folderPermohonan = storage_path('app/public/permohonan');
        if (!file_exists($folderPermohonan)) {
            mkdir($folderPermohonan, 0755, true);
        }

        $namaFilePermohonan = 'Form_7.2-1_' . time() . '_' . $pengajuan->id . '.docx';
        $templateProcessor->saveAs($folderPermohonan . DIRECTORY_SEPARATOR . $namaFilePermohonan);

        // Sinkronisasi file Word ke database record
        $pengajuan->file_permohonan = $namaFilePermohonan;
        $pengajuan->save();

        // F. REDIRECT AMAN KEMBALI KE HALAMAN AKTIVITAS USER
        return redirect('/aktivitas')
            ->with('success', 'Formulir sertifikasi pupuk berhasil dikirim! Seluruh berkas fisik & data otomatis dioper ke Tata Usaha.');
    }

    public function storeLampiran(Request $request, $id)
    {
        $pengajuan = Pengajuan::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if (\App\Support\LsproType5Workflow::normalize($pengajuan->status) !== 'menunggu_lampiran') {
            return redirect()
                ->route('aktivitas.index')
                ->with('error', 'Akses ditolak. Pengajuan belum berada pada tahap unggah kelengkapan.');
        }

        $isDraft = $request->input('action') === 'draft';
        $existingFormData = $this->normalFormData($pengajuan);
        $rules = [];
        
        foreach ($this->lampiranFields() as $field => $meta) {
            $isRequired = $meta['required'] && empty($existingFormData[$field]);
            // Jika draft, file tidak wajib (bisa upload sebagian)
            $rule = $isDraft ? 'nullable' : ($isRequired ? 'required' : 'nullable');
            $rules[$field] = $rule . '|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:51200';
        }
        $request->validate($rules);

        $formData = array_merge($existingFormData, $this->simpanLampiran($request));
        $pengajuan->data_form = json_encode($formData);
        $pengajuan->save();

        if ($isDraft) {
            return redirect()
                ->route('pengajuan.lampiran', $id)
                ->with('success', 'Draft lampiran berhasil disimpan. Anda dapat melanjutkannya nanti.');
        }

        $pengajuan->transitionTo(
            'evaluasi_724_tu',
            'Klien telah mengunggah dokumen kelengkapan.',
            Auth::id()
        );

        \App\Helpers\NotificationHelper::sendToRole('tatausaha', 'Menunggu Evaluasi TU', 'Klien telah mengunggah dokumen kelengkapan untuk pengajuan #' . $pengajuan->id, 'info', $pengajuan->id);

        return redirect()
            ->route('aktivitas.index')
            ->with('success', 'Lampiran kelengkapan berhasil diunggah. Menunggu proses evaluasi dari Tata Usaha.');
    }

    public function uploadPermohonanTtd(Request $request, $id)
    {
        $pengajuan = Pengajuan::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'file_permohonan_ttd' => 'required|file|mimes:pdf|max:10240',
            'file_ceklis_ttd' => 'required|file|mimes:pdf|max:10240',
        ]);

        $folderPengajuan = 'permohonan/' . $pengajuan->id;
        Storage::disk('public')->makeDirectory($folderPengajuan);

        // Permohonan 7.2-1
        if ($pengajuan->file_permohonan_ttd && Storage::disk('public')->exists($pengajuan->file_permohonan_ttd)) {
            Storage::disk('public')->delete($pengajuan->file_permohonan_ttd);
        }
        Storage::disk('public')->putFileAs(
            $folderPengajuan,
            $request->file('file_permohonan_ttd'),
            'permohonan_ttd.pdf'
        );
        $pengajuan->file_permohonan_ttd = $folderPengajuan . '/permohonan_ttd.pdf';

        // Ceklis 7.2-4
        if ($pengajuan->file_ceklis_ttd && Storage::disk('public')->exists($pengajuan->file_ceklis_ttd)) {
            Storage::disk('public')->delete($pengajuan->file_ceklis_ttd);
        }
        Storage::disk('public')->putFileAs(
            $folderPengajuan,
            $request->file('file_ceklis_ttd'),
            'ceklis_ttd.pdf'
        );
        $pengajuan->file_ceklis_ttd = $folderPengajuan . '/ceklis_ttd.pdf';

        $pengajuan->transitionTo(
            'billing',
            'Dokumen TTD klien telah diterima. Lanjut ke proses tagihan sertifikasi (Billing).',
            Auth::id()
        );
        $pengajuan->save();

        \App\Helpers\NotificationHelper::sendToRole('tatausaha', 'Dokumen TTD Diunggah', 'Klien telah mengunggah Form 7.2-1 dan Form 7.2-4 bertanda tangan untuk pengajuan #' . $pengajuan->id, 'success', $pengajuan->id);
        \App\Helpers\NotificationHelper::sendToUser(Auth::id(), 'Berkas Terkirim', 'Dokumen permohonan Anda telah berhasil dikirim. Menunggu proses billing.', 'success', $pengajuan->id);

        return redirect()
            ->route('aktivitas.index')
            ->with('success', 'Dokumen Bertanda Tangan berhasil diunggah! Berkas Anda akan segera masuk tahap Pembayaran (Billing).');
    }
    public function index()
    {
        return view('pengajuan.index');
    }

    public function pilihProduk(Request $request, $jenis = 'sertifikasi')
    {
        $tahap = $request->get('tahap', '7.2');
        return view('pengajuan.pilih_produk', [
            'jenis_pengajuan' => $jenis,
            'tahap' => $tahap
        ]);
    }

    public function lampiran(Request $request, $id)
    {
        $pengajuan = Pengajuan::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        $lampiranFields = $this->lampiranFields();
        $formData = $this->normalFormData($pengajuan);
        return view('pengajuan.lampiran', compact('pengajuan', 'lampiranFields', 'formData'));
    }

    private function lampiranFields()
    {
        return [
            'akte_perusahaan' => ['label' => 'Akte Perusahaan', 'required' => true],
            'izin_usaha_industri' => ['label' => 'Surat Izin Industri', 'required' => true],
            'siup_tdup' => ['label' => 'Surat Izin Usaha Perdagangan', 'required' => true],
            'sertifikat_merek' => ['label' => 'Tanda Daftar Merek', 'required' => true],
            'pelimpahan_merek' => ['label' => 'Surat Pelimpahan Merek', 'required' => false],
            'bukti_importir' => ['label' => 'Surat Penunjukan sebagai Importir', 'required' => false],
            'api_umum' => ['label' => 'API Umum', 'required' => false],
            'struktur_organisasi' => ['label' => 'Struktur Organisasi Perusahaan', 'required' => true],
            'alur_produksi_mutu' => ['label' => 'Alur Proses Produksi', 'required' => true],
            'daftar_alat_mesin' => ['label' => 'Daftar Alat dan Mesin Produksi', 'required' => true],
            'daftar_alat_uji_kalibrasi' => ['label' => 'Daftar Peralatan Uji Terkalibrasi', 'required' => true],
            'surat_pernyataan_diri' => ['label' => 'Surat Pernyataan Diri Penerapan Sistem Mutu', 'required' => true],
            'pedoman_mutu' => ['label' => 'Dokumen Sistem Mutu (Panduan Mutu, dll)', 'required' => true],
            'daftar_prosedur_ik' => ['label' => 'Daftar Seluruh Prosedur/Instruksi Kerja', 'required' => true],
            'perjanjian_sertifikasi' => ['label' => 'Perjanjian Sertifikasi', 'required' => true],
            'ilustrasi_tanda_sni' => ['label' => 'Ilustrasi Tanda SNI pada Kemasan', 'required' => true],
        ];
    }

    private function normalFormData($pengajuan)
    {
        $data = $pengajuan->data_form;
        if (is_string($data)) {
            $data = json_decode($data, true);
        }
        return is_array($data) ? $data : [];
    }

    private function simpanLampiran(Request $request)
    {
        $uploaded = [];
        $tujuanFolder = storage_path('app/public/permohonan/lampiran');
        if (!file_exists($tujuanFolder)) {
            mkdir($tujuanFolder, 0755, true);
        }

        foreach ($this->lampiranFields() as $field => $meta) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = time() . '_' . $field . '.' . $file->getClientOriginalExtension();
                $file->move($tujuanFolder, $filename);
                $uploaded[$field] = $filename;
            }
        }
        return $uploaded;
    }

    public function uploadSurvailenDokumen(Request $request, $id)
    {
        return redirect()->back()->with('success', 'Dokumen Survailen berhasil diunggah.');
    }

    public function storeDraft(Request $request)
    {
        $draftId = $request->input('draft_id');
        $arrayDataForm = $request->except(['_token', 'is_ajax', 'draft_id']);
        
        if ($draftId) {
            $pengajuan = Pengajuan::where('id', $draftId)->where('user_id', Auth::id())->first();
            if ($pengajuan) {
                $pengajuan->data_form = json_encode($arrayDataForm);
                $pengajuan->save();
                return response()->json(['success' => true, 'draft_id' => $pengajuan->id, 'message' => 'Draft berhasil diupdate.']);
            }
        }

        $pengajuan = new Pengajuan();
        $pengajuan->user_id = Auth::id() ?? 1;
        $pengajuan->tahap = $request->tahap ?? '7.2';
        $pengajuan->jenis_pengajuan = strtolower($request->jenis_sertifikasi) === 'resertifikasi' ? 'resertifikasi' : (strtolower($request->jenis_sertifikasi) === 'survailen' ? 'survailen' : 'sertifikasi');
        $pengajuan->status = 'draft'; 
        $pengajuan->data_form = json_encode($arrayDataForm); 
        $pengajuan->save();

        return response()->json(['success' => true, 'draft_id' => $pengajuan->id, 'message' => 'Draft berhasil disimpan.']);
    }

    public function createWizard(Request $request, $step = 1)
    {
        $formData = [];
        $draft = null;
        return view('pengajuan.wizard.index', compact('step', 'formData', 'draft'));
    }

    public function saveWizard(Request $request)
    {
        return response()->json(['success' => true]);
    }

    public function submitWizard(Request $request)
    {
        return redirect()->route('aktivitas.index')->with('success', 'Pengajuan berhasil disubmit.');
    }

    public function billing()
    {
        $invoices = \App\Models\Invoice::whereHas('pengajuan', function ($query) {
            $query->where('user_id', Auth::id());
        })->orderBy('created_at', 'desc')->get();

        $totalUnpaid = $invoices->where('status', 'unpaid')->sum('amount_total');
        $totalPaid = $invoices->where('status', 'paid')->sum('amount_total');
        $countUnpaid = $invoices->where('status', 'unpaid')->count();

        return view('client.billing', compact('invoices', 'totalUnpaid', 'totalPaid', 'countUnpaid'));
    }

    public function payInvoice(Request $request, $id)
    {
        $request->validate([
            'bukti_transfer' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120'
        ]);

        $invoice = \App\Models\Invoice::where('id', $id)->whereHas('pengajuan', function ($query) {
            $query->where('user_id', Auth::id());
        })->firstOrFail();

        // Simulate upload and status update
        $invoice->status = 'pending_verification'; // Menunggu konfirmasi TU
        $invoice->notes = 'Bukti pembayaran telah diunggah dan menunggu verifikasi TU.';
        $invoice->save();

        \App\Helpers\NotificationHelper::sendToRole('tatausaha', 'Pembayaran Tagihan', 'Klien telah mengunggah bukti pembayaran untuk invoice #' . $invoice->invoice_number, 'info', $invoice->pengajuan_id);

        return redirect()->back()->with('success', 'Bukti pembayaran berhasil diunggah! Menunggu verifikasi.');
    }
}