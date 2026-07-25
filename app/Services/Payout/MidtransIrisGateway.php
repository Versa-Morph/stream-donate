<?php

namespace App\Services\Payout;

use App\Models\Payout;
use Illuminate\Support\Facades\Http;

/**
 * Wraps Midtrans Iris (disbursement/payout).
 *
 * Request shapes below are confirmed against Midtrans's official Iris
 * Postman collection ("Midtrans IRIS Disbursement API"). Response field
 * names are NOT confirmed — the collection ships with no saved example
 * responses for any endpoint, so the response-parsing lines below (marked
 * TODO) are still placeholders. CONFIRM those against a live sandbox call
 * before enabling payout.automated_disbursement_enabled anywhere real (see
 * docs/superpowers/specs/2026-07-26-automated-payout-disbursement-design.md,
 * "Important limitation").
 *
 * Iris requires two separate API keys, each Basic-Auth'd as
 * "{key}:" (empty password) — confirmed from the collection's auth blocks:
 * - config('payout.iris_api_key'): the "creator" key. Can create/reject payouts
 *   and validate bank accounts. Cannot approve.
 * - config('payout.iris_approver_api_key'): the "approver" key. The collection's
 *   "Approve Payouts" request explicitly overrides auth to this separate key —
 *   it is the only one authorized to call POST /payouts/approve. This is
 *   Iris's maker-checker control; this class defeats that control by holding
 *   both keys and approving immediately (per the "auto-approve immediately"
 *   design decision), so both keys must be provisioned on the same server.
 */
class MidtransIrisGateway implements PayoutGatewayInterface
{
    private function baseUrl(): string
    {
        return config('midtrans.is_production')
            ? 'https://app.midtrans.com/iris/api/v1'
            : 'https://app.sandbox.midtrans.com/iris/api/v1';
    }

    private function client(string $apiKey)
    {
        return Http::withBasicAuth($apiKey, '')
            ->baseUrl($this->baseUrl())
            ->acceptJson();
    }

    private function creatorClient()
    {
        return $this->client(config('payout.iris_api_key'));
    }

    private function approverClient()
    {
        return $this->client(config('payout.iris_approver_api_key'));
    }

    public function validateBankAccount(Payout $payout): bool
    {
        // GET /account_validation?bank=...&account=... — path and both query
        // param names confirmed against the collection's "Validate Bank
        // Account" request (example: ?bank=danamon&account=000001137298).
        $response = $this->creatorClient()->get('account_validation', [
            'bank' => $payout->bank_name,
            'account' => $payout->bank_account_number,
        ]);

        // TODO: confirm the actual validity field in the response body — no
        // example response is saved in the collection for this endpoint.
        return $response->successful() && (bool) ($response->json('is_valid') ?? false);
    }

    public function disburse(Payout $payout): PayoutDisbursementResult
    {
        // POST /payouts — body shape confirmed against the collection's
        // "Create Payouts" request body verbatim (field names, nesting under
        // "payouts", amount as a decimal string).
        $createResponse = $this->creatorClient()->post('payouts', [
            'payouts' => [[
                'beneficiary_name' => $payout->bank_account_holder,
                'beneficiary_account' => $payout->bank_account_number,
                'beneficiary_bank' => $payout->bank_name,
                'beneficiary_email' => '',
                'amount' => number_format($payout->net_amount, 2, '.', ''),
                'notes' => "Payout #{$payout->id}",
            ]],
        ]);

        if (!$createResponse->successful()) {
            return new PayoutDisbursementResult(
                status: 'failed',
                errorMessage: 'CreatePayout gagal: ' . $createResponse->body(),
            );
        }

        // TODO: confirm the actual reference-number field in CreatePayout's
        // response — no example response is saved in the collection.
        $referenceNo = $createResponse->json('payouts.0.reference_no');

        // Full automation per design decision: approve immediately with the
        // separate approver key, no separate admin click. Body shape
        // (reference_nos array, no otp field) confirmed against the
        // collection's "Approve Payouts" request — it has no otp field.
        $approveResponse = $this->approverClient()->post('payouts/approve', [
            'reference_nos' => [$referenceNo],
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
        // GET /payouts/{reference_no} — path confirmed against the
        // collection's "Get Payout Details" request.
        $response = $this->creatorClient()->get("payouts/{$payout->reference}");

        // TODO: confirm the actual status field/values — no example response
        // is saved in the collection for this endpoint (this assumes
        // something like "approved"/"rejected"/"queued" — verify live).
        $status = $response->json('status');

        return new PayoutStatusResult(status: match ($status) {
            'completed', 'approved' => 'paid', // TODO: confirm actual terminal-success value
            'rejected', 'failed' => 'failed', // TODO: confirm actual terminal-failure value
            default => 'processing',
        });
    }
}
