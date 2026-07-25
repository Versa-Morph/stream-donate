<?php

namespace App\Jobs;

use App\Models\Donation;
use App\Models\Payout;
use App\Services\Payout\PayoutGatewayInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckPayoutDisbursementStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function handle(): void
    {
        $gateway = app(PayoutGatewayInterface::class);
        $processing = Payout::where('status', 'processing')->get();

        foreach ($processing as $payout) {
            $result = $gateway->checkStatus($payout);

            if ($result->status === 'paid') {
                $payout->update(['status' => 'paid', 'paid_at' => now()]);
            } elseif ($result->status === 'failed') {
                DB::transaction(function () use ($payout) {
                    Donation::where('payout_id', $payout->id)->update(['payout_id' => null]);
                    $payout->update(['status' => 'failed']);
                });
            }
            // status === 'processing': still in flight, leave as-is until next run.
        }

        Log::info("CheckPayoutDisbursementStatusJob: checked {$processing->count()} processing payouts");
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('CheckPayoutDisbursementStatusJob: semua retry habis', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
