@extends('layouts.app')

@section('title', 'Aktivitas Permohonan Sertifikasi')

@section('content')
<div class="container py-4">
    
    @php
        // Deteksi role user login saat ini
        $userRole = auth()->check() ? trim(strtolower(auth()->user()->role)) : 'guest';
        $isAdminTu = ($userRole === 'admin' || $userRole === 'tu' || str_contains($userRole, 'tu') || str_contains($userRole, 'admin') || str_contains($userRole, 'petugas'));
    @endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="color: #1e293b; font-weight: 700; letter-spacing: -0.5px; margin-bottom: 4px;">
                {{ $isAdminTu ? 'Manajemen Aktivitas (Akses internal TU)' : 'Aktivitas Permohonan' }}
            </h2>
            <p class="text-muted small mb-0">
                {{ $isAdminTu ? 'Daftar seluruh masuk permohonan sertifikasi dari klien untuk dievaluasi.' : 'Pantau riwayat proses, status evaluasi, dan unduh berkas FORM 7.2-4 LS Pro Anda.' }}
            </p>
        </div>
        
        @if($isAdminTu)
            <a href="{{ url('/admin/dashboard') }}" class="btn btn-sm btn-light border text-secondary" style="border-radius: 8px;">
                <i class="bi bi-speedometer2"></i> Dashboard Admin
            </a>
        @else
            <a href="{{ url('/dashboard') }}" class="btn btn-sm btn-light border text-secondary" style="border-radius: 8px;">
                <i class="bi bi-house-door"></i> Kembali ke Beranda
            </a>
        @endif
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden; background: white;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 13.5px;">
                <thead class="table-light border-bottom text-uppercase" style="font-size: 11.5px; font-weight: 700;">
                    <tr>
                        <th class="ps-4 py-3" width="15%">No. Permohonan</th>
                        <th class="py-3" width="30%">Komoditas / Merek</th>
                        <th class="py-3 text-center" width="15%">Tanggal Masuk</th>
                        <th class="py-3 text-center" width="20%">Status Prosedur</th>
                        <th class="pe-4 py-3 text-end" width="20%">Aksi Berkas</th>
                    </tr>
                </thead>
                <tbody style="color: #334155;">
                    @forelse($pengajuans as $p)
                        @php
                            // 1. Ekstraksi Data JSON / Array Lapangan Form
                            $rawForm = $p->parsed_form ?? $p->data_form;
                            if (is_array($rawForm)) {
                                $formData = $rawForm;
                            } elseif (is_string($rawForm)) {
                                $formData = json_decode($rawForm, true) ?? [];
                            } else {
                                $formData = [];
                            }

                            // 2. Mapping Penamaan Produk
                            $namaPupuk = $formData['nama_pupuk'] ?? ($formData['nama_produk'] ?? ($formData['jenis_pupuk'] ?? 'Sertifikasi Pupuk'));
                            $merekRaw = $formData['merek_pupuk'] ?? ($formData['merek'] ?? '');
                            $merekPupuk = $merekRaw ? ' (' . $merekRaw . ')' : '';
                            $standarSni = $formData['standar_sni'] ?? 'SNI Terbaru';
                            
                            // 3. Normalisasi Status
                            $statusBerkas = trim(strtolower($p->status));
                            
                            // 4. Cek Keberadaan File Utama
                            $fileExist = $p->file_permohonan && file_exists(storage_path('app/public/permohonan/' . $p->file_permohonan));
                        @endphp
                        
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td class="ps-4 py-3 font-monospace fw-bold text-secondary">
                                #{{ str_pad($p->id, 5, '0', STR_PAD_LEFT) }}
                            </td>
                            
                            <td class="py-3">
                                <div class="fw-bold text-dark" style="font-size: 14px;">
                                    {{ $namaPupuk }}{{ $merekPupuk }}
                                </div>
                                <div class="text-muted small d-flex align-items-center gap-2 mt-0.5">
                                    <span class="text-success font-monospace" style="font-size: 11px;">
                                        <i class="bi bi-bookmark-fill"></i> {{ $standarSni }}
                                    </span>
                                    @if($isAdminTu)
                                        <span class="text-dark fw-bold" style="font-size: 11px; background: #e2e8f0; padding: 1px 6px; border-radius: 4px;">
                                            <i class="bi bi-person"></i> Pengirim: {{ $p->user->name ?? 'Klien (ID: '.$p->user_id.')' }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            
                            <td class="py-3 text-center text-muted">
                                {{ $p->created_at ? $p->created_at->translatedFormat('d M Y') : '-' }}
                            </td>
                            
                            <td class="py-3 text-center">
                                @if($statusBerkas == 'diajukan' || $statusBerkas == 'pending' || $statusBerkas == 'terkirim')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1.5" style="border-radius: 6px; font-weight: 600;">
                                        <i class="bi bi-clock-history me-1"></i> Menunggu Verifikasi TU
                                    </span>
                                @elseif($statusBerkas == 'perbaikan' || $statusBerkas == 'ditolak')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-1.5" style="border-radius: 6px; font-weight: 600;">
                                        <i class="bi bi-exclamation-triangle me-1"></i> Butuh Perbaikan Berkas
                                    </span>
                                @elseif($statusBerkas == 'disetujui_tu' || $statusBerkas == 'lengkap')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5" style="border-radius: 6px; font-weight: 600;">
                                        <i class="bi bi-patch-check-fill me-1"></i> Berkas Lengkap
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark border px-2.5 py-1.5" style="border-radius: 6px; font-weight: 600;">
                                        {{ strtoupper($p->status) }}
                                    </span>
                                @endif
                            </td>
                            
                            <td class="pe-4 py-3 text-end">
                                <div class="d-inline-flex gap-1.5 align-items-center">
                                    
                                    @if($isAdminTu)
                                        @if($statusBerkas == 'diajukan' || $statusBerkas == 'pending' || $statusBerkas == 'terkirim')
                                            <a href="{{ url('/admin/pengajuan/' . $p->id . '/ceklis') }}" class="btn btn-sm px-3 fw-bold shadow-sm text-white" style="border-radius: 6px; font-size: 12px; background-color: #f59e0b; border: none;">
                                                <i class="bi bi-clipboard-check me-1"></i> Evaluasi TU
                                            </a>
                                        @endif
                                    @endif

                                    @if(!$isAdminTu && ($statusBerkas == 'perbaikan' || $statusBerkas == 'ditolak'))
                                        <a href="{{ url('/pengajuan/edit/' . $p->id) }}" class="btn btn-sm btn-danger px-2.5 fw-bold shadow-sm" style="border-radius: 6px; font-size: 12px;">
                                            <i class="bi bi-pencil-square"></i> Perbaiki Dokumen
                                        </a>
                                    @endif

                                    @if($statusBerkas == 'disetujui_tu' || $statusBerkas == 'lengkap')
                                        <a href="{{ url('/admin/pengajuan/' . $p->id . '/ceklis') }}" class="btn btn-sm btn-outline-success px-2.5 shadow-sm" target="_blank" style="border-radius: 6px; font-size: 12px; font-weight: 600;">
                                            <i class="bi bi-printer"></i> Hasil Form 7.2-4
                                        </a>
                                    @endif

                                    @if($fileExist)
                                        <a href="{{ asset('storage/permohonan/' . $p->file_permohonan) }}" class="btn btn-sm btn-light border text-secondary px-2 shadow-sm" target="_blank" style="border-radius: 6px;">
                                            <i class="bi bi-download"></i>
                                        </a>
                                    @else
                                        <button class="btn btn-sm btn-light border text-muted px-2" disabled style="border-radius: 6px; opacity: 0.5;">
                                            <i class="bi bi-file-earmark-x"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <div class="mb-2" style="font-size: 28px;"><i class="bi bi-folder-x text-secondary"></i></div>
                                <h6 class="fw-bold text-dark mb-1">Belum Ada Aktivitas Permohonan</h6>
                                <p class="small text-muted mb-0">Tidak ditemukan draf formulir permohonan sertifikasi pupuk di sistem.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection