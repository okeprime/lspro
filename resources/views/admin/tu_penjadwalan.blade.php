@extends('layouts.app')
@section('title', 'Penjadwalan Audit - LS Pro')

@section('content')
<div class="container-fluid py-3 px-3 px-md-4">
    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 style="color: #1e293b; font-weight: 700; letter-spacing: -0.5px; margin-bottom: 4px;">Penjadwalan Audit</h2>
            <p class="text-muted small mb-0">Penentuan jadwal audit lapangan dan pengambilan contoh oleh Administrasi.</p>
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
                <i class="fa-solid fa-calendar-alt text-primary me-2"></i> Daftar Menunggu Penjadwalan
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
                            <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700;">Status Jadwal</th>
                            <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700;">File Evaluasi / Permohonan</th>
                            <th class="pe-4 py-3 text-end text-secondary text-uppercase" style="font-size: 11px; font-weight: 700;">Tindakan</th>
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
                                <td style="font-size: 13px; color: #475569;">
                                    @if($item->status === 'audit_kecukupan')
                                        <span class="badge bg-warning text-dark">Belum Dijadwalkan</span>
                                    @elseif($item->status === 'menunggu_persetujuan_jadwal')
                                        <span class="badge bg-info text-white">Menunggu Persetujuan Klien</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('pengajuan.download', $item->id) }}" class="btn btn-sm btn-light border" target="_blank" style="border-radius: 8px; font-size: 12px; font-weight: 600;">
                                        <i class="fa-solid fa-file-pdf text-danger"></i> Dokumen
                                    </a>
                                </td>
                                <td class="text-end pe-4">
                                    @php
                                        $rejectionLog = $item->statusHistories()->where('to_status', 'audit_kecukupan')->where('notes', 'like', 'Klien menolak jadwal audit%')->latest()->first();
                                    @endphp
                                    @if($rejectionLog && $item->status === 'audit_kecukupan')
                                        <div class="alert alert-danger p-2 text-start mb-2 mx-auto" style="font-size: 11px; border-radius: 8px; max-width: 250px;">
                                            <i class="fa-solid fa-circle-exclamation me-1"></i> <strong>Ditolak Klien:</strong><br>
                                            {{ str_replace('Klien menolak jadwal audit dengan alasan: ', '', $rejectionLog->notes) }}
                                        </div>
                                    @endif

                                    @if(in_array($item->status, ['audit_kecukupan', 'menunggu_persetujuan_jadwal']))
                                        <button type="button" class="btn btn-sm btn-info px-3 fw-bold text-white mb-1" style="border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#modalJadwal{{ $item->id }}">
                                            {{ $item->status === 'menunggu_persetujuan_jadwal' ? 'Ubah Jadwal' : 'Tentukan Jadwal' }} <i class="fa-solid fa-calendar ms-1" style="font-size: 10px;"></i>
                                        </button>
                                    @endif

                                    <!-- Modal Jadwal -->
                                    @if(in_array($item->status, ['audit_kecukupan', 'menunggu_persetujuan_jadwal']))
                                    <div class="modal fade" id="modalJadwal{{ $item->id }}" tabindex="-1" aria-hidden="true" style="text-align: left;">
                                        <div class="modal-dialog">
                                            <div class="modal-content" style="border-radius: 16px; border: none;">
                                                <div class="modal-header border-bottom-0 pb-0">
                                                    <h5 class="modal-title fw-bold" style="color: #1e293b;">Tentukan Jadwal Audit & PPC #{{ $item->id }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.pengajuan.set_jadwal', $item->id) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold text-secondary" style="font-size: 12px;">Jadwal Audit Lapangan</label>
                                                            <input type="date" name="jadwal_audit" class="form-control" style="border-radius: 10px;" value="{{ $item->jadwal_audit ? \Carbon\Carbon::parse($item->jadwal_audit)->format('Y-m-d') : '' }}" required>
                                                        </div>
                                                        <div class="mb-4">
                                                            <label class="form-label fw-bold text-secondary" style="font-size: 12px;">Dokumen Rencana Jadwal Audit (Opsional, PDF)</label>
                                                            <input type="file" name="dokumen_jadwal" class="form-control" style="border-radius: 10px;" accept=".pdf">
                                                            <small class="text-muted" style="font-size: 11px;">Unggah dokumen rencana audit kesesuaian berdasarkan tanggal jika ada.</small>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-top-0 pt-0">
                                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600;">Batal</button>
                                                        <button type="submit" class="btn btn-info text-white" style="border-radius: 8px; font-weight: 600;">Simpan Jadwal</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

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
