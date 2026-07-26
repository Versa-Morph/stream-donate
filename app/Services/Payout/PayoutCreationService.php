<?php

namespace App\Services\Payout;

use App\Models\Payout;
use App\Models\Streamer;
use Illuminate\Support\Facades\DB;

class PayoutCreationService
{
    public function __construct(
        private readonly PayoutGatewayInterface $payoutGateway
    ) {}

    /**
     * @throws \InvalidArgumentException if the streamer isn't eligible right now
     */
    public function createFor(Streamer $streamer, ?int $createdByUserId): Payout
    {
        return DB::transaction(function () use ($streamer, $createdByUserId) {
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

            $transientPayout = new Payout([
                'bank_name' => $streamer->bank_name,
                'bank_account_number' => $streamer->bank_account_number,
                'bank_account_holder' => $streamer->bank_account_holder,
            ]);

            if (!$this->payoutGateway->validateBankAccount($transientPayout)) {
                throw new \InvalidArgumentException('Info rekening bank streamer tidak valid (gagal validasi Midtrans).');
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
                'created_by' => $createdByUserId,
            ]);

            $donations->each(fn ($d) => $d->update(['payout_id' => $payout->id]));

            $disbursement = $this->payoutGateway->disburse($payout);

            if ($disbursement->status === 'processing') {
                $payout->update(['status' => 'processing', 'reference' => $disbursement->reference]);
            } elseif ($disbursement->status === 'failed') {
                $donations->each(fn ($d) => $d->update(['payout_id' => null]));
                $payout->update(['status' => 'failed']);
            }
            // status === 'pending' (manual gateway): no change, payout stays pending as created.

            return $payout;
        });
    }
}
