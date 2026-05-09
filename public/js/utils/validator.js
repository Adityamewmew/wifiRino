/**
 * Sans Speed — Form Validation Utility
 * Reusable validation and UI feedback helpers
 */

/**
 * Show an inline error below a form field
 * @param {string} inputId - ID of the input element
 * @param {string} message - Error message to display
 */
export function showFieldError(inputId, message) {
    const input = document.getElementById(inputId);
    if (!input) return;
    clearFieldError(inputId);
    input.style.borderColor = '#ef4444';
    input.style.boxShadow = '0 0 0 2px rgba(239,68,68,0.25)';
    const errEl = document.createElement('div');
    errEl.id = `err-${inputId}`;
    errEl.style.cssText = 'color:#ef4444;font-size:11px;margin-top:4px;display:flex;align-items:center;gap:4px;';
    errEl.innerHTML = `<i class="fas fa-exclamation-circle"></i> ${message}`;
    input.parentNode.appendChild(errEl);
}

/**
 * Clear an inline error from a form field
 */
export function clearFieldError(inputId) {
    const input = document.getElementById(inputId);
    if (input) {
        input.style.borderColor = '';
        input.style.boxShadow = '';
    }
    const errEl = document.getElementById(`err-${inputId}`);
    if (errEl) errEl.remove();
}

/**
 * Clear all field errors in a form
 */
export function clearAllErrors(formId) {
    const form = document.getElementById(formId);
    if (!form) return;
    form.querySelectorAll('[style*="border-color"]').forEach(el => {
        el.style.borderColor = '';
        el.style.boxShadow = '';
    });
    form.querySelectorAll('[id^="err-"]').forEach(el => el.remove());
}

/**
 * Validate a phone number (Indonesian format: 08xx or 62xx)
 */
export function isValidPhone(phone) {
    if (!phone) return false;
    const cleaned = phone.replace(/[\s\-()]/g, '');
    return /^(08|62)\d{8,12}$/.test(cleaned);
}

/**
 * Normalize a phone number to 08xx format
 */
export function normalizePhone(phone) {
    const cleaned = phone.replace(/[\s\-()]/g, '');
    if (cleaned.startsWith('62')) return '0' + cleaned.slice(2);
    return cleaned;
}

/**
 * Show a toast notification (non-blocking feedback)
 */
export function showToast(message, type = 'success') {
    const existing = document.getElementById('ss-toast');
    if (existing) existing.remove();

    const icons = { success: 'check-circle', error: 'times-circle', warning: 'exclamation-triangle', info: 'info-circle' };
    const colors = { success: '#10b981', error: '#ef4444', warning: '#f59e0b', info: '#3b82f6' };

    const toast = document.createElement('div');
    toast.id = 'SS-toast';
    toast.style.cssText = `
        position:fixed; bottom:24px; right:24px; z-index:99999;
        background:${colors[type]}; color:white;
        padding:12px 18px; border-radius:10px;
        box-shadow:0 8px 25px rgba(0,0,0,0.3);
        display:flex; align-items:center; gap:10px;
        font-size:14px; font-weight:500;
        animation: slideInRight 0.3s ease;
        max-width: 360px;
    `;
    toast.innerHTML = `<i class="fas fa-${icons[type]}"></i><span>${message}</span>`;
    if (!document.getElementById('toast-style')) {
        const style = document.createElement('style');
        style.id = 'toast-style';
        style.textContent = `
            @keyframes slideInRight {
                from { transform: translateX(120%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOutRight {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(120%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    }
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease forwards';
        setTimeout(() => toast.remove(), 300);
    }, 3500);
}

/**
 * Validate a required text field (not empty, min length)
 */
export function validateRequired(inputId, label, minLength = 2) {
    const val = (document.getElementById(inputId)?.value || '').trim();
    if (!val) { showFieldError(inputId, `${label} tidak boleh kosong`); return false; }
    if (val.length < minLength) { showFieldError(inputId, `${label} minimal ${minLength} karakter`); return false; }
    clearFieldError(inputId);
    return true;
}

/**
 * Validate a numeric field (positive number)
 */
export function validatePositiveNumber(inputId, label) {
    const val = parseFloat((document.getElementById(inputId)?.value || '').replace(/[^0-9.]/g, ''));
    if (isNaN(val) || val <= 0) { showFieldError(inputId, `${label} harus berupa angka lebih dari 0`); return false; }
    clearFieldError(inputId);
    return true;
}

/**
 * Validate a select/dropdown (must not be empty or default)
 */
export function validateSelect(inputId, label) {
    const val = document.getElementById(inputId)?.value || '';
    if (!val || val === '' || val === '-') { showFieldError(inputId, `${label} harus dipilih`); return false; }
    clearFieldError(inputId);
    return true;
}
