@extends('layouts.app')

@section('title', 'Detail Alur Sertifikasi #' . str_pad($pengajuan->id, 5, '0', STR_PAD_LEFT))

@section('content')
@php
    $currentStage = $pengajuan->workflowStage();
    $latestInvoice = $pengajuan->invoices->sortByDesc('created_at')->first();
    $normalizedStatus = \App\Support\LsproType5Workflow::normalize($pengajuan->status);
    $isCorrection = \App\Support\LsproType5Workflow::isCorrection($pengajuan->status);
    $isRejected = \App\Support\LsproType5Workflow::isRejected($pengajuan->status);
    $isDone = $normalizedStatus === 'selesai';
    $accentColor = $isRejected ? '#dc2626' : ($isDone ? '#16a34a' : ($isCorrection ? '#d97706' : '#0284c7'));
@endphp

<div class="container-fluid py-4" style="max-width: 1400px;">

    {{-- Breadcrumb & Header --}}
    <div class="mb-4 pb-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-3 mb-2">
                <h2 class="fw-bold mb-0" style="color: #0f172a; font-size: 26px;">
                    Permohonan #{{ str_pad($pengajuan->id, 5, '0', STR_PAD_LEFT) }}
                </h2>
                <span class="badge" style="background: #e0f2fe; color: #0369a1; border-radius: 6px; font-size: 12px; padding: 6px 10px; font-weight: 600;">
                    {{ ucfirst($pengajuan->jenis_pengajuan) }}
                </span>
            </div>
            <div class="text-muted d-flex align-items-center gap-2" style="font-size: 13px;">
                <i class="fa-regular fa-calendar" style="color: #94a3b8;"></i> Dibuat pada {{ $pengajuan->created_at->translatedFormat('d F Y') }}
                @if($pengajuan->user)
                    <span style="color: #cbd5e1;">|</span>
                    <i class="fa-regular fa-user" style="color: #94a3b8;"></i> {{ $pengajuan->user->name }}
                @endif
            </div>
        </div>
        <div>
            <a href="{{ route('aktivitas.index') }}" class="btn btn-sm shadow-sm" style="background: #ffffff; color: #475569; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 13px; font-weight: 600; padding: 8px 16px;">
                <i class="fa-solid fa-arrow-left me-2"></i> Kembali ke Aktivitas
            </a>
        </div>
    </div>



    <div class="row g-4">
        {{-- Kiri: Detail Tahap --}}
        <div class="col-xl-8">

            {{-- Catatan Perbaikan --}}
            @if($isCorrection && !empty($pengajuan->catatan))
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; border-left: 4px solid #f59e0b !important; border: none;">
                    <div class="card-body" style="background: #fffbeb; border-radius: 14px;">
                        <div class="d-flex align-items-start gap-3">
                            <i class="fa-solid fa-triangle-exclamation" style="font-size: 22px; color: #d97706; margin-top: 2px; flex-shrink: 0;"></i>
                            @php
                                $perbaikanRole = 'Administrasi';
                                $lastHistory = $pengajuan->statusHistories()->latest()->first();
                                if ($lastHistory && $lastHistory->actor) {
                                    $actorSubRole = strtolower($lastHistory->actor->sub_role ?? '');
                                    if ($actorSubRole === 'audit') {
                                        $perbaikanRole = 'Tim Audit / Evaluasi';
                                    } elseif ($actorSubRole === 'layanan') {
                                        $perbaikanRole = 'Tim Layanan / Keuangan';
                                    }
                                }
                            @endphp
                            <div>
                                <div class="fw-bold mb-1" style="color: #92400e;">Catatan {{ $perbaikanRole }} â€“ Perbaikan Diperlukan</div>
                                <p class="mb-3" style="color: #78350f; font-size: 13px; line-height: 1.6;">{{ $pengajuan->catatan }}</p>
                                
                                @if(empty($pengajuan->nama_tu))
                                    {{-- Perbaikan Pengecekan Awal --}}
                                    <form action="{{ route('pengajuan.upload_permohonan', $pengajuan) }}"
                                          method="POST"
                                          enctype="multipart/form-data"
                                          class="d-flex flex-wrap align-items-center gap-2">
                                        @csrf
                                        <input type="file"
                                               name="file_permohonan_ttd"
                                               class="form-control"
                                               accept=".pdf"
                                               required
                                               style="max-width: 260px; border-radius: 8px; font-size: 13px;">
                                        <button type="submit" class="btn fw-semibold px-4" style="background: #f59e0b; color: white; border-radius: 8px; font-size: 13px; border: none;">
                                            <i class="fa-solid fa-upload me-1"></i> Upload Dokumen Perbaikan
                                        </button>
                                    </form>
                                @else
                                    {{-- Perbaikan Kelengkapan Berkas --}}
                                    <div class="alert alert-warning border-0 p-3 mb-3" style="background-color: rgba(245, 158, 11, 0.1); font-size: 13px; border-radius: 8px;">
                                        <i class="fa-solid fa-circle-info me-1 text-warning"></i> 
                                        Silakan cek bagian lampiran mana yang harus direvisi (lihat form evaluasi atau catatan), lalu unggah ulang lampiran tersebut.
                                    </div>
                                    <a href="{{ route('pengajuan.lampiran', $pengajuan->id) }}" class="btn fw-semibold px-4" style="background: #f59e0b; color: white; border-radius: 8px; font-size: 13px; border: none;">
                                        <i class="fa-solid fa-folder-open me-1"></i> Buka Halaman Upload Lampiran
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Menunggu Kop Surat & Tanda Tangan --}}
            @if($normalizedStatus === 'menunggu_ttd')
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; border: 1px solid #e2e8f0;">
                    <div class="card-body p-4" style="background: #ffffff; border-radius: 14px;">
                        <div class="d-flex align-items-start gap-3">
                            <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle" style="width: 45px; height: 45px; flex-shrink: 0;">
                                <i class="fa-solid fa-file-signature fs-5"></i>
                            </div>
                            <div class="w-100">
                                <div class="fw-bold mb-1" style="color: #1e293b; font-size: 16px;">Tindakan Diperlukan: Unggah Kop Surat & Tanda Tangan</div>
                                <div class="alert alert-info border-0 mt-3 mb-4" style="background-color: #eff6ff; color: #1e40af; border-radius: 8px;">
                                    <i class="fa-solid fa-circle-info me-2"></i> <strong>Instruksi Penting:</strong> Anda harus memasukkan Surat Permohonan yang <strong>sudah ditandatangani</strong> dan <strong>menggunakan Kop Surat Perusahaan</strong>.
                                </div>
                                <ol class="text-muted mb-4" style="font-size: 14px; padding-left: 1.2rem; line-height: 1.7;">
                                    <li>Unduh dokumen draf Surat Permohonan yang telah kami hasilkan secara otomatis.</li>
                                    <li>Cetak atau buka menggunakan aplikasi pengolah kata, lalu tambahkan <strong>Kop Surat Perusahaan</strong> Anda.</li>
                                    <li>Tandatangani dokumen tersebut secara resmi.</li>
                                    <li>Pindai (scan) atau simpan dokumen tersebut dalam format <strong>PDF</strong>.</li>
                                    <li>Unggah dokumen PDF tersebut melalui form di bawah ini.</li>
                                </ol>

                                <a href="{{ route('pengajuan.download_draft', $pengajuan->id) }}" class="btn btn-outline-primary fw-semibold mb-4 px-4" style="border-radius: 8px; font-size: 13px;">
                                    <i class="fa-solid fa-download me-2"></i> Unduh Draf Surat Permohonan
                                </a>

                                <form action="{{ route('pengajuan.upload_permohonan', $pengajuan->id) }}" method="POST" enctype="multipart/form-data" class="p-4" style="background: #f8fafc; border-radius: 10px; border: 1px dashed #cbd5e1;">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-secondary" style="font-size: 13px;">Unggah PDF Surat Permohonan (Kop & TTD) <span class="text-danger">*</span></label>
                                        <input type="file" name="file_permohonan_ttd" class="form-control bg-white" accept=".pdf" required style="border-radius: 8px; padding: 10px;">
                                        <div class="form-text mt-2" style="font-size: 12px;"><i class="fa-solid fa-file-pdf text-danger me-1"></i> Hanya format PDF (Maksimal 10MB)</div>
                                    </div>
                                    <button type="submit" class="btn btn-primary fw-semibold px-4 w-100 mt-2" style="border-radius: 8px; padding: 10px;">
                                        <i class="fa-solid fa-cloud-arrow-up me-2"></i> Unggah Surat Permohonan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Diajukan (Menunggu Administrasi) --}}
            @if($normalizedStatus === 'diajukan')
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                    <div class="card-body p-4" style="background: #f8fafc; border-radius: 14px;">
                        <div class="d-flex align-items-start gap-3">
                            <i class="fa-solid fa-hourglass-half" style="font-size: 22px; color: #64748b; margin-top: 2px; flex-shrink: 0;"></i>
                            <div>
                                <div class="fw-bold mb-1" style="color: #334155;">Menunggu Verifikasi Administrasi</div>
                                <p class="mb-0" style="color: #475569; font-size: 13px;">
                                    Permohonan Anda telah kami terima dan sedang dalam antrean pemeriksaan oleh bagian Administrasi. Mohon menunggu notifikasi selanjutnya.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Perjanjian & Lampiran (Tindakan Klien) --}}
            @if($normalizedStatus === 'perjanjian_lampiran')
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px; border: 1px solid #e2e8f0;">
                    <div class="card-body p-4" style="background: #ffffff; border-radius: 14px;">
                        <div class="d-flex align-items-start gap-3">
                            <div class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-circle" style="width: 45px; height: 45px; flex-shrink: 0;">
                                <i class="fa-solid fa-pen-to-square fs-5"></i>
                            </div>
                            <div class="w-100">
                                <div class="fw-bold mb-1" style="color: #1e293b; font-size: 16px;">Tindakan Diperlukan: Mengisi Perjanjian & Dokumen Kelengkapan</div>
                                <div class="alert alert-info border-0 mt-3 mb-4" style="background-color: #eff6ff; color: #1e40af; border-radius: 8px;">
                                    <i class="fa-solid fa-circle-info me-2"></i> <strong>Instruksi:</strong> Pembayaran Billing 1 Anda telah diverifikasi. Silakan isi form Perjanjian Sertifikasi dan unggah dokumen kelengkapan lainnya.
                                </div>
                                <a href="{{ route('pengajuan.lampiran', $pengajuan->id) }}" class="btn btn-primary fw-semibold px-4" style="border-radius: 8px; font-size: 13px;">
                                    <i class="fa-solid fa-pen-to-square me-2"></i> Buka Form Perjanjian & Lampiran
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Audit Kecukupan (Menunggu Tim) --}}
            @if($normalizedStatus === 'audit_kecukupan')
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                    <div class="card-body p-4" style="background: #f8fafc; border-radius: 14px;">
                        <div class="d-flex align-items-start gap-3">
                            <i class="fa-solid fa-list-check" style="font-size: 22px; color: #64748b; margin-top: 2px; flex-shrink: 0;"></i>
                            <div>
                                <div class="fw-bold mb-1" style="color: #334155;">Proses Audit Kecukupan Dokumen</div>
                                <p class="mb-0" style="color: #475569; font-size: 13px;">
                                    Tim Layanan/Audit sedang melakukan Audit Kecukupan Dokumen Anda. Mohon menunggu hasil pengecekan lebih lanjut.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Proses Evaluasi / Penjadwalan --}}
            @if($normalizedStatus === 'proses_evaluasi')
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                    <div class="card-body p-4" style="background: #eff6ff; border-radius: 14px;">
                        <div class="d-flex align-items-start gap-3">
                            <i class="fa-solid fa-calendar-alt" style="font-size: 22px; color: #3b82f6; margin-top: 2px; flex-shrink: 0;"></i>
                            <div>
                                <div class="fw-bold mb-1" style="color: #1e3a8a;">Menunggu Penjadwalan Audit</div>
                                <p class="mb-0" style="color: #1e40af; font-size: 13px;">
                                    Saat ini bagian Administrasi sedang menyiapkan jadwal Audit Lapangan dan Pengambilan Contoh. Mohon menunggu informasi selanjutnya.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Tindakan Perbaikan (LKS) --}}
            @if($normalizedStatus === 'tindakan_perbaikan')
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                    <div class="card-body p-4" style="background: #fef2f2; border-radius: 14px;">
                        <div class="d-flex align-items-start gap-3">
                            <i class="fa-solid fa-triangle-exclamation" style="font-size: 22px; color: #dc2626; margin-top: 2px; flex-shrink: 0;"></i>
                            <div>
                                <div class="fw-bold mb-1" style="color: #9f1239;">Tindakan Perbaikan Diperlukan (Temuan Audit)</div>
                                <p class="mb-3" style="color: #be123c; font-size: 13px;">
                                    Tim Audit menemukan adanya ketidaksesuaian saat Audit Lapangan. Silakan lihat catatan audit dan unggah Bukti Tindakan Perbaikan.
                                </p>
                                <a href="{{ route('pengajuan.tindakan_perbaikan', $pengajuan->id) }}" class="btn btn-danger fw-semibold px-4" style="border-radius: 8px; font-size: 13px;">
                                    <i class="fa-solid fa-upload me-1"></i> Unggah Tindakan Perbaikan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif


            {{-- Menunggu Persetujuan Jadwal Audit --}}
            @if($normalizedStatus === 'menunggu_persetujuan_jadwal')
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                    <div class="card-body p-4" style="background: #eef2ff; border-radius: 14px;">
                        <div class="d-flex align-items-start gap-3">
                            <i class="fa-solid fa-calendar-check" style="font-size: 22px; color: #4f46e5; margin-top: 2px; flex-shrink: 0;"></i>
                            <div style="flex-grow: 1;">
                                <div class="fw-bold mb-1" style="color: #312e81;">Konfirmasi Jadwal Audit & Pengambilan Contoh</div>
                                <p class="mb-3" style="color: #4338ca; font-size: 13px;">
                                    Tim Audit telah menentukan jadwal audit Anda. Silakan setujui untuk melanjutkan.
                                </p>
                                <div class="p-3 mb-3" style="background: white; border-radius: 8px; border: 1px solid #c7d2fe;">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted" style="font-size: 12px;">Tanggal Audit Lapangan & Pengambilan Sampel:</span>
                                        <span class="fw-bold" style="font-size: 13px; color: #1e293b;">
                                            {{ $pengajuan->jadwal_audit ? \Carbon\Carbon::parse($pengajuan->jadwal_audit)->translatedFormat('l, d F Y') : '-' }}
                                        </span>
                                    </div>
                                </div>
                                <form action="{{ route('pengajuan.setuju_jadwal', $pengajuan->id) }}" method="POST" class="d-flex gap-2">
                                    @csrf
                                    <input type="hidden" name="is_setuju" value="1">
                                    <button type="submit" class="btn fw-semibold px-4" style="border-radius: 8px; font-size: 13px; background-color: #4f46e5; color: white; border: none;">
                                        <i class="fa-solid fa-check me-1"></i> Setuju Jadwal
                                    </button>
                                </form>
                                <form action="{{ route('pengajuan.setuju_jadwal', $pengajuan->id) }}" method="POST" class="mt-2">
                                    @csrf
                                    <input type="hidden" name="is_setuju" value="0">
                                    <div class="input-group">
                                        <input type="text" name="alasan" class="form-control" placeholder="Alasan penolakan (jika menolak)..." style="font-size: 12px; border-radius: 8px 0 0 8px;">
                                        <button type="submit" class="btn btn-outline-danger" style="font-size: 12px; border-radius: 0 8px 8px 0;">Tolak</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Tunggu LHP --}}
            @if($normalizedStatus === 'menunggu_lhp')
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                    <div class="card-body p-4" style="background: #f8fafc; border-radius: 14px; border: 1px dashed #cbd5e1;">
                        <div class="d-flex align-items-start gap-3">
                            <i class="fa-solid fa-flask" style="font-size: 22px; color: #0284c7; margin-top: 2px; flex-shrink: 0;"></i>
                            <div style="flex-grow: 1;">
                                <div class="fw-bold mb-1" style="color: #0f172a;">Menunggu Hasil Uji Laboratorium</div>
                                <p class="mb-0" style="color: #475569; font-size: 13px;">
                                    Audit Lapangan dan Pengambilan Contoh telah selesai. Saat ini <strong>LSPro (Laboratorium)</strong> sedang memproses pengujian sampel Anda. Harap tunggu hingga Laporan Hasil Pengujian (LHP) diterbitkan dan diunggah ke dalam sistem.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Proses Evaluasi (Info) --}}
            @if($normalizedStatus === 'evaluasi')
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                    <div class="card-body p-4" style="background: #f8fafc; border-radius: 14px; border-left: 4px solid #0284c7;">
                        <div class="d-flex align-items-start gap-3">
                            <i class="fa-solid fa-microscope" style="font-size: 22px; color: #0284c7; margin-top: 2px; flex-shrink: 0;"></i>
                            <div style="flex-grow: 1;">
                                <div class="fw-bold mb-1" style="color: #0f172a;">Dokumen Sedang Dievaluasi</div>
                                <p class="mb-0" style="color: #475569; font-size: 13px;">
                                    Laporan Hasil Pengujian (LHP) Anda telah diterima. Saat ini, <strong>Tim Evaluasi</strong> LSPro sedang memproses dan mengevaluasi dokumen tersebut untuk memutuskan kelayakan sertifikasi. Harap tunggu hingga proses ini selesai (PIC: Tim Evaluasi).
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Selesai / Sertifikat Kesesuaian SNI --}}
            @if($isDone)
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                    <div class="card-body p-4" style="background: linear-gradient(135deg, #f0fdf4, #dcfce7); border-radius: 14px; text-align: center;">
                        <i class="fa-solid fa-award" style="font-size: 48px; color: #16a34a; margin-bottom: 12px;"></i>
                        <h5 class="fw-bold" style="color: #14532d;">Sertifikat Kesesuaian SNI Telah Diterbitkan</h5>
                        <p class="text-muted mb-3" style="font-size: 13px;">Selamat! Proses sertifikasi produk Anda telah selesai.</p>
                        <a href="{{ route('sertifikat.cetak', $pengajuan->id) }}" target="_blank" class="btn fw-semibold px-4" style="background: #16a34a; color: white; border-radius: 8px; font-size: 14px; border: none; box-shadow: 0 4px 6px rgba(22, 163, 74, 0.2);">
                            <i class="fa-solid fa-download me-2"></i> Unduh Sertifikat (PDF)
                        </a>
                    </div>
                </div>
            @endif

            {{-- Ditolak --}}
            @if($isRejected)
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                    <div class="card-body p-4" style="background: #fef2f2; border-radius: 14px;">
                        <div class="d-flex align-items-start gap-3">
                            <i class="fa-solid fa-circle-xmark" style="font-size: 28px; color: #dc2626; margin-top: 2px; flex-shrink: 0;"></i>
                            <div>
                                <div class="fw-bold mb-1" style="color: #991b1b;">Permohonan Ditolak</div>
                                @if(!empty($pengajuan->catatan))
                                    <p class="mb-3" style="color: #7f1d1d; font-size: 13px;">{{ $pengajuan->catatan }}</p>
                                @endif
                                <a href="{{ route('banding.index') }}" class="btn fw-semibold px-4" style="background: #dc2626; color: white; border-radius: 8px; font-size: 13px; border: none;">
                                    <i class="fa-solid fa-gavel me-1"></i> Ajukan Banding
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Grid Tahapan --}}
            <section class="bg-white border shadow-sm" style="border-radius: 14px;">
                <div class="px-4 py-3 border-bottom">
                    <h5 class="fw-bold mb-1" style="color: #0f172a; font-size: 15px;">Detail Setiap Tahap</h5>
                    <p class="text-muted mb-0" style="font-size: 12px;">Tahapan alur sertifikasi.</p>
                </div>
                <div class="p-4">
                    <div class="row g-3">
                        @foreach($workflowStages as $number => $stage)
                            @php
                                $stepDone = ($number < $currentStage) || $isDone;
                                $stepCurrent = ($number === $currentStage) && !$isDone;
                            @endphp
                            <div class="col-md-6">
                                <div class="h-100 p-3"
                                     style="border-radius: 10px; border: 1.5px solid {{ $stepCurrent ? '#0284c7' : ($stepDone ? '#bbf7d0' : '#e2e8f0') }}; background: {{ $stepCurrent ? '#f0f9ff' : ($stepDone ? '#f8fafc' : '#fff') }};">
                                    <div class="d-flex align-items-start gap-3">
                                        <span class="d-inline-flex align-items-center justify-content-center flex-shrink-0"
                                              style="width: 34px; height: 34px; border-radius: 50%; background: {{ $stepDone ? '#16a34a' : ($stepCurrent ? '#0284c7' : '#e2e8f0') }}; color: {{ $number <= $currentStage ? '#fff' : '#94a3b8' }}; font-size: 11px; font-weight: 700;">
                                            @if($stepDone)
                                                <i class="fa-solid fa-check" style="font-size: 11px;"></i>
                                            @else
                                                {{ str_pad($number, 2, '0', STR_PAD_LEFT) }}
                                            @endif
                                        </span>
                                        <div>
                                            <h6 class="fw-bold mb-1" style="color: #0f172a; font-size: 13px;">{{ $stage['title'] }}</h6>
                                            <div class="mt-3 pt-3" style="border-top: 1px dashed {{ $stepCurrent ? '#bae6fd' : '#e2e8f0' }};">
                                                <div style="font-size: 10px; font-weight: 700; color: {{ $stepCurrent ? '#0ea5e9' : '#94a3b8' }}; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px;">Sub Tahapan:</div>
                                                <div class="d-flex flex-column gap-2">
                                                    @php $subStagePassed = true; @endphp
                                                    @foreach(\App\Support\LsproType5Workflow::STATUS_MAP as $statusKey => $statusData)
                                                        @if($statusData['stage'] === $number && !isset($statusData['rejected']) && !isset($statusData['correction']))
                                                            @php
                                                                $isThisSubStageActive = ($normalizedStatus === $statusKey);
                                                                if ($isThisSubStageActive) {
                                                                    $subStagePassed = false;
                                                                }
                                                                
                                                                $isDoneSub = $stepDone || ($stepCurrent && $subStagePassed && !$isThisSubStageActive);
                                                                
                                                                $subColor = $isThisSubStageActive ? '#0284c7' : ($isDoneSub ? '#16a34a' : '#94a3b8');
                                                                $fontWeight = $isThisSubStageActive ? '700' : '500';
                                                                $icon = $isDoneSub ? 'fa-solid fa-circle-check' : ($isThisSubStageActive ? 'fa-solid fa-circle-dot' : 'fa-regular fa-circle');
                                                            @endphp
                                                            <div class="d-flex align-items-center gap-2" style="color: {{ $subColor }}; font-weight: {{ $fontWeight }};">
                                                                <i class="{{ $icon }}" style="font-size: 13px;"></i>
                                                                <span style="font-size: 11.5px; line-height: 1.2;">{{ $statusData['label'] }}</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        </div>

        {{-- Kanan: Invoice & Riwayat --}}
        <div class="col-xl-4">

            @if($pengajuan->invoices && $pengajuan->invoices->count() > 0)
                @php
                    $unpaidInvoices = $pengajuan->invoices->where('status', '!=', 'paid');
                    $totalUnpaid = $unpaidInvoices->sum('amount_total');
                    $isWaitingBilling = in_array($pengajuan->status, ['billing_1', 'billing_2', 'billing_3', 'billing_4']) && $totalUnpaid == 0;
                @endphp
                <section class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                    <div class="px-3 py-2 border-bottom">
                        <h5 class="fw-bold mb-0" style="color: #0f172a; font-size: 12px; text-transform: uppercase;">
                            <i class="fa-solid fa-file-invoice-dollar me-2" style="color: #0284c7;"></i>RINGKASAN TAGIHAN
                        </h5>
                    </div>
                    <div class="list-group list-group-flush" style="border-radius: 0 0 14px 14px;">
                        @if($totalUnpaid > 0)
                            <div class="list-group-item p-4" style="border-color: #f1f5f9; background-color: #fff;">
                                <div class="text-muted mb-3 text-center" style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Rincian Tagihan Aktif</div>
                                
                                <div class="d-flex flex-column gap-3 mb-4">
                                    @foreach($unpaidInvoices as $inv)
                                        @php
                                            $kategoriMapping = [
                                                'billing_1' => ['Permohonan'],
                                                'billing_2' => ['Audit Kecukupan'],
                                                'billing_3' => ['Audit Kesesuaian'],
                                                'billing_3_bdlt' => ['Biaya Di Luar Tarif (BDLT)'],
                                                'billing_4' => ['Jasa Sidang Komisi Teknis']
                                            ];
                                            $targetKategori = $kategoriMapping[$inv->jenis_tagihan] ?? [];
                                            $filteredItems = $pengajuan->rabItems ? $pengajuan->rabItems->filter(function($item) use ($targetKategori) {
                                                return in_array($item->kategori, $targetKategori);
                                            }) : collect();
                                            
                                            $jenisLabelMapping = [
                                                'billing_1' => 'Tagihan Permohonan',
                                                'billing_2' => 'Tagihan Audit Kecukupan',
                                                'billing_3' => 'Tagihan Audit Kesesuaian',
                                                'billing_3_bdlt' => 'Tagihan BDLT',
                                                'billing_4' => 'Tagihan Sidang Komtek',
                                            ];
                                            $jenisLabel = $jenisLabelMapping[$inv->jenis_tagihan] ?? 'Tagihan Sertifikasi';
                                        @endphp
                                        <div class="d-flex justify-content-between align-items-center" style="font-size: 12px; border-bottom: 1px dashed #e2e8f0; padding-bottom: 10px;">
                                            <div>
                                                <div class="fw-bold text-dark text-uppercase" style="margin-bottom: 2px; font-size: 12.5px;">{{ $jenisLabel }}</div>
                                                <div class="text-muted mb-1" style="font-size: 10px;"><i class="fa-regular fa-clock me-1"></i>Jatuh Tempo: {{ \Carbon\Carbon::parse($inv->due_date)->translatedFormat('d M Y') }}</div>
                                                @if($filteredItems->count() > 0)
                                                    <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none" style="font-size: 10.5px; font-weight: 600;" data-bs-toggle="modal" data-bs-target="#modalRincian{{ $inv->id }}">
                                                        <i class="fa-solid fa-list-check me-1"></i> Lihat Rincian RAB
                                                    </button>
                                                @endif
                                            </div>
                                            <div class="fw-bold" style="color: #0f172a;">Rp {{ number_format($inv->amount_total, 0, ',', '.') }}</div>
                                        </div>
                                    @endforeach
                                </div>

                                <a href="{{ route('billing.index') }}" class="text-decoration-none d-block text-center rounded p-3" style="background: #f8fafc; border: 1px solid #e2e8f0; cursor: pointer; transition: all 0.2s ease;">
                                    <div class="text-muted mb-1" style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Total Harus Dibayar</div>
                                    <div class="fw-bold text-danger mb-3" style="font-size: 24px;">
                                        Rp {{ number_format($totalUnpaid, 0, ',', '.') }}
                                    </div>
                                    <div class="btn btn-primary btn-sm w-100 shadow-sm" style="border-radius: 8px; font-weight: 600; padding: 8px;">
                                        <i class="fa-solid fa-credit-card me-1"></i> Bayar Sekarang
                                    </div>
                                </a>
                            </div>
                        @elseif($isWaitingBilling)
                            <div class="list-group-item p-4 text-center" style="border-color: #f1f5f9; background-color: #fefce8;">
                                <div class="text-muted mb-2" style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Status Tagihan</div>
                                <div class="fw-bold text-warning mb-1" style="font-size: 18px; color: #ca8a04 !important;">
                                    <i class="fa-solid fa-clock-rotate-left me-1"></i> Menunggu Diterbitkan
                                </div>
                                <div class="text-muted mb-3" style="font-size: 10px;">(Admin sedang memproses penerbitan tagihan Anda)</div>
                                <a href="{{ route('billing.index') }}" class="btn btn-outline-secondary btn-sm w-100" style="border-radius: 8px; font-weight: 600; font-size: 12px; padding: 8px;">
                                    Lihat Riwayat Billing
                                </a>
                            </div>
                        @else
                            <a href="{{ route('billing.index') }}" class="list-group-item list-group-item-action p-4 text-center" style="border-color: #f1f5f9; cursor: pointer; transition: all 0.2s ease; text-decoration: none; background-color: #f8fafc;">
                                <div class="text-muted mb-2" style="font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Status Tagihan</div>
                                <div class="fw-bold text-success mb-1" style="font-size: 18px;">
                                    <i class="fa-solid fa-circle-check me-1"></i> Seluruhnya Lunas
                                </div>
                                <div class="text-muted mb-3" style="font-size: 10px;">(Seluruh tagihan yang telah terbit sudah lunas)</div>
                                <div class="btn btn-outline-secondary btn-sm w-100" style="border-radius: 8px; font-weight: 600; font-size: 12px; padding: 8px;">
                                    Lihat Riwayat Billing
                                </div>
                            </a>
                        @endif
                    </div>
                </section>
            @endif

            {{-- Dokumen Pengajuan (Unduhan File Generate & Berkas Klien) --}}
            <section class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                <div class="px-4 py-3 border-bottom">
                    <h5 class="fw-bold mb-0" style="color: #0f172a; font-size: 15px;">
                        <i class="fa-solid fa-folder-open me-2" style="color: #0284c7;"></i>Dokumen Sertifikasi
                    </h5>
                </div>
                <div class="list-group list-group-flush" style="border-radius: 0 0 14px 14px;">
                    @if($pengajuan->file_permohonan)
                        <a href="{{ url('/unduh/permohonan/' . $pengajuan->file_permohonan) }}" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center gap-2" style="font-size: 13px; border-color: #f1f5f9;">
                            <i class="fa-solid fa-file-word" style="color: #2563eb; width: 16px;"></i> 
                            Form Pengajuan 7.2-1 (Sistem)
                        </a>
                    @endif

                    @if(!empty($pengajuan->data_form['perjanjian']))
                        <a href="{{ route('pengajuan.perjanjian.cetak', $pengajuan->id) }}" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center gap-2" style="font-size: 13px; border-color: #f1f5f9;">
                            <i class="fa-solid fa-print" style="color: #16a34a; width: 16px;"></i> 
                            Draft Perjanjian Sertifikasi
                        </a>
                    @endif

                    @if($pengajuan->file_permohonan_ttd)
                        <a href="{{ url('/unduh/' . $pengajuan->file_permohonan_ttd) }}" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center gap-2" style="font-size: 13px; border-color: #f1f5f9;">
                            <i class="fa-solid fa-file-pdf" style="color: #dc2626; width: 16px;"></i> 
                            Form Pengajuan (TTD Klien)
                        </a>
                    @endif

                    @if($pengajuan->file_ceklis_ttd)
                        <a href="{{ url('/unduh/' . $pengajuan->file_ceklis_ttd) }}" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center gap-2" style="font-size: 13px; border-color: #f1f5f9;">
                            <i class="fa-solid fa-file-pdf" style="color: #dc2626; width: 16px;"></i> 
                            Form Perjanjian (TTD Klien)
                        </a>
                    @endif

                    @if($pengajuan->file_lhp)
                        <a href="{{ url('/unduh/' . $pengajuan->file_lhp) }}" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center gap-2" style="font-size: 13px; border-color: #f1f5f9;">
                            <i class="fa-solid fa-file-pdf" style="color: #0891b2; width: 16px;"></i> 
                            Laporan Hasil Uji (LHP)
                        </a>
                    @endif

                    @if($normalizedStatus === 'selesai')
                        <a href="{{ route('sertifikat.cetak', $pengajuan->id) }}" target="_blank" class="list-group-item list-group-item-action d-flex align-items-center gap-2 text-success fw-bold" style="font-size: 13px; border-color: #f1f5f9;">
                            <i class="fa-solid fa-award" style="color: #16a34a; width: 16px;"></i> 
                            Sertifikat Sertifikat Kesesuaian SNI
                        </a>
                    @endif

                    @if(
                        !$pengajuan->file_permohonan && 
                        empty($pengajuan->data_form['perjanjian']) && 
                        !$pengajuan->file_permohonan_ttd && 
                        !$pengajuan->file_ceklis_ttd && 
                        !$pengajuan->file_lhp && 
                        $normalizedStatus !== 'selesai'
                    )
                        <div class="list-group-item text-muted" style="font-size: 12px; background: #fafafa; text-align: center;">
                            Belum ada dokumen yang digenerate.
                        </div>
                    @endif
                </div>
            </section>

            {{-- Riwayat Status --}}
            <section class="card border-0 shadow-sm" style="border-radius: 14px;">
                <div class="px-4 py-3 border-bottom">
                    <h5 class="fw-bold mb-0" style="color: #0f172a; font-size: 15px;">
                        <i class="fa-solid fa-clock-rotate-left me-2" style="color: #0284c7;"></i>Riwayat Permohonan
                    </h5>
                </div>
                <div class="p-4" style="max-height: 400px; overflow-y: auto; scrollbar-width: thin;">
                    @forelse($pengajuan->statusHistories as $history)
                        <div class="d-flex gap-3 {{ !$loop->last ? 'pb-4' : '' }}" style="position: relative;">
                            <div style="position: relative; flex-shrink: 0;">
                                <span style="display: block; width: 12px; height: 12px; border-radius: 50%; background: {{ $loop->first ? '#0284c7' : '#d1fae5' }}; border: 2px solid {{ $loop->first ? '#0369a1' : '#16a34a' }}; margin-top: 3px;"></span>
                                @if(!$loop->last)
                                    <span style="position: absolute; top: 17px; bottom: -18px; left: 5px; width: 2px; background: #e2e8f0;"></span>
                                @endif
                            </div>
                            <div>
                                <div class="fw-semibold" style="color: #1e293b; font-size: 13px;">
                                    {{ \App\Support\LsproType5Workflow::statusLabel($history->to_status) }}
                                </div>
                                <div class="text-muted mt-1" style="font-size: 11px;">
                                    {{ $history->created_at->translatedFormat('d M Y, H:i') }}
                                    @if($history->actor)
                                        &middot; <strong>{{ $history->actor->nama_penghubung ?? $history->actor->name }}</strong>
                                    @else
                                        &middot; <em>Sistem</em>
                                    @endif
                                </div>
                                @if($history->notes)
                                    <p class="mb-0 mt-1" style="font-size: 12px; color: #64748b; line-height: 1.5;">{{ $history->notes }}</p>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0" style="font-size: 13px;">Riwayat akan tercatat setelah ada perubahan status.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</div>

