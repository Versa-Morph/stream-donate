<x-app-layout>
@push('styles')
<style>
.dash-wrap{padding:28px 32px;max-width:1280px;margin:0 auto}
.dash-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;gap:16px}
.dash-title{font-family:'Space Grotesk',sans-serif;font-size:22px;font-weight:700;letter-spacing:-.5px}
.dash-sub{font-size:13px;color:var(--text-3);margin-top:3px}

/* ── Stats Grid ── */
.stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px}
.stat-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:20px;position:relative;overflow:hidden;transition:border-color .2s}
.stat-card:hover{border-color:var(--border-2)}
.stat-card::after{content:'';position:absolute;bottom:0;left:0;right:0;height:2px;background:var(--c,var(--brand));opacity:.6}
.stat-icon{
    width:38px;height:38px;border-radius:var(--radius-sm);
    display:flex;align-items:center;justify-content:center;
    margin-bottom:16px;
    border:1px solid color-mix(in srgb,var(--c,var(--brand)) 25%,transparent);
    background:color-mix(in srgb,var(--c,var(--brand)) 12%,transparent);
}
.stat-icon .iconify{width:20px;height:20px;color:var(--c,var(--brand))}
.stat-label{font-size:11px;color:var(--text-3);font-weight:600;letter-spacing:.5px;text-transform:uppercase;margin-bottom:6px}
.stat-value{font-family:'Space Grotesk',sans-serif;font-size:26px;font-weight:700;letter-spacing:-1px;color:var(--text);line-height:1;margin-bottom:6px}
.stat-sub{font-size:11px;color:var(--text-3)}

/* ── Main layout ── */
.dash-content{display:grid;grid-template-columns:1fr 360px;gap:16px;align-items:start}

/* ── Cards ── */
.dash-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:22px;display:flex;flex-direction:column}
.card-title-row{display:flex;align-items:center;gap:10px;margin-bottom:18px}
.card-title{font-family:'Space Grotesk',sans-serif;font-size:15px;font-weight:700;letter-spacing:-.3px}
.count-chip{background:var(--surface-3);border:1px solid var(--border);color:var(--text-3);padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}

