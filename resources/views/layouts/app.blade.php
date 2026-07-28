<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - LSPRO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    @yield('extra-css')
    
    <style>
        /* CSS reset & penyesuaian agar gaya bootstrap tidak merusak style asli Anda */
        .dropdown-toggle::after {
            display: none !important; /* Menghilangkan tanda panah bawaan bootstrap */
        }
        .user-profile-top {
            cursor: pointer;
            display: flex;
            align-items: center;
            background: none;
            border: none;
            padding: 0;
        }
        /* Memperbaiki posisi navbar jika tergeser akibat pembungkus baru */
        .top-navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .dropdown-menu-profile {
            border-radius: 12px !important;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
            border: 1px solid #e2e8f0 !important;
            padding: 8px 0;
            margin-top: 10px !important;
        }

        .nav-link-dropdown{
    display:flex;
    justify-content:space-between;
    align-items:center;
    width:100%;
    color:inherit;
    text-decoration:none;
}

.dropdown-menu-sidebar{
    display:none;
    list-style:none;
    padding-left:20px;
    margin-top:8px;
}

.nav-item-with-dropdown.active .dropdown-menu-sidebar{
    display:block;
}

.dropdown-icon{
    transition:.3s;
}

.nav-item-with-dropdown.active .dropdown-icon{
    transform:rotate(180deg);
}

        @media (min-width: 992px) {
            body {
                transition: margin-left 0.3s ease;
            }
            body.sidebar-active {
                margin-left: 260px;
            }
            .top-navbar {
                transition: left 0.3s ease;
            }
            body.sidebar-active .top-navbar {
                left: 260px;
            }
            body.sidebar-active .overlay {
                display: none !important;
            }
        }
    </style>
