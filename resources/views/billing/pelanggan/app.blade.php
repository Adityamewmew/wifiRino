<!DOCTYPE html>
<html lang="id">

<head>
    <script src="{{ asset('js/ss-storage-migrate.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Sans Speed - Portal Pelanggan</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Styles CSS Utama agar tetap sinkron dengan tema -->
    <link rel="stylesheet" href="{{ asset('style.css') }}">

    <style>
        /* --- KHUSUS APP PELANGGAN (MOBILE FIRST OVERRIDES) --- */
        body {
            background-color: var(--bg-color);
            color: var(--text-primary);
            overflow-x: hidden;
            -webkit-tap-highlight-color: transparent;
            /* Hilangkan blink tap di HP */
            font-family: 'Inter', sans-serif;
        }

        /* Container Aplikasi dibatasi lebarnya seperti layer HP */
        .mobile-container {
            max-width: 480px;
            margin: 0 auto;
            min-height: 100vh;
            background: rgba(15, 23, 42, 0.95);
            /* Gradient background halus */
            background-image:
                radial-gradient(circle at 10% 20%, rgba(59, 130, 246, 0.1) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(139, 92, 246, 0.1) 0%, transparent 40%);
            position: relative;
            box-shadow: 0 0 40px rgba(0, 0, 0, 0.5);
            display: flex;
            flex-direction: column;
        }

        /* Mode Terang Tetap Konsisten */
        body.light-mode .mobile-container {
            background-color: #f8fafc;
            background-image: none;
            box-shadow: 0 0 30px rgba(148, 163, 184, 0.2);
        }

        /* --- LOGIN SCREEN --- */
        #loginScreen {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 40px 30px;
            animation: fadeIn 0.5s ease;
        }

        .login-brand {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-brand i {
            font-size: 50px;
            color: var(--primary);
            filter: drop-shadow(0 0 15px var(--primary-glow));
            margin-bottom: 15px;
        }

        .login-brand h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 28px;
            font-weight: 800;
            margin: 0 0 5px 0;
            background: linear-gradient(to right, #fff, #94a3b8);
            -webkit-background-clip: text;
            background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        body.light-mode .login-brand h1 {
            background: linear-gradient(to right, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            background-clip: text;
        }

        .login-brand p {
            color: var(--text-secondary);
            font-size: 14px;
            margin: 0;
        }

        .input-group {
            position: relative;
            margin-bottom: 25px;
        }

        .input-group i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-secondary);
            font-size: 18px;
            transition: color 0.3s;
        }

        .login-input {
            width: 100%;
            padding: 18px 20px 18px 50px;
            background: rgba(30, 41, 59, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            color: white;
            font-family: 'Inter', sans-serif;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s;
        }

        body.light-mode .login-input {
            background: white;
            border-color: #cbd5e1;
            color: #1e293b;
            box-shadow: 0 4px 15px rgba(148, 163, 184, 0.1);
        }

        .login-input:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(30, 41, 59, 0.9);
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        }

        body.light-mode .login-input:focus {
            background: white;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        }

        .login-input:focus+i {
            color: var(--primary);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            width: 100%;
            padding: 18px;
            border-radius: 16px;
            font-weight: 700;
            font-size: 16px;
            font-family: 'Outfit', sans-serif;
            letter-spacing: 0.5px;
            border: none;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
            transition: transform 0.2s, box-shadow 0.2s;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .btn-login:active {
            transform: scale(0.98);
        }

        .login-footer {
            margin-top: 30px;
            text-align: center;
            color: var(--text-secondary);
            font-size: 12px;
        }

        /* --- DASHBOARD SCREEN --- */
        #dashboardScreen {
            display: none;
            flex-direction: column;
            flex: 1;
            padding: 20px;
            animation: slideUp 0.4s ease;
        }

        /* Modern App Header */
        .app-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-top: 10px;
        }

        .user-greeting {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 800;
            color: white;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }

        .greeting-text p {
            margin: 0;
            font-size: 12px;
            color: var(--text-secondary);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .greeting-text h2 {
            margin: 0;
            font-size: 18px;
            font-family: 'Outfit', sans-serif;
            font-weight: 700;
            color: var(--text-primary);
        }

        body.light-mode .greeting-text h2 {
            color: #0f172a;
        }

        .btn-logout {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.2s;
        }

        body.light-mode .btn-logout {
            background: white;
            border-color: #cbd5e1;
            color: #64748b;
        }

        .btn-logout:hover {
            color: var(--accent);
            background: rgba(244, 63, 94, 0.1);
            border-color: rgba(244, 63, 94, 0.3);
        }

        /* Live Status Marquee / Notification */
        .notif-marquee {
            background: rgba(59, 130, 246, 0.1);
            border-left: 3px solid var(--primary);
            border-radius: 8px;
            padding: 12px 15px;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
            overflow: hidden;
            position: relative;
        }

        body.light-mode .notif-marquee {
            background: #eff6ff;
        }

        .notif-marquee i {
            color: var(--primary);
            font-size: 18px;
            flex-shrink: 0;
            animation: pulse-icon 2s infinite;
        }

        @keyframes pulse-icon {
            0% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
                filter: drop-shadow(0 0 5px var(--primary-glow));
            }

            100% {
                transform: scale(1);
            }
        }

        .notif-text {
            color: var(--text-primary);
            font-size: 13px;
            font-weight: 500;
            white-space: normal;
        }

        body.light-mode .notif-text {
            color: #1e293b;
        }

        .notif-item {
            font-size: 12px;
            line-height: 1.4;
            margin-bottom: 6px;
        }

        .notif-item:last-child {
            margin-bottom: 0;
        }

        /* Tagihan Card Utama */
        .bill-card {
            background: linear-gradient(135deg, rgba(30, 41, 59, 0.9), rgba(15, 23, 42, 0.9));
            border-radius: 24px;
            padding: 25px;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            margin-bottom: 25px;
        }

        body.light-mode .bill-card {
            background: linear-gradient(135deg, #ffffff, #f8fafc);
            border-color: #e2e8f0;
            box-shadow: 0 15px 35px rgba(148, 163, 184, 0.15);
        }

        /* Dekorasi Lingkaran */
        .bill-card::before {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.2), transparent 70%);
            z-index: 0;
        }

        .bill-card-content {
            position: relative;
            z-index: 1;
        }

        .bill-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .bill-month {
            font-size: 14px;
            color: var(--text-secondary);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Status Badges */
        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .status-lunas {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        .status-belum {
            background: rgba(245, 158, 11, 0.15);
            color: #fbbf24;
            border: 1px solid rgba(245, 158, 11, 0.3);
        }

        .status-isolir {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
            animation: pulse-danger 2s infinite;
        }

        @keyframes pulse-danger {
            0% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }

        .bill-amount-label {
            font-size: 13px;
            color: var(--text-secondary);
            margin-bottom: 5px;
        }

        .bill-amount-value {
            font-family: 'Outfit', sans-serif;
            font-size: 36px;
            font-weight: 800;
            margin: 0 0 15px 0;
            color: var(--text-primary);
        }

        body.light-mode .bill-amount-value {
            color: #0f172a;
        }

        .bill-due-date {
            background: rgba(0, 0, 0, 0.2);
            padding: 10px 15px;
            border-radius: 12px;
            font-size: 13px;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        body.light-mode .bill-due-date {
            background: #f1f5f9;
        }

        .bill-due-date i {
            color: var(--warning);
        }

        /* Detail Information (Paket) */
        .info-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 25px;
        }

        body.light-mode .info-card {
            background: #ffffff;
            border-color: #e2e8f0;
            box-shadow: 0 4px 15px rgba(148, 163, 184, 0.05);
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px dashed rgba(255, 255, 255, 0.1);
        }

        body.light-mode .info-row {
            border-bottom-color: #e2e8f0;
        }

        .info-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .info-row:first-child {
            padding-top: 0;
        }

        .info-label {
            color: var(--text-secondary);
            font-size: 13px;
        }

        .info-value {
            font-weight: 600;
            color: var(--text-primary);
            font-size: 14px;
            text-align: right;
        }

        body.light-mode .info-value {
            color: #1e293b;
        }

        /* Action Buttons */
        .action-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 30px;
        }

        .btn-action-app {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: var(--text-primary);
            padding: 16px;
            border-radius: 16px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            transition: all 0.2s;
            cursor: pointer;
        }

        body.light-mode .btn-action-app {
            background: white;
            border-color: #cbd5e1;
            color: #334155;
            box-shadow: 0 4px 6px rgba(148, 163, 184, 0.05);
        }

        .btn-action-app i {
            font-size: 24px;
        }

        .btn-action-app:active {
            transform: scale(0.95);
            background: rgba(255, 255, 255, 0.1);
        }

        .btn-wa {
            color: #10b981;
        }

        .btn-wa i {
            color: #10b981;
        }

        .btn-history {
            color: var(--primary);
        }

        .btn-history i {
            color: var(--primary);
        }

        /* Histori Bottom Sheet */
        .history-sheet {
            position: fixed;
            bottom: -100%;
            left: 0;
            right: 0;
            background: var(--card-bg);
            border-top-left-radius: 24px;
            border-top-right-radius: 24px;
            padding: 24px 20px;
            z-index: 1000;
            box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.3);
            transition: bottom 0.4s cubic-bezier(0.175, 0.885, 0.32, 1);
            max-height: 85vh;
            display: flex;
            flex-direction: column;
        }

        body.light-mode .history-sheet {
            background: #ffffff;
            box-shadow: 0 -10px 40px rgba(148, 163, 184, 0.2);
        }

        /* Constraint lebar untuk tampilan Desktop (mirip emulator HP di web) */
        @media (min-width: 481px) {
            .history-sheet {
                left: 50%;
                transform: translateX(-50%);
                width: 480px;
            }
        }

        .history-sheet.show {
            bottom: 0;
        }

        .sheet-handle {
            width: 40px;
            height: 5px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 5px;
            margin: 0 auto 20px auto;
        }

        body.light-mode .sheet-handle {
            background: #cbd5e1;
        }

        .history-list {
            flex: 1;
            overflow-y: auto;
            margin-top: 15px;
            padding-right: 5px;
        }

        .history-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px;
            background: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            margin-bottom: 12px;
        }

        body.light-mode .history-item {
            background: #f8fafc;
            border-color: #e2e8f0;
        }

        .h-month {
            font-weight: 700;
            color: var(--text-primary);
            font-size: 15px;
            margin-bottom: 4px;
        }

        body.light-mode .h-month {
            color: #0f172a;
        }

        .h-date {
            font-size: 12px;
            color: var(--text-secondary);
        }

        .h-amount {
            font-weight: 800;
            color: #34d399;
            /* success green */
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
        }

        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.7);
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

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Utilities */
        .hidden {
            display: none !important;
        }

        .spinner-mini {
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body>
@include('billing.partials.web-bootstrap')

    <!-- Theme Toggle (Opsional, untuk konsistensi) -->
    <div style="position:fixed; top:15px; right:15px; z-index: 100;">
        <button id="themeToggleBtn"
            style="background:rgba(0,0,0,0.5); border-radius:50%; width:40px; height:40px; color:white; border:1px solid rgba(255,255,255,0.1); backdrop-filter:blur(5px);">
            <i class="fas fa-moon"></i>
        </button>
    </div>

    <div class="mobile-container">

        <!-- ============================== -->
        <!-- SCREEN 1: LOGIN PELANGGAN      -->
        <!-- ============================== -->
        <div id="loginScreen">
            <div class="login-brand">
                <i class="fas fa-wifi"></i>
                <h1>My Sans Speed</h1>
                <p>Portal Layanan Pelanggan</p>
            </div>

            <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid #ef4444; color: #ef4444; padding: 12px; border-radius: 12px; margin-bottom: 20px; font-size: 13px; display: none;"
                id="loginError">
                <i class="fas fa-exclamation-circle"></i> <span id="errorText">ID atau No WhatsApp tidak sesuai.</span>
            </div>

            <div class="input-group">
                <input type="text" id="inputId" class="login-input" placeholder="ID Pelanggan (Cth: C001)"
                    autocomplete="off">
                <i class="fas fa-id-card"></i>
            </div>

            <div class="input-group">
                <input type="tel" id="inputWa" class="login-input" placeholder="Nomor WhatsApp (Terdaftar)"
                    autocomplete="off">
                <i class="fab fa-whatsapp"></i>
            </div>

            <button class="btn-login" id="btnLogin" onclick="prosesLogin()">
                <span id="btnText">Masuk Sekarang</span>
            </button>

            <div class="login-footer">
                <p>Tidak tahu ID Pelanggan Anda?<br>Silakan cek pesan tagihan terakhir dari Sans Speed</p>
            </div>
        </div>


        <!-- ============================== -->
        <!-- SCREEN 2: DASHBOARD UTAMA      -->
        <!-- ============================== -->
        <div id="dashboardScreen">

            <div class="app-header">
                <div class="user-greeting">
                    <div class="avatar" id="userInitial">C</div>
                    <div class="greeting-text">
                        <p>Selamat Datang,</p>
                        <h2 id="userNameFull">Nama Pelanggan</h2>
                    </div>
                </div>
                <button class="btn-logout" onclick="prosesLogout()" title="Keluar">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </div>

            <!-- In-App Notification (Dynamic Broadcast) -->
            <div class="notif-marquee" id="notifBoard" style="display: none;">
                <i class="fas fa-info-circle"></i>
                <div style="flex: 1; overflow: hidden;">
                    <div class="notif-text" id="notifText">
                        Tidak ada pengumuman saat ini.
                    </div>
                    <div id="notifMeta" style="font-size:10px; color:var(--text-secondary); margin-top:4px;">Update: -</div>
                </div>
            </div>

            <!-- Kartu Tagihan -->
            <div class="bill-card">
                <div class="bill-card-content">
                    <div class="bill-header">
                        <div class="bill-month" id="billMonthStr">OKTOBER 2026</div>
                        <div class="" id="billBadgeContainer">
                            <div class="spinner-mini" style="border-width: 2px; width:15px; height:15px;"></div>
                        </div>
                    </div>

                    <div class="bill-amount-label">Total Tagihan Anda</div>
                    <div class="bill-amount-value" id="billAmountStr">Rp 0</div>

                    <div class="bill-due-date">
                        <i class="fas fa-calendar-alt"></i> Jatuh Tempo: <strong id="billDateStr">Tgl 10</strong>
                    </div>
                </div>
            </div>

            <!-- Info Paket -->
            <h3
                style="font-size: 15px; font-weight: 700; margin-bottom: 12px; color: var(--text-primary); font-family: 'Outfit', sans-serif;">
                Detail Layanan</h3>
            <div class="info-card">
                <div class="info-row">
                    <span class="info-label">ID Akun</span>
                    <span class="info-value" id="infoId">C000</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Paket Internet</span>
                    <span class="info-value" id="infoPaket">-</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Lokasi / Area</span>
                    <span class="info-value" id="infoArea">-</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Berlangganan Sejak</span>
                    <span class="info-value" id="infoMulai">-</span>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="action-grid">
                <div class="btn-action-app btn-wa" onclick="hubungiAdminCS()">
                    <i class="fab fa-whatsapp"></i>
                    Bantuan / CS
                </div>
                <div class="btn-action-app btn-history" onclick="bukaHistori()">
                    <i class="fas fa-clipboard-list"></i>
                    Riwayat Lunas
                </div>
            </div>

            <div
                style="text-align: center; font-size: 11px; color: var(--text-secondary); margin-top: auto; padding-top: 20px;">
                Sans Speed Billing V3.0 &copy; 2026
            </div>
        </div>

        <!-- ============================== -->
        <!-- BOTTOM SHEET: RIWAYAT          -->
        <!-- ============================== -->
        <div class="modal-overlay" id="historyOverlay" onclick="tutupHistori()"></div>
        <div class="history-sheet" id="historySheet">
            <div class="sheet-handle"></div>
            <h3
                style="font-weight: 800; font-size: 20px; font-family: 'Outfit', sans-serif; color: var(--text-primary);">
                <i class="fas fa-history" style="color:var(--primary); margin-right:8px;"></i> Riwayat Pembayaran
            </h3>
            <p style="color: var(--text-secondary); font-size: 13px; margin-bottom: 5px;">Rekam jejak pelunasan tagihan
                Anda sebelumnya.</p>

            <div class="history-list" id="historyListContent">
                <!-- Data akan di-inject kesini -->
                <div style="text-align:center; padding: 30px; color: var(--text-secondary); font-size: 13px;">
                    <div class="spinner-mini"
                        style="margin: 0 auto 10px auto; border-color: rgba(255,255,255,0.1); border-top-color: var(--primary);">
                    </div>
                    Memuat data...
                </div>
            </div>

            <button
                style="width: 100%; padding: 16px; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; color: var(--text-primary); font-weight: 600; margin-top: 15px;"
                onclick="tutupHistori()">
                Tutup Catatan
            </button>
        </div>

    </div>

    <!-- SCRIPT UTAMA MODULE -->
    <script type="module">
        import { apiFetch } from '{{ asset('api-config.js') }}';

        // Variables Global
        let myData = null;
        let today = new Date();
        const arrBulan = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        // DOM Elements setup
        const loginScreen = document.getElementById('loginScreen');
        const dashboardScreen = document.getElementById('dashboardScreen');
        const loginError = document.getElementById('loginError');
        const errorText = document.getElementById('errorText');
        const btnLogin = document.getElementById('btnLogin');
        const btnText = document.getElementById('btnText');

        // Check if already logged in via Session Storage
        window.addEventListener('DOMContentLoaded', () => {
            const savedSession = sessionStorage.getItem('ss_pelanggan_session');
            if (savedSession) {
                // Langsung masuk
                const parsed = JSON.parse(savedSession);
                myData = parsed;
                tampilkanDashboard();
            }
        });

        // ----------------------------------------------------
        // SYSTEM: LOGIN LOGIC
        // ----------------------------------------------------
        window.prosesLogin = async () => {
            const inId = document.getElementById('inputId').value.trim().toUpperCase();
            const inWa = document.getElementById('inputWa').value.trim();

            if (!inId || !inWa) {
                tampilError("Mohon isi ID dan Nomor WhatsApp Anda.");
                return;
            }

            // Bersihkan format WA jika user nulis +62 atau 08
            let bersihWa = inWa.replace(/\D/g, ''); // Ambil angka saja
            if (bersihWa.startsWith('62')) bersihWa = '0' + bersihWa.substring(2);

            tombolLoading(true);

            try {
                // Karena kita belum ada endpoint khusus Login Client di backend Express,
                // Kita gunakan trick fetch semua pelanggan lalu cari filter, atau panggil single data
                // Kita akan coba fetch semua dulu (Karena db kecil, ini sangat cepat)
                const res = await apiFetch('/collections/pelanggan');
                const pelangganList = res.data || [];

                const myProfile = pelangganList.find(p => p.id_pelanggan.toUpperCase() === inId);

                if (!myProfile) {
                    throw new Error("ID Pelanggan tidak ditemukan di sistem kami.");
                }

                // Cocokkan WA (Bisa agak longgar mentolerir spasi/strip yang sudah di regex)
                let dbWa = (myProfile.noWa || '').replace(/\D/g, ''); // Bersihkan data db juga
                if (dbWa.startsWith('62')) dbWa = '0' + dbWa.substring(2);

                if (bersihWa !== dbWa) {
                    throw new Error("Nomor WhatsApp tidak cocok dengan ID tersebut.");
                }

                // LOGIN SUKSES!
                myData = myProfile;
                // Simpan sesi sementara (Hilang saat browser close penuh)
                sessionStorage.setItem('ss_pelanggan_session', JSON.stringify(myData));

                tampilkanDashboard();

            } catch (e) {
                tampilError(e.message);
                console.error("Login Client Fail:", e);
            } finally {
                tombolLoading(false);
            }
        };

        function tampilError(msg) {
            errorText.innerText = msg;
            loginError.style.display = 'block';
            setTimeout(() => { loginError.style.display = 'none'; }, 4000);
        }

        function tombolLoading(isLoad) {
            if (isLoad) {
                btnLogin.disabled = true;
                btnLogin.innerHTML = `<div class="spinner-mini"></div>`;
            } else {
                btnLogin.disabled = false;
                btnLogin.innerHTML = `<span id="btnText">Masuk Sekarang</span>`;
            }
        }

        window.prosesLogout = () => {
            if (confirm("Apakah Anda yakin ingin keluar?")) {
                sessionStorage.removeItem('ss_pelanggan_session');
                myData = null;
                // Reset Forms
                document.getElementById('inputId').value = '';
                document.getElementById('inputWa').value = '';
                // Beralih screen
                dashboardScreen.style.display = 'none';
                loginScreen.style.display = 'flex';
            }
        };

        // ----------------------------------------------------
        // SYSTEM: DASHBOARD DISPLAY CALCULATION
        // ----------------------------------------------------
        async function tampilkanDashboard() {
            // Hide Login, Show Dash
            loginScreen.style.display = 'none';
            dashboardScreen.style.display = 'flex';

            // 1. Header Profile
            document.getElementById('userNameFull').innerText = myData.nama;
            document.getElementById('userInitial').innerText = (myData.nama || 'A').charAt(0).toUpperCase();

            // 2. Info Detail Basic
            document.getElementById('infoId').innerText = myData.id_pelanggan;
            document.getElementById('infoPaket').innerText = myData.paketInternet || 'Tidak Diset';
            document.getElementById('infoArea').innerText = myData.area || 'Reguler';
            const mBulan = arrBulan[parseInt(myData.bulanMulai) - 1] || '-';
            document.getElementById('infoMulai').innerText = `${mBulan} ${myData.tahunMulai || '-'}`;

            // Refresh Tagihan Terkini secara live dari database untuk memastikan akurasi
            await kalkulasiStatusBulanIni();

            // Cek Broadcast Message (Mockup/Palsu dulu jika db notif belum disetup)
            await cekPengumuman();
            if (window._pengumumanTimer) clearInterval(window._pengumumanTimer);
            window._pengumumanTimer = setInterval(() => {
                cekPengumuman().catch(() => { });
            }, 30000);
        }

        async function kalkulasiStatusBulanIni() {
            // Kita fetch ulang just in case (optional, tp lgs ambil spesifik record via API jika endpoint support param)
            // Atau cukup fetch dbnya
            try {
                // Ambil single data pelanggan terupdate
                const res = await apiFetch(`/collections/pelanggan/${myData.id}`);
                const freshData = res;

                const currM = String(today.getMonth() + 1).padStart(2, '0');
                const currY = String(today.getFullYear());

                document.getElementById('billMonthStr').innerText = `TAGIHAN: ${arrBulan[today.getMonth()].toUpperCase()} ${currY}`;
                document.getElementById('billDateStr').innerText = `Tgl ${freshData.tanggalJatuhTempo || '?'}`;

                const formatRp = (angka) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
                document.getElementById('billAmountStr').innerText = formatRp(freshData.hargaPaket || 0);

                let statusTeks = freshData.status_pembayaran; // 'belum' atau 'lunas'
                const badgeContainer = document.getElementById('billBadgeContainer');

                if (statusTeks === 'lunas') {
                    // LUNAS
                    badgeContainer.innerHTML = `<div class="status-badge status-lunas"><i class="fas fa-check-circle"></i> Lunas</div>`;
                    document.getElementById('billAmountStr').style.color = "#34d399";
                    document.getElementById('billAmountStr').innerHTML += ` <i class="fas fa-check-circle" style="font-size: 16px; margin-left: 5px; opacity: 0.5;"></i>`;
                } else {
                    // BELUM LUNAS
                    // Cek Isolir Threshold
                    let tagJt = parseInt(freshData.tanggalJatuhTempo) || 0;
                    if (tagJt > 0 && today.getDate() > tagJt) {
                        badgeContainer.innerHTML = `<div class="status-badge status-isolir"><i class="fas fa-exclamation-triangle"></i> Terlewat (Isolir)</div>`;
                        document.getElementById('billAmountStr').style.color = "#f87171";
                    } else {
                        badgeContainer.innerHTML = `<div class="status-badge status-belum"><i class="fas fa-clock"></i> Belum Bayar</div>`;
                        // Kembalikan ke warna asli di theme CSS
                        if (document.body.classList.contains('light-mode')) {
                            document.getElementById('billAmountStr').style.color = "#0f172a";
                        } else {
                            document.getElementById('billAmountStr').style.color = "var(--text-primary)";
                        }
                    }
                }

            } catch (e) {
                console.warn("Gagal refresh data live", e);
                // Fallback ke session
            }
        }

        function renderPengumumanBoard(list = [], sourceLabel = 'server') {
            const notifBoard = document.getElementById('notifBoard');
            const notifText = document.getElementById('notifText');
            const notifMeta = document.getElementById('notifMeta');
            if (!notifBoard || !notifText) return;
            if (!Array.isArray(list) || !list.length) {
                notifBoard.style.display = 'none';
                return;
            }
            notifBoard.style.display = 'flex';
            notifText.innerHTML = list.slice(0, 5).map((n, idx) => `
                <div class="notif-item">
                    <strong>${idx + 1}.</strong> ${String(n.pesan || '-')}
                </div>
            `).join('');
            if (notifMeta) {
                const nowLabel = new Date().toLocaleString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                notifMeta.innerText = `Update: ${nowLabel} • ${list.length} siaran • sumber ${sourceLabel}`;
            }
        }

        async function cekPengumuman() {
            const myArea = String(myData?.area || '').trim();
            const myId = String(myData?.id_pelanggan || myData?.idPelanggan || '').trim();
            try {
                const res = await apiFetch(`/pengumuman/aktif?idPelanggan=${encodeURIComponent(myId)}&area=${encodeURIComponent(myArea)}`);
                const list = Array.isArray(res?.data) ? res.data : (Array.isArray(res) ? res : []);
                if (list.length) {
                    renderPengumumanBoard(list, 'server');
                    return;
                }
                // Fallback kompatibilitas lama: localStorage mock notifikasi
                const raw = localStorage.getItem('ss_notifikasi_db');
                const legacy = raw ? JSON.parse(raw) : [];
                const fallbackList = legacy
                    .filter(n => n.area === 'ALL' || String(n.area || '').trim().toLowerCase() === myArea.toLowerCase())
                    .sort((a, b) => Number(b.waktu || 0) - Number(a.waktu || 0))
                    .map(n => ({ pesan: n.pesan || '' }));
                renderPengumumanBoard(fallbackList, 'fallback-local');
            } catch (e) {
                console.warn("Gagal memuat pengumuman", e);
                // Fallback local jika endpoint belum update/cache lama
                try {
                    const raw = localStorage.getItem('ss_notifikasi_db');
                    const legacy = raw ? JSON.parse(raw) : [];
                    const fallbackList = legacy
                        .filter(n => n.area === 'ALL' || String(n.area || '').trim().toLowerCase() === myArea.toLowerCase())
                        .sort((a, b) => Number(b.waktu || 0) - Number(a.waktu || 0))
                        .map(n => ({ pesan: n.pesan || '' }));
                    renderPengumumanBoard(fallbackList, 'fallback-local');
                } catch {
                    renderPengumumanBoard([], 'none');
                }
            }
        }

        // ----------------------------------------------------
        // SYSTEM: BOTTOM SHEET HISTORI
        // ----------------------------------------------------
        window.bukaHistori = async () => {
            document.getElementById('historyOverlay').classList.add('show');
            document.getElementById('historySheet').classList.add('show');

            const listDiv = document.getElementById('historyListContent');
            listDiv.innerHTML = `
                <div style="text-align:center; padding: 30px; color: var(--text-secondary); font-size: 13px;">
                    <div class="spinner-mini" style="margin: 0 auto 10px auto; border-color: rgba(255,255,255,0.1); border-top-color: var(--primary);"></div>
                    Menarik Data Pembayaran...
                </div>
            `;

            try {
                // Fetch pembukuan yg kategorinya Tagihan Internet, dan id_pelanggan ini.
                // Endpoint apiFetch mungkin nge-return semua, jd kita filter di FE jika tidak ada filter parameter di BE (karena ini sqlite dummy di local)
                const res = await apiFetch('/collections/pembukuan');
                const allAcount = res.data || [];

                // Filter hanya Lunas Internet oleh ID ini
                const riwayatSaya = allAcount.filter(p =>
                    p.id_pelanggan === myData.id &&
                    p.jenis === 'Pemasukan' &&
                    p.kategori === 'Tagihan Internet'
                );

                // Sortir dari yg terbaru
                riwayatSaya.sort((a, b) => b.id - a.id); // Asumsi makin besar ID makin baru

                if (riwayatSaya.length === 0) {
                    listDiv.innerHTML = `
                        <div style="text-align:center; padding: 40px 10px; color: var(--text-secondary);">
                            <i class="fas fa-file-invoice" style="font-size: 40px; opacity: 0.3; margin-bottom: 15px;"></i>
                            <p>Belum ada catatan pembayaran yang masuk di sistem.</p>
                        </div>
                    `;
                    return;
                }

                // Render List
                listDiv.innerHTML = '';
                const formatRp = (angka) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);

                riwayatSaya.forEach(pbb => {
                    const mS = arrBulan[parseInt(pbb.bulan) - 1] || pbb.bulan;
                    const hHtml = `
                        <div class="history-item">
                            <div>
                                <div class="h-month">Tagihan ${mS} ${pbb.tahun}</div>
                                <div class="h-date"><i class="fas fa-calendar-check"></i> Dibayar tgl: ${pbb.tanggal} ${mS}</div>
                            </div>
                            <div class="h-amount">${formatRp(pbb.nominal)}</div>
                        </div>
                    `;
                    listDiv.insertAdjacentHTML('beforeend', hHtml);
                });


            } catch (e) {
                listDiv.innerHTML = `<p style="color:#ef4444; text-align:center; padding: 20px;">Gagal memuat histori. ${e.message}</p>`;
            }
        };

        window.tutupHistori = () => {
            document.getElementById('historyOverlay').classList.remove('show');
            document.getElementById('historySheet').classList.remove('show');
        };

        window.hubungiAdminCS = () => {
            // Bisa direct wa ke nomor owner ISP
            // Sesuaikan dgn no perusahaan Anda
            window.open('https://wa.me/6281234567890?text=Halo%20Admin%20Sans Speed,%20saya%20pelanggan%20ID%20' + myData.id_pelanggan + '%20ingin%20bertanya...', '_blank');
        };

        // Theme Toggle Setup
        const themeBtn = document.getElementById('themeToggleBtn');
        const icon = themeBtn.querySelector('i');

        let isLight = localStorage.getItem('ss_theme') === 'light';
        if (isLight) setLightMode();

        themeBtn.addEventListener('click', () => {
            isLight = !isLight;
            if (isLight) {
                setLightMode();
                localStorage.setItem('ss_theme', 'light');
            } else {
                setDarkMode();
                localStorage.setItem('ss_theme', 'dark');
            }
        });

        function setLightMode() {
            document.body.classList.add('light-mode');
            icon.className = 'fas fa-sun';
            icon.style.color = '#f59e0b'; // Sun color
            themeBtn.style.background = 'white';
            themeBtn.style.border = '1px solid #cbd5e1';
            icon.parentElement.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
        }

        function setDarkMode() {
            document.body.classList.remove('light-mode');
            icon.className = 'fas fa-moon';
            icon.style.color = 'white';
            themeBtn.style.background = 'rgba(0,0,0,0.5)';
            themeBtn.style.border = '1px solid rgba(255,255,255,0.1)';
        }

    </script>
</body>

</html>