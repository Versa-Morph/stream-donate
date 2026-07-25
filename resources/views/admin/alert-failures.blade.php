<x-app-layout>
<div class="page-container">
    <div class="page-header">
        <h1 class="page-title">Alert Gagal</h1>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Donatur</th>
                    <th>Nominal</th>
                    <th>Streamer</th>
                    <th>Error</th>
                    <th>Waktu</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($failures as $failure)
                <tr>
                    @if($failure['donation'])
                        <td>{{ $failure['donation']->name }}</td>
                        <td class="amount-cell">Rp {{ number_format($failure['donation']->amount, 0, ',', '.') }}</td>
                        <td>{{ $failure['donation']->streamer->display_name ?? '—' }}</td>
                    @else
                        <td colspan="3" style="color:var(--text-3)">Donasi telah dihapus</td>
                    @endif
                    <td style="font-size:11px">{{ $failure['error'] ?? '—' }}</td>
                    <td style="font-size:11px; color:var(--text-3)">{{ $failure['log']->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($failure['donation'])
                            <form method="POST" action="{{ route('admin.alert-failures.retry', $failure['donation']) }}">
                                @csrf
                                <button type="submit" class="btn-xs">Retry</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="empty-cell">Tidak ada alert gagal saat ini</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-app-layout>
