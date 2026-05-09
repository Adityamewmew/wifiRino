/**
 * Sans Speed — Custom Dialog (premium)
 * Menggantikan browser native confirm() dan alert()
 * Desain Cyberpunk dark mode, responsif semua perangkat
 */

// Inject CSS sekali saja
function _injectDialogStyles() {
    if (document.getElementById('cnet-dialog-style')) return;
    const style = document.createElement('style');
    style.id = 'cnet-dialog-style';
    style.textContent = `
        .cnet-overlay {
            position: fixed; inset: 0; z-index: 999999;
            background: rgba(0,0,0,0.65);
            backdrop-filter: blur(4px);
            display: flex; align-items: center; justify-content: center;
            padding: 16px;
            animation: cnet-fadeIn 0.18s ease;
        }
        .cnet-dialog {
            background: var(--card-bg, #0f172a);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px;
            box-shadow: 0 24px 60px rgba(0,0,0,0.6), 0 0 0 1px rgba(255,255,255,0.04);
            max-width: 420px;
            width: 100%;
            padding: 28px 28px 24px;
            animation: cnet-slideUp 0.22s cubic-bezier(0.34,1.56,0.64,1);
            position: relative;
            overflow: hidden;
        }
        .cnet-dialog::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 2px;
            background: var(--cnet-dialog-accent, linear-gradient(90deg, #6366f1, #8b5cf6, #06b6d4));
        }
        .cnet-dialog-icon {
            width: 52px; height: 52px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; margin-bottom: 16px;
        }
        .cnet-dialog-title {
            font-size: 17px; font-weight: 700;
            color: var(--text-primary, #f1f5f9);
            margin-bottom: 8px; line-height: 1.3;
        }
        .cnet-dialog-msg {
            font-size: 13.5px; color: var(--text-secondary, #94a3b8);
            line-height: 1.6; margin-bottom: 24px;
        }
        .cnet-dialog-actions {
            display: flex; gap: 10px; justify-content: flex-end;
            flex-wrap: wrap;
        }
        .cnet-btn {
            padding: 10px 22px; border-radius: 10px; border: none;
            font-size: 13px; font-weight: 600; cursor: pointer;
            transition: all 0.18s; display: inline-flex; align-items: center; gap: 7px;
            white-space: nowrap;
        }
        .cnet-btn:active { transform: scale(0.96); }
        .cnet-btn-cancel {
            background: rgba(255,255,255,0.07);
            color: var(--text-secondary, #94a3b8);
            border: 1px solid rgba(255,255,255,0.08);
        }
        .cnet-btn-cancel:hover { background: rgba(255,255,255,0.12); color: #f1f5f9; }
        .cnet-btn-confirm { color: #fff; }
        .cnet-btn-confirm:hover { filter: brightness(1.15); }
        .cnet-btn-danger { background: linear-gradient(135deg, #ef4444, #dc2626); }
        .cnet-btn-warning { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .cnet-btn-success { background: linear-gradient(135deg, #10b981, #059669); }
        .cnet-btn-info { background: linear-gradient(135deg, #3b82f6, #2563eb); }
        .cnet-btn-primary { background: linear-gradient(135deg, #6366f1, #4f46e5); }
        
        /* Light mode */
        html.light-mode .cnet-dialog {
            background: #ffffff;
            border-color: rgba(0,0,0,0.08);
            box-shadow: 0 16px 50px rgba(0,0,0,0.15);
        }
        html.light-mode .cnet-dialog-title { color: #0f172a; }
        html.light-mode .cnet-dialog-msg { color: #64748b; }
        html.light-mode .cnet-btn-cancel {
            background: #f1f5f9; color: #475569;
            border-color: #e2e8f0;
        }
        html.light-mode .cnet-btn-cancel:hover { background: #e2e8f0; }

        @keyframes cnet-fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes cnet-slideUp {
            from { opacity: 0; transform: translateY(32px) scale(0.96); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }
        @media (max-width: 480px) {
            .cnet-dialog { padding: 22px 18px 20px; border-radius: 16px; }
            .cnet-dialog-actions { flex-direction: column-reverse; }
            .cnet-btn { width: 100%; justify-content: center; }
        }
    `;
    document.head.appendChild(style);
}

