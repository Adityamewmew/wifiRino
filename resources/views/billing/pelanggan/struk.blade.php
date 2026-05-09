<!DOCTYPE html>
<html lang="id">

<head>
    <script src="{{ asset('js/ss-storage-migrate.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Struk Sans Speed</title>
    <style>
        /* Gaya khusus untuk struk thermal 58mm */
        @media print {
            @page {
                margin: 0;
                size: 58mm 120mm;
                /* Sesuaikan atau biarkan auto */
            }

            body {
                margin: 0;
                padding: 0;
            }
        }

        body {
            background: #fff;
            color: #000;
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            width: 58mm;
            margin: 0 auto;
            padding: 5px;
            box-sizing: border-box;
            text-align: center;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p {
            margin: 0;
            padding: 0;
        }

        .header {
            margin-bottom: 10px;
        }

        .header h2 {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .header p {
            font-size: 11px;
            line-height: 1.2;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
        }

        .info-row span {
            font-size: 11px;
        }

        .info-left {
            text-align: left;
        }

        .info-right {
            text-align: right;
            font-weight: bold;
        }

        .table-items {
            width: 100%;
            text-align: left;
            margin: 10px 0;
            border-collapse: collapse;
        }

        .table-items th,
        .table-items td {
            font-size: 11px;
            padding: 2px 0;
        }

        .total-row {
            font-size: 14px;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            margin: 10px 0;
        }

        .footer {
            margin-top: 20px;
            margin-bottom: 20px;
            font-size: 11px;
            line-height: 1.3;
        }

        .stamp {
            border: 2px solid #000;
            display: inline-block;
            padding: 2px 10px;
            font-weight: bold;
            font-size: 14px;
            transform: rotate(-10deg);
            margin: 10px 0;
            border-radius: 4px;
        }

        /* Jangan tampilkan elemen UI di mode cetak */
        .no-print {
            margin: 20px 0;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        @media print {
            .no-print {
                display: none !important;
            }
        }

        button {
            padding: 10px;
            font-size: 14px;
            cursor: pointer;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
        }
    </style>
</head>

<body>
@include('billing.partials.web-bootstrap')


    <div id="receipt-content">
        <div class="header">
            <h2 id="printHeader">Sans Speed INTERNET</h2>
            <p id="printSub">Ds. Sukamaju Kec. Jaya</p>
        </div>

        <div class="divider"></div>

        <div class="info-row">
            <span class="info-left">No. TRX:</span>
            <span class="info-right" id="printRef">#123456</span>
        </div>
        <div class="info-row">
            <span class="info-left">Tanggal:</span>
            <span class="info-right" id="printTgl">01 Jan 2026</span>
        </div>
        <div class="info-row">
            <span class="info-left">Kasir:</span>
            <span class="info-right" id="printKasir">Admin</span>
        </div>

        <div class="divider"></div>

        <div class="info-row" style="margin-top: 5px;">
            <span class="info-left">Pelanggan:</span>
            <span class="info-right" id="printNama">Budi Santoso</span>
        </div>
        <div class="info-row">
            <span class="info-left">ID Pel:</span>
            <span class="info-right" id="printIdPel">C-001</span>
        </div>
        <div class="info-row">
            <span class="info-left">Area:</span>
            <span class="info-right" id="printArea">Griya Asri</span>
        </div>

        <div class="divider"></div>

        <div style="text-align: left; font-size: 11px; font-weight: bold; margin-bottom: 5px;">Deskripsi Tagihan:</div>
        <div class="info-row">
            <span class="info-left" id="printDeskripsi" style="max-width: 60%;">Tagihan Internet Bulan Jan/2026 (Paket
                20Mbps)</span>
            <span class="info-right" id="printNominalItem">Rp 150.000</span>
        </div>

        <div class="divider" style="border-top-style: solid;"></div>

        <div class="total-row">
            <span>TOTAL:</span>
            <span id="printTotal">Rp 150.000</span>
        </div>

        <div class="info-row">
            <span class="info-left">Status:</span>
            <span class="info-right" style="text-transform: uppercase;" id="printStatus">LUNAS</span>
        </div>

        <div class="divider"></div>

        <div class="footer">
            <p id="printFooter" style="text-align: center;">Terima kasih. Internet Lancar, Rezeki Lancar!</p>
        </div>

        <div><svg id="barcode"></svg></div>
    </div>

    <div class="no-print">
        <button onclick="window.print()">🖨️ Cetak Struk Ulang</button>
        <button onclick="window.close()" style="background: #ef4444;">❌ Tutup Halaman</button>
    </div>

    <!-- JsBarcode JS untuk membuat barcode simple ID (opsional) -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>

    <script type="module">
        import { auth, apiFetch } from '{{ asset('api-config.js') }}';

        // Load Setting Printer dari localStorage
        const loadSettings = () => {
            const printSet = JSON.parse(localStorage.getItem('ss_printer_config') || '{}');
            document.getElementById('printHeader').innerText = printSet.header || 'Sans Speed INTERNET';
            document.getElementById('printSub').innerText = printSet.sub || 'Layanan Internet Desa Terbaik';
            document.getElementById('printFooter').innerText = printSet.footer || 'Terima kasih atas pembayaran Anda.';
        };

        const formatRupiah = (angka) => {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);
        };

        async function renderData() {
            // Cek Auth
            auth.onAuthStateChanged(async (user) => {
                if (!user) {
                    alert("Akses ditolak. Silakan login terlebih dahulu.");
                    window.close();
                    return;
                }

                const profile = JSON.parse(localStorage.getItem('ss_user'));
                if (profile) {
                    document.getElementById('printKasir').innerText = profile.nama;
                }

                loadSettings();

                // Dapatkan ID dari URL params
                const params = new URLSearchParams(window.location.search);
                const tagihanId = params.get('id');

                if (!tagihanId) {
                    document.body.innerHTML = '<h3>Error: ID Tagihan Tidak Ditemukan!</h3>';
                    return;
                }

                try {
                    // Fetch tagihan_bulanan document
                    const tagihan = await apiFetch(`/collections/tagihan_bulanan/${tagihanId}`);

                    document.getElementById('printRef').innerText = '#' + tagihan.id.substring(0, 8).toUpperCase();

                    const tglBayar = tagihan.tglBayar ? new Date(tagihan.tglBayar) : new Date();
                    document.getElementById('printTgl').innerText = tglBayar.toLocaleString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });

                    document.getElementById('printNama').innerText = tagihan.namaPelanggan;
                    document.getElementById('printIdPel').innerText = tagihan.idPelanggan;
                    document.getElementById('printArea').innerText = tagihan.area || '-';

                    const namaBulan = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Ags", "Sep", "Okt", "Nov", "Des"];
                    const periodeBulan = namaBulan[tagihan.bulan - 1] || tagihan.bulan;

                    document.getElementById('printDeskripsi').innerText = `Iuaran Internet Bulan ${periodeBulan} ${tagihan.tahun} (${tagihan.paket || '-'})`;

                    const rpValue = formatRupiah(tagihan.totalTagihan);
                    document.getElementById('printNominalItem').innerText = rpValue;
                    document.getElementById('printTotal').innerText = rpValue;

                    document.getElementById('printStatus').innerText = tagihan.status.toUpperCase();

                    // Render Barcode
                    try {
                        JsBarcode("#barcode", tagihan.id.substring(0, 10).toUpperCase(), {
                            format: "CODE128",
                            width: 1.5,
                            height: 30,
                            displayValue: true,
                            fontSize: 10,
                            margin: 5
                        });
                    } catch (err) {
                        console.error("Barcode Error", err);
                    }

                    // Auto Print
                    setTimeout(() => {
                        window.print();
                    }, 500);

                } catch (e) {
                    console.error("Loading receipt error:", e);
                    document.body.innerHTML = `<h3>Gagal memuat struk: ${e.message}</h3>`;
                }
            });
        }

        renderData();

    </script>
    <script>
        // Override window.print untuk APK Android
        if (window.AndroidJS && typeof window.AndroidJS.print === 'function') {
            window.print = function () {
                window.AndroidJS.print();
            };
        }
    </script>
</body>

</html>