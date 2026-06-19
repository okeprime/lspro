@extends('layouts.app')

@section('title', 'Sertifikat Saya')

@section('content')
<div class="container-fluid py-4" style="max-width: 1400px;">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1" style="color: #0f172a; font-size: 24px;">Sertifikat SPPT SNI</h2>
            <p class="text-muted mb-0" style="font-size: 14px;">Daftar sertifikat produk yang telah diterbitkan oleh LSPro BRMP SDLP.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
        <div style="height: 4px; background: linear-gradient(90deg, #16a34a, #22c55e);"></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                    <thead style="background: #f8fafc; color: #475569; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px;">
                        <tr>
                            <th class="ps-4 py-3 border-bottom-0 fw-semibold">No. Sertifikat</th>
                            <th class="py-3 border-bottom-0 fw-semibold">Produk / Skema</th>
                            <th class="py-3 border-bottom-0 fw-semibold">Tanggal Terbit</th>
                            <th class="py-3 border-bottom-0 fw-semibold">Masa Berlaku</th>
                            <th class="py-3 border-bottom-0 fw-semibold">Status</th>
                            <th class="pe-4 py-3 border-bottom-0 fw-semibold text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sertifikats as $sertifikat)
                            @php
                                // Mockup certificate number and dates
                                $certNumber = 'SNI-BRMP-' . date('Y', strtotime($sertifikat->updated_at)) . '-' . str_pad($sertifikat->id, 5, '0', STR_PAD_LEFT);
                                $issueDate = \Carbon\Carbon::parse($sertifikat->updated_at);
                                $expiryDate = $issueDate->copy()->addYears(4);
                                $isActive = now()->lessThanOrEqualTo($expiryDate);
                            @endphp
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold" style="color: #1e293b;">{{ $certNumber }}</div>
                                    <div class="text-muted" style="font-size: 12px;">ID Pengajuan: #{{ str_pad($sertifikat->id, 5, '0', STR_PAD_LEFT) }}</div>
                                </td>
                                <td class="py-3">
                                    <div class="fw-semibold text-dark">{{ $sertifikat->jenis_sertifikasi ?? 'Sertifikasi Produk' }}</div>
                                    <div class="text-muted" style="font-size: 12px;">Pupuk & Pembenah Tanah</div>
                                </td>
                                <td class="py-3 text-muted">{{ $issueDate->translatedFormat('d F Y') }}</td>
                                <td class="py-3 text-muted">{{ $expiryDate->translatedFormat('d F Y') }}</td>
                                <td class="py-3">
                                    @if($isActive)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 rounded-pill">Aktif</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 rounded-pill">Kadaluarsa</span>
                                    @endif
                                </td>
                                <td class="pe-4 py-3 text-end">
                                    <a href="{{ route('pengajuan.workflow', $sertifikat->id) }}" class="btn btn-sm btn-light border fw-semibold px-3 me-2" style="border-radius: 8px;">
                                        <i class="fa-solid fa-eye me-1"></i> Detail
                                    </a>
                                    <button class="btn btn-sm fw-semibold px-3" style="background: #16a34a; color: white; border-radius: 8px; border: none;">
                                        <i class="fa-solid fa-download me-1"></i> PDF
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="fa-solid fa-certificate mb-3" style="font-size: 48px; color: #cbd5e1;"></i>
                                        <h6 class="fw-bold text-dark">Belum Ada Sertifikat</h6>
                                        <p class="text-muted" style="font-size: 13px; max-width: 400px;">Anda belum memiliki sertifikat SPPT SNI yang aktif. Sertifikat akan muncul di sini setelah proses sertifikasi Anda selesai disetujui.</p>
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