{{-- MODAL UPLOAD TTD PERJANJIAN & PENGAJUAN --}}
<div class="modal fade" id="uploadTtdModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" style="color: #0f172a;">Upload Dokumen Bertanda Tangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('pengajuan.upload_permohonan', $pengajuan) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body py-4">
                    <p class="text-muted" style="font-size: 13px;">
                        Unggah hasil cetak dokumen Form Pengajuan dan Perjanjian Sertifikasi yang telah <b>ditandatangani di atas materai</b> (maks. 5MB per file).
                    </p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 13px; color: #334155;">File PDF Form Pengajuan (Form 7.2-1)</label>
                        <input type="file" name="file_permohonan_ttd" class="form-control" accept=".pdf" required style="border-radius: 8px; font-size: 13px;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold" style="font-size: 13px; color: #334155;">File PDF Perjanjian Sertifikasi</label>
                        <input type="file" name="file_ceklis_ttd" class="form-control" accept=".pdf" required style="border-radius: 8px; font-size: 13px;">
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light fw-semibold" data-bs-dismiss="modal" style="border-radius: 8px; font-size: 13px;">Batal</button>
                    <button type="submit" class="btn btn-success fw-semibold" style="border-radius: 8px; font-size: 13px;">
                        <i class="fa-solid fa-paper-plane me-1"></i> Upload Dokumen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* =============================
   STEPPER DETAIL HORIZONTAL
