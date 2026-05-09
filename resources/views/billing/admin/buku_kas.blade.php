<!DOCTYPE html>
<!DOCTYPE html>
<html lang="id">

<head>
    <script src="{{ asset('js/ss-storage-migrate.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buku Kas & Pembukuan - Sans Speed</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <script>
        if (localStorage.getItem('ss_theme') === 'light') {
            document.documentElement.classList.add('light-mode');
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Specific Dashboard Styles */
        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: #f8fafc;
            margin: 0 0 24px 0;
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

        /* Table Styles */
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
            color: #374151;
            text-transform: uppercase;
            border-bottom: 1px solid #e5e7eb;
        }

        td {
            padding: 16px;
            font-size: 14px;
            color: #374151;
            border-bottom: 1px solid #f3f4f6;
        }

        tr:last-child td {
            border-bottom: none;
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

        .action-btn {
            padding: 6px 10px;
            border-radius: 6px;
            background: #f3f4f6;
            color: #4b5563;
            transition: all 0.2s;
        }

        .action-btn:hover {
            background: #e5e7eb;
            color: #111827;
        }

        /* Filter Row Styles */
        .filter-row {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
            align-items: center;
            flex-wrap: wrap;
        }

        .form-select,
        .form-input {
            padding: 10px 15px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            color: #1e293b;
            background: white;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-select:focus,
        .form-input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Modal specific */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .modal.active {
            display: flex;
            opacity: 1;
        }

        .modal-content {
            background: white;
            border-radius: 16px;
            width: 90%;
            max-width: 500px;
            padding: 30px;
            transform: translateY(20px);
            transition: transform 0.3s;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .modal.active .modal-content {
            transform: translateY(0);
        }

        .card-title {
            color: #0f172a;
            font-weight: 800;
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
            .filter-row .form-input,
            .filter-row .btn-primary {
                width: 100% !important;
                margin-left: 0 !important;
            }

            .kas-filter-actions {
                width: 100%;
                display: grid;
                grid-template-columns: 1fr;
                gap: 10px;
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

            <!-- DASHBOARD CONTENT -->
            <div class="content-wrapper">
                <h1 class="page-title"><i class="fas fa-book" style="color:#0ea5e9;"></i> Laporan Kas Bulanan</h1>

                <!-- Fitur Filter -->
                <div class="filter-row">
                    <select id="filterBulan" class="form-select" onchange="loadKas()">
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

                    <select id="filterTahun" class="form-select" onchange="loadKas()">
                        <!-- Diisi dinamis oleh script -->
                    </select>

                    <div class="kas-filter-actions" style="display:flex; gap:10px; margin-left:auto;">
                        <button class="btn-primary"
                            style="padding: 10px 20px; font-size: 14px; background: #10b981;"
                            onclick="bukaModalKas()">
                            <i class="fas fa-plus-circle"></i> Catat Transaksi
                        </button>

                        <button class="btn-primary" style="padding: 10px 20px; font-size: 14px; background: #8b5cf6;"
                            onclick="window.exportExcel()">
                            <i class="fas fa-file-excel"></i> Export Laporan
                        </button>
                    </div>
                </div>

                <!-- Statistik Cards -->
                <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));">
                    <div class="card-3d">
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <div class="stat-icon stat-icon-green">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <div class="stat-info">
                                <h3 style="color: #34d399; font-size: 14px; font-weight: 800;">Total
                                    Pemasukan<br>(Tagihan + Manual)</h3>
                                <p id="totalMasuk">Rp 0</p>
                            </div>
                        </div>
                    </div>

                    <div class="card-3d" style="border: 1px solid rgba(248, 113, 113, 0.3);">
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <div class="stat-icon stat-icon-red">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <div class="stat-info">
                                <h3 style="color: #f87171; font-size: 14px; font-weight: 800;">Total
                                    Pengeluaran<br>(Bulan Ini)</h3>
                                <p id="totalKeluar">Rp 0</p>
                            </div>
                        </div>
                    </div>

                    <div class="card-3d"
                        style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border: none;">
                        <div style="display: flex; align-items: center; gap: 16px;">
                            <div class="stat-icon" style="background: rgba(14, 165, 233, 0.2); color: #38bdf8;">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <div class="stat-info">
                                <h3 style="color: #94a3b8; font-size: 14px; font-weight: 800;">Laba Bersih<br>(Net
                                    Income)</h3>
                                <p id="totalSaldo" style="color: var(--text-primary, white);">Rp 0</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grafik Chart Js -->
                <div class="card" style="margin-top: 20px;">
                    <h2 class="card-title" style="margin-bottom: 20px;"><i class="fas fa-chart-line"
                            style="color:#3b82f6;"></i> Grafik Arus Kas (6 Bulan Terakhir)</h2>
                    <div style="width: 100%; height: 300px; position: relative;">
                        <canvas id="keuanganChart"></canvas>
                    </div>
                </div>

                <!-- Table Section -->
                <div class="card" style="margin-top: 20px;">
                    <div class="card-header">
                        <h2 class="card-title">Riwayat Transaksi</h2>
                    </div>

                    <div class="table-scroll-mobile desktop-kas-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Deskripsi</th>
                                    <th>Kategori</th>
                                    <th>Jenis</th>
                                    <th>Nominal</th>
                                    <th>Pencatat</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="tableKasBody">
                                <tr>
                                    <td colspan="7" style="text-align: center; color: #94a3b8; padding: 30px;">
                                        Mencari riwayat pembukuan...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="mobileKasList" class="mobile-data-list">
                        <div class="mobile-data-empty"><i class="fas fa-spinner fa-spin"></i> Memuat riwayat transaksi...</div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <!-- Modal Form Transaksi -->
    <div id="transaksiModal" class="modal">
        <div class="modal-content">
            <h2 style="margin: 0 0 20px 0; font-size: 18px; color: #1e293b;">Catat Arus Kas Baru</h2>
            <form id="formKas">
                <div style="margin-bottom: 15px;">
                    <label
                        style="display: block; margin-bottom: 5px; font-size: 14px; font-weight: 600; color: #334155;">Jenis
                        Transaksi</label>
                    <select id="inputJenis" class="form-select" style="width: 100%;" required>
                        <option value="pemasukan">📥 Pemasukan (Uang Masuk)</option>
                        <option value="pengeluaran">📤 Pengeluaran (Uang Keluar)</option>
                    </select>
                </div>

                <div style="margin-bottom: 15px;">
                    <label
                        style="display: block; margin-bottom: 5px; font-size: 14px; font-weight: 600; color: #334155;">Nominal
                        (Rp)</label>
                    <input type="number" id="inputNominal" class="form-input" style="width: 100%;"
                        placeholder="Contoh: 150000" required>
                </div>

                <div style="margin-bottom: 15px;">
                    <label
                        style="display: block; margin-bottom: 5px; font-size: 14px; font-weight: 600; color: #334155;">Kategori</label>
                    <select id="inputKategori" class="form-select" style="width: 100%;" required>
                        <option value="Pembayaran Pelanggan">Pembayaran Pelanggan</option>
                        <option value="Pembelian Alat/Kabel">Pembelian Alat/Kabel</option>
                        <option value="Gaji Karyawan">Gaji Karyawan</option>
                        <option value="Operasional (Bensin/Makan)">Operasional (Bensin/Makan)</option>
                        <option value="Biaya Listrik/Sewa">Biaya Listrik/Sewa</option>
                        <option value="Lainnya">Lain-Lainnya</option>
                    </select>
                </div>

                <div style="margin-bottom: 25px;">
                    <label
                        style="display: block; margin-bottom: 5px; font-size: 14px; font-weight: 600; color: #334155;">Deskripsi
                        Tambahan</label>
                    <input type="text" id="inputDeskripsi" class="form-input" style="width: 100%;"
                        placeholder="Contoh: Beli kabel FO 1 Roll" required>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 10px;">
                    <button type="button" class="btn-primary" style="background: #e2e8f0; color: #475569;"
                        onclick="tutupModalKas()">Batal</button>
                    <button type="submit" class="btn-primary" style="background: #3b82f6;" id="btnSimpanKas">Simpan
                        Transaksi</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script Local API Auth -->
    <script type="module">
        import { auth, apiFetch, isAdminAppRole, hasPermission } from '{{ asset('api-config.js') }}';

        // Global Variable untuk Inisiasi UI Filter bawaan (Bulan Ini)
        const dateNow = new Date();
        document.getElementById('filterBulan').value = String(dateNow.getMonth() + 1).padStart(2, '0');
        document.getElementById('filterTahun').value = String(dateNow.getFullYear());

        let activeUserName = 'Sistem';
        let canViewFinanceTotals = false;

        // 1. Cek Sesi Authentication & Proteksi
        auth.onAuthStateChanged(async (user) => {
            if (user) {
                const profile = JSON.parse(localStorage.getItem('ss_user'));
                if (profile) {
                    if (!isAdminAppRole(profile)) {
                        alert("Akses Ditolak! Anda bukan Admin.");
                        window.location.replace("{{ url('/app-teknisi') }}");
                        return;
                    }
                    canViewFinanceTotals = hasPermission(profile, 'view_finance_totals');
                    if (!canViewFinanceTotals) {
                        alert("Role ini tidak punya akses total keuangan.");
                        window.location.replace("{{ url('/tagihan') }}");
                        return;
                    }
                    // Layout header already managed by layout.js
                    window.loadKas();
                }
            } else {
                window.location.replace("{{ url('/login') }}");
            }
        });

        // Fungsi Format Rupiah
        const formatRupiah = (angka) => {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
        };

        let chartKeuangan = null;

        function renderChart(history) {
            const ctx = document.getElementById('keuanganChart').getContext('2d');
            if (chartKeuangan) chartKeuangan.destroy();

            // Reversing the arrays because the API returns from oldest (index 5) to newest (index 0) originally
            // Wait, the API pushes from i=5 to i=0, so index 0 is oldest (6 months ago). That is chronological.
            chartKeuangan = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: history.labels,
                    datasets: [
                        {
                            label: 'Pemasukan Kas',
                            data: history.pemasukan,
                            backgroundColor: '#34d399',
                            borderRadius: 4
                        },
                        {
                            label: 'Pengeluaran Kas',
                            data: history.pengeluaran,
                            backgroundColor: '#f87171',
                            borderRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    let label = context.dataset.label || '';
                                    if (label) label += ': ';
                                    if (context.parsed.y !== null) {
                                        label += new Intl.NumberFormat('id-ID', {
                                            style: 'currency', currency: 'IDR', minimumFractionDigits: 0
                                        }).format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    if (value >= 1000000) return 'Rp ' + (value / 1000000) + ' Jt';
                                    if (value >= 1000) return 'Rp ' + (value / 1000) + ' Rb';
                                    return 'Rp ' + value;
                                }
                            }
                        }
                    }
                }
            });
        }

        // Init dropdown tahun & bulan secara dinamis
        (function initKasFilter() {
            const now = new Date();
            const curY = now.getFullYear();
            const curM = now.getMonth() + 1;

            // Bulan default ke bulan ini
            const selBulan = document.getElementById('filterBulan');
            if (selBulan) selBulan.value = String(curM).padStart(2, '0');

            // Tahun: dinamis 2 lalu s/d 3 ke depan
            const selTahun = document.getElementById('filterTahun');
            if (selTahun) {
                selTahun.innerHTML = '';
                for (let y = curY + 3; y >= curY - 2; y--) {
                    const opt = document.createElement('option');
                    opt.value = y; opt.textContent = y;
                    selTahun.appendChild(opt);
                }
                selTahun.value = curY;
            }
        })();

        let globalKasData = [];


        let _lastKasHash = '';

        function _showLive() {
            const el = document.getElementById('liveIndicator');
            if (!el) return;
            el.classList.add('show');
            setTimeout(() => el.classList.remove('show'), 1800);
        }

        // Fungsi Global Fetch/Load Kas -> Bisa dipanggil dari onclick tombol
        window.loadKas = async function (silent = false) {
            const tableBody = document.getElementById('tableKasBody');
            const mobileList = document.getElementById('mobileKasList');
            if (!silent) {
                tableBody.innerHTML = '<tr><td colspan="7" style="text-align: center;"><i class="fas fa-spinner fa-spin"></i> Memuat data...</td></tr>';
                if (mobileList) {
                    mobileList.innerHTML = '<div class="mobile-data-empty"><i class="fas fa-spinner fa-spin"></i> Memuat riwayat transaksi...</div>';
                }
            }

            const selectedMonth = parseInt(document.getElementById('filterBulan').value);
            const selectedYear = parseInt(document.getElementById('filterTahun').value);

            try {
                // 1. Muat Data Statistik untuk Kartu & Chart
                try {
                    const stats = await apiFetch(`/stats/keuangan?bulan=${selectedMonth}&tahun=${selectedYear}`);
                    if (stats && stats.success) {
                        document.getElementById('totalMasuk').innerText = formatRupiah(stats.current.pemasukan);
                        document.getElementById('totalKeluar').innerText = formatRupiah(stats.current.pengeluaran);
                        document.getElementById('totalSaldo').innerText = formatRupiah(stats.current.laba);

                        renderChart(stats.history);
                    }
                } catch (e) {
                    console.error("Gagal load statistik:", e);
                }

                // 2. Muat Riwayat Tabel Pembukuan Manual
                const allData = await apiFetch('/collections/pembukuan');
                const datanya = allData.filter(d => {
                    if (!d.tgl && !d.tanggal) return false;
                    const tgl = new Date(d.tgl || d.tanggal);
                    return tgl.getMonth() + 1 === selectedMonth && tgl.getFullYear() === selectedYear;
                });

                // Sort by date descending
                datanya.sort((a, b) => new Date(b.tgl || b.tanggal) - new Date(a.tgl || a.tanggal));
                globalKasData = datanya;

                let tableHtml = '';
                if (mobileList) mobileList.innerHTML = '';

                if (datanya.length === 0) {
                    tableBody.innerHTML = '<tr><td colspan="7" style="text-align: center; color:#475569; font-weight: 500;">Belum ada riwayat pencatatan manual di bulan ini. (Data Tagihan tampil di tabel tersendiri)</td></tr>';
                    if (mobileList) {
                        mobileList.innerHTML = '<div class="mobile-data-empty">Belum ada riwayat transaksi pada bulan ini.</div>';
                    }
                    return;
                }

                datanya.forEach((data) => {
                    const docId = data.id;
                    const tglField = data.tgl || data.tanggal;
                    const isMasuk = data.jenis === 'pemasukan';
                    const tanggalLengkap = new Date(tglField).toLocaleString('id-ID', {
                        day: 'numeric', month: 'short',
                        year: 'numeric', hour: '2-digit', minute: '2-digit'
                    });

                    // Row UI Gen
                    tableHtml += `
                <tr style="background: ${isMasuk ? 'rgba(16, 185, 129, 0.03)' : 'rgba(244, 63, 94, 0.03)'};">
                    <td style="font-weight: 500;">${tanggalLengkap}</td>
                    <td style="font-weight: 600; color: #1e293b;">${data.deskripsi || data.keterangan || '-'}</td>
                    <td><span class="badge" style="background:#e2e8f0; color:#475569;">${data.kategori || '-'}</span>
                    </td>
                    <td><span class="badge ${isMasuk ? 'badge-success' : 'badge-danger'}">
                            <i class="fas ${isMasuk ? 'fa-arrow-down' : 'fa-arrow-up'}"></i> ${isMasuk ? 'UM' : 'UK'}
                        </span></td>
                    <td style="font-weight: 700; color: ${isMasuk ? '#10b981' : '#ef4444'};">
                        ${isMasuk ? '+' : '-'}${formatRupiah(data.nominal)}
                    </td>
                    <td style="font-size: 12px;"><i class="fas fa-user-edit" style="color:#cbd5e1;"></i>
                        ${data.createdBy || '-'}</td>
                    <td>
                        <button class="action-btn btn-del-kas" title="Hapus Info" data-id="${docId}"
                            onclick="window.hapusKas(this.dataset.id)"
                            style="color: #ef4444; background: rgba(239, 68, 68, 0.1);">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
                `;

                    if (mobileList) {
                        mobileList.innerHTML += `
                            <article class="mobile-data-card">
                                <div class="mobile-data-head">
                                    <div>
                                        <div class="mobile-data-title" style="font-size:14px;">${data.deskripsi || data.keterangan || '-'}</div>
                                        <div class="mobile-data-sub">${tanggalLengkap}</div>
                                    </div>
                                    <div class="mobile-data-value" style="font-size:16px; color:${isMasuk ? '#10b981' : '#ef4444'};">
                                        ${isMasuk ? '+' : '-'}${formatRupiah(data.nominal)}
                                    </div>
                                </div>
                                <div class="mobile-data-info">Kategori: ${data.kategori || '-'} • Pencatat: ${data.createdBy || '-'}</div>
                                <div class="mobile-data-actions">
                                    <span class="badge ${isMasuk ? 'badge-success' : 'badge-danger'}"><i class="fas ${isMasuk ? 'fa-arrow-down' : 'fa-arrow-up'}"></i> ${isMasuk ? 'Pemasukan' : 'Pengeluaran'}</span>
                                    <button class="action-btn" onclick="window.hapusKas('${docId}')" style="color:#ef4444; background: rgba(239, 68, 68, 0.1);">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </div>
                            </article>
                        `;
                    }
                });

                const newKasHash = JSON.stringify(datanya.map(d => d.id + d.nominal + d.jenis));
                if (silent && newKasHash === _lastKasHash) return; // Data sama, skip update
                _lastKasHash = newKasHash;

                // Smooth DOM swap
                tableBody.classList.add('updating');
                await new Promise(r => setTimeout(r, 200));
                tableBody.innerHTML = tableHtml;
                tableBody.classList.remove('updating');
                if (mobileList && !mobileList.innerHTML.trim()) {
                    mobileList.innerHTML = '<div class="mobile-data-empty">Belum ada data untuk ditampilkan.</div>';
                }
                if (silent) _showLive();

            } catch (error) {
                console.error("Gagal muat kas", error);
                tableBody.innerHTML = `<tr>
                    <td colspan="7" style="color: red; text-align: center;">Gagal memuat data. Cek koneksi server.
                        (${error.message})</td>
                </tr>`;
                if (mobileList) {
                    mobileList.innerHTML = '<div class="mobile-data-empty" style="color:#ef4444;">Gagal memuat riwayat transaksi.</div>';
                }
            }
        };

        // Fungsi Download Laporan CSV
        window.exportExcel = function () {
            if (globalKasData.length === 0) return showAlert({ title: 'Export Gagal', message: 'Data kosong. Tidak ada yang bisa di-export.', type: 'warning' });

            let csv = "Tanggal,Deskripsi,Kategori,Jenis,Nominal(Rp),Pencatat\n";

            globalKasData.forEach(d => {
                const tgl = new Date(d.tgl || d.tanggal).toLocaleString('id-ID');
                const deskripsi = (d.deskripsi || d.keterangan || '').replace(/,/g, ' '); // hindari koma pemisah csv
                const kategori = (d.kategori || '').replace(/,/g, ' ');
                csv += `"${tgl}","${deskripsi}","${kategori}","${d.jenis}","${d.nominal}","${d.createdBy}"\n`;
            });

            // Trigger download file
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement("a");
            if (link.download !== undefined) {
                const url = URL.createObjectURL(blob);
                link.setAttribute("href", url);
                link.setAttribute("download",
                    `Laporan_Buku_Kas_SansSpeed_${document.getElementById('filterBulan').value}_${document.getElementById('filterTahun').value}.csv`);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        };

        // Fungsi Global Insert
        document.getElementById('formKas').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btnSimpanKas');
            const original = btn.innerHTML;

            const jenis = document.getElementById('inputJenis').value;
            const nominal = parseFloat(document.getElementById('inputNominal').value) || 0;
            const kategori = document.getElementById('inputKategori').value;
            const desk = (document.getElementById('inputDeskripsi').value || '').trim();

            // Validasi
            let valid = true;
            ['errNominalKas', 'errDeskripsiKas'].forEach(id => { const el = document.getElementById(id); if (el) el.remove(); });
            const nominalEl = document.getElementById('inputNominal');
            const deskEl = document.getElementById('inputDeskripsi');
            nominalEl.style.borderColor = ''; deskEl.style.borderColor = '';

            if (!nominal || nominal <= 0) {
                nominalEl.style.borderColor = '#ef4444';
                nominalEl.style.boxShadow = '0 0 0 2px rgba(239,68,68,0.25)';
                const e2 = document.createElement('div');
                e2.id = 'errNominalKas';
                e2.style.cssText = 'color:#ef4444;font-size:11px;margin-top:4px;';
                e2.innerHTML = '<i class="fas fa-exclamation-circle"></i> Nominal harus lebih dari 0';
                nominalEl.insertAdjacentElement('afterend', e2);
                valid = false;
            }
            if (!desk) {
                deskEl.style.borderColor = '#ef4444';
                deskEl.style.boxShadow = '0 0 0 2px rgba(239,68,68,0.25)';
                const e3 = document.createElement('div');
                e3.id = 'errDeskripsiKas';
                e3.style.cssText = 'color:#ef4444;font-size:11px;margin-top:4px;';
                e3.innerHTML = '<i class="fas fa-exclamation-circle"></i> Deskripsi / keterangan wajib diisi';
                deskEl.insertAdjacentElement('afterend', e3);
                valid = false;
            }
            if (!valid) return;

            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            btn.disabled = true;

            try {
                await apiFetch('/collections/pembukuan', {
                    method: 'POST',
                    body: JSON.stringify({
                        jenis: jenis,
                        nominal: nominal,
                        kategori: kategori,
                        keterangan: desk,
                        tanggal: new Date().toISOString(),
                        createdBy: JSON.parse(localStorage.getItem('ss_user') || '{}').nama || 'Sistem'
                    })
                });

                window.tutupModalKas();
                document.getElementById('formKas').reset();
                window.loadKas();
            } catch (error) {
                console.error(error);
                const t = document.createElement('div');
                t.style.cssText = 'position:fixed;bottom:24px;right:24px;z-index:9999;background:#ef4444;color:white;padding:12px 18px;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,0.3);font-size:14px;';
                t.innerHTML = '<i class="fas fa-times-circle"></i> Gagal mencatat: ' + error.message;
                document.body.appendChild(t);
                setTimeout(() => t.remove(), 4000);
            } finally {
                btn.innerHTML = original;
                btn.disabled = false;
            }
        });


        // Delete Handler
        window.hapusKas = async function (docId) {
            const confirmed = await showConfirm({
                title: '⚠️ Konfirmasi Hapus',
                message: 'Yakin ingin menghapus catatan pembukuan ini secara <strong>permanen</strong>?',
                type: 'danger',
                confirmText: 'Ya, Hapus'
            });
            if (confirmed) {
                try {
                    await apiFetch(`/collections/pembukuan/${docId}`, { method: 'DELETE' });
                    showToast('✅ Catatan pembukuan berhasil dihapus.', 'success');
                    window.loadKas();
                } catch (err) {
                    console.error(err);
                    showAlert({ title: 'Gagal Menghapus', message: err.message, type: 'danger' });
                }
            }
        }
    </script>

    <script>
        // Modal UI Func
        const mdKas = document.getElementById('transaksiModal');
        window.bukaModalKas = function () {
            mdKas.classList.add('active');
        }
        window.tutupModalKas = function () {
            mdKas.classList.remove('active');
        }

        // Auto-refresh polling setiap 45 detik (realtime update)
        let _kasPollInterval = setInterval(() => {
            if (!document.hidden && typeof window.loadKas === 'function') window.loadKas(true);
        }, 45000);
        window.addEventListener('pagehide', () => clearInterval(_kasPollInterval));
    </script>
    <script type="module">
        import { renderSidebar, renderHeader } from './js/components/layout.js';
        import { guardAdmin } from './js/utils/role-guard.js';
        import { showConfirm, showAlert, showToast } from './js/utils/dialog.js';
        window.showConfirm = showConfirm;
        window.showAlert = showAlert;
        window.showToast = showToast;
        if (guardAdmin()) {
            renderSidebar('buku-kas');
            renderHeader();
        }
    </script>
</body>

</html>