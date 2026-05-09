// api-config.js
// Pengganti firebase-config.js untuk server lokal tersendiri
//
// URL API: dari http(s) memakai origin yang sama (/api) — Laravel mem-proxy ke Node, atau Node melayani HTML+API di port yang sama.
// file:// fallback ke 127.0.0.1:5858. Override: localStorage.setItem('ss_api_base', 'http://HOST:PORT/api')
const BACKEND_API_PORT = 5858;

function resolveApiBaseUrl() {
    const override = String(
        localStorage.getItem('ss_api_base') || localStorage.getItem('cnet_api_base') || ''
    )
        .trim()
        .replace(/\/$/, '');
    if (override) {
        return override.endsWith('/api') ? override : `${override}/api`;
    }

    // Dari Blade (web-bootstrap): path benar saat Laravel di subfolder /public XAMPP
    if (typeof window !== 'undefined' && window.SS_API_BASE) {
        const injected = String(window.SS_API_BASE).trim().replace(/\/$/, '');
        if (injected) {
            return injected;
        }
    }

    const proto = window.location.protocol;
    const hostname = window.location.hostname;

    // file:// atau tidak ada host: fallback ke Node langsung (port legacy)
    if (proto === 'file:' || !hostname) {
        return `http://127.0.0.1:${BACKEND_API_PORT}/api`;
    }

    // Laravel di root domain saja (tanpa subpath)
    return `${window.location.origin}/api`;
}

const API_BASE_URL = resolveApiBaseUrl();

const ROLE_ALIAS_TO_KEY = {
    superadmin: 'owner',
    admin: 'admin_keuangan',
    'admin keuangan': 'admin_keuangan',
    'admin-keuangan': 'admin_keuangan',
    adminkeuangan: 'admin_keuangan',
    owner: 'owner',
    admin_keuangan: 'admin_keuangan',
    admin_kasir: 'admin_kasir',
    'admin kasir': 'admin_kasir',
    'admin-kasir': 'admin_kasir',
    adminkasir: 'admin_kasir',
    kasir: 'admin_kasir',
    teknisi: 'teknisi',
    penagih: 'penagih',
    tekpen: 'tekpen',
    teknisipenagih: 'tekpen'
};

const ROLE_KEY_TO_COMPAT = {
    owner: 'superadmin',
    admin_keuangan: 'admin',
    admin_kasir: 'admin',
    teknisi: 'teknisi',
    penagih: 'penagih',
    tekpen: 'tekpen'
};

const ROLE_PERMISSIONS = {
    owner: ['access_admin_app', 'view_finance_totals', 'view_finance_reports', 'collect_customer_payment', 'view_customer_bill_amount', 'manage_settings', 'manage_settings_wa', 'manage_users', 'manage_backup_audit', 'manage_tasks'],
    admin_keuangan: ['access_admin_app', 'view_finance_totals', 'view_finance_reports', 'collect_customer_payment', 'view_customer_bill_amount', 'manage_settings', 'manage_settings_wa', 'manage_users', 'manage_backup_audit', 'manage_tasks'],
    admin_kasir: ['access_admin_app', 'collect_customer_payment', 'view_customer_bill_amount', 'manage_tasks', 'manage_settings_wa']
};

function resolveRoleKey(role) {
    return ROLE_ALIAS_TO_KEY[String(role || '').trim().toLowerCase()] || String(role || '').trim().toLowerCase();
}

function toCompatRole(roleOrKey) {
    const key = resolveRoleKey(roleOrKey);
    return ROLE_KEY_TO_COMPAT[key] || key;
}

function getPermissionsByRole(roleOrKey) {
    const key = resolveRoleKey(roleOrKey);
    return [...new Set(ROLE_PERMISSIONS[key] || [])];
}

function hasPermission(profile, permissionName) {
    const key = resolveRoleKey(profile?.roleKey || profile?.role);
    const explicit = Array.isArray(profile?.permissions) ? profile.permissions : [];
    const source = [...new Set([...(getPermissionsByRole(key) || []), ...explicit])];
    return source.includes(permissionName);
}

function isAdminAppRole(profile) {
    return hasPermission(profile, 'access_admin_app');
}

function parseStoredUser() {
    try {
        return JSON.parse(
            localStorage.getItem('ss_user') ||
                sessionStorage.getItem('ss_user') ||
                localStorage.getItem('cnet_user') ||
                sessionStorage.getItem('cnet_user') ||
                'null'
        );
    } catch {
        return null;
    }
}

function normalizeProfile(profile, fallback = {}) {
    const src = profile || {};
    const fb = fallback || {};
    const id = src.id || src.uid || fb.id || fb.uid || null;
    const roleKey = resolveRoleKey(src.roleKey || src.role || fb.roleKey || fb.role);
    const explicitSrc = Array.isArray(src.permissions) ? src.permissions : [];
    const explicitFallback = Array.isArray(fb.permissions) ? fb.permissions : [];
    const permissions = [...new Set([...(getPermissionsByRole(roleKey) || []), ...explicitFallback, ...explicitSrc])];
    return {
        ...fb,
        ...src,
        id,
        uid: id,
        nama: src.nama || fb.nama || '',
        email: src.email || fb.email || '',
        role: toCompatRole(src.role || src.roleKey || fb.role || fb.roleKey || ''),
        roleKey,
        permissions,
        aktif: typeof src.aktif !== 'undefined' ? src.aktif : (typeof fb.aktif !== 'undefined' ? fb.aktif : 1),
        areas: (() => {
            const raw = src.areas !== undefined ? src.areas : fb.areas;
            if (Array.isArray(raw)) {
                return raw
                    .map(v => String(v || '').trim())
                    .filter(Boolean)
                    .filter(v => v.toLowerCase() !== '[object object]');
            }
            if (typeof raw === 'string' && raw.trim()) {
                try {
                    const p = JSON.parse(raw);
                    if (Array.isArray(p)) {
                        return p
                            .map(v => String(v || '').trim())
                            .filter(Boolean)
                            .filter(v => v.toLowerCase() !== '[object object]');
                    }
                } catch { }
                return raw.split(',')
                    .map(s => String(s || '').trim())
                    .filter(Boolean)
                    .filter(v => v.toLowerCase() !== '[object object]');
            }
            return [];
        })()
    };
}

