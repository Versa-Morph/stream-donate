<x-app-layout>
<div class="page-container">
    @php
        $statusLabel = match($payout->status) {
            'pending' => 'Pending',
            'processing' => 'Diproses',
            'paid' => 'Dibayar',
            'failed' => 'Gagal',
            'voided' => 'Dibatalkan',
            default => ucfirst($payout->status),
        };
        $statusClass = match($payout->status) {
            'pending' => 'badge-yellow',
            'processing' => 'badge-purple',
            'paid' => 'badge-green',
            'failed' => 'badge-red',
            'voided' => 'badge-gray',
            default => 'badge-gray',
        };
    @endphp
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">Payout #{{ $payout->id }} — {{ $payout->streamer->display_name }}</h1>
            <p class="page-subtitle">
                <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
                @if($payout->reference)
                    · Referensi: {{ $payout->reference }}
                @endif
                · Dibuat oleh: {{ $payout->createdBy->name ?? '—' }}
            </p>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card c-brand">
            <div class="stat-label">Gross</div>
            <div class="stat-value">Rp {{ number_format($payout->gross_amount, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card c-orange">
            <div class="stat-label">Fee ({{ config('payout.platform_fee_percent') }}%)</div>
            <div class="stat-value">Rp {{ number_format($payout->platform_fee_amount, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card c-green">
            <div class="stat-label">Net</div>
            <div class="stat-value">{{ $payout->formatted_net_amount }}</div>
        </div>
    </div>

    <div class="table-card" style="margin-bottom:16px">
        <h2 class="section-title">Info Bank</h2>
        <p style="font-size:13px; color:var(--text-2); margin:0">
            {{ $payout->bank_name }} — {{ $payout->bank_account_number }} a.n. {{ $payout->bank_account_holder }}
        </p>

        @if(in_array($payout->status, ['pending', 'processing'], true))
            <form method="POST" action="{{ route('admin.payouts.mark-paid', $payout) }}" style="margin-top:16px; display:flex; gap:8px; align-items:center">
                @csrf
                <input type="text" name="reference" placeholder="Referensi transfer bank" required style="max-width:280px">
                <button type="submit" class="btn-xs">Tandai Sudah Dibayar</button>
            </form>
        @endif
        @if($payout->status === 'pending')
            <form method="POST" action="{{ route('admin.payouts.void', $payout) }}"
                onsubmit="return confirm('Batalkan payout ini? Donasi akan dikembalikan ke saldo owed.')" style="margin-top:8px">
                @csrf
                <button type="submit" class="btn-xs danger">Batalkan</button>
            </form>
        @endif
    </div>

    <div class="table-card">
        <h2 class="section-title">Donasi Termasuk</h2>
        <table>
            <thead><tr><th>Donatur</th><th>Nominal</th><th>Waktu</th></tr></thead>
            <tbody>
                @foreach($payout->donations as $d)
                <tr>
                    <td>{{ $d->name }}</td>
                    <td class="amount-cell">Rp {{ number_format($d->amount, 0, ',', '.') }}</td>
                    <td style="font-size:11px; color:var(--text-3)">{{ $d->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</x-app-layout>
