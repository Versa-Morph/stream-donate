<x-app-layout>
    @push('styles')
    <style>
        .admin-wrap { max-width: 1400px; margin: 0 auto; padding: 28px 28px 48px; }

        /* ── PAGE HEADER ── */
        .page-header { margin-bottom: 28px; }
        .page-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 26px; font-weight: 700; letter-spacing: -.5px;
            color: var(--text); line-height: 1.2;
        }
        .page-subtitle { font-size: 13px; color: var(--text-3); margin-top: 4px; }

        /* ── STATS GRID ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 20px 22px;
            position: relative; overflow: hidden;
            transition: border-color .2s;
        }
        .stat-card:hover { border-color: var(--border-2); }
        .stat-card::after {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 2px;
        }
        .stat-card.c-brand::after  { background: var(--brand); }
        .stat-card.c-orange::after { background: var(--orange); }
        .stat-card.c-green::after  { background: var(--green); }
        .stat-card.c-yellow::after { background: var(--yellow); }
        .stat-card.c-purple::after { background: var(--purple); }
        .stat-card.c-red::after    { background: var(--red); }

        .stat-icon {
            width: 36px; height: 36px; border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 14px;
        }
        .stat-icon .iconify { width: 20px; height: 20px; }
        .c-brand  .stat-icon { background: rgba(124,108,252,.12); border: 1px solid rgba(124,108,252,.2); }
        .c-brand  .stat-icon .iconify { color: var(--brand-light); }
        .c-orange .stat-icon { background: rgba(249,115,22,.10); border: 1px solid rgba(249,115,22,.2); }
        .c-orange .stat-icon .iconify { color: var(--orange); }
        .c-green  .stat-icon { background: rgba(34,211,160,.10); border: 1px solid rgba(34,211,160,.2); }
        .c-green  .stat-icon .iconify { color: var(--green); }
        .c-yellow .stat-icon { background: rgba(251,191,36,.10); border: 1px solid rgba(251,191,36,.2); }
        .c-yellow .stat-icon .iconify { color: var(--yellow); }
        .c-purple .stat-icon { background: rgba(168,85,247,.10); border: 1px solid rgba(168,85,247,.2); }
        .c-purple .stat-icon .iconify { color: var(--purple); }

        .stat-label {
            font-size: 11px; font-weight: 700; letter-spacing: 1px;
            text-transform: uppercase; color: var(--text-3); margin-bottom: 8px;
        }
        .stat-value {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 28px; font-weight: 700; letter-spacing: -.8px;
            color: var(--text); line-height: 1;
        }
        .stat-sub { font-size: 11px; color: var(--text-3); margin-top: 6px; }

        /* ── SECTION ── */
        .section { margin-bottom: 32px; }
        .section-header {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 16px;
        }
        .section-title {
            font-size: 14px; font-weight: 700; color: var(--text-2);
            letter-spacing: -.2px; display: flex; align-items: center; gap: 8px;
        }
        .section-title .iconify { width: 16px; height: 16px; color: var(--text-3); }
        .section-link {
            font-size: 12px; color: var(--brand-light);
            font-weight: 500;
        }
        .section-link:hover { color: var(--text); }

        /* ── TABLE ── */
        .table-wrap {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
        }
        table { width: 100%; border-collapse: collapse; }
        thead { background: var(--surface-2); }
        th {
            padding: 11px 16px; text-align: left;
            font-size: 11px; font-weight: 700; letter-spacing: .7px;
            text-transform: uppercase; color: var(--text-3);
        }
        td {
            padding: 12px 16px; border-top: 1px solid var(--border);
            font-size: 13px; color: var(--text-2);
            vertical-align: middle;
        }
        tr:hover td { background: var(--surface-2); }
        .td-mono { font-family: monospace; font-size: 12px; }
        .amount-cell {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700; color: var(--green);
        }
        .empty-cell { text-align: center; color: var(--text-3); padding: 32px; font-size: 13px; }

        /* ── DONOR AVATAR (initials) ── */
        .donor-avatar {
            width: 28px; height: 28px; border-radius: 8px;
            background: var(--surface-3); border: 1px solid var(--border);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 12px; vertical-align: middle; margin-right: 8px;
            font-weight: 700; color: var(--text-3); flex-shrink: 0;
        }

        /* ── BADGE ── */
        .badge {
            display: inline-block; padding: 2px 9px;
            border-radius: 20px; font-size: 10px; font-weight: 700;
            letter-spacing: .3px; text-transform: uppercase;
        }
        .badge-brand  { background: rgba(124,108,252,.12); color: var(--brand-light); border: 1px solid rgba(124,108,252,.25); }
        .badge-orange { background: rgba(249,115,22,.10);  color: var(--orange-light); border: 1px solid rgba(249,115,22,.25); }
        .badge-green  { background: rgba(34,211,160,.10);  color: var(--green);        border: 1px solid rgba(34,211,160,.25); }
        .badge-red    { background: rgba(244,63,94,.10);   color: var(--red);          border: 1px solid rgba(244,63,94,.25); }

        /* ── TWO COL ── */
        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        @media (max-width: 900px) { .two-col { grid-template-columns: 1fr; } }

        /* ── STREAMER TABLE ── */
        .streamer-rank {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 13px; font-weight: 700; color: var(--text-3);
            width: 32px; text-align: center;
        }

        /* ── IMPERSONATE BTN ── */
        .impersonate-btn {
            display: inline-flex; align-items: center; gap: 5px;
            font-size: 10px; padding: 4px 10px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border);
            background: var(--surface-2); color: var(--text-2);
            cursor: pointer; font-family: 'Inter', sans-serif;
            font-weight: 600; transition: all .15s;
        }
        .impersonate-btn .iconify { width: 11px; height: 11px; }
        .impersonate-btn:hover { border-color: var(--brand); color: var(--brand-light); background: rgba(124,108,252,.08); }
    </style>
    @endpush

    <div class="admin-wrap">
        <!-- Header -->
        <div class="page-header">
            <h1 class="page-title">Admin Dashboard</h1>
            <p class="page-subtitle">Overview platform StreamDonate</p>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card c-brand">
                <div class="stat-icon">
                    <span class="iconify" data-icon="solar:gamepad-bold-duotone"></span>
                </div>
                <div class="stat-label">Total Streamer</div>
                <div class="stat-value">{{ $totalStreamers }}</div>
                <div class="stat-sub">akun streamer terdaftar</div>
            </div>
            <div class="stat-card c-orange">
                <div class="stat-icon">
                    <span class="iconify" data-icon="solar:users-group-rounded-bold-duotone"></span>
                </div>
                <div class="stat-label">Total User</div>
                <div class="stat-value">{{ $totalUsers }}</div>
                <div class="stat-sub">semua role</div>
            </div>
            <div class="stat-card c-green">
                <div class="stat-icon">
                    <span class="iconify" data-icon="solar:heart-bold-duotone"></span>
                </div>
                <div class="stat-label">Total Donasi</div>
                <div class="stat-value">{{ number_format($totalDonations) }}</div>
                <div class="stat-sub">semua streamer</div>
            </div>
            <div class="stat-card c-yellow">
                <div class="stat-icon">
                    <span class="iconify" data-icon="solar:wallet-money-bold-duotone"></span>
                </div>
                <div class="stat-label">Total Nominal</div>
                <div class="stat-value">Rp {{ number_format($totalAmount / 1000, 0, ',', '.') }}K</div>
                <div class="stat-sub">all-time</div>
            </div>
            <div class="stat-card c-purple">
                <div class="stat-icon">
                    <span class="iconify" data-icon="solar:calendar-bold-duotone"></span>
                </div>
                <div class="stat-label">Donasi Hari Ini</div>
                <div class="stat-value">{{ number_format($todayCount) }}</div>
                <div class="stat-sub">Rp {{ number_format($todayAmount) }}</div>
            </div>
        </div>

        <!-- Two column: Recent Donations + Streamer Rankings -->
        <div class="two-col">

            <!-- Recent Donations -->
            <div class="section">
                <div class="section-header">
                    <span class="section-title">
                        <span class="iconify" data-icon="solar:history-bold-duotone"></span>
                        Donasi Terbaru
                    </span>
                    <a href="{{ route('admin.donations') }}" class="section-link">Lihat semua →</a>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Donatur</th>
                                <th>Streamer</th>
                                <th>Nominal</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentDonations as $d)
                            <tr>
                                <td>
                                    <span style="margin-right:5px">{{ $d->emoji ?? '🎉' }}</span>
                                    {{ $d->name }}
                                </td>
                                <td>
                                    @if($d->streamer)
                                        <a href="{{ route('donate.show', $d->streamer->slug) }}" target="_blank" style="color:var(--brand-light)">
                                            {{ $d->streamer->display_name }}
                                        </a>
                                    @else
                                        <span style="color:var(--text-3)">—</span>
                                    @endif
                                </td>
                                <td class="amount-cell">Rp {{ number_format($d->amount) }}</td>
                                <td style="color:var(--text-3); font-size:11px">{{ $d->created_at->diffForHumans() }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="empty-cell">Belum ada donasi</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Streamer Rankings -->
            <div class="section">
                <div class="section-header">
                    <span class="section-title">
                        <span class="iconify" data-icon="solar:ranking-bold-duotone"></span>
                        Top Streamer
                    </span>
                    <a href="{{ route('admin.users') }}" class="section-link">Kelola user →</a>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Streamer</th>
                                <th>Donasi</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($streamerStats as $i => $s)
                            <tr>
                                <td class="streamer-rank">{{ $i + 1 }}</td>
                                <td>
                                    <div style="font-size:13px; font-weight:600; color:var(--text)">{{ $s->display_name }}</div>
                                    <div style="font-size:11px; color:var(--text-3)">{{ $s->slug }}</div>
                                </td>
                                <td>{{ number_format($s->donations_count) }}</td>
                                <td class="amount-cell">Rp {{ number_format($s->donations_sum_amount ?? 0) }}</td>
                                <td>
                                    @if($s->user)
                                    <form method="POST" action="{{ route('admin.impersonate', $s->user) }}"
                                          onsubmit="return confirm('Impersonate sebagai {{ addslashes($s->display_name) }}?\n\nKamu akan masuk sebagai user ini. Klik Kembali ke Admin untuk berhenti.')">
                                        @csrf
                                        <button type="submit" class="impersonate-btn">
                                            <span class="iconify" data-icon="solar:user-speak-bold-duotone"></span>
                                            Login As
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="empty-cell">Belum ada streamer</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Activity Logs -->
        <div class="section">
            <div class="section-header">
                <span class="section-title">
                    <span class="iconify" data-icon="solar:document-text-bold-duotone"></span>
                    Activity Log Terbaru
                </span>
                <a href="{{ route('admin.logs') }}" class="section-link">Lihat semua →</a>
            </div>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Action</th>
                            <th>Deskripsi</th>
                            <th>User</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentLogs as $log)
                        <tr>
                            <td style="color:var(--text-3); font-size:11px; white-space:nowrap">
                                {{ $log->created_at->format('d/m H:i') }}
                            </td>
                            <td><span class="badge badge-brand td-mono">{{ $log->action }}</span></td>
                            <td>{{ Str::limit($log->description, 80) }}</td>
                            <td style="font-size:12px; color:var(--text-3)">
                                {{ $log->user?->name ?? '—' }}
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="empty-cell">Belum ada log aktivitas</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
