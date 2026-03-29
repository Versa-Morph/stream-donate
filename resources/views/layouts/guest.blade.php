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
            --bg:#070709;--bg-1:#0d0d12;
            --surface:rgba(20,20,26,0.85);
            --surface-2:#1a1a22;--surface-3:#1f1f28;
            --border:rgba(255,255,255,.08);--border-2:rgba(255,255,255,.14);
            --brand:#7c6cfc;--brand-light:#a99dff;--brand-glow:rgba(124,108,252,.22);
            --brand-deep:#6356e8;
            --success:#22d3a0;--success-glow:rgba(34,211,160,.15);
            --danger:#f43f5e;--danger-glow:rgba(244,63,94,.15);
            --warning:#f59e0b;
            --text:#f1f1f6;--text-2:#a0a0b4;--text-3:#606078;
            --radius:12px;--radius-lg:20px;--radius-xl:24px;
            --glass-bg:rgba(20, 20, 25, 0.7);
            --glass-bg-2:rgba(26, 26, 34, 0.6);
            --glass-border:rgba(255, 255, 255, 0.1);
            --glass-border-2:rgba(255, 255, 255, 0.15);
            --glass-shadow:0 8px 32px 0 rgba(0, 0, 0, 0.37);
            --glow-purple:0 0 20px rgba(124, 108, 252, 0.6);
        }
        *,*::before,*::after{margin:0;padding:0;box-sizing:border-box}

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        /* ========== FULLSCREEN BACKGROUND ========== */
        .auth-bg {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
        }

        .auth-bg-gradient {
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 50% at 20% 20%, rgba(124,108,252,.15) 0%, transparent 50%),
                radial-gradient(ellipse 60% 40% at 80% 80%, rgba(236,72,153,.12) 0%, transparent 45%),
                radial-gradient(ellipse 50% 30% at 50% 50%, rgba(6,182,212,.08) 0%, transparent 60%),
                var(--bg);
        }

        /* Canvas for stars & UFOs - fullscreen */
        .starfield-canvas {
            position: absolute;
            inset: 0;
            z-index: 1;
        }

        /* Animated gradient blobs - fullscreen */
        .auth-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.25;
            will-change: transform;
            pointer-events: none;
        }

        .auth-blob-1 {
            width: 600px; height: 600px;
            background: radial-gradient(circle, var(--brand) 0%, transparent 70%);
            top: -200px; left: -150px;
            animation: blobFloat1 18s ease-in-out infinite;
        }
        .auth-blob-2 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, #ec4899 0%, transparent 70%);
            bottom: -150px; right: -100px;
            animation: blobFloat2 22s ease-in-out infinite 2s;
        }
        .auth-blob-3 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, #06b6d4 0%, transparent 70%);
            top: 40%; right: 20%;
            animation: blobFloat3 20s ease-in-out infinite 4s;
        }
        .auth-blob-4 {
            width: 350px; height: 350px;
            background: radial-gradient(circle, #a855f7 0%, transparent 70%);
            bottom: 30%; left: 30%;
            animation: blobFloat4 24s ease-in-out infinite 1s;
        }

        @keyframes blobFloat1 {
            0%,100%{transform:translate(0,0) scale(1)}
            33%{transform:translate(100px,60px) scale(1.1)}
            66%{transform:translate(-50px,80px) scale(0.95)}
        }
        @keyframes blobFloat2 {
            0%,100%{transform:translate(0,0) scale(1)}
            50%{transform:translate(-80px,-60px) scale(1.15)}
        }
        @keyframes blobFloat3 {
            0%,100%{transform:translate(0,0) scale(1)}
            33%{transform:translate(-60px,40px) scale(1.2)}
            66%{transform:translate(40px,-30px) scale(0.9)}
        }
        @keyframes blobFloat4 {
            0%,100%{transform:translate(0,0) scale(1)}
            40%{transform:translate(50px,-40px) scale(1.1)}
            70%{transform:translate(-30px,50px) scale(0.95)}
        }

        /* Grid overlay - fullscreen */
        .auth-grid {
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(124,108,252,.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(124,108,252,.02) 1px, transparent 1px);
            background-size: 60px 60px;
            z-index: 0;
        }

        /* ========== TWO COLUMN LAYOUT ========== */
        .auth-layout {
            display: flex;
            min-height: 100vh;
            position: relative;
            z-index: 1;
        }

        /* ========== LEFT COLUMN - CONTENT ========== */
        .auth-visual {
            flex: 2;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            position: relative;
        }

        .auth-visual-content {
            position: relative;
            z-index: 2;
            text-align: center;
            max-width: 500px;
        }

        .visual-tagline-wrapper {
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
            animation: fadeInUp .8s ease both;
        }

        .visual-tagline {
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(2.2rem, 4vw, 3.5rem);
            font-weight: 900;
            line-height: 1.15;
            color: #fff;
            text-align: center;
            white-space: nowrap;
        }

        .visual-tagline .highlight {
            background: linear-gradient(135deg, var(--brand-light), #ec4899, #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .typing-cursor {
            display: inline-block;
            width: 3px;
            height: 0.9em;
            background: var(--brand-light);
            margin-left: 4px;
            animation: cursorBlink 1s infinite;
            vertical-align: baseline;
        }

        @keyframes cursorBlink {
            0%, 50% { opacity: 1; }
            51%, 100% { opacity: 0; }
        }

        .visual-desc {
            font-size: clamp(1rem, 1.5vw, 1.15rem);
            color: var(--text-2);
            line-height: 1.7;
            animation: fadeInUp .8s ease .1s both;
        }

        /* Feature highlights */
        .visual-features {
            display: flex;
            gap: 30px;
            margin-top: 48px;
            animation: fadeInUp .8s ease .2s both;
            justify-content: center;
        }

        .visual-feature {
            text-align: center;
        }

        .feature-icon {
            width: 56px; height: 56px;
            border-radius: 16px;
            background: rgba(124,108,252,.12);
            border: 1px solid rgba(124,108,252,.25);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 14px;
            transition: all .3s ease;
            backdrop-filter: blur(10px);
        }

        .feature-icon svg {
            width: 26px; height: 26px;
            color: var(--brand-light);
        }

        .visual-feature:hover .feature-icon {
            transform: translateY(-5px);
            background: rgba(124,108,252,.2);
            box-shadow: 0 15px 40px rgba(124,108,252,.25);
            border-color: rgba(124,108,252,.4);
        }

        .feature-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-2);
        }

        /* ========== RIGHT COLUMN - FORM ========== */
        .auth-form-column {
            flex: 1;
            min-width: 480px;
            max-width: 600px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 50px;
            position: relative;
        }

        .auth-wrap {
            width: 100%;
            max-width: 480px;
        }

        /* ========== LOGO ========== */
        .auth-logo {
            text-align: center; margin-bottom: 28px;
            animation: slideDown .6s cubic-bezier(.22,.68,0,1.2) both;
            cursor: pointer;
            text-decoration: none;
            display: block;
        }
        .auth-logo:hover {
            opacity: 0.8;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-25px) }
            to   { opacity: 1; transform: translateY(0) }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px) }
            to { opacity: 1; transform: translateY(0) }
        }
        .auth-logo-icon {
            width: 64px; height: 64px; border-radius: 20px;
            background: linear-gradient(135deg, var(--brand), #a855f7, #ec4899);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 28px; margin-bottom: 14px;
            box-shadow: 
                0 0 0 1px rgba(255,255,255,.1), 
                0 0 50px var(--brand-glow), 
                0 0 80px rgba(124,108,252,.3),
                0 8px 32px rgba(0,0,0,.3);
            transition: transform .35s cubic-bezier(.22,.68,0,1.2), box-shadow .3s;
            cursor: default;
            position: relative;
            animation: logoPulse 3s ease-in-out infinite;
        }
        @keyframes logoPulse {
            0%,100%{box-shadow:0 0 0 1px rgba(255,255,255,.1),0 0 50px var(--brand-glow),0 0 80px rgba(124,108,252,.3),0 8px 32px rgba(0,0,0,.3)}
            50%{box-shadow:0 0 0 1px rgba(255,255,255,.15),0 0 70px var(--brand-glow),0 0 100px rgba(124,108,252,.4),0 8px 32px rgba(0,0,0,.3)}
        }
        .auth-logo-icon:hover {
            transform: scale(1.12) rotate(-8deg);
            box-shadow: 0 0 0 1px rgba(255,255,255,.18), 0 0 80px var(--brand-glow), 0 12px 40px rgba(0,0,0,.35);
        }
        .auth-logo-icon .iconify { color: #fff; font-size: 32px; width: 32px; height: 32px; }
        .auth-logo-text {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 26px; font-weight: 800; letter-spacing: -.8px;
            background: linear-gradient(135deg, #fff, var(--brand-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .auth-logo-tagline {
            font-size: 12px; color: var(--text-3); margin-top: 6px;
            letter-spacing: .04em;
            animation: fadeInUp .6s ease .2s both;
        }

        /* ========== CARD ========== */
        .auth-card {
            background: rgba(15, 15, 20, 0.75);
            backdrop-filter: blur(40px) saturate(1.5);
            -webkit-backdrop-filter: blur(40px) saturate(1.5);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: var(--radius-xl);
            padding: 36px 32px;
            box-shadow:
                0 0 0 1px rgba(124,108,252,.1),
                0 4px 6px rgba(0,0,0,.1),
                0 25px 80px rgba(0,0,0,.5),
                inset 0 1px 0 rgba(255,255,255,.06);
            animation: cardSlideUp .6s cubic-bezier(.22,.68,0,1.2) .1s both;
            position: relative; overflow: hidden;
        }
        .auth-card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(124,108,252,.5), rgba(236,72,153,.3), transparent);
            animation: shimmer 3s ease-in-out infinite;
        }
        @keyframes shimmer {
            0%,100%{opacity:.5}
            50%{opacity:1}
        }
        .auth-card::after {
            content: '';
            position: absolute; bottom: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(124,108,252,.2), transparent);
        }
        @keyframes cardSlideUp {
            from { opacity: 0; transform: translateY(30px) scale(.97) }
            to   { opacity: 1; transform: translateY(0) scale(1) }
        }

        /* ========== TYPOGRAPHY ========== */
        .auth-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 22px; font-weight: 700; letter-spacing: -.4px;
            margin-bottom: 6px;
            background: linear-gradient(135deg, #fff, var(--text-2));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .auth-sub { font-size: 13px; color: var(--text-3); margin-bottom: 28px; line-height: 1.5; }

        /* ========== FORM ELEMENTS ========== */
        .form-group { margin-bottom: 20px; }

        label {
            display: flex; align-items: center; gap: 6px;
            font-size: 12px; font-weight: 600; color: var(--text-2);
            margin-bottom: 8px; letter-spacing: .01em;
            transition: color .2s;
        }
        label:hover { color: var(--text); }
        label .iconify { font-size: 14px; color: var(--brand-light); transition: color .2s; }

        .input-wrap { position: relative; }
        .input-wrap input { padding-right: 44px !important; }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%; padding: 13px 15px;
            background: rgba(26, 26, 34, 0.6);
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            font-size: 14px; outline: none;
            transition: all .2s ease;
            position: relative;
        }
        input:hover:not(:focus) { border-color: var(--border-2); }
        input:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 4px var(--brand-glow), 0 0 30px rgba(124,108,252,.15);
            background: rgba(30, 30, 40, 0.8);
            animation: inputGlow .5s ease;
        }
        @keyframes inputGlow {
            0%{box-shadow:0 0 0 0 var(--brand-glow)}
            50%{box-shadow:0 0 0 6px var(--brand-glow),0 0 40px rgba(124,108,252,.25)}
            100%{box-shadow:0 0 0 4px var(--brand-glow),0 0 30px rgba(124,108,252,.15)}
        }
        input.is-valid {
            border-color: rgba(34,211,160,.5);
            box-shadow: 0 0 0 3px var(--success-glow);
        }
        input.is-invalid {
            border-color: rgba(244,63,94,.5);
            box-shadow: 0 0 0 3px var(--danger-glow);
            animation: shake .4s ease;
        }
        @keyframes shake {
            0%,100%{transform:translateX(0)}
            20%,60%{transform:translateX(-4px)}
            40%,80%{transform:translateX(4px)}
        }
        input::placeholder { color: var(--text-3); }
        input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 100px rgba(30, 30, 40, 1) inset;
            -webkit-text-fill-color: var(--text);
            transition: background-color 9999s;
        }

        /* Eye button */
        .eye-btn {
            position: absolute; right: 0; top: 0; bottom: 0; width: 44px;
            background: transparent; border: none; cursor: pointer;
            color: var(--text-3);
            display: flex; align-items: center; justify-content: center;
            transition: all .2s; border-radius: 0 var(--radius) var(--radius) 0;
        }
        .eye-btn:hover { color: var(--brand-light); background: rgba(124,108,252,.05); }
        .eye-btn .iconify { font-size: 18px; width: 18px; height: 18px; }

        /* ========== BUTTONS ========== */
        .btn-submit {
            width: 100%; padding: 15px; border: none;
            border-radius: var(--radius-lg); cursor: pointer;
            background: linear-gradient(135deg, var(--brand) 0%, #a855f7 50%, var(--brand-deep) 100%);
            background-size: 200% auto;
            color: #fff;
            font-family: 'Inter', sans-serif; font-size: 15px; font-weight: 700;
            transition: all .35s ease;
            box-shadow: 
                0 4px 24px var(--brand-glow), 
                0 0 30px rgba(124, 108, 252, 0.4), 
                0 1px 0 rgba(255,255,255,.15) inset;
            display: flex; align-items: center; justify-content: center; gap: 10px;
            position: relative; overflow: hidden;
            margin-top: 24px;
        }
        .btn-submit::before {
            content: '';
            position: absolute; top: 0; left: -100%; width: 100%; height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.25), transparent);
            transition: left .6s ease;
        }
        .btn-submit:hover::before { left: 100%; }
        .btn-submit::after {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(to bottom, rgba(255,255,255,.1), transparent);
            pointer-events: none;
        }
        .btn-submit:hover {
            background-position: right center;
            transform: translateY(-3px);
            box-shadow: 
                0 8px 40px var(--brand-glow), 
                0 0 50px rgba(124, 108, 252, 0.5), 
                0 1px 0 rgba(255,255,255,.15) inset;
        }
        .btn-submit:active { 
            transform: translateY(-1px); 
            box-shadow: 0 4px 20px var(--brand-glow);
        }
        .btn-submit:disabled { 
            opacity: .6; cursor: not-allowed; transform: none; 
        }
        .btn-submit .iconify { font-size: 20px; width: 20px; height: 20px; }

        /* ========== FOOTER / MISC ========== */
        .auth-footer {
            text-align: center; margin-top: 24px;
            font-size: 13px; color: var(--text-3);
            animation: fadeInUp .5s ease .3s both;
        }
        .auth-footer a { 
            color: var(--brand-light); text-decoration: none; font-weight: 600;
            position: relative;
            transition: color .2s;
        }
        .auth-footer a::after {
            content: '';
            position: absolute; bottom: -2px; left: 0; right: 0;
            height: 1px;
            background: var(--brand-light);
            transform: scaleX(0);
            transition: transform .3s ease;
        }
        .auth-footer a:hover::after { transform: scaleX(1); }
        .auth-footer a:hover { color: #fff; }

        .error-msg {
            font-size: 11px; color: var(--danger); margin-top: 6px;
            display: flex; align-items: center; gap: 5px;
            animation: fadeInUp .3s ease;
        }

        .alert-success {
            background: rgba(34,211,160,.08);
            border: 1px solid rgba(34,211,160,.25);
            color: var(--success);
            padding: 12px 16px; border-radius: 12px;
            font-size: 13px; margin-bottom: 20px;
            display: flex; align-items: center; gap: 10px;
            animation: fadeInUp .4s ease, successPulse 2s ease infinite;
        }
        @keyframes successPulse {
            0%,100%{box-shadow:0 0 0 0 var(--success-glow)}
            50%{box-shadow:0 0 0 8px transparent}
        }
        .alert-success .iconify { font-size: 18px; }

        .checkbox-row {
            display: flex; align-items: center; gap: 10px;
            font-size: 13px; color: var(--text-2);
        }
        input[type="checkbox"] {
            width: 18px; height: 18px;
            accent-color: var(--brand);
            border-radius: 5px;
            cursor: pointer;
            transition: transform .2s;
        }
        input[type="checkbox"]:hover { transform: scale(1.1); }

        /* ========== SPIN ANIMATION ========== */
        @keyframes spin { from { transform: rotate(0deg) } to { transform: rotate(360deg) } }
        .spin { animation: spin .8s linear infinite; display: inline-block; }

        /* ========== SCROLLBAR ========== */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border-2); border-radius: 99px; }

        /* ========== FOOTER LINKS ========== */
        .auth-links {
            margin-top: 24px;
            text-align: center;
            font-size: 12px;
            color: var(--text-3);
            animation: fadeInUp .5s ease .4s both;
        }
        .auth-links a {
            color: var(--text-3);
            text-decoration: none;
            margin: 0 10px;
            transition: color .2s;
        }
        .auth-links a:hover {
            color: var(--brand-light);
        }
        .auth-links span {
            opacity: 0.4;
        }

        /* ========== SIMPLE ENTRANCE ANIMATION ========== */
        /* Simple fade in for content */
        .auth-layout {
            opacity: 0;
            animation: contentFadeIn 0.6s ease 0.1s forwards;
        }
        
        @keyframes contentFadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }
        
        .auth-bg {
            animation: bgFadeIn 0.8s ease forwards;
        }
        
        @keyframes bgFadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        /* ========== PAGE TRANSITION - RETURN TO LANDING ========== */
        .auth-layout {
            transition: opacity 0.7s ease-out, transform 0.7s ease-out;
        }
        
        .auth-layout.fading {
            opacity: 0;
            transform: translateX(40px);
        }
        
        .auth-bg.fading {
            opacity: 0;
        }
        
        .auth-bg.fading .auth-blob,
        .auth-bg.fading .auth-bg-gradient {
            animation-play-state: paused;
        }
        
        .auth-bg.fading canvas {
            animation-play-state: paused;
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 1200px) {
            .auth-visual {
                display: none;
            }
            .auth-form-column {
                flex: 1;
                min-width: unset;
                max-width: unset;
                width: 100%;
            }
        }

        @media (max-width: 540px) {
            .auth-card { padding: 28px 24px; }
            .auth-wrap { padding: 0 12px; max-width: 100%; }
            .auth-logo-icon { width: 56px; height: 56px; }
            .auth-form-column { padding: 20px; }
        }
    </style>
