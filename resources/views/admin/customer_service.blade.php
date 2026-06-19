@extends('layouts.app')
@section('title', 'Customer Service - LS Pro')

@section('content')
<div class="container-fluid py-3 px-3 px-md-4 h-100">
    <div class="mb-4">
        <h2 style="color: #1e293b; font-weight: 700; letter-spacing: -0.5px; margin-bottom: 4px;">Customer Service</h2>
        <p class="text-muted small mb-0">Layanan komunikasi langsung dengan Klien LSPro.</p>
    </div>

    <div class="row g-0 bg-white rounded-4 shadow-sm" style="border: 1px solid #e2e8f0; height: calc(100vh - 180px); min-height: 500px; overflow: hidden;">
        <!-- Sidebar Client List -->
        <div class="col-md-4 col-lg-3 border-end d-flex flex-column" style="background-color: #f8fafc;">
            <div class="p-3 border-bottom bg-white">
                <input type="text" class="form-control form-control-sm shadow-none" placeholder="Cari Klien..." style="border-radius: 8px;">
            </div>
            <div class="flex-grow-1 overflow-auto p-2">
                @foreach($clients as $client)
                <a href="?client_id={{ $client->id }}" class="text-decoration-none d-flex align-items-center gap-3 p-2 rounded-3 mb-1 {{ request('client_id') == $client->id ? 'bg-primary text-white shadow-sm' : 'text-dark hover-bg-light' }}" style="transition: 0.2s;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: {{ request('client_id') == $client->id ? '#fff' : '#e2e8f0' }}; color: {{ request('client_id') == $client->id ? '#0d6efd' : '#475569' }}; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                        {{ strtoupper(substr($client->name, 0, 1)) }}
                    </div>
                    <div class="overflow-hidden">
                        <div class="fw-bold text-truncate" style="font-size: 13px; {{ request('client_id') == $client->id ? 'color: white;' : '' }}">{{ $client->nama_perusahaan ?? $client->name ?? 'Klien' }}</div>
                        <div class="text-truncate" style="font-size: 11px; {{ request('client_id') == $client->id ? 'color: rgba(255,255,255,0.8);' : 'color: #64748b;' }}">{{ $client->email }}</div>
                    </div>
                </a>
                @endforeach
                
                @if($clients->isEmpty())
                <div class="text-center p-4 text-muted small">
                    Belum ada klien terdaftar.
                </div>
                @endif
            </div>
        </div>

        <!-- Chat Area -->
        <div class="col-md-8 col-lg-9 d-flex flex-column position-relative">
            @if(request('client_id'))
                @php
                    $activeClient = $clients->firstWhere('id', request('client_id'));
                @endphp
                <div class="p-3 border-bottom d-flex align-items-center gap-3 bg-white" style="z-index: 10;">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: #e2e8f0; color: #475569; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                        {{ strtoupper(substr($activeClient->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="fw-bold" style="color: #1e293b; font-size: 15px;">{{ $activeClient->nama_perusahaan ?? $activeClient->name ?? 'Klien' }}</div>
                        <div class="text-muted" style="font-size: 12px;">{{ $activeClient->name ? $activeClient->name . ' - ' : '' }}{{ $activeClient->email }}</div>
                    </div>
                </div>

                <div class="flex-grow-1 p-4 overflow-auto" style="background-color: #f1f5f9;" id="cs-chat-box">
                    <div class="text-center mb-4">
                        <span class="badge bg-light text-muted border px-3 py-1 rounded-pill">Mulai obrolan dengan {{ $activeClient->nama_perusahaan ?? $activeClient->name ?? 'Klien' }}</span>
                    </div>
                    <!-- Pesan akan dimuat via JS -->
                </div>

                <div class="p-3 border-top bg-white">
                    <form id="cs-chat-form" class="d-flex gap-2">
                        @csrf
                        <input type="text" id="cs-chat-input" class="form-control shadow-none" placeholder="Ketik balasan untuk klien..." required style="border-radius: 20px; padding-left: 20px;">
                        <button type="submit" class="btn btn-primary rounded-circle" style="width: 44px; height: 44px; flex-shrink: 0; display: flex; align-items: center; justify-content: center;">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
                
                <!-- JS Script khusus CS via Polling API Chat Internal dgn group dinamis -->
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const groupId = 'cs_client_{{ $activeClient->id }}';
                        const chatBox = document.getElementById('cs-chat-box');
                        const chatInput = document.getElementById('cs-chat-input');
                        const chatForm = document.getElementById('cs-chat-form');
                        const myUserId = {{ auth()->id() }};

                        function loadMessages() {
                            fetch(`/admin/chat/${groupId}`)
                                .then(r => r.json())
                                .then(data => {
                                    chatBox.innerHTML = '';
                                    if(data.length === 0) {
                                        chatBox.innerHTML = '<div class="text-center mb-4"><span class="badge bg-light text-muted border px-3 py-1 rounded-pill">Belum ada pesan.</span></div>';
                                    }
                                    data.forEach(msg => {
                                        const isMe = msg.user_id === myUserId;
                                        const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                                        const senderName = msg.user ? (msg.user.nama_perusahaan || msg.user.name || 'Admin') : 'Sistem';
                                        const bubble = `
                                            <div class="d-flex flex-column ${isMe ? 'align-items-end' : 'align-items-start'} mb-3">
                                                <div class="mb-1" style="font-size: 11px; color: #64748b; padding: 0 4px;">
                                                    <span class="fw-bold">${senderName}</span> • ${time}
                                                </div>
                                                <div style="max-width: 75%; padding: 10px 16px; border-radius: 16px; ${isMe ? 'background-color: #2563eb; color: white; border-bottom-right-radius: 4px;' : 'background-color: white; color: #1e293b; border-bottom-left-radius: 4px; box-shadow: 0 1px 2px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;'}">
                                                    ${msg.message}
                                                </div>
                                            </div>
                                        `;
                                        chatBox.insertAdjacentHTML('beforeend', bubble);
                                    });
                                    chatBox.scrollTop = chatBox.scrollHeight;
                                });
                        }

                        chatForm.addEventListener('submit', function(e) {
                            e.preventDefault();
                            const val = chatInput.value.trim();
                            if(!val) return;
                            
                            chatInput.value = '';
                            fetch(`/admin/chat/${groupId}`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ message: val })
                            }).then(() => loadMessages());
                        });

                        loadMessages();
                        setInterval(loadMessages, 5000);
                    });
                </script>
            @else
                <div class="h-100 d-flex flex-column align-items-center justify-content-center text-muted" style="background-color: #f8fafc;">
                    <i class="fa-regular fa-comments mb-3" style="font-size: 64px; color: #cbd5e1;"></i>
                    <h5 class="fw-bold" style="color: #475569;">Pilih Klien</h5>
                    <p style="font-size: 13px;">Silakan pilih klien dari daftar di sebelah kiri untuk mulai mengobrol.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .hover-bg-light:hover { background-color: #f1f5f9; }
</style>
@endsection
