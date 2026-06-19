@extends('layouts.app')

@section('title', 'Aktivitas – Tracking Center')

@section('extra-css')
<style>
    .page-header-bar {
        display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center;
        margin-bottom: 24px; gap: 12px;
    }
    .page-header-bar h2 {
        color: #1e293b; font-weight: 800; font-size: 24px; margin: 0;
    }
    .page-header-bar p { color: #64748b; font-size: 14px; margin: 4px 0 0; }
    
    /* Tab Filters */
    .filter-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 30px;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 12px;
    }
    .filter-tab {
        text-decoration: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        color: #64748b;
        background: transparent;
        transition: all 0.2s;
    }
    .filter-tab:hover {
        background: #f8fafc;
        color: #0f172a;
    }
    .filter-tab.active {
        background: #16a34a;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.2);
    }
    
    /* Stepper internal card style */
    .lspro-stepper-wrap { overflow-x: auto; padding-bottom: 4px; }
    .lspro-stepper { display: flex; align-items: center; min-width: max-content; gap: 0; padding: 4px 0; }
    .lspro-step-item { display: flex; align-items: center; }
    .lspro-connector { width: 36px; height: 2px; background: #e2e8f0; flex-shrink: 0; transition: background 0.3s; }
    .lspro-connector.done { background: #16a34a; }
    .lspro-step { display: flex; flex-direction: column; align-items: center; gap: 6px; text-decoration: none; color: #94a3b8; padding: 4px 6px; border-radius: 10px; transition: all 0.2s ease; min-width: 72px; }
    .lspro-step:hover { color: #0284c7; background: rgba(2, 132, 199, 0.05); }
    .lspro-step-circle { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: #e2e8f0; color: #94a3b8; font-size: 12px; font-weight: 700; flex-shrink: 0; transition: all 0.25s ease; border: 2px solid transparent; }
    .lspro-step-label { font-size: 10px; font-weight: 600; text-align: center; line-height: 1.3; white-space: nowrap; }
    .lspro-step.done .lspro-step-circle { background: #16a34a; color: white; border-color: #15803d; }
    .lspro-step.done { color: #16a34a; }
    .lspro-step.current .lspro-step-circle { background: #0284c7; color: white; border-color: #0369a1; box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.18); }
    .lspro-step.current { color: #0369a1; }
    .lspro-step.current .lspro-step-label { font-weight: 700; }
    .lspro-step.rejected .lspro-step-circle { background: #dc2626; color: white; border-color: #b91c1c; }
    .lspro-step.rejected { color: #dc2626; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4" style="max-width: 1200px;">

    {{-- Header --}}
    <div class="page-header-bar">
        <div>
            <h2><i class="fa-solid fa-list-check me-2" style="color:#0f766e;"></i>Tracking Center</h2>
            <p>Pantau seluruh progres aktivitas pengajuan sertifikasi, survailen, hingga keluhan Anda di sini.</p>
        </div>
        @if(!$isInternal)
            <div class="d-flex gap-2">
                <a href="{{ route('pengajuan.sertifikasi') }}" class="btn fw-semibold shadow-sm" style="background: #16a34a; color: white; border-radius: 8px; font-size: 13px;">
                    <i class="fa-solid fa-plus me-1"></i> Pengajuan Baru
                </a>
            </div>
        @endif
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert border-0 shadow-sm mb-4 d-flex align-items-center gap-3" style="border-radius: 12px; background: #dcfce7; color: #15803d; font-size: 14px;">
            <i class="fa-solid fa-circle-check fs-5"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Tab Filters --}}
    <div class="filter-tabs">
        <a href="{{ route('aktivitas.index', ['filter' => 'sertifikasi']) }}" class="filter-tab {{ $filter === 'sertifikasi' ? 'active' : '' }}">
            <i class="fa-solid fa-award me-1"></i> Sertifikasi
        </a>
        <a href="{{ route('aktivitas.index', ['filter' => 'resertifikasi']) }}" class="filter-tab {{ $filter === 'resertifikasi' ? 'active' : '' }}">
            <i class="fa-solid fa-rotate me-1"></i> Resertifikasi
        </a>
        <a href="{{ route('aktivitas.index', ['filter' => 'survailen']) }}" class="filter-tab {{ $filter === 'survailen' ? 'active' : '' }}">
            <i class="fa-solid fa-magnifying-glass me-1"></i> Survailen
        </a>
        <a href="{{ route('aktivitas.index', ['filter' => 'draft']) }}" class="filter-tab {{ $filter === 'draft' ? 'active' : '' }}">
            <i class="fa-solid fa-file-pen me-1"></i> Draft
        </a>
        <a href="{{ route('aktivitas.index', ['filter' => 'banding']) }}" class="filter-tab {{ $filter === 'banding' ? 'active' : '' }}">
            <i class="fa-solid fa-gavel me-1"></i> Banding
        </a>
        <a href="{{ route('aktivitas.index', ['filter' => 'keluhan']) }}" class="filter-tab {{ $filter === 'keluhan' ? 'active' : '' }}">
            <i class="fa-solid fa-comment-dots me-1"></i> Keluhan
        </a>
    </div>

    {{-- List Render --}}
    <div>
        @forelse($list as $item)
            @if(in_array($filter, ['sertifikasi', 'resertifikasi', 'survailen', 'draft']))
                @include('aktivitas._pengajuan_card', ['item' => $item])
            @else
                @include('aktivitas._banding_card', ['item' => $item])
            @endif
        @empty
            @include('aktivitas._empty_state', [
                'icon' => $filter === 'banding' ? 'fa-gavel' : ($filter === 'keluhan' ? 'fa-comment-dots' : 'fa-folder-open'),
                'title' => 'Belum ada data aktivitas.',
                'desc' => 'Tidak ditemukan riwayat ' . $filter . ' untuk saat ini.',
                'link' => in_array($filter, ['banding', 'keluhan']) ? route('banding.index') : route('pengajuan.sertifikasi'),
                'linkLabel' => 'Buat Pengajuan Baru',
                'isInternal' => $isInternal,
            ])
        @endforelse
    </div>

</div>
@endsection
