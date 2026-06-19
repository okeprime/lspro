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
                                    <div class="fw-bold">{{ $df['merek'] ?? 'Merek Produk' }}</div>
                                    <small class="text-muted">{{ $df['nama_produk'] ?? 'Pupuk' }}</small>
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
                                    @if($invoice->status === 'unpaid')
                                        <button type="button" class="btn btn-sm btn-primary fw-semibold rounded-pill px-3 shadow-sm" style="font-size: 12px;" data-bs-toggle="modal" data-bs-target="#payModal{{ $invoice->id }}">
                                            <i class="fa-solid fa-money-bill-wave me-1"></i> Bayar
                                        </button>
                                        
                                        <!-- Modal Pembayaran -->
                                        <div class="modal fade" id="payModal{{ $invoice->id }}" tabindex="-1" aria-labelledby="payModalLabel{{ $invoice->id }}" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <form action="{{ route('invoice.pay', $invoice->id) }}" method="POST" enctype="multipart/form-data" class="modal-content text-start" style="border-radius: 16px; border: none; overflow: hidden;">
                                                    @csrf
                                                    <div class="modal-header border-0 bg-light pb-2">
                                                        <h5 class="modal-title fw-bold text-dark" id="payModalLabel{{ $invoice->id }}">Detail Pembayaran Invoice</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body pt-2 px-4 pb-4">
                                                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                                            <div>
                                                                <small class="text-muted d-block" style="font-size: 12px;">No. Invoice</small>
                                                                <strong class="text-dark">{{ $invoice->invoice_number }}</strong>
                                                            </div>
                                                            <div class="text-end">
                                                                <small class="text-muted d-block" style="font-size: 12px;">Total Tagihan</small>
                                                                <strong class="text-success fs-5">Rp {{ number_format($invoice->amount_total, 0, ',', '.') }}</strong>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="mb-4">
                                                            <label class="form-label fw-semibold" style="font-size: 13px;">Rincian Biaya Sertifikasi</label>
                                                            <div class="bg-white border rounded p-3" style="font-size: 13px;">
                                                                <div class="d-flex justify-content-between mb-2">
                                                                    <span class="text-muted">Pendaftaran & Tinjauan Dokumen</span>
                                                                    <span class="fw-medium">Rp 2.000.000</span>
                                                                </div>
                                                                <div class="d-flex justify-content-between mb-2">
                                                                    <span class="text-muted">Audit Kecukupan & Kesesuaian</span>
                                                                    <span class="fw-medium">Rp 8.000.000</span>
                                                                </div>
                                                                <div class="d-flex justify-content-between mb-2">
                                                                    <span class="text-muted">Pengambilan & Uji Sampel</span>
                                                                    <span class="fw-medium">Rp 3.500.000</span>
                                                                </div>
                                                                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                                                    <span class="text-muted">Evaluasi & Keputusan</span>
                                                                    <span class="fw-medium">Rp 1.500.000</span>
                                                                </div>
                                                                <div class="d-flex justify-content-between mt-2">
                                                                    <strong class="text-dark">Total Biaya</strong>
                                                                    <strong class="text-success">Rp 15.000.000</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="mb-4">
                                                            <label class="form-label fw-semibold" style="font-size: 13px;">Pilih Metode Pembayaran</label>
                                                            <select class="form-select form-select-sm" id="paymentMethod{{ $invoice->id }}" onchange="togglePaymentInfo({{ $invoice->id }})">
                                                                <option value="" selected disabled>-- Pilih Metode --</option>
                                                                <option value="bank">Transfer Bank</option>
                                                                <option value="ewallet">E-Wallet (DANA)</option>
                                                            </select>
                                                        </div>
                                                        
                                                        <!-- Info Bank -->
                                                        <div id="infoBank{{ $invoice->id }}" class="d-none bg-light p-3 rounded-3 border mb-3">
                                                            <div class="d-flex align-items-center gap-3 mb-2">
                                                                <i class="fa-solid fa-building-columns text-primary fs-3"></i>
                                                                <div>
                                                                    <div class="fw-bold" style="font-size: 14px;">Bank Mandiri</div>
                                                                    <div class="text-muted" style="font-size: 12px;">KCP Bogor Juanda</div>
                                                                </div>
                                                            </div>
                                                            <hr class="my-2">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <small class="text-muted d-block" style="font-size: 11px;">Nomor Rekening</small>
                                                                    <strong class="text-dark" style="font-size: 16px;">133-00-998822-1</strong>
                                                                </div>
                                                                <div class="text-end">
                                                                    <small class="text-muted d-block" style="font-size: 11px;">Atas Nama</small>
                                                                    <strong class="text-dark" style="font-size: 13px;">BPN BRMP SDLP - LSPro</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <!-- Info E-Wallet -->
                                                        <div id="infoEwallet{{ $invoice->id }}" class="d-none bg-light p-3 rounded-3 border mb-3">
                                                            <div class="d-flex align-items-center gap-3 mb-2">
                                                                <i class="fa-solid fa-wallet text-info fs-3"></i>
                                                                <div>
                                                                    <div class="fw-bold" style="font-size: 14px;">E-Wallet DANA</div>
                                                                    <div class="text-muted" style="font-size: 12px;">Transfer Saldo</div>
                                                                </div>
                                                            </div>
                                                            <hr class="my-2">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div>
                                                                    <small class="text-muted d-block" style="font-size: 11px;">Nomor Tujuan</small>
                                                                    <strong class="text-dark" style="font-size: 16px;">0895-2270-4092</strong>
                                                                </div>
                                                                <div class="text-end">
                                                                    <small class="text-muted d-block" style="font-size: 11px;">Atas Nama</small>
                                                                    <strong class="text-dark" style="font-size: 13px;">MUHAMAD RAKHA BUANA</strong>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div id="uploadForm{{ $invoice->id }}" class="d-none mt-3 p-3 bg-white border rounded">
                                                            <label class="form-label fw-semibold" style="font-size: 13px;"><i class="fa-solid fa-cloud-arrow-up text-primary me-1"></i> Upload Bukti Transfer</label>
                                                            <input type="file" name="bukti_transfer" class="form-control form-control-sm mb-3" accept="image/*,.pdf" required>
                                                            <button type="submit" class="btn btn-success btn-sm w-100 fw-bold shadow-sm" style="border-radius: 8px;">
                                                                <i class="fa-solid fa-paper-plane me-1"></i> Konfirmasi & Kirim Bukti
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @elseif($invoice->status === 'pending_verification')
                                        <button class="btn btn-sm btn-light border fw-semibold rounded-pill px-3 shadow-sm" style="font-size: 12px; color: #0284c7; background: #f0f9ff;" disabled>
                                            <i class="fa-solid fa-clock-rotate-left me-1"></i> Proses Verifikasi
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-light border fw-semibold rounded-pill px-3" style="font-size: 12px; color: #64748b;" disabled>
                                            <i class="fa-solid fa-check me-1"></i> Lunas
                                        </button>
                                    @endif
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

<script>
function togglePaymentInfo(id) {
    const method = document.getElementById('paymentMethod' + id).value;
    const bankInfo = document.getElementById('infoBank' + id);
    const ewalletInfo = document.getElementById('infoEwallet' + id);
    const uploadForm = document.getElementById('uploadForm' + id);
    
    // Hide all first
    bankInfo.classList.add('d-none');
    ewalletInfo.classList.add('d-none');
    uploadForm.classList.add('d-none');
    
    // Show selected
    if (method === 'bank') {
        bankInfo.classList.remove('d-none');
        uploadForm.classList.remove('d-none');
    } else if (method === 'ewallet') {
        ewalletInfo.classList.remove('d-none');
        uploadForm.classList.remove('d-none');
    }
}
</script>
@endsection
