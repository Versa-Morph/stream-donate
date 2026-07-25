<?php

namespace App\Services\Payout;

use App\Models\Payout;
use Illuminate\Support\Facades\Http;

/**
 * Wraps Midtrans Iris (disbursement/payout).
 *
 * Every request/response shape below was verified live against the Iris
 * sandbox (not just a Postman collection) during this class's development —
 * see the "Manual verification" note in
 * docs/superpowers/specs/2026-07-26-automated-payout-disbursement-design.md.
 * No field is a guess.
 *
 * Iris requires two separate API keys, each Basic-Auth'd as "{key}:" (empty
 * password):
 * - config('payout.iris_api_key'): the "creator" key. Can create/validate,
 *   but a live call confirmed it gets HTTP 401 "You are not authorized to
 *   perform this action" on both /payouts/approve and /payouts/reject.
 * - config('payout.iris_approver_api_key'): the "approver" key — the only
 *   one that can approve/reject. This is Iris's maker-checker control; this
 *   class deliberately defeats it by holding both keys and approving
 *   immediately (per the "auto-approve immediately" design decision), so
 *   both keys must be provisioned on the same server.
 *
 * Approve/reject are asynchronous: POST /payouts/approve returns
 * {"status":"ok"} (HTTP 202) immediately, but GET /payouts/{reference_no}
 * still reports the pre-approval status for a few seconds afterward — this
 * is exactly why CheckPayoutDisbursementStatusJob polls rather than trusting
 * the approve call's response.
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
        // GET /account_validation?bank=...&account=... — confirmed live.
        // Response has NO boolean validity field — it's e.g.
        // {"id":"...","account_no":"...","bank_name":"...","account_name":"..."}.
        // Sandbox always returns HTTP 200 with account_name "External Account
        // Inquiry Simulator" regardless of the account number given — the
        // simulator doesn't actually check anything, so HTTP success is the
        // only signal available to test here. In production, an unknown
        // account is expected to fail (non-2xx) rather than return a
        // simulator placeholder — reconfirm this against a real production
        // account before relying on it there.
        $response = $this->creatorClient()->get('account_validation', [
            'bank' => $payout->bank_name,
            'account' => $payout->bank_account_number,
        ]);

        return $response->successful();
    }

    public function disburse(Payout $payout): PayoutDisbursementResult
    {
        // POST /payouts — request body and response confirmed live:
        // response is {"payouts":[{"status":"queued","reference_no":"..."}]}.
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

        $referenceNo = $createResponse->json('payouts.0.reference_no');

        // Full automation per design decision: approve immediately with the
        // separate approver key, no separate admin click. Confirmed live
        // that the creator key gets a 401 here — approverClient() is
        // required, not optional.
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
        // GET /payouts/{reference_no} — confirmed live. Response's "status"
        // field takes exactly: "queued" (created, pending approval or still
        // processing after approval), "completed" (terminal success),
        // "rejected" (terminal failure, whether via /payouts/reject or an
        // approval that failed downstream).
        $response = $this->creatorClient()->get("payouts/{$payout->reference}");
        $status = $response->json('status');

        return new PayoutStatusResult(status: match ($status) {
            'completed' => 'paid',
            'rejected' => 'failed',
            default => 'processing', // 'queued', or an unrecognized value
        });
    }
}
