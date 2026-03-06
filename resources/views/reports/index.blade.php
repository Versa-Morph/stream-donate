<x-app-layout>
    @push('styles')
    <style>
        .report-wrap { max-width: 1200px; margin: 0 auto; padding: 28px 28px 60px; }

        /* ── PAGE HEADER ── */
        .page-header {
            display: flex; align-items: flex-start; justify-content: space-between;
            margin-bottom: 24px; gap: 16px; flex-wrap: wrap;
        }
        .page-header-left { display: flex; align-items: center; gap: 14px; }
        .page-header-icon {
            width: 44px; height: 44px; border-radius: 14px; flex-shrink: 0;
            background: rgba(124,108,252,.12); border: 1px solid rgba(124,108,252,.25);
            display: flex; align-items: center; justify-content: center;
        }
        .page-header-icon .iconify { width: 22px; height: 22px; color: var(--brand-light); }
        .page-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 22px; font-weight: 700; letter-spacing: -.5px; color: var(--text);
        }
        .page-subtitle { font-size: 12px; color: var(--text-3); margin-top: 4px; }

        /* ── FILTER BAR ── */
        .filter-bar {
            display: flex; gap: 10px; align-items: center; flex-wrap: wrap;
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius-lg); padding: 14px 18px;
            margin-bottom: 24px;
        }
        .filter-bar-icon { color: var(--text-3); }
        .filter-bar-icon .iconify { width: 16px; height: 16px; }
        .filter-label { font-size: 12px; font-weight: 600; color: var(--text-3); white-space: nowrap; }
        .filter-bar input[type="date"] {
            width: auto; padding: 8px 12px; font-size: 13px;
        }
        .filter-sep { font-size: 12px; color: var(--text-3); }
        .btn-filter {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px; border-radius: var(--radius-sm);
            font-size: 12px; font-weight: 700; cursor: pointer; border: none;
            background: linear-gradient(135deg, var(--brand), #6356e8);
            color: #fff; font-family: 'Inter', sans-serif;
            box-shadow: 0 2px 10px var(--brand-glow); transition: all .15s;
        }
        .btn-filter:hover { transform: translateY(-1px); }
        .btn-filter .iconify { width: 13px; height: 13px; }
        .filter-actions { display: flex; gap: 8px; margin-left: auto; flex-wrap: wrap; }
        .btn-export {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 8px 16px; border-radius: var(--radius-sm);
            font-size: 12px; font-weight: 700; cursor: pointer;
            transition: all .15s; text-decoration: none;
            border: 1px solid var(--border); background: var(--surface-2); color: var(--text-2);
        }
        .btn-export .iconify { width: 14px; height: 14px; }
        .btn-export:hover { border-color: var(--border-2); color: var(--text); }
        .btn-export.csv  { border-color: rgba(34,211,160,.3);  color: var(--green);  background: rgba(34,211,160,.06); }
        .btn-export.pdf  { border-color: rgba(249,115,22,.3);  color: var(--orange); background: rgba(249,115,22,.06); }
        .btn-export.csv:hover  { background: rgba(34,211,160,.12); border-color: rgba(34,211,160,.5); }
        .btn-export.pdf:hover  { background: rgba(249,115,22,.12); border-color: rgba(249,115,22,.5); }

        /* ── STATS GRID ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 14px; margin-bottom: 28px;
        }
        .stat-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius-lg); padding: 18px 20px;
            position: relative; overflow: hidden; transition: border-color .2s;
        }
        .stat-card:hover { border-color: var(--border-2); }
        .stat-card::after {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
        }
        .stat-card.c-brand::after  { background: var(--brand); }
        .stat-card.c-orange::after { background: var(--orange); }
        .stat-card.c-green::after  { background: var(--green); }
        .stat-card.c-yellow::after { background: var(--yellow); }
        .stat-card.c-purple::after { background: var(--purple); }
        .stat-icon {
            width: 34px; height: 34px; border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 14px;
            border: 1px solid color-mix(in srgb, var(--c, var(--brand)) 25%, transparent);
            background: color-mix(in srgb, var(--c, var(--brand)) 10%, transparent);
        }
        .stat-icon .iconify { width: 18px; height: 18px; color: var(--c, var(--brand)); }
        .stat-label { font-size: 10px; font-weight: 700; letter-spacing: 1px;
            text-transform: uppercase; color: var(--text-3); margin-bottom: 7px; }
        .stat-value { font-family: 'Space Grotesk', sans-serif;
            font-size: 22px; font-weight: 700; letter-spacing: -.5px; color: var(--text); }
        .stat-sub { font-size: 10px; color: var(--text-3); margin-top: 4px; }

        /* ── CHART ── */
        .chart-section {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius-lg); padding: 20px 24px;
            margin-bottom: 24px;
        }
        .chart-header { display: flex; align-items: center; gap: 10px; margin-bottom: 16px; }
        .chart-header .iconify { width: 16px; height: 16px; color: var(--brand-light); }
        .chart-title { font-size: 13px; font-weight: 700; color: var(--text-2); }
        #report-chart { width: 100%; display: block; }

        /* ── TWO COL ── */
        .two-col { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        @media (max-width: 800px) { .two-col { grid-template-columns: 1fr; } }

        /* ── TABLE ── */
        .table-section {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius-lg); overflow: hidden;
        }
        .table-header {
            padding: 14px 18px 12px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; font-weight: 700; color: var(--text-2);
        }
        .table-header .iconify { width: 16px; height: 16px; color: var(--text-3); }
        .table-chip { margin-left: auto; background: var(--surface-3); border: 1px solid var(--border); color: var(--text-3); padding: 2px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; }
        thead { background: var(--surface-2); }
        th {
            padding: 10px 16px; text-align: left;
            font-size: 10px; font-weight: 700; letter-spacing: .7px;
            text-transform: uppercase; color: var(--text-3);
        }
        td {
            padding: 11px 16px; border-top: 1px solid var(--border);
            font-size: 12px; color: var(--text-2); vertical-align: middle;
        }
        tr:hover td { background: var(--surface-2); }
        .amount-cell {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700; color: var(--green); font-size: 13px;
        }
        .empty-cell { text-align: center; color: var(--text-3); padding: 36px; font-size: 12px; }

        /* ── RANK MEDALS ── */
        .rank-wrap { display: flex; align-items: center; gap: 4px; }
        .rank-badge {
            width: 22px; height: 22px; border-radius: 7px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 800; font-family: 'Space Grotesk', sans-serif;
        }
        .rank-badge.gold   { background: rgba(251,191,36,.15); color: var(--yellow); border: 1px solid rgba(251,191,36,.3); }
        .rank-badge.silver { background: rgba(160,160,180,.12); color: #a0a0b4; border: 1px solid rgba(160,160,180,.25); }
        .rank-badge.bronze { background: rgba(205,127,50,.12);  color: #cd7f32; border: 1px solid rgba(205,127,50,.25); }
        .rank-plain { font-family: 'Space Grotesk', sans-serif; font-size: 12px; font-weight: 700; color: var(--text-3); }
        .donor-emoji { margin-right: 5px; }

        /* ── EMPTY STATE ── */
        .report-empty {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius-lg); padding: 60px 24px;
            text-align: center; color: var(--text-3);
        }
        .report-empty .iconify { width: 40px; height: 40px; margin-bottom: 12px; display: block; margin-left: auto; margin-right: auto; opacity: .4; }
        .report-empty-title { font-size: 14px; font-weight: 600; color: var(--text-2); margin-bottom: 6px; }
        .report-empty-sub { font-size: 12px; }

        @media(max-width:640px) { .filter-bar { flex-direction: column; align-items: flex-start; } .filter-actions { margin-left: 0; } }
    </style>
    @endpush

    <div class="report-wrap">

        <!-- Header -->
        <div class="page-header">
            <div class="page-header-left">
                <div class="page-header-icon">
                    <span class="iconify" data-icon="solar:chart-2-bold-duotone"></span>
                </div>
                <div>
                    <h1 class="page-title">Laporan Donasi</h1>
                    <p class="page-subtitle">{{ $streamer->display_name }} &mdash; {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}</p>
                </div>
            </div>
        </div>

        <!-- Filter + Export -->
        <form method="GET" action="{{ route('streamer.reports') }}">
            <div class="filter-bar">
                <span class="filter-bar-icon"><span class="iconify" data-icon="solar:calendar-bold-duotone"></span></span>
                <span class="filter-label">Periode:</span>
                <input type="date" name="from" value="{{ $dateFrom }}">
                <span class="filter-sep">s/d</span>
                <input type="date" name="to" value="{{ $dateTo }}">
                <button type="submit" class="btn-filter">
                    <span class="iconify" data-icon="solar:refresh-bold-duotone"></span>
                    Tampilkan
                </button>
                <div class="filter-actions">
                    <a href="{{ route('streamer.reports.csv', ['from' => $dateFrom, 'to' => $dateTo]) }}"
                        class="btn-export csv">
                        <span class="iconify" data-icon="solar:download-minimalistic-bold-duotone"></span>
                        Export CSV
                    </a>
                    <a href="{{ route('streamer.reports.pdf', ['from' => $dateFrom, 'to' => $dateTo]) }}"
                        class="btn-export pdf" target="_blank">
                        <span class="iconify" data-icon="solar:file-pdf-bold-duotone"></span>
                        Export PDF
                    </a>
                </div>
            </div>
        </form>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card c-green" style="--c:var(--green)">
                <div class="stat-icon">
                    <span class="iconify" data-icon="solar:wallet-money-bold-duotone"></span>
                </div>
                <div class="stat-label">Total Terkumpul</div>
                <div class="stat-value">Rp {{ number_format($totalAmount) }}</div>
                <div class="stat-sub">dalam periode ini</div>
            </div>
            <div class="stat-card c-brand" style="--c:var(--brand)">
                <div class="stat-icon">
                    <span class="iconify" data-icon="solar:heart-bold-duotone"></span>
                </div>
                <div class="stat-label">Jumlah Donasi</div>
                <div class="stat-value">{{ number_format($totalCount) }}</div>
                <div class="stat-sub">transaksi</div>
            </div>
            <div class="stat-card c-orange" style="--c:var(--orange)">
                <div class="stat-icon">
                    <span class="iconify" data-icon="solar:users-group-rounded-bold-duotone"></span>
                </div>
                <div class="stat-label">Donatur Unik</div>
                <div class="stat-value">{{ number_format($uniqueDonors) }}</div>
                <div class="stat-sub">berdasarkan nama</div>
            </div>
            <div class="stat-card c-yellow" style="--c:var(--yellow)">
                <div class="stat-icon">
                    <span class="iconify" data-icon="solar:chart-bold-duotone"></span>
                </div>
                <div class="stat-label">Rata-rata</div>
                <div class="stat-value">Rp {{ number_format($avgAmount) }}</div>
                <div class="stat-sub">per donasi</div>
            </div>
            @if($maxDonation)
            <div class="stat-card c-purple" style="--c:var(--purple)">
                <div class="stat-icon">
                    <span class="iconify" data-icon="solar:cup-star-bold-duotone"></span>
                </div>
                <div class="stat-label">Donasi Terbesar</div>
                <div class="stat-value">Rp {{ number_format($maxDonation->amount) }}</div>
                <div class="stat-sub">dari {{ $maxDonation->name }}</div>
            </div>
            @endif
        </div>

        <!-- Bar Chart (harian) — Canvas -->
        @if($dailyData->count() > 0)
        <div class="chart-section">
            <div class="chart-header">
                <span class="iconify" data-icon="solar:chart-2-bold-duotone"></span>
                <div class="chart-title">Donasi Harian</div>
            </div>
            <canvas id="report-chart"></canvas>
        </div>
        @endif

        <!-- Two col: Top donors + Recent donations -->
        @if($totalCount > 0)
        <div class="two-col">

            <!-- Top 10 Donatur -->
            <div class="table-section">
                <div class="table-header">
                    <span class="iconify" data-icon="solar:ranking-bold-duotone"></span>
                    Top 10 Donatur
                    <span class="table-chip">{{ min(10, count($topDonors)) }}</span>
                </div>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Donatur</th>
                            <th>Total</th>
                            <th>Kali</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topDonors as $i => $donor)
                        <tr>
                            <td>
                                @if($i === 0)
                                    <span class="rank-badge gold">1</span>
                                @elseif($i === 1)
                                    <span class="rank-badge silver">2</span>
                                @elseif($i === 2)
                                    <span class="rank-badge bronze">3</span>
                                @else
                                    <span class="rank-plain">{{ $i + 1 }}</span>
                                @endif
                            </td>
                            <td>
                                <span class="donor-emoji">{{ $donor['emoji'] ?? '🎉' }}</span>
                                {{ $donor['name'] }}
                            </td>
                            <td class="amount-cell">Rp {{ number_format($donor['total']) }}</td>
                            <td style="color:var(--text-3)">{{ $donor['count'] }}×</td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="empty-cell">Belum ada data</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Riwayat Donasi -->
            <div class="table-section">
                <div class="table-header">
                    <span class="iconify" data-icon="solar:history-bold-duotone"></span>
                    Riwayat Donasi
                    <span class="table-chip">{{ $totalCount }}</span>
                </div>
                <div style="max-height:440px; overflow-y:auto">
                    <table>
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Donatur</th>
                                <th>Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($donations as $d)
                            <tr>
                                <td style="font-size:10px; color:var(--text-3); white-space:nowrap">
                                    {{ $d->created_at->format('d/m H:i') }}
                                </td>
                                <td>
                                    <span style="margin-right:4px">{{ $d->emoji ?? '🎉' }}</span>
                                    {{ Str::limit($d->name, 22) }}
                                </td>
                                <td class="amount-cell" style="font-size:12px">
                                    Rp {{ number_format($d->amount) }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="empty-cell">Tidak ada donasi dalam periode ini</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        <div class="report-empty">
            <span class="iconify" data-icon="solar:chart-2-bold-duotone"></span>
            <div class="report-empty-title">Tidak ada donasi dalam periode ini</div>
            <div class="report-empty-sub">Coba ubah rentang tanggal di atas</div>
        </div>
        @endif

    </div>

    @push('scripts')
    <script>
    // ── Canvas bar chart (Reports) ──
    (function () {
        const raw = @json($dailyData);
        if (!raw || !raw.length) return;
        const canvas = document.getElementById('report-chart');
        if (!canvas) return;

        const dpr  = window.devicePixelRatio || 1;
        const W    = canvas.parentElement.offsetWidth - 48;
        const H    = 140;
        canvas.width  = W * dpr;
        canvas.height = H * dpr;
        canvas.style.width  = W + 'px';
        canvas.style.height = H + 'px';

        const ctx = canvas.getContext('2d');
        ctx.scale(dpr, dpr);

        const maxVal   = Math.max(...raw.map(d => d.total), 1);
        const count    = raw.length;
        const padX     = 8;
        const gap      = 4;
        const barAreaW = W - padX * 2;
        const barW     = Math.max(10, Math.floor((barAreaW - gap * (count - 1)) / count));
        const barAreaH = H - 30;

        raw.forEach(function (d, i) {
            const x   = padX + i * (barW + gap);
            const pct = d.total / maxVal;
            const bH  = Math.max(pct * barAreaH, d.total > 0 ? 4 : 1);
            const y   = barAreaH - bH + 4;

            // bg slot
            ctx.fillStyle = 'rgba(255,255,255,.04)';
            ctx.beginPath();
            ctx.roundRect(x, 4, barW, barAreaH, 4);
            ctx.fill();

            if (d.total > 0) {
                const grad = ctx.createLinearGradient(x, y + bH, x, y);
                grad.addColorStop(0, 'rgba(124,108,252,.55)');
                grad.addColorStop(1, 'rgba(124,108,252,1)');
                ctx.fillStyle = grad;
                ctx.beginPath();
                ctx.roundRect(x, y, barW, bH, 4);
                ctx.fill();

                // Glow top
                ctx.fillStyle = 'rgba(169,157,255,.7)';
                ctx.beginPath();
                ctx.roundRect(x, y, barW, Math.min(3, bH), 2);
                ctx.fill();
            }

            // label — show every 3rd or if few bars
            if (count <= 14 || i % Math.ceil(count / 14) === 0) {
                ctx.fillStyle = 'rgba(96,96,120,.9)';
                ctx.font = '9px Inter, sans-serif';
                ctx.textAlign = 'center';
                const label = d.date.substring(0, 5);
                ctx.fillText(label, x + barW / 2, H - 6);
            }
        });
    })();
    </script>
    @endpush
</x-app-layout>
