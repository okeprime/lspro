<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengajuan;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    // Menampilkan Beranda Utama
    public function beranda()
    {
        return view('client.beranda');
    }

    // Menampilkan Dashboard (Statistik)
    public function index()
    {
        $user_id = Auth::id();
        
        $stats = [
            'total' => Pengajuan::where('user_id', $user_id)->count(),
            'proses' => Pengajuan::where('user_id', $user_id)->where('status', 'Diajukan')->count(),
            'selesai' => Pengajuan::where('user_id', $user_id)->where('status', 'Selesai')->count(),
        ];

        $aktivitas_terbaru = Pengajuan::where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('client.dashboard', compact('stats', 'aktivitas_terbaru'));
    }

    // FIX ERROR: Menampilkan Riwayat Aktivitas untuk Klien
    public function aktivitas()
    {
        // Mengambil semua data pengajuan milik user yang sedang login
        $aktivitas = Pengajuan::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('client.aktivitas', compact('aktivitas'));
    }
}