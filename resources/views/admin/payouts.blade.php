<x-app-layout>
<div class="page-container">
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">Payout Streamer</h1>
            <p class="page-subtitle">{{ $streamers->count() }} streamer dengan saldo owed</p>
        </div>
    </div>

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
                @php
                    $hasBankInfo = (bool) $streamer->bank_account_number;
                    $meetsMinimum = $streamer->owed_amount >= config('payout.minimum_amount');
                    $blockedReasons = [];
                    if (!$hasBankInfo) {
                        $blockedReasons[] = 'Info bank streamer belum diisi';
                    }
                    if (!$meetsMinimum) {
                        $blockedReasons[] = 'Saldo belum mencapai minimum Rp ' . number_format(config('payout.minimum_amount'), 0, ',', '.');
                    }
                @endphp
                <tr>
                    <td>{{ $streamer->display_name }}</td>
                    <td class="amount-cell">Rp {{ number_format($streamer->owed_amount, 0, ',', '.') }}</td>
                    <td>
                        @if($hasBankInfo)
                            {{ $streamer->bankDisplayName() }} — {{ $streamer->bank_account_number }}
                        @else
                            <span style="color:var(--text-3)">Belum diisi</span>
                        @endif
                    </td>
                    <td>
                        <form method="POST" action="{{ route('admin.payouts.create', $streamer) }}"
                            onsubmit="return confirm('Buat payout Rp {{ number_format($streamer->owed_amount, 0, ',', '.') }} untuk {{ addslashes($streamer->display_name) }}?')">
                            @csrf
                            <button type="submit" class="btn-xs" @if($blockedReasons) disabled title="{{ implode(' · ', $blockedReasons) }}" @endif>Buat Payout</button>
                        </form>
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
                    <td>{{ $p->streamer->display_name }}</td>
                    <td class="amount-cell">{{ $p->formatted_net_amount }}</td>
                    <td><span class="badge {{ $statusClass }}">{{ $statusLabel }}</span></td>
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
