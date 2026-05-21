<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User; // Memanggil model User bawaan Laravel Anda
use Illuminate\Support\Facades\Hash;

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

            // Redirect ke halaman dashboard
            return redirect()->intended('/dashboard');
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'], 
        ]);

        // 2. Simpan user baru ke database menggunakan Eloquent Model User
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // Enkripsi password demi keamanan
        ]);

        // 3. Setelah sukses mendaftar, lempar ke halaman login dengan pesan sukses
        return redirect('/login')->with('success', 'Registrasi berhasil! Silakan login dengan akun baru Anda.');
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

        // Kembalikan ke halaman login setelah sukses keluar
        return redirect('/login');
    }
}