<x-app-layout>
<div class="page-container">
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">Payout Streamer</h1>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <div class="table-card">
        <h2 class="section-title">Saldo Belum Dicairkan</h2>
        <table>
            <thead>
                <tr>
                    <th>Streamer</th>
                    <th>Saldo Owed</th>
                    <th>Info Bank</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($streamers as $streamer)
                <tr>
                    <td>{{ $streamer->display_name }}</td>
                    <td class="amount-cell">Rp {{ number_format($streamer->owed_amount, 0, ',', '.') }}</td>
                    <td>
                        @if($streamer->bank_account_number)
                            {{ $streamer->bank_name }} — {{ $streamer->bank_account_number }}
                        @else
                            <span style="color:var(--text-3)">Belum diisi</span>
                        @endif
                    </td>
                    <td>
                        @if($streamer->owed_amount >= config('payout.minimum_amount') && $streamer->bank_account_number)
                            <form method="POST" action="{{ route('admin.payouts.create', $streamer) }}"
                                onsubmit="return confirm('Buat payout Rp {{ number_format($streamer->owed_amount, 0, ',', '.') }} untuk {{ addslashes($streamer->display_name) }}?')">
                                @csrf
                                <button type="submit" class="btn-xs">Buat Payout</button>
                            </form>
                        @else
                            <span style="color:var(--text-3); font-size:11px">Belum memenuhi syarat</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="empty-cell">Tidak ada saldo owed saat ini</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="table-card">
        <h2 class="section-title">Riwayat Payout</h2>
        <table>
            <thead>
                <tr>
                    <th>Streamer</th>
                    <th>Net Amount</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($payouts as $p)
                <tr>
                    <td>{{ $p->streamer->display_name }}</td>
                    <td class="amount-cell">{{ $p->formatted_net_amount }}</td>
                    <td>{{ ucfirst($p->status) }}</td>
                    <td style="font-size:11px; color:var(--text-3)">{{ $p->created_at->format('d/m/Y H:i') }}</td>
                    <td><a href="{{ route('admin.payouts.show', $p) }}" class="btn-xs">Detail</a></td>
                </tr>
                @empty
                <tr><td colspan="5" class="empty-cell">Belum ada payout</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-app-layout>
