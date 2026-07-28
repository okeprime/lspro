<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengajuan;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PengajuanController extends Controller
{
    public function index()
    {
        return view('pengajuan.index');
    }

    /**
     * 1. Halaman Pilih Tahap (Menu Pendaftaran Awal)
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



        $formFields = \App\Models\FormField::where('form_type', 'permohonan')->orderBy('order_index')->get();
        $formFieldsBySection = $formFields->groupBy('section');

        return view('pengajuan.create_otomatis', compact('tahap', 'jenisSertifikasi', 'formData', 'draft', 'formFieldsBySection'));
    }

    /**
     * 3. Proses Kirim Formulir Kompleks & Auto-Generate Word
     */
    public function store(Request $request)
    {
        // A. BUILD DYNAMIC VALIDATION RULES from FormField DB table
        $formFields = \App\Models\FormField::where('form_type', 'permohonan')->orderBy('order_index')->get();
        $validationRules = ['tahap' => 'required'];
        foreach ($formFields as $field) {
            if ($field->type === 'file') continue; // File handled separately
            
            // Only validate if the field exists in the HTML form to prevent mismatch with DB seeder
            if ($request->has($field->name) || $request->exists($field->name)) {
                $rule = $field->is_required ? 'required|' : 'nullable|';
                $rule .= match($field->type) {
                    'number' => 'numeric',
                    'email'  => 'email|max:255',
                    'date'   => 'date',
                    default  => 'string|max:1000',
                };
                $validationRules[$field->name] = rtrim($rule, '|');
            }
        }
        $request->validate($validationRules);

        // C. STRUKTUR DATA FORM
        $arrayDataForm = $request->except(['_token', 'draft_id']);
        $arrayDataForm['lampiran'] = $request->lampiran ?? '1 Berkas';

        // D. SIMPAN DATA KE DATABASE ATAU UPDATE DRAFT/PERBAIKAN
        if ($request->filled('draft_id') && $request->draft_id !== 'temp') {
            $pengajuan = Pengajuan::where('id', $request->draft_id)->where('user_id', Auth::id())->first() ?? new Pengajuan();
        } else {
            $pengajuan = new Pengajuan();
        }

        $pengajuan->user_id = Auth::id() ?? 1;
        $pengajuan->tahap = $request->tahap ?? '7.2';
        $pengajuan->jenis_pengajuan = strtolower($request->jenis_sertifikasi) === 'resertifikasi' ? 'Resertifikasi' : (strtolower($request->jenis_sertifikasi) === 'survailen' ? 'Survailen' : 'Sertifikasi');
        
        // Generate Nomor Registrasi Berurutan (hanya saat diajukan perdana)
        if (empty($pengajuan->nomor_registrasi)) {
            $lastReg = Pengajuan::whereNotNull('nomor_registrasi')
                                ->whereRaw('nomor_registrasi REGEXP "^[0-9]+$"')
                                ->orderBy('id', 'desc')
                                ->first();
            
            if ($lastReg) {
                $nextSeq = str_pad((int)$lastReg->nomor_registrasi + 1, 5, '0', STR_PAD_LEFT);
            } else {
                $lastId = Pengajuan::max('id') ?? 0;
                $nextSeq = str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);
            }
            $pengajuan->nomor_registrasi = $nextSeq;
        }

        $oldStatus = $pengajuan->status;
        $pengajuan->status = 'menunggu_ttd'; 
        
        // Save ID before creating folder
        $pengajuan->save(); 
        
        $folderPengajuan = 'permohonan/' . $pengajuan->id;
        Storage::disk('public')->makeDirectory($folderPengajuan);
        
        $filesToSave = ['kop_surat', 'sketsa_logo', 'foto_depan', 'foto_belakang', 'foto_kanan', 'foto_kiri'];
        foreach ($filesToSave as $fileField) {
            if ($request->hasFile($fileField)) {
                $file = $request->file($fileField);
                $filename = $fileField . '_' . time() . '.' . $file->getClientOriginalExtension();
                Storage::disk('public')->putFileAs($folderPengajuan, $file, $filename);
                $arrayDataForm[$fileField] = $folderPengajuan . '/' . $filename;
            } else if ($request->filled('draft_id') && isset($pengajuan->data_form)) {
                $oldData = json_decode($pengajuan->data_form, true);
                if (isset($oldData[$fileField])) {
                    $arrayDataForm[$fileField] = $oldData[$fileField];
                }
            }
        }
        
        $pengajuan->data_form = json_encode($arrayDataForm); 
        $pengajuan->save();

        if ($oldStatus === 'perbaikan') {
            $pengajuan->transitionTo(
                'menunggu_ttd',
                'Klien telah mengirimkan ulang formulir permohonan yang telah direvisi. Menunggu unggah Kop Surat & TTD.',
                Auth::id()
            );
        }

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

        // --- DYNAMIC FIELD MAPPING: Loop semua field dari request ---
        foreach ($arrayDataForm as $key => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            if (is_string($value) || is_numeric($value)) {
                if (empty($value)) $value = '-';
                try {
                    if ($key === 'waktu_pabrik' && is_numeric($value)) {
                        $templateProcessor->setValue($key, htmlspecialchars((string) $value) . ' Menit');
                    } else {
                        $templateProcessor->setValue($key, htmlspecialchars((string) $value));
                    }
                } catch (\Exception $e) {}
            }
        }

        // --- FIELD STATIS TAMBAHAN (Legacy & Kepala Surat) ---
        // Alias / field yang namanya berbeda di template vs form
        $templateProcessor->setValue('nama_pupuk', $request->nama_produk ?? '-');
        $templateProcessor->setValue('merek_pupuk', $request->merek_produk ?? '-');
        $templateProcessor->setValue('jenis_pupuk', $request->tipe_produk ?? '-');
        $templateProcessor->setValue('alamat_perusahaan', $request->alamat_kantor ?? '-');
        $templateProcessor->setValue('kota_perusahaan', $request->kota_kantor ?? '-');
        $templateProcessor->setValue('provinsi_perusahaan', $request->provinsi_kantor ?? '-');
        $templateProcessor->setValue('telp_perusahaan', $request->telp_kantor ?? '-');
        $templateProcessor->setValue('foto_produk', 'Terlampir');
        $templateProcessor->setValue('nomor_surat', '-');
        $templateProcessor->setValue('tempat_ttd', $request->kota_kantor ?? 'Jakarta');
        $templateProcessor->setValue('tanggal_ttd', now()->translatedFormat('d F Y'));
        // Status lampiran statis
        $lampiranStatuses = [
            'akte_perusahaan_status', 'izin_usaha_industri_status', 'siup_tdup_status',
            'sertifikat_merek_status', 'pelimpahan_merek_status', 'api_umum_status',
            'alur_produksi_mutu_status', 'daftar_alat_mesin_status', 'daftar_alat_uji_kalibrasi_status',
            'pedoman_mutu_status', 'daftar_prosedur_ik_status', 'pernyataan_smm_status',
            'ilustrasi_tanda_sni_status', 'perjanjian_sertifikasi_status',
        ];
        foreach ($lampiranStatuses as $ls) {
            $templateProcessor->setValue($ls, '(Terlampir)');
        }

        // Clear Kop Surat placeholder as requested
        $templateProcessor->setValue('kop_surat', '');

        // Menyimpan Hasil File Word Jadi
        $folderPermohonan = storage_path('app/public/permohonan');
        if (!file_exists($folderPermohonan)) {
            mkdir($folderPermohonan, 0755, true);
        }

        $namaPerusahaan = $request->input('nama_perusahaan', 'PT');
        $safeNamaPerusahaan = preg_replace('/[^A-Za-z0-9\-]/', '_', strtolower($namaPerusahaan));
        $safeNamaPerusahaan = trim(preg_replace('/_+/', '_', $safeNamaPerusahaan), '_');
        $namaFilePermohonan = 'form_permohonan_' . $safeNamaPerusahaan . '_' . $pengajuan->id . '.docx';
        $templateProcessor->saveAs($folderPermohonan . DIRECTORY_SEPARATOR . $namaFilePermohonan);

        // Sinkronisasi file Word ke database record
        $pengajuan->file_permohonan = $namaFilePermohonan;
        $pengajuan->save();

        // F. REDIRECT AMAN KEMBALI KE HALAMAN AKTIVITAS USER
        return redirect('/aktivitas')
            ->with('success', 'Formulir berhasil disimpan! Silakan unduh draft Surat Permohonan, bubuhkan Kop Surat dan Tanda Tangan, lalu unggah kembali.');
    }

    public function storeLampiran(Request $request, $id)
    {
        $pengajuan = Pengajuan::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $statusNormalized = \App\Support\LsproType5Workflow::normalize($pengajuan->status);
        $isPerbaikanKelengkapan = $statusNormalized === 'perbaikan' && !empty($pengajuan->nama_tu);

        if (!in_array($statusNormalized, ['menunggu_lampiran', 'perjanjian_lampiran']) && !$isPerbaikanKelengkapan) {
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

        $formData = array_merge($existingFormData, $this->simpanLampiran($request, $id));
        $pengajuan->data_form = json_encode($formData);
        $pengajuan->save();

        if ($isDraft) {
            return redirect()
                ->route('pengajuan.lampiran', $id)
                ->with('success', 'Draft lampiran berhasil disimpan. Anda dapat melanjutkannya nanti.');
        }

        $pengajuan->transitionTo(
            'billing_2',
            'Klien telah mengunggah dokumen kelengkapan. Menunggu penerbitan Billing Audit Kecukupan.',
            Auth::id()
        );

        \App\Helpers\NotificationHelper::sendToRole('layanan', 'Menunggu Penerbitan Billing', 'Klien telah mengunggah dokumen kelengkapan untuk pengajuan #' . $pengajuan->id . '. Tahap selanjutnya: Penerbitan Billing Audit Kecukupan oleh bagian Administrasi/Keuangan.', 'info', $pengajuan->id);

        return redirect()
            ->route('aktivitas.index')
            ->with('success', 'Lampiran kelengkapan berhasil diunggah. Menunggu penerbitan Billing Audit dari Keuangan.');
    }

    public function uploadPermohonanTtd(Request $request, $id)
    {
        $pengajuan = Pengajuan::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'file_permohonan_ttd' => 'required|file|mimes:pdf|max:10240',
        ]);

        $folderPengajuan = 'permohonan/' . $pengajuan->id;
        Storage::disk('public')->makeDirectory($folderPengajuan);

        $noPermohonan = str_pad($pengajuan->id, 5, '0', STR_PAD_LEFT);
        $filenameTtd = 'surat_permohonan_ttd_permohonan_' . $noPermohonan . '.pdf';
        
        if ($pengajuan->file_permohonan_ttd && Storage::disk('public')->exists($pengajuan->file_permohonan_ttd)) {
            Storage::disk('public')->delete($pengajuan->file_permohonan_ttd);
        }
        Storage::disk('public')->putFileAs(
            $folderPengajuan,
            $request->file('file_permohonan_ttd'),
            $filenameTtd
        );
        $pengajuan->file_permohonan_ttd = $folderPengajuan . '/' . $filenameTtd;

        $pengajuan->transitionTo(
            'diajukan',
            'Surat Permohonan (dengan Kop Surat & Tanda Tangan) telah diterima. Menunggu Verifikasi Administrasi.',
            Auth::id()
        );
        $pengajuan->save();

        \App\Helpers\NotificationHelper::sendToRole('layanan', 'Surat Permohonan Diunggah', 'Klien telah mengunggah Surat Permohonan (Kop & TTD) untuk pengajuan #' . $pengajuan->id . '. Tahap selanjutnya: Tinjauan Permohonan oleh bagian Layanan.', 'info', $pengajuan->id);
        \App\Helpers\NotificationHelper::sendToUser(Auth::id(), 'Berkas Terkirim', 'Dokumen permohonan Anda telah berhasil dikirim. Menunggu verifikasi dari Administrasi.', 'success', $pengajuan->id);

        return redirect()
            ->route('aktivitas.index')
            ->with('success', 'Kop Surat dan Tanda Tangan berhasil diunggah! Berkas Anda sedang diproses oleh Tim Administrasi.');
    }


    public function setujuJadwal(Request $request, $id)
    {
        $pengajuan = Pengajuan::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        
        if ($request->input('is_setuju') == '1') {
            $pengajuan->is_jadwal_disetujui = true;
            $pengajuan->transitionTo(
                'billing_3',
                'Klien telah menyetujui jadwal Audit. Menunggu pembayaran Billing 3.',
                Auth::id()
            );
            $pengajuan->save();

            $settingsPath = storage_path('app/settings.json');
            $auditPrice = 15000000;
            
            // Cek apakah Admin sudah membuat RAB untuk Audit Kesesuaian
            $rabTotal = \App\Models\RabItem::where('pengajuan_id', $pengajuan->id)
                            ->where('kategori', 'like', '%Audit Kesesuaian%')
                            ->sum('tarif_pnbp_total');
                            
            if ($rabTotal > 0) {
                $auditPrice = $rabTotal;
            } elseif (file_exists($settingsPath)) {
                $settings = json_decode(file_get_contents($settingsPath), true);
                $auditPrice = $settings['billing_audit_price'] ?? 15000000;
            }

            // OTOMATIS TERBITKAN BILLING 2
            \App\Models\Invoice::create([
                'pengajuan_id' => $pengajuan->id,
                'invoice_number' => 'INV-' . time() . '-AUDIT',
                'invoice_date' => now(),
                'amount_total' => $auditPrice,
                'status' => 'unpaid',
                'due_date' => now()->addDays(7),
                'jenis_tagihan' => 'billing_3',
                'notes' => 'Invoice tahap BILLING 3 (Pelaksanaan Audit Lapangan & BDLT).',
            ]);

            \App\Helpers\NotificationHelper::sendToRole('layanan', 'Jadwal Disetujui', 'Klien telah menyetujui jadwal audit pengajuan #' . $pengajuan->id, 'success', $pengajuan->id);
            return redirect()->route('aktivitas.index')->with('success', 'Jadwal Audit disetujui. Silakan lanjut ke proses pembayaran (Billing 3).');
        } else {
            $pengajuan->is_jadwal_disetujui = false;
            $alasan = $request->input('alasan');
            $pengajuan->transitionTo(
                'proses_evaluasi',
                'Klien menolak jadwal audit dengan alasan: ' . $alasan,
                Auth::id()
            );
            $pengajuan->save();
            \App\Helpers\NotificationHelper::sendToRole('layanan', 'Jadwal Ditolak', 'Klien menolak jadwal audit pengajuan #' . $pengajuan->id . '. Alasan: ' . $alasan, 'danger', $pengajuan->id);
            return redirect()->route('aktivitas.index')->with('success', 'Penolakan jadwal telah dikirim ke Tim Audit.');
        }
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

    private function simpanLampiran(Request $request, $pengajuanId)
    {
        $uploaded = [];
        $tujuanFolder = storage_path('app/public/permohonan/lampiran');
        if (!file_exists($tujuanFolder)) {
            mkdir($tujuanFolder, 0755, true);
        }

        foreach ($this->lampiranFields() as $field => $meta) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $noPermohonan = str_pad($pengajuanId, 5, '0', STR_PAD_LEFT);
                $filename = 'lampiran_' . $field . '_permohonan_' . $noPermohonan . '.' . $file->getClientOriginalExtension();
                $file->move($tujuanFolder, $filename);
                $uploaded[$field] = $filename;
            }
        }
        return $uploaded;
    }

    public function cetakPerjanjian($id)
    {
        $pengajuan = Pengajuan::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        
        $templateCandidates = [
            storage_path('app/templates/Form_7.2-3_Perjanjian.docx'),
            storage_path('app/templates/Form 7.2-3_LS Pro - Perjanjian Sertifikasi.docx'),
            storage_path('app/templates/Form_7.2-3.docx'),
        ];

        $templatePath = collect($templateCandidates)->first(fn ($path) => file_exists($path));

        if (!$templatePath) {
            return back()->with('error', 'Template Form 7.2-3 (Perjanjian Sertifikasi) tidak ditemukan di server.');
        }

        $formData = $this->normalFormData($pengajuan);
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        foreach ($formData as $key => $value) {
            if (is_scalar($value)) {
                $templateProcessor->setValue($key, htmlspecialchars((string) $value));
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
        
        $templateProcessor->setValue('nama_perusahaan', htmlspecialchars($formData['nama_perusahaan'] ?? $pengajuan->user->nama_perusahaan ?? '-'));
        $templateProcessor->setValue('alamat_perusahaan', htmlspecialchars($formData['alamat_perusahaan'] ?? $formData['alamat_pabrik'] ?? $formData['alamat_kantor'] ?? '-'));
        $templateProcessor->setValue('nama_pemohon', htmlspecialchars($formData['nama_pemohon'] ?? $pengajuan->user->name ?? '-'));
        $templateProcessor->setValue('jabatan_pemohon', htmlspecialchars($formData['jabatan_pemohon'] ?? $formData['jabatan_penghubung'] ?? 'Pimpinan Perusahaan'));
        
        $templateProcessor->setValue('nama_produk', htmlspecialchars($formData['nama_produk'] ?? $formData['jenis_pupuk'] ?? '-'));
        $templateProcessor->setValue('no_sni', htmlspecialchars($formData['no_sni'] ?? $formData['nomor_sni'] ?? '-'));
        $templateProcessor->setValue('judul_sni', htmlspecialchars($formData['judul_sni'] ?? '-'));

        $folder = storage_path('app/public/perjanjian/' . $pengajuan->id);
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        $fileName = 'Form_7.2-3_Perjanjian_' . $pengajuan->id . '.docx';
        $savePath = $folder . DIRECTORY_SEPARATOR . $fileName;
        $templateProcessor->saveAs($savePath);

        return response()->download($savePath, $fileName);
    }

    public function uploadSurvailenDokumen(Request $request, $id)
    {
        return redirect()->back()->with('success', 'Dokumen Survailen berhasil diunggah.');
    }

    public function storeDraft(Request $request)
    {
        $draftId = $request->input('draft_id');
        $arrayDataForm = $request->except(['_token', 'is_ajax', 'draft_id']);
        
        $draft = null;
        if ($draftId) {
            $draft = Pengajuan::where('id', $draftId)->where('user_id', Auth::id())->first();
            if ($draft && $draft->status !== 'draft' && $draft->status !== 'perbaikan') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan sudah dikirim, tidak dapat menyimpan draft.'
                ]);
            }
        }

        $jenis = strtolower($request->jenis_sertifikasi) === 'resertifikasi' ? 'resertifikasi' : (strtolower($request->jenis_sertifikasi) === 'survailen' ? 'survailen' : 'sertifikasi');

        if (!$draft) {
            // Check if there's already an active draft for this type
            $draft = Pengajuan::where('user_id', Auth::id() ?? 1)
                ->where('status', 'draft')
                ->where('jenis_pengajuan', $jenis)
                ->first();

            if (!$draft) {
                $draft = new Pengajuan();
                $draft->user_id = Auth::id() ?? 1;
                $draft->jenis_pengajuan = $jenis;
            }
        }

        $draft->tahap = $request->tahap ?? '7.2';
        $draft->status = 'draft'; 
        $draft->data_form = json_encode($arrayDataForm); 
        $draft->save();
        $draftId = $draft->id;

        // Jika request AJAX (auto-save atau manual), kembalikan JSON
        if ($request->is_ajax || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success'  => true,
                'draft_id' => $draftId,
                'message'  => 'Draft berhasil disimpan.',
            ]);
        }

        // Jika manual save (form submit), redirect ke tab Draft
        return redirect()
            ->route('client.dashboard')
            ->with('success', 'Draft permohonan berhasil disimpan.');
    }

    public function destroyDraft($id)
    {
        $draft = Pengajuan::where('id', $id)
                          ->where('user_id', Auth::id())
                          ->where('status', 'draft')
                          ->firstOrFail();
                          
        $draft->delete();
        
        return redirect()->back()->with('success', 'Draft permohonan berhasil dihapus.');
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

        if ($request->hasFile('bukti_transfer')) {
            $file = $request->file('bukti_transfer');
            $noPermohonan = str_pad($invoice->pengajuan_id, 5, '0', STR_PAD_LEFT);
            $filename = 'bukti_bayar_billing_' . $invoice->id . '_permohonan_' . $noPermohonan . '.' . $file->getClientOriginalExtension();
            
            // Simpan ke disk public secara eksplisit untuk menghindari bug konfigurasi .env di hosting
            $path = $file->storeAs('bukti_pembayaran', $filename, 'public');
            
            // Simpan path relatif ke DB
            $invoice->file_bukti_bayar = 'bukti_pembayaran/' . $filename;
        }

        $invoice->status = 'pending_verification'; // Menunggu konfirmasi Administrasi
        $invoice->notes = 'Bukti pembayaran telah diunggah dan menunggu verifikasi Administrasi.';
        $invoice->save();

        \App\Helpers\NotificationHelper::sendToRole('layanan', 'Pembayaran Tagihan', 'Klien telah mengunggah bukti pembayaran untuk invoice #' . $invoice->invoice_number . '. Tahap selanjutnya: Verifikasi Pembayaran oleh bagian Administrasi/Keuangan.', 'info', $invoice->pengajuan_id);

        return redirect()->back()->with('success', 'Bukti pembayaran berhasil diunggah! Menunggu verifikasi.');
    }

    public function cetakInvoice($id)
    {
        $invoice = \App\Models\Invoice::where('id', $id)
            ->whereHas('pengajuan', function ($query) {
                $query->where('user_id', Auth::id());
            })->firstOrFail();

        return view('client.cetak_invoice', compact('invoice'));
    }



    public function formTindakanPerbaikan($id)
    {
        $pengajuan = Pengajuan::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        
        if ($pengajuan->status !== 'tindakan_perbaikan') {
            return redirect()->route('aktivitas.show', $id)->with('error', 'Status pengajuan tidak sedang memerlukan tindakan perbaikan.');
        }

        return view('client.form_tindakan_perbaikan', compact('pengajuan'));
    }

    public function uploadTindakanPerbaikan(Request $request, $id)
    {
        $pengajuan = Pengajuan::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'file_tindakan_perbaikan' => 'required|file|mimes:pdf,doc,docx,zip,rar|max:10240',
            'keterangan_perbaikan' => 'required|string'
        ]);

        if ($request->hasFile('file_tindakan_perbaikan')) {
            $file = $request->file('file_tindakan_perbaikan');
            $noPermohonan = str_pad($pengajuan->id, 5, '0', STR_PAD_LEFT);
            $filename = 'lks_tindakan_perbaikan_permohonan_' . $noPermohonan . '.' . $file->getClientOriginalExtension();
            
            $folder = 'public/tindakan_perbaikan/' . $pengajuan->id;
            $file->storeAs($folder, $filename);

            $formData = is_array($pengajuan->data_form) ? $pengajuan->data_form : (json_decode($pengajuan->data_form, true) ?? []);
            $formData['file_tindakan_perbaikan'] = $pengajuan->id . '/' . $filename;
            $formData['keterangan_perbaikan'] = $request->keterangan_perbaikan;
            
            $pengajuan->data_form = $formData;
            $pengajuan->transitionTo(
                'proses_audit', 
                'Klien telah mengunggah Laporan Tindakan Perbaikan: ' . $request->keterangan_perbaikan, 
                Auth::id()
            );
            $pengajuan->save();

            \App\Helpers\NotificationHelper::sendToRole('layanan', 'Tindakan Perbaikan Diunggah', 'Klien telah mengunggah Laporan Tindakan Perbaikan untuk pengajuan #' . $pengajuan->id, 'info', $pengajuan->id);
            
            return redirect()->route('aktivitas.show', $id)->with('success', 'Bukti Tindakan Perbaikan berhasil diunggah dan sedang dievaluasi oleh Tim Audit.');
        }

        return back()->with('error', 'Gagal mengunggah file.');
    }

    /**
     * Halaman Customer Service untuk Client
     */
    public function customerService()
    {
        return view('client.customer_service');
    }
}
