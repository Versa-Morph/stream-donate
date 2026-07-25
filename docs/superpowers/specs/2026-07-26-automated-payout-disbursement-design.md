# Automated Payout Disbursement (Midtrans Iris) — Design Spec

Date: 2026-07-26
Status: approved, ready for implementation planning

## Problem

Payout tracking is manual today (see `docs/superpowers/specs/2026-07-25-payout-settlement-design.md`) — an admin transfers money to a streamer outside the app and marks the record paid. This spec builds the deferred automation piece from that design and `BACKLOG.md`: integrate Midtrans's Iris (Payout/Disbursement) API so the app can trigger the actual bank transfer, gated behind a feature flag since real Midtrans Iris credentials and bank-account KYC aren't set up yet.

## Important limitation, stated up front

~~Iris's exact `CreatePayout`/`ApprovePayout` JSON request/response field names could not be confirmed through available documentation tools in this session~~ — **update:** subsequently confirmed, both from Midtrans's official Iris Postman collection and by live-testing every endpoint against the Iris sandbox (`account_validation`, `payouts` create, `payouts/approve`, `payouts/reject`, `payouts/{reference_no}`). All request/response field names in `MidtransIrisGateway` are now real, verified values — see that class's docblock for the confirmed shapes, and the implementation plan's Task 5 for how each endpoint was tested live. The two-step creator→approver workflow is real and enforced server-side (a live call with only the creator key against `/payouts/approve` returns HTTP 401 "You are not authorized to perform this action") — not just a Postman-collection convention.

## Decisions