</head>
<body>

<!-- Fullscreen Background -->
<div class="auth-bg">
    <div class="auth-bg-gradient"></div>
    <div class="auth-grid"></div>
    <canvas class="starfield-canvas" id="starfield-canvas"></canvas>
    
    <!-- Gradient Blobs -->
    <div class="auth-blob auth-blob-1"></div>
    <div class="auth-blob auth-blob-2"></div>
    <div class="auth-blob auth-blob-3"></div>
    <div class="auth-blob auth-blob-4"></div>
</div>

<!-- Two Column Layout -->
<div class="auth-layout">
    <!-- Left Column - Content -->
    <div class="auth-visual">
        <div class="auth-visual-content">
            <div class="visual-tagline-wrapper">
                <h1 class="visual-tagline" id="typing-text"></h1>
            </div>
            <p class="visual-desc">Platform donasi modern untuk streamer Indonesia. Integrasi mudah, pembayaran aman, dan dashboard real-time.</p>
            
            <div class="visual-features">
                <div class="visual-feature">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <polyline points="9 12 11 14 15 10"/>
                        </svg>
                    </div>
                    <div class="feature-label">100% Aman</div>
                </div>
                <div class="visual-feature">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                        </svg>
                    </div>
                    <div class="feature-label">Instant Setup</div>
                </div>
                <div class="visual-feature">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="3" width="20" height="14" rx="2" ry="2"/>
                            <line x1="8" y1="21" x2="16" y2="21"/>
                            <line x1="12" y1="17" x2="12" y2="21"/>
                        </svg>
                    </div>
                    <div class="feature-label">OBS Ready</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Column - Form -->
    <div class="auth-form-column">
        <div class="auth-wrap">
            <a href="{{ route('home') }}" class="auth-logo">
                <div class="auth-logo-icon">
                    <span class="iconify" data-icon="solar:gamepad-bold-duotone"></span>
                </div>
                <div class="auth-logo-text">Tiptipan</div>
                <div class="auth-logo-tagline">Platform donasi untuk para streamer Indonesia</div>
            </a>
            <div class="auth-card">
                {{ $slot }}
            </div>
            <div class="auth-links">
                <a href="{{ route('policies.index') }}">Kebijakan</a>
                <span>|</span>
                <a href="{{ route('home') }}">Beranda</a>
                <span>|</span>
                <a href="mailto:support@tiptipan.id">Support</a>
            </div>
        </div>
    </div>
