<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>StreamDonate — Panel Streamer</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        /* ══════════════════════════════════════════
           DESIGN TOKENS
        ══════════════════════════════════════════ */
        :root {
            --bg:           #070709;
            --bg-1:         #0d0d12;
            --bg-2:         #111118;
            --surface:      #141419;
            --surface-2:    #1a1a22;
            --surface-3:    #1f1f28;
            --border:       rgba(255,255,255,.07);
            --border-2:     rgba(255,255,255,.12);

            --brand:        #7c6cfc;
            --brand-light:  #a99dff;
            --brand-glow:   rgba(124,108,252,.2);

            --orange:       #f97316;
            --orange-light: #fb923c;
            --orange-glow:  rgba(249,115,22,.15);

            --green:        #22d3a0;
            --green-light:  #4ade80;
            --green-glow:   rgba(34,211,160,.15);

            --yellow:       #fbbf24;
            --red:          #f43f5e;
            --purple:       #a855f7;

            --text:         #f1f1f6;
            --text-2:       #a0a0b4;
            --text-3:       #606078;

            --radius-sm:    8px;
            --radius:       12px;
            --radius-lg:    18px;
            --radius-xl:    24px;

            --shadow-sm:    0 1px 3px rgba(0,0,0,.4);
            --shadow:       0 4px 16px rgba(0,0,0,.5);
            --shadow-lg:    0 16px 48px rgba(0,0,0,.6);
            --shadow-xl:    0 32px 80px rgba(0,0,0,.7);
        }

        /* ══════════════════════════════════════════
           RESET & BASE
        ══════════════════════════════════════════ */
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        html { scroll-behavior:smooth; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* ══════════════════════════════════════════
           TOPBAR / NAV
        ══════════════════════════════════════════ */
        nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 200;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            background: rgba(7,7,9,.92);
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
        }
        .logo-icon {
            width: 32px; height: 32px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--brand), var(--purple));
            display: flex; align-items: center; justify-content: center;
            font-size: 15px;
            box-shadow: 0 0 20px var(--brand-glow);
        }
        .logo-text span { color: var(--brand-light); }

        .nav-tabs {
            display: flex;
            gap: 2px;
            background: var(--surface);
            padding: 4px;
            border-radius: var(--radius);
            border: 1px solid var(--border);
        }
        .tab-btn {
            padding: 7px 18px;
            border: none;
            border-radius: var(--radius-sm);
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            font-size: 13px;
            font-weight: 500;
            background: transparent;
            color: var(--text-3);
            transition: all .18s ease;
            letter-spacing: -.1px;
            white-space: nowrap;
        }
        .tab-btn.active {
            background: var(--surface-3);
            color: var(--text);
            box-shadow: var(--shadow-sm);
        }
        .tab-btn:hover:not(.active) {
            color: var(--text-2);
            background: var(--surface-2);
        }

        /* Conn Badge */
        #conn-status {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 12px; border-radius: 20px;
            font-size: 11px; font-weight: 600; letter-spacing: .2px;
        }
        #conn-status.ok   { background: rgba(34,211,160,.1);  border: 1px solid rgba(34,211,160,.25);  color: var(--green); }
        #conn-status.err  { background: rgba(244,63,94,.1);   border: 1px solid rgba(244,63,94,.25);   color: var(--red); }
        #conn-status.wait { background: rgba(251,191,36,.08); border: 1px solid rgba(251,191,36,.2);   color: var(--yellow); }
        .conn-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: currentColor;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%,100% { opacity:1; transform:scale(1); }
            50%     { opacity:.4; transform:scale(.7); }
        }

        /* ══════════════════════════════════════════
           PAGE SYSTEM
        ══════════════════════════════════════════ */
        .page { display: none; padding-top: 60px; min-height: 100vh; }
        .page.active { display: block; }

        /* ══════════════════════════════════════════
           SHARED COMPONENTS
        ══════════════════════════════════════════ */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
        }

        .section-label {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.2px;
            color: var(--text-3);
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 10px; border-radius: 20px;
            font-size: 10px; font-weight: 700; letter-spacing: .8px;
        }

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
            font-weight: 400;
            transition: border-color .15s, box-shadow .15s;
            outline: none;
            appearance: none;
        }
        input:focus, textarea:focus, select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px var(--brand-glow);
            background: var(--surface-3);
        }
        input::placeholder, textarea::placeholder { color: var(--text-3); }
        textarea { resize: vertical; min-height: 88px; line-height: 1.6; }
        select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23606078' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 36px;
            cursor: pointer;
        }
        select option { background: var(--surface-2); }

        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--surface-3); border-radius: 2px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--border-2); }

        /* ══════════════════════════════════════════
           TOAST
        ══════════════════════════════════════════ */
        .toast {
            position: fixed; bottom: 24px; right: 24px; z-index: 9999;
            padding: 12px 18px;
            background: var(--surface-3);
            border: 1px solid var(--border-2);
            border-radius: var(--radius);
            font-size: 13px; font-weight: 500;
            color: var(--text);
            box-shadow: var(--shadow-lg);
            transform: translateY(20px) scale(.97);
            opacity: 0;
            transition: all .25s cubic-bezier(.34,1.3,.64,1);
            pointer-events: none;
            max-width: 320px;
        }
        .toast.show { transform: translateY(0) scale(1); opacity: 1; }

        /* ══════════════════════════════════════════
           ① DONATE PAGE
        ══════════════════════════════════════════ */
        .donate-layout {
            display: grid;
            grid-template-columns: 1fr 480px;
            gap: 0;
            min-height: calc(100vh - 60px);
        }

        /* Left hero panel */
        .donate-hero {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 48px;
            position: relative;
            border-right: 1px solid var(--border);
            background: var(--bg-1);
            overflow: hidden;
        }
        .donate-hero::before {
            content: '';
            position: absolute;
            top: -200px; left: -200px;
            width: 600px; height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(124,108,252,.08) 0%, transparent 70%);
            pointer-events: none;
        }
        .donate-hero::after {
            content: '';
            position: absolute;
            bottom: -100px; right: -100px;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(249,115,22,.06) 0%, transparent 70%);
            pointer-events: none;
        }

        .hero-avatar-wrap { position: relative; margin-bottom: 28px; }
        .hero-avatar {
            width: 96px; height: 96px; border-radius: 28px;
            background: linear-gradient(135deg, var(--brand), var(--purple));
            display: flex; align-items: center; justify-content: center;
            font-size: 40px;
            box-shadow: 0 0 0 1px rgba(124,108,252,.3), 0 24px 60px rgba(124,108,252,.25);
        }
        .hero-live {
            position: absolute; bottom: -8px; right: -8px;
            background: var(--red);
            border: 2px solid var(--bg-1);
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 9px; font-weight: 800; letter-spacing: 1px;
            color: #fff;
            display: flex; align-items: center; gap: 4px;
        }
        .live-dot {
            width: 5px; height: 5px; border-radius: 50%;
            background: #fff;
            animation: pulse 1.5s infinite;
        }
        .hero-name {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 36px; font-weight: 700;
            letter-spacing: -1px;
            color: var(--text);
            text-align: center;
            margin-bottom: 8px;
        }
        .hero-desc {
            font-size: 14px; font-weight: 400;
            color: var(--text-3);
            text-align: center;
            line-height: 1.6;
        }

        .hero-stats {
            display: grid; grid-template-columns: repeat(3,1fr);
            gap: 12px; margin-top: 40px; width: 100%; max-width: 340px;
        }
        .hero-stat {
            text-align: center;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 14px 8px;
        }
        .hero-stat-val {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 20px; font-weight: 700;
            color: var(--text);
            margin-bottom: 3px;
        }
        .hero-stat-lbl {
            font-size: 10px; color: var(--text-3); letter-spacing: .5px;
        }

        /* Right form panel */
        .donate-form-panel {
            background: var(--bg);
            display: flex; flex-direction: column;
            overflow-y: auto;
        }
        .donate-form-inner {
            padding: 48px 40px;
            flex: 1;
        }
        .form-heading {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 24px; font-weight: 700; letter-spacing: -.5px;
            color: var(--text);
            margin-bottom: 4px;
        }
        .form-subheading {
            font-size: 13px; color: var(--text-3); margin-bottom: 32px;
        }

        .form-group { margin-bottom: 20px; }

        .amount-presets {
            display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 10px;
        }
        .preset-btn {
            padding: 8px 14px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
            background: var(--surface-2);
            color: var(--text-3);
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            font-size: 13px; font-weight: 600;
            transition: all .15s;
        }
        .preset-btn:hover { border-color: var(--border-2); color: var(--text-2); }
        .preset-btn.selected {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
            box-shadow: 0 0 16px var(--brand-glow);
        }

        .yt-hint {
            font-size: 11px; color: var(--text-3);
            margin-top: 7px;
            display: flex; align-items: center; gap: 6px;
        }
        .yt-pill {
            background: rgba(239,68,68,.12);
            border: 1px solid rgba(239,68,68,.2);
            color: #f87171;
            padding: 2px 8px; border-radius: 4px;
            font-size: 10px; font-weight: 700; letter-spacing: .5px;
        }

        .submit-btn {
            width: 100%; padding: 15px;
            border: none; border-radius: var(--radius);
            cursor: pointer;
            background: linear-gradient(135deg, var(--brand), #6356e8);
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 15px; font-weight: 700; letter-spacing: -.2px;
            transition: all .2s;
            box-shadow: 0 4px 20px var(--brand-glow), 0 1px 0 rgba(255,255,255,.1) inset;
            position: relative; overflow: hidden;
        }
        .submit-btn::after {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(255,255,255,.12), transparent);
        }
        .submit-btn:hover:not(:disabled) {
            transform: translateY(-1px);
            box-shadow: 0 8px 32px var(--brand-glow);
        }
        .submit-btn:active:not(:disabled) { transform: translateY(0); }
        .submit-btn:disabled { opacity: .55; cursor: not-allowed; }

        /* ══════════════════════════════════════════
           ② OVERLAY PAGE
        ══════════════════════════════════════════ */
        .overlay-layout {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 24px;
            padding: 28px;
            min-height: calc(100vh - 60px);
        }

        .overlay-main {
            display: flex; flex-direction: column; gap: 16px;
        }

        .panel-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 0;
        }
        .panel-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 14px; font-weight: 600;
            color: var(--text);
            letter-spacing: -.2px;
        }
        .panel-subtitle { font-size: 12px; color: var(--text-3); margin-top: 1px; }

        /* Queue bar */
        .queue-bar {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 14px 18px;
            display: flex; align-items: center; gap: 16px;
        }
        .queue-num {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 28px; font-weight: 700;
            color: var(--brand-light);
            line-height: 1;
            min-width: 32px;
        }
        .queue-divider { width: 1px; height: 32px; background: var(--border); }
        .queue-label-main { font-size: 13px; font-weight: 600; color: var(--text-2); }
        .queue-label-sub { font-size: 11px; color: var(--text-3); margin-top: 2px; }
        .queue-items { display: flex; flex-direction: column; gap: 6px; flex: 1; min-width: 0; max-height: 120px; overflow-y: auto; }
        .queue-item {
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 8px 12px;
            display: flex; align-items: center; gap: 10px;
            font-size: 12px;
        }
        .queue-pos {
            width: 18px; height: 18px; border-radius: 50%;
            background: var(--brand);
            color: #fff; font-size: 9px; font-weight: 800;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .queue-item-name { font-weight: 600; color: var(--text); }
        .queue-item-amount { color: var(--orange); font-weight: 700; margin-left: auto; font-size: 12px; }
        .queue-yt-tag {
            font-size: 9px; font-weight: 700; letter-spacing: .5px;
            background: rgba(239,68,68,.15); border: 1px solid rgba(239,68,68,.2);
            color: #f87171; padding: 1px 6px; border-radius: 3px;
        }

        /* Preview screen */
        .preview-wrap {
            flex: 1;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 18px;
            display: flex; flex-direction: column;
        }
        .preview-screen {
            flex: 1;
            background: #0a0a0c;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            position: relative; overflow: hidden;
            min-height: 360px;
            background-image:
                radial-gradient(circle at 20% 20%, rgba(124,108,252,.03) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(249,115,22,.03) 0%, transparent 50%);
        }
        .preview-chip {
            position: absolute; top: 12px; left: 12px;
            background: rgba(0,0,0,.7);
            border: 1px solid var(--border);
            padding: 4px 10px; border-radius: 6px;
            font-size: 10px; color: var(--text-3); letter-spacing: .8px;
            font-weight: 600;
        }
        .preview-chip-right {
            position: absolute; top: 12px; right: 12px;
            background: rgba(34,211,160,.1);
            border: 1px solid rgba(34,211,160,.2);
            padding: 3px 8px; border-radius: 5px;
            font-size: 9px; color: var(--green); letter-spacing: .5px;
            font-weight: 700;
        }

        /* Alert box inside preview */
        .alert-box {
            position: absolute; bottom: 20px; left: 50%;
            transform: translateX(-50%);
            width: 88%; max-width: 460px;
            background: rgba(13,13,18,.96);
            border: 1px solid rgba(124,108,252,.3);
            border-radius: var(--radius-lg);
            padding: 18px 20px 0;
            box-shadow: 0 0 0 1px rgba(124,108,252,.1),
                        0 24px 60px rgba(0,0,0,.7),
                        0 0 40px rgba(124,108,252,.1);
            display: none; overflow: hidden;
        }
        .alert-box::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--brand), var(--purple), var(--green));
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
        }
        .alert-box.visible { display: block; animation: alertIn .45s cubic-bezier(.34,1.56,.64,1); }
        .alert-box.hiding  { animation: alertOut .35s ease-in forwards; }

        @keyframes alertIn {
            from { transform: translateX(-50%) translateY(50px); opacity: 0; }
            to   { transform: translateX(-50%) translateY(0); opacity: 1; }
        }
        @keyframes alertOut {
            from { transform: translateX(-50%) translateY(0); opacity: 1; }
            to   { transform: translateX(-50%) translateY(50px); opacity: 0; }
        }

        .alert-top-tag {
            position: absolute; top: 0; right: 20px;
            background: linear-gradient(135deg, var(--brand), var(--purple));
            color: #fff; font-size: 8px; font-weight: 800; letter-spacing: 1.2px;
            padding: 3px 10px; border-radius: 0 0 8px 8px;
        }
        .alert-header { display: flex; align-items: center; gap: 12px; margin-bottom: 10px; }
        .alert-avatar {
            width: 44px; height: 44px; border-radius: 14px;
            background: linear-gradient(135deg, rgba(124,108,252,.2), rgba(168,85,247,.2));
            border: 1px solid rgba(124,108,252,.3);
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; flex-shrink: 0;
        }
        .alert-donor { font-weight: 700; font-size: 15px; color: var(--text); }
        .alert-amount {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 20px; font-weight: 700; letter-spacing: -.5px;
            color: var(--orange);
        }
        .alert-message {
            font-size: 12px; color: var(--text-3);
            line-height: 1.6; margin-bottom: 12px;
            padding: 8px 12px;
            background: var(--surface-2);
            border-radius: var(--radius-sm);
            border-left: 2px solid var(--brand);
            font-style: italic;
        }
        .alert-yt { border-radius: var(--radius-sm); overflow: hidden; background: #000; aspect-ratio: 16/9; width: 100%; margin-bottom: 12px; }
        .alert-yt iframe { width: 100%; height: 100%; border: none; }
        .alert-progress { height: 2px; background: rgba(255,255,255,.06); margin: 0 -20px; }
        .alert-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--brand), var(--orange));
            transition: width linear;
        }

        /* Sidebar controls */
        .overlay-sidebar { display: flex; flex-direction: column; gap: 12px; }

        .ctrl-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 18px 20px;
        }
        .ctrl-title {
            font-size: 11px; font-weight: 700;
            letter-spacing: 1px; color: var(--text-3);
            text-transform: uppercase; margin-bottom: 16px;
        }

        .widget-row { margin-bottom: 10px; }
        .widget-label { font-size: 11px; color: var(--text-3); margin-bottom: 6px; font-weight: 500; }
        .widget-url-wrap { display: flex; gap: 6px; align-items: center; }
        .widget-url-wrap input {
            flex: 1; font-size: 11px; padding: 8px 10px;
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-3);
            font-family: 'Inter', sans-serif;
        }
        .copy-btn {
            padding: 8px 12px;
            background: var(--surface-3);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text-2); cursor: pointer;
            font-size: 11px; font-weight: 700;
            transition: all .15s; white-space: nowrap;
        }
        .copy-btn:hover { background: var(--brand); border-color: var(--brand); color: #fff; }

        .obs-hint {
            font-size: 11px; color: var(--text-3); line-height: 1.8;
            margin-top: 10px; padding-top: 10px;
            border-top: 1px solid var(--border);
        }
        .obs-hint strong { color: var(--text-2); }
        .obs-hint .obs-val { color: var(--brand-light); font-weight: 700; }

        .test-grid { display: flex; flex-direction: column; gap: 8px; }
        .test-btn {
            width: 100%; padding: 11px 14px;
            border: none; border-radius: var(--radius);
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            font-size: 13px; font-weight: 600; letter-spacing: -.1px;
            transition: all .15s;
            text-align: left;
            display: flex; align-items: center; gap: 10px;
        }
        .test-btn-icon {
            width: 28px; height: 28px; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 12px; flex-shrink: 0;
        }
        .test-btn.primary {
            background: var(--green-glow);
            border: 1px solid rgba(34,211,160,.25);
            color: var(--green);
        }
        .test-btn.primary .test-btn-icon { background: rgba(34,211,160,.15); }
        .test-btn.primary:hover { background: rgba(34,211,160,.2); }
        .test-btn.secondary {
            background: var(--surface-2);
            border: 1px solid var(--border);
            color: var(--text-2);
        }
        .test-btn.secondary .test-btn-icon { background: var(--surface-3); }
        .test-btn.secondary:hover { border-color: var(--border-2); color: var(--text); }

        .setting-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid var(--border);
        }
        .setting-row:last-child { border-bottom: none; padding-bottom: 0; }
        .setting-row:first-child { padding-top: 0; }
        .setting-info {}
        .setting-name { font-size: 13px; font-weight: 500; color: var(--text); }
        .setting-sub  { font-size: 11px; color: var(--text-3); margin-top: 2px; }

        .toggle {
            width: 40px; height: 22px;
            border-radius: 11px;
            background: var(--surface-3);
            border: 1px solid var(--border);
            position: relative; cursor: pointer;
            transition: background .2s, border-color .2s;
            flex-shrink: 0;
        }
        .toggle.on { background: var(--brand); border-color: var(--brand); }
        .toggle::after {
            content: '';
            position: absolute; top: 2px; left: 2px;
            width: 16px; height: 16px;
            border-radius: 50%; background: #fff;
            transition: transform .2s cubic-bezier(.34,1.4,.64,1);
            box-shadow: 0 1px 4px rgba(0,0,0,.3);
        }
        .toggle.on::after { transform: translateX(18px); }

        .setting-select {
            background: var(--surface-2);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 6px 28px 6px 10px;
            border-radius: var(--radius-sm);
            font-size: 12px;
            font-family: 'Inter', sans-serif;
            outline: none; cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 12 12'%3E%3Cpath fill='%23606078' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 8px center;
        }
        .setting-input-sm {
            width: 80px; padding: 6px 10px;
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text); font-size: 12px;
            font-family: 'Inter', sans-serif;
            outline: none; text-align: right;
        }

        /* ══════════════════════════════════════════
           ③ DASHBOARD PAGE
        ══════════════════════════════════════════ */
        .dashboard-layout {
            padding: 28px;
            max-width: 1280px;
        }

        .dash-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 24px;
        }
        .dash-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 22px; font-weight: 700; letter-spacing: -.5px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 24px;
        }
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 20px;
            position: relative; overflow: hidden;
            transition: border-color .2s;
        }
        .stat-card:hover { border-color: var(--border-2); }
        .stat-card::after {
            content: '';
            position: absolute; bottom: 0; left: 0; right: 0;
            height: 2px;
            background: var(--card-color, var(--brand));
            opacity: .6;
        }
        .stat-icon-wrap {
            width: 36px; height: 36px;
            border-radius: var(--radius-sm);
            background: color-mix(in srgb, var(--card-color, var(--brand)) 12%, transparent);
            border: 1px solid color-mix(in srgb, var(--card-color, var(--brand)) 25%, transparent);
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; margin-bottom: 16px;
        }
        .stat-label { font-size: 11px; color: var(--text-3); font-weight: 600; letter-spacing: .5px; margin-bottom: 6px; }
        .stat-value {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 28px; font-weight: 700; letter-spacing: -1px;
            color: var(--text); line-height: 1;
            margin-bottom: 6px;
        }
        .stat-sub { font-size: 11px; color: var(--text-3); }

        .dash-content {
            display: grid;
            grid-template-columns: 1fr 360px;
            gap: 16px;
        }

        /* History */
        .history-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 22px;
            display: flex; flex-direction: column;
        }
        .card-title-row {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 18px;
        }
        .card-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 15px; font-weight: 700; letter-spacing: -.3px;
            color: var(--text);
        }
        .count-chip {
            background: var(--surface-3);
            border: 1px solid var(--border);
            color: var(--text-3);
            padding: 3px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 600;
        }

        .history-list {
            display: flex; flex-direction: column; gap: 8px;
            max-height: 400px; overflow-y: auto;
            flex: 1;
        }
        .history-item {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 14px;
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            transition: border-color .15s;
            cursor: default;
        }
        .history-item:hover { border-color: var(--border-2); }
        .history-item.new {
            border-color: rgba(34,211,160,.3);
            animation: newItem .5s ease;
        }
        @keyframes newItem {
            from { transform: translateX(-8px); opacity: 0; }
            to   { transform: translateX(0); opacity: 1; }
        }
        .h-avatar {
            width: 38px; height: 38px; border-radius: 12px;
            background: linear-gradient(135deg, var(--surface-3), var(--surface));
            border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; flex-shrink: 0;
        }
        .h-name { font-size: 13px; font-weight: 600; color: var(--text); }
        .h-msg  { font-size: 11px; color: var(--text-3); margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 240px; }
        .h-yt   { font-size: 10px; color: #f87171; font-weight: 600; margin-top: 2px; display: flex; align-items: center; gap: 4px; }
        .h-amount {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 16px; font-weight: 700; letter-spacing: -.5px;
            color: var(--orange); margin-left: auto; flex-shrink: 0;
        }
        .h-time { font-size: 10px; color: var(--text-3); text-align: right; margin-top: 2px; }

        /* Leaderboard */
        .leader-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 22px;
            display: flex; flex-direction: column;
        }
        .leader-list { display: flex; flex-direction: column; gap: 8px; }
        .leader-item {
            display: flex; align-items: center; gap: 10px;
            padding: 12px;
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            transition: all .2s;
        }
        .leader-item.rank-1 { border-color: rgba(251,191,36,.2); background: rgba(251,191,36,.04); }
        .leader-item.rank-2 { border-color: rgba(160,160,180,.15); background: rgba(160,160,180,.02); }
        .leader-item.rank-3 { border-color: rgba(205,127,50,.2); background: rgba(205,127,50,.03); }
        .rank-num {
            width: 26px; text-align: center;
            font-size: 16px; flex-shrink: 0;
        }
        .rank-num.plain {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 13px; font-weight: 700; color: var(--text-3);
        }
        .leader-emoji {
            width: 32px; height: 32px; border-radius: 10px;
            background: var(--surface-3); border: 1px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; flex-shrink: 0;
        }
        .leader-name { font-size: 13px; font-weight: 600; flex: 1; color: var(--text); }
        .leader-right { text-align: right; flex-shrink: 0; }
        .leader-total {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 15px; font-weight: 700; letter-spacing: -.5px;
            color: var(--yellow);
        }
        .leader-count { font-size: 10px; color: var(--text-3); margin-top: 1px; }

        /* ══════════════════════════════════════════
           ④ SETTINGS PAGE
        ══════════════════════════════════════════ */
        .settings-layout {
            padding: 28px;
            max-width: 900px;
        }
        .settings-header {
            margin-bottom: 28px;
        }
        .settings-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 22px; font-weight: 700; letter-spacing: -.5px;
            margin-bottom: 4px;
        }
        .settings-sub { font-size: 13px; color: var(--text-3); }

        .settings-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }

        .settings-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 24px;
        }
        .settings-card-header {
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
        }
        .settings-card-icon {
            width: 32px; height: 32px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px;
        }
        .settings-card-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 14px; font-weight: 700; letter-spacing: -.2px;
        }

        .settings-form-group { margin-bottom: 14px; }
        .settings-form-group:last-child { margin-bottom: 0; }

        .ms-preview {
            margin-top: 16px;
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 14px;
        }
        .ms-preview-label { font-size: 10px; color: var(--text-3); font-weight: 600; letter-spacing: .8px; margin-bottom: 10px; }
        .ms-preview-bar-track {
            height: 6px; background: rgba(255,255,255,.06); border-radius: 3px; overflow: hidden; margin-bottom: 8px;
        }
        .ms-preview-bar {
            height: 100%; border-radius: 3px;
            background: linear-gradient(90deg, var(--brand), var(--orange));
            transition: width .8s cubic-bezier(.4,0,.2,1);
        }
        .ms-preview-labels {
            display: flex; justify-content: space-between;
            font-size: 11px; color: var(--text-3);
        }
        .ms-preview-labels .pct { color: var(--brand-light); font-weight: 700; }

        .settings-note {
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 12px 14px;
            margin-top: 12px;
            font-size: 12px; color: var(--text-3); line-height: 1.7;
        }

        .save-btn {
            margin-top: 20px;
            width: 100%; padding: 13px;
            border: none; border-radius: var(--radius);
            cursor: pointer;
            background: linear-gradient(135deg, var(--brand), #6356e8);
            color: #fff;
            font-family: 'Inter', sans-serif;
            font-size: 14px; font-weight: 700; letter-spacing: -.1px;
            transition: all .2s;
            box-shadow: 0 4px 20px var(--brand-glow);
        }
        .save-btn:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 8px 28px var(--brand-glow); }
        .save-btn:disabled { opacity: .55; cursor: not-allowed; }

        .danger-section {
            margin-top: 20px;
            background: rgba(244,63,94,.04);
            border: 1px solid rgba(244,63,94,.15);
            border-radius: var(--radius-lg);
            padding: 22px;
        }
        .danger-header {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 10px;
        }
        .danger-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--red);
        }
        .danger-title { font-size: 13px; font-weight: 700; color: var(--red); }
        .danger-desc { font-size: 12px; color: var(--text-3); line-height: 1.7; margin-bottom: 14px; }
        .danger-desc strong { color: var(--text-2); }
        .danger-desc em { color: var(--red); font-style: normal; font-weight: 600; }
        .reset-btn {
            padding: 9px 18px;
            border: 1px solid rgba(244,63,94,.3);
            border-radius: var(--radius-sm);
            cursor: pointer;
            background: rgba(244,63,94,.08);
            color: var(--red);
            font-family: 'Inter', sans-serif;
            font-size: 13px; font-weight: 600;
            transition: all .15s;
        }
        .reset-btn:hover { background: rgba(244,63,94,.15); border-color: rgba(244,63,94,.5); }

        /* ══════════════════════════════════════════
           RESPONSIVE
        ══════════════════════════════════════════ */
        @media (max-width: 1024px) {
            .donate-layout { grid-template-columns: 1fr; }
            .donate-hero { min-height: 300px; padding: 40px 32px; }
            .donate-hero::before, .donate-hero::after { display: none; }
        }
        @media (max-width: 900px) {
            .overlay-layout { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: repeat(2,1fr); }
            .dash-content { grid-template-columns: 1fr; }
            .settings-grid { grid-template-columns: 1fr; }
            nav { padding: 0 16px; }
            .overlay-layout, .dashboard-layout, .settings-layout { padding: 16px; }
        }
        @media (max-width: 600px) {
            .nav-tabs { gap: 1px; }
            .tab-btn { padding: 6px 12px; font-size: 12px; }
            .donate-form-inner { padding: 28px 20px; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
        }
    </style>
</head>
<body>

<!-- ─────────────────────── NAV ─────────────────────── -->
<nav>
    <div class="logo">
        <div class="logo-icon">🎮</div>
        <div class="logo-text">Stream<span>Donate</span></div>
    </div>
    <div class="nav-tabs">
        <button class="tab-btn active" onclick="showPage('donate')">Donasi</button>
        <button class="tab-btn"        onclick="showPage('overlay')">Overlay</button>
        <button class="tab-btn"        onclick="showPage('dashboard')">Dashboard</button>
        <button class="tab-btn"        onclick="showPage('settings')">Settings</button>
    </div>
    <div id="conn-status" class="wait"><span class="conn-dot"></span> Menghubungkan…</div>
</nav>


<!-- ═══════════════════════════════════════
     ① DONATE PAGE
═══════════════════════════════════════ -->
<div id="page-donate" class="page active">
    <div class="donate-layout">

        <!-- Hero side -->
        <div class="donate-hero">
            <div class="hero-avatar-wrap">
                <div class="hero-avatar">🎮</div>
                <div class="hero-live"><span class="live-dot"></span> LIVE</div>
            </div>
            <div class="hero-name">KangStreamer</div>
            <div class="hero-desc">Streaming setiap hari &mdash; FPS &bull; RPG &bull; Just Chatting</div>

            <div class="hero-stats">
                <div class="hero-stat">
                    <div class="hero-stat-val" id="hero-total">Rp 0</div>
                    <div class="hero-stat-lbl">TERKUMPUL</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-val" id="hero-donors">0</div>
                    <div class="hero-stat-lbl">DONATUR</div>
                </div>
                <div class="hero-stat">
                    <div class="hero-stat-val" id="hero-count">0</div>
                    <div class="hero-stat-lbl">DONASI</div>
                </div>
            </div>
        </div>

        <!-- Form side -->
        <div class="donate-form-panel">
            <div class="donate-form-inner">
                <div class="form-heading">Kirim Donasi</div>
                <div class="form-subheading">Support streamer favorit kamu sekarang</div>

                <div class="form-group">
                    <label>Nama kamu</label>
                    <input type="text" id="donor-name" placeholder="Nama atau username…" />
                </div>

                <div class="form-group">
                    <label>Nominal donasi</label>
                    <div class="amount-presets">
                        <button class="preset-btn" onclick="setPreset(this,5000)">Rp 5K</button>
                        <button class="preset-btn" onclick="setPreset(this,10000)">Rp 10K</button>
                        <button class="preset-btn selected" onclick="setPreset(this,25000)">Rp 25K</button>
                        <button class="preset-btn" onclick="setPreset(this,50000)">Rp 50K</button>
                        <button class="preset-btn" onclick="setPreset(this,100000)">Rp 100K</button>
                    </div>
                    <input type="number" id="donor-amount" value="25000" min="1000" placeholder="Atau ketik nominal lain…" />
                </div>

                <div class="form-group">
                    <label>Pesan <span style="color:var(--text-3);font-weight:400">(opsional)</span></label>
                    <textarea id="donor-msg" placeholder="Tulis pesan untuk streamer…"></textarea>
                </div>

                <div class="form-group">
                    <label>Request video YouTube <span style="color:var(--text-3);font-weight:400">(opsional)</span></label>
                    <input type="text" id="donor-yt" placeholder="https://youtube.com/watch?v=…" />
                    <div class="yt-hint">
                        <span class="yt-pill">YT</span>
                        Tempel link YouTube untuk request lagu atau video
                    </div>
                </div>

                <button class="submit-btn" id="submit-btn" onclick="submitDonation()">
                    Kirim Donasi Sekarang
                </button>
            </div>
        </div>
    </div>
</div>


<!-- ═══════════════════════════════════════
     ② OVERLAY PAGE
═══════════════════════════════════════ -->
<div id="page-overlay" class="page">
    <div class="overlay-layout">

        <!-- Main: preview + queue -->
        <div class="overlay-main">

            <!-- Queue bar -->
            <div class="queue-bar">
                <div class="queue-num" id="queue-count-display">0</div>
                <div class="queue-divider"></div>
                <div>
                    <div class="queue-label-main">Dalam antrean</div>
                    <div class="queue-label-sub">Donasi menunggu ditampilkan</div>
                </div>
                <div class="queue-items" id="queue-list">
                    <div style="color:var(--text-3);font-size:11px">Antrean kosong</div>
                </div>
            </div>

            <!-- Preview screen -->
            <div class="preview-wrap">
                <div class="panel-header" style="margin-bottom:14px">
                    <div>
                        <div class="panel-title">Preview Overlay</div>
                        <div class="panel-subtitle">Simulasi tampilan di OBS Browser Source (1920×1080)</div>
                    </div>
                </div>
                <div class="preview-screen">
                    <div class="preview-chip">1920 × 1080</div>
                    <div class="preview-chip-right">STREAM PREVIEW</div>

                    <div class="alert-box" id="alert-box">
                        <div class="alert-top-tag">DONASI MASUK</div>
                        <div class="alert-header">
                            <div class="alert-avatar" id="alert-avatar">😊</div>
                            <div>
                                <div class="alert-donor"  id="alert-donor">Nama Donatur</div>
                                <div class="alert-amount" id="alert-amount">Rp 0</div>
                            </div>
                        </div>
                        <div class="alert-message" id="alert-message">Pesan donatur akan muncul di sini!</div>
                        <div class="alert-yt" id="alert-yt" style="display:none">
                            <iframe id="yt-iframe" allow="autoplay" allowfullscreen></iframe>
                        </div>
                        <div class="alert-progress">
                            <div class="alert-progress-bar" id="progress-bar" style="width:100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="overlay-sidebar">

            <!-- Widget URLs -->
            <div class="ctrl-card">
                <div class="ctrl-title">URL Widget OBS</div>
                <div class="widget-row">
                    <div class="widget-label">Alert Donasi</div>
                    <div class="widget-url-wrap">
                        <input type="text" id="overlay-url-input" value="http://streamdonate-versamorph.test/overlay.php" readonly />
                        <button class="copy-btn" onclick="copyUrl('overlay-url-input')">Copy</button>
                    </div>
                </div>
                <div class="widget-row">
                    <div class="widget-label">Leaderboard (sidebar kanan)</div>
                    <div class="widget-url-wrap">
                        <input type="text" id="leaderboard-url-input" value="http://streamdonate-versamorph.test/leaderboard.php" readonly />
                        <button class="copy-btn" onclick="copyUrl('leaderboard-url-input')">Copy</button>
                    </div>
                </div>
                <div class="widget-row" style="margin-bottom:0">
                    <div class="widget-label">Milestone (bar bawah)</div>
                    <div class="widget-url-wrap">
                        <input type="text" id="milestone-url-input" value="http://streamdonate-versamorph.test/milestone.php" readonly />
                        <button class="copy-btn" onclick="copyUrl('milestone-url-input')">Copy</button>
                    </div>
                </div>
                <div class="obs-hint">
                    OBS → <strong>Sources</strong> → <strong>Browser</strong> → paste URL<br>
                    Width <span class="obs-val">1920</span>, Height <span class="obs-val">1080</span> &bull; centang <strong>Transparent BG</strong>
                </div>
            </div>

            <!-- Test -->
            <div class="ctrl-card">
                <div class="ctrl-title">Test Donasi</div>
                <div class="test-grid">
                    <button class="test-btn primary" onclick="testDonation()">
                        <div class="test-btn-icon">▶</div>
                        <div>Test Donasi Normal</div>
                    </button>
                    <button class="test-btn secondary" onclick="testDonationYT()">
                        <div class="test-btn-icon">📹</div>
                        <div>Test + Video YouTube</div>
                    </button>
                    <button class="test-btn secondary" onclick="testDonationQueue()">
                        <div class="test-btn-icon">📋</div>
                        <div>Kirim 3 Donasi (Queue)</div>
                    </button>
                </div>
            </div>

            <!-- Alert settings -->
            <div class="ctrl-card">
                <div class="ctrl-title">Pengaturan Alert</div>
                <div class="setting-row">
                    <div class="setting-info">
                        <div class="setting-name">Suara Notifikasi</div>
                        <div class="setting-sub">Bunyi saat donasi masuk</div>
                    </div>
                    <button class="toggle on" id="toggle-sound" onclick="toggleSetting(this)"></button>
                </div>
                <div class="setting-row">
                    <div class="setting-info">
                        <div class="setting-name">Putar Video YouTube</div>
                        <div class="setting-sub">Video request dari donatur</div>
                    </div>
                    <button class="toggle on" id="toggle-yt" onclick="toggleSetting(this)"></button>
                </div>
                <div class="setting-row">
                    <div class="setting-info">
                        <div class="setting-name">Durasi Alert</div>
                        <div class="setting-sub">Lama tampil (tanpa YT)</div>
                    </div>
                    <select class="setting-select" id="duration-select">
                        <option value="5">5 detik</option>
                        <option value="8" selected>8 detik</option>
                        <option value="12">12 detik</option>
                        <option value="15">15 detik</option>
                    </select>
                </div>
                <div class="setting-row">
                    <div class="setting-info">
                        <div class="setting-name">Minimal Tampil Alert</div>
                        <div class="setting-sub">Nominal minimum (Rp)</div>
                    </div>
                    <input type="number" class="setting-input-sm" id="min-amount" value="1000" />
                </div>
            </div>

        </div>
    </div>
</div>


<!-- ═══════════════════════════════════════
     ③ DASHBOARD PAGE
═══════════════════════════════════════ -->
<div id="page-dashboard" class="page">
    <div class="dashboard-layout">
        <div class="dash-header">
            <div class="dash-title">Dashboard</div>
        </div>

        <div class="stats-grid">
            <div class="stat-card" style="--card-color:var(--yellow)">
                <div class="stat-icon-wrap">💰</div>
                <div class="stat-label">TOTAL DONASI HARI INI</div>
                <div class="stat-value" id="stat-today">Rp 0</div>
                <div class="stat-sub" id="stat-today-count">0 transaksi</div>
            </div>
            <div class="stat-card" style="--card-color:var(--green)">
                <div class="stat-icon-wrap">👑</div>
                <div class="stat-label">DONASI TERBESAR</div>
                <div class="stat-value" id="stat-biggest">Rp 0</div>
                <div class="stat-sub" id="stat-biggest-name">—</div>
            </div>
            <div class="stat-card" style="--card-color:var(--brand-light)">
                <div class="stat-icon-wrap">👥</div>
                <div class="stat-label">TOTAL DONATUR</div>
                <div class="stat-value" id="stat-donors">0</div>
                <div class="stat-sub">orang unik</div>
            </div>
            <div class="stat-card" style="--card-color:var(--red)">
                <div class="stat-icon-wrap">🎬</div>
                <div class="stat-label">DONASI + VIDEO YT</div>
                <div class="stat-value" id="stat-yt">0</div>
                <div class="stat-sub">request video</div>
            </div>
        </div>

        <div class="dash-content">
            <div class="history-card">
                <div class="card-title-row">
                    <div class="card-title">Riwayat Donasi</div>
                    <div class="count-chip" id="history-badge">0 donasi</div>
                </div>
                <div class="history-list" id="history-list">
                    <div style="color:var(--text-3);font-size:13px;padding:24px 0;text-align:center">
                        Belum ada donasi masuk.<br>Coba test di tab Overlay!
                    </div>
                </div>
            </div>

            <div class="leader-card">
                <div class="card-title-row">
                    <div class="card-title">Top Donatur</div>
                    <div style="font-size:16px">🏆</div>
                </div>
                <div class="leader-list" id="leaderboard">
                    <div style="color:var(--text-3);font-size:13px;padding:20px 0;text-align:center">Belum ada data…</div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Toast -->
<div class="toast" id="toast"></div>


<!-- ═══════════════════════════════════════
     ④ SETTINGS PAGE
═══════════════════════════════════════ -->
<div id="page-settings" class="page">
    <div class="settings-layout">
        <div class="settings-header">
            <div class="settings-title">Settings</div>
            <div class="settings-sub">Konfigurasi widget dan overlay stream kamu</div>
        </div>

        <div class="settings-grid">
            <!-- Milestone -->
            <div class="settings-card">
                <div class="settings-card-header">
                    <div class="settings-card-icon" style="background:rgba(124,108,252,.12);border:1px solid rgba(124,108,252,.2)">🎯</div>
                    <div class="settings-card-title">Milestone</div>
                </div>
                <div class="settings-form-group">
                    <label>Judul milestone</label>
                    <input type="text" id="cfg-ms-title" value="Target Stream Hari Ini" maxlength="80" />
                </div>
                <div class="settings-form-group">
                    <label>Target donasi (Rp)</label>
                    <input type="number" id="cfg-ms-target" value="1000000" min="1000" step="10000" />
                </div>
                <div class="ms-preview">
                    <div class="ms-preview-label">PREVIEW PROGRESS</div>
                    <div class="ms-preview-bar-track">
                        <div class="ms-preview-bar" id="ms-preview-bar" style="width:0%"></div>
                    </div>
                    <div class="ms-preview-labels">
                        <span id="ms-preview-current">Rp 0</span>
                        <span class="pct" id="ms-preview-pct">0%</span>
                        <span id="ms-preview-target">/ Rp 1Jt</span>
                    </div>
                </div>
            </div>

            <!-- Leaderboard -->
            <div class="settings-card">
                <div class="settings-card-header">
                    <div class="settings-card-icon" style="background:rgba(251,191,36,.1);border:1px solid rgba(251,191,36,.2)">🏆</div>
                    <div class="settings-card-title">Leaderboard</div>
                </div>
                <div class="settings-form-group">
                    <label>Judul leaderboard</label>
                    <input type="text" id="cfg-lb-title" value="Top Donatur" maxlength="60" />
                </div>
                <div class="settings-form-group">
                    <label>Jumlah donatur ditampilkan</label>
                    <select id="cfg-lb-count">
                        <option value="3">3 donatur</option>
                        <option value="5">5 donatur</option>
                        <option value="8">8 donatur</option>
                        <option value="10" selected>10 donatur</option>
                        <option value="15">15 donatur</option>
                        <option value="20">20 donatur</option>
                    </select>
                </div>
                <div class="settings-note">
                    Leaderboard overlay menampilkan top donatur di sidebar kanan OBS.
                    Jumlah ini mempengaruhi tinggi panel overlay.
                </div>
            </div>
        </div>

        <button class="save-btn" id="save-config-btn" onclick="saveConfig()">Simpan Konfigurasi</button>

        <!-- Danger zone -->
        <div class="danger-section">
            <div class="danger-header">
                <div class="danger-dot"></div>
                <div class="danger-title">Danger Zone</div>
            </div>
            <div class="danger-desc">
                Reset akan menghapus <strong>seluruh riwayat donasi</strong> secara permanen.
                Leaderboard dan milestone akan kembali ke nol.
                Tindakan ini <em>tidak dapat dibatalkan</em>.
            </div>
            <button class="reset-btn" onclick="resetHistory()">Reset Riwayat Donasi</button>
        </div>
    </div>
</div>


<script>
// ═══════════════════════════════════════════
//  UTILS
// ═══════════════════════════════════════════
function esc(s) {
    return String(s)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ═══════════════════════════════════════════
//  STATE
// ═══════════════════════════════════════════
const state = {
    donations: [],
    queue:     [],
    isShowing: false,
    soundOn:   true,
    ytOn:      true,
};
const EMOJIS = ['😊','🔥','💎','🎮','🚀','⭐','🎯','💪','🦁','🐉'];

// ═══════════════════════════════════════════
//  SSE
// ═══════════════════════════════════════════
const processedSeqs = new Set();

function connectSSE() {
    const es    = new EventSource('api/stream.php');
    const badge = document.getElementById('conn-status');

    es.onopen = () => {
        badge.className = 'ok';
        badge.innerHTML = '<span class="conn-dot"></span> Terhubung';
    };

    es.addEventListener('donation', (e) => {
        try {
            const d = JSON.parse(e.data);
            if (processedSeqs.has(d.seq)) return;
            processedSeqs.add(d.seq);
            addToQueue({
                name: d.name, amount: d.amount, msg: d.msg,
                ytUrl: d.ytUrl, emoji: d.emoji,
                duration: d.duration, ytEnabled: d.ytEnabled,
                time: d.time ? new Date(d.time) : new Date(),
            });
        } catch(err) { console.error('SSE parse error:', err); }
    });

    es.addEventListener('stats', (e) => {
        try { applyServerStats(JSON.parse(e.data)); }
        catch(err) { console.error('SSE stats error:', err); }
    });

    es.addEventListener('ping', () => {});

    es.onerror = () => {
        badge.className = 'err';
        badge.innerHTML = '<span class="conn-dot"></span> Terputus…';
        es.close();
        setTimeout(connectSSE, 3000);
    };
}
connectSSE();

// ═══════════════════════════════════════════
//  NAVIGATION
// ═══════════════════════════════════════════
function showPage(page) {
    document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    document.getElementById('page-' + page).classList.add('active');
    document.querySelector(`.tab-btn[onclick*="${page}"]`).classList.add('active');
}

// ═══════════════════════════════════════════
//  DONATE FORM
// ═══════════════════════════════════════════
function setPreset(btn, amount) {
    document.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    document.getElementById('donor-amount').value = amount;
}

async function submitDonation() {
    const name   = document.getElementById('donor-name').value.trim() || 'Anonim';
    const amount = parseInt(document.getElementById('donor-amount').value) || 0;
    const msg    = document.getElementById('donor-msg').value.trim();
    const yt     = document.getElementById('donor-yt').value.trim();

    if (amount < 1000) { showToast('Minimal donasi Rp 1.000'); return; }
    const minAmount = parseInt(document.getElementById('min-amount').value) || 1000;
    if (amount < minAmount) { showToast('Minimal donasi ' + formatRp(minAmount)); return; }

    const ytEnabled = document.getElementById('toggle-yt').classList.contains('on');
    const duration  = yt && ytEnabled ? 20000
        : parseInt(document.getElementById('duration-select').value) * 1000;

    const emoji    = EMOJIS[Math.floor(Math.random() * EMOJIS.length)];
    const donation = { name, amount, msg: msg || 'Semangat streamnya! 🎉', ytUrl: yt, emoji, duration, ytEnabled };

    const btn = document.getElementById('submit-btn');
    btn.disabled = true; btn.textContent = 'Mengirim…';

    try {
        const res  = await fetch('api/push.php', {
            method: 'POST', headers: {'Content-Type':'application/json'},
            body: JSON.stringify(donation),
        });
        const data = await res.json();
        if (data.success) {
            processedSeqs.add(data.seq);
            addToQueue({ ...donation, time: new Date() });
            showToast('Donasi terkirim! Terima kasih ' + name + ' 🙏');
            document.getElementById('donor-name').value  = '';
            document.getElementById('donor-msg').value   = '';
            document.getElementById('donor-yt').value    = '';
            document.getElementById('donor-amount').value = '25000';
        } else { showToast(data.error || 'Gagal mengirim donasi'); }
    } catch (err) {
        showToast('Tidak dapat terhubung ke server');
        console.error(err);
    }
    btn.disabled = false;
    btn.textContent = 'Kirim Donasi Sekarang';
}

// ═══════════════════════════════════════════
//  QUEUE
// ═══════════════════════════════════════════
function addToQueue(donation) {
    state.queue.push(donation);
    updateQueueDisplay();
    if (!state.isShowing) processQueue();
}
function processQueue() {
    if (state.queue.length === 0) { state.isShowing = false; return; }
    state.isShowing = true;
    const donation = state.queue.shift();
    updateQueueDisplay();
    showAlert(donation);
}
function updateQueueDisplay() {
    document.getElementById('queue-count-display').textContent = state.queue.length;
    const list = document.getElementById('queue-list');
    if (state.queue.length === 0) {
        list.innerHTML = '<div style="color:var(--text-3);font-size:11px">Antrean kosong</div>';
        return;
    }
    list.innerHTML = state.queue.map((d, i) => `
        <div class="queue-item">
            <div class="queue-pos">${i+1}</div>
            <span class="queue-item-name">${esc(d.name)}</span>
            ${d.ytUrl ? '<span class="queue-yt-tag">YT</span>' : ''}
            <span class="queue-item-amount">${formatRp(d.amount)}</span>
        </div>`).join('');
}

// ═══════════════════════════════════════════
//  ALERT PREVIEW
// ═══════════════════════════════════════════
function showAlert(donation) {
    recordDonation(donation);
    if (state.soundOn) playSound(donation.amount);

    document.getElementById('alert-avatar').textContent  = donation.emoji;
    document.getElementById('alert-donor').textContent   = donation.name;
    document.getElementById('alert-amount').textContent  = formatRp(donation.amount);
    document.getElementById('alert-message').textContent = donation.msg;

    const ytSection = document.getElementById('alert-yt');
    const ytEnabled = document.getElementById('toggle-yt').classList.contains('on');
    const duration  = donation.duration || (donation.ytUrl && ytEnabled ? 20000
        : parseInt(document.getElementById('duration-select').value) * 1000);

    if (donation.ytUrl && ytEnabled) {
        const videoId = extractYtId(donation.ytUrl);
        if (videoId) {
            document.getElementById('yt-iframe').src = `https://www.youtube.com/embed/${videoId}?autoplay=1&mute=0`;
            ytSection.style.display = 'block';
        } else { ytSection.style.display = 'none'; }
    } else {
        ytSection.style.display = 'none';
        document.getElementById('yt-iframe').src = '';
    }

    const box = document.getElementById('alert-box');
    box.classList.remove('visible','hiding');
    void box.offsetWidth;
    box.classList.add('visible');

    const bar = document.getElementById('progress-bar');
    bar.style.transition = 'none'; bar.style.width = '100%';
    setTimeout(() => {
        bar.style.transition = `width ${duration}ms linear`;
        bar.style.width = '0%';
    }, 50);

    setTimeout(() => {
        box.classList.add('hiding');
        setTimeout(() => {
            box.classList.remove('visible','hiding');
            document.getElementById('yt-iframe').src = '';
            processQueue();
        }, 400);
    }, duration);
}

// ═══════════════════════════════════════════
//  SOUND
// ═══════════════════════════════════════════
function playSound(amount) {
    try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const notes = amount >= 50000 ? [523,659,784,1047] : [523,659,784];
        notes.forEach((freq, i) => {
            const osc  = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain); gain.connect(ctx.destination);
            osc.frequency.value = freq; osc.type = 'sine';
            const t = ctx.currentTime + i * 0.15;
            gain.gain.setValueAtTime(0, t);
            gain.gain.linearRampToValueAtTime(0.3, t + 0.05);
            gain.gain.exponentialRampToValueAtTime(0.001, t + 0.4);
            osc.start(t); osc.stop(t + 0.4);
        });
    } catch(e) {}
}

