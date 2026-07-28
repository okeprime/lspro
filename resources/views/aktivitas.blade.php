@extends('layouts.app')

@section('title', 'Aktivitas – Riwayat Pengajuan')

@section('content')
<div class="container-fluid py-4" style="max-width: 1200px;">

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h2 style="color: #1e293b; font-weight: 800; font-size: 24px; margin-bottom: 4px;" id="aktivitas-page-title">{{ $pageTitle ?? 'Aktivitas' }}</h2>
            <p style="color: #64748b; font-size: 13px; margin: 0;">
                {{ $pageDesc ?? 'Pusat riwayat pengajuan Anda.' }}
            </p>
        </div>
        @if(!$isInternal)
            <a href="{{ route('pengajuan.index') }}" class="btn btn-success fw-semibold px-4 shadow-sm" style="border-radius: 10px; font-size: 14px;" id="btn-pengajuan-baru">
                <i class="fa-solid fa-plus me-1"></i> Pengajuan Baru
            </a>
        @endif
    </div>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert border-0 shadow-sm mb-4 d-flex align-items-center gap-3" style="border-radius: 12px; background: #dcfce7; color: #15803d; font-size: 14px;">
            <i class="fa-solid fa-circle-check fs-5"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error'))
        <div class="alert border-0 shadow-sm mb-4 d-flex align-items-center gap-3" style="border-radius: 12px; background: #fee2e2; color: #991b1b; font-size: 14px;">
            <i class="fa-solid fa-circle-exclamation fs-5"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    {{-- ================================================================
         TAB CONTENT: SERTIFIKASI
    ================================================================ --}}
    {{-- TAB CONTENT: SERTIFIKASI --}}
    @if($activeTab === 'sertifikasi')
        @forelse($list as $item)
            @include('aktivitas._pengajuan_card', ['item' => $item])
        @empty
            @include('aktivitas._empty_state', [
                'icon' => 'fa-award',
                'title' => 'Belum ada pengajuan sertifikasi.',
                'desc' => 'Mulai proses sertifikasi produk Anda sekarang.',
                'link' => route('pengajuan.sertifikasi'),
                'linkLabel' => 'Ajukan Sertifikasi Baru',
                'isInternal' => $isInternal,
            ])
        @endforelse
    @endif

    {{-- TAB CONTENT: RESERTIFIKASI --}}
    @if($activeTab === 'resertifikasi')
        @forelse($list as $item)
            @include('aktivitas._pengajuan_card', ['item' => $item])
        @empty
            @include('aktivitas._empty_state', [
                'icon' => 'fa-rotate',
                'title' => 'Belum ada pengajuan resertifikasi.',
                'desc' => 'Resertifikasi dilakukan untuk memperpanjang sertifikat Sertifikat Kesesuaian SNI yang sudah habis masa berlakunya.',
                'link' => route('pengajuan.resertifikasi'),
                'linkLabel' => 'Ajukan Resertifikasi',
                'isInternal' => $isInternal,
            ])
        @endforelse
    @endif

    {{-- TAB CONTENT: BANDING --}}
    @if($activeTab === 'banding')
        @forelse($list as $b)
            @include('aktivitas._banding_card', ['b' => $b])
        @empty
            @include('aktivitas._empty_state', [
                'icon' => 'fa-gavel',
                'title' => 'Belum ada riwayat banding.',
                'desc' => 'Ajukan banding atas keputusan sertifikasi melalui menu Banding & Laporan.',
                'link' => route('banding.index'),
                'linkLabel' => 'Ajukan Banding',
                'isInternal' => $isInternal,
            ])
        @endforelse
    @endif

    {{-- TAB CONTENT: KELUHAN --}}
    @if($activeTab === 'keluhan')
        @forelse($list as $b)
            @include('aktivitas._banding_card', ['b' => $b])
        @empty
            @include('aktivitas._empty_state', [
                'icon' => 'fa-comment-dots',
                'title' => 'Belum ada riwayat keluhan.',
                'desc' => 'Ajukan keluhan atau laporan mengenai pelayanan LSPro.',
                'link' => route('banding.index'),
                'linkLabel' => 'Ajukan Keluhan',
                'isInternal' => $isInternal,
            ])
        @endforelse
    @endif

</div>

{{-- STEPPER CSS --}}
<style>
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

