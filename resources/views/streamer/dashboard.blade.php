<x-app-layout>
@push('styles')
<style>
/* ── Page-specific: Stat card colors ── */
.stat-card[data-color="brand"]  { --stat-color: var(--brand); }
.stat-card[data-color="green"]  { --stat-color: var(--green); }
.stat-card[data-color="orange"] { --stat-color: var(--orange); }
.stat-card[data-color="purple"] { --stat-color: var(--purple); }

/* ── Sidebar column (right side) ── */
.sidebar-col {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

/* ── History list (scrollable) ── */
.history-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-height: 540px;
    overflow-y: auto;
    padding-right: 4px;
}
.history-list::-webkit-scrollbar { width: 4px; }
.history-list::-webkit-scrollbar-track { background: var(--surface-2); border-radius: 4px; }
.history-list::-webkit-scrollbar-thumb { background: var(--brand); border-radius: 4px; }

/* ── History item (donation entry) ── */
.history-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 16px;
    background: var(--glass-bg-2);
    backdrop-filter: blur(8px);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius);
    transition: all .2s ease;
}
.history-item:hover {
    border-color: var(--glass-border-2);
    background: var(--glass-bg);
    transform: translateX(4px);
}
.h-avatar {
    width: 42px;
    height: 42px;
    border-radius: 14px;
    background: linear-gradient(135deg, var(--surface-3), var(--surface));
    border: 1px solid var(--glass-border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}
.h-info { flex: 1; min-width: 0; }
.h-name { font-size: 14px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.h-msg { font-size: 11px; color: var(--text-3); margin-top: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; font-style: italic; }
.h-meta { text-align: right; flex-shrink: 0; }
.h-amount { font-family: 'Space Grotesk', sans-serif; font-size: 16px; font-weight: 700; color: var(--orange); }
.h-time { font-size: 10px; color: var(--text-3); margin-top: 3px; }

/* ── Donation size badges ── */
.h-badge { display: inline-block; font-size: 9px; font-weight: 700; letter-spacing: .4px; padding: 3px 8px; border-radius: 6px; margin-top: 4px; text-transform: uppercase; }
.h-badge.tier-sm { background: rgba(96,96,120,.15); border: 1px solid rgba(96,96,120,.25); color: var(--text-3); }
.h-badge.tier-md { background: rgba(124,108,252,.15); border: 1px solid rgba(124,108,252,.3); color: var(--brand-light); }
.h-badge.tier-lg { background: rgba(249,115,22,.15); border: 1px solid rgba(249,115,22,.3); color: var(--orange); }

/* ── Leaderboard ── */
.leader-list { display: flex; flex-direction: column; gap: 10px; }
.leader-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px;
    background: var(--glass-bg-2);
    backdrop-filter: blur(8px);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius);
    transition: all .2s ease;
}
.leader-item:hover { transform: translateX(4px); border-color: var(--glass-border-2); }
.leader-item.rank-1 { border-color: rgba(251,191,36,.35); background: rgba(251,191,36,.08); box-shadow: 0 0 20px rgba(251,191,36,.1); }
.leader-item.rank-2 { border-color: rgba(192,192,192,.25); background: rgba(192,192,192,.05); }
.leader-item.rank-3 { border-color: rgba(205,127,50,.28); background: rgba(205,127,50,.06); }
.rank-num { width: 28px; text-align: center; font-size: 18px; flex-shrink: 0; }
.rank-num.plain { font-family: 'Space Grotesk', sans-serif; font-size: 13px; font-weight: 700; color: var(--text-3); }
.leader-emoji {
    width: 36px;
    height: 36px;
    border-radius: 12px;
    background: var(--glass-bg);
    backdrop-filter: blur(8px);
    border: 1px solid var(--glass-border);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}
.leader-name { font-size: 14px; font-weight: 600; flex: 1; min-width: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.leader-right { text-align: right; flex-shrink: 0; }
.leader-total { font-family: 'Space Grotesk', sans-serif; font-size: 15px; font-weight: 700; color: var(--yellow); }
.leader-count { font-size: 10px; color: var(--text-3); margin-top: 2px; }

/* ── Heatmap Activity Card ── */
.hm-card {
    background: var(--glass-bg);
    backdrop-filter: blur(12px) saturate(180%);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-lg);
    padding: 18px;
    position: relative;
    overflow: visible;
    box-shadow: var(--glass-shadow);
    transition: all .3s ease;
}
.hm-card:hover { border-color: var(--glass-border-2); }
.hm-card-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; gap: 6px; }
.hm-card-title { font-family: 'Space Grotesk', sans-serif; font-size: 14px; font-weight: 700; letter-spacing: -.2px; }

