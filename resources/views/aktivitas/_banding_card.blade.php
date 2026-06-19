@php
    $pb = $b->pengajuan;
    $pdf = $pb ? (is_array($pb->data_form) ? $pb->data_form : (json_decode($pb->data_form, true) ?? [])) : [];
    $badgeColor = $b->jenis === 'banding' ? '#dc2626' : ($b->jenis === 'keluhan' ? '#d97706' : '#6b7280');
    $badgeBg = $b->jenis === 'banding' ? '#fee2e2' : ($b->jenis === 'keluhan' ? '#fef3c7' : '#f3f4f6');
@endphp

<div class="card border-0 shadow-sm mb-3" style="border-radius: 16px; overflow: hidden;">
    <div style="height: 4px; background: {{ $b->jenis === 'banding' ? 'linear-gradient(90deg, #dc2626, #b91c1c)' : ($b->jenis === 'keluhan' ? 'linear-gradient(90deg, #f59e0b, #d97706)' : 'linear-gradient(90deg, #6b7280, #4b5563)') }};"></div>
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div style="flex: 1; min-width: 0;">
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <span class="badge text-uppercase" style="background: {{ $badgeBg }}; color: {{ $badgeColor }}; font-size: 11px;">
                        {{ $b->jenis }}
                    </span>
                    @if($b->status === 'terkirim')
                        <span class="badge" style="background: #f1f5f9; color: #475569; font-size: 11px;">Terkirim</span>
                    @elseif($b->status === 'diproses')
                        <span class="badge" style="background: #dbeafe; color: #1d4ed8; font-size: 11px;">Diproses</span>
                    @elseif($b->status === 'selesai')
                        <span class="badge" style="background: #dcfce7; color: #166534; font-size: 11px;">Selesai</span>
                    @endif
                    <span class="text-muted" style="font-size: 12px;">{{ $b->created_at->translatedFormat('d M Y') }}</span>
                </div>

                <p class="mb-2" style="font-size: 14px; color: #1e293b; line-height: 1.6;">{{ $b->pesan }}</p>

                @if($pb)
                    <div style="font-size: 13px; color: #64748b;">
                        Terkait pengajuan:
                        <a href="{{ route('aktivitas.show', $pb) }}" class="fw-semibold text-decoration-none" style="color: #0f766e;">
                            #{{ str_pad($pb->id, 5, '0', STR_PAD_LEFT) }} – {{ $pdf['merek'] ?? 'Tanpa Merek' }}
                        </a>
                    </div>
                @endif

                @if($b->catatan_admin)
                    <div class="mt-2 p-2 rounded" style="background: #f8fafc; border: 1px solid #e2e8f0; font-size: 13px; color: #374151;">
                        <i class="fa-solid fa-reply me-1 text-secondary"></i>
                        <strong>Tanggapan Admin:</strong> {{ $b->catatan_admin }}
                    </div>
                @endif
            </div>

            <div class="d-flex flex-column align-items-end gap-2" style="flex-shrink: 0;">
                @if($b->file_lampiran)
                    <a href="{{ asset('storage/banding_lampiran/' . $b->file_lampiran) }}" target="_blank"
                       class="btn btn-sm btn-outline-secondary" style="border-radius: 8px; font-size: 12px;">
                        <i class="fa-solid fa-file-arrow-down me-1"></i> Lampiran
                    </a>
                @endif
                @if($b->status !== 'selesai')
                    <span style="font-size: 12px; color: #94a3b8;">Menunggu tanggapan...</span>
                @endif
            </div>
        </div>
    </div>
</div>
