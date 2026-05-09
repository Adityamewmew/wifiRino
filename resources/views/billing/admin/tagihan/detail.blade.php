<!DOCTYPE html>
<html lang="id">

<head>
    <script src="{{ asset('js/ss-storage-migrate.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sans Speed App - Daftar Tagihan Pelanggan</title>
    <!-- Hindari zoom di Mobile untuk Look & Feel seperti App Native -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- Pustaka Standar -->
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        if (localStorage.getItem('ss_theme') === 'light') {
            document.documentElement.classList.add('light-mode');
        }
    </script>
    <style>
        /* CSS Reset & Setup */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
        }

        :root {
            --primary: #3b82f6;
            --primary-dark: #2563eb;
            --secondary: #8b5cf6;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --bg-body: #f1f5f9;
            --card-bg: rgba(255, 255, 255, 0.9);
            --text-main: #0f172a;
            --text-muted: #64748b;
            --glass-border: rgba(255, 255, 255, 0.6);
            --header-gradient: linear-gradient(135deg, var(--primary), var(--secondary));
        }

        body.light-mode {
            --bg-body: #0f172a;
            --card-bg: rgba(30, 41, 59, 0.8);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body {
            font-family: 'Outfit', 'Inter', sans-serif;
            background: var(--bg-body);
            color: var(--text-main);
            padding-bottom: 30px;
            overflow-x: hidden;
            transition: background 0.3s ease;
        }

        /* PREMIUM GLASS HEADER */
        .app-header {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 16px 20px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid var(--glass-border);
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            gap: 15px;
        }

        .btn-back {
            background: rgba(148, 163, 184, 0.1);
            border: 1px solid var(--glass-border);
            color: var(--text-main);
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 18px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-back:active {
            transform: scale(0.9);
            background: rgba(148, 163, 184, 0.2);
        }

        .header-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-main);
            flex-grow: 1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* FILTER SECTION */
        .filter-section {
            padding: 15px 20px;
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--glass-border);
            position: sticky;
            top: 73px;
            /* dibawah header */
            z-index: 40;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .search-box {
            position: relative;
            flex-grow: 1;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        .search-input {
            width: 100%;
            padding: 12px 15px 12px 40px;
            background: rgba(148, 163, 184, 0.1);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            font-family: inherit;
            font-size: 14px;
            color: var(--text-main);
            outline: none;
            transition: all 0.3s;
        }

        .search-input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            background: var(--card-bg);
        }

        .filter-row {
            display: flex;
            gap: 10px;
        }

        .filter-select {
            flex-grow: 1;
            padding: 12px 15px;
            background: rgba(148, 163, 184, 0.1);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            font-family: inherit;
            font-size: 14px;
            color: var(--text-main);
            outline: none;
            appearance: none;
            cursor: pointer;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
        }

        /* DYNAMIC TITLE BANNER */
        .info-banner {
            margin: 15px 20px;
            padding: 12px 15px;
            border-radius: 12px;
            background: rgba(59, 130, 246, 0.1);
            border-left: 4px solid var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .info-banner i {
            color: var(--primary);
            font-size: 20px;
        }

        .info-text {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-main);
        }

        .info-count {
            margin-left: auto;
            background: var(--primary);
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 800;
        }

        /* CUSTOMER LIST AREA */
        .list-container {
            padding: 0 20px;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .customer-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 16px;
            border: 1px solid var(--glass-border);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            display: flex;
            flex-direction: column;
            gap: 12px;
            animation: fadeIn 0.4s ease forwards;
            opacity: 0;
            transform: translateY(10px);
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .c-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .c-info h4 {
            font-size: 16px;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 2px;
        }

        .c-id {
            font-size: 12px;
            color: var(--text-muted);
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .c-badge {
            padding: 4px 8px;
            border-radius: 8px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-belum {
            background: rgba(245, 158, 11, 0.15);
            color: #d97706;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .badge-isolir {
            background: rgba(239, 68, 68, 0.15);
            color: #dc2626;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .badge-lunas {
            background: rgba(16, 185, 129, 0.15);
            color: #059669;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .badge-baru {
            background: rgba(139, 92, 246, 0.15);
            color: #7c3aed;
            border: 1px solid rgba(139, 92, 246, 0.3);
        }

        .c-body {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .c-detail-row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .c-detail-row i {
            width: 16px;
            text-align: center;
            color: var(--primary);
        }

        .c-footer {
            display: flex;
            gap: 10px;
            margin-top: 5px;
            border-top: 1px solid var(--glass-border);
            padding-top: 12px;
        }

        .btn-action {
            flex: 1;
            padding: 10px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 13px;
            border: none;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }

        .btn-action:active {
            transform: scale(0.95);
        }

        .btn-pay {
            background: linear-gradient(135deg, #34d399, #10b981);
            color: white;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.3);
        }

        .btn-wa {
            background: rgba(37, 211, 102, 0.1);
            color: #16a34a;
            border: 1px solid rgba(37, 211, 102, 0.3);
        }

        .btn-detail {
            background: rgba(148, 163, 184, 0.1);
            color: var(--text-main);
            border: 1px solid var(--glass-border);
        }

        .btn-disabled {
            background: var(--glass-border);
            color: var(--text-muted);
            cursor: not-allowed;
            box-shadow: none;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 40px;
            margin-bottom: 15px;
            color: #cbd5e1;
        }

        .empty-state h3 {
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 5px;
        }

        /* Loading Skeleton */
        .skeleton {
            background: linear-gradient(90deg, var(--card-bg) 25%, rgba(148, 163, 184, 0.2) 50%, var(--card-bg) 75%);
            background-size: 200% 100%;
            animation: shimmer 1.5s infinite;
            border-radius: 4px;
        }

        @keyframes shimmer {
            0% {
                background-position: -200% 0;
            }

            100% {
                background-position: 200% 0;
            }
        }

        /* BOTTOM SHEET MODAL (NATIVE APP FEEL) */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s;
        }

        .modal-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .bottom-sheet {
            position: fixed;
            bottom: -100%;
            left: 0;
            right: 0;
            background: var(--card-bg);
            border-top-left-radius: 24px;
            border-top-right-radius: 24px;
            padding: 24px 20px;
            z-index: 1000;
            box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.1);
            transition: bottom 0.4s cubic-bezier(0.175, 0.885, 0.32, 1);
            max-height: 90vh;
            overflow-y: auto;
        }

        .bottom-sheet.show {
            bottom: 0;
        }

        .sheet-handle {
            width: 40px;
            height: 5px;
            background: var(--glass-border);
            border-radius: 5px;
            margin: 0 auto 20px auto;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 14px 15px;
            background: rgba(148, 163, 184, 0.1);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            font-family: inherit;
            font-size: 15px;
            color: var(--text-main);
            outline: none;
            transition: all 0.3s;
        }

        .form-input:focus,
        .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            background: var(--card-bg);
        }

        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 16px;
        }

        .sum-box {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(139, 92, 246, 0.1));
            border: 1px solid var(--primary);
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
            text-align: center;
        }

        .sum-title {
            font-size: 12px;
            color: var(--primary);
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .sum-value {
            font-size: 28px;
            font-weight: 800;
            color: var(--text-main);
        }
    </style>
</head>

<body>
@include('billing.partials.web-bootstrap')

    <!-- Header -->
    <header class="app-header">
        <button class="btn-back" onclick="window.location.href("{{ url('/app-teknisi') }}")">
            <i class="fas fa-arrow-left"></i>
        </button>
        <div class="header-title" id="pageTitle">Daftar Tagihan</div>
    </header>

    <!-- Filters -->
    <div class="filter-section">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" id="searchInput" class="search-input" placeholder="Cari nama, ID, atau WA...">
        </div>
        <div class="filter-row">
            <select id="areaFilter" class="filter-select">
                <option value="ALL">Semua Area Saya</option>
                <!-- Opsi Area di-generate by JS -->
            </select>
        </div>
    </div>

    <!-- Active Filter Banner (Dynamic) -->
    <div class="info-banner" id="infoBanner">
        <i class="fas fa-filter" id="bannerIcon"></i>
        <div class="info-text" id="bannerText">Memuat Data...</div>
        <div class="info-count" id="bannerCount">0</div>
    </div>

    <!-- Data Container -->
    <div class="list-container" id="customerList">
        <!-- Skeletons Loading -->
        <div class="customer-card">
            <div class="c-header">
                <div>
                    <div class="skeleton" style="width: 150px; height: 18px; margin-bottom: 8px;"></div>
                    <div class="skeleton" style="width: 80px; height: 12px;"></div>
                </div>
            </div>
            <div class="c-body mt-2">
                <div class="skeleton" style="width: 100%; height: 12px; margin-bottom: 5px;"></div>
                <div class="skeleton" style="width: 70%; height: 12px;"></div>
            </div>
        </div>
        <div class="customer-card">
            <div class="c-header">
                <div>
                    <div class="skeleton" style="width: 120px; height: 18px; margin-bottom: 8px;"></div>
                    <div class="skeleton" style="width: 90px; height: 12px;"></div>
                </div>
            </div>
        </div>

        <!-- SKELETON CARDS END -->
    </div>
    <!-- End of list-container -->

    <!-- PAYMENT BOTTOM SHEET (outside customerList, so it's not wiped on re-render) -->
    <div class="modal-overlay" id="paymentOverlay" onclick="window.tutupPaymentSheet()"></div>
    <div class="bottom-sheet" id="paymentSheet">
        <div class="sheet-handle"></div>
        <h3 style="font-weight: 800; font-size: 20px; margin-bottom: 5px;">Proses Pembayaran</h3>
        <p style="color: var(--text-muted); font-size: 13px; margin-bottom: 20px;">Selesaikan tagihan bulan ini secara
            tunai di lokasi.</p>

        <div class="sum-box">
            <div class="sum-title">Total Tagihan</div>
            <div class="sum-value" id="payNominal">Rp 0</div>
        </div>

        <div class="form-group">
            <label class="form-label">Pelanggan</label>
            <input type="text" id="payNama" class="form-input" readonly
                style="background: rgba(0,0,0,0.05); color: var(--text-muted);">
        </div>
        <div class="form-group" style="display:none;">
            <input type="hidden" id="payIdPelanggan">
            <input type="hidden" id="payBulan">
            <input type="hidden" id="payTahun">
            <input type="hidden" id="payHargaRaw">
        </div>

        <button class="btn-action btn-pay" style="width: 100%; padding: 16px; font-size: 15px; margin-top: 10px;"
            id="btnKonfirmasiBayar" onclick="window.konfirmasiBayar()">
            <i class="fas fa-check-circle"></i> Terima Pembayaran
        </button>
        <button class="btn-action btn-detail" style="width: 100%; padding: 14px; font-size: 14px; margin-top: 10px;"
            onclick="window.tutupPaymentSheet()">
            Batal
        </button>
    </div>

    <script type="module">
        import { auth, apiFetch } from '{{ asset('api-config.js') }}';

        // State Global
        let allCustomers = [];
        let filteredCustomers = [];
        let penagihAreas = [];
        let currentFilterMode = 'semua';
        let currentUserRole = '';
        let currentUserId = '';
        let currentUserName = '';
        let allowedAreaKeys = [];
        let areaAliasMap = new Map();
        let areaIdToNameMap = new Map();
        const normalizeAreaKey = (val) => String(val || '').trim().toLowerCase();
        const extractAreaRefs = (raw) => {
            if (raw === null || typeof raw === 'undefined') return [];
            if (Array.isArray(raw)) return raw.flatMap(extractAreaRefs);
            if (typeof raw === 'object') {
                return [raw.id, raw.nama, raw.area, raw.value].flatMap(extractAreaRefs);
            }
            const txt = String(raw).trim();
            if (!txt) return [];
            if ((txt.startsWith('[') && txt.endsWith(']')) || (txt.startsWith('{') && txt.endsWith('}'))) {
                try {
                    return extractAreaRefs(JSON.parse(txt));
                } catch {
                    // fallback ke parser string biasa
                }
            }
            if (txt.includes(',')) {
                return txt.split(',').flatMap(extractAreaRefs);
            }
            return [txt];
        };
        const getAreaMatchKeys = (areaRef) => {
            const normalized = normalizeAreaKey(areaRef);
            if (!normalized) return [];
            const canonical = areaAliasMap.get(normalized) || normalized;
            const areaName = areaIdToNameMap.get(canonical) || '';
            return [...new Set([normalized, canonical, normalizeAreaKey(areaName)].filter(Boolean))];
        };

        // Ambil URL Params
        const urlParams = new URLSearchParams(window.location.search);
        const filterParam = urlParams.get('filter');
        if (filterParam) currentFilterMode = filterParam;

        // UI Elements
        const customerList = document.getElementById('customerList');
        const searchInput = document.getElementById('searchInput');
        const areaFilter = document.getElementById('areaFilter');
        const pageTitle = document.getElementById('pageTitle');
        const bannerText = document.getElementById('bannerText');
        const bannerIcon = document.getElementById('bannerIcon');
        const bannerCount = document.getElementById('bannerCount');

        // Main Initialize
        auth.onAuthStateChanged(async (user) => {
            if (user) {
                try {
                    const prof = JSON.parse(localStorage.getItem('ss_user'));
                    if (!prof || prof.aktif === false) throw new Error("Akses Ditolak");

                    currentUserRole = prof.role.toLowerCase();
                    currentUserId = prof.uid || prof.id;
                    currentUserName = prof.nama;

                    // Cegah Teknisi Murni Buka Tagihan
                    if (currentUserRole === 'teknisi') {
                        alert("Role Teknisi tidak memiliki akses penagihan.");
                        window.location.replace('{{ url('/app-teknisi') }}');
                        return;
                    }

                    // Setup Area Filter (Hanya ambil yg di assigned OR semua kalau admin)
                    let areaData = [];
                    try {
                        const resArea = await apiFetch('/collections/areas');
                        areaData = Array.isArray(resArea) ? resArea : (resArea.data || []);
                    } catch (e) {
                        console.warn("Gagal load detail area", e);
                    }

                    // Build alias map agar sinkron meski data pelanggan simpan ID/nama area berbeda.
                    areaAliasMap = new Map();
                    areaIdToNameMap = new Map();
                    areaData.forEach(a => {
                        const areaId = String(a?.id || '').trim();
                        const areaName = String(a?.nama || '').trim();
                        const idKey = normalizeAreaKey(areaId);
                        const nameKey = normalizeAreaKey(areaName);
                        if (idKey) {
                            areaAliasMap.set(idKey, idKey);
                            areaIdToNameMap.set(idKey, areaName || areaId);
                        }
                        if (nameKey && idKey) {
                            areaAliasMap.set(nameKey, idKey);
                        }
                    });

                    if (currentUserRole === 'superadmin' || currentUserRole === 'admin') {
                        // Admin punya akses ke semua area
                        penagihAreas = areaData.map(a => String(a.id || '').trim()).filter(Boolean);
                    } else {
                        // Kolektor hanya area assigned akun login, support format string/JSON/array/object.
                        const assignedAreas = extractAreaRefs(prof.areas);
                        penagihAreas = [...new Set(assignedAreas
                            .map(a => String(a || '').trim())
                            .filter(Boolean)
                            .filter(a => a.toLowerCase() !== '[object object]')
                            .map(areaRef => {
                                const areaKey = normalizeAreaKey(areaRef);
                                return areaAliasMap.get(areaKey) || areaRef;
                            }))];
                    }

                    // Normalized allow-list area (include alias id + nama)
                    allowedAreaKeys = [...new Set(
                        penagihAreas.flatMap(getAreaMatchKeys).filter(Boolean)
                    )];

                    // Populate Dropdown (hanya area yang memang boleh diakses)
                    areaFilter.innerHTML = '';
                    const defaultOpt = document.createElement('option');
                    defaultOpt.value = 'ALL';
                    defaultOpt.textContent = (currentUserRole === 'superadmin' || currentUserRole === 'admin')
                        ? 'Semua Area'
                        : 'Semua Area Saya';
                    areaFilter.appendChild(defaultOpt);

                    penagihAreas.forEach(areaId => {
                        const canonicalId = areaAliasMap.get(normalizeAreaKey(areaId)) || normalizeAreaKey(areaId);
                        const areaName = areaIdToNameMap.get(canonicalId) || areaId;
                        const opt = document.createElement('option');
                        opt.value = canonicalId || areaId;
                        opt.textContent = `Area: ${areaName}`;
                        areaFilter.appendChild(opt);
                    });

                    // Jika non-admin tidak punya area, kunci filter agar tidak misleading.
                    if (currentUserRole !== 'superadmin' && currentUserRole !== 'admin' && allowedAreaKeys.length === 0) {
                        areaFilter.innerHTML = '<option value="NONE">Tidak ada area terdaftar</option>';
                        areaFilter.disabled = true;
                    }

                    // Setup Header Title By Filter Mode
                    setupBannerByMode();

                    // Load Data
                    await loadDataUtama();

                } catch (e) {
                    alert(e.message);
                    window.location.replace('{{ url('/app-teknisi') }}');
                }
            } else {
                window.location.replace('{{ url('/login') }}');
            }
        });

        function setupBannerByMode() {
            if (currentFilterMode === 'semua') {
                pageTitle.innerText = "Semua Pelanggan";
                bannerText.innerText = "Seluruh Pelanggan Area Anda";
                bannerIcon.className = "fas fa-users";
                bannerIcon.style.color = "#3b82f6"; // primary
                document.getElementById('infoBanner').style.background = "rgba(59, 130, 246, 0.1)";
                document.getElementById('infoBanner').style.borderLeftColor = "#3b82f6";
            } else if (currentFilterMode === 'baru') {
                pageTitle.innerText = "Pelanggan Baru";
                bannerText.innerText = "Pelanggan Pasang Baru Bulan Ini";
                bannerIcon.className = "fas fa-user-plus";
                bannerIcon.style.color = "#8b5cf6"; // purple
                document.getElementById('infoBanner').style.background = "rgba(139, 92, 246, 0.1)";
                document.getElementById('infoBanner').style.borderLeftColor = "#8b5cf6";
            } else if (currentFilterMode === 'belum_bayar') {
                pageTitle.innerText = "Belum Bayar";
                bannerText.innerText = "Tagihan Belum Lunas";
                bannerIcon.className = "fas fa-file-invoice-dollar";
                bannerIcon.style.color = "#f59e0b"; // orange
                document.getElementById('infoBanner').style.background = "rgba(245, 158, 11, 0.1)";
                document.getElementById('infoBanner').style.borderLeftColor = "#f59e0b";
            } else if (currentFilterMode === 'isolir') {
                pageTitle.innerText = "Isolir / Jatuh Tempo";
                bannerText.innerText = "Tagihan Jatuh Tempo (Perlu Tindakan)";
                bannerIcon.className = "fas fa-exclamation-triangle";
                bannerIcon.style.color = "#ef4444"; // danger
                document.getElementById('infoBanner').style.background = "rgba(239, 68, 68, 0.1)";
                document.getElementById('infoBanner').style.borderLeftColor = "#ef4444";
            } else if (currentFilterMode === 'lunas_saya') {
                pageTitle.innerText = "Lunas Harian (By Saya)";
                bannerText.innerText = "Pelanggan yang Anda tagih bulan ini";
                bannerIcon.className = "fas fa-check-circle";
                bannerIcon.style.color = "#10b981"; // success
                document.getElementById('infoBanner').style.background = "rgba(16, 185, 129, 0.1)";
                document.getElementById('infoBanner').style.borderLeftColor = "#10b981";
            } else if (currentFilterMode === 'lunas_admin') {
                pageTitle.innerText = "Lunas Area (By Admin)";
                bannerText.innerText = "Tagihan diproses oleh Pihak Kantor";
                bannerIcon.className = "fas fa-building";
                bannerIcon.style.color = "#475569"; // dark
                document.getElementById('infoBanner').style.background = "rgba(71, 85, 105, 0.1)";
                document.getElementById('infoBanner').style.borderLeftColor = "#475569";
            }
        }

        async function loadDataUtama() {
            try {
                // Fetch All Customers
                const resPel = await apiFetch('/collections/pelanggan');
                let rawData = Array.isArray(resPel) ? resPel : (resPel.data || []);

                // 1. FILTER UMUM: Hanya Pelanggan Area Saya
                if (currentUserRole !== 'superadmin' && currentUserRole !== 'admin') {
                    if (allowedAreaKeys.length > 0) {
                        rawData = rawData.filter(p => allowedAreaKeys.includes(normalizeAreaKey(p.area)));
                    } else {
                        // Non-admin tanpa area assignment tidak boleh melihat data area lain.
                        rawData = [];
                    }
                }

                // Siapkan variabel waktu untuk filter tambahan
                const now = new Date();
                const currMonthInt = now.getMonth() + 1;
                const currMonth = String(currMonthInt).padStart(2, '0');
                const currYear = String(now.getFullYear());

                // ★ KRITIS: Ambil tagihan bulan ini dari tagihan_bulanan (SUMBER STATUS AKTUAL)
                // Ini yang dipakai di aplikasi admin, jadi status harus dari sini
                const resTagihan = await apiFetch(`/collections/tagihan_bulanan?bulan=${currMonthInt}&tahun=${currYear}`);
                const tagihanBulanIni = Array.isArray(resTagihan) ? resTagihan : (resTagihan.data || []);

                // Buat map: idPelanggan -> tagihan record bulan ini
                const tagihanMap = {};
                tagihanBulanIni.forEach(t => {
                    tagihanMap[t.idPelanggan] = t;
                });

                // Gabungkan data pelanggan dengan status tagihan bulan ini
                rawData = rawData.map(p => {
                    const pelId = p.idPelanggan || p.id;
                    const tagihan = tagihanMap[pelId];
                    return {
                        ...p,
                        // Override status dengan data live dari tagihan_bulanan
                        _statusBulanIni: tagihan ? tagihan.status : 'belum', // default 'belum' jika belum ada tagihan bulan ini
                        _tagihanId: tagihan ? tagihan.id : null,
                        _dibayar_ke: tagihan ? tagihan.dibayar_ke : null,
                        _totalTagihan: tagihan ? tagihan.totalTagihan : (p.hargaPaket || p.totalFinal || 0),
                        _tglJatuhTempo: tagihan ? tagihan.tglJatuhTempo : null,
                    };
                });

                // Kita butuh data pembukuan jika filter bersinggungan dengan riwayat "Lunas By ..."
                let pembukuanLunasBulanIni = [];
                if (currentFilterMode === 'lunas_saya' || currentFilterMode === 'lunas_admin') {
                    const resPem = await apiFetch(`/collections/pembukuan?bulan=${currMonth}&tahun=${currYear}`);
                    pembukuanLunasBulanIni = Array.isArray(resPem) ? resPem : (resPem.data || []);
                }

                // 2. FILTER SPESIFIK BERDASARKAN PARAMETER URL
                allCustomers = rawData.filter(p => {
                    if (currentFilterMode === 'semua') return true;

                    if (currentFilterMode === 'baru') {
                        const cmInt = parseInt(currMonth);
                        const cyInt = parseInt(currYear);
                        const startOfMonth = new Date(cyInt, cmInt - 1, 1).getTime();
                        const endOfMonth = new Date(cyInt, cmInt, 0, 23, 59, 59).getTime();

                        const created = p.createdAt ? new Date(p.createdAt).getTime() : 0;
                        return created >= startOfMonth && created <= endOfMonth;
                    }

                    if (currentFilterMode === 'belum_bayar') {
                        return p._statusBulanIni === 'belum';
                    }

                    if (currentFilterMode === 'isolir') {
                        return p.status === 'isolir';
                    }

                    if (currentFilterMode === 'lunas_saya' || currentFilterMode === 'lunas_admin') {
                        if (p._statusBulanIni !== 'lunas') return false;

                        const dibayarKe = p._dibayar_ke || '';
                        const isByMe = dibayarKe.toLowerCase() === currentUserName.toLowerCase();

                        if (currentFilterMode === 'lunas_saya') return isByMe;
                        if (currentFilterMode === 'lunas_admin') return !isByMe;
                    }

                    return true;
                });

                // Update Master Map
                applyFilters();

            } catch (e) {
                console.error("Gagal Render Data Utama", e);
                customerList.innerHTML = `<div class="empty-state">
                    <i class="fas fa-exclamation-triangle" style="color:#ef4444;"></i>
                    <h3>Gagal Memuat Data</h3>
                    <p>${e.message}</p>
                </div>`;
            }
        }

        function applyFilters() {
            const searchVal = searchInput.value.toLowerCase().trim();
            const areaVal = areaFilter.value;
            const selectedAreaKeys = areaVal === 'ALL' ? [] : getAreaMatchKeys(areaVal);

            filteredCustomers = allCustomers.filter(c => {
                const customerAreaKey = normalizeAreaKey(c.area);

                // Filter Area Dropdown
                if (areaVal !== 'ALL' && !selectedAreaKeys.includes(customerAreaKey)) return false;

                // Hard guard: non-admin tetap hanya boleh area miliknya meskipun value dropdown dimanipulasi.
                if (currentUserRole !== 'superadmin' && currentUserRole !== 'admin') {
                    if (!allowedAreaKeys.includes(customerAreaKey)) return false;
                }

                // Filter Kata Kunci
                if (searchVal) {
                    const txt = `${c.nama} ${c.idPelanggan} ${c.noWA} ${c.alamat}`.toLowerCase();
                    if (!txt.includes(searchVal)) return false;
                }

                return true;
            });

            bannerCount.innerText = filteredCustomers.length;
            renderHTML();
        }

        // Event Listener untuk Filter interaktif
        searchInput.addEventListener('input', applyFilters);
        areaFilter.addEventListener('change', applyFilters);

        function renderHTML() {
            customerList.innerHTML = ''; // Kosongkan

            if (filteredCustomers.length === 0) {
                customerList.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h3>Tidak Ada Data</h3>
                    <p style="font-size:13px;">Data pelanggan sesuai filter kosong atau tidak ditemukan.</p>
                </div>`;
                return;
            }

            // Fungsi Formatter Rupiah
            const formatRp = (angka) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);

            const now = new Date();

            filteredCustomers.forEach((c, index) => {
                // Menentukan Badge dan Status — DARI tagihan_bulanan (akurat)
                let badgeHTML = '';
                let isBelumBayar = false;

                if (c._statusBulanIni === 'belum') {
                    isBelumBayar = true;
                    badgeHTML = `<span class="c-badge badge-belum">Belum Bayar</span>`;
                    // Cek Isolir override
                    if (c._tglJatuhTempo) {
                        const jt = new Date(c._tglJatuhTempo);
                        if (jt < now) {
                            badgeHTML = `<span class="c-badge badge-isolir"><i class="fas fa-exclamation-triangle"></i> ISOLIR</span>`;
                        }
                    }
                } else {
                    badgeHTML = `<span class="c-badge badge-lunas"><i class="fas fa-check"></i> Lunas</span>`;
                }

                if (c.bayarDimuka) {
                    badgeHTML += ` <span style="background: rgba(16, 185, 129, 0.1); color: #10b981; font-size: 10px; padding: 2px 6px; border-radius: 4px; border: 1px solid rgba(16, 185, 129, 0.2); white-space: nowrap;"><i class="fas fa-forward"></i> +${c.bayarDimuka} Bln Dimuka</span>`;
                }

                // Cek Baru override
                const currMonthStr = String(now.getMonth() + 1).padStart(2, '0');
                if (c.bulanMulai === currMonthStr && String(c.tahunMulai) === String(now.getFullYear())) {
                    if (!isBelumBayar) badgeHTML += ` <span class="c-badge badge-baru">Baru</span>`;
                    else badgeHTML = `<span class="c-badge badge-baru">Baru</span> ` + badgeHTML;
                }

                // Format Jatuh Tempo untuk tampilan
                let tglJatuhTampilanStr = '?';
                if (c._tglJatuhTempo) {
                    tglJatuhTampilanStr = new Date(c._tglJatuhTempo).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
                }

                // Format Nominal
                const formatRp = (angka) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);

                // Tombol Aksi — gunakan _tagihanId untuk Tagih
                let actionBtnHTML = '';
                if (isBelumBayar) {
                    if (c._tagihanId) {
                        actionBtnHTML = `
                            <button class="btn-action btn-pay" onclick="window.bukaPembayaran('${c._tagihanId}')"><i class="fas fa-hand-holding-usd"></i> Proses Bayar</button>
                        `;
                    } else {
                        actionBtnHTML = `
                            <button class="btn-action btn-disabled" disabled title="Tagihan bulan ini belum di-generate oleh admin"><i class="fas fa-clock"></i> Menunggu Generate</button>
                        `;
                    }
                } else {
                    actionBtnHTML = `
                        <button class="btn-action btn-disabled" disabled><i class="fas fa-file-invoice"></i> Sudah Lunas</button>
                        <button class="btn-action" style="background:#3b82f6; border-color:#2563eb; color:white; margin-top:5px;" onclick="window.open('{{ url('/struk') }}?id=${c._tagihanId}','_blank','width=400,height=600')"><i class="fas fa-print"></i> Cetak Struk</button>
                    `;
                }

                const delay = index * 0.05; // Animasi staggered

                const html = `
                <div class="customer-card" style="animation-delay: ${delay}s">
                    <div class="c-header">
                        <div class="c-info">
                            <h4>${c.nama || 'Tanpa Nama'}</h4>
                            <div class="c-id">${c.idPelanggan || c.id || 'ID_KOSONG'}</div>
                        </div>
                        <div>${badgeHTML}</div>
                    </div>
                    
                    <div class="c-body">
                        <div class="c-detail-row">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${c.alamat || 'Alamat tidak diatur'}</span>
                        </div>
                        <div class="c-detail-row">
                            <i class="fas fa-box"></i>
                            <span>Paket: ${c.paket || '-'} <span style="margin:0 5px; color:#cbd5e1;">|</span> <strong>${formatRp(c._totalTagihan || c.hargaPaket || 0)}</strong></span>
                        </div>
                        <div class="c-detail-row" style="color: ${isBelumBayar && c._tglJatuhTempo && new Date(c._tglJatuhTempo) < now ? '#ef4444' : 'var(--text-muted)'}">
                            <i class="fas fa-calendar-alt"></i>
                            <span>Jatuh Tempo: ${tglJatuhTampilanStr}</span>
                        </div>
                    </div>

                    <div class="c-footer">
                        <button class="btn-action btn-wa" onclick="window.tujukkanWA('${(c.noWA || '').replace(/'/g, '')}'  , '${(c.nama || '').replace(/'/g, '')}')"><i class="fab fa-whatsapp"></i> Chat</button>
                        ${actionBtnHTML}
                    </div>
                </div>
                `;
                customerList.insertAdjacentHTML('beforeend', html);
            });
        }

        // Expose Global Helper Methods for onclick
        window.tujukkanWA = (noHp, nama) => {
            if (!noHp) return alert("Nomor WA tidak terdaftar");
            let hp = noHp.replace(/^0/, "62");
            window.open(`https://wa.me/${hp}?text=Halo%20Bapak/Ibu%20${nama},%20Ini%20dari%20Sans Speed.`, '_blank');
        };

        // Navigation & UI Helpers
        window.tutupPaymentSheet = () => {
            document.getElementById('paymentSheet').classList.remove('show');
            document.getElementById('paymentOverlay').classList.remove('show');
        };

        window.bukaPembayaran = (tagihanId) => {
            const customer = allCustomers.find(c => c._tagihanId === tagihanId);
            if (!customer) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Data tagihan tidak ditemukan! ID: ' + tagihanId
                });
                return;
            }

            // Populate Modal
            const formatRp = (angka) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);

            document.getElementById('payIdPelanggan').value = tagihanId; // Simpan tagihanId
            document.getElementById('payNama').value = `${customer.nama} (${customer.idPelanggan || customer.id_pelanggan || ''})`;
            document.getElementById('payNominal').innerText = formatRp(customer._totalTagihan || customer.hargaPaket || 0);
            document.getElementById('payHargaRaw').value = customer._totalTagihan || customer.hargaPaket || 0;

            const now = new Date();
            document.getElementById('payBulan').value = String(now.getMonth() + 1).padStart(2, '0');
            document.getElementById('payTahun').value = String(now.getFullYear());

            // Tampilkan Sheet
            document.getElementById('paymentOverlay').classList.add('show');
            document.getElementById('paymentSheet').classList.add('show');
        };

        window.konfirmasiBayar = async () => {
            const idPel = document.getElementById('payIdPelanggan').value;
            const harga = document.getElementById('payHargaRaw').value;
            const bulan = document.getElementById('payBulan').value;
            const tahun = document.getElementById('payTahun').value;
            const btn = document.getElementById('btnKonfirmasiBayar');

            const result = await Swal.fire({
                title: 'Konfirmasi Pembayaran',
                text: "Yakin proses LUNAS tagihan ini? Pastikan uang tunai sudah Anda terima.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#10b981',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Proses Lunas!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            });

            if (!result.isConfirmed) return;

            btn.disabled = true;
            btn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Memproses...`;

            try {
                // 1. Hit Unified Billing Endpoint
                await apiFetch(`/tagihan/${idPel}/bayar`, {
                    method: 'POST',
                    body: JSON.stringify({
                        dibayar_ke: currentUserName
                    })
                });

                await Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: 'Pembayaran Berhasil Dicatat!',
                    showConfirmButton: false,
                    timer: 1500
                });
                window.tutupPaymentSheet();

                // Refresh Data Seamlessly
                loadDataUtama();

            } catch (e) {
                console.error("Gagal bayar", e);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Memproses!',
                    text: e.message + "\n\nPastikan server Node.js menyala dan coba lagi."
                });
            } finally {
                btn.disabled = false;
                btn.innerHTML = `<i class="fas fa-check-circle"></i> Terima Pembayaran`;
            }
        };

    </script>
</body>

</html>