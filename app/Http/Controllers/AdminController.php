<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengajuan;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Tampilkan Dashboard Admin/TU
     */
    public function index()
    {
        $totalPengajuan = Pengajuan::count();
        $pengajuanBaru = Pengajuan::where('status', 'diajukan')->count();
        
        return view('admin.dashboard', compact('totalPengajuan', 'pengajuanBaru'));
    }

    /**
     * Tampilkan halaman Form Ceklis Kelengkapan Dokumen (7.2-4)
     * FIX: Membongkar data_form JSON agar bisa tampil di halaman Web
     */
    public function cekKelengkapan($id)
    {
        $pengajuan = Pengajuan::with('user')->findOrFail($id);
        
        // Bongkar isi kolom JSON data_form menjadi array PHP
        $formData = $pengajuan->data_form;
        if (is_string($formData)) {
            $formData = json_decode($formData, true) ?? [];
        }

        // Kirim $pengajuan dan $formData ke halaman web (view)
        return view('admin.ceklis', compact('pengajuan', 'formData'));
    }

    /**
     * Proses Simpan Hasil Evaluasi dari halaman Ceklis 7.2-4
     */
    public function prosesCeklis(Request $request, $id)
    {
        $request->validate([
            'kesimpulan' => 'required|in:lengkap,perbaikan'
        ]);

        $pengajuan = Pengajuan::findOrFail($id);
        
        $pengajuan->status = $request->kesimpulan; 
        $pengajuan->catatan = $request->catatan;
        $pengajuan->nama_tu = $request->nama_tu;
        
        if ($request->has('ceklis')) {
            $pengajuan->ceklis_dokumen = json_encode($request->ceklis);
        }
        
        $pengajuan->save();

        return back()->with('success', 'Hasil evaluasi ceklis dokumen Form 7.2-4 berhasil disimpan!');
    }

    /**
     * Auto-Generate & Download File .docx Form 7.2-4
     * FIX: Membongkar data_form JSON agar masuk ke template Word
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

        // =====================================================================
        // 🛠️ BONGKAR KOLOM JSON 'data_form' (SESUAI DATABASE ASLI ANDA)
        // =====================================================================
        $formData = $pengajuan->data_form;
        if (is_string($formData)) {
            $formData = json_decode($formData, true) ?? [];
        }

        // 1. Ambil Nama Pemohon (deteksi key JSON 'nama_klien' atau 'nama_pemohon')
        $namaPemohon = $formData['nama_klien'] 
            ?? $formData['nama_pemohon'] 
            ?? $pengajuan->user->name 
            ?? 'Klien';

        // 2. Nomor Permohonan 5 Digit (Contoh: #00010)
        $nomorPermohonan = str_pad($pengajuan->id, 5, '0', STR_PAD_LEFT);

        // 3. Ambil Alamat dari JSON data_form
        $alamat = $formData['alamat'] 
            ?? $formData['alamat_perusahaan'] 
            ?? $pengajuan->user->alamat 
            ?? 'Bogor'; // default backup jika kosong

        // 4. Ambil Jenis Pupuk (Di database Anda namanya 'nama_produk' atau 'jenis_pupuk')
        $jenisPupuk = $formData['nama_produk'] 
            ?? $formData['jenis_pupuk'] 
            ?? $formData['jenis_produk'] 
            ?? '-';

        // 5. Ambil Merek (Di database Anda namanya 'merek' atau 'merek_dagang')
        $merek = $formData['merek'] 
            ?? $formData['merek_dagang'] 
            ?? '-';

        // 6. Ambil Nomor SNI (Di database Anda namanya 'nomor_sni', 'no_sni', atau 'judul_sni')
        $noSni = $formData['nomor_sni'] 
            ?? $formData['no_sni'] 
            ?? $formData['judul_sni'] 
            ?? '-';

        // Inject data ke file Word Template
        $templateProcessor->setValue('nama_pemohon', $namaPemohon);
        $templateProcessor->setValue('nomor_permohonan', $nomorPermohonan);
        $templateProcessor->setValue('alamat_pemohon', $alamat);
        $templateProcessor->setValue('jenis_pupuk', $jenisPupuk);
        $templateProcessor->setValue('merek', $merek);
        $templateProcessor->setValue('no_sni', $noSni);

        // =====================================================================
        // 📊 LOOPING DATA MATRIKS CEKLIS TABEL (16 ITEM DOKUMEN)
        // =====================================================================
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

        // Format Coretan Kesimpulan Berdasarkan Status
        if ($pengajuan->status == 'lengkap') {
            $templateProcessor->setValue('kesimpulan_teks', 'Lengkap / <s>Tidak Lengkap</s>');
        } elseif ($pengajuan->status == 'perbaikan') {
            $templateProcessor->setValue('kesimpulan_teks', '<s>Lengkap</s> / Tidak Lengkap');
        } else {
            $templateProcessor->setValue('kesimpulan_teks', 'Lengkap / Tidak Lengkap');
        }

        Carbon::setLocale('id');
        $templateProcessor->setValue('tanggal', Carbon::parse($pengajuan->updated_at)->translatedFormat('d F Y'));
        $templateProcessor->setValue('nama_tu', $pengajuan->nama_tu ?? 'Petugas Tata Usaha');

        // =====================================================================
        // 💾 PROSES DOWNLOAD
        // =====================================================================
        $cleanName = preg_replace('/[^A-Za-z0-9\-]/', '_', $namaPemohon);
        $fileName = 'Form_7.2-4_Evaluasi_' . $cleanName . '.docx';
        
        $tempDirectory = storage_path('app/temp');
        if (!file_exists($tempDirectory)) {
            mkdir($tempDirectory, 0755, true);
        }

        $tempPath = $tempDirectory . DIRECTORY_SEPARATOR . $fileName;
        $templateProcessor->saveAs($tempPath);
        
        return response()->download($tempPath)->deleteFileAfterSend(true);
    }
}