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
    <div class="d-flex flex-wrap align-items-start justify-content-between gap-3 mb-4">
        <div>
            <a href="{{ route('aktivitas.index') }}" class="text-decoration-none small" style="color: #64748b;">
                <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Aktivitas
            </a>
            <h2 class="fw-bold mt-2 mb-1" style="color: #0f172a; font-size: 24px;">
                Permohonan #{{ str_pad($pengajuan->id, 5, '0', STR_PAD_LEFT) }}
            </h2>
            <p class="text-muted mb-0" style="font-size: 13px;">
                {{ ucfirst($pengajuan->jenis_pengajuan) }}
                &middot; Dibuat {{ $pengajuan->created_at->translatedFormat('d F Y') }}
                @if($pengajuan->user)
                    &middot; <strong>{{ $pengajuan->user->name }}</strong>
                @endif
            </p>
        </div>
        <div style="min-width: 220px;">
            @include('components.workflow-status', ['pengajuan' => $pengajuan])
        </div>
    </div>

    {{-- STEPPER HORIZONTAL INTERAKTIF --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; overflow: hidden;">
        <div style="height: 3px; background: linear-gradient(90deg, {{ $accentColor }}, {{ $isDone ? '#15803d' : $accentColor }}44);"></div>
        <div class="p-4">
            <h6 class="fw-bold mb-3" style="color: #475569; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;">
                Progress Sertifikasi Tipe 5
            </h6>
            <div class="detail-stepper-wrap">
                <div class="detail-stepper">
                    @foreach($workflowStages as $number => $stage)
                        @php
                            $stepDone = ($number < $currentStage) || $isDone;
                            $stepCurrent = ($number === $currentStage) && !$isDone;
                            $stepRejected = $isRejected && ($number === $currentStage);
                        @endphp
                        <div class="detail-step-item">
                            @if(!$loop->first)
                                <div class="detail-connector {{ $stepDone ? 'done' : '' }}"></div>
                            @endif
                            <div class="detail-step {{ $stepDone ? 'done' : '' }} {{ $stepCurrent ? 'current' : '' }} {{ $stepRejected ? 'rejected' : '' }}">
                                <div class="detail-step-circle">
                                    @if($isDone || $stepDone)
                                        <i class="fa-solid fa-check" style="font-size: 11px;"></i>
                                    @elseif($stepRejected)
                                        <i class="fa-solid fa-xmark" style="font-size: 11px;"></i>
                                    @else
                                        {{ $number }}
                                    @endif
                                </div>
                                <div class="detail-step-info">
                                    <div class="detail-step-label">{{ $stage['short_title'] }}</div>
                                    <div class="detail-step-pic">{{ $stage['pic'] }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
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
                            <div>
                                <div class="fw-bold mb-1" style="color: #92400e;">Catatan Tata Usaha – Perbaikan Diperlukan</div>
                                <p class="mb-3" style="color: #78350f; font-size: 13px; line-height: 1.6;">{{ $pengajuan->catatan }}</p>
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
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Upload Permohonan (Menunggu TTD) --}}
            @if($normalizedStatus === 'menunggu_ttd')
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                    <div class="card-body p-4" style="background: #f0f9ff; border-radius: 14px;">
                        <div class="d-flex align-items-start gap-3">
                            <i class="fa-solid fa-signature" style="font-size: 22px; color: #0284c7; margin-top: 2px; flex-shrink: 0;"></i>
                            <div class="flex-grow-1">
                                <div class="fw-bold mb-1" style="color: #0c4a6e;">Langkah Berikutnya: Upload Dokumen Bertanda Tangan</div>
                                <p class="mb-3" style="color: #075985; font-size: 13px;">
                                    Unduh Form 7.2-1 di bawah, tanda tangani dan berikan materai, lalu upload kembali sebagai PDF.
                                </p>
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    @if($pengajuan->file_permohonan)
                                        <a href="{{ route('pengajuan.download', $pengajuan->id) }}"
                                           class="btn fw-semibold"
                                           style="background: white; border: 1.5px solid #0284c7; color: #0284c7; border-radius: 8px; font-size: 13px;">
                                            <i class="fa-solid fa-file-arrow-down me-1"></i> Unduh Form 7.2-1
                                        </a>
                                    @endif
                                </div>
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
                                           style="max-width: 280px; border-radius: 8px; font-size: 13px;">
                                    <button type="submit" class="btn btn-success fw-semibold px-4" style="border-radius: 8px; font-size: 13px;">
                                        <i class="fa-solid fa-paper-plane me-1"></i> Kirim ke Tata Usaha
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Upload Lampiran (Perjanjian) --}}
            @if($normalizedStatus === 'perjanjian')
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                    <div class="card-body p-4" style="background: #f0fdf4; border-radius: 14px;">
                        <div class="d-flex align-items-start gap-3">
                            <i class="fa-solid fa-file-contract" style="font-size: 22px; color: #16a34a; margin-top: 2px; flex-shrink: 0;"></i>
                            <div>
                                <div class="fw-bold mb-1" style="color: #14532d;">Perjanjian Sertifikasi Siap</div>
                                <p class="mb-3" style="color: #166534; font-size: 13px;">
                                    Verifikasi TU telah selesai. Silakan upload lampiran pendukung untuk melanjutkan ke tahap berikutnya.
                                </p>
                                <a href="{{ route('pengajuan.lampiran', $pengajuan->id) }}"
                                   class="btn btn-success fw-semibold px-4"
                                   style="border-radius: 8px; font-size: 13px;">
                                    <i class="fa-solid fa-paperclip me-1"></i> Upload Lampiran Pendukung
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Selesai / SPPT SNI --}}
            @if($isDone)
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 14px;">
                    <div class="card-body p-4" style="background: linear-gradient(135deg, #f0fdf4, #dcfce7); border-radius: 14px; text-align: center;">
                        <i class="fa-solid fa-award" style="font-size: 48px; color: #16a34a; margin-bottom: 12px;"></i>
                        <h5 class="fw-bold" style="color: #14532d;">SPPT SNI Telah Diterbitkan</h5>
                        <p class="text-muted mb-0" style="font-size: 13px;">Selamat! Proses sertifikasi produk Anda telah selesai.</p>
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
                    <p class="text-muted mb-0" style="font-size: 12px;">Dokumen dan PIC di setiap tahapan alur sertifikasi.</p>
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
                                            <p class="mb-1 text-secondary" style="font-size: 11px;">
                                                <strong>PIC:</strong> {{ $stage['pic'] }}
                                            </p>
                                            <p class="mb-0 text-muted" style="font-size: 11px; line-height: 1.5;">
                                                {{ implode(' · ', $stage['documents']) }}
                                            </p>
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

            @if($latestInvoice)
                <section class="card border-0 shadow-sm mb-4" style="border-radius: 14px; overflow: hidden;">
                    <div style="height: 3px; background: linear-gradient(90deg, #0284c7, #0ea5e9);"></div>
                    <div class="card-body p-4">
                        <div class="text-muted fw-semibold mb-2" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.5px;">
                            <i class="fa-solid fa-file-invoice-dollar me-1"></i> Invoice Sertifikasi
                        </div>
                        <div class="fw-bold" style="color: #0f172a; font-size: 13px;">{{ $latestInvoice->invoice_number }}</div>
                        <div class="mt-2 mb-2" style="font-size: 22px; font-weight: 800; color: #166534;">
                            Rp {{ number_format($latestInvoice->amount_total, 0, ',', '.') }}
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge {{ $latestInvoice->status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}" style="font-size: 11px;">
                                {{ $latestInvoice->status === 'paid' ? 'Lunas' : 'Belum Lunas' }}
                            </span>
                            <span class="text-muted" style="font-size: 11px;">
                                Jatuh tempo: {{ \Carbon\Carbon::parse($latestInvoice->due_date)->translatedFormat('d M Y') }}
                            </span>
                        </div>
                    </div>
                </section>
            @endif

            {{-- Riwayat Status --}}
            <section class="card border-0 shadow-sm" style="border-radius: 14px;">
                <div class="px-4 py-3 border-bottom">
                    <h5 class="fw-bold mb-0" style="color: #0f172a; font-size: 15px;">
                        <i class="fa-solid fa-clock-rotate-left me-2" style="color: #0284c7;"></i>Riwayat Permohonan
                    </h5>
                </div>
                <div class="p-4">
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
@endsection
