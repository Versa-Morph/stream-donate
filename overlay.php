<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>StreamDonate — OBS Alert Overlay</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet" />
    <style>
        /* ─── TRANSPARAN — wajib untuk OBS Browser Source ─── */
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
            --text:    #f1f1f6;
            --text-2:  #a0a0b4;
            --surface: rgba(10,10,16,.95);
            --border:  rgba(124,108,252,.28);
        }

        /* ─── ALERT BOX ─── */
        .alert-box {
            position: fixed;
            bottom: 48px;
            left: 50%;
            transform: translateX(-50%) translateY(120px);
            width: 560px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 20px 22px 0;
            backdrop-filter: blur(32px) saturate(180%);
            box-shadow:
                0 0 0 1px rgba(124,108,252,.1),
                0 32px 80px rgba(0,0,0,.8),
                0 0 60px rgba(124,108,252,.12);
            opacity: 0;
            overflow: hidden;
            pointer-events: none;
        }

        /* Gradient top border */
        .alert-box::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--brand), #a855f7, var(--green));
        }

        /* Pill tag top-right */
        .alert-pill {
            position: absolute;
            top: 0; right: 20px;
            background: linear-gradient(135deg, var(--brand), #6356e8);
            color: #fff;
            font-size: 9px; font-weight: 800; letter-spacing: 1.5px;
            padding: 3px 12px;
            border-radius: 0 0 10px 10px;
            text-transform: uppercase;
        }

        /* ─── ANIMATIONS ─── */
        @keyframes alertIn {
            from { transform: translateX(-50%) translateY(100px) scale(.96); opacity: 0; }
            to   { transform: translateX(-50%) translateY(0) scale(1); opacity: 1; }
        }
        @keyframes alertOut {
            from { transform: translateX(-50%) translateY(0) scale(1); opacity: 1; }
            to   { transform: translateX(-50%) translateY(80px) scale(.97); opacity: 0; }
        }

        .alert-box.visible {
            animation: alertIn .5s cubic-bezier(.34, 1.5, .64, 1) forwards;
        }
        .alert-box.hiding {
            animation: alertOut .38s cubic-bezier(.4, 0, 1, 1) forwards;
        }

        /* ─── HEADER ─── */
        .alert-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 12px;
        }

        .alert-avatar {
            width: 52px; height: 52px;
            border-radius: 16px;
            background: linear-gradient(135deg, rgba(124,108,252,.18), rgba(168,85,247,.18));
            border: 1px solid rgba(124,108,252,.3);
            display: flex; align-items: center; justify-content: center;
            font-size: 24px; flex-shrink: 0;
            box-shadow: 0 4px 16px rgba(124,108,252,.15);
        }

        .alert-info {}
        .alert-donor {
            font-size: 18px; font-weight: 800; color: var(--text);
            letter-spacing: -.3px; line-height: 1.2;
        }
        .alert-amount {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 26px; font-weight: 700; letter-spacing: -.5px;
            color: var(--orange);
            line-height: 1.1;
            margin-top: 1px;
        }

        /* ─── MESSAGE ─── */
        .alert-message {
            font-size: 13.5px; font-weight: 400;
            color: var(--text-2);
            line-height: 1.6;
            margin-bottom: 14px;
            padding: 10px 14px;
            background: rgba(255,255,255,.04);
            border-radius: 10px;
            border-left: 2px solid rgba(124,108,252,.5);
            font-style: italic;
        }

        /* ─── YOUTUBE ─── */
        .alert-yt {
            margin-bottom: 14px;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,.06);
            display: none;
        }
        .alert-yt iframe {
            width: 100%;
            height: 192px;
            display: block;
            border: none;
        }

        /* ─── PROGRESS BAR ─── */
        .alert-progress {
            height: 2px;
            background: rgba(255,255,255,.05);
            margin: 0 -22px;
        }
        .alert-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--brand), var(--orange));
            width: 100%;
        }

        /* ─── SSE STATUS (debug) ─── */
        #sse-status {
            position: fixed;
            top: 10px; right: 14px;
            font-size: 9px; font-weight: 600;
            color: rgba(255,255,255,.2);
            font-family: monospace;
            pointer-events: none;
            letter-spacing: .5px;
        }
    </style>
</head>
<body>

<div id="sse-status">connecting…</div>

<div class="alert-box" id="alert-box">
    <div class="alert-pill">DONASI MASUK</div>
    <div class="alert-header">
        <div class="alert-avatar" id="alert-avatar">🎉</div>
        <div class="alert-info">
            <div class="alert-donor"  id="alert-donor">Nama Donatur</div>
            <div class="alert-amount" id="alert-amount">Rp 0</div>
        </div>
    </div>
    <div class="alert-message" id="alert-message">Pesan donatur akan muncul di sini!</div>
    <div class="alert-yt" id="alert-yt">
        <iframe id="yt-iframe" allow="autoplay" allowfullscreen></iframe>
    </div>
    <div class="alert-progress">
        <div class="alert-progress-bar" id="progress-bar"></div>
    </div>
</div>

<script>
const statusEl = document.getElementById('sse-status');
const seenIds  = new Set();

function connectSSE() {
    const es = new EventSource('api/stream.php');

    es.onopen = () => {
        statusEl.textContent = '● live';
        statusEl.style.color = 'rgba(34,211,160,.4)';
    };

    es.addEventListener('donation', (e) => {
        try {
            const donation = JSON.parse(e.data);
            if (seenIds.has(donation.id)) return;
            seenIds.add(donation.id);
            addToQueue(donation);
        } catch (err) { console.error('SSE parse error:', err); }
    });

    es.addEventListener('ping', () => {});

    es.onerror = () => {
        statusEl.textContent = '● reconnecting…';
        statusEl.style.color = 'rgba(249,115,22,.4)';
        es.close();
        setTimeout(connectSSE, 3000);
    };
}
connectSSE();

const queue   = [];
let isShowing = false;

function addToQueue(donation) {
    queue.push(donation);
    if (!isShowing) processQueue();
}
function processQueue() {
    if (queue.length === 0) { isShowing = false; return; }
    isShowing = true;
    showAlert(queue.shift());
}

function showAlert(donation) {
    document.getElementById('alert-avatar').textContent  = donation.emoji  || '🎉';
    document.getElementById('alert-donor').textContent   = donation.name;
    document.getElementById('alert-amount').textContent  = formatRp(donation.amount);
    document.getElementById('alert-message').textContent = donation.msg || '';

    const ytSection = document.getElementById('alert-yt');
    const duration  = donation.duration || 8000;

    if (donation.ytUrl && donation.ytEnabled !== false) {
        const vid = extractYtId(donation.ytUrl);
        if (vid) {
            document.getElementById('yt-iframe').src = `https://www.youtube.com/embed/${vid}?autoplay=1&mute=0`;
            ytSection.style.display = 'block';
        } else { ytSection.style.display = 'none'; }
    } else {
        ytSection.style.display = 'none';
        document.getElementById('yt-iframe').src = '';
    }

    const box = document.getElementById('alert-box');
    box.classList.remove('visible', 'hiding');
    void box.offsetWidth;
    box.classList.add('visible');

    const bar = document.getElementById('progress-bar');
    bar.style.transition = 'none'; bar.style.width = '100%';
    setTimeout(() => {
        bar.style.transition = `width ${duration}ms linear`;
        bar.style.width = '0%';
    }, 60);

    setTimeout(() => {
        box.classList.add('hiding');
        setTimeout(() => {
            box.classList.remove('visible', 'hiding');
            document.getElementById('yt-iframe').src = '';
            processQueue();
        }, 420);
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