// ═══════════════════════════════════════════
//  RECORD & DASHBOARD
// ═══════════════════════════════════════════
function recordDonation(donation) {
    state.donations.unshift(donation);
    updateDashboard();
}

function updateDashboard() {
    const d       = state.donations;
    const total   = d.reduce((s,x) => s+x.amount, 0);
    const biggest = d.reduce((m,x) => x.amount>m.amount?x:m, {amount:0,name:'—'});
    const unique  = new Set(d.map(x => x.name.toLowerCase())).size;
    const ytCount = d.filter(x => x.ytUrl).length;

    document.getElementById('stat-today').textContent       = formatRp(total);
    document.getElementById('stat-today-count').textContent = d.length + ' transaksi';
    document.getElementById('stat-biggest').textContent     = formatRp(biggest.amount);
    document.getElementById('stat-biggest-name').textContent= biggest.name;
    document.getElementById('stat-donors').textContent      = unique;
    document.getElementById('stat-yt').textContent          = ytCount;
    document.getElementById('history-badge').textContent    = d.length + ' donasi';

    // Hero stats
    document.getElementById('hero-total').textContent  = formatRp(total);
    document.getElementById('hero-donors').textContent = unique;
    document.getElementById('hero-count').textContent  = d.length;

    const hist = document.getElementById('history-list');
    hist.innerHTML = d.length === 0
        ? '<div style="color:var(--text-3);font-size:13px;padding:24px 0;text-align:center">Belum ada donasi masuk.<br>Coba test di tab Overlay!</div>'
        : d.map((don, i) => `
            <div class="history-item ${i===0?'new':''}">
                <div class="h-avatar">${esc(don.emoji)}</div>
                <div style="flex:1;min-width:0">
                    <div class="h-name">${esc(don.name)}</div>
                    <div class="h-msg">${esc(don.msg)}</div>
                    ${don.ytUrl ? '<div class="h-yt">▶ Video YouTube</div>' : ''}
                </div>
                <div>
                    <div class="h-amount">${formatRp(don.amount)}</div>
                    <div class="h-time">${timeAgo(don.time)}</div>
                </div>
            </div>`).join('');

    const leaderMap = {};
    d.forEach(don => {
        const key = don.name.toLowerCase();
        if (!leaderMap[key]) leaderMap[key] = {name:don.name,total:0,count:0,emoji:don.emoji};
        leaderMap[key].total += don.amount;
        leaderMap[key].count++;
    });
    const leaders   = Object.values(leaderMap).sort((a,b) => b.total-a.total).slice(0,8);
    const medals    = ['🥇','🥈','🥉'];
    const rankClass = ['rank-1','rank-2','rank-3'];

    const lb = document.getElementById('leaderboard');
    lb.innerHTML = leaders.length === 0
        ? '<div style="color:var(--text-3);font-size:13px;padding:20px 0;text-align:center">Belum ada data…</div>'
        : leaders.map((l,i) => `
            <div class="leader-item ${rankClass[i]||''}">
                <div class="rank-num ${medals[i]?'':'plain'}">${medals[i]||i+1}</div>
                <div class="leader-emoji">${esc(l.emoji)}</div>
                <div class="leader-name">${esc(l.name)}</div>
                <div class="leader-right">
                    <div class="leader-total">${formatRp(l.total)}</div>
                    <div class="leader-count">${l.count}x donasi</div>
                </div>
            </div>`).join('');
}

// ═══════════════════════════════════════════
//  TEST FUNCTIONS
// ═══════════════════════════════════════════
function testDonation() {
    const samples = [
        {name:'Budi Santoso', amount:50000, msg:'Semangat streamnya kak! 🔥', ytUrl:''},
        {name:'Sari_Gaming',  amount:25000, msg:'GG bro! Kapan main bareng lagi?', ytUrl:''},
        {name:'Anonymous',    amount:10000, msg:'Hehe salam dari lurker setia!', ytUrl:''},
    ];
    const s = samples[Math.floor(Math.random() * samples.length)];
    pushTest({ ...s, emoji: EMOJIS[Math.floor(Math.random()*EMOJIS.length)] });
}
function testDonationYT() {
    pushTest({name:'YoutubeKing',amount:100000,msg:'Request video kak hehe!',ytUrl:'https://www.youtube.com/watch?v=dQw4w9WgXcQ',emoji:'🎬'});
}
function testDonationQueue() {
    [{name:'Donatur A',amount:15000,msg:'Pertama!',ytUrl:'',emoji:'🅰️'},
     {name:'Donatur B',amount:30000,msg:'Kedua!',ytUrl:'',emoji:'🅱️'},
     {name:'Donatur C',amount:75000,msg:'Ketiga tapi paling besar 👑',ytUrl:'',emoji:'©️'}]
    .forEach(d => pushTest(d));
    showToast('3 donasi masuk ke antrean!');
}
async function pushTest(donation) {
    const ytEnabled = document.getElementById('toggle-yt').classList.contains('on');
    const duration  = donation.ytUrl && ytEnabled ? 20000
        : parseInt(document.getElementById('duration-select').value) * 1000;
    try {
        const res  = await fetch('api/push.php', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body:JSON.stringify({ ...donation, ytEnabled, duration }),
        });
        const data = await res.json();
        if (data.success) {
            processedSeqs.add(data.seq);
            addToQueue({ ...donation, duration, ytEnabled, time: new Date() });
            showToast('Test donasi ditambahkan ke queue!');
        }
    } catch(e) {
        addToQueue({ ...donation, duration, ytEnabled, time: new Date() });
        showToast('Server tidak tersedia, preview lokal saja');
    }
}

