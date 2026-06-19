@extends('layouts.app')

@section('title', 'Pilih Produk Sertifikasi')

@section('extra-css')
<style>
    .jenis-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 30px;
        text-align: center;
        transition: all 0.3s ease;
        height: 100%;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .jenis-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 20px rgba(15, 118, 110, 0.08);
        border-color: #0f766e;
    }
    .jenis-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        background-color: #f0fdf4;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: #0f766e;
        margin: 0 auto 20px auto;
        transition: all 0.3s ease;
    }
    .jenis-card:hover .jenis-icon {
        background-color: #0f766e;
        color: white;
    }
</style>
@endsection

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <span class="badge bg-teal-subtle text-teal px-3 py-2 rounded-pill text-uppercase fw-bold mb-2" style="background-color: #f0fdf4; color: #0f766e;">Langkah Pertama</span>
        <h2 class="fw-bold text-dark">Pilih Produk Sertifikasi</h2>
        <p class="text-muted max-w-2xl mx-auto" style="max-width: 600px; margin: 0 auto;">Pilih produk pupuk Anda di bawah ini untuk memulai pengisian dokumen Form 7.2-1 Permohonan Sertifikasi secara terarah.</p>
    </div>

    <div class="row g-4 justify-content-center">
        <!-- Pupuk Organik -->
        <div class="col-md-3 col-sm-6">
            <div class="jenis-card" onclick="window.location.href='{{ route('pengajuan.buat', ['tahap' => 1, 'jenis_sertifikasi' => 'Pupuk Organik']) }}'">
                <div>
                    <div class="jenis-icon" style="background:#dcfce7; color:#16a34a;">
                        <i class="fa-solid fa-leaf"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Pupuk Organik</h5>
                    <p class="text-muted mb-4" style="font-size: 13px;">Sertifikasi SNI untuk produk pupuk organik alami.</p>
                </div>
                <button class="btn btn-outline-success w-100 fw-bold py-2 mt-auto" style="border-radius: 8px;">
                    Pilih Produk
                </button>
            </div>
        </div>

        <!-- Pupuk NPK -->
        <div class="col-md-3 col-sm-6">
            <div class="jenis-card" onclick="window.location.href='{{ route('pengajuan.buat', ['tahap' => 1, 'jenis_sertifikasi' => 'Pupuk NPK']) }}'">
                <div>
                    <div class="jenis-icon" style="background:#dbeafe; color:#2563eb;">
                        <i class="fa-solid fa-flask"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Pupuk NPK</h5>
                    <p class="text-muted mb-4" style="font-size: 13px;">Sertifikasi SNI untuk pupuk majemuk NPK.</p>
                </div>
                <button class="btn btn-outline-primary w-100 fw-bold py-2 mt-auto" style="border-radius: 8px;">
                    Pilih Produk
                </button>
            </div>
        </div>

        <!-- Pupuk Urea -->
        <div class="col-md-3 col-sm-6">
            <div class="jenis-card" onclick="window.location.href='{{ route('pengajuan.buat', ['tahap' => 1, 'jenis_sertifikasi' => 'Pupuk Urea']) }}'">
                <div>
                    <div class="jenis-icon" style="background:#fef3c7; color:#d97706;">
                        <i class="fa-solid fa-droplet"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Pupuk Urea</h5>
                    <p class="text-muted mb-4" style="font-size: 13px;">Sertifikasi SNI untuk pupuk nitrogen Urea.</p>
                </div>
                <button class="btn btn-outline-warning w-100 fw-bold py-2 mt-auto" style="border-radius: 8px;">
                    Pilih Produk
                </button>
            </div>
        </div>

        <!-- Pupuk ZA -->
        <div class="col-md-3 col-sm-6">
            <div class="jenis-card" onclick="window.location.href='{{ route('pengajuan.buat', ['tahap' => 1, 'jenis_sertifikasi' => 'Pupuk ZA']) }}'">
                <div>
                    <div class="jenis-icon" style="background:#fce7f3; color:#9d174d;">
                        <i class="fa-solid fa-atom"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Pupuk ZA</h5>
                    <p class="text-muted mb-4" style="font-size: 13px;">Sertifikasi SNI untuk pupuk Amonium Sulfat.</p>
                </div>
                <button class="btn w-100 fw-bold py-2 mt-auto" style="color: #9d174d; border: 1px solid #9d174d; border-radius: 8px; background: transparent;">
                    Pilih Produk
                </button>
            </div>
        </div>
    </div>

    <div class="text-center mt-5">
        <a href="{{ route('pengajuan.index') }}" class="text-muted text-decoration-none fw-semibold">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>
</div>
@endsection
