<!DOCTYPE html>
<html lang="id">

<head>
    <script src="{{ asset('js/ss-storage-migrate.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Struk Sans Speed</title>
    <style>
        @page {
            margin: 0;
        }

        body {
            margin: 0;
            padding: 10px;
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            background: #fff;
            color: #000;
        }

        .struk-container {
            width: 100%;
            max-width: 58mm;
            /* Standard thermal printer width */
            margin: 0 auto;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .font-bold {
            font-weight: bold;
        }

        .divider {
            border-bottom: 1px dashed #000;
            margin: 8px 0;
        }

        .double-divider {
            border-bottom: 2px solid #000;
            margin: 8px 0;
        }

        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .row-item {
            flex: 1;
        }

        h1,
        h2,
        h3,
        p {
            margin: 0;
            padding: 0;
        }

        .header-logo {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .btn-print {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                padding: 0;
            }
        }
    </style>
</head>

<body>
@include('billing.partials.web-bootstrap')

    <div class="struk-container">
        <!-- Header -->
        <div class="text-center">
            <div class="header-logo">Sans Speed</div>
            <div>Layanan Internet Cepat</div>
            <div>Jl. Raya Contoh No. 123, Pusat</div>
            <div>WA: 0812-3456-7890</div>
        </div>

        <div class="divider"></div>

        <!-- Info Transaksi -->
        <div class="row">
            <span class="text-left">No Nota:</span>
            <span class="text-right" id="p_nota">TRX-1010</span>
        </div>
        <div class="row">
            <span class="text-left">Tanggal:</span>
            <span class="text-right" id="p_tanggal">-</span>
        </div>
        <div class="row">
            <span class="text-left">Kasir:</span>
            <span class="text-right" id="p_kasir">System</span>
        </div>

        <div class="divider"></div>

        <!-- Info Pelanggan -->
        <div class="row">
            <span class="text-left">Pelanggan:</span>
            <span class="text-right font-bold" id="p_pelanggan">-</span>
        </div>
        <div class="row">
            <span class="text-left">ID Pel:</span>
            <span class="text-right" id="p_id">-</span>
        </div>
        <div class="row">
            <span class="text-left">Paket:</span>
            <span class="text-right" id="p_paket">-</span>
        </div>

        <div class="divider"></div>

        <!-- Rincian -->
        <div class="text-left font-bold" style="margin-bottom: 5px;">Pembayaran Tagihan</div>
        <div class="row">
            <span class="text-left" id="p_deskripsi">Bulan -</span>
            <span class="text-right" id="p_total">Rp 0</span>
        </div>

        <div class="double-divider"></div>

        <!-- Total -->
        <div class="row font-bold" style="font-size: 14px;">
            <span class="text-left">TOTAL (LUNAS):</span>
            <span class="text-right" id="p_grand_total">Rp 0</span>
        </div>

        <div class="divider"></div>

        <!-- Footer -->
        <div class="text-center" style="margin-top: 10px; font-size: 10px; line-height: 1.4;">
            <div>Terima kasih atas pembayaran Anda.</div>
            <div>Simpan struk ini sebagai bukti</div>
            <div>pembayaran yang sah.</div>
            <div style="margin-top: 10px;">~ Powered by Sans Speed Apps ~</div>
        </div>

        <!-- Tombol Aksi (Hanya tampil di layar) -->
        <div class="no-print">
            <button class="btn-print" onclick="window.print()">
                <i class="fas fa-print"></i> CETAK STRUK ULANG
            </button>
            <button class="btn-print" style="background:#475569; margin-top:5px;" onclick="window.close()">
                TUTUP TAB
            </button>
        </div>
    </div>

    <script>
        // Ambil parameter dari URL untuk rendering dinamis
        const urlParams = new URLSearchParams(window.location.search);

        // Formatter Mata Uang
        const formatRp = (angka) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(angka);

        // Set Nilai Berdasarkan Parameter
        document.getElementById('p_nota').innerText = 'TRX-' + (urlParams.get('id') || Date.now().toString().slice(-6));

        const d = new Date();
        document.getElementById('p_tanggal').innerText = d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });

        document.getElementById('p_kasir').innerText = urlParams.get('kasir') || 'Admin/Teknisi';
        document.getElementById('p_pelanggan').innerText = urlParams.get('nama') || 'Budi Santoso';
        document.getElementById('p_id').innerText = urlParams.get('idpel') || 'P000';
        document.getElementById('p_paket').innerText = urlParams.get('paket') || 'Internet 10Mbps';

        const bln = urlParams.get('bulan') || '1';
        const thn = urlParams.get('tahun') || '2026';
        const MONTHS = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
        document.getElementById('p_deskripsi').innerText = `Tagihan ${MONTHS[parseInt(bln) - 1]} ${thn}`;

        const total = parseFloat(urlParams.get('total') || '150000');
        document.getElementById('p_total').innerText = formatRp(total);
        document.getElementById('p_grand_total').innerText = formatRp(total);

        // Auto Print setelah loading selesai
        window.addEventListener('load', () => {
            setTimeout(() => {
                window.print();
            }, 500);
        });
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