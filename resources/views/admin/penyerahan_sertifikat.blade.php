@extends('layouts.app')
@section('title', 'Penyerahan Sertifikat - LS Pro')

@section('content')
<div class="container-fluid py-3 px-3 px-md-4">
    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 style="color: #1e293b; font-weight: 700; letter-spacing: -0.5px; margin-bottom: 4px;">Penyerahan Sertifikat</h2>
            <p class="text-muted small mb-0">Kelola dan kirim notifikasi penerbitan Sertifikat Kesesuaian SNI kepada klien yang lulus sertifikasi.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm p-3 mb-4" role="alert" style="border-radius: 12px; background-color: #f0fdf4; color: #166534;">
            <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i> <strong>Sukses!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="mb-0 fw-bold d-flex align-items-center" style="color: #1e293b; font-size: 1rem;">
                <i class="fa-solid fa-award text-warning me-2"></i> Daftar Sertifikat Siap Serah
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700;">ID Pengajuan</th>
                            <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700;">Klien / Perusahaan</th>
                            <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700;">Jenis Produk</th>
                            <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700;">Tanggal Keputusan</th>
                            <th class="pe-4 py-3 text-end text-secondary text-uppercase" style="font-size: 11px; font-weight: 700;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengajuans as $item)
                            <tr>
                                <td class="ps-4 fw-bold" style="color: #475569;">#{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td>
                                    @php
                                        $df = is_array($item->data_form) ? $item->data_form : (json_decode($item->data_form, true) ?? []);
                                        $merek = $df['merek_produk'] ?? ($df['merek'] ?? 'Tanpa Merek');
                                    @endphp
                                    <div class="fw-bold" style="color: #1e293b;">{{ $merek }}</div>
                                    <div class="text-muted small">{{ $item->user->nama_perusahaan ?? $item->user->nama_penghubung ?? $item->user->name ?? 'Klien' }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        {{ $df['jenis_sertifikasi'] ?? 'Produk SNI' }}
                                    </span>
                                </td>
                                <td style="font-size: 13px; color: #475569;">{{ $item->updated_at->translatedFormat('d F Y') }}</td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-primary px-3 fw-bold" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#modalKirimSertifikat{{ $item->id }}">
                                        <i class="fa-regular fa-paper-plane me-1"></i> Kirim Notif
                                    </button>

                                    <!-- Modal Form Kirim -->
                                    <div class="modal fade" id="modalKirimSertifikat{{ $item->id }}" tabindex="-1" aria-hidden="true" style="text-align: left;">
                                        <div class="modal-dialog">
                                            <div class="modal-content" style="border-radius: 16px; border: none;">
                                                <div class="modal-header border-bottom-0 pb-0">
                                                    <h5 class="modal-title fw-bold" style="color: #1e293b;">Kirim Notifikasi Sertifikat</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.penyerahan_sertifikat.kirim', $item->id) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <p class="text-muted small mb-3">Kirim notifikasi penerbitan sertifikat ke sistem dan email klien.</p>
                                                        
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold text-secondary" style="font-size: 12px;">Penerima (Klien)</label>
                                                            <input type="text" class="form-control bg-light" value="{{ $item->user->name }} ({{ $item->user->email }})" readonly>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold text-secondary" style="font-size: 12px;">Pesan Email & Notifikasi</label>
                                                            <textarea name="pesan" class="form-control" rows="4" style="border-radius: 10px;" required>Yth. {{ $item->user->name }},

Selamat! Sertifikat Sertifikat Kesesuaian SNI untuk produk pengajuan #{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }} telah diterbitkan oleh LSPro BRMP SDLP. 

Silakan unduh dokumen sertifikat Anda di sistem.
Terima kasih.</textarea>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold text-secondary" style="font-size: 12px;">Unggah Sertifikat Kesesuaian (Opsional)</label>
                                                            <input type="file" name="file_sertifikat" class="form-control" style="border-radius: 10px;" accept=".pdf,.jpg,.jpeg,.png">
                                                            <small class="text-muted" style="font-size: 11px;">Unggah dokumen sertifikat agar Klien dapat mengunduhnya secara mandiri.</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top-0 pt-0">
                                                        <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal" style="border-radius: 8px;">Batal</button>
                                                        <button type="submit" class="btn btn-primary fw-bold" style="border-radius: 8px;">Kirim Notifikasi</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <div class="py-3">
                                        <i class="fa-solid fa-box-open d-block mb-2 text-secondary fs-2"></i>
                                        <span style="font-size: 14px; font-weight: 500;">Belum ada sertifikat siap serah.</span>
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

