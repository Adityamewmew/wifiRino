<!DOCTYPE html>
<html lang="id">

<head>
    <script src="{{ asset('js/ss-storage-migrate.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembukuan Agen - Sans Speed</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
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
            color: #f8fafc;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Stats Cards */
        .emp-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .emp-stat-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 24px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .emp-icon-box {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .emp-info h4 {
            margin: 0 0 6px 0;
            color: #94a3b8;
            font-size: 14px;
            font-weight: 600;
        }

        .emp-info .value {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
            color: white;
            letter-spacing: 0.5px;
        }

        /* Filter Section */
        .filter-section {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 24px;
        }

        .filter-left {
            display: flex;
            gap: 16px;
            align-items: center;
            flex: 1;
        }

        .search-input {
            width: 100%;
            padding: 14px 16px 14px 45px;
            background: rgba(15, 23, 42, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            color: white;
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
            border-color: rgba(96, 165, 250, 0.5);
            background: rgba(15, 23, 42, 0.6);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .filter-select {
            padding: 14px 16px;
            background: rgba(15, 23, 42, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            color: white;
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            cursor: pointer;
            min-width: 180px;
            transition: all 0.3s;
            appearance: none;
            -webkit-appearance: none;
            background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23ffffff%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E");
            background-repeat: no-repeat;
            background-position: right 16px top 50%;
            background-size: 10px auto;
            padding-right: 40px;
        }

        .filter-select:hover,
        .filter-select:focus {
            outline: none;
            border-color: rgba(96, 165, 250, 0.5);
            background-color: rgba(15, 23, 42, 0.6);
        }

        .filter-select option {
            background: #0f172a;
            color: white;
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
            max-width: 500px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            transform: scale(0.95) translateY(20px);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
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
            color: white;
        }

        .form-group {
            margin-bottom: 20px;
            padding: 0 24px;
        }

        .form-group:first-child {
            margin-top: 24px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #cbd5e1;
            letter-spacing: 0.5px;
        }

        .percentage-input {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .percentage-input span {
            font-size: 20px;
            font-weight: 700;
            color: #34d399;
        }

        /* Progress Bar for Sisa Uang */
        .progress-bar {
            width: 100%;
            height: 6px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            margin-top: 8px;
            overflow: hidden;
            position: relative;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #ef4444, #f59e0b);
            border-radius: 4px;
        }

        .table-scroll-mobile {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 6px;
        }

        .table-scroll-mobile table {
            min-width: 980px;
        }

        @media (max-width: 768px) {
            .filter-section {
                flex-direction: column;
                align-items: stretch;
            }

            .filter-left,
            .filter-select,
            .filter-section .btn-primary {
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
                        <i class="fas fa-book-open"
                            style="color: #34d399; text-shadow: 0 0 15px rgba(52, 211, 153, 0.4);"></i>
                        Pembukuan Agen (Sisa Saldo)
                    </h1>
                </div>

                <!-- Stats Overview (Global Total) -->
                <div class="emp-stats-grid">
                    <div class="emp-stat-card">
                        <div class="emp-icon-box" style="background: rgba(59, 130, 246, 0.1); color: #60a5fa;">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div class="emp-info">
                            <h4>Total Tagihan Terekap</h4>
                            <p class="value" id="totalGlobalRekap">Rp 12.850.000</p>
                        </div>
                    </div>
                    <div class="emp-stat-card">
                        <div class="emp-icon-box" style="background: rgba(244, 63, 94, 0.1); color: #fb7185;">
                            <i class="fas fa-wallet"></i>
                        </div>
                        <div class="emp-info">
                            <h4>Uang Di Tangan (Belum Disetor)</h4>
                            <p class="value" style="color: #fb7185;" id="totalGlobalDiTangan">Rp 4.250.000</p>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filter-section">
                    <div class="filter-left">
                        <select class="filter-select" id="periodeFilter" style="min-width: 200px;">
                            <option value="current">Periode: Bulan Ini</option>
                            <option value="last">Bulan Lalu</option>
                        </select>
                    </div>
                    <button class="btn-primary" style="background: linear-gradient(135deg, #8b5cf6, #6366f1);"
                        onclick="window.exportExcel()">
                        <i class="fas fa-file-excel"></i> Export Laporan
                    </button>
                </div>

                <!-- Table Section -->
                <div class="card-3d" style="padding: 20px;">
                    <div class="table-scroll-mobile desktop-agen-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Admin / Agen</th>
                                    <th>Total & Jml Pelanggan</th>
                                    <th>Setor (Ke Pusat)</th>
                                    <th>Sisa (Di Tangan)</th>
                                    <th>Pengeluaran</th>
                                    <th style="text-align: right;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="listPembukuan">
                                <tr>
                                    <td colspan="6"
                                        style="text-align: center; padding: 40px; color: var(--text-secondary);">
                                        <i class="fas fa-circle-notch fa-spin fa-2x"
                                            style="margin-bottom: 12px; color: #cbd5e1;"></i><br>
                                        Memuat data pembukuan...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="mobilePembukuanList" class="mobile-data-list">
                        <div class="mobile-data-empty"><i class="fas fa-spinner fa-spin"></i> Memuat data pembukuan...</div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- JS Modul ditaruh di bawah -->
    <script type="module">
        import { renderSidebar, renderHeader } from './js/components/layout.js';
        import { apiFetch, auth, isAdminAppRole, hasPermission, resolveRoleKey } from '{{ asset('api-config.js') }}';

        let currentProfile = null;
        const formatRp = (num) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(num || 0);
        const norm = (v) => String(v || '').trim().toLowerCase();
        const toNum = (v) => {
            const n = Number.parseFloat(v);
            return Number.isFinite(n) ? n : 0;
        };

        const getSelectedPeriod = () => {
            const mode = document.getElementById('periodeFilter')?.value || 'current';
            const now = new Date();
            if (mode === 'last') {
                now.setMonth(now.getMonth() - 1);
            }
            return { bulan: now.getMonth() + 1, tahun: now.getFullYear(), mode };
        };

        async function loadDataPembukuan() {
            const tbody = document.getElementById('listPembukuan');
            const mobileList = document.getElementById('mobilePembukuanList');
            try {
                const period = getSelectedPeriod();
                const summaryRes = await apiFetch(`/pembukuan/agen-summary?bulan=${period.bulan}&tahun=${period.tahun}`);
                const rows = Array.isArray(summaryRes?.data) ? summaryRes.data : [];

                const isAdminView = hasPermission(currentProfile, 'view_finance_totals');
                if (rows.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="6" style="text-align: center; padding: 30px; color: var(--text-secondary);">Belum ada data agen.</td></tr>`;
                    if (mobileList) {
                        mobileList.innerHTML = '<div class="mobile-data-empty">Belum ada data agen.</div>';
                    }
                    document.getElementById('totalGlobalRekap').textContent = 'Rp 0';
                    document.getElementById('totalGlobalDiTangan').textContent = 'Rp 0';
                    return;
                }
                tbody.innerHTML = '';
                if (mobileList) mobileList.innerHTML = '';

                const safeRows = rows.map((r) => ({
                    namaPenagih: String(r?.namaPenagih || '-').trim() || '-',
                    totalTagihan: toNum(r?.totalTagihan),
                    jumlahPelanggan: Math.max(0, Math.floor(toNum(r?.jumlahPelanggan))),
                    setor: toNum(r?.setor),
                    sisaDiTangan: toNum(r?.sisaDiTangan),
                    pengeluaran: toNum(r?.pengeluaran),
                    feePerPelanggan: 0,
                    totalFee: 0
                }));

                safeRows.forEach(admin => {
                    const progressPercent = admin.totalTagihan > 0 ? ((admin.setor / admin.totalTagihan) * 100).toFixed(0) : 0;
                    const namaPenagihEsc = admin.namaPenagih.replace(/'/g, "\\'");
                    const actionButtons = isAdminView
                        ? `
                            <button class="action-btn" title="Tarik Setoran Uang" style="color: #10b981;" onclick="bukaTarikSetoran('${namaPenagihEsc}', ${admin.sisaDiTangan})"><i class="fas fa-hand-holding-usd"></i></button>
                        `
                        : `<span style="color: var(--text-secondary); font-size: 12px;">-</span>`;

                    const row = `
                        <tr>
                            <td>
                                <div style="font-weight: 700; color: var(--text-primary); font-size: 15px;">${admin.namaPenagih}</div>
                                <div style="font-size: 12px; color: var(--text-secondary); margin-top: 4px;">Agen Aktif</div>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: #34d399; font-size: 15px;">${formatRp(admin.totalTagihan)}</div>
                                <div style="font-size: 11px; color: var(--text-secondary); margin-top: 2px;">(Dari ${admin.jumlahPelanggan} Pelanggan)</div>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: var(--text-secondary);">${formatRp(admin.setor)}</div>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: #ef4444; font-size: 15px;">${formatRp(admin.sisaDiTangan)}</div>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: ${progressPercent}%;"></div>
                                </div>
                            </td>
                            <td>
                                <div style="color: var(--text-secondary);">${formatRp(admin.pengeluaran)}</div>
                            </td>
                            <td style="text-align: right;">
                                ${actionButtons}
                            </td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);
                    if (mobileList) {
                        mobileList.innerHTML += `
                            <article class="mobile-data-card">
                                <div class="mobile-data-head">
                                    <div>
                                        <div class="mobile-data-title">${admin.namaPenagih}</div>
                                        <div class="mobile-data-sub">${admin.jumlahPelanggan} pelanggan</div>
                                    </div>
                                    <div class="mobile-data-value" style="font-size:15px; color:#34d399;">${formatRp(admin.totalTagihan)}</div>
                                </div>
                                <div class="mobile-data-info">
                                    <div>Setor: ${formatRp(admin.setor)}</div>
                                    <div>Sisa di tangan: <span style="color:#ef4444; font-weight:700;">${formatRp(admin.sisaDiTangan)}</span></div>
                                    <div>Pengeluaran: ${formatRp(admin.pengeluaran)}</div>
                                </div>
                                <div class="mobile-data-actions">
                                    ${isAdminView
                                ? `<button class="action-btn" onclick="bukaTarikSetoran('${namaPenagihEsc}', ${admin.sisaDiTangan})" style="color:#10b981;"><i class="fas fa-hand-holding-usd"></i> Tarik Setoran</button>`
                                : `<span style="color: var(--text-secondary); font-size: 12px;">Tanpa aksi</span>`}
                                </div>
                            </article>
                        `;
                    }
                });

                const totalsFromRows = safeRows.reduce((acc, row) => {
                    acc.totalTagihan += row.totalTagihan;
                    acc.totalSisa += row.sisaDiTangan;
                    return acc;
                }, { totalTagihan: 0, totalSisa: 0 });

                document.getElementById('totalGlobalRekap').textContent = formatRp(totalsFromRows.totalTagihan);
                document.getElementById('totalGlobalDiTangan').textContent = formatRp(totalsFromRows.totalSisa);

            } catch (err) {
                console.error(err);
                document.getElementById('totalGlobalRekap').textContent = 'Rp 0';
                document.getElementById('totalGlobalDiTangan').textContent = 'Rp 0';
                tbody.innerHTML = `<tr><td colspan="6" style="color:red; text-align:center;">Gagal memuat data pembukuan. ${err?.message ? `(${err.message})` : ''}</td></tr>`;
                if (mobileList) {
                    mobileList.innerHTML = `<div class="mobile-data-empty" style="color:#ef4444;">Gagal memuat data pembukuan. ${err?.message ? `(${err.message})` : ''}</div>`;
                }
            }
        }

        async function initPage() {
            renderSidebar('pembukuan-kang-tagih');
            renderHeader();
            const periodeFilter = document.getElementById('periodeFilter');
            if (periodeFilter) {
                periodeFilter.addEventListener('change', loadDataPembukuan);
            }
            await loadDataPembukuan();
        }

        auth.onAuthStateChanged(async (user) => {
            if (user) {
                const profile = JSON.parse(localStorage.getItem('ss_user'));
                if (profile) {
                    currentProfile = profile;
                    const role = norm(resolveRoleKey(profile.roleKey || profile.role));
                    if (['penagih', 'tekpen', 'teknisipenagih'].includes(role)) {
                        window.location.replace("{{ url('/pembukuan-saya') }}");
                        return;
                    }
                    if (!isAdminAppRole(profile)) {
                        window.location.replace("{{ url('/app-teknisi') }}");
                        return;
                    }
                    if (!hasPermission(profile, 'view_finance_totals')) {
                        window.location.replace("{{ url('/tagihan') }}");
                        return;
                    }
                    await initPage();
                }
            } else {
                window.location.replace("{{ url('/login') }}");
            }
        });

        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        function playClickSound() {
            if (audioCtx.state === 'suspended') audioCtx.resume();
            const osc = audioCtx.createOscillator();
            const gainNode = audioCtx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(800, audioCtx.currentTime);
            gainNode.gain.setValueAtTime(0.05, audioCtx.currentTime);
            gainNode.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.1);
            osc.connect(gainNode);
            gainNode.connect(audioCtx.destination);
            osc.start();
            osc.stop(audioCtx.currentTime + 0.1);
        }

        window.bukaTarikSetoran = async function (nama, sisa) {
            playClickSound();
            const formatSisa = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(sisa);

            // Custom prompt built inside sweetalert-like logic doesn't exist natively, 
            // so we'll use native prompt for data entry but custom dialog for confirmation
            const nominalStr = prompt(`💵 Setoran dari [${nama}]\nSisa di tangan saat ini: ${formatSisa}\n\nMasukkan nominal uang yang disetorkan (Tanpa titik/koma):`, sisa);

            if (!nominalStr) return;
            const nominal = parseFloat(nominalStr);
            if (isNaN(nominal) || nominal <= 0) {
                showAlert({ title: 'Gagal', message: 'Nominal tidak valid.', type: 'danger' });
                return;
            }
            if (nominal > sisa) {
                showAlert({ title: 'Overlimit', message: 'Nominal setoran melebih jumlah saldo di tangan agen.', type: 'danger' });
                return;
            }

            try {
                // Catat ke Buku Kas Induk
                await apiFetch('/collections/pembukuan', {
                    method: 'POST',
                    body: JSON.stringify({
                        jenis: 'pemasukan',
                        nominal: nominal,
                        kategori: 'Setoran Tunai Agen',
                        keterangan: `Tarik setoran tunai dari agen ${nama}`,
                        tanggal: new Date().toISOString(),
                        createdBy: JSON.parse(localStorage.getItem('ss_user') || '{}').nama || 'Admin Pusat'
                    })
                });

                showAlert({ title: 'Berhasil Ditarik!', message: `Uang Setoran <strong>Rp ${nominal.toLocaleString('id-ID')}</strong> berhasil masuk ke Buku Kas Pusat.`, type: 'success' });
                loadDataPembukuan();

            } catch (e) {
                console.error(e);
                showAlert({ title: 'Error Sistem', message: e.message, type: 'danger' });
            }
        };

        window.exportExcel = function () {
            const table = document.querySelector('table');
            let csv = [];
            const rows = table.querySelectorAll('tr');

            for (let i = 0; i < rows.length; i++) {
                let row = [], cols = rows[i].querySelectorAll('td, th');
                // Skip the final action column if it's there
                for (let j = 0; j < cols.length - 1; j++) {
                    let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, '').replace(/(\s\s)/gm, ' ');
                    data = data.replace(/"/g, '""');
                    row.push('"' + data + '"');
                }
                csv.push(row.join(','));
            }

            const csvFile = new Blob([new Uint8Array([0xEF, 0xBB, 0xBF]), csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
            const p = (document.getElementById('periodeFilter')?.value === 'last') ? 'Bulan-Lalu' : 'Bulan-Ini';
            const downloadLink = document.createElement('a');
            downloadLink.download = `Laporan_Agen_${p}.csv`;
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = 'none';
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        };
    </script>
</body>

</html>