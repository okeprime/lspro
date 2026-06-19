@extends('layouts.app')

@section('title', 'Chat Internal')

@section('extra-css')
<style>
    .chat-container {
        display: flex;
        height: calc(100vh - 140px);
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }
    /* SIDEBAR */
    .chat-sidebar {
        width: 260px;
        background: #fff;
        border-right: 1px solid #e2e8f0;
        display: flex;
        flex-direction: column;
    }
    .sidebar-header {
        background: #0f766e;
        color: white;
        padding: 20px 20px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-top-left-radius: 12px;
    }
    .sidebar-search {
        padding: 15px 20px;
    }
    .search-input {
        width: 100%;
        background: #f1f5f9;
        border: none;
        padding: 8px 12px 8px 35px;
        border-radius: 8px;
        font-size: 13px;
    }
    .search-wrapper {
        position: relative;
    }
    .search-wrapper i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 13px;
    }
    .sidebar-section-title {
        font-size: 11px;
        font-weight: 700;
        color: #94a3b8;
        padding: 10px 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .channel-item {
        padding: 8px 20px;
        cursor: pointer;
        font-weight: 600;
        color: #475569;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0 10px;
        border-radius: 6px;
    }
    .channel-item:hover { background: #f8fafc; }
    .channel-item.active {
        background: #dcfce7;
        color: #166534;
    }
    
    .member-item {
        padding: 8px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .avatar-wrapper {
        position: relative;
    }
    .avatar-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 13px;
        font-weight: bold;
    }
    .status-dot {
        width: 10px;
        height: 10px;
        background: #22c55e;
        border: 2px solid white;
        border-radius: 50%;
        position: absolute;
        bottom: 0;
        right: -2px;
    }
    .member-name { font-size: 13px; font-weight: 600; color: #1e293b; line-height: 1.2; }
    .member-role { font-size: 11px; color: #94a3b8; }
    
    /* MAIN CHAT */
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #fff;
    }
    .chat-header {
        padding: 15px 24px;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .chat-messages {
        flex: 1;
        padding: 24px;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
        gap: 20px;
    }
    
    /* SLACK STYLE MESSAGE */
    .msg-block {
        display: flex;
        gap: 15px;
    }
    .msg-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        flex-shrink: 0;
    }
    .msg-content-area {
        flex: 1;
    }
    .msg-header {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 6px;
    }
    .msg-name { font-weight: 700; color: #1e293b; font-size: 14px; }
    .msg-time { font-size: 12px; color: #94a3b8; }
    .msg-bubble {
        background: #f1f5f9;
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 14px;
        color: #334155;
        line-height: 1.5;
        display: inline-block;
        max-width: 90%;
    }
    
    /* BADGES */
    .badge-role {
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 600;
    }
    .badge-superadmin { background: #f3e8ff; color: #9333ea; }
    .badge-tata_usaha { background: #e0f2fe; color: #0284c7; }
    .badge-auditor { background: #ffedd5; color: #ea580c; }
    .badge-layanan { background: #dcfce7; color: #16a34a; }
    
    /* INPUT */
    .chat-input-area {
        padding: 20px 24px;
        background: #fff;
    }
    .input-box-wrapper {
        display: flex;
        align-items: center;
        gap: 10px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 12px;
        background: #f8fafc;
    }
    .btn-attach {
        color: #64748b;
        background: none;
        border: none;
        font-size: 18px;
        padding: 5px;
        cursor: pointer;
    }
    .chat-input {
        flex: 1;
        border: none;
        background: transparent;
        resize: none;
        outline: none;
        font-size: 14px;
        padding: 8px 0;
        max-height: 100px;
    }
    .btn-send {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #22c55e;
        color: white;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        cursor: pointer;
    }
    .btn-send:hover { background: #16a34a; }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="chat-container">
        <!-- Sidebar -->
        <div class="chat-sidebar">
            <div class="sidebar-header">
                <i class="fa-brands fa-envira fs-5"></i>
                <h5 class="fw-bold mb-0">Internal LSPro</h5>
            </div>
            
            <div class="sidebar-search">
                <div class="search-wrapper">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" class="search-input" placeholder="Cari...">
                </div>
            </div>
            
            <div class="sidebar-section-title">Saluran</div>
            
            <div class="channel-item active" data-group="semua_admin" onclick="switchGroup('semua_admin', '# koordinasi-umum', 'Koordinasi Tim LSPro BBPM SDLP')">
                <i class="fa-solid fa-hashtag"></i> koordinasi-umum
            </div>
            <div class="channel-item" data-group="tata_usaha" onclick="switchGroup('tata_usaha', '# verifikasi-berkas', 'Tim Tata Usaha')">
                <i class="fa-solid fa-hashtag"></i> verifikasi-berkas
            </div>
            <div class="channel-item" data-group="pelayanan" onclick="switchGroup('pelayanan', '# cs-pelayanan', 'Tim CS & Keluhan')">
                <i class="fa-solid fa-hashtag"></i> cs-pelayanan
            </div>
            <div class="channel-item" data-group="audit" onclick="switchGroup('audit', '# tim-auditor', 'Koordinasi Audit Lapangan')">
                <i class="fa-solid fa-hashtag"></i> tim-auditor
            </div>

            <div class="sidebar-section-title mt-2">Anggota Tim</div>
            
            @php
                function getRoleInfo($roleStr, $subRoleStr = null) {
                    $color = '#64748b'; $roleName = $roleStr;
                    if($roleStr === 'superadmin') { $color = '#9333ea'; $roleName = 'Superadmin'; }
                    else if($roleStr === 'admin') {
                        if($subRoleStr === 'tatausaha' || $subRoleStr === 'tata_usaha') { $color = '#0284c7'; $roleName = 'Tata Usaha'; }
                        else if($subRoleStr === 'audit' || $subRoleStr === 'auditor') { $color = '#ea580c'; $roleName = 'Auditor'; }
                        else if($subRoleStr === 'layanan') { $color = '#16a34a'; $roleName = 'Layanan'; }
                        else { $color = '#64748b'; $roleName = 'Admin'; }
                    }
                    return ['color' => $color, 'name' => $roleName];
                }
            @endphp
            
            @if(isset($teamMembers) && count($teamMembers) > 0)
                @foreach($teamMembers as $member)
                    @php
                        $info = getRoleInfo($member->role, $member->sub_role);
                        $memberName = $member->nama_penghubung ?? $member->nama_perusahaan ?? explode('@', $member->email)[0] ?? 'Admin';
                        $initials = strtoupper(substr($memberName, 0, 2));
                    @endphp
                    <div class="member-item">
                        <div class="avatar-wrapper">
                            <div class="avatar-circle" style="background: {{ $info['color'] }};">{{ $initials }}</div>
                            <div class="status-dot"></div>
                        </div>
                        <div>
                            <div class="member-name">{{ $memberName }}</div>
                            <div class="member-role">{{ $info['name'] }}</div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center text-muted small mt-2">Belum ada anggota tim</div>
            @endif
        </div>

        <!-- Main Chat Area -->
        <div class="chat-main">
            <!-- Header -->
            <div class="chat-header">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="fw-bold mb-0 text-dark" id="current-group-name"># koordinasi-umum</h5>
                    <span class="text-muted" style="font-size: 13px;" id="current-group-desc">• Koordinasi Tim LSPro BBPM SDLP</span>
                </div>
                <div class="text-muted" style="font-size: 13px;">
                    <i class="fa-regular fa-user"></i> 4 online
                </div>
            </div>

            <!-- Messages -->
            <div class="chat-messages" id="chat-messages">
                <!-- Messages injected via JS -->
            </div>

            <!-- Input Area -->
            <div class="chat-input-area">
                <div class="input-box-wrapper">
                    <button class="btn-attach"><i class="fa-solid fa-paperclip"></i></button>
                    <textarea class="chat-input" id="message-input" rows="1" placeholder="Tulis pesan... (Enter untuk kirim, Shift+Enter untuk baris baru)" oninput="this.style.height = ''; this.style.height = this.scrollHeight + 'px'"></textarea>
                    <button class="btn-send" onclick="sendMessage()"><i class="fa-solid fa-paper-plane"></i></button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const authUserId = {{ Auth::id() }};
    let currentGroup = 'semua_admin';

    function switchGroup(group, name, desc) {
        currentGroup = group;
        
        // Update UI styling
        document.querySelectorAll('.channel-item').forEach(el => el.classList.remove('active'));
        document.querySelector(`.channel-item[data-group="${group}"]`).classList.add('active');
        
        document.getElementById('current-group-name').innerText = name;
        document.getElementById('current-group-desc').innerText = `• ${desc}`;
        
        loadMessages();
    }

    function getAvatarColorAndRole(role) {
        let color = '#64748b';
        let badgeClass = '';
        let roleName = role;
        
        if(role === 'superadmin') { color = '#9333ea'; badgeClass = 'badge-superadmin'; roleName = 'Superadmin'; }
        else if(role === 'admin_tu' || role === 'tata_usaha' || role === 'tatausaha') { color = '#0284c7'; badgeClass = 'badge-tata_usaha'; roleName = 'Tata Usaha'; }
        else if(role === 'auditor' || role === 'audit') { color = '#ea580c'; badgeClass = 'badge-auditor'; roleName = 'Auditor'; }
        else if(role === 'layanan' || role === 'admin') { color = '#16a34a'; badgeClass = 'badge-layanan'; roleName = 'Layanan'; }
        
        return { color, badgeClass, roleName };
    }

    function loadMessages() {
        fetch(`/admin/chat/${currentGroup}`)
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('chat-messages');
                container.innerHTML = '';
                
                if(data.length === 0) {
                    container.innerHTML = '<div class="text-center text-muted mt-5">Belum ada obrolan di saluran ini.</div>';
                }
                
                data.forEach(msg => {
                    const time = new Date(msg.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
                    const emailParts = msg.user.email ? msg.user.email.split('@')[0] : null;
                    const senderName = msg.user.nama_penghubung || msg.user.nama_perusahaan || emailParts || 'Admin';
                    const initials = senderName.substring(0, 2).toUpperCase();
                    const roleInfo = getAvatarColorAndRole(msg.user.sub_role || msg.user.role || 'admin');
                    
                    container.innerHTML += `
                        <div class="msg-block">
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
        input.style.height = ''; // reset height

        fetch(`/admin/chat/${currentGroup}`, {
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

    // Allow Enter to send, Shift+Enter for new line
    document.getElementById('message-input').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Initial load
    loadMessages();
    
    // Auto refresh every 10 seconds (simple polling)
    setInterval(loadMessages, 10000);
</script>
@endsection
