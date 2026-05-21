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
    </style>
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
                <img src="{{ asset('assets/kementan.png') }}">
                <i class="fa-solid fa-xmark" id="close-sidebar" style="cursor: pointer; color: #a7f3d0; font-size: 20px;"></i>
            </div>
            <h3 style="margin-top: 10px; color: white;">LSPRO</h3>
        </div>
        <ul class="nav-links">
            <li>
                <a href="{{ route('beranda') }}" class="{{ request()->routeIs('beranda') ? 'active' : '' }}">
                    <i class="fa-solid fa-house"></i> Beranda
                </a>
            </li>
            
            @if(Auth::check() && strtolower(Auth::user()->role) === 'admin')
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fa-solid fa-gauge"></i> Dashboard Kerja
                    </a>
                </li>
            @endif
            <li>
                <a href="{{ route('pengajuan.pilih') }}" class="{{ request()->routeIs('pengajuan.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-circle-plus"></i> Pengajuan
                </a>
            </li>
            <li>
                <a href="{{ route('aktivitas.index') }}" class="{{ request()->routeIs('aktivitas.index') ? 'active' : '' }}">
                    <i class="fa-solid fa-clock-rotate-left"></i> Aktivitas
                </a>
            </li>
        </ul>
        <div style="margin-top: auto; padding: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
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
        <i class="fa-solid fa-bars" id="menu-toggle"></i>
        
        <div class="dropdown">
            <button class="user-profile-top dropdown-toggle" type="button" id="dropdownProfileMenu" data-bs-toggle="dropdown" aria-expanded="false">
                <div style="text-align: right; margin-right: 12px;">
                    <span style="font-weight: 700; color: #1e293b; font-size: 14px; display: block;">{{ Auth::user()->nama_penghubung ?? Auth::user()->name }}</span>
                    <span style="display: block; font-size: 11px; color: #16a34a; font-weight: bold; text-transform: uppercase;">{{ Auth::user()->role ?? 'CLIENT' }}</span>
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
                        <i class="fa-solid fa-user-gear text-secondary" style="width: 16px;"></i> Pengaturan Profil
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

        menuBtn.onclick = () => { sidebar.classList.add('active'); overlay.classList.add('active'); };
        closeBtn.onclick = () => { sidebar.classList.remove('active'); overlay.classList.remove('active'); };
        overlay.onclick = () => { sidebar.classList.remove('active'); overlay.classList.remove('active'); };
    </script>
</body>
</html>