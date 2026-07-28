@extends('layouts.app')
@section('title', $title . ' - LS Pro')

@section('content')
<div class="container-fluid py-3 px-3 px-md-4">
    <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h2 style="color: #1e293b; font-weight: 700; letter-spacing: -0.5px; margin-bottom: 4px;">{{ $title }}</h2>
            <p class="text-muted small mb-0">{{ $subtitle }}</p>
        </div>
    </div>

    @if(request()->has('dummy_success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm p-3 mb-4" role="alert" style="border-radius: 12px; background-color: #f0fdf4; color: #166534;">
            <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i> <strong>Simulasi Berhasil!</strong> Form berhasil disubmit. Pada sistem nyata, dokumen akan berpindah ke tahap yang dipilih dan hilang dari antrean ini.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm p-3 mb-4" role="alert" style="border-radius: 12px; background-color: #f0fdf4; color: #166534;">
            <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i> <strong>Sukses!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm p-3 mb-4" role="alert" style="border-radius: 12px; background-color: #fef2f2; color: #991b1b;">
            <i class="fa-solid fa-triangle-exclamation me-2 fs-5 text-danger"></i> <strong>Gagal Menyimpan!</strong>
            <ul class="mb-0 mt-2" style="font-size: 13px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold d-flex align-items-center" style="color: #1e293b; font-size: 1rem;">
                <i class="fa-solid fa-folder-open text-primary me-2"></i> Antrean Pengajuan
            </h5>
            <div><span class="badge bg-primary text-white rounded-pill">{{ count($pengajuans) }} Dokumen</span></div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 14px;">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700;">ID Berkas</th>
                            <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700;">Nama Klien</th>
                            <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700;">Tanggal Masuk</th>
                            <th class="pe-4 py-3 text-end text-secondary text-uppercase" style="font-size: 11px; font-weight: 700;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $hasData = false; @endphp
                        @foreach($pengajuans as $item)
                            @php $hasData = true; @endphp
                            <tr>
                                <td class="ps-4 fw-bold" style="color: #475569;">#{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td>
                                    @php
                                        $df = is_array($item->data_form) ? $item->data_form : (json_decode($item->data_form, true) ?? []);
                                        $merek = $df['merek_produk'] ?? ($df['merek'] ?? 'Tanpa Merek');
                                    @endphp
                                    <div class="fw-bold" style="color: #1e293b;">{{ $merek }}</div>
                                    <div class="text-muted small" style="font-size: 11px;">{{ $item->user->nama_perusahaan ?? $item->user->nama_penghubung ?? 'Klien' }}</div>
                                </td>
                                <td style="color: #475569; font-size: 13px;">{{ $item->updated_at->format('d/m/Y') }}</td>
                                <td class="text-end pe-4">
                                    @if($item->status === 'menunggu_lhp')
                                        <a href="{{ route('admin.pengajuan.form_lhp', $item->id) }}" class="btn btn-sm btn-danger px-3 me-1" style="border-radius: 8px; font-size: 12px; font-weight: 600;">
                                            <i class="ph ph-upload-simple"></i> Unggah LHP
                                        </a>
                                    @elseif($item->status === 'tinjauan_lhp')
                                        <a href="{{ route('admin.pengajuan.tinjau_lhp', $item->id) }}" class="btn btn-sm btn-warning px-3 me-1" style="border-radius: 8px; font-size: 12px; font-weight: 600; color: #78350f;">
                                            <i class="ph ph-magnifying-glass"></i> Tinjau LHP
                                        </a>
                                    @endif
                                    <button class="btn btn-sm btn-primary px-3" style="border-radius: 8px; font-size: 12px; font-weight: 600;" data-bs-toggle="modal" data-bs-target="#modalTindakLanjut{{ $item->id }}">
                                        <i class="fa-solid fa-bolt"></i> Tindak Lanjut
                                    </button>

                                    <!-- Modal Tindak Lanjut -->
                                    <div class="modal fade" id="modalTindakLanjut{{ $item->id }}" tabindex="-1" aria-hidden="true" style="text-align: left;">
                                        <div class="modal-dialog">
                                            <div class="modal-content" style="border-radius: 16px; border: none;">
                                                <div class="modal-header border-bottom-0 pb-0">
                                                    <h5 class="modal-title fw-bold" style="color: #1e293b;">Tindak Lanjut #{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('admin.pengajuan.teruskan', $item->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="alert alert-info py-2 mb-3" style="font-size: 13px; border-radius: 10px;">
                                                            Pilih tahap selanjutnya untuk dokumen ini. Status saat ini: <strong>{{ str_replace('_', ' ', $item->status) }}</strong>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold text-secondary" style="font-size: 12px;">Teruskan Ke Tahap</label>
                                                            <select name="status" class="form-select shadow-none" required style="border-radius: 10px;">
                                                                <option value="">-- Pilih Tujuan --</option>
                                                                @php
                                                                    $nextOptions = [];
                                                                    if ($item->status === 'proses_evaluasi') {
                                                                        $nextOptions = ['menunggu_persetujuan_jadwal' => 'Tetapkan Jadwal Audit -> Lanjut', 'perbaikan' => 'Kembalikan (Perlu Perbaikan)'];
                                                                    } elseif ($item->status === 'proses_audit') {
                                                                        $nextOptions = ['keputusan' => 'Evaluasi Selesai -> Lanjut Keputusan', 'perbaikan' => 'Terbitkan LKS (Perbaikan)'];
                                                                    } elseif ($item->status === 'proses_lab') {
                                                                        $nextOptions = ['menunggu_lhp' => 'Selesai Uji Lab -> Lanjut LHP', 'perbaikan' => 'Kembalikan (Perbaikan)'];
                                                                    } elseif ($item->status === 'menunggu_lhp') {
                                                                        $nextOptions = ['tinjauan_lhp' => 'Hasil LHP Keluar -> Lanjut Tinjauan', 'perbaikan' => 'Dikembalikan (Perbaikan)'];
                                                                    } elseif ($item->status === 'tinjauan_lhp') {
                                                                        $nextOptions = ['billing_4' => 'Tinjauan Selesai -> Lanjut Sidang Komtek (Billing 4)', 'billing_3' => 'Perlu Re-Audit (Billing 3)'];
                                                                    } elseif ($item->status === 'evaluasi') {
                                                                        $nextOptions = ['keputusan' => 'Evaluasi Selesai -> Lanjut Keputusan', 'perbaikan' => 'Kembalikan (Perbaikan)'];
                                                                    } elseif ($item->status === 'keputusan') {
                                                                        $nextOptions = ['selesai' => 'Sahkan Sertifikat Kesesuaian SNI -> Selesai', 'ditolak' => 'Tolak Pengajuan'];
                                                                    } else {
                                                                        $nextOptions = [
                                                                            'proses_evaluasi' => 'Proses Evaluasi',
                                                                            'proses_audit' => 'Proses Audit',
                                                                            'keputusan' => 'Menunggu Keputusan',
                                                                            'selesai' => 'Selesai (Terbit SNI)'
                                                                        ];
                                                                    }
                                                                @endphp
                                                                @foreach($nextOptions as $val => $label)
                                                                    <option value="{{ $val }}">{{ $label }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold text-secondary" style="font-size: 12px;">Catatan Tambahan</label>
                                                            <textarea name="catatan_status" class="form-control shadow-none" rows="3" style="border-radius: 10px;" placeholder="Tuliskan instruksi atau hasil evaluasi..."></textarea>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="modal-footer border-top-0 pt-0">
                                                        <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal" style="border-radius: 10px;">Batal</button>
                                                        <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm" style="border-radius: 10px;">Simpan & Teruskan</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        
                        @if(!$hasData)
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <div class="py-3">
                                        <i class="fa-solid fa-folder-open d-block mb-2 text-secondary fs-2"></i>
                                        <span style="font-size: 14px; font-weight: 500;">Belum ada antrean dokumen untuk tahap ini.</span>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

