import { showConfirm } from '../utils/dialog.js';
import { apiFetch, hasPermission, resolveRoleKey } from '../../api-config.js';

function applySidebarLogoData(logoData) {
    const logoMark = document.getElementById('sidebarLogoMark');
    if (!logoMark) return;

    const hasCustomLogo = typeof logoData === 'string' && logoData.trim().length > 0;
    if (hasCustomLogo) {
        logoMark.innerHTML = '';
        const img = document.createElement('img');
        img.alt = 'Logo Sans Speed';
        img.className = 'sidebar-logo-image';
        img.src = logoData;
        logoMark.appendChild(img);
    } else {
        logoMark.innerHTML = `<i class="fas fa-wifi"></i>`;
    }
}

async function hydrateSidebarLogo() {
    const cachedLogo = localStorage.getItem('ss_sidebar_logo_data') || '';
    applySidebarLogoData(cachedLogo);

    try {
        const json = await apiFetch('/pengaturan');
        const liveLogo = json?.data?.sidebar_logo_data || '';
        if (liveLogo !== cachedLogo) {
            localStorage.setItem('ss_sidebar_logo_data', liveLogo);
            applySidebarLogoData(liveLogo);
        }
    } catch {
        // Silent fallback to cached logo
    }
}

export function renderSidebar(activeMenuId) {
    // Helper to add 'active' class if the menu matches
    const active = (id) => activeMenuId === id ? 'active' : '';
    let profile = { role: 'admin' };
    try {
        profile = JSON.parse(localStorage.getItem('ss_user') || localStorage.getItem('cnet_user') || '{}') || {};
    } catch { }
    const canViewFinanceTotals = hasPermission(profile, 'view_finance_totals');
    const canManageSettings = hasPermission(profile, 'manage_settings') || hasPermission(profile, 'manage_settings_wa');

    const sidebarHtml = `
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
                <a href="/dashboard-admin" class="menu-link ${active('dashboard')}">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </li>

            <li class="menu-label">Pelanggan</li>
            <li class="menu-item">
                <a href="/pelanggan" class="menu-link ${active('pelanggan')}">
                    <i class="fas fa-users"></i> Data Pelanggan
                </a>
            </li>
            <li class="menu-item">
                <a href="/area" class="menu-link ${active('area')}">
                    <i class="fas fa-map-marked-alt"></i> Area/Wilayah
                </a>
            </li>
            <li class="menu-label">Pesan &amp; percakapan</li>
            <li class="menu-item">
                <a href="/messaging-pelanggan" class="menu-link ${active('messaging-pelanggan')}" target="_self">
                    <i class="fas fa-comments"></i> Percakapan pelanggan
                </a>
            </li>
            <li class="menu-item">
                <a href="/pengumuman" class="menu-link ${active('pengumuman')}" target="_self">
                    <i class="fas fa-bullhorn"></i> Siaran pelanggan
                </a>
            </li>
            <li class="menu-item">
                <a href="/paket" class="menu-link ${active('paket')}">
                    <i class="fas fa-box-open"></i> Paket Internet
                </a>
            </li>

            <li class="menu-label">Keuangan</li>
            <li class="menu-item">
                <a href="/tagihan" class="menu-link ${active('tagihan')}">
                    <i class="fas fa-file-invoice-dollar"></i> Tagihan Bulanan
                </a>
            </li>
            ${canViewFinanceTotals ? `
            <li class="menu-item">
                <a href="/buku-kas" class="menu-link ${active('buku-kas')}">
                    <i class="fas fa-wallet"></i> Buku Kas
                </a>
            </li>
            <li class="menu-item">
                <a href="/pembukuan-kang-tagih" class="menu-link ${active('pembukuan-kang-tagih')}">
                    <i class="fas fa-book-open"></i> Pembukuan Agen
                </a>
            </li>` : ''}

            <li class="menu-label">Tim Lapangan</li>
            <li class="menu-item">
                <a href="/tugas-teknisi" class="menu-link ${active('tugas-teknisi')}">
                    <i class="fas fa-tools"></i> Tugas Teknisi
                </a>
            </li>

            <li class="menu-label">Sistem</li>
            ${canManageSettings ? `<li class="menu-item">
                <a href="/pengaturan" class="menu-link ${active('pengaturan')}">
                    <i class="fas fa-cog"></i> Pengaturan
                </a>
            </li>` : ''}
        </ul>
    `;

    let sidebarContainer = document.getElementById('app-sidebar');
    if (!sidebarContainer) {
        console.warn('Sidebar container #app-sidebar not found!');
        return;
    }
    sidebarContainer.classList.add('sidebar');
    sidebarContainer.innerHTML = sidebarHtml;
    hydrateSidebarLogo();

    if (!window.__ssSidebarLogoEventBound) {
        window.addEventListener('ss:sidebarLogoUpdated', (ev) => {
            const logoData = ev?.detail?.logoData || '';
            localStorage.setItem('ss_sidebar_logo_data', logoData);
            applySidebarLogoData(logoData);
        });
        window.__ssSidebarLogoEventBound = true;
    }
}

