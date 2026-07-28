@extends('layouts.app')

@section('title', 'Pengajuan Sertifikasi')

@section('extra-css')
<style>
    .pengajuan-hero {
        background: linear-gradient(135deg, #0f766e 0%, #065f46 60%, #064e3b 100%);
        border-radius: 20px;
        padding: 40px 36px;
        color: white;
        margin-bottom: 36px;
        position: relative;
        overflow: hidden;
    }
    .pengajuan-hero::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 220px; height: 220px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .pengajuan-hero::after {
        content: '';
        position: absolute;
        bottom: -40px; left: 60px;
        width: 140px; height: 140px;
        background: rgba(255,255,255,0.04);
        border-radius: 50%;
    }
    .pengajuan-hero h1 {
        font-size: clamp(20px, 3vw, 26px);
        font-weight: 800;
        margin-bottom: 8px;
    }
    .pengajuan-hero p {
        font-size: 14px;
        opacity: 0.85;
        margin: 0;
    }

    .gateway-card {
        background: white;
        border-radius: 20px;
        padding: 36px 28px;
        border: 1.5px solid #e2e8f0;
        text-decoration: none;
        color: inherit;
        display: block;
        transition: all 0.25s ease;
        position: relative;
        overflow: hidden;
        height: 100%;
    }
    .gateway-card::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 4px;
        border-radius: 0 0 20px 20px;
        background: var(--card-accent, #0f766e);
        transform: scaleX(0);
        transition: transform 0.25s ease;
    }
    .gateway-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        border-color: transparent;
        color: inherit;
    }
    .gateway-card:hover::after {
        transform: scaleX(1);
    }
    .gateway-icon {
        width: 64px;
        height: 64px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        margin-bottom: 20px;
        flex-shrink: 0;
    }
    .gateway-card h3 {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 8px;
    }
    .gateway-card p {
        font-size: 13.5px;
        color: #64748b;
        line-height: 1.7;
        margin-bottom: 24px;
    }
    .gateway-card .btn-gateway {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 700;
        padding: 10px 20px;
        border-radius: 10px;
        border: none;
        transition: all 0.2s;
    }

    .section-divider {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 28px;
    }
    .section-divider h2 {
        font-size: 18px;
        font-weight: 800;
        color: #1e293b;
        white-space: nowrap;
        margin: 0;
    }
    .section-divider::after {
        content: '';
        flex: 1;
        height: 1px;
        background: #e2e8f0;
    }

    @media (max-width: 767px) {
        .gateway-col { margin-bottom: 16px; }
    }
</style>
@endsection

@section('content')
<div class="container py-4" style="max-width: 1100px;">

    {{-- Hero --}}
    <div class="pengajuan-hero">
        <div style="position: relative; z-index: 1;">
            <h1><i class="fa-solid fa-file-signature me-2"></i> Pengajuan Sertifikasi</h1>
            <p>Pilih jenis layanan sertifikasi yang ingin Anda ajukan. Proses akan dipandu langkah demi langkah sesuai prosedur LSPro BRMP SDLP.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert border-0 shadow-sm mb-4 d-flex align-items-center gap-3" style="border-radius: 12px; background: #dcfce7; color: #15803d; font-size: 14px;">
            <i class="fa-solid fa-circle-check fs-5"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Section Title --}}
    <div class="section-divider">
        <h2>Pilih Jenis Layanan</h2>
    </div>

    {{-- 3 Gateway Cards --}}
    <div class="row g-4 mb-5">

        {{-- Sertifikasi Baru --}}
        <div class="col-md-6 gateway-col">
            <a href="{{ route('pengajuan.sertifikasi') }}" class="gateway-card" style="--card-accent: #0f766e;">
                <div class="gateway-icon" style="background: #ccfbf1;">
                    <i class="fa-solid fa-award" style="color: #0f766e;"></i>
                </div>
                <h3>Sertifikasi Baru</h3>
                <p>Pengajuan sertifikasi Sertifikat Kesesuaian SNI pertama kali untuk produk/merek yang belum pernah bersertifikat. Berlaku untuk produk pupuk anorganik dan organik.</p>
                <span class="btn-gateway" style="background: #0f766e; color: white;">
                    Mulai Pengajuan <i class="fa-solid fa-arrow-right"></i>
                </span>
            </a>
        </div>

        {{-- Resertifikasi --}}
        <div class="col-md-6 gateway-col">
            <a href="{{ route('pengajuan.resertifikasi') }}" class="gateway-card" style="--card-accent: #0369a1;">
                <div class="gateway-icon" style="background: #e0f2fe;">
                    <i class="fa-solid fa-rotate" style="color: #0369a1;"></i>
                </div>
                <h3>Resertifikasi</h3>
                <p>Perpanjangan atau pembaruan Sertifikat Kesesuaian SNI yang telah habis masa berlakunya atau karena terjadi perubahan signifikan pada produk/proses produksi.</p>
                <span class="btn-gateway" style="background: #0369a1; color: white;">
                    Mulai Resertifikasi <i class="fa-solid fa-arrow-right"></i>
                </span>
            </a>
        </div>
    </div>

    {{-- Info Panel --}}
    <div class="row g-3">
        <div class="col-md-4">
            <div class="d-flex gap-3 p-3" style="background: #f8fafc; border-radius: 14px; border: 1px solid #e2e8f0;">
                <div style="width:40px; height:40px; border-radius: 10px; background:#dcfce7; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fa-solid fa-clock" style="color: #16a34a;"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size: 13px; color: #0f172a;">Pantau Riwayat</div>
                    <div style="font-size: 12px; color: #64748b;">Seluruh riwayat pengajuan tersedia di menu <a href="{{ route('aktivitas.index') }}" class="text-decoration-none fw-semibold" style="color: #0f766e;">Aktivitas</a>.</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="d-flex gap-3 p-3" style="background: #f8fafc; border-radius: 14px; border: 1px solid #e2e8f0;">
                <div style="width:40px; height:40px; border-radius: 10px; background:#e0f2fe; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fa-solid fa-file-invoice-dollar" style="color: #0284c7;"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size: 13px; color: #0f172a;">Info Pembayaran</div>
                    <div style="font-size: 12px; color: #64748b;">Tagihan dan status pembayaran tersedia di menu <a href="{{ route('billing.index') }}" class="text-decoration-none fw-semibold" style="color: #0284c7;">Billing</a>.</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="d-flex gap-3 p-3" style="background: #f8fafc; border-radius: 14px; border: 1px solid #e2e8f0;">
                <div style="width:40px; height:40px; border-radius: 10px; background:#fef3c7; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                    <i class="fa-solid fa-gavel" style="color: #d97706;"></i>
                </div>
                <div>
                    <div class="fw-bold" style="font-size: 13px; color: #0f172a;">Pengaduan</div>
                    <div style="font-size: 12px; color: #64748b;">Ajukan banding atau keluhan melalui menu <a href="{{ route('banding.index') }}" class="text-decoration-none fw-semibold" style="color: #d97706;">Banding & Laporan</a>.</div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

