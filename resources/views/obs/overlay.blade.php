<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>StreamDonate — OBS Alert Overlay</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet" />
    <style>
        /* ─── BASE ─── */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            width: 1920px; height: 1080px;
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

        /* ─── ANIMATIONS ─── */
        @keyframes alertIn {
            from { transform: translateX(-50%) translateY(80px) scale(.97); opacity: 0; }
            to   { transform: translateX(-50%) translateY(0) scale(1); opacity: 1; }
        }
        @keyframes alertOut {
            from { transform: translateX(-50%) translateY(0) scale(1); opacity: 1; }
            to   { transform: translateX(-50%) translateY(60px) scale(.97); opacity: 0; }
        }
        @keyframes alertIn_minimal {
            from { transform: translateX(-50%) translateY(-16px); opacity: 0; }
            to   { transform: translateX(-50%) translateY(0); opacity: 1; }
        }
        @keyframes alertOut_minimal {
            from { transform: translateX(-50%) translateY(0); opacity: 1; }
            to   { transform: translateX(-50%) translateY(-16px); opacity: 0; }
        }
        @keyframes alertIn_neon {
            0%   { transform: translateX(-50%) scale(.88); opacity: 0; filter: blur(6px); }
            65%  { transform: translateX(-50%) scale(1.02); opacity: 1; filter: blur(0); }
            100% { transform: translateX(-50%) scale(1); opacity: 1; }
        }
        @keyframes alertOut_neon {
            from { transform: translateX(-50%) scale(1); opacity: 1; }
            to   { transform: translateX(-50%) scale(.88); opacity: 0; filter: blur(6px); }
        }
        @keyframes alertIn_fire {
            0%   { transform: translateX(-50%) translateY(100px) scale(.92); opacity: 0; filter: blur(3px); }
            70%  { transform: translateX(-50%) translateY(-6px) scale(1.01); opacity: 1; filter: blur(0); }
            100% { transform: translateX(-50%) translateY(0) scale(1); opacity: 1; }
        }
        @keyframes alertOut_fire {
            from { transform: translateX(-50%) translateY(0) scale(1); opacity: 1; }
            to   { transform: translateX(-50%) translateY(-50px) scale(.95); opacity: 0; filter: blur(3px); }
        }
        @keyframes alertIn_ice {
            0%   { transform: translateX(-50%) scale(.7); opacity: 0; filter: blur(5px) brightness(1.6); }
            65%  { transform: translateX(-50%) scale(1.03); opacity: 1; filter: blur(0) brightness(1.05); }
            100% { transform: translateX(-50%) scale(1); opacity: 1; filter: blur(0) brightness(1); }
        }
        @keyframes alertOut_ice {
            from { transform: translateX(-50%) scale(1); opacity: 1; }
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
            font-family: 'Inter', sans-serif;
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
            padding: 18px 20px 0;
        }

        /* ─── HEADER ROW: avatar + donor/amount + badge ─── */
        .alert-header {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 14px;
        }

        .alert-avatar {
            width: 48px; height: 48px;
            border-radius: 12px;
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.1);
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; flex-shrink: 0;
            line-height: 1;
        }

        .alert-meta { flex: 1; min-width: 0; }

        .alert-donor {
            font-size: 17px; font-weight: 700;
            color: var(--donor-c);
            letter-spacing: -.3px; line-height: 1.2;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .alert-amount {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 24px; font-weight: 700; letter-spacing: -.6px;
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
            font-size: 13px; font-weight: 400;
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
<body class="theme-{{ $streamer->alert_theme ?? 'default' }}">

<div id="sse-status">connecting…</div>

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

<script>
const SSE_URL        = '{{ url("/" . $streamer->slug . "/sse") }}?key={{ $apiKey }}';
const ASSET_STORAGE  = '{{ asset("storage") }}';

// ── Mutable config — updated live via SSE stats.config ──
let SOUND_ON       = {{ $streamer->sound_enabled ? 'true' : 'false' }};
let SOUND_PREF     = {!! json_encode($streamer->notification_sound ?? 'ding') !!};
let ALERT_DURATION = {{ (int) ($streamer->alert_duration ?? 8000) }};

function getSoundUrl() {
    return (SOUND_PREF && !['ding','coin','whoosh'].includes(SOUND_PREF))
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
    if (p === 'coin') playCoin();
    else if (p === 'whoosh') playWhoosh();
    else playDing();
}

// ─── Live config from SSE stats.config ───
function applyConfig(config) {
    if (!config) return;
    if (config.alertTheme) {
        const body = document.body;
        body.className = body.className.replace(/\btheme-\S+/g, '').trim();
        if (config.alertTheme !== 'default') body.classList.add('theme-' + config.alertTheme);
    }
    if (config.alertDuration) ALERT_DURATION = config.alertDuration;
    if (config.soundEnabled !== undefined) SOUND_ON = !!config.soundEnabled;
    if (config.notificationSound !== undefined && config.notificationSound !== SOUND_PREF) {
        SOUND_PREF = config.notificationSound;
        _customSoundBuffer = null; _customSoundUrl = null;
    }
}

// ─── SSE ───
function connectSSE() {
    const es = new EventSource(SSE_URL);
    es.onopen = function() {
        statusEl.textContent = '● live';
        statusEl.style.color = 'rgba(34,211,160,.4)';
    };
    es.addEventListener('donation', function(e) {
        try {
            const d = JSON.parse(e.data);
            if (seenIds.has(d.seq ?? d.id)) return;
            seenIds.add(d.seq ?? d.id);
            addToQueue(d);
        } catch(err) { console.error('SSE parse error:', err); }
    });
    es.addEventListener('stats', function(e) {
        try { applyConfig(JSON.parse(e.data).config); }
        catch(err) { console.error('SSE stats error:', err); }
    });
    es.addEventListener('ping', function() {});
    es.onerror = function() {
        statusEl.textContent = '● reconnecting…';
        statusEl.style.color = 'rgba(249,115,22,.4)';
        es.close();
        setTimeout(connectSSE, 3000);
    };
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
    const duration  = ALERT_DURATION;

    document.getElementById('alert-avatar').textContent = donation.emoji || '🎉';
    document.getElementById('alert-donor').textContent  = donation.name;
    document.getElementById('alert-amount').textContent = formatRp(donation.amount);

    const msg = donation.message || donation.msg || '';
    msgEl.textContent = msg;
    // Show/hide divider based on whether there's a message
    divider.style.display = msg ? '' : 'none';

    const ytUrl     = donation.ytUrl     || donation.yt_url     || null;
    const ytEnabled = donation.ytEnabled !== undefined ? donation.ytEnabled : donation.yt_enabled;
    if (ytUrl && ytEnabled !== false) {
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