/* Month nav */
.hm-nav { display: flex; align-items: center; gap: 5px; }
.hm-nav-btn {
    width: 24px;
    height: 24px;
    border-radius: 6px;
    border: 1px solid var(--glass-border);
    background: var(--glass-bg-2);
    color: var(--text-2);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 13px;
    line-height: 1;
    transition: all .2s ease;
    flex-shrink: 0;
    padding: 0;
}
.hm-nav-btn:hover { border-color: var(--brand); color: var(--brand-light); background: rgba(124,108,252,.12); box-shadow: 0 0 10px rgba(124,108,252,.2); }
.hm-month-label { font-family: 'Space Grotesk', sans-serif; font-size: 11px; font-weight: 700; min-width: 92px; text-align: center; letter-spacing: -.1px; color: var(--text-2); }

/* Calendar layout */
.heatmap-cal {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 2px;
}
.hm-dow {
    font-size: 8px;
    font-weight: 700;
    color: var(--text-3);
    text-align: center;
    letter-spacing: .3px;
    text-transform: uppercase;
    padding-bottom: 3px;
}
.hm-cell {
    aspect-ratio: 1;
    border-radius: 2px;
    background: rgba(124,108,252, var(--hm-opacity, 0.04));
    cursor: default;
    transition: transform .12s, outline .12s;
    outline: 2px solid transparent;
    min-width: 0;
}
.hm-cell[data-has="1"] { cursor: pointer; }
.hm-cell[data-has="1"]:hover { transform: scale(1.35); outline-color: rgba(124,108,252,.6); z-index: 2; position: relative; }
.hm-cell[data-has="0"], .hm-cell.hm-empty { background: rgba(255,255,255,.04); }

/* Legend */
.heatmap-legend { display: flex; align-items: center; gap: 5px; margin-top: 10px; }
.legend-label { font-size: 9px; color: var(--text-3); }
.legend-cell {
    width: 10px;
    height: 10px;
    border-radius: 3px;
    flex-shrink: 0;
    cursor: default;
    background: rgba(124,108,252, var(--lc-op, .12));
}

/* Tooltip */
.hm-tooltip {
    position: fixed;
    z-index: 9999;
    background: var(--glass-bg);
    backdrop-filter: blur(16px) saturate(180%);
    border: 1px solid rgba(124,108,252,.5);
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 12px;
    color: var(--text);
    pointer-events: none;
    opacity: 0;
    transition: opacity .15s;
    white-space: nowrap;
    box-shadow: var(--glass-shadow-lg), 0 0 20px rgba(124,108,252,.2);
    line-height: 1.6;
}
.hm-tooltip.visible { opacity: 1; }
.hm-tooltip .tt-date { font-weight: 700; font-size: 11px; color: var(--text-3); margin-bottom: 3px; }
.hm-tooltip .tt-amount { font-size: 15px; font-weight: 700; color: var(--brand-light); }
.hm-tooltip .tt-count { font-size: 11px; color: var(--text-3); }

.empty-state { padding: 44px 24px; text-align: center; color: var(--text-3); font-size: 13px; }