============================= */
.detail-stepper-wrap {
    overflow-x: auto;
    padding-bottom: 8px;
}

.detail-stepper {
    display: flex;
    align-items: flex-start;
    min-width: max-content;
    padding: 4px 0;
}

.detail-step-item {
    display: flex;
    align-items: flex-start;
}

.detail-connector {
    width: 40px;
    height: 2px;
    background: #e2e8f0;
    margin-top: 18px;
    flex-shrink: 0;
    transition: background 0.3s;
}

.detail-connector.done {
    background: #16a34a;
}

.detail-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    padding: 4px 8px;
    min-width: 80px;
    color: #94a3b8;
}

.detail-step-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #e2e8f0;
    color: #94a3b8;
    font-size: 12px;
    font-weight: 700;
    border: 2px solid transparent;
    transition: all 0.25s ease;
}

.detail-step-info {
    text-align: center;
}

.detail-step-label {
    font-size: 10px;
    font-weight: 600;
    line-height: 1.3;
    white-space: nowrap;
}

.detail-step-pic {
    font-size: 9px;
    color: #94a3b8;
    white-space: nowrap;
    margin-top: 2px;
}

/* DONE */
.detail-step.done .detail-step-circle {
    background: #16a34a;
    color: white;
    border-color: #15803d;
}
.detail-step.done {
    color: #15803d;
}

