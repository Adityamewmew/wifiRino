import { auth, isAdminAppRole, resolveRoleKey, parseStoredUser } from '../api-config.js';

// Impor Views
import DashboardView from './pages/dashboard.js';
import PelangganView from './pages/pelanggan.js';

// Status Global SPA
window.appState = {
    user: null,
    profile: null
};

// Definisi Rute
const routes = {
    '': DashboardView,
    '#dashboard': DashboardView,
    '#pelanggan': PelangganView,
};

// Logika Router (Pengubah Tampilan Layar Tengah)
async function router() {
    const hash = window.location.hash;
    const ViewComponent = routes[hash] || DashboardView;
    const contentDiv = document.getElementById('app-content');
    if (!contentDiv) return;

    contentDiv.innerHTML = `
        <div style="display:flex; justify-content:center; align-items:center; height:50vh;">
            <i class="fas fa-circle-notch fa-spin fa-3x" style="color:#cbd5e1;"></i>
        </div>
    `;

    try {
        const viewHtml = await ViewComponent.getHtml();
        contentDiv.innerHTML = viewHtml;
        await ViewComponent.init();
    } catch (e) {
        console.error("Router error:", e);
        contentDiv.innerHTML = `<div style="padding:20px; color:red; font-weight:bold;"><i class="fas fa-exclamation-triangle"></i> Gagal Memuat Halaman: ${e.message}</div>`;
    }
}

// Logika Navigasi Sidebar (Merubah state aktif)
function updateActiveNav() {
    document.querySelectorAll('.menu-link').forEach(link => {
        link.classList.remove('active');
        const href = link.getAttribute('href');
        if (href === window.location.hash || (window.location.hash === '' && href === '#dashboard')) {
            link.classList.add('active');
        }
    });
}

// Deteksi jika hash/menu diubah
window.addEventListener('hashchange', () => {
    updateActiveNav();
    router();
});

// Helper: safely set text content only if element exists
function safeSetText(id, text) {
    const el = document.getElementById(id);
    if (el) el.textContent = text;
}

// Penjalanan Core Engine Saat Pertama Load
// layout.js runs first as a separate module script before app.js
// So we can use a short timeout to ensure layout DOM is ready, or just use DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {

    auth.onAuthStateChanged(async (user) => {
        if (user) {
            let profile = parseStoredUser();
            if (!profile?.email) {
                await new Promise((r) => setTimeout(r, 80));
                profile = parseStoredUser();
            }
            if (profile) {
                if (!isAdminAppRole(profile)) {
                    alert("Akses Master Data Ditolak! Anda bukan Admin.");
                    window.location.replace("/app-teknisi");
                    return;
                }

                // Simpan State Global
                window.appState.user = user;
                window.appState.profile = profile;

                // Update UI Profile (elements are injected by layout.js, use safeSetText)
                const namaUser = String(profile.nama || '').trim();
                safeSetText('userNameTxt', namaUser || 'Pengguna');
                const roleKey = resolveRoleKey(profile.roleKey || profile.role);
                const roleText = roleKey === 'owner'
                    ? 'Owner'
                    : (roleKey === 'admin_kasir' ? 'Admin Kasir' : 'Admin Keuangan');
                safeSetText('userRoleTxt', roleText);
                const initials = (namaUser.match(/\b\w/g) || []);
                const inisial = `${initials[0] || ''}${initials.length > 1 ? initials[initials.length - 1] : ''}`.toUpperCase();
                const avatarEl = document.getElementById('userAvatar');
                if (avatarEl) avatarEl.textContent = inisial || '?';

                // dropName and dropEmail may not exist in new layout - skip gracefully
                safeSetText('dropName', namaUser || 'Pengguna');
                safeSetText('dropEmail', user.email || '');

                // Render First View
                updateActiveNav();
                router();
            } else {
                await auth.signOut();
            }
        } else {
            window.location.replace("/login");
        }
    });
});
