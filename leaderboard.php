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
            --surface: rgba(10,10,16,.94);
            --border:  rgba(255,255,255,.07);
            --border2: rgba(124,108,252,.22);
        }

        /* ─── PANEL ─── */
        #leaderboard-panel {
            position: fixed;
            top: 50%;
            right: 0;
            transform: translateY(-50%) translateX(360px);
            width: 300px;
            pointer-events: none;
            opacity: 0;
            transition: opacity .55s ease, transform .55s cubic-bezier(.34, 1.3, .64, 1);
        }
        #leaderboard-panel.visible {
            opacity: 1;
            transform: translateY(-50%) translateX(0);
        }

        /* ─── WRAP ─── */
        .lb-wrap {
            background: var(--surface);
            border: 1px solid var(--border2);
            border-right: none;
            border-radius: 18px 0 0 18px;
            overflow: hidden;
            backdrop-filter: blur(28px) saturate(180%);
            box-shadow:
                -12px 0 48px rgba(0,0,0,.7),
                inset 0 0 0 1px rgba(255,255,255,.04),
                0 0 80px rgba(124,108,252,.08);
            position: relative;
        }

        /* Left accent bar */
        .lb-wrap::before {
            content: '';
            position: absolute;
            top: 0; left: 0; bottom: 0;
            width: 3px;
            background: linear-gradient(180deg, var(--brand), var(--purple), var(--green));
        }

        /* ─── HEADER ─── */
        .lb-header {
            padding: 16px 18px 14px 20px;
            border-bottom: 1px solid rgba(255,255,255,.05);
        }

        .lb-live {
            display: inline-flex; align-items: center; gap: 5px;
            background: rgba(124,108,252,.12);
            border: 1px solid rgba(124,108,252,.25);
            border-radius: 20px;
            padding: 3px 9px;
            font-size: 9px; font-weight: 800; letter-spacing: 1.5px;
            color: var(--brand2);
            text-transform: uppercase;
            margin-bottom: 8px;
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
            font-size: 22px; font-weight: 700; letter-spacing: -.5px;
            color: var(--text); line-height: 1;
        }
        .lb-subtitle {
            font-size: 10px; color: var(--text-3);
            margin-top: 4px; letter-spacing: .3px;
        }

        /* ─── LIST ─── */
        .lb-list {
            padding: 8px 0 10px;
            display: flex; flex-direction: column;
        }

        .lb-item {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 18px 9px 20px;
            position: relative;
            transition: background .25s;
        }
        .lb-item + .lb-item::before {
            content: '';
            position: absolute;
            top: 0; left: 20px; right: 18px;
            height: 1px;
            background: rgba(255,255,255,.04);
        }

        /* Top 3 highlights */
        .lb-item.rank-1 { background: rgba(251,191,36,.05); }
        .lb-item.rank-2 { background: rgba(180,180,200,.025); }
        .lb-item.rank-3 { background: rgba(205,127,50,.04); }

        @keyframes itemIn {
            from { opacity: 0; transform: translateX(16px); }
            to   { opacity: 1; transform: translateX(0); }
        }
        .lb-item.new { animation: itemIn .45s ease forwards; }

        /* Rank indicator */
        .lb-rank {
            width: 24px; flex-shrink: 0;
            text-align: center; font-size: 17px; line-height: 1;
        }
        .lb-rank.num {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 13px; font-weight: 700; color: var(--text-3);
        }

        /* Emoji avatar */
        .lb-avatar {
            width: 32px; height: 32px; border-radius: 10px;
            background: rgba(124,108,252,.1);
            border: 1px solid rgba(124,108,252,.15);
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; flex-shrink: 0;
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
            font-size: 15px; font-weight: 700; letter-spacing: -.3px;
            color: var(--yellow); line-height: 1;
        }
        .lb-item.rank-1 .lb-total { color: #fde68a; }

        /* ─── FOOTER ─── */
        .lb-footer {
            padding: 10px 18px 14px 20px;
            border-top: 1px solid rgba(255,255,255,.05);
            display: flex; align-items: center; justify-content: space-between;
        }
        .lb-footer-label { font-size: 9px; color: var(--text-3); font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
        .lb-footer-val {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 17px; font-weight: 700; letter-spacing: -.5px;
            color: var(--green);
        }

        /* Empty state */
        .lb-empty { padding: 22px 20px; font-size: 11px; color: var(--text-3); text-align: center; }

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
const statusEl  = document.getElementById('sse-status');
const panel     = document.getElementById('leaderboard-panel');
const MEDALS    = ['🥇','🥈','🥉'];

async function loadInitial() {
    try {
        const res  = await fetch('api/stats.php');
        const data = await res.json();
        applyStats(data, false);
    } catch(e) {
        document.getElementById('lb-list').innerHTML = '<div class="lb-empty">Belum ada donasi masuk</div>';
    }
    panel.classList.add('visible');
}

function connectSSE() {
    const es = new EventSource('api/stream.php');

    es.onopen = () => {
        statusEl.textContent = '● live';
        statusEl.style.color = 'rgba(34,211,160,.3)';
    };

    es.addEventListener('stats', (e) => {
        try { applyStats(JSON.parse(e.data), true); }
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
