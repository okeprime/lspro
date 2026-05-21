<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Pengajuan;

class HomeController extends Controller
{
    /**
     * Menampilkan Beranda Awal Klien dengan ringkasan aktivitas.
     */
    public function beranda()
    {
        $user = Auth::user();
        $role = strtolower(trim($user->role));
        
        // Mengambil 3 aktivitas terbaru klien untuk ditampilkan di dashboard awal
        $aktivitas = [];
        if ($role == 'client') {
            $aktivitas = Pengajuan::where('user_id', $user->id)
                                  ->latest()
                                  ->take(3)
                                  ->get();
        }

        return view('client.beranda', compact('user', 'role', 'aktivitas'));
    }

    /**
     * Menampilkan Dashboard Kerja Utama berdasarkan Level Role.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $role_db = strtolower(trim($user->role));

        $level = 0;
        // Mapping hirarki akses sesuai kebutuhan manajemen user
        if (in_array($role_db, ['superadmin', 'admin'])) {
            $level = 5;
        } elseif (in_array($role_db, ['pimpinan', 'ketua', 'kepala'])) {
            $level = 4;
        } elseif (in_array($role_db, ['admin2', 'admin_2', 'teknis'])) {
            $level = 3;
        } elseif (in_array($role_db, ['tata_usaha', 'tatausaha', 'tu', 'admin1', 'admin_1'])) {
            $level = 2; 
        } elseif (in_array($role_db, ['user', 'klien', 'client'])) {
            $level = 1;
        }

        return view('dashboard', compact('user', 'role_db', 'level'));
    }
}