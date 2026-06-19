@extends('layouts.app')

@section('title', 'Aktivitas Pengajuan Saya')

@section('content')
<div class="container-fluid py-4" style="max-width: 1400px;">
    
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="color: #1e293b; font-weight: 700; font-size: 26px; margin-bottom: 4px;">Aktivitas Pengajuan Saya</h2>
            <p style="color: #64748b; font-size: 14px; margin: 0;">Pantau pengajuan sertifikasi, survailen, dan resertifikasi Anda di sini.</p>
        </div>
        <div class="text-end">
            <a href="{{ route('pengajuan.pilih') }}" class="btn btn-primary" style="border-radius: 8px; padding: 8px 18px; font-size: 14px; font-weight: 500;">
                <i class="bi bi-plus-circle me-1"></i> Pengajuan Baru
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 10px; font-size: 14px; background: #dcfce7; color: #15803d;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    <section class="mb-4">
        <div class="d-flex align-items-end justify-content-between gap-3 mb-3">
            <div>
                <h5 class="fw-bold mb-1" style="color: #0f172a;">Alur Sertifikasi Tipe 5</h5>
                <p class="text-muted mb-0" style="font-size: 13px;">Alur operasional berdasarkan dokumen Klausul 7 LSPro.</p>
            </div>
        </div>
        <div class="type5-stage-scroll">
            <div class="type5-stage-track">
                @foreach($workflowStages as $number => $stage)
                    <div class="type5-stage-item">
                        <span class="type5-stage-number">{{ str_pad($number, 2, '0', STR_PAD_LEFT) }}</span>
                        <strong>{{ $stage['short_title'] }}</strong>
                        <small>{{ $stage['pic'] }}</small>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid #475569 !important;">
                <div class="card-body py-3 px-4">
                    <p class="text-uppercase fw-semibold mb-1" style="color: #64748b; font-size: 12px;">Total Pengajuan</p>
                    <h3 class="fw-bold m-0" style="color: #0f172a;">{{ $stats['total'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid #f59e0b !important;">
                <div class="card-body py-3 px-4">
                    <p class="text-uppercase fw-semibold mb-1" style="color: #64748b; font-size: 12px;">Dalam Proses</p>
                    <h3 class="fw-bold m-0" style="color: #d97706;">{{ $stats['proses'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid #16a34a !important;">
                <div class="card-body py-3 px-4">
                    <p class="text-uppercase fw-semibold mb-1" style="color: #64748b; font-size: 12px;">Selesai</p>
                    <h3 class="fw-bold m-0" style="color: #16a34a;">{{ $stats['selesai'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; border-left: 4px solid #06b6d4 !important;">
                <div class="card-body py-3 px-4">
                    <p class="text-uppercase fw-semibold mb-1" style="color: #64748b; font-size: 12px;">Pembayaran Pending</p>
                    <h3 class="fw-bold m-0" style="color: #0891b2;">{{ $stats['payment_pending'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-4" id="pengajuanTabs" role="tablist" style="border-bottom: 2px solid #e2e8f0;">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold" id="tahap-pendaftaran-tab" data-bs-toggle="tab" data-bs-target="#tahap-pendaftaran" type="button" role="tab">
                <i class="bi bi-file-earmark-text me-1"></i> Pendaftaran
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="tahap-dokumen-tab" data-bs-toggle="tab" data-bs-target="#tahap-dokumen" type="button" role="tab">
                <i class="bi bi-file-earmark-signature me-1"></i> Perjanjian
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="tahap-billing-tab" data-bs-toggle="tab" data-bs-target="#tahap-billing" type="button" role="tab">
                <i class="bi bi-receipt me-1"></i> Billing
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="tahap-audit-tab" data-bs-toggle="tab" data-bs-target="#tahap-audit" type="button" role="tab">
                <i class="bi bi-clipboard-check me-1"></i> Audit
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold" id="tahap-penerbitan-tab" data-bs-toggle="tab" data-bs-target="#tahap-penerbitan" type="button" role="tab">
                <i class="bi bi-certificate me-1"></i> Penerbitan
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="pengajuanTabsContent">
        
        <!-- TAB 1: PENDAFTARAN -->
        <div class="tab-pane fade show active" id="tahap-pendaftaran" role="tabpanel">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 px-4" style="border-bottom: 1px solid #f1f5f9;">
                    <h5 class="fw-bold m-0" style="color: #0f172a;">Tahap Pengajuan dan Verifikasi Berkas</h5>
                </div>
                <div class="card-body p-0">
                    @if(isset($pengajuanByTahap['pendaftaran']) && $pengajuanByTahap['pendaftaran']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light" style="font-size: 12px; color: #475569; border-bottom: 2px solid #e2e8f0;">
                                    <tr>
                                        <th class="px-4 py-3 fw-bold">ID</th>
                                        <th class="py-3 fw-bold">Tipe Pengajuan</th>
                                        <th class="py-3 fw-bold">Nomor Registrasi</th>
                                        <th class="py-3 fw-bold">Status</th>
                                        <th class="py-3 fw-bold">Tanggal Diajukan</th>
                                        <th class="px-4 py-3 fw-bold text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 14px; color: #334155;">
                                    @foreach($pengajuanByTahap['pendaftaran'] as $item)
                                        <tr style="border-bottom: 1px solid #f1f5f9;">
                                            <td class="px-4 py-3 fw-semibold text-secondary">#{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</td>
                                            <td class="py-3">
                                                <span class="badge {{ $item->jenis_pengajuan === 'sertifikasi' ? 'bg-primary' : ($item->jenis_pengajuan === 'survailen' ? 'bg-info' : 'bg-warning text-dark') }}">
                                                    {{ ucfirst($item->jenis_pengajuan) }}
                                                </span>
                                            </td>
                                            <td class="py-3 fw-semibold">{{ $item->nomor_registrasi ?? '-' }}</td>
                                            <td class="py-3">
                                                @include('components.workflow-status', ['pengajuan' => $item])
                                            </td>
                                            <td class="py-3">{{ $item->created_at->format('d M Y') }}</td>
                                            <td class="px-4 py-3 text-end">
                                                <a href="{{ route('aktivitas.show', $item) }}" class="btn btn-sm btn-outline-primary" style="border-radius: 6px; font-size: 12px;">
                                                    <i class="bi bi-diagram-3 me-1"></i> Detail Alur
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox d-block fs-2 mb-2 text-secondary"></i>
                            <p>Belum ada pengajuan dalam tahap pendaftaran.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- TAB 2: DOKUMEN -->
        <div class="tab-pane fade" id="tahap-dokumen" role="tabpanel">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 px-4" style="border-bottom: 1px solid #f1f5f9;">
                    <h5 class="fw-bold m-0" style="color: #0f172a;">Perjanjian Sertifikasi</h5>
                </div>
                <div class="card-body p-0">
                    @if(isset($pengajuanByTahap['dokumen']) && $pengajuanByTahap['dokumen']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light" style="font-size: 12px; color: #475569; border-bottom: 2px solid #e2e8f0;">
                                    <tr>
                                        <th class="px-4 py-3 fw-bold">ID</th>
                                        <th class="py-3 fw-bold">Tipe</th>
                                        <th class="py-3 fw-bold">Status</th>
                                        <th class="py-3 fw-bold">Dokumen Ditolak</th>
                                        <th class="py-3 fw-bold">Catatan</th>
                                        <th class="px-4 py-3 fw-bold text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 14px; color: #334155;">
                                    @foreach($pengajuanByTahap['dokumen'] as $item)
                                        <tr style="border-bottom: 1px solid #f1f5f9;">
                                            <td class="px-4 py-3 fw-semibold text-secondary">#{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</td>
                                            <td class="py-3">
                                                <span class="badge {{ $item->jenis_pengajuan === 'sertifikasi' ? 'bg-primary' : ($item->jenis_pengajuan === 'survailen' ? 'bg-info' : 'bg-warning text-dark') }}">
                                                    {{ ucfirst($item->jenis_pengajuan) }}
                                                </span>
                                            </td>
                                            <td class="py-3">
                                                @include('components.workflow-status', ['pengajuan' => $item])
                                            </td>
                                            <td class="py-3">
                                                <span class="text-muted small">-</span>
                                            </td>
                                            <td class="py-3 small text-muted">{{ $item->catatan ?? '-' }}</td>
                                            <td class="px-4 py-3 text-end">
                                                <a href="{{ route('aktivitas.show', $item) }}" class="btn btn-sm btn-primary" style="border-radius: 6px; font-size: 12px;">
                                                    <i class="bi bi-diagram-3 me-1"></i> Detail Alur
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox d-block fs-2 mb-2 text-secondary"></i>
                            <p>Belum ada pengajuan dalam tahap perjanjian sertifikasi.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- TAB 3: BILLING -->
        <div class="tab-pane fade" id="tahap-billing" role="tabpanel">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 px-4" style="border-bottom: 1px solid #f1f5f9;">
                    <h5 class="fw-bold m-0" style="color: #0f172a;">Pengajuan Menunggu Pembayaran Invoice</h5>
                </div>
                <div class="card-body p-0">
                    @if(isset($pengajuanByTahap['billing']) && $pengajuanByTahap['billing']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light" style="font-size: 12px; color: #475569; border-bottom: 2px solid #e2e8f0;">
                                    <tr>
                                        <th class="px-4 py-3 fw-bold">ID</th>
                                        <th class="py-3 fw-bold">Invoice</th>
                                        <th class="py-3 fw-bold">Jumlah</th>
                                        <th class="py-3 fw-bold">Status Bayar</th>
                                        <th class="py-3 fw-bold">Jatuh Tempo</th>
                                        <th class="px-4 py-3 fw-bold text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 14px; color: #334155;">
                                    @foreach($pengajuanByTahap['billing'] as $item)
                                        @php
                                            $invoice = $item->latestInvoice();
                                        @endphp
                                        @if($invoice)
                                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                                <td class="px-4 py-3 fw-semibold text-secondary">#{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</td>
                                                <td class="py-3 fw-semibold">{{ $invoice->invoice_number }}</td>
                                                <td class="py-3">Rp {{ number_format($invoice->amount_total, 0, ',', '.') }}</td>
                                                <td class="py-3">
                                                    <span class="badge {{ $invoice->status === 'paid' ? 'bg-success' : ($invoice->status === 'overdue' ? 'bg-danger' : 'bg-warning text-dark') }}">
                                                        {{ ucfirst($invoice->status) }}
                                                    </span>
                                                </td>
                                                <td class="py-3">{{ $invoice->due_date ? $invoice->due_date->format('d M Y') : '-' }}</td>
                                                <td class="px-4 py-3 text-end">
                                                    <a href="{{ route('aktivitas.show', $item) }}" class="btn btn-sm btn-outline-primary" style="border-radius: 6px; font-size: 12px;">
                                                        <i class="bi bi-eye me-1"></i> Lihat
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox d-block fs-2 mb-2 text-secondary"></i>
                            <p>Belum ada invoice yang menunggu pembayaran.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- TAB 4: AUDIT -->
        <div class="tab-pane fade" id="tahap-audit" role="tabpanel">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 px-4" style="border-bottom: 1px solid #f1f5f9;">
                    <h5 class="fw-bold m-0" style="color: #0f172a;">Pengajuan dalam Tahap Audit Kecukupan & Kesesuaian</h5>
                </div>
                <div class="card-body p-0">
                    @if(isset($pengajuanByTahap['audit']) && $pengajuanByTahap['audit']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light" style="font-size: 12px; color: #475569; border-bottom: 2px solid #e2e8f0;">
                                    <tr>
                                        <th class="px-4 py-3 fw-bold">ID</th>
                                        <th class="py-3 fw-bold">Tipe Audit</th>
                                        <th class="py-3 fw-bold">Jml Findings</th>
                                        <th class="py-3 fw-bold">Status</th>
                                        <th class="py-3 fw-bold">Tanggal Audit</th>
                                        <th class="px-4 py-3 fw-bold text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 14px; color: #334155;">
                                    @foreach($pengajuanByTahap['audit'] as $item)
                                        @php
                                            $auditFindings = $item->auditFindings()->get();
                                        @endphp
                                        <tr style="border-bottom: 1px solid #f1f5f9;">
                                            <td class="px-4 py-3 fw-semibold text-secondary">#{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</td>
                                            <td class="py-3">
                                                <span class="badge bg-primary">Audit</span>
                                            </td>
                                            <td class="py-3">{{ $auditFindings->count() }} items</td>
                                            <td class="py-3">
                                                @include('components.workflow-status', ['pengajuan' => $item])
                                            </td>
                                            <td class="py-3">{{ $item->updated_at->format('d M Y') }}</td>
                                            <td class="px-4 py-3 text-end">
                                                <a href="{{ route('aktivitas.show', $item) }}" class="btn btn-sm btn-outline-primary" style="border-radius: 6px; font-size: 12px;">
                                                    <i class="bi bi-eye me-1"></i> Detail
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox d-block fs-2 mb-2 text-secondary"></i>
                            <p>Belum ada pengajuan dalam tahap audit.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- TAB 5: PENERBITAN -->
        <div class="tab-pane fade" id="tahap-penerbitan" role="tabpanel">
            <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 px-4" style="border-bottom: 1px solid #f1f5f9;">
                    <h5 class="fw-bold m-0" style="color: #0f172a;">Evaluasi, Keputusan, dan Penerbitan Sertifikat</h5>
                </div>
                <div class="card-body p-0">
                    @if(isset($pengajuanByTahap['penerbitan']) && $pengajuanByTahap['penerbitan']->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light" style="font-size: 12px; color: #475569; border-bottom: 2px solid #e2e8f0;">
                                    <tr>
                                        <th class="px-4 py-3 fw-bold">ID</th>
                                        <th class="py-3 fw-bold">Tipe</th>
                                        <th class="py-3 fw-bold">No Registrasi</th>
                                        <th class="py-3 fw-bold">Status</th>
                                        <th class="py-3 fw-bold">Tanggal Selesai</th>
                                        <th class="px-4 py-3 fw-bold text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody style="font-size: 14px; color: #334155;">
                                    @foreach($pengajuanByTahap['penerbitan'] as $item)
                                        <tr style="border-bottom: 1px solid #f1f5f9;">
                                            <td class="px-4 py-3 fw-semibold text-secondary">#{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</td>
                                            <td class="py-3">
                                                <span class="badge {{ $item->jenis_pengajuan === 'sertifikasi' ? 'bg-primary' : ($item->jenis_pengajuan === 'survailen' ? 'bg-info' : 'bg-warning text-dark') }}">
                                                    {{ ucfirst($item->jenis_pengajuan) }}
                                                </span>
                                            </td>
                                            <td class="py-3 fw-semibold">{{ $item->nomor_registrasi ?? '-' }}</td>
                                            <td class="py-3">
                                                @include('components.workflow-status', ['pengajuan' => $item])
                                            </td>
                                            <td class="py-3">{{ $item->updated_at->format('d M Y') }}</td>
                                            <td class="px-4 py-3 text-end">
                                                <a href="{{ route('aktivitas.show', $item) }}" class="btn btn-sm btn-outline-primary" style="border-radius: 6px; font-size: 12px;">
                                                    <i class="bi bi-eye me-1"></i> Lihat
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox d-block fs-2 mb-2 text-secondary"></i>
                            <p>Belum ada pengajuan yang selesai.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

</div>

<style>
.type5-stage-scroll {
    overflow-x: auto;
    padding-bottom: 6px;
}

.type5-stage-track {
    display: grid;
    grid-template-columns: repeat(8, minmax(145px, 1fr));
    min-width: 1160px;
    background: #fff;
    border: 1px solid #e2e8f0;
}

.type5-stage-item {
    position: relative;
    min-height: 112px;
    padding: 16px 14px;
    border-right: 1px solid #e2e8f0;
}

.type5-stage-item:last-child {
    border-right: 0;
}

.type5-stage-number {
    display: block;
    margin-bottom: 10px;
    color: #166534;
    font-size: 12px;
    font-weight: 700;
}

.type5-stage-item strong {
    display: block;
    color: #0f172a;
    font-size: 12px;
    line-height: 1.35;
}

.type5-stage-item small {
    display: block;
    margin-top: 7px;
    color: #64748b;
    font-size: 10px;
    line-height: 1.35;
}

.nav-link {
    color: #475569 !important;
    border: none;
    padding: 12px 16px;
    transition: all 0.3s ease;
}

.nav-link:hover {
    color: #0f172a !important;
    border-bottom: 3px solid #3b82f6;
}

.nav-link.active {
    color: #0284c7 !important;
    border-bottom: 3px solid #0284c7 !important;
    background: none !important;
}
</style>
@endsection
