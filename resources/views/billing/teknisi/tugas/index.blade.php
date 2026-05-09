<!DOCTYPE html>
<html lang="id">

<head>
    <script src="{{ asset('js/ss-storage-migrate.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tugas Teknisi - Sans Speed Billing</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <script>
        if (localStorage.getItem('ss_theme') === 'light') document.documentElement.classList.add('light-mode');
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .page-title {
            font-size: 22px;
            font-weight: 800;
            color: var(--text-primary, #f8fafc);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }

        .stat-mini {
            background: var(--card-bg, #1e293b);
            border: 1px solid var(--border-color, #334155);
            border-radius: 14px;
            padding: 18px;
            text-align: center;
        }

        .stat-mini .val {
            font-size: 28px;
            font-weight: 800;
        }

        .stat-mini .lbl {
            font-size: 12px;
            color: #64748b;
            font-weight: 600;
            margin-top: 4px;
        }

        .filters {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 20px;
            align-items: center;
        }

        .filter-btn {
            padding: 8px 18px;
            border-radius: 20px;
            border: 1px solid #334155;
            background: transparent;
            color: #94a3b8;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-btn.active,
        .filter-btn:hover {
            background: #3b82f6;
            color: white;
            border-color: #3b82f6;
        }

        .task-grid {
            display: grid;
            gap: 14px;
        }

        .task-card {
            background: var(--card-bg, #1e293b);
            border: 1px solid var(--border-color, #334155);
            border-radius: 14px;
            padding: 20px;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 12px;
            align-items: start;
            transition: box-shadow 0.2s;
        }

        .task-card:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .task-card.prioritas-tinggi {
            border-left: 4px solid #ef4444;
        }

        .task-card.prioritas-normal {
            border-left: 4px solid #3b82f6;
        }

        .task-card.prioritas-rendah {
            border-left: 4px solid #10b981;
        }

        .task-judul {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-primary, #f1f5f9);
            margin-bottom: 6px;
        }

        .task-meta {
            font-size: 12px;
            color: #64748b;
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            margin-top: 8px;
        }

        .task-meta span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
        }

        .badge-pending {
            background: rgba(234, 179, 8, .15);
            color: #eab308;
        }

        .badge-proses {
            background: rgba(59, 130, 246, .15);
            color: #60a5fa;
        }

        .badge-selesai {
            background: rgba(16, 185, 129, .15);
            color: #10b981;
        }

        .badge-batal {
            background: rgba(100, 116, 139, .15);
            color: #94a3b8;
        }

        .badge-tinggi {
            background: rgba(239, 68, 68, .15);
            color: #f87171;
        }

        .badge-normal {
            background: rgba(59, 130, 246, .15);
            color: #60a5fa;
        }

        .badge-rendah {
            background: rgba(16, 185, 129, .15);
            color: #10b981;
        }

        .task-actions {
            display: flex;
            gap: 8px;
            align-items: flex-start;
        }

        .btn-icon {
            width: 34px;
            height: 34px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            transition: all 0.2s;
        }

        .btn-icon.edit {
            background: rgba(59, 130, 246, .15);
            color: #60a5fa;
        }

        .btn-icon.edit:hover {
            background: #3b82f6;
            color: white;
        }

        .btn-icon.done {
            background: rgba(16, 185, 129, .15);
            color: #10b981;
        }

        .btn-icon.done:hover {
            background: #10b981;
            color: white;
        }

        .btn-icon.del {
            background: rgba(239, 68, 68, .15);
            color: #f87171;
        }

        .btn-icon.del:hover {
            background: #ef4444;
            color: white;
        }

        /* Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 999;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-box {
            background: var(--card-bg, #1e293b);
            border: 1px solid #334155;
            border-radius: 20px;
            padding: 32px;
            width: 95%;
            max-width: 560px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-title {
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 24px;
            color: var(--text-primary, #f1f5f9);
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #94a3b8;
            margin-bottom: 7px;
            display: block;
        }

        .form-control {
            width: 100%;
            padding: 11px 14px;
            background: rgba(255, 255, 255, 0.05);
            /* Slight transparent for dark mode */
            border: 1px solid #334155;
            border-radius: 10px;
            color: var(--text-primary, #f1f5f9);
            font-size: 14px;
            transition: border 0.2s;
            box-sizing: border-box;
        }

        .form-control option {
            background: #1e293b;
            /* Prevent white on white */
            color: #f1f5f9;
        }

        .form-control:focus {
            outline: none;
            border-color: #3b82f6;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .btn-primary:hover {
            opacity: 0.85;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: #94a3b8;
            border: 1px solid #334155;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 24px;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #64748b;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 14px;
            display: block;
        }

        .catatan-box {
            background: rgba(16, 185, 129, .07);
            border: 1px solid rgba(16, 185, 129, .2);
            border-radius: 10px;
            padding: 12px;
            margin-top: 10px;
            font-size: 13px;
            color: #34d399;
        }

        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .stats-row {
                grid-template-columns: repeat(2, 1fr);
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
        <main class="main-content" id="main-content">
            <!-- HEADER -->
            <header id="app-header"></header>

            <div class="content-wrapper">

                <div class="page-header">
                    <div class="page-title"><i class="fas fa-hard-hat"></i> Manajemen Tugas Teknisi</div>
                    <button class="btn-primary" id="btnBuatTugas" onclick="bukaModalBuat()"><i class="fas fa-plus"></i> Buat Tugas
                        Baru</button>
                </div>

                <!-- Stats -->
                <div class="stats-row">
                    <div class="stat-mini">
                        <div class="val" id="statTotal" style="color:#3b82f6">0</div>
                        <div class="lbl">Total Tugas</div>
                    </div>
                    <div class="stat-mini">
                        <div class="val" id="statPending" style="color:#eab308">0</div>
                        <div class="lbl">Menunggu</div>
                    </div>
                    <div class="stat-mini">
                        <div class="val" id="statProses" style="color:#60a5fa">0</div>
                        <div class="lbl">Dikerjakan</div>
                    </div>
                    <div class="stat-mini">
                        <div class="val" id="statSelesai" style="color:#10b981">0</div>
                        <div class="lbl">Selesai</div>
                    </div>
                    <div class="stat-mini">
                        <div class="val" id="statTinggi" style="color:#f87171">0</div>
                        <div class="lbl">Prioritas Tinggi</div>
                    </div>
                </div>

                <!-- Filter Buttons -->
                <div class="filters">
                    <button class="filter-btn active" onclick="setFilter('semua', this)"><i class="fas fa-list"></i>
                        Semua</button>
                    <button class="filter-btn" onclick="setFilter('pending', this)"><i class="fas fa-clock"></i>
                        Menunggu</button>
                    <button class="filter-btn" onclick="setFilter('proses', this)"><i class="fas fa-spinner"></i>
                        Dikerjakan</button>
                    <button class="filter-btn" onclick="setFilter('selesai', this)"><i class="fas fa-check-circle"></i>
                        Selesai</button>
                    <button class="filter-btn" onclick="setFilter('batal', this)"><i class="fas fa-ban"></i>
                        Dibatalkan</button>
                    <div style="flex:1"></div>
                    <input type="text" class="form-control" id="searchInput" style="max-width:220px; padding: 8px 14px;"
                        placeholder="🔍 Cari tugas..." oninput="renderTugas()">
                </div>

                <!-- Task List -->
                <div class="task-grid" id="taskGrid">
                    <div class="empty-state"><i class="fas fa-spinner fa-spin"></i>
                        <div>Memuat tugas...</div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Buat/Edit Tugas -->
    <div class="modal-overlay" id="modalTugas">
        <div class="modal-box">
            <div class="modal-title" id="modalTugasTitle"><i class="fas fa-plus-circle" style="color:#3b82f6"></i> Buat
                Tugas Baru</div>
            <input type="hidden" id="editId">

            <div class="form-group">
                <label class="form-label">Judul Tugas *</label>
                <input type="text" class="form-control" id="inputJudul" placeholder="Contoh: Pasang Baru - Ibu Sari">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Jenis Tugas *</label>
                    <select class="form-control" id="inputJenis">
                        <option value="pemasangan">🔧 Pemasangan Baru</option>
                        <option value="troubleshoot">⚠️ Gangguan / Perbaikan</option>
                        <option value="cabut">🔋 Cabut/Putus Langganan</option>
                        <option value="survey">📍 Survey Lokasi</option>
                        <option value="lain">📋 Lainnya</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Prioritas *</label>
                    <select class="form-control" id="inputPrioritas">
                        <option value="normal">🔵 Normal</option>
                        <option value="tinggi">🔴 Tinggi / Mendesak</option>
                        <option value="rendah">🟢 Rendah</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Assign ke Teknisi *</label>
                <select class="form-control" id="inputTeknisi">
                    <option value="">-- Pilih Teknisi --</option>
                </select>
                <div style="font-size:11px;color:#94a3b8;margin-top:6px;">
                    Jika tidak dipilih, tugas akan dibroadcast ke semua teknisi.
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Nama Pelanggan</label>
                    <input type="text" class="form-control" id="inputNamaPelanggan" placeholder="Opsional">
                </div>
                <div class="form-group">
                    <label class="form-label">No. WhatsApp Pelanggan</label>
                    <input type="text" class="form-control" id="inputNoWA" placeholder="Contoh: 0812xxxxxxx">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Alamat Lokasi</label>
                <input type="text" class="form-control" id="inputAlamat" placeholder="Alamat lengkap lokasi tugas">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Deadline</label>
                    <input type="datetime-local" class="form-control" id="inputDeadline">
                </div>
                <div></div>
            </div>
            <div class="form-group">
                <label class="form-label">Deskripsi / Keterangan Tambahan</label>
                <textarea class="form-control" id="inputDeskripsi" rows="3"
                    placeholder="Detail pekerjaan, catatan khusus, dll..."></textarea>
            </div>

            <div class="modal-footer">
                <button class="btn-secondary" onclick="tutupModal()">Batal</button>
                <button class="btn-primary" id="btnSimpanTugas" onclick="simpanTugas()"><i class="fas fa-save"></i>
                    Simpan Tugas</button>
            </div>
        </div>
    </div>

    <!-- Modal Update Status -->
    <div class="modal-overlay" id="modalStatus">
        <div class="modal-box" style="max-width:420px;">
            <div class="modal-title"><i class="fas fa-sync-alt" style="color:#10b981"></i> Update Status Tugas</div>
            <input type="hidden" id="statusEditId">
            <div class="form-group">
                <label class="form-label">Status Baru</label>
                <select class="form-control" id="statusBaru">
                    <option value="pending">⏳ Menunggu / Belum Dikerjakan</option>
                    <option value="proses">🔧 Sedang Dikerjakan</option>
                    <option value="selesai">✅ Selesai</option>
                    <option value="batal">🚫 Dibatalkan</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Catatan Teknisi (Opsional)</label>
                <textarea class="form-control" id="catatanTeknisi" rows="3"
                    placeholder="Catatan hasil pekerjaan, kendala, dll..."></textarea>
            </div>
            <div class="modal-footer">
                <button class="btn-secondary" onclick="tutupModalStatus()">Batal</button>
                <button class="btn-primary" onclick="simpanStatus()"><i class="fas fa-save"></i> Update Status</button>
            </div>
        </div>
    </div>

    <script>
        const showToast = (msg, type = 'success') => {
            const t = document.createElement('div');
            const bg = { success: '#10b981', danger: '#ef4444', warning: '#eab308', info: '#3b82f6' }[type] || '#3b82f6';
            t.style.cssText = `position:fixed;bottom:24px;right:24px;background:${bg};color:white;padding:14px 22px;border-radius:12px;font-size:14px;font-weight:600;z-index:9999;box-shadow:0 4px 20px rgba(0,0,0,0.3);transition:all 0.3s;`;
            t.textContent = msg;
            document.body.appendChild(t);
            setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 300); }, 3000);
        };
    </script>

    <script type="module">
        import { renderSidebar, renderHeader } from './js/components/layout.js';
        import { apiFetch, auth, hasPermission } from '{{ asset('api-config.js') }}';

        renderSidebar(); renderHeader();

        let allTugas = [];
        let allTeknisi = [];
        let currentFilter = 'semua';
        let canManageTasks = false;

        // --- LOAD ---
        async function loadAll() {
            try {
                const [tugasRes, usersRes] = await Promise.all([
                    apiFetch('/tugas'),
                    apiFetch('/users/task-assignees')
                ]);
                allTugas = tugasRes.data || [];

                const userList = Array.isArray(usersRes) ? usersRes : (usersRes.data || []);
                allTeknisi = userList.filter(u => ['teknisi', 'penagih', 'tekpen', 'teknisipenagih'].includes(u.role?.toLowerCase()));

                populateTeknisiSelect(allTeknisi);
                renderTugas();
                updateStats();
            } catch (e) {
                console.error(e);
                document.getElementById('taskGrid').innerHTML = `<div class="empty-state"><i class="fas fa-exclamation-triangle" style="color:#ef4444"></i><div>Gagal memuat data: ${e.message}</div></div>`;
            }
        }

        function populateTeknisiSelect(list) {
            const sel = document.getElementById('inputTeknisi');
            sel.innerHTML = '<option value="">-- Pilih Teknisi --</option><option value="__all__" data-nama="Semua Teknisi">📣 Semua Teknisi (Broadcast)</option>';
            list.forEach(u => {
                const opt = document.createElement('option');
                opt.value = u.id;
                opt.dataset.nama = u.nama;
                opt.textContent = `${u.nama} (${u.role})`;
                sel.appendChild(opt);
            });
        }

        function updateStats() {
            document.getElementById('statTotal').textContent = allTugas.length;
            document.getElementById('statPending').textContent = allTugas.filter(t => t.status === 'pending').length;
            document.getElementById('statProses').textContent = allTugas.filter(t => t.status === 'proses').length;
            document.getElementById('statSelesai').textContent = allTugas.filter(t => t.status === 'selesai').length;
            document.getElementById('statTinggi').textContent = allTugas.filter(t => t.prioritas === 'tinggi').length;
        }

        window.setFilter = (f, el) => {
            currentFilter = f;
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            el.classList.add('active');
            renderTugas();
        };

        window.renderTugas = () => {
            const search = document.getElementById('searchInput').value.toLowerCase();
            let list = currentFilter === 'semua' ? allTugas : allTugas.filter(t => t.status === currentFilter);
            if (search) list = list.filter(t =>
                (t.judul || '').toLowerCase().includes(search) ||
                (t.assignToNama || '').toLowerCase().includes(search) ||
                (t.namaPelanggan || '').toLowerCase().includes(search) ||
                (t.alamat || '').toLowerCase().includes(search)
            );
            const grid = document.getElementById('taskGrid');
            if (list.length === 0) {
                grid.innerHTML = `<div class="empty-state"><i class="fas fa-inbox"></i><div>Tidak ada tugas${currentFilter !== 'semua' ? ' dengan status ini' : ''}.</div></div>`;
                return;
            }
            grid.innerHTML = list.map(t => {
                const badgeStatus = { pending: 'badge-pending', proses: 'badge-proses', selesai: 'badge-selesai', batal: 'badge-batal' }[t.status] || 'badge-pending';
                const badgePri = { tinggi: 'badge-tinggi', normal: 'badge-normal', rendah: 'badge-rendah' }[t.prioritas] || 'badge-normal';
                const statusLabel = { pending: '⏳ Menunggu', proses: '🔧 Dikerjakan', selesai: '✅ Selesai', batal: '🚫 Batal' }[t.status] || t.status;
                const priLabel = { tinggi: '🔴 Mendesak', normal: '🔵 Normal', rendah: '🟢 Rendah' }[t.prioritas] || t.prioritas;
                const jenisLabel = { pemasangan: '🔧 Pemasangan', troubleshoot: '⚠️ Gangguan', cabut: '🔋 Cabut', survey: '📍 Survey', lain: '📋 Lainnya' }[t.jenisTask] || t.jenisTask;
                const deadline = t.tglDeadline ? new Date(t.tglDeadline).toLocaleString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) : '-';
                const selesai = t.tglSelesai ? new Date(t.tglSelesai).toLocaleString('id-ID', { day: 'numeric', month: 'short' }) : null;
                const isBroadcast = Number(t.isBroadcast || 0) === 1 || t.assignTo === '__all__';
                const assignLabel = isBroadcast
                    ? (t.claimedByNama ? `📣 Semua Teknisi • Diambil: ${t.claimedByNama}` : '📣 Semua Teknisi • Belum ada yang ambil')
                    : (t.assignToNama || '-');
                return `
                <div class="task-card prioritas-${t.prioritas || 'normal'}">
                    <div>
                        <div class="task-judul">${t.judul || '-'}</div>
                        <div style="font-size:13px;color:#94a3b8;margin-bottom:8px;">${t.deskripsi || ''}</div>
                        <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:8px;">
                            <span class="badge ${badgeStatus}">${statusLabel}</span>
                            <span class="badge ${badgePri}">${priLabel}</span>
                            <span class="badge" style="background:rgba(148,163,184,.1);color:#94a3b8;">${jenisLabel}</span>
                        </div>
                        <div class="task-meta">
                            <span><i class="fas fa-user-hard-hat"></i> ${assignLabel}</span>
                            ${t.namaPelanggan ? `<span><i class="fas fa-user"></i> ${t.namaPelanggan}</span>` : ''}
                            ${t.alamat ? `<span><i class="fas fa-map-marker-alt"></i> ${t.alamat}</span>` : ''}
                            <span><i class="fas fa-clock"></i> Deadline: <strong>${deadline}</strong></span>
                            ${selesai ? `<span><i class="fas fa-check"></i> Selesai: ${selesai}</span>` : ''}
                        </div>
                        ${t.catatanTeknisi ? `<div class="catatan-box"><i class="fas fa-comment-alt"></i> <strong>Catatan Teknisi:</strong> ${t.catatanTeknisi}</div>` : ''}
                        ${t.noWA ? `<div style="margin-top:8px;"><a href="https://wa.me/${t.noWA.replace(/^0/, '62')}" target="_blank" style="font-size:12px;color:#10b981;text-decoration:none;"><i class="fab fa-whatsapp"></i> ${t.noWA}</a></div>` : ''}
                    </div>
                    <div class="task-actions">
                        <button class="btn-icon edit" title="Update Status" onclick="window.bukaModalStatus('${t.id}','${t.status || ''}','${(t.catatanTeknisi || '').replace(/'/g, "\\'")}')"><i class="fas fa-sync-alt"></i></button>
                        ${canManageTasks ? `<button class="btn-icon del" title="Hapus Tugas" onclick="window.hapusTugas('${t.id}')"><i class="fas fa-trash"></i></button>` : ''}
                    </div>
                </div>`;
            }).join('');
        };

        // --- MODAL BUAT ---
        window.bukaModalBuat = () => {
            if (!canManageTasks) {
                showToast('Akses ditolak. Hanya admin yang dapat membuat tugas.', 'danger');
                return;
            }
            document.getElementById('editId').value = '';
            document.getElementById('inputJudul').value = '';
            document.getElementById('inputDeskripsi').value = '';
            document.getElementById('inputJenis').value = 'pemasangan';
            document.getElementById('inputPrioritas').value = 'normal';
            document.getElementById('inputTeknisi').value = '';
            document.getElementById('inputNamaPelanggan').value = '';
            document.getElementById('inputNoWA').value = '';
            document.getElementById('inputAlamat').value = '';
            document.getElementById('inputDeadline').value = '';
            document.getElementById('modalTugasTitle').innerHTML = '<i class="fas fa-plus-circle" style="color:#3b82f6"></i> Buat Tugas Baru';
            document.getElementById('modalTugas').classList.add('show');
        };

        window.tutupModal = () => document.getElementById('modalTugas').classList.remove('show');

        window.simpanTugas = async () => {
            if (!canManageTasks) {
                showToast('Akses ditolak. Hanya admin yang dapat membuat tugas.', 'danger');
                return;
            }
            const judul = document.getElementById('inputJudul').value.trim();
            const sel = document.getElementById('inputTeknisi');
            const assignRaw = sel.value;
            const assignTo = assignRaw || '__all__';
            const assignToNama = assignTo === '__all__'
                ? 'Semua Teknisi'
                : (sel.options[sel.selectedIndex]?.dataset?.nama || '');
            if (!judul) { showToast('Judul tugas wajib diisi!', 'danger'); return; }

            const btn = document.getElementById('btnSimpanTugas');
            btn.disabled = true; btn.textContent = 'Menyimpan...';
            try {
                await apiFetch('/tugas', {
                    method: 'POST',
                    body: JSON.stringify({
                        judul,
                        deskripsi: document.getElementById('inputDeskripsi').value,
                        jenisTask: document.getElementById('inputJenis').value,
                        prioritas: document.getElementById('inputPrioritas').value,
                        assignTo, assignToNama,
                        namaPelanggan: document.getElementById('inputNamaPelanggan').value,
                        noWA: document.getElementById('inputNoWA').value,
                        alamat: document.getElementById('inputAlamat').value,
                        tglDeadline: document.getElementById('inputDeadline').value
                    })
                });
                showToast('✅ Tugas berhasil dibuat dan dikirim ke teknisi!');
                tutupModal();
                await loadAll();
            } catch (e) {
                showToast('Gagal menyimpan: ' + e.message, 'danger');
            } finally {
                btn.disabled = false; btn.innerHTML = '<i class="fas fa-save"></i> Simpan Tugas';
            }
        };

        // --- MODAL STATUS ---
        window.bukaModalStatus = (id, status, catatan) => {
            document.getElementById('statusEditId').value = id;
            document.getElementById('statusBaru').value = status || 'pending';
            document.getElementById('catatanTeknisi').value = catatan || '';
            document.getElementById('modalStatus').classList.add('show');
        };

        window.tutupModalStatus = () => document.getElementById('modalStatus').classList.remove('show');

        window.simpanStatus = async () => {
            const id = document.getElementById('statusEditId').value;
            const status = document.getElementById('statusBaru').value;
            const catatan = document.getElementById('catatanTeknisi').value;
            try {
                await apiFetch(`/tugas/${id}`, {
                    method: 'PATCH',
                    body: JSON.stringify({ status, catatanTeknisi: catatan })
                });
                showToast('✅ Status tugas berhasil diperbarui!');
                tutupModalStatus();
                await loadAll();
            } catch (e) {
                showToast('Gagal update: ' + e.message, 'danger');
            }
        };

        // --- DELETE ---
        window.hapusTugas = async (id) => {
            if (!confirm('Yakin ingin menghapus tugas ini?')) return;
            try {
                await apiFetch(`/tugas/${id}`, { method: 'DELETE' });
                showToast('Tugas dihapus.', 'warning');
                await loadAll();
            } catch (e) {
                showToast('Gagal hapus: ' + e.message, 'danger');
            }
        };

        // Close modal on overlay click
        document.getElementById('modalTugas').addEventListener('click', e => { if (e.target === e.currentTarget) tutupModal(); });
        document.getElementById('modalStatus').addEventListener('click', e => { if (e.target === e.currentTarget) tutupModalStatus(); });

        // Auth guard & init
        auth.onAuthStateChanged(user => {
            if (!user) { window.location.href("{{ url('/login') }}"); return; }
            const prof = JSON.parse(localStorage.getItem('ss_user') || '{}');
            canManageTasks = hasPermission(prof, 'manage_tasks');
            const btnBuat = document.getElementById('btnBuatTugas');
            if (btnBuat) btnBuat.style.display = canManageTasks ? 'inline-flex' : 'none';
            loadAll();
        });
    </script>
</body>

</html>