/* ── Header buttons ── */
.header-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: var(--radius);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all .2s ease;
    text-decoration: none;
    border: none;
    font-family: 'Inter', sans-serif;
}
.header-btn-ghost {
    border: 1px solid var(--glass-border);
    color: var(--text-2);
    background: var(--glass-bg);
    backdrop-filter: blur(8px);
}
.header-btn-ghost:hover { border-color: var(--glass-border-2); color: var(--text); background: var(--glass-bg-2); transform: translateY(-1px); }
.header-btn-brand {
    background: linear-gradient(135deg, var(--brand) 0%, #6356e8 50%, var(--purple) 100%);
    background-size: 200% 200%;
    color: #fff;
    box-shadow: 0 4px 20px var(--brand-glow), 0 0 15px rgba(124,108,252,.4);
    animation: gradientShift 3s ease infinite;
}
@keyframes gradientShift { 0%, 100% { background-position: 0% 50%; } 50% { background-position: 100% 50%; } }
.header-btn-brand:hover { transform: translateY(-2px); box-shadow: 0 8px 28px var(--brand-glow), 0 0 25px rgba(124,108,252,.6); }
.header-btn .iconify { width: 16px; height: 16px; }

/* ── Test Alert button ── */
.test-alert-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: var(--radius);
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all .2s ease;
    border: none;
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, var(--orange) 0%, #ea580c 50%, #dc2626 100%);
    background-size: 200% 200%;
    color: #fff;
    box-shadow: 0 4px 20px rgba(249,115,22,.35), 0 0 15px rgba(249,115,22,.3);
    animation: gradientShift 3s ease infinite;
}
.test-alert-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(249,115,22,.5), 0 0 25px rgba(249,115,22,.5); }
.test-alert-btn:active { transform: translateY(0); }
.test-alert-btn:disabled { opacity: .6; cursor: not-allowed; transform: none; animation: none; }
.test-alert-btn .iconify { width: 16px; height: 16px; }
.btn-spin { width: 14px; height: 14px; border: 2px solid rgba(255,255,255,.3); border-top-color: #fff; border-radius: 50%; display: none; }
.test-alert-btn.loading .btn-spin { display: block; animation: btnSpin .6s linear infinite; }
.test-alert-btn.loading .btn-icon-wrap { display: none; }
.test-alert-toast {
    position: fixed;
    bottom: 28px;
    left: 50%;
    transform: translateX(-50%) translateY(16px);
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 12px 20px;
    font-size: 13px;
    font-weight: 600;
    color: var(--text);
    box-shadow: 0 12px 40px rgba(0,0,0,.5);
    z-index: 9999;
    opacity: 0;
    transition: all .3s;
    pointer-events: none;
    white-space: nowrap;
    display: flex;
    align-items: center;
    gap: 10px;
}
.test-alert-toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
.test-alert-toast.toast-error { background: #3b1a1a; border-color: #e53e3e; color: #fc8181; }
.test-alert-toast .toast-emoji { font-size: 18px; }

/* ── SSE notification badge ── */
.live-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    background: rgba(34,211,160,.1);
    border: 1px solid rgba(34,211,160,.3);
    color: var(--green);
    letter-spacing: .3px;
    margin-left: 8px;
    animation: fadeInBadge .3s ease;
}
.live-badge.hidden { display: none; }
.live-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--green); animation: pulseDot 1.2s ease-in-out infinite; }
@keyframes pulseDot { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: .4; transform: scale(.6); } }
@keyframes fadeInBadge { from { opacity: 0; transform: translateY(-4px); } to { opacity: 1; transform: translateY(0); } }

.donation-flash { animation: flashItem .6s ease; }
@keyframes flashItem { 0% { background: rgba(124,108,252,.18); border-color: rgba(124,108,252,.5); } 100% { background: var(--surface-2); border-color: var(--border); } }

