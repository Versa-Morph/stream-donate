<x-app-layout>
@push('styles')
<style>
    /* ── Modal ── */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 1000;
        background: rgba(0,0,0,.75);
        backdrop-filter: blur(8px);
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.open { display: flex; }
    .modal {
        background: var(--glass-bg);
        backdrop-filter: blur(20px) saturate(180%);
        border: 1px solid rgba(124,108,252,.2);
        border-radius: var(--radius-xl);
        padding: 28px 32px;
        width: 400px;
        max-width: 92vw;
        box-shadow: 0 24px 60px rgba(0,0,0,.6), 0 0 30px rgba(124,108,252,.1);
    }
    .modal-title { font-size: 16px; font-weight: 700; color: var(--text); margin-bottom: 18px; }
    .modal-footer { display: flex; gap: 10px; justify-content: flex-end; margin-top: 22px; }
    .payout-breakdown { border: 1px solid var(--border); border-radius: var(--radius); overflow: hidden; margin-bottom: 16px; }
    .payout-breakdown-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 10px 14px; font-size: 13px; color: var(--text-2);
        border-top: 1px solid var(--border);
    }
    .payout-breakdown-row:first-child { border-top: none; }
    .payout-breakdown-row.total {
        background: var(--surface-2); font-weight: 700; color: var(--text);
    }
    .payout-breakdown-row .amount-cell { font-size: 13px; }
