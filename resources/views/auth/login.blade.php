<!DOCTYPE html>
<html lang="id">

<head>
    <script src="{{ asset('js/ss-storage-migrate.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#0f172a">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Sans Speed</title>
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('{{ url('/sw.js') }}').then(r => r.update()).catch(() => {});
            });
            caches.keys().then((names) => {
                const legacy = ['cnet-portal-v1', 'cnet-portal-v4', 'SS-portal-v1', 'SS-portal-v4', 'sans-speed-portal-v4'];
                for (const name of names) {
                    if (legacy.includes(name) || /^cnet-portal-/.test(name) || /^SS-portal-/.test(name)) {
                        caches.delete(name);
                    }
                }
            });
        }
    </script>
    <style>
        html,
        body {
            width: 100%;
            overflow-x: hidden;
        }

        body.login-page {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            min-height: 100dvh;
            padding: max(16px, env(safe-area-inset-top, 0px)) max(16px, env(safe-area-inset-right, 0px)) max(16px, env(safe-area-inset-bottom, 0px)) max(16px, env(safe-area-inset-left, 0px));
            box-sizing: border-box;
        }

        .bg-shape {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: -1;
            animation: float 10s infinite ease-in-out;
        }

        .shape-1 {
            width: 400px;
            height: 400px;
            background: rgba(59, 130, 246, 0.3);
            top: -100px;
            left: -100px;
        }

        .shape-2 {
            width: 500px;
            height: 500px;
            background: rgba(139, 92, 246, 0.2);
            bottom: -150px;
            right: -100px;
            animation-delay: -5s;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) scale(1);
            }

            50% {
                transform: translateY(30px) scale(1.05);
            }
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            z-index: 10;
        }

        .login-card {
            background: rgba(30, 41, 59, 0.6);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .staff-shortcut {
            position: absolute;
            top: 18px;
            right: 18px;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(15, 23, 42, 0.35);
            color: #e2e8f0;
        }

        .login-theme-btn {
            position: absolute;
            top: 18px;
            left: 18px;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(15, 23, 42, 0.35);
            color: #e2e8f0;
        }

        .login-header {
            position: relative;
            text-align: center;
            margin-bottom: 24px;
        }

        .login-logo {
            font-size: 2.5rem;
            color: #60a5fa;
            margin-bottom: 8px;
        }

        .login-logo img {
            max-height: 56px;
            max-width: 200px;
            object-fit: contain;
        }

        .form-section {
            display: none;
        }

        .form-section.active {
            display: block;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .input-group {
            display: flex;
            align-items: center;
            gap: 10px;
            background: rgba(15, 23, 42, 0.5);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 4px 12px;
        }

        .input-group input {
            flex: 1;
            border: none;
            background: transparent;
            color: var(--text-primary);
            padding: 12px 0;
        }

        .btn-primary {
            width: 100%;
            padding: 14px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            font-weight: 600;
            margin-top: 8px;
        }

        .remember-me-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 12px 0;
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .form-error {
            color: #fb7185;
            margin-top: 10px;
            font-size: 0.9rem;
            display: none;
        }

        .form-error.show {
            display: block;
        }

        .portal-session-hint {
            display: none;
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-bottom: 16px;
            padding: 10px;
            border-radius: 10px;
            background: rgba(59, 130, 246, 0.12);
        }

        #toast {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%) translateY(120px);
            padding: 12px 20px;
            border-radius: 12px;
            color: white;
            z-index: 9999;
            opacity: 0;
            transition: 0.3s;
        }

        #toast.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
    </style>
</head>

<body class="login-page">
    <script>
        if (localStorage.getItem('ss_theme') === 'light') {
            document.documentElement.classList.add('light-mode');
            document.body.classList.add('light-mode');
        }
    </script>
    <div id="toast" style="background: rgba(244, 63, 94, 0.9);">
        <i class="fas fa-exclamation-circle"></i>
        <span id="toastMessage"></span>
    </div>
    <div class="bg-shape shape-1"></div>
    <div class="bg-shape shape-2"></div>
    <div class="login-wrapper">
        <div class="login-card" style="position:relative">
            <div class="login-header">
                <button type="button" id="loginThemeBtn" class="login-theme-btn" onclick="toggleLoginTheme()" title="Mode terang/gelap"><i class="fas fa-sun"></i></button>
                <button type="button" id="staffShortcutBtn" class="staff-shortcut" onclick="toggleStaffShortcut(this)" title="Akses staf"><i class="fas fa-user-shield"></i></button>
                <div class="login-logo" id="loginLogoMark">
                    @if ($sidebarLogo)
                        <img src="{{ $sidebarLogo }}" alt="Logo">
                    @else
                        <i class="fas fa-wifi"></i>
                    @endif
                </div>
                <h1 style="margin:0;font-size:1.5rem">Sans Speed</h1>
                <p style="color:var(--text-secondary);margin:8px 0 0;font-size:0.85rem">THE BEST YOUR CONNECTION</p>
            </div>
            <div id="portalSessionHint" class="portal-session-hint">
                Sesi <strong>portal pelanggan</strong> masih tersimpan.
                <a href="{{ url('/portal-pelanggan') }}">Buka portal</a>
            </div>
            <div id="form-staff" class="form-section">
                <form method="post" action="{{ route('login.staff') }}">
                    @csrf
                    <div class="form-group">
                        <label for="email">Email Login</label>
                        <div class="input-group">
                            <i class="fas fa-user-astronaut"></i>
                            <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="password">Kata Sandi</label>
                        <div class="input-group">
                            <i class="fas fa-fingerprint"></i>
                            <input type="password" id="password" name="password" required autocomplete="current-password">
                        </div>
                    </div>
                    <label class="remember-me-row">
                        <input type="checkbox" name="remember" value="1" checked>
                        <span>Ingat perangkat ini</span>
                    </label>
                    <button type="submit" class="btn-primary">Masuk sistem <i class="fas fa-rocket" style="margin-left:8px"></i></button>
                    @error('email')
                        <div class="form-error show">{{ $message }}</div>
                    @enderror
                </form>
            </div>
            <div id="form-customer" class="form-section active">
                <form method="post" action="{{ route('login.portal') }}">
                    @csrf
                    <div class="form-group">
                        <label for="pelId">Client ID / PPOE ID / No. WA</label>
                        <div class="input-group">
                            <i class="fas fa-id-card-clip"></i>
                            <input type="text" id="pelId" name="pelId" value="{{ old('pelId') }}" required autocomplete="username">
                        </div>
                    </div>
                    <button type="submit" class="btn-primary" style="background:linear-gradient(135deg,var(--success),#059669)">Cek status <i class="fas fa-magnifying-glass" style="margin-left:8px"></i></button>
                    <label class="remember-me-row">
                        <input type="checkbox" name="remember" value="1" checked>
                        <span>Ingat perangkat ini</span>
                    </label>
                    @error('pelId')
                        <div class="form-error show">{{ $message }}</div>
                    @enderror
                    <div class="forgot-pass" style="margin-top:16px;text-align:center">
                        <a id="supportWaLink" href="{{ $waUrl }}" target="_blank" rel="noopener" style="color:var(--primary)"><i class="fab fa-whatsapp"></i> Hubungi pusat dukungan</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function toggleLoginTheme() {
            const next = !document.body.classList.contains('light-mode');
            document.documentElement.classList.toggle('light-mode', next);
            document.body.classList.toggle('light-mode', next);
            localStorage.setItem('ss_theme', next ? 'light' : 'dark');
            const btn = document.getElementById('loginThemeBtn');
            if (btn) btn.innerHTML = next ? '<i class="fas fa-moon"></i>' : '<i class="fas fa-sun"></i>';
        }

        function switchRole(role) {
            document.querySelectorAll('.form-section').forEach(f => f.classList.remove('active'));
            document.getElementById('form-' + role).classList.add('active');
            const b = document.getElementById('staffShortcutBtn');
            if (b) {
                const staff = role === 'staff';
                b.classList.toggle('active', staff);
                b.innerHTML = staff ? '<i class="fas fa-user"></i>' : '<i class="fas fa-user-shield"></i>';
            }
        }

        function toggleStaffShortcut() {
            const staffOn = document.getElementById('form-staff').classList.contains('active');
            switchRole(staffOn ? 'customer' : 'staff');
        }
        @if (session('info'))
            (function() {
                const t = document.getElementById('toast');
                document.getElementById('toastMessage').textContent = @json(session('info'));
                t.style.background = 'rgba(59,130,246,0.95)';
                t.classList.add('show');
                setTimeout(() => t.classList.remove('show'), 4000);
            })();
        @endif
        (function() {
            const cust = localStorage.getItem('ss_customer') || sessionStorage.getItem('ss_customer');
            const staffTok = localStorage.getItem('ss_token') || sessionStorage.getItem('ss_token');
            if (cust && !staffTok) document.getElementById('portalSessionHint').style.display = 'block';
        })();
    </script>
</body>

</html>
