@extends('layouts.app')
@section('title', 'Tinjauan Laporan Hasil Uji (LHP) - LS Pro')

@section('content')
<div class="container-fluid py-3 px-3 px-md-4">
    <div class="row justify-content-center">
        <div class="col-xl-8 col-lg-10">
            <div class="mb-4 text-center">
                <div class="d-inline-flex align-items-center justify-content-center bg-primary bg-opacity-10 rounded-circle mb-3" style="width: 60px; height: 60px;">
                    <i class="fa-solid fa-clipboard-check text-primary fs-3"></i>
                </div>
                <h2 style="color: #1e293b; font-weight: 700; letter-spacing: -0.5px; margin-bottom: 8px;">Tinjauan Hasil Uji Lab (LHP)</h2>
                <p class="text-muted mb-0">Pengajuan #{{ str_pad($pengajuan->id, 5, '0', STR_PAD_LEFT) }} &mdash; {{ $pengajuan->user->nama_perusahaan }}</p>
            </div>

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm p-3 mb-4" role="alert" style="border-radius: 12px; background-color: #fef2f2; color: #991b1b;">
                    <i class="fa-solid fa-circle-exclamation me-2 fs-5 text-danger"></i> <strong>Error!</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm p-3 mb-4" role="alert" style="border-radius: 12px; background-color: #fef2f2; color: #991b1b;">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold" style="color: #1e293b; font-size: 1.1rem;">
                        <i class="fa-solid fa-file-pdf text-danger me-2"></i> Dokumen LHP
                    </h5>
                </div>
                <div class="card-body p-4">
                    @php
                        $formData = is_array($pengajuan->data_form) ? $pengajuan->data_form : (json_decode($pengajuan->data_form, true) ?? []);
                        $fileLhp = $formData['file_lhp'] ?? null;
                    @endphp

                    @if($fileLhp)
                        <div class="d-flex align-items-center justify-content-between p-3 border rounded mb-4" style="background: #f8fafc;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-danger bg-opacity-10 p-2 rounded">
                                    <i class="fa-solid fa-file-pdf text-danger fs-4"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark">Laporan Hasil Pengujian (LHP)</div>
                                    <div class="text-muted" style="font-size:12px;">Diunggah oleh Laboratorium</div>
                                </div>
                            </div>
                            <a href="{{ asset('storage/' . $fileLhp) }}" target="_blank" class="btn btn-sm btn-outline-primary" style="border-radius: 8px;">
                                <i class="fa-solid fa-eye me-1"></i> Lihat Dokumen
                            </a>
                        </div>
                    @else
                        <div class="alert alert-warning">Dokumen LHP belum diunggah atau tidak ditemukan.</div>
                    @endif

                    <hr class="my-4 text-muted">

                    <form action="{{ route('admin.pengajuan.proses_tinjau_lhp', $pengajuan->id) }}" method="POST">
                        @csrf
                        
                        <h6 class="fw-bold mb-3">Tentukan Keputusan Tinjauan:</h6>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="card border border-2 border-success h-100 option-card" style="border-radius: 12px; cursor: pointer;" onclick="document.getElementById('keputusan_setuju').checked = true; toggleCatatan(false);">
                                    <div class="card-body text-center p-4">
                                        <div class="form-check d-flex justify-content-center mb-3">
                                            <input class="form-check-input fs-4" type="radio" name="keputusan" id="keputusan_setuju" value="setuju" required>
                                        </div>
                                        <h5 class="fw-bold text-success mb-2">Setuju & Lanjut</h5>
                                        <p class="text-muted small mb-0">LHP telah sesuai. Pengajuan akan dilanjutkan ke tahap Sidang Komtek & Billing 4.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border border-2 border-danger h-100 option-card" style="border-radius: 12px; cursor: pointer;" onclick="document.getElementById('keputusan_perbaikan').checked = true; toggleCatatan(true);">
                                    <div class="card-body text-center p-4">
                                        <div class="form-check d-flex justify-content-center mb-3">
                                            <input class="form-check-input fs-4" type="radio" name="keputusan" id="keputusan_perbaikan" value="perbaikan" required>
                                        </div>
                                        <h5 class="fw-bold text-danger mb-2">Perlu Perbaikan / Re-Audit</h5>
                                        <p class="text-muted small mb-0">LHP tidak sesuai/tidak lulus. Kembalikan ke tahap Penjadwalan & tagihan Billing 2 baru.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="catatan_wrapper" class="mb-4" style="display: none;">
                            <label class="form-label fw-bold text-secondary" style="font-size: 13px;">Catatan Penolakan / Alasan Re-Audit <span class="text-danger">*</span></label>
                            <textarea name="catatan" id="catatan" rows="3" class="form-control shadow-none" style="border-radius: 10px; padding: 10px 15px;" placeholder="Tuliskan alasan mengapa LHP ditolak dan perlu re-audit..."></textarea>
                            <div class="form-text mt-1" style="font-size: 12px;">Catatan ini akan dikirimkan kepada Klien.</div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-3 border-top mt-4">
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-light fw-bold px-4" style="border-radius: 10px;">Batal</a>
                            <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm" style="border-radius: 10px;">
                                <i class="fa-solid fa-save me-1"></i> Simpan Keputusan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('extra-js')
<script>
    function toggleCatatan(show) {
        var wrapper = document.getElementById('catatan_wrapper');
        var textarea = document.getElementById('catatan');
        if (show) {
            wrapper.style.display = 'block';
            textarea.setAttribute('required', 'required');
        } else {
            wrapper.style.display = 'none';
            textarea.removeAttribute('required');
            textarea.value = '';
        }
    }

    // Optional styling for selected card
    document.querySelectorAll('.option-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.option-card').forEach(c => c.style.backgroundColor = '#fff');
            this.style.backgroundColor = '#f8fafc';
        });
    });
</script>
@endsection
