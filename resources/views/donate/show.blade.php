<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="theme-color" content="#070709" />
    <title>Donasi untuk {{ $streamer->display_name }} — StreamDonate</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js" defer></script>
    <style>
        :root {
            --bg:#070709;--bg-1:#0d0d12;--surface:#141419;--surface-2:#1a1a22;--surface-3:#1f1f28;
            --border:rgba(255,255,255,.07);--border-2:rgba(255,255,255,.12);
            --brand:#7c6cfc;--brand-light:#a99dff;--brand-glow:rgba(124,108,252,.2);
            --orange:#f97316;--orange-glow:rgba(249,115,22,.15);
            --green:#22d3a0;--red:#f43f5e;--purple:#a855f7;
            --text:#f1f1f6;--text-2:#a0a0b4;--text-3:#606078;
            --radius-sm:8px;--radius:12px;--radius-lg:18px;
        }
        *,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
        html{scroll-behavior:smooth}
        body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;-webkit-font-smoothing:antialiased}

        /* ── LAYOUT ── */
        .donate-layout{display:grid;grid-template-columns:1fr 480px;min-height:100vh}

        /* ── HERO SIDE ── */
        .donate-hero{
            display:flex;flex-direction:column;align-items:center;justify-content:center;
            padding:60px 48px;position:relative;border-right:1px solid var(--border);
            background:var(--bg-1);overflow:hidden;
        }
        .donate-hero::before{content:'';position:absolute;top:-200px;left:-200px;width:600px;height:600px;border-radius:50%;background:radial-gradient(circle,rgba(124,108,252,.08) 0%,transparent 70%);pointer-events:none}
        .donate-hero::after{content:'';position:absolute;bottom:-100px;right:-100px;width:400px;height:400px;border-radius:50%;background:radial-gradient(circle,rgba(249,115,22,.06) 0%,transparent 70%);pointer-events:none}

        .hero-avatar-wrap{position:relative;margin-bottom:28px}
        .hero-avatar{
            width:96px;height:96px;border-radius:28px;
            background:linear-gradient(135deg,var(--brand),var(--purple));
            display:flex;align-items:center;justify-content:center;
            font-family:'Space Grotesk',sans-serif;font-size:38px;font-weight:700;color:#fff;letter-spacing:-1px;
            box-shadow:0 0 0 1px rgba(124,108,252,.3),0 24px 60px rgba(124,108,252,.25);
            user-select:none;
        }
        .hero-live{position:absolute;bottom:-8px;right:-8px;background:var(--red);border:2px solid var(--bg-1);padding:2px 8px;border-radius:6px;font-size:9px;font-weight:800;letter-spacing:1px;color:#fff;display:flex;align-items:center;gap:4px}
        .live-dot{width:5px;height:5px;border-radius:50%;background:#fff;animation:pulse 1.5s infinite}
        @keyframes pulse{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.4;transform:scale(.7)}}

        .hero-name{font-family:'Space Grotesk',sans-serif;font-size:36px;font-weight:700;letter-spacing:-1px;text-align:center;margin-bottom:8px}
        .hero-bio{font-size:14px;color:var(--text-3);text-align:center;line-height:1.6;max-width:320px}

        .hero-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-top:40px;width:100%;max-width:340px}
        .hero-stat{text-align:center;background:var(--surface);border:1px solid var(--border);border-radius:var(--radius);padding:14px 8px}
        .hero-stat-val{font-family:'Space Grotesk',sans-serif;font-size:20px;font-weight:700;color:var(--text);margin-bottom:3px}
        .hero-stat-lbl{font-size:10px;color:var(--text-3);letter-spacing:.5px}

        /* ── QR SHARE SECTION ── */
        .hero-qr{margin-top:28px;display:flex;flex-direction:column;align-items:center;gap:10px}
        .hero-qr-label{font-size:10px;font-weight:700;letter-spacing:1px;color:var(--text-3);text-transform:uppercase}
        .hero-qr-wrap{width:130px;height:130px;background:var(--surface);border:1px solid var(--border);border-radius:14px;overflow:hidden;display:flex;align-items:center;justify-content:center;position:relative}
        .hero-qr-wrap img{width:100%;height:100%;display:block}
        .hero-qr-sub{font-size:10px;color:var(--text-3);text-align:center;line-height:1.5}

        /* ── FORM SIDE ── */
        .donate-form-panel{background:var(--bg);display:flex;flex-direction:column;overflow-y:auto}
        .donate-form-inner{padding:48px 40px;flex:1;display:flex;flex-direction:column}
        .form-heading{font-family:'Space Grotesk',sans-serif;font-size:24px;font-weight:700;letter-spacing:-.5px;margin-bottom:4px}
        .form-subheading{font-size:13px;color:var(--text-3);margin-bottom:32px}
        .form-group{margin-bottom:20px}
        label{display:block;font-size:12px;font-weight:600;color:var(--text-2);margin-bottom:7px}

        input[type="text"],input[type="number"],textarea{
            width:100%;padding:11px 14px;background:var(--surface-2);border:1px solid var(--border);
            border-radius:var(--radius);color:var(--text);font-family:'Inter',sans-serif;font-size:14px;
            outline:none;transition:border-color .15s,box-shadow .15s;appearance:none;
        }
        input:focus,textarea:focus{border-color:var(--brand);box-shadow:0 0 0 3px var(--brand-glow);background:var(--surface-3)}
        input::placeholder,textarea::placeholder{color:var(--text-3)}
        textarea{resize:vertical;min-height:80px;line-height:1.6}

        /* ── AMOUNT ── */
        .amount-presets{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:10px}
        .preset-btn{padding:8px 14px;border-radius:var(--radius-sm);border:1px solid var(--border);background:var(--surface-2);color:var(--text-3);cursor:pointer;font-family:'Inter',sans-serif;font-size:13px;font-weight:600;transition:all .15s}
        .preset-btn:hover{border-color:var(--border-2);color:var(--text-2)}
        .preset-btn.selected{background:var(--brand);border-color:var(--brand);color:#fff;box-shadow:0 0 16px var(--brand-glow)}

        /* ── AMOUNT DISPLAY ── */
        .amount-display-wrap{position:relative}
        .amount-display-wrap .amount-prefix{
            position:absolute;left:14px;top:50%;transform:translateY(-50%);
            font-size:14px;font-weight:600;color:var(--text-2);pointer-events:none;
        }
        #donor-amount-display{padding-left:44px;font-weight:600}

        /* ── CHAR COUNTER ── */
        .char-counter-wrap{position:relative}
        #char-counter{
            position:absolute;bottom:10px;right:12px;
            font-size:11px;color:var(--text-3);
            pointer-events:none;background:var(--surface-2);padding:1px 4px;border-radius:4px;
        }
        #char-counter.near-limit{color:#fbbf24}
        #char-counter.at-limit{color:var(--red)}
        .char-counter-wrap textarea{padding-bottom:28px}

        /* ── EMOJI PICKER ── */
        .emoji-picker-label{font-size:12px;font-weight:600;color:var(--text-2);margin-bottom:8px;display:block}
        .emoji-picker-row{display:flex;gap:6px;flex-wrap:wrap}
        .emoji-opt{
            width:40px;height:40px;border-radius:var(--radius-sm);
            border:1px solid var(--border);background:var(--surface-2);
            cursor:pointer;font-size:20px;display:flex;align-items:center;justify-content:center;
            transition:all .15s;user-select:none;
        }
        .emoji-opt:hover{border-color:var(--border-2);background:var(--surface-3);transform:scale(1.1)}
        .emoji-opt.selected{border-color:var(--brand);background:rgba(124,108,252,.15);box-shadow:0 0 12px var(--brand-glow);transform:scale(1.1)}

        /* ── YT HINT ── */
        .yt-hint{font-size:11px;color:var(--text-3);margin-top:7px;display:flex;align-items:center;gap:6px}
        .yt-pill{background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.2);color:#f87171;padding:2px 8px;border-radius:4px;font-size:10px;font-weight:700;letter-spacing:.5px}

        /* ── SUBMIT BUTTON ── */
        .submit-btn{
            width:100%;padding:15px;border:none;border-radius:var(--radius);cursor:pointer;
            background:linear-gradient(135deg,var(--brand),#6356e8);color:#fff;
            font-family:'Inter',sans-serif;font-size:15px;font-weight:700;letter-spacing:-.2px;
            transition:all .2s;box-shadow:0 4px 20px var(--brand-glow),0 1px 0 rgba(255,255,255,.1) inset;
            display:flex;align-items:center;justify-content:center;gap:10px;
            position:relative;overflow:hidden;
        }
        .submit-btn:hover:not(:disabled){transform:translateY(-1px);box-shadow:0 8px 32px var(--brand-glow)}
        .submit-btn:disabled{opacity:.55;cursor:not-allowed}
        @keyframes spin{to{transform:rotate(360deg)}}
        .btn-spinner{
            width:18px;height:18px;border-radius:50%;
            border:2px solid rgba(255,255,255,.3);
            border-top-color:#fff;
            animation:spin .7s linear infinite;
            display:none;flex-shrink:0;
        }
        .submit-btn.loading .btn-spinner{display:block}
        .submit-btn.loading .btn-label{opacity:.8}

        /* ── ERROR / SUCCESS ── */
        .error-box{background:rgba(244,63,94,.08);border:1px solid rgba(244,63,94,.2);border-radius:var(--radius);padding:12px 16px;margin-bottom:20px;font-size:13px;color:#f43f5e;display:none}
        .error-box.visible{display:block}

        /* ── THANK YOU STATE ── */
        .success-box{display:none;text-align:center;padding:32px 20px;position:relative;overflow:hidden}
        .success-box.visible{display:block}

        /* Confetti particles */
        .confetti-wrap{position:absolute;top:0;left:0;right:0;bottom:0;pointer-events:none;overflow:hidden}
        .confetti-p{
            position:absolute;top:-10px;width:8px;height:8px;border-radius:2px;
            animation:confettiFall var(--dur,1.8s) var(--delay,0s) ease-in forwards;
            opacity:0;
        }
        @keyframes confettiFall{
            0%{transform:translateY(0) rotate(0deg);opacity:1}
            100%{transform:translateY(320px) rotate(720deg);opacity:0}
        }

        .ty-emoji{font-size:56px;line-height:1;margin-bottom:16px;display:inline-block;animation:bounceIn .5s cubic-bezier(.34,1.56,.64,1) both}
        @keyframes bounceIn{0%{transform:scale(0) rotate(-10deg);opacity:0}100%{transform:scale(1) rotate(0deg);opacity:1}}

        .ty-heading{font-family:'Space Grotesk',sans-serif;font-size:22px;font-weight:700;letter-spacing:-.5px;color:var(--text);margin-bottom:8px}
        .ty-message{font-size:14px;color:var(--brand-light);background:rgba(124,108,252,.08);border:1px solid rgba(124,108,252,.2);border-radius:var(--radius);padding:14px 16px;margin:12px 0;line-height:1.7;font-style:italic}
        .ty-sub{font-size:12px;color:var(--text-3);margin-bottom:20px;line-height:1.6}

        .ty-countdown{
            display:inline-flex;align-items:center;gap:6px;
            font-size:11px;color:var(--text-3);
            background:var(--surface-2);border:1px solid var(--border);
            border-radius:20px;padding:5px 14px;margin-bottom:20px;
        }
        .ty-countdown-num{font-weight:700;color:var(--text-2);min-width:16px;text-align:center}

        .donate-again-btn{margin-top:4px;padding:12px 28px;background:linear-gradient(135deg,var(--brand),#6356e8);border:none;color:#fff;border-radius:var(--radius);font-size:14px;font-weight:700;cursor:pointer;transition:all .2s;width:100%;box-shadow:0 4px 16px var(--brand-glow)}
        .donate-again-btn:hover{transform:translateY(-1px);box-shadow:0 6px 20px var(--brand-glow)}

        /* ── SSE CONN ── */
        #conn-status{display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:20px;font-size:11px;font-weight:600;letter-spacing:.2px;margin-bottom:24px}
        #conn-status.ok{background:rgba(34,211,160,.1);border:1px solid rgba(34,211,160,.25);color:var(--green)}
        #conn-status.err{background:rgba(244,63,94,.1);border:1px solid rgba(244,63,94,.25);color:var(--red)}
        #conn-status.wait{background:rgba(251,191,36,.08);border:1px solid rgba(251,191,36,.2);color:#fbbf24}
        .conn-dot{width:6px;height:6px;border-radius:50%;background:currentColor;animation:pulse 2s infinite}

        /* ── FORM FOOTER ── */
        .form-footer{
            margin-top:auto;padding-top:24px;
            display:flex;align-items:center;justify-content:center;gap:6px;
            font-size:11px;color:var(--text-3);
        }
        .form-footer .iconify{width:13px;height:13px;color:var(--text-3)}
        .form-footer a{color:var(--text-3);text-decoration:none;font-weight:600;transition:color .15s}
        .form-footer a:hover{color:var(--brand-light)}

        @media(max-width:900px){
            .donate-layout{grid-template-columns:1fr}
            .donate-hero{min-height:280px;padding:40px 24px}
            .donate-form-inner{padding:32px 24px}
        }
    </style>
</head>
<body>

<div class="donate-layout">

    <!-- Hero Side -->
    <div class="donate-hero">
        <div class="hero-avatar-wrap">
            <div class="hero-avatar" id="hero-avatar">
                @if($streamer->avatar)
                    <img src="{{ asset('storage/' . $streamer->avatar) }}" alt="{{ $streamer->display_name }}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;display:block" />
                @else
                    {{ strtoupper(substr($streamer->display_name, 0, 2)) }}
                @endif
            </div>
            <div class="hero-live"><span class="live-dot"></span> LIVE</div>
        </div>
        <div class="hero-name">{{ $streamer->display_name }}</div>
        @if($streamer->bio)
            <div class="hero-bio">{{ $streamer->bio }}</div>
        @endif

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

        <!-- QR Share -->
        <div class="hero-qr">
            <div class="hero-qr-label">Share ke teman</div>
            <div class="hero-qr-wrap">
                <img src="/{{ $streamer->slug }}/qr" alt="QR Donasi {{ $streamer->display_name }}" loading="lazy" />
            </div>
            <div class="hero-qr-sub">Scan untuk donasi langsung<br>ke {{ $streamer->display_name }}</div>
        </div>
    </div>

    <!-- Form Side -->
    <div class="donate-form-panel">
        <div class="donate-form-inner">

            <div id="conn-status" class="wait"><span class="conn-dot"></span> Memuat…</div>

            <div class="form-heading">Kirim Donasi</div>
            <div class="form-subheading">Support <strong>{{ $streamer->display_name }}</strong> sekarang</div>

            <div id="error-box"   class="error-box"></div>
            <div id="success-box" class="success-box"></div>

            <div id="donate-form">
                <div class="form-group">
                    <label>Nama kamu</label>
                    <input type="text" id="donor-name" placeholder="Nama atau username…" maxlength="60" />
                </div>

                <div class="form-group">
                    <label>Nominal donasi (min Rp {{ number_format($streamer->min_donation, 0, ',', '.') }})</label>
                    <div class="amount-presets">
                        <button class="preset-btn" onclick="setPreset(this,5000)">Rp 5K</button>
                        <button class="preset-btn" onclick="setPreset(this,10000)">Rp 10K</button>
                        <button class="preset-btn selected" onclick="setPreset(this,25000)">Rp 25K</button>
                        <button class="preset-btn" onclick="setPreset(this,50000)">Rp 50K</button>
                        <button class="preset-btn" onclick="setPreset(this,100000)">Rp 100K</button>
                    </div>
                    <div class="amount-display-wrap">
                        <span class="amount-prefix">Rp</span>
                        <input type="text" id="donor-amount-display" value="25.000" placeholder="Ketik nominal…" inputmode="numeric" autocomplete="off" />
                        <input type="hidden" id="donor-amount" value="25000" />
                    </div>
                </div>

                <div class="form-group">
                    <label>Pesan <span style="color:var(--text-3);font-weight:400">(opsional)</span></label>
                    <div class="char-counter-wrap">
                        <textarea id="donor-msg" placeholder="Tulis pesan untuk streamer…" maxlength="200"></textarea>
                        <span id="char-counter">0/200</span>
                    </div>
                </div>

                <div class="form-group">
                    <span class="emoji-picker-label">Pilih emoji donasi</span>
                    <div class="emoji-picker-row" id="emoji-picker">
                        <button type="button" class="emoji-opt selected" data-emoji="💝" onclick="selectEmoji(this)">💝</button>
                        <button type="button" class="emoji-opt" data-emoji="💸" onclick="selectEmoji(this)">💸</button>
                        <button type="button" class="emoji-opt" data-emoji="🎉" onclick="selectEmoji(this)">🎉</button>
                        <button type="button" class="emoji-opt" data-emoji="🔥" onclick="selectEmoji(this)">🔥</button>
                        <button type="button" class="emoji-opt" data-emoji="❤️" onclick="selectEmoji(this)">❤️</button>
                        <button type="button" class="emoji-opt" data-emoji="🎮" onclick="selectEmoji(this)">🎮</button>
                        <button type="button" class="emoji-opt" data-emoji="👏" onclick="selectEmoji(this)">👏</button>
                        <button type="button" class="emoji-opt" data-emoji="🌟" onclick="selectEmoji(this)">🌟</button>
                    </div>
                    <input type="hidden" id="selected-emoji" value="💝" />
                </div>

                @if($streamer->yt_enabled)
                <div class="form-group">
                    <label>Request YouTube <span style="color:var(--text-3);font-weight:400">(opsional)</span></label>
                    <input type="text" id="donor-yt" placeholder="https://youtube.com/watch?v=…" />
                    <div class="yt-hint">
                        <span class="yt-pill">YT</span>
                        Tempel link YouTube untuk request lagu atau video
                    </div>
                </div>
                @endif

                <button class="submit-btn" id="submit-btn" onclick="submitDonation()">
                    <span class="btn-spinner" id="btn-spinner"></span>
                    <span class="btn-label">Kirim Donasi Sekarang</span>
                </button>
            </div>

            {{-- Form footer --}}
            <div class="form-footer">
                <span class="iconify" data-icon="solar:gamepad-bold-duotone"></span>
                Powered by <a href="#" tabindex="-1">StreamDonate</a>
            </div>
        </div>
    </div>
</div>

<script>
const SLUG      = '{{ $streamer->slug }}';
const SSE_URL   = '/{{ $streamer->slug }}/sse?key={{ $streamer->api_key }}';
const STORE_URL = '/{{ $streamer->slug }}/donate';
const CSRF      = document.querySelector('meta[name="csrf-token"]')?.content || '';
const MIN_AMT   = {{ $streamer->min_donation }};

// ── Avatar initial color (hash from name) — only when no image ──
(function () {
    const avatarEl = document.getElementById('hero-avatar');
    if (!avatarEl || avatarEl.querySelector('img')) return; // skip if avatar image present
    const name = '{{ addslashes($streamer->display_name) }}';
    const colors = [
        ['#7c6cfc','#a855f7'],['#f97316','#f43f5e'],['#22d3a0','#06b6d4'],
        ['#fbbf24','#f97316'],['#a855f7','#6356e8'],['#22d3a0','#7c6cfc'],
    ];
    let hash = 0;
    for (let i = 0; i < name.length; i++) hash = name.charCodeAt(i) + ((hash << 5) - hash);
    const pair = colors[Math.abs(hash) % colors.length];
    avatarEl.style.background = `linear-gradient(135deg,${pair[0]},${pair[1]})`;
})();

// ── SSE Stats ──
function connectSSE() {
    const connEl = document.getElementById('conn-status');
    const es = new EventSource(SSE_URL);
    es.onopen = () => {
        connEl.className = 'ok';
        connEl.innerHTML = '<span class="conn-dot"></span> Terhubung';
    };
    es.addEventListener('stats', (e) => {
        try {
            const parsed = JSON.parse(e.data);
            if (parsed) applyStats(parsed);
        } catch(err) {
            // Stats parse gagal — biarkan tampilan tetap dengan data terakhir yang valid
            console.warn('Stats parse error:', err);
        }
    });
    es.onerror = () => {
        connEl.className = 'err';
        connEl.innerHTML = '<span class="conn-dot"></span> Reconnecting…';
        es.close();
        setTimeout(connectSSE, 4000);
    };
}

function applyStats(data) {
    document.getElementById('hero-total').textContent  = formatRp(data.total  || 0);
    document.getElementById('hero-donors').textContent = data.donors || 0;
    document.getElementById('hero-count').textContent  = data.count  || 0;
}

function formatRp(n) {
    if (n >= 1000000) return 'Rp ' + (n/1000000).toFixed(1).replace('.0','') + 'Jt';
    if (n >= 1000)    return 'Rp ' + Math.round(n/1000) + 'K';
    return 'Rp ' + n;
}

// ── Preset buttons ──
function setPreset(btn, val) {
    document.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    document.getElementById('donor-amount').value = val;
    document.getElementById('donor-amount-display').value = val.toLocaleString('id-ID');
}

// ── Amount formatter ──
document.addEventListener('DOMContentLoaded', function () {
    const displayInput = document.getElementById('donor-amount-display');
    const hiddenInput  = document.getElementById('donor-amount');

    displayInput.addEventListener('input', function () {
        // Strip non-digits
        const raw = this.value.replace(/\D/g, '');
        const num = parseInt(raw, 10) || 0;
        // Format with thousand separator
        this.value = num ? num.toLocaleString('id-ID') : '';
        hiddenInput.value = num;
        // Deselect presets if manual input
        document.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('selected'));
        // Re-select if matches a preset
        document.querySelectorAll('.preset-btn').forEach(b => {
            const match = b.getAttribute('onclick').match(/\d+/);
            if (match && parseInt(match[0]) === num) b.classList.add('selected');
        });
    });

    displayInput.addEventListener('blur', function () {
        const num = parseInt(hiddenInput.value, 10) || 0;
        this.value = num ? num.toLocaleString('id-ID') : '';
    });

    // ── Char counter ──
    const msgArea  = document.getElementById('donor-msg');
    const counter  = document.getElementById('char-counter');
    if (msgArea && counter) {
        msgArea.addEventListener('input', function () {
            const len = this.value.length;
            counter.textContent = len + '/200';
            counter.className = len >= 200 ? 'at-limit' : (len >= 160 ? 'near-limit' : '');
        });
    }
});

// ── Emoji picker ──
function selectEmoji(btn) {
    document.querySelectorAll('.emoji-opt').forEach(b => b.classList.remove('selected'));
    btn.classList.add('selected');
    document.getElementById('selected-emoji').value = btn.dataset.emoji;
}

// ── Submit ──
async function submitDonation() {
    const btn       = document.getElementById('submit-btn');
    const spinner   = document.getElementById('btn-spinner');
    const errBox    = document.getElementById('error-box');
    const successBox = document.getElementById('success-box');
    errBox.className    = 'error-box';
    successBox.className = 'success-box';

    const name   = document.getElementById('donor-name').value.trim();
    const amount = parseInt(document.getElementById('donor-amount').value);
    const msg    = document.getElementById('donor-msg')?.value.trim() || '';
    const ytUrl  = document.getElementById('donor-yt')?.value.trim() || '';
    const emoji  = document.getElementById('selected-emoji').value || '💝';

    if (!name) { showErr('Nama wajib diisi.'); return; }
    if (!amount || amount < MIN_AMT) {
        showErr('Minimum donasi Rp {{ number_format($streamer->min_donation, 0, ',', '.') }}');
        return;
    }

    btn.disabled = true;
    btn.classList.add('loading');

    try {
        const res = await fetch(STORE_URL, {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':CSRF,'Accept':'application/json'},
            body: JSON.stringify({ name, amount, msg: msg || null, yt_url: ytUrl || null, emoji }),
        });

        // Coba parse response sebagai JSON
        // Jika server mengembalikan HTML (misal halaman error 500), res.json() akan throw
        let data;
        try {
            data = await res.json();
        } catch (parseErr) {
            // Server mengembalikan non-JSON (HTML error page, bukan network error)
            showErr('Terjadi kesalahan pada server. Mohon coba lagi dalam beberapa saat.');
            return;
        }

        if (res.ok && data.success) {
            document.getElementById('donate-form').style.display = 'none';
            showThankYou(emoji, data.message);
        } else {
            const msgs = data.errors
                ? Object.values(data.errors).flat().join('<br>')
                : (data.message || 'Gagal mengirim donasi. Coba lagi.');
            showErr(msgs);
        }
    } catch(e) {
        // Network error — tidak bisa terhubung ke server sama sekali
        showErr('Tidak dapat terhubung ke server. Periksa koneksi internet Anda dan coba lagi.');
    } finally {
        btn.disabled = false;
        btn.classList.remove('loading');
    }
}

function showErr(msg) {
    const el = document.getElementById('error-box');
    el.innerHTML = msg;
    el.className = 'error-box visible';
}

function resetForm() {
    clearInterval(countdownInterval);
    document.getElementById('donor-name').value   = '';
    document.getElementById('donor-amount').value = 25000;
    document.getElementById('donor-amount-display').value = '25.000';
    if (document.getElementById('donor-msg'))  document.getElementById('donor-msg').value  = '';
    if (document.getElementById('donor-yt'))   document.getElementById('donor-yt').value   = '';

    // Reset char counter
    const counter = document.getElementById('char-counter');
    if (counter) { counter.textContent = '0/200'; counter.className = ''; }

    // Reset emoji picker
    document.querySelectorAll('.emoji-opt').forEach(b => b.classList.remove('selected'));
    const firstEmoji = document.querySelector('.emoji-opt[data-emoji="💝"]');
    if (firstEmoji) { firstEmoji.classList.add('selected'); }
    document.getElementById('selected-emoji').value = '💝';

    // Reset presets
    document.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('selected'));
    const defaultPreset = document.querySelector('.preset-btn[onclick*="25000"]');
    if (defaultPreset) defaultPreset.classList.add('selected');

    document.getElementById('success-box').className = 'success-box';
    document.getElementById('error-box').className   = 'error-box';
    document.getElementById('donate-form').style.display = '';
}

// ── Thank You state with confetti + countdown ──
var countdownInterval = null;

function buildConfetti() {
    var colors = ['#7c6cfc','#f97316','#22d3a0','#fbbf24','#f43f5e','#a855f7','#38bdf8'];
    var html = '<div class="confetti-wrap">';
    for (var i = 0; i < 28; i++) {
        var left  = (5 + Math.random() * 90).toFixed(1) + '%';
        var dur   = (1.2 + Math.random() * 1.2).toFixed(2) + 's';
        var delay = (Math.random() * 0.8).toFixed(2) + 's';
        var color = colors[i % colors.length];
        var rot   = Math.round(Math.random() * 360);
        html += '<div class="confetti-p" style="left:' + left +
                ';background:' + color +
                ';--dur:' + dur +
                ';--delay:' + delay +
                ';transform:rotate(' + rot + 'deg)"></div>';
    }
    return html + '</div>';
}

function showThankYou(emoji, serverMsg) {
    var successBox = document.getElementById('success-box');
    var tyMsg      = serverMsg || '{{ addslashes($streamer->thank_you_message ?? 'Terima kasih atas donasinya!') }}';

    successBox.innerHTML =
        buildConfetti() +
        '<div class="ty-emoji">' + emoji + '</div>' +
        '<div class="ty-heading">Donasi Terkirim!</div>' +
        '<div class="ty-message">' + escHtml(tyMsg) + '</div>' +
        '<div class="ty-sub">Donasi kamu sudah diterima dan akan segera ditampilkan di stream.</div>' +
        '<div class="ty-countdown"><span>Kembali ke form dalam</span>' +
        '<span class="ty-countdown-num" id="countdown-num">30</span>' +
        '<span>detik</span></div>' +
        '<button class="donate-again-btn" onclick="resetForm()">Donasi Lagi</button>';

    successBox.className = 'success-box visible';

    // Countdown 30s then auto-reset
    var secs = 30;
    clearInterval(countdownInterval);
    countdownInterval = setInterval(function () {
        secs--;
        var el = document.getElementById('countdown-num');
        if (el) el.textContent = secs;
        if (secs <= 0) {
            clearInterval(countdownInterval);
            resetForm();
        }
    }, 1000);
}

function escHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

connectSSE();
</script>
</body>
</html>
