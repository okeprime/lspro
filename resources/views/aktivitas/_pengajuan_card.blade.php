@php
    $dataForm = is_array($item->data_form)
        ? (object) $item->data_form
        : (is_string($item->data_form) ? (json_decode($item->data_form) ?: (object) []) : (object) []);
    $normalizedStatus = \App\Support\LsproType5Workflow::normalize($item->status);
    $currentStage = $item->workflowStage();
    $steps = \App\Support\LsproType5Workflow::STAGES;
    $isRejected = \App\Support\LsproType5Workflow::isRejected($item->status);
    $isCorrection = \App\Support\LsproType5Workflow::isCorrection($item->status);
    $isDone = $normalizedStatus === 'selesai';
@endphp

<article class="card border-0 shadow-sm mb-4" style="border-radius: 18px; overflow: hidden;">
    {{-- Header strip --}}
    <div style="height: 4px; background: {{ $isRejected ? 'linear-gradient(90deg, #ef4444, #dc2626)' : ($isDone ? 'linear-gradient(90deg, #16a34a, #15803d)' : ($isCorrection ? 'linear-gradient(90deg, #f59e0b, #d97706)' : 'linear-gradient(90deg, #0284c7, #0369a1)')) }};"></div>

    <div class="card-body p-4">
        {{-- Info Header --}}
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <span class="badge bg-light text-dark border" style="font-family: monospace; font-size: 11px;">#{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}</span>
                    <span class="badge" style="background: #e0f2fe; color: #0369a1; font-size: 11px;">{{ ucfirst($item->jenis_pengajuan ?? 'sertifikasi') }}</span>
                    @if($item->jenis_sertifikasi)
                        <span class="badge" style="background: #f3f4f6; color: #374151; font-size: 11px;">{{ $item->jenis_sertifikasi }}</span>
                    @endif
                    @if($isCorrection)
                        <span class="badge" style="background: #fef3c7; color: #92400e; font-size: 11px;">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i> Perlu Perbaikan
                        </span>
                    @elseif($isRejected)
                        <span class="badge" style="background: #fee2e2; color: #991b1b; font-size: 11px;">
                            <i class="fa-solid fa-xmark me-1"></i> Ditolak
                        </span>
                    @elseif($isDone)
                        <span class="badge" style="background: #dcfce7; color: #166534; font-size: 11px;">
                            <i class="fa-solid fa-circle-check me-1"></i> Selesai
                        </span>
                    @endif
                </div>
                <h5 class="fw-bold mb-1" style="color: #0f172a;">
                    {{ $dataForm->merek_produk ?? $dataForm->merek ?? 'Tanpa Merek' }}
                </h5>
                <p class="text-muted mb-0" style="font-size: 13px;">
                    {{ $dataForm->nama_produk ?? $dataForm->nama_pupuk ?? 'Pupuk' }}
                    @if(!empty($dataForm->no_sni ?? $dataForm->sni_acuan ?? null))
                        &middot; SNI {{ $dataForm->no_sni ?? $dataForm->sni_acuan }}
                    @endif
                    &middot; {{ $item->created_at?->format('d M Y') }}
                    @if(isset($isInternal) && $isInternal && $item->user)
                        &middot; <span class="badge bg-secondary" style="font-size: 10px;">{{ $item->user->nama_penghubung ?? $item->user->name }}</span>
                    @endif
                </p>
            </div>

            <div style="min-width: 200px;">
                @include('components.workflow-status', ['pengajuan' => $item])
            </div>
        </div>

        {{-- STEPPER --}}
        <div class="lspro-stepper-wrap mb-4">
            <div class="lspro-stepper">
                @foreach($steps as $number => $step)
                    @php
                        $stepDone = ($number < $currentStage) || $isDone;
                        $stepCurrent = ($number === $currentStage) && !$isDone;
                        $stepRejected = $isRejected && ($number === $currentStage);
                    @endphp
                    <div class="lspro-step-item">
                        @if(!$loop->first)
                            <div class="lspro-connector {{ $stepDone || $isDone ? 'done' : '' }}"></div>
                        @endif
                        <a href="{{ route('aktivitas.show', $item) }}"
                           class="lspro-step {{ $stepDone ? 'done' : '' }} {{ $stepCurrent ? 'current' : '' }} {{ $stepRejected ? 'rejected' : '' }}"
                           title="{{ $step['title'] }}">
                            <div class="lspro-step-circle">
                                @if($isDone || $stepDone)
                                    <i class="fa-solid fa-check" style="font-size: 10px;"></i>
                                @elseif($stepRejected)
                                    <i class="fa-solid fa-xmark" style="font-size: 10px;"></i>
                                @else
                                    {{ $number }}
                                @endif
                            </div>
                            <span class="lspro-step-label">{{ $step['short_title'] }}</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Catatan --}}
        @if($isCorrection && !empty($item->catatan))
            <div class="alert mb-4" style="border-radius: 12px; background: #fffbeb; border: 1px solid #fcd34d; color: #78350f;">
                <div class="d-flex align-items-start gap-3">
                    <i class="fa-solid fa-clipboard-list" style="font-size: 20px; color: #d97706; margin-top: 2px; flex-shrink: 0;"></i>
                    <div>
                        <div class="fw-bold mb-1" style="font-size: 13px;">Catatan TU – Perbaikan Diperlukan</div>
                        <p class="mb-0" style="font-size: 13px; line-height: 1.6;">{{ $item->catatan }}</p>
                    </div>
                </div>
            </div>
        @elseif(!empty($item->catatan))
            <div class="alert mb-4" style="border-radius: 12px; background: #f0f9ff; border: 1px solid #bae6fd; color: #0c4a6e; font-size: 13px;">
                <i class="fa-solid fa-circle-info me-2"></i> <strong>Catatan:</strong> {{ $item->catatan }}
            </div>
        @endif

        {{-- Action Buttons --}}
        <div class="d-flex flex-wrap align-items-center gap-2">
            @if($item->file_permohonan && in_array($normalizedStatus, ['menunggu_ttd', 'billing', 'proses_evaluasi', 'proses_audit', 'keputusan', 'selesai']))
                <a href="{{ route('pengajuan.download', $item->id) }}"
                   class="btn btn-sm btn-outline-primary fw-semibold" style="border-radius: 8px; font-size: 13px;">
                    <i class="fa-solid fa-file-arrow-down me-1"></i> Unduh Form 7.2-1
                </a>
            @endif

            @if(in_array($normalizedStatus, ['menunggu_ttd', 'billing', 'proses_evaluasi', 'proses_audit', 'keputusan', 'selesai']))
                <a href="{{ route('pengajuan.download724', $item->id) }}"
                   class="btn btn-sm btn-outline-danger fw-semibold" style="border-radius: 8px; font-size: 13px;">
                    <i class="fa-solid fa-file-arrow-down me-1"></i> Unduh Form 7.2-4
                </a>
            @endif

            @if(in_array($normalizedStatus, ['menunggu_ttd'], true))
                <form action="{{ route('pengajuan.upload_permohonan', $item->id) }}"
                      method="POST"
                      enctype="multipart/form-data"
                      class="d-flex flex-column gap-2 mt-2 p-3 bg-light rounded border w-100">
                    @csrf
                    <div class="fw-bold" style="font-size: 13px;">Upload Dokumen Bertanda Tangan & Meterai</div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small mb-1 text-muted">Upload TTD Form 7.2-1 (PDF)</label>
                            <input type="file" name="file_permohonan_ttd" class="form-control form-control-sm" accept=".pdf" required style="border-radius: 8px; font-size: 12px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small mb-1 text-muted">Upload TTD Form 7.2-4 (PDF)</label>
                            <input type="file" name="file_ceklis_ttd" class="form-control form-control-sm" accept=".pdf" required style="border-radius: 8px; font-size: 12px;">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sm fw-semibold w-100 mt-1" style="border-radius: 8px; font-size: 13px; background: {{ $isCorrection ? '#f59e0b' : '#16a34a' }}; color: white; border: none;">
                        <i class="fa-solid fa-upload me-1"></i> Upload & Lanjutkan
                    </button>
                </form>
            @endif

            @if(in_array($normalizedStatus, ['menunggu_lampiran'], true))
                <a href="{{ route('pengajuan.lampiran', $item->id) }}"
                   class="btn btn-sm btn-outline-success fw-semibold" style="border-radius: 8px; font-size: 13px;">
                    <i class="fa-solid fa-paperclip me-1"></i> Upload Lampiran Pendukung
                </a>
            @endif

            @if(in_array($normalizedStatus, ['draft', 'perbaikan'], true) || $item->is_draft)
                <a href="{{ route('pengajuan.create', ['draft_id' => $item->id]) }}"
                   class="btn btn-sm fw-semibold" style="background: #e2e8f0; color: #475569; border-radius: 8px; font-size: 13px;">
                    <i class="fa-solid fa-pen-to-square me-1"></i> {{ $normalizedStatus === 'perbaikan' ? 'Revisi Form Pengajuan' : 'Lanjutkan Draft' }}
                </a>
            @endif

            <a href="{{ route('aktivitas.show', $item) }}"
               class="btn btn-sm btn-light border fw-semibold ms-auto" style="border-radius: 8px; font-size: 13px;">
                <i class="fa-solid fa-timeline me-1"></i> Lihat Detail Alur
            </a>
        </div>
    </div>
</article>