/**
 * Konfigurasi tipe dialog
 */
const DIALOG_TYPES = {
    danger: {
        icon: '🗑️', iconBg: 'rgba(239,68,68,0.12)', iconColor: '#ef4444',
        accent: 'linear-gradient(90deg, #ef4444, #f97316)',
        btnClass: 'cnet-btn-danger', btnLabel: 'Ya, Hapus'
    },
    warning: {
        icon: '⚠️', iconBg: 'rgba(245,158,11,0.12)', iconColor: '#f59e0b',
        accent: 'linear-gradient(90deg, #f59e0b, #f97316)',
        btnClass: 'cnet-btn-warning', btnLabel: 'Ya, Lanjutkan'
    },
    success: {
        icon: '✅', iconBg: 'rgba(16,185,129,0.12)', iconColor: '#10b981',
        accent: 'linear-gradient(90deg, #10b981, #06b6d4)',
        btnClass: 'cnet-btn-success', btnLabel: 'Konfirmasi'
    },
    info: {
        icon: 'ℹ️', iconBg: 'rgba(59,130,246,0.12)', iconColor: '#3b82f6',
        accent: 'linear-gradient(90deg, #3b82f6, #8b5cf6)',
        btnClass: 'cnet-btn-info', btnLabel: 'Oke'
    },
    confirm: {
        icon: '❓', iconBg: 'rgba(99,102,241,0.12)', iconColor: '#6366f1',
        accent: 'linear-gradient(90deg, #6366f1, #8b5cf6)',
        btnClass: 'cnet-btn-primary', btnLabel: 'Ya, Lanjutkan'
    }
};

/**
 * Custom confirm dialog — menggantikan window.confirm()
 * @param {Object} options
 * @param {string} options.title - Judul dialog
 * @param {string} options.message - Pesan / body
 * @param {string} [options.type] - 'danger' | 'warning' | 'success' | 'info' | 'confirm'
 * @param {string} [options.confirmText] - Label tombol konfirmasi
 * @param {string} [options.cancelText] - Label tombol batal
 * @returns {Promise<boolean>}
 */
export function showConfirm({ title, message, type = 'confirm', confirmText, cancelText = 'Batal' }) {
    _injectDialogStyles();
    const cfg = DIALOG_TYPES[type] || DIALOG_TYPES.confirm;
    const btnLabel = confirmText || cfg.btnLabel;

    return new Promise((resolve) => {
        const overlay = document.createElement('div');
        overlay.className = 'cnet-overlay';
        overlay.innerHTML = `
            <div class="cnet-dialog" role="dialog" aria-modal="true" style="--cnet-dialog-accent: ${cfg.accent}">
                <div class="cnet-dialog-icon" style="background:${cfg.iconBg}; color:${cfg.iconColor};">
                    ${cfg.icon}
                </div>
                <div class="cnet-dialog-title">${title}</div>
                <div class="cnet-dialog-msg">${message}</div>
                <div class="cnet-dialog-actions">
                    <button class="cnet-btn cnet-btn-cancel" id="cnet-cancel-btn">
                        <i class="fas fa-times"></i> ${cancelText}
                    </button>
                    <button class="cnet-btn cnet-btn-confirm ${cfg.btnClass}" id="cnet-confirm-btn">
                        <i class="fas fa-check"></i> ${btnLabel}
                    </button>
                </div>
            </div>
        `;

        const close = (result) => {
            overlay.style.animation = 'cnet-fadeIn 0.15s ease reverse';
            setTimeout(() => { overlay.remove(); resolve(result); }, 140);
        };

        overlay.querySelector('#cnet-confirm-btn').addEventListener('click', () => close(true));
        overlay.querySelector('#cnet-cancel-btn').addEventListener('click', () => close(false));
        overlay.addEventListener('click', (e) => { if (e.target === overlay) close(false); });
        document.addEventListener('keydown', function esc(e) {
            if (e.key === 'Escape') { close(false); document.removeEventListener('keydown', esc); }
        });

        document.body.appendChild(overlay);
        // Focus tombol konfirmasi
        setTimeout(() => overlay.querySelector('#cnet-confirm-btn').focus(), 50);
    });
}