export function renderHeader() {
    // Get user details from localStorage
    let profile = { nama: 'Super Admin', role: 'admin' };
    try {
        const storedUser = JSON.parse(localStorage.getItem('ss_user') || localStorage.getItem('cnet_user'));
        if (storedUser) profile = storedUser;
    } catch (e) {
        console.warn('Could not read user profile from localStorage', e);
    }

    // Get Initials for Avatar
    let initials = 'AD';
    if (profile.nama) {
        const parts = profile.nama.trim().split(' ');
        if (parts.length > 1) {
            initials = parts[0].charAt(0).toUpperCase() + parts[1].charAt(0).toUpperCase();
        } else {
            initials = profile.nama.substring(0, 2).toUpperCase();
        }
    }

    // Role Label
    const roleMap = {
        owner: 'Owner',
        admin_keuangan: 'Admin Keuangan',
        admin_kasir: 'Admin Kasir',
        superadmin: 'Owner',
        admin: 'Administrator',
        teknisi: 'Teknisi Lapangan',
        penagih: 'Agen',
        tekpen: 'Teknisi + Agen'
    };
    const roleKey = resolveRoleKey(profile.roleKey || profile.role);
    const roleLabel = roleMap[roleKey] || roleMap[profile.role] || 'Administrator';

    const headerHtml = `
        <div>
            <!-- Hamburger menu for mobile -->
            <button class="action-btn" id="hamburgerBtn" style="display: none;">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <div style="display: flex; align-items: center; gap: 20px;">
            <!-- User Profile -->
            <div style="display: flex; align-items: center; gap: 12px; cursor: pointer; position: relative;" id="userProfileBtn">
                <div style="text-align: right;">
                    <div style="font-size: 14px; font-weight: 700; color: white;" id="userRoleTxt">${roleLabel}</div>
                    <div style="font-size: 12px; color: #94a3b8;" id="userNameTxt">${profile.nama || 'Super Admin'}</div>
                </div>
                <div id="userAvatar" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center; font-weight: bold; color: white; flex-shrink: 0;">
                    ${initials}
                </div>

                <!-- Profile Dropdown -->
                <div id="profileDropdown" style="display: none; position: absolute; top: 52px; right: 0; background: var(--card-bg, #1e293b); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); min-width: 180px; z-index: 200;">
                    <div style="padding: 10px; border-bottom: 1px solid rgba(255,255,255,0.08); margin-bottom: 8px;">
                        <div style="font-weight: 700; color: var(--text-primary, white); font-size: 14px;">${profile.nama || 'Super Admin'}</div>
                        <div style="font-size: 12px; color: #94a3b8; text-transform: capitalize;">${roleLabel}</div>
                    </div>
                    <button id="btnLogout" onclick="window._ssLogout && window._ssLogout()" style="width: 100%; text-align: left; padding: 10px; border-radius: 8px; color: #ef4444; font-weight: 600; border: none; background: transparent; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='rgba(239,68,68,0.1)'" onmouseout="this.style.background='transparent'">
                        <i class="fas fa-sign-out-alt"></i> Keluar Sistem
                    </button>
                </div>
            </div>
        </div>
    `;

    let headerContainer = document.getElementById('app-header');
    if (!headerContainer) {
        console.warn('Header container #app-header not found!');
        return;
    }
    headerContainer.classList.add('header');
    headerContainer.innerHTML = headerHtml;

    // === Profile Dropdown Toggle ===
    const profileBtn = document.getElementById('userProfileBtn');
    const dropdown = document.getElementById('profileDropdown');
    if (profileBtn && dropdown) {
        profileBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const isVisible = dropdown.style.display === 'block';
            dropdown.style.display = isVisible ? 'none' : 'block';
        });
        document.addEventListener('click', () => {
            if (dropdown) dropdown.style.display = 'none';
        });
    }

    // === Mobile Hamburger ===
    const hamburger = document.getElementById('hamburgerBtn');
    const sidebar = document.getElementById('app-sidebar');
    if (hamburger && sidebar) {
        let sidebarBackStatePushed = false;

        const pushSidebarState = () => {
            if (window.innerWidth > 768 || sidebarBackStatePushed) return;
            try {
                window.history.pushState({ sidebarOpen: true }, '');
                sidebarBackStatePushed = true;
            } catch (e) {
                // Ignore history errors (very rare), sidebar still works
            }
        };

        const releaseSidebarState = () => {
            if (!sidebarBackStatePushed) return;
            sidebarBackStatePushed = false;
        };

        const closeSidebarMobile = () => {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('open');
                releaseSidebarState();
            }
        };

        hamburger.addEventListener('click', () => {
            const willOpen = !sidebar.classList.contains('open');
            sidebar.classList.toggle('open');
            if (willOpen) {
                pushSidebarState();
            } else {
                releaseSidebarState();
            }
        });

        // Close sidebar when user taps outside menu area on mobile
        document.addEventListener('click', (event) => {
            if (window.innerWidth > 768) return;
            if (!sidebar.classList.contains('open')) return;

            const clickedInsideSidebar = sidebar.contains(event.target);
            const clickedHamburger = hamburger.contains(event.target);
            if (!clickedInsideSidebar && !clickedHamburger) {
                closeSidebarMobile();
            }
        });

        // Close sidebar after selecting any menu item on mobile
        sidebar.querySelectorAll('.menu-link').forEach(link => {
            link.addEventListener('click', () => {
                closeSidebarMobile();
            });
        });

        // Mobile back button behavior: close sidebar first
        window.addEventListener('popstate', () => {
            if (window.innerWidth > 768) return;
            if (sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
                releaseSidebarState();
            }
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('open');
                releaseSidebarState();
            }
        });
    }

    // === Logout Function ===
    if (typeof window._ssLogout !== 'function') {
        window._ssLogout = async function () {
            const confirmed = await showConfirm({
                title: '🚪 Keluar Sistem',
                message: 'Apakah Anda yakin ingin <strong>Keluar</strong> dari sistem Sans Speed?',
                type: 'danger',
                confirmText: 'Ya, Keluar',
                cancelText: 'Batal'
            });
            if (confirmed) {
                ['ss_token', 'ss_user', 'cnet_token', 'cnet_user'].forEach((k) => localStorage.removeItem(k));
                window.location.replace('/login');
            }
        };
    }

    // === CSS for mobile hamburger ===
    if (!document.getElementById('layoutStyles')) {
        const style = document.createElement('style');
        style.id = 'layoutStyles';
        style.textContent = `
            @media (max-width: 768px) {
                #hamburgerBtn { display: block !important; }
            }
        `;
        document.head.appendChild(style);
    }
}
