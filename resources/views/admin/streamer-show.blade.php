<x-app-layout>
<div class="page-container">
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">{{ $streamer->display_name }}</h1>
            <p class="page-subtitle">
                {{ $streamer->slug }} · Bergabung {{ $streamer->created_at->format('d/m/Y') }} ·
                @if($streamer->is_accepting_donation)
                    <span style="color:var(--green)">Menerima Donasi</span>
                @else
                    <span style="color:var(--text-3)">Tidak Menerima Donasi</span>
                @endif
            </p>
        </div>
        <a href="{{ route('donate.show', $streamer->slug) }}" target="_blank" class="card-link">Lihat halaman donasi →</a>
    </div>

    <div class="stats-grid">
        <div class="stat-card c-brand">
            <div class="stat-label">Total Donasi</div>
            <div class="stat-value">Rp {{ number_format($stats['total'], 0, ',', '.') }}</div>
        </div>
        <div class="stat-card c-orange">
            <div class="stat-label">Jumlah Donasi</div>
            <div class="stat-value">{{ number_format($stats['count'], 0, ',', '.') }}</div>
        </div>
        <div class="stat-card c-green">
            <div class="stat-label">Donatur Unik</div>
            <div class="stat-value">{{ number_format($stats['donors'], 0, ',', '.') }}</div>
        </div>
        <div class="stat-card c-purple">
            <div class="stat-label">Saldo Owed</div>
            <div class="stat-value">Rp {{ number_format($owedBalance, 0, ',', '.') }}</div>
        </div>
    </div>

    <div style="margin-bottom:16px">
        <a href="{{ route('admin.donations', ['streamer_id' => $streamer->id]) }}" class="btn-xs">Semua Donasi</a>
        <a href="{{ route('admin.payouts.index') }}" class="btn-xs">Payout</a>
    </div>

    <div class="table-card" style="margin-bottom:16px">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px">
            <h2 class="section-title">Donasi Terbaru</h2>
            <a href="{{ route('admin.donations', ['streamer_id' => $streamer->id]) }}" class="card-link">Lihat semua →</a>
        </div>
        <table>
            <thead><tr><th>Donatur</th><th>Nominal</th><th>Status</th><th>Waktu</th></tr></thead>
            <tbody>
                @forelse($recentDonations as $d)
                <tr>
                    <td>{{ $d->name }}</td>
                    <td class="amount-cell">Rp {{ number_format($d->amount, 0, ',', '.') }}</td>
                    <td>{{ ucfirst($d->status) }}</td>
                    <td style="font-size:11px; color:var(--text-3)">{{ $d->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="empty-cell">Belum ada donasi</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="table-card">
        <h2 class="section-title">Aktivitas Terbaru</h2>
        <table>
            <thead><tr><th>Aksi</th><th>Deskripsi</th><th>Waktu</th></tr></thead>
            <tbody>
                @forelse($recentActivity as $log)
                <tr>
                    <td style="font-size:11px">{{ $log->action }}</td>
                    <td>{{ $log->description }}</td>
                    <td style="font-size:11px; color:var(--text-3)">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="empty-cell">Belum ada aktivitas</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-app-layout>
