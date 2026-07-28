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

use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
});

Route::get('/test-sistem', function() {
    return 'BERHASIL! File web.php Anda sukses terupdate dan terbaca oleh server!';
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

// =====================================================================
// ✉️ EMAIL VERIFICATION ROUTES (BISA DIAKSES TANPA LOGIN)
// =====================================================================
Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Http\Request $request, $id, $hash) {
    $user = \App\Models\User::find($id);

    if (! $user) {
        abort(404);
    }

    if (! hash_equals(sha1($user->getEmailForVerification()), (string) $hash)) {
        abort(403, 'Link verifikasi tidak valid atau sudah kadaluarsa.');
    }

    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new \Illuminate\Auth\Events\Verified($user));
    }

    // Auto-login user setelah verifikasi berhasil
    \Illuminate\Support\Facades\Auth::login($user);

    return redirect('/dashboard')->with('success', 'Email berhasil diverifikasi!');
})->middleware(['signed'])->name('verification.verify');

// =========================================================================
// 🛡️ RUTE YANG WAJIB LOGIN (AUTH)
// =========================================================================
Route::middleware('auth')->group(function () {
    
    // Aksi Keluar Aplikasi
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

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
    Route::middleware(['auth', 'verified'])->group(function () {
        
        Route::get('/dashboard', function () {
            $user_id = auth()->id();
            $totalPengajuan = \App\Models\Pengajuan::where('user_id', $user_id)->count();
            $pengajuanAktif = \App\Models\Pengajuan::where('user_id', $user_id)->whereNotIn('status', ['selesai', 'ditolak'])->count();
            $sertifikatTerbit = \App\Models\Pengajuan::where('user_id', $user_id)->where('status', 'selesai')->count();
            $menungguPembayaran = \App\Models\Pengajuan::where('user_id', $user_id)->where('status', 'menunggu_pembayaran')->count();
            
            $recentPengajuan = \App\Models\Pengajuan::where('user_id', $user_id)->orderBy('created_at', 'desc')->take(5)->get();

            return \Inertia\Inertia::render('Client/Beranda', compact('totalPengajuan', 'pengajuanAktif', 'sertifikatTerbit', 'menungguPembayaran', 'recentPengajuan'));
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
        Route::get('/invoice/{id}/cetak', [PengajuanController::class, 'cetakInvoice'])->name('invoice.cetak');

        // Customer Service Page Client
        Route::get('/client/cs', [PengajuanController::class, 'customerService'])->name('client.cs');

        // Sertifikat
        Route::get('/sertifikat', [SertifikatController::class, 'index'])->name('sertifikat.index');
        Route::get('/sertifikat/{id}/cetak', [SertifikatController::class, 'cetak'])->name('sertifikat.cetak');
        
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
        Route::get('/pengajuan/sertifikasi', [PengajuanController::class, 'create'])->defaults('jenis_pengajuan', 'Sertifikasi')->name('pengajuan.sertifikasi');
        Route::get('/pengajuan/resertifikasi', [PengajuanController::class, 'create'])->defaults('jenis_pengajuan', 'Resertifikasi')->name('pengajuan.resertifikasi');
        Route::get('/pengajuan/survailen', [PengajuanController::class, 'create'])->defaults('jenis_pengajuan', 'Survailen')->name('pengajuan.survailen');
        Route::get('/pengajuan/form', [PengajuanController::class, 'create'])->name('pengajuan.create');
        Route::get('/pengajuan/{id}/perjanjian/cetak', [PengajuanController::class, 'cetakPerjanjian'])->name('pengajuan.perjanjian.cetak');
        Route::post('/pengajuan/survailen/{id}/upload', [PengajuanController::class, 'uploadSurvailenDokumen'])->name('pengajuan.survailen.upload');
        Route::post('/pengajuan/draft', [PengajuanController::class, 'storeDraft'])->name('pengajuan.store_draft');
        Route::delete('/pengajuan/draft/{id}', [PengajuanController::class, 'destroyDraft'])->name('pengajuan.destroy_draft');
        


        // ALUR KLAUSUL TAHAP 7.2
        Route::get('/pengajuan/buat', [PengajuanController::class, 'create'])->name('pengajuan.buat');
        Route::post('/pengajuan/store', [PengajuanController::class, 'store'])->name('pengajuan.store');
        Route::get('/pengajuan/{id}/lampiran', [PengajuanController::class, 'lampiran'])->name('pengajuan.lampiran');
        Route::post('/pengajuan/{id}/lampiran', [PengajuanController::class, 'storeLampiran'])->name('pengajuan.lampiran.store');
        

        // UPLOAD PDF PERMOHONAN BERMETERAI OLEH CLIENT
        Route::post('/pengajuan/{id}/upload-permohonan', [PengajuanController::class, 'uploadPermohonanTtd'])->name('pengajuan.upload_permohonan');
        
        // TINDAKAN KLIEN (JADWAL, LKS)
        Route::post('/pengajuan/{id}/setuju-jadwal', [PengajuanController::class, 'setujuJadwal'])->name('pengajuan.setuju_jadwal');
        // Client tidak boleh upload LHP, ini wewenang admin:
        // Route::get('/pengajuan/{id}/upload-lhp', [PengajuanController::class, 'formLhp'])->name('pengajuan.form_lhp');
        // Route::post('/pengajuan/{id}/upload-lhp', [PengajuanController::class, 'uploadLhp'])->name('pengajuan.upload_lhp');
        
        Route::get('/pengajuan/{id}/tindakan-perbaikan', [PengajuanController::class, 'formTindakanPerbaikan'])->name('pengajuan.tindakan_perbaikan');
        Route::post('/pengajuan/{id}/tindakan-perbaikan', [PengajuanController::class, 'uploadTindakanPerbaikan'])->name('pengajuan.tindakan_perbaikan.upload');
        
        // =====================================================================
        // ⚖️ BANDING & LAPORAN
        // =====================================================================
        Route::get('/banding', [BandingController::class, 'index'])->name('banding.index');
        Route::post('/banding', [BandingController::class, 'store'])->name('banding.store');

    }); // End of Verified middleware

    // DOWNLOAD BERKAS KLIEN (Bisa diakses Admin/Administrasi/Klien tanpa harus verified jika admin)
    Route::get('/pengajuan/download-draft/{id}', function ($id) {
        $user = auth()->user();
        $role = trim(strtolower($user->role));

        if ($role === 'admin' || $role === 'tu' || str_contains($role, 'tu') || str_contains($role, 'admin') || str_contains($role, 'layanan')) {
            $pengajuan = Pengajuan::findOrFail($id);
        } else {
            $pengajuan = Pengajuan::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        }

        $filePath = storage_path('app/public/permohonan/' . $pengajuan->file_permohonan);

        if ($pengajuan->file_permohonan && file_exists($filePath)) {
            return response()->download($filePath, $pengajuan->file_permohonan);
        }

        return redirect()->back()->with('error', 'Draf dokumen tidak ditemukan.');
    })->name('pengajuan.download_draft');

    Route::get('/pengajuan/download/{id}', function ($id) {
        $user = auth()->user();
        $role = trim(strtolower($user->role));

        if ($role === 'admin' || $role === 'tu' || str_contains($role, 'tu') || str_contains($role, 'admin') || str_contains($role, 'layanan')) {
            $pengajuan = Pengajuan::findOrFail($id);
        } else {
            $pengajuan = Pengajuan::where('id', $id)->where('user_id', $user->id)->firstOrFail();
        }

        if ($pengajuan->file_permohonan_ttd && file_exists(storage_path('app/public/' . $pengajuan->file_permohonan_ttd))) {
            return response()->download(storage_path('app/public/' . $pengajuan->file_permohonan_ttd), 'Form_7.2-1_Permohonan_TTD.pdf');
        }

        $filePath = storage_path('app/public/permohonan/' . $pengajuan->file_permohonan);

        if ($pengajuan->file_permohonan && file_exists($filePath)) {
            return response()->download($filePath, $pengajuan->file_permohonan);
        }

        return redirect()->back()->with('error', 'Berkas dokumen fisik gagal ditemukan di server penyimpanan.');
    })->name('pengajuan.download');

    // DOWNLOAD AUTO-GENERATE DOCX FORM 7.2-4 (Bisa diakses Administrasi maupun Klien)
    Route::get('/pengajuan/{id}/download-form-724', [AdminController::class, 'downloadForm724'])->name('pengajuan.download724');

    // =====================================================================
    // 🔔 NOTIFIKASI (Bisa diakses tanpa harus verified sepenuhnya)
    // =====================================================================
    Route::get('/notifikasi', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifikasi.markAllRead');
    Route::post('/notifikasi/{id}/mark-read', [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifikasi.markRead');
    Route::delete('/notifikasi/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifikasi.destroy');

    // =====================================================================
    // 💼 JALUR PERAN: Administrasi AS ADMIN 
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

        // FORM DINAMIS: Pengisian form oleh Admin/Auditor dari Form Builder
        Route::get('/pengajuan/{id}/isi-form/{form_type}', [AdminController::class, 'isiFormDinamis'])->name('admin.pengajuan.isi_form');
        Route::post('/pengajuan/{id}/isi-form/{form_type}', [AdminController::class, 'simpanFormDinamis'])->name('admin.pengajuan.simpan_form');

        // FITUR PENDUKUNG: Rute Penyerahan / Penerusan Berkas Kerja ke Bagian Lain
        Route::post('/pengajuan/{id}/teruskan', [AdminController::class, 'teruskan'])->name('admin.pengajuan.teruskan');
        Route::post('/pengajuan/{id}/create-invoice', [AdminController::class, 'createInvoice'])->name('admin.pengajuan.create_invoice');
        Route::get('/dummy-teruskan', [AdminController::class, 'dummyTeruskan'])->name('admin.dummy.teruskan');

        // JADWAL AUDIT & BILLING LAB
        Route::post('/pengajuan/{id}/set-jadwal', [AdminController::class, 'setJadwalAudit'])->name('admin.pengajuan.set_jadwal');
        Route::post('/pengajuan/{id}/terbitkan-billing-lab', [AdminController::class, 'terbitkanBillingLab'])->name('admin.pengajuan.terbitkan_billing_lab');

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
        Route::get('/penjadwalan-audit', [AdminController::class, 'penjadwalanAudit'])->name('admin.penjadwalan_audit');
        Route::get('/panel-keuangan', [AdminController::class, 'panelKeuangan'])->name('admin.panel_keuangan');
        Route::post('/invoice/{id}/upload-billing', [AdminController::class, 'uploadBilling'])->name('admin.invoice.upload_billing');
        Route::put('/invoice/{id}/update', [AdminController::class, 'updateInvoice'])->name('admin.invoice.update');
        Route::delete('/invoice/{id}/destroy', [AdminController::class, 'destroyInvoice'])->name('admin.invoice.destroy');
        Route::post('/invoice/{id}/verify', [AdminController::class, 'verifyPayment'])->name('admin.invoice.verify');
        Route::post('/invoice/{id}/upload-kwitansi', [AdminController::class, 'uploadKwitansi'])->name('admin.invoice.upload_kwitansi');
        
        // Pembuatan RAB / Rencana Anggaran Biaya
        Route::get('/pengajuan/{id}/rab/create', [AdminController::class, 'createRab'])->name('admin.rab.create');
        Route::post('/pengajuan/{id}/rab', [AdminController::class, 'storeRab'])->name('admin.rab.store');
        Route::get('/customer-service', [AdminController::class, 'customerService'])->name('admin.cs');
        Route::get('/data-sampel', [AdminController::class, 'dataSampel'])->name('admin.data_sampel');
        Route::get('/penyerahan-sertifikat', [AdminController::class, 'penyerahanSertifikat'])->name('admin.penyerahan_sertifikat');
        Route::post('/penyerahan-sertifikat/{id}/kirim', [AdminController::class, 'kirimSertifikat'])->name('admin.penyerahan_sertifikat.kirim');

        // Audit Kesesuaian (Proses Audit Lapangan/Berkas)
        Route::get('/audit-berkas', [AdminController::class, 'auditBerkas'])->name('admin.audit_berkas');
        Route::post('/audit-berkas/{id}/proses', [AdminController::class, 'prosesAuditBerkas'])->name('admin.audit_berkas.proses');
        
        // --- ROUTES LAYANAN & STANDAR (Placeholder) ---
        Route::get('/layanan/evaluasi-dokumen', [AdminController::class, 'layananEvaluasiDokumen'])->name('admin.layanan.evaluasi_dokumen');
        Route::get('/layanan/penugasan-tim', [AdminController::class, 'layananPenugasanTim'])->name('admin.layanan.penugasan_tim');
        Route::get('/layanan/evaluasi-laporan', [AdminController::class, 'layananEvaluasiLaporan'])->name('admin.layanan.evaluasi_laporan');
        Route::get('/layanan/komisi-teknis', [AdminController::class, 'layananKomisiTeknis'])->name('admin.layanan.komisi_teknis');

        // --- ROUTES TIM AUDIT (Placeholder) ---
        Route::get('/audit/laporan-ketidaksesuaian', [AdminController::class, 'auditLKS'])->name('admin.audit.lks');
        
        Route::get('/hasil-lab', [AdminController::class, 'auditHasilLab'])->name('admin.hasil_lab');
        
        // LHP (Laporan Hasil Pengujian)
        Route::get('/pengajuan/{id}/upload-lhp', [AdminController::class, 'formLhp'])->name('admin.pengajuan.form_lhp');
        Route::post('/pengajuan/{id}/upload-lhp', [AdminController::class, 'uploadLhp'])->name('admin.pengajuan.upload_lhp');
        Route::get('/pengajuan/{id}/tinjau-lhp', [AdminController::class, 'tinjauLhp'])->name('admin.pengajuan.tinjau_lhp');
        Route::post('/pengajuan/{id}/tinjau-lhp', [AdminController::class, 'prosesTinjauLhp'])->name('admin.pengajuan.proses_tinjau_lhp');
        // Chat Internal
        Route::get('/chat', [\App\Http\Controllers\ChatController::class, 'index'])->name('admin.chat.index');
        Route::get('/chat/{group}', [\App\Http\Controllers\ChatController::class, 'getMessages'])->name('admin.chat.messages');
        Route::post('/chat/{group}', [\App\Http\Controllers\ChatController::class, 'sendMessage'])->name('admin.chat.send');
    });

    // =====================================================================
    // 🧪 PPC (PETUGAS PENGAMBIL CONTOH)
    // =====================================================================
    Route::prefix('ppc')->middleware(CheckAdminRole::class)->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\PpcController::class, 'dashboard'])->name('ppc.dashboard');
    });

    // =====================================================================
    // 👑 SUPERADMIN (MANAJEMEN USER)
    // =====================================================================
    Route::prefix('superadmin')->middleware(CheckAdminRole::class)->group(function () {
        Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('superadmin.users.index');
        
        // Pengaturan Sistem & Billing
        // Halaman Pengaturan Sistem (Sekarang diarahkan ke Form Builder)
        Route::get('/settings', [\App\Http\Controllers\Superadmin\FormBuilderController::class, 'index'])->name('superadmin.settings');
        // Route upload template baru untuk spesifik form_type
        Route::post('/form-builder/{form_type}/upload-template', [\App\Http\Controllers\Superadmin\FormBuilderController::class, 'uploadTemplate'])->name('superadmin.form_builder.upload_template');        
        // Form Builder
        Route::resource('form-builder', \App\Http\Controllers\Superadmin\FormBuilderController::class)->names('superadmin.form_builder');

        // --- ROUTES KETUA LSPRO / SUPERADMIN (Placeholder) ---
        Route::get('/persetujuan-penugasan', [AdminController::class, 'superadminPersetujuanPenugasan'])->name('admin.superadmin.persetujuan_penugasan');
        Route::get('/pengesahan-sertifikat', [AdminController::class, 'superadminPengesahanSertifikat'])->name('admin.superadmin.pengesahan_sertifikat');
        
        Route::get('/users/create', [\App\Http\Controllers\UserController::class, 'create'])->name('superadmin.users.create');
        Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('superadmin.users.store');
        Route::get('/users/{user}/edit', [\App\Http\Controllers\UserController::class, 'edit'])->name('superadmin.users.edit');
        Route::put('/users/{user}', [\App\Http\Controllers\UserController::class, 'update'])->name('superadmin.users.update');
        Route::patch('/users/{user}/toggle-status', [\App\Http\Controllers\UserController::class, 'toggleStatus'])->name('superadmin.users.toggle_status');
        Route::patch('/users/{user}/reset-password', [\App\Http\Controllers\UserController::class, 'resetPassword'])->name('superadmin.users.reset_password');
        Route::delete('/users/{user}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('superadmin.users.destroy');
    });

    // Rute Cadangan Administrasi Berdasarkan Role Tambahan
    Route::prefix('tu')->middleware(CheckAdminRole::class)->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('tu.dashboard');
        Route::get('/evaluasi/{id}', [AdminController::class, 'cekKelengkapan'])->name('tu.ceklis');
        Route::post('/evaluasi/{id}/proses', [AdminController::class, 'prosesCeklis'])->name('tu.proses_ceklis');
    });

});

// Rute Download File (Pakai /unduh/ agar tidak dicegat LiteSpeed)
Route::get('/unduh/{path}', function ($path) {
    $fullPath = storage_path('app/public/' . $path);
    if (!file_exists($fullPath)) {
        return "FILE TIDAK DITEMUKAN. Sistem mencari di: <b>" . $fullPath . "</b>";
    }
    
    // Gunakan helper File dari Illuminate untuk memastikan MIME type yang tepat
    $mimeType = \Illuminate\Support\Facades\File::mimeType($fullPath);
    
    return response()->file($fullPath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'no-cache, no-store, must-revalidate',
        'Pragma' => 'no-cache',
        'Expires' => '0'
    ]);
})->where('path', '.*')->name('unduh');
