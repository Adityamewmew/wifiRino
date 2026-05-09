<!DOCTYPE html>
<html lang="id">

<head>
    <script src="{{ asset('js/ss-storage-migrate.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Tagihan Bulanan - Sans Speed</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <script>
        if (localStorage.getItem('ss_theme') === 'light') {
            document.documentElement.classList.add('light-mode');
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #f8fafc;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0f2fe;
            color: #0284c7;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: left;
            padding: 12px 16px;
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            border-bottom: 1px solid #e5e7eb;
        }

        td {
            padding: 16px;
            font-size: 14px;
            color: #374151;
            border-bottom: 1px solid #f3f4f6;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-danger {
            background: #fee2e2;
            color: #ef4444;
        }

        .badge-success {
            background: #dcfce3;
            color: #10b981;
        }

        .badge-warning {
            background: #fef3c7;
            color: #f59e0b;
        }

        .action-btn {
            padding: 6px 10px;
            border-radius: 6px;
            background: #f3f4f6;
            color: #4b5563;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .action-btn:hover {
            background: #e5e7eb;
            color: #111827;
        }

        .filter-row {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .form-select,
        .form-input {
            padding: 8px 12px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            color: #1e293b;
            background: white;
            outline: none;
        }

        .stats-top {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .stat-box {
            background: white;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            border-left: 4px solid #3b82f6;
        }

        .stat-box.success {
            border-left-color: #10b981;
        }

        .stat-box.danger {
            border-left-color: #ef4444;
        }

        .table-scroll-mobile {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 6px;
        }

        .table-scroll-mobile table {
            min-width: 860px;
        }

        @media (max-width: 768px) {
            .filter-row {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-row .form-select,
            .filter-row .form-input {
                width: 100% !important;
            }

            .tagihan-filter-actions {
                margin-left: 0 !important;
                width: 100%;
                justify-content: stretch !important;
                flex-wrap: wrap;
            }

            .tagihan-filter-actions .btn-primary {
                width: 100%;
                text-align: center;
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

            <div class="content-wrapper">
                <div
                    style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 15px;">
                    <h1 class="page-title"><i class="fas fa-file-invoice" style="color:#f59e0b;"></i> Billing & Tagihan
                    </h1>

                    <div
                        style="display: flex; gap: 10px; align-items: center; background: white; padding: 6px 12px; border-radius: 10px; border: 1px solid #e5e7eb;">
                        <span style="font-size: 13px; font-weight: 600; color: #64748b; margin-right: 5px;"><i
                                class="fas fa-calendar-alt"></i> Bulan Penagihan:</span>
                        <select id="filterBulan" class="form-select"
                            style="border: none; background: #f8fafc; padding: 6px;"
                            onchange="window.loadTagihan && window.loadTagihan()">
                            <option value="01">Januari</option>
                            <option value="02">Februari</option>
                            <option value="03">Maret</option>
                            <option value="04">April</option>
                            <option value="05">Mei</option>
                            <option value="06">Juni</option>
                            <option value="07">Juli</option>
                            <option value="08">Agustus</option>
                            <option value="09">September</option>
                            <option value="10">Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option>
                        </select>
                        <select id="filterTahun" class="form-select"
                            style="border: none; background: #f8fafc; padding: 6px;"
                            onchange="window.loadTagihan && window.loadTagihan()">
                            <!-- Diisi dinamis oleh script -->
                        </select>
                    </div>
                </div>

                <div class="stats-top">
                    <div class="stat-box">
                        <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Total
                            Tagihan Bulan Ini</div>
                        <div style="font-size: 24px; font-weight: 800; color: #1e293b; margin-top: 5px;" id="statTotal">
                            0</div>
                    </div>
                    <div class="stat-box success">
                        <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Sudah
                            Lunas</div>
                        <div style="font-size: 24px; font-weight: 800; color: #10b981; margin-top: 5px;" id="statLunas">
                            0</div>
                    </div>
                    <div class="stat-box danger">
                        <div style="font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase;">Belum
                            Terbayar</div>
                        <div style="font-size: 24px; font-weight: 800; color: #ef4444; margin-top: 5px;" id="statBelum">
                            0</div>
                    </div>
                </div>

                <div class="card" id="pendingDeleteCard" style="display:none;">
                    <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; margin-bottom:14px; flex-wrap:wrap;">
                        <h3 style="margin:0; font-size:18px; color:#1e293b;">
                            <i class="fas fa-user-check" style="color:#f59e0b;"></i> Approval Hapus Tagihan Pending
                            <span id="pendingDeleteCount" class="badge badge-warning" style="margin-left:8px;">0</span>
                        </h3>
                        <button class="btn-primary" onclick="window.loadPendingDeleteRequests && window.loadPendingDeleteRequests()">
                            <i class="fas fa-rotate"></i> Refresh
                        </button>
                    </div>
                    <div id="pendingDeleteEmpty" style="display:none; color:#64748b; font-size:13px;">Belum ada request approval.</div>
                    <div id="pendingDeleteList" style="display:grid; gap:10px;"></div>
                </div>

                <div class="card">
                    <div class="filter-row">
                        <select id="filterArea" class="form-select">
                            <option value="all">Semua Area/Wilayah</option>
                            <!-- Nanti dipopulasi dinamis dari Firestore -->
                        </select>
                        <select id="filterStatus" class="form-select">
                            <option value="all">Semua Status</option>
                            <option value="belum">Belum Bayar</option>
                            <option value="lunas">Lunas</option>
                            <option value="isolir">Terisolir</option>
                        </select>
                        <input type="text" id="filterSearch" class="form-input" placeholder="Cari Nama / ID / Alamat..."
                            style="width: 250px;">

                        <div class="tagihan-filter-actions" style="margin-left: auto; display: flex; gap: 10px;">
                            <button class="btn-primary" style="background: #10b981;" onclick="window.broadcastWA()">
                                <i class="fab fa-whatsapp"></i> Broadcast WA
                            </button>
                            <button class="btn-primary" style="background: #0f172a;" onclick="window.exportExcel()">
                                <i class="fas fa-file-excel"></i> Export Rekap
                            </button>
                        </div>
                    </div>

                    <div class="table-scroll-mobile desktop-tagihan-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID Pelanggan</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Area</th>
                                    <th>Total (biaya &amp; diskon)</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Tgl isolir</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tableTagihanBody" class="live-table-body">
                                <tr>
                                    <td colspan="8" style="text-align: center; color: #94a3b8;">Silakan tunggu,
                                        menyiapkan data tagihan...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="mobileTagihanList" class="mobile-data-list">
                        <div class="mobile-data-empty"><i class="fas fa-spinner fa-spin"></i> Memuat data tagihan...</div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Modal Bayar Dimuka dari Tagihan -->
    <div id="modalDimukaTagihan"
        style="display:none; position:fixed; inset:0; background:rgba(15,23,42,0.55); z-index:1200; align-items:center; justify-content:center; padding:16px;">
        <div
            style="width:100%; max-width:520px; background:white; border-radius:14px; box-shadow:0 20px 40px rgba(2,6,23,0.25); overflow:hidden;">
            <div
                style="display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid #e2e8f0;">
                <h3 style="margin:0; font-size:18px; color:#10b981;"><i class="fas fa-calendar-check"></i> Bayar Dimuka
                </h3>
                <button onclick="window.tutupModalDimukaTagihan()"
                    style="border:none; background:transparent; color:#64748b; font-size:20px; cursor:pointer;">&times;</button>
            </div>
            <div style="padding:14px 16px;">
                <div id="dimukaTagihanTargetInfo"
                    style="margin-bottom:10px; font-size:12px; color:#334155; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:6px 10px;">
                    Target: -
                </div>
                <div style="display:flex; gap:8px; margin-bottom:8px;">
                    <button type="button" onclick="window.pilihSemuaDimukaTagihan(true)"
                        style="padding:4px 8px; font-size:11px; border:1px solid #d1d5db; border-radius:6px; background:#f8fafc; cursor:pointer;">Cek
                        Semua</button>
                    <button type="button" onclick="window.pilihSemuaDimukaTagihan(false)"
                        style="padding:4px 8px; font-size:11px; border:1px solid #d1d5db; border-radius:6px; background:#f8fafc; cursor:pointer;">Reset</button>
                </div>
                <div style="font-size:10px; color:#64748b; margin-bottom:6px;">
                    Keterangan: <span style="color:#ef4444;">tagihan belum dibayar</span> • <span style="color:#64748b;">tagihan akan dibuat</span> • <span style="color:#10b981;">sudah lunas</span>
                </div>
                <div id="dimukaTagihanChecklist"
                    style="max-height:240px; overflow:auto; border:1px solid #e2e8f0; border-radius:8px; padding:10px; background:#f8fafc;">
                    <div style="font-size:12px; color:#64748b;">Memuat daftar periode...</div>
                </div>
                <div id="dimukaTagihanSummary" style="font-size:11px; color:#64748b; margin-top:8px;">0 bulan dipilih</div>
                <input type="text" id="dimukaTagihanKet" class="form-input"
                    placeholder="Contoh: Bayar langganan 5 bulan dimuka tunai"
                    style="width:100%; margin-top:10px; border:1px solid #e2e8f0; border-radius:8px; padding:8px 10px;">
            </div>
            <div
                style="display:flex; justify-content:flex-end; gap:10px; padding:12px 16px; border-top:1px solid #e2e8f0;">
                <button onclick="window.tutupModalDimukaTagihan()"
                    style="background:#f1f5f9; color:#475569; border:none; border-radius:8px; padding:8px 12px; cursor:pointer;">Batal</button>
                <button id="btnProsesDimukaTagihan" onclick="window.prosesDimukaTagihan()"
                    style="background:#10b981; color:white; border:none; border-radius:8px; padding:8px 12px; cursor:pointer;">Proses
                    Pembayaran</button>
            </div>
        </div>
    </div>

    <!-- Live Refresh Indicator -->
    <div id="liveIndicator"><span class="dot"></span> Live</div>

    <!-- Script Local API Auth -->
    <script type="module">
        import { auth, apiFetch, isAdminAppRole, hasPermission, resolveRoleKey } from '{{ asset('api-config.js') }}';
        const showConfirm = (...args) => window.showConfirm?.(...args);
        const showAlert = (...args) => window.showAlert?.(...args);
        const showToast = (...args) => window.showToast?.(...args);

        // Init Form Waktu
        const dateNow = new Date();
        document.getElementById('filterBulan').value = String(dateNow.getMonth() + 1).padStart(2, '0');

        // Isi dropdown tahun dinamis (2 tahun lalu s/d 3 tahun ke depan)
        (function populateTahunTagihan() {
            const sel = document.getElementById('filterTahun');
            if (!sel) return;
            sel.innerHTML = '';
            const curY = dateNow.getFullYear();
            for (let y = curY + 3; y >= curY - 2; y--) {
                const opt = document.createElement('option');
                opt.value = y;
                opt.textContent = y;
                sel.appendChild(opt);
            }
            sel.value = curY; // Default ke tahun ini
        })();

        // Deep-link dari dashboard: langsung fokus ke pelanggan tertentu
        (function applyFocusFromQuery() {
            try {
                const params = new URLSearchParams(window.location.search);
                const focus = String(params.get('focus') || '').trim();
                if (!focus) return;
                const searchEl = document.getElementById('filterSearch');
                if (searchEl) searchEl.value = focus;
            } catch (_) {
                // ignore invalid query
            }
        })();

        let currentProfile = null;
        const isOwnerProfile = (profile = {}) => {
            const roleKey = String(profile?.roleKey || '').trim().toLowerCase();
            const role = String(profile?.role || '').trim().toLowerCase();
            return roleKey === 'owner' || role === 'owner' || role === 'superadmin';
        };
        const isPenagihAgenOnly = (profile = {}) => resolveRoleKey(profile?.roleKey || profile?.role) === 'penagih';

        auth.onAuthStateChanged(async (user) => {
            if (user) {
                const profile = JSON.parse(localStorage.getItem('ss_user'));
                if (profile) {
                    currentProfile = profile;
                    if (!isAdminAppRole(profile)) {
                        alert("Akses Ditolak!"); window.location.replace("{{ url('/app-teknisi') }}"); return;
                    }
                    if (!hasPermission(profile, 'collect_customer_payment')) {
                        alert("Akses Ditolak! Role ini tidak dapat memproses pembayaran.");
                        window.location.replace("{{ url('/dashboard-admin') }}");
                        return;
                    }
                    // Layout header already managed by layout.js
                    if (isOwnerProfile(profile)) {
                        const ownerCard = document.getElementById('pendingDeleteCard');
                        if (ownerCard) ownerCard.style.display = 'block';
                        await window.loadPendingDeleteRequests?.();
                    }
                    window.loadTagihan();
                }
            } else {
                window.location.replace("{{ url('/login') }}");
            }
        });

        let _lastTagihanHash = '';
        const _norm = (v) => String(v || '').trim().toLowerCase();
        const _formatRp = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(Number(n || 0));
        const _escHtml = (v) => String(v ?? '').replace(/[&<>"']/g, (m) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]));
        const _fmtTglId = (iso) => {
            if (!iso) return '-';
            const d = new Date(iso);
            if (Number.isNaN(d.getTime())) return '-';
            return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        };
        const _parseTagihanSnapshotJson = (raw) => {
            if (raw == null || raw === '') return null;
            if (typeof raw === 'object') return raw;
            try { return JSON.parse(String(raw)); } catch (_) { return null; }
        };
        const _tagihanRincianHtml = (row) => {
            const parts = [];
            const biaya = _parseTagihanSnapshotJson(row.biayaSnapshot);
            const diskon = _parseTagihanSnapshotJson(row.diskonSnapshot);
            if (biaya?.items?.length) {
                biaya.items.forEach((it) => {
                    const n = Number(it.nominal || 0) || 0;
                    const lbl = String(it.rincian || 'Biaya').trim();
                    if (n > 0 || lbl) {
                        parts.push(`<span style="color:#64748b;">+ ${_escHtml(lbl)} ${_formatRp(n)}</span>`);
                    }
                });
            }
            if (diskon && (Number(diskon.nominal || 0) > 0 || String(diskon.keterangan || '').trim())) {
                const n = Number(diskon.nominal || 0) || 0;
                const lbl = String(diskon.keterangan || 'Diskon').trim();
                parts.push(`<span style="color:#b45309;">− ${_escHtml(lbl)} ${_formatRp(n)}</span>`);
            }
            if (!parts.length) return '';
            return `<div style="font-size:10px; line-height:1.35; margin-top:4px; font-weight:500;">${parts.join('<br>')}</div>`;
        };
        const _tagihanRincianText = (row) => {
            const biaya = _parseTagihanSnapshotJson(row.biayaSnapshot);
            const diskon = _parseTagihanSnapshotJson(row.diskonSnapshot);
            const bits = [];
            if (biaya?.items?.length) {
                biaya.items.forEach((it) => {
                    const n = Number(it.nominal || 0) || 0;
                    const lbl = String(it.rincian || 'Biaya').trim();
                    if (n > 0 || lbl) bits.push(`+ ${lbl} ${_formatRp(n)}`);
                });
            }
            if (diskon && (Number(diskon.nominal || 0) > 0 || String(diskon.keterangan || '').trim())) {
                const n = Number(diskon.nominal || 0) || 0;
                bits.push(`− ${String(diskon.keterangan || 'Diskon').trim()} ${_formatRp(n)}`);
            }
            return bits.length ? bits.join(' · ') : '';
        };
        const _effectiveIsolirIso = (row) => row?.tglIsolir || row?.tglJatuhTempo || null;
        function _applyWaTemplate(template, vars) {
            let out = String(template || '');
            Object.entries(vars).forEach(([k, val]) => {
                out = out.split(`{${k}}`).join(String(val ?? ''));
            });
            return out;
        }
        function _defaultWaTagihanTemplate() {
            return 'Halo Yth. *{nama}*,\nKami informasikan bahwa tagihan internet Anda sebesar *{total_tagihan}* sudah terbit.\nMohon dibayar sebelum tanggal *{jatuh_tempo}*.\nEstimasi tanggal isolir layanan: *{tgl_isolir}*.\nTerima kasih.';
        }

        function populateAreaFilter(rows = []) {
            const sel = document.getElementById('filterArea');
            if (!sel) return;
            const prev = sel.value || 'all';
            const uniqueAreas = [...new Set(
                rows
                    .map(r => String(r.area || '').trim())
                    .filter(Boolean)
            )].sort((a, b) => a.localeCompare(b, 'id'));

            sel.innerHTML = '<option value="all">Semua Area/Wilayah</option>';
            uniqueAreas.forEach(areaName => {
                const opt = document.createElement('option');
                opt.value = areaName;
                opt.textContent = areaName;
                sel.appendChild(opt);
            });

            if ([...sel.options].some(o => o.value === prev)) {
                sel.value = prev;
            } else {
                sel.value = 'all';
            }
        }

        function applyTagihanFilters(rows = []) {
            const selectedArea = document.getElementById('filterArea')?.value || 'all';
            const selectedStatus = document.getElementById('filterStatus')?.value || 'all';
            const search = _norm(document.getElementById('filterSearch')?.value || '');

            return rows.filter((item) => {
                const itemArea = String(item.area || '').trim();
                const itemStatus = _norm(item.status);
                const itemSearch = _norm(`${item.idPelanggan || ''} ${item.namaPelanggan || ''} ${item.alamat || ''}`);

                if (selectedArea !== 'all' && itemArea !== selectedArea) return false;
                if (selectedStatus !== 'all') {
                    if (selectedStatus === 'belum' && itemStatus !== 'belum') return false;
                    if (selectedStatus === 'lunas' && itemStatus !== 'lunas') return false;
                    if (selectedStatus === 'isolir' && itemStatus !== 'isolir') return false;
                }
                if (search && !itemSearch.includes(search)) return false;
                return true;
            });
        }

        function _showLive() {
            const el = document.getElementById('liveIndicator');
            if (!el) return;
            el.classList.add('show');
            setTimeout(() => el.classList.remove('show'), 1800);
        }

        // Fungsi Load Data Tagihan Inti dari Firestore
        window.loadTagihan = async function (silent = false) {
            const tableBody = document.getElementById('tableTagihanBody');
            const mobileList = document.getElementById('mobileTagihanList');
            if (!tableBody) return;
            if (!silent) {
                tableBody.innerHTML = '<tr><td colspan="8" style="text-align: center;"><i class="fas fa-spinner fa-spin"></i> Memuat data tagihan...</td></tr>';
                if (mobileList) {
                    mobileList.innerHTML = '<div class="mobile-data-empty"><i class="fas fa-spinner fa-spin"></i> Memuat data tagihan...</div>';
                }
            }

            const selectedMonthStr = document.getElementById('filterBulan').value;
            const selectedMonth = parseInt(selectedMonthStr);
            const selectedYear = parseInt(document.getElementById('filterTahun').value);

            try {
                // Auto-Sync: Generate tagihan untuk bulan yang dipilih (termasuk masa depan)
                // Tagihan hanya dibuat jika belum ada — pelanggan aktif saat itu digunakan sebagai acuan
                try {
                    await apiFetch(`/tagihan/sync?bulan=${selectedMonth}&tahun=${selectedYear}`);
                } catch (e) {
                    console.error("Auto-sync background error:", e);
                }

                // 2. Fetch Data Tagihan
                const url = `/collections/tagihan_bulanan?bulan=${selectedMonth}&tahun=${selectedYear}`;
                const datanya = await apiFetch(url);

                let tableHtml = '';
                let t_total = 0;
                let t_lunas = 0;
                let t_belum = 0;

                if (datanya.length === 0) {
                    document.getElementById('statTotal').innerText = "0";
                    document.getElementById('statLunas').innerText = "0";
                    document.getElementById('statBelum').innerText = "0";

                    const nowDate = new Date();
                    const nowMonth = nowDate.getMonth() + 1;
                    const nowYear = nowDate.getFullYear();

                    const isFuture = selectedYear > nowYear || (selectedYear === nowYear && selectedMonth > nowMonth);
                    const isCurrent = selectedYear === nowYear && selectedMonth === nowMonth;

                    if (isCurrent || isFuture) {
                        // Sedang menyiapkan (bulan ini atau masa depan)
                        tableBody.innerHTML = '<tr><td colspan="8" style="text-align:center; color:#64748b; padding:30px;"><i class="fas fa-spinner fa-spin"></i> Menyiapkan tagihan periode ini... Harap tunggu.</td></tr>';
                        if (mobileList) {
                            mobileList.innerHTML = '<div class="mobile-data-empty"><i class="fas fa-spinner fa-spin"></i> Menyiapkan tagihan periode ini...</div>';
                        }
                        // Hanya retry jika dipanggil secara manual (bukan silent poll)
                        if (!silent) {
                            setTimeout(() => window.loadTagihan && window.loadTagihan(true), 2500);
                        }
                    } else {
                        // Bulan Lampau - Memang tidak ada data
                        tableBody.innerHTML = `<tr><td colspan="8" style="text-align:center; padding:30px;">
                            <i class="fas fa-inbox" style="font-size:32px; color:#64748b; margin-bottom:12px; display:block;"></i>
                            <div style="font-size:15px; font-weight:700; color:#94a3b8;">Tidak ada data tagihan</div>
                            <div style="font-size:12px; color:#64748b; margin-top:6px;">Tidak ada tagihan yang tercatat untuk periode lampau ini.</div>
                        </td></tr>`;
                        if (mobileList) {
                            mobileList.innerHTML = '<div class="mobile-data-empty">Tidak ada data tagihan pada periode ini.</div>';
                        }
                    }
                    return;
                }

                // Sort client-side berdasarkan nama pelanggan (A-Z)
                const docsArr = datanya.sort((a, b) => {
                    const nA = (a.namaPelanggan || '').toLowerCase();
                    const nB = (b.namaPelanggan || '').toLowerCase();
                    return nA.localeCompare(nB);
                });

                populateAreaFilter(docsArr);
                const filteredDocs = applyTagihanFilters(docsArr);
                if (mobileList) mobileList.innerHTML = '';
                let mobileHtml = '';
                const periodeLabel = `${document.getElementById('filterBulan').options[document.getElementById('filterBulan').selectedIndex].text} ${document.getElementById('filterTahun').value}`;

                filteredDocs.forEach((data) => {
                    const docId = data.id;
                    const safeDocId = _escHtml(docId || '');
                    const safeIdPelanggan = _escHtml(data.idPelanggan || '-');
                    const safeNama = _escHtml(data.namaPelanggan || '-');
                    const safeArea = _escHtml(data.area || '-');
                    const safeStatusRaw = _escHtml(data.status || '');
                    const safeNoWA = _escHtml(data.noWA || '');

                    t_total++;
                    if (data.status === 'lunas') t_lunas++;
                    else t_belum++;

                    // Format Rupiah
                    const formatRp = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(data.totalTagihan || 0);

                    // Format Tanggal
                    let tgl = '-';
                    if (data.tglJatuhTempo) {
                        tgl = new Date(data.tglJatuhTempo).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                    }
                    const tglIsolirEff = _effectiveIsolirIso(data);
                    const tglIsolirTxt = _fmtTglId(tglIsolirEff);
                    const showIsolirBtn = currentProfile && !isPenagihAgenOnly(currentProfile);

                    // Label Status
                    let badgeClass = 'badge-danger';
                    let statusTxt = 'Belum Bayar';
                    if (data.status === 'lunas') { badgeClass = 'badge-success'; statusTxt = 'Lunas'; }
                    else if (data.status === 'isolir') { badgeClass = 'badge-warning'; statusTxt = 'Terisolir'; }
                    const idPelangganEsc = (data.idPelanggan || '').replace(/'/g, "\\'");
                    const namaEsc = (data.namaPelanggan || '').replace(/'/g, "\\'");

                    tableHtml += `
                        <tr style="background: ${data.status === 'lunas' ? 'rgba(16, 185, 129, 0.05)' : 'transparent'};">
                            <td style="font-weight: 700; color: #3b82f6;">${safeIdPelanggan}</td>
                            <td style="font-weight: 600;">
                                ${safeNama}
                                ${data.bayarDimuka ? `<br><span style="background: rgba(16, 185, 129, 0.1); color: #10b981; font-size: 10px; padding: 2px 6px; border-radius: 4px; border: 1px solid rgba(16, 185, 129, 0.2); white-space: nowrap; display: inline-block; margin-top: 4px;"><i class="fas fa-forward"></i> +${data.bayarDimuka} Bln (Dimuka)</span>` : ''}
                            </td>
                            <td>${safeArea}</td>
                            <td style="font-weight: 700;">${formatRp}${_tagihanRincianHtml(data)}</td>
                            <td>${tgl}</td>
                            <td style="white-space:nowrap;">
                                <span>${_escHtml(tglIsolirTxt)}</span>
                                ${showIsolirBtn && data.status !== 'lunas' ? `<button type="button" class="action-btn" title="Undur / atur tanggal isolir" onclick="window.aturTglIsolirTagihan('${safeDocId}')" style="color:#ca8a04; background:rgba(245,158,11,0.12); margin-left:6px;"><i class="fas fa-calendar-alt"></i></button>` : ''}
                            </td>
                            <td><span class="badge ${badgeClass}">${statusTxt}</span></td>
                            <td>
                                <button class="action-btn btn-wa" title="Kirim WA Tagihan/Kwitansi"
                                    data-wa="${safeNoWA}"
                                    data-nama="${safeNama}"
                                    data-total="${formatRp}"
                                    data-tgl="${tgl}"
                                    data-status="${safeStatusRaw}"
                                    onclick="window.kirimWA(this.dataset.wa, this.dataset.nama, this.dataset.total, this.dataset.tgl, this.dataset.status, '${tglIsolirTxt.replace(/'/g, "\\'")}', '${idPelangganEsc}', '${periodeLabel.replace(/'/g, "\\'")}')"
                                    style="color: #10b981;"><i class="fab fa-whatsapp"></i></button>
                                ${data.status !== 'lunas' ?
                            `<button class="action-btn btn-bayar" title="Terima Pembayaran Manual"
                                            data-id="${safeDocId}"
                                            onclick="window.konfirmasiBayar(this.dataset.id)"
                                            style="color: #3b82f6; background: rgba(59, 130, 246, 0.1);"><i class="fas fa-check-circle"></i> Terima Kas</button>
                             <button class="action-btn" title="Bayar Dimuka"
                                            onclick="window.bayarDimukaDariTagihan('${(data.idPelanggan || '').replace(/'/g, "\\'")}', '${(data.namaPelanggan || '').replace(/'/g, "\\'")}')"
                                            style="color: #10b981; background: rgba(16, 185, 129, 0.1);"><i class="fas fa-calendar-check"></i> Dimuka</button>`
                            :
                            `<button class="action-btn" title="Cetak Struk Thermal" onclick="window.open('{{ url('/struk') }}?id=${safeDocId}','_blank','width=400,height=600')" style="color: #8b5cf6; background: rgba(139, 92, 246, 0.1); cursor: pointer; margin-right:5px;"><i class="fas fa-print"></i> Cetak Struk</button>
                                     ${_norm(data.metodeBayar) === 'bayar dimuka' ? `<button class="action-btn" title="Edit Bayar Dimuka" onclick="window.editDimukaDariTagihan('${(data.idPelanggan || '').replace(/'/g, "\\'")}', '${(data.namaPelanggan || '').replace(/'/g, "\\'")}')" style="color: #f59e0b; background: rgba(245, 158, 11, 0.12); cursor: pointer;"><i class="fas fa-pen"></i> Edit Dimuka</button>` : ''}
                                     <button class="action-btn" title="Undo Pembayaran / Batal Lunas" data-id="${safeDocId}" onclick="window.undoBayar(this.dataset.id)" style="color: #ef4444; background: rgba(239, 68, 68, 0.1); cursor: pointer;"><i class="fas fa-undo"></i> Batal Lunas</button>`}
                            </td>
                        </tr>
                        `;

                    if (mobileList) {
                        mobileHtml += `
                            <article class="mobile-data-card" style="${data.status === 'lunas' ? 'border-color:rgba(16,185,129,0.25);' : ''}">
                                <div class="mobile-data-head">
                                    <div>
                                        <div class="mobile-data-title">${safeNama}</div>
                                        <div class="mobile-data-sub">${safeArea}${data.idPelanggan ? ` • ${safeIdPelanggan}` : ''}</div>
                                    </div>
                                    <div class="mobile-data-value" style="font-size:16px;">${formatRp}</div>
                                </div>
                                <div class="mobile-data-info">Jatuh Tempo: ${tgl}</div>
                                ${_tagihanRincianText(data) ? `<div class="mobile-data-info" style="font-size:11px; color:#64748b;">${_escHtml(_tagihanRincianText(data))}</div>` : ''}
                                <div class="mobile-data-info">Tgl isolir: ${tglIsolirTxt}${showIsolirBtn && data.status !== 'lunas' ? ` <button type="button" class="action-btn" onclick="window.aturTglIsolirTagihan('${docId}')" style="color:#ca8a04; padding:2px 8px; font-size:11px;"><i class="fas fa-calendar-alt"></i></button>` : ''}</div>
                                <div class="mobile-data-actions" style="justify-content:space-between; align-items:center;">
                                    <span class="badge ${badgeClass}">${statusTxt}</span>
                                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                                        <button class="action-btn" onclick="window.kirimWA('${(data.noWA || '').replace(/'/g, "\\'")}', '${namaEsc}', '${formatRp}', '${tgl}', '${String(data.status || '').replace(/'/g, "\\'")}', '${tglIsolirTxt.replace(/'/g, "\\'")}', '${idPelangganEsc}', '${periodeLabel.replace(/'/g, "\\'")}')"
                                            style="color:#10b981;"><i class="fab fa-whatsapp"></i> WA</button>
                                        ${data.status !== 'lunas'
                                ? `<button class="action-btn" onclick="window.konfirmasiBayar('${docId}')" style="color:#3b82f6; background: rgba(59, 130, 246, 0.1);"><i class="fas fa-check-circle"></i> Terima Kas</button>
                                           <button class="action-btn" onclick="window.bayarDimukaDariTagihan('${idPelangganEsc}', '${namaEsc}')" style="color:#10b981; background: rgba(16, 185, 129, 0.1);"><i class="fas fa-calendar-check"></i> Dimuka</button>`
                                : `<button class="action-btn" onclick="window.open('{{ url('/struk') }}?id=${docId}','_blank','width=400,height=600')" style="color:#8b5cf6; background: rgba(139, 92, 246, 0.1);"><i class="fas fa-print"></i> Struk</button>
                                           <button class="action-btn" onclick="window.undoBayar('${docId}')" style="color:#ef4444; background: rgba(239, 68, 68, 0.1);"><i class="fas fa-undo"></i> Batal</button>`
                            }
                                    </div>
                                </div>
                            </article>
                        `;
                    }
                });

                const newHash = JSON.stringify(filteredDocs.map(d => d.id + d.status + d.totalTagihan + (d.tglIsolir || '')));
                if (silent && newHash === _lastTagihanHash) return; // Data sama, skip update
                _lastTagihanHash = newHash;

                // Smooth DOM swap
                tableBody.classList.add('updating');
                await new Promise(r => setTimeout(r, 200));
                if (!tableHtml) {
                    tableBody.innerHTML = '<tr><td colspan="8" style="text-align:center; color:#64748b; padding:30px;">Tidak ada data sesuai filter.</td></tr>';
                    if (mobileList) mobileList.innerHTML = '<div class="mobile-data-empty">Tidak ada data sesuai filter.</div>';
                } else {
                    tableBody.innerHTML = tableHtml;
                    if (mobileList) mobileList.innerHTML = mobileHtml || '<div class="mobile-data-empty">Tidak ada data sesuai filter.</div>';
                }
                tableBody.classList.remove('updating');
                if (silent) _showLive();

                // Simpan data untuk keperluan export
                window._tagihanDataExport = filteredDocs;

                // Tombol aksi dikontrol via onclick sebaris

                document.getElementById('statTotal').innerText = t_total;
                document.getElementById('statLunas').innerText = t_lunas;
                document.getElementById('statBelum').innerText = t_belum;

            } catch (error) {
                console.error("Gagal load tagihan", error);
                tableBody.innerHTML = `<tr><td colspan="8" style="color: red; text-align: center;">Gagal memuat data tagihan. Cek koneksi internet dan pastikan sudah login. (${error.code || error.message})</td></tr>`;
                if (mobileList) {
                    mobileList.innerHTML = '<div class="mobile-data-empty" style="color:#ef4444;">Gagal memuat data tagihan.</div>';
                }
            }
        };

        // Fungsi Tombol Action
        window.konfirmasiBayar = async function (docId) {
            const confirmed = await showConfirm({
                title: '💵 Konfirmasi Terima Pembayaran',
                message: 'Konfirmasi penerimaan pembayaran tagihan ini secara <strong>Cash / Transfer Manual</strong>?<br><br><span style="font-size:12px;opacity:0.8;">Kas akan otomatis bertambah di Buku Kas.</span>',
                type: 'success',
                confirmText: 'Ya, Terima Kas'
            });
            if (confirmed) {
                try {
                    await apiFetch(`/tagihan/${docId}/bayar`, { method: 'POST' });
                    showToast('✅ Pembayaran berhasil disahkan dan tercatat di Buku Kas!', 'success');
                    window.loadTagihan();
                } catch (e) {
                    console.error('Gagal update bayar', e);
                    showAlert({ title: 'Error Konfirmasi Bayar', message: e.message, type: 'danger' });
                }
            }
        }

        window._dimukaTagihanCtx = null;
        window._dimukaTagihanMode = 'create';
        window._dimukaTagihanLatestSet = new Set();
        const _monthNameTagihan = (m) => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'][m - 1] || '-';

        window.updateSummaryDimukaTagihan = () => {
            const checked = Array.from(document.querySelectorAll('.dimuka-tagihan-check:checked'));
            const total = checked.reduce((acc, el) => acc + (Number(el.dataset.nominal || 0) || 0), 0);
            const el = document.getElementById('dimukaTagihanSummary');
            if (el) el.innerText = `${checked.length} bulan dipilih • estimasi ${_formatRp(total)}`;
        };

        window.pilihSemuaDimukaTagihan = (isChecked) => {
            document.querySelectorAll('.dimuka-tagihan-check:not(:disabled)').forEach(chk => {
                chk.checked = !!isChecked;
            });
            window.updateSummaryDimukaTagihan();
        };

        window.tutupModalDimukaTagihan = () => {
            const modal = document.getElementById('modalDimukaTagihan');
            if (modal) modal.style.display = 'none';
        };

        window.bayarDimukaDariTagihan = async function (idPelanggan, namaPelanggan, mode = 'create') {
            if (!idPelanggan) {
                showAlert({ title: 'Data Tidak Lengkap', message: 'ID pelanggan tidak ditemukan pada tagihan ini.', type: 'warning' });
                return;
            }
            window._dimukaTagihanMode = mode;
            window._dimukaTagihanLatestSet = new Set();
            window._dimukaTagihanCtx = { idPelanggan, namaPelanggan };
            const targetEl = document.getElementById('dimukaTagihanTargetInfo');
            if (targetEl) targetEl.innerHTML = `<strong>Target:</strong> ${_escHtml(namaPelanggan)} <span style="color:#64748b;">(${_escHtml(idPelanggan)})</span>`;
            const ket = document.getElementById('dimukaTagihanKet');
            if (ket) ket.value = '';
            const btn = document.getElementById('btnProsesDimukaTagihan');
            if (btn) btn.innerText = mode === 'edit' ? 'Simpan Perubahan' : 'Proses Pembayaran';

            const box = document.getElementById('dimukaTagihanChecklist');
            if (box) box.innerHTML = '<div style="font-size:12px; color:#64748b;"><i class="fas fa-spinner fa-spin"></i> Menyiapkan bulan tagihan...</div>';
            const modal = document.getElementById('modalDimukaTagihan');
            if (modal) modal.style.display = 'flex';

            try {
                if (mode === 'edit') {
                    const latest = await apiFetch(`/pelanggan/${encodeURIComponent(idPelanggan)}/bayar-dimuka/latest`);
                    if (!latest?.exists || !latest?.data) {
                        const lanjutBuatBaru = await showConfirm({
                            title: 'Belum Ada Transaksi Dimuka',
                            message: 'Belum ada transaksi bayar dimuka yang bisa diedit untuk pelanggan ini.<br><br>Ingin lanjut ke mode <strong>Bayar Dimuka Baru</strong>?',
                            type: 'info',
                            confirmText: 'Ya, Buat Baru'
                        });
                        if (lanjutBuatBaru) {
                            window._dimukaTagihanMode = 'create';
                            if (btn) btn.innerText = 'Proses Pembayaran';
                        } else {
                            window.tutupModalDimukaTagihan();
                            return;
                        }
                    } else {
                        window._dimukaTagihanLatestSet = new Set((latest.data.bulan || []).map(v => String(v)));
                        if (ket && !ket.value) ket.value = 'Perubahan bayar dimuka';
                    }
                }

                const tagihanArr = await apiFetch(`/collections/tagihan_bulanan?idPelanggan=${encodeURIComponent(idPelanggan)}`);
                const map = new Map();
                (tagihanArr || []).forEach(t => map.set(`${t.bulan}/${t.tahun}`, t));

                // ambil nominal default dari salah satu tagihan pelanggan
                const sample = (tagihanArr || []).find(t => Number(t.totalTagihan || 0) > 0) || {};
                const defaultNominal = Number(sample.totalTagihan || 0) || 0;
                const now = new Date();
                const rows = [];
                for (let i = 0; i < 12; i++) {
                    const d = new Date(now.getFullYear(), now.getMonth() + i, 1);
                    const bulan = d.getMonth() + 1;
                    const tahun = d.getFullYear();
                    const key = `${bulan}/${tahun}`;
                    const existing = map.get(key);
                    const status = String(existing?.status || 'baru').toLowerCase();
                    const nominal = Number(existing?.totalTagihan || defaultNominal) || 0;
                    rows.push({ bulan, tahun, status, nominal });
                }

                if (!box) return;
                box.innerHTML = rows.map(item => {
                    const isLunas = item.status === 'lunas';
                    const key = `${item.bulan}/${item.tahun}`;
                    const isFromLatestDimuka = window._dimukaTagihanLatestSet.has(key);
                    const allowEditLunas = window._dimukaTagihanMode === 'edit' && isFromLatestDimuka;
                    const isChecked = window._dimukaTagihanMode === 'edit' ? isFromLatestDimuka : false;
                    const isDisabled = isLunas && !allowEditLunas;
                    const badge = isLunas
                        ? (isFromLatestDimuka
                            ? '<span style="font-size:10px; color:#f59e0b;">(dimuka terakhir)</span>'
                            : '<span style="font-size:10px; color:#10b981;">(sudah lunas)</span>')
                        : (item.status === 'baru'
                            ? '<span style="font-size:10px; color:#64748b;">(tagihan akan dibuat)</span>'
                            : '<span style="font-size:10px; color:#ef4444;">(tagihan belum dibayar)</span>');
                    return `
                        <label style="display:flex; align-items:center; justify-content:space-between; gap:8px; padding:6px 2px; border-bottom:1px dashed #e2e8f0;">
                            <span style="display:flex; align-items:center; gap:8px;">
                                <input type="checkbox" class="dimuka-tagihan-check" data-bulan="${item.bulan}" data-tahun="${item.tahun}" data-nominal="${item.nominal}" ${isChecked ? 'checked' : ''} ${isDisabled ? 'disabled' : ''}>
                                <span style="font-size:12px; color:#0f172a;">${_monthNameTagihan(item.bulan)} ${item.tahun}</span>
                                ${badge}
                            </span>
                            <span style="font-size:11px; color:#64748b;">${_formatRp(item.nominal)}</span>
                        </label>
                    `;
                }).join('');
                document.querySelectorAll('.dimuka-tagihan-check').forEach(el => {
                    el.addEventListener('change', window.updateSummaryDimukaTagihan);
                });
                window.updateSummaryDimukaTagihan();
            } catch (e) {
                if (box) box.innerHTML = `<div style="font-size:12px; color:#ef4444;">Gagal memuat checklist: ${_escHtml(e.message)}</div>`;
            }
        };

        window.editDimukaDariTagihan = async function (idPelanggan, namaPelanggan) {
            return window.bayarDimukaDariTagihan(idPelanggan, namaPelanggan, 'edit');
        };

        window.prosesDimukaTagihan = async function () {
            const ctx = window._dimukaTagihanCtx;
            if (!ctx?.idPelanggan) return;
            const selected = Array.from(document.querySelectorAll('.dimuka-tagihan-check:checked')).map(el => ({
                bulan: Number(el.dataset.bulan),
                tahun: Number(el.dataset.tahun)
            }));
            if (selected.length === 0) {
                showAlert({ title: 'Pilih Periode', message: 'Centang minimal 1 bulan untuk diproses.', type: 'warning' });
                return;
            }
            const btn = document.getElementById('btnProsesDimukaTagihan');
            const prev = btn?.innerHTML || 'Proses Pembayaran';
            if (btn) {
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
                btn.disabled = true;
            }

            try {
                const endpoint = window._dimukaTagihanMode === 'edit'
                    ? `/pelanggan/${encodeURIComponent(ctx.idPelanggan)}/bayar-dimuka/edit`
                    : `/pelanggan/${encodeURIComponent(ctx.idPelanggan)}/bayar-dimuka`;
                await apiFetch(endpoint, {
                    method: 'POST',
                    body: JSON.stringify({
                        jumlahBulan: selected.length,
                        periodeList: selected,
                        keterangan: (document.getElementById('dimukaTagihanKet')?.value || `Bayar dimuka via menu Tagihan Bulanan untuk ${ctx.namaPelanggan}`)
                    })
                });
                window.tutupModalDimukaTagihan();
                showToast(`✅ ${window._dimukaTagihanMode === 'edit' ? 'Edit' : 'Bayar'} dimuka ${selected.length} bulan berhasil untuk ${ctx.namaPelanggan}.`, 'success');
                window.loadTagihan();
            } catch (e) {
                showAlert({ title: 'Gagal Bayar Dimuka', message: e.message, type: 'danger' });
            } finally {
                if (btn) {
                    btn.innerHTML = prev;
                    btn.disabled = false;
                }
            }
        };
        window.undoBayar = async function (docId) {
            const confirmed = await showConfirm({
                title: 'Batalkan Status Lunas?',
                message: 'Jika Anda bukan Owner, aksi ini akan menjadi <strong>permintaan ACC ke Superadmin/Owner</strong>.<br><br>Lanjutkan?',
                type: 'danger',
                confirmText: 'Lanjutkan'
            });
            if (confirmed) {
                try {
                    const result = await apiFetch(`/tagihan/${docId}/undo`, { method: 'POST' });
                    if ((result?.mode || '') === 'requested') {
                        showToast('Permintaan hapus dikirim ke Owner. Menunggu ACC.', 'warning');
                    } else {
                        showToast('Pembatalan berhasil. Status kembali menjadi Belum Bayar.', 'warning');
                    }
                    window.loadTagihan();
                } catch (e) {
                    console.error('Gagal undo bayar', e);
                    showAlert({ title: 'Error Batalkan Lunas', message: e.message, type: 'danger' });
                }
            }
        }

        window.loadPendingDeleteRequests = async function () {
            const wrap = document.getElementById('pendingDeleteList');
            const empty = document.getElementById('pendingDeleteEmpty');
            const countEl = document.getElementById('pendingDeleteCount');
            if (!wrap || !empty || !isOwnerProfile(currentProfile)) return;
            wrap.innerHTML = '<div style="color:#64748b; font-size:13px;"><i class="fas fa-spinner fa-spin"></i> Memuat request...</div>';
            empty.style.display = 'none';
            try {
                const result = await apiFetch('/tagihan/pending-delete-requests');
                const rows = Array.isArray(result?.data) ? result.data : [];
                if (countEl) countEl.textContent = String(rows.length || 0);
                if (!rows.length) {
                    wrap.innerHTML = '';
                    empty.style.display = 'block';
                    return;
                }
                wrap.innerHTML = rows.map((r) => {
                    const safeId = _escHtml(r.id || '');
                    const safeLabel = _escHtml(r.targetLabel || r.targetId || '-');
                    const safeReq = _escHtml(r.requestedByEmail || '-');
                    const safeReason = _escHtml(r.reason || '-');
                    const safeCreatedAt = r.createdAt ? new Date(r.createdAt).toLocaleString('id-ID') : '-';
                    return `
                        <div style="border:1px solid #e2e8f0; border-radius:10px; padding:12px; background:#fff;">
                            <div style="font-weight:700; color:#0f172a;">${safeLabel}</div>
                            <div style="font-size:12px; color:#64748b; margin-top:4px;">Requester: ${safeReq} • ${safeCreatedAt}</div>
                            <div style="font-size:12px; color:#475569; margin-top:4px;">Alasan: ${safeReason}</div>
                            <div style="margin-top:10px; text-align:right;">
                                <button class="btn-primary" onclick="window.approvePendingDeleteRequest('${safeId}')">
                                    <i class="fas fa-check"></i> ACC Hapus
                                </button>
                            </div>
                        </div>
                    `;
                }).join('');
            } catch (e) {
                wrap.innerHTML = '';
                empty.style.display = 'block';
                empty.style.color = '#ef4444';
                empty.textContent = `Gagal memuat request: ${e.message || 'Unknown error'}`;
                if (countEl) countEl.textContent = '0';
            }
        };

        window.approvePendingDeleteRequest = async function (requestId) {
            const ok = await showConfirm({
                title: 'ACC Hapus Tagihan Pending?',
                message: 'Setelah di-ACC, status lunas akan dibatalkan dan transaksi terkait akan dihapus.',
                type: 'danger',
                confirmText: 'Ya, ACC'
            });
            if (!ok) return;
            try {
                await apiFetch(`/tagihan/pending-delete-requests/${encodeURIComponent(requestId)}/approve`, { method: 'POST' });
                showToast('✅ Request berhasil di-ACC oleh Owner.', 'success');
                await window.loadPendingDeleteRequests();
                await window.loadTagihan(true);
            } catch (e) {
                showAlert({ title: 'Gagal ACC', message: e.message || 'Terjadi kesalahan', type: 'danger' });
            }
        };

        window.cetakStruk = function (docId) {
            window.open(`{{ url('/struk') }}?id=${docId}`, '_blank', 'width=420,height=600');
        }

        window.kirimWA = function (noWA, nama, totalRp, tglJatuhTempo, status, tglIsolirTxt, idPelanggan, periodeLabel) {
            if (!noWA) {
                showAlert({ title: 'Nomor WA Tidak Ada', message: 'Nomor WhatsApp tidak terdaftar untuk pelanggan ini.', type: 'warning' });
                return;
            }
            let wa = noWA.trim();
            if (wa.startsWith('0')) wa = '62' + wa.substring(1);

            const blnNama = document.getElementById('filterBulan').options[document.getElementById('filterBulan').selectedIndex].text;
            const thn = document.getElementById('filterTahun').value;
            const periode = periodeLabel || `${blnNama} ${thn}`;
            const isolirTampil = (tglIsolirTxt && tglIsolirTxt !== '-') ? tglIsolirTxt : tglJatuhTempo;

            const waSet = JSON.parse(localStorage.getItem('ss_wa_config') || '{}');
            let teks = '';
            if (_norm(status) === 'lunas') {
                const tpl = waSet.lunas || 'Halo Yth. *{nama}*,\nPembayaran tagihan internet sebesar *{total_tagihan}* telah kami terima.\nPeriode: *{periode}*. Terima kasih.';
                teks = _applyWaTemplate(tpl, {
                    nama: nama || '',
                    total_tagihan: totalRp || '',
                    jatuh_tempo: tglJatuhTempo || '',
                    tgl_isolir: isolirTampil || '',
                    id_pelanggan: idPelanggan || '',
                    periode
                });
            } else {
                const tpl = waSet.tagihan || _defaultWaTagihanTemplate();
                teks = _applyWaTemplate(tpl, {
                    nama: nama || '',
                    total_tagihan: totalRp || '',
                    jatuh_tempo: tglJatuhTempo || '',
                    tgl_isolir: isolirTampil || '',
                    id_pelanggan: idPelanggan || '',
                    periode
                });
            }

            const urwa = `https://wa.me/${wa}?text=${encodeURIComponent(teks)}`;
            window.open(urwa, '_blank');
        };

        window.aturTglIsolirTagihan = async function (docId) {
            if (isPenagihAgenOnly(currentProfile || {})) {
                showAlert({ title: 'Akses ditolak', message: 'Agen tidak dapat mengubah tanggal isolir.', type: 'warning' });
                return;
            }
            const hint = 'Tanggal isolir layanan (YYYY-MM-DD). Kosongkan lalu OK untuk kembali mengikuti jatuh tempo.';
            const inp = window.prompt(hint, '');
            if (inp === null) return;
            const trimmed = String(inp).trim();
            let body = {};
            if (!trimmed) body = { tglIsolir: null };
            else {
                const d = new Date(trimmed);
                if (Number.isNaN(d.getTime())) {
                    showAlert({ title: 'Tanggal tidak valid', message: 'Gunakan format YYYY-MM-DD atau tanggal yang dikenali browser.', type: 'warning' });
                    return;
                }
                body = { tglIsolir: trimmed };
            }
            try {
                await apiFetch(`/tagihan/${encodeURIComponent(docId)}/tgl-isolir`, { method: 'POST', body: JSON.stringify(body) });
                showToast('Tanggal isolir disimpan.', 'success');
                window.loadTagihan(true);
            } catch (e) {
                showAlert({ title: 'Gagal simpan', message: e.message || 'Error', type: 'danger' });
            }
        };

        window.exportExcel = function () {
            const table = document.querySelector('table');
            let csv = [];
            const rows = table.querySelectorAll('tr');

            for (let i = 0; i < rows.length; i++) {
                let row = [], cols = rows[i].querySelectorAll('td, th');

                // Skip column Action indicating it by length
                for (let j = 0; j < cols.length - 1; j++) {
                    let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').replace(/(\s\s)/gm, ' ');
                    data = data.replace(/"/g, '""');
                    row.push('"' + data + '"');
                }
                csv.push(row.join(','));
            }

            const csvFile = new Blob([csv.join('\n')], { type: "text/csv" });
            const downloadLink = document.createElement("a");
            downloadLink.download = `Rekap_Tagihan_${document.getElementById('filterBulan').value}_${document.getElementById('filterTahun').value}.csv`;
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        };

        window.broadcastWA = async function () {
            const selectedMonthStr = document.getElementById('filterBulan').value;
            const selectedMonth = parseInt(selectedMonthStr);
            const selectedYear = parseInt(document.getElementById('filterTahun').value);
            const blnNama = document.getElementById('filterBulan').options[document.getElementById('filterBulan').selectedIndex].text;

            try {
                const datanya = await apiFetch(`/collections/tagihan_bulanan?bulan=${selectedMonth}&tahun=${selectedYear}&status=belum`);

                if (datanya.length === 0) {
                    showAlert({ title: 'Semua Sudah Lunas! 🎉', message: 'Semua tagihan bulan ini sudah lunas! Tidak ada yang perlu dibroadcast.', type: 'success' });
                    return;
                }

                const ok = await showConfirm({
                    title: `📊 Broadcast ke ${datanya.length} Pelanggan`,
                    message: `Ditemukan <strong>${datanya.length} pelanggan</strong> belum membayar tagihan bulan <strong>${blnNama} ${selectedYear}</strong>.<br><br>Lanjutkan Broadcast WA? Browser akan membuka tab WA satu per satu dengan jeda 3 detik.`,
                    type: 'warning',
                    confirmText: 'Ya, Broadcast Sekarang'
                });
                if (!ok) return;

                showToast('📢 Broadcast dimulai... Jangan tutup tab yang muncul.', 'info');

                const waSet = JSON.parse(localStorage.getItem('ss_wa_config') || '{}');
                const tplBc = waSet.tagihan || _defaultWaTagihanTemplate();
                const periodeBc = `${blnNama} ${selectedYear}`;

                for (let i = 0; i < datanya.length; i++) {
                    const data = datanya[i];
                    if (!data.noWA) continue;
                    let wa = data.noWA.replace(/\D/g, '');
                    if (wa.startsWith('0')) wa = '62' + wa.substring(1);
                    const formatRp = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(data.totalTagihan || 0);
                    let tglJatuhTempo = '-';
                    if (data.tglJatuhTempo) tglJatuhTempo = new Date(data.tglJatuhTempo).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                    const tglIs = _fmtTglId(_effectiveIsolirIso(data));
                    const isolirTampil = (tglIs && tglIs !== '-') ? tglIs : tglJatuhTempo;
                    const teks = _applyWaTemplate(tplBc, {
                        nama: data.namaPelanggan || '',
                        total_tagihan: formatRp,
                        jatuh_tempo: tglJatuhTempo,
                        tgl_isolir: isolirTampil,
                        id_pelanggan: data.idPelanggan || '',
                        periode: periodeBc
                    });
                    setTimeout(() => window.open(`https://wa.me/${wa}?text=${encodeURIComponent(teks)}`, '_blank'), i * 3500);
                }

            } catch (err) {
                showAlert({ title: 'Error Broadcast WA', message: err.message, type: 'danger' });
            }
        };

        // Fungsi Generate Massal Tagihan Bulanan (Core Engine)
        window.bukaModalGenerate = async function () {
            const blnValue = document.getElementById('filterBulan').value;
            const blnNama = document.getElementById('filterBulan').options[document.getElementById('filterBulan').selectedIndex].text;
            const blnInt = parseInt(blnValue);
            const thnInt = parseInt(document.getElementById('filterTahun').value);

            const ok = await showConfirm({
                title: `⚡ Generate Tagihan ${blnNama} ${thnInt}`,
                message: `Sistem akan memindai semua <strong>Pelanggan Aktif</strong> yang mulai berlangganan sebelum atau pada bulan ini, lalu membuat lembar tagihan baru.<br><br>Tagihan yang sudah ada akan di-<em>skip</em> otomatis.`,
                type: 'warning',
                confirmText: 'Ya, Generate Sekarang'
            });
            if (!ok) return;

            const btnGen = document.getElementById('btnGenerate');
            const originBtn = btnGen.innerHTML;
            btnGen.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Prosesing...';
            btnGen.disabled = true;

            try {
                const pelSnap = await apiFetch('/collections/pelanggan');
                let counterBaru = 0, counterSkip = 0, counterBelumMulai = 0;

                for (const pData of pelSnap) {
                    if (pData.status && pData.status !== 'aktif') continue;
                    if (pData.bulanMulai && pData.tahunMulai) {
                        const mulaiDate = new Date(parseInt(pData.tahunMulai), parseInt(pData.bulanMulai) - 1, 1);
                        const selectedDate = new Date(thnInt, blnInt - 1, 1);
                        if (mulaiDate > selectedDate) { counterBelumMulai++; continue; }
                    }
                    const pelId = pData.idPelanggan || pData.id;
                    const cekSnap = await apiFetch(`/collections/tagihan_bulanan?idPelanggan=${pelId}&bulan=${blnInt}&tahun=${thnInt}`);
                    if (cekSnap.length > 0) { counterSkip++; continue; }

                    const defaultTglTagih = pData.tglTagih ? parseInt(pData.tglTagih) : 10;
                    const jatuhTempo = new Date(thnInt, blnInt - 1, defaultTglTagih, 23, 59, 59);
                    let biaya = pData.totalFinal || pData.hargaPaket || 0;
                    if (!biaya && pData.paket) {
                        if (pData.paket.includes('20')) biaya = 200000;
                        else if (pData.paket.includes('30')) biaya = 250000;
                        else if (pData.paket.includes('50')) biaya = 350000;
                        else if (pData.paket.includes('100')) biaya = 500000;
                        else biaya = 150000;
                    }
                    await apiFetch('/collections/tagihan_bulanan', {
                        method: 'POST',
                        body: JSON.stringify({
                            idPelanggan: pelId, namaPelanggan: pData.nama || 'Pelanggan Tanpa Nama',
                            area: pData.area || 'Unknown', paket: pData.paket || '-',
                            noWA: pData.noWA || '', bulan: blnInt, tahun: thnInt,
                            totalTagihan: biaya, status: 'belum',
                            tglJatuhTempo: jatuhTempo.toISOString(), createdAt: new Date().toISOString()
                        })
                    });
                    counterBaru++;
                }

                showAlert({
                    title: '✅ Generate Berhasil!',
                    message: `<strong>📄 ${counterBaru}</strong> Tagihan Baru Dicetak<br><strong>⏭️ ${counterSkip}</strong> Sudah Ada (Skip)<br><strong>🕐 ${counterBelumMulai}</strong> Belum Mulai Berlangganan`,
                    type: 'success',
                    confirmText: 'Oke'
                });
                window.loadTagihan();

            } catch (e) {
                console.error('Gagal Generate Massal:', e);
                showAlert({ title: 'Error Generate Tagihan', message: e.message, type: 'danger' });
            } finally {
                btnGen.innerHTML = originBtn;
                btnGen.disabled = false;
            }
        }

        // Filter interaktif (area/status/search)
        document.getElementById('filterArea')?.addEventListener('change', () => window.loadTagihan(true));
        document.getElementById('filterStatus')?.addEventListener('change', () => window.loadTagihan(true));
        document.getElementById('filterSearch')?.addEventListener('input', () => {
            clearTimeout(window._filterSearchDebounce);
            window._filterSearchDebounce = setTimeout(() => window.loadTagihan(true), 250);
        });

        // Note: filterBulan dan filterTahun sudah punya onchange di HTML — tidak perlu addEventListener lagi

        // Auto-refresh polling setiap 45 detik (realtime update)
        let _tagihanPollInterval = setInterval(() => {
            // Hanya refresh jika halaman aktif/terlihat user
            if (!document.hidden) window.loadTagihan(true); // silent mode
        }, 45000);

        // Bersihkan interval saat meninggalkan halaman
        window.addEventListener('pagehide', () => clearInterval(_tagihanPollInterval));



        // Fetch awal sudah dipanggil di onAuthStateChanged agar tidak double request

    </script>
    <script type="module">
        import { renderSidebar, renderHeader } from './js/components/layout.js';
        import { guardAdmin } from './js/utils/role-guard.js';
        import { showConfirm, showAlert, showToast } from './js/utils/dialog.js';
        window.showConfirm = showConfirm;
        window.showAlert = showAlert;
        window.showToast = showToast;
        if (guardAdmin()) {
            renderSidebar('tagihan');
            renderHeader();
        }
    </script>
</body>

</html>