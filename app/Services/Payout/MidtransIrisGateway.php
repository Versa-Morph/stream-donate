<?php

namespace App\Services\Payout;

use App\Models\Payout;
use Illuminate\Support\Facades\Http;

/**
 * Wraps Midtrans Iris (disbursement/payout). Uses a separate Iris API key
 * (config('payout.iris_api_key')), NOT the Snap/Core API server key.
 *
 * IMPORTANT: the exact request/response JSON shape for CreatePayout,
 * ApprovePayout, and ValidateBankAccount could not be confirmed against
 * Midtrans's live Iris API reference in the session that wrote this class
 * (see docs/superpowers/specs/2026-07-26-automated-payout-disbursement-design.md,
 * "Important limitation"). CONFIRM the real field names against
 * https://docs.midtrans.com/reference/ before enabling
 * payout.automated_disbursement_enabled in any real environment — the
 * bodies below are structurally wired (auth header, base URL, method
 * shape) but the field names inside each request/response are marked and
 * MUST be verified, not trusted as-is.
 */
class MidtransIrisGateway implements PayoutGatewayInterface
{
    private function baseUrl(): string
    {
        return config('midtrans.is_production')
            ? 'https://app.midtrans.com/iris/api/v1'
            : 'https://app.sandbox.midtrans.com/iris/api/v1';
    }

    private function client()
    {
        return Http::withBasicAuth(config('payout.iris_api_key'), '')
            ->baseUrl($this->baseUrl())
            ->acceptJson();
    }

    public function validateBankAccount(Payout $payout): bool
    {
        // CONFIRM FIELD NAMES: this call signature (bank name + account number
        // → validity/account-holder-name response) is real per midtrans-go's
        // ValidateBankAccount method, but the exact query/body field names and
        // response shape are not verified here.
        $response = $this->client()->get('bank_account_validation', [
            'bank' => $payout->bank_name, // TODO: confirm param name against live API reference
            'account' => $payout->bank_account_number, // TODO: confirm param name
        ]);

        // TODO: confirm the actual success/validity field in the response body
        return $response->successful() && (bool) ($response->json('is_valid') ?? false);
    }

    public function disburse(Payout $payout): PayoutDisbursementResult
    {
        // CONFIRM FIELD NAMES: CreatePayout + ApprovePayout are real Iris
        // methods (per midtrans-go), but the exact request bodies below are
        // NOT verified — placeholders for the shape, not trusted values.
        $createResponse = $this->client()->post('payouts', [
            'payouts' => [[
                'beneficiary_name' => $payout->bank_account_holder, // TODO: confirm field name
                'beneficiary_account' => $payout->bank_account_number, // TODO: confirm field name
                'beneficiary_bank' => $payout->bank_name, // TODO: confirm field name
                'amount' => (string) $payout->net_amount, // TODO: confirm field name/type
                'notes' => "Payout #{$payout->id}", // TODO: confirm field name
            ]],
        ]);

        if (!$createResponse->successful()) {
            return new PayoutDisbursementResult(
                status: 'failed',
                errorMessage: 'CreatePayout gagal: ' . $createResponse->body(),
            );
        }

        // TODO: confirm the actual reference-number field in CreatePayout's response
        $referenceNo = $createResponse->json('payouts.0.reference_no');

        // Full automation per design decision: approve immediately, no
        // separate admin click.
        $approveResponse = $this->client()->post('payouts/approve', [
            'reference_nos' => [$referenceNo], // TODO: confirm field name
            'otp' => null, // TODO: confirm whether/how 2FA OTP applies for this account tier
        ]);

        if (!$approveResponse->successful()) {
            return new PayoutDisbursementResult(
                status: 'failed',
                reference: $referenceNo,
                errorMessage: 'ApprovePayout gagal: ' . $approveResponse->body(),
            );
        }

        return new PayoutDisbursementResult(status: 'processing', reference: $referenceNo);
    }

    public function checkStatus(Payout $payout): PayoutStatusResult
    {
        // CONFIRM FIELD NAMES: GetPayoutDetails is a real Iris method (per
        // midtrans-go), but the exact response status values/field names
        // below are NOT verified.
        $response = $this->client()->get("payouts/{$payout->reference}");

        // TODO: confirm the actual status field/values (this assumes something
        // like "approved"/"rejected"/"queued" — verify against the live API).
        $status = $response->json('status'); // TODO: confirm field name

        return new PayoutStatusResult(status: match ($status) {
            'completed', 'approved' => 'paid', // TODO: confirm actual terminal-success value
            'rejected', 'failed' => 'failed', // TODO: confirm actual terminal-failure value
            default => 'processing',
        });
    }
}
