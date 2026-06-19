<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AktivitasController;
use App\Http\Controllers\BandingController;
use App\Http\Controllers\SertifikatController;
use App\Http\Middleware\CheckAdminRole;
use App\Models\Pengajuan;

/*
|--------------------------------------------------------------------------
| Web Routes - Aplikasi LSPro BBPM SDLP (Kementerian Pertanian)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// =========================================================================
// 🔒 RUTE GUEST (HANYA UNTUK USER YANG BELUM LOGIN)
// =========================================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'registerView'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// =========================================================================
// 🛡️ RUTE YANG WAJIB LOGIN (AUTH)
// =========================================================================
Route::middleware('auth')->group(function () {
    
    // Aksi Keluar Aplikasi
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // =====================================================================
    // ✉️ EMAIL VERIFICATION ROUTES
    // =====================================================================
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('client.dashboard');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Link verifikasi telah dikirim ulang ke email Anda.');
    })->middleware(['throttle:6,1'])->name('verification.send');

    // =====================================================================
    // 👤 MANAJEMEN PROFIL (BISA DIAKSES SEBELUM VERIFIKASI)
    // =====================================================================
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // =====================================================================
    // 👥 RUTE KLIEN (WAJIB VERIFIKASI EMAIL)
    // =====================================================================
    Route::middleware(['verified'])->group(function () {
        
        Route::get('/dashboard', function () {
            $user_id = auth()->id();
            $totalPengajuan = \App\Models\Pengajuan::where('user_id', $user_id)->count();
            $pengajuanAktif = \App\Models\Pengajuan::where('user_id', $user_id)->whereNotIn('status', ['selesai', 'ditolak'])->count();
            $sertifikatTerbit = \App\Models\Pengajuan::where('user_id', $user_id)->where('status', 'selesai')->count();
            $menungguPembayaran = \App\Models\Pengajuan::where('user_id', $user_id)->where('status', 'menunggu_pembayaran')->count();
            
            $recentPengajuan = \App\Models\Pengajuan::where('user_id', $user_id)->orderBy('created_at', 'desc')->take(5)->get();

            return view('client.beranda', compact('totalPengajuan', 'pengajuanAktif', 'sertifikatTerbit', 'menungguPembayaran', 'recentPengajuan'));
        })->name('client.dashboard');

        Route::redirect('/beranda', '/dashboard')->name('beranda');

        // =====================================================================
        // 🚀 HALAMAN AKTIVITAS (MULTI ROLE)
        // =====================================================================
        Route::get('/aktivitas', [AktivitasController::class, 'index'])->name('aktivitas.index');
        Route::get('/aktivitas/sertifikasi', [AktivitasController::class, 'sertifikasi'])->name('aktivitas.sertifikasi');
        Route::get('/aktivitas/resertifikasi', [AktivitasController::class, 'resertifikasi'])->name('aktivitas.resertifikasi');
        Route::get('/aktivitas/banding', [AktivitasController::class, 'banding'])->name('aktivitas.banding');
        Route::get('/aktivitas/keluhan', [AktivitasController::class, 'keluhan'])->name('aktivitas.keluhan');
        Route::get('/aktivitas/{pengajuan}/detail', [AktivitasController::class, 'show'])->name('aktivitas.show');
        Route::get('/aktivitas-evaluasi', [AktivitasController::class, 'evaluasi'])->name('aktivitas.evaluasi');
        
        // Billing Page Client
        Route::get('/billing', [PengajuanController::class, 'billing'])->name('billing.index');
        Route::post('/invoice/{id}/pay', [PengajuanController::class, 'payInvoice'])->name('invoice.pay');

        // Sertifikat
        Route::get('/sertifikat', [SertifikatController::class, 'index'])->name('sertifikat.index');
        
        // Customer Service (Client side)
        Route::get('/customer-service', function() {
            return view('client.customer_service');
        })->name('client.cs');
        
        // Chat API untuk Client
        Route::get('/chat/{group}', [\App\Http\Controllers\ChatController::class, 'getMessages'])->name('client.chat.messages');
        Route::post('/chat/{group}', [\App\Http\Controllers\ChatController::class, 'sendMessage'])->name('client.chat.send');

        // =====================================================================
        // 📋 PENGAJUAN (SERTIFIKASI, RESERTIFIKASI, SURVAILEN)
        // =====================================================================
        Route::get('/pengajuan', [PengajuanController::class, 'index'])->name('pengajuan.index');
        Route::get('/pengajuan/sertifikasi', [PengajuanController::class, 'pilihProduk'])->defaults('jenis', 'sertifikasi')->name('pengajuan.sertifikasi');
        Route::get('/pengajuan/resertifikasi', [PengajuanController::class, 'pilihProduk'])->defaults('jenis', 'resertifikasi')->name('pengajuan.resertifikasi');
        Route::get('/pengajuan/survailen', [PengajuanController::class, 'pilihProduk'])->defaults('jenis', 'survailen')->name('pengajuan.survailen');
        Route::get('/pengajuan/form', [PengajuanController::class, 'create'])->name('pengajuan.create');
        Route::post('/pengajuan/survailen/{id}/upload', [PengajuanController::class, 'uploadSurvailenDokumen'])->name('pengajuan.survailen.upload');
        Route::post('/pengajuan/store-draft', [PengajuanController::class, 'storeDraft'])->name('pengajuan.store_draft');
        
        // MULTI-STEP WIZARD (AUTO-SAVE)
        Route::get('/pengajuan/form/{step?}', [PengajuanController::class, 'createWizard'])->name('pengajuan.wizard');
        Route::post('/pengajuan/form/save', [PengajuanController::class, 'saveWizard'])->name('pengajuan.wizard.save');
        Route::post('/pengajuan/form/submit', [PengajuanController::class, 'submitWizard'])->name('pengajuan.wizard.submit');

        // ALUR KLAUSUL TAHAP 7.2
        Route::get('/pengajuan/pilih', [PengajuanController::class, 'pilihTahap'])->name('pengajuan.pilih');
        Route::get('/pengajuan/buat', [PengajuanController::class, 'create'])->name('pengajuan.buat');
        Route::post('/pengajuan/store', [PengajuanController::class, 'store'])->name('pengajuan.store');
        Route::get('/pengajuan/{id}/lampiran', [PengajuanController::class, 'lampiran'])->name('pengajuan.lampiran');
        Route::post('/pengajuan/{id}/lampiran', [PengajuanController::class, 'storeLampiran'])->name('pengajuan.lampiran.store');
        
        // UPLOAD PDF PERMOHONAN BERMETERAI OLEH CLIENT
        Route::post('/pengajuan/{id}/upload-permohonan', [PengajuanController::class, 'uploadPermohonanTtd'])->name('pengajuan.upload_permohonan');
        
        // =====================================================================
        // ⚖️ BANDING & LAPORAN
        // =====================================================================
        Route::get('/banding', [BandingController::class, 'index'])->name('banding.index');
        Route::post('/banding', [BandingController::class, 'store'])->name('banding.store');

    }); // End of Verified middleware

    // DOWNLOAD BERKAS KLIEN (Bisa diakses Admin/TU/Klien tanpa harus verified jika admin)
    Route::get('/pengajuan/download/{id}', function ($id) {
        $user = auth()->user();
        $role = trim(strtolower($user->role));

        if ($role === 'admin' || $role === 'tu' || str_contains($role, 'tu') || str_contains($role, 'admin')) {
            $pengajuan = Pengajuan::findOrFail($id);
        } else {
            $pengajuan = Pengajuan::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        }

        $filePath = storage_path('app/public/permohonan/' . $pengajuan->file_permohonan);

        if ($pengajuan->file_permohonan && file_exists($filePath)) {
            return response()->download($filePath, $pengajuan->file_permohonan);
        }

        return redirect()->back()->with('error', 'Berkas dokumen fisik gagal ditemukan di server penyimpanan.');
    })->name('pengajuan.download');

    // DOWNLOAD AUTO-GENERATE DOCX FORM 7.2-4 (Bisa diakses TU maupun Klien)
    Route::get('/pengajuan/{id}/download-form-724', [AdminController::class, 'downloadForm724'])->name('pengajuan.download724');

    // =====================================================================
    // 🔔 NOTIFIKASI (Bisa diakses tanpa harus verified sepenuhnya)
    // =====================================================================
    Route::get('/notifikasi', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifikasi.markAllRead');
    Route::post('/notifikasi/{id}/mark-read', [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifikasi.markRead');
    Route::delete('/notifikasi/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifikasi.destroy');

    // =====================================================================
    // 💼 JALUR PERAN: TU AS ADMIN 
    // =====================================================================
    Route::prefix('admin')->middleware(CheckAdminRole::class)->group(function () {
        
        // Dashboard Admin
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        
        // Form 7.2-4 Tampilkan Ceklis Kelengkapan Dokumen
        Route::get('/pengajuan/{id}/ceklis', [AdminController::class, 'cekKelengkapan'])->name('admin.ceklis_kelengkapan');
        Route::get('/pengajuan/{id}/ceklis_lama', [AdminController::class, 'cekKelengkapan'])->name('admin.ceklis');
        
        // PROSES SIMPAN EVALUASI CEKLIS (Action dari view form 7.2-4)
        Route::post('/pengajuan/{id}/terima-awal', [AdminController::class, 'terimaPengecekanAwal'])->name('admin.pengajuan.terima_awal');
        Route::post('/pengajuan/{id}/ceklis', [AdminController::class, 'prosesCeklis'])->name('admin.pengajuan.ceklis');

        // FITUR PENDUKUNG: Rute Cetak & Download Hasil Form Evaluasi 7.2-4
        Route::get('/pengajuan/{id}/download', [AdminController::class, 'downloadForm724'])->name('admin.pengajuan.download');

        // FITUR PENDUKUNG: Rute Penyerahan / Penerusan Berkas Kerja ke Bagian Lain
        Route::post('/pengajuan/{id}/teruskan', [AdminController::class, 'teruskan'])->name('admin.pengajuan.teruskan');

        // Kelola Survailen
        Route::get('/survailen', [AdminController::class, 'surveilanIndex'])->name('admin.survailen.index');
        Route::post('/survailen', [AdminController::class, 'surveilanStore'])->name('admin.survailen.store');
        Route::post('/survailen/{id}/kirim-notif', [AdminController::class, 'surveilanKirimNotif'])->name('admin.survailen.kirim_notif');
        Route::post('/survailen/{id}/proses-dokumen', [AdminController::class, 'surveilanProsesDokumen'])->name('admin.survailen.proses_dokumen');

        // Kelola Banding
        Route::get('/banding', [BandingController::class, 'adminIndex'])->name('admin.banding.index');
        Route::post('/banding/{id}/proses', [BandingController::class, 'prosesAdmin'])->name('admin.banding.proses');
        
        // Modul Baru Admin (Sesuai Sub Role)
        Route::get('/panel-tu', [AdminController::class, 'panelTU'])->name('admin.panel_tu');
        Route::get('/panel-keuangan', [AdminController::class, 'panelKeuangan'])->name('admin.panel_keuangan');
        Route::post('/invoice/{id}/verify', [AdminController::class, 'verifyPayment'])->name('admin.invoice.verify');
        Route::get('/customer-service', [AdminController::class, 'customerService'])->name('admin.cs');
        Route::get('/data-sampel', [AdminController::class, 'dataSampel'])->name('admin.data_sampel');
        Route::get('/penyerahan-sertifikat', [AdminController::class, 'penyerahanSertifikat'])->name('admin.penyerahan_sertifikat');
        Route::post('/penyerahan-sertifikat/{id}/kirim', [AdminController::class, 'kirimSertifikat'])->name('admin.penyerahan_sertifikat.kirim');
        Route::get('/audit-724', [AdminController::class, 'panelAudit724'])->name('admin.audit_724');
        Route::get('/audit-berkas', [AdminController::class, 'auditBerkas'])->name('admin.audit_berkas');
        Route::post('/audit-berkas/{id}/proses', [AdminController::class, 'prosesAuditBerkas'])->name('admin.audit_berkas.proses');
        
        // DUMMY ROUTES DENGAN DATA KONTEKSTUAL
        Route::get('/audit-kecukupan', function() { 
            $mockData = [
                ['id' => 'AUD-001', 'status' => '<span class="badge bg-warning text-dark">Pemeriksaan Berkas</span>', 'desc' => 'PT Semesta Agro Tbk - Dokumen ISO 9001 belum lengkap'],
                ['id' => 'AUD-002', 'status' => '<span class="badge bg-primary">Penjadwalan</span>', 'desc' => 'CV Bumi Indah Sejahtera - Menunggu konfirmasi auditor lapangan'],
                ['id' => 'AUD-003', 'status' => '<span class="badge bg-success">Selesai</span>', 'desc' => 'PT Tani Makmur - Seluruh berkas telah dinyatakan cukup']
            ];
            return view('admin.dummy', ['title' => 'Audit Kecukupan', 'desc' => 'Daftar audit kecukupan dokumen yang sedang berlangsung (Data Dummy)', 'mockData' => $mockData]); 
        })->name('admin.audit_kecukupan');
        
        Route::get('/hasil-lab', function() { 
            $mockData = [
                ['id' => 'LAB-2026-901', 'status' => '<span class="badge bg-success">Lulus Uji</span>', 'desc' => 'Pupuk Organik Padat (Kadar NPK memenuhi standar)'],
                ['id' => 'LAB-2026-902', 'status' => '<span class="badge bg-danger">Tidak Lulus</span>', 'desc' => 'Pupuk Urea (Kadar Biuret melebihi batas maksimal 1%)'],
                ['id' => 'LAB-2026-903', 'status' => '<span class="badge bg-warning text-dark">Sedang Diuji</span>', 'desc' => 'Pupuk NPK 15-15-15 (Pengujian spektofotometri tahap 2)']
            ];
            return view('admin.dummy', ['title' => 'Hasil Laboratorium', 'desc' => 'Daftar hasil laboratorium terintegrasi dengan LIMS (Data Dummy)', 'mockData' => $mockData]); 
        })->name('admin.hasil_lab');

        // Chat Internal
        Route::get('/chat', [\App\Http\Controllers\ChatController::class, 'index'])->name('admin.chat.index');
        Route::get('/chat/{group}', [\App\Http\Controllers\ChatController::class, 'getMessages'])->name('admin.chat.messages');
        Route::post('/chat/{group}', [\App\Http\Controllers\ChatController::class, 'sendMessage'])->name('admin.chat.send');
    });

    // =====================================================================
    // 👑 SUPERADMIN (MANAJEMEN USER)
    // =====================================================================
    Route::prefix('superadmin')->middleware(CheckAdminRole::class)->group(function () {
        Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('superadmin.users.index');
        Route::get('/users/create', [\App\Http\Controllers\UserController::class, 'create'])->name('superadmin.users.create');
        Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('superadmin.users.store');
        Route::get('/users/{user}/edit', [\App\Http\Controllers\UserController::class, 'edit'])->name('superadmin.users.edit');
        Route::put('/users/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('superadmin.users.update');
        Route::patch('/users/{user}/toggle-status', [\App\Http\Controllers\UserController::class, 'toggleStatus'])->name('superadmin.users.toggle_status');
        Route::patch('/users/{user}/reset-password', [\App\Http\Controllers\UserController::class, 'resetPassword'])->name('superadmin.users.reset_password');
        Route::delete('/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('superadmin.users.destroy');
    });

    // Rute Cadangan TU Berdasarkan Role Tambahan
    Route::prefix('tu')->middleware(CheckAdminRole::class)->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('tu.dashboard');
        Route::get('/evaluasi/{id}', [AdminController::class, 'cekKelengkapan'])->name('tu.ceklis');
        Route::post('/evaluasi/{id}/proses', [AdminController::class, 'prosesCeklis'])->name('tu.proses_ceklis');
    });

});
