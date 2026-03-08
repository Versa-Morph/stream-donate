<x-app-layout>
    @push('styles')
    <style>
        .admin-wrap { max-width: 1200px; margin: 0 auto; padding: 28px 28px 48px; }
        .page-header {
            display: flex; align-items: flex-start; justify-content: space-between;
            margin-bottom: 24px; gap: 16px;
        }
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
        .filter-bar input, .filter-bar select {
            width: auto; flex: 1; min-width: 160px; max-width: 280px;
            padding: 8px 12px; font-size: 13px;
        }
        .filter-bar button {
            padding: 8px 18px; font-size: 13px; font-weight: 600;
            background: var(--brand); color: #fff;
            border: none; border-radius: var(--radius); cursor: pointer;
            transition: opacity .15s;
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
            padding: 13px 16px; border-top: 1px solid var(--border);
            font-size: 13px; color: var(--text-2); vertical-align: middle;
        }
        tr:hover td { background: var(--surface-2); }

        /* ── BADGE ── */
        .badge {
            display: inline-block; padding: 2px 9px;
            border-radius: 20px; font-size: 10px; font-weight: 700;
            letter-spacing: .3px; text-transform: uppercase;
        }
        .badge-admin  { background: rgba(168,85,247,.12); color: #c084fc; border: 1px solid rgba(168,85,247,.25); }
        .badge-stream { background: rgba(124,108,252,.12); color: var(--brand-light); border: 1px solid rgba(124,108,252,.25); }
        .badge-active { background: rgba(34,211,160,.10);  color: var(--green);        border: 1px solid rgba(34,211,160,.25); }
        .badge-inactive { background: rgba(244,63,94,.10); color: var(--red);          border: 1px solid rgba(244,63,94,.25); }

        /* ── ACTIONS ── */
        .actions { display: flex; gap: 6px; align-items: center; flex-wrap: wrap; }
        .btn-xs {
            padding: 4px 10px; font-size: 11px; font-weight: 600;
            border-radius: var(--radius-sm); cursor: pointer;
            border: 1px solid var(--border); background: var(--surface-2);
            color: var(--text-2); transition: all .15s;
        }
        .btn-xs:hover { border-color: var(--border-2); color: var(--text); }
        .btn-xs.danger { border-color: rgba(244,63,94,.3); color: var(--red); background: rgba(244,63,94,.06); }
        .btn-xs.danger:hover { background: rgba(244,63,94,.12); }
        .btn-xs.success { border-color: rgba(34,211,160,.3); color: var(--green); background: rgba(34,211,160,.06); }
        .btn-xs.success:hover { background: rgba(34,211,160,.12); }
        .btn-xs.warn { border-color: rgba(251,191,36,.3); color: var(--yellow); background: rgba(251,191,36,.06); }
        .btn-xs.warn:hover { background: rgba(251,191,36,.12); }

        /* ── MODAL ── */
        .modal-overlay {
            display: none; position: fixed; inset: 0; z-index: 1000;
            background: rgba(0,0,0,.7); backdrop-filter: blur(4px);
            align-items: center; justify-content: center;
        }
        .modal-overlay.open { display: flex; }
        .modal {
            background: var(--surface);
            border: 1px solid var(--border-2);
            border-radius: var(--radius-xl);
            padding: 28px 32px;
            width: 380px; max-width: 92vw;
        }
        .modal-title { font-size: 16px; font-weight: 700; color: var(--text); margin-bottom: 18px; }
        .modal-footer { display: flex; gap: 10px; justify-content: flex-end; margin-top: 22px; }

        /* ── PAGINATION ── */
        .pagination {
            display: flex; gap: 4px; align-items: center;
            justify-content: center; margin-top: 20px;
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

        .empty-cell { text-align: center; color: var(--text-3); padding: 40px; font-size: 13px; }
    </style>
    @endpush

    <div class="admin-wrap">
        <!-- Header -->
        <div class="page-header">
            <div>
                <h1 class="page-title">Manajemen User</h1>
                <p class="page-subtitle">{{ $users->total() }} user terdaftar</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="btn-primary" style="padding:10px 20px; font-size:13px; text-decoration:none; display:inline-flex; align-items:center; gap:6px">
                + Tambah User
            </a>
        </div>

        <!-- Filter -->
        <form method="GET" action="{{ route('admin.users') }}">
            <div class="filter-bar">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Cari nama atau email…">
                <select name="role" style="max-width:160px">
                    <option value="">Semua Role</option>
                    <option value="admin"    {{ request('role') === 'admin'    ? 'selected' : '' }}>Admin</option>
                    <option value="streamer" {{ request('role') === 'streamer' ? 'selected' : '' }}>Streamer</option>
                </select>
                <button type="submit">Filter</button>
                @if(request('search') || request('role'))
                    <a href="{{ route('admin.users') }}" class="btn-xs">Reset</a>
                @endif
            </div>
        </form>

        <!-- Table -->
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Streamer</th>
                        <th>Terdaftar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div style="font-weight:600; color:var(--text)">{{ $user->name }}</div>
                            <div style="font-size:11px; color:var(--text-3)">{{ $user->email }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $user->role === 'admin' ? 'badge-admin' : 'badge-stream' }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $user->is_active ? 'badge-active' : 'badge-inactive' }}">
                                {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            @if($user->streamer)
                                <a href="{{ route('donate.show', $user->streamer->slug) }}" target="_blank"
                                    style="color:var(--brand-light); font-size:12px">
                                    {{ $user->streamer->display_name }}
                                </a>
                            @else
                                <span style="color:var(--text-3); font-size:12px">—</span>
                            @endif
                        </td>
                        <td style="font-size:11px; color:var(--text-3)">
                            {{ $user->created_at->format('d M Y') }}
                        </td>
                        <td>
                            <div class="actions">
                                {{-- Toggle aktif --}}
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.toggle', $user) }}" style="display:inline">
                                    @csrf
                                    <button type="submit" class="btn-xs {{ $user->is_active ? 'danger' : 'success' }}">
                                        {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>
                                @endif

                                {{-- Reset password --}}
                                <button type="button" class="btn-xs warn"
                                    onclick="openResetModal({{ $user->id }}, '{{ addslashes($user->email) }}')">
                                    Reset PW
                                </button>

                                {{-- Impersonate (hanya streamer) --}}
                                @if($user->isStreamer())
                                <form method="POST" action="{{ route('admin.impersonate', $user) }}" style="display:inline">
                                    @csrf
                                    <button type="submit" class="btn-xs">Login As</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="empty-cell">Tidak ada user ditemukan</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
        <div class="pagination">
            {{ $users->links() }}
        </div>
        @endif
    </div>

    <!-- Modal Reset Password -->
    <div class="modal-overlay" id="reset-modal">
        <div class="modal">
            <div class="modal-title">Reset Password</div>
            <form method="POST" id="reset-form" action="">
                @csrf
                <p style="font-size:13px; color:var(--text-3); margin-bottom:16px">
                    Reset password untuk: <strong id="reset-email" style="color:var(--text)"></strong>
                </p>
                <div class="form-group">
                    <label>Password Baru</label>
                    <input type="password" name="password" required minlength="8" placeholder="Min. 8 karakter">
                </div>
                <div class="form-group">
                    <label>Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" required placeholder="Ulangi password">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-xs" onclick="closeResetModal()">Batal</button>
                    <button type="submit" class="btn-primary" style="padding:8px 20px; font-size:13px">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
    const BASE_RESET_URL = '{{ url("/admin/users") }}';

    function openResetModal(userId, email) {
        document.getElementById('reset-email').textContent = email;
        document.getElementById('reset-form').action = BASE_RESET_URL + '/' + userId + '/reset-password';
        document.getElementById('reset-modal').classList.add('open');
    }
    function closeResetModal() {
        document.getElementById('reset-modal').classList.remove('open');
    }
    document.getElementById('reset-modal').addEventListener('click', function(e) {
        if (e.target === this) closeResetModal();
    });
    </script>
    @endpush
</x-app-layout>
