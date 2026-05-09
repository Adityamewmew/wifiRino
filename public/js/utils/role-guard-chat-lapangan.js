/**
 * Akses messaging lapangan: teknisi, penagih, tekpen (token staff).
 */
import { resolveRoleKey, isAdminAppRole } from '../../api-config.js';

const FIELD_KEYS = new Set(['teknisi', 'penagih', 'tekpen']);

export function guardChatLapangan() {
    const token =
        localStorage.getItem('ss_token') ||
        sessionStorage.getItem('ss_token') ||
        localStorage.getItem('cnet_token') ||
        sessionStorage.getItem('cnet_token');
    const stored = localStorage.getItem('ss_user') || localStorage.getItem('cnet_user');
    if (!token || !stored) {
        window.location.replace('/login');
        return false;
    }
    try {
        const profile = JSON.parse(stored);
        const roleKey = resolveRoleKey(profile.roleKey || profile.role);
        if (FIELD_KEYS.has(roleKey)) return true;
        if (isAdminAppRole(profile)) {
            window.location.replace('/messaging-pelanggan');
            return false;
        }
        window.location.replace('/app-teknisi');
        return false;
    } catch {
        window.location.replace('/login');
        return false;
    }
}
