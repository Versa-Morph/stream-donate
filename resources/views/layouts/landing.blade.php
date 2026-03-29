<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#070709">
    <title>{{ $title ?? config('app.name', 'Tiptipan') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js" defer></script>
    <style>
        :root {
            --bg: #070709;
            --bg-1: #0d0d12;
            --surface: #141419;
            --surface-2: #1a1a22;
            --surface-3: #1f1f28;
            --border: rgba(255, 255, 255, .07);
            --border-2: rgba(255, 255, 255, .12);
            --brand: #7c6cfc;
            --brand-light: #a99dff;
            --brand-glow: rgba(124, 108, 252, .2);
            --brand-deep: #6356e8;
            --purple: #a855f7;
            --green: #22d3a0;
            --yellow: #fbbf24;
            --text: #f1f1f6;
            --text-2: #a0a0b4;
            --text-3: #606078;
            --radius-sm: 8px;
            --radius: 12px;
            --radius-lg: 18px;
            --radius-xl: 24px;
            --glass-bg: rgba(20, 20, 25, 0.7);
            --glass-bg-2: rgba(26, 26, 34, 0.6);
            --glass-border: rgba(255, 255, 255, 0.1);
            --glass-border-2: rgba(255, 255, 255, 0.15);
            --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
            --glass-shadow-lg: 0 12px 48px 0 rgba(0, 0, 0, 0.5);
        }

        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        a {
            color: var(--brand-light);
            text-decoration: none;
        }

        /* ── TOPBAR (Simple for all users) ── */
        .topbar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 200;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            background: rgba(7, 7, 9, 0.8);
            backdrop-filter: blur(24px) saturate(180%);
            border-bottom: 1px solid var(--border);
        }

        .logo {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            font-size: 18px;
            letter-spacing: -.3px;
            color: var(--text);
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .logo:hover {
            color: var(--text);
        }

        .logo-icon {
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--brand), var(--purple));
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 20px var(--brand-glow);
            flex-shrink: 0;
        }

        .logo-icon .iconify {
            width: 20px;
            height: 20px;
            color: #fff;
        }

        .logo-text span {
            color: var(--brand-light);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-nav {
            padding: 8px 18px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all .2s;
        }

        .btn-nav-ghost {
            color: var(--text-2);
            background: transparent;
            border: 1px solid var(--border);
        }

        .btn-nav-ghost:hover {
            background: var(--surface-2);
            border-color: var(--border-2);
            color: var(--text);
        }

        .btn-nav-primary {
            color: white;
            background: linear-gradient(135deg, var(--brand), var(--purple));
            border: none;
        }

        .btn-nav-primary:hover {
            box-shadow: 0 0 20px var(--brand-glow);
            transform: translateY(-1px);
        }

        /* ── CONTENT WRAP ── */
        .page-wrap {
            padding-top: 60px;
            min-height: 100vh;
        }

        /* ── PAGE CONTAINER ── */
        .page-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 32px 48px;
        }
        .page-container.narrow {
            max-width: 900px;
        }

        /* ── SCROLLBAR ── */
        ::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--surface-3);
            border-radius: 2px;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 768px) {
            .topbar {
                padding: 0 16px;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <!-- Simple Topbar for All Users -->
    <nav class="topbar">
        <a href="{{ route('home') }}" class="logo">
            <div class="logo-icon">
                <span class="iconify" data-icon="solar:gamepad-bold-duotone"></span>
            </div>
            <div class="logo-text">Tiptipan<span>.</span></div>
        </a>

        <div class="topbar-right">
            @auth
                <a href="{{ route('streamer.dashboard') }}" class="btn-nav btn-nav-primary">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn-nav btn-nav-ghost">Masuk</a>
                <a href="{{ route('register') }}" class="btn-nav btn-nav-primary">Daftar</a>
            @endauth
        </div>
    </nav>

    <div class="page-wrap">
        {{ $slot }}
    </div>

</body>
</html>