// Helper untuk fetch API ber-token
async function apiFetch(endpoint, options = {}) {
    // Cek localStorage (ingat permanen) maupun sessionStorage (ingat sesi)
    const token =
        localStorage.getItem('ss_token') ||
        sessionStorage.getItem('ss_token') ||
        localStorage.getItem('cnet_token') ||
        sessionStorage.getItem('cnet_token');

    const headers = {
        'Content-Type': 'application/json',
        ...options.headers
    };

    if (token) {
        headers['Authorization'] = `Bearer ${token}`;
    }

    const response = await fetch(`${API_BASE_URL}${endpoint}`, {
        ...options,
        headers
    });

    const contentType = response.headers.get("content-type");
    let data = {};
    if (contentType && contentType.includes("application/json")) {
        try { data = await response.json(); } catch (e) { }
    } else {
        data = { error: await response.text() };
    }

    if (!response.ok) {
        if (response.status === 401) {
            // Pengecualian untuk Portal Pelanggan Publik yang tidak butuh token staff
            const isPublicRoute = window.location.pathname.includes('portal-') ||
                (window.location.pathname.includes('login.html') && endpoint.includes('/pelanggan')) ||
                endpoint.includes('/auth/client-login');

            if (!isPublicRoute) {
                // Sesi habis, arahkan ke login untuk Admin/Teknisi
                ['ss_token', 'ss_user', 'cnet_token', 'cnet_user'].forEach((k) => {
                    localStorage.removeItem(k);
                    sessionStorage.removeItem(k);
                });
                window.location.href = '/login';
            } else {
                // Jika route publik tapi Unauthorized, kita bisa lempar error biasa tanpa redirect
                throw new Error("Data tidak ditemukan atau akses ditolak.");
            }
        }
        if (response.status === 403) {
            // Forbidden = hak akses tidak cukup, jangan paksa logout sesi.
            throw new Error(data.error || 'Akses ditolak untuk resource ini.');
        }
        throw new Error(data.error || 'Terjadi kesalahan pada server');
    }

    return data;
}

// Simulasi Objek Auth (Mirip Firebase SDK agar minim rombak kode HTML)
const auth = {
    currentUser: normalizeProfile(parseStoredUser()),

    // Fungsi simulasi onAuthStateChanged Firebase
    onAuthStateChanged: (callback) => {
        const token =
            localStorage.getItem('ss_token') ||
            sessionStorage.getItem('ss_token') ||
            localStorage.getItem('cnet_token') ||
            sessionStorage.getItem('cnet_token');
        const locT = localStorage.getItem('ss_token') || localStorage.getItem('cnet_token');
        const sesT = sessionStorage.getItem('ss_token') || sessionStorage.getItem('cnet_token');
        const usingSession = !locT && !!sesT;
        if (token) {
            // Verifikasi token ke backend
            apiFetch('/auth/me')
                .then(user => {
                    const existing = parseStoredUser();
                    const normalized = normalizeProfile(user, existing);
                    // Simpan kembali ke storage yang sama (jangan pindah)
                    const targetStorage = usingSession ? sessionStorage : localStorage;
                    targetStorage.setItem('ss_user', JSON.stringify(normalized));
                    auth.currentUser = normalized;
                    callback({ uid: normalized.uid, email: normalized.email }); // Mock user object
                })
                .catch(() => {
                    callback(null);
                });
        } else {
            callback(null);
        }
    },

    // Fungsi log in (Email/Password)
    signInWithEmailAndPassword: async (email, password) => {
        const data = await apiFetch('/auth/login', {
            method: 'POST',
            body: JSON.stringify({ email, password })
        });
        const normalized = normalizeProfile(data.user);
        localStorage.setItem('ss_token', data.token);
        localStorage.setItem('ss_user', JSON.stringify(normalized));
        auth.currentUser = normalized;
        return { user: { uid: normalized.uid, email: normalized.email } };
    },

    // Fungsi Sign Out — hapus dari kedua storage
    signOut: async () => {
        ['ss_token', 'ss_user', 'cnet_token', 'cnet_user'].forEach((k) => {
            localStorage.removeItem(k);
            sessionStorage.removeItem(k);
        });
        window.location.href = '/logout';
    }
};

// Simulasi Objek Firestore (DB)
const db = {
    // collection: (name) => name, dsb dapat ditangani di halaman masing-masing (lihat langkah berikutnya)
};

export { auth, db, apiFetch, API_BASE_URL, resolveRoleKey, toCompatRole, getPermissionsByRole, hasPermission, isAdminAppRole, parseStoredUser };
