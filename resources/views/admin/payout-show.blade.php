<x-app-layout>
@push('styles')
<style>
    .section-title { margin: 0 0 14px; }

    .bank-actions-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 32px;
        padding: 24px;
    }
    .bank-actions-grid.single-col { grid-template-columns: 1fr; }
    @media (max-width: 720px) {
        .bank-actions-grid { grid-template-columns: 1fr; }
        .bank-actions-grid .payout-actions-col { border-left: none; border-top: 1px solid var(--border); padding-left: 0; padding-top: 24px; }
    }

    .payout-actions-col {
        border-left: 1px solid var(--border);
        padding-left: 32px;
    }

    .bank-info-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 12px 16px; font-size: 13px; color: var(--text-2);
        border-top: 1px solid var(--border);
    }
    .bank-info-row:first-child { border-top: none; }
    .bank-info-row .value { color: var(--text); font-weight: 600; }
    .bank-info-card { border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; }

    .payout-actions-label {
        font-size: 11px; font-weight: 700; letter-spacing: .7px; text-transform: uppercase;
        color: var(--text-3); margin-bottom: 14px; display: block;
    }
    .payout-actions-form { display: flex; flex-direction: column; gap: 14px; }
    .payout-actions-form .form-group { margin-bottom: 0; }

    .payout-actions-row {
        display: grid;
        grid-template-columns: 1fr auto;
        align-items: end;
        gap: 24px;
    }
    .payout-void-form { border-left: 1px solid var(--border); padding-left: 24px; }
    @media (max-width: 480px) {
        .payout-actions-row { grid-template-columns: 1fr; }
        .payout-void-form { border-left: none; padding-left: 0; padding-top: 14px; border-top: 1px solid var(--border); }
    }
</style>
@endpush
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
        $bankLabel = config('banks')[$payout->bank_name] ?? $payout->bank_name;
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
        <div class="stat-card" data-color="brand">
            <div class="stat-label">Gross</div>
            <div class="stat-value">Rp {{ number_format($payout->gross_amount, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card" data-color="orange">
            <div class="stat-label">Fee ({{ config('payout.platform_fee_percent') }}%)</div>
            <div class="stat-value">Rp {{ number_format($payout->platform_fee_amount, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card" data-color="green">
            <div class="stat-label">Net</div>
            <div class="stat-value">{{ $payout->formatted_net_amount }}</div>
        </div>
    </div>

    <!-- Donasi Termasuk: donasi mana saja yang dibundel ke payout ini -->
    <h2 class="section-title">Donasi Termasuk</h2>
    <div class="table-card" style="margin-bottom:28px">
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

    @php $hasActions = in_array($payout->status, ['pending', 'processing'], true); @endphp
    <h2 class="section-title">Info Bank</h2>
    <div class="table-card">
        <div class="bank-actions-grid @if(!$hasActions) single-col @endif">
            <div class="bank-info-card">
                <div class="bank-info-row">
                    <span>Bank</span>
                    <span class="value">{{ $bankLabel }}</span>
                </div>
                <div class="bank-info-row">
                    <span>No. Rekening</span>
                    <span class="value">{{ $payout->bank_account_number }}</span>
                </div>
                <div class="bank-info-row">
                    <span>Atas Nama</span>
                    <span class="value">{{ $payout->bank_account_holder }}</span>
                </div>
            </div>

            @if($hasActions)
            <div class="payout-actions-col">
                <span class="payout-actions-label">Aksi</span>
                <div class="payout-actions-row">
                    <form method="POST" action="{{ route('admin.payouts.mark-paid', $payout) }}" class="payout-actions-form">
                        @csrf
                        <div class="form-group">
                            <label>Referensi Transfer Bank</label>
                            <input type="text" name="reference" placeholder="Contoh: TRX/2026/00123" required>
                        </div>
                        <button type="submit" class="btn-primary" style="padding:10px 20px; font-size:13px">Tandai Sudah Dibayar</button>
                    </form>

                    @if($payout->status === 'pending')
                        <form method="POST" action="{{ route('admin.payouts.void', $payout) }}"
                            onsubmit="return confirm('Batalkan payout ini? Donasi akan dikembalikan ke saldo owed.')"
                            class="payout-void-form">
                            @csrf
                            <button type="submit" class="btn-xs danger">Batalkan Payout</button>
                        </form>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
</x-app-layout>
