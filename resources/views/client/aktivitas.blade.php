@extends('layouts.app')

@section('title', 'Aktivitas Permohonan Sertifikasi')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 style="color: #1e293b; font-weight: 700; margin: 0; font-size: 28px;">Aktivitas Permohonan</h2>
            <p style="color: #64748b; font-size: 14px; margin: 5px 0 0 0;">
                Pantau riwayat proses, status evaluasi, berkas lampiran, dan unduh dokumen resmi LS Pro Anda.
            </p>
        </div>
        <a href="/beranda" class="btn btn-light border text-secondary fw-semibold px-4" style="border-radius: 10px;">
            <i class="fa-solid fa-house me-2"></i> Kembali ke Beranda
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 12px; font-size: 14px; background: #dcfce7; color: #15803d;">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm border-0" style="border-radius: 16px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="background: white;">
                <thead style="background: #f8fafc; border-bottom: 2px solid #cbd5e1;">
                    <tr>
                        <th class="px-4 py-3 text-secondary uppercase font-bold" style="font-size: 11px; width: 12%;">NO. PERMOHONAN</th>
                        <th class="py-3 text-secondary uppercase font-bold" style="font-size: 11px; width: 38%;">KOMODITAS, MEREK & LAMPIRAN FISIK</th>
                        <th class="py-3 text-secondary uppercase font-bold" style="font-size: 11px; width: 12%;">TANGGAL MASUK</th>
                        <th class="py-3 text-secondary uppercase font-bold" style="font-size: 11px; width: 14%;">STATUS PROSEDUR</th>
                        <th class="py-3 text-center text-secondary uppercase font-bold" style="font-size: 11px; width: 12%;">DOKUMEN ANDA (7.2-1)</th>
                        <th class="py-3 text-center text-secondary uppercase font-bold" style="font-size: 11px; width: 12%;">HASIL EVALUASI (7.2-4)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pengajuans as $item)
                    @php
                        // Decode data_form json untuk mengambil informasi produk dan list file lampiran
                        $dataForm = json_decode($item->data_form);
                        $lampiran = $dataForm->lampiran ?? null;
                    @endphp
                    <tr style="border-bottom: 1px solid #f1f5f9;">
                        
                        <td class="px-4 fw-bold text-dark" style="font-size: 14px;">
                            #{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}
                        </td>

                        <td>
                            <div class="fw-bold text-dark text-capitalize" style="font-size: 16px;">
                                {{ $dataForm->nama_produk ?? 'Sertifikasi Pupuk' }}
                            </div>
                            <div class="text-muted small mb-2">
                                Merek: <span class="fw-semibold text-secondary">{{ $dataForm->merek_produk ?? '-' }}</span> | 
                                Jenis: <span class="text-secondary">{{ $dataForm->tipe_produk ?? '-' }}</span>
                            </div>

                            <div class="d-flex flex-wrap gap-1 mt-2">
                                <span class="badge bg-success bg-opacity-10 text-success fw-normal px-2 py-1" style="font-size: 10px; border-radius: 4px;">
                                    <i class="fa-solid fa-bookmark me-1"></i> SNI {{ $dataForm->sni_acuan ?? 'Terbaru' }}
                                </span>
                                
                                @if($lampiran)
                                    @if(!empty($lampiran->akte_perusahaan))
                                        <span class="badge bg-light text-secondary border fw-normal px-2 py-1" style="font-size: 10px; border-radius: 4px;">
                                            <i class="fa-solid fa-paperclip text-success me-1"></i> Akte Perusahaan
                                        </span>
                                    @endif
                                    @if(!empty($lampiran->izin_usaha_industri))
                                        <span class="badge bg-light text-secondary border fw-normal px-2 py-1" style="font-size: 10px; border-radius: 4px;">
                                            <i class="fa-solid fa-paperclip text-success me-1"></i> Izin Industri
                                        </span>
                                    @endif
                                    @if(!empty($lampiran->sertifikat_merek))
                                        <span class="badge bg-light text-secondary border fw-normal px-2 py-1" style="font-size: 10px; border-radius: 4px;">
                                            <i class="fa-solid fa-paperclip text-success me-1"></i> Hak Merek
                                        </span>
                                    @endif
                                    @if(!empty($lampiran->pedoman_mutu))
                                        <span class="badge bg-light text-secondary border fw-normal px-2 py-1" style="font-size: 10px; border-radius: 4px;">
                                            <i class="fa-solid fa-paperclip text-success me-1"></i> Pedoman SMM
                                        </span>
                                    @endif
                                    @if(!empty($lampiran->foto_produk))
                                        <span class="badge bg-light text-secondary border fw-normal px-2 py-1" style="font-size: 10px; border-radius: 4px;">
                                            <i class="fa-solid fa-image text-success me-1"></i> Foto Sampel
                                        </span>
                                    @endif
                                @endif
                            </div>
                        </td>

                        <td class="text-secondary" style="font-size: 14px;">
                            {{ $item->created_at ? $item->created_at->format('d M Y') : date('d M Y') }}
                        </td>

                        <td>
                            @if(strtolower($item->status) == 'lengkap')
                                <span class="badge px-3 py-2 text-success fw-semibold" style="background: #dcfce7; border-radius: 20px; font-size: 12px; display: inline-flex; align-items: center;">
                                    <i class="fa-solid fa-circle-check me-1.5"></i> Berkas Lengkap
                                </span>
                            @else
                                <span class="badge px-3 py-2 text-warning fw-semibold" style="background: #fef3c7; color: #d97706; border-radius: 20px; font-size: 12px; display: inline-flex; align-items: center;">
                                    <i class="fa-solid fa-spinner fa-spin me-1.5"></i> Verifikasi TU
                                </span>
                            @endif
                        </td>

                        <td class="text-center">
                            @if($item->file_permohonan)
                                <a href="{{ asset('storage/permohonan/' . $item->file_permohonan) }}" class="btn btn-outline-primary btn-sm px-3 fw-semibold shadow-sm" style="border-radius: 8px; font-size: 13px;" download>
                                    <i class="fa-solid fa-file-word me-1"></i> Form 7.2-1
                                </a>
                            @else
                                <span class="text-muted small" style="font-style: italic;">Membuat berkas...</span>
                            @endif
                        </td>

                        <td class="text-center">
                            @if(strtolower($item->status) == 'lengkap')
                                <a href="/admin/pengajuan/{{ $item->id }}/download-ceklis" class="btn btn-success text-white btn-sm px-3 fw-semibold shadow-sm animate__animated animate__pulse animate__infinite" style="background: #16a34a; border: none; border-radius: 8px; font-size: 13px;">
                                    <i class="fa-solid fa-file-shield me-1"></i> Form 7.2-4
                                </a>
                            @else
                                <button class="btn btn-light text-muted btn-sm px-3 border" style="border-radius: 8px; font-size: 13px; background: #f8fafc;" disabled>
                                    <i class="fa-solid fa-lock me-1" style="font-size: 11px;"></i> Terkunci
                                </button>
                            @endif
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-folder-open d-block mb-3 fs-2 text-secondary" style="opacity: 0.5;"></i>
                            <span class="fw-semibold">Belum Ada Riwayat Permohonan</span><br>
                            <small class="text-muted">Silakan lakukan pengajuan sertifikasi otomatis baru terlebih dahulu.</small>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection