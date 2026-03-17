<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>StreamDonate — OBS Canvas</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet" />
    <style>
        /* ─── BASE ─── */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            width: {{ $canvasConfig['width'] }}px;
            height: {{ $canvasConfig['height'] }}px;
            background: transparent !important;
            overflow: hidden;
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        /* ─── CSS VARIABLES ─── */
        :root {
            --bg:        rgba(8,8,12,.96);
            --border:    rgba(255,255,255,.1);
            --accent:    #7c6cfc;
            --accent2:   #a99dff;
            --orange:    #f97316;
            --green:     #22d3a0;
            --text:      #f1f1f6;
            --text-2:    rgba(241,241,246,.55);
            --bar-bg:    rgba(255,255,255,.07);
            --radius:    16px;
            --shadow:    0 8px 40px rgba(0,0,0,.7), 0 0 0 1px rgba(255,255,255,.06);
            --amount-c:  #f97316;
            --donor-c:   #f1f1f6;
            --msg-c:     rgba(241,241,246,.6);
            --top-line:  linear-gradient(90deg,#7c6cfc,#a855f7,#22d3a0);
            --prog-bar:  linear-gradient(90deg,#7c6cfc,#f97316);
            --brand:     #7c6cfc;
            --brand2:    #a99dff;
            --yellow:    #fbbf24;
            --purple:    #a855f7;
            --surface:   rgba(8,8,12,.96);
            --border2:   rgba(124,108,252,.2);
        }

        /* ─── THEME overrides ─── */
        body.theme-minimal {
            --bg:       rgba(12,12,16,.95); --border: rgba(255,255,255,.14);
            --accent:   #e0e0f0; --accent2: #ffffff;
            --amount-c: #ffffff; --donor-c: #ffffff;
            --top-line: linear-gradient(90deg,rgba(255,255,255,.5),rgba(255,255,255,.15));
            --prog-bar: linear-gradient(90deg,rgba(255,255,255,.7),rgba(255,255,255,.3));
            --shadow:   0 8px 32px rgba(0,0,0,.6), 0 0 0 1px rgba(255,255,255,.1);
            --radius:   12px;
        }
        body.theme-neon {
            --bg:       rgba(2,4,18,.97); --border: rgba(0,255,200,.22);
            --accent:   #00ffc8; --accent2: #00e5ff;
            --amount-c: #00e5ff; --donor-c: #00ffc8; --msg-c: rgba(0,255,200,.6);
            --top-line: linear-gradient(90deg,#00ffc8,#00c8ff,#7c6cfc);
            --prog-bar: linear-gradient(90deg,#00ffc8,#00c8ff);
            --shadow:   0 8px 40px rgba(0,0,0,.85), 0 0 0 1px rgba(0,255,200,.12), 0 0 40px rgba(0,255,200,.1);
            --radius:   14px;
        }
        body.theme-neon .alert-donor { text-shadow: 0 0 18px rgba(0,255,200,.5); }
        body.theme-neon .alert-amount { text-shadow: 0 0 16px rgba(0,229,255,.45); }
        body.theme-fire {
            --bg:       rgba(10,4,2,.97); --border: rgba(249,115,22,.22);
            --accent:   #f97316; --accent2: #fbbf24;
            --amount-c: #fbbf24; --donor-c: #fef3c7; --msg-c: rgba(254,243,199,.5);
            --top-line: linear-gradient(90deg,#f97316,#ef4444,#fbbf24);
            --prog-bar: linear-gradient(90deg,#f97316,#ef4444,#fbbf24);
            --shadow:   0 8px 40px rgba(0,0,0,.85), 0 0 0 1px rgba(249,115,22,.1), 0 0 50px rgba(239,68,68,.1);
            --radius:   16px;
        }
        body.theme-fire .alert-donor  { text-shadow: 0 0 24px rgba(251,191,36,.35); }
        body.theme-fire .alert-amount { text-shadow: 0 0 16px rgba(251,191,36,.4); }
        body.theme-ice {
            --bg:       rgba(2,8,22,.96); --border: rgba(147,210,255,.18);
            --accent:   #38bdf8; --accent2: #818cf8;
            --amount-c: #38bdf8; --donor-c: #e0f2fe; --msg-c: rgba(224,242,254,.5);
            --top-line: linear-gradient(90deg,#38bdf8,#818cf8,#e0f2fe);
            --prog-bar: linear-gradient(90deg,#38bdf8,#818cf8);
            --shadow:   0 8px 40px rgba(0,0,0,.85), 0 0 0 1px rgba(147,210,255,.08), 0 0 40px rgba(56,189,248,.08);
            --radius:   18px;
        }
        body.theme-ice .alert-donor  { text-shadow: 0 0 18px rgba(56,189,248,.4); }
        body.theme-ice .alert-amount { text-shadow: 0 0 14px rgba(56,189,248,.35); }

        /* ════════════════════════════════════════════
           WIDGET CONTAINER — absolute positioning
        ════════════════════════════════════════════ */
        .widget-container {
            position: absolute;
            overflow: hidden;
        }

        /* ════════════════════════════════════════════
           NOTIFICATION ALERT WIDGET
        ════════════════════════════════════════════ */
        @keyframes alertIn {
            from { transform: translateY(30px) scale(.97); opacity: 0; }
            to   { transform: translateY(0) scale(1); opacity: 1; }
        }
        @keyframes alertOut {
            from { transform: translateY(0) scale(1); opacity: 1; }
            to   { transform: translateY(20px) scale(.97); opacity: 0; }
        }
        @keyframes alertIn_minimal {
            from { transform: translateY(-12px); opacity: 0; }
            to   { transform: translateY(0); opacity: 1; }
        }
        @keyframes alertOut_minimal {
            from { transform: translateY(0); opacity: 1; }
            to   { transform: translateY(-12px); opacity: 0; }
        }
        @keyframes alertIn_neon {
            0%   { transform: scale(.88); opacity: 0; filter: blur(6px); }
            65%  { transform: scale(1.02); opacity: 1; filter: blur(0); }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes alertOut_neon {
            from { transform: scale(1); opacity: 1; }
            to   { transform: scale(.88); opacity: 0; filter: blur(6px); }
        }
        @keyframes alertIn_fire {
            0%   { transform: translateY(50px) scale(.92); opacity: 0; filter: blur(3px); }
            70%  { transform: translateY(-4px) scale(1.01); opacity: 1; filter: blur(0); }
            100% { transform: translateY(0) scale(1); opacity: 1; }
        }
        @keyframes alertOut_fire {
            from { transform: translateY(0) scale(1); opacity: 1; }
            to   { transform: translateY(-30px) scale(.95); opacity: 0; filter: blur(3px); }
        }
        @keyframes alertIn_ice {
            0%   { transform: scale(.7); opacity: 0; filter: blur(5px) brightness(1.6); }
            65%  { transform: scale(1.03); opacity: 1; filter: blur(0) brightness(1.05); }
            100% { transform: scale(1); opacity: 1; filter: blur(0) brightness(1); }
        }
        @keyframes alertOut_ice {
            from { transform: scale(1); opacity: 1; }
            to   { transform: scale(.7); opacity: 0; filter: blur(5px); }
        }

        .alert-box {
            width: 100%; height: 100%;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            opacity: 0;
            overflow: hidden;
            pointer-events: none;
            font-family: 'Inter', sans-serif;
            display: flex; flex-direction: column;
            position: relative;
        }
        .alert-box::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
            background: var(--top-line);
        }
        .alert-box.visible {
            animation: alertIn .48s cubic-bezier(.34,1.56,.64,1) forwards;
        }
        .alert-box.hiding {
            animation: alertOut .34s cubic-bezier(.4,0,1,1) forwards;
        }
        body.theme-minimal .alert-box.visible { animation-name: alertIn_minimal;  animation-duration: .38s; animation-timing-function: ease-out; }
        body.theme-minimal .alert-box.hiding  { animation-name: alertOut_minimal; animation-duration: .28s; animation-timing-function: ease-in; }
        body.theme-neon    .alert-box.visible { animation-name: alertIn_neon;     animation-duration: .5s;  animation-timing-function: ease-out; }
        body.theme-neon    .alert-box.hiding  { animation-name: alertOut_neon;    animation-duration: .32s; }
        body.theme-fire    .alert-box.visible { animation-name: alertIn_fire;     animation-duration: .5s;  animation-timing-function: ease-out; }
        body.theme-fire    .alert-box.hiding  { animation-name: alertOut_fire;    animation-duration: .36s; }
        body.theme-ice     .alert-box.visible { animation-name: alertIn_ice;      animation-duration: .46s; animation-timing-function: ease-out; }
        body.theme-ice     .alert-box.hiding  { animation-name: alertOut_ice;     animation-duration: .32s; }

        .alert-inner { padding: 16px 18px 0; flex: 1; display: flex; flex-direction: column; }
        .alert-header { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 12px; }
        .alert-avatar {
            width: 44px; height: 44px; border-radius: 11px;
            background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1);
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; flex-shrink: 0; line-height: 1;
        }
        .alert-meta { flex: 1; min-width: 0; }
        .alert-donor  { font-size: 16px; font-weight: 700; color: var(--donor-c); letter-spacing: -.3px; line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .alert-amount { font-family: 'Space Grotesk', sans-serif; font-size: 22px; font-weight: 700; letter-spacing: -.6px; color: var(--amount-c); line-height: 1.1; margin-top: 2px; }
        .alert-badge  { flex-shrink: 0; font-size: 8px; font-weight: 800; letter-spacing: 1.4px; text-transform: uppercase; color: var(--accent2); opacity: .7; padding-top: 3px; white-space: nowrap; }
        .alert-divider { height: 1px; background: rgba(255,255,255,.06); margin: 0 -18px; }
        .alert-message { font-size: 12px; font-weight: 400; color: var(--msg-c); line-height: 1.65; padding: 10px 0 12px; word-break: break-word; white-space: pre-wrap; }
        .alert-message:empty { display: none; }
        .alert-yt { margin-bottom: 0; border-radius: 10px; overflow: hidden; border: 1px solid rgba(255,255,255,.06); display: none; margin: 0 0 12px; }
        .alert-yt iframe { width: 100%; height: 160px; display: block; border: none; }
        .alert-progress { height: 2px; background: var(--bar-bg); margin-top: auto; }
        .alert-progress-bar { height: 100%; background: var(--prog-bar); width: 100%; }

        /* ════════════════════════════════════════════
           LEADERBOARD WIDGET
        ════════════════════════════════════════════ */
        @keyframes blink { 0%,100% { opacity:1; } 50% { opacity:.25; } }
        @keyframes itemIn { from { opacity:0; transform:translateX(-10px); } to { opacity:1; transform:translateX(0); } }

        #lb-widget {
            width: 100%; height: 100%;
            opacity: 0;
            transform: translateX(-16px);
            transition: opacity .5s ease, transform .55s cubic-bezier(.34,1.3,.64,1);
        }
        #lb-widget.visible { opacity: 1; transform: translateX(0); }

        .lb-wrap {
            width: 100%; height: 100%;
            background: var(--surface);
            border: 1px solid var(--border2);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 40px rgba(0,0,0,.7), inset 0 0 0 1px rgba(255,255,255,.04);
            position: relative;
            display: flex; flex-direction: column;
        }
        .lb-wrap::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
            background: linear-gradient(90deg, var(--brand), var(--purple), var(--green));
        }
        .lb-header { padding: 14px 16px 12px; border-bottom: 1px solid rgba(255,255,255,.06); flex-shrink: 0; }
        .lb-live { display: inline-flex; align-items: center; gap: 5px; background: rgba(124,108,252,.1); border: 1px solid rgba(124,108,252,.22); border-radius: 20px; padding: 2px 8px; font-size: 8px; font-weight: 800; letter-spacing: 1.5px; color: var(--brand2); text-transform: uppercase; margin-bottom: 7px; }
        .lb-live-dot { width: 5px; height: 5px; border-radius: 50%; background: var(--brand); animation: blink 2s infinite; }
        .lb-title    { font-family: 'Space Grotesk', sans-serif; font-size: 17px; font-weight: 700; letter-spacing: -.4px; color: var(--text); line-height: 1; }
        .lb-subtitle { font-size: 10px; color: rgba(241,241,246,.35); margin-top: 3px; }
        .lb-list { flex: 1; overflow: hidden; padding: 4px 0 6px; display: flex; flex-direction: column; }
        .lb-item { display: flex; align-items: center; gap: 8px; padding: 7px 14px; position: relative; transition: background .25s; }
        .lb-item + .lb-item::before { content: ''; position: absolute; top: 0; left: 14px; right: 14px; height: 1px; background: rgba(255,255,255,.04); }
        .lb-item.rank-1 { background: rgba(251,191,36,.04); }
        .lb-item.rank-2 { background: rgba(180,180,200,.02); }
        .lb-item.rank-3 { background: rgba(205,127,50,.03); }
        .lb-item.new    { animation: itemIn .4s ease forwards; }
        .lb-rank { width: 22px; flex-shrink: 0; text-align: center; font-size: 15px; line-height: 1; }
        .lb-rank.num { font-family: 'Space Grotesk', sans-serif; font-size: 11px; font-weight: 700; color: rgba(241,241,246,.35); }
        .lb-avatar { width: 27px; height: 27px; border-radius: 7px; background: rgba(124,108,252,.08); border: 1px solid rgba(124,108,252,.14); display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; }
        .lb-info   { flex: 1; min-width: 0; }
        .lb-name   { font-size: 12px; font-weight: 600; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.2; }
        .lb-count  { font-size: 9px; color: rgba(241,241,246,.35); margin-top: 1px; }
        .lb-amount { text-align: right; flex-shrink: 0; }
        .lb-total  { font-family: 'Space Grotesk', sans-serif; font-size: 13px; font-weight: 700; letter-spacing: -.3px; color: var(--yellow); line-height: 1; }
        .lb-item.rank-1 .lb-total { color: #fde68a; }
        .lb-footer { padding: 8px 14px 12px; border-top: 1px solid rgba(255,255,255,.06); display: flex; align-items: center; justify-content: space-between; flex-shrink: 0; }
        .lb-footer-label { font-size: 9px; color: rgba(241,241,246,.35); font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
        .lb-footer-val   { font-family: 'Space Grotesk', sans-serif; font-size: 15px; font-weight: 700; letter-spacing: -.4px; color: var(--green); }
        .lb-empty { padding: 18px 14px; font-size: 11px; color: rgba(241,241,246,.35); text-align: center; }

        /* ════════════════════════════════════════════
           MILESTONE WIDGET
        ════════════════════════════════════════════ */
        @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }
        @keyframes reachedGlow {
            0%,100% { box-shadow: 0 8px 40px rgba(0,0,0,.7), 0 0 0 1px rgba(255,255,255,.04), 0 0 30px rgba(34,211,160,.1); }
            50%     { box-shadow: 0 8px 40px rgba(0,0,0,.7), 0 0 0 1px rgba(255,255,255,.04), 0 0 60px rgba(34,211,160,.25), 0 0 20px rgba(124,108,252,.12); }
        }

        #ms-widget {
            width: 100%; height: 100%;
            opacity: 0;
            transform: translateY(14px);
            transition: opacity .5s ease, transform .55s cubic-bezier(.34,1.3,.64,1);
        }
        #ms-widget.visible { opacity: 1; transform: translateY(0); }

        .ms-wrap {
            width: 100%; height: 100%;
            background: var(--surface);
            border: 1px solid var(--border2);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 40px rgba(0,0,0,.7), inset 0 0 0 1px rgba(255,255,255,.04);
            position: relative;
            display: flex; flex-direction: column;
        }
        .ms-wrap::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px; background: linear-gradient(90deg, var(--brand), var(--purple), var(--green)); }
        .ms-inner { flex: 1; display: flex; flex-direction: column; padding: 14px 16px 16px; gap: 8px; }
        .ms-badge { display: inline-flex; align-items: center; gap: 5px; background: rgba(124,108,252,.1); border: 1px solid rgba(124,108,252,.22); border-radius: 20px; padding: 2px 8px; font-size: 8px; font-weight: 800; letter-spacing: 1.5px; color: var(--brand2); text-transform: uppercase; width: fit-content; }
        .ms-title { font-family: 'Space Grotesk', sans-serif; font-size: 14px; font-weight: 700; letter-spacing: -.3px; color: var(--text); line-height: 1.2; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .ms-amounts { display: flex; align-items: baseline; gap: 5px; }
        .ms-current { font-family: 'Space Grotesk', sans-serif; font-size: 20px; font-weight: 700; letter-spacing: -.6px; color: var(--orange); line-height: 1; }
        .ms-sep     { font-size: 12px; color: rgba(241,241,246,.35); }
        .ms-target  { font-family: 'Space Grotesk', sans-serif; font-size: 13px; font-weight: 600; letter-spacing: -.3px; color: rgba(241,241,246,.55); line-height: 1; }
        .ms-pct     { margin-left: auto; font-family: 'Space Grotesk', sans-serif; font-size: 13px; font-weight: 700; letter-spacing: -.3px; color: var(--brand2); }
        .ms-reached-label { display: none; margin-left: auto; font-family: 'Space Grotesk', sans-serif; font-size: 12px; font-weight: 700; color: var(--green); }
        .ms-track { height: 7px; background: rgba(255,255,255,.06); border-radius: 4px; overflow: hidden; position: relative; margin-top: auto; }
        .ms-bar { height: 100%; width: 0%; border-radius: 4px; background: linear-gradient(90deg, var(--brand), var(--purple), var(--orange)); background-size: 200% 100%; transition: width 1.2s cubic-bezier(.4,0,.2,1); position: relative; }
        .ms-bar::after { content: ''; position: absolute; inset: 0; background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,.22) 50%, transparent 100%); background-size: 200% 100%; animation: shimmer 2.8s ease-in-out infinite; }
        #ms-widget.reached .ms-bar { background: linear-gradient(90deg, var(--green), #00e5b0, var(--brand2)); }
        #ms-widget.reached .ms-bar::after { animation: shimmer 1.2s ease-in-out infinite; }
        #ms-widget.reached .ms-wrap { border-color: rgba(34,211,160,.28); animation: reachedGlow 2s ease-in-out infinite; }
        #ms-widget.reached .ms-wrap::before { background: linear-gradient(90deg, var(--green), var(--brand2), var(--green)); }
        #ms-widget.reached .ms-pct { display: none; }
        #ms-widget.reached .ms-reached-label { display: inline; }

        /* ════════════════════════════════════════════
           QR CODE WIDGET
        ════════════════════════════════════════════ */
        @keyframes slideInQr { from { transform: translateY(20px) scale(.95); opacity: 0; } to { transform: translateY(0) scale(1); opacity: 1; } }
        @keyframes pulse { 0%,100% { opacity:1; transform:scale(1); } 50% { opacity:.4; transform:scale(.65); } }

        .qr-widget {
            width: 100%; height: 100%;
            background: rgba(10,10,16,.93);
            border: 1px solid rgba(124,108,252,.28);
            border-radius: 22px;
            padding: 16px;
            box-shadow: 0 0 0 1px rgba(124,108,252,.08), 0 24px 60px rgba(0,0,0,.7), 0 0 80px rgba(124,108,252,.12);
            animation: slideInQr .6s cubic-bezier(.34,1.3,.64,1) both;
            display: flex; flex-direction: column; align-items: center; gap: 8px;
            position: relative; box-sizing: border-box;
        }
        .qr-widget::before {
            content: ''; position: absolute; top: 0; left: 14px; right: 14px; height: 2px;
            border-radius: 2px; background: linear-gradient(90deg, #7c6cfc, #a99dff);
        }
        .qr-header { display: flex; align-items: center; gap: 8px; width: 100%; }
        .qr-logo-icon { width: 24px; height: 24px; border-radius: 7px; background: linear-gradient(135deg, #7c6cfc, #6356e8); display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 800; color: #fff; flex-shrink: 0; }
        .qr-header-text { flex: 1; min-width: 0; }
        .qr-header-title { font-family: 'Space Grotesk', sans-serif; font-size: 12px; font-weight: 700; color: #f1f1f6; letter-spacing: -.2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .qr-header-sub   { font-size: 9px; color: #a0a0b4; margin-top: 1px; }
        .qr-image-wrap { flex: 1; width: 100%; border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,.07); background: #141419; display: flex; align-items: center; justify-content: center; min-height: 0; }
        .qr-image-wrap img, .qr-image-wrap svg { width: 100%; height: 100%; display: block; object-fit: contain; }
        .qr-footer { display: flex; align-items: center; gap: 6px; }
        .scan-pulse { width: 7px; height: 7px; border-radius: 50%; background: #a99dff; animation: pulse 2s infinite; flex-shrink: 0; }
        .scan-label { font-size: 10px; color: #a0a0b4; font-weight: 600; letter-spacing: .3px; }
        .qr-url { font-size: 9px; color: rgba(169,157,255,.6); font-family: monospace; word-break: break-all; text-align: center; line-height: 1.4; }

        /* ── SSE status ── */
        #sse-status {
            position: fixed; top: 8px; right: 10px;
            font-size: 8px; color: rgba(255,255,255,.15);
            font-family: monospace; pointer-events: none; letter-spacing: .5px;
        }
    </style>
</head>
<body class="theme-{{ $streamer->alert_theme ?? 'default' }}">

<div id="sse-status">connecting…</div>

{{-- ════ NOTIFICATION WIDGET ════ --}}
@php $notif = $canvasConfig['widgets']['notification']; @endphp
@if($notif['active'])
<div class="widget-container" style="left:{{ $notif['x'] }}px; top:{{ $notif['y'] }}px; width:{{ $notif['w'] }}px; height:{{ $notif['h'] }}px;">
    <div class="alert-box" id="alert-box">
        <div class="alert-inner">
            <div class="alert-header">
                <div class="alert-avatar" id="alert-avatar">🎉</div>
                <div class="alert-meta">
                    <div class="alert-donor"  id="alert-donor">Nama Donatur</div>
                    <div class="alert-amount" id="alert-amount">Rp 0</div>
                </div>
                <div class="alert-badge">DONASI MASUK</div>
            </div>
            <div class="alert-divider" id="alert-divider"></div>
            <div class="alert-message" id="alert-message"></div>
            <div class="alert-yt" id="alert-yt">
                <iframe id="yt-iframe" allow="autoplay" allowfullscreen></iframe>
            </div>
        </div>
        <div class="alert-progress">
            <div class="alert-progress-bar" id="progress-bar"></div>
        </div>
    </div>
</div>
@endif

{{-- ════ LEADERBOARD WIDGET ════ --}}
@php $lb = $canvasConfig['widgets']['leaderboard']; @endphp
@if($lb['active'])
<div class="widget-container" style="left:{{ $lb['x'] }}px; top:{{ $lb['y'] }}px; width:{{ $lb['w'] }}px; height:{{ $lb['h'] }}px;">
    <div id="lb-widget">
        <div class="lb-wrap">
            <div class="lb-header">
                <div class="lb-live"><span class="lb-live-dot"></span>LIVE</div>
                <div class="lb-title" id="lb-title">Top Donatur</div>
                <div class="lb-subtitle" id="lb-subtitle">0 donatur &bull; Rp 0 total</div>
            </div>
            <div class="lb-list" id="lb-list">
                <div class="lb-empty">Memuat data…</div>
            </div>
            <div class="lb-footer">
                <div class="lb-footer-label">Total Terkumpul</div>
                <div class="lb-footer-val" id="lb-grand-total">Rp 0</div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ════ MILESTONE WIDGET ════ --}}
@php $ms = $canvasConfig['widgets']['milestone']; @endphp
@if($ms['active'])
<div class="widget-container" style="left:{{ $ms['x'] }}px; top:{{ $ms['y'] }}px; width:{{ $ms['w'] }}px; height:{{ $ms['h'] }}px;">
    <div id="ms-widget">
        <div class="ms-wrap">
            <div class="ms-inner">
                <div class="ms-badge">🎯 MILESTONE</div>
                <div class="ms-title" id="ms-title">Target Stream Hari Ini</div>
                <div class="ms-amounts">
                    <div class="ms-current" id="ms-current">Rp 0</div>
                    <div class="ms-sep">/</div>
                    <div class="ms-target" id="ms-target">Rp 1Jt</div>
                    <div class="ms-pct" id="ms-pct">0%</div>
                    <div class="ms-reached-label">TERCAPAI! 🎉</div>
                </div>
                <div class="ms-track">
                    <div class="ms-bar" id="ms-bar"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ════ QR CODE WIDGET ════ --}}
@php $qr = $canvasConfig['widgets']['qrcode']; @endphp
@if($qr['active'])
<div class="widget-container" style="left:{{ $qr['x'] }}px; top:{{ $qr['y'] }}px; width:{{ $qr['w'] }}px; height:{{ $qr['h'] }}px;">
    <div class="qr-widget">
        <div class="qr-header">
            <div class="qr-logo-icon">SD</div>
            <div class="qr-header-text">
                <div class="qr-header-title">{{ $streamer->display_name }}</div>
                <div class="qr-header-sub">Scan untuk donasi</div>
            </div>
        </div>
        <div class="qr-image-wrap">{!! $qrSvg !!}</div>
        <div class="qr-footer">
            <span class="scan-pulse"></span>
            <span class="scan-label">SCAN TO DONATE</span>
        </div>
        <div class="qr-url">{{ $donateUrl }}</div>
    </div>
</div>
@endif

<script>
// ── Config dari server ──
const SSE_BASE_URL  = '{{ url("/" . $streamer->slug . "/sse") }}?key={{ $apiKey }}';
const STATS_URL     = '{{ url("/" . $streamer->slug . "/stats") }}?key={{ $apiKey }}';
const ASSET_STORAGE = '{{ asset("storage") }}';
const WIDGETS_ACTIVE = {
    notification: {{ $notif['active'] ? 'true' : 'false' }},
    leaderboard:  {{ $lb['active']    ? 'true' : 'false' }},
    milestone:    {{ $ms['active']    ? 'true' : 'false' }},
    qrcode:       {{ $qr['active']    ? 'true' : 'false' }},
};

// ── Mutable config ──
let SOUND_ON       = {{ $streamer->sound_enabled ? 'true' : 'false' }};
let SOUND_PREF     = {!! json_encode($streamer->notification_sound ?? 'ding') !!};
let ALERT_DURATION = {{ (int) ($streamer->alert_duration ?? 8000) }};

// ── localStorage untuk recovery ──
const LS_SEQ_KEY = 'ssd_canvas_seq_{{ $streamer->slug }}';
function getLastKnownSeq() {
    try { const r = localStorage.getItem(LS_SEQ_KEY); if (!r) return null; const n = parseInt(r,10); return isNaN(n)?null:n; } catch(e) { return null; }
}
function saveLastKnownSeq(seq) {
    try { if (seq!=null) localStorage.setItem(LS_SEQ_KEY, String(seq)); } catch(e) {}
}
function buildSseUrl() {
    const seq = getLastKnownSeq();
    return seq !== null ? SSE_BASE_URL + '&last_seq=' + seq : SSE_BASE_URL;
}

const statusEl = document.getElementById('sse-status');
const seenIds  = new Set();
const MEDALS   = ['🥇','🥈','🥉'];

// ═══ WEB AUDIO API ═══
let _audioCtx = null;
function getAudioCtx() {
    if (!_audioCtx) _audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    if (_audioCtx.state === 'suspended') _audioCtx.resume();
    return _audioCtx;
}
let _customSoundUrl = null, _customSoundBuffer = null;
function playDing() {
    const ctx = getAudioCtx(); const osc = ctx.createOscillator(), gain = ctx.createGain();
    osc.connect(gain); gain.connect(ctx.destination); osc.type = 'sine';
    osc.frequency.setValueAtTime(880, ctx.currentTime); osc.frequency.exponentialRampToValueAtTime(1320, ctx.currentTime+.05); osc.frequency.exponentialRampToValueAtTime(660, ctx.currentTime+.4);
    gain.gain.setValueAtTime(.6, ctx.currentTime); gain.gain.exponentialRampToValueAtTime(.001, ctx.currentTime+.6);
    osc.start(ctx.currentTime); osc.stop(ctx.currentTime+.6);
}
function playCoin() {
    const ctx = getAudioCtx();
    [0,.12].forEach(function(d){ const osc=ctx.createOscillator(),gain=ctx.createGain(); osc.connect(gain); gain.connect(ctx.destination); osc.type='square'; osc.frequency.setValueAtTime(988,ctx.currentTime+d); osc.frequency.setValueAtTime(1319,ctx.currentTime+d+.07); gain.gain.setValueAtTime(.35,ctx.currentTime+d); gain.gain.exponentialRampToValueAtTime(.001,ctx.currentTime+d+.3); osc.start(ctx.currentTime+d); osc.stop(ctx.currentTime+d+.3); });
}
function playWhoosh() {
    const ctx=getAudioCtx(), bufSize=ctx.sampleRate*.6, buf=ctx.createBuffer(1,bufSize,ctx.sampleRate), data=buf.getChannelData(0);
    for(let i=0;i<bufSize;i++) data[i]=(Math.random()*2-1);
    const src=ctx.createBufferSource(); src.buffer=buf;
    const filter=ctx.createBiquadFilter(); filter.type='bandpass'; filter.frequency.setValueAtTime(400,ctx.currentTime); filter.frequency.exponentialRampToValueAtTime(2000,ctx.currentTime+.2); filter.frequency.exponentialRampToValueAtTime(200,ctx.currentTime+.6); filter.Q.value=.8;
    const gain=ctx.createGain(); gain.gain.setValueAtTime(0,ctx.currentTime); gain.gain.linearRampToValueAtTime(.5,ctx.currentTime+.05); gain.gain.exponentialRampToValueAtTime(.001,ctx.currentTime+.6);
    src.connect(filter); filter.connect(gain); gain.connect(ctx.destination); src.start(ctx.currentTime); src.stop(ctx.currentTime+.6);
}
function _playDecoded(decoded) { const ctx=getAudioCtx(),src=ctx.createBufferSource(),gain=ctx.createGain(); src.buffer=decoded; src.connect(gain); gain.connect(ctx.destination); gain.gain.setValueAtTime(.9,ctx.currentTime); src.start(ctx.currentTime); }
function getSoundUrl() { return (SOUND_PREF && !['ding','coin','whoosh'].includes(SOUND_PREF)) ? ASSET_STORAGE+'/'+SOUND_PREF : null; }
function playAlertSound() {
    if (!SOUND_ON) return;
    const url = getSoundUrl();
    if (url) {
        if (_customSoundBuffer && _customSoundUrl===url) { _playDecoded(_customSoundBuffer); return; }
        const ctx=getAudioCtx();
        fetch(url).then(function(r){return r.arrayBuffer();}).then(function(b){return ctx.decodeAudioData(b);}).then(function(d){ _customSoundUrl=url; _customSoundBuffer=d; _playDecoded(d); }).catch(function(){ playDing(); });
        return;
    }
    const p = SOUND_PREF||'ding';
    if (p==='coin') playCoin(); else if (p==='whoosh') playWhoosh(); else playDing();
}

// ═══ LIVE CONFIG ═══
function applyConfig(config) {
    if (!config) return;
    if (config.alertTheme) { const b=document.body; b.className=b.className.replace(/\btheme-\S+/g,'').trim(); if(config.alertTheme!=='default') b.classList.add('theme-'+config.alertTheme); }
    if (config.alertDuration) ALERT_DURATION = config.alertDuration;
    if (config.soundEnabled !== undefined) SOUND_ON = !!config.soundEnabled;
    if (config.notificationSound !== undefined && config.notificationSound !== SOUND_PREF) { SOUND_PREF=config.notificationSound; _customSoundBuffer=null; _customSoundUrl=null; }
}

// ═══ SSE — satu koneksi untuk semua widget ═══
let currentEventSource = null;
let sseHandlers = { onopen: null, donation: null, stats: null, ping: null, stream_error: null, onerror: null };

function connectSSE() {
    // Clean up existing connection and handlers
    if (currentEventSource) {
        if (sseHandlers.donation) currentEventSource.removeEventListener('donation', sseHandlers.donation);
        if (sseHandlers.stats) currentEventSource.removeEventListener('stats', sseHandlers.stats);
        if (sseHandlers.ping) currentEventSource.removeEventListener('ping', sseHandlers.ping);
        if (sseHandlers.stream_error) currentEventSource.removeEventListener('stream_error', sseHandlers.stream_error);
        currentEventSource.close();
        currentEventSource = null;
    }

    currentEventSource = new EventSource(buildSseUrl());

    sseHandlers.onopen = function() {
        statusEl.textContent = '● live';
        statusEl.style.color = 'rgba(34,211,160,.3)';
    };
    currentEventSource.onopen = sseHandlers.onopen;

    // Event: donasi baru → notifikasi alert
    sseHandlers.donation = function(e) {
        try {
            const d = JSON.parse(e.data);
            if (seenIds.has(d.seq ?? d.id)) return;
            seenIds.add(d.seq ?? d.id);
            saveLastKnownSeq(d.seq ?? null);
            if (WIDGETS_ACTIVE.notification) addToQueue(d);
        } catch(err) { console.error('SSE parse error:', err); }
    };
    currentEventSource.addEventListener('donation', sseHandlers.donation);

    // Event: stats → leaderboard + milestone update
    sseHandlers.stats = function(e) {
        try {
            const data = JSON.parse(e.data);
            if (data && data.config) applyConfig(data.config);
            if (WIDGETS_ACTIVE.leaderboard) applyLeaderboard(data, true);
            if (WIDGETS_ACTIVE.milestone)   applyMilestone(data);
        } catch(err) { console.error('SSE stats error:', err); }
    };
    currentEventSource.addEventListener('stats', sseHandlers.stats);

    sseHandlers.ping = function() {};
    currentEventSource.addEventListener('ping', sseHandlers.ping);

    sseHandlers.stream_error = function() {
        statusEl.textContent = '● error — reconnecting…';
        statusEl.style.color = 'rgba(249,115,22,.4)';
        if (currentEventSource) currentEventSource.close();
        setTimeout(connectSSE, 5000);
    };
    currentEventSource.addEventListener('stream_error', sseHandlers.stream_error);

    sseHandlers.onerror = function() {
        statusEl.textContent = '● reconnecting…';
        statusEl.style.color = 'rgba(249,115,22,.4)';
        if (currentEventSource) currentEventSource.close();
        setTimeout(connectSSE, 3000);
    };
    currentEventSource.onerror = sseHandlers.onerror;
}

// ═══ NOTIFICATION QUEUE ═══
const queue   = [];
let isShowing = false;
let _watchdog = null;

function addToQueue(d) { queue.push(d); if (!isShowing) processQueue(); }
function processQueue() {
    clearTimeout(_watchdog);
    if (queue.length === 0) { isShowing = false; return; }
    isShowing = true;
    showAlert(queue.shift());
}
function armWatchdog(dur) {
    clearTimeout(_watchdog);
    _watchdog = setTimeout(function() {
        const box = document.getElementById('alert-box');
        if (box) { box.classList.remove('visible','hiding'); }
        var ytf = document.getElementById('yt-iframe'); if(ytf) ytf.src='';
        isShowing = false; processQueue();
    }, dur + 2000);
}
function showAlert(donation) {
    const box = document.getElementById('alert-box');
    if (!box) return;
    const dur = ALERT_DURATION;

    document.getElementById('alert-avatar').textContent = donation.emoji || '🎉';
    document.getElementById('alert-donor').textContent  = donation.name;
    document.getElementById('alert-amount').textContent = formatRp(donation.amount);

    const msg    = donation.message || donation.msg || '';
    const msgEl  = document.getElementById('alert-message');
    const divEl  = document.getElementById('alert-divider');
    if(msgEl) msgEl.textContent = msg;
    if(divEl) divEl.style.display = msg ? '' : 'none';

    const ytUrl     = donation.ytUrl || donation.yt_url || null;
    const ytEnabled = donation.ytEnabled !== undefined ? donation.ytEnabled : donation.yt_enabled;
    const ytSection = document.getElementById('alert-yt');
    const ytIframe  = document.getElementById('yt-iframe');
    if (ytUrl && ytEnabled !== false) {
        const vid = extractYtId(ytUrl);
        if (vid && ytSection && ytIframe) { ytIframe.src='https://www.youtube.com/embed/'+vid+'?autoplay=1&mute=0'; ytSection.style.display='block'; }
        else if(ytSection) ytSection.style.display='none';
    } else {
        if(ytSection) ytSection.style.display='none';
        if(ytIframe)  ytIframe.src='';
    }

    box.classList.remove('visible','hiding');
    void box.offsetWidth;
    box.classList.add('visible');
    playAlertSound();

    const bar = document.getElementById('progress-bar');
    if(bar){ bar.style.transition='none'; bar.style.width='100%'; setTimeout(function(){ bar.style.transition='width '+dur+'ms linear'; bar.style.width='0%'; }, 60); }

    armWatchdog(dur + 450);
    setTimeout(function() {
        box.classList.add('hiding');
        setTimeout(function() {
            box.classList.remove('visible','hiding');
            if(ytIframe) ytIframe.src='';
            clearTimeout(_watchdog);
            processQueue();
        }, 450);
    }, dur);
}

// ═══ LEADERBOARD ═══
function applyLeaderboard(data, animate) {
    const lb     = data.leaderboard || [];
    const total  = data.total  || 0;
    const donors = data.donors || 0;
    const config = data.config || {};
    const title  = config.leaderboardTitle || 'Top Donatur';
    const titleEl = document.getElementById('lb-title');
    const subEl   = document.getElementById('lb-subtitle');
    const totalEl = document.getElementById('lb-grand-total');
    const listEl  = document.getElementById('lb-list');
    if (!listEl) return;

    if(titleEl) titleEl.textContent = title;
    if(subEl)   subEl.textContent   = donors + ' donatur \u2022 ' + formatRp(total) + ' total';
    if(totalEl) totalEl.textContent = formatRp(total);

    if (lb.length === 0) {
        listEl.innerHTML = '<div class="lb-empty">Belum ada donasi masuk</div>';
        return;
    }
    listEl.innerHTML = lb.map(function(d, i) {
        const rankClass = i < 3 ? 'rank-'+(i+1) : '';
        const medal     = i < 3 ? MEDALS[i] : null;
        const rankHtml  = medal ? '<div class="lb-rank">'+esc(medal)+'</div>' : '<div class="lb-rank num">'+(i+1)+'</div>';
        const newCls    = animate ? 'new' : '';
        const delay     = animate ? 'animation-delay:'+(i*55)+'ms' : '';
        return '<div class="lb-item '+rankClass+' '+newCls+'" style="'+delay+'">'+rankHtml+'<div class="lb-avatar">'+esc(d.emoji||'🎉')+'</div><div class="lb-info"><div class="lb-name">'+esc(d.name)+'</div><div class="lb-count">'+d.count+'x donasi</div></div><div class="lb-amount"><div class="lb-total">'+formatRp(d.total)+'</div></div></div>';
    }).join('');

    const widget = document.getElementById('lb-widget');
    if(widget) widget.classList.add('visible');
}

// ═══ MILESTONE ═══
let wasReached = false;
function applyMilestone(data) {
    const ms      = data.milestone || {};
    const current = ms.current || 0;
    const target  = ms.target  || 1000000;
    const title   = ms.title   || 'Target Stream Hari Ini';
    const reached = ms.reached || false;
    const pct     = target > 0 ? Math.min(100, Math.round(current / target * 100)) : 0;

    const titleEl = document.getElementById('ms-title');
    const currEl  = document.getElementById('ms-current');
    const tgtEl   = document.getElementById('ms-target');
    const pctEl   = document.getElementById('ms-pct');
    const barEl   = document.getElementById('ms-bar');
    const widget  = document.getElementById('ms-widget');
    if (!widget) return;

    if(titleEl) titleEl.textContent = title;
    if(currEl)  currEl.textContent  = formatRp(current);
    if(tgtEl)   tgtEl.textContent   = formatRp(target);
    if(pctEl)   pctEl.textContent   = pct + '%';
    if(barEl) setTimeout(function(){ barEl.style.width = pct+'%'; }, 80);

    if (reached && !wasReached) { widget.classList.add('reached'); wasReached = true; }
    else if (!reached) { widget.classList.remove('reached'); wasReached = false; }

    widget.classList.add('visible');
}

// ═══ INITIAL DATA LOAD ═══
async function loadInitial() {
    try {
        const res  = await fetch(STATS_URL);
        if (!res.ok) throw new Error('Server error: ' + res.status);
        const data = await res.json();
        if (WIDGETS_ACTIVE.leaderboard) applyLeaderboard(data, false);
        if (WIDGETS_ACTIVE.milestone)   applyMilestone(data);
    } catch(e) {
        console.warn('Canvas loadInitial failed, will retry in 10s:', e.message);
        setTimeout(loadInitial, 10000);
    }
}

// ═══ UTILS ═══
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
function esc(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

// ═══ START ═══
loadInitial();
connectSSE();
</script>
</body>
</html>
