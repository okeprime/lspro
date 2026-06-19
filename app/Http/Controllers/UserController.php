<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    private function checkSuperadmin()
    {
        abort_if(auth()->user()->role !== 'superadmin', 403, 'Akses ditolak. Hanya Superadmin yang dapat mengakses halaman ini.');
    }

    public function index()
    {
        $this->checkSuperadmin();
        $users = User::where('role', 'admin')->latest()->get();
        return view('admin.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $this->checkSuperadmin();
        $validated = $request->validate([
            'nama_penghubung' => 'required|string|max:255',
            'email'           => 'required|string|email|max:255|unique:users',
            'password'        => 'required|string|min:8',
            'sub_role'        => 'required|in:tatausaha,layanan,audit',
            'nip'             => 'nullable|string|max:50',
            'unit_kerja'      => 'nullable|string|max:255',
            'jabatan'         => 'nullable|string|max:255',
        ]);

        User::create([
            'nama_perusahaan' => 'Internal LSPro',
            'nama_penghubung' => $validated['nama_penghubung'],
            'no_telp'         => '-',
            'alamat'          => '-',
            'email'           => $validated['email'],
            'password'        => Hash::make($validated['password']),
            'role'            => 'admin',
            'sub_role'        => $validated['sub_role'],
            'nip'             => $request->nip,
            'unit_kerja'      => $request->unit_kerja,
            'jabatan'         => $request->jabatan,
            'is_active'       => true,
        ]);

        return redirect()->route('superadmin.users.index')->with('success', 'Petugas berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $this->checkSuperadmin();
        $validated = $request->validate([
            'nama_penghubung' => 'required|string|max:255',
            'email'           => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'sub_role'        => 'required|in:tatausaha,layanan,audit',
            'nip'             => 'nullable|string|max:50',
            'unit_kerja'      => 'nullable|string|max:255',
            'jabatan'         => 'nullable|string|max:255',
        ]);

        $user->update([
            'nama_penghubung' => $validated['nama_penghubung'],
            'email'           => $validated['email'],
            'sub_role'        => $validated['sub_role'],
            'nip'             => $request->nip,
            'unit_kerja'      => $request->unit_kerja,
            'jabatan'         => $request->jabatan,
        ]);

        return redirect()->route('superadmin.users.index')->with('success', 'Data petugas berhasil diperbarui.');
    }

    public function toggleStatus(User $user)
    {
        $this->checkSuperadmin();
        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('superadmin.users.index')->with('success', "Akun petugas berhasil $status.");
    }

    public function resetPassword(Request $request, User $user)
    {
        $this->checkSuperadmin();
        $request->validate(['password' => 'required|string|min:8']);
        $user->update(['password' => Hash::make($request->password)]);
        return redirect()->route('superadmin.users.index')->with('success', 'Password petugas berhasil di-reset.');
    }

    public function destroy(User $user)
    {
        $this->checkSuperadmin();
        $user->delete();
        return redirect()->route('superadmin.users.index')->with('success', 'Akun petugas berhasil dihapus.');
    }
}