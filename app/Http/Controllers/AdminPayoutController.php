<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Payout;
use App\Models\Streamer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminPayoutController extends Controller
{
    public function create(Streamer $streamer): RedirectResponse
    {
        try {
            $payout = DB::transaction(function () use ($streamer) {
                $donations = $streamer->unpaidOutDonations()->lockForUpdate()->get();
                $gross = (int) $donations->sum('amount');

                if ($gross < config('payout.minimum_amount', 50000)) {
                    throw new \InvalidArgumentException(
                        'Saldo belum mencapai minimum payout (Rp ' . number_format(config('payout.minimum_amount', 50000), 0, ',', '.') . ').'
                    );
                }

                if (!$streamer->bank_account_number) {
                    throw new \InvalidArgumentException('Streamer belum melengkapi info rekening bank.');
                }

                $feePercent = config('payout.platform_fee_percent', 10);
                $fee = (int) round($gross * $feePercent / 100);
                $net = $gross - $fee;

                $payout = Payout::create([
                    'streamer_id' => $streamer->id,
                    'gross_amount' => $gross,
                    'platform_fee_amount' => $fee,
                    'net_amount' => $net,
                    'status' => 'pending',
                    'bank_name' => $streamer->bank_name,
                    'bank_account_number' => $streamer->bank_account_number,
                    'bank_account_holder' => $streamer->bank_account_holder,
                    'created_by' => Auth::id(),
                ]);

                $donations->each(fn ($d) => $d->update(['payout_id' => $payout->id]));

                return $payout;
            });
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
}
