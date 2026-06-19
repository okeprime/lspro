@extends('layouts.app')
@section('title', 'Panel Keuangan - LS Pro')

@section('content')
<div class="container-fluid py-3 px-3 px-md-4">
    
    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 style="color: #1e293b; font-weight: 700; letter-spacing: -0.5px; margin-bottom: 4px;">Panel Kerja Keuangan</h2>
            <p class="text-muted small mb-0">Verifikasi bukti pembayaran invoice dari klien.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm p-3 mb-4" role="alert" style="border-radius: 12px; background-color: #f0fdf4; color: #166534;">
            <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i> <strong>Sukses!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
            <h5 class="mb-0 fw-bold d-flex align-items-center" style="color: #1e293b; font-size: 1rem;">
                <i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i> Antrean Verifikasi Pembayaran
            </h5>
            <div>
                <span class="badge bg-info text-white rounded-pill">{{ $invoices->where('status', 'pending_verification')->count() }} Menunggu Verifikasi</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">No. Invoice</th>
                            <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">Nama Klien</th>
                            <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">Total Tagihan</th>
                            <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">Status</th>
                            <th class="pe-4 py-3 text-end text-secondary text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">Aksi Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoices as $invoice)
                            <tr>
                                <td class="ps-4 fw-bold" style="color: #475569;">
                                    {{ $invoice->invoice_number }}
                                    <div class="text-muted small" style="font-size: 11px;">Tgl: {{ $invoice->created_at->format('d/m/Y') }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold" style="color: #1e293b;">{{ $invoice->pengajuan->user->nama_perusahaan ?? $invoice->pengajuan->user->nama_penghubung ?? 'Klien' }}</div>
                                    <div class="text-muted small" style="font-size: 11px;">ID Pengajuan: #{{ str_pad($invoice->pengajuan_id, 5, '0', STR_PAD_LEFT) }}</div>
                                </td>
                                <td style="color: #475569; font-size: 14px; font-weight: 600;">
                                    Rp {{ number_format($invoice->amount_total, 0, ',', '.') }}
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
                                        @if($invoice->status === 'pending_verification')
                                            <button type="button" class="btn btn-sm btn-primary px-3 d-inline-flex align-items-center gap-1 shadow-sm" style="border-radius: 8px; font-size: 12px; font-weight: 600;" data-bs-toggle="modal" data-bs-target="#modalVerifikasi{{ $invoice->id }}">
                                                <i class="fa-solid fa-check-to-slot"></i> Verifikasi Pembayaran
                                            </button>
                                            
                                            <!-- Modal Verifikasi -->
                                            <div class="modal fade" id="modalVerifikasi{{ $invoice->id }}" tabindex="-1" aria-hidden="true" style="text-align: left;">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content" style="border-radius: 16px; border: none;">
                                                        <div class="modal-header border-bottom-0 pb-0">
                                                            <h5 class="modal-title fw-bold" style="color: #1e293b;">Verifikasi Pembayaran</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('admin.invoice.verify', $invoice->id) }}" method="POST">
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
                                                                        <i class="fa-regular fa-image fs-1 text-muted mb-2"></i>
                                                                        <p class="small text-muted mb-0">Klien telah mengunggah bukti pembayaran.</p>
                                                                        <a href="#" class="btn btn-sm btn-outline-primary mt-2"><i class="fa-solid fa-download me-1"></i> Unduh File</a>
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
                                            <script>
                                                function toggleTolak(id) {
                                                    const action = document.getElementById('selectAction' + id).value;
                                                    const catatan = document.getElementById('catatanTolak' + id);
                                                    if(action === 'tolak') {
                                                        catatan.classList.remove('d-none');
                                                        catatan.querySelector('textarea').required = true;
                                                    } else {
                                                        catatan.classList.add('d-none');
                                                        catatan.querySelector('textarea').required = false;
                                                    }
                                                }
                                            </script>
                                        @else
                                            <span class="text-muted small"><i class="fa-solid fa-minus"></i></span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fa-solid fa-file-invoice-dollar fs-1 mb-3 text-light"></i>
                                        <p class="mb-0">Belum ada antrean verifikasi pembayaran.</p>
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
@endsection
