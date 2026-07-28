@extends('layouts.app')
@section('title', 'Data Sampel Uji - LS Pro')

@section('content')
<div class="container-fluid py-3 px-3 px-md-4">
    <div class="mb-4">
        <h2 style="color: #1e293b; font-weight: 700; letter-spacing: -0.5px; margin-bottom: 4px;">Data Sampel Uji Laboratorium</h2>
        <p class="text-muted small mb-0">Daftar sampel produk yang dikirim untuk pengujian teknis (Data Dummy).</p>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold" style="color: #1e293b; font-size: 1rem;">
                <i class="fa-solid fa-vials text-primary me-2"></i> Status Pengujian Sampel
            </h5>
            <button class="btn btn-sm btn-primary rounded-pill px-3 fw-bold shadow-sm">
                <i class="fa-solid fa-plus me-1"></i> Input Sampel Baru
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700;">Kode Sampel</th>
                            <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700;">Nama Produk / Merek</th>
                            <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700;">Klien</th>
                            <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700;">Tanggal Uji</th>
                            <th class="py-3 text-secondary text-uppercase" style="font-size: 11px; font-weight: 700;">Status Uji</th>
                            <th class="pe-4 py-3 text-end text-secondary text-uppercase" style="font-size: 11px; font-weight: 700;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengajuans as $item)
                            @php
                                $formData = is_string($item->data_form) ? json_decode($item->data_form, true) : ($item->data_form ?? []);
                                $namaProduk = $formData['nama_produk'] ?? 'Produk Tidak Diketahui';
                                $merekDagang = $formData['merek_dagang'] ?? '-';
                                $kodeSampel = 'SMPL-' . date('Y', strtotime($item->created_at)) . '-' . str_pad($item->id, 3, '0', STR_PAD_LEFT);
                                
                                $statusUjiText = 'Sedang Diuji';
                                $statusUjiClass = 'background-color: #fffbeb; color: #b45309; border: 1px solid #fde68a;';
                                $statusUjiIcon = 'fa-solid fa-spinner fa-spin';
                                
                                if ($item->status === 'keputusan') {
                                    $statusUjiText = 'Lulus Uji';
                                    $statusUjiClass = 'background-color: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0;';
                                    $statusUjiIcon = 'fa-solid fa-check';
                                }
                            @endphp
                            <tr>
                                <td class="ps-4 fw-bold" style="color: #475569;">{{ $kodeSampel }}</td>
                                <td>
                                    <div class="fw-bold" style="color: #1e293b;">{{ $namaProduk }}</div>
                                    <div class="text-muted" style="font-size: 11px;">Merek: {{ $merekDagang }}</div>
                                </td>
                                <td>{{ $item->user->name ?? 'Klien' }}</td>
                                <td style="font-size: 13px; color: #475569;">{{ $item->updated_at->translatedFormat('d M Y') }}</td>
                                <td>
                                    <span class="badge" style="{{ $statusUjiClass }} font-weight: 600; border-radius: 6px;">
                                        <i class="{{ $statusUjiIcon }} me-1"></i> {{ $statusUjiText }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <button class="btn btn-sm btn-light border" style="border-radius: 8px;"><i class="fa-solid fa-eye text-muted"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <div class="py-3">
                                        <i class="fa-solid fa-vial-circle-check d-block mb-2 text-secondary fs-2"></i>
                                        <span style="font-size: 14px; font-weight: 500;">Belum ada data sampel yang diproses.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3 bg-light text-center border-top">
                <span class="text-muted" style="font-size: 12px;">Data di atas merupakan simulasi (dummy). Integrasi dengan LIMS Lab akan ditambahkan pada tahap selanjutnya.</span>
            </div>
        </div>
    </div>
</div>
@endsection