</div>

{{-- Footer --}}
<x-site-footer />

<script>
// ===================== TYPING EFFECT =====================
(function() {
    const typingElement = document.getElementById('typing-text');
    if (!typingElement) return;
    
    const phrases = [
        [
            { text: 'Terima Donasi,', delay: 70 },
            { text: 'Tanpa Ribet!', delay: 90, highlight: true }
        ],
        [
            { text: 'Alert Keren,', delay: 70 },
            { text: 'Stream Seru!', delay: 90, highlight: true }
        ],
        [
            { text: 'Donasi Masuk,', delay: 70 },
            { text: 'Langsung Cair!', delay: 90, highlight: true }
        ],
        [
            { text: 'Custom Alert,', delay: 70 },
            { text: 'Unik & Cantik!', delay: 90, highlight: true }
        ],
        [
            { text: 'Gratis Daftar,', delay: 70 },
            { text: 'Tanpa Ribet!', delay: 90, highlight: true }
        ]
    ];
    
    let phraseIndex = 0;
    let lineIndex = 0;
    let charIndex = 0;
    
    function type() {
        const currentPhrase = phrases[phraseIndex];
        const currentLine = currentPhrase[lineIndex];
        
        const targetText = currentLine.text.substring(0, charIndex + 1);
        typingElement.innerHTML = '';
        
        // Build the full text up to current line
        for (let i = 0; i < lineIndex; i++) {
            if (currentPhrase[i].highlight) {
                typingElement.innerHTML += `<span class="highlight">${currentPhrase[i].text}</span><br>`;
            } else {
                typingElement.innerHTML += currentPhrase[i].text + '<br>';
            }
        }
        
        // Add current line being typed
        if (currentLine.highlight) {
            typingElement.innerHTML += `<span class="highlight">${targetText}</span>`;
        } else {
            typingElement.innerHTML += targetText;
        }
        
        // Add cursor
        typingElement.innerHTML += '<span class="typing-cursor"></span>';
        
        charIndex++;
        
        if (charIndex >= currentLine.text.length) {
            lineIndex++;
            charIndex = 0;
            
            if (lineIndex >= currentPhrase.length) {
                // Phrase complete, remove cursor after delay
                setTimeout(() => {
                    typingElement.innerHTML = typingElement.innerHTML.replace('<span class="typing-cursor"></span>', '');
                }, 100);
                
                setTimeout(() => {
                    phraseIndex = (phraseIndex + 1) % phrases.length;
                    lineIndex = 0;
                    charIndex = 0;
                    type();
                }, 2500);
            } else {
                setTimeout(type, 350);
            }
            return;
        }
        
        setTimeout(type, currentLine.delay);
    }
    
    // Start typing
    setTimeout(type, 400);
})();

