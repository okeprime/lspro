@extends('layouts.app')
@section('title', 'Unggah Tindakan Perbaikan (LKS) - LS Pro')

@section('content')
<div class="container-fluid py-3 px-3 px-md-4">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <div class="mb-4 text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-danger bg-opacity-10 rounded-circle mb-3" style="width: 60px; height: 60px;">
                    <i class="fa-solid fa-triangle-exclamation text-danger fs-3"></i>
                </div>
                <h2 style="color: #1e293b; font-weight: 700; letter-spacing: -0.5px; margin-bottom: 8px;">Unggah Tindakan Perbaikan</h2>
                <p class="text-muted mb-0">Pengajuan #{{ str_pad($pengajuan->id, 5, '0', STR_PAD_LEFT) }}</p>
            </div>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm p-3 mb-4" role="alert" style="border-radius: 12px; background-color: #fef2f2; color: #991b1b;">
                    <i class="fa-solid fa-circle-exclamation me-2 fs-5 text-danger"></i> <strong>Error!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold" style="color: #1e293b; font-size: 1.1rem;">
                        <i class="fa-solid fa-upload text-danger me-2"></i> Laporan Tindakan Perbaikan (LKS)
                    </h5>
                </div>
                <div class="card-body p-4">
                    @php
                        $df = is_array($pengajuan->data_form) ? $pengajuan->data_form : (json_decode($pengajuan->data_form, true) ?? []);
                    @endphp
                    <div class="alert alert-warning border-0" style="background-color: #fffbeb; color: #92400e; border-radius: 10px;">
                        <div class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-info-circle mt-1"></i>
                            <div style="font-size: 14px;">
                                Silakan lihat catatan ketidaksesuaian yang diberikan oleh Tim Auditor pada Riwayat Pengajuan, kemudian unggah dokumen bukti perbaikan di bawah ini.
                                @if(isset($df['lks_deadline']))
                                    <div class="mt-2 fw-bold text-danger">
                                        <i class="fa-solid fa-clock me-1"></i> Batas Waktu Perbaikan: {{ \Carbon\Carbon::parse($df['lks_deadline'])->translatedFormat('d F Y') }}
                                        <span class="ms-2 badge bg-danger rounded-pill">Iterasi ke-{{ $df['lks_iterasi'] ?? 1 }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('pengajuan.tindakan_perbaikan.upload', $pengajuan->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary" style="font-size: 13px;">Deskripsi / Keterangan Perbaikan <span class="text-danger">*</span></label>
                            <textarea name="keterangan_perbaikan" class="form-control shadow-none" rows="4" style="border-radius: 10px;" required placeholder="Jelaskan tindakan perbaikan yang telah dilakukan berdasarkan LKS..."></textarea>
                            <div class="form-text mt-1" style="font-size: 12px;">Tuliskan secara detail apa saja yang telah diperbaiki.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary" style="font-size: 13px;">Unggah File Bukti Perbaikan <span class="text-danger">*</span></label>
                            <input type="file" name="file_tindakan_perbaikan" class="form-control shadow-none" style="border-radius: 10px; padding: 10px 15px;" required accept=".pdf,.doc,.docx,.zip,.rar">
                            <div class="form-text mt-1" style="font-size: 12px;">Format yang didukung: PDF, DOCX, ZIP, RAR. Maksimal 10MB. Jika ada lebih dari satu dokumen, gabungkan ke dalam format PDF atau ZIP.</div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-4">
                            <a href="{{ route('aktivitas.show', $pengajuan->id) }}" class="btn btn-light fw-bold px-4" style="border-radius: 10px;">Kembali</a>
                            <button type="submit" class="btn btn-danger fw-bold px-4 shadow-sm" style="border-radius: 10px;">
                                <i class="fa-solid fa-paper-plane me-1"></i> Kirim Perbaikan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
