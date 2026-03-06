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
            --surface: rgba(10,10,16,.93);
            --border:  rgba(124,108,252,.22);
        }

        /* ─── PANEL WRAPPER ─── */
        #milestone-panel {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            padding: 0 0 24px;
            pointer-events: none;
            opacity: 0;
            transform: translateY(70px);
            transition: opacity .55s ease, transform .6s cubic-bezier(.34, 1.3, .64, 1);
        }
        #milestone-panel.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ─── CARD ─── */
        .ms-wrap {
            margin: 0 auto;
            width: calc(100% - 96px);
            max-width: 1824px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 14px 24px 16px;
            backdrop-filter: blur(28px) saturate(180%);
            box-shadow:
                0 -8px 48px rgba(0,0,0,.65),
                inset 0 1px 0 rgba(255,255,255,.06),
                0 0 80px rgba(124,108,252,.07);
            position: relative;
            overflow: hidden;
        }

        /* Top gradient border */
        .ms-wrap::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--brand), var(--purple), var(--green));
        }

        /* ─── TOP ROW ─── */
        .ms-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }

        .ms-left {
            display: flex; align-items: center; gap: 14px;
        }

        .ms-badge {
            display: inline-flex; align-items: center; gap: 5px;
            background: rgba(124,108,252,.12);
            border: 1px solid rgba(124,108,252,.25);
            border-radius: 20px;
            padding: 4px 11px;
            font-size: 9px; font-weight: 800; letter-spacing: 1.5px;
            color: var(--brand2);
            text-transform: uppercase;
            white-space: nowrap;
        }

        .ms-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 18px; font-weight: 700; letter-spacing: -.3px;
            color: var(--text); line-height: 1;
        }

        /* Right: amounts */
        .ms-right {
            display: flex; align-items: baseline; gap: 8px;
        }
        .ms-current {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 26px; font-weight: 700; letter-spacing: -.8px;
            color: var(--orange); line-height: 1;
        }
        .ms-sep { font-size: 16px; color: var(--text-2); font-weight: 400; }
        .ms-target {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 18px; font-weight: 600; letter-spacing: -.3px;
            color: var(--text-2); line-height: 1;
        }
        .ms-pct {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 18px; font-weight: 700; letter-spacing: -.3px;
            color: var(--brand2); margin-left: 10px; line-height: 1;
        }
        .ms-reached-label {
            display: none;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 16px; font-weight: 700; letter-spacing: -.2px;
            color: var(--green); margin-left: 10px;
        }

        /* ─── PROGRESS TRACK ─── */
        .ms-track {
            height: 10px;
            background: rgba(255,255,255,.06);
            border-radius: 5px;
            overflow: hidden;
            position: relative;
        }

        .ms-bar {
            height: 100%;
            width: 0%;
            border-radius: 5px;
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
            border-color: rgba(34,211,160,.3);
        }
        #milestone-panel.reached .ms-wrap::before {
            background: linear-gradient(90deg, var(--green), var(--brand2), var(--green));
        }

        @keyframes reachedGlow {
            0%,100% { box-shadow: 0 -8px 48px rgba(0,0,0,.65), 0 0 50px rgba(34,211,160,.12); }
            50%     { box-shadow: 0 -8px 48px rgba(0,0,0,.65), 0 0 100px rgba(34,211,160,.3), 0 0 30px rgba(124,108,252,.15); }
        }
        #milestone-panel.reached .ms-wrap {
            animation: reachedGlow 2s ease-in-out infinite;
        }
        #milestone-panel.reached .ms-pct { display: none; }
        #milestone-panel.reached .ms-reached-label { display: inline; }

        /* ─── SSE status ─── */
        #sse-status {
            position: fixed; bottom: 8px; left: 10px;
            font-size: 9px; color: rgba(255,255,255,.18);
            font-family: monospace; pointer-events: none;
        }
    </style>
</head>
<body>

<div id="sse-status">connecting…</div>

<div id="milestone-panel">
    <div class="ms-wrap">
        <div class="ms-top">
            <div class="ms-left">
                <div class="ms-badge">🎯 MILESTONE</div>
                <div class="ms-title" id="ms-title">Target Stream Hari Ini</div>
            </div>
            <div class="ms-right">
                <div class="ms-current" id="ms-current">Rp 0</div>
                <div class="ms-sep">/</div>
                <div class="ms-target"  id="ms-target">Rp 1Jt</div>
                <div class="ms-pct"     id="ms-pct">0%</div>
                <div class="ms-reached-label">TERCAPAI! 🎉</div>
            </div>
        </div>
        <div class="ms-track">
            <div class="ms-bar" id="ms-bar"></div>
        </div>
    </div>
</div>

<script>
const statusEl = document.getElementById('sse-status');
const panel    = document.getElementById('milestone-panel');
let wasReached = false;

async function loadInitial() {
    try {
        const res  = await fetch('api/stats.php');
        const data = await res.json();
        applyStats(data);
    } catch(e) {}
    panel.classList.add('visible');
}

function connectSSE() {
    const es = new EventSource('api/stream.php');

    es.onopen = () => {
        statusEl.textContent = '● live';
        statusEl.style.color = 'rgba(34,211,160,.3)';
    };

    es.addEventListener('stats', (e) => {
        try { applyStats(JSON.parse(e.data)); }
        catch(err) { console.error('SSE stats error:', err); }
    });

    es.addEventListener('ping', () => {});

    es.onerror = () => {
        statusEl.textContent = '● reconnecting…';
        statusEl.style.color = 'rgba(249,115,22,.3)';
        es.close();
        setTimeout(connectSSE, 3000);
    };
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