// ===================== STARFIELD & UFO =====================
// Expose speed control for page transition with smooth interpolation
window.starfieldSpeed = {
    normal: 1.2,
    fast: 3,
    current: 1.2,
    target: 1.2,
    // Smooth transition to target speed
    update: function() {
        const diff = this.target - this.current;
        this.current += diff * 0.08; // Easing factor - smooth interpolation
    },
    setTarget: function(newSpeed) {
        this.target = newSpeed;
    }
};

(function() {
    const canvas = document.getElementById('starfield-canvas');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    
    let width, height;
    let stars = [];
    const numStars = 350;
    const centerGap = 120;
    
    // Use dynamic speed from window object
    function getSpeed() {
        window.starfieldSpeed.update();
        return window.starfieldSpeed.current;
    }
    
    function resize() {
        width = canvas.width = window.innerWidth;
        height = canvas.height = window.innerHeight;
    }
    
    // Star class - hyperspace effect with trails
    class Star {
        constructor() {
            this.reset();
        }
        
        reset() {
            let angle = Math.random() * Math.PI * 2;
            let radius = centerGap + Math.random() * Math.max(width, height);
            
            this.x = width / 2 + Math.cos(angle) * radius;
            this.y = height / 2 + Math.sin(angle) * radius;
            this.dx = Math.cos(angle);
            this.dy = Math.sin(angle);
            const speed = getSpeed();
            this.vx = this.dx * speed;
            this.vy = this.dy * speed;
            this.z = 400 + Math.random() * 600;
            this.prevX = this.x;
            this.prevY = this.y;
        }
        
        update() {
            this.prevX = this.x;
            this.prevY = this.y;
            const speed = getSpeed();
            this.vx = this.dx * speed;
            this.vy = this.dy * speed;
            this.x += this.vx * (1000 / this.z);
            this.y += this.vy * (1000 / this.z);
            this.z -= speed * 2.5;
            
            const margin = 200;
            const outOfBounds = this.x < -margin || this.x > width + margin || this.y < -margin || this.y > height + margin;
            
            if (this.z <= 50 || outOfBounds) {
                this.reset();
            }
        }
        
        draw() {
            const speed = getSpeed();
            const scale = 800 / this.z;
            const screenX = this.x * scale + (width - width * scale) / 2;
            const screenY = this.y * scale + (height - height * scale) / 2;
            
            const prevScale = 800 / (this.z + speed * 2.5);
            const prevScreenX = this.prevX * prevScale + (width - width * prevScale) / 2;
            const prevScreenY = this.prevY * prevScale + (height - height * prevScale) / 2;
            
            const size = Math.max(0.3, (1.5 - this.z / 1200));
            let opacity = Math.min(1, (1200 - this.z) / 300) * Math.min(1, this.z / 100);
            opacity = Math.max(0, Math.min(0.85, opacity));
            
            const distFromCenter = Math.sqrt(Math.pow(screenX - width/2, 2) + Math.pow(screenY - height/2, 2));
            if (distFromCenter < centerGap * scale) return;
            
            const dx = screenX - prevScreenX;
            const dy = screenY - prevScreenY;
            const trailLength = Math.sqrt(dx * dx + dy * dy);
            
            if (trailLength > 2) {
                const extendFactor = 7;
                const trailEndX = screenX - dx * extendFactor;
                const trailEndY = screenY - dy * extendFactor;
                
                const gradient = ctx.createLinearGradient(trailEndX, trailEndY, screenX, screenY);
                gradient.addColorStop(0, 'rgba(255, 255, 255, 0)');
                gradient.addColorStop(0.4, `rgba(255, 255, 255, ${opacity * 0.3})`);
                gradient.addColorStop(1, `rgba(255, 255, 255, ${opacity})`);
                
                ctx.beginPath();
                ctx.moveTo(trailEndX, trailEndY);
                ctx.lineTo(screenX, screenY);
                ctx.strokeStyle = gradient;
                ctx.lineWidth = size;
                ctx.lineCap = 'round';
                ctx.stroke();
            }
            
            ctx.beginPath();
            ctx.arc(screenX, screenY, size, 0, Math.PI * 2);
            ctx.fillStyle = `rgba(255, 255, 255, ${opacity})`;
            ctx.fill();
        }
    }
    
    // UFO color variants
    const ufoColors = [
        { main: '#7C3AED', light: '#A78BFA', dark: '#5B21B6', accent: '#C4B5FD' },
        { main: '#EC4899', light: '#F472B6', dark: '#BE185D', accent: '#FBCFE8' },
        { main: '#06B6D4', light: '#22D3EE', dark: '#0891B2', accent: '#A5F3FC' },
        { main: '#A78BFA', light: '#C4B5FD', dark: '#7C3AED', accent: '#DDD6FE' },
        { main: '#F43F5E', light: '#FB7185', dark: '#E11D48', accent: '#FECDD3' }
    ];
    
    // UFO class - moves toward center
    class UFO {
        constructor(colorIndex = 0) {
            this.colorIndex = colorIndex;
            this.colors = ufoColors[colorIndex];
            this.reset();
        }
        
        reset() {
            const angle = Math.random() * Math.PI * 2;
            const radius = Math.max(width, height) * 0.55 + Math.random() * 200;
            
            this.x = width / 2 + Math.cos(angle) * radius;
            this.y = height / 2 + Math.sin(angle) * radius;
            this.angle = angle;
            this.speed = 0.5 + Math.random() * 0.35;
            this.z = 550 + Math.random() * 450;
            this.size = 16 + Math.random() * 12;
            this.wobble = 0;
            this.wobbleSpeed = 0.018 + Math.random() * 0.015;
            this.glowPhase = Math.random() * Math.PI * 2;
            this.trail = [];
            this.maxTrailLength = 22;
        }
        
        update() {
            const dirX = -Math.cos(this.angle);
            const dirY = -Math.sin(this.angle);
            
            this.trail.unshift({ x: this.x, y: this.y, z: this.z });
            if (this.trail.length > this.maxTrailLength) this.trail.pop();
            
            this.x += dirX * this.speed * (this.z / 400);
            this.y += dirY * this.speed * (this.z / 400);
            this.z -= this.speed * 1.8;
            this.wobble += this.wobbleSpeed;
            this.glowPhase += 0.045;
            
            const distFromCenter = Math.sqrt(Math.pow(this.x - width/2, 2) + Math.pow(this.y - height/2, 2));
            
            if (this.z <= 50 || distFromCenter < 90) {
                this.colorIndex = Math.floor(Math.random() * ufoColors.length);
                this.colors = ufoColors[this.colorIndex];
                this.reset();
            }
        }
        
        hexToRgba(hex, alpha) {
            const r = parseInt(hex.slice(1, 3), 16);
            const g = parseInt(hex.slice(3, 5), 16);
            const b = parseInt(hex.slice(5, 7), 16);
            return `rgba(${r}, ${g}, ${b}, ${alpha})`;
        }
        
        draw() {
            const scale = 550 / this.z;
            const screenX = this.x * scale + (width - width * scale) / 2;
            const screenY = this.y * scale + (height - height * scale) / 2;
            
            if (screenX < -100 || screenX > width + 100 || screenY < -100 || screenY > height + 100) return;
            
            const size = this.size * scale;
            const opacity = Math.min(0.88, Math.max(0.18, (950 - this.z) / 500));
            const c = this.colors;
            
            // Trail
            this.trail.forEach((point, i) => {
                const trailScale = 550 / point.z;
                const trailX = point.x * trailScale + (width - width * trailScale) / 2;
                const trailY = point.y * trailScale + (height - height * trailScale) / 2;
                const trailOpacity = opacity * (1 - i / this.trail.length) * 0.22;
                const trailSize = size * (1 - i / this.trail.length);
                
                ctx.beginPath();
                ctx.arc(trailX, trailY, trailSize * 0.22, 0, Math.PI * 2);
                ctx.fillStyle = this.hexToRgba(c.main, trailOpacity);
                ctx.fill();
            });
            
            // Glow
            const glowSize = size * 2.2;
            const gradient = ctx.createRadialGradient(screenX, screenY, 0, screenX, screenY, glowSize);
            gradient.addColorStop(0, this.hexToRgba(c.main, opacity * 0.38));
            gradient.addColorStop(0.5, this.hexToRgba(c.light, opacity * 0.18));
            gradient.addColorStop(1, this.hexToRgba(c.main, 0));
            ctx.beginPath();
            ctx.arc(screenX, screenY, glowSize, 0, Math.PI * 2);
            ctx.fillStyle = gradient;
            ctx.fill();
            
            // Body
            ctx.save();
            ctx.translate(screenX, screenY + Math.sin(this.wobble) * 1.8);
            
            // Bottom dome
            ctx.beginPath();
            ctx.ellipse(0, size * 0.13, size * 0.38, size * 0.13, 0, 0, Math.PI);
            ctx.fillStyle = this.hexToRgba(c.light, opacity);
            ctx.fill();
            
            // Main body
            ctx.beginPath();
            ctx.ellipse(0, 0, size * 0.48, size * 0.11, 0, 0, Math.PI * 2);
            const bodyGradient = ctx.createLinearGradient(-size * 0.48, 0, size * 0.48, 0);
            bodyGradient.addColorStop(0, this.hexToRgba(c.dark, opacity));
            bodyGradient.addColorStop(0.5, this.hexToRgba(c.main, opacity));
            bodyGradient.addColorStop(1, this.hexToRgba(c.dark, opacity));
            ctx.fillStyle = bodyGradient;
            ctx.fill();
            ctx.strokeStyle = this.hexToRgba(c.light, opacity);
            ctx.lineWidth = 0.9;
            ctx.stroke();
            
            // Top dome
            ctx.beginPath();
            ctx.ellipse(0, -size * 0.07, size * 0.24, size * 0.18, 0, Math.PI, 0);
            const domeGradient = ctx.createRadialGradient(0, -size * 0.13, 0, 0, -size * 0.09, size * 0.24);
            domeGradient.addColorStop(0, this.hexToRgba(c.accent, opacity * 0.88));
            domeGradient.addColorStop(1, this.hexToRgba(c.main, opacity * 0.58));
            ctx.fillStyle = domeGradient;
            ctx.fill();
            
            // Rim lights
            const numLights = 7;
            for (let i = 0; i < numLights; i++) {
                const lightAngle = (i / numLights) * Math.PI * 2 + this.glowPhase * 0.45;
                const lx = Math.cos(lightAngle) * size * 0.42;
                const ly = Math.sin(lightAngle) * size * 0.07;
                const lightIntensity = 0.5 + Math.sin(this.glowPhase + i * 0.85) * 0.5;
                
                const lightGlow = ctx.createRadialGradient(lx, ly, 0, lx, ly, size * 0.12);
                lightGlow.addColorStop(0, this.hexToRgba('#FFFFFF', opacity * lightIntensity));
                lightGlow.addColorStop(0.5, this.hexToRgba(c.light, opacity * lightIntensity * 0.65));
                lightGlow.addColorStop(1, this.hexToRgba(c.main, 0));
                ctx.beginPath();
                ctx.arc(lx, ly, size * 0.12, 0, Math.PI * 2);
                ctx.fillStyle = lightGlow;
                ctx.fill();
            }
            
            ctx.restore();
        }
    }
    
    const ufos = [];
    const numUFOs = 4;
    
    function init() {
        resize();
        stars = [];
        ufos.length = 0;
        
        for (let i = 0; i < numStars; i++) {
            stars.push(new Star());
        }
        
        for (let i = 0; i < numUFOs; i++) {
            const ufo = new UFO(i % ufoColors.length);
            ufo.z = 150 + i * 220;
            ufo.speed = 0.45 + (i % 3) * 0.15;
            ufos.push(ufo);
        }
    }
    
    function animate() {
        ctx.clearRect(0, 0, width, height);
        
        stars.forEach(star => {
            star.update();
            star.draw();
        });
        
        ufos.forEach(ufo => {
            ufo.update();
            ufo.draw();
        });
        
        requestAnimationFrame(animate);
    }
    
    window.addEventListener('resize', resize);
    init();
    animate();
})();

