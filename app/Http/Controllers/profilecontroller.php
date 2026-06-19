<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil berdasarkan role user yang sedang login.
     */
    public function index()
    {
        $user = Auth::user();
        $role = strtolower($user->role ?? 'client');

        // Tentukan dashboard_route berdasarkan role yang benar
        if (in_array($role, ['superadmin', 'admin'])) {
            $dashboard_route = 'admin.dashboard';
            $is_internal = true;
        } else {
            $dashboard_route = 'client.dashboard';
            $is_internal = false;
        }

        return view('pengajuan.profile', compact('user', 'dashboard_route', 'is_internal'));
    }

    /**
     * Menyimpan perubahan data profil dari form.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $role = strtolower($user->role ?? 'client');
        $is_internal = in_array($role, ['superadmin', 'admin']);
        $oldEmail = $user->email;

        if ($is_internal) {
            // Validasi untuk Admin / Superadmin (Pegawai Internal)
            $request->validate([
                'nama_penghubung' => 'required|string|max:255',
                'email'           => 'required|email|max:255|unique:users,email,' . $user->id,
                'nip'             => 'nullable|string|max:50',
                'unit_kerja'      => 'nullable|string|max:255',
                'jabatan'         => 'nullable|string|max:255',
            ]);

            $updateData = [
                'nama_penghubung' => $request->nama_penghubung,
                'email'           => $request->email,
                'nip'             => $request->nip,
                'unit_kerja'      => $request->unit_kerja,
                'jabatan'         => $request->jabatan,
            ];

            // Jika email berubah, reset status verifikasi
            if ($oldEmail !== $request->email) {
                $updateData['email_verified_at'] = null;
            }

            $user->update($updateData);

            // Kirim ulang email verifikasi jika email berubah
            if ($oldEmail !== $request->email) {
                $user->sendEmailVerificationNotification();
                return redirect()->route('profile.index')
                    ->with('success', 'Profil diperbarui. Silakan verifikasi email baru Anda yang telah kami kirimkan.');
            }

        } else {
            // Validasi untuk Client / Vendor Perusahaan Luar
            $request->validate([
                'nama_perusahaan' => 'required|string|max:255',
                'nama_penghubung' => 'required|string|max:255',
                'no_telp'         => 'required|string|max:20',
                'email'           => 'required|email|max:255|unique:users,email,' . $user->id,
                'alamat'          => 'required|string',
            ]);

            $updateData = [
                'nama_perusahaan' => $request->nama_perusahaan,
                'nama_penghubung' => $request->nama_penghubung,
                'no_telp'         => $request->no_telp,
                'email'           => $request->email,
                'alamat'          => $request->alamat,
            ];

            // Jika email berubah, reset status verifikasi
            if ($oldEmail !== $request->email) {
                $updateData['email_verified_at'] = null;
            }

            $user->update($updateData);

            // Kirim ulang email verifikasi jika email berubah
            if ($oldEmail !== $request->email) {
                $user->sendEmailVerificationNotification();
                return redirect()->route('profile.index')
                    ->with('success', 'Profil diperbarui. Silakan verifikasi email baru Anda yang telah kami kirimkan.');
            }
        }

        return redirect()->route('profile.index')->with('success', 'Profil Anda berhasil diperbarui!');
    }
}