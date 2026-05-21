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
        return view('pengajuan.create_otomatis', ['tahap' => '7.2']); 
    }

    /**
     * 2. Tampilkan Form Input Data (Tahap 1)
     */
    public function create(Request $request)
    {
        $tahap = $request->get('tahap', '7.2');
        return view('pengajuan.create_otomatis', compact('tahap'));
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
            'sni_acuan'                 => 'required|string',
            'kapasitas_produksi'        => 'required|string',
            'standar_smm'               => 'required|string',
            
            // Tenaga Kerja
            'total_tk'                  => 'required|integer',
            'tk_produksi'               => 'required|integer',
            'tk_mutu'                   => 'required|integer',
            'tk_staf'                   => 'required|integer',
            'tk_nonstaf'                => 'required|integer',
            
            // File Lampiran Dokumen & Foto (Masing-masing Maks 5MB)
            'akte_perusahaan'           => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'izin_usaha_industri'       => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'siup_tdup'                 => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'sertifikat_merek'          => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'bukti_importir'            => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'struktur_organisasi'       => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'alur_produksi_mutu'        => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'daftar_alat_mesin'         => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'daftar_material_kritis'    => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'daftar_alat_uji_kalibrasi' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'pedoman_mutu'              => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'daftar_prosedur_ik'        => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'ilustrasi_tanda_sni'       => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120',
            'foto_produk'               => 'required|file|mimes:jpg,jpeg,png|max:5120',
        ]);

        // B. PROSES UPLOAD MULTIPLE FILE LAMPIRAN
        $uploadedFiles = [];
        $fileFields = [
            'akte_perusahaan', 'izin_usaha_industri', 'siup_tdup', 'sertifikat_merek', 
            'bukti_importir', 'struktur_organisasi', 'alur_produksi_mutu', 'daftar_alat_mesin', 
            'daftar_material_kritis', 'daftar_alat_uji_kalibrasi', 'pedoman_mutu', 
            'daftar_prosedur_ik', 'ilustrasi_tanda_sni', 'foto_produk'
        ];

        $tujuanFolder = storage_path('app/public/permohonan/lampiran');
        if (!file_exists($tujuanFolder)) {
            mkdir($tujuanFolder, 0755, true);
        }

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $namaBaru = $field . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($tujuanFolder, $namaBaru);
                $uploadedFiles[$field] = $namaBaru;
            } else {
                $uploadedFiles[$field] = null;
            }
        }

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
            'sni_acuan'                 => $request->sni_acuan,
            'kapasitas_produksi'        => $request->kapasitas_produksi,
            'standar_smm'               => $request->standar_smm,
            
            // Alokasi Tenaga Kerja
            'total_tk'                  => $request->total_tk,
            'tk_produksi'               => $request->tk_produksi,
            'tk_mutu'                   => $request->tk_mutu,
            'tk_staf'                   => $request->tk_staf,
            'tk_nonstaf'                => $request->tk_nonstaf,
            
            'lampiran'                  => $uploadedFiles
        ];

        // D. SIMPAN DATA KE DATABASE
        $pengajuan = new Pengajuan();
        $pengajuan->user_id = Auth::id() ?? 1;
        $pengajuan->tahap = $request->tahap ?? '7.2';
        $pengajuan->jenis_pengajuan = 'Sertifikasi';
        $pengajuan->status = 'Diajukan'; 
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
        $templateProcessor->setValue('status_pemohon', '-'); 
        
        // --- 2. IDENTITAS PENGHUBUNG & PERUSAHAAN ---
        $templateProcessor->setValue('nama_penghubung', $request->nama_penghubung ?? '-');
        $templateProcessor->setValue('jabatan_penghubung', '-');
        $templateProcessor->setValue('nama_perusahaan', $request->nama_perusahaan ?? '-');
        $templateProcessor->setValue('alamat_perusahaan', $request->alamat_kantor ?? '-'); 
        $templateProcessor->setValue('kota_perusahaan', '-');
        $templateProcessor->setValue('provinsi_perusahaan', '-');
        $templateProcessor->setValue('telp_perusahaan', $request->telp_pemohon ?? '-');
        $templateProcessor->setValue('hp_penghubung', $request->hp_pemohon ?? '-');
        $templateProcessor->setValue('email_penghubung', '-');

        // --- 3. LEGALITAS & PABRIK ---
        $templateProcessor->setValue('badan_hukum', '-');
        $templateProcessor->setValue('alamat_kantor', $request->alamat_kantor ?? '-');
        $templateProcessor->setValue('kota_kantor', '-');
        $templateProcessor->setValue('provinsi_kantor', '-');
        $templateProcessor->setValue('telp_kantor', $request->telp_pemohon ?? '-');
        $templateProcessor->setValue('email_kantor', '-');
        $templateProcessor->setValue('alamat_pabrik', $request->alamat_pabrik ?? '-');
        $templateProcessor->setValue('kota_pabrik', '-');
        $templateProcessor->setValue('provinsi_pabrik', '-');
        $templateProcessor->setValue('telp_pabrik', '-');
        $templateProcessor->setValue('email_pabrik', '-');

        // --- 4. IMPORTIR & LAIN-LAIN ---
        $templateProcessor->setValue('nama_importir', '-');
        $templateProcessor->setValue('alamat_importir', '-');
        $templateProcessor->setValue('api_importir', '-');
        $templateProcessor->setValue('bahasa_pabrik', '-');
        $templateProcessor->setValue('penerjemah_pabrik', '-');
        $templateProcessor->setValue('jarak_pabrik', '-');
        $templateProcessor->setValue('waktu_pabrik', '-');

        // --- 5. DATA PRODUK PUPUK ---
        $templateProcessor->setValue('nama_pupuk', $request->nama_produk ?? '-');
        $templateProcessor->setValue('judul_sni', 'SNI Pupuk Terdaftar');
        $templateProcessor->setValue('no_sni', $request->sni_acuan ?? '-');
        $templateProcessor->setValue('merek_pupuk', $request->merek_produk ?? '-');
        $templateProcessor->setValue('jenis_pupuk', $request->tipe_produk ?? '-');
        $templateProcessor->setValue('asal_pabrik', $request->nama_perusahaan ?? '-');
        $templateProcessor->setValue('status_produk', '-');
        $templateProcessor->setValue('foto_produk', 'Terlampir pada berkas terpisah');

        // --- 6. ORGANISASI & SMM ---
        $templateProcessor->setValue('nama_wmm', '-');
        $templateProcessor->setValue('telp_wmm', '-');
        $templateProcessor->setValue('hp_wmm', '-');
        $templateProcessor->setValue('email_wmm', '-');
        $templateProcessor->setValue('jumlah_lini', '-');
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
}