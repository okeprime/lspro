<aside class="sidebar" id="sidebar">
    <style>
        .nav-section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            color: #d1fae5;
            font-weight: 700;
        }

        .nav-submenu {
            list-style: none;
            margin: 0 0 8px 0;
            padding: 0 0 0 34px;
        }

        .nav-submenu a {
            display: block;
            padding: 8px 16px;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 8px 0 0 8px;
            font-size: 13px;
        }

        .nav-submenu a.active,
        .nav-submenu a:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
        }
        .nav-dropdown .nav-submenu{
    display:none;
}

.nav-dropdown.active .nav-submenu{
    display:block;
}

.dropdown-toggle-menu{
    cursor:pointer;
    justify-content:space-between;
}

.dropdown-arrow{
    transition:0.3s;
}

.nav-dropdown.active .dropdown-arrow{
    transform:rotate(180deg);
}
    </style>
    <div class="brand">
        <div style="display: flex; align-items: center; gap: 10px;">
            <img src="{{ asset('assets/kementan.png') }}" alt="Logo">
            <h3>LSPRO</h3>
        </div>
        <i class="fa-solid fa-xmark" id="close-sidebar" style="cursor: pointer; font-size: 24px; color: #a7f3d0;"></i>
    </div>

    <ul class="nav-links">
        <li>
            <a href="{{ route('client.dashboard') }}" class="{{ request()->routeIs('client.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge"></i> <span>Dashboard</span>
            </a>
        </li>

        <li class="nav-section nav-dropdown active">
    <div class="nav-section-title dropdown-toggle-menu">
        <span>
            <i class="fa-solid fa-file-circle-plus"></i>
            Pengajuan
        </span>

        <i class="fa-solid fa-chevron-down dropdown-arrow"></i>
    </div>

    <ul class="nav-submenu">
                <li>
                    <a href="{{ route('pengajuan.sertifikasi') }}" class="{{ request()->routeIs('pengajuan.sertifikasi') ? 'active' : '' }}">
                        Sertifikasi
                    </a>
                </li>
                <li>
                    <a href="{{ route('pengajuan.survailen') }}" class="{{ request()->routeIs('pengajuan.survailen') ? 'active' : '' }}">
                        Survailen
                    </a>
                </li>
                <li>
                    <a href="{{ route('pengajuan.resertifikasi') }}" class="{{ request()->routeIs('pengajuan.resertifikasi') ? 'active' : '' }}">
                        Resertifikasi
                    </a>
                </li>
            </ul>
        </li>

        <li>
            <a href="{{ route('aktivitas.index') }}" class="{{ request()->routeIs('aktivitas.*') ? 'active' : '' }}">
                <i class="fa-solid fa-list-check"></i> <span>Aktivitas</span>
            </a>
        </li>

        <li>
            <a href="{{ route('billing.index') }}" class="{{ request()->routeIs('billing.*') ? 'active' : '' }}">
                <i class="fa-solid fa-receipt"></i> <span>Billing</span>
            </a>
        </li>

        <li>
            <a href="{{ route('banding.index') }}" class="{{ request()->routeIs('banding.*') ? 'active' : '' }}">
                <i class="fa-solid fa-gavel"></i> <span>Banding & Laporan</span>
            </a>
        </li>
    </ul>

    <div style="margin-top: auto; padding: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" style="background:none; border:none; width:100%; text-align:left; cursor:pointer; color: #f87171; font-weight: 600; display: flex; align-items: center; gap: 10px;">
                <i class="fa-solid fa-power-off"></i> <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>

<div class="overlay" id="overlay"></div>