/**
 * Custom alert dialog — menggantikan window.alert()
 * @param {Object} options
 * @param {string} options.title
 * @param {string} options.message
 * @param {string} [options.type]
 */
export function showAlert({ title, message, type = 'info', confirmText = 'Oke' }) {
    _injectDialogStyles();
    const cfg = DIALOG_TYPES[type] || DIALOG_TYPES.info;

    return new Promise((resolve) => {
        const overlay = document.createElement('div');
        overlay.className = 'cnet-overlay';
        overlay.innerHTML = `
            <div class="cnet-dialog" role="alertdialog" aria-modal="true" style="--cnet-dialog-accent: ${cfg.accent}">
                <div class="cnet-dialog-icon" style="background:${cfg.iconBg}; color:${cfg.iconColor};">
                    ${cfg.icon}
                </div>
                <div class="cnet-dialog-title">${title}</div>
                <div class="cnet-dialog-msg">${message}</div>
                <div class="cnet-dialog-actions">
                    <button class="cnet-btn cnet-btn-confirm ${cfg.btnClass}" id="cnet-ok-btn">
                        <i class="fas fa-check"></i> ${confirmText}
                    </button>
                </div>
            </div>
        `;

        const close = () => {
            overlay.style.animation = 'cnet-fadeIn 0.15s ease reverse';
            setTimeout(() => { overlay.remove(); resolve(); }, 140);
        };

        overlay.querySelector('#cnet-ok-btn').addEventListener('click', close);
        overlay.addEventListener('click', (e) => { if (e.target === overlay) close(); });
        document.addEventListener('keydown', function esc(e) {
            if (e.key === 'Enter' || e.key === 'Escape') { close(); document.removeEventListener('keydown', esc); }
        });

        document.body.appendChild(overlay);
        setTimeout(() => overlay.querySelector('#cnet-ok-btn').focus(), 50);
    });
}

/**
 * Custom prompt dialog — menggantikan window.prompt()
 * @param {Object} options
 * @param {string} options.title
 * @param {string} options.message
 * @param {string} [options.type]
 * @param {string} [options.inputType] - 'text' | 'password'
 * @param {string} [options.placeholder]
 * @param {string} [options.confirmText]
 * @param {string} [options.cancelText]
 * @returns {Promise<string|null>}
 */
