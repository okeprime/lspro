@extends('layouts.app')

@section('title', 'Manajemen Survailen')

@section('extra-css')
<style>
    .admin-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold mb-1 text-dark">Kelola Survailen Tahunan</h2>
            <p class="text-muted mb-0">Atur jadwal pengawasan berkala dan verifikasi dokumen pemantauan klien.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 10px;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="row g-4">
        {{-- Form Tambah Jadwal --}}
        <div class="col-lg-4">
            <div class="admin-card p-4">
                <h5 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-calendar-plus text-teal me-1"></i> Buat Jadwal Baru</h5>
                
                <form action="{{ route('admin.survailen.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 13px;">Pilih Pengajuan Klien <span class="text-danger">*</span></label>
                        <select name="pengajuan_id" class="form-select" required style="border-radius: 8px;">
                            <option value="">-- Pilih Pengajuan Klien --</option>
                            @foreach($pengajuans as $p)
                                @php
                                    $pdf = is_array($p->data_form) ? $p->data_form : (json_decode($p->data_form, true) ?? []);
                                @endphp
                                <option value="{{ $p->id }}">
                                    {{ $p->user->name ?? 'Klien' }} - {{ $pdf['merek'] ?? 'Tanpa Merek' }} (#{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 13px;">Tahun Pengawasan <span class="text-danger">*</span></label>
                        <input type="number" name="survailen_year" class="form-control" value="{{ date('Y') }}" required style="border-radius: 8px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 13px;">Bulan Pengingat <span class="text-danger">*</span></label>
                        <select name="reminder_month" class="form-select" required style="border-radius: 8px;">
                            @foreach(['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'] as $key => $bulan)
                                <option value="{{ $key + 1 }}" {{ date('m') == ($key + 1) ? 'selected' : '' }}>{{ $bulan }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 13px;">Batas Upload (Deadline)</label>
                        <input type="date" name="deadline" class="form-control" style="border-radius: 8px;">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 13px;">Instruksi / Catatan Tambahan</label>
                        <textarea name="catatan" rows="3" class="form-control" placeholder="Tulis instruksi khusus berkas yang harus diunggah klien..." style="border-radius: 8px;"></textarea>
                    </div>

                    <button type="submit" class="btn btn-teal w-100 fw-bold py-2 mt-2" style="background-color: #0f766e; color: white; border-radius: 8px;">
                        <i class="fa-solid fa-save me-1"></i> Simpan Jadwal
                    </button>
                </form>
            </div>
        </div>

        {{-- Daftar Jadwal Survailen --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-calendar-days me-1 text-secondary"></i> Jadwal &amp; Status Dokumen</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 13.5px;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Tahun/Klien</th>
                                    <th>Deadline</th>
                                    <th>Pemberitahuan</th>
                                    <th>Status Dokumen</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($schedules as $s)
                                    @php
                                        $pdf = is_array($s->pengajuan->data_form) ? $s->pengajuan->data_form : (json_decode($s->pengajuan->data_form, true) ?? []);
                                    @endphp
                                    <tr>
                                        <td class="ps-4">
                                            <span class="badge bg-teal-subtle text-teal px-2 py-1 rounded fw-bold text-uppercase mb-1" style="background-color: #f0fdf4; color: #0f766e;">{{ $s->survailen_year }}</span>
                                            <div class="fw-bold">{{ $s->user->name ?? 'Klien' }}</div>
                                            <small class="text-muted">{{ $pdf['merek'] ?? 'Tanpa Merek' }}</small>
                                        </td>
                                        <td>
                                            <span class="text-danger fw-bold">{{ $s->deadline ? \Carbon\Carbon::parse($s->deadline)->format('d M Y') : 'N/A' }}</span>
                                        </td>
                                        <td>
                                            @if($s->reminder_sent)
                                                <span class="badge bg-success">Terkirim</span>
                                                <div class="text-muted" style="font-size: 11px;">{{ $s->last_reminder_date ? $s->last_reminder_date->format('d/m H:i') : '' }}</div>
                                            @else
                                                <button type="button" class="btn btn-sm btn-outline-warning py-1 px-2" style="font-size: 11px; border-radius: 6px;" data-bs-toggle="modal" data-bs-target="#notifModal{{ $s->id }}">
                                                    <i class="fa-solid fa-paper-plane"></i> Kirim Notif
                                                </button>

                                                <!-- Modal Kirim Notif -->
                                                <div class="modal fade" id="notifModal{{ $s->id }}" tabindex="-1" aria-hidden="true" style="text-align: left;">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form action="{{ route('admin.survailen.kirim_notif', $s->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title fw-bold">Kirim Notifikasi Survailen</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-bold">Email Klien Tujuan</label>
                                                                        <input type="email" name="email_klien" class="form-control" value="{{ $s->user->email ?? '' }}" required>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-bold">Pesan Reminder</label>
                                                                        <textarea name="pesan" rows="5" class="form-control" required>Yth. {{ $s->user->name ?? 'Klien' }},

Mengingatkan kembali bahwa jadwal survailen sertifikasi produk Anda untuk tahun ke-{{ $s->survailen_year - date('Y', strtotime($s->pengajuan->created_at ?? now())) > 0 ? $s->survailen_year - date('Y', strtotime($s->pengajuan->created_at ?? now())) : 1 }} (Tahun {{ $s->survailen_year }}) telah tiba.
Silakan login ke aplikasi LSPro dan unggah dokumen persyaratan survailen sebelum tanggal {{ $s->deadline ? \Carbon\Carbon::parse($s->deadline)->format('d F Y') : 'yang ditentukan' }}.

Link Login: {{ url('/login') }}

Terima Kasih,
LSPro BRMP SDLP</textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" class="btn btn-warning"><i class="fa-solid fa-paper-plane"></i> Kirim</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            @if($s->file_dokumen)
                                                <a href="{{ asset('storage/survailen_dokumen/' . $s->file_dokumen) }}" target="_blank" class="text-teal fw-semibold d-block mb-1 text-decoration-none">
                                                    <i class="fa-solid fa-file-arrow-down"></i> Unduh Berkas
                                                </a>
                                            @endif
                                            @if($s->status_dokumen === 'menunggu')
                                                <span class="badge bg-warning text-dark">Menunggu Verifikasi</span>
                                            @elseif($s->status_dokumen === 'diterima')
                                                <span class="badge bg-primary">Diterima</span>
                                            @elseif($s->status_dokumen === 'selesai')
                                                <span class="badge bg-success">Disetujui</span>
                                            @else
                                                <span class="badge bg-light text-dark border">Belum Unggah</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            @if($s->file_dokumen && $s->status_dokumen !== 'selesai')
                                                <button class="btn btn-sm btn-teal fw-bold" style="background-color: #0f766e; color: white; border-radius: 6px; font-size: 11px;"
                                                    data-bs-toggle="modal" data-bs-target="#prosesModal{{ $s->id }}">
                                                    Proses
                                                </button>

                                                <!-- Modal Proses Dokumen -->
                                                <div class="modal fade" id="prosesModal{{ $s->id }}" tabindex="-1" aria-hidden="true" style="text-align: left;">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form action="{{ route('admin.survailen.proses_dokumen', $s->id) }}" method="POST">
                                                                @csrf
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title fw-bold">Verifikasi Dokumen Survailen</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-bold">Keputusan Verifikasi</label>
                                                                        <select name="status_dokumen" class="form-select" required>
                                                                            <option value="diterima" {{ $s->status_dokumen == 'diterima' ? 'selected' : '' }}>Diterima (Butuh perbaikan/proses lanjut)</option>
                                                                            <option value="selesai" {{ $s->status_dokumen == 'selesai' ? 'selected' : '' }}>Selesai &amp; Disetujui (Survailen Berhasil)</option>
                                                                        </select>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-bold">Catatan Hasil Evaluasi</label>
                                                                        <textarea name="catatan" rows="3" class="form-control" placeholder="Tulis catatan kelengkapan berkas..."></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                                                    <button type="submit" class="btn btn-teal text-white" style="background-color: #0f766e;">Simpan Verifikasi</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="fa-solid fa-calendar-xmark d-block fs-1 mb-3"></i>
                                            Belum ada jadwal survailen yang terdaftar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
