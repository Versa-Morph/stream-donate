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

        /* ── Trend chart ── */
        .chart-wrap { position: relative; height: 280px; }

        /* ── Payout owed banner: informational nudge, not an error ── */
        .alert-warning {
            padding: 12px 16px;
            border-radius: var(--radius);
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
            background: rgba(251, 191, 36, .08);
            border: 1px solid rgba(251, 191, 36, .2);
            color: var(--yellow);
        }

        /* ── Modal ── */
        .modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 1000;
            background: rgba(0,0,0,.75);
            backdrop-filter: blur(8px);
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.open { display: flex; }
        .modal {
            background: var(--glass-bg);
            backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(124,108,252,.2);
            border-radius: var(--radius-xl);
            padding: 28px 32px;
            width: 480px;
            max-width: 92vw;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 24px 60px rgba(0,0,0,.6), 0 0 30px rgba(124,108,252,.1);
        }
        .modal-title { font-size: 16px; font-weight: 700; color: var(--text); margin-bottom: 18px; }
        .modal-footer { display: flex; gap: 10px; justify-content: flex-end; margin-top: 22px; }
        #trend-table { width: 100%; border-collapse: collapse; }
        #trend-table th {
            padding: 8px 10px; text-align: left; font-size: 11px; font-weight: 700;
            letter-spacing: .5px; text-transform: uppercase; color: var(--text-3);
            border-bottom: 1px solid var(--border);
        }
        #trend-table td {
            padding: 9px 10px; font-size: 13px; color: var(--text-2);
            border-top: 1px solid var(--border);
        }

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

        @if($unresolvedAlertFailures > 0)
        <div class="alert-error" style="margin-bottom:16px">
            ⚠ {{ $unresolvedAlertFailures }} Alert Gagal —
            <a href="{{ route('admin.alert-failures.index') }}" style="text-decoration:underline">Lihat →</a>
        </div>
        @endif

        @if($totalOwed > 0 || $pendingPayoutCount > 0)
        <div class="alert-warning">
            💰 Rp {{ number_format($totalOwed, 0, ',', '.') }} saldo owed belum dicairkan
            @if($pendingPayoutCount > 0)
                · {{ $pendingPayoutCount }} payout pending
            @endif
            —
            <a href="{{ route('admin.payouts.index') }}" style="text-decoration:underline">Lihat →</a>
        </div>
        @endif

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; flex-wrap:wrap; gap:8px">
            <h2 class="section-title" style="margin:0">Tren Donasi</h2>
            <div style="display:flex; align-items:center; gap:8px">
                <button type="button" class="btn-xs range-btn" data-days="7">7 Hari</button>
                <button type="button" class="btn-xs range-btn active" data-days="30">30 Hari</button>
                <button type="button" class="btn-xs range-btn" data-days="90">90 Hari</button>
                <button type="button" class="btn-xs" id="open-trend-table" style="display:inline-flex; align-items:center; gap:5px">
                    <span class="iconify" data-icon="solar:list-bold-duotone"></span>
                    Lihat Tabel
                </button>
            </div>
        </div>

        <div class="table-card" style="margin-bottom:16px">
            <div class="chart-wrap">
                <canvas id="trend-chart"></canvas>
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
                                    <a href="{{ route('admin.streamers.show', $s) }}" style="text-decoration:none">
                                        <div style="font-size:13px; font-weight:600; color:var(--text)">{{ $s->display_name }}</div>
                                        <div style="font-size:11px; color:var(--text-3)">{{ $s->slug }}</div>
                                    </a>
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

    <!-- Modal Data Tren Donasi -->
    <div class="modal-overlay" id="trend-table-modal">
        <div class="modal">
            <div class="modal-title">Data Tren Donasi</div>
            <table id="trend-table">
                <thead><tr><th>Tanggal</th><th>Jumlah Donasi</th><th>Total Nominal</th></tr></thead>
                <tbody></tbody>
            </table>
            <div class="modal-footer">
                <button type="button" class="btn-xs" onclick="closeTrendTableModal()">Tutup</button>
            </div>
        </div>
    </div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
@endpush

