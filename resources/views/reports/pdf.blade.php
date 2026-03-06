<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Donasi — {{ $streamer->display_name }}</title>
    <style>
        /* DomPDF uses inline styles + basic CSS */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1a1a2e;
            background: #ffffff;
        }

        /* ── HEADER ── */
        .header {
            background: #7c6cfc;
            color: #fff;
            padding: 22px 28px 20px;
            margin-bottom: 0;
        }
        .header-brand {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            opacity: 0.65;
            margin-bottom: 6px;
        }
        .header-streamer {
            font-size: 26px;
            font-weight: 700;
            letter-spacing: -0.8px;
            line-height: 1.1;
        }
        .header-doc-type {
            font-size: 12px;
            opacity: 0.75;
            margin-top: 4px;
        }
        .header-meta {
            font-size: 10px;
            opacity: 0.65;
            margin-top: 8px;
            padding-top: 10px;
            border-top: 1px solid rgba(255,255,255,.25);
        }

        /* ── SUMMARY BOX ── */
        .summary-section {
            background: #f4f3ff;
            border: 2px solid #e0deff;
            border-top: 3px solid #7c6cfc;
            padding: 16px 20px;
            margin-bottom: 0;
        }
        .summary-title {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #7c6cfc;
            margin-bottom: 12px;
        }
        .summary {
            display: table;
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
        }
        .summary-row { display: table-row; }
        .stat-box {
            display: table-cell;
            width: 25%;
            background: #ffffff;
            border: 1px solid #e0deff;
            border-radius: 8px;
            padding: 12px 14px;
            text-align: center;
        }
        .stat-box-label {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: #6b6b8a;
            margin-bottom: 5px;
        }
        .stat-box-value {
            font-size: 17px;
            font-weight: 700;
            color: #7c6cfc;
        }

        /* ── SECTION TITLE ── */
        .section-title {
            font-size: 12px;
            font-weight: 700;
            color: #3a3a5c;
            border-bottom: 2px solid #7c6cfc;
            padding-bottom: 5px;
            margin-bottom: 12px;
            margin-top: 20px;
        }

        /* ── TABLE ── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        thead tr {
            background: #7c6cfc;
            color: #fff;
        }
        thead th {
            padding: 8px 10px;
            text-align: left;
            font-weight: 700;
            font-size: 9px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        tbody tr:nth-child(even) { background: #f8f8fd; }
        tbody tr:nth-child(odd)  { background: #ffffff; }
        td {
            padding: 7px 10px;
            border-bottom: 1px solid #ededf5;
            vertical-align: middle;
        }
        .td-amount {
            font-weight: 700;
            color: #059669;
            text-align: right;
        }
        .td-right { text-align: right; }

        /* ── FOOTER ── */
        .footer {
            margin-top: 28px;
            padding: 12px 20px;
            background: #f4f3ff;
            border: 1px solid #e0deff;
            border-radius: 6px;
            font-size: 9px;
            color: #7c6cfc;
            text-align: center;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        .footer-sub {
            font-size: 8px;
            color: #9090a8;
            font-weight: 400;
            margin-top: 3px;
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <div class="header-brand">StreamDonate &bull; Laporan Donasi</div>
    <div class="header-streamer">{{ $streamer->display_name }}</div>
    <div class="header-doc-type">Laporan Keuangan Donasi</div>
    <div class="header-meta">
        Periode: {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }}
        &ndash; {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}
        &nbsp;&bull;&nbsp;
        Dicetak: {{ now()->format('d M Y, H:i') }} WIB
    </div>
</div>

<!-- Summary -->
<div class="summary-section">
    <div class="summary-title">Ringkasan Periode</div>
    <table class="summary">
        <tr class="summary-row">
            <td class="stat-box">
                <div class="stat-box-label">Total Terkumpul</div>
                <div class="stat-box-value">Rp {{ number_format($totalAmount) }}</div>
            </td>
            <td class="stat-box">
                <div class="stat-box-label">Jumlah Donasi</div>
                <div class="stat-box-value">{{ number_format($totalCount) }}</div>
            </td>
            <td class="stat-box">
                <div class="stat-box-label">Donatur Unik</div>
                <div class="stat-box-value">{{ number_format($uniqueDonors) }}</div>
            </td>
            <td class="stat-box">
                <div class="stat-box-label">Rata-rata / Donasi</div>
                <div class="stat-box-value">
                    Rp {{ $totalCount > 0 ? number_format(intdiv($totalAmount, $totalCount)) : 0 }}
                </div>
            </td>
        </tr>
    </table>
</div>

<!-- Donation Table -->
<div class="section-title">Rincian Donasi</div>
<table>
    <thead>
        <tr>
            <th style="width:30px">No</th>
            <th>Tanggal &amp; Waktu</th>
            <th>Nama Donatur</th>
            <th>Emoji</th>
            <th>Pesan</th>
            <th style="text-align:right">Nominal (Rp)</th>
        </tr>
    </thead>
    <tbody>
        @forelse($donations as $i => $d)
        <tr>
            <td style="text-align:center; color:#9090a8">{{ $i + 1 }}</td>
            <td style="color:#6b6b8a; white-space:nowrap">{{ $d->created_at->format('d/m/Y H:i') }}</td>
            <td style="font-weight:600">{{ $d->name }}</td>
            <td style="text-align:center">{{ $d->emoji ?? '🎉' }}</td>
            <td style="color:#6b6b8a; font-style:italic">
                {{ $d->message ? \Str::limit($d->message, 60) : '—' }}
            </td>
            <td class="td-amount">{{ number_format($d->amount) }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="6" style="text-align:center; padding:20px; color:#9090a8">
                Tidak ada donasi dalam periode ini
            </td>
        </tr>
        @endforelse
    </tbody>
    @if($donations->count() > 0)
    <tfoot>
        <tr style="background:#7c6cfc; color:#fff">
            <td colspan="5" style="padding:8px 10px; font-weight:700; font-size:10px">TOTAL</td>
            <td class="td-right" style="padding:8px 10px; font-weight:700; font-size:12px; color:#fff">
                Rp {{ number_format($totalAmount) }}
            </td>
        </tr>
    </tfoot>
    @endif
</table>

<!-- Footer -->
<div class="footer">
    StreamDonate &bull; {{ $streamer->display_name }}
    <div class="footer-sub">
        Dicetak otomatis pada {{ now()->format('d M Y, H:i') }} WIB &bull; {{ config('app.url') }}
    </div>
</div>

</body>
</html>
