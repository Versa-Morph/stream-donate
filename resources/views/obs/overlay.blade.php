<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>StreamDonate — OBS Alert Overlay</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Poppins:wght@400;500;600;700&family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <style>
        /* ─── BASE ─── */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            width: 1920px; height: 1080px;
            background: transparent !important;
            overflow: hidden;
            font-family: var(--font-family-body);
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
            /* ─ Typography ─ */
            --font-family-body:    'Inter', sans-serif;
            --font-family-display: 'Space Grotesk', sans-serif;
            --font-size-title:  17px;
            --font-size-amount: 24px;
            --font-size-msg:    13px;
            /* ─ Spacing ─ */
            --spacing-inner: 18px;
            /* ─ Style effects ─ */
            --blur-amount: 12px;
        }

        /* ─── THEME: minimal ─── */
        body.theme-minimal {
            --bg:       rgba(12,12,16,.95);
            --border:   rgba(255,255,255,.14);
            --accent:   #e0e0f0;
            --accent2:  #ffffff;
            --amount-c: #ffffff;
            --donor-c:  #ffffff;
            --top-line: linear-gradient(90deg,rgba(255,255,255,.5),rgba(255,255,255,.15));
            --prog-bar: linear-gradient(90deg,rgba(255,255,255,.7),rgba(255,255,255,.3));
            --shadow:   0 8px 32px rgba(0,0,0,.6), 0 0 0 1px rgba(255,255,255,.1);
            --radius:   12px;
        }

        /* ─── THEME: neon ─── */
        body.theme-neon {
            --bg:       rgba(2,4,18,.97);
            --border:   rgba(0,255,200,.22);
            --accent:   #00ffc8;
            --accent2:  #00e5ff;
            --amount-c: #00e5ff;
            --donor-c:  #00ffc8;
            --msg-c:    rgba(0,255,200,.6);
            --top-line: linear-gradient(90deg,#00ffc8,#00c8ff,#7c6cfc);
            --prog-bar: linear-gradient(90deg,#00ffc8,#00c8ff);
            --shadow:   0 8px 40px rgba(0,0,0,.85), 0 0 0 1px rgba(0,255,200,.12), 0 0 40px rgba(0,255,200,.1);
            --radius:   14px;
        }
        body.theme-neon .alert-donor { text-shadow: 0 0 18px rgba(0,255,200,.5); }
        body.theme-neon .alert-amount { text-shadow: 0 0 16px rgba(0,229,255,.45); }

        /* ─── THEME: fire ─── */
        body.theme-fire {
            --bg:       rgba(10,4,2,.97);
            --border:   rgba(249,115,22,.22);
            --accent:   #f97316;
            --accent2:  #fbbf24;
            --amount-c: #fbbf24;
            --donor-c:  #fef3c7;
            --msg-c:    rgba(254,243,199,.5);
            --top-line: linear-gradient(90deg,#f97316,#ef4444,#fbbf24);
            --prog-bar: linear-gradient(90deg,#f97316,#ef4444,#fbbf24);
            --shadow:   0 8px 40px rgba(0,0,0,.85), 0 0 0 1px rgba(249,115,22,.1), 0 0 50px rgba(239,68,68,.1);
            --radius:   16px;
        }
        body.theme-fire .alert-donor  { text-shadow: 0 0 24px rgba(251,191,36,.35); }
        body.theme-fire .alert-amount { text-shadow: 0 0 16px rgba(251,191,36,.4); }

        /* ─── THEME: ice ─── */
        body.theme-ice {
            --bg:       rgba(2,8,22,.96);
            --border:   rgba(147,210,255,.18);
            --accent:   #38bdf8;
            --accent2:  #818cf8;
            --amount-c: #38bdf8;
            --donor-c:  #e0f2fe;
            --msg-c:    rgba(224,242,254,.5);
            --top-line: linear-gradient(90deg,#38bdf8,#818cf8,#e0f2fe);
            --prog-bar: linear-gradient(90deg,#38bdf8,#818cf8);
            --shadow:   0 8px 40px rgba(0,0,0,.85), 0 0 0 1px rgba(147,210,255,.08), 0 0 40px rgba(56,189,248,.08);
            --radius:   18px;
        }
        body.theme-ice .alert-donor  { text-shadow: 0 0 18px rgba(56,189,248,.4); }
        body.theme-ice .alert-amount { text-shadow: 0 0 14px rgba(56,189,248,.35); }

        /* ─── AESTHETIC STYLES ─── */

        /* style-glass (default — translucent, subtle glow) */
        body.style-glass {
            --bg:     rgba(8,8,12,.82);
            --border: rgba(255,255,255,.12);
            --shadow: 0 8px 40px rgba(0,0,0,.6), 0 0 0 1px rgba(255,255,255,.06);
        }
        body.style-glass .alert-box {
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
        }

        /* style-solid — fully opaque, no blur */
        body.style-solid {
            --bg:     rgba(12,12,18,1);
            --border: rgba(255,255,255,.18);
            --shadow: 0 4px 24px rgba(0,0,0,.75), 0 0 0 2px rgba(255,255,255,.1);
        }
        body.style-solid .alert-box {
            backdrop-filter: none;
        }

        /* style-neon — neon border glow */
        body.style-neon {
            --bg:     rgba(4,4,10,.92);
            --border: var(--accent);
            --shadow: 0 0 0 1px var(--accent), 0 0 32px var(--accent), 0 8px 40px rgba(0,0,0,.9);
        }
        body.style-neon .alert-box {
            backdrop-filter: blur(0);
        }
        body.style-neon .alert-donor  { text-shadow: 0 0 18px var(--accent); }
        body.style-neon .alert-amount { text-shadow: 0 0 16px var(--accent2); }

        /* style-minimal — very clean, barely-there */
        body.style-minimal {
            --bg:     rgba(10,10,14,.78);
            --border: rgba(255,255,255,.07);
            --shadow: 0 2px 16px rgba(0,0,0,.5);
        }
        body.style-minimal .alert-box {
            backdrop-filter: blur(0);
        }
        body.style-minimal .alert-box::before { height: 1px; opacity: .5; }

        /* style-retro — bold opaque border */
        body.style-retro {
            --bg:     rgba(14,10,22,1);
            --border: var(--accent);
            --shadow: 4px 4px 0 var(--accent), 0 8px 32px rgba(0,0,0,.7);
        }
        body.style-retro .alert-box {
            border-width: 3px;
            border-radius: calc(var(--radius) * .75);
            backdrop-filter: blur(0);
        }

        /* style-frosted → Outlined: transparent bg + strong accent border */
        body.style-frosted {
            --bg:     rgba(8,8,16,.04);
            --border: var(--accent);
            --shadow: 0 4px 24px rgba(0,0,0,.6);
        }
        body.style-frosted .alert-box {
            border-width: 2px;
            box-shadow: 0 0 0 1px var(--accent), var(--shadow, 0 4px 24px rgba(0,0,0,.6));
        }

        /* ─── LAYOUT: CENTERED ─── */
        body.layout-centered .alert-inner {
            padding: var(--spacing-inner) 20px 0;
        }
        body.layout-centered .alert-header {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        body.layout-centered .alert-meta {
            text-align: center;
        }
        body.layout-centered .alert-badge {
            display: none;
        }
        body.layout-centered .alert-message {
            text-align: center;
        }

        /* ─── LAYOUT: SIDE ─── */
        body.layout-side .alert-header { display: none; }
        body.layout-side .alert-divider { display: none; }
        body.layout-side .alert-message { display: none; }
        body.layout-side .alert-side-body {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: var(--spacing-inner) 20px 0;
        }
        body.layout-side .alert-side-left {
            flex: 1;
            min-width: 0;
        }
        body.layout-side .alert-side-donor {
            font-size: var(--font-size-title); font-weight: 700;
            color: var(--donor-c);
            letter-spacing: -.3px; line-height: 1.2;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        body.layout-side .alert-side-badge {
            font-size: 8px; font-weight: 800; letter-spacing: 1.4px;
            text-transform: uppercase;
            color: var(--accent2); opacity: .7;
            margin-top: 4px;
        }
        body.layout-side .alert-side-msg {
            font-size: var(--font-size-msg);
            color: var(--msg-c);
            line-height: 1.55;
            margin-top: 6px;
            word-break: break-word;
            white-space: pre-wrap;
        }
        body.layout-side .alert-side-right {
            flex-shrink: 0;
            text-align: right;
        }
        body.layout-side .alert-side-amount {
            font-family: var(--font-family-display);
            font-size: 32px; font-weight: 800;
            color: var(--amount-c);
            letter-spacing: -.8px; line-height: 1;
        }


        @keyframes alertIn {
            from { transform: translateX(-50%) translateY(80px) scale(.97); opacity: 0; }
            to   { transform: translateX(-50%) translateY(0) scale(1); opacity: var(--card-opacity, 1); }
        }
        @keyframes alertOut {
            from { transform: translateX(-50%) translateY(0) scale(1); opacity: var(--card-opacity, 1); }
            to   { transform: translateX(-50%) translateY(60px) scale(.97); opacity: 0; }
        }
        @keyframes alertIn_minimal {
            from { transform: translateX(-50%) translateY(-16px); opacity: 0; }
            to   { transform: translateX(-50%) translateY(0); opacity: var(--card-opacity, 1); }
        }
        @keyframes alertOut_minimal {
            from { transform: translateX(-50%) translateY(0); opacity: var(--card-opacity, 1); }
            to   { transform: translateX(-50%) translateY(-16px); opacity: 0; }
        }
        @keyframes alertIn_neon {
            0%   { transform: translateX(-50%) scale(.88); opacity: 0; filter: blur(6px); }
            65%  { transform: translateX(-50%) scale(1.02); opacity: var(--card-opacity, 1); filter: blur(0); }
            100% { transform: translateX(-50%) scale(1); opacity: var(--card-opacity, 1); }
        }
        @keyframes alertOut_neon {
            from { transform: translateX(-50%) scale(1); opacity: var(--card-opacity, 1); }
            to   { transform: translateX(-50%) scale(.88); opacity: 0; filter: blur(6px); }
        }
        @keyframes alertIn_fire {
            0%   { transform: translateX(-50%) translateY(100px) scale(.92); opacity: 0; filter: blur(3px); }
            70%  { transform: translateX(-50%) translateY(-6px) scale(1.01); opacity: var(--card-opacity, 1); filter: blur(0); }
            100% { transform: translateX(-50%) translateY(0) scale(1); opacity: var(--card-opacity, 1); }
        }
        @keyframes alertOut_fire {
            from { transform: translateX(-50%) translateY(0) scale(1); opacity: var(--card-opacity, 1); }
            to   { transform: translateX(-50%) translateY(-50px) scale(.95); opacity: 0; filter: blur(3px); }
        }
        @keyframes alertIn_ice {
            0%   { transform: translateX(-50%) scale(.7); opacity: 0; filter: blur(5px) brightness(1.6); }
            65%  { transform: translateX(-50%) scale(1.03); opacity: var(--card-opacity, 1); filter: blur(0) brightness(1.05); }
            100% { transform: translateX(-50%) scale(1); opacity: var(--card-opacity, 1); filter: blur(0) brightness(1); }
        }
        @keyframes alertOut_ice {
            from { transform: translateX(-50%) scale(1); opacity: var(--card-opacity, 1); }
            to   { transform: translateX(-50%) scale(.7); opacity: 0; filter: blur(5px); }
        }

        /* ─── ALERT BOX ─── */
        .alert-box {
            position: fixed;
            bottom: 48px; left: 50%;
            transform: translateX(-50%) translateY(80px);
            width: 560px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            opacity: 0;
            overflow: hidden;
            pointer-events: none;
        }

        /* Top accent line */
        .alert-box::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 2px;
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

        /* ─── INNER LAYOUT ─── */
        .alert-inner {
            padding: var(--spacing-inner) 20px 0;
        }

        /* ─── HEADER ROW: avatar + donor/amount + badge ─── */
        .alert-header {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 14px;
        }

        .alert-meta { flex: 1; min-width: 0; }

        .alert-donor {
            font-size: var(--font-size-title); font-weight: 700;
            color: var(--donor-c);
            letter-spacing: -.3px; line-height: 1.2;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .alert-amount {
            font-family: var(--font-family-display);
            font-size: var(--font-size-amount); font-weight: 700; letter-spacing: -.6px;
            color: var(--amount-c); line-height: 1.1; margin-top: 2px;
        }

        /* DONASI MASUK pill — top-right corner */
        .alert-badge {
            flex-shrink: 0;
            font-size: 8px; font-weight: 800; letter-spacing: 1.4px;
            text-transform: uppercase;
            color: var(--accent2);
            opacity: .7;
            padding-top: 3px;
            white-space: nowrap;
        }

        /* ─── DIVIDER ─── */
        .alert-divider {
            height: 1px;
            background: rgba(255,255,255,.06);
            margin: 0 -20px;
        }

        /* ─── MESSAGE ─── */
        .alert-message {
            font-size: var(--font-size-msg); font-weight: 400;
            color: var(--msg-c);
            line-height: 1.65;
            padding: 12px 0 14px;
            /* Long messages wrap naturally — box grows upward via flex-column-reverse on parent */
            word-break: break-word;
            white-space: pre-wrap;
        }
        .alert-message:empty { display: none; }
        /* Hide divider when message is empty */
        .alert-message:empty + .alert-divider-after,
        .no-msg .alert-divider { display: none; }

        /* ─── YOUTUBE ─── */
        .alert-yt {
            margin-bottom: 0;
            border-radius: 10px; overflow: hidden;
            border: 1px solid rgba(255,255,255,.06);
            display: none;
            margin: 0 0 14px;
        }
        .alert-yt iframe { width: 100%; height: 192px; display: block; border: none; }

        /* ─── PROGRESS BAR ─── */
        .alert-progress {
            height: 2px;
            background: var(--bar-bg);
            margin: 0 -20px;  /* flush with box edges, accounting for inner padding */
        }
        .alert-progress-bar {
            height: 100%;
            background: var(--prog-bar);
            width: 100%;
        }

        /* ─── SSE STATUS ─── */
        #sse-status {
            position: fixed; top: 10px; right: 14px;
            font-size: 9px; font-weight: 600;
            color: rgba(255,255,255,.18); font-family: monospace;
            pointer-events: none; letter-spacing: .5px;
        }
    </style>
</head>
<body class="theme-{{ $streamer->getWidgetSettings()['alert']['preset'] ?? 'default' }} layout-{{ $streamer->getWidgetSettings()['alert']['layout'] ?? 'classic' }} style-{{ $streamer->getWidgetSettings()['alert']['style'] ?? 'glass' }}">

<style id="widget-custom-vars">
@php
    $ws = $streamer->getWidgetSettings()['alert'] ?? [];
    $preset = $ws['preset'] ?? 'default';
    // Font family mapping (always applied)
    $fontMap = [
        'inter'        => "'Inter', sans-serif",
        'space-grotesk'=> "'Space Grotesk', sans-serif",
        'plus-jakarta' => "'Plus Jakarta Sans', sans-serif",
        'poppins'      => "'Poppins', sans-serif",
        'nunito'       => "'Nunito', sans-serif",
    ];
    $fontFamily = $fontMap[$ws['font_family'] ?? 'inter'] ?? "'Inter', sans-serif";
    // Spacing mapping: 1=compact(12px), 2=default(18px), 3=spacious(26px)
    $spacingMap = ['1' => '12', '2' => '18', '3' => '26'];
    $spacingVal = $spacingMap[$ws['spacing'] ?? '2'] ?? '18';
    // Always-applied vars (typography, spacing, blur)
    $alwaysVars = [];
    $alwaysVars[] = '--font-family-body: '    . $fontFamily . ';';
    $alwaysVars[] = '--font-family-display: ' . $fontFamily . ';';
    $alwaysVars[] = '--font-size-title: '     . (int)($ws['font_size_title']  ?? 17) . 'px;';
    $alwaysVars[] = '--font-size-amount: '    . (int)($ws['font_size_amount'] ?? 24) . 'px;';
    $alwaysVars[] = '--font-size-msg: '       . (int)($ws['font_size_msg']    ?? 13) . 'px;';
    $alwaysVars[] = '--spacing-inner: '       . $spacingVal . 'px;';
    $alwaysVars[] = '--blur-amount: '         . (int)($ws['blur_amount'] ?? 12) . 'px;';
    $alwaysVars[] = '--card-opacity: '        . round((int)($ws['card_opacity'] ?? 96) / 100, 2) . ';';
    // Color vars — selalu diinjeksi dari widget_settings yang tersimpan.
    // <style id="widget-custom-vars"> muncul setelah <head> stylesheet, sehingga
    // secara cascade otomatis menimpa rules body.theme-* dari stylesheet utama.
    // Ini memastikan warna yang dikustomisasi user (preset apapun) selalu diterapkan.
    $colorVars = [];
    if (!empty($ws['bg']))           $colorVars[] = '--bg: '       . $ws['bg']           . ';';
    if (!empty($ws['border']))       $colorVars[] = '--border: '   . $ws['border']       . ';';
    if (!empty($ws['accent']))       $colorVars[] = '--accent: '   . $ws['accent']       . ';';
    if (!empty($ws['accent2']))      $colorVars[] = '--accent2: '  . $ws['accent2']      . ';';
    if (!empty($ws['amount_color'])) $colorVars[] = '--amount-c: ' . $ws['amount_color'] . ';';
    if (!empty($ws['donor_color']))  $colorVars[] = '--donor-c: '  . $ws['donor_color']  . ';';
    if (!empty($ws['top_line']))     $colorVars[] = '--top-line: ' . $ws['top_line']     . ';';
    if (!empty($ws['prog_bar']))     $colorVars[] = '--prog-bar: ' . $ws['prog_bar']     . ';';
    if (!empty($ws['radius']))       $colorVars[] = '--radius: '   . $ws['radius']       . 'px;';
    // Width & position always applied
    $alertWidth = !empty($ws['width'])      ? (int)$ws['width']      : 560;
    $posX       = $ws['position_x'] ?? 'center';
    $posY       = $ws['position_y'] ?? 'bottom';
    $layout     = $ws['layout'] ?? 'classic';
@endphp
:root {
    {!! implode("\n    ", $alwaysVars) !!}
}
@if(!empty($colorVars))
body {
    {!! implode("\n    ", $colorVars) !!}
}
@endif
.alert-box {
    width: {{ $alertWidth }}px;
@if($posX === 'left')
    left: 40px; transform: translateY(80px); transform-origin: left bottom;
@elseif($posX === 'right')
    left: auto; right: 40px; transform: translateY(80px); transform-origin: right bottom;
@else
    left: 50%; transform: translateX(-50%) translateY(80px);
@endif
@if($posY === 'top')
    bottom: auto; top: 48px;
@else
    bottom: 48px; top: auto;
@endif
{{-- banner layout removed; side uses the same .alert-box, so no display:none needed --}}
}
.alert-box.visible {
@if($posX === 'left')
    animation: alertIn_pos .48s cubic-bezier(.34,1.56,.64,1) forwards;
@elseif($posX === 'right')
    animation: alertIn_pos .48s cubic-bezier(.34,1.56,.64,1) forwards;
@else
    animation: alertIn .48s cubic-bezier(.34,1.56,.64,1) forwards;
@endif
}
@if($posX === 'left' || $posX === 'right')
.alert-box.hiding {
    animation: alertOut_pos .34s cubic-bezier(.4,0,1,1) forwards;
}
@endif
@keyframes alertIn_pos {
    from { transform: translateY(80px) scale(.97); opacity: 0; }
    to   { transform: translateY(0) scale(1); opacity: 1; }
}
@keyframes alertOut_pos {
    from { transform: translateY(0) scale(1); opacity: 1; }
    to   { transform: translateY(60px) scale(.97); opacity: 0; }
}
</style>

<div id="sse-status">connecting…</div>

<div class="alert-box" id="alert-box">
    <div class="alert-inner">
        <div class="alert-header">
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
    {{-- Side layout body (hidden unless layout-side) --}}
    <div class="alert-side-body" style="display:none" id="alert-side-body">
        <div class="alert-side-left">
            <div class="alert-side-donor"  id="side-donor">Nama Donatur</div>
            <div class="alert-side-badge">DONASI MASUK</div>
            <div class="alert-side-msg"    id="side-msg"></div>
        </div>
        <div class="alert-side-right">
            <div class="alert-side-amount" id="side-amount">Rp 0</div>
        </div>
    </div>
    <div class="alert-progress">
        <div class="alert-progress-bar" id="progress-bar"></div>
    </div>
</div>

<script>
const SSE_BASE_URL   = '{{ url("/" . $streamer->slug . "/sse") }}?key={{ $apiKey }}';
const ASSET_STORAGE  = '{{ asset("storage") }}';

// ── Mutable config — updated live via SSE stats.config ──
let SOUND_ON       = {{ $streamer->sound_enabled ? 'true' : 'false' }};
let SOUND_PREF     = {!! json_encode($streamer->notification_sound ?? 'ding') !!};
let ALERT_DURATION = {{ (int) ($streamer->alert_duration ?? 8000) }};
let DURATION_TIERS = {!! json_encode($streamer->getAlertDurationTiers()) !!};
let MAX_DURATION   = {{ (int) min((int)($streamer->alert_max_duration ?? 30), 120) }};

// ── Kunci localStorage untuk menyimpan posisi SSE terakhir ──
// Key di-scope per slug agar tidak bentrok antar streamer di browser yang sama
const LS_SEQ_KEY = 'ssd_last_seq_{{ $streamer->slug }}';

/**
 * Baca lastKnownSeq dari localStorage.
 * Dikembalikan sebagai integer atau null jika belum ada / tidak valid.
 */
function getLastKnownSeq() {
    try {
        const raw = localStorage.getItem(LS_SEQ_KEY);
        if (raw === null) return null;
        const n = parseInt(raw, 10);
        return isNaN(n) ? null : n;
    } catch (e) {
        return null;
    }
}

/**
 * Simpan posisi SSE terakhir ke localStorage.
 * Dipanggil setiap kali event: donation diterima.
 */
function saveLastKnownSeq(seq) {
    try {
        if (seq != null) localStorage.setItem(LS_SEQ_KEY, String(seq));
    } catch (e) { /* localStorage tidak tersedia (private mode / full) — abaikan */ }
}

/**
 * Bangun SSE URL dengan menyertakan last_seq jika tersedia.
 * Server akan melanjutkan dari titik ini sehingga alert yang
 * masuk selama OBS offline / reload akan tetap diputar ulang
 * selama masih dalam TTL (15 menit).
 */
function buildSseUrl() {
    const seq = getLastKnownSeq();
    return seq !== null ? SSE_BASE_URL + '&last_seq=' + seq : SSE_BASE_URL;
}

function getSoundUrl() {
    const synth = ['ding','coin','whoosh','chime','pop','tada','woosh_light','blip','sparkle','fanfare'];
    return (SOUND_PREF && !synth.includes(SOUND_PREF))
        ? ASSET_STORAGE + '/' + SOUND_PREF
        : null;
}

const statusEl = document.getElementById('sse-status');
const seenIds  = new Set();

// ─── Web Audio API ───
let _audioCtx = null;
function getAudioCtx() {
    if (!_audioCtx) _audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    if (_audioCtx.state === 'suspended') _audioCtx.resume();
    return _audioCtx;
}

let _customSoundUrl    = null;
let _customSoundBuffer = null;

function playDing() {
    const ctx = getAudioCtx();
    const osc = ctx.createOscillator(), gain = ctx.createGain();
    osc.connect(gain); gain.connect(ctx.destination);
    osc.type = 'sine';
    osc.frequency.setValueAtTime(880, ctx.currentTime);
    osc.frequency.exponentialRampToValueAtTime(1320, ctx.currentTime + 0.05);
    osc.frequency.exponentialRampToValueAtTime(660, ctx.currentTime + 0.4);
    gain.gain.setValueAtTime(0.6, ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.6);
    osc.start(ctx.currentTime); osc.stop(ctx.currentTime + 0.6);
}
function playCoin() {
    const ctx = getAudioCtx();
    [0, 0.12].forEach(function(delay) {
        const osc = ctx.createOscillator(), gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.type = 'square';
        osc.frequency.setValueAtTime(988, ctx.currentTime + delay);
        osc.frequency.setValueAtTime(1319, ctx.currentTime + delay + 0.07);
        gain.gain.setValueAtTime(0.35, ctx.currentTime + delay);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + delay + 0.3);
        osc.start(ctx.currentTime + delay); osc.stop(ctx.currentTime + delay + 0.3);
    });
}
function playWhoosh() {
    const ctx = getAudioCtx();
    const bufSize = ctx.sampleRate * 0.6;
    const buf = ctx.createBuffer(1, bufSize, ctx.sampleRate);
    const data = buf.getChannelData(0);
    for (let i = 0; i < bufSize; i++) data[i] = (Math.random() * 2 - 1);
    const source = ctx.createBufferSource(); source.buffer = buf;
    const filter = ctx.createBiquadFilter();
    filter.type = 'bandpass';
    filter.frequency.setValueAtTime(400, ctx.currentTime);
    filter.frequency.exponentialRampToValueAtTime(2000, ctx.currentTime + 0.2);
    filter.frequency.exponentialRampToValueAtTime(200, ctx.currentTime + 0.6);
    filter.Q.value = 0.8;
    const gain = ctx.createGain();
    gain.gain.setValueAtTime(0, ctx.currentTime);
    gain.gain.linearRampToValueAtTime(0.5, ctx.currentTime + 0.05);
    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.6);
    source.connect(filter); filter.connect(gain); gain.connect(ctx.destination);
    source.start(ctx.currentTime); source.stop(ctx.currentTime + 0.6);
}
function playChime() {
    const ctx = getAudioCtx();
    const notes = [1047, 1319, 1568, 2093];
    notes.forEach(function(freq, i) {
        const delay = i * 0.12;
        const osc = ctx.createOscillator(), gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.type = 'sine';
        osc.frequency.setValueAtTime(freq, ctx.currentTime + delay);
        gain.gain.setValueAtTime(0.4, ctx.currentTime + delay);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + delay + 0.7);
        osc.start(ctx.currentTime + delay); osc.stop(ctx.currentTime + delay + 0.8);
    });
}
function playPop() {
    const ctx = getAudioCtx();
    const osc = ctx.createOscillator(), gain = ctx.createGain();
    osc.connect(gain); gain.connect(ctx.destination);
    osc.type = 'sine';
    osc.frequency.setValueAtTime(400, ctx.currentTime);
    osc.frequency.exponentialRampToValueAtTime(80, ctx.currentTime + 0.08);
    gain.gain.setValueAtTime(0.7, ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.12);
    osc.start(ctx.currentTime); osc.stop(ctx.currentTime + 0.14);
}
function playTada() {
    const ctx = getAudioCtx();
    const notes = [523, 659, 784, 1047, 1319];
    notes.forEach(function(freq, i) {
        const delay = i * 0.07;
        const osc = ctx.createOscillator(), gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.type = 'triangle';
        osc.frequency.setValueAtTime(freq, ctx.currentTime + delay);
        gain.gain.setValueAtTime(0.35, ctx.currentTime + delay);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + delay + 0.35);
        osc.start(ctx.currentTime + delay); osc.stop(ctx.currentTime + delay + 0.4);
    });
}
function playWooshLight() {
    const ctx = getAudioCtx();
    const bufSize = ctx.sampleRate * 0.35;
    const buf = ctx.createBuffer(1, bufSize, ctx.sampleRate);
    const data = buf.getChannelData(0);
    for (let i = 0; i < bufSize; i++) data[i] = (Math.random() * 2 - 1);
    const source = ctx.createBufferSource(); source.buffer = buf;
    const filter = ctx.createBiquadFilter();
    filter.type = 'highpass';
    filter.frequency.setValueAtTime(1200, ctx.currentTime);
    filter.frequency.exponentialRampToValueAtTime(4000, ctx.currentTime + 0.15);
    filter.frequency.exponentialRampToValueAtTime(800, ctx.currentTime + 0.35);
    const gain = ctx.createGain();
    gain.gain.setValueAtTime(0, ctx.currentTime);
    gain.gain.linearRampToValueAtTime(0.3, ctx.currentTime + 0.04);
    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
    source.connect(filter); filter.connect(gain); gain.connect(ctx.destination);
    source.start(ctx.currentTime); source.stop(ctx.currentTime + 0.4);
}
function playBlip() {
    const ctx = getAudioCtx();
    const osc = ctx.createOscillator(), gain = ctx.createGain();
    osc.connect(gain); gain.connect(ctx.destination);
    osc.type = 'square';
    osc.frequency.setValueAtTime(1200, ctx.currentTime);
    osc.frequency.setValueAtTime(1600, ctx.currentTime + 0.05);
    gain.gain.setValueAtTime(0.25, ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.15);
    osc.start(ctx.currentTime); osc.stop(ctx.currentTime + 0.18);
}
function playSparkle() {
    const ctx = getAudioCtx();
    for (let i = 0; i < 5; i++) {
        const delay = i * 0.06;
        const freq  = 1400 + Math.random() * 1200;
        const osc   = ctx.createOscillator(), gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.type = 'sine';
        osc.frequency.setValueAtTime(freq, ctx.currentTime + delay);
        gain.gain.setValueAtTime(0.2, ctx.currentTime + delay);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + delay + 0.25);
        osc.start(ctx.currentTime + delay); osc.stop(ctx.currentTime + delay + 0.3);
    }
}
function playFanfare() {
    const ctx = getAudioCtx();
    const sequence = [523, 659, 784, 659, 1047];
    const timing   = [0, 0.1, 0.2, 0.32, 0.44];
    sequence.forEach(function(freq, i) {
        const delay = timing[i];
        const osc   = ctx.createOscillator(), gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.type = 'sawtooth';
        osc.frequency.setValueAtTime(freq, ctx.currentTime + delay);
        gain.gain.setValueAtTime(0.28, ctx.currentTime + delay);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + delay + 0.3);
        osc.start(ctx.currentTime + delay); osc.stop(ctx.currentTime + delay + 0.35);
    });
}
function _playDecodedBuffer(decoded) {
    const ctx = getAudioCtx();
    const src = ctx.createBufferSource(), gain = ctx.createGain();
    src.buffer = decoded; src.connect(gain); gain.connect(ctx.destination);
    gain.gain.setValueAtTime(0.9, ctx.currentTime);
    src.start(ctx.currentTime);
}
function playAlertSound() {
    if (!SOUND_ON) return;
    const soundUrl = getSoundUrl();
    if (soundUrl) {
        if (_customSoundBuffer && _customSoundUrl === soundUrl) {
            _playDecodedBuffer(_customSoundBuffer); return;
        }
        const ctx = getAudioCtx();
        fetch(soundUrl)
            .then(function(r) { return r.arrayBuffer(); })
            .then(function(buf) { return ctx.decodeAudioData(buf); })
            .then(function(decoded) {
                _customSoundUrl = soundUrl; _customSoundBuffer = decoded;
                _playDecodedBuffer(decoded);
            }).catch(function() { playDing(); });
        return;
    }
    const p = SOUND_PREF || 'ding';
    if      (p === 'coin')        playCoin();
    else if (p === 'whoosh')      playWhoosh();
    else if (p === 'chime')       playChime();
    else if (p === 'pop')         playPop();
    else if (p === 'tada')        playTada();
    else if (p === 'woosh_light') playWooshLight();
    else if (p === 'blip')        playBlip();
    else if (p === 'sparkle')     playSparkle();
    else if (p === 'fanfare')     playFanfare();
    else                          playDing();
}

/**
 * Hitung durasi alert (ms) berdasarkan jumlah donasi.
 * Loop tiers dari yang terbesar ke terkecil (descending by `from`).
 * Return durasi detik × 1000. Fallback ke ALERT_DURATION.
 */
function getDurationForAmount(amount) {
    if (!DURATION_TIERS || !DURATION_TIERS.length) return ALERT_DURATION;
    const sorted = DURATION_TIERS.slice().sort(function(a, b) { return b.from - a.from; });
    for (let i = 0; i < sorted.length; i++) {
        if (amount >= sorted[i].from) return sorted[i].duration * 1000;
    }
    return ALERT_DURATION;
}

// ─── Live config dari SSE stats.config ───
function applyConfig(config) {
    if (!config) return;

    // ── Theme class (legacy alert_theme column) ──
    if (config.alertTheme) {
        const body = document.body;
        body.className = body.className.replace(/\btheme-\S+/g, '').trim();
        if (config.alertTheme !== 'default') body.classList.add('theme-' + config.alertTheme);
    }

    // ── Alert colors dari widget_settings (live update tanpa reload) ──
    if (config.alertColors) {
        const c    = config.alertColors;
        const body = document.body;

        // Terapkan CSS vars langsung ke body (menimpa stylesheet theme-* via specificity/cascade)
        if (c.bg)           body.style.setProperty('--bg',       c.bg);
        if (c.border)       body.style.setProperty('--border',   c.border);
        if (c.accent)       body.style.setProperty('--accent',   c.accent);
        if (c.accent2)      body.style.setProperty('--accent2',  c.accent2);
        if (c.amount_color) body.style.setProperty('--amount-c', c.amount_color);
        if (c.donor_color)  body.style.setProperty('--donor-c',  c.donor_color);
        if (c.top_line)     body.style.setProperty('--top-line', c.top_line);
        if (c.prog_bar)     body.style.setProperty('--prog-bar', c.prog_bar);
        if (c.radius)       body.style.setProperty('--radius',   c.radius + 'px');

        // Layout & style class (Gaya)
        if (c.layout) {
            body.className = body.className.replace(/\blayout-\S+/g, '').trim();
            body.classList.add('layout-' + c.layout);
        }
        if (c.style) {
            body.className = body.className.replace(/\bstyle-\S+/g, '').trim();
            body.classList.add('style-' + c.style);
        }

        // Typography vars
        const fontMap = {
            'inter':         "'Inter', sans-serif",
            'space-grotesk': "'Space Grotesk', sans-serif",
            'plus-jakarta':  "'Plus Jakarta Sans', sans-serif",
            'poppins':       "'Poppins', sans-serif",
            'nunito':        "'Nunito', sans-serif",
        };
        const spacingMap = {'1': '12px', '2': '18px', '3': '26px'};
        if (c.font_family) {
            const ff = fontMap[c.font_family] || "'Inter', sans-serif";
            body.style.setProperty('--font-family-body',    ff);
            body.style.setProperty('--font-family-display', ff);
        }
        if (c.font_size_title)  body.style.setProperty('--font-size-title',  c.font_size_title  + 'px');
        if (c.font_size_amount) body.style.setProperty('--font-size-amount', c.font_size_amount + 'px');
        if (c.font_size_msg)    body.style.setProperty('--font-size-msg',    c.font_size_msg    + 'px');
        if (c.spacing)          body.style.setProperty('--spacing-inner',    spacingMap[String(c.spacing)] || '18px');
        if (c.blur_amount)      body.style.setProperty('--blur-amount',      c.blur_amount      + 'px');
        if (c.card_opacity !== undefined) body.style.setProperty('--card-opacity', (parseInt(c.card_opacity, 10) / 100).toFixed(2));
    }

    if (config.alertDuration) ALERT_DURATION = config.alertDuration;
    if (config.alertDurationTiers && Array.isArray(config.alertDurationTiers)) DURATION_TIERS = config.alertDurationTiers;
    if (config.alertMaxDuration) MAX_DURATION = config.alertMaxDuration;
    if (config.soundEnabled !== undefined) SOUND_ON = !!config.soundEnabled;
    if (config.notificationSound !== undefined && config.notificationSound !== SOUND_PREF) {
        SOUND_PREF = config.notificationSound;
        _customSoundBuffer = null; _customSoundUrl = null;
    }
}

// ─── SSE ───
let currentEventSource = null;
let sseHandlers = {
    onopen: null,
    donation: null,
    stats: null,
    ping: null,
    stream_error: null,
    onerror: null
};

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
        statusEl.style.color = 'rgba(34,211,160,.4)';
    };
    currentEventSource.onopen = sseHandlers.onopen;

    sseHandlers.donation = function(e) {
        try {
            const d = JSON.parse(e.data);
            if (seenIds.has(d.seq ?? d.id)) return;
            seenIds.add(d.seq ?? d.id);
            // Simpan posisi terakhir ke localStorage untuk recovery setelah reload/reconnect
            saveLastKnownSeq(d.seq ?? null);
            addToQueue(d);
        } catch(err) { console.error('SSE parse error:', err); }
    };
    currentEventSource.addEventListener('donation', sseHandlers.donation);

    sseHandlers.stats = function(e) {
        try {
            const parsed = JSON.parse(e.data);
            if (parsed && parsed.config) applyConfig(parsed.config);
        } catch(err) { console.error('SSE stats error:', err); }
    };
    currentEventSource.addEventListener('stats', sseHandlers.stats);

    sseHandlers.ping = function() {};
    currentEventSource.addEventListener('ping', sseHandlers.ping);

    sseHandlers.stream_error = function(e) {
        // Server mengirim event ini sebelum menutup koneksi karena error internal
        statusEl.textContent = '● error — reconnecting…';
        statusEl.style.color = 'rgba(249,115,22,.4)';
        if (currentEventSource) currentEventSource.close();
        // Reconnect dengan last_seq dari localStorage agar tidak melewatkan alert
        setTimeout(connectSSE, 5000);
    };
    currentEventSource.addEventListener('stream_error', sseHandlers.stream_error);

    sseHandlers.onerror = function() {
        statusEl.textContent = '● reconnecting…';
        statusEl.style.color = 'rgba(249,115,22,.4)';
        if (currentEventSource) currentEventSource.close();
        // lastKnownSeq sudah tersimpan di localStorage — buildSseUrl() akan membacanya
        // sehingga alert yang masuk selama disconnect tetap akan diputar ulang
        setTimeout(connectSSE, 3000);
    };
    currentEventSource.onerror = sseHandlers.onerror;
}
connectSSE();