// ===================== PAGE TRANSITION - RETURN TO LANDING =====================
(function() {
    const authLayout = document.querySelector('.auth-layout');
    const authBg = document.querySelector('.auth-bg');
    let isTransitioning = false;
    
    function startTransition(targetUrl) {
        if (isTransitioning) return;
        isTransitioning = true;
        
        // Accelerate starfield speed (same effect as landing page)
        if (window.starfieldSpeed) {
            window.starfieldSpeed.setTarget(window.starfieldSpeed.fast);
        }
        
        // Fade out with slide effect
        authLayout.classList.add('fading');
        authBg.classList.add('fading');
        
        // Navigate after transition completes
        setTimeout(() => {
            window.location.href = targetUrl;
        }, 800);
    }
    
    // Get home URL from Blade route
    const homeUrl = '{{ route("home") }}';
    
    // Intercept all clicks
    document.addEventListener('click', function(e) {
        // Find the closest anchor element
        let link = e.target;
        while (link && link.tagName !== 'A') {
            link = link.parentElement;
            if (!link) break;
        }
        
        if (link) {
            const href = link.getAttribute('href');
            
            // Check if link goes to home/landing page
            if (href === homeUrl || href === '/' || href === '' || href === '/home') {
                e.preventDefault();
                e.stopPropagation();
                startTransition(homeUrl);
            }
        }
    });
})();
</script>
</body>
</html>
