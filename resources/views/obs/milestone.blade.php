<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>StreamDonate — Milestone Overlay</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet" />
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            width: 1920px;
            height: 1080px;
            background: transparent !important;
            overflow: hidden;
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        :root {
            --brand:   #7c6cfc;
            --brand2:  #a99dff;
            --orange:  #f97316;
            --green:   #22d3a0;
            --yellow:  #fbbf24;
            --purple:  #a855f7;
            --text:    #f1f1f6;
            --text-2:  rgba(241,241,246,.55);
            --text-3:  rgba(241,241,246,.35);
            --surface: rgba(8,8,12,.96);
            --border:  rgba(255,255,255,.09);
            --border2: rgba(124,108,252,.2);
        }

        /* ─── PANEL ─── */
        #milestone-panel {
            position: fixed;
            bottom: 40px;
            left: 40px;
            width: 340px;
            pointer-events: none;
            opacity: 0;
            transform: translateY(20px);
            transition: opacity .5s ease, transform .55s cubic-bezier(.34, 1.3, .64, 1);
        }
        #milestone-panel.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ─── CARD ─── */
        .ms-wrap {
            background: var(--surface);
            border: 1px solid var(--border2);
            border-radius: var(--radius-ms, 16px);
            overflow: hidden;
            box-shadow:
                0 8px 40px rgba(0,0,0,.7),
                inset 0 0 0 1px rgba(255,255,255,.04);
            position: relative;
        }

        /* Top accent bar */
        .ms-wrap::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--brand), var(--purple), var(--green));
        }

        /* ─── INNER CONTENT ─── */
        .ms-inner {
            padding: 16px 18px 18px;
        }

        /* ─── BADGE ─── */
        .ms-badge {
            display: inline-flex; align-items: center; gap: 5px;
            background: rgba(124,108,252,.1);
            border: 1px solid rgba(124,108,252,.22);
            border-radius: 20px;
            padding: 3px 9px;
            font-size: 9px; font-weight: 800; letter-spacing: 1.5px;
            color: var(--brand2);
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        /* ─── TITLE ─── */
        .ms-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 16px; font-weight: 700; letter-spacing: -.3px;
            color: var(--text); line-height: 1.2;
            margin-bottom: 12px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }

        /* ─── AMOUNTS ROW ─── */
        .ms-amounts {
            display: flex; align-items: baseline; gap: 6px;
            margin-bottom: 10px;
        }
        .ms-current {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 24px; font-weight: 700; letter-spacing: -.7px;
            color: var(--orange); line-height: 1;
        }
        .ms-sep {
            font-size: 14px; color: var(--text-3); font-weight: 400;
        }
        .ms-target {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 16px; font-weight: 600; letter-spacing: -.3px;
            color: var(--text-2); line-height: 1;
        }
        .ms-pct {
            margin-left: auto;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 15px; font-weight: 700; letter-spacing: -.3px;
            color: var(--brand2); line-height: 1;
        }
        .ms-reached-label {
            display: none;
            margin-left: auto;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 13px; font-weight: 700; letter-spacing: -.2px;
            color: var(--green);
        }

        /* ─── PROGRESS TRACK ─── */
        .ms-track {
            height: 8px;
            background: rgba(255,255,255,.06);
            border-radius: 4px;
            overflow: hidden;
            position: relative;
        }

        .ms-bar {
            height: 100%;
            width: 0%;
            border-radius: 4px;
            background: linear-gradient(90deg, var(--brand), var(--purple), var(--orange));
            background-size: 200% 100%;
            transition: width 1.2s cubic-bezier(.4, 0, .2, 1);
            position: relative;
        }

        /* Shimmer */
        .ms-bar::after {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(90deg,
                transparent 0%, rgba(255,255,255,.22) 50%, transparent 100%);
            background-size: 200% 100%;
            animation: shimmer 2.8s ease-in-out infinite;
        }
        @keyframes shimmer {
            0%   { background-position: -200% 0; }
            100% { background-position:  200% 0; }
        }

        /* ─── REACHED STATE ─── */
        #milestone-panel.reached .ms-bar {
            background: linear-gradient(90deg, var(--green), #00e5b0, var(--brand2));
        }
        #milestone-panel.reached .ms-bar::after {
            animation: shimmer 1.2s ease-in-out infinite;
        }
        #milestone-panel.reached .ms-wrap {
            border-color: rgba(34,211,160,.28);
        }
        #milestone-panel.reached .ms-wrap::before {
            background: linear-gradient(90deg, var(--green), var(--brand2), var(--green));
        }

        @keyframes reachedGlow {
            0%,100% { box-shadow: 0 8px 40px rgba(0,0,0,.7), 0 0 0 1px rgba(255,255,255,.04), 0 0 30px rgba(34,211,160,.1); }
            50%     { box-shadow: 0 8px 40px rgba(0,0,0,.7), 0 0 0 1px rgba(255,255,255,.04), 0 0 60px rgba(34,211,160,.25), 0 0 20px rgba(124,108,252,.12); }
        }
        #milestone-panel.reached .ms-wrap {
            animation: reachedGlow 2s ease-in-out infinite;
        }
        #milestone-panel.reached .ms-pct { display: none; }
        #milestone-panel.reached .ms-reached-label { display: inline; }

        /* ─── THEMES ─── */
        body.theme-neon {
            --surface: rgba(2,4,18,.97);
            --border2: rgba(0,255,200,.22);
            --brand:   #00ffc8;
            --brand2:  #00e5ff;
            --orange:  #00ffc8;
            --green:   #00e5ff;
        }
        body.theme-fire {
            --surface: rgba(10,4,2,.97);
            --border2: rgba(249,115,22,.22);
            --brand:   #f97316;
            --brand2:  #fbbf24;
            --orange:  #f97316;
            --green:   #fbbf24;
        }
        body.theme-ice {
            --surface: rgba(2,8,22,.96);
            --border2: rgba(147,210,255,.18);
            --brand:   #38bdf8;
            --brand2:  #818cf8;
            --orange:  #38bdf8;
            --green:   #818cf8;
        }
        body.theme-minimal {
            --surface: rgba(12,12,16,.95);
            --border2: rgba(255,255,255,.14);
            --brand:   #e0e0f0;
            --brand2:  #ffffff;
            --orange:  #e0e0f0;
            --green:   #ffffff;
        }

        /* ─── SSE status ─── */
        #sse-status {
            position: fixed; bottom: 8px; left: 10px;
            font-size: 9px; color: rgba(255,255,255,.18);
            font-family: monospace; pointer-events: none;
        }
    </style>
