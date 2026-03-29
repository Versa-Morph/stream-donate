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
    </style>
    @endpush

    <div class="page-container">
        <!-- Header -->
        <div class="page-header">
            <div class="page-header-left">
                <h1 class="page-title">Activity Logs</h1>
                <p class="page-subtitle">{{ $logs->total() }} entri log aktivitas</p>
            </div>
        </div>

        <!-- Filter by action -->
        <form method="GET" action="{{ route('admin.logs') }}">
            <div class="filter-bar">
                <input type="text" name="action" value="{{ request('action') }}"
                    placeholder="Filter berdasarkan action (cth: donation, admin, user)…">
                <button type="submit" class="btn-filter">Filter</button>
                @if(request('action'))
                    <a href="{{ route('admin.logs') }}" class="btn-sm" style="font-size:12px;padding:7px 14px">Reset</a>
                @endif
            </div>
        </form>

        <!-- Table -->
        <div class="table-card">
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
