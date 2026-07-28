@extends('layouts.app')

@section('title', 'Aktivitas Permohonan Sertifikasi')

@section('content')
<div class="container-fluid py-4" style="max-width: 1200px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="color: #1e293b; font-weight: 700; font-size: 28px; margin-bottom: 4px;">Aktivitas Permohonan</h2>
            <p style="color: #64748b; font-size: 14px; margin: 0;">
                Pantau riwayat proses, status evaluasi, dan unduh berkas FORM 7.2-4 LS Pro Anda.
            </p>
        </div>
        <a href="/beranda" class="btn btn-white bg-white border text-secondary fw-semibold px-4 shadow-sm" style="border-radius: 8px; font-size: 14px;">
            Kembali ke Beranda
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px; font-size: 14px; background: #dcfce7; color: #15803d;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 12px; font-size: 14px; background: #fef2f2; color: #b91c1c;">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card shadow-sm border-0" style="border-radius: 16px; background-color: #ffffff;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0">
                    <thead style="border-bottom: 2px solid #f1f5f9;">
                        <tr>
                            <th class="px-4 py-3 text-dark text-uppercase fw-bold" style="font-size: 12px; width: 12%;">NO. PERMOHONAN</th>
                            <th class="py-3 text-dark text-uppercase fw-bold" style="font-size: 12px; width: 25%;">MEREK / KOMODITAS</th>
                            <th class="py-3 text-dark text-uppercase fw-bold" style="font-size: 12px; width: 15%;">TANGGAL MASUK</th>
                            <th class="py-3 text-dark text-uppercase fw-bold" style="font-size: 12px; width: 15%;">HASIL PERMOHONAN</th>
                            <th class="py-3 text-center text-dark text-uppercase fw-bold" style="font-size: 12px; width: 12%;">STATUS PROSEDUR</th>
                            <th class="py-3 text-dark text-uppercase fw-bold" style="font-size: 12px; width: 12%;">CATATAN Administrasi</th>
                            <th class="py-3 px-4 text-center text-dark text-uppercase fw-bold" style="font-size: 12px; width: 9%;">AKSI BERKAS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengajuans as $item)
                        @php
                            // Decode JSON untuk membaca data komoditas
                            $dataForm = is_string($item->data_form) ? json_decode($item->data_form) : (object) $item->data_form;
                        @endphp
                        <tr style="border-bottom: 1px solid #f8fafc;">
                            
                            <td class="px-4 py-3 fw-bold" style="color: #475569; font-size: 14px;">
                                #{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}
                            </td>

                            <td class="py-3">
                                <div class="fw-bold" style="color: #0f172a; font-size: 15px;">
                                    {{ $dataForm->merek_produk ?? $dataForm->merek ?? 'Tanpa Merek' }}
                                </div>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span style="color: #64748b; font-size: 13px;">
                                        Komoditas: {{ $dataForm->nama_produk ?? $dataForm->nama_pupuk ?? 'Pupuk' }}
                                    </span>
                                    <span class="fw-semibold" style="color: #16a34a; font-size: 11px;">
                                        {{ $dataForm->sni_acuan ?? $dataForm->no_sni ?? 'SNI Terbaru' }}
                                    </span>
                                </div>
                            </td>

                            <td class="py-3" style="color: #475569; font-size: 14px;">
                                {{ $item->created_at ? $item->created_at->translatedFormat('d M Y') : date('d M Y') }}
                            </td>

                            <td class="py-3">
                                @if($item->file_permohonan)
                                    <a href="{{ route('pengajuan.download', $item->id) }}" class="fw-bold text-decoration-none" style="color: #0284c7; font-size: 14px;" target="_blank">
                                        Form 7.2-1 Permohonan
                                    </a>
                                @else
                                    <span class="text-muted small fst-italic">Memproses...</span>
                                @endif
                            </td>

                            <td class="py-3 text-center">
                                @if(in_array(\App\Support\LsproType5Workflow::normalize($item->status), ['diajukan', 'verifikasi_tu'], true))
                                    <span class="badge fw-medium px-3 py-2 border" style="background-color: #fffbeb; color: #d97706; border-color: #fde68a !important; border-radius: 6px; font-size: 11px;">Menunggu Administrasi</span>
                                @elseif(\App\Support\LsproType5Workflow::normalize($item->status) === 'perjanjian')
                                    <span class="badge fw-medium px-3 py-2" style="background-color: #dcfce7; color: #16a34a; border-radius: 6px; font-size: 11px;">Lolos Verifikasi Administrasi</span>
                                @elseif(\App\Support\LsproType5Workflow::normalize($item->status) === 'perbaikan')
                                    <span class="badge fw-medium px-3 py-2" style="background-color: #fef2f2; color: #b91c1c; border-radius: 6px; font-size: 11px;">Masa Perbaikan</span>
                                @elseif(\App\Support\LsproType5Workflow::normalize($item->status) === 'proses_audit')
                                    <span class="badge fw-medium px-3 py-2" style="background-color: #e0f2fe; color: #0369a1; border-radius: 6px; font-size: 11px;">Sedang di Audit</span>
                                @elseif(\App\Support\LsproType5Workflow::normalize($item->status) === 'proses_evaluasi')
                                    <span class="badge fw-medium px-3 py-2" style="background-color: #f3e8ff; color: #6b21a8; border-radius: 6px; font-size: 11px;">Proses Evaluator</span>
                                @elseif(\App\Support\LsproType5Workflow::normalize($item->status) === 'selesai')
                                    <span class="badge fw-medium px-3 py-2" style="background-color: #ecfdf5; color: #065f46; border-radius: 6px; font-size: 11px;">Sertifikasi Terbit</span>
                                @else
                                    <span class="badge bg-secondary px-3 py-2 text-capitalize">{{ str_replace('_', ' ', $item->status) }}</span>
                                @endif
                            </td>

                            <td class="py-3">
                                @if(!empty($item->catatan))
                                    <span class="d-inline-block text-secondary" tabindex="0" data-bs-toggle="tooltip" title="{{ $item->catatan }}" style="font-size: 13px; max-width: 140px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $item->catatan }}
                                    </span>
                                @else
                                    <span class="text-muted fst-italic" style="font-size: 12px;">-</span>
                                @endif
                            </td>

                            <td class="py-3 px-4 text-center">
                                @if(in_array(\App\Support\LsproType5Workflow::normalize($item->status), ['perjanjian', 'billing', 'proses_evaluasi', 'proses_audit', 'keputusan', 'selesai'], true))
                                    <a href="{{ route('pengajuan.download724', $item->id) }}" class="btn btn-sm w-100 fw-bold shadow-sm" style="background-color: #15803d; color: white; border-radius: 6px; font-size: 12px; padding: 6px 12px;">
                                        <i class="fa-solid fa-download me-1"></i> Form 7.2-4
                                    </a>
                                @else
                                    <span class="badge w-100 fw-medium px-3 py-2 border" style="background-color: #f8fafc; color: #64748b; border-color: #e2e8f0 !important; border-radius: 6px; font-size: 12px;">
                                        Belum Tersedia
                                    </span>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-folder-open mb-2 fs-3 text-secondary d-block"></i>
                                Belum ada riwayat permohonan yang diajukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function(){
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endsection
