<!DOCTYPE html>
<html lang="id">

<head>
    <script src="{{ asset('js/ss-storage-migrate.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Percakapan (Lapangan) - Sans Speed</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <script>
        if (localStorage.getItem('ss_theme') === 'light') document.documentElement.classList.add('light-mode');
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .msg-shell { display: flex; gap: 0; min-height: calc(100vh - 120px); border-radius: 12px; overflow: hidden; border: 1px solid var(--border-color, #334155); background: var(--card-bg, #0f172a); }
        .msg-sidebar { width: 300px; flex-shrink: 0; border-right: 1px solid var(--border-color, #334155); display: flex; flex-direction: column; max-height: 78vh; }
        .msg-sidebar-head { padding: 12px 14px; font-weight: 700; font-size: 13px; color: var(--text-secondary, #94a3b8); text-transform: uppercase; }
        .msg-thread-list { overflow-y: auto; flex: 1; }
        .msg-thread-row { display: flex; align-items: stretch; border-bottom: 1px solid rgba(148,163,184,0.12); }
        .msg-thread-row:hover { background: rgba(59,130,246,0.06); }
        .msg-thread-row.active { background: rgba(59,130,246,0.18); border-left: 3px solid #3b82f6; }
        .msg-thread-main { flex: 1; text-align: left; padding: 12px 14px; border: none; background: transparent; color: inherit; cursor: pointer; min-width: 0; }
        .msg-thread-name { font-weight: 700; font-size: 14px; }
        .msg-thread-sub { font-size: 11px; color: var(--text-secondary, #94a3b8); margin-top: 4px; }
        .msg-tiny { font-size: 10px; color: #64748b; }
        .msg-tiny-warn { color: #f59e0b; }
        .msg-main { flex: 1; display: flex; flex-direction: column; min-width: 0; max-height: 78vh; }
        .msg-banner { padding: 10px 14px; font-size: 13px; line-height: 1.45; border-bottom: 1px solid rgba(148,163,184,0.15); }
        .msg-banner-neutral { background: rgba(100,116,139,0.15); color: #cbd5e1; }
        .msg-banner-me { background: rgba(16,185,129,0.12); color: #6ee7b7; }
        .msg-banner-other { background: rgba(245,158,11,0.12); color: #fcd34d; }
        .msg-note { padding: 8px 12px; font-size: 12px; color: #94a3b8; background: rgba(59,130,246,0.06); border-bottom: 1px solid rgba(148,163,184,0.12); }
        .msg-messages { flex: 1; overflow-y: auto; padding: 14px; display: flex; flex-direction: column; gap: 10px; }
        .msg-bubble-wrap { max-width: 92%; }
        .msg-bubble-wrap.staff { align-self: flex-end; text-align: right; }
        .msg-bubble-wrap.cust { align-self: flex-start; text-align: left; }
        .msg-meta { font-size: 10px; color: #64748b; margin-bottom: 4px; display: block; }
        .msg-bubble { display: inline-flex; align-items: flex-start; gap: 8px; background: rgba(30,41,59,0.9); border-radius: 12px; padding: 10px 12px; text-align: left; border: 1px solid rgba(148,163,184,0.15); }
        .msg-bubble-wrap.staff .msg-bubble { background: hsla(var(--hue, 210), 40%, 22%, 0.95); }
        .msg-bubble-wrap.cust .msg-bubble { background: rgba(51,65,85,0.65); }
        .msg-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; margin-top: 4px; }
        .msg-text { white-space: pre-wrap; word-break: break-word; font-size: 14px; color: #e2e8f0; }
        .msg-time { font-size: 10px; color: #64748b; margin-top: 4px; }
        .msg-composer { display: flex; gap: 8px; padding: 12px; border-top: 1px solid rgba(148,163,184,0.15); }
        .msg-composer textarea { flex: 1; min-height: 44px; max-height: 120px; resize: vertical; border-radius: 10px; padding: 10px; border: 1px solid #475569; background: rgba(15,23,42,0.6); color: inherit; }
        .msg-empty { padding: 20px; color: #64748b; font-size: 13px; }
        body.light-mode .msg-shell { background: #fff; border-color: #e2e8f0; }
        body.light-mode .msg-text { color: #0f172a; }
        @media (max-width: 900px) {
            .msg-shell { flex-direction: column; max-height: none; }
            .msg-sidebar { width: 100%; max-height: 200px; border-right: none; border-bottom: 1px solid #334155; }
            .msg-main { max-height: 65vh; }
        }
    </style>
</head>

<body class="app-body" data-messaging="lapangan">
@include('billing.partials.web-bootstrap')

    <div class="app-container">
        <div class="main-content" style="margin-left:0;">
            <div class="content-wrapper" style="padding:16px;">
                <a href="{{ url('/app-teknisi') }}" style="display:inline-block;margin-bottom:12px;color:#3b82f6;font-size:14px;"><i class="fas fa-arrow-left"></i> Kembali</a>
                <div style="margin-bottom: 18px;">
                    <h1 class="page-title" style="display:flex; align-items:center; gap:10px;">
                        <i class="fas fa-hard-hat" style="color:#f59e0b;"></i> Percakapan (lapangan)
                    </h1>
                    <p style="color: var(--text-secondary, #94a3b8); font-size: 14px; margin: 0;">
                        Hanya percakapan yang di-<strong>publikasikan</strong> dari kantor dengan jadwal waktu. Setelah waktu habis, thread hilang dari daftar ini.
                    </p>
                </div>
                <div class="msg-shell" id="msgDetail">
                    <div class="msg-sidebar">
                        <div class="msg-sidebar-head">Publik dari kantor</div>
                        <div class="msg-thread-list" id="msgThreadList">
                            <div class="msg-empty"><i class="fas fa-spinner fa-spin"></i> Memuat...</div>
                        </div>
                    </div>
                    <div class="msg-main">
                        <div class="msg-note" id="msgLapNote">
                            Jika admin menonaktifkan balasan lapangan, Anda hanya dapat membaca.
                        </div>
                        <div class="msg-banner msg-banner-neutral" id="msgAssignBanner"> Pilih thread di kiri. </div>
                        <div class="msg-messages" id="msgMessages"></div>
                        <div class="msg-composer">
                            <textarea id="msgInput" placeholder="Balasan (jika diizinkan)…"></textarea>
                            <button type="button" class="btn-primary" id="msgBtnSend" style="align-self:flex-end;padding:12px 18px;"><i class="fas fa-paper-plane"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="module">
        import { guardChatLapangan } from './js/utils/role-guard-chat-lapangan.js';
        import { initMessagingPelangganPage } from './js/pages/messaging-pelanggan.js';
        if (guardChatLapangan()) initMessagingPelangganPage();
    </script>
</body>

</html>
