<x-app-layout>
    @push('styles')
    <style>
        /* ── Admin-specific stat card color classes ── */
        .stat-card.c-brand  { --stat-color: var(--brand); }
        .stat-card.c-orange { --stat-color: var(--orange); }
        .stat-card.c-green  { --stat-color: var(--green); }
        .stat-card.c-yellow { --stat-color: var(--yellow); }
        .stat-card.c-purple { --stat-color: var(--purple); }
        .stat-card.c-red    { --stat-color: var(--red); }
        
        /* Top accent line for stat cards */
        .stat-card::after {
            top: 0;
            bottom: auto;
            height: 3px;
        }

        /* ── Section styling ── */
        .section { margin-bottom: 32px; }

        /* ── Table specific ── */
        .td-mono { font-family: monospace; font-size: 12px; }
        .amount-cell {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700;
            color: var(--green);
        }

        /* ── Donor avatar ── */
        .donor-avatar {
            width: 32px;
            height: 32px;
            border-radius: 10px;
            background: var(--glass-bg);
            backdrop-filter: blur(8px);
            border: 1px solid var(--glass-border);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            vertical-align: middle;
            margin-right: 10px;
            font-weight: 700;
            color: var(--text-3);
            flex-shrink: 0;
        }

        /* ── Streamer rank ── */
        .streamer-rank {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 14px;
            font-weight: 700;
            color: var(--text-3);
            width: 36px;
            text-align: center;
        }

        /* ── Impersonate button ── */
        .impersonate-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 10px;
            padding: 5px 12px;
            border-radius: var(--radius);
            border: 1px solid var(--glass-border);
            background: var(--glass-bg);
            backdrop-filter: blur(8px);
            color: var(--text-2);
            cursor: pointer;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            transition: all .2s ease;
        }
        .impersonate-btn .iconify { width: 12px; height: 12px; }
        .impersonate-btn:hover { 
            border-color: var(--brand);
            color: var(--brand-light); 
            background: rgba(124,108,252,.12);
            box-shadow: 0 0 12px rgba(124,108,252,.2);
            transform: translateY(-1px);
        }
    </style>
    @endpush

    <div class="page-container wide">
        <!-- Header -->
        <div class="page-header">
            <div class="page-header-left">
                <h1 class="page-title">Admin Dashboard</h1>
                <p class="page-subtitle">Overview platform StreamDonate</p>
            </div>
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
        <div class="content-grid cols-2">

            <!-- Recent Donations -->
            <div class="section">
                <div class="section-header">
                    <span class="section-title">
                        <span class="iconify" data-icon="solar:history-bold-duotone"></span>
                        Donasi Terbaru
                    </span>
                    <a href="{{ route('admin.donations') }}" class="card-link">Lihat semua →</a>
                </div>
                <div class="table-card">
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
                            <tr><td colspan="4" class="table-empty">Belum ada donasi</td></tr>
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
                    <a href="{{ route('admin.users') }}" class="card-link">Kelola user →</a>
                </div>
                <div class="table-card">
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
                            <tr><td colspan="5" class="table-empty">Belum ada streamer</td></tr>
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
                <a href="{{ route('admin.logs') }}" class="card-link">Lihat semua →</a>
            </div>
            <div class="table-card">
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
                        <tr><td colspan="4" class="table-empty">Belum ada log aktivitas</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="section">
            <div class="section-header">
                <span class="section-title">
                    <span class="iconify" data-icon="solar:shield-warning-bold-duotone"></span>
                    Moderasi Konten
                </span>
                <a href="{{ route('admin.banned-words.index') }}" class="card-link">Kelola kata terlarang →</a>
            </div>
            <div class="content-card" style="padding:16px 20px;">
                <p style="color:var(--text-3); font-size:13px; margin:0;">
                    Atur kata-kata yang akan disensor otomatis (***) pada nama dan pesan donasi di seluruh platform.
                    Streamer juga dapat menambahkan kata khusus mereka sendiri di halaman Settings.
                </p>
            </div>
        </div>

    </div>
</x-app-layout>
