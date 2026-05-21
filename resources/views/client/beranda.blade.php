@extends('layouts.app')
@section('title', 'Beranda')
@section('content')
    <div class="hero-section">
        <h1>Selamat Datang di Sistem Informasi LSPro</h1>
        <p>Balai Pengujian Standardisasi Instrumen Lingkungan Pertanian (BPSILP)</p>
        <a href="{{ route('client.dashboard') }}" style="display:inline-block; margin-top:20px; padding:12px 30px; background:#2563eb; color:white; text-decoration:none; border-radius:30px; font-weight:600;">Mulai Bekerja →</a>
    </div>
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 25px;">
        <div class="info-card">
            <h3 style="color: #1e40af; margin-bottom: 15px;"><i class="fa-solid fa-leaf"></i> Tentang BPSILP</h3>
            <p style="font-size: 14px; color: #4b5563; text-align: justify;">[Isi Informasi BPSILP Anda...]</p>
        </div>
        <div class="info-card" style="border-top-color: #10b981;">
            <h3 style="color: #065f46; margin-bottom: 15px;"><i class="fa-solid fa-certificate"></i> Tentang LSPro</h3>
            <p style="font-size: 14px; color: #4b5563; text-align: justify;">[Isi Informasi LSPro Anda...]</p>
        </div>
    </div>
@endsection 