<script src="https://unpkg.com/@phosphor-icons/web"></script></head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="brand" style="padding: 20px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; width: 100%;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <img src="{{ asset('assets/kementan.png') }}" alt="Logo" style="height: 42px; width: auto; max-width: none; margin-bottom: 0;">
                    <div style="text-align: left; line-height: 1.2;">
                        <div style="font-size: 11px; font-weight: 500; color: #cbd5e1;">Kementerian Pertanian RI</div>
                        <div style="font-size: 14px; font-weight: 800; color: #4ade80;">LSPro BRMP SDLP</div>
                    </div>
                </div>
                <i class="ph ph-x" id="close-sidebar" style="cursor: pointer; color: #a7f3d0; font-size: 20px; margin-top: 10px;"></i>
            </div>
        </div>
        <ul class="nav-links">
            @php
                $role = strtolower(Auth::user()->role ?? 'client');
                $subRole = strtolower(Auth::user()->sub_role ?? '');
                $isInternal = in_array($role, ['superadmin', 'admin']);
            @endphp

            @if(!$isInternal)
                <!-- ================= SIDEBAR CLIENT ================= -->
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('client.dashboard') }}" class="{{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
                        <i class="ph ph-squares-four"></i> Dashboard
                    </a>
                </li>

                <!-- Pengajuan dengan Submenu -->
                @php
                    $isPengajuanActive = request()->routeIs('pengajuan.*') || request()->routeIs('aktivitas.*');
                @endphp
                <li class="nav-item-with-dropdown {{ $isPengajuanActive ? 'active' : '' }}">
                    <a href="{{ route('pengajuan.index') }}" class="nav-link-dropdown">
                        <span>
                            <i class="ph ph-file-plus"></i>
                            Pengajuan
                        </span>
                        <i class="ph ph-caret-down dropdown-icon dropdown-toggle-arrow" style="cursor: pointer; padding: 5px;"></i>
                    </a>

                    <ul class="dropdown-menu-sidebar">
                        <li>
                            <a href="{{ route('pengajuan.sertifikasi') }}" class="{{ request()->routeIs('pengajuan.sertifikasi') ? 'active' : '' }}">
                                <i class="ph ph-certificate"></i> Sertifikasi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pengajuan.resertifikasi') }}" class="{{ request()->routeIs('pengajuan.resertifikasi') ? 'active' : '' }}">
                                <i class="ph ph-arrows-clockwise"></i> Resertifikasi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pengajuan.survailen') }}" class="{{ request()->routeIs('pengajuan.survailen') ? 'active' : '' }}">
                                <i class="ph ph-magnifying-glass"></i> Survailen
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Aktivitas Pengajuan -->
                <li class="nav-item-with-dropdown {{ request()->routeIs('aktivitas.*') ? 'active' : '' }}">
                    <a href="{{ route('aktivitas.index') }}" class="nav-link-dropdown">
                        <span>
                            <i class="ph ph-list-checks"></i> Aktivitas
                        </span>
                        <i class="ph ph-caret-down dropdown-icon dropdown-toggle-arrow" style="cursor: pointer; padding: 5px;"></i>
                    </a>
                    <ul class="dropdown-menu-sidebar">
                        <li>
                            <a href="{{ route('aktivitas.index', ['filter' => 'sertifikasi']) }}" class="{{ request('filter') === 'sertifikasi' || request()->routeIs('aktivitas.sertifikasi') ? 'active' : '' }}">
                                <i class="ph ph-certificate"></i> Sertifikasi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('aktivitas.index', ['filter' => 'resertifikasi']) }}" class="{{ request('filter') === 'resertifikasi' || request()->routeIs('aktivitas.resertifikasi') ? 'active' : '' }}">
                                <i class="ph ph-arrows-clockwise"></i> Resertifikasi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('aktivitas.index', ['filter' => 'survailen']) }}" class="{{ request('filter') === 'survailen' ? 'active' : '' }}">
                                <i class="ph ph-magnifying-glass"></i> Survailen
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('aktivitas.index', ['filter' => 'banding']) }}" class="{{ request('filter') === 'banding' || request()->routeIs('aktivitas.banding') ? 'active' : '' }}">
                                <i class="ph ph-scales"></i> Banding
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('aktivitas.index', ['filter' => 'keluhan']) }}" class="{{ request('filter') === 'keluhan' || request()->routeIs('aktivitas.keluhan') ? 'active' : '' }}">
                                <i class="ph ph-chat-teardrop-text"></i> Keluhan
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Sertifikat (New) -->
                <li>
                    <a href="{{ route('sertifikat.index') }}" class="{{ request()->routeIs('sertifikat.*') ? 'active' : '' }}">
                        <i class="ph ph-medal"></i> Sertifikat
                    </a>
                </li>

                <!-- Billing -->
                <li>
                    <a href="{{ route('billing.index') }}" class="{{ request()->routeIs('billing.*') ? 'active' : '' }}">
                        <i class="ph ph-receipt"></i> Billing
                    </a>
                </li>

                <!-- Banding dan Keluhan -->
                <li>
                    <a href="{{ route('banding.index') }}" class="{{ request()->routeIs('banding.*') ? 'active' : '' }}">
                        <i class="ph ph-scales"></i> Banding &amp; Keluhan
                    </a>
                </li>

            @else
                {{-- ================= SIDEBAR ADMIN / INTERNAL ================= --}}
                @php
                    $isSuperAdmin = $role === 'superadmin';
                    $isLayanan = in_array($role, ['superadmin', 'admin']);
                    $sidebarHead = 'color:#a7f3d0;font-size:11px;font-weight:700;text-transform:uppercase;margin-top:22px;margin-bottom:4px;padding-left:25px;letter-spacing:1px;opacity:0.6;background:transparent;';
                @endphp

                {{-- Dashboard (Semua Admin) --}}
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="ph ph-squares-four"></i> Dashboard
                    </a>
                </li>

                {{-- ============================================================ --}}
                {{-- SUPERADMIN / KETUA LSPRO --}}
                {{-- ============================================================ --}}
                @if($isSuperAdmin)
                    <li class="sidebar-heading" style="{{ $sidebarHead }}">Superadmin / Ketua LSPro</li>
                    <li>
                        <a href="{{ route('admin.superadmin.persetujuan_penugasan') }}" class="{{ request()->routeIs('admin.superadmin.persetujuan_penugasan*') ? 'active' : '' }}">
                            <i class="ph ph-signature"></i> Persetujuan Penugasan
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.superadmin.pengesahan_sertifikat') }}" class="{{ request()->routeIs('admin.superadmin.pengesahan_sertifikat*') ? 'active' : '' }}">
                            <i class="ph ph-stamp"></i> Pengesahan Sertifikat
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('superadmin.users.index') }}" class="{{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}">
                            <i class="ph ph-users"></i> Manajemen User
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('superadmin.form_builder.index') }}" class="{{ request()->routeIs('superadmin.form_builder.*') ? 'active' : '' }}">
                            <i class="ph ph-gear"></i> Pengaturan Sistem
                        </a>
                    </li>
                @endif

                {{-- ============================================================ --}}
                {{-- Administrasi --}}
                {{-- ============================================================ --}}
                @if($isLayanan)
                    @php
                        // Hitung jumlah notifikasi per bagian
                        $c_pengajuan = \App\Models\Pengajuan::whereIn('status', ['diajukan', 'menunggu_tinjauan_admin'])->count();
                        $c_billing = \App\Models\Invoice::where('status', 'pending_verification')->count();
                        $c_jadwal = \App\Models\Pengajuan::where(function($q) {
                            $q->where('status', 'audit_kecukupan')->whereNotNull('ceklis_dokumen')
                              ->orWhereIn('status', ['menunggu_persetujuan_jadwal', 'evaluasi_724_audit', 'proses_audit']);
                        })->count();
                        $c_eval_dok = \App\Models\Pengajuan::where('status', 'audit_kecukupan')->whereNull('ceklis_dokumen')->count();
                        $c_penugasan = \App\Models\Pengajuan::whereIn('status', ['menunggu_jadwal'])->count();
                        $c_eval_lap = \App\Models\Pengajuan::whereIn('status', ['evaluasi', 'proses_evaluasi'])->count();
                        $c_komtek = \App\Models\Pengajuan::whereIn('status', ['keputusan', 'proses_keputusan'])->count();
                        $c_banding = class_exists('\App\Models\Banding') ? \App\Models\Banding::whereNotIn('status', ['selesai', 'ditolak'])->count() : 0;
                        $c_lks = \App\Models\Pengajuan::whereIn('status', ['tindakan_perbaikan'])->count();
                        $c_lhp = \App\Models\Pengajuan::whereIn('status', ['menunggu_lhp', 'tinjauan_lhp'])->count();
                    @endphp

                    <li class="sidebar-heading" style="{{ $sidebarHead }}">Administrasi</li>
                    <li>
                        <a href="{{ route('admin.panel_tu') }}" class="{{ request()->routeIs('admin.panel_tu*') ? 'active' : '' }}">
                            <i class="ph ph-tray"></i> Pengajuan Masuk
                            @if($c_pengajuan > 0)
                                <span class="badge bg-danger rounded-pill ms-auto" style="font-size:10px;">{{ $c_pengajuan }}</span>
                            @endif
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.penjadwalan_audit') }}" class="{{ request()->routeIs('admin.penjadwalan_audit*') ? 'active' : '' }}">
                            <i class="ph ph-calendar"></i> Penjadwalan Audit
                            @if($c_jadwal > 0)
                                <span class="badge bg-danger rounded-pill ms-auto" style="font-size:10px;">{{ $c_jadwal }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.panel_keuangan') }}" class="{{ request()->routeIs('admin.panel_keuangan*') ? 'active' : '' }}">
                            <i class="ph ph-receipt"></i> Perjanjian & Billing
                            @if($c_billing > 0)
                                <span class="badge bg-danger rounded-pill ms-auto" style="font-size:10px;">{{ $c_billing }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.penyerahan_sertifikat') ?? '#' }}" class="{{ request()->routeIs('admin.penyerahan_sertifikat*') ? 'active' : '' }}">
                            <i class="ph ph-medal"></i> Penerbitan Sertifikat
                        </a>
                    </li>

                    <li class="sidebar-heading" style="{{ $sidebarHead }}">Layanan & Standar</li>
                    <li>
                        <a href="{{ route('admin.layanan.evaluasi_dokumen') }}" class="{{ request()->routeIs('admin.layanan.evaluasi_dokumen*') ? 'active' : '' }}">
                            <i class="ph ph-file-dashed"></i> Evaluasi Dokumen
                            @if($c_eval_dok > 0)
                                <span class="badge bg-danger rounded-pill ms-auto" style="font-size:10px;">{{ $c_eval_dok }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.layanan.penugasan_tim') }}" class="{{ request()->routeIs('admin.layanan.penugasan_tim*') ? 'active' : '' }}">
                            <i class="ph ph-users-three"></i> Penugasan Tim
                            @if($c_penugasan > 0)
                                <span class="badge bg-danger rounded-pill ms-auto" style="font-size:10px;">{{ $c_penugasan }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.layanan.evaluasi_laporan') }}" class="{{ request()->routeIs('admin.layanan.evaluasi_laporan*') ? 'active' : '' }}">
                            <i class="ph ph-clipboard-text"></i> Evaluasi Laporan
                            @if($c_eval_lap > 0)
                                <span class="badge bg-danger rounded-pill ms-auto" style="font-size:10px;">{{ $c_eval_lap }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.layanan.komisi_teknis') }}" class="{{ request()->routeIs('admin.layanan.komisi_teknis*') ? 'active' : '' }}">
                            <i class="ph ph-scales"></i> Kajian Komisi Teknis
                            @if($c_komtek > 0)
                                <span class="badge bg-danger rounded-pill ms-auto" style="font-size:10px;">{{ $c_komtek }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.banding.index') }}" class="{{ request()->routeIs('admin.banding.*') ? 'active' : '' }}">
                            <i class="ph ph-warning"></i> Penanganan Keluhan & Banding
                            @if($c_banding > 0)
                                <span class="badge bg-danger rounded-pill ms-auto" style="font-size:10px;">{{ $c_banding }}</span>
                            @endif
                        </a>
                    </li>

                    <li class="sidebar-heading" style="{{ $sidebarHead }}">Tim Audit & Laboratorium</li>
                    <li>
                        <a href="{{ route('admin.audit_berkas') }}" class="{{ request()->routeIs('admin.audit_berkas*') ? 'active' : '' }}">
                            <i class="ph ph-calendar-check"></i> Penjadwalan & Audit Kesesuaian
                            @if($c_jadwal > 0)
                                <span class="badge bg-danger rounded-pill ms-auto" style="font-size:10px;">{{ $c_jadwal }}</span>
                            @endif
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('admin.audit.lks') }}" class="{{ request()->routeIs('admin.audit.lks*') ? 'active' : '' }}">
                            <i class="ph ph-warning"></i> Laporan Ketidaksesuaian
                            @if($c_lks > 0)
                                <span class="badge bg-danger rounded-pill ms-auto" style="font-size:10px;">{{ $c_lks }}</span>
                            @endif
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.hasil_lab') }}" class="{{ request()->routeIs('admin.hasil_lab') || request()->routeIs('admin.data_sampel*') || request()->routeIs('admin.pengajuan.form_lhp*') ? 'active' : '' }}">
                            <i class="ph ph-flask"></i> Hasil Uji & Upload LHP
                            @if($c_lhp > 0)
                                <span class="badge bg-danger rounded-pill ms-auto" style="font-size:10px;">{{ $c_lhp }}</span>
                            @endif
                        </a>
                    </li>
                @endif



            @endif
        </ul>
        <div style="margin-top: auto; padding: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
            <a href="{{ url('/') }}" style="color: #a7f3d0; text-decoration: none; display: flex; align-items: center; gap: 10px; font-weight: 500; margin-bottom: 15px; font-size: 14px;">
                <i class="ph ph-house"></i> Kembali ke Beranda
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" style="background:none; border:none; color:#f87171; cursor:pointer; display:flex; align-items:center; gap:10px; font-weight:600;">
                    <i class="ph ph-power"></i> Keluar
                </button>
            </form>
        </div>
    </aside>

    <div class="overlay" id="overlay"></div>

    <div class="top-navbar">
        <div style="display: flex; align-items: center; gap: 20px; flex: 1;">
            <i class="ph ph-list" id="menu-toggle"></i>
            
            @if(request()->routeIs('client.dashboard') || request()->routeIs('beranda'))
            <!-- Navigation links removed -->
            @endif
        </div>
        <div style="display: flex; align-items: center; gap: 20px;">
            <div class="dropdown">
                @php
                    $unreadCount = \App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count();
                @endphp
                <button class="user-profile-top dropdown-toggle" type="button" id="dropdownNotifMenu" data-bs-toggle="dropdown" aria-expanded="false" style="position: relative;">
                    <i class="ph ph-bell" style="font-size: 22px; color: #64748b;"></i>
                    @if($unreadCount > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 9px; padding: 3px 5px;">
                        {{ $unreadCount }}
                    </span>
                    @endif
                </button>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-profile" aria-labelledby="dropdownNotifMenu" style="width: 300px; padding: 0;">
                    <li class="px-3 py-2 border-bottom fw-bold" style="font-size: 13px; background: #f8fafc;">
                        Notifikasi Terbaru
                    </li>
                    @php
                        $latestNotifs = \App\Models\Notification::where('user_id', Auth::id())->orderBy('created_at', 'desc')->take(3)->get();
                    @endphp
                    @forelse($latestNotifs as $notif)
                    <li>
                        <a class="dropdown-item py-2" href="{{ route('notifikasi.index') }}" style="font-size: 12px; white-space: normal; border-bottom: 1px solid #f1f5f9;">
                            <div class="fw-bold text-dark mb-1">{{ $notif->title }}</div>
                            <div class="text-muted">{{ Str::limit($notif->message, 60) }}</div>
                            <div class="text-muted mt-1" style="font-size: 10px;">{{ $notif->created_at->diffForHumans() }}</div>
                        </a>
                    </li>
                    @empty
                    <li>
                        <div class="dropdown-item py-3 text-center text-muted" style="font-size: 12px; white-space: normal;">
                            Tidak ada notifikasi baru.
                        </div>
                    </li>
                    @endforelse
                    <li>
                        <a class="dropdown-item py-2 text-center fw-bold text-primary" href="{{ route('notifikasi.index') }}" style="font-size: 12px;">
                            Lihat Semua Notifikasi
                        </a>
                    </li>
                </ul>
            </div>

            <div class="dropdown">
                <button class="user-profile-top dropdown-toggle" type="button" id="dropdownProfileMenu" data-bs-toggle="dropdown" aria-expanded="false">
                <div style="text-align: right; margin-right: 12px;">
                    @php
                        $navRole    = strtolower(Auth::user()->role ?? 'client');
                        $navSubRole = strtolower(Auth::user()->sub_role ?? '');
                    @endphp

                    @if($navRole === 'superadmin')
                        {{-- SUPERADMIN: Nama | SUPERADMIN --}}
                        <span style="font-weight: 700; color: #1e293b; font-size: 14px; display: block;">
                            {{ Auth::user()->nama_penghubung ?? 'Superadmin' }}
                        </span>
                        <span style="display: block; font-size: 11px; color: #dc2626; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
                            SUPERADMIN
                        </span>

                    @elseif($navRole === 'admin')
                        {{-- ADMIN: Nama | Unit Kerja --}}
                        <span style="font-weight: 700; color: #1e293b; font-size: 14px; display: block;">
                            {{ Auth::user()->nama_penghubung ?? Auth::user()->email }}
                        </span>
                        <span style="display: block; font-size: 11px; color: #16a34a; font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em;">
                            {{ Auth::user()->unit_kerja ?? (Auth::user()->sub_role ? strtoupper(Auth::user()->sub_role) : 'ADMIN') }}
                        </span>

                    @else
                        {{-- CLIENT: Nama PIC | Nama Perusahaan --}}
                        <span style="font-weight: 700; color: #1e293b; font-size: 14px; display: block;">
                            {{ Auth::user()->nama_penghubung ?? Auth::user()->email }}
                        </span>
                        <span style="display: block; font-size: 11px; color: #16a34a; font-weight: 600;">
                            {{ Auth::user()->nama_perusahaan ?? 'CLIENT' }}
                        </span>
                    @endif
                </div>
                <i class="ph ph-user-circle" style="font-size: 32px; color: #cbd5e1;"></i>
            </button>
            
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-profile" aria-labelledby="dropdownProfileMenu">
                <li class="px-3 py-2 border-bottom mb-1">
                    <div class="fw-bold text-dark" style="font-size: 13px;">{{ Auth::user()->name }}</div>
                    <div class="text-muted" style="font-size: 11px; text-transform: lowercase;">{{ Auth::user()->email }}</div>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('profile.index') }}" style="font-size: 13px; color: #334155;">
                        <i class="ph ph-gear text-secondary" style="width: 16px;"></i> Pengaturan Profile
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger" href="#" 
                       onclick="event.preventDefault(); document.getElementById('logout-top-form').submit();"
                       style="font-size: 13px; font-weight: 500;">
                        <i class="ph ph-sign-out" style="width: 16px;"></i> Keluar Aplikasi
                    </a>
                    <form id="logout-top-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>

    <main class="main-content">@yield('content')</main>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const menuBtn = document.getElementById('menu-toggle');
        const closeBtn = document.getElementById('close-sidebar');

        menuBtn.onclick = () => { 
            sidebar.classList.toggle('active'); 
            overlay.classList.toggle('active'); 
            document.body.classList.toggle('sidebar-active');
        };
        if (closeBtn) {
            closeBtn.onclick = () => { 
                sidebar.classList.remove('active'); 
                overlay.classList.remove('active'); 
                document.body.classList.remove('sidebar-active');
            };
        }
        overlay.onclick = () => { 
            sidebar.classList.remove('active'); 
            overlay.classList.remove('active'); 
            document.body.classList.remove('sidebar-active');
        };

        // Auto mark notifications as read when clicking the bell icon
        const dropdownNotifMenu = document.getElementById('dropdownNotifMenu');
        if (dropdownNotifMenu) {
            dropdownNotifMenu.addEventListener('click', function() {
                let badge = this.querySelector('.badge');
                if (badge) {
                    fetch('{{ route("notifikasi.markAllRead") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    }).then(response => response.json())
                      .then(data => {
                          if (data.success) {
                              badge.remove();
                          }
                      }).catch(err => console.error(err));
                }
            });
        }

        // Dropdown Menu Toggle
        document.querySelectorAll('.dropdown-toggle-arrow').forEach(arrow => {
            arrow.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const parent = this.closest('.nav-item-with-dropdown');
                parent.classList.toggle('active');
            });
        });

        // Close dropdown when clicking on submenu items
        document.querySelectorAll('.dropdown-menu-sidebar a').forEach(link => {
            link.addEventListener('click', function() {
                const parent = this.closest('.nav-item-with-dropdown');
                parent.classList.remove('active');
            });
        });

        // Responsive Tables: Auto inject data-label based on thead
        document.querySelectorAll('.table-responsive table').forEach(table => {
            const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.innerText.trim());
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                cells.forEach((cell, index) => {
                    if (headers[index] && !cell.hasAttribute('data-label')) {
                        cell.setAttribute('data-label', headers[index]);
                    }
                });
            });
        });

        // Mark Notifications as Read on click
        document.addEventListener('DOMContentLoaded', function() {
            var notifDropdownBtn = document.getElementById('dropdownNotifMenu');
            if (notifDropdownBtn) {
                notifDropdownBtn.addEventListener('show.bs.dropdown', function () {
                    var badge = notifDropdownBtn.querySelector('.badge');
                    if (badge) {
                        badge.style.display = 'none'; // Sembunyikan badge
                        
                        // Kirim request ke backend via AJAX
                        fetch('{{ route("notifikasi.markAllRead") }}', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        }).catch(e => console.error(e));
                    }
                });
            }
        });
    </script>
    @yield('extra-js')
</body>
</html>