/* ── Responsive ── */
@media (max-width: 1100px) {
    .stats-grid.cols-4 { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 1024px) {
    .content-grid.main-sidebar { grid-template-columns: 1fr; }
    .sidebar-col { flex-direction: row; flex-wrap: wrap; }
    .sidebar-col > * { flex: 1; min-width: 280px; }
}
@media (max-width: 768px) {
    .page-header { flex-direction: column; align-items: flex-start; }
    .page-header-right { width: 100%; flex-wrap: wrap; }
    .stats-grid.cols-4 { grid-template-columns: 1fr; }
}
@media (max-width: 600px) {
    .sidebar-col { flex-direction: column; }
    .sidebar-col > * { min-width: 100%; }
}
</style>
@endpush

<div class="page-container">

    {{-- Header --}}
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">Dashboard</h1>
            <p class="page-subtitle">Selamat datang, {{ auth()->user()->name }}</p>
        </div>
        <div class="page-header-right">
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
    <div class="stats-grid cols-4">
        <div class="stat-card" data-color="brand">
            <div class="stat-icon">
                <span class="iconify" data-icon="solar:wallet-money-bold-duotone"></span>
            </div>
            <div class="stat-label">Total Terkumpul</div>
            <div class="stat-value" id="stat-total">Rp {{ number_format($stats['total'], 0, ',', '.') }}</div>
            <div class="stat-sub" id="stat-count">{{ $stats['count'] }} donasi masuk</div>
        </div>
        <div class="stat-card" data-color="green">
            <div class="stat-icon">
                <span class="iconify" data-icon="solar:heart-bold-duotone"></span>
            </div>
            <div class="stat-label">Total Donatur</div>
            <div class="stat-value" id="stat-donors">{{ $stats['donors'] }}</div>
            <div class="stat-sub">donatur unik</div>
        </div>
        <div class="stat-card" data-color="orange">
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
        <div class="stat-card" data-color="purple">
            <div class="stat-icon">
                <span class="iconify" data-icon="solar:target-bold-duotone"></span>
            </div>
            <div class="stat-label">Milestone</div>
            <div class="stat-value" id="stat-milestone">{{ $stats['milestone']['reached'] ? '100%' : round($stats['milestone']['target'] > 0 ? min(100, $stats['milestone']['current'] / $stats['milestone']['target'] * 100) : 0) . '%' }}</div>
            <div class="stat-sub">{{ $stats['milestone']['title'] }}</div>
        </div>
    </div>

    {{-- Main content --}}
    <div class="content-grid main-sidebar">

        {{-- Donation History --}}
        <div class="content-card">
            <div class="card-header">
                <div class="card-header-left">
                    <span class="iconify" data-icon="solar:history-bold-duotone" style="width:18px;height:18px;color:var(--brand-light)"></span>
                    <span class="card-title">Riwayat Donasi</span>
                    <span class="card-count" id="history-count">{{ $donations->count() }}</span>
                    <span class="live-badge hidden" id="live-badge">
                        <span class="live-dot"></span>DONASI BARU
                    </span>
                </div>
                <a href="{{ route('streamer.reports') }}" class="card-link">Lihat laporan →</a>
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

        {{-- Kolom kanan: Heatmap + Leaderboard --}}
        <div class="sidebar-col">

            {{-- Heatmap Activity --}}
            <div class="hm-card">
                <div class="hm-card-header">
                    <div class="hm-card-title">Aktivitas Donasi</div>
                    <div class="hm-nav">
                        <button class="hm-nav-btn" id="hm-prev" title="Bulan sebelumnya" aria-label="Bulan sebelumnya">&#8249;</button>
                        <div class="hm-month-label" id="hm-month-label"></div>
                        <button class="hm-nav-btn" id="hm-next" title="Bulan berikutnya" aria-label="Bulan berikutnya">&#8250;</button>
                    </div>
                </div>
                <div class="heatmap-cal" id="heatmap-cal"></div>
                <div class="heatmap-legend">
                    <span class="legend-label">Sepi</span>
                    <span class="legend-cell" style="--lc-op:0.12"></span>
                    <span class="legend-cell" style="--lc-op:0.35"></span>
                    <span class="legend-cell" style="--lc-op:0.60"></span>
                    <span class="legend-cell" style="--lc-op:0.85"></span>
                    <span class="legend-cell" style="--lc-op:1.00"></span>
                    <span class="legend-label">Ramai</span>
                </div>
                <div class="hm-tooltip" id="hm-tooltip" role="tooltip" aria-hidden="true"></div>
            </div>

            {{-- Leaderboard --}}
            <div class="content-card">
                <div class="card-header">
                    <div class="card-header-left">
                        <span class="iconify" data-icon="solar:ranking-bold-duotone" style="width:18px;height:18px;color:var(--yellow)"></span>
                        <span class="card-title">{{ $streamer->leaderboard_title }}</span>
                    </div>
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

        </div>{{-- end sidebar-col --}}

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
    .then(r => {
        if (!r.ok) {
            return r.json().catch(() => ({})).then(body => {
                throw new Error(body.error || 'Server error ' + r.status);
            });
        }
        return r.json();
    })
    .then(data => {
        if (data.ok) {
            document.getElementById('toast-emoji').textContent = data.emoji;
            document.getElementById('toast-msg').textContent =
                data.emoji + ' ' + data.name + ' • Rp ' + Number(data.amount).toLocaleString('id-ID') + ' — dikirim ke OBS!';
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3500);
        } else {
            document.getElementById('toast-emoji').textContent = '⚠️';
            document.getElementById('toast-msg').textContent = data.error || 'Gagal mengirim test alert.';
            toast.classList.add('show', 'toast-error');
            setTimeout(() => { toast.classList.remove('show', 'toast-error'); }, 4000);
        }
    })
    .catch(err => {
        document.getElementById('toast-emoji').textContent = '⚠️';
        document.getElementById('toast-msg').textContent = err.message || 'Gagal mengirim test alert.';
        toast.classList.add('show', 'toast-error');
        setTimeout(() => { toast.classList.remove('show', 'toast-error'); }, 4000);
    })
    .finally(() => {
        btn.classList.remove('loading');
        btn.disabled = false;
    });
}
// ── Heatmap Calendar ──
(function () {
    var HEATMAP_URL = '{{ route('streamer.heatmap-data') }}';
    var CSRF        = document.querySelector('meta[name="csrf-token"]') ?
                      document.querySelector('meta[name="csrf-token"]').content : '';

    // Initial data from server (no AJAX needed for first render)
    var initialData = @json($heatmapInitial);

    var cal       = document.getElementById('heatmap-cal');
    var tip       = document.getElementById('hm-tooltip');
    var labelEl   = document.getElementById('hm-month-label');
    var btnPrev   = document.getElementById('hm-prev');
    var btnNext   = document.getElementById('hm-next');

    if (!cal || !tip) return;

    var currentYear  = initialData.year;
    var currentMonth = initialData.month;
    var activeCell   = null;

    // Day-of-week headers: Sun first (matches JS Date.getDay())
    var DOW_LABELS = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'];

    function toOpacity(total, maxTotal) {
        if (!total) return 0;
        var ratio = Math.log1p(total) / Math.log1p(maxTotal);
        return Math.max(0.18, Math.min(1.0, 0.18 + ratio * 0.82));
    }

    function formatRp(n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID');
    }

    // ── Tooltip ──
    function showTip(cell) {
        var total = parseInt(cell.dataset.total, 10) || 0;
        var count = parseInt(cell.dataset.count, 10) || 0;
        var date  = cell.dataset.dateLabel || cell.dataset.date || '';
        tip.innerHTML =
            '<div class="tt-date">' + date + '</div>' +
            '<div class="tt-amount">' + (total ? formatRp(total) : 'Tidak ada donasi') + '</div>' +
            (count ? '<div class="tt-count">' + count + ' donasi</div>' : '');
        tip.classList.add('visible');
        tip.setAttribute('aria-hidden', 'false');
        positionTip(cell);
    }

    function hideTip() {
        tip.classList.remove('visible');
        tip.setAttribute('aria-hidden', 'true');
        activeCell = null;
    }

    function positionTip(cell) {
        var rect  = cell.getBoundingClientRect();
        var tipW  = tip.offsetWidth  || 160;
        var tipH  = tip.offsetHeight || 60;
        var margin = 8;
        var top   = rect.top - tipH - margin;
        var left  = rect.left + rect.width / 2 - tipW / 2;
        if (top < 8) top = rect.bottom + margin;
        if (left < 8) left = 8;
        if (left + tipW > window.innerWidth - 8) left = window.innerWidth - tipW - 8;
        tip.style.top  = top  + 'px';
        tip.style.left = left + 'px';
    }

    // ── Render ──
    function renderCalendar(data) {
        if (!data || !data.days) return;

        currentYear  = data.year;
        currentMonth = data.month;

        if (labelEl) labelEl.textContent = data.monthLabel;

        // Disable next button if we're at current month
        var now = new Date();
        if (btnNext) {
            var isCurrentOrFuture = (data.year > now.getFullYear()) ||
                (data.year === now.getFullYear() && data.month >= now.getMonth() + 1);
            btnNext.disabled = isCurrentOrFuture;
            btnNext.style.opacity = isCurrentOrFuture ? '.3' : '';
            btnNext.style.cursor  = isCurrentOrFuture ? 'not-allowed' : '';
        }

        // Find max for opacity scale
        var maxTotal = 1;
        data.days.forEach(function (d) { if (d.total > maxTotal) maxTotal = d.total; });

        // Build grid HTML
        // Row 0: day-of-week headers
        var html = '';
        DOW_LABELS.forEach(function (lbl) {
            html += '<div class="hm-dow">' + lbl + '</div>';
        });

        // Leading empty cells (firstWeekday = 0 means Sun, so 0 empty cells before Sunday)
        for (var e = 0; e < data.firstWeekday; e++) {
            html += '<div class="hm-cell hm-empty"></div>';
        }

        // Day cells
        data.days.forEach(function (day) {
            var op  = toOpacity(day.total, maxTotal);
            var has = day.total > 0 ? '1' : '0';
            html += '<div class="hm-cell"' +
                ' data-date="' + day.iso + '"' +
                ' data-date-label="' + day.dateLabel + '"' +
                ' data-total="' + day.total + '"' +
                ' data-count="' + day.count + '"' +
                ' data-has="' + has + '"' +
                ' style="--hm-opacity:' + (day.total ? op.toFixed(3) : '0.04') + '"' +
                '></div>';
        });

        cal.innerHTML = html;

        // Attach tooltip events to day cells (not headers/empties)
        cal.querySelectorAll('.hm-cell[data-date]').forEach(function (cell) {
            cell.addEventListener('mouseenter', function () {
                activeCell = cell;
                showTip(cell);
            });
            cell.addEventListener('mousemove', function () {
                if (activeCell === cell) positionTip(cell);
            });
            cell.addEventListener('mouseleave', hideTip);
            cell.addEventListener('touchstart', function (e) {
                e.preventDefault();
                showTip(cell);
            }, { passive: false });
            cell.addEventListener('touchend', function () {
                setTimeout(hideTip, 2000);
            });
        });
    }

    // ── AJAX navigation ──
    function loadMonth(year, month) {
        hideTip();
        if (labelEl) labelEl.textContent = '…';
        fetch(HEATMAP_URL + '?year=' + year + '&month=' + month, {
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        })
        .then(function (r) { return r.json(); })
        .then(function (data) { renderCalendar(data); })
        .catch(function () {
            if (labelEl) labelEl.textContent = 'Error';
        });
    }

    if (btnPrev) {
        btnPrev.addEventListener('click', function () {
            var m = currentMonth - 1, y = currentYear;
            if (m < 1) { m = 12; y--; }
            loadMonth(y, m);
        });
    }

    if (btnNext) {
        btnNext.addEventListener('click', function () {
            var now = new Date();
            var m = currentMonth + 1, y = currentYear;
            if (m > 12) { m = 1; y++; }
            // Never navigate beyond current month
            if (y > now.getFullYear() || (y === now.getFullYear() && m > now.getMonth() + 1)) return;
            loadMonth(y, m);
        });
    }

    // Reposition tooltip on scroll
    window.addEventListener('scroll', function () {
        if (activeCell) positionTip(activeCell);
    }, { passive: true });

    // Initial render — use server-side data directly (no AJAX)
    renderCalendar(initialData);
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

    let sseHandlers = { donation: null, stats: null, ping: null, onerror: null };

    function connect() {
        // Clean up existing connection and handlers
        if (es) {
            try {
                if (sseHandlers.donation) es.removeEventListener('donation', sseHandlers.donation);
                if (sseHandlers.stats) es.removeEventListener('stats', sseHandlers.stats);
                if (sseHandlers.ping) es.removeEventListener('ping', sseHandlers.ping);
                es.close();
            } catch(e) {}
        }

        es = new EventSource(SSE_URL);

        sseHandlers.donation = function (e) {
            try { onDonation(JSON.parse(e.data)); } catch (err) {}
        };
        es.addEventListener('donation', sseHandlers.donation);

        sseHandlers.stats = function (e) {
            try { onStats(JSON.parse(e.data)); } catch (err) {}
        };
        es.addEventListener('stats', sseHandlers.stats);

        sseHandlers.ping = function () { /* keep-alive — no-op */ };
        es.addEventListener('ping', sseHandlers.ping);

        sseHandlers.onerror = function () {
            // On error, close and reconnect after 5s
            try { if (es) es.close(); } catch(e) {}
            setTimeout(connect, 5000);
        };
        es.onerror = sseHandlers.onerror;
    }

    // Only connect if EventSource is supported
    if (window.EventSource) {
        connect();
    }
})();
</script>
@endpush
</x-app-layout>