</head>
<body class="@php
    $ws = $streamer->getWidgetSettings()['milestone'] ?? [];
    $preset = $ws['preset'] ?? 'default';
    $themeMap = ['neon'=>'theme-neon','fire'=>'theme-fire','ice'=>'theme-ice','minimal'=>'theme-minimal'];
    echo $themeMap[$preset] ?? '';
@endphp">

<style id="widget-custom-vars">
@php
    $ws = $streamer->getWidgetSettings()['milestone'] ?? [];
    $preset = $ws['preset'] ?? 'default';
    $vars = [];
    if ($preset === 'custom'):
        if (!empty($ws['surface'])) $vars[] = '--surface: ' . $ws['surface'] . ';';
        if (!empty($ws['border']))  $vars[] = '--border2: ' . $ws['border']  . ';';
        if (!empty($ws['brand']))   $vars[] = '--brand: '   . $ws['brand']   . ';';
        if (!empty($ws['brand2']))  $vars[] = '--brand2: '  . $ws['brand2']  . ';';
        if (!empty($ws['orange']))  $vars[] = '--orange: '  . $ws['orange']  . ';';
        if (!empty($ws['green']))   $vars[] = '--green: '   . $ws['green']   . ';';
        if (!empty($ws['radius']))  $vars[] = '--radius-ms: ' . intval($ws['radius']) . 'px;';
    elseif ($preset === 'neon'):
        $vars[] = '--surface: rgba(2,4,18,.97);';
        $vars[] = '--border2: rgba(0,255,200,.22);';
        $vars[] = '--brand: #00ffc8;';
        $vars[] = '--brand2: #00e5ff;';
        $vars[] = '--orange: #00ffc8;';
        $vars[] = '--green: #00e5ff;';
    elseif ($preset === 'fire'):
        $vars[] = '--surface: rgba(10,4,2,.97);';
        $vars[] = '--border2: rgba(249,115,22,.22);';
        $vars[] = '--brand: #f97316;';
        $vars[] = '--brand2: #fbbf24;';
        $vars[] = '--orange: #f97316;';
        $vars[] = '--green: #fbbf24;';
    elseif ($preset === 'ice'):
        $vars[] = '--surface: rgba(2,8,22,.96);';
        $vars[] = '--border2: rgba(147,210,255,.18);';
        $vars[] = '--brand: #38bdf8;';
        $vars[] = '--brand2: #818cf8;';
        $vars[] = '--orange: #38bdf8;';
        $vars[] = '--green: #818cf8;';
    elseif ($preset === 'minimal'):
        $vars[] = '--surface: rgba(12,12,16,.95);';
        $vars[] = '--border2: rgba(255,255,255,.14);';
        $vars[] = '--brand: #e0e0f0;';
        $vars[] = '--brand2: #ffffff;';
        $vars[] = '--orange: #e0e0f0;';
        $vars[] = '--green: #ffffff;';
    endif;
    // Radius for non-default presets
    if ($preset !== 'default' && $preset !== 'custom' && !empty($ws['radius'])):
        $vars[] = '--radius-ms: ' . intval($ws['radius']) . 'px;';
    endif;
    // Width / position
    $msWidth = !empty($ws['width']) ? intval($ws['width']) : 340;
    $msPos   = $ws['position'] ?? 'bottom-left';