</style>
@endpush
<div class="page-container">
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">Payout Streamer</h1>
            <p class="page-subtitle">{{ $eligibleStreamerCount }} streamer dengan saldo owed</p>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card" data-color="brand">
            <div class="stat-label">Total Saldo Owed</div>
            <div class="stat-value">Rp {{ number_format($totalOwed, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card" data-color="orange">
            <div class="stat-label">Streamer Belum Dicairkan</div>
            <div class="stat-value">{{ $eligibleStreamerCount }}</div>
        </div>
        <div class="stat-card" data-color="green">
            <div class="stat-label">Total Sudah Dibayarkan</div>
            <div class="stat-value">Rp {{ number_format($totalPaidOut, 0, ',', '.') }}</div>
        </div>
        <div class="stat-card" data-color="yellow">
            <div class="stat-label">Payout Pending</div>
            <div class="stat-value">{{ $pendingPayoutCount }}</div>
        </div>
    </div>

    <!-- Saldo Belum Dicairkan -->
    <h2 class="section-title" style="margin-bottom:12px">Saldo Belum Dicairkan</h2>

    <form method="GET" action="{{ route('admin.payouts.index') }}">
        @foreach(request()->except(['owed_search', 'owed_page']) as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
        <div class="filter-bar">
            <input type="text" name="owed_search" value="{{ request('owed_search') }}"
                placeholder="Cari nama streamer…">
            <button type="submit" class="btn-filter">Filter</button>
            @if(request('owed_search'))
                <a href="{{ route('admin.payouts.index', request()->except(['owed_search', 'owed_page'])) }}" class="btn-xs" style="font-size:12px;padding:7px 14px">Reset</a>
            @endif
        </div>
    </form>

    <div class="table-card" style="margin-bottom:16px">
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
                    $feePercent = config('payout.platform_fee_percent', 10);
                    $feeAmount = (int) round($streamer->owed_amount * $feePercent / 100);
                    $netAmount = $streamer->owed_amount - $feeAmount;
                    $bankLabel = $hasBankInfo
                        ? $streamer->bankDisplayName() . ' — ' . $streamer->bank_account_number . ' a.n. ' . $streamer->bank_account_holder
                        : 'Belum diisi';
                @endphp
                <tr @if($blockedReasons) style="opacity:.6" @endif>
                    <td style="font-weight:600; color:var(--text)">{{ $streamer->display_name }}</td>
                    <td class="amount-cell">Rp {{ number_format($streamer->owed_amount, 0, ',', '.') }}</td>
                    <td>
                        @if($hasBankInfo)
                            <span class="badge badge-green">{{ $streamer->bankDisplayName() }} — {{ $streamer->bank_account_number }}</span>
                        @else
                            <span class="badge badge-red">Belum diisi</span>
                        @endif
                    </td>
                    <td>
                        <button type="button" class="btn-xs success"
                            @if($blockedReasons) disabled title="{{ implode(' · ', $blockedReasons) }}" @endif
                            onclick="openPayoutModal({
                                action: '{{ route('admin.payouts.create', $streamer) }}',
                                name: '{{ addslashes($streamer->display_name) }}',
                                gross: '{{ number_format($streamer->owed_amount, 0, ',', '.') }}',
                                feePercent: '{{ $feePercent }}',
                                fee: '{{ number_format($feeAmount, 0, ',', '.') }}',
                                net: '{{ number_format($netAmount, 0, ',', '.') }}',
                                bank: '{{ addslashes($bankLabel) }}'
                            })">Buat Payout</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="empty-cell">Tidak ada saldo owed saat ini</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($streamers->hasPages())
    <div class="pagination">
        {{ $streamers->links() }}
    </div>
    @endif

    <!-- Riwayat Payout -->
    <h2 class="section-title" style="margin-top:32px; margin-bottom:12px">Riwayat Payout</h2>

    <form method="GET" action="{{ route('admin.payouts.index') }}">
        @foreach(request()->except(['streamer_id', 'status', 'from', 'to', 'payouts_page']) as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
        <div class="filter-bar">
            <select name="streamer_id" style="max-width:220px">
                <option value="">Semua Streamer</option>
                @foreach($allStreamers as $s)
                <option value="{{ $s->id }}" {{ request('streamer_id') == $s->id ? 'selected' : '' }}>
                    {{ $s->display_name }}
                </option>
                @endforeach
            </select>
            <select name="status" style="max-width:180px">
                <option value="">Semua Status</option>
                @foreach(['pending' => 'Pending', 'processing' => 'Diproses', 'paid' => 'Dibayar', 'failed' => 'Gagal', 'voided' => 'Dibatalkan'] as $value => $label)
                <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <input type="date" name="from" value="{{ request('from') }}" style="max-width:150px">
            <input type="date" name="to" value="{{ request('to') }}" style="max-width:150px">
            <button type="submit" class="btn-filter">Filter</button>
            @if(request('streamer_id') || request('status') || request('from') || request('to'))
                <a href="{{ route('admin.payouts.index', request()->except(['streamer_id', 'status', 'from', 'to', 'payouts_page'])) }}" class="btn-xs" style="font-size:12px;padding:7px 14px">Reset</a>
            @endif
        </div>
    </form>

    <div class="table-card">
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

    @if($payouts->hasPages())
    <div class="pagination">
        {{ $payouts->links() }}
    </div>
    @endif
</div>

<!-- Modal Konfirmasi Buat Payout -->
<div class="modal-overlay" id="payout-modal">
    <div class="modal">
        <div class="modal-title">Konfirmasi Buat Payout</div>
        <form method="POST" id="payout-form" action="">
            @csrf
            <p style="font-size:13px; color:var(--text-3); margin-bottom:16px">
                Untuk: <strong id="payout-name" style="color:var(--text)"></strong>
            </p>
            <div class="payout-breakdown">
                <div class="payout-breakdown-row">
                    <span>Saldo Owed (Gross)</span>
                    <span class="amount-cell" id="payout-gross"></span>
                </div>
                <div class="payout-breakdown-row">
                    <span>Fee Platform (<span id="payout-fee-percent"></span>%)</span>
                    <span class="amount-cell" style="color:var(--red)" id="payout-fee"></span>
                </div>
                <div class="payout-breakdown-row total">
                    <span>Net Diterima Streamer</span>
                    <span class="amount-cell" id="payout-net"></span>
                </div>
            </div>
            <p style="font-size:12px; color:var(--text-3); margin-bottom:4px">Tujuan Transfer</p>
            <p style="font-size:13px; color:var(--text); margin-bottom:16px" id="payout-bank"></p>
            <div class="modal-footer">
                <button type="button" class="btn-xs" onclick="closePayoutModal()">Batal</button>
                <button type="submit" class="btn-primary" style="padding:8px 20px; font-size:13px">Buat Payout</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openPayoutModal(data) {
    document.getElementById('payout-form').action = data.action;
    document.getElementById('payout-name').textContent = data.name;
    document.getElementById('payout-gross').textContent = 'Rp ' + data.gross;
    document.getElementById('payout-fee-percent').textContent = data.feePercent;
    document.getElementById('payout-fee').textContent = '- Rp ' + data.fee;
    document.getElementById('payout-net').textContent = 'Rp ' + data.net;
    document.getElementById('payout-bank').textContent = data.bank;
    document.getElementById('payout-modal').classList.add('open');
}
function closePayoutModal() {
    document.getElementById('payout-modal').classList.remove('open');
}
document.getElementById('payout-modal').addEventListener('click', function (e) {
    if (e.target === this) closePayoutModal();
});
</script>
@endpush
</x-app-layout>
