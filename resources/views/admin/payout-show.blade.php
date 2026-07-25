<x-app-layout>
<div class="page-container">
    <div class="page-header">
        <h1 class="page-title">Payout #{{ $payout->id }} — {{ $payout->streamer->display_name }}</h1>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <div class="table-card">
        <p>Gross: Rp {{ number_format($payout->gross_amount, 0, ',', '.') }}</p>
        <p>Fee ({{ config('payout.platform_fee_percent') }}%): Rp {{ number_format($payout->platform_fee_amount, 0, ',', '.') }}</p>
        <p>Net: {{ $payout->formatted_net_amount }}</p>
        <p>Bank: {{ $payout->bank_name }} — {{ $payout->bank_account_number }} a.n. {{ $payout->bank_account_holder }}</p>
        <p>Status: {{ ucfirst($payout->status) }}</p>
        @if($payout->reference)
            <p>Referensi: {{ $payout->reference }}</p>
        @endif

        @if($payout->status === 'pending')
            <form method="POST" action="{{ route('admin.payouts.mark-paid', $payout) }}" style="margin-top:12px">
                @csrf
                <input type="text" name="reference" placeholder="Referensi transfer bank" required>
                <button type="submit" class="btn-xs">Tandai Sudah Dibayar</button>
            </form>
            <form method="POST" action="{{ route('admin.payouts.void', $payout) }}"
                onsubmit="return confirm('Batalkan payout ini? Donasi akan dikembalikan ke saldo owed.')" style="margin-top:8px">
                @csrf
                <button type="submit" class="btn-xs">Batalkan</button>
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