@endphp
@if(!empty($vars))
:root {
    {!! implode("\n    ", $vars) !!}
}
@endif
#milestone-panel {
    width: {{ $msWidth }}px !important;
@php
    $posMap = [
        'top-left'     => ['top:40px',     'left:40px',   'bottom:auto', 'right:auto'],
        'top-right'    => ['top:40px',      'right:40px',  'bottom:auto', 'left:auto'],
        'bottom-left'  => ['bottom:40px',   'left:40px',   'top:auto',    'right:auto'],
        'bottom-right' => ['bottom:40px',   'right:40px',  'top:auto',    'left:auto'],
        'center'       => ['top:50%',       'left:50%',    'transform:translate(-50%,-50%)', 'bottom:auto', 'right:auto'],
    ];
    $posStyles = $posMap[$msPos] ?? $posMap['bottom-left'];
    echo implode(";\n    ", $posStyles) . ';';
@endphp
}
</style>

<div id="sse-status">connecting…</div>

<div id="milestone-panel">
    <div class="ms-wrap">
        <div class="ms-inner">
            <div class="ms-badge">🎯 MILESTONE</div>
            <div class="ms-title" id="ms-title">Target Stream Hari Ini</div>
            <div class="ms-amounts">
                <div class="ms-current" id="ms-current">Rp 0</div>
                <div class="ms-sep">/</div>
                <div class="ms-target"  id="ms-target">Rp 1Jt</div>
                <div class="ms-pct"     id="ms-pct">0%</div>
                <div class="ms-reached-label">TERCAPAI! 🎉</div>
            </div>
            <div class="ms-track">
                <div class="ms-bar" id="ms-bar"></div>
            </div>
        </div>
    </div>
