@php
    $user = session('staff_user', []);
    $nama = $user['nama'] ?? 'Admin';
    $roleKey = $user['roleKey'] ?? $user['role'] ?? 'admin';
    $roleMap = [
        'owner' => 'Owner',
        'admin_keuangan' => 'Admin Keuangan',
        'admin_kasir' => 'Admin Kasir',
        'superadmin' => 'Owner',
        'admin' => 'Administrator',
    ];
    $roleLabel = $roleMap[$roleKey] ?? 'Administrator';
    $parts = explode(' ', trim($nama));
    $initials = strtoupper(substr($parts[0] ?? '', 0, 1) . substr($parts[1] ?? $parts[0] ?? '', 0, 1));
@endphp

<div>
    <button class="action-btn" id="hamburgerBtn" style="display: none;">
        <i class="fas fa-bars"></i>
    </button>
</div>

<div style="display: flex; align-items: center; gap: 20px;">
    <div style="display: flex; align-items: center; gap: 12px; cursor: pointer; position: relative;" id="userProfileBtn">
        <div style="text-align: right;">
            <div style="font-size: 14px; font-weight: 700; color: white;">{{ $roleLabel }}</div>
            <div style="font-size: 12px; color: #94a3b8;">{{ $nama }}</div>
        </div>
        <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center; font-weight: bold; color: white; flex-shrink: 0;">
            {{ $initials }}
        </div>

        <div id="profileDropdown" style="display: none; position: absolute; top: 52px; right: 0; background: var(--card-bg, #1e293b); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); min-width: 180px; z-index: 200;">
            <div style="padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.08); margin-bottom: 8px;">
                <div style="font-weight: 700; color: var(--text-primary, white); font-size: 14px;">{{ $nama }}</div>
                <div style="font-size: 12px; color: #94a3b8;">{{ $roleLabel }}</div>
            </div>
            <a href="{{ url('/logout') }}" style="display: block; width: 100%; text-align: left; padding: 10px; border-radius: 8px; color: #ef4444; font-weight: 600; text-decoration: none; font-size: 14px;"
               onmouseover="this.style.background='rgba(239,68,68,0.1)'" onmouseout="this.style.background='transparent'">
                <i class="fas fa-sign-out-alt"></i> Keluar Sistem
            </a>
        </div>
    </div>
</div>

<style>
    @media (max-width: 768px) {
        #hamburgerBtn { display: block !important; }
    }
</style>
