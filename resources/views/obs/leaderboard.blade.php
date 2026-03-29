<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>StreamDonate — Leaderboard Overlay</title>
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
            --text-2:  rgba(241,241,246,.6);
            --text-3:  rgba(241,241,246,.35);
            --surface: rgba(8,8,12,.88);
            --border:  rgba(255,255,255,.1);
            --border2: rgba(124,108,252,.22);
            --glass-blur: 16px;
            --glass-glow: 0 0 60px rgba(124,108,252,.1);
        }

        /* ─── PANEL ─── */
        #leaderboard-panel {
            position: fixed;
            top: 60px;
            left: 60px;
            width: 300px;
            pointer-events: none;
            opacity: 0;
            transform: translateX(-24px);
            transition: opacity .5s ease, transform .55s cubic-bezier(.34, 1.3, .64, 1);
        }
        #leaderboard-panel.visible {
            opacity: 1;
            transform: translateX(0);
        }

        /* ─── WRAP ─── */
        .lb-wrap {
            background: var(--surface);
            border: 1px solid var(--border2);
            border-radius: var(--radius-lb, 18px);
            overflow: hidden;
            box-shadow:
                0 8px 40px rgba(0,0,0,.7),
                inset 0 0 0 1px rgba(255,255,255,.05),
                var(--glass-glow);
            position: relative;
            backdrop-filter: blur(var(--glass-blur)) saturate(180%);
            -webkit-backdrop-filter: blur(var(--glass-blur)) saturate(180%);
        }

        /* Top accent bar */
        .lb-wrap::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2.5px;
            background: linear-gradient(90deg, var(--brand), var(--purple), var(--green));
            box-shadow: 0 0 20px rgba(124,108,252,.4);
        }

        /* ─── HEADER ─── */
        .lb-header {
            padding: 18px 18px 14px 18px;
            border-bottom: 1px solid var(--border);
        }

        .lb-live {
            display: inline-flex; align-items: center; gap: 5px;
            background: rgba(124,108,252,.12);
            border: 1px solid rgba(124,108,252,.25);
            border-radius: 20px;
            padding: 3px 10px;
            font-size: 9px; font-weight: 800; letter-spacing: 1.5px;
            color: var(--brand2);
            text-transform: uppercase;
            margin-bottom: 9px;
            box-shadow: 0 0 20px rgba(124,108,252,.15);
        }
        .lb-live-dot {
            width: 5px; height: 5px;
            border-radius: 50%;
            background: var(--brand);
            animation: blink 2s infinite;
        }
        @keyframes blink {
            0%,100% { opacity:1; }
            50%      { opacity:.25; }
        }

        .lb-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 20px; font-weight: 700; letter-spacing: -.4px;
            color: var(--text); line-height: 1;
        }
        .lb-subtitle {
            font-size: 10px; color: var(--text-3);
            margin-top: 4px; letter-spacing: .3px;
        }

        /* ─── LIST ─── */
        .lb-list {
            padding: 6px 0 8px;
            display: flex; flex-direction: column;
        }

        .lb-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 16px;
            position: relative;
            transition: background .25s;
        }
        .lb-item + .lb-item::before {
            content: '';
            position: absolute;
            top: 0; left: 16px; right: 16px;
            height: 1px;
            background: rgba(255,255,255,.04);
        }

        /* Top 3 highlights */
        .lb-item.rank-1 { background: rgba(251,191,36,.04); }
        .lb-item.rank-2 { background: rgba(180,180,200,.02); }
        .lb-item.rank-3 { background: rgba(205,127,50,.03); }

        @keyframes itemIn {
            from { opacity: 0; transform: translateX(-12px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        .lb-item.new { animation: itemIn .4s ease forwards; }

        /* Rank indicator */
        .lb-rank {
            width: 24px; flex-shrink: 0;
            text-align: center; font-size: 16px; line-height: 1;
        }
        .lb-rank.num {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 12px; font-weight: 700; color: var(--text-3);
        }

        /* Emoji avatar */
        .lb-avatar {
            width: 30px; height: 30px; border-radius: 10px;
            background: rgba(124,108,252,.1);
            border: 1px solid rgba(124,108,252,.18);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; flex-shrink: 0;
            box-shadow: 0 0 12px rgba(124,108,252,.1);
        }

        /* Name + count */
        .lb-info { flex: 1; min-width: 0; }
        .lb-name {
            font-size: 13px; font-weight: 600; color: var(--text);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
            line-height: 1.2;
        }
        .lb-count { font-size: 9px; color: var(--text-3); margin-top: 2px; letter-spacing: .3px; }

        /* Amount */
        .lb-amount { text-align: right; flex-shrink: 0; }
        .lb-total {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 14px; font-weight: 700; letter-spacing: -.3px;
            color: var(--yellow); line-height: 1;
        }
        .lb-item.rank-1 .lb-total { color: #fde68a; }

        /* ─── FOOTER ─── */
        .lb-footer {
            padding: 10px 16px 14px;
            border-top: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .lb-footer-label { font-size: 9px; color: var(--text-3); font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
        .lb-footer-val {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 16px; font-weight: 700; letter-spacing: -.4px;
            color: var(--green);
        }

        /* Empty state */
        .lb-empty { padding: 22px 16px; font-size: 11px; color: var(--text-3); text-align: center; }

        /* ─── THEMES ─── */
        body.theme-neon {
            --surface: rgba(2,4,18,.97);
            --border2: rgba(0,255,200,.22);
            --brand:   #00ffc8;
            --brand2:  #00e5ff;
            --yellow:  #00ffc8;
            --green:   #00e5ff;
        }
        body.theme-fire {
            --surface: rgba(10,4,2,.97);
            --border2: rgba(249,115,22,.22);
            --brand:   #f97316;
            --brand2:  #fbbf24;
            --yellow:  #f97316;
            --green:   #fbbf24;
        }
        body.theme-ice {
            --surface: rgba(2,8,22,.96);
            --border2: rgba(147,210,255,.18);
            --brand:   #38bdf8;
            --brand2:  #818cf8;
            --yellow:  #38bdf8;
            --green:   #818cf8;
        }
        body.theme-minimal {
            --surface: rgba(12,12,16,.95);
            --border2: rgba(255,255,255,.14);
            --brand:   #e0e0f0;
            --brand2:  #ffffff;
            --yellow:  #e0e0f0;
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
    $ws = $streamer->getWidgetSettings()['leaderboard'] ?? [];
    $preset = $ws['preset'] ?? 'default';
    $themeMap = ['neon'=>'theme-neon','fire'=>'theme-fire','ice'=>'theme-ice','minimal'=>'theme-minimal'];
    echo $themeMap[$preset] ?? '';
@endphp">

<style id="widget-custom-vars">
@php
    $ws = $streamer->getWidgetSettings()['leaderboard'] ?? [];
    $preset = $ws['preset'] ?? 'default';
    $vars = [];
    if ($preset === 'custom'):
        if (!empty($ws['surface'])) $vars[] = '--surface: ' . $ws['surface'] . ';';
        if (!empty($ws['border']))  $vars[] = '--border2: ' . $ws['border']  . ';';
        if (!empty($ws['brand']))   $vars[] = '--brand: '   . $ws['brand']   . ';';
        if (!empty($ws['brand2']))  $vars[] = '--brand2: '  . $ws['brand2']  . ';';
        if (!empty($ws['yellow']))  $vars[] = '--yellow: '  . $ws['yellow']  . ';';
        if (!empty($ws['green']))   $vars[] = '--green: '   . $ws['green']   . ';';
        if (!empty($ws['radius']))  $vars[] = '--radius-lb: ' . intval($ws['radius']) . 'px;';
    elseif ($preset === 'neon'):
        $vars[] = '--surface: rgba(2,4,18,.97);';
        $vars[] = '--border2: rgba(0,255,200,.22);';
        $vars[] = '--brand: #00ffc8;';
        $vars[] = '--brand2: #00e5ff;';
        $vars[] = '--yellow: #00ffc8;';
        $vars[] = '--green: #00e5ff;';
    elseif ($preset === 'fire'):
        $vars[] = '--surface: rgba(10,4,2,.97);';
        $vars[] = '--border2: rgba(249,115,22,.22);';
        $vars[] = '--brand: #f97316;';
        $vars[] = '--brand2: #fbbf24;';
        $vars[] = '--yellow: #f97316;';
        $vars[] = '--green: #fbbf24;';
    elseif ($preset === 'ice'):
        $vars[] = '--surface: rgba(2,8,22,.96);';
        $vars[] = '--border2: rgba(147,210,255,.18);';
        $vars[] = '--brand: #38bdf8;';
        $vars[] = '--brand2: #818cf8;';
        $vars[] = '--yellow: #38bdf8;';
        $vars[] = '--green: #818cf8;';
    elseif ($preset === 'minimal'):
        $vars[] = '--surface: rgba(12,12,16,.95);';
        $vars[] = '--border2: rgba(255,255,255,.14);';
        $vars[] = '--brand: #e0e0f0;';
        $vars[] = '--brand2: #ffffff;';
        $vars[] = '--yellow: #e0e0f0;';
        $vars[] = '--green: #ffffff;';
    endif;
    // Radius for non-default presets
    if ($preset !== 'default' && $preset !== 'custom' && !empty($ws['radius'])):
        $vars[] = '--radius-lb: ' . intval($ws['radius']) . 'px;';
    endif;
    // Width / position
    $lbWidth = !empty($ws['width']) ? intval($ws['width']) : 300;
    $lbPos   = $ws['position'] ?? 'top-left';
@endphp
@if(!empty($vars))
:root {
    {!! implode("\n    ", $vars) !!}
}
@endif
#leaderboard-panel {
    width: {{ $lbWidth }}px !important;
@php
    $posMap = [
        'top-left'     => ['top:60px',      'left:60px',   'bottom:auto', 'right:auto'],
        'top-right'    => ['top:60px',       'right:60px',  'bottom:auto', 'left:auto'],
        'bottom-left'  => ['bottom:60px',    'left:60px',   'top:auto',    'right:auto'],
        'bottom-right' => ['bottom:60px',    'right:60px',  'top:auto',    'left:auto'],
        'center'       => ['top:50%',        'left:50%',    'transform:translate(-50%,-50%)', 'bottom:auto', 'right:auto'],
    ];
    $posStyles = $posMap[$lbPos] ?? $posMap['top-left'];
    echo implode(";\n    ", $posStyles) . ';';
@endphp
}
</style>

<div id="sse-status">connecting…</div>

<div id="leaderboard-panel">
    <div class="lb-wrap">
        <div class="lb-header">
            <div class="lb-live">
                <span class="lb-live-dot"></span>
                LIVE
            </div>
            <div class="lb-title"    id="lb-title">Top Donatur</div>
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

<script>
const SSE_URL    = '{{ url("/" . $streamer->slug . "/sse") }}?key={{ $apiKey }}';
const STATS_URL  = '{{ url("/" . $streamer->slug . "/stats") }}?key={{ $apiKey }}';
const statusEl   = document.getElementById('sse-status');
const panel      = document.getElementById('leaderboard-panel');
const MEDALS     = ['🥇','🥈','🥉'];

async function loadInitial() {
    try {
        const res = await fetch(STATS_URL);

        // Bedakan server error (non-JSON / non-2xx) dari data kosong
        if (!res.ok) {
            throw new Error('Server error: ' + res.status);
        }

        let data;
        try {
            data = await res.json();
        } catch (parseErr) {
            throw new Error('Invalid response from server');
        }

        applyStats(data, false);
    } catch(e) {
        // Tampilkan state error yang BERBEDA dari state kosong
        // agar streamer tahu ini adalah masalah koneksi, bukan "belum ada donasi"
        document.getElementById('lb-list').innerHTML =
            '<div class="lb-empty" style="color:rgba(249,115,22,.7);font-size:12px;">Gagal memuat data<br>Mencoba lagi…</div>';

        // Auto-retry setelah 10 detik
        setTimeout(loadInitial, 10000);
        console.warn('Leaderboard loadInitial failed, will retry in 10s:', e.message);
    }
    panel.classList.add('visible');
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
        try { applyStats(JSON.parse(e.data), true); }
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

function applyStats(data, animate) {
    const lb     = data.leaderboard || [];
    const total  = data.total  || 0;
    const donors = data.donors || 0;
    const config = data.config || {};
    const title  = config.leaderboardTitle || 'Top Donatur';

    document.getElementById('lb-title').textContent    = title;
    document.getElementById('lb-subtitle').textContent = donors + ' donatur \u2022 ' + formatRp(total) + ' total';
    document.getElementById('lb-grand-total').textContent = formatRp(total);

    const list = document.getElementById('lb-list');

    if (lb.length === 0) {
        list.innerHTML = '<div class="lb-empty">Belum ada donasi masuk</div>';
        return;
    }

    list.innerHTML = lb.map((d, i) => {
        const rankClass = i < 3 ? 'rank-' + (i+1) : '';
        const medal     = i < 3 ? MEDALS[i] : null;
        const rankHtml  = medal
            ? `<div class="lb-rank">${esc(medal)}</div>`
            : `<div class="lb-rank num">${i+1}</div>`;
        const newCls    = animate ? 'new' : '';
        const delay     = animate ? `animation-delay:${i * 55}ms` : '';

        return `
        <div class="lb-item ${rankClass} ${newCls}" style="${delay}">
            ${rankHtml}
            <div class="lb-avatar">${esc(d.emoji || '🎉')}</div>
            <div class="lb-info">
                <div class="lb-name">${esc(d.name)}</div>
                <div class="lb-count">${d.count}x donasi</div>
            </div>
            <div class="lb-amount">
                <div class="lb-total">${formatRp(d.total)}</div>
            </div>
        </div>`;
    }).join('');
}

function formatRp(n) {
    if (n >= 1000000) return 'Rp ' + (n/1000000).toFixed(1).replace('.0','') + 'Jt';
    if (n >= 1000)    return 'Rp ' + (n/1000).toFixed(0) + 'K';
    return 'Rp ' + n;
}
function esc(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

loadInitial();
connectSSE();
</script>
</body>
</html>
