@extends('layouts.app')
@section('title', 'Dashboard - LSPro BRMP SDLP')

@section('extra-css')
<style>
    /* ===== DASHBOARD HERO ===== */
    .dashboard-hero {
        background: linear-gradient(135deg, #022c22 0%, #064e3b 100%);
        border-radius: 16px;
        padding: 30px;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .dashboard-hero::before {
        content: ''; position: absolute;
        top: -50px; right: -50px;
        width: 200px; height: 200px;
        background: rgba(22, 163, 74, 0.15);
        border-radius: 50%;
    }
    .hero-content { position: relative; z-index: 1; }
    .badge-status {
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(16, 185, 129, 0.2); border: 1px solid rgba(16, 185, 129, 0.3);
        color: #6ee7b7; border-radius: 99px; padding: 4px 12px; font-size: 11px; font-weight: 600; text-transform: uppercase;
        margin-bottom: 15px;
    }

    /* ===== STATS CARDS ===== */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }
    .stat-card {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px;
        color: #1e293b;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex; flex-direction: column; justify-content: space-between;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.05);
    }
    .stat-card .stat-icon {
        width: 44px; height: 44px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; margin-bottom: 16px;
    }
    .stat-card .stat-value {
        font-size: 28px; font-weight: 800; margin-bottom: 4px; color: #0f172a;
    }
    .stat-card .stat-label {
        font-size: 13px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;
    }

    /* Specific Colors */
    .card-total { border-top: 4px solid #38bdf8; }
    .card-total .stat-icon { background: #f0f9ff; color: #0ea5e9; }
    .card-total .stat-value { color: #0284c7; }

    .card-proses { border-top: 4px solid #7dd3fc; }
    .card-proses .stat-icon { background: #f0f9ff; color: #38bdf8; }
    .card-proses .stat-value { color: #0369a1; }

    .card-bayar { border-top: 4px solid #22c55e; }
    .card-bayar .stat-icon { background: #f0fdf4; color: #22c55e; }
    .card-bayar .stat-value { color: #15803d; }

    .card-sertifikat { border-top: 4px solid #16a34a; }
    .card-sertifikat .stat-icon { background: #dcfce7; color: #16a34a; }
    .card-sertifikat .stat-value { color: #166534; }

    /* ===== RECENT SECTION ===== */
    .recent-section {
        background: white;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.02);
        overflow: hidden;
    }
    .recent-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e2e8f0;
        display: flex; justify-content: space-between; align-items: center;
    }
    .recent-title { font-size: 16px; font-weight: 700; color: #1e293b; margin: 0; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4" style="max-width: 1200px; margin: 0 auto;">
    
    <!-- Hero Section -->
    <div class="dashboard-hero">
        <div class="hero-content d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <div class="badge-status">
                    <i class="fa-solid fa-circle" style="font-size: 8px;"></i> Akun Terhubung
                </div>
                <h1 style="font-size: 24px; font-weight: 800; margin-bottom: 5px;">Selamat Datang, {{ Auth::user()->nama_perusahaan ?? Auth::user()->name }}</h1>
                <p style="color: #cbd5e1; font-size: 14px; margin: 0;">Pusat Monitoring Sertifikasi Produk SNI Anda</p>
            </div>
            <div style="text-align: right; background: rgba(0,0,0,0.2); padding: 12px 20px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1);">
                <div style="font-size: 11px; color: #94a3b8; font-weight: 600; letter-spacing: 0.5px; margin-bottom: 4px;">TANGGAL HARI INI</div>
                <div style="font-size: 16px; font-weight: 700; color: white;">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card card-total">
            <div class="stat-icon"><i class="fa-solid fa-file-lines"></i></div>
            <div>
                <div class="stat-value">{{ $totalPengajuan }}</div>
                <div class="stat-label">Total Pengajuan</div>
            </div>
        </div>
        <div class="stat-card card-proses">
            <div class="stat-icon"><i class="fa-solid fa-arrows-rotate"></i></div>
            <div>
                <div class="stat-value">{{ $pengajuanAktif }}</div>
                <div class="stat-label">Sedang Diproses</div>
            </div>
        </div>
        <div class="stat-card card-bayar">
            <div class="stat-icon"><i class="ph-fill ph-receipt"></i></div>
            <div>
                <div class="stat-value">{{ $menungguPembayaran }}</div>
                <div class="stat-label">Menunggu Pembayaran</div>
            </div>
        </div>
        <div class="stat-card card-sertifikat">
            <div class="stat-icon"><i class="fa-solid fa-award"></i></div>
            <div>
                <div class="stat-value">{{ $sertifikatTerbit }}</div>
                <div class="stat-label">Sertifikat Terbit</div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="recent-section">
        <div class="recent-header">
            <h2 class="recent-title"><i class="ph-fill ph-clock-counter-clockwise text-muted me-2"></i> Pengajuan Terbaru</h2>
            <a href="{{ route('aktivitas.index') }}" class="btn btn-sm btn-outline-success rounded-pill fw-semibold px-3">Lihat Semua</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size: 14px;">
                <thead style="background: #f8fafc;">
                    <tr>
                        <th class="py-3 px-4 text-muted fw-semibold" style="border-bottom: 1px solid #e2e8f0; font-size: 12px; text-transform: uppercase;">ID / Tanggal</th>
                        <th class="py-3 px-4 text-muted fw-semibold" style="border-bottom: 1px solid #e2e8f0; font-size: 12px; text-transform: uppercase;">Jenis Sertifikasi</th>
                        <th class="py-3 px-4 text-muted fw-semibold" style="border-bottom: 1px solid #e2e8f0; font-size: 12px; text-transform: uppercase;">Status</th>
                        <th class="py-3 px-4 text-muted fw-semibold text-end" style="border-bottom: 1px solid #e2e8f0; font-size: 12px; text-transform: uppercase;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPengajuan as $item)
                        <tr>
                            <td class="py-3 px-4 align-middle">
                                <div class="fw-bold text-dark">{{ $item->id_pengajuan }}</div>
                                <div class="text-muted" style="font-size: 12px;">{{ $item->created_at->format('d M Y') }}</div>
                            </td>
                            <td class="py-3 px-4 align-middle">
                                <div class="fw-bold" style="color: #0f172a;">{{ ucfirst($item->jenis_pengajuan) }}</div>
                            </td>
                            <td class="py-3 px-4 align-middle">
                                @php
                                    $badgeColor = 'secondary';
                                    if(in_array($item->status, ['diajukan', 'verifikasi_tu'])) $badgeColor = 'primary';
                                    if(in_array($item->status, ['selesai'])) $badgeColor = 'success';
                                    if(in_array($item->status, ['ditolak'])) $badgeColor = 'danger';
                                    if(in_array($item->status, ['menunggu_pembayaran'])) $badgeColor = 'warning text-dark';
                                @endphp
                                <span class="badge bg-{{ $badgeColor }} rounded-pill" style="font-weight: 500; padding: 5px 10px;">
                                    {{ str_replace('_', ' ', strtoupper($item->status)) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 align-middle text-end">
                                <a href="{{ route('aktivitas.show', $item->id) }}" class="btn btn-sm btn-light border text-primary fw-semibold rounded-pill px-3">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">
                                <div style="font-size: 40px; margin-bottom: 10px; opacity: 0.3;"><i class="fa-solid fa-folder-open"></i></div>
                                Belum ada pengajuan sertifikasi yang dilakukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
