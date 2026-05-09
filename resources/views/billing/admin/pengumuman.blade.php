<!DOCTYPE html>
<html lang="id">

<head>
    <script src="{{ asset('js/ss-storage-migrate.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siaran Pengumuman Sans Speed</title>
    <!-- Styles CSS Utama -->
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <script>
        if (localStorage.getItem('ss_theme') === 'light') {
            document.documentElement.classList.add('light-mode');
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .page-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--text-primary);
            margin: 0 0 5px 0;
            font-family: 'Outfit', sans-serif;
            letter-spacing: 0.5px;
        }

        .page-subtitle {
            font-size: 14px;
            color: var(--text-secondary);
            margin-bottom: 24px;
        }

        body.light-mode .page-title {
            color: #0f172a;
        }

        /* CARD FORM */
        .glass-card {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
        }

        body.light-mode .glass-card {
            background: #ffffff;
            border-color: #e2e8f0;
            box-shadow: 0 10px 30px rgba(148, 163, 184, 0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
        }

        body.light-mode .form-label {
            color: #334155;
        }

        .form-input,
        .form-select,
        .form-textarea {
            width: 100%;
            padding: 14px 16px;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            transition: all 0.3s;
        }

        body.light-mode .form-input,
        body.light-mode .form-select,
        body.light-mode .form-textarea {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
        }

        .form-input:focus,
        .form-select:focus,
        .form-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            background: rgba(15, 23, 42, 0.9);
        }

        body.light-mode .form-input:focus,
        body.light-mode .form-select:focus,
        body.light-mode .form-textarea:focus {
            background: white;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .btn-blast {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 14px 24px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 15px;
            font-family: 'Outfit', sans-serif;
            cursor: pointer;
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-blast:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(59, 130, 246, 0.4);
        }

        .btn-blast:active {
            transform: translateY(1px);
        }

        /* HISTORY LIST */
        .h-item {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 15px;
            transition: transform 0.2s;
        }

        body.light-mode .h-item {
            background: #f8fafc;
            border-color: #e2e8f0;
        }

        .h-item:hover {
            transform: translateX(5px);
            background: rgba(255, 255, 255, 0.06);
        }

        body.light-mode .h-item:hover {
            background: #f1f5f9;
        }

        .h-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .h-title {
            font-weight: 700;
            font-size: 15px;
            color: var(--primary);
            margin: 0 0 5px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .h-date {
            font-size: 12px;
            color: var(--text-secondary);
        }

        .h-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: rgba(139, 92, 246, 0.2);
            color: #c4b5fd;
            border: 1px solid rgba(139, 92, 246, 0.4);
        }

        body.light-mode .h-badge {
            background: #ede9fe;
            color: #8b5cf6;
            border-color: #ddd6fe;
        }

        .h-body {
            font-size: 14px;
            color: var(--text-primary);
            line-height: 1.6;
        }

        body.light-mode .h-body {
            color: #334155;
        }

        .btn-del {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            border: none;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.2s;
            margin-top: 15px;
        }

        .btn-del:hover {
            background: #ef4444;
            color: white;
        }

        .preview-box {
            background: #000;
            border-radius: 16px;
            padding: 15px;
            border: 2px solid #3b82f6;
            margin-top: 15px;
            position: relative;
        }

        .preview-box::before {
            content: 'PREVIEW APP PELANGGAN';
            position: absolute;
            top: -10px;
            left: 15px;
            background: #3b82f6;
            color: white;
            font-size: 10px;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 10px;
            letter-spacing: 1px;
        }

        .p-marquee {
            color: white;
            font-size: 13px;
            white-space: nowrap;
            overflow: hidden;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        @media (max-width: 768px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }

        .schedule-quick {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-top: 10px;
        }

        @media (max-width: 768px) {
            .schedule-quick {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
@include('billing.partials.web-bootstrap')

    <div class="app-container">
        <!-- SIDEBAR -->
        <aside id="app-sidebar"></aside>

        <!-- MAIN CONTENT -->
        <main class="main-content">
            <!-- HEADER -->
            <header id="app-header"></header>

            <!-- CONTENT WRAPPER -->
            <div class="content-wrapper">

                <h1 class="page-title"><i class="fas fa-bullhorn" style="color:var(--primary); margin-right:10px;"></i>
                    Siaran Pusat Layanan</h1>
                <p class="page-subtitle">Kirim pengumuman pemeliharaan jaringan, tagihan, atau promo yang akan tampil
                    langsung di Aplikasi Pelanggan.</p>

                <div class="grid-2">
                    <!-- KOLOM 1: FORM -->
                    <div>
                        <div class="glass-card">
                            <h3 style="margin-top:0; font-family:'Outfit'; color:var(--text-primary);">Buat Pengumuman
                                Baru</h3>
                            <hr style="border:0; border-bottom:1px solid rgba(255,255,255,0.1); margin:15px 0 20px 0;">

                            <div class="form-group">
                                <label class="form-label">Mode Target Siaran</label>
                                <select id="inputTargetType" class="form-select">
                                    <option value="global">🌐 Semua Pelanggan (Global Broadcast)</option>
                                    <option value="area">📍 Per Area/Wilayah</option>
                                    <option value="pelanggan">👤 Pilih Nama Pelanggan</option>
                                </select>
                            </div>

                            <div class="form-group" id="groupAreaTarget" style="display:none;">
                                <label class="form-label">Target Penerima (Area)</label>
                                <select id="inputArea" class="form-select">
                                    <option value="">Pilih area...</option>
                                    <!-- Options injected via JS -->
                                </select>
                            </div>

                            <div class="form-group" id="groupPelangganTarget" style="display:none;">
                                <label class="form-label">Pilih Pelanggan Tujuan</label>
                                <input id="inputCariPelanggan" class="form-input" placeholder="Cari nama / ID pelanggan..." style="margin-bottom:8px;">
                                <div id="targetPelangganList" style="max-height:170px; overflow:auto; border:1px solid rgba(255,255,255,0.1); border-radius:10px; padding:8px;"></div>
                                <div id="targetPelangganInfo" style="font-size:12px; color:var(--text-secondary); margin-top:6px;">Belum ada pelanggan dipilih.</div>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Isi Pesan Siaran</label>
                                <textarea id="inputPesan" class="form-textarea" rows="4"
                                    placeholder="Contoh: Yth Pelanggan Sans Speed, akan ada perbaikan fiber optik di jalur utama pada hari Minggu jam 10:00. Mohon maaf atas ketidaknyamanannya..."></textarea>
                                <div
                                    style="font-size:11px; color:var(--text-secondary); margin-top:5px; text-align:right;">
                                    Maks 200 karakter disarankan</div>
                            </div>

                            <div class="form-group" style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
                                <div>
                                    <label class="form-label">Mulai Tayang (Opsional)</label>
                                    <input id="inputStartAt" type="datetime-local" class="form-input">
                                </div>
                                <div>
                                    <label class="form-label">Berakhir / Expired (Opsional)</label>
                                    <input id="inputEndAt" type="datetime-local" class="form-input">
                                </div>
                            </div>
                            <div class="schedule-quick">
                                <label style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--text-secondary);">
                                    <input type="checkbox" id="inputStartNow"> Mulai sekarang
                                </label>
                                <select id="inputQuickDuration" class="form-select">
                                    <option value="">Durasi cepat (opsional)</option>
                                    <option value="60">+ 1 jam</option>
                                    <option value="180">+ 3 jam</option>
                                    <option value="360">+ 6 jam</option>
                                    <option value="720">+ 12 jam</option>
                                    <option value="1440">+ 1 hari</option>
                                    <option value="4320">+ 3 hari</option>
                                    <option value="10080">+ 7 hari</option>
                                </select>
                            </div>

                            <!-- Live Preview Mockup -->
                            <div class="preview-box">
                                <div class="p-marquee">
                                    <i class="fas fa-info-circle" style="color:#60a5fa;"></i>
                                    <span id="previewText">Akan tampil berjalan seperti ini di HP pelanggan...</span>
                                </div>
                                <div id="previewTarget" style="font-size:11px; margin-top:8px; color:#93c5fd;">Target: Global</div>
                                <div id="previewSchedule" style="font-size:11px; margin-top:4px; color:#67e8f9;">Jadwal: langsung tayang</div>
                            </div>

                            <button class="btn-blast" style="margin-top: 25px; width: 100%; justify-content:center;"
                                onclick="kirimSiaran()" id="btnKirim">
                                <i class="fas fa-paper-plane"></i> Publikasikan Sekarang
                            </button>
                        </div>
                    </div>

                    <!-- KOLOM 2: RIWAYAT -->
                    <div>
                        <div class="glass-card" style="padding: 20px;">
                            <div
                                style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 15px;">
                                <h3 style="margin:0; font-family:'Outfit'; color:var(--text-primary);"><i
                                        class="fas fa-history" style="color:var(--primary);"></i> Riwayat Siaran Aktif
                                </h3>
                            </div>

                            <div id="loadingRiwayat"
                                style="text-align:center; padding: 20px; color:var(--text-secondary);">
                                <i class="fas fa-circle-notch fa-spin"></i> Memuat data...
                            </div>

                            <div id="listRiwayat" style="max-height: 500px; overflow-y:auto; padding-right:5px;">
                                <!-- Item Template -->
                                <!-- 
                                <div class="h-item">
                                    <div class="h-header">
                                        <div class="h-title"><i class="fas fa-broadcast-tower"></i> Global (Semua)</div>
                                        <div class="h-date">03 Mar 2026, 14:00</div>
                                    </div>
                                    <div class="h-body">Perbaikan server sementara...</div>
                                    <div style="text-align:right;">
                                        <button class="btn-del"><i class="fas fa-trash"></i> Hapus</button>
                                    </div>
                                </div> 
                                -->
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script type="module">
        import { renderSidebar, renderHeader } from './js/components/layout.js';
        import { apiFetch, auth, isAdminAppRole } from '{{ asset('api-config.js') }}';
        import { showConfirm, showToast } from './js/utils/dialog.js';

        let areaMap = {};
        let pelangganMaster = [];
        const selectedPelangganIds = new Set();
        const escHtml = (v) => String(v ?? '').replace(/[&<>"']/g, (m) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]));
        function setKirimBusy(isBusy = false, text = 'Kirim Siaran Sekarang') {
            const btn = document.getElementById('btnKirim');
            if (!btn) return;
            if (!btn.dataset.defaultHtml) btn.dataset.defaultHtml = btn.innerHTML;
            btn.disabled = !!isBusy;
            btn.style.opacity = isBusy ? '0.75' : '';
            btn.style.cursor = isBusy ? 'not-allowed' : '';
            btn.innerHTML = isBusy
                ? `<i class="fas fa-circle-notch fa-spin"></i> ${text}`
                : (btn.dataset.defaultHtml || `<i class="fas fa-paper-plane"></i> Kirim Siaran Sekarang`);
        }

        async function initPage() {
            renderSidebar('pengumuman');
            renderHeader();

            setupPreview();
            await loadAreaDropdown();
            await loadPelangganList();
            await loadHistori();
        }

        // Security Check
        auth.onAuthStateChanged(async (user) => {
            if (user) {
                const profile = JSON.parse(localStorage.getItem('ss_user'));
                if (profile) {
                    if (!isAdminAppRole(profile)) {
                        window.location.replace("{{ url('/app-teknisi') }}");
                        return;
                    }
                    // Bootstrapping Data Awal
                    await initPage();
                }
            } else {
                window.location.replace("{{ url('/login') }}");
            }
        });

        function setupPreview() {
            const input = document.getElementById('inputPesan');
            const preview = document.getElementById('previewText');
            const targetType = document.getElementById('inputTargetType');
            const areaSel = document.getElementById('inputArea');
            const searchPel = document.getElementById('inputCariPelanggan');
            const startAt = document.getElementById('inputStartAt');
            const endAt = document.getElementById('inputEndAt');
            const startNow = document.getElementById('inputStartNow');
            const quickDuration = document.getElementById('inputQuickDuration');
            input.addEventListener('input', (e) => {
                preview.innerText = e.target.value || "Akan tampil berjalan seperti ini di HP pelanggan...";
            });
            targetType.addEventListener('change', refreshTargetUI);
            areaSel.addEventListener('change', updatePreviewTarget);
            searchPel.addEventListener('input', () => renderPelangganTargetList(searchPel.value || ''));
            startAt.addEventListener('change', updatePreviewSchedule);
            endAt.addEventListener('change', updatePreviewSchedule);
            startNow.addEventListener('change', syncStartNow);
            quickDuration.addEventListener('change', applyQuickDuration);
            refreshTargetUI();
            syncStartNow();
            updatePreviewSchedule();
        }

        async function loadAreaDropdown() {
            try {
                const areas = await apiFetch('/collections/areas');
                const sel = document.getElementById('inputArea');
                sel.innerHTML = '<option value="">Pilih area...</option>';

                areas.forEach(a => {
                    areaMap[a.id] = a.nama;
                    const opt = document.createElement('option');
                    opt.value = a.id;
                    opt.textContent = `📍 Area Spesifik: ${a.nama}`;
                    sel.appendChild(opt);
                });
            } catch (e) {
                console.error("Gagal load area", e);
            }
        }

        async function loadPelangganList() {
            try {
                const rows = await apiFetch('/collections/pelanggan');
                pelangganMaster = (Array.isArray(rows) ? rows : [])
                    .map((p) => ({
                        idPelanggan: String(p.idPelanggan || p.id || '').trim(),
                        nama: String(p.nama || '').trim(),
                        area: String(p.area || '').trim()
                    }))
                    .filter((p) => p.idPelanggan && p.nama)
                    .sort((a, b) => a.nama.localeCompare(b.nama));
                renderPelangganTargetList('');
            } catch (e) {
                console.error("Gagal load pelanggan", e);
            }
        }

        function refreshTargetUI() {
            const mode = document.getElementById('inputTargetType').value;
            const gArea = document.getElementById('groupAreaTarget');
            const gPel = document.getElementById('groupPelangganTarget');
            gArea.style.display = mode === 'area' ? '' : 'none';
            gPel.style.display = mode === 'pelanggan' ? '' : 'none';
            updatePreviewTarget();
        }

        function updatePreviewTarget() {
            const mode = document.getElementById('inputTargetType').value;
            const previewTarget = document.getElementById('previewTarget');
            if (mode === 'global') {
                previewTarget.innerText = 'Target: Semua pelanggan (global)';
                return;
            }
            if (mode === 'area') {
                const areaId = document.getElementById('inputArea').value;
                const areaName = areaMap[areaId] || '-';
                previewTarget.innerText = `Target: Area ${areaName}`;
                return;
            }
            previewTarget.innerText = `Target: ${selectedPelangganIds.size} pelanggan terpilih`;
        }

        function formatDateTimeLabel(v) {
            if (!v) return '';
            const dt = new Date(v);
            if (Number.isNaN(dt.getTime())) return '';
            return dt.toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
        }

        function updatePreviewSchedule() {
            const startRaw = document.getElementById('inputStartAt').value;
            const endRaw = document.getElementById('inputEndAt').value;
            const el = document.getElementById('previewSchedule');
            const startLabel = formatDateTimeLabel(startRaw);
            const endLabel = formatDateTimeLabel(endRaw);
            if (!startLabel && !endLabel) {
                el.innerText = 'Jadwal: langsung tayang';
                return;
            }
            if (startLabel && endLabel) {
                el.innerText = `Jadwal: ${startLabel} s/d ${endLabel}`;
                return;
            }
            if (startLabel) {
                el.innerText = `Jadwal: mulai ${startLabel}`;
                return;
            }
            el.innerText = `Jadwal: aktif sampai ${endLabel}`;
        }

        function toLocalDateTimeInput(dateObj) {
            const d = new Date(dateObj.getTime() - (dateObj.getTimezoneOffset() * 60000));
            return d.toISOString().slice(0, 16);
        }

        function syncStartNow() {
            const startNow = document.getElementById('inputStartNow');
            const startAt = document.getElementById('inputStartAt');
            if (startNow.checked) {
                startAt.value = toLocalDateTimeInput(new Date());
                startAt.disabled = true;
            } else {
                startAt.disabled = false;
            }
            updatePreviewSchedule();
        }

        function applyQuickDuration() {
            const quickDuration = document.getElementById('inputQuickDuration');
            const minutes = Number.parseInt(quickDuration.value, 10);
            if (!Number.isFinite(minutes) || minutes <= 0) return;
            const startAt = document.getElementById('inputStartAt');
            const endAt = document.getElementById('inputEndAt');
            const base = startAt.value ? new Date(startAt.value) : new Date();
            const end = new Date(base.getTime() + (minutes * 60000));
            endAt.value = toLocalDateTimeInput(end);
            updatePreviewSchedule();
        }

        function renderPelangganTargetList(keyword = '') {
            const listEl = document.getElementById('targetPelangganList');
            const infoEl = document.getElementById('targetPelangganInfo');
            if (!listEl || !infoEl) return;
            const q = String(keyword || '').trim().toLowerCase();
            const rows = pelangganMaster.filter((p) => {
                if (!q) return true;
                return `${p.nama} ${p.idPelanggan} ${p.area}`.toLowerCase().includes(q);
            });
            if (!rows.length) {
                listEl.innerHTML = '<div style="font-size:12px; color:var(--text-secondary); padding:10px;">Tidak ada pelanggan sesuai pencarian.</div>';
                infoEl.innerText = `${selectedPelangganIds.size} pelanggan dipilih.`;
                return;
            }
            listEl.innerHTML = rows.map((p) => {
                const checked = selectedPelangganIds.has(p.idPelanggan) ? 'checked' : '';
                return `
                    <label style="display:flex; align-items:flex-start; gap:8px; padding:8px; border-radius:8px; cursor:pointer;">
                        <input type="checkbox" data-pel-id="${escHtml(p.idPelanggan)}" ${checked} style="margin-top:2px;">
                        <span style="font-size:13px; color:var(--text-primary); line-height:1.35;">
                            <strong>${escHtml(p.nama)}</strong><br>
                            <span style="color:var(--text-secondary);">${escHtml(p.idPelanggan)}${p.area ? ` • ${escHtml(p.area)}` : ''}</span>
                        </span>
                    </label>
                `;
            }).join('');
            listEl.querySelectorAll('input[type="checkbox"]').forEach((cb) => {
                cb.addEventListener('change', (e) => {
                    const id = e.target.dataset.pelId;
                    if (!id) return;
                    if (e.target.checked) selectedPelangganIds.add(id);
                    else selectedPelangganIds.delete(id);
                    infoEl.innerText = `${selectedPelangganIds.size} pelanggan dipilih.`;
                    updatePreviewTarget();
                });
            });
            infoEl.innerText = `${selectedPelangganIds.size} pelanggan dipilih.`;
            updatePreviewTarget();
        }

        async function loadHistori() {
            const loader = document.getElementById('loadingRiwayat');
            const container = document.getElementById('listRiwayat');

            loader.style.display = 'block';
            container.innerHTML = '';
            let listData = [];
            try {
                const rows = await apiFetch('/collections/pengumuman?aktif=1');
                listData = Array.isArray(rows) ? rows : [];
            } catch (e) {
                console.error("Gagal load histori pengumuman", e);
            } finally {
                loader.style.display = 'none';
            }
            listData.sort((a, b) => new Date(b.createdAt || 0) - new Date(a.createdAt || 0));

            if (listData.length === 0) {
                container.innerHTML = `<div style="text-align:center; padding: 30px; color:var(--text-secondary);"><i class="fas fa-comment-slash" style="font-size:30px; margin-bottom:10px; opacity:0.3;"></i><br>Belum ada siaran yang aktif.</div>`;
                return;
            }

            listData.forEach(item => {
                const dateObj = new Date(item.createdAt || Date.now());
                const dStr = dateObj.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                const startLabel = formatDateTimeLabel(item.startAt);
                const endLabel = formatDateTimeLabel(item.endAt);
                const scheduleText = startLabel || endLabel
                    ? `🗓️ ${startLabel || 'langsung'} ${endLabel ? `s/d ${endLabel}` : ''}`
                    : '🗓️ langsung tayang';

                let targetLabel = "🌐 Semua Pelanggan";
                let tColor = "#3b82f6";
                const mode = String(item.targetType || 'global');
                if (mode === 'area') {
                    const nm = item.targetAreaName || areaMap[item.targetAreaId] || item.targetAreaId || '-';
                    targetLabel = `📍 Area: ${nm}`;
                    tColor = "#8b5cf6";
                } else if (mode === 'pelanggan') {
                    const ids = Array.isArray(item.targetPelangganIds) ? item.targetPelangganIds : [];
                    targetLabel = `👤 Personal: ${ids.length} pelanggan`;
                    tColor = "#22c55e";
                }

                const html = `
                <div class="h-item">
                    <div class="h-header">
                        <div class="h-title" style="color:${tColor};"><i class="fas fa-broadcast-tower"></i> ${escHtml(targetLabel)}</div>
                        <div class="h-date">${dStr}</div>
                    </div>
                    <div class="h-body">"${escHtml(item.pesan || '-')}"</div>
                    <div style="font-size:11px; color:var(--text-secondary); margin-top:8px;">${escHtml(scheduleText)}</div>
                    <div style="text-align:right;">
                        <button class="btn-del" onclick='hapusSiaran(${JSON.stringify(String(item.id || ""))})'><i class="fas fa-trash"></i> Cabut Siaran</button>
                    </div>
                </div>
                `;
                container.insertAdjacentHTML('beforeend', html);
            });
        }

        window.kirimSiaran = async () => {
            const targetType = document.getElementById('inputTargetType').value;
            const targetAreaId = document.getElementById('inputArea').value;
            const pesan = document.getElementById('inputPesan').value.trim();

            if (!pesan) {
                showToast("Pesan tidak boleh kosong!", "error");
                return;
            }

            if (targetType === 'area' && !targetAreaId) {
                showToast("Pilih area tujuan dulu.", "warning");
                return;
            }
            if (targetType === 'pelanggan' && selectedPelangganIds.size === 0) {
                showToast("Pilih minimal 1 pelanggan tujuan.", "warning");
                return;
            }

            let msg = `Yakin kirim siaran ini ke SEMUA PELANGGAN secara Global?`;
            if (targetType === 'area') msg = `Yakin kirim siaran ini khusus ke area ${areaMap[targetAreaId] || '-'}?`;
            if (targetType === 'pelanggan') msg = `Yakin kirim siaran ini ke ${selectedPelangganIds.size} pelanggan terpilih?`;

            showConfirm({
                title: 'Konfirmasi Siaran',
                message: msg,
                type: 'confirm'
            }).then(async (confirmed) => {
                if (confirmed) {
                    let payload = null;
                    try {
                        payload = buildPayloadFromForm();
                    } catch (e) {
                        showToast(e.message || 'Jadwal siaran tidak valid.', "warning");
                        return;
                    }
                    setKirimBusy(true, 'Mengirim siaran...');
                    try {
                        await apiFetch('/collections/pengumuman', {
                            method: 'POST',
                            body: JSON.stringify(payload)
                        });
                    } catch (e) {
                        showToast(`Gagal kirim siaran: ${e.message}`, "danger");
                        setKirimBusy(false);
                        return;
                    }

                    document.getElementById('inputPesan').value = '';
                    document.getElementById('inputTargetType').value = 'global';
                    document.getElementById('inputArea').value = '';
                    document.getElementById('inputStartAt').value = '';
                    document.getElementById('inputEndAt').value = '';
                    document.getElementById('inputStartNow').checked = false;
                    document.getElementById('inputQuickDuration').value = '';
                    selectedPelangganIds.clear();
                    renderPelangganTargetList(document.getElementById('inputCariPelanggan').value || '');
                    refreshTargetUI();
                    syncStartNow();
                    updatePreviewSchedule();
                    document.getElementById('previewText').innerText = "Akan tampil berjalan seperti ini di HP pelanggan...";

                    showToast("Siaran berhasil dipublikasikan!", "success");
                    loadHistori();
                    setKirimBusy(false);
                }
            });
        };

        window.hapusSiaran = (id) => {
            showConfirm({
                title: 'Cabut Siaran',
                message: "Yakin ingin mencabut (menghapus) siaran pengumuman ini? Fitur notif di HP pelanggan akan otomatis hilang.",
                type: 'danger',
                confirmText: 'Ya, Cabut'
            }).then(async (confirmed) => {
                if (confirmed) {
                    try {
                        await apiFetch(`/collections/pengumuman/${id}`, { method: 'DELETE' });
                    } catch (e) {
                        showToast(`Gagal cabut siaran: ${e.message}`, "danger");
                        return;
                    }

                    showToast("Siaran dicabut!", "success");
                    loadHistori();
                }
            });
        };

        function buildPayloadFromForm() {
            const targetType = document.getElementById('inputTargetType').value;
            const targetAreaId = document.getElementById('inputArea').value;
            const targetAreaName = areaMap[targetAreaId] || '';
            const pesan = document.getElementById('inputPesan').value.trim();
            const startNow = document.getElementById('inputStartNow').checked;
            const startAt = document.getElementById('inputStartAt').value;
            const endAt = document.getElementById('inputEndAt').value;
            const effectiveStart = startNow ? new Date().toISOString() : (startAt ? new Date(startAt).toISOString() : null);
            if (startAt && endAt) {
                const s = new Date(startAt).getTime();
                const e = new Date(endAt).getTime();
                if (Number.isFinite(s) && Number.isFinite(e) && e < s) {
                    throw new Error('Tanggal expired tidak boleh lebih awal dari mulai tayang.');
                }
            }
            return {
                targetType,
                targetAreaId: targetType === 'area' ? targetAreaId : '',
                targetAreaName: targetType === 'area' ? targetAreaName : '',
                targetPelangganIds: targetType === 'pelanggan' ? Array.from(selectedPelangganIds) : [],
                pesan,
                startAt: effectiveStart,
                endAt: endAt ? new Date(endAt).toISOString() : null,
                aktif: 1,
                createdAt: new Date().toISOString()
            };
        }

    </script>
</body>

</html>