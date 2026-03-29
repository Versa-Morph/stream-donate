<x-app-layout>
    @push('styles')
    <style>
        /* ── Uses unified .page-container, .page-header, .filter-bar, .table-card, .pagination from app.blade.php ── */

        /* ── TABLE (local overrides) ── */
        table { width: 100%; border-collapse: collapse; }
        thead { background: var(--surface-2); }
        th {
            padding: 11px 16px; text-align: left;
            font-size: 11px; font-weight: 700; letter-spacing: .7px;
            text-transform: uppercase; color: var(--text-3);
        }
        td {
            padding: 12px 16px; border-top: 1px solid var(--border);
            font-size: 13px; color: var(--text-2); vertical-align: middle;
        }
        tr:hover td { background: var(--surface-2); }
        .amount-cell {
            font-family: 'Space Grotesk', sans-serif;
            font-weight: 700; color: var(--green);
        }
        .empty-cell { text-align: center; color: var(--text-3); padding: 40px; font-size: 13px; }

        .btn-xs {
            padding: 4px 10px; font-size: 11px; font-weight: 600;
            border-radius: var(--radius-sm); cursor: pointer;
            border: 1px solid rgba(244,63,94,.3); background: rgba(244,63,94,.06);
            color: var(--red); transition: all .15s;
        }
        .btn-xs:hover { background: rgba(244,63,94,.12); }

        /* ── TRUNCATED TEXT ── */
        .msg-cell {
            max-width: 200px; white-space: nowrap;
            overflow: hidden; text-overflow: ellipsis;
            font-style: italic; color: var(--text-3);
        }
    </style>
    @endpush

    <div class="page-container">
        <!-- Header -->
        <div class="page-header">
            <div class="page-header-left">
                <h1 class="page-title">Semua Donasi</h1>
                <p class="page-subtitle">{{ $donations->total() }} donasi dari semua streamer</p>
            </div>
        </div>

        <!-- Filter -->
        <form method="GET" action="{{ route('admin.donations') }}">
            <div class="filter-bar">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama donatur / pesan…">
                <select name="streamer_id" style="max-width:220px">
                    <option value="">Semua Streamer</option>
                    @foreach($streamers as $s)
                    <option value="{{ $s->id }}" {{ request('streamer_id') == $s->id ? 'selected' : '' }}>
                        {{ $s->display_name }}
                    </option>
                    @endforeach
                </select>
                <button type="submit" class="btn-filter">Filter</button>
                @if(request('search') || request('streamer_id'))
                    <a href="{{ route('admin.donations') }}" class="btn-sm" style="font-size:12px;padding:7px 14px">Reset</a>
                @endif
            </div>
        </form>

        <!-- Table -->
        <div class="table-card">
            <table>
                <thead>
                    <tr>
                        <th>Donatur</th>
                        <th>Nominal</th>
                        <th>Pesan</th>
                        <th>Streamer</th>
                        <th>Waktu</th>
                        <th>YT</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($donations as $d)
                    <tr>
                        <td>
                            <span style="margin-right:5px; font-size:16px">{{ $d->emoji ?? '🎉' }}</span>
                            <span style="font-weight:600; color:var(--text)">{{ $d->name }}</span>
                        </td>
                        <td class="amount-cell">Rp {{ number_format($d->amount) }}</td>
                        <td class="msg-cell" title="{{ $d->message }}">
                            {{ $d->message ?: '—' }}
                        </td>
                        <td>
                            @if($d->streamer)
                                <a href="{{ route('donate.show', $d->streamer->slug) }}" target="_blank"
                                    style="color:var(--brand-light); font-size:12px">
                                    {{ $d->streamer->display_name }}
                                </a>
                            @else
                                <span style="color:var(--text-3)">—</span>
                            @endif
                        </td>
                        <td style="font-size:11px; color:var(--text-3); white-space:nowrap">
                            {{ $d->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td>
                            @if($d->yt_url)
                                <a href="{{ $d->yt_url }}" target="_blank" style="font-size:11px; color:var(--red)">▶ YT</a>
                            @else
                                <span style="color:var(--text-3); font-size:11px">—</span>
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('admin.donations.delete', $d) }}"
                                onsubmit="return confirm('Hapus donasi dari {{ addslashes($d->name) }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-xs">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="empty-cell">Tidak ada donasi ditemukan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($donations->hasPages())
        <div class="pagination">
            {{ $donations->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