- **Full automation, no manual approval checkpoint**: the app calls `CreatePayout` then immediately `ApprovePayout` back-to-back — Iris's two-step creator/approver split exists as a safety checkpoint, and this explicitly removes it in favor of hands-off disbursement. Flagged during brainstorming as a real tradeoff; the user chose full automation over keeping a second deliberate admin click.
- **Single global feature flag**, `config('payout.automated_disbursement_enabled', false)` — default off (the only safe state pre-KYC). Off: `ManualPayoutGateway` (today's behavior, unchanged). On: `MidtransIrisGateway`.
- **Bank field becomes a controlled-vocabulary dropdown**, not free text — Iris needs a specific bank code, which free text can't reliably map to. Existing free-text `bank_name` values get nulled by a one-time migration data-fix; affected streamers re-select once.
- **New `processing` + `failed` `Payout` statuses**, plus a polling job — `CreatePayout`+`ApprovePayout` succeeding doesn't guarantee the underlying bank transfer succeeds; a scheduled job polls Iris until resolved rather than trusting the initial accept as final.

## Architecture

### Gateway abstraction + feature flag

```php
// app/Services/Payout/PayoutGatewayInterface.php
interface PayoutGatewayInterface
{
    public function validateBankAccount(Payout $payout): bool;
    public function disburse(Payout $payout): PayoutDisbursementResult; // reference, status
    public function checkStatus(Payout $payout): PayoutStatusResult;
}
```

- **`ManualPayoutGateway`** (default) — wraps exactly what `AdminPayoutController` does today: no API calls; `disburse()` is a no-op, the payout stays `pending` for manual handling.
- **`MidtransIrisGateway`** (behind the flag) — `validateBankAccount()` → Iris `ValidateBankAccount`; `disburse()` → `CreatePayout` then `ApprovePayout`; `checkStatus()` → `GetPayoutDetails`. Uses a **separate Iris API key** (`config('payout.iris_api_key')`, distinct from `config('midtrans.server_key')` used for Snap), Basic Auth (same `Authorization: Basic base64(key:)` pattern as the existing Snap/Core API auth). The three method bodies are the pre-implementation TODO described above.
- `config('payout.automated_disbursement_enabled', false)` (env-overridable) selects which gateway `AdminPayoutController` resolves from the container.

### Bank dropdown

New `config/banks.php` — a bundled static list of major Indonesian banks with their Iris bank codes (BCA, BNI, BRI, Mandiri, CIMB Niaga, Permata, BSI, etc.), since live `GetBeneficiaryBanks()` can't be called without real credentials right now. `Streamer::bank_name` stores the **code** (e.g. `"bca"`) going forward, not a display name. A new `Streamer::bankDisplayName()` accessor looks up the friendly name from `config('banks')` wherever it's rendered (payout views, admin drill-down page). A migration nulls out any existing `bank_name` value that doesn't match a known code — a one-time, unavoidable cost of moving from free text to a controlled vocabulary.

### `processing`/`failed` status + polling job

No schema migration needed for the new status values — `Payout.status` is already a plain `string(20)` column, not a DB-level enum.

Flow when the flag is on: `AdminPayoutController::create()` still creates the `Payout` as `pending` exactly as today (same locking, fee calc, bank-info snapshot). If `validateBankAccount()` fails, that's a fail-fast validation error *before* the `Payout` row exists at all (same shape as today's "missing bank info" check). If validation passes, immediately calls `disburse()`:
- **Success** → `status = 'processing'`, Iris's returned reference stored in the existing `reference` column (dual-purpose: admin-entered for manual payouts, gateway-returned for automated ones).
- **Failure** (e.g. `ApprovePayout` throws after `CreatePayout` succeeded) → `status = 'failed'`, donations released back to unpaid-out (`payout_id = null`, reusing `void()`'s existing release logic) — never silently lost.

New `CheckPayoutDisbursementStatusJob` (scheduled every 15 min, mirrors `CleanupExpiredPendingDonationsJob`'s cadence/pattern): fetches every `processing` `Payout`, calls `checkStatus()`, resolves to `paid` (+`paid_at`) or `failed` (+donation release).

**Lifecycle guard updates**: `void()` still only allows `pending` (a `processing` payout has money already in flight via Iris, can't be cancelled). `markPaid()` now also accepts `processing` → `paid` (admin manual override if they see confirmation in the Midtrans dashboard before the polling job catches up), alongside its existing `pending` → `paid` path.

### Testing strategy

- **`FakePayoutGateway`** (test-only, mirrors `FakePaymentGateway`'s precedent exactly) — controllable `validateBankAccount()`/`disburse()`/`checkStatus()` results, bound as a **singleton** (not `bind()`) when the flag is on for a given test.
- **Flag off (default)** — `AdminPayoutController::create()` behaves exactly as today; a regression test, not new-behavior proof.
- **Flag on, `disburse()` succeeds** — `Payout` → `processing` with the gateway's reference stored; donations still assigned.
- **Flag on, `validateBankAccount()` fails** — validation error before any `Payout` row is created, no donations touched.
- **Flag on, `disburse()` fails after creation** — `Payout` → `failed`, donations released back to unpaid-out.
- **`CheckPayoutDisbursementStatusJob`** — a `processing` payout resolves to `paid` or `failed` (+release) per the fake's `checkStatus()` result; `pending`/`paid`/`voided` payouts are never touched by the job.
- **`markPaid()` valid from `processing`** now too, alongside `pending`. **`void()` still rejects `processing`**, alongside already rejecting `paid`.
- **Bank dropdown** — Settings validates the selected code against `config('banks')`; a migration test confirms non-matching existing free-text values get nulled.

Same boundary as the Snap/Chart.js work: `MidtransIrisGateway`'s actual HTTP calls can't be tested against the real API here — coverage stops at "everything around the gateway boundary is correct," verified via the fake.

## Out of scope (tracked separately)

- **The exact `CreatePayout`/`ApprovePayout`/`ValidateBankAccount` JSON payloads** — must be confirmed against Midtrans's live Iris API reference before `MidtransIrisGateway`'s three methods can actually be implemented; everything else in this spec doesn't depend on that confirmation.
- **Dynamic bank list** (calling `GetBeneficiaryBanks()` to keep `config/banks.php` current) — the static bundled list is a deliberate starting point, not meant to be the permanent source once real credentials exist.
- **Restoring the creator/approver checkpoint** as an option — this spec removes it entirely per the user's explicit choice; reintroducing it as a configurable choice is a possible future refinement, not built here.