/* CURRENT */
.detail-step.current .detail-step-circle {
    background: #0284c7;
    color: white;
    border-color: #0369a1;
    box-shadow: 0 0 0 5px rgba(2, 132, 199, 0.15);
}
.detail-step.current {
    color: #0369a1;
}
.detail-step.current .detail-step-label {
    font-weight: 700;
}

/* REJECTED */
.detail-step.rejected .detail-step-circle {
    background: #dc2626;
    color: white;
    border-color: #b91c1c;
}
.detail-step.rejected {
    color: #dc2626;
}
</style>

@foreach($pengajuan->invoices as $inv)
    @php
        $kategoriMapping = [
            'billing_1' => ['Permohonan'],
            'billing_2' => ['Audit Kecukupan'],
            'billing_3' => ['Audit Kesesuaian'],
            'billing_3_bdlt' => ['Biaya Di Luar Tarif (BDLT)'],
            'billing_4' => ['Jasa Sidang Komisi Teknis']
        ];
        $targetKategori = $kategoriMapping[$inv->jenis_tagihan] ?? [];
        $filteredItems = $pengajuan->rabItems ? $pengajuan->rabItems->filter(function($item) use ($targetKategori) {
            return in_array($item->kategori, $targetKategori);
        }) : collect();
    @endphp
    @if($filteredItems->count() > 0)
    <div class="modal fade" id="modalRincian{{ $inv->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 16px; border: none;">
                <div class="modal-header border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold" style="color: #1e293b;">Rincian Tagihan {{ $inv->invoice_number }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered mb-0" style="font-size: 13px;">
                            <thead class="bg-light">
                                <tr>
                                    <th>Komponen</th>
                                    <th class="text-center">Hari/Qty</th>
                                    <th class="text-end">Jumlah (Rp)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $grandTotal = 0; @endphp
                                @foreach($filteredItems as $item)
                                    @php
                                        $subtotal = $item->tarif_pnbp_total ?? 0;
                                        $grandTotal += $subtotal;
                                    @endphp
                                    <tr>
                                        <td class="ps-3">{{ $item->komponen }}</td>
                                        <td class="text-center">{{ $item->hari ?? 1 }}</td>
                                        <td class="text-end">{{ $item->tarif_pnbp_total ? 'Rp ' . number_format($item->tarif_pnbp_total, 0, ',', '.') : '-' }}</td>
                                    </tr>
                                @endforeach
                                <tr class="fw-bold bg-light">
                                    <td colspan="2" class="text-end">TOTAL TAGIHAN</td>
                                    <td class="text-end text-danger fs-5">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; font-weight: 600;">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach

@endsection
