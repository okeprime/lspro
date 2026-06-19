@extends('layouts.app')

@section('title', 'Manajemen Banding & Keluhan')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1 text-dark">Kelola Banding &amp; Keluhan</h2>
            <p class="text-muted mb-0">Tinjau, respon, dan perbarui status keluhan/banding klien.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 10px;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-bottom">
            <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-gavel me-1 text-secondary"></i> Daftar Pengaduan Masuk</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 13.5px;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Tipe / Tanggal</th>
                            <th>Klien</th>
                            <th>Terkait Pengajuan</th>
                            <th>Pesan Pengaduan</th>
                            <th>Status</th>
                            <th>Lampiran</th>
                            <th class="pe-4 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bandings as $b)
                            @php
                                $pb = $b->pengajuan;
                                $pdf = $pb ? (is_array($pb->data_form) ? $pb->data_form : (json_decode($pb->data_form, true) ?? [])) : [];
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <span class="badge text-uppercase mb-1 {{ $b->jenis === 'banding' ? 'bg-danger text-white' : ($b->jenis === 'keluhan' ? 'bg-warning text-dark' : 'bg-secondary text-white') }}" style="font-size: 10px;">
                                        {{ $b->jenis }}
                                    </span>
                                    <div class="text-muted" style="font-size: 11px;">{{ $b->created_at->translatedFormat('d M Y') }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $b->user->name ?? 'Klien' }}</div>
                                    <small class="text-muted">{{ $b->user->email }}</small>
                                </td>
                                <td>
                                    @if($pb)
                                        <a href="{{ route('aktivitas.show', $pb) }}" class="fw-semibold text-decoration-none text-teal">
                                            #{{ str_pad($pb->id, 5, '0', STR_PAD_LEFT) }} - {{ $pdf['merek'] ?? 'Tanpa Merek' }}
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span title="{{ $b->pesan }}">{{ Str::limit($b->pesan, 70) }}</span>
                                </td>
                                <td>
                                    @if($b->status === 'terkirim')
                                        <span class="badge bg-secondary">Terkirim</span>
                                    @elseif($b->status === 'diproses')
                                        <span class="badge bg-primary">Diproses</span>
                                    @elseif($b->status === 'selesai')
                                        <span class="badge bg-success">Selesai</span>
                                    @endif
                                </td>
                                <td>
                                    @if($b->file_lampiran)
                                        <a href="{{ asset('storage/banding_lampiran/' . $b->file_lampiran) }}" target="_blank" class="btn btn-sm btn-outline-secondary py-1 px-2" style="border-radius: 6px; font-size: 11px;">
                                            <i class="fa-solid fa-file-arrow-down"></i> Unduh
                                        </a>
                                    @else
                                        <span class="text-muted" style="font-size: 12px;">Tidak Ada</span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    <button class="btn btn-sm btn-teal text-white fw-bold" style="background-color: #0f766e; border-radius: 6px;"
                                        data-bs-toggle="modal" data-bs-target="#prosesBandingModal{{ $b->id }}">
                                        Respon
                                    </button>

                                    <!-- Modal Respon Banding -->
                                    <div class="modal fade" id="prosesBandingModal{{ $b->id }}" tabindex="-1" aria-hidden="true" style="text-align: left;">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.banding.proses', $b->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header">
                                                        <h5 class="modal-title fw-bold">Proses Pengaduan / Keluhan</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Perbarui Status</label>
                                                            <select name="status" class="form-select" required>
                                                                <option value="terkirim" {{ $b->status == 'terkirim' ? 'selected' : '' }}>Terkirim / Belum Diproses</option>
                                                                <option value="diproses" {{ $b->status == 'diproses' ? 'selected' : '' }}>Diproses (Sedang ditinjau)</option>
                                                                <option value="selesai" {{ $b->status == 'selesai' ? 'selected' : '' }}>Selesai (Keputusan diberikan)</option>
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">Tanggapan Resmi Admin</label>
                                                            <textarea name="catatan_admin" rows="4" class="form-control" placeholder="Tulis penjelasan/keputusan tanggapan resmi dari LSPro..." required>{{ $b->catatan_admin }}</textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                        <button type="submit" class="btn btn-teal text-white" style="background-color: #0f766e;">Kirim Tanggapan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-gavel d-block fs-1 mb-3"></i>
                                    Belum ada pengaduan banding atau keluhan masuk.
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
