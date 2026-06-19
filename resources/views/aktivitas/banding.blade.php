@extends('layouts.app')

@section('title', 'Aktivitas Banding')

@section('extra-css')
<style>
.page-header-bar {
    display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center;
    margin-bottom: 28px; gap: 12px;
}
.page-header-bar h2 { color: #1e293b; font-weight: 800; font-size: 22px; margin: 0; }
.page-header-bar p { color: #64748b; font-size: 13px; margin: 4px 0 0; }
.breadcrumb-bar { font-size: 12.5px; color: #64748b; margin-bottom: 20px; }
.breadcrumb-bar a { color: #2563eb; text-decoration: none; font-weight: 500; }
.breadcrumb-bar a:hover { text-decoration: underline; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4" style="max-width: 1200px;">

    {{-- Breadcrumb --}}
    <div class="breadcrumb-bar">
        <a href="{{ route('aktivitas.index') }}"><i class="fa-solid fa-list-check me-1"></i>Aktivitas</a>
        <span class="mx-2">/</span>
        <span>Banding</span>
    </div>

    {{-- Header --}}
    <div class="page-header-bar">
        <div>
            <h2><i class="fa-solid fa-gavel me-2" style="color:#d97706;"></i>Aktivitas Banding</h2>
            <p>Riwayat banding yang telah Anda ajukan atas keputusan sertifikasi. Pantau status penanganan dan tanggapan resmi LSPro.</p>
        </div>
        @if(!$isInternal)
            <a href="{{ route('banding.index') }}" class="btn fw-semibold px-4 shadow-sm" style="border-radius: 10px; font-size: 14px; background:#d97706; color:white;" id="btn-ajukan-banding">
                <i class="fa-solid fa-plus me-1"></i> Ajukan Banding
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

    {{-- List --}}
    @forelse($list as $b)
        @include('aktivitas._banding_card', ['b' => $b])
    @empty
        @include('aktivitas._empty_state', [
            'icon' => 'fa-gavel',
            'title' => 'Belum ada riwayat banding.',
            'desc' => 'Ajukan banding atas keputusan sertifikasi melalui menu Banding & Keluhan.',
            'link' => route('banding.index'),
            'linkLabel' => 'Ajukan Banding',
            'isInternal' => $isInternal,
        ])
    @endforelse

</div>
@endsection