// ═══════════════════════════════════════════
//  SETTINGS
// ═══════════════════════════════════════════
function toggleSetting(btn) {
    btn.classList.toggle('on');
    if (btn.id === 'toggle-sound') state.soundOn = btn.classList.contains('on');
    if (btn.id === 'toggle-yt')    state.ytOn    = btn.classList.contains('on');
}
function copyUrl(inputId) {
    const url = document.getElementById(inputId).value;
    navigator.clipboard.writeText(url).then(() => showToast('URL disalin!'));
}

// ═══════════════════════════════════════════
//  UTILS
// ═══════════════════════════════════════════
function formatRp(n) {
    if (n >= 1000000) return 'Rp ' + (n/1000000).toFixed(1).replace('.0','') + 'Jt';
    if (n >= 1000)    return 'Rp ' + (n/1000).toFixed(0) + 'K';
    return 'Rp ' + n;
}
function extractYtId(url) {
    if (!url) return null;
    const m = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/);
    return m ? m[1] : null;
}
function timeAgo(date) {
    if (!date || isNaN(date)) return 'baru saja';
    const s = Math.floor((new Date() - date) / 1000);
    if (s < 60)   return 'baru saja';
    if (s < 3600) return Math.floor(s/60) + ' mnt lalu';
    return Math.floor(s/3600) + ' jam lalu';
}
function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
}