@push('scripts')
<script>
(function () {
    const style = getComputedStyle(document.documentElement);
    const brand = style.getPropertyValue('--brand').trim();
    const orange = style.getPropertyValue('--orange').trim();
    const bg = style.getPropertyValue('--bg').trim();
    const text3 = style.getPropertyValue('--text-3').trim();
    const border = style.getPropertyValue('--border-2').trim();
    const rupiah = (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(v);
    const number = (v) => new Intl.NumberFormat('id-ID').format(v);

    function hexToRgba(hex, alpha) {
        const r = parseInt(hex.slice(1, 3), 16);
        const g = parseInt(hex.slice(3, 5), 16);
        const b = parseInt(hex.slice(5, 7), 16);
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }

    function lineDataset(label, data, color, yAxisID) {
        return {
            label,
            data,
            yAxisID,
            borderColor: color,
            backgroundColor: hexToRgba(color, 0.1),
            borderWidth: 2,
            pointRadius: 2,
            pointHoverRadius: 5,
            pointBackgroundColor: color,
            pointBorderColor: bg,
            pointBorderWidth: 2,
            fill: true,
            tension: 0.3,
        };
    }

    let initial = @json($trend);

    const trendChart = new Chart(document.getElementById('trend-chart'), {
        type: 'line',
        data: {
            labels: initial.labels,
            datasets: [
                lineDataset('Total Donasi', initial.amounts, brand, 'yAmount'),
                lineDataset('Jumlah Donasi', initial.counts, orange, 'yCount'),
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    align: 'end',
                    labels: { color: text3, boxWidth: 10, usePointStyle: true, font: { size: 11 } },
                },
                tooltip: {
                    callbacks: {
                        label: (ctx) => `${ctx.dataset.label}: ${ctx.dataset.yAxisID === 'yAmount' ? rupiah(ctx.parsed.y) : number(ctx.parsed.y)}`,
                    },
                },
            },
            scales: {
                x: { grid: { display: false }, ticks: { color: text3 } },
                yAmount: {
                    position: 'left',
                    beginAtZero: true,
                    grid: { color: border },
                    ticks: { color: text3, callback: (v) => rupiah(v) },
                },
                yCount: {
                    position: 'right',
                    beginAtZero: true,
                    grid: { display: false },
                    ticks: { color: text3, precision: 0 },
                },
            },
        },
    });

    function renderTable(labels, counts, amounts) {
        const tbody = document.querySelector('#trend-table tbody');
        tbody.innerHTML = '';
        labels.forEach((label, i) => {
            const tr = document.createElement('tr');
            const tdLabel = document.createElement('td');
            tdLabel.textContent = label;
            const tdCount = document.createElement('td');
            tdCount.textContent = number(counts[i]);
            const tdAmount = document.createElement('td');
            tdAmount.textContent = rupiah(amounts[i]);
            tr.append(tdLabel, tdCount, tdAmount);
            tbody.appendChild(tr);
        });
    }
    renderTable(initial.labels, initial.counts, initial.amounts);

    document.getElementById('open-trend-table').addEventListener('click', function () {
        document.getElementById('trend-table-modal').classList.add('open');
    });
    document.getElementById('trend-table-modal').addEventListener('click', function (e) {
        if (e.target === this) closeTrendTableModal();
    });
    window.closeTrendTableModal = function () {
        document.getElementById('trend-table-modal').classList.remove('open');
    };

    document.querySelectorAll('.range-btn').forEach((btn) => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.range-btn').forEach((b) => b.classList.remove('active'));
            btn.classList.add('active');

            trendChart.canvas.style.opacity = '0.5';

            fetch(`{{ route('admin.dashboard.trends') }}?days=${btn.dataset.days}`)
                .then((r) => r.json())
                .then((data) => {
                    trendChart.data.labels = data.labels;
                    trendChart.data.datasets[0].data = data.amounts;
                    trendChart.data.datasets[1].data = data.counts;
                    trendChart.update();
                    renderTable(data.labels, data.counts, data.amounts);
                    trendChart.canvas.style.opacity = '1';
                });
        });
    });
})();
</script>
@endpush
</x-app-layout>
