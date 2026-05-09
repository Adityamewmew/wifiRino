/**
 * Sans Speed — Role-Based Access Guard
 * Import + call guardAdmin() di setiap halaman admin standalone.
 * Jika user tidak punya akses admin app → redirect ke APP-TEKNISI.html
 */
import { isAdminAppRole, resolveRoleKey } from '../../api-config.js';

const ADMIN_PAGES_REDIRECT = '/app-teknisi';

export function guardAdmin() {
    const stored = localStorage.getItem('ss_user') || localStorage.getItem('cnet_user');
    const token = localStorage.getItem('ss_token') || localStorage.getItem('cnet_token');

    if (!token || !stored) {
        // Belum login sama sekali
        window.location.replace('/login');
        return false;
    }

    try {
        const profile = JSON.parse(stored);
        const roleKey = resolveRoleKey(profile.roleKey || profile.role);
        if (!isAdminAppRole(profile)) {
            console.warn(`[RoleGuard] Akses ditolak untuk role: ${roleKey}`);
            _showAccessDenied();
            setTimeout(() => window.location.replace(ADMIN_PAGES_REDIRECT), 2000);
            return false;
        }
        return true;
    } catch (e) {
        window.location.replace('/login');
        return false;
    }
}

function _showAccessDenied() {
    const overlay = document.createElement('div');
    overlay.style.cssText = `
        position:fixed; top:0; left:0; width:100%; height:100%; z-index:99999;
        background:rgba(0,0,0,0.85); display:flex; align-items:center;
        justify-content:center; flex-direction:column; gap:16px;
    `;
    overlay.innerHTML = `
        <div style="font-size:56px;">🚫</div>
        <div style="color:white;font-size:22px;font-weight:700;">Akses Ditolak</div>
        <div style="color:#94a3b8;font-size:14px;">Halaman ini hanya untuk Administrator.</div>
        <div style="color:#64748b;font-size:13px;">Mengalihkan ke halaman Anda...</div>
    `;
    document.body.appendChild(overlay);
}