// ═══════════════════════════════════════════
//  SERVER STATS
// ═══════════════════════════════════════════
function applyServerStats(data) {
    const total   = data.total   || 0;
    const count   = data.count   || 0;
    const donors  = data.donors  || 0;
    const ytCount = data.ytCount || 0;
    const biggest = data.biggest || {name:'—', amount:0};
    const lb      = data.leaderboard || [];
    const ms      = data.milestone   || {};
    const config  = data.config      || {};

    document.getElementById('stat-today').textContent       = formatRp(total);
    document.getElementById('stat-today-count').textContent = count + ' transaksi';
    document.getElementById('stat-biggest').textContent     = formatRp(biggest.amount);
    document.getElementById('stat-biggest-name').textContent= biggest.name;
    document.getElementById('stat-donors').textContent      = donors;
    document.getElementById('stat-yt').textContent          = ytCount;
    document.getElementById('history-badge').textContent    = count + ' donasi';
    document.getElementById('hero-total').textContent       = formatRp(total);
    document.getElementById('hero-donors').textContent      = donors;
    document.getElementById('hero-count').textContent       = count;

    const medals    = ['🥇','🥈','🥉'];
    const rankClass = ['rank-1','rank-2','rank-3'];
    const lbEl = document.getElementById('leaderboard');
    lbEl.innerHTML = lb.length === 0
        ? '<div style="color:var(--text-3);font-size:13px;padding:20px 0;text-align:center">Belum ada data…</div>'
        : lb.map((l,i) => `
            <div class="leader-item ${rankClass[i]||''}">
                <div class="rank-num ${medals[i]?'':'plain'}">${medals[i]||i+1}</div>
                <div class="leader-emoji">${esc(l.emoji||'🎉')}</div>
                <div class="leader-name">${esc(l.name)}</div>
                <div class="leader-right">
                    <div class="leader-total">${formatRp(l.total)}</div>
                    <div class="leader-count">${l.count}x donasi</div>
                </div>
            </div>`).join('');

    updateMsPreview(ms.current || 0, ms.target || config.milestoneTarget || 1000000);

    if (config.milestoneTitle)   document.getElementById('cfg-ms-title').value   = config.milestoneTitle;
    if (config.milestoneTarget)  document.getElementById('cfg-ms-target').value  = config.milestoneTarget;
    if (config.leaderboardTitle) document.getElementById('cfg-lb-title').value   = config.leaderboardTitle;
    if (config.leaderboardCount) document.getElementById('cfg-lb-count').value   = config.leaderboardCount;
}

