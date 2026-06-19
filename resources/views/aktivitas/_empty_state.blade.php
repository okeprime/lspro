<div class="card border-0" style="border-radius: 16px; border: 1.5px dashed #e2e8f0 !important;">
    <div class="card-body text-center py-5">
        <div style="width: 64px; height: 64px; border-radius: 50%; background: #f1f5f9; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
            <i class="fa-solid {{ $icon }}" style="font-size: 28px; color: #cbd5e1;"></i>
        </div>
        <p class="fw-semibold mb-1" style="color: #475569; font-size: 15px;">{{ $title }}</p>
        <p class="text-muted mb-4" style="font-size: 13px;">{{ $desc }}</p>
        @if(!$isInternal)
            <a href="{{ $link }}" class="btn btn-success px-4 fw-semibold" style="border-radius: 10px; font-size: 14px;">
                <i class="fa-solid fa-plus me-1"></i> {{ $linkLabel }}
            </a>
        @endif
    </div>
</div>
