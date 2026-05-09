<!DOCTYPE html>
<html lang="id">

<head>
    <script src="{{ asset('js/ss-storage-migrate.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Sans Speed - Pembukuan Saya</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        window.__ssTheme = localStorage.getItem('ss_theme') || 'dark';
    </script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-tap-highlight-color: transparent;
        }

        :root {
            --primary: #3b82f6;
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
            font-family: 'Inter', sans-serif;
            background: var(--bg-body);
            color: var(--text-main);
            padding-bottom: 24px;
        }

        .app-header {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--glass-border);
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .btn-back,
        .theme-toggle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid var(--glass-border);
            background: rgba(255, 255, 255, 0.06);
            color: var(--text-main);
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            cursor: pointer;
        }

        .header-title {
            flex: 1;
            font-size: 17px;
            font-weight: 800;
        }

        .content {
            padding: 16px;
        }

        .hero {
            background: var(--header-gradient);
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.25);
            color: #fff;
            margin-bottom: 14px;
        }

        .hero small {
            opacity: 0.9;
            font-weight: 600;
        }

        .hero h2 {
            margin-top: 6px;
            font-size: 22px;
            font-weight: 800;
        }

        .filter-row {
            display: flex;
            gap: 8px;
            margin-bottom: 14px;
        }

        .filter-select {
            flex: 1;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid var(--glass-border);
            background: var(--card-bg);
            color: var(--text-main);
            font-size: 13px;
        }

        .btn-refresh {
            border: 1px solid var(--glass-border);
            background: var(--card-bg);
            color: var(--text-main);
            border-radius: 10px;
            padding: 10px 12px;
            cursor: pointer;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            border-radius: 14px;
            padding: 12px;
        }

        .stat-title {
            font-size: 11px;
            color: var(--text-muted);
            margin-bottom: 6px;
        }

        .stat-value {
            font-size: 18px;
            font-weight: 800;
        }

        .full-row {
            grid-column: 1 / -1;
        }

        .empty {
            margin-top: 12px;
            text-align: center;
            color: var(--text-muted);
            font-size: 13px;
            display: none;
        }
    </style>
</head>

<body>
@include('billing.partials.web-bootstrap')

    <header class="app-header">
        <a href="{{ url('/app-teknisi') }}" class="btn-back"><i class="fas fa-arrow-left"></i></a>
        <div class="header-title">Pembukuan Saya</div>
        <button id="themeToggleBtn" class="theme-toggle"><i class="fas fa-moon"></i></button>
    </header>

    <div class="content">
        <div class="hero">
            <small>Rekap Agen</small>
            <h2 id="collectorName">...</h2>
        </div>

        <div class="filter-row">
            <select id="periodeFilter" class="filter-select">
                <option value="current">Periode: Bulan Ini</option>
                <option value="last">Periode: Bulan Lalu</option>
            </select>
            <button id="btnRefresh" class="btn-refresh"><i class="fas fa-rotate"></i></button>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-title">Total Ditagih</div>
                <div class="stat-value" id="valTotalTagihan">Rp 0</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Pelanggan Bayar</div>
                <div class="stat-value" id="valJumlahPelanggan">0</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Sudah Disetor</div>
                <div class="stat-value" id="valSetor">Rp 0</div>
            </div>
            <div class="stat-card">
                <div class="stat-title">Sisa Di Tangan</div>
                <div class="stat-value" id="valSisa">Rp 0</div>
            </div>
            <div class="stat-card full-row">
                <div class="stat-title">Status Komisi</div>
                <div class="stat-value" id="valFee">-</div>
                <div style="font-size:11px; color: var(--text-muted); margin-top: 6px;" id="feeInfo">Komisi dinonaktifkan</div>
            </div>
        </div>

        <div id="emptyState" class="empty">Belum ada transaksi untuk periode ini.</div>
    </div>

    <script type="module">
        import { auth, apiFetch } from '{{ asset('api-config.js') }}';

        const formatRp = (n) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(Number(n || 0));
        const norm = (v) => String(v || '').trim().toLowerCase();

        let currentProfile = null;

        const applyThemeIcon = () => {
            const btn = document.getElementById('themeToggleBtn');
            if (!btn) return;
            btn.innerHTML = document.body.classList.contains('light-mode')
                ? '<i class="fas fa-sun"></i>'
                : '<i class="fas fa-moon"></i>';
        };

        const getPeriod = () => {
            const mode = document.getElementById('periodeFilter')?.value || 'current';
            const d = new Date();
            if (mode === 'last') d.setMonth(d.getMonth() - 1);
            return { bulan: d.getMonth() + 1, tahun: d.getFullYear() };
        };

        async function loadMySummary() {
            const { bulan, tahun } = getPeriod();
            const res = await apiFetch(`/pembukuan/agen-summary?bulan=${bulan}&tahun=${tahun}`);
            const row = (res.data || [])[0] || null;
            const empty = document.getElementById('emptyState');

            document.getElementById('collectorName').textContent = currentProfile?.nama || '-';
            if (!row) {
                document.getElementById('valTotalTagihan').textContent = 'Rp 0';
                document.getElementById('valJumlahPelanggan').textContent = '0';
                document.getElementById('valSetor').textContent = 'Rp 0';
                document.getElementById('valSisa').textContent = 'Rp 0';
                document.getElementById('valFee').textContent = '-';
                document.getElementById('feeInfo').textContent = 'Komisi dinonaktifkan';
                empty.style.display = 'block';
                return;
            }

            empty.style.display = 'none';
            document.getElementById('collectorName').textContent = row.namaPenagih || currentProfile?.nama || '-';
            document.getElementById('valTotalTagihan').textContent = formatRp(row.totalTagihan);
            document.getElementById('valJumlahPelanggan').textContent = String(row.jumlahPelanggan || 0);
            document.getElementById('valSetor').textContent = formatRp(row.setor);
            document.getElementById('valSisa').textContent = formatRp(row.sisaDiTangan);
            document.getElementById('valFee').textContent = '-';
            document.getElementById('feeInfo').textContent = 'Komisi dinonaktifkan';
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (window.__ssTheme === 'light') document.body.classList.add('light-mode');
            applyThemeIcon();

            document.getElementById('themeToggleBtn')?.addEventListener('click', () => {
                document.body.classList.toggle('light-mode');
                localStorage.setItem('ss_theme', document.body.classList.contains('light-mode') ? 'light' : 'dark');
                applyThemeIcon();
            });

            document.getElementById('periodeFilter')?.addEventListener('change', loadMySummary);
            document.getElementById('btnRefresh')?.addEventListener('click', loadMySummary);
        });

        auth.onAuthStateChanged(async (user) => {
            if (!user) {
                window.location.replace('{{ url('/login') }}');
                return;
            }
            const prof = JSON.parse(localStorage.getItem('ss_user') || '{}');
            currentProfile = prof;
            const role = norm(prof.role);

            if (role === 'admin' || role === 'superadmin') {
                window.location.replace('{{ url('/pembukuan-kang-tagih') }}');
                return;
            }
            if (!['penagih', 'tekpen', 'teknisipenagih'].includes(role)) {
                window.location.replace('{{ url('/app-teknisi') }}');
                return;
            }

            try {
                await loadMySummary();
            } catch (e) {
                document.getElementById('emptyState').textContent = `Gagal memuat data: ${e.message}`;
                document.getElementById('emptyState').style.display = 'block';
            }
        });
    </script>
</body>

</html>
