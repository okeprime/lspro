<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProfileController extends Controller implements HasMiddleware
{
    /**
     * Mendaftarkan middleware secara modern di Laravel 11/12/13.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('auth'),
        ];
    }

    /**
     * Menampilkan halaman profil.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Kunci Deteksi: Jika email mengandung kata 'admin' atau 'tu', anggap sebagai Admin/Pegawai Internal
        if (str_contains($user->email, 'admin') || str_contains($user->email, 'tu')) {
            $role = 'Admin / Petugas';
            $dashboard_route = 'admin.dashboard';
            $is_admin = true;
        } else {
            $role = 'Client';
            $dashboard_route = 'client.dashboard';
            $is_admin = false;
        }

        return view('pengajuan.profile', compact('user', 'dashboard_route', 'role', 'is_admin'));
    }

    /**
     * Menyimpan perubahan data profil dari form.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        // Jalankan deteksi role yang sama dengan fungsi index
        $is_admin = (str_contains($user->email, 'admin') || str_contains($user->email, 'tu'));

        if ($is_admin) {
            // Validasi data untuk akun Admin / Pegawai Internal
            $request->validate([
                'name'  => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            ]);

            // Di database Anda, kolom nama utama disimpan pada field 'name'
            $user->update([
                'name'  => $request->name,
                'email' => $request->email,
            ]);
        } else {
            // Validasi data untuk akun Klien / Vendor Luar
            $request->validate([
                'nama_perusahaan' => 'required|string|max:255',
                'nama_penghubung' => 'required|string|max:255',
                'no_telp'         => 'required|string|max:20',
                'email'           => 'required|email|max:255|unique:users,email,' . $user->id,
                'alamat'          => 'required|string',
            ]);

            $user->update([
                'name'            => $request->nama_penghubung, // name diisi nama penghubung agar sinkron
                'nama_perusahaan' => $request->nama_perusahaan,
                'nama_penghubung' => $request->nama_penghubung,
                'no_telp'         => $request->no_telp,
                'email'           => $request->email,
                'alamat'          => $request->alamat,
            ]);
        }

        return redirect()->route('profile.index')->with('success', 'Profil Anda berhasil diperbarui!');
    }
}