// ─── Queue ───
const queue   = [];
let isShowing = false;
let _watchdog = null;

function addToQueue(donation) {
    queue.push(donation);
    if (!isShowing) processQueue();
}
function processQueue() {
    clearTimeout(_watchdog);
    if (queue.length === 0) { isShowing = false; return; }
    isShowing = true;
    showAlert(queue.shift());
}
function armWatchdog(duration) {
    clearTimeout(_watchdog);
    _watchdog = setTimeout(function() {
        const box = document.getElementById('alert-box');
        box.classList.remove('visible', 'hiding');
        document.getElementById('yt-iframe').src = '';
        isShowing = false;
        processQueue();
    }, duration + 2000);
}

function showAlert(donation) {
    const box      = document.getElementById('alert-box');
    const divider  = document.getElementById('alert-divider');
    const msgEl    = document.getElementById('alert-message');
    const ytSection = document.getElementById('alert-yt');
    const duration  = getDurationForAmount(donation.amount || 0);
    const isSide    = document.body.classList.contains('layout-side');

    const emoji  = donation.emoji || '🎉';
    const name   = donation.name;
    const amount = formatRp(donation.amount);
    const msg    = donation.message || donation.msg || '';

    // Populate classic/centered elements
    document.getElementById('alert-donor').textContent  = name;
    document.getElementById('alert-amount').textContent = amount;
    msgEl.textContent = msg;
    divider.style.display = msg ? '' : 'none';

    // Populate side elements
    document.getElementById('side-donor').textContent  = name;
    document.getElementById('side-amount').textContent = amount;
    document.getElementById('side-msg').textContent    = msg;
    document.getElementById('alert-side-body').style.display = isSide ? '' : 'none';

    const ytUrl     = donation.ytUrl     || donation.yt_url     || null;
    const ytEnabled = donation.ytEnabled !== undefined ? donation.ytEnabled : donation.yt_enabled;
    if (!isSide && ytUrl && ytEnabled !== false) {
        const vid = extractYtId(ytUrl);
        if (vid) {
            document.getElementById('yt-iframe').src = 'https://www.youtube.com/embed/' + vid + '?autoplay=1&mute=0';
            ytSection.style.display = 'block';
        } else { ytSection.style.display = 'none'; }
    } else {
        ytSection.style.display = 'none';
        document.getElementById('yt-iframe').src = '';
    }

    box.classList.remove('visible', 'hiding');
    void box.offsetWidth;
    box.classList.add('visible');
    playAlertSound();

    const bar = document.getElementById('progress-bar');
    bar.style.transition = 'none'; bar.style.width = '100%';
    setTimeout(function() {
        bar.style.transition = 'width ' + duration + 'ms linear';
        bar.style.width = '0%';
    }, 60);

    armWatchdog(duration + 450);

    setTimeout(function() {
        box.classList.add('hiding');
        setTimeout(function() {
            box.classList.remove('visible', 'hiding');
            document.getElementById('yt-iframe').src = '';
            clearTimeout(_watchdog);
            processQueue();
        }, 450);
    }, duration);
}

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
</script>
</body>
</html>
