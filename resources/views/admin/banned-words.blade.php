<x-app-layout>
    @push('styles')
    <style>
        .admin-wrap { max-width: 1100px; margin: 0 auto; padding: 28px 28px 48px; }
        .page-header { margin-bottom: 24px; }
        .page-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 24px; font-weight: 700; letter-spacing: -.5px; color: var(--text);
        }
        .page-subtitle { font-size: 13px; color: var(--text-3); margin-top: 4px; }

        /* ── LAYOUT ── */
        .bw-grid { display: grid; grid-template-columns: 340px 1fr; gap: 24px; align-items: start; }
        @media(max-width: 860px){ .bw-grid { grid-template-columns: 1fr; } }

        /* ── ADD FORM CARD ── */
        .add-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius-lg); padding: 22px;
            position: sticky; top: 20px;
        }
        .add-card h3 { font-size: 14px; font-weight: 700; color: var(--text); margin-bottom: 14px; }
        .add-card input[type="text"] {
            width: 100%; padding: 9px 12px; font-size: 13px;
            background: var(--surface-2); border: 1px solid var(--border);
            border-radius: var(--radius); color: var(--text);
            margin-bottom: 10px;
        }
        .add-card input[type="text"]:focus { outline: none; border-color: var(--brand); }
        .btn-add {
            width: 100%; padding: 9px 16px; font-size: 13px; font-weight: 600;
            background: var(--brand); color: #fff; border: none;
            border-radius: var(--radius); cursor: pointer; transition: opacity .2s;
        }
        .btn-add:hover { opacity: .85; }
        .add-hint { font-size: 11px; color: var(--text-3); margin-top: 8px; line-height: 1.5; }

        /* ── FILTER BAR ── */
        .filter-bar {
            display: flex; gap: 10px; align-items: center; flex-wrap: wrap;
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--radius-lg); padding: 12px 16px;
            margin-bottom: 16px;
        }
        .filter-bar input {
            flex: 1; min-width: 180px; padding: 8px 12px; font-size: 13px;
            background: var(--surface-2); border: 1px solid var(--border);
            border-radius: var(--radius); color: var(--text);
        }
        .filter-bar input:focus { outline: none; border-color: var(--brand); }
        .filter-bar button {
            padding: 8px 18px; font-size: 13px; font-weight: 600;
            background: var(--brand); color: #fff;
            border: none; border-radius: var(--radius); cursor: pointer;
        }
        .filter-bar button:hover { opacity: .85; }
        .filter-bar a {
            font-size: 12px; color: var(--text-3); text-decoration: none; padding: 8px 4px;
        }
        .filter-bar a:hover { color: var(--text); }

        /* ── TABLE ── */
        .table-wrap { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; font-size: 13px; }
        thead th {
            background: var(--surface-2); color: var(--text-3);
            font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase;
            padding: 10px 14px; text-align: left; border-bottom: 1px solid var(--border);
        }
        tbody td { padding: 10px 14px; border-bottom: 1px solid var(--border); color: var(--text); vertical-align: middle; }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: var(--surface-2); }
        .empty-cell { text-align: center; color: var(--text-3); padding: 32px 0 !important; }

        /* ── BADGES ── */
        .badge { display: inline-block; padding: 2px 8px; border-radius: 20px; font-size: 10px; font-weight: 700; letter-spacing: .5px; }
        .badge-global { background: rgba(124,108,252,.15); color: var(--brand-light); border: 1px solid rgba(124,108,252,.25); }
        .badge-streamer { background: rgba(34,211,160,.12); color: var(--green); border: 1px solid rgba(34,211,160,.2); }

        /* ── DELETE BUTTON ── */
        .btn-del {
            padding: 4px 10px; font-size: 11px; font-weight: 600;
            background: transparent; border: 1px solid var(--red);
            color: var(--red); border-radius: var(--radius-sm); cursor: pointer;
            transition: all .15s;
        }
        .btn-del:hover { background: var(--red); color: #fff; }

        /* ── PAGINATION ── */
        .pagination-wrap { padding: 14px 16px; display: flex; justify-content: flex-end; }

        /* ── STAT PILL ── */
        .stat-pill {
            display: inline-flex; align-items: center; gap: 6px;
            background: var(--surface-2); border: 1px solid var(--border);
            border-radius: 20px; padding: 4px 12px; font-size: 12px; color: var(--text-3);
            margin-bottom: 16px;
        }
        .stat-pill strong { color: var(--text); }
    </style>
    @endpush

    <div class="admin-wrap">
        <div class="page-header">
            <div class="page-title">Kata Terlarang</div>
            <div class="page-subtitle">
                Kelola daftar kata yang akan disensor otomatis (***) pada nama dan pesan donasi.
            </div>
        </div>

        @if(session('success'))
        <div class="flash flash-success" id="flash-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="flash flash-error" id="flash-error">{{ session('error') }}</div>
        @endif

        <div class="stat-pill">
            Total <strong>{{ $words->total() }} kata</strong> terdaftar
        </div>

        <div class="bw-grid">

            {{-- ── ADD FORM ── --}}
            <div class="add-card">
                <h3>Tambah Kata Global</h3>
                <form method="POST" action="{{ route('admin.banned-words.store') }}">
                    @csrf
                    <input
                        type="text"
                        name="word"
                        placeholder="Contoh: gacor, slot online ..."
                        maxlength="100"
                        autocomplete="off"
                        required
                    >
                    @error('word')
                        <div style="color:var(--red); font-size:12px; margin-bottom:8px;">{{ $message }}</div>
                    @enderror
                    <button type="submit" class="btn-add">+ Tambah ke Daftar Global</button>
                </form>
                <p class="add-hint">
                    Kata global berlaku untuk <strong>semua streamer</strong>.<br>
                    Masukkan dalam huruf kecil. Frasa multi-kata (misal: "slot gacor") juga didukung.
                </p>
            </div>

            {{-- ── TABLE ── --}}
            <div>
                {{-- Search --}}
                <form method="GET" action="{{ route('admin.banned-words.index') }}" class="filter-bar">
                    <input
                        type="text"
                        name="q"
                        value="{{ $search }}"
                        placeholder="Cari kata..."
                        autocomplete="off"
                    >
                    <button type="submit">Cari</button>
                    @if($search)
                        <a href="{{ route('admin.banned-words.index') }}">Reset</a>
                    @endif
                </form>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Kata</th>
                                <th>Scope</th>
                                <th>Streamer</th>
                                <th>Ditambah oleh</th>
                                <th>Tanggal</th>
                                <th style="width:80px">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($words as $bw)
                            <tr>
                                <td>
                                    <code style="font-size:12px; color:var(--brand-light); background:rgba(124,108,252,.1); padding:2px 6px; border-radius:4px;">{{ $bw->word }}</code>
                                </td>
                                <td>
                                    @if(is_null($bw->streamer_id))
                                        <span class="badge badge-global">Global</span>
                                    @else
                                        <span class="badge badge-streamer">Streamer</span>
                                    @endif
                                </td>
                                <td style="color:var(--text-3); font-size:12px;">
                                    {{ $bw->streamer?->display_name ?? '—' }}
                                </td>
                                <td style="color:var(--text-3); font-size:12px;">
                                    {{ $bw->createdBy?->name ?? 'System' }}
                                </td>
                                <td style="color:var(--text-3); font-size:11px; white-space:nowrap;">
                                    {{ $bw->created_at->format('d/m/Y') }}
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.banned-words.destroy', $bw) }}"
                                          onsubmit="return confirm('Hapus kata \'{{ addslashes($bw->word) }}\'?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-del">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="empty-cell">Tidak ada kata yang ditemukan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    @if($words->hasPages())
                    <div class="pagination-wrap">
                        {{ $words->links() }}
                    </div>
                    @endif
                </div>
            </div>

        </div>{{-- .bw-grid --}}
    </div>

    @push('scripts')
    <script>
    // Auto-dismiss flash messages
    ['flash-success','flash-error'].forEach(function(id) {
        var el = document.getElementById(id);
        if (el) setTimeout(function(){ el.style.opacity='0'; el.style.transition='opacity .4s'; setTimeout(function(){ el.remove(); }, 400); }, 4000);
    });
    </script>
    @endpush
</x-app-layout>
