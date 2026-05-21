@extends('layouts.app')

@section('title', 'Panel Kendali Administrasi TU - LS Pro')

@section('content')
<div class="container py-2">
    
    <div class="mb-4">
        <h2 style="color: #1e293b; font-weight: 700; letter-spacing: -0.5px; margin-bottom: 4px;">Panel Kerja Tata Usaha</h2>
        <p class="text-muted small">Verifikasi berkas persyaratan klausal SNI masuk berdasarkan Skema Sertifikasi Form Kelengkapan 7.2-4.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm p-3 mb-4" role="alert" style="border-radius: 12px; background-color: #f0fdf4; color: #166534;">
            <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i> <strong>Sukses!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm p-3 mb-4" role="alert" style="border-radius: 12px; background-color: #fef2f2; color: #991b1b;">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5 text-danger"></i> <strong>Gagal!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="p-4 bg-white shadow-sm" style="border-left: 4px solid #2563eb; border-radius: 12px;">
                <div class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Total Masuk</div>
                <div class="fs-2 fw-bold mt-1" style="color: #1e293b;">{{ $stats['total_masuk'] ?? $pengajuans->count() }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-4 bg-white shadow-sm" style="border-left: 4px solid #d97706; border-radius: 12px;">
                <div class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Perlu Dicek (TU)</div>
                <div class="fs-2 fw-bold mt-1" style="color: #1e293b;">{{ $stats['perlu_dicek'] ?? $pengajuans->whereIn('status', ['diajukan', 'perbaikan'])->count() }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-4 bg-white shadow-sm" style="border-left: 4px solid #16a34a; border-radius: 12px;">
                <div class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Berkas Lengkap</div>
                <div class="fs-2 fw-bold mt-1" style="color: #1e293b;">{{ $stats['total_lengkap'] ?? $pengajuans->where('status', 'disetujui_tu')->count() }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="p-4 bg-white shadow-sm" style="border-left: 4px solid #dc2626; border-radius: 12px;">
                <div class="text-muted small fw-bold text-uppercase" style="letter-spacing: 0.5px;">Perlu Revisi</div>
                <div class="fs-2 fw-bold mt-1" style="color: #1e293b;">{{ $stats['total_revisi'] ?? $pengajuans->where('status', 'perbaikan')->count() }}</div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-bottom" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
            <h5 class="mb-0 fw-bold d-flex align-items-center" style="color: #1e293b; font-size: 1rem;">
                <i class="bi bi-journal-check text-success me-2 fs-5"></i> Antrean Verifikasi Kelengkapan Dokumen (Form 7.2-4)
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;" width="12%">ID Berkas</th>
                            <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;" width="25%">Nama Perusahaan / Klien</th>
                            <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;" width="18%">Tanggal Masuk</th>
                            <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;" width="12%">Prosedur</th>
                            <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;" width="15%">File Form 7.2-1</th>
                            <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;" width="18%">Status Alur</th>
                            <th class="pe-4 py-3 text-end text-secondary text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;" width="15%">Aksi Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengajuans as $item)
                            <tr>
                                <td class="ps-4 fw-bold" style="color: #475569;">
                                    #{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}
                                </td>
                                <td>
                                    <div class="fw-bold" style="color: #1e293b;">{{ $item->user->name ?? 'Klien Umum' }}</div>
                                    <div class="text-muted small" style="font-size: 11px;">{{ $item->user->email ?? '-' }}</div>
                                </td>
                                <td style="color: #475569; font-size: 13px;">
                                    <div>{{ $item->created_at->translatedFormat('d M Y') }}</div>
                                    <div class="text-muted" style="font-size: 11px;">Jam {{ $item->created_at->format('H:i') }} WIB</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1" style="font-weight: 600; border-radius: 6px;">Tahap {{ $item->tahap }}</span>
                                </td>
                                <td>
                                    <a href="{{ asset('storage/permohonan/' . $item->file_permohonan) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1" target="_blank" style="border-radius: 8px; font-size: 12px; font-weight: 500;">
                                        <i class="bi bi-file-earmark-word text-primary"></i> Unduh Berkas
                                    </a>
                                </td>
                                <td>
                                    @if($item->status === 'diajukan')
                                        <span class="badge px-2 py-1 d-inline-flex align-items-center" style="background-color: #fffbeb; color: #b45309; border: 1px solid #fde68a; font-weight: 600; font-size: 11px; border-radius: 6px;">
                                            <i class="bi bi-clock me-1"></i> Menunggu Dicek TU
                                        </span>
                                    @elseif($item->status === 'disetujui_tu' || $item->status === 'lengkap')
                                        <span class="badge px-2 py-1 d-inline-flex align-items-center" style="background-color: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; font-weight: 600; font-size: 11px; border-radius: 6px;">
                                            <i class="bi bi-check-circle me-1"></i> Valid / Lengkap
                                        </span>
                                    @elseif($item->status === 'perbaikan')
                                        <span class="badge px-2 py-1 d-inline-flex align-items-center" style="background-color: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; font-weight: 600; font-size: 11px; border-radius: 6px;">
                                            <i class="bi bi-exclamation-triangle me-1"></i> Masa Perbaikan
                                        </span>
                                    @else
                                        <span class="badge bg-light text-secondary border px-2 py-1 text-capitalize" style="font-size: 11px; border-radius: 6px;">
                                            {{ $item->status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    @if($item->status === 'disetujui_tu' || $item->status === 'lengkap')
                                        <button class="btn btn-sm btn-light text-muted border" disabled style="border-radius: 8px; font-size: 12px; font-weight: 600;">
                                            <i class="bi bi-lock-fill me-1"></i> Selesai
                                        </button>
                                    @else
                                        <a href="{{ route('admin.ceklis_kelengkapan', $item->id) }}" class="btn btn-sm btn-success px-3 d-inline-flex align-items-center gap-1" style="border-radius: 8px; font-size: 12px; font-weight: 600; background-color: #16a34a; border-color: #16a34a;">
                                            Isi Form Ceklis <i class="bi bi-chevron-right" style="font-size: 10px; -webkit-text-stroke: 0.5px;"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <div class="py-3">
                                        <i class="bi bi-inbox d-block mb-2 text-secondary fs-2"></i>
                                        <span style="font-size: 14px; font-weight: 500;">Belum ada data berkas permohonan masuk dari klien di database.</span>
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