@extends('layouts.app')
@section('title', 'Panel Tata Usaha - LS Pro')

@section('content')
<div class="container-fluid py-3 px-3 px-md-4">
    
    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 style="color: #1e293b; font-weight: 700; letter-spacing: -0.5px; margin-bottom: 4px;">Panel Kerja Tata Usaha</h2>
            <p class="text-muted small mb-0">Verifikasi berkas persyaratan permohonan sertifikasi (Form 7.2-4).</p>
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
                <i class="fa-solid fa-folder-open text-success me-2"></i> Antrean Verifikasi Dokumen Klien
            </h5>
            <div>
                <span class="badge bg-danger rounded-pill">{{ $pengajuans->where('status', 'diajukan')->count() }} Pengajuan Baru</span>
                <span class="badge bg-warning text-dark rounded-pill">{{ $pengajuans->where('status', 'perbaikan')->count() }} Perbaikan Client</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">ID Berkas</th>
                            <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">Nama Klien</th>
                            <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">Tanggal Masuk</th>
                            <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">File 7.2-1</th>
                            <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">Status</th>
                            <th class="pe-4 py-3 text-end text-secondary text-uppercase" style="font-size: 11px; font-weight: 700; letter-spacing: 0.5px;">Aksi Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengajuans as $item)
                            <tr>
                                <td class="ps-4 fw-bold" style="color: #475569;">
                                    #{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}
                                </td>
                                <td>
                                    <div class="fw-bold" style="color: #1e293b;">{{ $item->user->nama_perusahaan ?? $item->user->nama_penghubung ?? explode('@', $item->user->email)[0] ?? 'Klien' }}</div>
                                    <div class="text-muted small" style="font-size: 11px;">{{ $item->user->email ?? '-' }}</div>
                                </td>
                                <td style="color: #475569; font-size: 13px;">
                                    <div>{{ $item->created_at->translatedFormat('d M Y') }}</div>
                                    <div class="text-muted" style="font-size: 11px;">Jam {{ $item->created_at->format('H:i') }} WIB</div>
                                </td>
                                <td>
                                    @if($item->file_permohonan)
                                        <a href="{{ route('pengajuan.download', $item->id) }}" class="btn btn-sm btn-light border d-inline-flex align-items-center gap-1" target="_blank" style="border-radius: 8px; font-size: 12px; font-weight: 600; color: #0f766e;">
                                            <i class="fa-solid fa-file-pdf"></i> PDF
                                        </a>
                                    @else
                                        <span class="text-muted small">Belum ada file</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->status === 'diajukan')
                                        <span class="badge px-2 py-1" style="background-color: #fffbeb; color: #b45309; border: 1px solid #fde68a; font-weight: 600; font-size: 11px; border-radius: 6px;">
                                            Menunggu Dicek
                                        </span>
                                    @elseif($item->status === 'perbaikan')
                                        <span class="badge px-2 py-1" style="background-color: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; font-weight: 600; font-size: 11px; border-radius: 6px;">
                                            Perbaikan
                                        </span>
                                    @elseif(in_array($item->status, ['disetujui_tu', 'lengkap', 'perjanjian']))
                                        <span class="badge px-2 py-1" style="background-color: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; font-weight: 600; font-size: 11px; border-radius: 6px;">
                                            Lengkap (Lanjut)
                                        </span>
                                    @else
                                        <span class="badge bg-light text-secondary border px-2 py-1 text-capitalize" style="font-size: 11px; border-radius: 6px;">
                                            {{ str_replace('_', ' ', $item->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        @if(in_array($item->status, ['diajukan', 'perbaikan']))
                                            <button type="button" class="btn btn-sm btn-primary px-3 d-inline-flex align-items-center gap-1" style="border-radius: 8px; font-size: 12px; font-weight: 600;" data-bs-toggle="modal" data-bs-target="#modalCekAwal{{ $item->id }}">
                                                <i class="fa-solid fa-eye"></i> Pengecekan Awal
                                            </button>
                                        @elseif($item->status === 'menunggu_lampiran')
                                            <span class="btn btn-sm btn-light px-3 d-inline-flex align-items-center gap-1 text-muted" style="border-radius: 8px; font-size: 12px; font-weight: 600; border: 1px dashed #cbd5e1;">
                                                <i class="fa-solid fa-clock"></i> Menunggu Lampiran Klien
                                            </span>
                                        @else
                                            <a href="{{ route('admin.ceklis', $item->id) }}" class="btn btn-sm btn-success px-3 d-inline-flex align-items-center gap-1" style="border-radius: 8px; font-size: 12px; font-weight: 600; background-color: #16a34a; border-color: #16a34a;">
                                                <i class="fa-solid fa-clipboard-check"></i> Form 7.2-4
                                            </a>
                                        @endif

                                        @if(in_array($item->status, ['diajukan', 'perbaikan']))
                                            <!-- Modal Pengecekan Awal -->
                                            <div class="modal fade" id="modalCekAwal{{ $item->id }}" tabindex="-1" aria-hidden="true" style="text-align: left;">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content" style="border-radius: 16px; border: none;">
                                                        <div class="modal-header border-bottom-0 pb-0">
                                                            <h5 class="modal-title fw-bold" style="color: #1e293b;">Pengecekan Awal Pengajuan #{{ $item->id }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('admin.pengajuan.terima_awal', $item->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <div class="alert alert-info py-2" style="font-size: 13px; border-radius: 10px;">
                                                                    <i class="fa-solid fa-circle-info me-1"></i> Periksa kesesuaian data form sebelum klien diizinkan mengunggah dokumen kelengkapan.
                                                                </div>
                                                                
                                                                <div class="border rounded-3 p-3 mb-3 bg-light">
                                                                    <h6 class="fw-bold mb-3 text-secondary" style="font-size: 13px; letter-spacing: 0.5px;">DATA PERMOHONAN:</h6>
                                                                    <div class="row gy-2" style="font-size: 13px;">
                                                                        @php
                                                                            $formData = is_string($item->data_form) ? json_decode($item->data_form, true) : (array)($item->data_form ?? []);
                                                                        @endphp
                                                                        @foreach($formData as $key => $val)
                                                                            @if(is_string($val) && !empty($val) && !str_starts_with($val, 'lampiran/'))
                                                                                <div class="col-md-6">
                                                                                    <div class="text-muted small text-capitalize">{{ str_replace('_', ' ', $key) }}</div>
                                                                                    <div class="fw-bold text-dark">{{ $val }}</div>
                                                                                </div>
                                                                            @endif
                                                                        @endforeach
                                                                    </div>
                                                                </div>

                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold text-secondary" style="font-size: 12px;">Kesimpulan Pengecekan</label>
                                                                    <select name="kesimpulan" class="form-select shadow-none" required style="border-radius: 10px;">
                                                                        <option value="">-- Pilih Keputusan --</option>
                                                                        <option value="menunggu_lampiran">Setujui (Lanjut Upload Lampiran)</option>
                                                                        <option value="perbaikan">Tolak / Minta Revisi Form</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-2">
                                                                    <label class="form-label fw-bold text-secondary" style="font-size: 12px;">Catatan untuk Klien (Wajib jika Revisi)</label>
                                                                    <textarea name="catatan" class="form-control shadow-none" rows="2" style="border-radius: 10px;" placeholder="Tulis catatan revisi atau pesan ke klien..."></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer border-top-0 pt-0">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600;">Batal</button>
                                                                <button type="submit" class="btn btn-primary" style="border-radius: 8px; font-weight: 600;">Simpan Hasil</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if(in_array($item->status, ['perjanjian', 'billing', 'disetujui_tu']))
                                            <button type="button" class="btn btn-sm btn-outline-primary px-3" style="border-radius: 8px; font-size: 12px; font-weight: 600;" data-bs-toggle="modal" data-bs-target="#modalTeruskan{{ $item->id }}">
                                                <i class="fa-solid fa-share"></i> Oper Berkas
                                            </button>

                                            <!-- Modal Oper Berkas -->
                                            <div class="modal fade" id="modalTeruskan{{ $item->id }}" tabindex="-1" aria-hidden="true" style="text-align: left;">
                                                <div class="modal-dialog">
                                                    <div class="modal-content" style="border-radius: 16px; border: none;">
                                                        <div class="modal-header border-bottom-0 pb-0">
                                                            <h5 class="modal-title fw-bold" style="color: #1e293b;">Oper Berkas #{{ $item->id }}</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="{{ route('admin.pengajuan.teruskan', $item->id) }}" method="POST">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold text-secondary" style="font-size: 12px;">Teruskan Ke Bagian</label>
                                                                    <select name="status" class="form-select shadow-none" required style="border-radius: 10px;">
                                                                        <option value="">-- Pilih Tujuan --</option>
                                                                        <option value="proses_evaluasi">Layanan (Proses Evaluasi)</option>
                                                                        <option value="proses_audit">Audit (Proses Audit)</option>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold text-secondary" style="font-size: 12px;">Catatan (Opsional)</label>
                                                                    <textarea name="catatan_status" class="form-control shadow-none" rows="3" style="border-radius: 10px;"></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer border-top-0 pt-0">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600;">Batal</button>
                                                                <button type="submit" class="btn btn-primary" style="border-radius: 8px; font-weight: 600;">Proses Berkas</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <div class="py-3">
                                        <i class="fa-solid fa-inbox d-block mb-2 text-secondary fs-2"></i>
                                        <span style="font-size: 14px; font-weight: 500;">Belum ada antrean berkas masuk.</span>
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
