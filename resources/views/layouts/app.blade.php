<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - LSPRO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"/>
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
</head>
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
                <i class="fa-solid fa-xmark" id="close-sidebar" style="cursor: pointer; color: #a7f3d0; font-size: 20px; margin-top: 10px;"></i>
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
                        <i class="fa-solid fa-gauge"></i> Dashboard
                    </a>
                </li>

                <!-- Pengajuan dengan Submenu -->
                @php
                    $isPengajuanActive = request()->routeIs('pengajuan.*') || request()->routeIs('aktivitas.*');
                @endphp
                <li class="nav-item-with-dropdown {{ $isPengajuanActive ? 'active' : '' }}">
                    <a href="{{ route('pengajuan.index') }}" class="nav-link-dropdown">
                        <span>
                            <i class="fa-solid fa-file-circle-plus"></i>
                            Pengajuan
                        </span>
                        <i class="fa-solid fa-chevron-down dropdown-icon dropdown-toggle-arrow" style="cursor: pointer; padding: 5px;"></i>
                    </a>

                    <ul class="dropdown-menu-sidebar">
                        <li>
                            <a href="{{ route('pengajuan.sertifikasi') }}" class="{{ request()->routeIs('pengajuan.sertifikasi') ? 'active' : '' }}">
                                <i class="fa-solid fa-certificate"></i> Sertifikasi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pengajuan.resertifikasi') }}" class="{{ request()->routeIs('pengajuan.resertifikasi') ? 'active' : '' }}">
                                <i class="fa-solid fa-arrow-rotate-right"></i> Resertifikasi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('pengajuan.survailen') }}" class="{{ request()->routeIs('pengajuan.survailen') ? 'active' : '' }}">
                                <i class="fa-solid fa-magnifying-glass"></i> Survailen
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Aktivitas Pengajuan -->
                <li class="nav-item-with-dropdown {{ request()->routeIs('aktivitas.*') ? 'active' : '' }}">
                    <a href="{{ route('aktivitas.index') }}" class="nav-link-dropdown">
                        <span>
                            <i class="fa-solid fa-list-check"></i> Aktivitas
                        </span>
                        <i class="fa-solid fa-chevron-down dropdown-icon dropdown-toggle-arrow" style="cursor: pointer; padding: 5px;"></i>
                    </a>
                    <ul class="dropdown-menu-sidebar">
                        <li>
                            <a href="{{ route('aktivitas.index', ['filter' => 'sertifikasi']) }}" class="{{ request('filter') === 'sertifikasi' || request()->routeIs('aktivitas.sertifikasi') ? 'active' : '' }}">
                                <i class="fa-solid fa-certificate"></i> Sertifikasi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('aktivitas.index', ['filter' => 'resertifikasi']) }}" class="{{ request('filter') === 'resertifikasi' || request()->routeIs('aktivitas.resertifikasi') ? 'active' : '' }}">
                                <i class="fa-solid fa-arrow-rotate-right"></i> Resertifikasi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('aktivitas.index', ['filter' => 'survailen']) }}" class="{{ request('filter') === 'survailen' ? 'active' : '' }}">
                                <i class="fa-solid fa-magnifying-glass"></i> Survailen
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('aktivitas.index', ['filter' => 'banding']) }}" class="{{ request('filter') === 'banding' || request()->routeIs('aktivitas.banding') ? 'active' : '' }}">
                                <i class="fa-solid fa-gavel"></i> Banding
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('aktivitas.index', ['filter' => 'keluhan']) }}" class="{{ request('filter') === 'keluhan' || request()->routeIs('aktivitas.keluhan') ? 'active' : '' }}">
                                <i class="fa-solid fa-comment-dots"></i> Keluhan
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Sertifikat (New) -->
                <li>
                    <a href="{{ route('sertifikat.index') }}" class="{{ request()->routeIs('sertifikat.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-award"></i> Sertifikat
                    </a>
                </li>

                <!-- Billing -->
                <li>
                    <a href="{{ route('billing.index') }}" class="{{ request()->routeIs('billing.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-invoice-dollar"></i> Billing
                    </a>
                </li>

                <!-- Banding dan Keluhan -->
                <li>
                    <a href="{{ route('banding.index') }}" class="{{ request()->routeIs('banding.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-gavel"></i> Banding &amp; Keluhan
                    </a>
                </li>

                <!-- Customer Service -->
                <li>
                    <a href="{{ route('client.cs') }}" class="{{ request()->routeIs('client.cs') ? 'active' : '' }}">
                        <i class="fa-solid fa-headset"></i> Customer Service
                    </a>
                </li>

                <!-- Notifikasi Client -->
                <li>
                    <a href="{{ route('notifikasi.index') }}" class="{{ request()->routeIs('notifikasi.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-bell"></i> Notifikasi
                        @php
                            $unreadNotifs = \App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count();
                        @endphp
                        @if($unreadNotifs > 0)
                            <span class="badge bg-danger rounded-pill ms-2" style="font-size: 10px;">{{ $unreadNotifs }} New</span>
                        @endif
                    </a>
                </li>

            @else
                <!-- ================= SIDEBAR ADMIN / INTERNAL ================= -->

                <!-- Dashboard (Semua Admin) -->
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-gauge"></i> Dashboard Admin
                    </a>
                </li>

                <!-- DASHBOARD KERJA TATA USAHA -->
                <li class="sidebar-heading" style="color: #a7f3d0; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-top: 25px; margin-bottom: 5px; padding-left: 25px; letter-spacing: 1px; opacity: 0.7; background: transparent;">Dashboard Kerja Tata Usaha</li>
                <li>
                    <a href="{{ route('admin.panel_tu') ?? '#' }}" class="{{ request()->routeIs('admin.panel_tu*') ? 'active' : '' }}">
                        <i class="fa-solid fa-folder-open"></i> Panel Tata Usaha
                        @php
                            $tuNewCount = \App\Models\Pengajuan::where('status', 'diajukan')->where('is_read_tu', false)->count();
                        @endphp
                        @if($tuNewCount > 0)
                            <span class="badge bg-danger rounded-pill ms-2" style="font-size: 10px;">{{ $tuNewCount }} New</span>
                        @endif
                    </a>
                </li>

                <!-- DASHBOARD KERJA KEUANGAN -->
                <li class="sidebar-heading" style="color: #a7f3d0; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-top: 25px; margin-bottom: 5px; padding-left: 25px; letter-spacing: 1px; opacity: 0.7; background: transparent;">Dashboard Kerja Keuangan</li>
                <li>
                    <a href="{{ route('admin.panel_keuangan') }}" class="{{ request()->routeIs('admin.panel_keuangan*') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-invoice-dollar"></i> Verifikasi Pembayaran
                        @php
                            $keuanganCount = \App\Models\Invoice::where('status', 'pending_verification')->count();
                        @endphp
                        @if($keuanganCount > 0)
                            <span class="badge bg-danger rounded-pill ms-2" style="font-size: 10px;">{{ $keuanganCount }} Pending</span>
                        @endif
                    </a>
                </li>

                <!-- DASHBOARD KERJA LAYANAN -->
                <li class="sidebar-heading" style="color: #a7f3d0; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-top: 25px; margin-bottom: 5px; padding-left: 25px; letter-spacing: 1px; opacity: 0.7; background: transparent;">Dashboard Kerja Layanan</li>
                <li>
                    <a href="{{ route('admin.survailen.index') }}" class="{{ request()->routeIs('admin.survailen.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-magnifying-glass"></i> Survailen
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.cs') ?? '#' }}" class="{{ request()->routeIs('admin.cs*') ? 'active' : '' }}">
                        <i class="fa-solid fa-headset"></i> Customer Service
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.banding.index') }}" class="{{ request()->routeIs('admin.banding.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-gavel"></i> Keluhan &amp; Banding
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.penyerahan_sertifikat') ?? '#' }}" class="{{ request()->routeIs('admin.penyerahan_sertifikat*') ? 'active' : '' }}">
                        <i class="fa-solid fa-award"></i> Penyerahan Sertifikat
                    </a>
                </li>

                <!-- DASHBOARD KERJA AUDIT -->
                <li class="sidebar-heading" style="color: #a7f3d0; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-top: 25px; margin-bottom: 5px; padding-left: 25px; letter-spacing: 1px; opacity: 0.7; background: transparent;">Dashboard Kerja Audit</li>
                <li>
                    <a href="{{ route('admin.data_sampel') ?? '#' }}" class="{{ request()->routeIs('admin.data_sampel*') ? 'active' : '' }}">
                        <i class="fa-solid fa-vials"></i> Data Sampel
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.audit_kecukupan') }}" class="{{ request()->routeIs('admin.audit_kecukupan') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-circle-check"></i> Audit Kecukupan
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.audit_berkas') ?? '#' }}" class="{{ request()->routeIs('admin.audit_berkas*') ? 'active' : '' }}">
                        <i class="fa-solid fa-file-signature"></i> Audit Kesesuaian
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.hasil_lab') }}" class="{{ request()->routeIs('admin.hasil_lab') ? 'active' : '' }}">
                        <i class="fa-solid fa-flask"></i> Hasil Lab
                    </a>
                </li>

                <!-- INTERNAL & NOTIFIKASI -->
                <li class="sidebar-heading" style="color: #a7f3d0; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-top: 25px; margin-bottom: 5px; padding-left: 25px; letter-spacing: 1px; opacity: 0.7; background: transparent;">Internal &amp; Sistem</li>
                <li>
                    <a href="{{ route('admin.chat.index') }}" class="{{ request()->routeIs('admin.chat.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-comments"></i> Chat Internal
                    </a>
                </li>
                <li>
                    <a href="{{ route('notifikasi.index') }}" class="{{ request()->routeIs('notifikasi.*') ? 'active' : '' }}">
                        <i class="fa-solid fa-bell"></i> Notifikasi
                        @php
                            $unreadAdminNotifs = \App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count();
                        @endphp
                        @if($unreadAdminNotifs > 0)
                            <span class="badge bg-danger rounded-pill ms-2" style="font-size: 10px;">{{ $unreadAdminNotifs }} New</span>
                        @endif
                    </a>
                </li>

                <!-- MANAJEMEN USER (SUPERADMIN) -->
                @if($role === 'superadmin')
                    <li style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 10px; margin-top: 10px;">
                        <a href="{{ route('superadmin.users.index') }}" class="{{ request()->routeIs('superadmin.users.*') ? 'active' : '' }}">
                            <i class="fa-solid fa-users-gear"></i> Manajemen User
                        </a>
                    </li>
                @endif

            @endif
        </ul>
        <div style="margin-top: auto; padding: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
            <a href="{{ url('/') }}" style="color: #a7f3d0; text-decoration: none; display: flex; align-items: center; gap: 10px; font-weight: 500; margin-bottom: 15px; font-size: 14px;">
                <i class="fa-solid fa-house"></i> Kembali ke Beranda
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" style="background:none; border:none; color:#f87171; cursor:pointer; display:flex; align-items:center; gap:10px; font-weight:600;">
                    <i class="fa-solid fa-power-off"></i> Keluar
                </button>
            </form>
        </div>
    </aside>

    <div class="overlay" id="overlay"></div>

    <div class="top-navbar">
        <div style="display: flex; align-items: center; gap: 20px; flex: 1;">
            <i class="fa-solid fa-bars" id="menu-toggle"></i>
            
            @if(request()->routeIs('client.dashboard') || request()->routeIs('beranda'))
            <!-- Navigation links removed -->
            @endif
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
                <i class="fa-solid fa-circle-user" style="font-size: 32px; color: #cbd5e1;"></i>
            </button>
            
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-profile" aria-labelledby="dropdownProfileMenu">
                <li class="px-3 py-2 border-bottom mb-1">
                    <div class="fw-bold text-dark" style="font-size: 13px;">{{ Auth::user()->name }}</div>
                    <div class="text-muted" style="font-size: 11px; text-transform: lowercase;">{{ Auth::user()->email }}</div>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('profile.index') }}" style="font-size: 13px; color: #334155;">
                        <i class="fa-solid fa-user-gear text-secondary" style="width: 16px;"></i> Pengaturan Profile
                    </a>
                </li>
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger" href="#" 
                       onclick="event.preventDefault(); document.getElementById('logout-top-form').submit();"
                       style="font-size: 13px; font-weight: 500;">
                        <i class="fa-solid fa-right-from-bracket" style="width: 16px;"></i> Keluar Aplikasi
                    </a>
                    <form id="logout-top-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
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
        closeBtn.onclick = () => { 
            sidebar.classList.remove('active'); 
            overlay.classList.remove('active'); 
            document.body.classList.remove('sidebar-active');
        };
        overlay.onclick = () => { 
            sidebar.classList.remove('active'); 
            overlay.classList.remove('active'); 
            document.body.classList.remove('sidebar-active');
        };

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
    </script>
</body>
</html>
