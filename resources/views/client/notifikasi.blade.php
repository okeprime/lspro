@extends('layouts.app')

@section('title', 'Notifikasi')

@section('extra-css')
<style>
    .filter-btn {
        transition: all 0.2s ease;
        color: #4b5563;
        border-color: #e5e7eb !important;
    }
    .filter-btn:hover {
        background-color: #f3f4f6 !important;
        border-color: #d1d5db !important;
    }
    .filter-btn.active {
        background-color: #16a34a !important;
        color: white !important;
        border-color: #16a34a !important;
    }
    .filter-btn.active .badge {
        background-color: #ef4444 !important;
    }
    
    .notif-card {
        border-radius: 12px;
        transition: all 0.2s ease;
    }
    .notif-card.unread-perbaikan {
        background-color: #fef2f2;
        border: 1px solid #fee2e2 !important;
    }
    .notif-card.unread-perbaikan .notif-icon { color: #ef4444; }
    
    .notif-card.unread-pembayaran {
        background-color: #eff6ff;
        border: 1px solid #dbeafe !important;
    }
    .notif-card.unread-pembayaran .notif-icon { color: #3b82f6; }

    .notif-card.unread-default {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0 !important;
    }
    .notif-card.unread-default .notif-icon { color: #64748b; }
    
    .notif-card.read {
        background-color: #ffffff;
        border: 1px solid #f3f4f6 !important;
    }
    .notif-card.read .notif-icon { color: #9ca3af; }
    
    .notif-badge {
        font-size: 12px;
        font-weight: 500;
        background: #f3f4f6;
        color: #6b7280;
        padding: 2px 8px;
        border-radius: 6px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4" style="max-width: 1000px; margin: 0 auto;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="fw-bold mb-1" style="color: #111827; font-size: 24px;">Notifikasi</h2>
            @if($unreadCount > 0)
                <p class="text-success mb-0 fw-medium" style="font-size: 14px;"><i class="fa-solid fa-circle-info"></i> {{ $unreadCount }} notifikasi baru (otomatis ditandai dibaca)</p>
            @else
                <p class="text-muted mb-0" style="font-size: 14px;">Semua notifikasi telah dibaca</p>
            @endif
        </div>
    </div>

    <div class="d-flex gap-2 mb-4 overflow-auto pb-2" id="notif-filters">
        <button class="btn rounded-pill px-4 py-1 fw-semibold border filter-btn active" data-filter="all">
            Semua @if($unreadCount > 0)<span class="badge rounded-circle ms-1" style="background: #ef4444;">{{ $unreadCount }}</span>@endif
        </button>
        <button class="btn rounded-pill px-4 py-1 fw-semibold border bg-white filter-btn" data-filter="perbaikan">Perbaikan</button>
        <button class="btn rounded-pill px-4 py-1 fw-semibold border bg-white filter-btn" data-filter="pembayaran">Pembayaran</button>
        <button class="btn rounded-pill px-4 py-1 fw-semibold border bg-white filter-btn" data-filter="audit">Audit</button>
        <button class="btn rounded-pill px-4 py-1 fw-semibold border bg-white filter-btn" data-filter="sertifikat">Sertifikat</button>
        <button class="btn rounded-pill px-4 py-1 fw-semibold border bg-white filter-btn" data-filter="pengajuan">Pengajuan</button>
    </div>

    <div id="notif-list">
        @forelse($notifications as $notif)
            @php
                $type = strtolower($notif->type ?? 'pengajuan');
                // Maps icons and base types based on title/message heuristics if type is generic
                if(str_contains(strtolower($notif->title), 'perbaikan')) $type = 'perbaikan';
                elseif(str_contains(strtolower($notif->title), 'invoice') || str_contains(strtolower($notif->title), 'bayar')) $type = 'pembayaran';
                elseif(str_contains(strtolower($notif->title), 'audit')) $type = 'audit';
                elseif(str_contains(strtolower($notif->title), 'sertifikat') || str_contains(strtolower($notif->title), 'spk')) $type = 'sertifikat';

                $icon = 'fa-file-lines';
                if($type === 'perbaikan') $icon = 'fa-circle-exclamation';
                if($type === 'pembayaran') $icon = 'fa-credit-card';
                if($type === 'audit') $icon = 'fa-calendar';
                if($type === 'sertifikat') $icon = 'fa-award';

                $cardClass = 'read';
                if(!$notif->is_read) {
                    if($type === 'perbaikan') $cardClass = 'unread-perbaikan';
                    elseif($type === 'pembayaran') $cardClass = 'unread-pembayaran';
                    else $cardClass = 'unread-default'; // default unread tint
                }
            @endphp
            <div class="card mb-3 notif-card {{ $cardClass }} shadow-sm notif-item" data-type="{{ $type }}">
                <div class="card-body p-4 d-flex gap-4 align-items-start">
                    <div class="notif-icon fs-4 pt-1">
                        <i class="fa-solid {{ $icon }}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="fw-bold mb-0 d-inline-block text-dark">{{ $notif->title }}</h6>
                                @if(!$notif->is_read)
                                    <span class="text-danger ms-1 align-top" style="font-size: 8px;"><i class="fa-solid fa-circle"></i></span>
                                @endif
                                <span class="notif-badge ms-2">{{ ucfirst($type) }}</span>
                            </div>
                            <div class="d-flex gap-3 align-items-center">
                                <!-- Tombol check dihilangkan karena otomatis dibaca saat dibuka -->
                                <form action="{{ route('notifikasi.destroy', $notif->id) }}" method="POST" class="m-0" onsubmit="return confirm('Hapus notifikasi ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link text-muted p-0" title="Hapus"><i class="fa-regular fa-trash-can"></i></button>
                                </form>
                            </div>
                        </div>
                        <p class="text-muted mb-2" style="font-size: 14.5px;">{{ $notif->message }}</p>
                        <small class="text-muted" style="font-size: 12px;">{{ $notif->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 bg-white rounded-3 border shadow-sm">
                <i class="fa-regular fa-bell-slash text-muted mb-3" style="font-size: 48px; opacity: 0.3;"></i>
                <h5 class="fw-bold text-dark">Belum ada notifikasi</h5>
                <p class="text-muted mb-0">Anda akan menerima pemberitahuan di sini jika ada update penting.</p>
            </div>
        @endforelse
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const notifItems = document.querySelectorAll('.notif-item');

    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Remove active class from all
            filterBtns.forEach(b => {
                b.classList.remove('active');
                b.classList.add('bg-white');
            });
            
            // Add active class to clicked
            btn.classList.add('active');
            btn.classList.remove('bg-white');

            const filter = btn.getAttribute('data-filter');

            notifItems.forEach(item => {
                if(filter === 'all' || item.getAttribute('data-type') === filter) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
});
</script>
@endsection