async function loadServerStats() {
    try {
        const res  = await fetch('api/stats.php');
        const data = await res.json();
        applyServerStats(data);
    } catch(e) {}
}

// ═══════════════════════════════════════════
//  MILESTONE PREVIEW
// ═══════════════════════════════════════════
function updateMsPreview(current, target) {
    const pct = target > 0 ? Math.min(100, Math.round(current / target * 100)) : 0;
    document.getElementById('ms-preview-bar').style.width     = pct + '%';
    document.getElementById('ms-preview-current').textContent = formatRp(current);
    document.getElementById('ms-preview-pct').textContent     = pct + '%';
    document.getElementById('ms-preview-target').textContent  = '/ ' + formatRp(target);
}

document.addEventListener('DOMContentLoaded', () => {
    const targetInput = document.getElementById('cfg-ms-target');
    if (targetInput) {
        targetInput.addEventListener('input', () => {
            const current = parseInt(document.getElementById('stat-today').textContent.replace(/[^0-9]/g,'')) || 0;
            updateMsPreview(current, parseInt(targetInput.value) || 1000000);
        });
    }
});

// ═══════════════════════════════════════════
//  SAVE CONFIG
// ═══════════════════════════════════════════
async function saveConfig() {
    const btn = document.getElementById('save-config-btn');
    btn.disabled = true; btn.textContent = 'Menyimpan…';
    const payload = {
        milestoneTitle:   document.getElementById('cfg-ms-title').value.trim(),
        milestoneTarget:  parseInt(document.getElementById('cfg-ms-target').value) || 1000000,
        leaderboardTitle: document.getElementById('cfg-lb-title').value.trim(),
        leaderboardCount: parseInt(document.getElementById('cfg-lb-count').value) || 10,
    };
    try {
        const res  = await fetch('api/config.php', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body:JSON.stringify(payload),
        });
        const data = await res.json();
        if (data.success) { showToast('Konfigurasi disimpan!'); }
        else { showToast(data.error || 'Gagal menyimpan'); }
    } catch(e) { showToast('Tidak dapat terhubung ke server'); }
    btn.disabled = false;
    btn.textContent = 'Simpan Konfigurasi';
}

// ═══════════════════════════════════════════
//  RESET HISTORY
// ═══════════════════════════════════════════
async function resetHistory() {
    if (!confirm('Reset seluruh riwayat donasi?\n\nLeaderboard dan milestone akan kembali ke nol.\nTindakan ini tidak dapat dibatalkan.')) return;
    try {
        const res  = await fetch('api/config.php', {
            method:'POST', headers:{'Content-Type':'application/json'},
            body:JSON.stringify({ resetHistory: true }),
        });
        const data = await res.json();
        if (data.success) {
            showToast('Riwayat donasi berhasil direset');
            state.donations = [];
            updateDashboard();
            await loadServerStats();
        } else { showToast('Gagal reset: ' + (data.error || 'unknown')); }
    } catch(e) { showToast('Tidak dapat terhubung ke server'); }
}

// Init
updateDashboard();
loadServerStats();
</script>
</body>
</html>
