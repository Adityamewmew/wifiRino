<!DOCTYPE html>
<html lang="id">

<head>
    <script src="{{ asset('js/ss-storage-migrate.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Karyawan - Sans Speed</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <script>
        if (localStorage.getItem('ss_theme') === 'light') {
            document.documentElement.classList.add('light-mode');
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--text-primary, #f8fafc);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Filter Section */
        .filter-section {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }

        .search-box {
            position: relative;
            flex: 1;
        }

        .search-box i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .search-input {
            width: 100%;
            padding: 14px 16px 14px 45px;
            background: var(--card-bg, rgba(15, 23, 42, 0.4));
            border: 1px solid var(--border-color, rgba(255, 255, 255, 0.08));
            border-radius: 12px;
            color: var(--text-primary, white);
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            transition: all 0.3s;
        }

        .search-input::placeholder {
            color: #94a3b8;
            font-weight: 300;
        }

        .search-input:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }

        .filter-select {
            padding: 12px 16px;
            background: var(--card-bg, rgba(15, 23, 42, 0.5));
            border: 1px solid var(--border-color, rgba(255, 255, 255, 0.1));
            border-radius: 12px;
            color: var(--text-primary, white);
            font-family: 'Outfit', sans-serif;
            cursor: pointer;
            min-width: 150px;
        }

        .filter-select option {
            background: var(--card-bg, #1e293b);
            color: var(--text-primary, white);
        }

        /* Stats Cards for Employees */
        .emp-stats-grid {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 15px;
            margin-bottom: 24px;
        }

        @media (max-width: 1200px) {
            .emp-stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            }
        }

        .emp-stat-card {
            background: var(--card-bg);
            border-radius: 12px;
            padding: 15px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .emp-icon-box {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .emp-info h4 {
            margin: 0 0 4px 0;
            color: var(--text-secondary, #94a3b8);
            font-size: 11px;
            font-weight: 600;
            line-height: 1.2;
        }

        .emp-info .value {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary, white);
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(8px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            background: var(--card-bg);
            width: 100%;
            max-width: 600px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            transform: scale(0.95) translateY(20px);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            max-height: 90vh;
            display: flex;
            flex-direction: column;
        }

        .modal-overlay.active .modal-content {
            transform: scale(1) translateY(0);
        }

        .modal-header {
            padding: 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: var(--text-primary, white);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .close-btn {
            color: #94a3b8;
            font-size: 20px;
            transition: color 0.2s;
        }

        .close-btn:hover {
            color: white;
        }

        .modal-body {
            padding: 24px;
            overflow-y: auto;
        }

        .modal-footer {
            padding: 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #cbd5e1;
            letter-spacing: 0.5px;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-primary, white);
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 600;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.2s;
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .role-admin {
            background: rgba(139, 92, 246, 0.15);
            color: #c084fc;
            border: 1px solid rgba(139, 92, 246, 0.3);
        }

        .role-teknisi {
            background: rgba(59, 130, 246, 0.15);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .role-penagih {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

        /* Tabs */
        .tabs-container {
            display: flex;
            gap: 15px;
            margin-bottom: 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 15px;
            overflow-x: auto;
        }

        .tab-btn {
            background: transparent;
            color: #94a3b8;
            border: none;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .tab-btn:hover {
            color: var(--text-primary, white);
            background: rgba(255, 255, 255, 0.05);
        }

        .tab-btn.active {
            color: var(--text-primary, white);
            background: rgba(59, 130, 246, 0.2);
            border-bottom: 2px solid #3b82f6;
        }

        /* Light mode specific overrides for inline styles */
        body.light-mode .tab-btn:hover {
            color: var(--primary);
            background: rgba(59, 130, 246, 0.05);
        }

        body.light-mode .tab-btn.active {
            color: var(--primary);
            background: rgba(59, 130, 246, 0.1);
        }

        body.light-mode .close-btn:hover {
            color: var(--primary);
        }

        .integrasi-subtabs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 18px;
        }

        .integrasi-subtab-btn {
            padding: 10px 16px;
            border-radius: 10px;
            border: 1px solid rgba(148, 163, 184, 0.35);
            background: rgba(15, 23, 42, 0.35);
            color: var(--text-primary, #e2e8f0);
            font-weight: 600;
            cursor: pointer;
            font-size: 13px;
        }

        body.light-mode .integrasi-subtab-btn {
            background: #f1f5f9;
            border-color: #cbd5e1;
            color: #0f172a;
        }

        .integrasi-subtab-btn.active {
            border-color: var(--primary, #38bdf8);
            background: rgba(56, 189, 248, 0.15);
            color: var(--primary, #38bdf8);
        }

        .badge-ok {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            background: rgba(16, 185, 129, 0.2);
            color: #34d399;
        }

        .badge-bad {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            background: rgba(148, 163, 184, 0.2);
            color: #94a3b8;
        }

        .badge-warn {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            background: rgba(245, 158, 11, 0.2);
            color: #fbbf24;
        }

        .tab-pane {
            display: none;
            animation: fadeIn 0.3s ease;
        }

        .tab-pane.active {
            display: block;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .settings-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 30px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            max-width: 800px;
            margin-bottom: 24px;
        }

        .table-scroll-mobile {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 6px;
        }

        .table-scroll-mobile table {
            min-width: 1060px;
        }

        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .filter-section {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-select {
                width: 100%;
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

            <!-- PAGE CONTENT -->
            <div class="content-wrapper">

                <div class="page-header">
                    <h1 class="page-title">
                        <i class="fas fa-cog"
                            style="color: var(--primary); text-shadow: 0 0 15px var(--primary-glow);"></i>
                        Pengaturan Sistem Terpusat
                    </h1>
                </div>

                <div class="tabs-container">
                    <button class="tab-btn active" data-tab="karyawan" onclick="switchTab('karyawan', this)"><i class="fas fa-user-tie"></i>
                        Data Karyawan</button>
                    <button class="tab-btn" data-tab="pembayaran" onclick="switchTab('pembayaran', this)"><i
                            class="fas fa-money-check-alt"></i>
                        Rekening & Pembayaran</button>
                    <button class="tab-btn" data-tab="printer" onclick="switchTab('printer', this)"><i class="fas fa-print"></i> Printer
                        Thermal</button>
                    <button class="tab-btn" data-tab="whatsapp" onclick="switchTab('whatsapp', this)"><i class="fab fa-whatsapp"></i>
                        Template WA</button>
                    <button class="tab-btn" data-tab="integrasi" onclick="switchTab('integrasi', this)"><i class="fas fa-plug"></i>
                        Integrasi</button>
                    <button class="tab-btn" data-tab="tampilan" onclick="switchTab('tampilan', this)"><i class="fas fa-palette"></i>
                        Tampilan</button>
                    <button class="tab-btn" data-tab="keamanan" onclick="switchTab('keamanan', this)"><i class="fas fa-shield-alt"></i>
                        Keamanan Data</button>
                </div>
                <p style="margin: 6px 0 14px 0; font-size: 12px; color: var(--text-muted, #94a3b8);"><i class="fas fa-arrows-alt-h"></i> Geser tab ke kiri/kanan jika item banyak.</p>

                <!-- TAB KARYAWAN -->
                <div id="tab-karyawan" class="tab-pane active">
                    <div style="display: flex; justify-content: flex-end; margin-bottom: 15px;">
                        <button class="btn-primary" onclick="window.bukaModalKaryawan()">
                            <i class="fas fa-user-plus"></i> Tambah Anggota Staf
                        </button>
                    </div>

                    <!-- Stats Overview -->
                    <div class="emp-stats-grid">
                        <div class="emp-stat-card">
                            <div class="emp-icon-box" style="background: rgba(139, 92, 246, 0.1); color: #c084fc;">
                                <i class="fas fa-users-cog"></i>
                            </div>
                            <div class="emp-info">
                                <h4>Total Karyawan</h4>
                                <p class="value" id="stat-total">0 <span
                                        style="font-size: 12px; font-weight: 400; color: #64748b;">Orang</span></p>
                            </div>
                        </div>
                        <div class="emp-stat-card">
                            <div class="emp-icon-box" style="background: rgba(245, 158, 11, 0.1); color: #fbbf24;">
                                <i class="fas fa-motorcycle"></i>
                            </div>
                            <div class="emp-info">
                                <h4>Hanya Agen Saja</h4>
                                <p class="value" id="stat-penagih">0 <span
                                        style="font-size: 12px; font-weight: 400; color: #64748b;">Orang</span></p>
                            </div>
                        </div>
                        <div class="emp-stat-card">
                            <div class="emp-icon-box" style="background: rgba(59, 130, 246, 0.1); color: #60a5fa;">
                                <i class="fas fa-tools"></i>
                            </div>
                            <div class="emp-info">
                                <h4>Teknisi (Tanpa Tagih)</h4>
                                <p class="value" id="stat-teknisi">0 <span
                                        style="font-size: 12px; font-weight: 400; color: #64748b;">Orang</span></p>
                            </div>
                        </div>
                        <div class="emp-stat-card">
                            <div class="emp-icon-box" style="background: rgba(16, 185, 129, 0.1); color: #34d399;">
                                <i class="fas fa-motorcycle"></i>
                            </div>
                            <div class="emp-info">
                                <h4>Teknisi & Agen</h4>
                                <p class="value" id="stat-tekpen">0 <span
                                        style="font-size: 12px; font-weight: 400; color: #64748b;">Orang</span></p>
                            </div>
                        </div>
                        <div class="emp-stat-card">
                            <div class="emp-icon-box" style="background: rgba(244, 63, 94, 0.1); color: #fb7185;">
                                <i class="fas fa-desktop"></i>
                            </div>
                            <div class="emp-info">
                                <h4>Staff Admin Ops</h4>
                                <p class="value" id="stat-admin">0 <span
                                        style="font-size: 12px; font-weight: 400; color: #64748b;">Orang</span></p>
                            </div>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="filter-section">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" class="search-input" placeholder="Cari Nama Karyawan...">
                        </div>
                        <select class="filter-select">
                            <option value="">Semua Jabatan</option>
                            <option value="owner">Owner</option>
                            <option value="admin_keuangan">Admin Keuangan</option>
                            <option value="admin_kasir">Admin Kasir</option>
                            <option value="teknisi">Hanya Teknisi Jaringan</option>
                            <option value="penagih">Hanya Agen Bulanan</option>
                            <option value="tekpen">Teknisi + Agen Lapangan</option>
                        </select>
                    </div>

                    <!-- Table Section -->
                    <div class="card-3d" style="padding: 20px;">
                        <div class="table-scroll-mobile desktop-karyawan-table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Profil Karyawan</th>
                                        <th>Hak Akses (Role)</th>
                                        <th>Kontak / Akun</th>
                                        <th>Wilayah / Tugas</th>
                                        <th>Gaji / Saldo Setoran</th>
                                        <th style="text-align: right;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="tableKaryawanBody">
                                    <tr>
                                        <td colspan="6" style="text-align: center;"><i
                                                class="fas fa-spinner fa-spin"></i>
                                            Memuat data karyawan...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div id="mobileKaryawanList" class="mobile-data-list">
                            <div class="mobile-data-empty"><i class="fas fa-spinner fa-spin"></i> Memuat data karyawan...</div>
                        </div>
                    </div>

                    <!-- Tutup Tab Karyawan -->
                </div>

                <!-- TAB REKENING & PEMBAYARAN -->
                <div id="tab-pembayaran" class="tab-pane">
                    <div class="settings-card">
                        <h2 style="color: var(--text-primary); margin:0 0 15px 0;"><i class="fas fa-university"></i>
                            Informasi Rekening Bank & E-Wallet
                        </h2>
                        <p style="color: var(--text-secondary); font-size:14px; margin-bottom: 20px;">
                            Informasi di bawah ini akan ditampilkan kepada pelanggan di "Portal Pelanggan -> Bayar
                            Tagihan". Biarkan kosong jika tidak ingin ditampilkan.
                        </p>

                        <!-- Bank BCA -->
                        <div
                            style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 12px; margin-bottom: 15px;">
                            <h4 style="color:#60a5fa; margin: 0 0 10px 0;"><i class="fas fa-money-check-alt"></i> Bank
                                Central Asia (BCA)</h4>
                            <div class="form-grid">
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label">Nomor Rekening</label>
                                    <input type="text" id="set_bca_no" class="search-input" placeholder="8732 1199 00">
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label">Atas Nama (a.n)</label>
                                    <input type="text" id="set_bca_nama" class="search-input"
                                        placeholder="PT Sans Speed">
                                </div>
                            </div>
                        </div>

                        <!-- Bank Mandiri -->
                        <div
                            style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 12px; margin-bottom: 15px;">
                            <h4 style="color:#f59e0b; margin: 0 0 10px 0;"><i class="fas fa-money-check-alt"></i> Bank
                                Mandiri</h4>
                            <div class="form-grid">
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label">Nomor Rekening</label>
                                    <input type="text" id="set_mdr_no" class="search-input"
                                        placeholder="1370 0011 2233">
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label">Atas Nama (a.n)</label>
                                    <input type="text" id="set_mdr_nama" class="search-input"
                                        placeholder="PT Sans Speed">
                                </div>
                            </div>
                        </div>

                        <!-- E-Wallet -->
                        <div
                            style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 12px; margin-bottom: 15px;">
                            <h4 style="color:#34d399; margin: 0 0 10px 0;"><i class="fas fa-wallet"></i> E-Wallet
                                (DANA/OVO/GoPay)</h4>
                            <div class="form-grid">
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label">Nomor Tujuan / Telepon</label>
                                    <input type="text" id="set_dana_no" class="search-input"
                                        placeholder="0812 3456 7890">
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="form-label">Atas Nama (a.n)</label>
                                    <input type="text" id="set_dana_nama" class="search-input"
                                        placeholder="Admin Sans Speed">
                                </div>
                            </div>
                        </div>

                        <!-- Nomor WA CS -->
                        <div
                            style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 12px; margin-bottom: 25px;">
                            <h4 style="color:#fb7185; margin: 0 0 10px 0;"><i class="fab fa-whatsapp"></i> WhatsApp
                                Konfirmasi Pembayaran</h4>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label">No WhatsApp CS (Gunakan format 62...)</label>
                                <input type="text" id="set_wa_cs" class="search-input" placeholder="628123456789">
                            </div>
                        </div>

                        <div
                            style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 12px; margin-bottom: 25px;">
                            <h4 style="color:#38bdf8; margin: 0 0 10px 0;"><i class="fas fa-building"></i> Identitas ISP
                                (Untuk Nota)</h4>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label">ISP Support By</label>
                                <input type="text" id="set_isp_support_by" class="search-input"
                                    placeholder="Contoh: PT Sans Speed (contoh)">
                            </div>
                        </div>

                        <div
                            style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 12px; margin-bottom: 25px;">
                            <h4 style="color:#22d3ee; margin: 0 0 10px 0;"><i class="fas fa-circle-info"></i> Info Jadwal
                                Pembayaran (Portal Pelanggan)</h4>
                            <div class="form-group" style="margin-bottom:10px;">
                                <label class="form-label">Baris 1</label>
                                <input type="text" id="set_payment_info_line1" class="search-input"
                                    placeholder="Pembayaran dibuka tanggal 25 - 05 setiap bulan.">
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <label class="form-label">Baris 2</label>
                                <input type="text" id="set_payment_info_line2" class="search-input"
                                    placeholder="Jika melewati batas, pelanggan akan isolir tanggal 8.">
                            </div>
                        </div>

                        <div
                            style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 12px; margin-bottom: 25px;">
                            <h4 style="color:#a78bfa; margin: 0 0 10px 0;"><i class="fas fa-layer-group"></i> Metode
                                Pembayaran Dinamis</h4>
                            <p style="color:#94a3b8; font-size:12px; margin:0 0 12px 0;">
                                Tambah metode pembayaran baru dan atur apakah ditampilkan di portal pelanggan.
                            </p>
                            <div id="paymentAccountsContainer" style="display:flex; flex-direction:column; gap:10px;"></div>
                            <div style="margin-top:10px;">
                                <button class="btn-secondary" onclick="window.addPaymentAccountRow()"><i
                                        class="fas fa-plus"></i> Tambah Metode</button>
                            </div>
                        </div>

                        <div style="display:flex; gap:10px;">
                            <button class="btn-primary" onclick="window.saveSettingsPembayaran()"><i
                                    class="fas fa-save"></i> Simpan Info Pembayaran</button>
                        </div>
                    </div>
                </div>

                <!-- TAB PRINTER -->
                <div id="tab-printer" class="tab-pane">
                    <div class="settings-card">
                        <h2 style="color: var(--text-primary); margin:0 0 15px 0;"><i class="fas fa-print"></i>
                            Konfigurasi Cetak Struk
                        </h2>
                        <p style="color: var(--text-secondary); font-size:14px; margin-bottom: 20px;">Sistem mendukung
                            pencetakan
                            langsung melalui fitur Browser Print ke Thermal Printer 58mm / 80mm.</p>

                        <div
                            style="background: rgba(0,0,0,0.2); padding: 15px; border-radius: 12px; margin-bottom: 20px;">
                            <h4 style="color:#60a5fa; margin: 0 0 10px 0;"><i class="fas fa-image"></i> Branding Logo
                                Sidebar</h4>
                            <p style="color:#94a3b8; font-size:12px; margin:0 0 12px 0;">
                                Rekomendasi file: PNG/WebP 96x96 sampai 128x128, maksimal 15KB. Jika file terlalu besar,
                                sistem akan otomatis kompres ke standar ringan.
                            </p>
                            <div
                                style="display:flex; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:10px;">
                                <div
                                    style="width:46px; height:46px; border-radius:10px; border:1px solid rgba(255,255,255,0.12); background:rgba(15,23,42,0.45); display:flex; align-items:center; justify-content:center; overflow:hidden;">
                                    <img id="previewSidebarLogo" alt="Preview Logo"
                                        style="max-width:100%; max-height:100%; object-fit:contain; display:none;">
                                    <i id="previewSidebarLogoFallback" class="fas fa-wifi" style="color:#60a5fa;"></i>
                                </div>
                                <div style="flex:1; min-width:220px;">
                                    <input type="file" id="inSidebarLogoFile" class="search-input"
                                        accept="image/png,image/jpeg,image/webp,image/svg+xml"
                                        onchange="window.handleSidebarLogoFile(event)">
                                </div>
                            </div>
                            <div class="form-group" style="margin-bottom: 10px;">
                                <label class="form-label">Atau pakai URL gambar/logo</label>
                                <input type="text" id="inSidebarLogoUrl" class="search-input"
                                    placeholder="https://domain.com/logo.webp"
                                    oninput="window.previewSidebarLogoFromUrl()">
                            </div>
                            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                                <button class="btn-primary" onclick="window.saveSettingsBranding()"><i
                                        class="fas fa-save"></i> Simpan Logo</button>
                                <button class="btn-secondary" onclick="window.resetSettingsBranding()"><i
                                        class="fas fa-undo"></i> Reset ke Icon</button>
                            </div>
                            <input type="hidden" id="inSidebarLogoData">
                        </div>

                        <div class="form-group">
                            <label class="form-label">Nama Outlet / Header Baris 1</label>
                            <input type="text" id="inPrintHeader" class="search-input"
                                placeholder="Misal: Sans Speed PUSAT / BUMDES">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Pesan Sub-Header Baris 2</label>
                            <input type="text" id="inPrintSub" class="search-input" placeholder="Alamat Singkat / Moto">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Pesan Footer Tertulis di Akhir Struk</label>
                            <input type="text" id="inPrintFooter" class="search-input"
                                placeholder="Misal: Terima kasih. Internet Lancar, Rezeki Lancar!">
                        </div>
                        <div style="margin-top: 20px; display:flex; gap:10px;">
                            <button class="btn-primary" onclick="window.saveSettingsPrinter()"><i
                                    class="fas fa-save"></i> Simpan Konfigurasi</button>
                        </div>
                    </div>
                </div>

                <!-- TAB WA -->
                <div id="tab-whatsapp" class="tab-pane">
                    <div class="settings-card">
                        <h2 style="color: var(--text-primary); margin:0 0 15px 0;"><i class="fab fa-whatsapp"></i>
                            Template Pesan
                            Otomatis (Auto-WA)</h2>
                        <p style="color:#94a3b8; font-size:14px; margin-bottom: 20px;">Gunakan tag/variabel yang
                            tersedia untuk menyesuaikan pesan otomatis: <code
                                style="background:#334155; padding:2px 6px; border-radius:4px;">{nama}</code> <code
                                style="background:#334155; padding:2px 6px; border-radius:4px;">{total_tagihan}</code>
                            <code style="background:#334155; padding:2px 6px; border-radius:4px;">{jatuh_tempo}</code>
                            <code style="background:#334155; padding:2px 6px; border-radius:4px;">{tgl_isolir}</code>
                            <code style="background:#334155; padding:2px 6px; border-radius:4px;">{id_pelanggan}</code>
                            <code style="background:#334155; padding:2px 6px; border-radius:4px;">{periode}</code>
                        </p>

                        <div class="form-group">
                            <label class="form-label">Template Pesan Pengingat Tagihan Masuk (Belum Lunas)</label>
                            <textarea id="inWaTagihan" class="search-input"
                                style="height: 120px; resize:vertical; line-height:1.5;"></textarea>
                        </div>
                        <div class="form-group" style="margin-top:20px;">
                            <label class="form-label">Template Pesan Kwitansi (Selesai Dibayar / Lunas)</label>
                            <textarea id="inWaLunas" class="search-input"
                                style="height: 120px; resize:vertical; line-height:1.5;"></textarea>
                        </div>
                        <div style="margin-top: 20px;">
                            <button class="btn-primary" onclick="window.saveSettingsWA()"><i class="fas fa-save"></i>
                                Simpan Template</button>
                        </div>
                    </div>
                </div>

                <!-- TAB INTEGRASI -->
                <div id="tab-integrasi" class="tab-pane">
                    <div class="integrasi-subtabs" id="integrasiSubtabs">
                        <button type="button" class="integrasi-subtab-btn active" data-integrasi-sub="pg"
                            onclick="window.switchIntegrasiSubtab('pg', this)"><i class="fas fa-credit-card"></i> Payment
                            Gateway</button>
                        <button type="button" class="integrasi-subtab-btn" data-integrasi-sub="mikrotik"
                            onclick="window.switchIntegrasiSubtab('mikrotik', this)"><i class="fas fa-network-wired"></i>
                            Mikrotik</button>
                    </div>

                    <div id="integrasi-pane-pg" class="settings-card" style="margin-bottom: 20px;">
                        <div
                            style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
                            <div>
                                <h2 style="color: var(--text-primary); margin:0 0 6px 0;"><i class="fas fa-credit-card"
                                        style="color:var(--primary);"></i> Payment Gateway (wadah)</h2>
                                <p style="color:#94a3b8; font-size:13px; margin:0;">Simpan kredensial & URL callback di
                                    server. Integrasi pembayaran otomatis menyusul.</p>
                            </div>
                            <span class="badge-warn" id="pgApiStatusBadge">API: belum diuji</span>
                        </div>

                        <div
                            style="display:flex; flex-wrap:wrap; gap:16px; align-items:center; margin-bottom:16px; padding:12px; background:rgba(0,0,0,0.15); border-radius:12px; border:1px solid rgba(255,255,255,0.06);">
                            <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-weight:600;">
                                <input type="checkbox" id="pgAktif" style="width:18px; height:18px;"> Aktifkan payment
                                gateway
                            </label>
                            <div class="form-group" style="margin:0; min-width:160px;">
                                <label class="form-label" style="margin-bottom:4px;">Vendor</label>
                                <select id="pgVendor" class="filter-select" style="width:100%;">
                                    <option value="tripay">Tripay</option>
                                </select>
                            </div>
                            <div class="form-group" style="margin:0; min-width:160px;">
                                <label class="form-label" style="margin-bottom:4px;">Mode</label>
                                <select id="pgMode" class="filter-select" style="width:100%;">
                                    <option value="sandbox">Sandbox</option>
                                    <option value="production">Production</option>
                                </select>
                            </div>
                        </div>

                        <div
                            style="padding:12px 14px; border-radius:10px; background:rgba(245,158,11,0.12); border:1px solid rgba(245,158,11,0.35); color:#fbbf24; font-size:13px; margin-bottom:16px;">
                            <i class="fas fa-exclamation-triangle"></i> Rahasiakan API Key &amp; Private Key. Jangan
                            ekspos di client publik.
                        </div>

                        <div style="display:grid; grid-template-columns:repeat(auto-fill,minmax(240px,1fr)); gap:14px;">
                            <div class="form-group" style="margin:0;">
                                <label class="form-label">Kode merchant</label>
                                <input type="text" id="pgMerchantCode" class="search-input" placeholder="Txxxxx" autocomplete="off">
                            </div>
                            <div class="form-group" style="margin:0;">
                                <label class="form-label">Biaya admin (Rp)</label>
                                <input type="number" id="pgBiayaAdmin" class="search-input" value="0" min="0" step="1">
                            </div>
                            <div class="form-group" style="margin:0;">
                                <label class="form-label">Expired (jam)</label>
                                <input type="number" id="pgExpiredJam" class="search-input" value="24" min="1" step="1">
                            </div>
                        </div>
                        <div class="form-group" style="margin-top:14px;">
                            <label class="form-label">API Key <span id="pgApiKeyHint"
                                    style="font-size:11px;color:#64748b;font-weight:400;"></span></label>
                            <input type="password" id="pgApiKey" class="search-input" placeholder="Kosongkan jika tidak diubah" autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Private Key <span id="pgPrivateKeyHint"
                                    style="font-size:11px;color:#64748b;font-weight:400;"></span></label>
                            <input type="password" id="pgPrivateKey" class="search-input" placeholder="Kosongkan jika tidak diubah" autocomplete="off">
                        </div>

                        <h3 style="color:var(--text-primary); font-size:15px; margin:20px 0 10px 0;"><i
                                class="fas fa-store"></i> Pengaturan merchant (callback)</h3>
                        <div style="display:grid; grid-template-columns:1fr; gap:12px;">
                            <div class="form-group" style="margin:0;">
                                <label class="form-label">URL website</label>
                                <input type="url" id="pgUrlWebsite" class="search-input"
                                    placeholder="https://domain-anda.id/">
                            </div>
                            <div class="form-group" style="margin:0;">
                                <label class="form-label">Whitelist IP server</label>
                                <input type="text" id="pgWhitelistIp" class="search-input" placeholder="103.x.x.x">
                            </div>
                            <div class="form-group" style="margin:0;">
                                <label class="form-label">URL callback</label>
                                <input type="url" id="pgUrlCallback" class="search-input"
                                    placeholder="https://domain-anda.id/tripay">
                            </div>
                        </div>
                        <div style="margin-top:20px;">
                            <button type="button" class="btn-primary" onclick="window.saveSettingsPaymentGateway()"><i
                                    class="fas fa-save"></i> Simpan payment gateway</button>
                        </div>
                    </div>

                    <div id="integrasi-pane-mikrotik" class="card-3d" style="padding: 20px; display: none;">
                        <div
                            style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; margin-bottom:16px;">
                            <h2 style="color: var(--text-primary); margin:0; font-size:18px;"><i class="fas fa-server"></i>
                                Data router Mikrotik</h2>
                            <button type="button" class="btn-primary" onclick="window.bukaModalMikrotikRouter(null)"><i
                                    class="fas fa-plus"></i> Tambah router</button>
                        </div>
                        <p style="color:#94a3b8; font-size:13px; margin:0 0 14px 0;">Router yang tersimpan di sini bisa
                            dipilih di form pelanggan (tab Koneksi Mikrotik). Uji koneksi memeriksa port TCP (bukan login
                            API RouterOS).</p>
                        <div class="table-scroll-mobile">
                            <table>
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Aksi</th>
                                        <th>Nama router</th>
                                        <th>ROS</th>
                                        <th>User Mgr</th>
                                        <th>Hotspot</th>
                                        <th>Pelanggan</th>
                                        <th>Host / IP</th>
                                        <th>Koneksi</th>
                                        <th>User</th>
                                        <th>Layanan — Port</th>
                                        <th>Test</th>
                                    </tr>
                                </thead>
                                <tbody id="tableMikrotikRoutersBody">
                                    <tr>
                                        <td colspan="12" style="text-align:center;"><i class="fas fa-spinner fa-spin"></i>
                                            Memuat...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- TAB TAMPILAN -->
                <div id="tab-tampilan" class="tab-pane">
                    <div class="settings-card">
                        <h2 style="color: var(--text-primary); margin:0 0 10px 0;"><i class="fas fa-palette"></i> Tema
                            aplikasi</h2>
                        <p style="color:#94a3b8; font-size:14px; margin:0 0 18px 0;">Pilihan disimpan di perangkat ini
                            (sama seperti ikon matahari/bulan di header).</p>
                        <div style="display:flex; flex-wrap:wrap; gap:16px; align-items:center;">
                            <label
                                style="display:flex; align-items:center; gap:10px; cursor:pointer; font-weight:600; padding:12px 16px; border-radius:12px; border:1px solid rgba(148,163,184,0.35);">
                                <input type="radio" name="prefTheme" value="dark" id="themePrefDark"> Gelap
                            </label>
                            <label
                                style="display:flex; align-items:center; gap:10px; cursor:pointer; font-weight:600; padding:12px 16px; border-radius:12px; border:1px solid rgba(148,163,184,0.35);">
                                <input type="radio" name="prefTheme" value="light" id="themePrefLight"> Terang
                            </label>
                        </div>
                        <div style="margin-top:18px;">
                            <button type="button" class="btn-primary" onclick="window.applyThemeFromSettings()"><i
                                    class="fas fa-check"></i> Terapkan tema</button>
                        </div>
                    </div>
                </div>

                <!-- TAB KEAMANAN -->
                <div id="tab-keamanan" class="tab-pane">
                    <div class="settings-card" style="margin-bottom: 20px;">
                        <h2 style="color: var(--text-primary); margin:0 0 15px 0;"><i class="fas fa-database"></i>
                            Backup Database</h2>
                        <p style="color:#94a3b8; font-size:14px; margin-bottom: 20px;">Unduh salinan database SQLite
                            untuk mencadangkan data pelanggan, tagihan, dan pengaturan secara berkala. Hanya Super Admin
                            & Admin yang memiliki akses.</p>
                        <button class="btn-primary" onclick="window.downloadBackup()"><i class="fas fa-download"></i>
                            Download Backup (.db)</button>
                    </div>

                    <div class="card-3d" style="padding: 20px;">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <h2 style="color: var(--text-primary); margin:0; font-size: 16px;"><i
                                    class="fas fa-history"></i> Log
                                Aktivitas (100 Terakhir)</h2>
                            <button class="action-btn" onclick="window.loadAuditLogs()"><i class="fas fa-sync-alt"></i>
                                Refresh</button>
                        </div>
                        <div style="overflow-x: auto;">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Waktu</th>
                                        <th>User</th>
                                        <th>Tindakan</th>
                                        <th>Detail</th>
                                    </tr>
                                </thead>
                                <tbody id="tableAuditLogs">
                                    <tr>
                                        <td colspan="4" style="text-align: center;"><i
                                                class="fas fa-spinner fa-spin"></i> Memuat histori...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Modal Tambah Karyawan -->
    <div class="modal-overlay" id="addModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title"><i class="fas fa-user-plus" style="color: var(--primary);"></i> Form Data
                    Karyawan</h2>
                <button class="close-btn" onclick="closeModal()"><i class="fas fa-times"></i></button>
            </div>

            <div class="modal-body">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label class="form-label">Nama Lengkap Karyawan</label>
                        <input type="text" id="inNamaEmp" class="search-input" placeholder="Masukkan nama...">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email Akun (Login Aplikasi)</label>
                        <input type="email" id="inEmailEmp" class="search-input" placeholder="nama@sansspeed.id">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Password Login</label>
                        <input type="text" id="inPasswordEmp" class="search-input"
                            placeholder="Isi untuk membuat/mengubah password...">
                        <small style="color:var(--text-muted);font-size:11px;opacity:0.6;">*Kosongkan saat Edit jika
                            tidak ingin ganti password.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">No WhatsApp</label>
                        <input type="text" id="inWAEmp" class="search-input" placeholder="08...">
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label">Jabatan (Hak Akses Aplikasi)</label>
                        <select id="inJabatan" class="filter-select" style="width: 100%; border-color: var(--primary);">
                            <option value="owner">Owner (Akses Penuh)</option>
                            <option value="admin_keuangan">Staff Admin Keuangan (Lihat Total Keuangan)</option>
                            <option value="admin_kasir">Staff Admin Kasir (Transaksi Pelanggan Saja)</option>
                            <option value="penagih">Hanya Agen Bulanan (App Teknisi Pembukuan Saja)</option>
                            <option value="tekpen">Teknisi merangkap Agen Lapangan (App Teknisi Pembukuan)</option>
                            <option value="teknisi">Hanya Teknisi (Gangguan Jaringan, App Teknisi Polos)</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label">Tugaskan Area (Hanya untuk opsi Teknisi Agen)</label>
                        <div id="areaCheckboxContainer"
                            style="background: rgba(0,0,0,0.2); padding: 12px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); display: flex; gap: 10px; flex-wrap: wrap; min-height: 44px; align-items: center;">
                            <span style="color: #64748b; font-size: 13px;"><i class="fas fa-spinner fa-spin"></i> Memuat
                                area...</span>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label class="form-label">Gaji Pokok Dasar</label>
                        <input type="number" id="inGaji" class="search-input" placeholder="Contoh: 3000000">
                    </div>
                    <input type="hidden" id="editEmpId">
                </div>
            </div>

            <div class="modal-footer">
                <button class="btn-secondary" onclick="closeModal()">Batal</button>
                <button class="btn-primary" onclick="simpanData()"><i class="fas fa-save"></i> Buat Akun &
                    Profil</button>
            </div>
        </div>
    </div>

    <!-- Modal Router Mikrotik (Integrasi) -->
    <div class="modal-overlay" id="modalMikrotikRouter">
        <div class="modal-content" style="max-width: 520px;">
            <div class="modal-header">
                <h2 class="modal-title" id="modalMikrotikRouterTitle"><i class="fas fa-server"
                        style="color: var(--primary);"></i> Router</h2>
                <button type="button" class="close-btn" onclick="window.tutupModalMikrotikRouter()"><i
                        class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="mrEditId" value="">
                <div class="form-group">
                    <label class="form-label">Nama router / alias</label>
                    <input type="text" id="mrNama" class="search-input" placeholder="mis. POP Pusat">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label">ROS versi</label>
                        <select id="mrRosVersi" class="filter-select" style="width:100%;">
                            <option value="V6">V6</option>
                            <option value="V7">V7</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Hotspot manager</label>
                        <select id="mrHotspot" class="filter-select" style="width:100%;">
                            <option value="tidak_aktif">Tidak aktif</option>
                            <option value="aktif">Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">User manager (Radius / opsional)</label>
                    <select id="mrUserManager" class="filter-select" style="width:100%;">
                        <option value="">— Tidak dipakai —</option>
                        <option value="radius">Radius</option>
                        <option value="user_manager">User Manager</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">IP address / VPN</label>
                    <input type="text" id="mrHost" class="search-input" placeholder="45.x.x.x">
                </div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label">User API</label>
                        <input type="text" id="mrApiUser" class="search-input" autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Layanan</label>
                        <select id="mrServiceType" class="filter-select" style="width:100%;">
                            <option value="API">API</option>
                            <option value="API-SSL">API-SSL</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Port</label>
                    <input type="number" id="mrApiPort" class="search-input" value="8728" min="1" max="65535">
                </div>
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer; margin-bottom:10px;">
                    <input type="checkbox" id="mrGantiPassword"> Ganti password API
                </label>
                <div class="form-group" id="mrPasswordWrap" style="display:none;">
                    <label class="form-label">Password API</label>
                    <input type="password" id="mrApiPassword" class="search-input" autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label class="form-label">Keterangan</label>
                    <input type="text" id="mrKeterangan" class="search-input">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="window.tutupModalMikrotikRouter()">Batal</button>
                <button type="button" class="btn-primary" onclick="window.simpanModalMikrotikRouter()"><i
                        class="fas fa-save"></i> Simpan</button>
            </div>
        </div>
    </div>

    <script type="module">
        import { showConfirm, showAlert, showToast } from './js/utils/dialog.js';
        window.showConfirm = showConfirm;
        window.showAlert = showAlert;
        window.showToast = showToast;
    </script>
    <script type="module">
        import { auth, apiFetch, API_BASE_URL, isAdminAppRole, hasPermission, resolveRoleKey } from '{{ asset('api-config.js') }}';
        const showConfirm = (...args) => window.showConfirm?.(...args);
        const showAlert = (...args) => window.showAlert?.(...args);
        const showToast = (...args) => window.showToast?.(...args);
        const escHtml = (v) => String(v ?? '').replace(/[&<>"']/g, (m) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[m]));

        const formatRupiah = (angka) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(angka || 0);
        window._blockedSettingsTabs = new Set();

        window.applySettingsTabAccess = function (profile) {
            const roleKey = resolveRoleKey(profile?.roleKey || profile?.role);
            const blocked = roleKey === 'admin_keuangan'
                ? new Set(['karyawan', 'pembayaran'])
                : (roleKey === 'admin_kasir'
                    ? new Set(['karyawan', 'pembayaran', 'printer', 'keamanan', 'integrasi'])
                    : new Set());
            window._blockedSettingsTabs = blocked;

            document.querySelectorAll('.tab-btn[data-tab]').forEach((btn) => {
                const tabId = btn.dataset.tab;
                btn.style.display = blocked.has(tabId) ? 'none' : '';
            });

            blocked.forEach((tabId) => {
                const pane = document.getElementById('tab-' + tabId);
                if (pane) pane.classList.remove('active');
            });

            const activePane = document.querySelector('.tab-pane.active');
            const activeTabId = activePane?.id?.replace('tab-', '') || '';
            if (roleKey === 'admin_kasir') {
                const waBtn = document.querySelector('.tab-btn[data-tab="whatsapp"]');
                if (waBtn) {
                    window.switchTab('whatsapp', waBtn);
                    return;
                }
            }
            if (!activeTabId || blocked.has(activeTabId)) {
                const fallbackBtn = [...document.querySelectorAll('.tab-btn[data-tab]')]
                    .find((btn) => btn.style.display !== 'none');
                if (fallbackBtn) {
                    window.switchTab(fallbackBtn.dataset.tab, fallbackBtn);
                }
            }
        };

        auth.onAuthStateChanged(async (user) => {
            if (user) {
                const profile = JSON.parse(localStorage.getItem('ss_user'));
                if (profile) {
                    const roleKey = resolveRoleKey(profile.roleKey || profile.role);
                    if (!isAdminAppRole(profile)) {
                        alert("Akses Ditolak!"); window.location.replace("{{ url('/app-teknisi') }}"); return;
                    }
                    const canOpenSettings = roleKey === 'admin_kasir' ||
                        hasPermission(profile, 'manage_settings') ||
                        hasPermission(profile, 'manage_settings_wa');
                    if (!canOpenSettings) {
                        alert("Role ini tidak punya akses Pengaturan.");
                        window.location.replace("{{ url('/dashboard-admin') }}");
                        return;
                    }
                    window.applySettingsTabAccess(profile);
                    window.applySettingsHashTab?.();
                    // Layout header already managed by layout.js
                    if (roleKey !== 'admin_keuangan' && roleKey !== 'admin_kasir') {
                        window.loadKaryawan();
                    }
                    if (roleKey !== 'admin_kasir') {
                        window.loadAuditLogs();
                    }
                }
            } else {
                window.location.replace("{{ url('/login') }}");
            }
        });

        // ==========================================
        // FITUR REKENING & PEMBAYARAN
        // ==========================================
        window._draggingPaymentRow = null;

        window.bindPaymentRowDragDrop = function (row) {
            row.setAttribute('draggable', 'true');

            row.addEventListener('dragstart', (e) => {
                window._draggingPaymentRow = row;
                row.style.opacity = '0.55';
                row.style.transform = 'scale(0.995)';
                try {
                    e.dataTransfer.effectAllowed = 'move';
                    e.dataTransfer.setData('text/plain', row.dataset.rowId || 'payment-row');
                } catch { }
            });

            row.addEventListener('dragend', () => {
                row.style.opacity = '1';
                row.style.transform = 'none';
                row.style.outline = 'none';
                window._draggingPaymentRow = null;
            });

            row.addEventListener('dragover', (e) => {
                e.preventDefault();
                row.style.outline = '1px dashed rgba(96,165,250,0.75)';
            });

            row.addEventListener('dragleave', () => {
                row.style.outline = 'none';
            });

            row.addEventListener('drop', (e) => {
                e.preventDefault();
                row.style.outline = 'none';
                const dragged = window._draggingPaymentRow;
                if (!dragged || dragged === row || !row.parentElement) return;

                const rect = row.getBoundingClientRect();
                const dropAfter = e.clientY > rect.top + (rect.height / 2);
                if (dropAfter) {
                    row.parentElement.insertBefore(dragged, row.nextSibling);
                } else {
                    row.parentElement.insertBefore(dragged, row);
                }
            });
        };

        window.renderPaymentAccountsEditor = function (accounts = []) {
            const container = document.getElementById('paymentAccountsContainer');
            if (!container) return;
            container.innerHTML = '';

            if (!Array.isArray(accounts) || accounts.length === 0) {
                accounts = [
                    { tipe: 'Bank Central Asia (BCA)', nomor: '', namaPemilik: '', tampilkan: true },
                    { tipe: 'Bank Mandiri', nomor: '', namaPemilik: '', tampilkan: true },
                    { tipe: 'E-Wallet (DANA/OVO/GoPay)', nomor: '', namaPemilik: '', tampilkan: true }
                ];
            }

            accounts.forEach((acc) => window.addPaymentAccountRow(acc));
        };

        window.addPaymentAccountRow = function (acc = {}) {
            const container = document.getElementById('paymentAccountsContainer');
            if (!container) return;
            const row = document.createElement('div');
            row.dataset.rowId = `pa-${Date.now()}-${Math.floor(Math.random() * 10000)}`;
            row.className = 'payment-account-row';
            row.style.cssText = 'background: rgba(15,23,42,0.28); border:1px solid rgba(255,255,255,0.08); border-radius:10px; padding:10px;';
            row.innerHTML = `
                <div style="display:grid; grid-template-columns: auto 1.2fr 1fr 1fr auto auto; gap:8px; align-items:center;">
                    <div title="Drag untuk ubah urutan" style="color:#94a3b8; display:flex; align-items:center; justify-content:center; width:30px; height:36px; border:1px dashed rgba(148,163,184,0.35); border-radius:8px; cursor:grab;">
                        <i class="fas fa-grip-vertical"></i>
                    </div>
                    <input type="text" class="search-input pa-type" placeholder="Jenis/Label (contoh: BCA)" value="${(acc.tipe || '').replace(/"/g, '&quot;')}" style="min-width:160px;">
                    <input type="text" class="search-input pa-number" placeholder="Nomor rekening/tujuan" value="${(acc.nomor || '').replace(/"/g, '&quot;')}">
                    <input type="text" class="search-input pa-owner" placeholder="Atas nama" value="${(acc.namaPemilik || '').replace(/"/g, '&quot;')}">
                    <label style="display:flex; align-items:center; gap:6px; color:#cbd5e1; font-size:12px; white-space:nowrap;">
                        <input type="checkbox" class="pa-visible" ${acc.tampilkan !== false ? 'checked' : ''}> Tampilkan
                    </label>
                    <button type="button" class="action-btn" title="Hapus baris" style="color:#ef4444;" onclick="this.closest('.payment-account-row')?.remove()"><i class="fas fa-trash"></i></button>
                </div>
            `;
            container.appendChild(row);
            window.bindPaymentRowDragDrop(row);
        };

        window.collectPaymentAccountsData = function () {
            const rows = [...document.querySelectorAll('#paymentAccountsContainer .payment-account-row')];
            return rows.map((row) => ({
                tipe: row.querySelector('.pa-type')?.value?.trim() || '',
                nomor: row.querySelector('.pa-number')?.value?.trim() || '',
                namaPemilik: row.querySelector('.pa-owner')?.value?.trim() || '',
                tampilkan: !!row.querySelector('.pa-visible')?.checked
            })).filter((x) => x.tipe || x.nomor || x.namaPemilik);
        };

        window.loadSettingsPembayaran = async function () {
            try {
                const res = await apiFetch('/pengaturan');
                if (res.success && res.data) {
                    const d = res.data;
                    document.getElementById('set_bca_no').value = d.payment_bca_no || '';
                    document.getElementById('set_bca_nama').value = d.payment_bca_nama || '';

                    document.getElementById('set_mdr_no').value = d.payment_mdr_no || '';
                    document.getElementById('set_mdr_nama').value = d.payment_mdr_nama || '';

                    document.getElementById('set_dana_no').value = d.payment_dana_no || '';
                    document.getElementById('set_dana_nama').value = d.payment_dana_nama || '';

                    document.getElementById('set_wa_cs').value = d.payment_wa_cs || '';
                    document.getElementById('set_isp_support_by').value = d.isp_support_by || 'Sans Speed MEDIA';
                    document.getElementById('set_payment_info_line1').value = d.payment_info_line1 || 'Pembayaran dibuka tanggal 25 - 05 setiap bulan.';
                    document.getElementById('set_payment_info_line2').value = d.payment_info_line2 || 'Jika melewati batas, pelanggan akan isolir tanggal 8.';

                    let paymentAccounts = [];
                    try {
                        paymentAccounts = JSON.parse(d.payment_accounts || '[]');
                    } catch {
                        paymentAccounts = [];
                    }

                    // Backward compatibility: seed from legacy fields if dynamic accounts empty
                    if (!Array.isArray(paymentAccounts) || paymentAccounts.length === 0) {
                        paymentAccounts = [
                            { tipe: 'Bank Central Asia (BCA)', nomor: d.payment_bca_no || '', namaPemilik: d.payment_bca_nama || '', tampilkan: true },
                            { tipe: 'Bank Mandiri', nomor: d.payment_mdr_no || '', namaPemilik: d.payment_mdr_nama || '', tampilkan: true },
                            { tipe: 'E-Wallet (DANA/OVO/GoPay)', nomor: d.payment_dana_no || '', namaPemilik: d.payment_dana_nama || '', tampilkan: true }
                        ].filter((x) => x.nomor || x.namaPemilik);
                    }
                    window.renderPaymentAccountsEditor(paymentAccounts);
                }
            } catch (err) {
                console.error("Gagal load pengaturan:", err);
            }
        };

        window.saveSettingsPembayaran = async function () {
            const payload = {
                payment_bca_no: document.getElementById('set_bca_no').value,
                payment_bca_nama: document.getElementById('set_bca_nama').value,
                payment_mdr_no: document.getElementById('set_mdr_no').value,
                payment_mdr_nama: document.getElementById('set_mdr_nama').value,
                payment_dana_no: document.getElementById('set_dana_no').value,
                payment_dana_nama: document.getElementById('set_dana_nama').value,
                payment_wa_cs: document.getElementById('set_wa_cs').value,
                isp_support_by: document.getElementById('set_isp_support_by').value,
                payment_info_line1: document.getElementById('set_payment_info_line1').value,
                payment_info_line2: document.getElementById('set_payment_info_line2').value,
                payment_accounts: JSON.stringify(window.collectPaymentAccountsData())
            };

            const confirmed = await window.showConfirm({
                title: "Simpan Perubahan?",
                message: "Apakah Anda yakin ingin memperbarui info rekening dan pembayaran ini?",
                type: 'confirm'
            });

            if (confirmed) {
                try {
                    const { apiFetch } = await import('./api-config.js');
                    const res = await apiFetch('/pengaturan', {
                        method: 'POST',
                        body: JSON.stringify(payload)
                    });

                    if (res.success) {
                        window.showToast("✅ Berhasil menyimpan pengaturan pembayaran.", "success");
                    } else {
                        window.showAlert({
                            title: "Gagal Menyimpan",
                            message: res.error || "Terjadi kesalahan pada server",
                            type: "danger"
                        });
                    }
                } catch (err) {
                    console.error(err);
                    window.showAlert({
                        title: "Terjadi Kesalahan",
                        message: err.message,
                        type: "danger"
                    });
                }
            }
        };

        window.switchTab = function (tabId, element) {
            if (window._blockedSettingsTabs?.has(tabId)) return;
            document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.tab-btn').forEach(el => el.classList.remove('active'));
            document.getElementById('tab-' + tabId).classList.add('active');
            element.classList.add('active');

            if (tabId === 'pembayaran') {
                window.loadSettingsPembayaran();
            }
            if (tabId === 'printer') {
                window.loadSettingsBranding();
            }
            if (tabId === 'integrasi') {
                window.loadSettingsIntegrasi?.();
            }
            if (tabId === 'tampilan') {
                window.loadSettingsTampilan?.();
            }
        };

        window._integrasiSubtab = 'pg';

        window.switchIntegrasiSubtab = function (sub, btnEl) {
            window._integrasiSubtab = sub;
            document.querySelectorAll('.integrasi-subtab-btn').forEach((b) => {
                b.classList.toggle('active', (b.getAttribute('data-integrasi-sub') || '') === sub);
            });
            const pg = document.getElementById('integrasi-pane-pg');
            const mk = document.getElementById('integrasi-pane-mikrotik');
            if (pg) pg.style.display = sub === 'pg' ? 'block' : 'none';
            if (mk) mk.style.display = sub === 'mikrotik' ? 'block' : 'none';
            if (sub === 'mikrotik') window.loadMikrotikRoutersTable?.();
        };

        window.defaultPgConfig = function () {
            return {
                vendor: 'tripay',
                isActive: false,
                mode: 'production',
                merchantCode: '',
                apiKey: '',
                privateKey: '',
                biayaAdmin: 0,
                expiredJam: 24,
                urlWebsite: '',
                whitelistIp: '',
                urlCallback: ''
            };
        };

        window.loadSettingsPaymentGateway = async function () {
            const badge = document.getElementById('pgApiStatusBadge');
            try {
                const res = await apiFetch('/pengaturan');
                let cfg = window.defaultPgConfig();
                if (res.success && res.data?.integration_payment_gateway) {
                    try {
                        cfg = { ...cfg, ...JSON.parse(res.data.integration_payment_gateway) };
                    } catch { /* ignore */ }
                }
                window.__pgCfgSnapshot = { ...cfg };
                document.getElementById('pgAktif').checked = !!cfg.isActive;
                document.getElementById('pgVendor').value = cfg.vendor || 'tripay';
                document.getElementById('pgMode').value = cfg.mode === 'sandbox' ? 'sandbox' : 'production';
                document.getElementById('pgMerchantCode').value = cfg.merchantCode || '';
                document.getElementById('pgBiayaAdmin').value = Number(cfg.biayaAdmin) || 0;
                document.getElementById('pgExpiredJam').value = Number(cfg.expiredJam) || 24;
                document.getElementById('pgUrlWebsite').value = cfg.urlWebsite || '';
                document.getElementById('pgWhitelistIp').value = cfg.whitelistIp || '';
                document.getElementById('pgUrlCallback').value = cfg.urlCallback || '';
                document.getElementById('pgApiKey').value = '';
                document.getElementById('pgPrivateKey').value = '';
                const hApi = document.getElementById('pgApiKeyHint');
                const hPk = document.getElementById('pgPrivateKeyHint');
                if (hApi) hApi.textContent = cfg.apiKey ? ' · sudah tersimpan' : '';
                if (hPk) hPk.textContent = cfg.privateKey ? ' · sudah tersimpan' : '';
                if (badge) {
                    badge.className = 'badge-warn';
                    badge.textContent = cfg.merchantCode ? 'API: wadah (belum diuji)' : 'API: lengkapi merchant';
                }
            } catch (e) {
                console.warn('loadSettingsPaymentGateway', e);
                if (badge) {
                    badge.className = 'badge-bad';
                    badge.textContent = 'Gagal memuat';
                }
            }
        };

        window.saveSettingsPaymentGateway = async function () {
            const snap = window.__pgCfgSnapshot || window.defaultPgConfig();
            const next = {
                vendor: document.getElementById('pgVendor').value || 'tripay',
                isActive: !!document.getElementById('pgAktif').checked,
                mode: document.getElementById('pgMode').value || 'production',
                merchantCode: document.getElementById('pgMerchantCode').value.trim(),
                biayaAdmin: Number(document.getElementById('pgBiayaAdmin').value) || 0,
                expiredJam: Math.max(1, Number(document.getElementById('pgExpiredJam').value) || 24),
                urlWebsite: document.getElementById('pgUrlWebsite').value.trim(),
                whitelistIp: document.getElementById('pgWhitelistIp').value.trim(),
                urlCallback: document.getElementById('pgUrlCallback').value.trim(),
                apiKey: document.getElementById('pgApiKey').value.trim() || (snap.apiKey || ''),
                privateKey: document.getElementById('pgPrivateKey').value.trim() || (snap.privateKey || '')
            };
            try {
                const res = await apiFetch('/pengaturan', {
                    method: 'POST',
                    body: JSON.stringify({ integration_payment_gateway: JSON.stringify(next) })
                });
                if (res.success) {
                    window.__pgCfgSnapshot = { ...next };
                    document.getElementById('pgApiKey').value = '';
                    document.getElementById('pgPrivateKey').value = '';
                    const hApi = document.getElementById('pgApiKeyHint');
                    const hPk = document.getElementById('pgPrivateKeyHint');
                    if (hApi) hApi.textContent = next.apiKey ? ' · sudah tersimpan' : '';
                    if (hPk) hPk.textContent = next.privateKey ? ' · sudah tersimpan' : '';
                    showToast('✅ Pengaturan payment gateway disimpan.', 'success');
                } else {
                    showAlert({ title: 'Gagal', message: res.error || 'Simpan gagal', type: 'danger' });
                }
            } catch (err) {
                showAlert({ title: 'Gagal', message: err.message || 'Error', type: 'danger' });
            }
        };

        window.loadMikrotikRoutersTable = async function () {
            const tbody = document.getElementById('tableMikrotikRoutersBody');
            if (!tbody) return;
            tbody.innerHTML = '<tr><td colspan="12" style="text-align:center;"><i class="fas fa-spinner fa-spin"></i> Memuat...</td></tr>';
            try {
                const res = await apiFetch('/mikrotik-routers');
                const rows = (res.success && res.data) ? res.data : [];
                if (!rows.length) {
                    tbody.innerHTML = '<tr><td colspan="12" style="text-align:center;color:#94a3b8;padding:20px;">Belum ada router. Klik Tambah router.</td></tr>';
                    return;
                }
                tbody.innerHTML = rows.map((r, i) => {
                    const ros = escHtml(r.rosVersi || 'V6');
                    const um = r.userManager ? escHtml(r.userManager) : '—';
                    const hs = escHtml((r.hotspotManager || 'tidak_aktif').replace('_', ' '));
                    const pc = Number(r.pelangganCount) || 0;
                    const host = escHtml(r.host || '—');
                    const user = escHtml(r.apiUser || '—');
                    const svc = escHtml(r.serviceType || 'API');
                    const port = escHtml(String(r.apiPort || ''));
                    let conn = '<span class="badge-bad">Belum diuji</span>';
                    if (r.lastProbeAt != null) {
                        if (Number(r.lastProbeOk) === 1) {
                            const ms = r.lastProbeMs != null ? ` (${Number(r.lastProbeMs).toFixed(0)} ms)` : '';
                            conn = `<span class="badge-ok">Online${escHtml(ms)}</span>`;
                        } else {
                            conn = '<span class="badge-bad">Offline / gagal</span>';
                        }
                    }
                    return `
                        <tr>
                            <td>${i + 1}</td>
                            <td>
                                <button type="button" class="action-btn" title="Edit" onclick="window.bukaModalMikrotikRouter('${escHtml(r.id)}')"><i class="fas fa-edit"></i></button>
                                <button type="button" class="action-btn" style="color:#ef4444;" title="Hapus" onclick="window.hapusMikrotikRouter('${escHtml(r.id)}')"><i class="fas fa-trash"></i></button>
                            </td>
                            <td><strong>${escHtml(r.nama)}</strong></td>
                            <td>${ros}</td>
                            <td>${um}</td>
                            <td>${hs}</td>
                            <td>${pc}</td>
                            <td style="font-size:12px;">${host}</td>
                            <td>${conn}</td>
                            <td>${user}</td>
                            <td style="font-size:12px;">${svc} — ${port}</td>
                            <td><button type="button" class="btn-primary" style="padding:6px 10px;font-size:12px;" onclick="window.testMikrotikProbe('${escHtml(r.id)}')">Test</button></td>
                        </tr>`;
                }).join('');
            } catch (e) {
                tbody.innerHTML = `<tr><td colspan="12" style="text-align:center;color:#ef4444;">${escHtml(e.message)}</td></tr>`;
            }
        };

        window.loadSettingsIntegrasi = function () {
            const sub = window._integrasiSubtab || 'pg';
            const subBtn = document.querySelector('.integrasi-subtab-btn[data-integrasi-sub="' + sub + '"]');
            window.switchIntegrasiSubtab(sub, subBtn);
            window.loadSettingsPaymentGateway();
        };

        window.bukaModalMikrotikRouter = async function (id) {
            const modal = document.getElementById('modalMikrotikRouter');
            const title = document.getElementById('modalMikrotikRouterTitle');
            document.getElementById('mrGantiPassword').checked = false;
            document.getElementById('mrPasswordWrap').style.display = 'none';
            document.getElementById('mrApiPassword').value = '';
            if (!id) {
                if (title) title.innerHTML = '<i class="fas fa-server" style="color: var(--primary);"></i> Router baru';
                document.getElementById('mrEditId').value = '';
                document.getElementById('mrNama').value = '';
                document.getElementById('mrRosVersi').value = 'V6';
                document.getElementById('mrHotspot').value = 'tidak_aktif';
                document.getElementById('mrUserManager').value = '';
                document.getElementById('mrHost').value = '';
                document.getElementById('mrApiUser').value = '';
                document.getElementById('mrServiceType').value = 'API';
                document.getElementById('mrApiPort').value = '8728';
                document.getElementById('mrKeterangan').value = '';
                document.getElementById('mrGantiPassword').checked = true;
                document.getElementById('mrPasswordWrap').style.display = 'block';
                document.getElementById('mrApiPassword').value = '';
            } else {
                if (title) title.innerHTML = '<i class="fas fa-server" style="color: var(--primary);"></i> Edit router';
                document.getElementById('mrEditId').value = id;
                try {
                    const res = await apiFetch('/mikrotik-routers');
                    const row = (res.success && res.data) ? res.data.find((x) => x.id === id) : null;
                    if (!row) {
                        showToast('Router tidak ditemukan', 'error');
                        return;
                    }
                    document.getElementById('mrNama').value = row.nama || '';
                    document.getElementById('mrRosVersi').value = row.rosVersi === 'V7' ? 'V7' : 'V6';
                    document.getElementById('mrHotspot').value = (row.hotspotManager === 'aktif') ? 'aktif' : 'tidak_aktif';
                    document.getElementById('mrUserManager').value = row.userManager || '';
                    document.getElementById('mrHost').value = row.host || '';
                    document.getElementById('mrApiUser').value = row.apiUser || '';
                    document.getElementById('mrServiceType').value = (row.serviceType === 'API-SSL') ? 'API-SSL' : 'API';
                    document.getElementById('mrApiPort').value = String(row.apiPort || 8728);
                    document.getElementById('mrKeterangan').value = row.keterangan || '';
                } catch (e) {
                    showAlert({ title: 'Error', message: e.message, type: 'danger' });
                    return;
                }
            }
            if (modal) modal.classList.add('active');
        };

        window.tutupModalMikrotikRouter = function () {
            document.getElementById('modalMikrotikRouter')?.classList.remove('active');
        };

        window.simpanModalMikrotikRouter = async function () {
            const id = document.getElementById('mrEditId').value.trim();
            const nama = document.getElementById('mrNama').value.trim();
            if (!nama) {
                showToast('Nama router wajib diisi', 'warning');
                return;
            }
            const body = {
                nama,
                host: document.getElementById('mrHost').value.trim() || null,
                apiPort: parseInt(document.getElementById('mrApiPort').value, 10) || 8728,
                apiUser: document.getElementById('mrApiUser').value.trim() || null,
                keterangan: document.getElementById('mrKeterangan').value.trim() || null,
                rosVersi: document.getElementById('mrRosVersi').value,
                userManager: document.getElementById('mrUserManager').value.trim() || null,
                hotspotManager: document.getElementById('mrHotspot').value,
                serviceType: document.getElementById('mrServiceType').value
            };
            const isNew = !id;
            const gantiPw = document.getElementById('mrGantiPassword').checked;
            if (isNew || gantiPw) {
                body.apiPassword = document.getElementById('mrApiPassword').value;
            }
            try {
                if (id) {
                    await apiFetch('/mikrotik-routers/' + id, { method: 'PUT', body: JSON.stringify(body) });
                } else {
                    await apiFetch('/mikrotik-routers', { method: 'POST', body: JSON.stringify(body) });
                }
                showToast('✅ Router disimpan', 'success');
                window.tutupModalMikrotikRouter();
                window.loadMikrotikRoutersTable();
            } catch (e) {
                showAlert({ title: 'Gagal simpan', message: e.message, type: 'danger' });
            }
        };

        window.hapusMikrotikRouter = async function (id) {
            const ok = await showConfirm({
                title: 'Hapus router?',
                message: 'Pelanggan yang memakai router ini akan kehilangan referensi (router dikosongkan).',
                type: 'danger',
                confirmText: 'Hapus',
                cancelText: 'Batal'
            });
            if (!ok) return;
            try {
                await apiFetch('/mikrotik-routers/' + id, { method: 'DELETE' });
                showToast('Router dihapus', 'success');
                window.loadMikrotikRoutersTable();
            } catch (e) {
                showAlert({ title: 'Gagal', message: e.message, type: 'danger' });
            }
        };

        window.testMikrotikProbe = async function (id) {
            try {
                const res = await apiFetch('/mikrotik-routers/' + id + '/probe', { method: 'POST', body: '{}' });
                showToast(res.ok ? `TCP OK · ${res.latencyMs != null ? res.latencyMs + ' ms' : ''}` : (res.message || 'Gagal'), res.ok ? 'success' : 'warning');
                window.loadMikrotikRoutersTable();
            } catch (e) {
                showToast(e.message || 'Gagal test', 'error');
            }
        };

        window.loadSettingsTampilan = function () {
            const light = localStorage.getItem('ss_theme') === 'light';
            document.getElementById('themePrefLight').checked = light;
            document.getElementById('themePrefDark').checked = !light;
        };

        window.applyThemeFromSettings = function () {
            const light = !!document.getElementById('themePrefLight').checked;
            document.documentElement.classList.toggle('light-mode', light);
            document.body.classList.toggle('light-mode', light);
            localStorage.setItem('ss_theme', light ? 'light' : 'dark');
            showToast(light ? 'Tema terang diterapkan' : 'Tema gelap diterapkan', 'success');
        };

        window.applySettingsHashTab = function () {
            const raw = (location.hash || '').replace(/^#/, '');
            if (!raw || !raw.startsWith('integrasi')) return;
            const btn = document.querySelector('.tab-btn[data-tab="integrasi"]');
            if (!btn || btn.style.display === 'none') return;
            window.switchTab('integrasi', btn);
            if (raw.includes('mikrotik')) {
                const subBtn = document.querySelector('.integrasi-subtab-btn[data-integrasi-sub="mikrotik"]');
                window.switchIntegrasiSubtab('mikrotik', subBtn);
            } else {
                const subBtn = document.querySelector('.integrasi-subtab-btn[data-integrasi-sub="pg"]');
                window.switchIntegrasiSubtab('pg', subBtn);
            }
        };

        document.getElementById('mrGantiPassword')?.addEventListener('change', function () {
            const show = this.checked;
            document.getElementById('mrPasswordWrap').style.display = show ? 'block' : 'none';
            if (!show) document.getElementById('mrApiPassword').value = '';
        });

        document.getElementById('modalMikrotikRouter')?.addEventListener('click', function (e) {
            if (e.target === this) window.tutupModalMikrotikRouter();
        });

        window.addEventListener('hashchange', () => window.applySettingsHashTab?.());

        window.downloadBackup = async function () {
            try {
                const token = localStorage.getItem('ss_token');
                const response = await fetch(`${API_BASE_URL}/backup`, {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                if (!response.ok) {
                    const errorObj = await response.json().catch(() => ({}));
                    throw new Error(errorObj.error || "Gagal mengunduh backup database");
                }
                const blob = await response.blob();
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `backup_sans_speed_database_${new Date().getTime()}.db`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
            } catch (err) {
                alert("Gagal: " + err.message);
            }
        };

        window.loadAuditLogs = async function () {
            const tbody = document.getElementById('tableAuditLogs');
            if (!tbody) return;
            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;"><i class="fas fa-spinner fa-spin"></i> Memuat histori...</td></tr>';
            try {
                const res = await apiFetch('/audit');
                if (res.success && res.data.length > 0) {
                    tbody.innerHTML = res.data.map(log => `
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="font-size:12px; color:#cbd5e1;">${escHtml(new Date(log.tanggal).toLocaleString('id-ID'))}</td>
                            <td><div style="font-weight:600;">${escHtml(log.userEmail)}</div><span class="badge" style="background:rgba(255,255,255,0.1); color:#94a3b8;">${escHtml(log.userRole)}</span></td>
                            <td><span style="color:#60a5fa; font-weight:bold;">${escHtml(log.aksi)}</span> <br> <span style="font-size:11px;color:#94a3b8;">${escHtml(log.entitas || '-')}</span></td>
                            <td style="color:#cbd5e1; font-size:13px;">${escHtml(log.keterangan || '-')}</td>
                        </tr>
                    `).join('');
                } else {
                    tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;color:#94a3b8;padding:20px;">Belum ada log aktivitas yang tercatat.</td></tr>';
                }
            } catch (err) {
                tbody.innerHTML = `<tr><td colspan="4" style="text-align:center;color:#ef4444;padding:20px;">Gagal memuat log: ${escHtml(err.message)}</td></tr>`;
            }
        };

        // Load Karyawan
        window.loadKaryawan = async function () {
            const tbody = document.getElementById('tableKaryawanBody');
            const mobileList = document.getElementById('mobileKaryawanList');
            tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;"><i class="fas fa-spinner fa-spin"></i> Memuat...</td></tr>';
            if (mobileList) {
                mobileList.innerHTML = '<div class="mobile-data-empty"><i class="fas fa-spinner fa-spin"></i> Memuat data karyawan...</div>';
            }
            try {
                const [datanya, performaRes] = await Promise.all([
                    apiFetch('/collections/users'),
                    apiFetch('/stats/performa-karyawan')
                ]);
                window.statsArr = performaRes.success ? performaRes.data : [];

                // Update KPI Cards
                let cTotal = datanya.length, cTeknisi = 0, cTekpen = 0, cAdmin = 0, cPenagih = 0;
                datanya.forEach(d => {
                    const roleKey = resolveRoleKey(d.roleKey || d.role);
                    if (roleKey === 'teknisi') cTeknisi++;
                    else if (roleKey === 'tekpen') cTekpen++;
                    else if (roleKey === 'penagih') cPenagih++;
                    else if (roleKey === 'owner' || roleKey === 'admin_keuangan' || roleKey === 'admin_kasir') cAdmin++;
                });
                const elTot = document.getElementById('stat-total');
                if (elTot) elTot.innerHTML = `${cTotal} <span style="font-size: 12px; font-weight: 400; color: #64748b;">Orang</span>`;
                const elPenagih = document.getElementById('stat-penagih');
                if (elPenagih) elPenagih.innerHTML = `${cPenagih} <span style="font-size: 12px; font-weight: 400; color: #64748b;">Orang</span>`;
                const elTek = document.getElementById('stat-teknisi');
                if (elTek) elTek.innerHTML = `${cTeknisi} <span style="font-size: 12px; font-weight: 400; color: #64748b;">Orang</span>`;
                const elTekpen = document.getElementById('stat-tekpen');
                if (elTekpen) elTekpen.innerHTML = `${cTekpen} <span style="font-size: 12px; font-weight: 400; color: #64748b;">Orang</span>`;
                const elAdmin = document.getElementById('stat-admin');
                if (elAdmin) elAdmin.innerHTML = `${cAdmin} <span style="font-size: 12px; font-weight: 400; color: #64748b;">Orang</span>`;

                // Sort client-side berdasarkan nama (A-Z)
                const sortedDocs = datanya.sort((a, b) =>
                    (a.nama || '').localeCompare(b.nama || ''));
                let html = '';

                if (datanya.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" style="text-align: center;">Belum ada data Karyawan.</td></tr>';
                    if (mobileList) {
                        mobileList.innerHTML = '<div class="mobile-data-empty">Belum ada data karyawan.</div>';
                    }
                    return;
                }
                if (mobileList) mobileList.innerHTML = '';

                sortedDocs.forEach((data) => {
                    const id = data.id;
                    const safeId = escHtml(id || '');
                    const safeNama = escHtml(data.nama || '-');
                    const safeEmail = escHtml(data.email || '-');
                    const safeWA = escHtml(data.noWA || '-');
                    const safeRole = escHtml(data.role || '');
                    const inisial = (data.nama || 'K').substring(0, 2).toUpperCase();

                    let roleBadge = '';
                    const roleKey = resolveRoleKey(data.roleKey || data.role);
                    let roleTitle = roleKey;
                    if (roleKey === 'owner') roleBadge = '<span class="role-badge role-admin"><i class="fas fa-crown"></i> Owner</span>';
                    else if (roleKey === 'admin_keuangan') roleBadge = '<span class="role-badge role-admin"><i class="fas fa-laptop"></i> Admin Keuangan</span>';
                    else if (roleKey === 'admin_kasir') roleBadge = '<span class="role-badge role-admin" style="background:rgba(56,189,248,.2); color:#38bdf8;"><i class="fas fa-cash-register"></i> Admin Kasir</span>';
                    else if (roleKey === 'penagih') roleBadge = '<span class="role-badge role-penagih"><i class="fas fa-motorcycle"></i> Agen Saja</span>';
                    else if (roleKey === 'tekpen') roleBadge = '<span class="role-badge role-penagih" style="background:rgba(16, 185, 129, 0.2);"><i class="fas fa-motorcycle"></i> Tek+Agen</span>';
                    else roleBadge = '<span class="role-badge role-teknisi"><i class="fas fa-tools"></i> Teknisi</span>';

                    let performaHtml = '';
                    const userStat = window.statsArr ? window.statsArr.find(s => s.email === data.email) : null;
                    const statValues = userStat || { totalTagihanDikumpulkan: 0, jobTeknisiSelesai: 0 };

                    if (roleKey === 'penagih' || roleKey === 'tekpen') {
                        performaHtml += `<div style="font-size: 11px; color:#34d399; margin-top:2px;" title="Total tagihan sukses tertagih bulan ini">Rekap Tagihan: <span style="font-weight:600;">${formatRupiah(statValues.totalTagihanDikumpulkan)}</span></div>`;
                    }
                    if (roleKey === 'teknisi' || roleKey === 'tekpen') {
                        performaHtml += `<div style="font-size: 11px; color:#60a5fa; margin-top:2px;" title="Tugas/Job selesai bulan ini">Job Selesai: <span style="font-weight:600;">${statValues.jobTeknisiSelesai}</span></div>`;
                    }

                    html += `
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div style="width: 36px; height: 36px; border-radius: 50%; background: rgba(59, 130, 246, 0.2); color: #60a5fa; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 12px;">
                                        ${inisial}
                                    </div>
                                    <div>
                                        <div style="font-weight: 600; color: var(--text-primary, white);">${safeNama}</div>
                                        <div style="font-size: 12px; color: #94a3b8; margin-top: 2px;">ID: ${safeId.substring(0, 6)}</div>
                                    </div>
                                </div>
                            </td>
                            <td>${roleBadge}</td>
                            <td>
                                <div style="font-size: 13px; color: #cbd5e1;"><i class="fas fa-envelope"></i> ${safeEmail}</div>
                                <div style="font-size: 12px; color: #94a3b8; margin-top: 4px;"><i class="fab fa-whatsapp"></i> ${safeWA}</div>
                            </td>
                            <td>
                                <div style="color: #94a3b8; font-size: 13px;">Semua Area (Global)</div>
                                ${performaHtml}
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #cbd5e1;">Gaji Pokok</div>
                                <div style="font-size: 12px; color: #94a3b8;">${formatRupiah(data.gaji || 0)}</div>
                            </td>
                            <td style="text-align: right;">
                                <button class="action-btn btn-edit-emp" title="Edit Data"
                                    data-id="${safeId}"
                                    data-nama="${safeNama}"
                                    data-email="${safeEmail}"
                                    data-wa="${safeWA}"
                                    data-role="${safeRole}"
                                    data-gaji="${data.gaji || 0}"
                                    onclick="window.editKaryawan(this.dataset.id, this.dataset.nama, this.dataset.email, this.dataset.wa, this.dataset.role, this.dataset.gaji)"
                                    ><i class="fas fa-edit"></i></button>
                                <button class="action-btn btn-del-emp" title="Hapus Karyawan"
                                    data-id="${safeId}"
                                    onclick="window.hapusKaryawan(this.dataset.id)"
                                    style="color: #ef4444;"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    `;
                    if (mobileList) {
                        mobileList.innerHTML += `
                            <article class="mobile-data-card">
                                <div class="mobile-data-head">
                                    <div>
                                        <div class="mobile-data-title">${safeNama}</div>
                                        <div class="mobile-data-sub">ID: ${safeId.substring(0, 6)} • ${escHtml(roleTitle || '-')}</div>
                                    </div>
                                    <div style="width: 34px; height: 34px; border-radius: 50%; background: rgba(59, 130, 246, 0.2); color: #60a5fa; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 11px;">
                                        ${inisial}
                                    </div>
                                </div>
                                <div class="mobile-data-info">
                                    <div><i class="fas fa-envelope"></i> ${safeEmail}</div>
                                    <div><i class="fab fa-whatsapp"></i> ${safeWA}</div>
                                    <div style="margin-top:4px;">Gaji Pokok: ${formatRupiah(data.gaji || 0)}</div>
                                    ${performaHtml ? `<div style="margin-top:4px;">${performaHtml}</div>` : ''}
                                </div>
                                <div class="mobile-data-actions">
                                    ${roleBadge}
                                    <button class="action-btn" onclick="window.editKaryawan('${String(id || '').replace(/'/g, "\\'")}', '${String(data.nama || '').replace(/'/g, "\\'")}', '${String(data.email || '').replace(/'/g, "\\'")}', '${String(data.noWA || '').replace(/'/g, "\\'")}', '${String(data.role || '').replace(/'/g, "\\'")}', '${data.gaji || 0}')" style="color:#3b82f6;">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="action-btn" onclick="window.hapusKaryawan('${String(id || '').replace(/'/g, "\\'")}')" style="color:#ef4444;">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </article>
                        `;
                    }
                });
                tbody.innerHTML = html;

                // Tombol dikelola by inline onclick

            } catch (err) {
                console.error(err);
                tbody.innerHTML = '<tr><td colspan="6" style="text-align: center; color:red;">Gagal memuat data.</td></tr>';
                if (mobileList) {
                    mobileList.innerHTML = '<div class="mobile-data-empty" style="color:#ef4444;">Gagal memuat data karyawan.</div>';
                }
            }
        };

        const parseAreaValues = (raw) => {
            if (raw === null || typeof raw === 'undefined') return [];
            if (Array.isArray(raw)) return raw.flatMap(parseAreaValues);
            if (typeof raw === 'object') return [raw.id, raw.nama, raw.area, raw.value].flatMap(parseAreaValues);
            const txt = String(raw || '').trim();
            if (!txt) return [];
            if ((txt.startsWith('[') && txt.endsWith(']')) || (txt.startsWith('{') && txt.endsWith('}'))) {
                try { return parseAreaValues(JSON.parse(txt)); } catch { }
            }
            if (txt.includes(',')) return txt.split(',').flatMap(parseAreaValues);
            return [txt];
        };

        window.loadAreaForModal = async function (existingAreaIds = []) {
            const container = document.getElementById('areaCheckboxContainer');
            container.innerHTML = '<span style="color: #64748b; font-size: 13px;"><i class="fas fa-spinner fa-spin"></i> Memuat area...</span>';
            try {
                const areas = await apiFetch('/collections/areas');
                if (!areas || areas.length === 0) {
                    container.innerHTML = '<span style="color: #64748b; font-size: 13px;">Belum ada Area terdaftar.</span>';
                    return;
                }
                const existingSet = new Set(parseAreaValues(existingAreaIds).map(v => String(v || '').trim().toLowerCase()));
                const sorted = areas.sort((a, b) => (a.nama || '').localeCompare(b.nama || ''));
                container.innerHTML = sorted.map(area => {
                    const areaIdKey = String(area.id || '').trim().toLowerCase();
                    const areaNameKey = String(area.nama || '').trim().toLowerCase();
                    const isChecked = (existingSet.has(areaIdKey) || existingSet.has(areaNameKey)) ? 'checked' : '';
                    return `<label style="display: flex; align-items: center; gap: 6px; color: #cbd5e1; font-size: 13px; background: rgba(59,130,246,0.1); padding: 4px 10px; border-radius: 6px; cursor: pointer;">
                        <input type="checkbox" class="area-cb" value="${escHtml(area.id)}" ${isChecked}> ${escHtml(area.nama)}
                    </label>`;
                }).join('');
            } catch (err) {
                container.innerHTML = '<span style="color: #ef4444; font-size: 13px;"><i class="fas fa-exclamation-triangle"></i> Gagal memuat area.</span>';
            }
        };

        window.bukaModalKaryawan = function () {
            document.getElementById('editEmpId').value = '';
            document.getElementById('inNamaEmp').value = '';
            document.getElementById('inEmailEmp').value = '';
            document.getElementById('inPasswordEmp').value = '';
            document.getElementById('inWAEmp').value = '';
            document.getElementById('inJabatan').value = 'penagih';
            document.getElementById('inGaji').value = '';
            window.loadAreaForModal([]);
            document.getElementById('addModal').classList.add('active');
        };

        window.closeModal = function () {
            document.getElementById('addModal').classList.remove('active');
        };

        window.editKaryawan = async function (id, nama, email, wa, role, gaji) {
            document.getElementById('editEmpId').value = id;
            document.getElementById('inNamaEmp').value = nama;
            document.getElementById('inEmailEmp').value = email !== 'undefined' ? email : '';
            document.getElementById('inPasswordEmp').value = ''; // Selalu kosongkan untuk UI safety
            document.getElementById('inWAEmp').value = wa !== 'undefined' ? wa : '';
            document.getElementById('inJabatan').value = role;
            document.getElementById('inGaji').value = gaji !== 'undefined' ? gaji : '';
            document.getElementById('addModal').classList.add('active');
            // Load data area karyawan yang sudah ada
            try {
                const userData = await apiFetch(`/collections/users/${id}`);
                const existingAreas = parseAreaValues(userData.areas);
                window.loadAreaForModal(existingAreas);
            } catch {
                window.loadAreaForModal([]);
            }
        };

        window.simpanData = async function () {
            const id = document.getElementById('editEmpId').value;
            const inputPass = document.getElementById('inPasswordEmp').value.trim();
            // Baca area yang dipilih dari checkbox dinamis
            const selectedAreas = [...document.querySelectorAll('.area-cb:checked')].map(cb => cb.value);
            const payload = {
                nama: document.getElementById('inNamaEmp').value,
                email: document.getElementById('inEmailEmp').value,
                noWA: document.getElementById('inWAEmp').value,
                role: document.getElementById('inJabatan').value,
                gaji: Number(document.getElementById('inGaji').value) || 0,
                areas: selectedAreas
            };

            // Validasi: Wajib isi nama
            if (!payload.nama) return showToast('⚠️ Nama wajib diisi!', 'warning');

            // Validasi: Wajib isi password JIKA BUAT BARU
            if (!id && !inputPass) return showToast('⚠️ Password wajib diisi untuk Karyawan Baru!', 'warning');

            // Tambahkan parameter password hanya jika diketik (berubah format di backend otomatis)
            if (inputPass) payload.password = inputPass;

            try {
                if (id) {
                    await apiFetch(`/collections/users/${id}`, {
                        method: 'PUT',
                        body: JSON.stringify(payload)
                    });
                    showToast('✅ Data Karyawan berhasil diperbarui!', 'success');
                } else {
                    await apiFetch('/collections/users', {
                        method: 'POST',
                        body: JSON.stringify(payload)
                    });
                    showToast('✅ Karyawan Baru dibuat ke Database!', 'success');
                }
                window.closeModal();
                window.loadKaryawan();
            } catch (error) {
                console.error(error);
                showAlert({ title: 'Gagal Menyimpan', message: error.message || 'Gagal menyimpan data Karyawan', type: 'danger' });
            }
        };

        window.hapusKaryawan = async function (id) {
            const confirmed = await showConfirm({
                title: '⚠️ Coret Karyawan',
                message: 'Yakin menghapus Profil Karyawan / Staff ini secara <strong>permanen</strong>?',
                type: 'danger',
                confirmText: 'Ya, Hapus',
                cancelText: 'Batal'
            });

            if (confirmed) {
                try {
                    await apiFetch(`/collections/users/${id}`, { method: 'DELETE' });
                    window.loadKaryawan();
                    showToast('✅ Profil Karyawan terhapus.', 'success');
                } catch (err) {
                    showAlert({ title: 'Error Menghapus', message: err.message, type: 'danger' });
                }
            }
        };

        document.getElementById('addModal').addEventListener('click', function (e) {
            if (e.target === this) window.closeModal();
        });

        // Local Settings Management (WA & Printer)
        const loadSettings = () => {
            const printSet = JSON.parse(localStorage.getItem('ss_printer_config') || '{}');
            document.getElementById('inPrintHeader').value = printSet.header || 'Sans Speed PUSAT';
            document.getElementById('inPrintSub').value = printSet.sub || 'Layanan Internet Desa';
            document.getElementById('inPrintFooter').value = printSet.footer || 'Terima kasih atas pembayaran Anda.';

            const waSet = JSON.parse(localStorage.getItem('ss_wa_config') || '{}');
            document.getElementById('inWaTagihan').value = waSet.tagihan || 'Halo Yth. *{nama}*,\nKami informasikan bahwa tagihan internet Anda sebesar *{total_tagihan}* sudah terbit.\nMohon dibayar sebelum tanggal *{jatuh_tempo}*.\nEstimasi tanggal isolir layanan: *{tgl_isolir}*.\nID: *{id_pelanggan}* • Periode *{periode}*. Terima kasih.';
            document.getElementById('inWaLunas').value = waSet.lunas || 'Halo Yth. *{nama}*,\nPembayaran tagihan internet sebesar *{total_tagihan}* telah kami terima.\nPeriode *{periode}*. Internet Lancar, Rezeki Lancar!';
        };

        window.saveSettingsPrinter = async () => {
            const printSet = {
                header: document.getElementById('inPrintHeader').value,
                sub: document.getElementById('inPrintSub').value,
                footer: document.getElementById('inPrintFooter').value
            };
            localStorage.setItem('ss_printer_config', JSON.stringify(printSet));
            const pendingLogoData = document.getElementById('inSidebarLogoData')?.value?.trim();
            const pendingLogoUrl = document.getElementById('inSidebarLogoUrl')?.value?.trim();
            if (pendingLogoData || pendingLogoUrl) {
                await window.saveSettingsBranding(true);
                showToast('🖨️ Konfigurasi printer + logo sidebar berhasil disimpan!', 'success');
            } else {
                showToast('🖨️ Pengaturan layout struk printer berhasil disimpan!', 'success');
            }
        };

        window.saveSettingsWA = () => {
            const waSet = {
                tagihan: document.getElementById('inWaTagihan').value,
                lunas: document.getElementById('inWaLunas').value
            };
            localStorage.setItem('ss_wa_config', JSON.stringify(waSet));
            showToast('✅ Template pesan otomatis WhatsApp berhasil disimpan!', 'success');
        };

        const SIDEBAR_LOGO_MAX_BYTES = 15 * 1024;
        const SIDEBAR_LOGO_MAX_DIM = 128;

        const estimateDataUrlBytes = (dataUrl = '') => {
            const base64 = (dataUrl.split(',')[1] || '');
            return Math.floor((base64.length * 3) / 4);
        };

        const setSidebarLogoPreview = (logoData = '') => {
            const img = document.getElementById('previewSidebarLogo');
            const fallback = document.getElementById('previewSidebarLogoFallback');
            const hidden = document.getElementById('inSidebarLogoData');
            if (!img || !fallback || !hidden) return;

            hidden.value = logoData || '';
            if (logoData) {
                img.src = logoData;
                img.style.display = 'block';
                fallback.style.display = 'none';
            } else {
                img.src = '';
                img.style.display = 'none';
                fallback.style.display = 'inline-block';
            }
        };

        const readFileAsDataURL = (file) => new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => resolve(reader.result);
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });

        const loadImage = (src) => new Promise((resolve, reject) => {
            const img = new Image();
            img.onload = () => resolve(img);
            img.onerror = reject;
            img.src = src;
        });

        const optimizeRasterLogo = async (file) => {
            const originalDataUrl = await readFileAsDataURL(file);
            const img = await loadImage(originalDataUrl);

            const scale = Math.min(1, SIDEBAR_LOGO_MAX_DIM / Math.max(img.width, img.height));
            const width = Math.max(1, Math.round(img.width * scale));
            const height = Math.max(1, Math.round(img.height * scale));

            const canvas = document.createElement('canvas');
            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, width, height);
            ctx.drawImage(img, 0, 0, width, height);

            let bestDataUrl = canvas.toDataURL('image/webp', 0.9);
            for (let q = 0.9; q >= 0.45; q -= 0.1) {
                const compressed = canvas.toDataURL('image/webp', q);
                bestDataUrl = compressed;
                if (estimateDataUrlBytes(compressed) <= SIDEBAR_LOGO_MAX_BYTES) {
                    break;
                }
            }
            return bestDataUrl;
        };

        window.handleSidebarLogoFile = async function (event) {
            const file = event?.target?.files?.[0];
            if (!file) return;

            try {
                if (!file.type.startsWith('image/')) {
                    showToast('⚠️ File harus berupa gambar (PNG/JPG/WebP/SVG).', 'warning');
                    return;
                }
                if (file.size > 2 * 1024 * 1024) {
                    showToast('⚠️ Ukuran file terlalu besar. Maksimal 2MB.', 'warning');
                    return;
                }

                let logoData = '';
                if (file.type === 'image/svg+xml') {
                    logoData = await readFileAsDataURL(file);
                } else {
                    logoData = await optimizeRasterLogo(file);
                }

                setSidebarLogoPreview(logoData);
                document.getElementById('inSidebarLogoUrl').value = '';
                window.dispatchEvent(new CustomEvent('ss:sidebarLogoUpdated', { detail: { logoData } }));

                const resultBytes = estimateDataUrlBytes(logoData);
                const kb = (resultBytes / 1024).toFixed(1);
                showToast(`✅ Logo siap digunakan (${kb} KB).`, 'success');
            } catch (err) {
                console.error(err);
                showAlert({ title: 'Gagal memproses logo', message: err.message || 'Format gambar tidak didukung.', type: 'danger' });
            } finally {
                if (event?.target) event.target.value = '';
            }
        };

        window.loadSettingsBranding = async function () {
            try {
                const res = await apiFetch('/pengaturan');
                const d = (res && res.success && res.data) ? res.data : {};
                const logoData = d.sidebar_logo_data || localStorage.getItem('ss_sidebar_logo_data') || '';
                const logoUrlInput = document.getElementById('inSidebarLogoUrl');
                if (logoUrlInput) logoUrlInput.value = '';
                setSidebarLogoPreview(logoData);
            } catch (err) {
                const fallback = localStorage.getItem('ss_sidebar_logo_data') || '';
                setSidebarLogoPreview(fallback);
            }
        };

        window.saveSettingsBranding = async function (silentToast = false) {
            const dataFromUpload = document.getElementById('inSidebarLogoData').value.trim();
            const dataFromUrl = document.getElementById('inSidebarLogoUrl').value.trim();
            let logoData = dataFromUpload || dataFromUrl || '';

            if (dataFromUrl && !/^https?:\/\//i.test(dataFromUrl) && !/^data:image\//i.test(dataFromUrl)) {
                showToast('⚠️ URL logo tidak valid. Gunakan http(s):// atau upload file.', 'warning');
                return;
            }

            try {
                const payload = { sidebar_logo_data: logoData };
                const res = await apiFetch('/pengaturan', {
                    method: 'POST',
                    body: JSON.stringify(payload)
                });
                if (res.success) {
                    localStorage.setItem('ss_sidebar_logo_data', logoData);
                    window.dispatchEvent(new CustomEvent('ss:sidebarLogoUpdated', { detail: { logoData } }));
                    setSidebarLogoPreview(logoData);
                    if (!silentToast) showToast('✅ Logo sidebar berhasil disimpan.', 'success');
                } else {
                    showAlert({ title: 'Gagal simpan logo', message: res.error || 'Terjadi kesalahan server.', type: 'danger' });
                }
            } catch (err) {
                showAlert({ title: 'Gagal simpan logo', message: err.message || 'Terjadi kesalahan.', type: 'danger' });
            }
        };

        window.resetSettingsBranding = async function () {
            document.getElementById('inSidebarLogoUrl').value = '';
            setSidebarLogoPreview('');
            window.dispatchEvent(new CustomEvent('ss:sidebarLogoUpdated', { detail: { logoData: '' } }));
            await window.saveSettingsBranding();
        };

        window.previewSidebarLogoFromUrl = function () {
            const logoUrl = document.getElementById('inSidebarLogoUrl').value.trim();
            if (!logoUrl) {
                const existingData = document.getElementById('inSidebarLogoData').value.trim();
                setSidebarLogoPreview(existingData);
                return;
            }
            if (!/^https?:\/\//i.test(logoUrl) && !/^data:image\//i.test(logoUrl)) return;
            setSidebarLogoPreview(logoUrl);
            window.dispatchEvent(new CustomEvent('ss:sidebarLogoUpdated', { detail: { logoData: logoUrl } }));
        };

        // Init load
        loadSettings();
        window.loadSettingsBranding();
    </script>
    <script type="module">
        import { renderSidebar, renderHeader } from './js/components/layout.js';
        import { guardAdmin } from './js/utils/role-guard.js';
        if (guardAdmin()) {
            renderSidebar('pengaturan');
            renderHeader();
        }
    </script>
</body>

</html>