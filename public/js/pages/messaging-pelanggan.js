/**
 * Percakapan pelanggan (staf kantor) — layout split + polling.
 * Kantor: buka thread = klaim otomatis; pindah thread = lepas klaim sebelumnya.
 * Delegasi / info: ⋮, klik kanan baris, atau long-press (mobile).
 */
import { apiFetch } from '../../api-config.js';
import { showConfirm, showAlert, showToast } from '../utils/dialog.js';

const POLL_MS = 4500;

function hashHue(str) {
    let h = 0;
    for (let i = 0; i < String(str || '').length; i++) h = (h << 5) - h + str.charCodeAt(i);
    return Math.abs(h) % 360;
}

function isLapanganUi() {
    return document.body?.dataset?.messaging === 'lapangan';
}

function threadFieldReplyOk(t) {
    if (!t?.fieldPublicUntil) return false;
    const d = new Date(String(t.fieldPublicUntil).replace(' ', 'T'));
    if (Number.isNaN(d.getTime()) || d.getTime() <= Date.now()) return false;
    return Number(t.fieldPublicCanReply) === 1;
}

export function initMessagingPelangganPage() {
    const elList = document.getElementById('msgThreadList');
    const elDetail = document.getElementById('msgDetail');
    const elMessages = document.getElementById('msgMessages');
    const elInput = document.getElementById('msgInput');
    const elBanner = document.getElementById('msgAssignBanner');
    const btnSend = document.getElementById('msgBtnSend');
    const selDelegate = document.getElementById('msgDelegateSelect');
    const fldPublicUntil = document.getElementById('msgFieldUntil');
    const fldPublicReply = document.getElementById('msgFieldCanReply');
    const btnFieldSet = document.getElementById('msgFieldSet');
    const btnFieldClear = document.getElementById('msgFieldClear');
    const elContextMenu = document.getElementById('msgContextMenu');
    const elDelegateOverlay = document.getElementById('msgDelegateOverlay');
    const btnDelegateOk = document.getElementById('msgDelegateOk');
    const btnDelegateCancel = document.getElementById('msgDelegateCancel');

    let currentThreadId = null;
    let currentDetail = null;
    let myUserId = null;
    let pollTimer = null;
    let delegateTargetThreadId = null;
    let longPressTimer = null;

    async function loadMe() {
        try {
            const u = JSON.parse(localStorage.getItem('ss_user') || localStorage.getItem('cnet_user') || '{}');
            myUserId = u.id || u.uid || null;
            if (!myUserId) {
                const me = await apiFetch('/auth/me');
                myUserId = me?.id || me?.uid;
            }
        } catch {
            myUserId = null;
        }
    }

    function hideContextMenu() {
        if (!elContextMenu) return;
        elContextMenu.style.display = 'none';
        elContextMenu.setAttribute('aria-hidden', 'true');
        elContextMenu.innerHTML = '';
    }

    function hideDelegateModal() {
        if (elDelegateOverlay) elDelegateOverlay.style.display = 'none';
        delegateTargetThreadId = null;
    }

    function positionFixedMenu(x, y, el) {
        if (!el) return;
        const pad = 8;
        const w = el.offsetWidth || 220;
        const h = el.offsetHeight || 120;
        let left = x;
        let top = y;
        if (left + w > window.innerWidth - pad) left = window.innerWidth - w - pad;
        if (top + h > window.innerHeight - pad) top = window.innerHeight - h - pad;
        if (left < pad) left = pad;
        if (top < pad) top = pad;
        el.style.left = `${left}px`;
        el.style.top = `${top}px`;
    }

    function openThreadContextMenu(clientX, clientY, ctx) {
        if (!elContextMenu || isLapanganUi()) return;
        elContextMenu.innerHTML = `
            <button type="button" data-act="delegate">Delegasi ke staf…</button>
            <button type="button" data-act="info">Info pelanggan</button>
        `;
        elContextMenu.style.display = 'block';
        elContextMenu.setAttribute('aria-hidden', 'false');
        requestAnimationFrame(() => positionFixedMenu(clientX, clientY, elContextMenu));

        elContextMenu.querySelectorAll('button[data-act]').forEach((btn) => {
            btn.addEventListener('click', (ev) => {
                ev.stopPropagation();
                const act = btn.getAttribute('data-act');
                hideContextMenu();
                if (act === 'delegate') {
                    delegateTargetThreadId = ctx.threadId;
                    if (elDelegateOverlay) elDelegateOverlay.style.display = 'flex';
                } else if (act === 'info') {
                    showAlert({
                        title: 'Info pelanggan',
                        message: `Nama: ${escapeHtml(ctx.nama)}<br>ID pelanggan: ${escapeHtml(ctx.idPel)}<br>Thread: ${escapeHtml(ctx.threadId)}`,
                        type: 'info'
                    });
                }
            });
        });
    }

    function bindRowInteractions(row, ctx) {
        const main = row.querySelector('.msg-thread-main');
        const menuBtn = row.querySelector('.msg-thread-menu-btn');

        main?.addEventListener('click', () => selectThread(ctx.threadId));

        if (isLapanganUi() || !menuBtn) return;

        menuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            openThreadContextMenu(e.clientX, e.clientY, ctx);
        });

        row.addEventListener('contextmenu', (e) => {
            e.preventDefault();
            openThreadContextMenu(e.clientX, e.clientY, ctx);
        });

        let lpMoved = false;
        row.addEventListener(
            'touchstart',
            (e) => {
                lpMoved = false;
                const t = e.touches[0];
                longPressTimer = window.setTimeout(() => {
                    longPressTimer = null;
                    if (!lpMoved) {
                        e.preventDefault();
                        openThreadContextMenu(t.clientX, t.clientY, ctx);
                    }
                }, 480);
            },
            { passive: false }
        );
        row.addEventListener('touchmove', () => {
            lpMoved = true;
            if (longPressTimer) {
                clearTimeout(longPressTimer);
                longPressTimer = null;
            }
        });
        row.addEventListener('touchend', () => {
            if (longPressTimer) {
                clearTimeout(longPressTimer);
                longPressTimer = null;
            }
        });
        row.addEventListener('touchcancel', () => {
            if (longPressTimer) {
                clearTimeout(longPressTimer);
                longPressTimer = null;
            }
        });
    }

    function renderThreadList(rows) {
        if (!elList) return;
        if (!rows.length) {
            elList.innerHTML = '<div class="msg-empty">Belum ada percakapan.</div>';
            return;
        }
        const showOfficeMenu = !isLapanganUi();
        elList.innerHTML = rows
            .map((t) => {
                const active = t.id === currentThreadId ? 'active' : '';
                const assignHint = t.assignedName
                    ? `<span class="msg-tiny">${t.assignedUserId === myUserId ? 'Anda (klaim)' : '↪ ' + escapeHtml(t.assignedName)}</span>`
                    : '<span class="msg-tiny msg-tiny-warn">Belum ada yang klaim</span>';
                const menuBtn = showOfficeMenu
                    ? `<button type="button" class="msg-thread-menu-btn" aria-label="Menu thread">⋮</button>`
                    : '';
                return `<div class="msg-thread-row ${active}" data-id="${escapeAttr(t.id)}" data-id-pel="${escapeAttr(t.idPelanggan)}" data-nama="${escapeAttr(t.namaPelanggan || '')}">
                <button type="button" class="msg-thread-main">
                <div class="msg-thread-name">${escapeHtml(t.namaPelanggan || t.idPelanggan)}</div>
                <div class="msg-thread-sub">${escapeHtml(t.idPelanggan)} • ${assignHint}</div>
            </button>${menuBtn}</div>`;
            })
            .join('');

        elList.querySelectorAll('.msg-thread-row').forEach((row) => {
            const threadId = row.getAttribute('data-id');
            const idPel = row.getAttribute('data-id-pel') || '';
            const nama = row.getAttribute('data-nama') || idPel;
            bindRowInteractions(row, { threadId, idPel, nama });
        });
    }

    async function loadThreads() {
        const res = await apiFetch('/chat/threads');
        const rows = res.data || [];
        renderThreadList(rows);
        return rows;
    }

    function scrollMessagesBottom() {
        if (elMessages) elMessages.scrollTop = elMessages.scrollHeight;
    }

    function renderMessages(msgs, detail) {
        if (!elMessages) return;
        elMessages.innerHTML = (msgs || [])
            .map((m) => {
                const isStaff = m.senderType === 'staff';
                const mine = isStaff && m.senderUserId === myUserId;
                const hue = isStaff && m.senderUserId ? hashHue(m.senderUserId) : 210;
                const sub = isStaff
                    ? `<span class="msg-meta">${escapeHtml(m.senderName || 'Staf')} ${mine ? '(Anda)' : ''}</span>`
                    : '<span class="msg-meta">Pelanggan</span>';
                return `<div class="msg-bubble-wrap ${isStaff ? 'staff' : 'cust'} ${mine ? 'mine' : ''}">
                ${sub}
                <div class="msg-bubble" style="--hue:${hue}"><div class="msg-dot" style="background:hsl(${hue},70%,50%)"></div><div class="msg-text">${escapeHtml(m.body)}</div></div>
                <div class="msg-time">${formatTime(m.createdAt)}</div>
            </div>`;
            })
            .join('');
        scrollMessagesBottom();
    }

    function formatTime(s) {
        if (!s) return '';
        try {
            const d = new Date(String(s).replace(' ', 'T'));
            return d.toLocaleString('id-ID', { dateStyle: 'short', timeStyle: 'short' });
        } catch {
            return String(s);
        }
    }

    function escapeHtml(t) {
        return String(t ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }
    function escapeAttr(t) {
        return String(t ?? '')
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;');
    }

    function updateBanner(d) {
        if (!elBanner || !d?.data) return;
        const thr = d.data.thread;
        const asg = thr.assignedUserId;
        const asgName = d.data.assignedName;
        const parts = d.data.participants || [];
        const del = (thr.delegatedTo || []).length
            ? `<br>Delegasi: ${(d.data.delegated || []).map((x) => escapeHtml(x.nama)).join(', ')}`
            : '';
        if (!asg) {
            elBanner.innerHTML = `<i class="fas fa-info-circle"></i> Tidak ada yang mengklaim — chat bebas. Semua staf kantor dapat melihat &amp; membalas.${del}`;
            elBanner.className = 'msg-banner msg-banner-neutral';
            return;
        }
        const self = asg === myUserId;
        elBanner.innerHTML = `<i class="fas fa-user-headset"></i> Ditangani: <strong>${escapeHtml(asgName || '')}</strong>${self ? ' (Anda)' : ''}. ${parts.filter((p) => p.userId !== asg).length ? 'Ada ' + parts.filter((p) => p.userId !== asg).length + ' staf ikut balas.' : ''}${del}`;
        elBanner.className = 'msg-banner ' + (self ? 'msg-banner-me' : 'msg-banner-other');
    }

    async function selectThread(id) {
        if (!id) return;
        const prev = currentThreadId;
        const same = prev === id;

        if (!isLapanganUi()) {
            if (!same) {
                try {
                    if (prev) {
                        await apiFetch(`/chat/threads/${encodeURIComponent(prev)}/release`, { method: 'POST', body: '{}' });
                    }
                } catch (e) {
                    console.warn('release prev thread', e);
                }
                try {
                    await apiFetch(`/chat/threads/${encodeURIComponent(id)}/claim`, { method: 'POST', body: '{}' });
                } catch (e) {
                    showToast('Klaim otomatis gagal: ' + (e.message || ''), 'danger');
                }
            }
        }

        currentThreadId = id;
        hideContextMenu();
        await loadThreads();

        const det = await apiFetch(`/chat/threads/${encodeURIComponent(id)}`);
        currentDetail = det;
        const msgs = await apiFetch(`/chat/threads/${encodeURIComponent(id)}/messages`);
        renderMessages(msgs.data || [], det);
        updateBanner(det);
        const thr = det.data.thread;
        if (fldPublicUntil) {
            if (thr.fieldPublicUntil) {
                const iso = String(thr.fieldPublicUntil).replace(' ', 'T');
                fldPublicUntil.value = iso.slice(0, 16);
            } else fldPublicUntil.value = '';
        }
        if (fldPublicReply) fldPublicReply.checked = Number(thr.fieldPublicCanReply) === 1;
        document.querySelectorAll('.msg-thread-row').forEach((row) => {
            row.classList.toggle('active', row.getAttribute('data-id') === id);
        });
        elDetail?.classList.remove('msg-detail-empty');

        if (isLapanganUi()) {
            const ok = threadFieldReplyOk(thr);
            if (elInput) {
                elInput.disabled = !ok;
                elInput.placeholder = ok
                    ? 'Balasan ke pelanggan (nama Anda terlihat sesama staf di kantor; pelanggan melihat nama perusahaan).'
                    : 'Balasan dinonaktifkan admin — hanya baca.';
            }
            if (btnSend) btnSend.disabled = !ok;
            const note = document.getElementById('msgLapNote');
            if (note) {
                note.textContent = ok
                    ? 'Anda dapat membalas pada thread ini.'
                    : 'Mode baca saja: admin tidak mengizinkan balasan lapangan untuk thread ini, atau jendela waktu sudah lewat.';
            }
        }
    }

    async function sendMessage(ack = false) {
        const text = String(elInput?.value || '').trim();
        if (!text || !currentThreadId) return;
        try {
            await apiFetch(`/chat/threads/${encodeURIComponent(currentThreadId)}/messages`, {
                method: 'POST',
                body: JSON.stringify({ body: text, acknowledgeSecondParticipant: ack })
            });
            elInput.value = '';
            const msgs = await apiFetch(`/chat/threads/${encodeURIComponent(currentThreadId)}/messages`);
            currentDetail = await apiFetch(`/chat/threads/${encodeURIComponent(currentThreadId)}`);
            renderMessages(msgs.data || [], currentDetail);
            updateBanner(currentDetail);
            await loadThreads();
            showToast('Pesan terkirim', 'success');
        } catch (e) {
            const msg = e.message || '';
            if (msg.includes('SECOND_PARTICIPANT')) {
                const ok = await showConfirm({
                    title: 'Admin lain aktif',
                    message:
                        'Percakapan ini sedang ditangani staf lain. Lanjutkan ikut membalas? Setelah kirim, Anda ikut dalam thread.',
                    type: 'warning',
                    confirmText: 'Ya, kirim'
                });
                if (ok) await sendMessage(true);
                return;
            }
            showAlert({ title: 'Gagal', message: msg, type: 'danger' });
        }
    }

    btnSend?.addEventListener('click', () => sendMessage(false));
    elInput?.addEventListener('keydown', (ev) => {
        if (ev.key === 'Enter' && !ev.shiftKey) {
            ev.preventDefault();
            sendMessage(false);
        }
    });

    btnDelegateOk?.addEventListener('click', async () => {
        const tid = delegateTargetThreadId;
        if (!tid || !selDelegate?.value) {
            showAlert({ title: 'Pilih staf', message: 'Pilih staf tujuan delegasi.', type: 'warning' });
            return;
        }
        try {
            await apiFetch(`/chat/threads/${encodeURIComponent(tid)}/delegate`, {
                method: 'POST',
                body: JSON.stringify({ userIds: [selDelegate.value] })
            });
            hideDelegateModal();
            showToast('Delegasi tercatat', 'success');
            if (currentThreadId === tid) await selectThread(tid);
            else await loadThreads();
        } catch (e) {
            showAlert({ title: 'Gagal', message: e.message, type: 'danger' });
        }
    });

    btnDelegateCancel?.addEventListener('click', () => hideDelegateModal());

    elDelegateOverlay?.addEventListener('click', (e) => {
        if (e.target === elDelegateOverlay) hideDelegateModal();
    });

    document.addEventListener(
        'click',
        (e) => {
            if (!elContextMenu || elContextMenu.style.display !== 'block') return;
            if (elContextMenu.contains(e.target)) return;
            hideContextMenu();
        },
        true
    );

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            hideContextMenu();
            hideDelegateModal();
        }
    });

    btnFieldSet?.addEventListener('click', async () => {
        if (!currentThreadId || !fldPublicUntil?.value) {
            showAlert({ title: 'Perlu tanggal', message: 'Isi sampai kapan mode publik lapangan.', type: 'warning' });
            return;
        }
        const iso = new Date(fldPublicUntil.value);
        if (Number.isNaN(iso.getTime())) return;
        try {
            await apiFetch(`/chat/threads/${encodeURIComponent(currentThreadId)}/field-public`, {
                method: 'POST',
                body: JSON.stringify({
                    until: iso.toISOString(),
                    canReply: !!fldPublicReply?.checked
                })
            });
            showToast('Mode publik lapangan disimpan', 'success');
            await selectThread(currentThreadId);
        } catch (e) {
            showAlert({ title: 'Gagal', message: e.message, type: 'danger' });
        }
    });

    btnFieldClear?.addEventListener('click', async () => {
        if (!currentThreadId) return;
        const ok = await showConfirm({
            title: 'Matikan publik lapangan?',
            message: 'Teknisi tidak akan melihat thread ini lagi setelah dimatikan.',
            type: 'warning',
            confirmText: 'Ya, matikan'
        });
        if (!ok) return;
        try {
            await apiFetch(`/chat/threads/${encodeURIComponent(currentThreadId)}/field-public`, {
                method: 'POST',
                body: JSON.stringify({ until: null, canReply: false })
            });
            showToast('Mode publik dimatikan', 'success');
            await selectThread(currentThreadId);
        } catch (e) {
            showAlert({ title: 'Gagal', message: e.message, type: 'danger' });
        }
    });

    async function fillDelegates() {
        if (!selDelegate) return;
        try {
            const r = await apiFetch('/chat/office-users');
            const opts = (r.data || [])
                .map((u) => `<option value="${escapeAttr(u.id)}">${escapeHtml(u.nama)}</option>`)
                .join('');
            selDelegate.innerHTML = '<option value="">— Pilih staf —</option>' + opts;
        } catch {
            selDelegate.innerHTML = '<option value="">(gagal memuat)</option>';
        }
    }

    async function start() {
        await loadMe();
        await fillDelegates();
        await loadThreads();
        pollTimer = setInterval(async () => {
            try {
                await loadThreads();
                if (currentThreadId) {
                    const msgs = await apiFetch(`/chat/threads/${encodeURIComponent(currentThreadId)}/messages`);
                    currentDetail = await apiFetch(`/chat/threads/${encodeURIComponent(currentThreadId)}`);
                    renderMessages(msgs.data || [], currentDetail);
                    updateBanner(currentDetail);
                }
            } catch {
                /* */
            }
        }, POLL_MS);
    }

    start();

    window.addEventListener('beforeunload', () => {
        if (pollTimer) clearInterval(pollTimer);
    });
}
