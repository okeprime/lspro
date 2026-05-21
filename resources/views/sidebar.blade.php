<aside class="sidebar" id="sidebar">
    <div class="brand">
        <div style="display: flex; align-items: center; gap: 10px;">
            <img src="{{ asset('assets/kementan.png') }}" alt="Logo">
            <h3>LSPRO</h3>
        </div>
        <i class="fa-solid fa-xmark" id="close-sidebar" style="cursor: pointer; font-size: 24px; color: #a7f3d0;"></i>
    </div>
    
    <ul class="nav-links">
        <li><a href="{{ route('beranda') }}" class="{{ request()->routeIs('beranda') ? 'active' : '' }}"><i class="fa-solid fa-house"></i> <span>Beranda</span></a></li>
        <li><a href="{{ route('client.dashboard') }}" class="{{ request()->routeIs('client.dashboard') ? 'active' : '' }}"><i class="fa-solid fa-gauge"></i> <span>Dashboard Kerja</span></a></li>
        <li><a href="{{ route('pengajuan.pilih') }}" class="{{ request()->routeIs('pengajuan.*') ? 'active' : '' }}"><i class="fa-solid fa-file-circle-plus"></i> <span>Pengajuan</span></a></li>
        <li><a href="{{ route('aktivitas.index') }}" class="{{ request()->routeIs('aktivitas.index') ? 'active' : '' }}"><i class="fa-solid fa-clock-rotate-left"></i> <span>Aktivitas</span></a></li>
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