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
                        <!-- DUMMY DATA 1 -->
                        <tr>
                            <td class="ps-4 fw-bold" style="color: #475569;">SMPL-2026-001</td>
                            <td>
                                <div class="fw-bold" style="color: #1e293b;">Pupuk Organik Granul</div>
                                <div class="text-muted" style="font-size: 11px;">Merek: Subur Makmur</div>
                            </td>
                            <td>PT Pertanian Nusantara</td>
                            <td style="font-size: 13px; color: #475569;">10 Jun 2026</td>
                            <td>
                                <span class="badge" style="background-color: #fffbeb; color: #b45309; border: 1px solid #fde68a; font-weight: 600; border-radius: 6px;">
                                    <i class="fa-solid fa-spinner fa-spin me-1"></i> Sedang Diuji
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-light border" style="border-radius: 8px;"><i class="fa-solid fa-eye text-muted"></i></button>
                            </td>
                        </tr>
                        <!-- DUMMY DATA 2 -->
                        <tr>
                            <td class="ps-4 fw-bold" style="color: #475569;">SMPL-2026-002</td>
                            <td>
                                <div class="fw-bold" style="color: #1e293b;">Pupuk NPK 15-15-15</div>
                                <div class="text-muted" style="font-size: 11px;">Merek: Tani Jaya Utama</div>
                            </td>
                            <td>CV Bumi Hijau</td>
                            <td style="font-size: 13px; color: #475569;">08 Jun 2026</td>
                            <td>
                                <span class="badge" style="background-color: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; font-weight: 600; border-radius: 6px;">
                                    <i class="fa-solid fa-check me-1"></i> Lulus Uji
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-light border" style="border-radius: 8px;"><i class="fa-solid fa-eye text-muted"></i></button>
                            </td>
                        </tr>
                        <!-- DUMMY DATA 3 -->
                        <tr>
                            <td class="ps-4 fw-bold" style="color: #475569;">SMPL-2026-003</td>
                            <td>
                                <div class="fw-bold" style="color: #1e293b;">Pupuk Urea Prill</div>
                                <div class="text-muted" style="font-size: 11px;">Merek: Agro Super</div>
                            </td>
                            <td>PT Agro Mandiri</td>
                            <td style="font-size: 13px; color: #475569;">05 Jun 2026</td>
                            <td>
                                <span class="badge" style="background-color: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; font-weight: 600; border-radius: 6px;">
                                    <i class="fa-solid fa-xmark me-1"></i> Tidak Lulus
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <button class="btn btn-sm btn-light border" style="border-radius: 8px;"><i class="fa-solid fa-eye text-muted"></i></button>
                            </td>
                        </tr>
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