</div>

<script>
const SSE_URL   = '{{ url("/" . $streamer->slug . "/sse") }}?key={{ $apiKey }}';
const STATS_URL = '{{ url("/" . $streamer->slug . "/stats") }}?key={{ $apiKey }}';
const statusEl  = document.getElementById('sse-status');
const panel     = document.getElementById('milestone-panel');
let wasReached  = false;

async function loadInitial() {
    try {
        const res = await fetch(STATS_URL);

        // Bedakan server error dari data valid
        if (!res.ok) {
            throw new Error('Server error: ' + res.status);
        }

        let data;
        try {
            data = await res.json();
        } catch (parseErr) {
            throw new Error('Invalid response from server');
        }

        applyStats(data);
        // Tampilkan panel hanya setelah data berhasil dimuat
        panel.classList.add('visible');
    } catch(e) {
        // Tampilkan indikator error yang jelas — jangan biarkan tampil diam dengan default Rp 0
        statusEl.textContent = '● error';
        statusEl.style.color = 'rgba(249,115,22,.4)';

        // Auto-retry setelah 10 detik
        setTimeout(loadInitial, 10000);
        console.warn('Milestone loadInitial failed, will retry in 10s:', e.message);
    }
}

let currentEventSource = null;
let sseHandlers = { onopen: null, stats: null, ping: null, onerror: null };

function connectSSE() {
    // Clean up existing connection and handlers
    if (currentEventSource) {
        if (sseHandlers.stats) currentEventSource.removeEventListener('stats', sseHandlers.stats);
        if (sseHandlers.ping) currentEventSource.removeEventListener('ping', sseHandlers.ping);
        currentEventSource.close();
        currentEventSource = null;
    }

    currentEventSource = new EventSource(SSE_URL);

    sseHandlers.onopen = () => {
        statusEl.textContent = '● live';
        statusEl.style.color = 'rgba(34,211,160,.3)';
    };
    currentEventSource.onopen = sseHandlers.onopen;

    sseHandlers.stats = (e) => {
        try { applyStats(JSON.parse(e.data)); }
        catch(err) { console.error('SSE stats error:', err); }
    };
    currentEventSource.addEventListener('stats', sseHandlers.stats);

    sseHandlers.ping = () => {};
    currentEventSource.addEventListener('ping', sseHandlers.ping);

    sseHandlers.onerror = () => {
        statusEl.textContent = '● reconnecting…';
        statusEl.style.color = 'rgba(249,115,22,.3)';
        if (currentEventSource) currentEventSource.close();
        setTimeout(connectSSE, 3000);
    };
    currentEventSource.onerror = sseHandlers.onerror;
}

function applyStats(data) {
    const ms      = data.milestone || {};
    const current = ms.current || 0;
    const target  = ms.target  || 1000000;
    const title   = ms.title   || 'Target Stream Hari Ini';
    const reached = ms.reached || false;

    const pct = target > 0 ? Math.min(100, Math.round(current / target * 100)) : 0;

    document.getElementById('ms-title').textContent   = title;
    document.getElementById('ms-current').textContent = formatRp(current);
    document.getElementById('ms-target').textContent  = formatRp(target);
    document.getElementById('ms-pct').textContent     = pct + '%';

    setTimeout(() => {
        document.getElementById('ms-bar').style.width = pct + '%';
    }, 80);

    if (reached && !wasReached) {
        panel.classList.add('reached');
        wasReached = true;
    } else if (!reached) {
        panel.classList.remove('reached');
        wasReached = false;
    }
}

function formatRp(n) {
    if (n >= 1000000) return 'Rp ' + (n/1000000).toFixed(1).replace('.0','') + 'Jt';
    if (n >= 1000)    return 'Rp ' + (n/1000).toFixed(0) + 'K';
    return 'Rp ' + n;
}

loadInitial();
connectSSE();
</script>
</body>
</html>
