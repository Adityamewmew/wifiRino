<!DOCTYPE html>
<html lang="id">

<head>
    <script src="{{ asset('js/ss-storage-migrate.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sans Speed App - Tim Lapangan</title>
    <!-- Hindari zoom di Mobile untuk Look & Feel seperti App Native -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <!-- Pustaka Standar -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.__ssTheme = localStorage.getItem('ss_theme') || 'dark';
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
            --bg-body: #0b1220;
            --card-bg: rgba(30, 41, 59, 0.88);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --glass-border: rgba(255, 255, 255, 0.12);
            --header-gradient: linear-gradient(135deg, var(--primary), var(--secondary));
        }

        body.light-mode {
            --bg-body: #f1f5f9;
            --card-bg: rgba(255, 255, 255, 0.95);
            --text-main: #0f172a;
            --text-muted: #64748b;
            --glass-border: rgba(148, 163, 184, 0.35);
        }

        body {
            font-family: 'Outfit', 'Inter', sans-serif;
            background: var(--bg-body);
            color: var(--text-main);
            padding-bottom: 24px;
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
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--glass-border);
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .header-logo {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 8px;
            background: var(--header-gradient);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .header-logo i {
            -webkit-text-fill-color: var(--primary);
            font-size: 22px;
        }

        .profile-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--header-gradient);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 700;
            font-size: 14px;
            position: relative;
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
            border: 2px solid rgba(255, 255, 255, 0.8);
            cursor: pointer;
            transition: transform 0.2s;
        }

        .profile-btn:active {
            transform: scale(0.9);
        }

        .notif-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 12px;
            height: 12px;
            background: var(--danger);
            border-radius: 50%;
            border: 2px solid white;
            display: none;
            box-shadow: 0 0 8px var(--danger);
        }

        /* 3D HERO SECTION */
        .hero-section {
            background: var(--header-gradient);
            margin: 15px 20px;
            padding: 25px 20px;
            color: white;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4), inset 0 2px 0 rgba(255, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
        }

        .hero-section::after {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.2) 0%, transparent 70%);
            border-radius: 50%;
        }

        .greeting-text {
            font-size: 14px;
            font-weight: 500;
            opacity: 0.9;
            margin-bottom: 4px;
        }

        .employee-name {
            font-size: 24px;
            font-weight: 800;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }

        .role-badge {
            display: inline-block;
            padding: 6px 12px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(5px);
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        /* MAIN CONTENT AREA */
        .content-area {
            padding: 10px 20px 20px 20px;
        }

        .section-title {
            font-size: 16px;
            font-weight: 800;
            margin-bottom: 15px;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-title.compact {
            margin-bottom: 0;
        }

        .section-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
            gap: 10px;
        }

        .section-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .section-action-btn {
            border: 1px solid var(--glass-border);
            background: rgba(255, 255, 255, 0.05);
            color: var(--primary);
            border-radius: 999px;
            padding: 6px 12px;
            font-size: 11px;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
        }

        /* 3D GRID MENU */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 25px;
        }

        .menu-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 20px 15px;
            text-align: center;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04), 0 2px 4px rgba(0, 0, 0, 0.02);
            border: 1px solid var(--glass-border);
            text-decoration: none;
            color: inherit;
            display: block;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            cursor: pointer;
            display: none;
            position: relative;
            overflow: hidden;
        }

        .menu-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.4) 0%, transparent 100%);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .menu-card:active {
            transform: translateY(4px) scale(0.95);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .menu-card:active::before {
            opacity: 1;
        }

        .menu-icon {
            width: 55px;
            height: 55px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 12px;
            box-shadow: inset 0 2px 0 rgba(255, 255, 255, 0.5), 0 4px 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .menu-text {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-main);
        }

        /* 3D Color variants */
        .bg-blue {
            background: linear-gradient(135deg, #60a5fa, #3b82f6);
            color: white;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .bg-green {
            background: linear-gradient(135deg, #34d399, #10b981);
            color: white;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .bg-orange {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
            color: white;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .bg-purple {
            background: linear-gradient(135deg, #a78bfa, #8b5cf6);
            color: white;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        /* DASHBOARD ANALYTICS (STAT CARDS) */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 20px;
            display: none;
            /* Dihide default, muncul via JS untuk agen */
        }

        .stats-grid.compact .stat-card {
            display: none;
        }

        .stats-grid.compact .stat-card.is-front {
            display: block;
        }

        .stat-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            border-radius: 14px;
            padding: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            border: 1px solid var(--glass-border);
            text-align: left;
            position: relative;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-card:active {
            transform: scale(0.95);
        }

        .stat-icon {
            width: 31px;
            height: 31px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            margin-bottom: 8px;
            color: white;
        }

        .stat-value {
            font-size: 17px;
            font-weight: 800;
            color: var(--text-main);
            margin-bottom: 2px;
        }

        .stat-label {
            font-size: 10px;
            font-weight: 600;
            color: var(--text-muted);
            line-height: 1.2;
        }

        /* Stat Icon Colors */
        .icon-blue {
            background: linear-gradient(135deg, #60a5fa, #3b82f6);
        }

        .icon-green {
            background: linear-gradient(135deg, #34d399, #10b981);
        }

        .icon-red {
            background: linear-gradient(135deg, #f87171, #ef4444);
        }

        .icon-orange {
            background: linear-gradient(135deg, #fbbf24, #f59e0b);
        }

        .icon-purple {
            background: linear-gradient(135deg, #a78bfa, #8b5cf6);
        }

        .icon-dark {
            background: linear-gradient(135deg, #475569, #1e293b);
        }

        /* BRIEF TASK LIST */
        .task-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            display: none;
        }

        .task-title-row-action {
            cursor: pointer;
            color: var(--primary);
            font-size: 12px;
            font-weight: 700;
        }

        .task-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            padding: 16px;
            border-radius: 16px;
            border-left: 5px solid var(--primary);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            border-top: 1px solid var(--glass-border);
            border-right: 1px solid var(--glass-border);
            border-bottom: 1px solid var(--glass-border);
            position: relative;
            overflow: hidden;
        }

        .task-card.high {
            border-left-color: var(--danger);
        }

        .task-card.high::after {
            content: 'URGENT';
            position: absolute;
            top: 10px;
            right: -25px;
            background: var(--danger);
            color: white;
            font-size: 9px;
            font-weight: 800;
            padding: 3px 25px;
            transform: rotate(45deg);
            box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
        }

        .task-title {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 6px;
            color: var(--text-main);
        }

        .task-meta {
            font-size: 12px;
            color: var(--text-muted);
            display: flex;
            justify-content: space-between;
            font-weight: 500;
        }

        /* PROFILE MODAL (Logout) - BOTTOM SHEET */
        #profileModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            z-index: 100;
            align-items: flex-end;
            opacity: 0;
            transition: opacity 0.3s;
        }

        #profileModal.show {
            opacity: 1;
        }

        .modal-bottom {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            width: 100%;
            border-top-left-radius: 30px;
            border-top-right-radius: 30px;
            padding: 30px 20px 40px 20px;
            transform: translateY(100%);
            transition: transform 0.4s cubic-bezier(0.19, 1, 0.22, 1);
            box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.1);
            border-top: 1px solid var(--glass-border);
            text-align: center;
        }

        .modal-bottom::before {
            content: '';
            position: absolute;
            top: 12px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 5px;
            background: rgba(148, 163, 184, 0.4);
            border-radius: 5px;
        }

        .modal-bottom.open {
            transform: translateY(0);
        }

        .modal-bottom h3 {
            color: var(--text-main);
            font-size: 20px;
            font-weight: 800;
            margin-bottom: 5px;
            margin-top: 10px;
        }

        .btn-full {
            display: block;
            width: 100%;
            padding: 16px;
            text-align: center;
            border-radius: 16px;
            font-weight: 700;
            font-size: 15px;
            border: none;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            overflow: hidden;
        }

        .btn-danger {
            background: linear-gradient(135deg, #f87171, #ef4444);
            color: white;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3), inset 0 2px 0 rgba(255, 255, 255, 0.2);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .btn-danger:active {
            transform: scale(0.96);
            box-shadow: 0 2px 5px rgba(239, 68, 68, 0.3);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--glass-border);
            color: var(--text-muted);
        }

        .btn-outline:active {
            transform: scale(0.96);
            background: rgba(0, 0, 0, 0.05);
        }

        /* Loading Overlay Full */
        #loadingApp {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--bg-body);
            z-index: 200;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 20px;
            transition: opacity 0.5s;
        }

        #loadingApp i {
            filter: drop-shadow(0 0 15px rgba(59, 130, 246, 0.5));
        }

        #loadingApp h2 {
            font-weight: 600;
            letter-spacing: 0.5px;
        }
    </style>
</head>

<body>
@include('billing.partials.web-bootstrap')

    <!-- SplashScreen / Loading Firebase -->
    <div id="loadingApp">
        <i class="fas fa-wifi fa-3x" style="color: #3b82f6; animation: spin 2s linear infinite;"></i>
        <h2 style="font-size: 16px; color: #64748b;">Menghubungkan ke Sans Speed...</h2>
    </div>

    <!-- Header Native -->
    <header class="app-header">
        <div class="header-logo"><i class="fas fa-wifi"></i> Sans Speed <span
                style="font-size: 12px; font-weight: 500; color: #94a3b8; margin-left:5px;">Tim Lapangan</span></div>
        <div style="display: flex; align-items: center; gap: 15px;">
            <button id="themeToggleBtn"
                style="background: transparent; border: none; color: var(--text-main); font-size: 20px; cursor: pointer;">
                <i class="fas fa-sun"></i>
            </button>
            <div class="profile-btn" onclick="bukaModalProfil()" id="avatarHeader">
                --
                <div class="notif-badge" id="notifBadge"></div>
            </div>
        </div>
    </header>

    <!-- Hero Boarding -->
    <div class="hero-section">
        <div class="greeting-text" id="waktuSapa">Selamat Datang,</div>
        <div class="employee-name" id="userNameLabel">Memuat Profil...</div>
        <div class="role-badge" id="userRoleBadge"><i class="fas fa-spinner fa-spin"></i> Checking Role</div>
    </div>

    <!-- Konten Khusus Role -->
    <div class="content-area">

        <!-- Dashboard Analytics (Khusus Agen) -->
        <div id="penagihStatsContainer" style="display: none;">
            <div class="section-header-row">
                <div class="section-title compact"><i class="fas fa-chart-pie"></i> Ringkasan Area Saya</div>
                <div class="section-actions">
                    <button id="statsCustomizeBtn" class="section-action-btn" type="button" style="display:none;">Atur</button>
                    <button id="statsToggleBtn" class="section-action-btn" type="button" style="display:none;">Lihat Semua</button>
                </div>
            </div>
            <div class="stats-grid" id="statsGrid">
                <div class="stat-card" data-stat-key="total" data-stat-label="Total Pelanggan" onclick="bukaHalaman('tagih-{{ url('/pelanggan') }}?filter=semua')">
                    <div class="stat-icon icon-blue"><i class="fas fa-users"></i></div>
                    <div class="stat-value" id="statPelanggan">0</div>
                    <div class="stat-label">Total Pelanggan</div>
                </div>
                <div class="stat-card" data-stat-key="baru" data-stat-label="Pelanggan Baru" onclick="bukaHalaman('tagih-{{ url('/pelanggan') }}?filter=baru')">
                    <div class="stat-icon icon-purple"><i class="fas fa-user-plus"></i></div>
                    <div class="stat-value" id="statBaru">0</div>
                    <div class="stat-label">Pelanggan Baru<br>(Bulan Ini)</div>
                </div>
                <div class="stat-card" data-stat-key="belum" data-stat-label="Belum Bayar" onclick="bukaHalaman('tagih-{{ url('/pelanggan') }}?filter=belum_bayar')">
                    <div class="stat-icon icon-orange"><i class="fas fa-file-invoice-dollar"></i></div>
                    <div class="stat-value" id="statBelum">0</div>
                    <div class="stat-label">Belum Bayar</div>
                </div>
                <div class="stat-card" data-stat-key="isolir" data-stat-label="Pelanggan Isolir" onclick="bukaHalaman('tagih-{{ url('/pelanggan') }}?filter=isolir')">
                    <div class="stat-icon icon-red"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="stat-value" id="statIsolir">0</div>
                    <div class="stat-label">Pelanggan Isolir<br>(Jatuh Tempo)</div>
                </div>
                <div class="stat-card" data-stat-key="lunas_saya" data-stat-label="Lunas by Saya" onclick="bukaHalaman('tagih-{{ url('/pelanggan') }}?filter=lunas_saya')">
                    <div class="stat-icon icon-green"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-value" id="statLunasSaya">0</div>
                    <div class="stat-label">Lunas by Saya</div>
                </div>
                <div class="stat-card" data-stat-key="lunas_admin" data-stat-label="Lunas by Admin" onclick="bukaHalaman('tagih-{{ url('/pelanggan') }}?filter=lunas_admin')">
                    <div class="stat-icon icon-dark"><i class="fas fa-building"></i></div>
                    <div class="stat-value" id="statLunasAdmin">0</div>
                    <div class="stat-label">Lunas by Admin</div>
                </div>
            </div>
        </div>

        <!-- Grid Menu Dinamis -->
        <div class="section-title"><i class="fas fa-th-large"></i> Menu Pintasan</div>
        <div class="menu-grid" id="mainMenuGrid">

            <!-- Menu Khusus Agen -->
            <div class="menu-card menu-penagih" onclick="bukaHalaman('tagih-{{ url('/pelanggan') }}')">
                <div class="menu-icon bg-green"><i class="fas fa-hand-holding-usd"></i></div>
                <div class="menu-text">Tagihan Pelanggan</div>
            </div>

            <div class="menu-card menu-penagih" onclick="bukaHalaman('{{ url('/pembukuan-saya') }}')">
                <div class="menu-icon bg-blue"><i class="fas fa-receipt"></i></div>
                <div class="menu-text">Pembukuan</div>
            </div>

            <!-- Menu Khusus Teknisi -->
            <div class="menu-card menu-teknisi" onclick="bukaHalaman('{{ url('/troubleshoot') }}')">
                <div class="menu-icon bg-orange"><i class="fas fa-tools"></i></div>
                <div class="menu-text">Gangguan</div>
            </div>

            <div class="menu-card menu-teknisi" onclick="bukaHalaman('{{ url('/pemasangan-baru') }}')">
                <div class="menu-icon bg-purple"><i class="fas fa-network-wired"></i></div>
                <div class="menu-text">Pemasangan Baru</div>
            </div>

            <div class="menu-card menu-teknisi menu-penagih" onclick="bukaHalaman('{{ url('/messaging-lapangan') }}')">
                <div class="menu-icon" style="background:linear-gradient(135deg,#0d9488,#14b8a6);"><i class="fas fa-comments"></i></div>
                <div class="menu-text">Chat lapangan</div>
            </div>
        </div>

        <!-- Notif Singkat Darurat (Opsional dimunculkan saat ada tugas) -->
        <div class="section-title" id="tugasTitle" style="display: none; justify-content: space-between;">
            <span><i class="fas fa-clipboard-list"></i> Tugas Mendesak</span>
            <span class="task-title-row-action" onclick="lihatSemuaTugas()">Lihat Semua</span>
        </div>
        <div class="task-list" id="pendingTasks">
            <!-- Di-generate via JS jika ada tugas di Firestore -->
        </div>

    </div>

    <!-- Modal Profil & Keluar -->
    <div id="profileModal">
        <div class="modal-bottom" id="modalBottomContent">
            <h3 style="margin-bottom: 5px; font-size: 18px;" id="modalNama">Nama Karyawan</h3>
            <p style="color: #64748b; font-size: 13px; margin-bottom: 25px;" id="modalEmail">teknisi@sansspeed.id</p>

            <button class="btn-full btn-danger" id="btnKeluar">
                <i class="fas fa-sign-out-alt"></i> Akhiri Sesi Kerja (Logout)
            </button>
            <button class="btn-full btn-outline" onclick="tutupModalProfil()">
                Tutup Papan
            </button>
        </div>
    </div>

    <!-- Local API Auth System -->
    <script type="module">
        import { auth, apiFetch, resolveRoleKey, isAdminAppRole } from '{{ asset('api-config.js') }}';
        const FRONT_STATS_KEY = 'ss_front_stats';
        const DEFAULT_FRONT_STATS = ['belum', 'isolir', 'lunas_saya'];
        const MAX_FRONT_STATS = 3;
        const normalizeAreaKey = (val) => String(val || '').trim().toLowerCase();
        const extractAreaRefs = (raw) => {
            if (raw === null || typeof raw === 'undefined') return [];
            
            // Jika sudah array, proses setiap elemennya
            if (Array.isArray(raw)) return raw.flatMap(extractAreaRefs);
            
            // Jika Object (misal doc Firestore), ambil property id/nama/area/value
            if (typeof raw === 'object') {
                const vals = [raw.id, raw.nama, raw.area, raw.value].filter(Boolean);
                if (vals.length > 0) return vals.flatMap(extractAreaRefs);
                return []; // Object kosong atau tidak punya properti yg relevan
            }
            
            const txt = String(raw).trim();
            if (!txt) return [];
            
            // Coba parsing jika string terlihat seperti JSON array/object
            if ((txt.startsWith('[') && txt.endsWith(']')) || (txt.startsWith('{') && txt.endsWith('}'))) {
                try {
                    return extractAreaRefs(JSON.parse(txt));
                } catch {
                    // Jika gagal parse JSON, lanjutkan proses string sebagai nilai literal
                }
            }
            
            // Jika string dipisahkan koma (misal: "Induk, Cabang 1")
            if (txt.includes(',')) return txt.split(',').flatMap(extractAreaRefs);
            
            // String tunggal biasa (misal: "Induk")
            return [txt];
        };
        const buildAreaAliases = (areas) => {
            const aliasMap = new Map();
            areas.forEach(a => {
                const idKey = normalizeAreaKey(a?.id);
                const nameKey = normalizeAreaKey(a?.nama);
                if (idKey) aliasMap.set(idKey, idKey);
                if (nameKey && idKey) aliasMap.set(nameKey, idKey);
            });
            return aliasMap;
        };

        // Fungsi Waktu Sapaan
        const jam = new Date().getHours();
        const sapa = document.getElementById('waktuSapa');
        if (jam < 11) sapa.innerText = "Selamat Pagi,";
        else if (jam < 15) sapa.innerText = "Selamat Siang,";
        else if (jam < 18) sapa.innerText = "Selamat Sore,";
        else sapa.innerText = "Selamat Malam,";

        // Logic Proteksi Lapangan
        const getAvailableStatKeys = () => {
            const cards = Array.from(document.querySelectorAll('#statsGrid .stat-card'));
            return cards.map(c => c.dataset.statKey).filter(Boolean);
        };

        const sanitizeFrontStatKeys = (keys) => {
            const available = getAvailableStatKeys();
            const requested = Array.isArray(keys) ? keys : [];
            const unique = requested.filter((k, idx) => available.includes(k) && requested.indexOf(k) === idx);
            if (unique.length >= MAX_FRONT_STATS) return unique.slice(0, MAX_FRONT_STATS);
            const fallback = DEFAULT_FRONT_STATS.filter(k => available.includes(k));
            const filled = [...unique];
            fallback.forEach(k => {
                if (!filled.includes(k) && filled.length < MAX_FRONT_STATS) filled.push(k);
            });
            available.forEach(k => {
                if (!filled.includes(k) && filled.length < MAX_FRONT_STATS) filled.push(k);
            });
            return filled;
        };

        const getFrontStatKeys = () => {
            try {
                const parsed = JSON.parse(localStorage.getItem(FRONT_STATS_KEY) || '[]');
                return sanitizeFrontStatKeys(parsed);
            } catch {
                return sanitizeFrontStatKeys([]);
            }
        };

        const saveFrontStatKeys = (keys) => {
            const sanitized = sanitizeFrontStatKeys(keys);
            localStorage.setItem(FRONT_STATS_KEY, JSON.stringify(sanitized));
            return sanitized;
        };

        const applyFrontStatSelection = () => {
            const selected = getFrontStatKeys();
            document.querySelectorAll('#statsGrid .stat-card').forEach(card => {
                const key = card.dataset.statKey;
                card.classList.toggle('is-front', selected.includes(key));
            });
        };

        const openStatsCustomize = async () => {
            const cards = Array.from(document.querySelectorAll('#statsGrid .stat-card'));
            const selected = getFrontStatKeys();
            const html = cards.map(c => {
                const key = c.dataset.statKey;
                const label = c.dataset.statLabel || key;
                const checked = selected.includes(key) ? 'checked' : '';
                return `<label style="display:flex;align-items:center;gap:8px;margin:8px 0;color:#cbd5e1;">
                    <input type="checkbox" value="${key}" ${checked} style="width:16px;height:16px;">
                    <span>${label}</span>
                </label>`;
            }).join('');

            const result = await Swal.fire({
                title: 'Atur Ringkasan Depan',
                html: `<div style="text-align:left;font-size:13px;">Pilih maksimal ${MAX_FRONT_STATS} card untuk tampilan ringkas.<div style="margin-top:8px;">${html}</div></div>`,
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    const picks = Array.from(document.querySelectorAll('.swal2-container input[type="checkbox"]:checked')).map(i => i.value);
                    if (picks.length === 0 || picks.length > MAX_FRONT_STATS) {
                        Swal.showValidationMessage(`Pilih 1 sampai ${MAX_FRONT_STATS} card.`);
                        return null;
                    }
                    return picks;
                }
            });

            if (result.isConfirmed && result.value) {
                saveFrontStatKeys(result.value);
                applyFrontStatSelection();
            }
        };

        const setupStatsCollapse = () => {
            const grid = document.getElementById('statsGrid');
            const btn = document.getElementById('statsToggleBtn');
            const customizeBtn = document.getElementById('statsCustomizeBtn');
            if (!grid || !btn || !customizeBtn) return;

            applyFrontStatSelection();

            const isMobile = window.innerWidth < 768;
            if (!isMobile) {
                grid.classList.remove('compact');
                btn.style.display = 'none';
                customizeBtn.style.display = 'none';
                btn.textContent = 'Lihat Semua';
                return;
            }

            btn.style.display = 'inline-flex';
            customizeBtn.style.display = 'inline-flex';
            if (!grid.dataset.compactInit) {
                grid.classList.add('compact');
                grid.dataset.compactInit = '1';
                btn.textContent = 'Lihat Semua';
            }

            btn.onclick = () => {
                const isCompact = grid.classList.toggle('compact');
                btn.textContent = isCompact ? 'Lihat Semua' : 'Ringkas';
            };

            customizeBtn.onclick = openStatsCustomize;
        };

        window.addEventListener('resize', setupStatsCollapse);

        auth.onAuthStateChanged(async (user) => {
            if (user) {
                try {
                    const prof = JSON.parse(localStorage.getItem('ss_user'));
                    if (prof) {
                        const roleKey = resolveRoleKey(prof.roleKey || prof.role);
                        const r = String(prof.role || '').toLowerCase();
                        window.__lapanganProfile = { role: r, roleKey };

                        // Cegah non-karyawan / blokir
                        if (prof.aktif === false) {
                            alert("Akun anda sedang dinonaktifkan Admin!"); await auth.signOut(); return;
                        }

                        // Halaman lapangan khusus role lapangan, admin langsung ke dashboard admin.
                        if (isAdminAppRole(prof)) {
                            window.location.replace('{{ url('/dashboard-admin') }}');
                            return;
                        }

                        // Set Nama & Avatar
                        document.getElementById('userNameLabel').innerText = prof.nama;
                        document.getElementById('modalNama').innerText = prof.nama;
                        document.getElementById('modalEmail').innerText = prof.email;

                        let initials = ((prof.nama.match(/\b\w/g) || []).shift() || '') + ((prof.nama.match(/\b\w/g) || []).pop() || '');
                        document.getElementById('avatarHeader').innerHTML = `${initials.toUpperCase()}<div class="notif-badge" id="notifBadge"></div>`;

                        // ENGINE RENDER BERBASIS ROLE TUNGGAL HTML
                        document.getElementById('userRoleBadge').innerHTML = `<i class="fas fa-id-badge"></i> ${r.toUpperCase()}`;

                        // Ekstrak referensi Nodes Menu
                        const mPend = document.querySelectorAll('.menu-penagih');
                        const mTekn = document.querySelectorAll('.menu-teknisi');
                        // Matikan Loading Tabir
                        setTimeout(() => { document.getElementById('loadingApp').style.display = 'none'; }, 300);

                        if (roleKey === 'tekpen') {
                            // Buka Semua
                            mPend.forEach(e => e.style.display = 'block');
                            mTekn.forEach(e => e.style.display = 'block');

                        } else if (roleKey === 'penagih') {
                            mPend.forEach(e => e.style.display = 'block');     // Buka menu agen
                            mTekn.forEach(e => e.style.display = 'none');      // Tutup Tools Kabel

                        } else if (roleKey === 'teknisi') {
                            mPend.forEach(e => e.style.display = 'none');      // Tutup Uang
                            mTekn.forEach(e => e.style.display = 'block');     // Buka Tools
                        }

                        // --- LOGIC LOAD STATS AGEN ---
                        if (['penagih', 'tekpen'].includes(roleKey)) {
                            document.getElementById('penagihStatsContainer').style.display = 'block';
                            document.getElementById('statsGrid').style.display = 'grid';
                            setupStatsCollapse();

                            try {
                                const resPel = await apiFetch('/collections/pelanggan');
                                let pelanggan = Array.isArray(resPel) ? resPel : (resPel.data || []);
                                let areaData = [];
                                try {
                                    const resArea = await apiFetch('/collections/areas');
                                    areaData = Array.isArray(resArea) ? resArea : (resArea.data || []);
                                } catch (areaErr) {
                                    console.warn('Gagal memuat master area untuk sinkronisasi stats:', areaErr);
                                }
                                const areaAliasMap = buildAreaAliases(areaData);

                                // Filter by Area Agen
                                if (!isAdminAppRole(prof)) {
                                    const assignedAreas = extractAreaRefs(prof.areas);
                                    const canonicalAreas = [...new Set(assignedAreas
                                        .map(a => String(a || '').trim())
                                        .filter(Boolean)
                                        .filter(a => a.toLowerCase() !== '[object object]')
                                        .map(a => areaAliasMap.get(normalizeAreaKey(a)) || normalizeAreaKey(a)))];

                                    if (canonicalAreas.length > 0) {
                                        pelanggan = pelanggan.filter(p => {
                                            const customerAreaKey = normalizeAreaKey(p.area);
                                            const canonicalCustomerArea = areaAliasMap.get(customerAreaKey) || customerAreaKey;
                                            return canonicalAreas.includes(canonicalCustomerArea);
                                        });
                                    } else {
                                        // Non-admin tanpa assignment area: jangan tampilkan data area lain.
                                        pelanggan = [];
                                    }
                                }


                                document.getElementById('statPelanggan').innerText = pelanggan.length;

                                const now = new Date();
                                const currMonthInt = now.getMonth() + 1;
                                const currYearStr = String(now.getFullYear());

                                // Hitung pelanggan baru (Bulan Ini) - Sync 100% dengan Admin (pakai createdAt)
                                const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1).getTime();
                                const endOfMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0, 23, 59, 59).getTime();

                                const newThisMonth = pelanggan.filter(p => {
                                    const created = p.createdAt ? new Date(p.createdAt).getTime() : 0;
                                    return created >= startOfMonth && created <= endOfMonth;
                                });
                                document.getElementById('statBaru').innerText = newThisMonth.length;

                                // Ambil Tagihan LIVE untuk bulan ini
                                const resTagihan = await apiFetch(`/collections/tagihan_bulanan?bulan=${currMonthInt}&tahun=${currYearStr}`);
                                const tagihanRaw = Array.isArray(resTagihan) ? resTagihan : (resTagihan.data || []);

                                // Buat Map untuk cross-reference
                                const tagihanMap = {};
                                tagihanRaw.forEach(t => {
                                    tagihanMap[t.idPelanggan] = t;
                                });

                                let belum = 0;
                                let isolir = 0;
                                let lunasSaya = 0;
                                let lunasAdmin = 0;

                                pelanggan.forEach(p => {
                                    const pelId = p.idPelanggan || p.id;
                                    const tagihan = tagihanMap[pelId];
                                    const statusBulanIni = tagihan ? tagihan.status : 'belum';

                                    if (statusBulanIni === 'belum') {
                                        belum++;
                                    } else if (statusBulanIni === 'lunas') {
                                        const dibayarKe = tagihan.dibayar_ke ? String(tagihan.dibayar_ke).trim().toLowerCase() : '';
                                        const namaku = prof.nama ? String(prof.nama).trim().toLowerCase() : '';

                                        if (dibayarKe === namaku) {
                                            lunasSaya++;
                                        } else if (!dibayarKe || dibayarKe === '') {
                                            lunasAdmin++;
                                        }
                                    }

                                    // Samakan logika Isolir 100% dengan Main App Admin:
                                    if (p.status === 'isolir') {
                                        isolir++;
                                    }
                                });

                                document.getElementById('statBelum').innerText = belum;
                                document.getElementById('statIsolir').innerText = isolir;
                                document.getElementById('statLunasSaya').innerText = lunasSaya;
                                document.getElementById('statLunasAdmin').innerText = lunasAdmin;

                            } catch (e) {
                                console.error("Gagal load stats", e);
                            }
                        }
                        try {
                            const tugasRes = await apiFetch('/tugas?status=pending');
                            const tasks = tugasRes.data || [];
                            const tugasTitle = document.getElementById('tugasTitle');
                            const pendingWrap = document.getElementById('pendingTasks');
                            const role = (prof.role || '').toLowerCase();
                            const myUid = prof.uid || prof.id;
                            const filtered = tasks.filter(t => {
                                const isBroadcast = Number(t.isBroadcast || 0) === 1;
                                const isMine = t.assignTo && myUid && t.assignTo === myUid;
                                if (role === 'admin' || role === 'superadmin') return true;
                                if (role === 'teknisi') return (t.jenisTask === 'troubleshoot' || t.jenisTask === 'pemasangan') && (isMine || (isBroadcast && !t.claimedBy));
                                if (role === 'penagih') return (t.jenisTask === 'tagih' || t.jenisTask === 'collection') && (isMine || (isBroadcast && !t.claimedBy));
                                return isMine || (isBroadcast && !t.claimedBy);
                            }).slice(0, 3);
                            window.__lapanganUrgentKinds = filtered.map(t => String(t.jenisTask || '').toLowerCase());

                            if (filtered.length > 0) {
                                tugasTitle.style.display = 'flex';
                                pendingWrap.style.display = 'flex';
                                pendingWrap.innerHTML = filtered.map(t => {
                                    const highCls = t.prioritas === 'tinggi' ? 'high' : '';
                                    let goTo = '{{ url('/troubleshoot') }}';
                                    if (t.jenisTask === 'pemasangan') goTo = '{{ url('/pemasangan-baru') }}';
                                    if (t.jenisTask === 'tagih') goTo = 'tagih-{{ url('/pelanggan') }}';
                                    if (t.jenisTask === 'collection') goTo = '{{ url('/pembukuan-saya') }}';
                                    return `
                                        <div class="task-card ${highCls}" onclick="bukaHalaman('${goTo}')">
                                            <div class="task-title">${t.judul || 'Tugas Lapangan'}</div>
                                            <div class="task-meta"><span>📍 ${t.alamat || '-'}</span><span>👷 ${t.assignToNama || 'Broadcast'}</span></div>
                                        </div>
                                    `;
                                }).join('');
                            }
                        } catch (taskErr) {
                            console.error('Gagal memuat tugas mendesak:', taskErr);
                        }
                        // --- END LOGIC LOAD STATS ---

                    } else {
                        // User Auth ada tp hilang dari localstorage (Bisa bug sinkronisasi cache / Didelete manual)
                        alert("Fail-safe Trigger: Sesi tidak dikenal!");
                        await auth.signOut();
                    }
                } catch (e) {
                    alert("Akses Server Offline."); console.error(e);
                }
            } else {
                window.location.replace("{{ url('/login') }}");
            }
        });

        // Theme Toggle
        document.addEventListener('DOMContentLoaded', () => {
            const btn = document.getElementById('themeToggleBtn');
            if (btn) {
                const isLightInit = window.__ssTheme === 'light';
                if (isLightInit) {
                    document.body.classList.add('light-mode');
                    btn.innerHTML = '<i class="fas fa-sun"></i>';
                }
                btn.addEventListener('click', () => {
                    document.body.classList.toggle('light-mode');
                    const isLight = document.body.classList.contains('light-mode');
                    localStorage.setItem('ss_theme', isLight ? 'light' : 'dark');
                    btn.innerHTML = isLight ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
                });
            }
        });

        // Logout Eksekutor
        document.getElementById('btnKeluar').addEventListener('click', async () => {
            const result = await Swal.fire({
                title: 'Akhiri Pekerjaan?',
                text: "Anda akan keluar dari sesi ini.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal',
                reverseButtons: true
            });
            if (result.isConfirmed) {
                await auth.signOut();
            }
        });

    </script>

    <!-- Modal UI Sederhana -->
    <script>
        const pm = document.getElementById('profileModal');
        const pmb = document.getElementById('modalBottomContent');

        function bukaModalProfil() {
            pm.style.display = 'flex';
            // Sedikit delay agar property display:flex teraplikasikan sebelum transition opacity
            setTimeout(() => {
                pm.classList.add('show');
                pmb.classList.add('open');
            }, 10);
        }

        function tutupModalProfil() {
            pmb.classList.remove('open');
            pm.classList.remove('show');
            setTimeout(() => { pm.style.display = 'none'; }, 300); // Tunggu animasi selesai
        }

        // Close modal when clicking outside the bottom sheet
        pm.addEventListener('click', (e) => {
            if (e.target === pm) {
                tutupModalProfil();
            }
        });

        // Redirect Alert / Routing
        function lihatSemuaTugas() {
            const role = String(window.__lapanganProfile?.role || '').toLowerCase();
            const kinds = Array.isArray(window.__lapanganUrgentKinds) ? window.__lapanganUrgentKinds : [];
            const has = (jenis) => kinds.includes(jenis);
            let target = '{{ url('/tugas-teknisi') }}';

            // Pastikan user lapangan tetap berada di alur aplikasi lapangan.
            if (role === 'teknisi') {
                target = has('pemasangan') && !has('troubleshoot') ? '{{ url('/pemasangan-baru') }}' : '{{ url('/troubleshoot') }}';
            } else if (role === 'penagih') {
                target = has('collection') && !has('tagih') ? '{{ url('/pembukuan-saya') }}' : 'tagih-{{ url('/pelanggan') }}';
            } else if (role === 'tekpen' || role === 'teknisipenagih') {
                if (has('tagih') || has('collection')) {
                    target = 'tagih-{{ url('/pelanggan') }}';
                } else {
                    target = has('pemasangan') && !has('troubleshoot') ? '{{ url('/pemasangan-baru') }}' : '{{ url('/troubleshoot') }}';
                }
            } else if (role === 'admin' || role === 'superadmin') {
                target = '{{ url('/tugas-teknisi') }}';
            }

            bukaHalaman(target);
        }

        function bukaHalaman(url) {
            document.getElementById('loadingApp').style.opacity = '1';
            document.getElementById('loadingApp').style.display = 'flex';

            setTimeout(() => {
                const basePath = url.split('?')[0];

                // Route valid pages directly; keep fallback alert for unfinished modules.
                if (basePath === 'tagih-{{ url('/pelanggan') }}' || basePath === '{{ url('/pembukuan-saya') }}' || basePath === '{{ url('/pembukuan-kang-tagih') }}' || basePath === '{{ url('/tugas-teknisi') }}' || basePath === '{{ url('/troubleshoot') }}' || basePath === '{{ url('/pemasangan-baru') }}') {
                    window.location.href = url;
                } else {
                    alert("Aplikasi Lapangan: " + url + "\n(Dalam Konstruksi Sub-pages / Menunggu Update Tahap Berikutnya)");
                    document.getElementById('loadingApp').style.opacity = '0';
                    setTimeout(() => { document.getElementById('loadingApp').style.display = 'none'; }, 500);
                }
            }, 500);
        }

    </script>
</body>

</html>