export function showPrompt({ title, message, type = 'confirm', inputType = 'text', placeholder = '', confirmText = 'Lanjut', cancelText = 'Batal' }) {
    _injectDialogStyles();
    if (!document.getElementById('cnet-prompt-style')) {
        const style = document.createElement('style');
        style.id = 'cnet-prompt-style';
        style.textContent = `
            .cnet-dialog-input {
                width: 100%; padding: 12px 16px; border-radius: 10px;
                border: 1px solid rgba(255,255,255,0.15);
                background: rgba(0,0,0,0.2);
                color: var(--text-primary, #f1f5f9);
                font-size: 14px; margin-bottom: 24px;
                transition: all 0.2s;
                outline: none;
                box-sizing: border-box;
            }
            .cnet-dialog-input:focus {
                border-color: #6366f1;
                box-shadow: 0 0 0 3px rgba(99,102,241,0.2);
            }
            html.light-mode .cnet-dialog-input {
                background: #f8fafc; border-color: #cbd5e1; color: #0f172a;
            }
            html.light-mode .cnet-dialog-input:focus {
                border-color: #6366f1;
                background: #fff;
            }
        `;
        document.head.appendChild(style);
    }

    const cfg = DIALOG_TYPES[type] || DIALOG_TYPES.confirm;

    return new Promise((resolve) => {
        const overlay = document.createElement('div');
        overlay.className = 'cnet-overlay';
        overlay.innerHTML = `
            <div class="cnet-dialog" role="dialog" aria-modal="true" style="--cnet-dialog-accent: ${cfg.accent}">
                <div class="cnet-dialog-icon" style="background:${cfg.iconBg}; color:${cfg.iconColor};">
                    ${cfg.icon}
                </div>
                <div class="cnet-dialog-title">${title}</div>
                <div class="cnet-dialog-msg" style="margin-bottom: 16px;">${message}</div>
                <input type="${inputType}" class="cnet-dialog-input" id="cnet-prompt-input" placeholder="${placeholder}" autocomplete="off">
                <div class="cnet-dialog-actions">
                    <button class="cnet-btn cnet-btn-cancel" id="cnet-cancel-btn">
                        <i class="fas fa-times"></i> ${cancelText}
                    </button>
                    <button class="cnet-btn cnet-btn-confirm ${cfg.btnClass}" id="cnet-confirm-btn">
                        <i class="fas fa-check"></i> ${confirmText}
                    </button>
                </div>
            </div>
        `;

        const close = (result) => {
            overlay.style.animation = 'cnet-fadeIn 0.15s ease reverse';
            setTimeout(() => { overlay.remove(); resolve(result); }, 140);
        };

        const input = overlay.querySelector('#cnet-prompt-input');

        overlay.querySelector('#cnet-confirm-btn').addEventListener('click', () => close(input.value));
        overlay.querySelector('#cnet-cancel-btn').addEventListener('click', () => close(null));
        overlay.addEventListener('click', (e) => { if (e.target === overlay) close(null); });

        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') close(input.value);
            if (e.key === 'Escape') close(null);
        });

        document.addEventListener('keydown', function esc(e) {
            if (e.key === 'Escape') { close(null); document.removeEventListener('keydown', esc); }
        });

        document.body.appendChild(overlay);
        setTimeout(() => input.focus(), 50);
    });
}

/**
 * Toast ringan (notif cepat tanpa dialog) - juga tersedia
 */
export function showToast(message, type = 'success') {
    const existing = document.getElementById('cnet-main-toast');
    if (existing) existing.remove();
    const colors = { success: '#10b981', error: '#ef4444', warning: '#f59e0b', info: '#3b82f6' };
    const icons = { success: 'check-circle', error: 'times-circle', warning: 'exclamation-triangle', info: 'info-circle' };
    _injectDialogStyles();
    const t = document.createElement('div');
    t.id = 'cnet-main-toast';
    t.style.cssText = `position:fixed;bottom:24px;right:24px;z-index:99999;background:${colors[type] || '#10b981'};color:#fff;padding:12px 18px;border-radius:12px;box-shadow:0 8px 25px rgba(0,0,0,0.3);display:flex;align-items:center;gap:10px;font-size:14px;font-weight:500;animation:cnet-slideToast 0.3s ease;max-width:350px;`;
    t.innerHTML = `<i class="fas fa-${icons[type] || 'check-circle'}"></i> <span>${message}</span>`;
    if (!document.getElementById('cnet-toast-kf')) {
        const s = document.createElement('style');
        s.id = 'cnet-toast-kf';
        s.textContent = `@keyframes cnet-slideToast{from{transform:translateX(120%);opacity:0}to{transform:translateX(0);opacity:1}}`;
        document.head.appendChild(s);
    }
    document.body.appendChild(t);
    setTimeout(() => { t.style.transition = 'all 0.3s'; t.style.transform = 'translateX(120%)'; t.style.opacity = '0'; setTimeout(() => t.remove(), 300); }, 3500);
}