/* ── History list ── */
.history-list{display:flex;flex-direction:column;gap:8px;max-height:520px;overflow-y:auto}
.history-item{display:flex;align-items:center;gap:12px;padding:12px 14px;background:var(--surface-2);border:1px solid var(--border);border-radius:var(--radius);transition:border-color .15s}
.history-item:hover{border-color:var(--border-2)}
.h-avatar{width:38px;height:38px;border-radius:12px;background:linear-gradient(135deg,var(--surface-3),var(--surface));border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0}
.h-info{flex:1;min-width:0}
.h-name{font-size:13px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.h-msg{font-size:11px;color:var(--text-3);margin-top:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.h-meta{text-align:right;flex-shrink:0}
.h-amount{font-family:'Space Grotesk',sans-serif;font-size:15px;font-weight:700;color:var(--orange)}
.h-time{font-size:10px;color:var(--text-3);margin-top:2px}

/* ── Donation size badges ── */
.h-badge{display:inline-block;font-size:9px;font-weight:700;letter-spacing:.4px;padding:2px 6px;border-radius:4px;margin-top:3px;text-transform:uppercase}
.h-badge.tier-sm{background:rgba(96,96,120,.15);border:1px solid rgba(96,96,120,.25);color:var(--text-3)}
.h-badge.tier-md{background:rgba(124,108,252,.12);border:1px solid rgba(124,108,252,.25);color:var(--brand-light)}
.h-badge.tier-lg{background:rgba(249,115,22,.12);border:1px solid rgba(249,115,22,.25);color:var(--orange)}

/* ── Leaderboard ── */
.leader-list{display:flex;flex-direction:column;gap:8px}
.leader-item{display:flex;align-items:center;gap:10px;padding:12px;background:var(--surface-2);border:1px solid var(--border);border-radius:var(--radius)}
.leader-item.rank-1{border-color:rgba(251,191,36,.25);background:rgba(251,191,36,.05)}
.leader-item.rank-2{border-color:rgba(160,160,180,.18)}
.leader-item.rank-3{border-color:rgba(205,127,50,.22)}
.rank-num{width:26px;text-align:center;font-size:16px;flex-shrink:0}
.rank-num.plain{font-family:'Space Grotesk',sans-serif;font-size:12px;font-weight:700;color:var(--text-3)}
.leader-emoji{width:32px;height:32px;border-radius:10px;background:var(--surface-3);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0}
.leader-name{font-size:13px;font-weight:600;flex:1;min-width:0;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.leader-right{text-align:right;flex-shrink:0}
.leader-total{font-family:'Space Grotesk',sans-serif;font-size:14px;font-weight:700;color:var(--yellow)}
.leader-count{font-size:10px;color:var(--text-3);margin-top:1px}

/* ── Bar Chart ── */
.chart-section{margin-bottom:20px}
.chart-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:20px}
.chart-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px}
.chart-title{font-family:'Space Grotesk',sans-serif;font-size:14px;font-weight:700;letter-spacing:-.3px}
.chart-subtitle{font-size:11px;color:var(--text-3)}
#daily-chart{width:100%;height:120px;display:block}

/* ── OBS Widgets ── */
.widgets-section{margin-top:20px}
.section-label{font-size:11px;font-weight:700;letter-spacing:1px;color:var(--text-3);text-transform:uppercase;margin-bottom:4px}
.section-sub{font-size:12px;color:var(--text-3);margin-bottom:14px}
.widget-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
.widget-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:20px;transition:border-color .2s}
.widget-card:hover{border-color:var(--border-2)}
.widget-icon{
    width:36px;height:36px;border-radius:var(--radius-sm);
    display:flex;align-items:center;justify-content:center;
    margin-bottom:12px;
    background:var(--surface-2);border:1px solid var(--border);
}
.widget-icon .iconify{width:20px;height:20px;color:var(--brand-light)}
.widget-name{font-family:'Space Grotesk',sans-serif;font-size:13px;font-weight:700;margin-bottom:4px}
.widget-desc{font-size:11px;color:var(--text-3);margin-bottom:14px;line-height:1.6}
.widget-url-row{display:flex;gap:6px}
.widget-url-input{flex:1;font-size:11px;padding:8px 10px;background:var(--surface-2);border:1px solid var(--border);border-radius:var(--radius-sm);color:var(--text-3);font-family:monospace;outline:none;min-width:0}
.copy-btn{padding:8px 12px;background:var(--surface-3);border:1px solid var(--border);border-radius:var(--radius-sm);color:var(--text-2);cursor:pointer;font-size:11px;font-weight:700;transition:all .15s;white-space:nowrap;flex-shrink:0;display:inline-flex;align-items:center;gap:5px}
.copy-btn:hover{background:var(--brand);border-color:var(--brand);color:#fff}
.copy-btn .iconify{width:13px;height:13px}

.empty-state{padding:40px 20px;text-align:center;color:var(--text-3);font-size:13px}

/* ── Header buttons ── */
.header-btn{
    display:inline-flex;align-items:center;gap:8px;
    padding:9px 16px;border-radius:var(--radius-sm);
    font-size:13px;font-weight:600;cursor:pointer;
    transition:all .15s;text-decoration:none;border:none;
    font-family:'Inter',sans-serif;
}
.header-btn-ghost{border:1px solid var(--border);color:var(--text-2);background:var(--surface-2)}
.header-btn-ghost:hover{border-color:var(--border-2);color:var(--text)}
.header-btn-brand{background:linear-gradient(135deg,var(--brand),#6356e8);color:#fff;box-shadow:0 4px 16px var(--brand-glow)}
.header-btn-brand:hover{transform:translateY(-1px);box-shadow:0 6px 20px var(--brand-glow)}
.header-btn .iconify{width:15px;height:15px}

/* ── QR preview ── */
.qr-preview-wrap{width:80px;height:80px;margin:0 auto 10px;background:var(--surface-2);border:1px solid var(--border);border-radius:var(--radius-sm);overflow:hidden;display:flex;align-items:center;justify-content:center}
.qr-preview-wrap img{width:100%;height:100%;display:block}

/* ── Test Alert button ── */
.test-alert-btn{display:inline-flex;align-items:center;gap:8px;padding:9px 16px;border-radius:var(--radius-sm);font-size:13px;font-weight:600;cursor:pointer;transition:all .2s;border:none;font-family:'Inter',sans-serif;background:linear-gradient(135deg,var(--orange),#ea580c);color:#fff;box-shadow:0 4px 16px rgba(249,115,22,.3)}
.test-alert-btn:hover{transform:translateY(-1px);box-shadow:0 6px 22px rgba(249,115,22,.4)}
.test-alert-btn:active{transform:translateY(0)}
.test-alert-btn:disabled{opacity:.6;cursor:not-allowed;transform:none}
.test-alert-btn .iconify{width:15px;height:15px}
.btn-spin{width:13px;height:13px;border:2px solid rgba(255,255,255,.3);border-top-color:#fff;border-radius:50%;display:none}
.test-alert-btn.loading .btn-spin{display:block;animation:btnSpin .6s linear infinite}
.test-alert-btn.loading .btn-icon-wrap{display:none}
.test-alert-toast{position:fixed;bottom:28px;left:50%;transform:translateX(-50%) translateY(16px);background:var(--surface-2);border:1px solid var(--border);border-radius:var(--radius-lg);padding:12px 20px;font-size:13px;font-weight:600;color:var(--text);box-shadow:0 12px 40px rgba(0,0,0,.5);z-index:9999;opacity:0;transition:all .3s;pointer-events:none;white-space:nowrap;display:flex;align-items:center;gap:10px}
.test-alert-toast.show{opacity:1;transform:translateX(-50%) translateY(0)}
.test-alert-toast .toast-emoji{font-size:18px}

/* ── SSE notification badge ── */
.live-badge{
    display:inline-flex;align-items:center;gap:6px;
    padding:5px 12px;border-radius:20px;font-size:11px;font-weight:700;
    background:rgba(34,211,160,.1);border:1px solid rgba(34,211,160,.3);
    color:var(--green);letter-spacing:.3px;margin-left:8px;
    animation:fadeInBadge .3s ease;
}
.live-badge.hidden{display:none}
.live-dot{width:6px;height:6px;border-radius:50%;background:var(--green);animation:pulseDot 1.2s ease-in-out infinite}
@keyframes pulseDot{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.4;transform:scale(.6)}}
@keyframes fadeInBadge{from{opacity:0;transform:translateY(-4px)}to{opacity:1;transform:translateY(0)}}

.donation-flash{animation:flashItem .6s ease}
@keyframes flashItem{0%{background:rgba(124,108,252,.18);border-color:rgba(124,108,252,.5)}100%{background:var(--surface-2);border-color:var(--border)}}

.test-alert-toast .toast-emoji{font-size:18px}
@media(max-width:1100px){.stats-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:900px){.dash-content{grid-template-columns:1fr}.widget-grid{grid-template-columns:1fr 1fr}}
@media(max-width:600px){.widget-grid{grid-template-columns:1fr}.dash-header{flex-direction:column;align-items:flex-start}}
</style>
@endpush

<div class="dash-wrap">

    {{-- Header --}}
    <div class="dash-header">
        <div>
            <div class="dash-title">Dashboard</div>
            <div class="dash-sub">Selamat datang, {{ auth()->user()->name }}</div>
        </div>
        <div style="display:flex;gap:10px;flex-shrink:0;align-items:center">
            {{-- Test Alert --}}
            <button class="test-alert-btn" id="test-alert-btn" onclick="sendTestAlert()" title="Kirim test alert ke OBS overlay">
                <span class="btn-spin"></span>
                <span class="btn-icon-wrap" style="display:inline-flex;align-items:center;gap:8px">
                    <span class="iconify" data-icon="solar:play-circle-bold-duotone"></span>
                    Test Alert
                </span>
            </button>
            {{-- Copy Link Donasi --}}
            <button class="header-btn header-btn-ghost"
                onclick="copyText('{{ url('/'.$streamer->slug) }}', 'Link donasi')"
                title="Copy link form donasi">
                <span class="iconify" data-icon="solar:copy-bold-duotone"></span>
                Copy Link
            </button>
            <a href="{{ route('donate.show', $streamer->slug) }}" target="_blank" class="header-btn header-btn-ghost">
                <span class="iconify" data-icon="solar:heart-send-bold-duotone"></span>
                Form Donasi
                <span class="iconify" data-icon="solar:arrow-right-up-bold" style="width:11px;height:11px;opacity:.5"></span>
            </a>
            <a href="{{ route('streamer.settings') }}" class="header-btn header-btn-brand">
                <span class="iconify" data-icon="solar:settings-bold-duotone"></span>
                Settings
            </a>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="stats-grid">
        <div class="stat-card" style="--c:var(--brand)">
            <div class="stat-icon">
                <span class="iconify" data-icon="solar:wallet-money-bold-duotone"></span>
            </div>
            <div class="stat-label">Total Terkumpul</div>
            <div class="stat-value" id="stat-total">Rp {{ number_format($stats['total'], 0, ',', '.') }}</div>
            <div class="stat-sub" id="stat-count">{{ $stats['count'] }} donasi masuk</div>
        </div>
        <div class="stat-card" style="--c:var(--green)">
            <div class="stat-icon">
                <span class="iconify" data-icon="solar:heart-bold-duotone"></span>
            </div>
            <div class="stat-label">Total Donatur</div>
            <div class="stat-value" id="stat-donors">{{ $stats['donors'] }}</div>
            <div class="stat-sub">donatur unik</div>
        </div>
        <div class="stat-card" style="--c:var(--orange)">
            <div class="stat-icon">
                <span class="iconify" data-icon="solar:cup-star-bold-duotone"></span>
            </div>
            <div class="stat-label">Donasi Terbesar</div>
            <div class="stat-value" id="stat-biggest">
                @if($stats['biggest'])
                    Rp {{ number_format($stats['biggest']['amount'], 0, ',', '.') }}
                @else
                    Rp 0
                @endif
            </div>
            <div class="stat-sub" id="stat-biggest-name">{{ $stats['biggest']['name'] ?? '-' }}</div>
        </div>
        <div class="stat-card" style="--c:var(--purple)">
            <div class="stat-icon">
                <span class="iconify" data-icon="solar:target-bold-duotone"></span>
            </div>
            <div class="stat-label">Milestone</div>
            <div class="stat-value" id="stat-milestone">{{ $stats['milestone']['reached'] ? '100%' : round($stats['milestone']['target'] > 0 ? min(100, $stats['milestone']['current'] / $stats['milestone']['target'] * 100) : 0) . '%' }}</div>
            <div class="stat-sub">{{ $stats['milestone']['title'] }}</div>
        </div>
    </div>

    {{-- Bar Chart — 7 Hari Terakhir --}}
    <div class="chart-section">
        <div class="chart-card">
            <div class="chart-header">
                <div>
                    <div class="chart-title">Donasi 7 Hari Terakhir</div>
                    <div class="chart-subtitle">Total nominal per hari</div>
                </div>
            </div>
            <canvas id="daily-chart"></canvas>
        </div>
    </div>

    {{-- Main content --}}
    <div class="dash-content">

        {{-- Donation History --}}
        <div class="dash-card">
            <div class="card-title-row">
                <span class="iconify" data-icon="solar:history-bold-duotone" style="width:18px;height:18px;color:var(--brand-light)"></span>
                <div class="card-title">Riwayat Donasi</div>
                <span class="count-chip" id="history-count">{{ $donations->count() }}</span>
                <span class="live-badge hidden" id="live-badge">
                    <span class="live-dot"></span>DONASI BARU
                </span>
                <a href="{{ route('streamer.reports') }}" style="margin-left:auto;font-size:12px;color:var(--brand-light)">Lihat laporan →</a>
            </div>
            <div class="history-list" id="history-list">
                @forelse($donations as $d)
                    @php
                        $tier = $d->amount < 10000 ? 'tier-sm' : ($d->amount <= 50000 ? 'tier-md' : 'tier-lg');
                        $tierLabel = $d->amount < 10000 ? 'Kecil' : ($d->amount <= 50000 ? 'Medium' : 'Big');
                    @endphp
                    <div class="history-item">
                        <div class="h-avatar">{{ $d->emoji }}</div>
                        <div class="h-info">
                            <div class="h-name">{{ $d->name }}</div>
                            @if($d->message)
                                <div class="h-msg">{{ $d->message }}</div>
                            @elseif($d->yt_url)
                                <div class="h-msg" style="color:#f87171;display:flex;align-items:center;gap:4px">
                                    <span class="iconify" style="width:11px;height:11px" data-icon="solar:play-bold"></span>
                                    YouTube Request
                                </div>
                            @endif
                            <span class="h-badge {{ $tier }}">{{ $tierLabel }}</span>
                        </div>
                        <div class="h-meta">
                            <div class="h-amount">{{ $d->formatted_amount }}</div>
                            <div class="h-time">{{ $d->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">Belum ada donasi masuk</div>
                @endforelse
            </div>
        </div>

        {{-- Leaderboard --}}
        <div class="dash-card">
            <div class="card-title-row">
                <span class="iconify" data-icon="solar:ranking-bold-duotone" style="width:18px;height:18px;color:var(--yellow)"></span>
                <div class="card-title">{{ $streamer->leaderboard_title }}</div>
            </div>
            <div class="leader-list">
                @php $medals = ['🥇','🥈','🥉']; @endphp
                @forelse($stats['leaderboard'] as $i => $item)
                    <div class="leader-item {{ $i < 3 ? 'rank-'.($i+1) : '' }}">
                        <div class="rank-num {{ $i >= 3 ? 'plain' : '' }}">{{ $i < 3 ? $medals[$i] : ($i+1) }}</div>
                        <div class="leader-emoji">{{ $item['emoji'] }}</div>
                        <div class="leader-name">{{ $item['name'] }}</div>
                        <div class="leader-right">
                            <div class="leader-total">Rp {{ number_format($item['total'], 0, ',', '.') }}</div>
                            <div class="leader-count">{{ $item['count'] }}x donasi</div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">Belum ada data</div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- OBS Widget URLs --}}
    <div class="widgets-section">
        <div class="section-label">URL Widget OBS</div>
        <div class="section-sub">Copy URL berikut, lalu paste di OBS → Browser Source</div>
        <div class="widget-grid">
            @php
                $baseUrl = config('app.url');
                $key     = $streamer->api_key;
                $slug    = $streamer->slug;
            @endphp
            <div class="widget-card">
                <div class="widget-icon" style="background:rgba(124,108,252,.1);border-color:rgba(124,108,252,.2)">
                    <span class="iconify" data-icon="solar:bell-bold-duotone" style="color:var(--brand-light)"></span>
                </div>
                <div class="widget-name">Alert Donasi</div>
                <div class="widget-desc">Popup alert saat donasi masuk. Tambahkan sebagai Browser Source di OBS.</div>
                <div class="widget-url-row">
                    <input class="widget-url-input" readonly value="{{ $baseUrl }}/{{ $slug }}/obs/overlay?key={{ $key }}" id="url-overlay" />
                    <button class="copy-btn" onclick="copyText(document.getElementById('url-overlay').value,'URL Overlay')">
                        <span class="iconify" data-icon="solar:copy-bold-duotone"></span>Copy
                    </button>
                </div>
            </div>
            <div class="widget-card">
                <div class="widget-icon" style="background:rgba(251,191,36,.1);border-color:rgba(251,191,36,.2)">
                    <span class="iconify" data-icon="solar:ranking-bold-duotone" style="color:var(--yellow)"></span>
                </div>
                <div class="widget-name">Leaderboard</div>
                <div class="widget-desc">Panel top donatur real-time. Bisa ditaruh di sisi kanan layar.</div>
                <div class="widget-url-row">
                    <input class="widget-url-input" readonly value="{{ $baseUrl }}/{{ $slug }}/obs/leaderboard?key={{ $key }}" id="url-lb" />
                    <button class="copy-btn" onclick="copyText(document.getElementById('url-lb').value,'URL Leaderboard')">
                        <span class="iconify" data-icon="solar:copy-bold-duotone"></span>Copy
                    </button>
                </div>
            </div>
            <div class="widget-card">
                <div class="widget-icon" style="background:rgba(34,211,160,.1);border-color:rgba(34,211,160,.2)">
                    <span class="iconify" data-icon="solar:target-bold-duotone" style="color:var(--green)"></span>
                </div>
                <div class="widget-name">Milestone Bar</div>
                <div class="widget-desc">Progress bar target donasi. Bisa ditaruh di bagian bawah layar.</div>
                <div class="widget-url-row">
                    <input class="widget-url-input" readonly value="{{ $baseUrl }}/{{ $slug }}/obs/milestone?key={{ $key }}" id="url-ms" />
                    <button class="copy-btn" onclick="copyText(document.getElementById('url-ms').value,'URL Milestone')">
                        <span class="iconify" data-icon="solar:copy-bold-duotone"></span>Copy
                    </button>
                </div>
            </div>
            <div class="widget-card">
                <div class="widget-icon" style="background:rgba(168,85,247,.1);border-color:rgba(168,85,247,.2)">
                    <span class="iconify" data-icon="solar:qr-code-bold-duotone" style="color:var(--purple)"></span>
                </div>
                <div class="widget-name">QR Donasi</div>
                <div class="widget-desc">QR code yang bisa di-scan penonton untuk langsung ke form donasi kamu.</div>
                <div class="qr-preview-wrap">
                    <img src="{{ $baseUrl }}/{{ $slug }}/qr" alt="QR {{ $streamer->display_name }}" loading="lazy" />
                </div>
                <div class="widget-url-row">
                    <input class="widget-url-input" readonly value="{{ $baseUrl }}/{{ $slug }}/obs/qr" id="url-qr" />
                    <button class="copy-btn" onclick="copyText(document.getElementById('url-qr').value,'URL QR Widget')">
                        <span class="iconify" data-icon="solar:copy-bold-duotone"></span>Copy
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Test Alert Toast --}}
<div class="test-alert-toast" id="test-alert-toast">
    <span class="toast-emoji" id="toast-emoji">🎉</span>
    <span id="toast-msg">Test alert dikirim!</span>
</div>

@push('scripts')
<script>
// ── Test Alert ──
function sendTestAlert() {
    const btn   = document.getElementById('test-alert-btn');
    const toast = document.getElementById('test-alert-toast');
    if (btn.classList.contains('loading')) return;

    btn.classList.add('loading');
    btn.disabled = true;

    fetch('{{ route('streamer.test-alert') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) {
            document.getElementById('toast-emoji').textContent = data.emoji;
            document.getElementById('toast-msg').textContent =
                data.emoji + ' ' + data.name + ' • Rp ' + Number(data.amount).toLocaleString('id-ID') + ' — dikirim ke OBS!';
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3500);
        }
    })
    .catch(() => {
        document.getElementById('toast-msg').textContent = 'Gagal mengirim test alert.';
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    })
    .finally(() => {
        btn.classList.remove('loading');
        btn.disabled = false;
    });
}
// ── Bar Chart: 7 Hari Terakhir ──
(function () {
    const data = @json($dailySummary);
    const canvas = document.getElementById('daily-chart');
    if (!canvas) return;

    const dpr = window.devicePixelRatio || 1;
    const W   = canvas.offsetWidth || canvas.parentElement.offsetWidth;
    const H   = 120;
    canvas.width  = W * dpr;
    canvas.height = H * dpr;
    canvas.style.width  = W + 'px';
    canvas.style.height = H + 'px';

    const ctx = canvas.getContext('2d');
    ctx.scale(dpr, dpr);

    const maxVal = Math.max(...data.map(d => d.total), 1);
    const barW   = Math.floor((W - 60) / data.length) - 6;
    const barAreaH = H - 36;
    const startX = 30;

    // Draw bars
    data.forEach(function (d, i) {
        const x   = startX + i * ((W - 60) / data.length);
        const pct = d.total / maxVal;
        const bH  = Math.max(pct * barAreaH, d.total > 0 ? 3 : 1);
        const y   = barAreaH - bH + 8;

        // Bar bg
        ctx.fillStyle = 'rgba(255,255,255,.04)';
        ctx.beginPath();
        ctx.roundRect(x, 8, barW, barAreaH, 4);
        ctx.fill();

        if (d.total > 0) {
            // Gradient bar
            const grad = ctx.createLinearGradient(x, y + bH, x, y);
            grad.addColorStop(0, 'rgba(124,108,252,.6)');
            grad.addColorStop(1, 'rgba(124,108,252,1)');
            ctx.fillStyle = grad;
            ctx.beginPath();
            ctx.roundRect(x, y, barW, bH, 4);
            ctx.fill();
        }

        // Date label
        ctx.fillStyle = 'rgba(96,96,120,.9)';
        ctx.font = '9px Inter, sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText(d.date, x + barW / 2, H - 4);
    });
})();

// ── SSE Live Feed ──
(function () {
    const SLUG    = '{{ $streamer->slug }}';
    const API_KEY = '{{ $streamer->api_key }}';
    const SSE_URL = '/' + SLUG + '/sse?key=' + API_KEY;

    var es = null;
    var liveCount = {{ $donations->count() }};
    var badgeTimer = null;

    function formatRp(amount) {
        return 'Rp ' + Number(amount).toLocaleString('id-ID');
    }

    function tierInfo(amount) {
        if (amount < 10000) return {cls: 'tier-sm', label: 'Kecil'};
        if (amount <= 50000) return {cls: 'tier-md', label: 'Medium'};
        return {cls: 'tier-lg', label: 'Big'};
    }

    function timeAgo(isoString) {
        var diff = Math.floor((Date.now() - new Date(isoString).getTime()) / 1000);
        if (diff < 60) return 'baru saja';
        if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
        if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
        return Math.floor(diff / 86400) + ' hari lalu';
    }

    function buildHistoryItem(d) {
        var tier = tierInfo(d.amount);
        var ytRow = '';
        if (d.ytEnabled && d.ytUrl) {
            ytRow = '<div class="h-msg" style="color:#f87171;display:flex;align-items:center;gap:4px">' +
                    '<span class="iconify" style="width:11px;height:11px" data-icon="solar:play-bold"></span>' +
                    'YouTube Request</div>';
        } else if (d.msg) {
            ytRow = '<div class="h-msg">' + escHtml(d.msg) + '</div>';
        }
        return '<div class="history-item donation-flash">' +
            '<div class="h-avatar">' + escHtml(d.emoji) + '</div>' +
            '<div class="h-info">' +
                '<div class="h-name">' + escHtml(d.name) + '</div>' +
                ytRow +
                '<span class="h-badge ' + tier.cls + '">' + tier.label + '</span>' +
            '</div>' +
            '<div class="h-meta">' +
                '<div class="h-amount">' + formatRp(d.amount) + '</div>' +
                '<div class="h-time">' + timeAgo(d.time) + '</div>' +
            '</div>' +
        '</div>';
    }

    function escHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function showLiveBadge() {
        var badge = document.getElementById('live-badge');
        if (!badge) return;
        badge.classList.remove('hidden');
        clearTimeout(badgeTimer);
        badgeTimer = setTimeout(function () {
            badge.classList.add('hidden');
        }, 5000);
    }

    function onDonation(d) {
        // Prepend to history list
        var list = document.getElementById('history-list');
        if (list) {
            // Remove empty state if present
            var empty = list.querySelector('.empty-state');
            if (empty) empty.remove();

            list.insertAdjacentHTML('afterbegin', buildHistoryItem(d));
            // Re-init iconify for new icons
            if (window.Iconify) window.Iconify.scan(list.firstElementChild);

            // Trim to max 50 items to avoid unbounded growth
            while (list.children.length > 50) {
                list.removeChild(list.lastChild);
            }
        }

        // Update count chip
        liveCount++;
        var chip = document.getElementById('history-count');
        if (chip) chip.textContent = liveCount;

        showLiveBadge();
    }

    function onStats(stats) {
        var el;

        // Total
        el = document.getElementById('stat-total');
        if (el && stats.total !== undefined) {
            el.textContent = 'Rp ' + Number(stats.total).toLocaleString('id-ID');
        }
        // Count
        el = document.getElementById('stat-count');
        if (el && stats.count !== undefined) {
            el.textContent = stats.count + ' donasi masuk';
        }
        // Donors
        el = document.getElementById('stat-donors');
        if (el && stats.donors !== undefined) {
            el.textContent = stats.donors;
        }
        // Biggest
        if (stats.biggest) {
            el = document.getElementById('stat-biggest');
            if (el) el.textContent = 'Rp ' + Number(stats.biggest.amount).toLocaleString('id-ID');
            el = document.getElementById('stat-biggest-name');
            if (el) el.textContent = stats.biggest.name || '-';
        }
        // Milestone
        if (stats.milestone) {
            el = document.getElementById('stat-milestone');
            if (el) {
                var pct = stats.milestone.reached ? 100
                    : (stats.milestone.target > 0
                        ? Math.min(100, Math.round(stats.milestone.current / stats.milestone.target * 100))
                        : 0);
                el.textContent = pct + '%';
            }
        }
    }

    function connect() {
        if (es) { try { es.close(); } catch(e) {} }

        es = new EventSource(SSE_URL);

        es.addEventListener('donation', function (e) {
            try { onDonation(JSON.parse(e.data)); } catch (err) {}
        });

        es.addEventListener('stats', function (e) {
            try { onStats(JSON.parse(e.data)); } catch (err) {}
        });

        es.addEventListener('ping', function () { /* keep-alive — no-op */ });

        es.onerror = function () {
            // On error, close and reconnect after 5s
            try { es.close(); } catch(e) {}
            setTimeout(connect, 5000);
        };
    }

    // Only connect if EventSource is supported
    if (window.EventSource) {
        connect();
    }
})();
</script>
@endpush
</x-app-layout>
