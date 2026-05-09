<!DOCTYPE html>
<html lang="id">

<head>
    <script src="{{ asset('js/ss-storage-migrate.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Sans Speed - Pekerjaan Pemasangan</title>
    <!-- Pustaka Standar -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <script>
        window.__ssTheme = localStorage.getItem('ss_theme') || 'dark';
    </script>
    <style>
        /* CSS Reset & Setup - Mobile First */
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
            --header-gradient: linear-gradient(135deg, #a78bfa, #8b5cf6);
            /* Purple theme for pemasangan */
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
            padding-bottom: 30px;
        }

        /* HEADER */
        .app-header {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            padding: 15px 20px;
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
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(148, 163, 184, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-main);
            text-decoration: none;
            font-size: 16px;
            border: none;
            cursor: pointer;
        }

        .header-title {
            font-size: 18px;
            font-weight: 800;
            color: var(--text-main);
            flex-grow: 1;
        }

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
            cursor: pointer;
        }

        /* TABS */
        .tabs-container {
            display: flex;
            padding: 10px 20px;
            gap: 10px;
            overflow-x: auto;
            scrollbar-width: none;
            -ms-overflow-style: none;
            margin-bottom: 5px;
            background: var(--card-bg);
            border-bottom: 1px solid var(--glass-border);
        }

        .tabs-container::-webkit-scrollbar {
            display: none;
        }

        .tab-btn {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: 0.3s;
            color: var(--text-muted);
            background: transparent;
            border: 1px solid var(--glass-border);
        }

        .tab-btn.active {
            background: var(--header-gradient);
            color: white;
            border: none;
            box-shadow: 0 4px 10px rgba(139, 92, 246, 0.3);
        }

        /* LIST & CARDS */
        .content-area {
            padding: 15px 20px;
        }

        .task-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .task-card {
            background: var(--card-bg);
            backdrop-filter: blur(10px);
            padding: 16px;
            border-radius: 16px;
            border-left: 5px solid var(--secondary);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.03);
            border-top: 1px solid var(--glass-border);
            border-right: 1px solid var(--glass-border);
            border-bottom: 1px solid var(--glass-border);
            position: relative;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .task-card:active {
            transform: scale(0.98);
        }

        .task-card.proses {
            border-left-color: var(--warning);
        }

        .task-card.selesai {
            border-left-color: var(--success);
            opacity: 0.7;
        }

        .task-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            align-items: flex-start;
        }

        .task-title {
            font-size: 15px;
            font-weight: 800;
            color: var(--text-main);
            line-height: 1.3;
        }

        .task-status {
            font-size: 10px;
            font-weight: 800;
            padding: 3px 8px;
            border-radius: 8px;
            text-transform: uppercase;
        }

        .status-badge-pending {
            background: #e0e7ff;
            color: #4f46e5;
        }

        .status-badge-proses {
            background: #fef3c7;
            color: #d97706;
        }

        .status-badge-selesai {
            background: #dcfce7;
            color: #16a34a;
        }

        .task-meta {
            font-size: 12px;
            color: var(--text-muted);
            display: flex;
            flex-direction: column;
            gap: 4px;
            margin-top: 8px;
        }

        .task-meta div {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* BOTTOM MODAL SHEET */
        #taskModal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(5px);
            z-index: 100;
            align-items: flex-end;
            opacity: 0;
            transition: opacity 0.3s;
        }

        #taskModal.show {
            opacity: 1;
        }

        .modal-bottom {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            width: 100%;
            border-top-left-radius: 20px;
            border-top-right-radius: 20px;
            padding: 25px 20px 30px 20px;
            transform: translateY(100%);
            transition: transform 0.4s cubic-bezier(0.19, 1, 0.22, 1);
            max-height: 85vh;
            overflow-y: auto;
        }

        .modal-bottom.open {
            transform: translateY(0);
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

        .modal-title {
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 5px;
            margin-top: 10px;
            color: var(--text-main);
        }

        .modal-pelanggan {
            font-size: 14px;
            font-weight: 600;
            color: var(--secondary);
            margin-bottom: 15px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            margin-bottom: 5px;
        }

        .form-control,
        .form-select {
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            border: 1px solid var(--glass-border);
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-main);
            -webkit-text-fill-color: var(--text-main);
            font-family: inherit;
            font-size: 14px;
            outline: none;
        }

        .form-select option {
            background: #1e293b;
            color: #f8fafc;
        }

        .form-select option:checked {
            background: #334155;
            color: #f8fafc;
        }

        body.light-mode .form-select option {
            background: #ffffff;
            color: #0f172a;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
        }

        .btn-full {
            display: block;
            width: 100%;
            padding: 14px;
            text-align: center;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            border: none;
            cursor: pointer;
            transition: 0.2s;
            margin-top: 10px;
            color: white;
        }

        .btn-primary {
            background: var(--header-gradient);
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        }

        .btn-primary:active {
            transform: scale(0.96);
        }

        .loading-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-muted);
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-muted);
            display: none;
        }

        .empty-state i {
            font-size: 40px;
            margin-bottom: 10px;
            opacity: 0.5;
        }

        @media (min-width: 900px) {
            .task-list {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>
</head>

<body>
@include('billing.partials.web-bootstrap')

    <!-- Header -->
    <header class="app-header">
        <a href="{{ url('/app-teknisi') }}" class="btn-back"><i class="fas fa-arrow-left"></i></a>
        <div class="header-title">Pemasangan Baru</div>
        <button class="theme-toggle" id="themeToggleBtn" title="Ganti Mode"><i class="fas fa-moon"></i></button>
    </header>

    <!-- Filter Tabs -->
    <div class="tabs-container">
        <button class="tab-btn active" onclick="filterTask('pending', this)">Antrean Pemasangan</button>
        <button class="tab-btn" onclick="filterTask('proses', this)">Sedang Instalasi</button>
        <button class="tab-btn" onclick="filterTask('selesai', this)">Selesai</button>
    </div>

    <!-- Content -->
    <div class="content-area">
        <div id="loadingIndicator" class="loading-state">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p style="margin-top: 10px;">Memuat antrean pemasangan...</p>
        </div>

        <div id="emptyIndicator" class="empty-state">
            <i class="fas fa-calendar-check"></i>
            <h3>Tidak ada jadwal</h3>
            <p>Anda tidak memiliki jadwal pemasangan di kategori ini.</p>
        </div>

        <div class="task-list" id="taskListContainer">
            <!-- Data akan dimuat disini via JS -->
        </div>
    </div>

    <!-- Modal Form Update Task -->
    <div id="taskModal">
        <div class="modal-bottom" id="modalBottomContent">
            <h3 class="modal-title" id="mJudul">Instalasi Baru</h3>
            <div class="modal-pelanggan"><i class="fas fa-user-plus"></i> <span id="mNamaPelanggan">Nama Calon
                    Pelanggan</span></div>

            <div
                style="background: rgba(139, 92, 246, 0.1); padding: 12px; border-radius: 10px; margin-bottom: 15px; font-size: 13px; color: var(--text-main); border-left: 3px solid var(--secondary);">
                <i class="fas fa-map-marker-alt" style="color: var(--secondary);"></i> <span id="mAlamat">Alamat
                    instalasi...</span>
            </div>

            <div style="font-size: 13px; color: var(--text-muted); margin-bottom: 15px;">
                <i class="fab fa-whatsapp"></i> <span id="mWA"></span>
            </div>

            <form id="formUpdateTask">
                <input type="hidden" id="inTaskId">

                <div class="form-group">
                    <label class="form-label">Ubah Status Pemasangan</label>
                    <select id="inStatus" class="form-select">
                        <option value="pending">Menunggu Antrean</option>
                        <option value="proses">Menuju Lokasi / Mulai Tarik Kabel</option>
                        <option value="selesai">Instalasi Selesai & Aktif</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Catatan instalasi (Opsional)</label>
                    <textarea id="inCatatan" class="form-control" rows="3"
                        placeholder="Contoh: redaman (dBm), kabel terpakai, perangkat pelanggan..."></textarea>
                </div>

                <button type="submit" class="btn-full btn-primary" id="btnSimpan"><i class="fas fa-save"></i> Simpan
                    Laporan Pemasangan</button>
                <button type="button" class="btn-full"
                    style="background: transparent; color: var(--text-muted); border: 1px solid var(--glass-border);"
                    onclick="tutupModal()">Batal</button>
            </form>
        </div>
    </div>

    <script type="module">
        import { auth, apiFetch } from '{{ asset('api-config.js') }}';

        let allTasks = [];
        let currentFilter = 'pending';
        let currentUser = null;
        const getCurrentUserId = () => currentUser?.uid || currentUser?.id || null;

        document.addEventListener('DOMContentLoaded', () => {
            auth.onAuthStateChanged(async (user) => {
                if (user) {
                    const prof = JSON.parse(localStorage.getItem('ss_user'));
                    if (prof) {
                        currentUser = prof;
                        if (window.__ssTheme === 'light') document.body.classList.add('light-mode');
                        const themeBtn = document.getElementById('themeToggleBtn');
                        const applyThemeIcon = () => {
                            themeBtn.innerHTML = document.body.classList.contains('light-mode')
                                ? '<i class="fas fa-sun"></i>'
                                : '<i class="fas fa-moon"></i>';
                        };
                        applyThemeIcon();
                        themeBtn.addEventListener('click', () => {
                            document.body.classList.toggle('light-mode');
                            const mode = document.body.classList.contains('light-mode') ? 'light' : 'dark';
                            localStorage.setItem('ss_theme', mode);
                            applyThemeIcon();
                        });
                        loadTasks();
                    } else {
                        window.location.replace("{{ url('/login') }}");
                    }
                } else {
                    window.location.replace("{{ url('/login') }}");
                }
            });
        });

        async function loadTasks() {
            try {
                const res = await apiFetch('/tugas');
                const tasks = res.data || [];
                const myUid = getCurrentUserId();
                const role = (currentUser?.role || '').toLowerCase();
                allTasks = tasks.filter(t => {
                    if (t.jenisTask !== 'pemasangan') return false;
                    if (role === 'admin' || role === 'superadmin') return true;
                    const isMine = t.assignTo && myUid && t.assignTo === myUid;
                    const isBroadcast = Number(t.isBroadcast || 0) === 1;
                    const canSeeBroadcast = isBroadcast && (!t.claimedBy || t.claimedBy === myUid);
                    return isMine || canSeeBroadcast;
                });

                document.getElementById('loadingIndicator').style.display = 'none';
                renderTasks();
            } catch (err) {
                console.error("Gagal memuat tugas", err);
                document.getElementById('loadingIndicator').innerHTML = `<span style="color:red">Gagal memuat: ${err.message}</span>`;
            }
        }

        function renderTasks() {
            const container = document.getElementById('taskListContainer');
            const empty = document.getElementById('emptyIndicator');
            container.innerHTML = '';

            const filteredTasks = allTasks.filter(t => (t.status || 'pending') === currentFilter);

            // Sort by tglDibuat asc (Prioritaskan antrean yang masuk duluan)
            filteredTasks.sort((a, b) => new Date(a.createdAt || a.tglDibuat) - new Date(b.createdAt || b.tglDibuat));

            if (filteredTasks.length === 0) {
                empty.style.display = 'block';
                return;
            }
            empty.style.display = 'none';

            filteredTasks.forEach(t => {
                let badgeClass = t.status === 'selesai' ? 'status-badge-selesai' : (t.status === 'proses' ? 'status-badge-proses' : 'status-badge-pending');
                let badgeText = t.status === 'selesai' ? 'SELESAI' : (t.status === 'proses' ? 'PROSES' : 'PENDING');
                let cardClass = t.status === 'selesai' ? 'selesai' : (t.status === 'proses' ? 'proses' : '');

                let tglText = '-';
                if (t.tglDibuat || t.createdAt) {
                    const d = new Date(t.tglDibuat || t.createdAt);
                    tglText = d.toLocaleString('id-ID', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' });
                }

                const isBroadcast = Number(t.isBroadcast || 0) === 1;
                const PIC = isBroadcast
                    ? (t.claimedByNama ? `Broadcast • Diambil ${t.claimedByNama}` : 'Broadcast • Belum diambil')
                    : (t.assignToNama || '-');
                const htmlStr = `
                    <div class="task-card ${cardClass}" onclick="bukaModal('${t.id}')">
                        <div class="task-header">
                            <div class="task-title">${t.judul || 'Pemasangan Baru'}</div>
                            <div class="task-status ${badgeClass}">${badgeText}</div>
                        </div>
                        <div class="task-meta">
                            <div><i class="fas fa-user-plus" style="width:16px;"></i> ${t.namaPelanggan || 'Tanpa Nama'}</div>
                            <div><i class="fas fa-user-hard-hat" style="width:16px;"></i> ${PIC}</div>
                            <div><i class="fas fa-map-marker-alt" style="width:16px;"></i> ${t.alamat || '-'}</div>
                            <div><i class="fab fa-whatsapp" style="width:16px;"></i> ${t.noWA || '-'}</div>
                            <div><i class="far fa-calendar-alt" style="width:16px;"></i> Masuk: ${tglText}</div>
                        </div>
                    </div>
                `;
                container.innerHTML += htmlStr;
            });
        }

        window.filterTask = function (status, btnElement) {
            currentFilter = status;
            document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            btnElement.classList.add('active');
            renderTasks();
        }

        window.bukaModal = function (id) {
            const t = allTasks.find(x => x.id === id);
            if (!t) return;

            document.getElementById('inTaskId').value = t.id;
            document.getElementById('mJudul').innerText = t.judul || 'Pemasangan Baru';
            document.getElementById('mNamaPelanggan').innerText = t.namaPelanggan || '-';
            document.getElementById('mAlamat').innerText = t.alamat || 'Alamat tidak tersedia';
            document.getElementById('mWA').innerText = t.noWA ? t.noWA : 'No WA Belum diisi';
            document.getElementById('inStatus').value = t.status || 'pending';
            document.getElementById('inCatatan').value = t.catatanTeknisi || '';

            const pm = document.getElementById('taskModal');
            const pmb = document.getElementById('modalBottomContent');
            pm.style.display = 'flex';
            setTimeout(() => { pm.classList.add('show'); pmb.classList.add('open'); }, 10);
        }

        window.tutupModal = function () {
            const pm = document.getElementById('taskModal');
            const pmb = document.getElementById('modalBottomContent');
            pmb.classList.remove('open');
            pm.classList.remove('show');
            setTimeout(() => { pm.style.display = 'none'; }, 300);
        }

        // Close outside click
        document.getElementById('taskModal').addEventListener('click', (e) => {
            if (e.target.id === 'taskModal') window.tutupModal();
        });

        // Submit Form Update
        document.getElementById('formUpdateTask').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('btnSimpan');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
            btn.disabled = true;

            const id = document.getElementById('inTaskId').value;
            const payload = {
                status: document.getElementById('inStatus').value,
                catatanTeknisi: document.getElementById('inCatatan').value,
                updatedAt: new Date().toISOString()
            };

            if (payload.status === 'selesai') {
                payload.tglSelesai = new Date().toISOString();
            }

            try {
                await apiFetch('/tugas/' + id, {
                    method: 'PATCH',
                    body: JSON.stringify(payload)
                });

                window.tutupModal();
                await loadTasks();
            } catch (err) {
                alert("Gagal update pemasangan: " + err.message);
            } finally {
                btn.innerHTML = '<i class="fas fa-save"></i> Simpan Laporan Pemasangan';
                btn.disabled = false;
            }
        });

    </script>
</body>

</html>