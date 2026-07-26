<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Donation;
use App\Models\Payout;
use App\Models\Streamer;
use App\Services\Payout\PayoutCreationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AdminPayoutController extends Controller
{
    public function __construct(
        private readonly PayoutCreationService $payoutCreationService
    ) {}

    public function index(): View
    {
        // SQL-level aggregation via withSum, not a per-streamer loop — matches the
        // existing convention in StreamerStatsService/AdminController::dashboard
        // (see CLAUDE.md: "all aggregation done in SQL, no in-memory get()").
        $streamers = Streamer::withSum(
            ['donations as owed_amount' => fn ($q) => $q->where('status', 'paid')->whereNull('payout_id')],
            'amount'
        )
            // whereHas (EXISTS), not having() on the withSum alias — SQLite rejects
            // a HAVING clause without a GROUP BY, which withSum's correlated
            // subquery column doesn't provide. Equivalent since amounts are always
            // positive: "at least one matching donation exists" == "sum > 0".
            ->whereHas('donations', fn ($q) => $q->where('status', 'paid')->whereNull('payout_id'))
            ->orderByDesc('owed_amount')
            ->get();

        $payouts = Payout::with('streamer')
            ->orderByDesc('created_at')
            ->limit(config('pagination.admin_payouts', 50))
            ->get();

        return view('admin.payouts', compact('streamers', 'payouts'));
    }

    public function show(Payout $payout): View
    {
        $payout->load('streamer', 'donations', 'createdBy');

        return view('admin.payout-show', compact('payout'));
    }

    public function create(Streamer $streamer): RedirectResponse
    {
        try {
            $payout = $this->payoutCreationService->createFor($streamer, Auth::id());
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['payout' => $e->getMessage()]);
        }

        ActivityLog::log(
            action: 'payout.created',
            description: "Payout Rp " . number_format($payout->net_amount, 0, ',', '.') . " dibuat untuk {$streamer->display_name}",
            userId: Auth::id(),
            streamerId: $streamer->id,
            payload: ['payout_id' => $payout->id],
        );

        return back()->with('success', 'Payout berhasil dibuat.');
    }

    public function markPaid(Payout $payout, Request $request): RedirectResponse
    {
        if (!in_array($payout->status, ['pending', 'processing'], true)) {
            return back()->withErrors(['payout' => 'Payout ini sudah diproses sebelumnya.']);
        }

        $validated = $request->validate([
            'reference' => ['required', 'string', 'max:100'],
        ]);

        $payout->update([
            'status' => 'paid',
            'reference' => $validated['reference'],
            'paid_at' => now(),
        ]);

        ActivityLog::log(
            action: 'payout.paid',
            description: "Payout #{$payout->id} ditandai sudah dibayar (ref: {$validated['reference']})",
            userId: Auth::id(),
            streamerId: $payout->streamer_id,
            payload: ['payout_id' => $payout->id],
        );

        return back()->with('success', 'Payout ditandai sudah dibayar.');
    }

    public function void(Payout $payout): RedirectResponse
    {
        if ($payout->status !== 'pending') {
            return back()->withErrors(['payout' => 'Hanya payout berstatus pending yang bisa dibatalkan.']);
        }

        DB::transaction(function () use ($payout) {
            Donation::where('payout_id', $payout->id)->update(['payout_id' => null]);
            $payout->update(['status' => 'voided']);
        });

        ActivityLog::log(
            action: 'payout.voided',
            description: "Payout #{$payout->id} dibatalkan",
            userId: Auth::id(),
            streamerId: $payout->streamer_id,
            payload: ['payout_id' => $payout->id],
        );

        return back()->with('success', 'Payout dibatalkan, donasi dikembalikan ke saldo.');
    }
}
