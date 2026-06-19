@php
    $stageNumber = $pengajuan->workflowStage();
    $stageMeta = $pengajuan->workflowStageMeta();
    $isCorrection = \App\Support\LsproType5Workflow::isCorrection($pengajuan->status);
    $isRejected = \App\Support\LsproType5Workflow::isRejected($pengajuan->status);
    $accent = ($isCorrection || $isRejected) ? '#dc2626' : ($stageNumber === 8 ? '#15803d' : '#0369a1');
    $background = ($isCorrection || $isRejected) ? '#fef2f2' : ($stageNumber === 8 ? '#ecfdf5' : '#f0f9ff');
@endphp

<div style="min-width: 150px;">
    <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
        <span class="fw-bold" style="color: {{ $accent }}; font-size: 11px;">
            Tahap {{ str_pad($stageNumber, 2, '0', STR_PAD_LEFT) }}
        </span>
        <span class="text-muted" style="font-size: 10px;">{{ $pengajuan->workflowProgress() }}%</span>
    </div>
    <div class="progress mb-1" style="height: 5px; background: #e2e8f0;">
        <div
            class="progress-bar"
            role="progressbar"
            style="width: {{ $pengajuan->workflowProgress() }}%; background: {{ $accent }};"
            aria-valuenow="{{ $pengajuan->workflowProgress() }}"
            aria-valuemin="0"
            aria-valuemax="100"
        ></div>
    </div>
    <span
        class="badge text-wrap text-start"
        style="background: {{ $background }}; color: {{ $accent }}; border: 1px solid {{ $accent }}33; font-size: 10px; line-height: 1.35;"
    >
        {{ $pengajuan->workflowStatusLabel() }}
    </span>
</div>
