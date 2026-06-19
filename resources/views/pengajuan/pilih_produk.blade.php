@extends('layouts.app')

@section('title', 'Pilih Produk ' . ucfirst($jenis_pengajuan))

@section('extra-css')
<style>
    .product-card {
        background: white;
        border-radius: 16px;
        padding: 30px;
        border: 2px solid #e2e8f0;
        text-align: center;
        transition: all 0.3s ease;
        text-decoration: none;
        color: inherit;
        display: block;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .product-card:hover {
        border-color: #16a34a;
        box-shadow: 0 10px 25px rgba(22, 163, 74, 0.1);
        transform: translateY(-5px);
        color: inherit;
    }
    .product-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: #f0fdf4;
        color: #16a34a;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        margin: 0 auto 20px;
        transition: all 0.3s ease;
    }
    .product-card:hover .product-icon {
        background: #16a34a;
        color: white;
        transform: scale(1.1);
    }
    .product-title {
        font-size: 18px;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 10px;
    }
    .product-desc {
        font-size: 13px;
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 0;
    }
    .page-header-bar {
        display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center;
        margin-bottom: 30px; gap: 12px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4" style="max-width: 1000px;">
    <div class="page-header-bar">
        <div>
            <h2 style="color: #1e293b; font-weight: 800; font-size: 26px; margin: 0;">
                <i class="fa-solid fa-seedling me-2" style="color: #16a34a;"></i>
                Pilih Kategori Produk {{ ucfirst($jenis_pengajuan) }}
            </h2>
            <p style="color: #64748b; font-size: 14.5px; margin: 6px 0 0;">
                Silakan pilih jenis pupuk yang akan diajukan untuk proses {{ $jenis_pengajuan }} LSPro BRMP SDLP.
            </p>
        </div>
        <a href="{{ route('aktivitas.index') }}" class="btn btn-light border fw-semibold shadow-sm" style="border-radius: 10px; font-size: 14px;">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @php
        $tahap = $jenis_pengajuan === 'resertifikasi' ? '3' : ($jenis_pengajuan === 'survailen' ? '2' : '1');
    @endphp

    <div class="row g-4">
        {{-- Pupuk Organik --}}
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('pengajuan.create', ['jenis_sertifikasi' => 'Pupuk Organik', 'tahap' => $tahap]) }}" class="product-card">
                <div class="product-icon"><i class="fa-solid fa-leaf"></i></div>
                <h3 class="product-title">Pupuk Organik</h3>
                <p class="product-desc">Sertifikasi SNI untuk produk pupuk organik padat dan cair.</p>
            </a>
        </div>

        {{-- Pupuk NPK --}}
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('pengajuan.create', ['jenis_sertifikasi' => 'Pupuk NPK', 'tahap' => $tahap]) }}" class="product-card">
                <div class="product-icon"><i class="fa-solid fa-flask"></i></div>
                <h3 class="product-title">Pupuk NPK</h3>
                <p class="product-desc">Sertifikasi SNI untuk pupuk majemuk NPK padat.</p>
            </a>
        </div>

        {{-- Pupuk Urea --}}
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('pengajuan.create', ['jenis_sertifikasi' => 'Pupuk Urea', 'tahap' => $tahap]) }}" class="product-card">
                <div class="product-icon"><i class="fa-solid fa-vial"></i></div>
                <h3 class="product-title">Pupuk Urea</h3>
                <p class="product-desc">Sertifikasi SNI untuk pupuk Urea prill maupun granul.</p>
            </a>
        </div>

        {{-- Pupuk ZA --}}
        <div class="col-md-6 col-lg-3">
            <a href="{{ route('pengajuan.create', ['jenis_sertifikasi' => 'Pupuk ZA', 'tahap' => $tahap]) }}" class="product-card">
                <div class="product-icon"><i class="fa-solid fa-vial-virus"></i></div>
                <h3 class="product-title">Pupuk ZA</h3>
                <p class="product-desc">Sertifikasi SNI untuk produk Amonium Sulfat (ZA).</p>
            </a>
        </div>
    </div>

    <div class="alert mt-4 shadow-sm border-0 d-flex align-items-center gap-3" style="background: #f8fafc; border-radius: 12px; font-size: 13.5px; color: #475569;">
        <i class="fa-solid fa-circle-info fs-5" style="color: #0ea5e9;"></i>
        <div>
            <strong>Informasi:</strong> Pemilihan produk ini akan menyesuaikan form isian spesifikasi bahan aktif dan klausul SNI yang berlaku untuk produk Anda.
        </div>
    </div>
</div>
@endsection
