<x-app-layout>
    @push('styles')
    <style>
        .admin-wrap { max-width: 1300px; margin: 0 auto; padding: 28px 28px 48px; }
        .page-header { margin-bottom: 24px; }
        .page-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 24px; font-weight: 700; letter-spacing: -.5px; color: var(--text);
        }
        .page-subtitle { font-size: 13px; color: var(--text-3); margin-top: 4px; }

        /* ── FILTER BAR ── */
        .filter-bar {
            display: flex; gap: 10px; align-items: center; flex-wrap: wrap;
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius-lg); padding: 14px 18px;
            margin-bottom: 20px;
        }
        .filter-bar input {
            width: auto; flex: 1; min-width: 200px; max-width: 360px;
            padding: 8px 12px; font-size: 13px;
        }
        .filter-bar button {
            padding: 8px 18px; font-size: 13px; font-weight: 600;
            background: var(--brand); color: #fff;
            border: none; border-radius: var(--radius); cursor: pointer;
        }
        .filter-bar button:hover { opacity: .85; }

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
            font-size: 13px; color: var(--text-2); vertical-align: middle;
        }
        tr:hover td { background: var(--surface-2); }
        .empty-cell { text-align: center; color: var(--text-3); padding: 40px; font-size: 13px; }

        /* ── ACTION BADGE ── */
        .action-badge {
            display: inline-block; padding: 2px 8px;
            border-radius: 6px; font-size: 10px; font-weight: 600;
            font-family: monospace; letter-spacing: .3px;
            background: rgba(124,108,252,.1); color: var(--brand-light);
            border: 1px solid rgba(124,108,252,.2);
            white-space: nowrap;
        }
        .action-badge.donation { background: rgba(34,211,160,.08); color: var(--green); border-color: rgba(34,211,160,.2); }
        .action-badge.admin    { background: rgba(168,85,247,.08); color: #c084fc;       border-color: rgba(168,85,247,.2); }
        .action-badge.user     { background: rgba(251,191,36,.08); color: var(--yellow); border-color: rgba(251,191,36,.2); }

        /* ── PAGINATION ── */
        .pagination {
            display: flex; gap: 4px; align-items: center;
            justify-content: center; margin-top: 20px; flex-wrap: wrap;
        }
        .pagination a, .pagination span {
            padding: 6px 12px; border-radius: var(--radius-sm);
            font-size: 13px; background: var(--surface); border: 1px solid var(--border);
            color: var(--text-2);
        }
        .pagination a:hover { border-color: var(--brand); color: var(--brand-light); }
        .pagination .active span {
            background: var(--brand); border-color: var(--brand); color: #fff;
        }
    </style>
    @endpush

    <div class="admin-wrap">
        <!-- Header -->
        <div class="page-header">
            <h1 class="page-title">Activity Logs</h1>
            <p class="page-subtitle">{{ $logs->total() }} entri log aktivitas</p>
        </div>

        <!-- Filter by action -->
        <form method="GET" action="{{ route('admin.logs') }}">
            <div class="filter-bar">
                <input type="text" name="action" value="{{ request('action') }}"
                    placeholder="Filter berdasarkan action (cth: donation, admin, user)…">
                <button type="submit">Filter</button>
                @if(request('action'))
                    <a href="{{ route('admin.logs') }}" class="btn-sm" style="font-size:12px;padding:7px 14px">Reset</a>
                @endif
            </div>
        </form>

        <!-- Table -->
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Action</th>
                        <th>Deskripsi</th>
                        <th>User</th>
                        <th>Streamer</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    @php
                        $actionClass = '';
                        if (str_contains($log->action, 'donation')) $actionClass = 'donation';
                        elseif (str_contains($log->action, 'admin')) $actionClass = 'admin';
                        elseif (str_contains($log->action, 'user'))  $actionClass = 'user';
                    @endphp
                    <tr>
                        <td style="font-size:11px; color:var(--text-3); white-space:nowrap">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td>
                            <span class="action-badge {{ $actionClass }}">{{ $log->action }}</span>
                        </td>
                        <td style="max-width:360px; font-size:12px">
                            {{ $log->description }}
                        </td>
                        <td style="font-size:12px; color:var(--text-3)">
                            {{ $log->user?->name ?? '—' }}
                        </td>
                        <td style="font-size:12px; color:var(--text-3)">
                            {{ $log->streamer?->display_name ?? '—' }}
                        </td>
                        <td style="font-size:11px; color:var(--text-3); font-family:monospace">
                            {{ $log->ip_address ?? '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="empty-cell">Tidak ada log aktivitas</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($logs->hasPages())
        <div class="pagination">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
