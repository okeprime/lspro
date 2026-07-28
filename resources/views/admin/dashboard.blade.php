@extends('layouts.app')
@section('title', 'Dashboard - LSPro')

@section('extra-css')
<style>
    .stat-card {
        background: #fff;
        border-radius: 12px;
        padding: 24px;
        border: 1px solid #e2e8f0;
        display: flex; align-items: center; gap: 18px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        transition: all 0.2s ease-in-out;
        text-decoration: none;
        color: inherit;
    }
    .stat-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); transform: translateY(-1px); color: inherit; border-color: #cbd5e1; }
    .stat-icon {
        width: 48px; height: 48px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 20px; flex-shrink: 0;
        background: #f1f5f9; color: #475569; /* Professional default slate */
    }
    .stat-label { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.08em; }
    .stat-value { font-size: 28px; font-weight: 600; color: #0f172a; line-height: 1; margin-top: 4px; }
    .stat-sub   { font-size: 11px; color: #94a3b8; margin-top: 4px; }

    .section-title { font-size: 15px; font-weight: 700; color: #0f172a; margin-bottom: 4px; letter-spacing: -0.01em; }
    .section-sub   { font-size: 13px; color: #64748b; margin-bottom: 18px; }
    .section-divider { border: none; border-top: 1px solid #e2e8f0; margin: 28px 0; }

    .quick-action-card {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
        padding: 16px 20px; display: flex; align-items: center; gap: 14px;
        text-decoration: none; color: #0f172a; transition: all 0.2s ease;
        box-shadow: 0 1px 2px rgba(0,0,0,0.03);
    }
    .quick-action-card:hover { box-shadow: 0 4px 10px rgba(0,0,0,0.06); transform: translateY(-1px); border-color: #cbd5e1; color: #0f172a; }
    .quick-action-icon {
        width: 38px; height: 38px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; flex-shrink: 0;
        background: #f1f5f9; color: #475569; /* Professional default slate */
    }
    .quick-action-label { font-size: 13px; font-weight: 600; color: #0f172a; }
    .quick-action-desc  { font-size: 11px; color: #64748b; margin-top: 2px; }

    .welcome-banner {
        border-radius: 16px; padding: 32px;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;
        margin-bottom: 28px;
        background: linear-gradient(135deg, #0f172a, #1e293b);
        border: 1px solid #334155;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    /* We'll override the colored backgrounds to uniform sleek variations if needed, or keep them all slate */
    .welcome-banner.tu     { background: linear-gradient(135deg, #064e3b, #047857); }
    .welcome-banner.layanan{ background: linear-gradient(135deg, #1e40af, #2563eb); }
    .welcome-banner.audit  { background: linear-gradient(135deg, #7c2d12, #c2410c); }
    .welcome-banner.super  { background: linear-gradient(135deg, #312e81, #4f46e5); }
    .welcome-banner.default{ background: linear-gradient(135deg, #1e293b, #334155); }

    .badge-role {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 6px 14px; border-radius: 6px; font-size: 11px;
        font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase;
        color: #e2e8f0; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1);
        margin-bottom: 12px;
    }

    .table-mini thead th { font-size: 11px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; padding: 12px 16px; border-bottom: 1px solid #e2e8f0; background: #f8fafc; }
    .table-mini tbody td { font-size: 13px; padding: 14px 16px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; color: #334155; }
    .table-mini tbody tr:hover { background: #f8fafc; }

    .status-pill {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 4px 12px; border-radius: 6px; font-size: 11px; font-weight: 600;
        border: 1px solid rgba(0,0,0,0.05);
    }

    .info-box {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    }
    .info-box-title { font-size: 14px; font-weight: 600; color: #0f172a; margin-bottom: 14px; }

    /* MOBILE APP-LIKE STYLING */
    @media (max-width: 576px) {
        .container-fluid { padding: 16px 12px !important; }
        .welcome-banner { padding: 24px 20px; border-radius: 14px; margin-bottom: 20px; flex-direction: column; align-items: flex-start; text-align: left; }
        .welcome-banner h2 { font-size: 18px !important; margin-bottom: 4px !important; }
        .welcome-banner p { font-size: 12px !important; }
        .stat-card { padding: 16px; border-radius: 10px; gap: 12px; }
        .stat-icon { width: 40px; height: 40px; font-size: 18px; border-radius: 8px; }
        .stat-label { font-size: 10px; }
        .stat-value { font-size: 24px; }
        .quick-action-card { padding: 14px; border-radius: 10px; gap: 12px; flex-direction: row; text-align: left; justify-content: flex-start; min-height: auto;}
        .quick-action-icon { width: 32px; height: 32px; font-size: 14px; margin: 0; }
        .section-title { font-size: 14px; }
    }
</style>
@endsection

@section('content')
@php
    $user       = auth()->user();
    $userRole   = strtolower($user->role ?? 'admin');
    $userSub    = strtolower($user->sub_role ?? '');
    $userName   = $user->nama_penghubung ?? $user->name ?? explode('@', $user->email)[0];
    $isSuperAdmin = $userRole === 'superadmin';

    $bannerClass = match($userSub) {
        'layanan'   => 'tu',
        'audit'     => 'audit',
        default     => $isSuperAdmin ? 'super' : 'default',
    };

    $subLabel = match($userSub) {
        'layanan'   => 'Administrasi',
        'audit'     => 'Tim Audit',
        default     => $isSuperAdmin ? 'Superadmin' : 'Admin',
    };

    $greetingIcon = match($userSub) {
        'layanan'   => 'fa-folder-open',
        'audit'     => 'fa-magnifying-glass-chart',
        default     => $isSuperAdmin ? 'fa-crown' : 'fa-gauge',
    };

    $greetingDesc = match($userSub) {
        'layanan'   => 'Kelola verifikasi berkas dan antrian pengajuan sertifikasi.',
        'audit'     => 'Kelola proses audit kecukupan dan audit lapangan.',
        default     => $isSuperAdmin ? 'Pantau seluruh sistem dan kelola akses pengguna.' : 'Ringkasan aktivitas sistem sertifikasi LSPro.',
    };

    $statusLabels = [
        'draft'            => ['label' => 'Draft',              'class' => 'bg-secondary'],
        'diajukan'         => ['label' => 'Diajukan',           'class' => 'bg-primary'],
        'perbaikan'        => ['label' => 'Perbaikan',          'class' => 'bg-warning text-dark'],
        'verifikasi_tu'    => ['label' => 'Verifikasi Administrasi',      'class' => 'bg-info text-dark'],
        'perjanjian'       => ['label' => 'Perjanjian',         'class' => 'bg-info'],
        'billing'          => ['label' => 'Billing',            'class' => 'bg-warning text-dark'],
        'proses_evaluasi'  => ['label' => 'Evaluasi',           'class' => 'bg-primary'],
        'proses_audit'     => ['label' => 'Audit',              'class' => 'bg-primary'],
        'keputusan'        => ['label' => 'Keputusan',          'class' => 'bg-info'],
        'selesai'          => ['label' => 'Selesai',            'class' => 'bg-success'],
        'sppt_sni'         => ['label' => 'Sertifikat Kesesuaian SNI',           'class' => 'bg-success'],
        'ditolak'          => ['label' => 'Ditolak',            'class' => 'bg-danger'],
        'pending_ttd'      => ['label' => 'Menunggu TTD',       'class' => 'bg-warning text-dark'],
    ];
@endphp

<div class="container-fluid py-3 px-3 px-md-4">

    {{-- ====== WELCOME BANNER ====== --}}
    <div class="welcome-banner {{ $bannerClass }}">
        <div>
            <div class="badge-role">
                <i class="fa-solid {{ $greetingIcon }}"></i> {{ $subLabel }}
            </div>
            <h2 style="color:#fff; font-size:20px; font-weight:800; margin:0 0 6px 0;">
                Selamat datang, {{ $userName }}!
            </h2>
            <p style="color:rgba(255,255,255,0.75); font-size:13px; margin:0;">
                {{ $greetingDesc }}
            </p>
        </div>
        <div style="color:rgba(255,255,255,0.6); font-size:12px; text-align:right;">
            <i class="fa-regular fa-clock me-1"></i> {{ now()->translatedFormat('l, d F Y') }}<br>
            <span style="font-size:18px; font-weight:700; color:#fff;">{{ now()->format('H:i') }}</span> WIB
        </div>
    </div>

    @if(session('success'))
        <div style="background:#f0fdf4;border:1px solid #bbf7d0;color:#166534;padding:12px 18px;border-radius:10px;margin-bottom:20px;font-size:14px;display:flex;align-items:center;gap:10px;">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    {{-- ================================================================ --}}
    {{-- ====== SUPERADMIN DASHBOARD ====== --}}
    {{-- ================================================================ --}}
    @if($isSuperAdmin)

        <div class="section-title"><i class="fa-solid fa-chart-bar me-2" ></i>Ringkasan Sistem</div>
        <p class="section-sub">Statistik keseluruhan aktivitas layanan sertifikasi.</p>

        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.panel_tu') }}" class="stat-card">
                    <div class="stat-icon" style="background:#ede9fe;color:#7c3aed;"><i class="fa-solid fa-file-arrow-up"></i></div>
                    <div>
                        <div class="stat-label">Total Pengajuan</div>
                        <div class="stat-value">{{ $stats['total_masuk'] ?? 0 }}</div>
                        <div class="stat-sub">Semua status</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.panel_tu') }}" class="stat-card">
                    <div class="stat-icon" style="background:#fef3c7;color:#d97706;"><i class="fa-solid fa-gears"></i></div>
                    <div>
                        <div class="stat-label">Belum Diverifikasi</div>
                        <div class="stat-value">{{ $stats['belum_diverifikasi'] ?? 0 }}</div>
                        <div class="stat-sub">Menunggu Administrasi</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.panel_keuangan') }}" class="stat-card">
                    <div class="stat-icon" style="background:#dbeafe;color:#2563eb;"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                    <div>
                        <div class="stat-label">Invoice Pending</div>
                        <div class="stat-value">{{ $stats['invoice_pending'] ?? 0 }}</div>
                        <div class="stat-sub">Menunggu verifikasi</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('superadmin.users.index') }}" class="stat-card">
                    <div class="stat-icon" style="background:#dcfce7;color:#16a34a;"><i class="fa-solid fa-users-gear"></i></div>
                    <div>
                        <div class="stat-label">Sertifikat Terbit</div>
                        <div class="stat-value">{{ $stats['sertifikat_terbit'] ?? 0 }}</div>
                        <div class="stat-sub">Status selesai</div>
                    </div>
                </a>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fce7f3;color:#9d174d;"><i class="fa-solid fa-gavel"></i></div>
                    <div>
                        <div class="stat-label">Banding/Keluhan</div>
                        <div class="stat-value">{{ $stats['banding_open'] ?? 0 }}</div>
                        <div class="stat-sub">Aktif terbuka</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fef3c7;color:#ca8a04;"><i class="fa-solid fa-magnifying-glass"></i></div>
                    <div>
                        <div class="stat-label">Survailen Aktif</div>
                        <div class="stat-value">{{ $stats['survailen_aktif'] ?? 0 }}</div>
                        <div class="stat-sub">Terjadwal/berjalan</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#ffedd5;color:#c2410c;"><i class="fa-solid fa-clipboard-check"></i></div>
                    <div>
                        <div class="stat-label">Perlu Audit</div>
                        <div class="stat-value">{{ $stats['perlu_audit'] ?? 0 }}</div>
                        <div class="stat-sub">Evaluasi + Audit</div>
                    </div>
                </div>
            </div>
        </div>

        <hr class="section-divider">

        {{-- Akses Cepat Superadmin --}}
        <div class="section-title mb-3"><i class="fa-solid fa-bolt me-2" ></i>Akses Cepat</div>
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <a href="{{ route('superadmin.users.index') }}" class="quick-action-card">
                    <div class="quick-action-icon" style="background:#ede9fe;color:#7c3aed;"><i class="fa-solid fa-bolt"></i></div>
                    <div>
                        <div class="quick-action-label">Superadmin</div>
                        <div class="quick-action-desc">Kelola sistem & user</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.panel_tu') }}" class="quick-action-card">
                    <div class="quick-action-icon" style="background:#dcfce7;color:#16a34a;"><i class="fa-solid fa-folder-open"></i></div>
                    <div>
                        <div class="quick-action-label">Administrasi</div>
                        <div class="quick-action-desc">Pengajuan & Keuangan</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.layanan.evaluasi_dokumen') }}" class="quick-action-card">
                    <div class="quick-action-icon" style="background:#dbeafe;color:#2563eb;"><i class="fa-solid fa-file-circle-check"></i></div>
                    <div>
                        <div class="quick-action-label">Layanan & Standar</div>
                        <div class="quick-action-desc">Evaluasi & Komisi Teknis</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.audit_berkas') }}" class="quick-action-card">
                    <div class="quick-action-icon" style="background:#ffedd5;color:#c2410c;"><i class="fa-solid fa-flask"></i></div>
                    <div>
                        <div class="quick-action-label">Tim Audit & Lab</div>
                        <div class="quick-action-desc">Audit & Laporan Lab</div>
                    </div>
                </a>
            </div>
        </div>

        <hr class="section-divider">

    @endif {{-- END SUPERADMIN --}}


    {{-- ================================================================ --}}
    {{-- ====== LAYANAN (ADMINISTRASI & KEUANGAN) DASHBOARD ====== --}}
    {{-- ================================================================ --}}
    @if($userSub === 'layanan' && !$isSuperAdmin)

        <div class="row g-3 mb-4">
            {{-- Stat Cards: Administrasi --}}
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.panel_tu') }}" class="stat-card">
                    <div class="stat-icon" style="background:#dcfce7;color:#16a34a;"><i class="fa-solid fa-file-arrow-up"></i></div>
                    <div>
                        <div class="stat-label">Pengajuan Masuk</div>
                        <div class="stat-value">{{ $stats['total_masuk'] ?? 0 }}</div>
                        <div class="stat-sub">Total aktif</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.panel_tu') }}" class="stat-card">
                    <div class="stat-icon" style="background:#fee2e2;color:#dc2626;"><i class="fa-solid fa-inbox"></i></div>
                    <div>
                        <div class="stat-label">Belum Diverifikasi</div>
                        <div class="stat-value">{{ $stats['belum_diverifikasi'] ?? 0 }}</div>
                        <div class="stat-sub">Perlu tindakan</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fef3c7;color:#d97706;"><i class="fa-solid fa-rotate-left"></i></div>
                    <div>
                        <div class="stat-label">Perbaikan Klien</div>
                        <div class="stat-value">{{ $stats['perbaikan'] ?? 0 }}</div>
                        <div class="stat-sub">Menunggu revisi</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#dbeafe;color:#2563eb;"><i class="fa-solid fa-calendar-day"></i></div>
                    <div>
                        <div class="stat-label">Masuk Hari Ini</div>
                        <div class="stat-value">{{ $stats['baru_hari_ini'] ?? 0 }}</div>
                        <div class="stat-sub">Pengajuan baru</div>
                    </div>
                </div>
            </div>

            {{-- Stat Cards: Keuangan & Survailen --}}
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.panel_keuangan') }}" class="stat-card">
                    <div class="stat-icon" style="background:#fee2e2;color:#dc2626;"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                    <div>
                        <div class="stat-label">Invoice Pending</div>
                        <div class="stat-value">{{ $stats['invoice_pending'] ?? 0 }}</div>
                        <div class="stat-sub">Belum dikonfirmasi</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#dcfce7;color:#16a34a;"><i class="fa-solid fa-circle-check"></i></div>
                    <div>
                        <div class="stat-label">Invoice Lunas</div>
                        <div class="stat-value">{{ $stats['invoice_lunas'] ?? 0 }}</div>
                        <div class="stat-sub">Sudah dikonfirmasi</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.survailen.index') }}" class="stat-card">
                    <div class="stat-icon" style="background:#dbeafe;color:#2563eb;"><i class="fa-solid fa-magnifying-glass"></i></div>
                    <div>
                        <div class="stat-label">Survailen Aktif</div>
                        <div class="stat-value">{{ $stats['survailen_aktif'] ?? 0 }}</div>
                        <div class="stat-sub">Terjadwal/berjalan</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.banding.index') }}" class="stat-card">
                    <div class="stat-icon" style="background:#fce7f3;color:#9d174d;"><i class="fa-solid fa-gavel"></i></div>
                    <div>
                        <div class="stat-label">Banding/Keluhan</div>
                        <div class="stat-value">{{ $stats['banding_open'] ?? 0 }}</div>
                        <div class="stat-sub">Aktif terbuka</div>
                    </div>
                </a>
            </div>
        </div>

        {{-- Akses Cepat (Menu Kerja) --}}
        <div class="section-title mb-3"><i class="fa-solid fa-bolt me-2" ></i>Menu Kerja</div>
        <div class="row g-3 mb-4">
            {{-- Quick Actions: Administrasi --}}
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.panel_tu') }}" class="quick-action-card">
                    <div class="quick-action-icon" style="background:#dcfce7;color:#16a34a;"><i class="fa-solid fa-folder-open"></i></div>
                    <div>
                        <div class="quick-action-label">Panel Administrasi</div>
                        <div class="quick-action-desc">Antrian verifikasi</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.chat.index') }}" class="quick-action-card">
                    <div class="quick-action-icon" style="background:#ede9fe;color:#7c3aed;"><i class="fa-solid fa-comments"></i></div>
                    <div>
                        <div class="quick-action-label">Chat Internal</div>
                        <div class="quick-action-desc">Komunikasi tim</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('notifikasi.index') }}" class="quick-action-card">
                    <div class="quick-action-icon" style="background:#fee2e2;color:#dc2626;"><i class="fa-solid fa-bell"></i></div>
                    <div>
                        <div class="quick-action-label">Notifikasi</div>
                        <div class="quick-action-desc">Pesan masuk</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.audit_berkas') }}" class="quick-action-card">
                    <div class="quick-action-icon" style="background:#fef3c7;color:#ca8a04;"><i class="fa-solid fa-file-signature"></i></div>
                    <div>
                        <div class="quick-action-label">Audit Berkas</div>
                        <div class="quick-action-desc">Cek kesesuaian</div>
                    </div>
                </a>
            </div>

            {{-- Quick Actions: Layanan & Keuangan --}}
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.panel_keuangan') }}" class="quick-action-card">
                    <div class="quick-action-icon" style="background:#dbeafe;color:#2563eb;"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                    <div>
                        <div class="quick-action-label">Verifikasi Billing</div>
                        <div class="quick-action-desc">Konfirmasi pembayaran</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.survailen.index') }}" class="quick-action-card">
                    <div class="quick-action-icon" style="background:#dcfce7;color:#16a34a;"><i class="fa-solid fa-magnifying-glass"></i></div>
                    <div>
                        <div class="quick-action-label">Survailen</div>
                        <div class="quick-action-desc">Jadwal pengawasan</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.banding.index') }}" class="quick-action-card">
                    <div class="quick-action-icon" style="background:#fce7f3;color:#9d174d;"><i class="fa-solid fa-gavel"></i></div>
                    <div>
                        <div class="quick-action-label">Banding & Keluhan</div>
                        <div class="quick-action-desc">Tangani keluhan klien</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.cs') ?? '#' }}" class="quick-action-card">
                    <div class="quick-action-icon" style="background:#fef3c7;color:#ca8a04;"><i class="fa-solid fa-headset"></i></div>
                    <div>
                        <div class="quick-action-label">Customer Service</div>
                        <div class="quick-action-desc">Chat dengan klien</div>
                    </div>
                </a>
            </div>
        </div>

        <hr class="section-divider">

    @endif {{-- END LAYANAN --}}


    {{-- ================================================================ --}}
    {{-- ====== AUDIT DASHBOARD ====== --}}
    {{-- ================================================================ --}}
    @if($userSub === 'audit' && !$isSuperAdmin)

        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.audit_berkas') }}" class="stat-card">
                    <div class="stat-icon" style="background:#fee2e2;color:#dc2626;"><i class="fa-solid fa-file-circle-check"></i></div>
                    <div>
                        <div class="stat-label">Perlu Audit</div>
                        <div class="stat-value">{{ $stats['perlu_audit'] ?? 0 }}</div>
                        <div class="stat-sub">Evaluasi + Audit aktif</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#fef3c7;color:#d97706;"><i class="fa-solid fa-scale-balanced"></i></div>
                    <div>
                        <div class="stat-label">Menunggu Keputusan</div>
                        <div class="stat-value">{{ $stats['menunggu_keputusan'] ?? 0 }}</div>
                        <div class="stat-sub">Siap diputuskan</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#dcfce7;color:#16a34a;"><i class="fa-solid fa-award"></i></div>
                    <div>
                        <div class="stat-label">Sertifikat Terbit</div>
                        <div class="stat-value">{{ $stats['sertifikat_terbit'] ?? 0 }}</div>
                        <div class="stat-sub">Status selesai</div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="stat-card">
                    <div class="stat-icon" style="background:#dbeafe;color:#2563eb;"><i class="fa-solid fa-file-arrow-up"></i></div>
                    <div>
                        <div class="stat-label">Total Pengajuan</div>
                        <div class="stat-value">{{ $stats['total_masuk'] ?? 0 }}</div>
                        <div class="stat-sub">Semua status aktif</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Akses Cepat Audit --}}
        <div class="section-title mb-3"><i class="fa-solid fa-bolt me-2" ></i>Menu Kerja</div>
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.audit_berkas') }}" class="quick-action-card">
                    <div class="quick-action-icon" style="background:#ffedd5;color:#c2410c;"><i class="fa-solid fa-file-signature"></i></div>
                    <div>
                        <div class="quick-action-label">Audit Kesesuaian</div>
                        <div class="quick-action-desc">Audit berkas dokumen</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.audit_kecukupan') }}" class="quick-action-card">
                    <div class="quick-action-icon" style="background:#fce7f3;color:#9d174d;"><i class="fa-solid fa-file-circle-check"></i></div>
                    <div>
                        <div class="quick-action-label">Audit Kecukupan</div>
                        <div class="quick-action-desc">Evaluasi kelengkapan</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.hasil_lab') }}" class="quick-action-card">
                    <div class="quick-action-icon" style="background:#dbeafe;color:#2563eb;"><i class="fa-solid fa-flask"></i></div>
                    <div>
                        <div class="quick-action-label">Hasil Lab</div>
                        <div class="quick-action-desc">Input hasil pengujian</div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-md-3">
                <a href="{{ route('admin.data_sampel') ?? '#' }}" class="quick-action-card">
                    <div class="quick-action-icon" style="background:#dcfce7;color:#16a34a;"><i class="fa-solid fa-vials"></i></div>
                    <div>
                        <div class="quick-action-label">Data Sampel</div>
                        <div class="quick-action-desc">Kelola data sampel</div>
                    </div>
                </a>
            </div>
        </div>

        <hr class="section-divider">

    @endif {{-- END AUDIT --}}


    {{-- ================================================================ --}}
    {{-- ====== TABEL PENGAJUAN TERBARU (SEMUA ROLE) ====== --}}
    {{-- ================================================================ --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <div class="section-title">
                <i class="fa-solid fa-list-check me-2" ></i>
                @if($userSub === 'layanan') Antrian Verifikasi
                @elseif($userSub === 'audit')   Pengajuan Perlu Audit
                @else Semua Pengajuan
                @endif
            </div>
            <p class="section-sub mb-0">20 pengajuan terbaru â€¢ Data real-time</p>
        </div>
        <a href="{{ route('admin.panel_tu') }}" class="btn btn-sm" style="background:#f1f5f9;color:#475569;border-radius:8px;font-size:12px;font-weight:600;">
            Lihat Semua <i class="fa-solid fa-arrow-right ms-1"></i>
        </a>
    </div>

    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.04);">
        <div class="table-responsive">
            <table class="table table-mini table-hover mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">ID</th>
                        <th>Nama Klien</th>
                        <th>Jenis</th>
                        <th>Status</th>
                        @if($userSub === 'layanan' || $isSuperAdmin)<th>Invoice</th>@endif
                        <th>Tgl Masuk</th>
                        <th class="pe-4 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuans as $item)
                    @php
                        $stLabel = $statusLabels[$item->status] ?? ['label' => ucfirst($item->status), 'class' => 'bg-secondary'];
                        $namaKlien = $item->user->nama_perusahaan ?? $item->user->nama_penghubung ?? explode('@', $item->user->email ?? '')[0] ?? '-';
                    @endphp
                    <tr>
                        <td class="ps-4 fw-bold" >#{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            @php
                                $df = is_array($item->data_form) ? $item->data_form : (json_decode($item->data_form, true) ?? []);
                                $merek = $df['merek_produk'] ?? ($df['merek'] ?? 'Tanpa Merek');
                            @endphp
                            <div class="fw-semibold" style="color:#1e293b;font-size:13px;">{{ Str::limit($merek, 28) }}</div>
                            <div class="text-muted" style="font-size:11px;">{{ Str::limit($namaKlien, 28) }}</div>
                        </td>
                        <td>
                            @php $jenis = $item->jenis_sertifikasi ?? $item->jenis ?? '-'; @endphp
                            <span style="font-size:12px;color:#64748b;">{{ ucfirst($jenis) }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $stLabel['class'] }} status-pill">{{ $stLabel['label'] }}</span>
                        </td>
                        @if($userSub === 'layanan' || $isSuperAdmin)
                        <td>
                            @if($item->invoice)
                                @php $inv = $item->invoice; @endphp
                                <span class="badge {{ $inv->status === 'paid' ? 'bg-success' : ($inv->status === 'pending_verification' ? 'bg-warning text-dark' : 'bg-secondary') }}" style="font-size:11px;">
                                    {{ $inv->status === 'paid' ? 'Lunas' : ($inv->status === 'pending_verification' ? 'Konfirmasi' : ucfirst($inv->status)) }}
                                </span>
                            @else
                                <span style="font-size:11px;color:#94a3b8;">â€”</span>
                            @endif
                        </td>
                        @endif
                        <td style="color:#64748b;font-size:12px;">{{ $item->created_at->translatedFormat('d M Y') }}</td>
                        <td class="pe-4 text-end">
                            @php
                                if (in_array($item->status, ['diajukan', 'perbaikan'])) {
                                    $detailRoute = route('admin.panel_tu');
                                } else {
                                    $detailRoute = route('admin.ceklis_kelengkapan', $item->id);
                                }
                                
                                if (in_array($item->status, ['mengisi_perjanjian', 'menunggu_ttd', 'billing', 'billing_1', 'billing_2', 'billing_3', 'billing_4'])) {
                                    $detailRoute = route('admin.panel_keuangan');
                                } elseif (in_array($item->status, ['evaluasi_724_audit', 'proses_audit', 'evaluasi', 'proses_evaluasi', 'keputusan', 'menunggu_persetujuan_jadwal'])) {
                                    $detailRoute = route('admin.audit_berkas');
                                } elseif ($item->status === 'menunggu_lhp') {
                                    $detailRoute = route('admin.hasil_lab');
                                }
                            @endphp
                            <div class="btn-group" role="group">
                                <a href="{{ $detailRoute }}" class="btn btn-sm btn-light border" style="border-radius:8px 0 0 8px;font-size:11px;font-weight:600;">
                                    Detail
                                </a>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-light border dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius:0 8px 8px 0;font-size:11px;">
                                        <span class="visually-hidden">Menu</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow" style="font-size:13px;">
                                        @if($item->status === 'menunggu_lhp')
                                            <li><a class="dropdown-item" href="{{ route('admin.pengajuan.form_lhp', $item->id) }}"><i class="bi bi-upload me-2 text-danger"></i>Unggah LHP</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                        @endif
                                        <li><h6 class="dropdown-header"><i class="bi bi-file-earmark-text me-1"></i> Isi Dokumen</h6></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.pengajuan.isi_form', ['id' => $item->id, 'form_type' => 'perjanjian']) }}"><i class="bi bi-handshake me-2 text-success"></i>Buat Perjanjian</a></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.pengajuan.isi_form', ['id' => $item->id, 'form_type' => 'rencana_audit']) }}"><i class="bi bi-calendar-check me-2 text-primary"></i>Rencana Audit</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="{{ route('admin.ceklis_kelengkapan', $item->id) }}"><i class="bi bi-ui-checks me-2 text-info"></i>Ceklis 7.2-4</a></li>
                                        @if($isSuperAdmin)
                                        <li><hr class="dropdown-divider"></li>
                                        <li><h6 class="dropdown-header text-danger"><i class="fa-solid fa-triangle-exclamation me-1"></i> Bypass (Superadmin)</h6></li>
                                        <li>
                                            <form action="{{ route('admin.pengajuan.teruskan', $item->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="perbaikan">
                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Yakin kembalikan ke Perbaikan?')">Kembalikan ke Perbaikan</button>
                                            </form>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.pengajuan.teruskan', $item->id) }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="status" value="billing_1">
                                                <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Yakin ubah ke Billing 1?')">Force to Billing 1</button>
                                            </form>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div style="color:#94a3b8;font-size:14px;">
                                <i class="fa-solid fa-inbox fa-2x mb-2 d-block"></i>
                                Belum ada data pengajuan.
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

