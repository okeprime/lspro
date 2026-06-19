@extends('layouts.app')
@section('title', 'Customer Service')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden; height: calc(100vh - 140px); display: flex; flex-direction: column;">
                
                <!-- Chat Header -->
                <div class="p-3 border-bottom d-flex align-items-center gap-3" style="background: #0f766e; color: white;">
                    <div class="d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background: rgba(255,255,255,0.2); border-radius: 50%;">
                        <i class="fa-solid fa-headset fs-4"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">Customer Service LSPro</h5>
                        <div style="font-size: 13px; opacity: 0.9;">Kami siap membantu Anda terkait proses sertifikasi.</div>
                    </div>
                </div>

                <!-- Chat Messages -->
                <div class="p-4" id="chat-messages" style="flex: 1; overflow-y: auto; background: #f8fafc; display: flex; flex-direction: column; gap: 20px;">
                    <!-- Messages will be injected here -->
                </div>

                <!-- Chat Input -->
                <div class="p-3 bg-white border-top">
                    <div class="d-flex align-items-center gap-2" style="background: #f1f5f9; padding: 8px 16px; border-radius: 12px; border: 1px solid #e2e8f0;">
                        <button class="btn p-1 text-secondary" style="border: none;"><i class="fa-solid fa-paperclip fs-5"></i></button>
                        <textarea id="message-input" class="form-control border-0 bg-transparent shadow-none" rows="1" placeholder="Tulis pesan Anda di sini... (Enter untuk kirim)" style="resize: none; max-height: 100px;"></textarea>
                        <button class="btn btn-success rounded-circle" onclick="sendMessage()" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;"><i class="fa-solid fa-paper-plane"></i></button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<style>
    /* Styling for chat messages */
    .msg-block { display: flex; gap: 15px; }
    .msg-block.self { flex-direction: row-reverse; }
    
    .msg-avatar {
        width: 40px; height: 40px; border-radius: 50%; color: white;
        display: flex; align-items: center; justify-content: center; font-weight: bold; flex-shrink: 0;
    }
    .msg-content-area { flex: 1; display: flex; flex-direction: column; }
    .msg-block.self .msg-content-area { align-items: flex-end; }
    
    .msg-header { display: flex; align-items: center; gap: 8px; margin-bottom: 6px; }
    .msg-block.self .msg-header { flex-direction: row-reverse; }
    
    .msg-name { font-weight: 700; color: #1e293b; font-size: 14px; }
    .msg-time { font-size: 12px; color: #94a3b8; }
    
    .msg-bubble {
        padding: 12px 16px; border-radius: 12px; font-size: 14px; line-height: 1.5;
        max-width: 85%; display: inline-block;
    }
    .msg-block:not(.self) .msg-bubble { background: #fff; border: 1px solid #e2e8f0; border-top-left-radius: 4px; color: #334155; }
    .msg-block.self .msg-bubble { background: #dcfce7; border: 1px solid #bbf7d0; border-top-right-radius: 4px; color: #166534; }
    
    .badge-role { padding: 2px 8px; border-radius: 12px; font-size: 10px; font-weight: 600; }
    .badge-admin { background: #f3e8ff; color: #9333ea; }
    .badge-client { background: #e0f2fe; color: #0284c7; }
</style>

<script>
    const authUserId = {{ Auth::id() }};
    const currentGroup = 'cs_client_' + authUserId;

    function getAvatarColorAndRole(role) {
        if(role === 'client') return { color: '#0284c7', badgeClass: 'badge-client', roleName: 'Client' };
        return { color: '#9333ea', badgeClass: 'badge-admin', roleName: 'Customer Service' };
    }

    function loadMessages() {
        fetch(`/chat/${currentGroup}`)
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('chat-messages');
                container.innerHTML = '';
                
                if(data.length === 0) {
                    container.innerHTML = '<div class="text-center text-muted mt-5"><i class="fa-regular fa-comments fs-1 mb-3"></i><br>Belum ada pesan. Silakan mulai percakapan dengan Customer Service kami.</div>';
                }
                
                data.forEach(msg => {
                    const isSelf = msg.user_id === authUserId;
                    const time = new Date(msg.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                    const isClient = msg.user.role === 'client';
                    
                    const senderName = isClient ? (msg.user.nama_penghubung || msg.user.nama_perusahaan || 'Anda') : 'Customer Service';
                    const initials = isClient ? senderName.substring(0, 2).toUpperCase() : 'CS';
                    
                    const roleInfo = getAvatarColorAndRole(msg.user.role);
                    
                    container.innerHTML += `
                        <div class="msg-block ${isSelf ? 'self' : ''}">
                            <div class="msg-avatar" style="background: ${roleInfo.color};">${initials}</div>
                            <div class="msg-content-area">
                                <div class="msg-header">
                                    <span class="msg-name">${senderName}</span>
                                    <span class="badge-role ${roleInfo.badgeClass}">${roleInfo.roleName}</span>
                                    <span class="msg-time">${time}</span>
                                </div>
                                <div class="msg-bubble">
                                    ${msg.message.replace(/\n/g, '<br>')}
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                scrollToBottom();
            });
    }

    function sendMessage() {
        const input = document.getElementById('message-input');
        const text = input.value.trim();
        if (!text) return;

        input.value = '';
        input.style.height = '';

        fetch(`/chat/${currentGroup}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: text })
        })
        .then(res => res.json())
        .then(msg => {
            loadMessages();
        });
    }

    function scrollToBottom() {
        const container = document.getElementById('chat-messages');
        container.scrollTop = container.scrollHeight;
    }

    document.getElementById('message-input').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
    
    document.getElementById('message-input').addEventListener('input', function() {
        this.style.height = ''; 
        this.style.height = this.scrollHeight + 'px';
    });

    loadMessages();
    setInterval(loadMessages, 10000);
</script>
@endsection
