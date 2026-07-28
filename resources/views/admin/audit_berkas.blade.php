@extends('layouts.app')
@section('title', 'Audit Kesesuaian Berkas - LS Pro')

@section('content')
<div class="container-fluid py-3 px-3 px-md-4">
    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 style="color: #1e293b; font-weight: 700; letter-spacing: -0.5px; margin-bottom: 4px;">Audit Kesesuaian Berkas</h2>
            <p class="text-muted small mb-0">Pemeriksaan dokumen teknis dan pemberian catatan audit oleh Tim Auditor.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm p-3 mb-4" role="alert" style="border-radius: 12px; background-color: #f0fdf4; color: #166534;">
            <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i> <strong>Sukses!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold" style="color: #1e293b; font-size: 1rem;">
                <i class="fa-solid fa-file-signature text-primary me-2"></i> Daftar Berkas Menunggu Audit
            </h5>
            <span class="badge bg-primary rounded-pill">{{ $pengajuans->count() }} Dokumen</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700;">ID Berkas</th>
                            <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700;">Klien / Perusahaan</th>
                            <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700;">Tanggal Oper</th>
                            <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700;">File Evaluasi / Permohonan</th>
                            <th class="pe-4 py-3 text-end text-secondary text-uppercase" style="font-size: 11px; font-weight: 700;">Tindakan Audit</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengajuans as $item)
                            <tr>
                                <td class="ps-4 fw-bold" style="color: #475569;">#{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td>
                                    <div class="fw-bold" style="color: #1e293b;">{{ $item->user->name }}</div>
                                    <div class="text-muted small">{{ $item->user->email }}</div>
                                </td>
                                <td style="font-size: 13px; color: #475569;">{{ $item->updated_at->translatedFormat('d F Y') }}</td>
                                <td>
                                    <a href="{{ route('pengajuan.download', $item->id) }}" class="btn btn-sm btn-light border" target="_blank" style="border-radius: 8px; font-size: 12px; font-weight: 600;">
                                        <i class="fa-solid fa-file-pdf text-danger"></i> Dokumen
                                    </a>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('admin.ceklis_kelengkapan', $item->id) }}" class="btn btn-sm btn-primary px-3 fw-bold" style="border-radius: 8px;">
                                        <i class="fa-solid fa-file-signature me-1"></i> Proses Audit Lapangan
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <div class="py-3">
                                        <i class="fa-solid fa-file-circle-check d-block mb-2 text-secondary fs-2"></i>
                                        <span style="font-size: 14px; font-weight: 500;">Belum ada dokumen yang perlu diaudit.</span>
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
