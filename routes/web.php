<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PengajuanController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AktivitasController;
use App\Models\Pengajuan;

/*
|--------------------------------------------------------------------------
| Web Routes - Aplikasi LSPro BBPM SDLP (Kementerian Pertanian)
|--------------------------------------------------------------------------
*/
// ... rute lainnya ...
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
    // ⚙️ PENGATURAN PROFIL GLOBAL (MULTI-ROLE: ADMIN & CLIENT)
    // =====================================================================
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // =====================================================================
    // 👥 JALUR PERAN: KLIEN (CLIENT)
    // =====================================================================
    Route::get('/dashboard', function () {
        $userId = auth()->id();
        $stats = [
            'total'   => Pengajuan::where('user_id', $userId)->count(),
            'proses'  => Pengajuan::where('user_id', $userId)->whereIn('status', ['diajukan', 'perbaikan', 'lengkap'])->count(),
            'selesai' => Pengajuan::where('user_id', $userId)->where('status', 'selesai')->count(),
        ];
        $aktivitas_terbaru = Pengajuan::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('client.dashboard', compact('stats', 'aktivitas_terbaru')); 
    })->name('client.dashboard');

    Route::get('/beranda', function () {
        return view('client.beranda');
    })->name('beranda');

    // =====================================================================
    // 🚀 HALAMAN AKTIVITAS (SUDAH DIPINDAHKAN KE CONTROLLER - MULTI ROLE)
    // =====================================================================
    Route::get('/aktivitas', [AktivitasController::class, 'index'])->name('aktivitas.index');

    // DOWNLOAD BERKAS KLIEN (Bisa diakses Admin/TU/Klien)
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

    // 🟢 DOWNLOAD AUTO-GENERATE DOCX FORM 7.2-4 (Bisa diakses TU maupun Klien)
    Route::get('/pengajuan/{id}/download-form-724', [AdminController::class, 'downloadForm724'])->name('pengajuan.download724');

    // ALUR KLAUSUL TAHAP 7.2
    Route::get('/pengajuan/pilih', [PengajuanController::class, 'pilihTahap'])->name('pengajuan.pilih');
    Route::get('/pengajuan/buat', [PengajuanController::class, 'create'])->name('pengajuan.buat');
    Route::post('/pengajuan/store', [PengajuanController::class, 'store'])->name('pengajuan.store');
    Route::get('/client/dashboard', [PengajuanController::class, 'dashboardClient'])->name('client.dashboard');

    // =====================================================================
    // 💼 JALUR PERAN: TU AS ADMIN 
    // =====================================================================
    Route::prefix('admin')->middleware(CheckAdminRole::class)->group(function () {
        
        // Dashboard Admin
        Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
        
        // Form 7.2-4 Tampilkan Ceklis Kelengkapan Dokumen
        Route::get('/pengajuan/{id}/ceklis', [AdminController::class, 'cekKelengkapan'])->name('admin.ceklis_kelengkapan');
        Route::get('/pengajuan/{id}/ceklis_lama', [AdminController::class, 'cekKelengkapan'])->name('admin.ceklis');
        
        // 🟢 PROSES SIMPAN EVALUASI CEKLIS (Action dari view form 7.2-4)
        Route::post('/pengajuan/{id}/ceklis', [AdminController::class, 'prosesCeklis'])->name('admin.pengajuan.ceklis');
    });

    // Rute Cadangan TU Berdasarkan Role Tambahan
    Route::prefix('tu')->middleware(CheckAdminRole::class)->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('tu.dashboard');
        Route::get('/evaluasi/{id}', [AdminController::class, 'cekKelengkapan'])->name('tu.ceklis');
        Route::post('/evaluasi/{id}/proses', [AdminController::class, 'prosesCeklis'])->name('tu.proses_ceklis');
    });

});

// =========================================================================
// 🛠️ INLINE MIDDLEWARE CLASS
// =========================================================================
class CheckAdminRole
{
    public function handle($request, Closure $next)
    {
        $role = strtolower(auth()->user()->role ?? '');
        if (!auth()->check() || ($role !== 'admin' && $role !== 'tu' && !str_contains($role, 'tata'))) {
            abort(403, 'Akses ditolak. Halaman Dashboard Kerja hanya untuk Admin/Tata Usaha.');
        }
        return $next($request);
    }
}