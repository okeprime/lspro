@extends('layouts.app')
@section('title', 'Panel Keuangan - LS Pro')

@section('content')
<div class="container-fluid py-3 px-3 px-md-4">
    
    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 style="color: #1e293b; font-weight: 700; letter-spacing: -0.5px; margin-bottom: 4px;">Panel Kerja Keuangan</h2>
            <p class="text-muted small mb-0">Manajemen Perjanjian Sertifikasi dan Verifikasi Pembayaran (Billing).</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm p-3 mb-4" role="alert" style="border-radius: 12px; background-color: #f0fdf4; color: #166534;">
            <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i> <strong>Sukses!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm p-3 mb-4" role="alert" style="border-radius: 12px; background-color: #fef2f2; color: #991b1b;">
            <i class="fa-solid fa-triangle-exclamation me-2 fs-5 text-danger"></i> <strong>Gagal!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <ul class="nav nav-pills mb-4" id="keuanganTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-4" id="perjanjian-tab" data-bs-toggle="pill" data-bs-target="#perjanjian" type="button" role="tab" style="font-weight:600;">
                <i class="fa-solid fa-file-signature me-1"></i> Tagihan & Pembayaran
                <span class="badge bg-white text-primary ms-1 rounded-pill">{{ $pengajuansPerjanjian->count() + $invoices->count() }}</span>
            </button>
        </li>
        <li class="nav-item ms-2" role="presentation">
            <button class="nav-link rounded-pill px-4" id="riwayat-tab" data-bs-toggle="pill" data-bs-target="#riwayat" type="button" role="tab" style="font-weight:600;">
                <i class="fa-solid fa-clock-rotate-left me-1"></i> Riwayat Invoice
                <span class="badge bg-white text-primary ms-1 rounded-pill">{{ collect($allInvoices ?? [])->count() }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="keuanganTabsContent">
        <!-- TAB 1: PERJANJIAN SERTIFIKASI -->
        <div class="tab-pane fade show active" id="perjanjian" role="tabpanel">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                    <h5 class="mb-0 fw-bold d-flex align-items-center" style="color: #1e293b; font-size: 1rem;">
                        <i class="fa-solid fa-money-check-dollar text-primary me-2"></i> Antrean Tagihan & Pembayaran
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700;">ID / Referensi</th>
                                    <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700;">Nama Klien</th>
                                    <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700;">Keterangan</th>
                                    <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700;">Status</th>
                                    <th class="pe-4 py-3 text-end text-secondary text-uppercase" style="font-size: 11px; font-weight: 700;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $combinedQueue = collect();
                                    foreach ($pengajuansPerjanjian as $p) {
                                        $combinedQueue->push((object)[
                                            'type' => 'pengajuan',
                                            'date' => $p->updated_at,
                                            'data' => $p
                                        ]);
                                    }
                                    foreach ($invoices as $inv) {
                                        $combinedQueue->push((object)[
                                            'type' => 'invoice',
                                            'date' => $inv->created_at, // Use created_at or updated_at for invoices
                                            'data' => $inv
                                        ]);
                                    }
                                    $combinedQueue = $combinedQueue->sortByDesc('date');
                                @endphp

                                @forelse($combinedQueue as $row)
                                    @if($row->type === 'pengajuan')
                                        @php $item = $row->data; @endphp
                                        <!-- LOOP PERJANJIAN (PENERBITAN) -->
                                    <tr>
                                        <td class="ps-4 fw-bold" style="color: #475569;">
                                            #{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}
                                            <div class="text-muted small" style="font-size: 11px;">Tgl: {{ $item->updated_at->format('d/m/Y') }}</div>
                                        </td>
                                        <td>
                                            @php
                                                $df = is_array($item->data_form) ? $item->data_form : (json_decode($item->data_form, true) ?? []);
                                                $merek = $df['merek_produk'] ?? ($df['merek'] ?? 'Tanpa Merek');
                                            @endphp
                                            <div class="fw-bold" style="color: #1e293b;">{{ $merek }}</div>
                                            <div class="text-muted small" style="font-size: 11px;">{{ $item->user->nama_perusahaan ?? $item->user->nama_penghubung ?? 'Klien' }}</div>
                                        </td>
                                        <td>
                                            <div style="font-size: 13px; color: #475569; font-weight: 500;">Penerbitan Tagihan</div>
                                            <div class="text-muted small" style="font-size: 12px; max-width: 200px;">Menunggu admin menerbitkan invoice untuk tahap ini.</div>
                                        </td>
                                        <td>
                                            @if($item->status === 'billing_1')
                                                <span class="badge px-2 py-1" style="background-color: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; font-weight: 600; font-size: 11px; border-radius: 6px;">Menunggu Pembayaran Billing 1</span>
                                            @else
                                                <span class="badge bg-light text-secondary border px-2 py-1 text-capitalize" style="font-size: 11px; border-radius: 6px;">{{ str_replace('_', ' ', $item->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('admin.rab.create', $item->id) }}" class="btn btn-sm btn-outline-success px-3" style="border-radius: 8px; font-size: 12px; font-weight: 600;" title="Buat atau Edit Jumlah Tagihan untuk Klien ini">
                                                    <i class="fa-solid fa-calculator"></i> Jumlah Tagihan
                                                </a>
                                                
                                                @if(in_array($item->status, ['billing_1', 'billing_2', 'billing_3', 'billing_4']))
                                                    @php
                                                        $hasInvoice = $item->invoices()->where('jenis_tagihan', $item->status)->whereIn('status', ['unpaid', 'pending_verification'])->exists();
                                                    @endphp
                                                    @if($item->status === 'billing_3')
                                                        @php
                                                            $hasBdltInvoice = $item->invoices()->where('jenis_tagihan', 'billing_3_bdlt')->whereIn('status', ['unpaid', 'pending_verification'])->exists();
                                                        @endphp
                                                        @if(!$hasInvoice)
                                                            <button type="button" class="btn btn-sm btn-primary px-3" style="border-radius: 8px; font-size: 12px; font-weight: 600;" data-bs-toggle="modal" data-bs-target="#modalTagihanAudit{{ $item->id }}">
                                                                <i class="fa-solid fa-file-invoice-dollar"></i> Tagihan Audit
                                                            </button>
                                                            <div class="modal fade" id="modalTagihanAudit{{ $item->id }}" tabindex="-1" aria-hidden="true" style="text-align: left;">
                                                                <div class="modal-dialog modal-dialog-centered">
                                                                    <div class="modal-content" style="border-radius: 16px; border: none;">
                                                                        <div class="modal-header border-bottom-0 pb-0">
                                                                            <h5 class="modal-title fw-bold" style="color: #1e293b;">Terbitkan Tagihan Audit</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <form action="{{ route('admin.pengajuan.create_invoice', $item->id) }}" method="POST" enctype="multipart/form-data">
                                                                            @csrf
                                                                            <div class="modal-body">
                                                                                <p class="text-muted" style="font-size: 13px;">Upload file Billing (PDF/Gambar) tagihan Audit Kesesuaian ke klien.</p>
                                                                                <input type="file" name="file_invoice" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required style="border-radius: 10px;">
                                                                            </div>
                                                                            <div class="modal-footer border-top-0 pt-0">
                                                                                <button type="submit" class="btn btn-primary" style="border-radius: 8px; font-weight: 600;">Terbitkan & Upload</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <span class="badge bg-light text-secondary border mt-1">Audit Menunggu Pembayaran</span>
                                                        @endif
                                                        
                                                        @if(!$hasBdltInvoice)
                                                            <button type="button" class="btn btn-sm btn-info px-3" style="border-radius: 8px; font-size: 12px; font-weight: 600; color: white;" data-bs-toggle="modal" data-bs-target="#modalTagihanBdlt{{ $item->id }}">
                                                                <i class="fa-solid fa-plane"></i> Tagihan BDLT
                                                            </button>
                                                            <div class="modal fade" id="modalTagihanBdlt{{ $item->id }}" tabindex="-1" aria-hidden="true" style="text-align: left;">
                                                                <div class="modal-dialog modal-dialog-centered">
                                                                    <div class="modal-content" style="border-radius: 16px; border: none;">
                                                                        <div class="modal-header border-bottom-0 pb-0">
                                                                            <h5 class="modal-title fw-bold" style="color: #1e293b;">Terbitkan Tagihan BDLT</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <form action="{{ route('admin.pengajuan.create_invoice', ['id' => $item->id, 'jenis' => 'bdlt']) }}" method="POST" enctype="multipart/form-data">
                                                                            @csrf
                                                                            <div class="modal-body">
                                                                                <p class="text-muted" style="font-size: 13px;">Upload file Billing (PDF/Gambar) Biaya Di Luar Tarif.</p>
                                                                                <input type="file" name="file_invoice" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required style="border-radius: 10px;">
                                                                            </div>
                                                                            <div class="modal-footer border-top-0 pt-0">
                                                                                <button type="submit" class="btn btn-primary" style="border-radius: 8px; font-weight: 600;">Terbitkan & Upload</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <span class="badge bg-light text-secondary border mt-1">BDLT Menunggu Pembayaran</span>
                                                        @endif
                                                    @else
                                                        @if(!$hasInvoice)
                                                            <button type="button" class="btn btn-sm btn-primary px-3" style="border-radius: 8px; font-size: 12px; font-weight: 600;" data-bs-toggle="modal" data-bs-target="#modalTagihanBaru{{ $item->id }}">
                                                                <i class="fa-solid fa-file-invoice-dollar"></i> Terbitkan Tagihan
                                                            </button>
                                                            <div class="modal fade" id="modalTagihanBaru{{ $item->id }}" tabindex="-1" aria-hidden="true" style="text-align: left;">
                                                                <div class="modal-dialog modal-dialog-centered">
                                                                    <div class="modal-content" style="border-radius: 16px; border: none;">
                                                                        <div class="modal-header border-bottom-0 pb-0">
                                                                            <h5 class="modal-title fw-bold" style="color: #1e293b;">Terbitkan Tagihan</h5>
                                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <form action="{{ route('admin.pengajuan.create_invoice', $item->id) }}" method="POST" enctype="multipart/form-data">
                                                                            @csrf
                                                                            <div class="modal-body">
                                                                                <p class="text-muted" style="font-size: 13px;">Upload file Billing (PDF/Gambar) yang akan dikirim ke klien untuk tahap ini.</p>
                                                                                <input type="file" name="file_invoice" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required style="border-radius: 10px;">
                                                                            </div>
                                                                            <div class="modal-footer border-top-0 pt-0">
                                                                                <button type="submit" class="btn btn-primary" style="border-radius: 8px; font-weight: 600;">Terbitkan & Upload</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @else
                                                            <span class="badge bg-light text-secondary border mt-1">Menunggu Pembayaran</span>
                                                        @endif
                                                    @endif
                                                @else
                                                    <span class="badge bg-light text-secondary border mt-1">Menunggu Pembayaran</span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @elseif($row->type === 'invoice')
                                        @php $invoice = $row->data; @endphp
                                        <!-- LOOP INVOICES (VERIFIKASI PEMBAYARAN) -->
                                    <tr>
                                        <td class="ps-4 fw-bold" style="color: #475569;">
                                            {{ $invoice->invoice_number }}
                                            <div class="text-muted small" style="font-size: 11px;">Tgl: {{ $invoice->created_at->format('d/m/Y') }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-bold" style="color: #1e293b;">{{ $invoice->pengajuan->user->nama_perusahaan ?? $invoice->pengajuan->user->nama_penghubung ?? 'Klien' }}</div>
                                            <div class="text-muted small" style="font-size: 11px;">ID Pengajuan: #{{ str_pad($invoice->pengajuan_id, 5, '0', STR_PAD_LEFT) }}</div>
                                        </td>
                                        <td>
                                            <div style="font-size: 13px; color: #475569; font-weight: 600;">Rp {{ number_format($invoice->amount_total, 0, ',', '.') }}</div>
                                            <div class="text-muted small" style="font-size: 12px; max-width: 200px;">{{ $invoice->notes ?? 'Biaya Pendaftaran & Sertifikasi Awal' }}</div>
                                        </td>
                                        <td>
                                            @if($invoice->status === 'pending_verification')
                                                <span class="badge px-2 py-1" style="background-color: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; font-weight: 600; font-size: 11px; border-radius: 6px;">
                                                    <i class="fa-solid fa-hourglass-half me-1"></i> Menunggu Verifikasi
                                                </span>
                                            @elseif($invoice->status === 'unpaid')
                                                <span class="badge px-2 py-1" style="background-color: #fffbeb; color: #b45309; border: 1px solid #fde68a; font-weight: 600; font-size: 11px; border-radius: 6px;">
                                                    <i class="fa-solid fa-clock me-1"></i> Belum Dibayar
                                                </span>
                                            @elseif($invoice->status === 'paid')
                                                <span class="badge px-2 py-1" style="background-color: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; font-weight: 600; font-size: 11px; border-radius: 6px;">
                                                    <i class="fa-solid fa-check-circle me-1"></i> Lunas
                                                </span>
                                            @else
                                                <span class="badge bg-light text-secondary border px-2 py-1 text-capitalize" style="font-size: 11px; border-radius: 6px;">
                                                    {{ str_replace('_', ' ', $invoice->status) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('admin.rab.create', $invoice->pengajuan_id) }}" class="btn btn-sm btn-outline-success px-3 d-inline-flex align-items-center gap-1 shadow-sm" style="border-radius: 8px; font-size: 12px; font-weight: 600;" title="Buka Jumlah Tagihan">
                                                    <i class="fa-solid fa-calculator"></i> Jumlah Tagihan
                                                </a>
        
                                                @if($invoice->status === 'pending_verification')
                                                    <button type="button" class="btn btn-sm btn-primary px-3 d-inline-flex align-items-center gap-1 shadow-sm" style="border-radius: 8px; font-size: 12px; font-weight: 600;" data-bs-toggle="modal" data-bs-target="#modalVerifikasi{{ $invoice->id }}">
                                                        <i class="fa-solid fa-check-to-slot"></i> Verifikasi & Kwitansi
                                                    </button>
                                                @elseif($invoice->status === 'unpaid' && !$invoice->file_invoice)
                                                    <button type="button" class="btn btn-sm btn-outline-primary px-3 d-inline-flex align-items-center gap-1 shadow-sm" style="border-radius: 8px; font-size: 12px; font-weight: 600;" data-bs-toggle="modal" data-bs-target="#modalUploadBilling{{ $invoice->id }}">
                                                        <i class="fa-solid fa-upload"></i> Upload File Billing
                                                    </button>
                                                @else
                                                    <span class="text-muted small align-self-center"><i class="fa-solid fa-minus"></i></span>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fa-solid fa-check-circle fs-1 mb-3 text-light"></i>
                                                <p class="mb-0">Belum ada antrean tagihan atau verifikasi pembayaran.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div> <!-- Close card -->
        </div> <!-- Close tab-pane -->
        
        <div class="tab-pane fade" id="riwayat" role="tabpanel">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                    <h5 class="mb-0 fw-bold d-flex align-items-center" style="color: #1e293b; font-size: 1rem;">
                        <i class="fa-solid fa-clock-rotate-left text-primary me-2"></i> Riwayat Semua Invoice
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">No. Invoice</th>
                                    <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">Nama Klien</th>
                                    <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">Total Tagihan</th>
                                    <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">Jatuh Tempo</th>
                                    <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">Status</th>
                                    <th class="pe-4 py-3 text-end text-secondary text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">Aksi Tindakan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($allInvoices as $inv)
                                    <tr>
                                        <td class="ps-4 fw-bold" style="color: #475569;">
                                            {{ $inv->invoice_number }}
                                            <div class="text-muted small" style="font-size: 11px;">Tgl: {{ $inv->created_at->format('d/m/Y') }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-bold" style="color: #1e293b;">{{ $inv->pengajuan->user->nama_perusahaan ?? $inv->pengajuan->user->nama_penghubung ?? 'Klien' }}</div>
                                            <div class="text-muted small" style="font-size: 11px;">ID Pengajuan: #{{ str_pad($inv->pengajuan_id, 5, '0', STR_PAD_LEFT) }}</div>
                                        </td>
                                        <td style="color: #475569; font-size: 14px; font-weight: 600;">
                                            Rp {{ number_format($inv->amount_total, 0, ',', '.') }}
                                        </td>
                                        <td>
                                            <div style="font-size: 12px; color: #64748b;">{{ $inv->due_date ? \Carbon\Carbon::parse($inv->due_date)->format('d/m/Y') : '-' }}</div>
                                        </td>
                                        <td>
                                            @if($inv->status === 'pending_verification')
                                                <span class="badge px-2 py-1" style="background-color: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; font-weight: 600; font-size: 11px; border-radius: 6px;">Menunggu Verifikasi</span>
                                            @elseif($inv->status === 'unpaid')
                                                <span class="badge px-2 py-1" style="background-color: #fffbeb; color: #b45309; border: 1px solid #fde68a; font-weight: 600; font-size: 11px; border-radius: 6px;">Belum Dibayar</span>
                                            @elseif($inv->status === 'paid')
                                                <span class="badge px-2 py-1" style="background-color: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; font-weight: 600; font-size: 11px; border-radius: 6px;">Lunas</span>
                                            @else
                                                <span class="badge bg-light text-secondary border px-2 py-1 text-capitalize" style="font-size: 11px; border-radius: 6px;">{{ str_replace('_', ' ', $inv->status) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex justify-content-end gap-2">
                                                @if($inv->file_invoice)
                                                <a href="{{ Storage::url($inv->file_invoice) }}" target="_blank" class="btn btn-sm btn-outline-info px-2" style="border-radius: 6px;" title="Lihat E-Billing">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>
                                                @endif
                                                
                                                <button type="button" class="btn btn-sm btn-outline-primary px-2" style="border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#modalEditInvoice{{ $inv->id }}" title="Edit Invoice">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </button>
                                                
                                                <form action="{{ route('admin.invoice.destroy', $inv->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus invoice ini secara permanen?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger px-2" style="border-radius: 6px;" title="Hapus Invoice">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fa-solid fa-folder-open fs-1 mb-3 text-light"></i>
                                                <p class="mb-0">Belum ada riwayat invoice.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
</div>

<!-- MODALS -->
@foreach($allInvoices as $inv)
    <!-- Modal Edit Invoice -->
    <div class="modal fade" id="modalEditInvoice{{ $inv->id }}" tabindex="-1" aria-hidden="true" style="text-align: left;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; border: none;">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" style="color: #1e293b;">Edit Invoice #{{ $inv->invoice_number }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.invoice.update', $inv->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body py-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Nomor Invoice</label>
                            <input type="text" name="invoice_number" class="form-control form-control-sm" value="{{ $inv->invoice_number }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Total Tagihan (Rp)</label>
                            <input type="number" name="amount_total" class="form-control form-control-sm" value="{{ $inv->amount_total }}" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Jatuh Tempo</label>
                            <input type="date" name="due_date" class="form-control form-control-sm" value="{{ $inv->due_date ? \Carbon\Carbon::parse($inv->due_date)->format('Y-m-d') : '' }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size: 13px;">Ganti File E-Billing (Opsional)</label>
                            <input type="file" name="file_invoice" class="form-control form-control-sm" accept=".pdf,.jpg,.jpeg,.png">
                            <small class="text-muted" style="font-size: 11px;">Abaikan jika tidak ingin mengubah file saat ini.</small>
                        </div>
                    </div>
                    <div class="modal-footer border-top-0 pt-0">
                        <button type="button" class="btn btn-light shadow-sm px-4" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600;">Batal</button>
                        <button type="submit" class="btn btn-primary shadow-sm px-4" style="border-radius: 8px; font-weight: 600;">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endforeach

@foreach($invoices as $invoice)
    @if($invoice->status === 'unpaid' && !$invoice->file_invoice)
            <!-- Modal Upload Billing -->
            <div class="modal fade" id="modalUploadBilling{{ $invoice->id }}" tabindex="-1" aria-hidden="true" style="text-align: left;">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border-radius: 16px; border: none;">
                        <div class="modal-header border-bottom-0 pb-0">
                            <h5 class="modal-title fw-bold" style="color: #1e293b;">Upload File Billing (Invoice)</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('admin.invoice.upload_billing', $invoice->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-secondary" style="font-size: 12px;">File Dokumen Billing (PDF/JPG)</label>
                                    <input type="file" name="file_invoice" class="form-control shadow-none" accept=".pdf,.jpg,.jpeg,.png" required style="border-radius: 10px;">
                                    <div class="form-text" style="font-size: 11px;">Maksimal 5MB. File ini akan diunduh oleh Klien.</div>
                                </div>
                            </div>
                            <div class="modal-footer border-top-0 pt-0">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600;">Batal</button>
                                <button type="submit" class="btn btn-primary" style="border-radius: 8px; font-weight: 600;">Upload & Kirim</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    @endif
    
    @if($invoice->status === 'pending_verification')
            <!-- Modal Verifikasi -->
            <div class="modal fade" id="modalVerifikasi{{ $invoice->id }}" tabindex="-1" aria-hidden="true" style="text-align: left;">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border-radius: 16px; border: none;">
                        <div class="modal-header border-bottom-0 pb-0">
                            <h5 class="modal-title fw-bold" style="color: #1e293b;">Verifikasi Pembayaran</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form action="{{ route('admin.invoice.verify', $invoice->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="alert alert-info py-2 mb-3" style="font-size: 13px; border-radius: 10px;">
                                    <i class="fa-solid fa-circle-info me-1"></i> Periksa apakah dana telah masuk ke rekening / e-wallet.
                                </div>
                                
                                <div class="border rounded-3 p-3 mb-3 bg-light">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">No. Invoice:</span>
                                        <span class="fw-bold">{{ $invoice->invoice_number }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted small">Klien:</span>
                                        <span class="fw-bold">{{ $invoice->pengajuan->user->nama_perusahaan ?? 'Klien' }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-muted small">Total Dibayar:</span>
                                        <span class="fw-bold text-success">Rp {{ number_format($invoice->amount_total, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label fw-bold text-secondary" style="font-size: 12px;">Bukti Transfer</label>
                                    <div class="p-3 border rounded text-center bg-white">
                                        @php
                                            $buktiFile = $invoice->file_bukti_bayar;
                                        @endphp
                                        
                                        @if($buktiFile)
                                            @php
                                                $ext = pathinfo($buktiFile, PATHINFO_EXTENSION);
                                                $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png']);
                                                $fileUrl = url('/unduh/' . $buktiFile);
                                            @endphp
                                            
                                            @if($isImage)
                                                <img src="{{ $fileUrl }}" alt="Bukti Transfer" class="img-fluid rounded mb-3" style="max-height: 200px; object-fit: contain;">
                                            @else
                                                <i class="fa-solid fa-file-pdf fs-1 text-danger mb-2"></i>
                                            @endif
                                            
                                            <p class="small text-muted mb-0">Klien telah mengunggah bukti pembayaran.</p>
                                            <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                                <i class="fa-solid fa-download me-1"></i> Unduh / Lihat File
                                            </a>
                                        @else
                                            <i class="fa-regular fa-image fs-1 text-muted mb-2"></i>
                                            <p class="small text-muted mb-0">Belum ada bukti pembayaran.</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold text-secondary" style="font-size: 12px;">Keputusan Verifikasi</label>
                                    <select name="action" class="form-select shadow-none" required id="selectAction{{ $invoice->id }}" onchange="toggleTolak({{ $invoice->id }})">
                                        <option value="">-- Pilih Keputusan --</option>
                                        <option value="terima">Terima & Validasi Lunas</option>
                                        <option value="tolak">Tolak (Bukti tidak sah / kurang)</option>
                                    </select>
                                </div>
                                <div class="mb-3 d-none" id="uploadKwitansi{{ $invoice->id }}">
                                    <label class="form-label fw-bold text-secondary" style="font-size: 12px;">Upload Dokumen Kwitansi</label>
                                    <input type="file" name="file_kwitansi" class="form-control shadow-none" accept=".pdf,.jpg,.jpeg,.png" style="border-radius: 10px;">
                                    <div class="form-text" style="font-size: 11px;">Wajib jika menerima pembayaran. Maks 5MB.</div>
                                </div>
                                <div class="mb-2 d-none" id="catatanTolak{{ $invoice->id }}">
                                    <label class="form-label fw-bold text-secondary" style="font-size: 12px;">Alasan Penolakan</label>
                                    <textarea name="catatan" class="form-control shadow-none" rows="2" placeholder="Sebutkan mengapa bukti ditolak..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-top-0 pt-0">
                                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600;">Batal</button>
                                <button type="submit" class="btn btn-primary" style="border-radius: 8px; font-weight: 600;">Simpan Validasi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
    @endif
@endforeach

<script>
    function toggleTolak(id) {
        var action = document.getElementById('selectAction' + id).value;
        var tolakDiv = document.getElementById('catatanTolak' + id);
        var uploadDiv = document.getElementById('uploadKwitansi' + id);
        
        if (action === 'tolak') {
            tolakDiv.classList.remove('d-none');
            uploadDiv.classList.add('d-none');
            tolakDiv.querySelector('textarea').required = true;
            if(uploadDiv.querySelector('input')) uploadDiv.querySelector('input').required = false;
        } else if (action === 'terima') {
            tolakDiv.classList.add('d-none');
            uploadDiv.classList.remove('d-none');
            tolakDiv.querySelector('textarea').required = false;
            if(uploadDiv.querySelector('input')) uploadDiv.querySelector('input').required = true;
        } else {
            tolakDiv.classList.add('d-none');
            uploadDiv.classList.add('d-none');
            tolakDiv.querySelector('textarea').required = false;
            if(uploadDiv.querySelector('input')) uploadDiv.querySelector('input').required = false;
        }
    }

    function formatRupiah(elem) {
        let value = elem.value.replace(/[^,\d]/g, '').toString();
        let split = value.split(',');
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        elem.value = rupiah;
        
        let hiddenInput = elem.parentElement.querySelector('.input-nominal-hidden');
        if(hiddenInput) {
            // Kita hilangkan titik (pemisah ribuan)
            hiddenInput.value = value.replace(/\./g, '').replace(',', '.');
        }
    }

    function calculateBilling2(elem) {
        let jumlah = parseInt(elem.value) || 1;
        let baseHarga = 5000000;
        let hargaPerPaket = 2000000;
        let total = baseHarga + (hargaPerPaket * jumlah);
        
        let container = elem.closest('.modal-body');
        let nominalInput = container.querySelector('.input-rupiah');
        if(nominalInput) {
            nominalInput.value = total;
            formatRupiah(nominalInput);
        }
    }
</script>
@endsection
