<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>StreamDonate — Platform Donasi untuk Streamer Indonesia</title>
    <meta name="description" content="Platform donasi terpercaya khusus untuk streamer Indonesia. Terima donasi realtime, alert kustom, dan kelola supporter Anda dengan mudah.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --purple: #7C3AED;
            --purple-light: #A78BFA;
            --purple-dark: #5B21B6;
            --pink: #EC4899;
            --cyan: #06B6D4;
            --dark: #0A0A14;
            --dark-2: #12121F;
            --dark-3: #1A1A2E;
            --card: #16162A;
            --border: rgba(255,255,255,0.08);
            --text: #E2E8F0;
            --muted: #94A3B8;
            --white: #FFFFFF;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark);
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* ===================== SCROLLBAR ===================== */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--dark); }
        ::-webkit-scrollbar-thumb { background: var(--purple); border-radius: 3px; }

        /* ===================== NOISE OVERLAY ===================== */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
            opacity: 0.4;
        }

        /* ===================== NAVBAR ===================== */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            background: rgba(10, 10, 20, 0.7);
            border-bottom: 1px solid var(--border);
            transition: all 0.3s ease;
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .logo-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--purple), var(--pink));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            box-shadow: 0 0 20px rgba(124, 58, 237, 0.4);
        }

        .logo-text {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--white);
            letter-spacing: -0.02em;
        }

        .logo-text span { color: var(--purple-light); }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 2rem;
            list-style: none;
        }

        .nav-links a {
            color: var(--muted);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        .nav-links a:hover { color: var(--white); }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .btn-ghost {
            padding: 0.5rem 1.25rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--text);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
            background: transparent;
        }

        .btn-ghost:hover {
            border-color: rgba(124, 58, 237, 0.5);
            color: var(--white);
            background: rgba(124, 58, 237, 0.1);
        }

        .btn-primary {
            padding: 0.5rem 1.25rem;
            background: linear-gradient(135deg, var(--purple), var(--purple-dark));
            border-radius: 8px;
            color: var(--white);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.3s;
            border: 1px solid rgba(124, 58, 237, 0.5);
            box-shadow: 0 0 20px rgba(124, 58, 237, 0.3);
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 0 30px rgba(124, 58, 237, 0.5);
        }

        /* ===================== HERO ===================== */
        .hero {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 8rem 2rem 4rem;
            position: relative;
            overflow: hidden;
        }

        /* Glow blobs */
        .glow-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.15;
            pointer-events: none;
        }

        .glow-1 {
            width: 600px;
            height: 600px;
            background: var(--purple);
            top: -200px;
            left: -200px;
            animation: float 8s ease-in-out infinite;
        }

        .glow-2 {
            width: 500px;
            height: 500px;
            background: var(--pink);
            bottom: -150px;
            right: -150px;
            animation: float 10s ease-in-out infinite reverse;
        }

        .glow-3 {
            width: 400px;
            height: 400px;
            background: var(--cyan);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            animation: pulse-glow 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) scale(1); }
            50%       { transform: translateY(-30px) scale(1.05); }
        }

        @keyframes pulse-glow {
            0%, 100% { opacity: 0.08; transform: translate(-50%, -50%) scale(1); }
            50%       { opacity: 0.15; transform: translate(-50%, -50%) scale(1.2); }
        }

        /* Grid lines */
        .hero-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(124,58,237,0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(124,58,237,0.05) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(124, 58, 237, 0.15);
            border: 1px solid rgba(124, 58, 237, 0.3);
            border-radius: 9999px;
            padding: 0.35rem 1rem;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--purple-light);
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
            position: relative;
            z-index: 1;
            animation: fadeUp 0.6s ease both;
        }

        .badge-dot {
            width: 6px;
            height: 6px;
            background: var(--purple-light);
            border-radius: 50%;
            animation: blink 2s ease infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.2; }
        }

        .hero-title {
            font-size: clamp(2.5rem, 6vw, 5rem);
            font-weight: 900;
            line-height: 1.1;
            letter-spacing: -0.03em;
            color: var(--white);
            max-width: 800px;
            position: relative;
            z-index: 1;
            animation: fadeUp 0.7s 0.1s ease both;
        }

        .hero-title .gradient-text {
            background: linear-gradient(135deg, var(--purple-light), var(--pink), var(--cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: clamp(1rem, 2vw, 1.2rem);
            color: var(--muted);
            max-width: 560px;
            margin: 1.25rem auto 2.5rem;
            font-weight: 400;
            position: relative;
            z-index: 1;
            animation: fadeUp 0.7s 0.2s ease both;
        }

        .hero-cta {
            display: flex;
            align-items: center;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            position: relative;
            z-index: 1;
            animation: fadeUp 0.7s 0.3s ease both;
        }

        .btn-cta {
            padding: 0.85rem 2rem;
            background: linear-gradient(135deg, var(--purple), var(--pink));
            border-radius: 12px;
            color: var(--white);
            text-decoration: none;
            font-size: 1rem;
            font-weight: 700;
            transition: all 0.3s;
            border: 1px solid rgba(255,255,255,0.1);
            box-shadow: 0 0 40px rgba(124, 58, 237, 0.4);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 60px rgba(124, 58, 237, 0.6);
        }

        .btn-outline-cta {
            padding: 0.85rem 2rem;
            border: 1px solid var(--border);
            border-radius: 12px;
            color: var(--text);
            text-decoration: none;
            font-size: 1rem;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(255,255,255,0.03);
        }

        .btn-outline-cta:hover {
            border-color: rgba(124, 58, 237, 0.5);
            background: rgba(124, 58, 237, 0.08);
            color: var(--white);
        }

        /* Stats bar */
        .hero-stats {
            position: relative;
            z-index: 1;
            display: flex;
            align-items: center;
            gap: 3rem;
            margin-top: 4rem;
            padding-top: 2rem;
            border-top: 1px solid var(--border);
            animation: fadeUp 0.7s 0.4s ease both;
        }

        .stat-item { text-align: center; }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--white);
            letter-spacing: -0.02em;
            line-height: 1;
        }

        .stat-value .stat-accent { color: var(--purple-light); }

        .stat-label {
            font-size: 0.8rem;
            color: var(--muted);
            margin-top: 0.25rem;
            font-weight: 500;
        }

        .stat-divider {
            width: 1px;
            height: 40px;
            background: var(--border);
        }

        /* Mock dashboard preview */
        .hero-preview {
            position: relative;
            z-index: 1;
            margin-top: 4rem;
            width: 100%;
            max-width: 960px;
            animation: fadeUp 0.8s 0.5s ease both;
        }

        .preview-browser {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow:
                0 0 0 1px rgba(124,58,237,0.1),
                0 40px 80px rgba(0,0,0,0.5),
                0 0 120px rgba(124,58,237,0.15);
        }

        .browser-bar {
            background: var(--dark-3);
            padding: 0.75rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            border-bottom: 1px solid var(--border);
        }

        .browser-dots { display: flex; gap: 0.35rem; }

        .browser-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .dot-red   { background: #FF5F57; }
        .dot-yellow { background: #FFBD2E; }
        .dot-green { background: #28C840; }

        .browser-url-bar {
            flex: 1;
            background: var(--dark-2);
            border-radius: 6px;
            padding: 0.3rem 0.75rem;
            font-size: 0.75rem;
            color: var(--muted);
        }

        .browser-content {
            padding: 1.5rem;
            display: grid;
            grid-template-columns: 180px 1fr 1fr;
            gap: 1rem;
            min-height: 280px;
        }

        /* Sidebar mock */
        .mock-sidebar {
            background: var(--dark-2);
            border-radius: 10px;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .mock-sidebar-item {
            height: 32px;
            border-radius: 6px;
            background: rgba(255,255,255,0.04);
        }

        .mock-sidebar-item.active {
            background: rgba(124, 58, 237, 0.25);
        }

        /* Stats cards mock */
        .mock-stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
            align-content: start;
        }

        .mock-stat-card {
            background: var(--dark-2);
            border-radius: 10px;
            padding: 1rem;
            border: 1px solid var(--border);
        }

        .mock-stat-line {
            height: 10px;
            border-radius: 4px;
            background: rgba(255,255,255,0.06);
            margin-bottom: 0.5rem;
        }

        .mock-stat-line:first-child {
            width: 60%;
            background: rgba(124, 58, 237, 0.3);
        }

        .mock-stat-number {
            width: 80%;
            height: 20px;
            border-radius: 4px;
            background: rgba(255,255,255,0.08);
            margin-top: 0.25rem;
        }

        /* Chart mock */
        .mock-chart {
            background: var(--dark-2);
            border-radius: 10px;
            padding: 1rem;
            border: 1px solid var(--border);
            display: flex;
            align-items: flex-end;
            gap: 0.35rem;
        }

        .mock-bar {
            flex: 1;
            border-radius: 4px 4px 0 0;
            background: rgba(124, 58, 237, 0.3);
            transition: background 0.3s;
        }

        .mock-bar.highlight { background: linear-gradient(180deg, var(--purple), var(--pink)); }

        /* ===================== SECTION BASE ===================== */
        section {
            padding: 6rem 2rem;
            position: relative;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
        }

        .section-tag {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--purple-light);
            margin-bottom: 0.75rem;
        }

        .section-title {
            font-size: clamp(1.75rem, 3.5vw, 2.75rem);
            font-weight: 800;
            color: var(--white);
            letter-spacing: -0.02em;
            line-height: 1.2;
            margin-bottom: 1rem;
        }

        .section-desc {
            font-size: 1.05rem;
            color: var(--muted);
            max-width: 520px;
            font-weight: 400;
        }

        /* ===================== FEATURES ===================== */
        .features { background: var(--dark-2); }

        .features-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .feature-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(124,58,237,0.05), transparent);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .feature-card:hover::before { opacity: 1; }

        .feature-card:hover {
            border-color: rgba(124, 58, 237, 0.4);
            transform: translateY(-4px);
            box-shadow: 0 20px 60px rgba(0,0,0,0.3), 0 0 40px rgba(124,58,237,0.1);
        }

        .feature-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1.25rem;
        }

        .icon-purple { background: rgba(124, 58, 237, 0.2); }
        .icon-pink   { background: rgba(236, 72, 153, 0.2); }
        .icon-cyan   { background: rgba(6, 182, 212, 0.2);  }
        .icon-green  { background: rgba(16, 185, 129, 0.2); }
        .icon-orange { background: rgba(245, 158, 11, 0.2); }
        .icon-rose   { background: rgba(244, 63, 94, 0.2);  }

        .feature-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 0.5rem;
        }

        .feature-desc {
            font-size: 0.9rem;
            color: var(--muted);
            line-height: 1.6;
        }

        /* ===================== HOW IT WORKS ===================== */
        .how-it-works { background: var(--dark); }

        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 2rem;
            margin-top: 3.5rem;
            position: relative;
        }

        .steps-grid::before {
            content: '';
            position: absolute;
            top: 2rem;
            left: calc(12.5% + 1.5rem);
            right: calc(12.5% + 1.5rem);
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--purple), transparent);
        }

        .step-card { text-align: center; }

        .step-num {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--purple), var(--pink));
            color: var(--white);
            font-size: 1.1rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            box-shadow: 0 0 30px rgba(124,58,237,0.4);
            position: relative;
            z-index: 1;
        }

        .step-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 0.5rem;
        }

        .step-desc {
            font-size: 0.875rem;
            color: var(--muted);
        }

        /* ===================== LIVE DEMO ===================== */
        .live-demo { background: var(--dark-2); }

        .demo-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4rem;
            align-items: center;
            margin-top: 3rem;
        }

        .demo-alert-preview {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 2rem;
            position: relative;
        }

        .alert-notification {
            background: linear-gradient(135deg, rgba(124,58,237,0.15), rgba(236,72,153,0.15));
            border: 1px solid rgba(124,58,237,0.3);
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            animation: slideInUp 0.5s ease both;
        }

        .alert-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--purple), var(--pink));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .alert-text .alert-name {
            font-weight: 700;
            color: var(--white);
            font-size: 0.95rem;
        }

        .alert-text .alert-amount {
            color: var(--purple-light);
            font-size: 1.1rem;
            font-weight: 800;
        }

        .alert-text .alert-msg {
            font-size: 0.8rem;
            color: var(--muted);
            margin-top: 0.15rem;
        }

        .alert-stack {
            margin-top: 1rem;
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .alert-mini {
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
        }

        .alert-mini-name { color: var(--text); font-weight: 600; }
        .alert-mini-amount { color: var(--cyan); font-weight: 700; }

        .demo-features {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .demo-feature-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .demo-feature-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(124,58,237,0.15);
            border: 1px solid rgba(124,58,237,0.25);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            flex-shrink: 0;
        }

        .demo-feature-text .demo-feature-title {
            font-weight: 700;
            color: var(--white);
            font-size: 0.95rem;
            margin-bottom: 0.2rem;
        }

        .demo-feature-text .demo-feature-desc {
            font-size: 0.85rem;
            color: var(--muted);
        }

        /* ===================== TESTIMONIALS ===================== */
        .testimonials { background: var(--dark); }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 3rem;
        }

        .testimonial-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 1.75rem;
            transition: all 0.3s;
        }

        .testimonial-card:hover {
            border-color: rgba(124,58,237,0.3);
            transform: translateY(-2px);
        }

        .testimonial-stars {
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        .testimonial-text {
            font-size: 0.9rem;
            color: var(--muted);
            line-height: 1.7;
            margin-bottom: 1.25rem;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .author-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .author-name { font-weight: 700; color: var(--white); font-size: 0.875rem; }
        .author-handle { font-size: 0.775rem; color: var(--muted); }

        /* ===================== CTA BANNER ===================== */
        .cta-section {
            background: var(--dark-2);
            padding: 6rem 2rem;
        }

        .cta-box {
            background: linear-gradient(135deg, rgba(124,58,237,0.15), rgba(236,72,153,0.1), rgba(6,182,212,0.05));
            border: 1px solid rgba(124,58,237,0.3);
            border-radius: 24px;
            padding: 4rem 3rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            max-width: 800px;
            margin: 0 auto;
        }

        .cta-box::before {
            content: '';
            position: absolute;
            top: -100px;
            left: 50%;
            transform: translateX(-50%);
            width: 400px;
            height: 400px;
            background: var(--purple);
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.1;
        }

        .cta-box .section-title { font-size: clamp(1.5rem, 3vw, 2.25rem); margin-bottom: 1rem; }
        .cta-box .section-desc { margin: 0 auto 2rem; max-width: 440px; }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* ===================== FOOTER ===================== */
        footer {
            background: var(--dark);
            border-top: 1px solid var(--border);
            padding: 3rem 2rem 2rem;
        }

        .footer-inner {
            max-width: 1100px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 3rem;
            margin-bottom: 2.5rem;
        }

        .footer-brand p {
            font-size: 0.875rem;
            color: var(--muted);
            margin-top: 0.75rem;
            max-width: 260px;
            line-height: 1.6;
        }

        .footer-col h4 {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--white);
            margin-bottom: 1rem;
        }

        .footer-col ul { list-style: none; }
        .footer-col ul li { margin-bottom: 0.6rem; }
        .footer-col ul li a {
            color: var(--muted);
            text-decoration: none;
            font-size: 0.85rem;
            transition: color 0.2s;
        }
        .footer-col ul li a:hover { color: var(--white); }

        .footer-bottom {
            max-width: 1100px;
            margin: 0 auto;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .footer-copy {
            font-size: 0.8rem;
            color: var(--muted);
        }

        .footer-copy span { color: var(--purple-light); }

        .footer-links {
            display: flex;
            gap: 1.5rem;
        }

        .footer-links a {
            font-size: 0.8rem;
            color: var(--muted);
            text-decoration: none;
            transition: color 0.2s;
        }

        .footer-links a:hover { color: var(--white); }

        /* Shine badge */
        .badge-platform {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(6, 182, 212, 0.1);
            border: 1px solid rgba(6, 182, 212, 0.25);
            border-radius: 9999px;
            padding: 0.3rem 0.85rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--cyan);
            margin-bottom: 0.75rem;
        }

        /* ===================== ANIMATIONS ===================== */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Intersection observer reveal */
        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ===================== RESPONSIVE ===================== */
        @media (max-width: 768px) {
            nav { padding: 0.75rem 1.25rem; }
            .nav-links { display: none; }

            .hero-stats { gap: 1.5rem; }
            .stat-value { font-size: 1.4rem; }

            .browser-content {
                grid-template-columns: 1fr;
                min-height: auto;
            }

            .mock-sidebar { display: none; }

            .demo-layout {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .footer-inner {
                grid-template-columns: 1fr 1fr;
                gap: 2rem;
            }

            .footer-brand { grid-column: span 2; }

            .steps-grid::before { display: none; }
        }

        @media (max-width: 480px) {
            .hero-stats { flex-direction: column; gap: 1rem; }
            .stat-divider { width: 40px; height: 1px; }

            .footer-inner { grid-template-columns: 1fr; }
            .footer-brand { grid-column: 1; }
        }
    </style>
</head>
<body>

    {{-- ===================== NAVBAR ===================== --}}
    <nav>
        <a href="#" class="nav-logo">
            <div class="logo-icon">🎮</div>
            <span class="logo-text">Stream<span>Donate</span></span>
        </a>
        <ul class="nav-links">
            <li><a href="#features">Fitur</a></li>
            <li><a href="#how-it-works">Cara Kerja</a></li>
            <li><a href="#testimonials">Testimoni</a></li>
        </ul>
        <div class="nav-actions">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn-primary">🚀 Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn-ghost" id="btn-login">Masuk</a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-primary">✨ Daftar Gratis</a>
                @endif
            @endauth
        </div>
    </nav>

    {{-- ===================== HERO ===================== --}}
    <section class="hero">
        <div class="glow-blob glow-1"></div>
        <div class="glow-blob glow-2"></div>
        <div class="glow-blob glow-3"></div>
        <div class="hero-grid"></div>

        <div class="hero-badge">
            <div class="badge-dot"></div>
            Platform Donasi #1 untuk Streamer Indonesia
        </div>

        <h1 class="hero-title">
            Terima Donasi,<br>
            <span class="gradient-text">Hype Stream-mu! 🔥</span>
        </h1>

        <p class="hero-subtitle">
            Platform donasi realtime paling mudah dan modern untuk streamer. Alert kustom, manajemen supporter, laporan lengkap — semua di satu dashboard.
        </p>

        <div class="hero-cta">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn-cta">
                    🚀 Buka Dashboard →
                </a>
            @else
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="btn-cta">
                        ✨ Mulai Gratis Sekarang →
                    </a>
                @endif
                <a href="{{ route('login') }}" class="btn-outline-cta" id="btn-masuk">
                    🔑 Sudah punya akun
                </a>
            @endauth
        </div>

        <div class="hero-stats">
            <div class="stat-item">
                <div class="stat-value"><span class="stat-accent">1.2K+</span></div>
                <div class="stat-label">Streamer Aktif</div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
                <div class="stat-value"><span class="stat-accent">50Jt+</span></div>
                <div class="stat-label">Total Donasi</div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
                <div class="stat-value"><span class="stat-accent">99.9%</span></div>
                <div class="stat-label">Uptime</div>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
                <div class="stat-value"><span class="stat-accent">0s</span></div>
                <div class="stat-label">Delay Alert</div>
            </div>
        </div>

        {{-- Mock dashboard preview --}}
        <div class="hero-preview">
            <div class="preview-browser">
                <div class="browser-bar">
                    <div class="browser-dots">
                        <div class="browser-dot dot-red"></div>
                        <div class="browser-dot dot-yellow"></div>
                        <div class="browser-dot dot-green"></div>
                    </div>
                    <div class="browser-url-bar">streamdonate.app/streamer/dashboard</div>
                </div>
                <div class="browser-content">
                    <div class="mock-sidebar">
                        <div class="mock-sidebar-item active" style="margin-bottom:0.5rem;"></div>
                        @for($i = 0; $i < 5; $i++)
                            <div class="mock-sidebar-item" style="width:{{ 80 - $i * 8 }}%"></div>
                        @endfor
                    </div>

                    <div class="mock-stats">
                        <div class="mock-stat-card">
                            <div class="mock-stat-line"></div>
                            <div class="mock-stat-number"></div>
                        </div>
                        <div class="mock-stat-card">
                            <div class="mock-stat-line" style="background:rgba(236,72,153,0.3)"></div>
                            <div class="mock-stat-number"></div>
                        </div>
                        <div class="mock-stat-card" style="grid-column: span 2;">
                            <div class="mock-stat-line" style="width:40%;background:rgba(6,182,212,0.3)"></div>
                            <div class="mock-chart" style="margin-top:0.75rem; height:70px;">
                                <div class="mock-bar" style="height:40%"></div>
                                <div class="mock-bar" style="height:60%"></div>
                                <div class="mock-bar" style="height:80%"></div>
                                <div class="mock-bar highlight" style="height:100%"></div>
                                <div class="mock-bar" style="height:70%"></div>
                                <div class="mock-bar" style="height:55%"></div>
                                <div class="mock-bar" style="height:85%"></div>
                            </div>
                        </div>
                    </div>

                    <div style="display:flex; flex-direction:column; gap:0.75rem; align-content:start;">
                        <div style="background:var(--dark-2); border-radius:10px; padding:0.75rem; border:1px solid var(--border);">
                            <div style="font-size:0.65rem; color:var(--muted); font-weight:600; margin-bottom:0.5rem; text-transform:uppercase; letter-spacing:0.05em;">Donasi Terbaru</div>
                            @foreach([['👑','RajaGaming','Rp 50.000'],['⚡','ThunderX','Rp 25.000'],['🌙','MoonChan','Rp 10.000']] as $d)
                            <div class="alert-mini">
                                <span class="alert-mini-name">{{ $d[0] }} {{ $d[1] }}</span>
                                <span class="alert-mini-amount">{{ $d[2] }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== FEATURES ===================== --}}
    <section class="features" id="features">
        <div class="container">
            <div class="features-header reveal">
                <span class="section-tag">✦ Fitur Unggulan</span>
                <h2 class="section-title">Semua yang kamu butuhkan<br>ada di sini</h2>
                <p class="section-desc" style="margin:0 auto">Dirancang khusus untuk streamer Indonesia yang ingin profesional.</p>
            </div>

            <div class="features-grid">
                @php
                $features = [
                    ['icon'=>'🔴','iconClass'=>'icon-rose','title'=>'Alert Realtime','desc'=>'Notifikasi donasi muncul langsung di OBS tanpa delay. Kustomisasi suara, animasi, dan tampilan sesuka hati.'],
                    ['icon'=>'📊','iconClass'=>'icon-purple','title'=>'Dashboard Analitik','desc'=>'Lihat statistik donasi, supporter terbanyak, dan grafik tren dalam satu tampilan yang bersih dan intuitif.'],
                    ['icon'=>'🎨','iconClass'=>'icon-cyan','title'=>'OBS Widget Kustom','desc'=>'Widget overlay untuk OBS yang bisa dikustomisasi penuh. Dari leaderboard hingga progress bar milestone.'],
                    ['icon'=>'🛡️','iconClass'=>'icon-green','title'=>'Filter Kata Kasar','desc'=>'Sistem filter otomatis untuk menyaring pesan donasi yang tidak pantas. Streammu tetap aman dan nyaman.'],
                    ['icon'=>'📱','iconClass'=>'icon-orange','title'=>'Halaman Donasi Kustom','desc'=>'Setiap streamer punya link donasi personal dengan branding sendiri yang bisa dibagikan ke penonton.'],
                    ['icon'=>'📥','iconClass'=>'icon-pink','title'=>'Laporan & Export','desc'=>'Download laporan donasi dalam format CSV atau PDF. Mudah untuk laporan pajak atau audit keuangan.'],
                ];
                @endphp

                @foreach($features as $f)
                <div class="feature-card reveal" style="transition-delay: {{ $loop->index * 0.05 }}s">
                    <div class="feature-icon {{ $f['iconClass'] }}">{{ $f['icon'] }}</div>
                    <h3 class="feature-title">{{ $f['title'] }}</h3>
                    <p class="feature-desc">{{ $f['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== HOW IT WORKS ===================== --}}
    <section class="how-it-works" id="how-it-works">
        <div class="container">
            <div class="reveal" style="text-align:center; margin-bottom:0;">
                <span class="section-tag">✦ Cara Kerja</span>
                <h2 class="section-title">Mulai dalam 3 langkah mudah</h2>
                <p class="section-desc" style="margin:0 auto 0">Tidak perlu skill teknis. Setup dalam hitungan menit.</p>
            </div>

            <div class="steps-grid">
                @php
                $steps = [
                    ['num'=>'1','title'=>'Buat Akun Gratis','desc'=>'Daftar dengan email kamu dan lengkapi profil streamer dalam 2 menit.'],
                    ['num'=>'2','title'=>'Setup OBS Widget','desc'=>'Copy link widget dan paste ke OBS Browser Source. Selesai!'],
                    ['num'=>'3','title'=>'Bagikan Link Donasi','desc'=>'Share link donasi personalmu ke penonton dan mulai terima support!'],
                    ['num'=>'4','title'=>'Cuan & Seru-seruan','desc'=>'Lihat alert muncul realtime, pantau stats, dan kelola semua donasi.'],
                ];
                @endphp

                @foreach($steps as $s)
                <div class="step-card reveal" style="transition-delay: {{ $loop->index * 0.1 }}s">
                    <div class="step-num">{{ $s['num'] }}</div>
                    <h3 class="step-title">{{ $s['title'] }}</h3>
                    <p class="step-desc">{{ $s['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== LIVE DEMO PREVIEW ===================== --}}
    <section class="live-demo">
        <div class="container">
            <div class="demo-layout">
                <div class="reveal">
                    <span class="section-tag">✦ Alert Realtime</span>
                    <h2 class="section-title">Notifikasi yang bikin penonton hype</h2>
                    <p class="section-desc" style="margin-bottom:2rem">Setiap donasi tampil sebagai alert yang menarik perhatian. Kustomisasi penuh dari warna hingga animasi.</p>

                    <div class="demo-features">
                        @php
                        $demoFeatures = [
                            ['icon'=>'⚡','title'=>'0 Detik Delay','desc'=>'Alert muncul instan menggunakan teknologi Server-Sent Events (SSE).'],
                            ['icon'=>'🎵','title'=>'Suara Custom','desc'=>'Upload sound effect sendiri untuk tiap range nominal donasi.'],
                            ['icon'=>'🖌️','title'=>'Desain Bebas','desc'=>'Canvas editor untuk layout alert yang 100% sesuai tema streammu.'],
                        ];
                        @endphp
                        @foreach($demoFeatures as $df)
                        <div class="demo-feature-item">
                            <div class="demo-feature-icon">{{ $df['icon'] }}</div>
                            <div class="demo-feature-text">
                                <div class="demo-feature-title">{{ $df['title'] }}</div>
                                <div class="demo-feature-desc">{{ $df['desc'] }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="demo-alert-preview reveal">
                    <div style="font-size:0.7rem; text-transform:uppercase; letter-spacing:0.1em; color:var(--muted); font-weight:700; margin-bottom:1rem;">
                        🔴 Live Demo Alert
                    </div>

                    <div class="alert-notification" id="demo-alert">
                        <div class="alert-avatar" id="demo-avatar">👑</div>
                        <div class="alert-text">
                            <div class="alert-name" id="demo-name">RajaGaming</div>
                            <div class="alert-amount" id="demo-amount">Rp 50.000</div>
                            <div class="alert-msg" id="demo-msg">"GG streamer, terus semangat ya!"</div>
                        </div>
                    </div>

                    <div class="alert-stack">
                        <div class="alert-mini">
                            <span class="alert-mini-name">⚡ ThunderBolt99</span>
                            <span class="alert-mini-amount">Rp 25.000</span>
                        </div>
                        <div class="alert-mini">
                            <span class="alert-mini-name">🌙 MoonLight</span>
                            <span class="alert-mini-amount">Rp 10.000</span>
                        </div>
                        <div class="alert-mini">
                            <span class="alert-mini-name">🎮 GamerPro</span>
                            <span class="alert-mini-amount">Rp 5.000</span>
                        </div>
                    </div>

                    <div style="margin-top:1.25rem; padding-top:1.25rem; border-top:1px solid var(--border); display:flex; justify-content:space-between; font-size:0.8rem;">
                        <span style="color:var(--muted)">Total hari ini</span>
                        <span style="color:var(--purple-light); font-weight:700">Rp 90.000</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== TESTIMONIALS ===================== --}}
    <section class="testimonials" id="testimonials">
        <div class="container">
            <div class="reveal" style="text-align:center; margin-bottom:0">
                <span class="section-tag">✦ Testimoni</span>
                <h2 class="section-title">Dipercaya ribuan streamer</h2>
                <p class="section-desc" style="margin:0 auto">Dengar langsung dari streamer yang sudah merasakan manfaatnya.</p>
            </div>

            <div class="testimonials-grid">
                @php
                $testimonials = [
                    ['avatar'=>'🎮','avatarBg'=>'rgba(124,58,237,0.3)','stars'=>'⭐⭐⭐⭐⭐','text'=>'"StreamDonate benar-benar game changer! Alert-nya keren banget dan setup-nya gampang pakai banget. Penonton saya jadi makin aktif donasi."','name'=>'ZephyrGaming','handle'=>'@zephyrgaming_id • 15K followers'],
                    ['avatar'=>'🌸','avatarBg'=>'rgba(236,72,153,0.3)','stars'=>'⭐⭐⭐⭐⭐','text'=>'"Dari yang tadinya pakai platform luar yang ribet, sekarang pakai StreamDonate jauh lebih simpel. Dashboard-nya clean dan informatif."','name'=>'SakuraStream','handle'=>'@sakurastream • 8K followers'],
                    ['avatar'=>'⚡','avatarBg'=>'rgba(6,182,212,0.3)','stars'=>'⭐⭐⭐⭐⭐','text'=>'"Fitur filter kata kasarnya TOP banget! Stream saya jadi lebih aman dan profesional. Highly recommended buat semua streamer Indonesia."','name'=>'VoltCaster','handle'=>'@voltcaster_gg • 32K followers'],
                ];
                @endphp

                @foreach($testimonials as $t)
                <div class="testimonial-card reveal" style="transition-delay: {{ $loop->index * 0.1 }}s">
                    <div class="testimonial-stars">{{ $t['stars'] }}</div>
                    <p class="testimonial-text">{{ $t['text'] }}</p>
                    <div class="testimonial-author">
                        <div class="author-avatar" style="background: {{ $t['avatarBg'] }}">{{ $t['avatar'] }}</div>
                        <div>
                            <div class="author-name">{{ $t['name'] }}</div>
                            <div class="author-handle">{{ $t['handle'] }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== CTA SECTION ===================== --}}
    <section class="cta-section">
        <div class="container">
            <div class="cta-box reveal">
                <div class="badge-platform">
                    ✦ Gratis • Tanpa Biaya Setup
                </div>
                <h2 class="section-title">Siap upgrade stream-mu? 🚀</h2>
                <p class="section-desc">Bergabung bersama ribuan streamer yang sudah memonetisasi stream-nya dengan StreamDonate. Gratis selamanya, tanpa syarat!</p>
                <div class="cta-buttons">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-cta">🚀 Buka Dashboard Saya</a>
                    @else
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-cta">✨ Daftar Gratis Sekarang →</a>
                        @endif
                        <a href="{{ route('login') }}" class="btn-outline-cta">🔑 Login ke Dashboard</a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== FOOTER ===================== --}}
    <footer>
        <div class="footer-inner">
            <div class="footer-brand">
                <a href="#" class="nav-logo">
                    <div class="logo-icon">🎮</div>
                    <span class="logo-text">Stream<span>Donate</span></span>
                </a>
                <p>Platform donasi realtime terpercaya untuk streamer Indonesia. Mudah, cepat, dan profesional.</p>
            </div>

            <div class="footer-col">
                <h4>Produk</h4>
                <ul>
                    <li><a href="#features">Fitur</a></li>
                    <li><a href="#how-it-works">Cara Kerja</a></li>
                    <li><a href="{{ route('login') }}">Dashboard</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Perusahaan</h4>
                <ul>
                    <li><a href="#">Tentang Kami</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Kontak</a></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Legal</h4>
                <ul>
                    <li><a href="#">Privasi</a></li>
                    <li><a href="#">Syarat & Ketentuan</a></li>
                    <li><a href="#">Cookie</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p class="footer-copy">© {{ date('Y') }} <span>StreamDonate</span> by VersaMorph. Dibuat dengan ❤️ di Indonesia.</p>
            <div class="footer-links">
                <a href="#">Twitter/X</a>
                <a href="#">Discord</a>
                <a href="#">Instagram</a>
            </div>
        </div>
    </footer>

    <script>
        // Smooth scroll reveal
        const reveals = document.querySelectorAll('.reveal');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('visible');
                    observer.unobserve(e.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

        reveals.forEach(r => observer.observe(r));

        // Navbar scroll effect
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('nav');
            if (window.scrollY > 50) {
                nav.style.background = 'rgba(10, 10, 20, 0.95)';
                nav.style.boxShadow = '0 4px 30px rgba(0,0,0,0.3)';
            } else {
                nav.style.background = 'rgba(10, 10, 20, 0.7)';
                nav.style.boxShadow = 'none';
            }
        });

        // Dummy live demo alert rotation
        const alerts = [
            { avatar:'👑', name:'RajaGaming', amount:'Rp 50.000', msg:'"GG streamer, terus semangat ya!"' },
            { avatar:'⚡', name:'ThunderBolt99', amount:'Rp 25.000', msg:'"W stream, ez clap!"' },
            { avatar:'🌙', name:'MoonLight', amount:'Rp 100.000', msg:'"Best streamer Indo! ❤️"' },
            { avatar:'🎮', name:'GamerPro88', amount:'Rp 15.000', msg:'"GG ez, next game!"' },
            { avatar:'🌸', name:'SakuraChan', amount:'Rp 75.000', msg:'"Stream-mu bikin hari cerah!"' },
        ];
        let alertIdx = 0;

        function rotateAlert() {
            const el = document.getElementById('demo-alert');
            el.style.opacity = '0';
            el.style.transform = 'translateY(5px)';
            el.style.transition = 'all 0.3s ease';

            setTimeout(() => {
                alertIdx = (alertIdx + 1) % alerts.length;
                const a = alerts[alertIdx];
                document.getElementById('demo-avatar').textContent = a.avatar;
                document.getElementById('demo-name').textContent = a.name;
                document.getElementById('demo-amount').textContent = a.amount;
                document.getElementById('demo-msg').textContent = a.msg;
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }, 300);
        }

        setInterval(rotateAlert, 2800);

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                e.preventDefault();
                const target = document.querySelector(a.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });
    </script>
</body>
</html>
