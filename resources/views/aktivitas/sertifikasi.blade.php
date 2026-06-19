@extends('layouts.app')

@section('title', 'Aktivitas Sertifikasi')

@section('extra-css')
<style>
.page-header-bar {
    display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center;
    margin-bottom: 28px; gap: 12px;
}
.page-header-bar h2 {
    color: #1e293b; font-weight: 800; font-size: 22px; margin: 0;
}
.page-header-bar p { color: #64748b; font-size: 13px; margin: 4px 0 0; }
.breadcrumb-bar { font-size: 12.5px; color: #64748b; margin-bottom: 20px; }
.breadcrumb-bar a { color: #2563eb; text-decoration: none; font-weight: 500; }
.breadcrumb-bar a:hover { text-decoration: underline; }
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

    {{-- Breadcrumb --}}
    <div class="breadcrumb-bar">
        <a href="{{ route('aktivitas.index') }}"><i class="fa-solid fa-list-check me-1"></i>Aktivitas</a>
        <span class="mx-2">/</span>
        <span>Sertifikasi</span>
    </div>

    {{-- Header --}}
    <div class="page-header-bar">
        <div>
            <h2><i class="fa-solid fa-award me-2" style="color:#0f766e;"></i>Aktivitas Sertifikasi</h2>
            <p>Riwayat pengajuan sertifikasi produk Anda beserta status terkini dan progress setiap tahap.</p>
        </div>
        @if(!$isInternal)
            <a href="{{ route('pengajuan.sertifikasi') }}" class="btn btn-success fw-semibold px-4 shadow-sm" style="border-radius: 10px; font-size: 14px;" id="btn-ajukan-sertifikasi">
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

    {{-- List --}}
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

</div>
@endsection
