<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sans Speed Billing')</title>
    <link rel="stylesheet" href="{{ asset('style.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        if (localStorage.getItem('ss_theme') === 'light') {
            document.documentElement.classList.add('light-mode');
        }
    </script>
    @stack('styles')
</head>
<body>
    <div class="app-container">
        <!-- SIDEBAR -->
        <aside id="app-sidebar" class="sidebar">
            @include('layouts.partials.sidebar')
        </aside>

        <!-- MAIN CONTENT -->
        <main class="main-content">
            <!-- HEADER -->
            <header id="app-header" class="header">
                @include('layouts.partials.header')
            </header>

            <div class="content-wrapper">
                @if(session('success'))
                    <div style="background: #dcfce7; color: #166534; padding: 12px 16px; border-radius: 10px; margin-bottom: 16px; font-size: 14px; font-weight: 600; border: 1px solid #bbf7d0; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-check-circle"></i> {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div style="background: #fee2e2; color: #991b1b; padding: 12px 16px; border-radius: 10px; margin-bottom: 16px; font-size: 14px; font-weight: 600; border: 1px solid #fecaca; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
    <script>
        // Mobile hamburger
        document.addEventListener('DOMContentLoaded', function() {
            const hamburger = document.getElementById('hamburgerBtn');
            const sidebar = document.getElementById('app-sidebar');
            if (hamburger && sidebar) {
                hamburger.addEventListener('click', () => sidebar.classList.toggle('open'));
                document.addEventListener('click', (e) => {
                    if (window.innerWidth <= 768 && sidebar.classList.contains('open')
                        && !sidebar.contains(e.target) && !hamburger.contains(e.target)) {
                        sidebar.classList.remove('open');
                    }
                });
            }
            // Profile dropdown
            const profileBtn = document.getElementById('userProfileBtn');
            const dropdown = document.getElementById('profileDropdown');
            if (profileBtn && dropdown) {
                profileBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
                });
                document.addEventListener('click', () => dropdown.style.display = 'none');
            }
        });
    </script>
</body>
</html>
