<x-app-layout>
<div class="page-container">
    @php
        $ownedAmount = $streamer->unpaidOutDonations()->sum('amount');
        $hasBankInfo = (bool) $streamer->bank_account_number;
        $meetsMinimum = $ownedAmount >= config('payout.minimum_amount');
        $blockedReasons = [];
        if (!$hasBankInfo) {
            $blockedReasons[] = 'Info bank belum diisi (lengkapi di Settings)';
        }
        if (!$meetsMinimum) {
            $blockedReasons[] = 'Saldo belum mencapai minimum Rp ' . number_format(config('payout.minimum_amount'), 0, ',', '.');
        }
    @endphp
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">Riwayat Payout</h1>
            <p class="page-subtitle">{{ $payouts->count() }} payout tercatat · Saldo owed: Rp {{ number_format($ownedAmount, 0, ',', '.') }}</p>
        </div>
        <form method="POST" action="{{ route('streamer.payouts.request') }}"
            onsubmit="return confirm('Ajukan payout Rp {{ number_format($ownedAmount, 0, ',', '.') }}?')">
            @csrf
            <button type="submit" class="btn-xs" @if($blockedReasons) disabled title="{{ implode(' · ', $blockedReasons) }}" @endif>Request Payout</button>
        </form>
    </div>

    <div class="table-card">
        <table>
            <thead><tr><th>Tanggal</th><th>Gross</th><th>Fee</th><th>Net</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($payouts as $p)
                @php
                    $statusLabel = match($p->status) {
                        'pending' => 'Pending',
                        'processing' => 'Diproses',
                        'paid' => 'Dibayar',
                        'failed' => 'Gagal',
                        'voided' => 'Dibatalkan',
                        default => ucfirst($p->status),
                    };
                    $statusClass = match($p->status) {
                        'pending' => 'badge-yellow',
                        'processing' => 'badge-purple',
                        'paid' => 'badge-green',
                        'failed' => 'badge-red',
                        'voided' => 'badge-gray',
                        default => 'badge-gray',
                    };
                @endphp
                <tr>
                    <td>{{ $p->created_at->format('d/m/Y') }}</td>
                    <td class="amount-cell">Rp {{ number_format($p->gross_amount, 0, ',', '.') }}</td>
                    <td class="amount-cell">Rp {{ number_format($p->platform_fee_amount, 0, ',', '.') }}</td>
                    <td class="amount-cell">{{ $p->formatted_net_amount }}</td>
                    <td><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
                </tr>
                @empty
                <tr><td colspan="5" class="empty-cell">Belum ada payout</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-app-layout>
