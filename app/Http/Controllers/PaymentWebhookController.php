<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessDonationJob;
use App\Models\ActivityLog;
use App\Models\Donation;
use App\Models\Milestone;
use App\Services\Payment\InvalidPaymentSignatureException;
use App\Services\Payment\PaymentGatewayInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function __construct(
        private readonly PaymentGatewayInterface $paymentGateway
    ) {}

    public function handle(Request $request): JsonResponse
    {
        try {
            $notification = $this->paymentGateway->verifyNotification($request->all());
        } catch (InvalidPaymentSignatureException $e) {
            Log::warning('PaymentWebhookController: signature tidak valid', ['payload' => $request->all()]);

            return response()->json(['message' => 'Signature tidak valid.'], 403);
        }

        $donation = Donation::where('payment_reference', $notification->orderId)->first();

        if (!$donation) {
            Log::warning('PaymentWebhookController: order_id tidak ditemukan', [
                'order_id' => $notification->orderId,
            ]);

            return response()->json(['message' => 'Donasi tidak ditemukan.'], 404);
        }

        // Idempotency gate: only the first notification to see this row as
        // "pending" gets to act on it. A retried/duplicate notification (or one
        // arriving after the row already resolved) is a no-op.
        $affected = Donation::where('id', $donation->id)
            ->where('status', 'pending')
            ->update([
                'status'       => $notification->status,
                'payment_type' => $notification->paymentType,
                'paid_at'      => $notification->status === 'paid' ? now() : null,
            ]);

        if ($affected === 0) {
            return response()->json(['message' => 'Sudah diproses.'], 200);
        }

        $donation->refresh();

        if ($notification->status === 'paid') {
            $this->creditDonation($donation);
        } elseif (in_array($notification->status, ['failed', 'expired'], true)) {
            ActivityLog::log(
                action: "donation.{$notification->status}",
                description: "Pembayaran donasi #{$donation->id} berstatus {$notification->status}",
                streamerId: $donation->streamer_id,
                payload: ['donation_id' => $donation->id],
            );
        }
        // status === 'pending' (fraud-review "challenge" case): tidak ada aksi,
        // tunggu notifikasi Midtrans berikutnya.

        return response()->json(['message' => 'OK'], 200);
    }

    /**
     * Satu-satunya titik yang meng-kreditkan donasi setelah pembayaran
     * dikonfirmasi: milestone, subathon, alert queue, dan activity log.
     */
    private function creditDonation(Donation $donation): void
    {
        $streamer = $donation->streamer;

        if ($donation->milestone_id) {
            $milestone = Milestone::find($donation->milestone_id);
            if ($milestone && $milestone->streamer_id === $streamer->id) {
                $milestone->addAmount($donation->amount);
            }
        }

        if ($streamer->subathon_enabled) {
            $streamer->addSubathonTime($donation->amount);
        }

        $alertQueued = true;
        try {
            ProcessDonationJob::dispatchSync($donation);
        } catch (\Throwable $e) {
            $alertQueued = false;

            Log::error('PaymentWebhookController: ProcessDonationJob sync gagal, fallback ke queue', [
                'donation_id' => $donation->id,
                'error'       => $e->getMessage(),
            ]);

            try {
                ProcessDonationJob::dispatch($donation)->delay(now()->addSeconds(5));
            } catch (\Throwable $queueError) {
                Log::critical('PaymentWebhookController: fallback queue juga gagal', [
                    'donation_id' => $donation->id,
                    'error'       => $queueError->getMessage(),
                ]);
            }
        }

        ActivityLog::log(
            action: 'donation.paid',
            description: "{$donation->name} berhasil membayar donasi Rp " . number_format($donation->amount, 0, ',', '.'),
            streamerId: $streamer->id,
            payload: ['donation_id' => $donation->id, 'alert_queued' => $alertQueued],
        );
    }
}
