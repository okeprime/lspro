<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pengajuan;

class AktivitasController extends Controller
{
    /**
     * Menampilkan halaman aktivitas permohonan sertifikasi.
     * Logika ini membagi data secara otomatis: Admin/TU melihat semua berkas klien,
     * sedangkan Client biasa hanya melihat berkas miliknya sendiri.
     */
    public function index()
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        $user = auth()->user();
        $role = trim(strtolower($user->role));

        // 🟢 SELEKSI DATA AGRESIF (SINKRONISASI TATA USAHA)
        if ($role === 'admin' || $role === 'tu' || str_contains($role, 'tu') || str_contains($role, 'admin') || str_contains($role, 'tata')) {
            
            // Jika login sebagai ADMIN / Tata Usaha, ditarik SEMUA data pengajuan milik semua klien
            $pengajuans = Pengajuan::with('user')
                                   ->orderBy('created_at', 'desc')
                                   ->get();
        } else {
            
            // Jika login sebagai CLIENT, hanya ambil data permohonan miliknya sendiri
            $pengajuans = Pengajuan::where('user_id', $user->id)
                                   ->orderBy('created_at', 'desc')
                                   ->get();
        }

        // Jalankan parser JSON data_form sebelum dikirim ke View (Anti-Error TypeError)
        $pengajuans->map(function ($item) {
            $data = $item->data_form;
            
            if (is_string($data)) {
                $data = json_decode($data, true) ?? [];
            } elseif (is_object($data)) {
                $data = (array) $data;
            }
            
            $item->parsed_form = is_array($data) ? $data : [];
            return $item;
        });

        return view('aktivitas', compact('pengajuans'));
    }
}