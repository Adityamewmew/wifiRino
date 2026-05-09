<!DOCTYPE html>
<html lang="id">

<head>
    <script src="{{ asset('js/ss-storage-migrate.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Teknisi - Sans Speed</title>
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
            margin: 0 0 24px 0;
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
            background: #fef08a;
            color: #ca8a04;
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

        .area-badge {
            display: inline-block;
            background: #e0f2fe;
            color: #0284c7;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 16px;
        }

        .pelanggan-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .pelanggan-card {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.2s;
        }

        .pelanggan-card:hover {
            border-color: #0284c7;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .pelanggan-info h4 {
            margin: 0 0 4px 0;
            font-size: 16px;
            color: #111827;
        }

        .pelanggan-info p {
            margin: 0;
            font-size: 13px;
            color: #6b7280;
        }

        .pelanggan-action {
            text-align: right;
        }

        .tagihan-amount {
            font-size: 18px;
            font-weight: 700;
            color: #ef4444;
            margin-bottom: 8px;
        }

        .btn-pay {
            background: #10b981;
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s;
        }

        .btn-pay:hover {
            background: #059669;
            transform: translateY(-2px);
        }

        @media (max-width: 640px) {
            .pelanggan-card {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }

            .pelanggan-action {
                text-align: left;
                width: 100%;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .btn-pay {
                width: auto;
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
                <h1 class="page-title">Penagihan Agen Wilayah Anda</h1>

                <div class="area-badge">
                    <i class="fas fa-map-marker-alt"></i> Area Aktif: Perumahan Griya Asri
                </div>

                <!-- Input Pencarian -->
                <div style="margin-bottom: 24px; position: relative;">
                    <i class="fas fa-search" style="position: absolute; left: 16px; top: 12px; color: #9ca3af;"></i>
                    <input type="text" placeholder="Cari nama pelanggan atau ID..."
                        style="width: 100%; max-width: 400px; padding: 12px 16px 12px 42px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                </div>

                <!-- List Pelanggan Menunggak di Wilayahnya -->
                <div class="card">
                    <h2 style="font-size: 16px; margin: 0 0 16px 0;">Daftar Pelanggan Belum Bayar</h2>

                    <div class="pelanggan-list">
                        <!-- Card 1 -->
                        <div class="pelanggan-card">
                            <div class="pelanggan-info">
                                <h4>Budi Santoso (BTC-0081)</h4>
                                <p><i class="fas fa-map-pin"></i> Blok C2 No. 12 - Griya Asri</p>
                                <p style="margin-top: 4px; color: #0284c7;"><i class="fas fa-wifi"></i> Paket 20 Mbps
                                </p>
                            </div>
                            <div class="pelanggan-action">
                                <div class="tagihan-amount">Rp 200.000</div>
                                <button class="btn-pay" onclick="konfirmasiBayar('Budi Santoso', 200000)">
                                    <i class="fas fa-check-circle"></i> Terima Uang
                                </button>
                            </div>
                        </div>

                        <!-- Card 2 -->
                        <div class="pelanggan-card">
                            <div class="pelanggan-info">
                                <h4>Siti Aminah (BTC-0094)</h4>
                                <p><i class="fas fa-map-pin"></i> Blok A1 No. 5 - Griya Asri</p>
                                <p style="margin-top: 4px; color: #0284c7;"><i class="fas fa-wifi"></i> Paket 10 Mbps
                                </p>
                            </div>
                            <div class="pelanggan-action">
                                <div class="tagihan-amount" style="color: #f59e0b;">Rp 150.000</div>
                                <button class="btn-pay" onclick="konfirmasiBayar('Siti Aminah', 150000)">
                                    <i class="fas fa-check-circle"></i> Terima Uang
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script>
        function konfirmasiBayar(nama, nominal) {
            const formatRupiah = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(nominal);
            if (confirm(`Konfirmasi penerimaan uang dari ${nama} sebesar ${formatRupiah}?`)) {
                alert(`Pembayaran ${nama} berhasil dikonfirmasi. Saldo setoran Anda bertambah.`);
            }
        }
    </script>
    <script type="module">
        import { renderSidebar, renderHeader } from './js/components/layout.js';
        renderSidebar('teknisi-dashboard');
        renderHeader();
    </script>
</body>

</html>