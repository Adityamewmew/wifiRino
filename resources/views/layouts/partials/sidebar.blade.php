@php
    $user = session('staff_user', []);
    $roleKey = $user['roleKey'] ?? $user['role'] ?? 'admin';
    $permissions = $user['permissions'] ?? [];
    $canViewFinance = in_array('view_finance_totals', $permissions);
    $canManageSettings = in_array('manage_settings', $permissions) || in_array('manage_settings_wa', $permissions);
    $active = $activeMenu ?? '';
@endphp

<div class="sidebar-logo">
    <div id="sidebarLogoMark" class="sidebar-logo-mark">
        <i class="fas fa-wifi"></i>
    </div>
    <div class="sidebar-brand">
        <div class="sidebar-brand-title">Sans Speed</div>
        <div class="sidebar-brand-tagline">THE BEST YOUR CONNECTION</div>
    </div>
</div>

<ul class="menu-list">
    <li class="menu-item">
        <a href="{{ url('/dashboard-admin') }}" class="menu-link {{ $active === 'dashboard' ? 'active' : '' }}">
            <i class="fas fa-home"></i> Dashboard
        </a>
    </li>

    <li class="menu-label">Pelanggan</li>
    <li class="menu-item">
        <a href="{{ url('/pelanggan') }}" class="menu-link {{ $active === 'pelanggan' ? 'active' : '' }}">
            <i class="fas fa-users"></i> Data Pelanggan
        </a>
    </li>
    <li class="menu-item">
        <a href="{{ url('/area') }}" class="menu-link {{ $active === 'area' ? 'active' : '' }}">
            <i class="fas fa-map-marked-alt"></i> Area/Wilayah
        </a>
    </li>

    <li class="menu-label">Pesan & Percakapan</li>
    <li class="menu-item">
        <a href="{{ url('/messaging-pelanggan') }}" class="menu-link {{ $active === 'messaging-pelanggan' ? 'active' : '' }}">
            <i class="fas fa-comments"></i> Percakapan pelanggan
        </a>
    </li>
    <li class="menu-item">
        <a href="{{ url('/pengumuman') }}" class="menu-link {{ $active === 'pengumuman' ? 'active' : '' }}">
            <i class="fas fa-bullhorn"></i> Siaran pelanggan
        </a>
    </li>

    <li class="menu-item">
        <a href="{{ url('/paket') }}" class="menu-link {{ $active === 'paket' ? 'active' : '' }}">
            <i class="fas fa-box-open"></i> Paket Internet
        </a>
    </li>

    <li class="menu-label">Keuangan</li>
    <li class="menu-item">
        <a href="{{ url('/tagihan') }}" class="menu-link {{ $active === 'tagihan' ? 'active' : '' }}">
            <i class="fas fa-file-invoice-dollar"></i> Tagihan Bulanan
        </a>
    </li>
    @if($canViewFinance)
    <li class="menu-item">
        <a href="{{ url('/buku-kas') }}" class="menu-link {{ $active === 'buku-kas' ? 'active' : '' }}">
            <i class="fas fa-wallet"></i> Buku Kas
        </a>
    </li>
    <li class="menu-item">
        <a href="{{ url('/pembukuan-kang-tagih') }}" class="menu-link {{ $active === 'pembukuan-kang-tagih' ? 'active' : '' }}">
            <i class="fas fa-book-open"></i> Pembukuan Agen
        </a>
    </li>
    @endif

    <li class="menu-label">Tim Lapangan</li>
    <li class="menu-item">
        <a href="{{ url('/tugas-teknisi') }}" class="menu-link {{ $active === 'tugas-teknisi' ? 'active' : '' }}">
            <i class="fas fa-tools"></i> Tugas Teknisi
        </a>
    </li>

    <li class="menu-label">Sistem</li>
    @if($canManageSettings)
    <li class="menu-item">
        <a href="{{ url('/pengaturan') }}" class="menu-link {{ $active === 'pengaturan' ? 'active' : '' }}">
            <i class="fas fa-cog"></i> Pengaturan
        </a>
    </li>
    @endif
</ul>
