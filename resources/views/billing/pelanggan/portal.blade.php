<!DOCTYPE html>
<html lang="id">

<head>
    <script src="{{ asset('js/ss-storage-migrate.js') }}"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Portal Sans Speed</title>
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('sw.js').catch(err => console.log('SW Reg failed:', err));
            });
        }
        // Force light mode for better readability on basic phones or follow system preference? Let's follow theme or default dark.
        if (localStorage.getItem('ss_theme') === 'light') { document.documentElement.classList.add('light-mode'); }
    </script>
    <style>
        :root {
            --portal-bg: radial-gradient(1000px 560px at 8% -10%, #ef1f2d30, transparent 52%),
                radial-gradient(860px 520px at 100% 0%, #a0131f36, transparent 44%),
                linear-gradient(180deg, #060606 0%, #121212 100%);
            --panel: rgba(18, 18, 20, 0.76);
            --panel-border: rgba(255, 255, 255, 0.16);
            --text-main: #fafafa;
            --text-soft: #b6b8be;
            --primary-glow: #ef1f2d;
            --secondary-glow: #9f0f19;
            --danger-glow: #fb7185;
            --success-glow: #34d399;
            --shadow-lg: 0 20px 45px rgba(0, 0, 0, 0.45);
        }

        html.light-mode {
            --portal-bg: radial-gradient(1000px 560px at 8% -10%, #ef1f2d1f, transparent 52%),
                radial-gradient(900px 500px at 100% 0%, #dc262622, transparent 45%),
                linear-gradient(180deg, #f7f7f7 0%, #ececec 100%);
            --panel: rgba(255, 255, 255, 0.86);
            --panel-border: rgba(30, 30, 35, 0.16);
            --text-main: #141414;
            --text-soft: #5f6168;
            --shadow-lg: 0 18px 35px rgba(17, 17, 20, 0.15);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            padding-bottom: 80px;
            color: var(--text-main);
            background: var(--portal-bg);
            position: relative;
            overflow-x: hidden;
        }

        body::before,
        body::after {
            content: "";
            position: fixed;
            inset: auto;
            border-radius: 50%;
            filter: blur(40px);
            pointer-events: none;
            z-index: 0;
        }

        body::before {
            width: 240px;
            height: 240px;
            top: 90px;
            right: -60px;
            background: #ef1f2d3b;
        }

        body::after {
            width: 220px;
            height: 220px;
            bottom: 50px;
            left: -40px;
            background: #a0131f3b;
        }

        .portal-container {
            max-width: 560px;
            margin: 0 auto;
            min-height: 100vh;
            position: relative;
            z-index: 1;
            background: linear-gradient(160deg, rgba(255, 255, 255, 0.02), rgba(255, 255, 255, 0));
            backdrop-filter: blur(2px);
        }

        .portal-header {
            margin: 14px 14px 16px;
            padding: 16px 16px;
            border-radius: 18px;
            border: 1px solid var(--panel-border);
            background: var(--panel);
            backdrop-filter: blur(14px);
            box-shadow: var(--shadow-lg);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            animation: slideDown 0.45s ease;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .user-greeting {
            display: flex;
            align-items: center;
            gap: 12px;
            min-width: 0;
        }

        .avatar-circle {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--primary-glow), var(--secondary-glow));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            color: #fff;
            box-shadow: 0 10px 20px rgba(239, 31, 45, 0.34);
            flex-shrink: 0;
        }

        .greet-text h2 {
            font-size: 18px;
            font-weight: 800;
            margin: 0;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 220px;
        }

        .greet-text p {
            font-size: 12px;
            color: var(--text-soft);
            margin: 2px 0 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 220px;
        }

        .btn-logout {
            background: rgba(244, 63, 94, 0.14);
            color: #fb7185;
            border: 1px solid rgba(244, 63, 94, 0.25);
            padding: 9px 12px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: transform .2s ease, box-shadow .2s ease, filter .2s ease;
            white-space: nowrap;
        }

        .btn-theme {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            border: 1px solid var(--panel-border);
            background: linear-gradient(135deg, rgba(239, 31, 45, .2), rgba(120, 16, 23, .14));
            color: #ffd4d8;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform .2s ease, filter .2s ease, box-shadow .2s ease;
        }

        html.light-mode .btn-theme {
            color: #7f0f19;
        }

        .btn-theme:hover {
            transform: translateY(-1px);
            filter: brightness(1.08);
            box-shadow: 0 8px 18px rgba(239, 31, 45, .2);
        }

        .btn-logout:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 18px rgba(244, 63, 94, .2);
            filter: brightness(1.08);
        }

        .portal-content {
            padding: 0 14px 24px;
            display: grid;
            gap: 16px;
        }

        .announcement-box {
            background: linear-gradient(135deg, rgba(239, 31, 45, 0.2), rgba(120, 16, 23, 0.15));
            border: 1px solid rgba(239, 31, 45, 0.3);
            border-radius: 14px;
            padding: 11px 12px;
            display: none;
            align-items: center;
            gap: 10px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(120, 16, 23, .18);
        }

        .announcement-icon {
            color: #fecdd3;
            animation: pulse 1.9s ease-in-out infinite;
            flex-shrink: 0;
        }

        .marquee-container {
            overflow: hidden;
            white-space: nowrap;
            width: 100%;
        }

        .marquee-content {
            display: inline-block;
            font-size: 13px;
            font-weight: 600;
            color: #ffe4e6;
            animation: marquee 14s linear infinite;
        }

        .bill-card {
            background: linear-gradient(165deg, rgba(30, 30, 32, 0.95), rgba(12, 12, 12, 0.92));
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 22px;
            padding: 20px;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
            transform-style: preserve-3d;
            animation: riseIn 0.5s ease;
        }

        html.light-mode .bill-card {
            background: linear-gradient(165deg, rgba(255, 255, 255, 0.95), rgba(245, 245, 245, 0.92));
        }

        .bill-card::before {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-glow), var(--secondary-glow));
        }

        .bill-card::after {
            content: '';
            position: absolute;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            right: -100px;
            top: -120px;
            background: radial-gradient(circle, rgba(239, 31, 45, .22), transparent 70%);
            pointer-events: none;
        }

        .bill-ribbon {
            position: absolute;
            top: 0;
            left: 0;
            width: 42%;
            height: 100%;
            background: linear-gradient(160deg, rgba(239, 31, 45, .92), rgba(136, 10, 20, .92));
            clip-path: polygon(0 0, 100% 0, 58% 100%, 0 100%);
            opacity: .18;
            pointer-events: none;
        }

        .bill-accent {
            position: absolute;
            right: 0;
            bottom: 0;
            width: 115px;
            height: 86px;
            background: linear-gradient(325deg, rgba(255, 255, 255, .92), rgba(242, 242, 242, .2));
            clip-path: polygon(100% 0, 100% 100%, 0 100%);
            opacity: .2;
            pointer-events: none;
        }

        html.light-mode .bill-accent {
            background: linear-gradient(325deg, rgba(239, 31, 45, .2), rgba(239, 31, 45, .04));
            opacity: .45;
        }

        .bill-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            gap: 8px;
            position: relative;
            z-index: 2;
        }

        .bill-header-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-nota {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            border: 1px solid rgba(148, 163, 184, 0.25);
            background: rgba(15, 23, 42, 0.45);
            color: #e2e8f0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform .2s ease, filter .2s ease;
        }

        .btn-nota:hover {
            transform: translateY(-1px);
            filter: brightness(1.08);
        }

        html.light-mode .btn-nota {
            background: rgba(255, 255, 255, 0.82);
            color: #334155;
            border-color: rgba(100, 116, 139, 0.25);
        }

        .bill-month {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-soft);
            text-transform: uppercase;
            letter-spacing: .12em;
        }

        .badge {
            padding: 9px 16px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 900;
            letter-spacing: .06em;
            line-height: 1;
        }

        .badge-success {
            background: rgba(16, 185, 129, 0.18);
            color: #34d399;
        }

        .badge-danger {
            background: rgba(239, 31, 45, 0.22);
            color: #ffc2c9;
            font-size: 15px;
            padding: 11px 22px;
            min-width: 150px;
            text-align: center;
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.18);
            color: #fbbf24;
        }

        html.light-mode .badge-success {
            background: rgba(16, 185, 129, 0.22);
            color: #047857;
            border: 1px solid rgba(16, 185, 129, 0.35);
        }

        html.light-mode .badge-danger {
            background: rgba(239, 31, 45, 0.18);
            color: #9f0f19;
            border: 1px solid rgba(239, 31, 45, 0.34);
            font-size: 15px;
            padding: 11px 22px;
            min-width: 150px;
            text-align: center;
        }

        html.light-mode .badge-warning {
            background: rgba(245, 158, 11, 0.2);
            color: #b45309;
            border: 1px solid rgba(245, 158, 11, 0.34);
        }

        .bill-amount {
            font-size: clamp(32px, 8.2vw, 44px);
            font-weight: 900;
            margin-bottom: 4px;
            letter-spacing: -0.03em;
            color: #fff;
            text-shadow: 0 6px 16px rgba(2, 6, 23, .25);
            position: relative;
            z-index: 2;
        }

        html.light-mode .bill-amount {
            color: #0f172a;
            text-shadow: none;
        }

        .bill-due {
            font-size: 13px;
            color: var(--text-soft);
            margin-bottom: 16px;
            position: relative;
            z-index: 2;
        }

        .bill-due span {
            color: var(--danger-glow);
            font-weight: 700;
        }

        .bill-details {
            background: rgba(2, 6, 23, 0.26);
            border: 1px solid rgba(148, 163, 184, 0.16);
            border-radius: 14px;
            padding: 14px;
            backdrop-filter: blur(6px);
            position: relative;
            z-index: 2;
        }

        .billing-schedule-note {
            margin-top: 12px;
            background: rgba(15, 23, 42, 0.34);
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 12px;
            padding: 10px 12px;
            font-size: 12px;
            color: #cbd5e1;
            line-height: 1.5;
            position: relative;
            z-index: 2;
        }

        .billing-schedule-note strong {
            color: #f8fafc;
            font-weight: 700;
        }

        html.light-mode .billing-schedule-note {
            background: rgba(255, 255, 255, 0.72);
            color: #475569;
            border-color: rgba(100, 116, 139, 0.22);
        }

        html.light-mode .billing-schedule-note strong {
            color: #0f172a;
        }

        html.light-mode .bill-details {
            background: rgba(255, 255, 255, 0.65);
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            font-size: 13px;
            gap: 12px;
        }

        .detail-row:last-child {
            margin-bottom: 0;
        }

        .detail-label {
            color: var(--text-soft);
        }

        .detail-value {
            font-weight: 700;
            color: #e2e8f0;
            text-align: right;
        }

        html.light-mode .detail-value {
            color: #1f2937;
        }

        .action-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .promo-package-section {
            background: linear-gradient(165deg, rgba(30, 30, 32, 0.95), rgba(12, 12, 12, 0.92));
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 20px;
            padding: 16px;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }

        .promo-package-section::before {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary-glow), var(--secondary-glow));
        }

        html.light-mode .promo-package-section {
            background: linear-gradient(165deg, rgba(255, 255, 255, 0.95), rgba(245, 245, 245, 0.92));
        }

        .promo-package-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 10px;
        }

        .promo-package-title {
            margin: 0;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: .02em;
        }

        .promo-chip {
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            color: #ffd3d7;
            background: rgba(239, 31, 45, 0.18);
            border: 1px solid rgba(239, 31, 45, 0.35);
            white-space: nowrap;
        }

        html.light-mode .promo-chip {
            color: #9f0f19;
            background: rgba(239, 31, 45, 0.13);
        }

        .promo-package-copy {
            margin: 0 0 12px;
            font-size: 12px;
            color: var(--text-soft);
            line-height: 1.55;
        }

        .package-list {
            display: grid;
            gap: 10px;
        }

        .package-item {
            border-radius: 14px;
            border: 1px solid rgba(148, 163, 184, 0.18);
            background: rgba(2, 6, 23, 0.28);
            padding: 12px;
        }

        html.light-mode .package-item {
            background: rgba(255, 255, 255, 0.72);
        }

        .package-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 10px;
        }

        .package-name {
            margin: 0;
            font-size: 14px;
            font-weight: 800;
            color: var(--text-main);
        }

        .package-name-wrap {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .package-badge {
            font-size: 10px;
            font-weight: 800;
            padding: 4px 8px;
            border-radius: 999px;
            color: #9f0f19;
            background: rgba(251, 191, 36, 0.22);
            border: 1px solid rgba(251, 191, 36, 0.45);
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .package-price {
            font-size: 13px;
            font-weight: 900;
            color: #34d399;
            white-space: nowrap;
        }

        .package-desc {
            margin: 6px 0 0;
            font-size: 12px;
            color: var(--text-soft);
            line-height: 1.5;
        }

        .package-cta {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 10px;
            padding: 7px 10px;
            border-radius: 9px;
            font-size: 11px;
            font-weight: 800;
            text-decoration: none;
            color: #ffd3d7;
            background: rgba(239, 31, 45, 0.16);
            border: 1px solid rgba(239, 31, 45, 0.35);
        }

        html.light-mode .package-cta {
            color: #9f0f19;
            background: rgba(239, 31, 45, 0.11);
        }

        .package-empty {
            font-size: 12px;
            color: var(--text-soft);
            border: 1px dashed rgba(148, 163, 184, 0.28);
            border-radius: 10px;
            padding: 10px;
            text-align: center;
        }

        .action-btn {
            border-radius: 16px;
            padding: 16px 12px;
            text-align: center;
            text-decoration: none;
            transition: transform .25s ease, box-shadow .25s ease, filter .25s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            position: relative;
            overflow: hidden;
            border: 1px solid var(--panel-border);
            backdrop-filter: blur(10px);
            box-shadow: 0 10px 22px rgba(2, 6, 23, .2);
        }

        .action-btn::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(120deg, transparent 30%, rgba(255, 255, 255, .12) 50%, transparent 70%);
            transform: translateX(-120%);
            transition: transform .6s ease;
            pointer-events: none;
        }

        .action-btn:hover {
            transform: translateY(-3px);
            filter: brightness(1.06);
        }

        .action-btn:hover::after {
            transform: translateX(120%);
        }

        .action-btn i {
            font-size: 21px;
        }

        .action-btn span {
            font-size: 13px;
            font-weight: 700;
        }

        .btn-pay {
            background: linear-gradient(135deg, rgba(239, 31, 45, 0.22), rgba(120, 16, 23, 0.2));
            color: #ffd3d7;
        }

        .btn-cs {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.12), rgba(128, 128, 128, 0.12));
            color: #f4f4f5;
        }

        html.light-mode .btn-cs {
            color: #2f3136;
        }

        html.light-mode .btn-pay {
            background: linear-gradient(135deg, rgba(239, 31, 45, 0.2), rgba(239, 31, 45, 0.1));
            color: #7f0f19;
            border: 1px solid rgba(239, 31, 45, 0.3);
        }

        .loader-box {
            text-align: center;
            padding: 32px 20px;
            color: var(--text-soft);
            background: var(--panel);
            border: 1px solid var(--panel-border);
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
        }

        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: all .3s ease;
            padding: 14px;
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal-content {
            width: 100%;
            max-width: 430px;
            background: var(--panel);
            border-radius: 18px;
            border: 1px solid var(--panel-border);
            box-shadow: var(--shadow-lg);
            transform: scale(.94) translateY(18px);
            transition: all .3s ease;
            overflow: hidden;
        }

        .modal-overlay.active .modal-content {
            transform: scale(1) translateY(0);
        }

        .modal-header {
            padding: 16px;
            border-bottom: 1px solid var(--panel-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, rgba(239, 31, 45, .2), rgba(120, 16, 23, .12));
        }

        .modal-title {
            margin: 0;
            font-size: 16px;
            font-weight: 800;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .close-btn {
            background: none;
            border: none;
            color: var(--text-soft);
            font-size: 18px;
            cursor: pointer;
        }

        .bank-card {
            background: rgba(15, 23, 42, 0.45);
            border: 1px solid var(--panel-border);
            border-radius: 12px;
            padding: 14px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        html.light-mode .bank-card {
            background: rgba(255, 255, 255, 0.72);
        }

        .bank-logo {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: #0f172a;
            font-size: 12px;
        }

        .modal-body {
            padding: 18px;
        }

        .modal-intro {
            font-size: 13px;
            color: var(--text-soft);
            margin: 0 0 16px;
            line-height: 1.55;
        }

        .modal-loading {
            text-align: center;
            padding: 20px;
            color: var(--text-soft);
        }

        .modal-note {
            font-size: 12px;
            color: #fb7185;
            background: rgba(244, 63, 94, 0.14);
            padding: 10px;
            border-radius: 10px;
            margin: 14px 0 0;
        }

        html.light-mode .modal-note {
            color: #9f1239;
            background: rgba(239, 31, 45, 0.12);
        }

        .modal-footer {
            padding: 14px 18px;
            border-top: 1px solid var(--panel-border);
            text-align: center;
            background: rgba(15, 23, 42, 0.22);
        }

        html.light-mode .modal-footer {
            background: rgba(255, 255, 255, 0.44);
        }

        .btn-wa-confirm {
            display: inline-block;
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            font-size: 13px;
            color: #fff;
            background: linear-gradient(135deg, #ef1f2d, #9f0f19);
            box-shadow: 0 8px 16px rgba(239, 31, 45, .28);
        }

        .bank-meta {
            font-size: 12px;
            color: var(--text-soft);
        }

        .bank-number {
            font-size: 16px;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: .04em;
            margin: 4px 0;
        }

        .bank-owner {
            font-size: 12px;
            color: var(--success-glow);
        }

        html.light-mode .announcement-box {
            background: linear-gradient(135deg, rgba(239, 31, 45, 0.16), rgba(239, 31, 45, 0.08));
            border-color: rgba(239, 31, 45, 0.28);
        }

        html.light-mode .announcement-icon {
            color: #be123c;
        }

        html.light-mode .marquee-content {
            color: #9f1239;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.12);
            }
        }

        @keyframes marquee {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes riseIn {
            from {
                opacity: 0;
                transform: translateY(14px) scale(.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @media (max-width: 420px) {
            .portal-header {
                padding: 14px;
            }

            .greet-text h2,
            .greet-text p {
                max-width: 160px;
            }

            .bill-card {
                padding: 16px;
                border-radius: 18px;
            }

            .action-btn {
                border-radius: 14px;
                padding: 14px 10px;
            }
        }
    </style>
</head>

<body>
@include('billing.partials.web-bootstrap')

    <div class="portal-container">

        <!-- Header -->
        <header class="portal-header">
            <div class="user-greeting">
                <div class="avatar-circle">
                    <i class="fas fa-user-astronaut"></i>
                </div>
                <div class="greet-text">
                    <h2>Halo, <span id="uiCustomerName">Pelanggan</span></h2>
                    <p id="uiCustomerArea">Memuat Info...</p>
                </div>
            </div>
            <div class="header-actions">
                <button id="themeToggleBtn" class="btn-theme" onclick="togglePortalTheme()"
                    aria-label="Ganti mode siang malam">
                    <i class="fas fa-moon"></i>
                </button>
                <button class="btn-logout" onclick="logout()">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </button>
            </div>
        </header>

        <div class="portal-content">

            <!-- Announcement Board -->
            <div id="announcementBox" class="announcement-box">
                <i class="fas fa-bell announcement-icon"></i>
                <div class="marquee-container">
                    <span class="marquee-content" id="marqueeText">Tidak ada pengumuman hari ini.</span>
                </div>
            </div>

            <!-- Bill Output -->
            <div id="billContainer">
                <div class="loader-box">
                    <i class="fas fa-circle-notch fa-spin fa-2x"
                        style="color:var(--primary-glow); margin-bottom:10px;"></i>
                    <p>Mengecek data tagihan...</p>
                </div>
            </div>

            <!-- Chat dengan kantor (internal, bukan WhatsApp) -->
            <section id="portalChatSection" class="portal-chat-section" style="margin-top:20px;padding:16px;border-radius:16px;border:1px solid var(--panel-border);background:var(--panel);">
                <h3 style="margin:0 0 12px;font-size:17px;display:flex;align-items:center;gap:8px;color:var(--text-main);">
                    <i class="fas fa-comments" style="color:var(--primary-glow);"></i> Chat dengan kantor
                </h3>
                <p style="font-size:12px;color:var(--text-soft);margin:0 0 10px;">Percakapan dengan tim layanan. Anda melihat balasan atas nama perusahaan.</p>
                <div id="portalChatMessages" style="max-height:220px;overflow-y:auto;display:flex;flex-direction:column;gap:10px;margin-bottom:12px;"></div>
                <div style="display:flex;gap:8px;align-items:flex-end;">
                    <textarea id="portalChatInput" rows="2" placeholder="Tulis pesan…" style="flex:1;border-radius:12px;padding:10px;border:1px solid rgba(255,255,255,0.12);background:rgba(0,0,0,0.2);color:inherit;resize:vertical;min-height:44px;"></textarea>
                    <button type="button" id="portalChatSend" class="btn-theme" style="padding:12px 16px;border-radius:12px;border:none;background:var(--primary-glow);color:#fff;font-weight:600;"><i class="fas fa-paper-plane"></i></button>
                </div>
            </section>

            <!-- Promo & Daftar Paket -->
            <section class="promo-package-section">
                <div class="promo-package-head">
                    <h3 class="promo-package-title"><i class="fas fa-bullhorn"></i> Promo & Daftar Paket</h3>
                    <span class="promo-chip">Update terbaru</span>
                </div>
                <p id="promoPackageText" class="promo-package-copy">Memuat promo paket internet...</p>
                <div id="promoPackageList" class="package-list">
                    <div class="package-empty"><i class="fas fa-circle-notch fa-spin"></i> Memuat daftar paket...</div>
                </div>
            </section>

            <!-- Menus -->
            <div class="action-grid" style="margin-top: 24px;">
                <a href="#" class="action-btn btn-pay" onclick="bukaInfoBayar()">
                    <i class="fas fa-wallet"></i>
                    <span>Cara Pembayaran</span>
                </a>
                <a href="https://wa.me/628123456789" target="_blank" class="action-btn btn-cs">
                    <i class="fab fa-whatsapp"></i>
                    <span>Hubungi CS</span>
                </a>
            </div>

        </div>
    </div>

    <!-- Modal Informasi Pembayaran -->
    <div class="modal-overlay" id="modalBayar">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fas fa-money-check-alt" style="color:var(--primary-glow);"></i>
                    Informasi
                    Pembayaran</h3>
                <button class="close-btn" onclick="tutupInfoBayar()"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body">
                <p class="modal-intro">
                    Silakan lakukan transfer sesuai dengan total tagihan bulan ini ke salah satu rekening resmi Sans Speed di
                    bawah:
                </p>

                <div id="dynamicPaymentInfo">
                    <div class="modal-loading">
                        <i class="fas fa-spinner fa-spin"></i> Memuat informasi rekening...
                    </div>
                </div>

                <p class="modal-note">
                    <i class="fas fa-exclamation-circle"></i> Setelah transfer, wajib kirim bukti Struk/Screenshot ke
                    WhatsApp CS agar status tagihan bisa diupdate oleh Admin.
                </p>
            </div>
            <div class="modal-footer">
                <a href="#" id="btnWaKonfirmasi" target="_blank" class="btn-wa-confirm">
                    <i class="fab fa-whatsapp"></i> Konfirmasi Pembayaran
                </a>
            </div>
        </div>
    </div>

    <!-- Modal Tripay -->
    <div class="modal-overlay" id="modalTripay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title"><i class="fas fa-credit-card" style="color:#34d399;"></i> Bayar Online</h3>
                <button class="close-btn" onclick="tutupTripayModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
                <p class="modal-intro">Pilih metode pembayaran (Otomatis dicek sistem 24/7):</p>
                <div id="tripayChannelsContainer">
                    <div class="modal-loading">
                        <i class="fas fa-spinner fa-spin"></i> Memuat metode pembayaran...
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background: rgba(15, 23, 42, 0.45); font-size:11px; color:#94a3b8; display:flex; justify-content:space-between; align-items:center;">
                <span>Secured by Tripay</span>
                <i class="fas fa-shield-alt"></i>
            </div>
        </div>
    </div>

    <!-- Script Application -->
    <script>
        async function loadPaymentInfo() {
            const container = document.getElementById('dynamicPaymentInfo');
            const btnWa = document.getElementById('btnWaKonfirmasi');
            try {
                const res = await fetch('/api/pengaturan').then(r => r.json());
                if (res.success && res.data) {
                    const d = res.data;
                    let html = '';

                    let dynamicAccounts = [];
                    try {
                        dynamicAccounts = JSON.parse(d.payment_accounts || '[]');
                    } catch {
                        dynamicAccounts = [];
                    }

                    if (Array.isArray(dynamicAccounts) && dynamicAccounts.length > 0) {
                        const visible = dynamicAccounts.filter(a => a && a.tampilkan !== false && (a.nomor || a.namaPemilik || a.tipe));
                        html += visible.map(acc => `
                        <div class="bank-card">
                            <div class="bank-logo" style="color: #1f2937;">${(acc.tipe || 'PAY').substring(0, 3).toUpperCase()}</div>
                            <div>
                                <div class="bank-meta">${acc.tipe || 'Metode Pembayaran'}</div>
                                <div class="bank-number">${acc.nomor || '-'}</div>
                                <div class="bank-owner"><i class="fas fa-user-check"></i> a.n. ${acc.namaPemilik || '-'}</div>
                            </div>
                        </div>`).join('');
                    } else {
                        // Legacy fallback
                        if (d.payment_bca_no) {
                            html += `
                            <div class="bank-card">
                                <div class="bank-logo" style="color: #0066AE;">BCA</div>
                                <div>
                                    <div class="bank-meta">Bank Central Asia</div>
                                    <div class="bank-number">${d.payment_bca_no}</div>
                                    <div class="bank-owner"><i class="fas fa-user-check"></i> a.n. ${d.payment_bca_nama || '-'}</div>
                                </div>
                            </div>`;
                        }
                        if (d.payment_mdr_no) {
                            html += `
                            <div class="bank-card">
                                <div class="bank-logo" style="color: #f59e0b;">MDR</div>
                                <div>
                                    <div class="bank-meta">Bank Mandiri</div>
                                    <div class="bank-number">${d.payment_mdr_no}</div>
                                    <div class="bank-owner"><i class="fas fa-user-check"></i> a.n. ${d.payment_mdr_nama || '-'}</div>
                                </div>
                            </div>`;
                        }
                        if (d.payment_dana_no) {
                            html += `
                            <div class="bank-card" style="margin-bottom:0;">
                                <div class="bank-logo" style="background:#00A859; color:white;">DANA</div>
                                <div>
                                    <div class="bank-meta">E-Wallet DANA / OVO / GoPay</div>
                                    <div class="bank-number">${d.payment_dana_no}</div>
                                    <div class="bank-owner"><i class="fas fa-user-check"></i> a.n. ${d.payment_dana_nama || '-'}</div>
                                </div>
                            </div>`;
                        }
                    }

                    if (html === '') {
                        html = '<div class="modal-loading">Belum ada informasi rekening pembayaran. Silakan hubungi admin.</div>';
                    }
                    container.innerHTML = html;

                    // WA CS
                    if (d.payment_wa_cs) {
                        let waClean = d.payment_wa_cs.replace(/\D/g, '');
                        if (waClean.startsWith('0')) waClean = '62' + waClean.substring(1);
                        btnWa.href = `https://wa.me/${waClean}`;
                    } else {
                        btnWa.href = `https://wa.me/628123456789`; // Default fallback
                    }
                }
            } catch (err) {
                container.innerHTML = '<div class="modal-loading" style="color:#ef4444;"><i class="fas fa-exclamation-triangle"></i> Gagal memuat info pembayaran.</div>';
            }
        }

        function bukaInfoBayar() {
            document.getElementById('modalBayar').classList.add('active');
            loadPaymentInfo();
        }
        function tutupInfoBayar() { document.getElementById('modalBayar').classList.remove('active'); }

        function updateThemeToggleIcon() {
            const btn = document.getElementById('themeToggleBtn');
            if (!btn) return;
            const isLight = document.documentElement.classList.contains('light-mode');
            btn.innerHTML = isLight ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
            btn.title = isLight ? 'Mode siang aktif' : 'Mode malam aktif';
        }

        function togglePortalTheme() {
            const root = document.documentElement;
            root.classList.toggle('light-mode');
            const isLight = root.classList.contains('light-mode');
            localStorage.setItem('ss_theme', isLight ? 'light' : 'dark');
            updateThemeToggleIcon();
        }

        window.togglePortalTheme = togglePortalTheme;
        document.addEventListener('DOMContentLoaded', updateThemeToggleIcon);

        // Tripay Logic
        async function bukaTripayModal() {
            if (!latestBillForPdf || !latestBillForPdf.id) {
                alert("Data tagihan tidak valid.");
                return;
            }
            document.getElementById('modalTripay').classList.add('active');
            const container = document.getElementById('tripayChannelsContainer');
            container.innerHTML = '<div class="modal-loading"><i class="fas fa-spinner fa-spin"></i> Memuat metode pembayaran...</div>';

            try {
                // We use our local API which fetches from Tripay
                const res = await fetch('/api/payment/channels');
                if (!res.ok) {
                    if (res.status === 503) {
                        container.innerHTML = '<div class="modal-loading" style="color:#fb7185;"><i class="fas fa-exclamation-triangle"></i> Fitur pembayaran online belum diaktifkan (menunggu konfigurasi).</div>';
                        return;
                    }
                    throw new Error('Gagal memuat metode pembayaran');
                }
                const json = await res.json();
                if (!json.success || !json.data) throw new Error(json.message || 'Gagal memuat');

                let html = '';
                for (const groupName in json.data) {
                    html += `<div style="margin-bottom:12px;">`;
                    html += `<div style="font-size:12px; font-weight:700; color:var(--text-soft); margin-bottom:8px; text-transform:uppercase;">${groupName}</div>`;
                    
                    json.data[groupName].forEach(channel => {
                        html += `
                        <div class="bank-card" style="cursor:pointer; transition:transform 0.2s;" onclick="prosesTripay('${channel.code}', '${channel.name}')" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                            <div class="bank-logo" style="background:transparent; width:auto; max-width:60px;">
                                <img src="${channel.icon_url}" alt="${channel.name}" style="max-width:100%; max-height:24px; border-radius:4px;">
                            </div>
                            <div style="flex:1;">
                                <div class="bank-number" style="font-size:14px; margin:0;">${channel.name}</div>
                                <div class="bank-meta">Biaya admin: Rp ${new Intl.NumberFormat('id-ID').format(channel.fee_customer?.flat || 0)}</div>
                            </div>
                            <i class="fas fa-chevron-right" style="color:var(--text-soft); opacity:0.5;"></i>
                        </div>`;
                    });
                    html += `</div>`;
                }
                container.innerHTML = html || '<div class="modal-loading">Tidak ada channel yang aktif.</div>';
            } catch (err) {
                container.innerHTML = '<div class="modal-loading" style="color:#fb7185;"><i class="fas fa-exclamation-triangle"></i> Gagal memuat metode pembayaran.</div>';
            }
        }

        function tutupTripayModal() {
            document.getElementById('modalTripay').classList.remove('active');
        }

        async function prosesTripay(method, methodName) {
            if (!confirm(`Lanjutkan pembayaran menggunakan ${methodName}?`)) return;

            const container = document.getElementById('tripayChannelsContainer');
            container.innerHTML = '<div class="modal-loading"><i class="fas fa-spinner fa-spin"></i> Membuat tagihan... mohon tunggu...</div>';

            try {
                // To call jwt-protected endpoint, we need the token from localStorage
                // We use our apiFetch helper which attaches the token automatically
                const res = await fetch('/api/payment/create-invoice', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + (localStorage.getItem('ss_token') || '') // Even for portal, if using jwt.auth, we might need a token
                        // Actually, if portal users don't use ss_token, we have a problem.
                        // Let's use apiFetch helper which is imported below!
                    },
                    body: JSON.stringify({
                        tagihanId: latestBillForPdf.id,
                        method: method
                    })
                });

                const json = await res.json();
                if (!res.ok) throw new Error(json.message || 'Gagal membuat tagihan');

                // Redirect to checkout URL
                window.location.href = json.data.checkout_url;
            } catch (err) {
                alert(err.message || "Gagal membuat invoice");
                tutupTripayModal();
            }
        }
    </script>
    <script type="module">
        import { apiFetch } from '{{ asset('api-config.js') }}';

        const MONTHS = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

        // 1. Validasi Sesi
        const sessionStore = localStorage.getItem('ss_customer');
        if (!sessionStore) {
            window.location.replace("{{ url('/login') }}");
            throw new Error('Session pelanggan tidak ditemukan');
        }

        let customer;
        let latestBillForPdf = null;
        try {
            customer = JSON.parse(sessionStore);
        } catch (err) {
            localStorage.removeItem('ss_customer');
            window.location.replace("{{ url('/login') }}");
            throw new Error('Session pelanggan rusak');
        }

        if (!customer || typeof customer !== 'object' || !customer.idPelanggan) {
            localStorage.removeItem('ss_customer');
            window.location.replace("{{ url('/login') }}");
            throw new Error('Session pelanggan tidak valid');
        }
        document.getElementById('uiCustomerName').innerText = limitString(customer.nama, 15);
        document.getElementById('uiCustomerArea').innerText = `ID: ${customer.idPelanggan} • ${customer.area}`;

        // 2. Load Pengumuman (server-first, multi siaran)
        async function loadAnnouncements() {
            const box = document.getElementById('announcementBox');
            const text = document.getElementById('marqueeText');
            if (!box || !text) return;
            try {
                const res = await fetch(`/api/pengumuman/aktif?idPelanggan=${encodeURIComponent(customer.idPelanggan)}&area=${encodeURIComponent(customer.area || '')}`);
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const json = await res.json();
                const list = Array.isArray(json?.data) ? json.data : [];
                if (!list.length) {
                    box.style.display = 'none';
                    return;
                }
                box.style.display = 'flex';
                text.innerText = 'INFO Sans Speed: ' + list.slice(0, 5).map((n) => String(n.pesan || '-')).join('  •  ');
            } catch (e) {
                console.warn("Gagal load pengumuman server, fallback local:", e.message);
                try {
                    const listNotif = JSON.parse(localStorage.getItem('ss_notifikasi_db') || "[]");
                    const myArea = String(customer.area || '').trim().toLowerCase();
                    const filtered = listNotif
                        .filter(n => n.area === 'ALL' || String(n.area || '').trim().toLowerCase() === myArea)
                        .sort((a, b) => Number(b.waktu || 0) - Number(a.waktu || 0));
                    if (!filtered.length) {
                        box.style.display = 'none';
                        return;
                    }
                    box.style.display = 'flex';
                    text.innerText = 'INFO Sans Speed: ' + filtered.slice(0, 5).map((n) => String(n.pesan || '-')).join('  •  ');
                } catch {
                    box.style.display = 'none';
                }
            }
        }
        await loadAnnouncements();
        if (window._portalAnnouncementTimer) clearInterval(window._portalAnnouncementTimer);
        window._portalAnnouncementTimer = setInterval(() => {
            loadAnnouncements().catch(() => { });
        }, 30000);

        function escapeHtmlChat(s) {
            return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        }
        /** Baca body JSON; jika server mengembalikan HTML (mis. {{ url('/login') }}), beri pesan jelas */
        async function readFetchAsJson(res) {
            const text = await res.text();
            const trim = text.trim();
            if (trim.startsWith('<!DOCTYPE') || trim.startsWith('<!doctype') || trim.startsWith('<html')) {
                throw new Error(
                    'Server mengembalikan halaman web, bukan API chat. Pastikan Anda mengakses portal lewat server Billing (port yang sama dengan Node, mis. ' +
                        window.location.port +
                        '), lalu restart aplikasi server (node server.js) setelah pembaruan.'
                );
            }
            try {
                return JSON.parse(text);
            } catch {
                throw new Error((trim || 'Respons kosong').slice(0, 200));
            }
        }
        async function renderPortalChat(data) {
            const box = document.getElementById('portalChatMessages');
            if (!box || !data) return;
            const list = Array.isArray(data.messages) ? data.messages : [];
            if (!list.length) {
                box.innerHTML = '<div style="opacity:.65;font-size:13px;">Belum ada pesan. Tulis pertanyaan di bawah.</div>';
                return;
            }
            box.innerHTML = list.map((m) => {
                const me = m.fromCustomer;
                const who = me ? 'Anda' : (m.fromLabel || 'Kantor');
                const align = me ? 'flex-end' : 'flex-start';
                const bg = me ? 'rgba(239,31,45,0.22)' : 'rgba(255,255,255,0.09)';
                return `<div style="display:flex;flex-direction:column;align-items:${align};max-width:100%;">
                    <span style="font-size:10px;opacity:.75;margin-bottom:3px;">${escapeHtmlChat(who)}</span>
                    <div style="background:${bg};padding:10px 12px;border-radius:12px;text-align:left;max-width:92%;white-space:pre-wrap;word-break:break-word;font-size:14px;">${escapeHtmlChat(m.body)}</div>
                </div>`;
            }).join('');
            box.scrollTop = box.scrollHeight;
        }
        async function loadPortalChat() {
            try {
                const idPel = encodeURIComponent(String(customer.idPelanggan || '').trim());
                const url = `${window.location.origin}/api/chat/publik/pelanggan/thread?idPelanggan=${idPel}`;
                const res = await fetch(url, { cache: 'no-store' });
                const j = await readFetchAsJson(res);
                if (!res.ok) throw new Error(j.error || 'Gagal memuat chat');
                if (j.success && j.data) await renderPortalChat(j.data);
            } catch (e) {
                console.warn('Portal chat:', e.message);
            }
        }
        await loadPortalChat();
        if (window._portalChatTimer) clearInterval(window._portalChatTimer);
        window._portalChatTimer = setInterval(() => { loadPortalChat().catch(() => { }); }, 15000);
        document.getElementById('portalChatSend')?.addEventListener('click', async () => {
            const inp = document.getElementById('portalChatInput');
            const t = String(inp?.value || '').trim();
            if (!t) return;
            try {
                const idPel = String(customer.idPelanggan || '').trim();
                if (!idPel) throw new Error('ID pelanggan tidak valid.');
                const res = await fetch(`${window.location.origin}/api/chat/publik/pelanggan/messages`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
                    cache: 'no-store',
                    body: JSON.stringify({ idPelanggan: idPel, body: t })
                });
                const j = await readFetchAsJson(res);
                if (!res.ok) throw new Error(j.error || 'Gagal mengirim pesan');
                inp.value = '';
                await loadPortalChat();
            } catch (e) {
                alert(e.message || 'Gagal kirim pesan');
            }
        });

        // 3. Load Tagihan Bulan Ini
        async function loadCurrentBill() {
            const today = new Date();
            const currMonth = today.getMonth() + 1;
            const currYear = today.getFullYear();
            const settings = await getPortalSettings();
            const billingNoteHtml = buildBillingScheduleNote(settings);

            try {
                // Fetch tagihan bulan berjalan via endpoint publik (tidak butuh token staff)
                let ds = [];
                try {
                    const result = await apiFetch(`/tagihan/pelanggan/${encodeURIComponent(customer.idPelanggan)}?bulan=${currMonth}&tahun=${currYear}`);
                    ds = result && result.data ? result.data : [];
                } catch (apiErr) {
                    console.warn("Gagal ambil tagihan dari server:", apiErr);
                    ds = [];
                }

                const billBox = document.getElementById('billContainer');

                if (ds && ds.length > 0) {
                    const bill = ds[0];
                    const formatRp = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(bill.totalTagihan || 0);
                    latestBillForPdf = {
                        id: bill.id || null,
                        bulan: currMonth,
                        tahun: currYear,
                        totalTagihan: bill.totalTagihan || 0,
                        status: bill.status || 'belum',
                        paket: bill.paket || customer.paket || '-',
                        jatuhTempo: bill.tglJatuhTempo || null
                    };

                    let bgStatus = 'badge-danger';
                    let txtStatus = 'BELUM BAYAR';
                    let jatuhTempoTxt = 'Batas Akhir: <span>' + (bill.tglJatuhTempo ? new Date(bill.tglJatuhTempo).toLocaleDateString('id-ID', { day: 'numeric', month: 'long' }) : '-') + '</span>';

                    if (bill.status === 'lunas') {
                        bgStatus = 'badge-success';
                        txtStatus = 'SUDAH LUNAS';
                        jatuhTempoTxt = 'Terima kasih, pembayaran telah diterima.';
                    } else if (bill.status === 'isolir') {
                        bgStatus = 'badge-warning';
                        txtStatus = 'TERISOLIR';
                    }

                    billBox.innerHTML = `
                        <div class="bill-card">
                            <div class="bill-ribbon"></div>
                            <div class="bill-accent"></div>
                            <div class="bill-header">
                                <div class="bill-month">Tagihan ${MONTHS[currMonth - 1]} ${currYear}</div>
                                <div class="bill-header-right">
                                    <button class="btn-nota" onclick="downloadNotaPdf()" title="Download Nota PDF" aria-label="Download Nota PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </button>
                                    <div class="badge ${bgStatus}">${txtStatus}</div>
                                </div>
                            </div>
                            <div class="bill-amount">${formatRp}</div>
                            <div class="bill-due">${jatuhTempoTxt}</div>
                            
                            <div class="bill-details">
                                <div class="detail-row">
                                    <span class="detail-label">Paket Layanan</span>
                                    <span class="detail-value">${bill.paket || customer.paket || '-'}</span>
                                </div>
                                <div class="detail-row" style="margin-top: 10px;">
                                    <span class="detail-label">Status Koneksi</span>
                                    <span class="detail-value" style="color:${customer.status === 'aktif' ? '#34d399' : '#fb7185'}">${(customer.status || 'aktif').toUpperCase()}</span>
                                </div>
                            </div>
                            ${billingNoteHtml}
                            ${bill.status !== 'lunas' ? `
                                <button onclick="bukaTripayModal()" style="width:100%; margin-top:16px; padding:14px; border-radius:14px; border:none; font-weight:800; color:#fff; background:linear-gradient(135deg, #10b981, #047857); cursor:pointer; font-size:14px; box-shadow:0 8px 16px rgba(16,185,129,0.25); display:flex; align-items:center; justify-content:center; gap:8px; transition:transform 0.2s;" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                                    <i class="fas fa-bolt"></i> Bayar Online Sekarang
                                </button>
                            ` : ''}
                        </div>
                    `;
                } else {
                    latestBillForPdf = null;
                    // Jika belum dicetak oleh sistem
                    billBox.innerHTML = `
                        <div class="bill-card" style="text-align: center; padding: 40px 20px;">
                            <div class="bill-ribbon"></div>
                            <div class="bill-accent"></div>
                            <i class="fas fa-file-invoice" style="font-size: 40px; color: var(--text-soft); opacity:.3; margin-bottom: 15px; position:relative; z-index:2;"></i>
                            <h3 style="margin:0 0 5px 0; font-size:16px; color:var(--text-main);">Tagihan Belum Terbit</h3>
                            <p style="margin:0; font-size:13px; color:var(--text-soft);">Tagihan bulan ${MONTHS[currMonth - 1]} belum dicetak oleh sistem admin.</p>
                            <div style="text-align:left; margin-top:12px;">${billingNoteHtml}</div>
                        </div>
                    `;
                }

            } catch (e) {
                console.error(e);
                document.getElementById('billContainer').innerHTML = `<div class="loader-box" style="color:#fb7185;"><i class="fas fa-exclamation-triangle"></i> Gagal memuat tagihan dari server.</div>`;
            }
        }

        // Helpers
        function limitString(str, maxLength) {
            if (!str) return '';
            return str.length > maxLength ? str.substring(0, maxLength) + '...' : str;
        }

        function escapeHtml(value) {
            return String(value || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function buildBillingScheduleNote(settings = {}) {
            const line1 = settings.payment_info_line1 || 'Pembayaran dibuka tanggal 25 - 05 setiap bulan.';
            const line2 = settings.payment_info_line2 || 'Jika melewati batas, pelanggan akan isolir tanggal 8.';
            return `<div class="billing-schedule-note">
                        <strong>Info Pembayaran:</strong> ${escapeHtml(line1)}<br>
                        ${escapeHtml(line2)}
                    </div>`;
        }

        const formatRupiah = (value) => new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(Number(value) || 0);

        async function loadPromoAndPackages() {
            const promoEl = document.getElementById('promoPackageText');
            const listEl = document.getElementById('promoPackageList');
            if (!promoEl || !listEl) return;
            let settings = {};
            let waCs = '628123456789';

            try {
                settings = await getPortalSettings();
                const promoText = settings.portal_promo_text || settings.promo_paket_text || settings.promo_text || 'Nikmati promo terbaik Sans Speed dan pilih paket sesuai kebutuhan internet rumah Anda.';
                promoEl.textContent = promoText;
                waCs = normalizeWaNumber(settings.payment_wa_cs);
            } catch {
                promoEl.textContent = 'Nikmati promo terbaik Sans Speed dan pilih paket sesuai kebutuhan internet rumah Anda.';
            }

            const buildWaLink = (namaPaket) => {
                const msg = `Halo Admin Sans Speed, saya ${customer.nama || 'Pelanggan'} (${customer.idPelanggan || '-'}) tertarik dengan paket ${namaPaket}. Mohon info detail dan pemasangan.`;
                return `https://wa.me/${waCs}?text=${encodeURIComponent(msg)}`;
            };

            const renderRows = (rows, source = 'live') => {
                const cleanRows = rows
                    .map(item => ({
                        nama: String(item?.nama || '').trim(),
                        harga: Number(item?.harga) || 0,
                        deskripsi: String(item?.deskripsi || '').trim(),
                        aktif: Number(item?.aktif ?? 1)
                    }))
                    .filter(item => item.nama && item.aktif !== 0)
                    .sort((a, b) => a.harga - b.harga);

                if (!cleanRows.length) {
                    listEl.innerHTML = '<div class="package-empty">Belum ada paket aktif yang ditampilkan saat ini.</div>';
                    return;
                }

                listEl.innerHTML = cleanRows.map((item, idx) => `
                    <article class="package-item">
                        <div class="package-row">
                            <div class="package-name-wrap">
                                <h4 class="package-name">${escapeHtml(item.nama || 'Paket Internet')}</h4>
                                ${idx === 0 ? '<span class="package-badge">Best Value</span>' : ''}
                            </div>
                            <div class="package-price">${item.harga > 0 ? formatRupiah(item.harga) : 'Hubungi CS'}</div>
                        </div>
                        <p class="package-desc">${escapeHtml(item.deskripsi || 'Paket internet stabil untuk kebutuhan harian Anda.')}</p>
                        <a href="${buildWaLink(item.nama || 'Paket Internet')}" target="_blank" class="package-cta">
                            <i class="fab fa-whatsapp"></i> Minat Paket Ini
                        </a>
                    </article>
                `).join('');

                if (source === 'cache') {
                    promoEl.textContent = `${promoEl.textContent} (menampilkan data paket tersimpan sementara)`;
                }
            };

            try {
                const res = await fetch('/api/paket/publik');
                const contentType = String(res.headers.get('content-type') || '').toLowerCase();
                if (!res.ok || !contentType.includes('application/json')) throw new Error(`HTTP ${res.status || 0}`);
                const json = await res.json();
                const rows = Array.isArray(json?.data) ? json.data : [];
                localStorage.setItem('ss_public_packages_cache', JSON.stringify(rows));
                renderRows(rows, 'live');
            } catch (err) {
                console.warn('Gagal memuat paket publik:', err.message);
                let fallbackRows = [];
                try {
                    fallbackRows = JSON.parse(localStorage.getItem('ss_public_packages_cache') || '[]');
                } catch {
                    fallbackRows = [];
                }

                if ((!Array.isArray(fallbackRows) || !fallbackRows.length) && customer?.paket) {
                    fallbackRows = [{
                        nama: customer.paket,
                        harga: 0,
                        deskripsi: 'Paket aktif Anda saat ini. Untuk pilihan paket lain, silakan hubungi CS.',
                        aktif: 1
                    }];
                }

                if (Array.isArray(fallbackRows) && fallbackRows.length) {
                    renderRows(fallbackRows, 'cache');
                } else {
                    listEl.innerHTML = '<div class="package-empty">Daftar paket belum tersedia. Silakan hubungi CS untuk info promo terbaru.</div>';
                }
            }
        }

        window.logout = function () {
            localStorage.removeItem('ss_customer');
            window.location.replace('{{ url('/login') }}');
        }

        function loadJsPdfLib() {
            return new Promise((resolve, reject) => {
                if (window.jspdf && window.jspdf.jsPDF) return resolve(window.jspdf.jsPDF);
                const s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js';
                s.onload = () => resolve(window.jspdf && window.jspdf.jsPDF);
                s.onerror = reject;
                document.head.appendChild(s);
            });
        }

        let portalSettingsCache = null;
        async function getPortalSettings() {
            if (portalSettingsCache) return portalSettingsCache;
            try {
                const res = await apiFetch('/pengaturan');
                portalSettingsCache = (res && res.success && res.data) ? res.data : {};
            } catch {
                portalSettingsCache = {};
            }
            return portalSettingsCache;
        }

        function normalizeWaNumber(raw) {
            const digits = String(raw || '').replace(/\D/g, '');
            if (!digits) return '628123456789';
            if (digits.startsWith('0')) return '62' + digits.slice(1);
            if (digits.startsWith('62')) return digits;
            return '62' + digits;
        }

        const readBlobAsDataUrl = (blob) => new Promise((resolve, reject) => {
            const fr = new FileReader();
            fr.onload = () => resolve(fr.result);
            fr.onerror = reject;
            fr.readAsDataURL(blob);
        });

        async function toDataUrlFromUrl(url) {
            const res = await fetch(url);
            if (!res.ok) throw new Error('Gagal mengambil gambar');
            const blob = await res.blob();
            return readBlobAsDataUrl(blob);
        }

        async function prepareLogoForPdf(dataUrl) {
            const img = new Image();
            await new Promise((resolve, reject) => {
                img.onload = resolve;
                img.onerror = reject;
                img.src = dataUrl;
            });

            const canvas = document.createElement('canvas');
            canvas.width = 260;
            canvas.height = 260;
            const ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            const scale = Math.min(canvas.width / img.width, canvas.height / img.height);
            const drawW = Math.max(1, Math.round(img.width * scale));
            const drawH = Math.max(1, Math.round(img.height * scale));
            const dx = Math.round((canvas.width - drawW) / 2);
            const dy = Math.round((canvas.height - drawH) / 2);

            ctx.imageSmoothingEnabled = true;
            ctx.imageSmoothingQuality = 'high';
            ctx.drawImage(img, dx, dy, drawW, drawH);
            return canvas.toDataURL('image/png');
        }

        window.downloadNotaPdf = async function () {
            if (!latestBillForPdf) return;
            try {
                const jsPDF = await loadJsPdfLib();
                const doc = new jsPDF({ unit: 'mm', format: 'a5' });
                const fmt = (v) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(v || 0);
                const now = new Date();
                const periodText = `${MONTHS[latestBillForPdf.bulan - 1]} ${latestBillForPdf.tahun}`;
                const statusText = (latestBillForPdf.status || 'belum').toUpperCase();
                const jatuhTempo = latestBillForPdf.jatuhTempo
                    ? new Date(latestBillForPdf.jatuhTempo).toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' })
                    : '-';

                const settings = await getPortalSettings();
                const ispSupportBy = settings.isp_support_by || 'Sans Speed MEDIA';
                const waCs = normalizeWaNumber(settings.payment_wa_cs);
                const waMessage = `Halo Admin Sans Speed, saya ${customer.nama || '-'} (${customer.idPelanggan || '-'}) ingin konfirmasi pembayaran tagihan ${periodText} sebesar ${fmt(latestBillForPdf.totalTagihan)}.`;
                const waLink = `https://wa.me/${waCs}?text=${encodeURIComponent(waMessage)}`;

                // Header
                doc.setFillColor(15, 23, 42);
                doc.rect(0, 0, 148, 28, 'F');
                doc.setTextColor(255, 255, 255);
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(15);
                doc.text('NOTA TAGIHAN', 12, 11);
                doc.setFontSize(13);
                doc.text('Sans Speed MEDIA', 12, 17);
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(9);
                doc.text('THE BEST YOUR CONNECTION', 12, 21);
                doc.text(`ISP Support By: ${ispSupportBy}`, 12, 24.5);
                doc.text(`Dicetak: ${now.toLocaleDateString('id-ID')} ${now.toLocaleTimeString('id-ID')}`, 12, 28);

                // Branding logo (if available)
                const logoDataRaw = settings.sidebar_logo_data || localStorage.getItem('ss_sidebar_logo_data') || '';
                if (logoDataRaw) {
                    try {
                        const logoData = logoDataRaw.startsWith('data:image/') ? logoDataRaw : await toDataUrlFromUrl(logoDataRaw);
                        const logoPng = await prepareLogoForPdf(logoData);
                        doc.setFillColor(255, 255, 255);
                        doc.roundedRect(118, 5, 24, 24, 2, 2, 'F');
                        doc.addImage(logoPng, 'PNG', 120, 7, 20, 20);
                    } catch {
                        // Ignore logo rendering failures; PDF still generated
                    }
                }

                // Customer info box
                doc.setTextColor(20, 20, 20);
                doc.setDrawColor(200, 210, 220);
                doc.roundedRect(10, 32, 128, 52, 3, 3);
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(10);
                doc.text('Nama Pelanggan', 14, 40);
                doc.text('ID Pelanggan', 14, 47);
                doc.text('Area', 14, 54);
                doc.text('Periode', 14, 61);
                doc.text('Paket', 14, 68);
                doc.text('Status', 14, 75);

                doc.setFont('helvetica', 'normal');
                doc.text(`: ${customer.nama || '-'}`, 50, 40);
                doc.text(`: ${customer.idPelanggan || '-'}`, 50, 47);
                doc.text(`: ${customer.area || '-'}`, 50, 54);
                doc.text(`: ${periodText}`, 50, 61);
                doc.text(`: ${latestBillForPdf.paket || '-'}`, 50, 68);
                doc.text(`: ${statusText}`, 50, 75);

                // Amount box
                doc.setDrawColor(220, 220, 220);
                doc.roundedRect(10, 88, 90, 28, 3, 3);
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(11);
                doc.text('Total Tagihan', 14, 97);
                doc.setFontSize(19);
                doc.text(fmt(latestBillForPdf.totalTagihan), 14, 110);
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(9);
                doc.text(`Jatuh Tempo: ${jatuhTempo}`, 14, 115);

                // QR for WA confirmation
                doc.setFont('helvetica', 'bold');
                doc.setFontSize(10);
                doc.text('QR Konfirmasi WA', 106, 93);
                try {
                    const qrUrl = `https://quickchart.io/qr?size=220&text=${encodeURIComponent(waLink)}`;
                    const qrData = await toDataUrlFromUrl(qrUrl);
                    doc.addImage(qrData, 'PNG', 106, 96, 28, 28);
                } catch {
                    doc.setFont('helvetica', 'normal');
                    doc.setFontSize(8);
                    doc.text('QR tidak tersedia', 106, 106);
                }

                // Footer note (clean without long URL text)
                doc.setFont('helvetica', 'normal');
                doc.setFontSize(8.5);
                doc.text(`Konfirmasi WA CS: +${waCs}`, 10, 126);
                doc.text(`ISP Support By: ${ispSupportBy}`, 10, 130);
                doc.text('Scan QR di atas untuk konfirmasi pembayaran pelanggan.', 10, 134);
                doc.text('Simpan nota ini sebagai bukti tagihan resmi pelanggan.', 10, 138);

                const fileName = `Nota_${customer.idPelanggan || 'Pelanggan'}_${latestBillForPdf.bulan}-${latestBillForPdf.tahun}.pdf`;

                // Terintegrasi dengan APK Android untuk unduh file tanpa kendala blob/permission
                if (window.AndroidJS && typeof window.AndroidJS.saveBase64File === 'function') {
                    const b64 = doc.output('datauristring');
                    window.AndroidJS.saveBase64File(b64, 'application/pdf', fileName.replace(/[^\w.-]/g, '_'));
                } else {
                    doc.save(fileName.replace(/[^\w.-]/g, '_'));
                }
            } catch (err) {
                console.error('Gagal generate PDF:', err);
                alert('Gagal membuat PDF nota. Silakan coba lagi.');
            }
        };

        // ─── FCM TOKEN BINDING: Daftarkan token Android ke server dengan ID pelanggan ───
        async function registerFcmTokenForCustomer() {
            try {
                // Ambil FCM token yang di-inject oleh APK Android ke localStorage / window
                const fcmToken = window.__ssMobileFcmToken
                    || localStorage.getItem('ss_mobile_fcm_token')
                    || '';
                if (!fcmToken || fcmToken.length < 20) return; // Bukan dari APK Android, abaikan

                const idPelanggan = customer.idPelanggan || '';
                if (!idPelanggan) return;

                const body = JSON.stringify({
                    token: fcmToken,
                    platform: 'android',
                    targetType: 'pelanggan',
                    targetId: idPelanggan
                });

                // Kirim ke server (tidak butuh auth token staff)
                const res = await fetch('/api/push/register', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body
                });
                if (res.ok) {
                    console.log('[FCM] Token pelanggan berhasil didaftarkan:', idPelanggan);
                } else {
                    console.warn('[FCM] Gagal daftar token, status:', res.status);
                }
            } catch (err) {
                console.warn('[FCM] registerFcmTokenForCustomer error:', err.message);
            }
        }

        // Execute
        loadCurrentBill();
        loadPromoAndPackages();
        registerFcmTokenForCustomer();
    </script>
</body>

</html>