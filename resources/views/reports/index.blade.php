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

        /* ── EMPTY STATE ── */
        .report-empty {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius-lg); padding: 60px 24px;
            text-align: center; color: var(--text-3);
        }
        .report-empty .iconify { width: 40px; height: 40px; margin-bottom: 12px; display: block; margin-left: auto; margin-right: auto; opacity: .4; }
        .report-empty-title { font-size: 14px; font-weight: 600; color: var(--text-2); margin-bottom: 6px; }
        .report-empty-sub { font-size: 12px; }

        /* ── PAGINATION ── */
        .rp-pagination {
            display: flex; align-items: center; justify-content: space-between;
            gap: 12px; padding: 12px 18px;
            border-top: 1px solid var(--border);
            flex-wrap: wrap;
        }
        .rp-pagination-info { font-size: 11px; color: var(--text-3); }
        .rp-pagination-links { display: flex; gap: 4px; align-items: center; flex-wrap: wrap; }
        .rp-page-btn {
            min-width: 30px; height: 30px; padding: 0 8px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 7px; font-size: 12px; font-weight: 600;
            border: 1px solid var(--border); background: var(--surface-2);
            color: var(--text-2); text-decoration: none; cursor: pointer;
            transition: all .15s; line-height: 1;
        }
        .rp-page-btn:hover { border-color: rgba(124,108,252,.4); color: var(--brand-light); background: rgba(124,108,252,.06); }
        .rp-page-btn.active { background: var(--brand); border-color: var(--brand); color: #fff; cursor: default; }
        .rp-page-btn.disabled { opacity: .35; pointer-events: none; }
        .rp-page-ellipsis { font-size: 12px; color: var(--text-3); padding: 0 2px; }

        @media(max-width:640px) { .filter-bar { flex-direction: column; align-items: flex-start; } .filter-actions { margin-left: 0; } .rp-pagination { flex-direction: column; align-items: flex-start; } }
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

        @if($totalCount > 0)
        <!-- Riwayat Donasi — full width + pagination -->
        <div class="table-section" style="margin-bottom:24px">
            <div class="table-header">
                <span class="iconify" data-icon="solar:history-bold-duotone"></span>
                Riwayat Donasi
                <span class="table-chip">{{ $totalCount }}</span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th style="width:130px">Waktu</th>
                        <th>Donatur</th>
                        <th>Pesan</th>
                        <th style="text-align:right">Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($donationsPaginated as $d)
                    <tr>
                        <td style="font-size:11px; color:var(--text-3); white-space:nowrap">
                            {{ $d->created_at->format('d M Y') }}<br>
                            <span style="font-size:10px">{{ $d->created_at->format('H:i') }}</span>
                        </td>
                        <td>
                            <span style="margin-right:5px">{{ $d->emoji ?? '🎉' }}</span>
                            <span style="font-weight:600;color:var(--text)">{{ Str::limit($d->name, 28) }}</span>
                        </td>
                        <td style="color:var(--text-3);font-size:11px;max-width:280px">
                            {{ $d->message ? Str::limit($d->message, 60) : '—' }}
                        </td>
                        <td class="amount-cell" style="text-align:right">
                            Rp {{ number_format($d->amount) }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="empty-cell">Tidak ada donasi dalam periode ini</td></tr>
                    @endforelse
                </tbody>
            </table>

            @if($donationsPaginated->hasPages())
            <div class="rp-pagination">
                <div class="rp-pagination-info">
                    Menampilkan {{ $donationsPaginated->firstItem() }}–{{ $donationsPaginated->lastItem() }}
                    dari {{ number_format($donationsPaginated->total()) }} donasi
                </div>
                <div class="rp-pagination-links">
                    {{-- Prev --}}
                    @if($donationsPaginated->onFirstPage())
                        <span class="rp-page-btn disabled">&#8249;</span>
                    @else
                        <a class="rp-page-btn" href="{{ $donationsPaginated->previousPageUrl() }}">&#8249;</a>
                    @endif

                    {{-- Page numbers --}}
                    @foreach($donationsPaginated->getUrlRange(1, $donationsPaginated->lastPage()) as $page => $url)
                        @php
                            $cur  = $donationsPaginated->currentPage();
                            $last = $donationsPaginated->lastPage();
                            $show = ($page === 1 || $page === $last || abs($page - $cur) <= 2);
                            $ellipsisBefore = ($page === $cur - 3 && $cur - 3 > 1);
                            $ellipsisAfter  = ($page === $cur + 3 && $cur + 3 < $last);
                        @endphp
                        @if($ellipsisBefore || $ellipsisAfter)
                            <span class="rp-page-ellipsis">…</span>
                        @elseif($show)
                            @if($page === $cur)
                                <span class="rp-page-btn active">{{ $page }}</span>
                            @else
                                <a class="rp-page-btn" href="{{ $url }}">{{ $page }}</a>
                            @endif
                        @endif
                    @endforeach

                    {{-- Next --}}
                    @if($donationsPaginated->hasMorePages())
                        <a class="rp-page-btn" href="{{ $donationsPaginated->nextPageUrl() }}">&#8250;</a>
                    @else
                        <span class="rp-page-btn disabled">&#8250;</span>
                    @endif
                </div>
            </div>
            @endif
        </div>

        @else
        <div class="report-empty">
            <span class="iconify" data-icon="solar:chart-2-bold-duotone"></span>
            <div class="report-empty-title">Tidak ada donasi dalam periode ini</div>
            <div class="report-empty-sub">Coba ubah rentang tanggal di atas</div>
        </div>
        @endif

    </div>

</x-app-layout>
