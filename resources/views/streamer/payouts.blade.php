<x-app-layout>
<div class="page-container">
    <div class="page-header">
        <h1 class="page-title">Riwayat Payout</h1>
    </div>

    <div class="table-card">
        <table>
            <thead><tr><th>Tanggal</th><th>Gross</th><th>Fee</th><th>Net</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($payouts as $p)
                <tr>
                    <td>{{ $p->created_at->format('d/m/Y') }}</td>
                    <td class="amount-cell">Rp {{ number_format($p->gross_amount, 0, ',', '.') }}</td>
                    <td class="amount-cell">Rp {{ number_format($p->platform_fee_amount, 0, ',', '.') }}</td>
                    <td class="amount-cell">{{ $p->formatted_net_amount }}</td>
                    <td>{{ ucfirst($p->status) }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="empty-cell">Belum ada payout</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-app-layout>
