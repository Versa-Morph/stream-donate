<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#070709">
    <title>{{ $title ?? config('app.name', 'Tiptipan') }}</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&family=Nunito:wght@400;500;600;700;800&display=swap"
        rel="stylesheet" />
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js" defer></script>
    <style>
        :root {
            --bg: #070709;
            --bg-1: #0d0d12;
            --bg-2: #111118;
            --surface: #141419;
            --surface-2: #1a1a22;
            --surface-3: #1f1f28;
            --border: rgba(255, 255, 255, .07);
            --border-2: rgba(255, 255, 255, .12);
            --brand: #7c6cfc;
            --brand-light: #a99dff;
            --brand-glow: rgba(124, 108, 252, .2);
            --orange: #f97316;
            --orange-light: #fb923c;
            --orange-glow: rgba(249, 115, 22, .15);
            --green: #22d3a0;
            --green-light: #4ade80;
            --green-glow: rgba(34, 211, 160, .15);
            --yellow: #fbbf24;
            --red: #f43f5e;
            --purple: #a855f7;
            --text: #f1f1f6;
            --text-2: #a0a0b4;
            --text-3: #606078;
            --radius-sm: 8px;
            --radius: 12px;
            --radius-lg: 18px;
            --radius-xl: 24px;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, .4);
            --shadow: 0 4px 16px rgba(0, 0, 0, .5);
            --shadow-lg: 0 16px 48px rgba(0, 0, 0, .6);
            /* Glassmorphism effects */
            --glass-bg: rgba(20, 20, 25, 0.7);
            --glass-bg-2: rgba(26, 26, 34, 0.6);
            --glass-border: rgba(255, 255, 255, 0.1);
            --glass-border-2: rgba(255, 255, 255, 0.15);
            --glass-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
            --glass-shadow-lg: 0 12px 48px 0 rgba(0, 0, 0, 0.5);
            --glow-purple: 0 0 20px rgba(124, 108, 252, 0.6);
            --glow-purple-lg: 0 0 30px rgba(124, 108, 252, 0.8);
        }

        *,
        *::before,
        *::after {
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

        a:hover {
            color: var(--text);
        }

        /* ── TOPBAR ── */
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
            background: var(--glass-bg);
            backdrop-filter: blur(24px) saturate(180%);
            border-bottom: 1px solid var(--glass-border);
            box-shadow: var(--glass-shadow);
            transition: all 0.3s ease;
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

        /* ── NAV LINKS ── */
        .topbar-nav {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .nav-link {
            padding: 7px 14px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            font-weight: 500;
            color: var(--text-3);
            transition: all .15s;
            border: none;
            background: transparent;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 7px;
            position: relative;
            text-decoration: none;
        }

        .nav-link .iconify {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
            transition: color .15s;
        }

        .nav-link:hover {
            color: var(--text-2);
            background: var(--surface-2);
        }

        .nav-link:hover .iconify {
            color: var(--text-2);
        }

        .nav-link.active {
            color: var(--text);
            background: var(--surface-3);
        }

        .nav-link.active .iconify {
            color: var(--brand-light);
        }

        .nav-link.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 14px;
            right: 14px;
            height: 2px;
            background: var(--brand);
            border-radius: 2px 2px 0 0;
            animation: navUnderline .2s ease;
        }

        @keyframes navUnderline {
            from {
                transform: scaleX(0);
                opacity: 0;
            }

            to {
                transform: scaleX(1);
                opacity: 1;
            }
        }

        .nav-link.nav-external {
            color: var(--text-3);
        }

        /* ── TOPBAR RIGHT ── */
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* ── USER DROPDOWN (pure <details>) ── */
        .user-dropdown {
            position: relative;
        }

        .user-dropdown summary {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 10px 5px 6px;
            border-radius: 20px;
            background: var(--surface);
            border: 1px solid var(--border);
            font-size: 12px;
            font-weight: 500;
            color: var(--text-2);
            cursor: pointer;
            list-style: none;
            user-select: none;
            transition: border-color .15s, background .15s;
        }

        .user-dropdown summary::-webkit-details-marker {
            display: none;
        }

        .user-dropdown summary:hover {
            border-color: var(--border-2);
            background: var(--surface-2);
        }

        .user-dropdown[open] summary {
            border-color: var(--brand);
            background: var(--surface-2);
        }

        .user-avatar {
            width: 24px;
            height: 24px;
            border-radius: 7px;
            background: linear-gradient(135deg, var(--brand), var(--purple));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .user-name {
            max-width: 110px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .user-role-badge {
            font-size: 10px;
            color: var(--text-3);
            background: var(--surface-3);
            border-radius: 4px;
            padding: 1px 5px;
            line-height: 1.6;
            font-weight: 600;
        }

        .dropdown-caret .iconify {
            width: 14px;
            height: 14px;
            color: var(--text-3);
            transition: transform .2s;
        }

        .user-dropdown[open] .dropdown-caret .iconify {
            transform: rotate(180deg);
        }

        .user-dropdown-menu {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            min-width: 200px;
            background: var(--glass-bg);
            backdrop-filter: blur(16px) saturate(180%);
            border: 1px solid var(--glass-border-2);
            border-radius: var(--radius);
            box-shadow: var(--glass-shadow-lg);
            overflow: hidden;
            animation: dropdownIn .15s ease;
            z-index: 300;
        }

        @keyframes dropdownIn {
            from {
                opacity: 0;
                transform: translateY(-6px) scale(.97);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .dropdown-header {
            padding: 10px 14px 8px;
            border-bottom: 1px solid var(--border);
        }

        .dropdown-user-full {
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
        }

        .dropdown-user-email {
            font-size: 11px;
            color: var(--text-3);
            margin-top: 2px;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 14px;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-2);
            transition: background .12s, color .12s;
            cursor: pointer;
            text-decoration: none;
        }

        .dropdown-item .iconify {
            width: 16px;
            height: 16px;
            color: var(--text-3);
            flex-shrink: 0;
        }

        .dropdown-item:hover {
            background: var(--surface-3);
            color: var(--text);
        }

        .dropdown-item:hover .iconify {
            color: var(--brand-light);
        }

        .dropdown-item.danger {
            color: var(--red);
        }

        .dropdown-item.danger .iconify {
            color: var(--red);
        }

        .dropdown-item.danger:hover {
            background: rgba(244, 63, 94, .08);
        }

        .dropdown-divider {
            height: 1px;
            background: var(--border);
            margin: 4px 0;
        }

        .dropdown-form {
            padding: 4px 8px;
        }

        .dropdown-submit {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 9px 6px;
            font-size: 13px;
            font-weight: 500;
            color: var(--red);
            background: transparent;
            border: none;
            cursor: pointer;
            border-radius: var(--radius-sm);
            transition: background .12s;
            font-family: 'Inter', sans-serif;
        }

        .dropdown-submit .iconify {
            width: 16px;
            height: 16px;
            color: var(--red);
            flex-shrink: 0;
        }

        .dropdown-submit:hover {
            background: rgba(244, 63, 94, .08);
        }

        .btn-sm {
            padding: 6px 14px;
            border-radius: var(--radius-sm);
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s;
            border: 1px solid var(--border);
            background: var(--surface-2);
            color: var(--text-2);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-sm:hover {
            border-color: var(--border-2);
            color: var(--text);
        }

        .btn-danger {
            border-color: rgba(244, 63, 94, .3);
            color: var(--red);
            background: rgba(244, 63, 94, .06);
        }

        .btn-danger:hover {
            background: rgba(244, 63, 94, .12);
            border-color: rgba(244, 63, 94, .5);
        }

        /* ── CONTENT WRAP ── */
        .page-wrap {
            padding-top: 60px;
            min-height: 100vh;
        }

        /* ══════════════════════════════════════════════════════════════════
           UNIFIED LAYOUT SYSTEM
           Consistent page structure across all dashboard pages
           ══════════════════════════════════════════════════════════════════ */
        
        /* ── Page Container ── */
        .page-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 28px 32px 48px;
        }
        .page-container.wide {
            max-width: 1440px;
        }
        .page-container.narrow {
            max-width: 1080px;
        }

        /* ── Page Header ── */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 28px;
            gap: 16px;
            flex-wrap: wrap;
        }
        .page-header-left {
            flex: 1;
            min-width: 200px;
        }
        .page-header-right {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .page-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: -.5px;
            background: linear-gradient(135deg, var(--text) 0%, var(--brand-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
        }
        .page-subtitle {
            font-size: 13px;
            color: var(--text-3);
            margin-top: 5px;
            line-height: 1.5;
        }

        /* ── Stats Grid ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }
        .stats-grid.cols-4 {
            grid-template-columns: repeat(4, 1fr);
        }
        .stats-grid.cols-3 {
            grid-template-columns: repeat(3, 1fr);
        }
        @media (max-width: 1200px) {
            .stats-grid.cols-4 {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        @media (max-width: 768px) {
            .stats-grid.cols-4,
            .stats-grid.cols-3 {
                grid-template-columns: 1fr;
            }
        }

        /* ── Stat Card ── */
        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: 22px 24px;
            position: relative;
            overflow: hidden;
            transition: all .3s ease;
            box-shadow: var(--glass-shadow);
        }
        .stat-card:hover {
            border-color: var(--glass-border-2);
            transform: translateY(-3px);
            box-shadow: var(--glass-shadow-lg);
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: radial-gradient(ellipse at 20% 20%, color-mix(in srgb, var(--stat-color, var(--brand)) 8%, transparent) 0%, transparent 70%);
            pointer-events: none;
        }
        .stat-card::after {
            content: '';
            position: absolute;
            bottom: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--stat-color, var(--brand)), transparent);
            opacity: .8;
        }
        .stat-card[data-color="brand"]  { --stat-color: var(--brand); }
        .stat-card[data-color="orange"] { --stat-color: var(--orange); }
        .stat-card[data-color="green"]  { --stat-color: var(--green); }
        .stat-card[data-color="yellow"] { --stat-color: var(--yellow); }
        .stat-card[data-color="purple"] { --stat-color: var(--purple); }
        .stat-card[data-color="red"]    { --stat-color: var(--red); }
        
        .stat-icon {
            width: 42px;
            height: 42px;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            position: relative;
            z-index: 1;
            background: color-mix(in srgb, var(--stat-color, var(--brand)) 15%, transparent);
            border: 1px solid color-mix(in srgb, var(--stat-color, var(--brand)) 30%, transparent);
            box-shadow: 0 0 20px color-mix(in srgb, var(--stat-color, var(--brand)) 20%, transparent);
        }
        .stat-icon .iconify {
            width: 22px;
            height: 22px;
            color: var(--stat-color, var(--brand));
        }
        .stat-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
            color: var(--text-3);
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }
        .stat-value {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -1px;
            color: var(--text);
            line-height: 1;
            margin-bottom: 8px;
            position: relative;
            z-index: 1;
        }
        .stat-sub {
            font-size: 11px;
            color: var(--text-3);
            position: relative;
            z-index: 1;
        }

        /* ── Content Grid Layouts ── */
        .content-grid {
            display: grid;
            gap: 20px;
            align-items: start;
        }
        .content-grid.cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
        .content-grid.cols-2-1 {
            grid-template-columns: minmax(0, 2fr) minmax(0, 1fr);
        }
        .content-grid.cols-1-2 {
            grid-template-columns: minmax(0, 1fr) minmax(0, 2fr);
        }
        .content-grid.main-sidebar {
            grid-template-columns: minmax(0, 1fr) 360px;
        }
        .content-grid.sidebar-main {
            grid-template-columns: 280px minmax(0, 1fr);
        }
        @media (max-width: 1024px) {
            .content-grid.cols-2,
            .content-grid.cols-2-1,
            .content-grid.cols-1-2,
            .content-grid.main-sidebar,
            .content-grid.sidebar-main {
                grid-template-columns: 1fr;
            }
        }

        /* ── Content Card ── */
        .content-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--glass-shadow);
            transition: all .3s ease;
        }
        .content-card:hover {
            border-color: var(--glass-border-2);
        }
        .content-card.no-hover:hover {
            border-color: var(--glass-border);
            transform: none;
        }

        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 12px;
        }
        .card-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .card-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 16px;
            font-weight: 700;
            letter-spacing: -.3px;
            color: var(--text);
        }
        .card-count {
            background: var(--glass-bg-2);
            backdrop-filter: blur(8px);
            border: 1px solid var(--glass-border);
            color: var(--text-3);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .card-link {
            font-size: 12px;
            color: var(--brand-light);
            font-weight: 600;
            transition: all .2s ease;
        }
        .card-link:hover {
            color: var(--text);
            transform: translateX(2px);
        }

        /* ── Section Headers ── */
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            gap: 12px;
        }
        .section-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-2);
            letter-spacing: -.2px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-title .iconify {
            width: 17px;
            height: 17px;
            color: var(--brand-light);
        }

        /* ── Table System ── */
        .table-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--glass-shadow);
            position: relative;
        }
        .table-card::before {
            content: '';
            position: absolute;
            top: 0; left: 20px; right: 20px;
            height: 2px;
            background: linear-gradient(90deg, rgba(124,108,252,.5), rgba(168,85,247,.3), rgba(124,108,252,.5));
            box-shadow: 0 0 10px rgba(124,108,252,.3);
        }
        .table-card table {
            width: 100%;
            border-collapse: collapse;
        }
        .table-card thead {
            background: var(--glass-bg-2);
        }
        .table-card th {
            padding: 13px 18px;
            text-align: left;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .7px;
            text-transform: uppercase;
            color: var(--text-3);
        }
        .table-card td {
            padding: 14px 18px;
            border-top: 1px solid var(--glass-border);
            font-size: 13px;
            color: var(--text-2);
            vertical-align: middle;
        }
        .table-card tr {
            transition: all .2s ease;
        }
        .table-card tr:hover td {
            background: var(--glass-bg-2);
        }
        .table-empty {
            text-align: center;
            color: var(--text-3);
            padding: 40px;
            font-size: 13px;
        }

        /* ── Filter Bar ── */
        .filter-bar {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
            background: var(--glass-bg);
            backdrop-filter: blur(16px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: 16px 20px;
            margin-bottom: 20px;
            box-shadow: var(--glass-shadow);
        }
        .filter-bar input,
        .filter-bar select {
            flex: 1;
            min-width: 160px;
            max-width: 280px;
            padding: 10px 14px;
            font-size: 13px;
        }
        .filter-bar .btn-filter {
            padding: 10px 20px;
            font-size: 13px;
            font-weight: 600;
            background: var(--brand);
            color: #fff;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            transition: all .2s;
        }
        .filter-bar .btn-filter:hover {
            opacity: .85;
            transform: translateY(-1px);
        }

        /* ── Badge System ── */
        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .3px;
            text-transform: uppercase;
            backdrop-filter: blur(8px);
        }
        .badge-brand  { background: rgba(124,108,252,.15); color: var(--brand-light); border: 1px solid rgba(124,108,252,.3); }
        .badge-orange { background: rgba(249,115,22,.12);  color: var(--orange-light); border: 1px solid rgba(249,115,22,.3); }
        .badge-green  { background: rgba(34,211,160,.12);  color: var(--green);        border: 1px solid rgba(34,211,160,.3); }
        .badge-yellow { background: rgba(251,191,36,.12);  color: var(--yellow);       border: 1px solid rgba(251,191,36,.3); }
        .badge-purple { background: rgba(168,85,247,.12);  color: var(--purple);       border: 1px solid rgba(168,85,247,.3); }
        .badge-red    { background: rgba(244,63,94,.10);   color: var(--red);          border: 1px solid rgba(244,63,94,.25); }
        .badge-gray   { background: rgba(96,96,120,.15);   color: var(--text-3);       border: 1px solid rgba(96,96,120,.25); }

        /* ── Pagination ── */
        .pagination {
            display: flex;
            gap: 4px;
            align-items: center;
            justify-content: center;
            margin-top: 24px;
        }
        .pagination a,
        .pagination span {
            padding: 8px 14px;
            border-radius: var(--radius-sm);
            font-size: 13px;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-2);
            transition: all .2s ease;
        }
        .pagination a:hover {
            border-color: var(--brand);
            color: var(--brand-light);
            background: var(--glass-bg-2);
        }
        .pagination .active span {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
        }
        .pagination .disabled span {
            opacity: .4;
            cursor: not-allowed;
        }

        /* ── List Items ── */
        .list-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            background: var(--glass-bg-2);
            backdrop-filter: blur(8px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius);
            transition: all .2s ease;
        }
        .list-item:hover {
            border-color: var(--glass-border-2);
            background: var(--glass-bg);
            transform: translateX(4px);
        }
        .list-item + .list-item {
            margin-top: 10px;
        }
        .list-avatar {
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--surface-3), var(--surface));
            border: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }
        .list-content {
            flex: 1;
            min-width: 0;
        }
        .list-title {
            font-size: 14px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .list-subtitle {
            font-size: 11px;
            color: var(--text-3);
            margin-top: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .list-meta {
            text-align: right;
            flex-shrink: 0;
        }
        .list-value {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 16px;
            font-weight: 700;
            color: var(--orange);
        }
        .list-time {
            font-size: 10px;
            color: var(--text-3);
            margin-top: 3px;
        }

        /* ── Action Buttons ── */
        .btn-action {
            padding: 6px 12px;
            font-size: 11px;
            font-weight: 600;
            border-radius: var(--radius-sm);
            cursor: pointer;
            border: 1px solid var(--border);
            background: var(--surface-2);
            color: var(--text-2);
            transition: all .15s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-action:hover {
            border-color: var(--border-2);
            color: var(--text);
        }
        .btn-action.danger {
            border-color: rgba(244,63,94,.3);
            color: var(--red);
            background: rgba(244,63,94,.06);
        }
        .btn-action.danger:hover {
            background: rgba(244,63,94,.12);
        }
        .btn-action.success {
            border-color: rgba(34,211,160,.3);
            color: var(--green);
            background: rgba(34,211,160,.06);
        }
        .btn-action.success:hover {
            background: rgba(34,211,160,.12);
        }
        .btn-action.warning {
            border-color: rgba(251,191,36,.3);
            color: var(--yellow);
            background: rgba(251,191,36,.06);
        }
        .btn-action.warning:hover {
            background: rgba(251,191,36,.12);
        }

        /* ── Responsive adjustments ── */
        @media (max-width: 768px) {
            .page-container {
                padding: 20px 16px 40px;
            }
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
            .page-title {
                font-size: 22px;
            }
            .stat-value {
                font-size: 24px;
            }
            .content-card {
                padding: 18px;
            }
            .filter-bar {
                padding: 14px 16px;
            }
            .filter-bar input,
            .filter-bar select {
                max-width: 100%;
            }
        }

        /* ── FLASH ── */
        .flash {
            margin: 16px 28px 0;
            padding: 12px 16px;
            border-radius: var(--radius);
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            backdrop-filter: blur(12px) saturate(180%);
            box-shadow: var(--glass-shadow);
            animation: flashIn .25s ease;
        }

        @keyframes flashIn {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .flash.success {
            background: rgba(34, 211, 160, .08);
            border: 1px solid rgba(34, 211, 160, .2);
            color: var(--green);
        }

        .flash.error {
            background: rgba(244, 63, 94, .08);
            border: 1px solid rgba(244, 63, 94, .2);
            color: var(--red);
        }

        .flash.info {
            background: rgba(124, 108, 252, .08);
            border: 1px solid rgba(124, 108, 252, .2);
            color: var(--brand-light);
        }

        .flash.warning {
            background: rgba(251, 191, 36, .08);
            border: 1px solid rgba(251, 191, 36, .2);
            color: var(--yellow);
        }

        .flash .iconify {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        .flash-msg {
            flex: 1;
        }

        .flash-dismiss {
            background: none;
            border: none;
            cursor: pointer;
            color: inherit;
            opacity: .6;
            padding: 2px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            transition: opacity .15s;
        }

        .flash-dismiss:hover {
            opacity: 1;
        }

        .flash-dismiss .iconify {
            width: 14px;
            height: 14px;
        }

        .flash.hiding {
            animation: flashOut .3s ease forwards;
        }

        @keyframes flashOut {
            to {
                opacity: 0;
                transform: translateY(-6px);
                max-height: 0;
                margin: 0;
                padding: 0;
                border-width: 0;
            }
        }

        /* ── IMPERSONATE BANNER ── */
        .impersonate-banner {
            background: rgba(251, 191, 36, .08);
            border-bottom: 1px solid rgba(251, 191, 36, .2);
            padding: 8px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 12px;
            color: var(--yellow);
            font-weight: 600;
            gap: 12px;
        }

        .impersonate-banner .banner-left {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .impersonate-banner .iconify {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
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

        /* ── SHARED FORM ELEMENTS ── */
        label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-2);
            letter-spacing: -.1px;
            margin-bottom: 7px;
        }

        input[type="text"],
        input[type="number"],
        input[type="email"],
        input[type="password"],
        input[type="date"],
        textarea,
        select {
            width: 100%;
            padding: 11px 14px;
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            transition: border-color .15s, box-shadow .15s;
            outline: none;
            appearance: none;
        }

        input:focus,
        textarea:focus,
        select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px var(--brand-glow);
            background: var(--surface-3);
        }

        input::placeholder,
        textarea::placeholder {
            color: var(--text-3);
        }

        textarea {
            resize: vertical;
            min-height: 80px;
            line-height: 1.6;
        }

        select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23606078' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 36px;
            cursor: pointer;
        }

        select option {
            background: var(--surface-2);
        }

        .btn-primary {
            padding: 12px 24px;
            border: none;
            border-radius: var(--radius);
            cursor: pointer;
            background: linear-gradient(135deg, var(--brand), #6356e8);
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: -.1px;
            transition: all .2s;
            box-shadow: 0 4px 20px var(--brand-glow);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 8px 28px var(--brand-glow);
        }

        .btn-primary:disabled {
            opacity: .55;
            cursor: not-allowed;
        }

        .card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            box-shadow: var(--glass-shadow);
            transition: all 0.3s ease;
        }

        .card:hover {
            border-color: var(--glass-border-2);
            box-shadow: var(--glass-shadow-lg);
            transform: translateY(-2px);
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group:last-child {
            margin-bottom: 0;
        }

        /* ── TOAST ── */
        .toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 9999;
            padding: 12px 18px;
            background: var(--glass-bg);
            backdrop-filter: blur(16px) saturate(180%);
            border: 1px solid var(--glass-border-2);
            border-radius: var(--radius);
            font-size: 13px;
            font-weight: 500;
            color: var(--text);
            box-shadow: var(--glass-shadow-lg);
            transform: translateY(20px) scale(.97);
            opacity: 0;
            transition: all .25s cubic-bezier(.34, 1.3, .64, 1);
            pointer-events: none;
            max-width: 320px;
        }

        .toast.show {
            transform: translateY(0) scale(1);
            opacity: 1;
        }

        /* ── Shared spinner animation ── */
        @keyframes btnSpin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ── Input with icon (shared) ── */
        .input-icon-wrap {
            position: relative
        }

        .input-icon-wrap .iconify {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: var(--text-3);
            pointer-events: none;
            z-index: 1
        }

        .input-icon-wrap input {
            padding-left: 40px !important
        }

        .input-icon-wrap textarea {
            padding-left: 40px !important
        }

        .input-icon-wrap.textarea-wrap .iconify {
            top: 14px;
            transform: none
        }

        /* ── Profile saved flash ── */
        .profile-saved {
            font-size: 13px;
            color: var(--green);
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: opacity .4s
        }

        .profile-saved .iconify {
            font-size: 16px
        }

        /* ── Delete modal ── */
        .delete-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, .75);
            backdrop-filter: blur(8px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
            padding: 20px;
            animation: fadeIn .3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        .delete-modal-overlay.open {
            display: flex
        }

        .delete-modal {
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(244, 63, 94, .3);
            border-radius: var(--radius-lg);
            padding: 28px;
            width: 100%;
            max-width: 420px;
            box-shadow: var(--glass-shadow-lg);
            animation: slideUp .3s ease
        }

        .delete-modal h3 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #f43f5e;
            display: flex;
            align-items: center;
            gap: 8px
        }

        .delete-modal p {
            font-size: 13px;
            color: var(--text-2);
            line-height: 1.65;
            margin-bottom: 20px
        }

        .delete-modal-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 4px
        }

        .btn-cancel {
            padding: 9px 18px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
            background: transparent;
            color: var(--text-2);
            font-size: 13px;
            cursor: pointer;
            transition: .2s
        }

        .btn-cancel:hover {
            background: var(--surface);
            color: var(--text)
        }

        .btn-danger-confirm {
            padding: 9px 18px;
            border-radius: var(--radius);
            border: none;
            background: linear-gradient(135deg, #f43f5e, #e11d48);
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: .2s;
            display: inline-flex;
            align-items: center;
            gap: 6px
        }

        .btn-danger-confirm:hover {
            opacity: .88
        }

        @media (max-width:768px) {
            .topbar {
                padding: 0 16px;
            }

            .topbar-nav {
                display: none;
            }
        }
    </style>
    @stack('styles')
</head>

<body>

    <!-- Topbar -->
    <nav class="topbar">
        {{-- Logo --}}
        @auth
            @if (auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}" class="logo">
                @elseif(auth()->user()->isStreamer())
                    <a href="{{ route('streamer.dashboard') }}" class="logo">
                    @else
                        <span class="logo">
            @endif
        @else
            <span class="logo">
            @endauth
            <div class="logo-icon">
                <span class="iconify" data-icon="solar:gamepad-bold-duotone"></span>
            </div>
            <div class="logo-text">Stream<span>Donate</span></div>
            @auth
                @if (auth()->user()->isAdmin() || auth()->user()->isStreamer())
                    </a>
                @else
            </span>
            @endif
        @else
            </span>
        @endauth

    {{-- Nav links --}}
        <div class="topbar-nav">
            <a href="{{ route('policies.index') }}" class="nav-link {{ request()->routeIs('policies.*') ? 'active' : '' }}">
                <span class="iconify" data-icon="solar:document-text-bold-duotone"></span>Policies
            </a>
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <span class="iconify" data-icon="solar:widget-bold-duotone"></span>Dashboard
                    </a>
                    <a href="{{ route('admin.users') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                        <span class="iconify" data-icon="solar:users-group-rounded-bold-duotone"></span>Users
                    </a>
                    <a href="{{ route('admin.donations') }}" class="nav-link {{ request()->routeIs('admin.donations*') ? 'active' : '' }}">
                        <span class="iconify" data-icon="solar:wallet-money-bold-duotone"></span>Donasi
                    </a>
                    <a href="{{ route('admin.logs') }}" class="nav-link {{ request()->routeIs('admin.logs*') ? 'active' : '' }}">
                        <span class="iconify" data-icon="solar:document-text-bold-duotone"></span>Logs
                    </a>
                @elseif(auth()->user()->isStreamer())
                    <a href="{{ route('streamer.dashboard') }}" class="nav-link {{ request()->routeIs('streamer.dashboard') ? 'active' : '' }}">
                        <span class="iconify" data-icon="solar:widget-bold-duotone"></span>Dashboard
                    </a>
                    <a href="{{ route('streamer.settings') }}" class="nav-link {{ request()->routeIs('streamer.settings*') ? 'active' : '' }}">
                        <span class="iconify" data-icon="solar:settings-bold-duotone"></span>Settings
                    </a>
                    <a href="{{ route('streamer.reports') }}" class="nav-link {{ request()->routeIs('streamer.reports*') ? 'active' : '' }}">
                        <span class="iconify" data-icon="solar:chart-bold-duotone"></span>Laporan
                    </a>
                    <a href="{{ route('streamer.obs-canvas') }}" class="nav-link {{ request()->routeIs('streamer.obs-canvas*') ? 'active' : '' }}">
                        <span class="iconify" data-icon="solar:monitor-bold-duotone"></span>OBS Canvas
                    </a>
                    <a href="{{ route('streamer.widgets') }}" class="nav-link {{ request()->routeIs('streamer.widgets*') ? 'active' : '' }}">
                        <span class="iconify" data-icon="solar:palette-bold-duotone"></span>Widget Studio
                    </a>
                    @if(auth()->user()->streamer)
                        <a href="{{ route('donate.show', auth()->user()->streamer->slug) }}" class="nav-link nav-external" target="_blank">
                            <span class="iconify" data-icon="solar:heart-send-bold-duotone"></span>Form Donasi
                            <span class="iconify" data-icon="solar:arrow-right-up-bold" style="width:11px;height:11px;opacity:.5"></span>
                        </a>
                    @endif
                @endif
            @endauth
        </div>

    {{-- Right side --}}
        <div class="topbar-right">
            @auth
                {{-- Impersonate stop button --}}
                @if (session('impersonating_admin_id'))
                    <form method="POST" action="{{ route('admin.impersonate.stop') }}" style="display:inline">
                        @csrf
                        <button type="submit" class="btn-sm btn-danger">
                            <span class="iconify" data-icon="solar:shield-warning-bold-duotone"
                                style="width:14px;height:14px"></span>
                            Stop Impersonate
                        </button>
                    </form>
                @endif

                {{-- User dropdown --}}
                <details class="user-dropdown" id="user-dropdown">
                    <summary>
                        <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                        <span class="user-name">{{ auth()->user()->name }}</span>
                        <span class="user-role-badge">{{ auth()->user()->role }}</span>
                        <span class="dropdown-caret">
                            <span class="iconify" data-icon="solar:alt-arrow-down-bold"></span>
                        </span>
                    </summary>
                    <div class="user-dropdown-menu">
                        <div class="dropdown-header">
                            <div class="dropdown-user-full">{{ auth()->user()->name }}</div>
                            <div class="dropdown-user-email">{{ auth()->user()->email }}</div>
                        </div>
                        @if (auth()->user()->isStreamer())
                            <a href="{{ route('streamer.settings') }}" class="dropdown-item">
                                <span class="iconify" data-icon="solar:user-bold-duotone"></span>
                                Profil & Settings
                            </a>
                        @endif
                        <div class="dropdown-divider"></div>
                        <div class="dropdown-form">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-submit">
                                    <span class="iconify" data-icon="solar:logout-bold-duotone"></span>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </details>
            @endauth
        </div>
    </nav>

    <div class="page-wrap">
        {{-- Impersonate banner --}}
        @if (session('impersonating_admin_id'))
            <div class="impersonate-banner">
                <span class="banner-left">
                    <span class="iconify" data-icon="solar:shield-warning-bold-duotone"></span>
                    Kamu sedang impersonate sebagai <strong>{{ auth()->user()->name }}</strong>
                </span>
                <form method="POST" action="{{ route('admin.impersonate.stop') }}">
                    @csrf
                    <button type="submit" class="btn-sm" style="font-size:11px;padding:4px 12px">Kembali ke
                        Admin</button>
                </form>
            </div>
        @endif

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="flash success" id="flash-success">
                <span class="iconify" data-icon="solar:check-circle-bold-duotone"></span>
                <span class="flash-msg">{{ session('success') }}</span>
                <button class="flash-dismiss" onclick="dismissFlash('flash-success')" aria-label="Tutup">
                    <span class="iconify" data-icon="solar:close-circle-bold"></span>
                </button>
            </div>
        @endif
        @if (session('error'))
            <div class="flash error" id="flash-error">
                <span class="iconify" data-icon="solar:close-circle-bold-duotone"></span>
                <span class="flash-msg">{{ session('error') }}</span>
                <button class="flash-dismiss" onclick="dismissFlash('flash-error')" aria-label="Tutup">
                    <span class="iconify" data-icon="solar:close-circle-bold"></span>
                </button>
            </div>
        @endif
        @if (session('info'))
            <div class="flash info" id="flash-info">
                <span class="iconify" data-icon="solar:info-circle-bold-duotone"></span>
                <span class="flash-msg">{{ session('info') }}</span>
                <button class="flash-dismiss" onclick="dismissFlash('flash-info')" aria-label="Tutup">
                    <span class="iconify" data-icon="solar:close-circle-bold"></span>
                </button>
            </div>
        @endif
        @if (session('warning'))
            <div class="flash warning" id="flash-warning">
                <span class="iconify" data-icon="solar:danger-triangle-bold-duotone"></span>
                <span class="flash-msg">{{ session('warning') }}</span>
                <button class="flash-dismiss" onclick="dismissFlash('flash-warning')" aria-label="Tutup">
                    <span class="iconify" data-icon="solar:close-circle-bold"></span>
                </button>
            </div>
        @endif
        @if (session('status'))
            <div class="flash info" id="flash-status">
                <span class="iconify" data-icon="solar:info-circle-bold-duotone"></span>
                <span class="flash-msg">{{ session('status') }}</span>
                <button class="flash-dismiss" onclick="dismissFlash('flash-status')" aria-label="Tutup">
                    <span class="iconify" data-icon="solar:close-circle-bold"></span>
                </button>
            </div>
        @endif

        {{ $slot }}
    </div>

    <div class="toast" id="global-toast"></div>

    <script>
        // ── Toast ──
        function showToast(msg, duration = 3000) {
            const t = document.getElementById('global-toast');
            t.textContent = msg;
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), duration);
        }

        function copyText(text, label) {
            const msg = (label || 'Teks') + ' disalin!';
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(() => showToast(msg)).catch(() => _copyFallback(text, msg));
            } else {
                _copyFallback(text, msg);
            }
        }

        function _copyFallback(text, msg) {
            const ta = document.createElement('textarea');
            ta.value = text;
            ta.style.cssText = 'position:fixed;top:-9999px;left:-9999px;opacity:0';
            document.body.appendChild(ta);
            ta.focus();
            ta.select();
            try {
                document.execCommand('copy');
                showToast(msg);
            } catch (e) {
                showToast('Gagal menyalin. Salin manual.');
            }
            document.body.removeChild(ta);
        }

        // ── Flash dismiss + auto-hide ──
        function dismissFlash(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.add('hiding');
            el.addEventListener('animationend', () => el.remove(), {
                once: true
            });
        }
        document.addEventListener('DOMContentLoaded', function() {
            ['flash-success', 'flash-error', 'flash-info', 'flash-warning', 'flash-status'].forEach(function(id) {
                const el = document.getElementById(id);
                if (el) setTimeout(() => dismissFlash(id), 6000);
            });

            // Close user dropdown when clicking outside
            const dropdown = document.getElementById('user-dropdown');
            if (dropdown) {
                document.addEventListener('click', function(e) {
                    if (dropdown.open && !dropdown.contains(e.target)) {
                        dropdown.removeAttribute('open');
                    }
                });
            }
        });
    </script>
    @stack('scripts')
</body>

</html>
