@extends('layouts.app')

@section('title', 'Tagihan & Billing')

@section('extra-css')
<style>
    .billing-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .billing-stat-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .billing-val {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        margin-top: 5px;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1 text-dark">Tagihan &amp; Riwayat Pembayaran</h2>
            <p class="text-muted mb-0">Pantau seluruh invoice biaya sertifikasi LSPro Anda secara transparan.</p>
        </div>
    </div>

    {{-- Stats Summary --}}
    @php
        $totalUnpaid = $invoices->where('status', 'unpaid')->sum('amount_total');
        $totalPaid = $invoices->where('status', 'paid')->sum('amount_total');
        $countUnpaid = $invoices->where('status', 'unpaid')->count();
    @endphp
    <div class="billing-summary">
        <div class="billing-stat-card border-start border-4 border-warning">
            <span class="text-muted fw-semibold" style="font-size: 13px;"><i class="fa-solid fa-clock-rotate-left text-warning me-1"></i> Tagihan Belum Dibayar</span>
            <div class="billing-val text-warning">Rp {{ number_format($totalUnpaid, 0, ',', '.') }}</div>
            <small class="text-muted">{{ $countUnpaid }} invoice menunggu pembayaran</small>
        </div>
        <div class="billing-stat-card border-start border-4 border-success">
            <span class="text-muted fw-semibold" style="font-size: 13px;"><i class="fa-solid fa-circle-check text-success me-1"></i> Total Pembayaran Selesai</span>
            <div class="billing-val text-success">Rp {{ number_format($totalPaid, 0, ',', '.') }}</div>
            <small class="text-muted">Pembayaran telah terverifikasi</small>
        </div>
        <div class="billing-stat-card border-start border-4 border-teal">
            <span class="text-muted fw-semibold" style="font-size: 13px;"><i class="fa-solid fa-file-invoice-dollar text-teal me-1"></i> Total Invoice</span>
            <div class="billing-val" style="color: #0f766e;">{{ $invoices->count() }}</div>
            <small class="text-muted">Riwayat invoice diterbitkan</small>
        </div>
    </div>

    {{-- Info rekening dipindahkan ke dalam Modal --}}

    {{-- Invoices Table --}}
    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-file-invoice me-1 text-secondary"></i> Daftar Invoice</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">No. Invoice</th>
                            <th>Pengajuan</th>
                            <th>Keterangan</th>
                            <th>Tanggal Terbit</th>
                            <th>Jatuh Tempo</th>
                            <th>Total Tagihan</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            @php
                                $df = is_array($invoice->pengajuan->data_form) ? $invoice->pengajuan->data_form : (json_decode($invoice->pengajuan->data_form, true) ?? []);
                            @endphp
                            <tr>
                                <td class="ps-4 fw-bold text-dark">
                                    <span class="text-secondary font-monospace">{{ $invoice->invoice_number }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold">#{{ $invoice->pengajuan->nomor_registrasi ?? str_pad($invoice->pengajuan->id, 5, '0', STR_PAD_LEFT) }} - {{ $df['merek_produk'] ?? ($df['merek'] ?? 'Tanpa Merek') }}</div>
                                    <small class="text-muted">{{ $df['nama_produk'] ?? 'Produk tidak diketahui' }}</small>
                                </td>
                                <td>
                                    <div style="font-size: 13px;">{{ $invoice->notes ?? 'Biaya Pendaftaran & Sertifikasi Awal' }}</div>
                                </td>
                                <td>{{ $invoice->invoice_date->translatedFormat('d M Y') }}</td>
                                <td>{{ $invoice->due_date->translatedFormat('d M Y') }}</td>
                                <td class="fw-bold text-dark">Rp {{ number_format($invoice->amount_total, 0, ',', '.') }}</td>
                                <td>
                                    @if($invoice->status === 'paid')
                                        <span class="badge bg-success text-white" style="font-size: 11px; padding: 4px 8px;"><i class="fa-solid fa-circle-check me-1"></i> Lunas</span>
                                    @elseif($invoice->status === 'pending_verification')
                                        <span class="badge bg-info text-white" style="font-size: 11px; padding: 4px 8px;"><i class="fa-solid fa-hourglass-half me-1"></i> Menunggu Verifikasi</span>
                                    @else
                                        <span class="badge bg-warning text-dark" style="font-size: 11px; padding: 4px 8px;"><i class="fa-solid fa-clock me-1"></i> Belum Dibayar</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2 align-items-center">
                                        @if($invoice->file_invoice)
                                            <a href="{{ url('storage/' . $invoice->file_invoice) }}" target="_blank" class="btn btn-sm btn-outline-primary fw-semibold rounded-pill px-3 shadow-sm" style="font-size: 12px;">
                                                <i class="fa-solid fa-download me-1"></i> Download Tagihan
                                            </a>
                                            
                                            @if($invoice->status === 'unpaid')
                                                <button type="button" class="btn btn-sm btn-primary fw-semibold rounded-pill px-3 shadow-sm" style="font-size: 12px;" data-bs-toggle="modal" data-bs-target="#payModal{{ $invoice->id }}">
                                                    <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload Struk
                                                </button>
                                            
                                            <!-- Modal Upload Struk -->
                                            <div class="modal fade text-start" id="payModal{{ $invoice->id }}" tabindex="-1" aria-labelledby="payModalLabel{{ $invoice->id }}" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <form action="{{ route('invoice.pay', $invoice->id) }}" method="POST" enctype="multipart/form-data" class="modal-content" style="border-radius: 16px; border: none; overflow: hidden;">
                                                        @csrf
                                                        <div class="modal-header border-0 bg-light pb-2">
                                                            <h5 class="modal-title fw-bold text-dark" id="payModalLabel{{ $invoice->id }}">Upload Bukti Pembayaran</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body pt-2 px-4 pb-4">
                                                            <div class="alert alert-info" style="font-size: 13px;">
                                                                <i class="fa-solid fa-circle-info me-1"></i> Silakan cetak/unduh Surat Tagihan Anda, lakukan pembayaran di Bank, lalu unggah struk transfer di bawah ini.
                                                            </div>
                                                            
                                                            <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
                                                                <div>
                                                                    <small class="text-muted d-block" style="font-size: 12px;">No. Tagihan</small>
                                                                    <strong class="text-dark">{{ $invoice->invoice_number }}</strong>
                                                                </div>
                                                                <div class="text-end">
                                                                    <small class="text-muted d-block" style="font-size: 12px;">Total Tagihan</small>
                                                                    <strong class="text-success fs-5">Rp {{ number_format($invoice->amount_total, 0, ',', '.') }}</strong>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="p-3 bg-white border rounded">
                                                                <label class="form-label fw-semibold" style="font-size: 13px;"><i class="fa-solid fa-cloud-arrow-up text-primary me-1"></i> File Bukti Transfer (Struk/Resi)</label>
                                                                <input type="file" name="bukti_transfer" class="form-control form-control-sm mb-3" accept="image/*,.pdf" required>
                                                                <button type="submit" class="btn btn-success btn-sm w-100 fw-bold shadow-sm" style="border-radius: 8px;">
                                                                    <i class="fa-solid fa-paper-plane me-1"></i> Unggah Struk
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            @endif
                                        @else
                                            <span class="text-muted fst-italic px-3 py-1 bg-light rounded-pill border" style="font-size: 11px;">
                                                <i class="fa-solid fa-clock me-1"></i> Menunggu Admin Mengunggah Billing
                                            </span>
                                        @endif
                                        @if($invoice->status === 'pending_verification')
                                            <button class="btn btn-sm btn-light border fw-semibold rounded-pill px-3 shadow-sm" style="font-size: 12px; color: #0284c7; background: #f0f9ff;" disabled>
                                                <i class="fa-solid fa-clock-rotate-left me-1"></i> Proses Verifikasi
                                            </button>
                                        @else
                                            @if($invoice->file_kwitansi)
                                                <a href="{{ url('storage/' . $invoice->file_kwitansi) }}" target="_blank" class="btn btn-sm btn-success fw-semibold rounded-pill px-3 shadow-sm" style="font-size: 12px;">
                                                    <i class="fa-solid fa-receipt me-1"></i> Download Kwitansi
                                                </a>
                                            @else
                                                <button class="btn btn-sm btn-light border fw-semibold rounded-pill px-3" style="font-size: 12px; color: #64748b;" disabled>
                                                    <i class="fa-solid fa-check me-1"></i> Lunas
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-receipt d-block fs-1 mb-3"></i>
                                    Belum ada tagihan/invoice diterbitkan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
