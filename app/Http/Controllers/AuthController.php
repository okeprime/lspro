<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Memanggil model User bawaan Laravel Anda
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function index()
    {
        return view('auth.login'); // Mengarah ke resources/views/auth/login.blade.php
    }

    /**
     * Memproses data login (Autentikasi).
     */
    public function login(Request $request)
    {
        // 1. Validasi input dari form login
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // 2. Coba proses autentikasi
        if (Auth::attempt($credentials)) {
            // Jika sukses, amankan session
            $request->session()->regenerate();

            // Redirect ke dashboard masing-masing berdasarkan role
            $role = trim(strtolower(Auth::user()->role));
            if ($role === 'admin' || $role === 'superadmin') {
                return redirect('/admin/dashboard');
            }
            return redirect('/dashboard');
        }

        // 3. Jika login gagal, kembalikan ke halaman login dengan error
        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan salah.',
        ])->onlyInput('email');
    }

    /**
     * Menampilkan halaman register.
     */
    public function registerView()
    {
        return view('auth.register'); // Mengarah ke resources/views/auth/register.blade.php
    }

    /**
     * Memproses data registrasi pengguna baru.
     */
    public function register(Request $request)
    {
        // 1. Validasi data input form register
        $request->validate([
            'nama_perusahaan' => ['required', 'string', 'max:255'],
            'nama_penghubung' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'], 
            'alamat' => ['required', 'string'],
        ]);

        // 2. Simpan user baru ke database menggunakan Eloquent Model User
        $user = User::create([
            'nama_perusahaan' => $request->nama_perusahaan,
            'nama_penghubung' => $request->nama_penghubung,
            'no_telp' => null, // No longer collected at registration
            'email' => $request->email,
            'alamat' => $request->alamat,
            'password' => Hash::make($request->password), // Enkripsi password demi keamanan
            'role' => 'client',
            'is_active' => true,
        ]);

        // 3. Picu event Registered untuk mengirim email verifikasi
        event(new Registered($user));

        // 4. Langsung loginkan user
        Auth::login($user);

        // 5. Lempar ke halaman verifikasi email (dashboard dengan middleware verified akan otomatis redirect ke verify)
        return redirect('/dashboard')->with('success', 'Registrasi berhasil! Silakan periksa email Anda untuk verifikasi.');
    }

    /**
     * Memproses logout pengguna.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Hancurkan session lama agar tidak disalahgunakan
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Kembalikan ke halaman landing page setelah sukses keluar
        return redirect('/');
    }
}
