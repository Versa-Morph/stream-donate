# Payment Gateway Integration — Design

Status: approved (brainstorming), pending implementation plan.

## Goal

Right now `DonationController::store` accepts a donor-typed `amount` and treats it as truth — no money is ever verified as received. This design makes donations real: a `Donation` is only confirmed (and only then does it trigger an alert, milestone/subathon update, or count in stats) once a payment gateway confirms the money actually arrived.

Provider: **Midtrans**, integration mode: **Snap** (Midtrans-hosted popup UI covering QRIS/e-wallet/VA/card), popup overlay UX (donor never leaves the donation page).

**Explicitly out of scope for this design:** streamer payout/settlement (how collected money is distributed to individual streamers). Captured as a follow-up in `BACKLOG.md`. Also out of scope: a manual fraud-review queue for card `challenge` status — those are treated as `failed` for now (see Status mapping).

## Why an interface, not a direct SDK call

The user has already indicated Core API (custom-built payment-method UI, instead of Midtrans's hosted Snap popup) is a planned follow-up. A `PaymentGatewayContract` interface with a `MidtransSnapGateway` implementation means that swap is a new class + one container binding change — `DonationController` and the webhook route never move. This isn't speculative abstraction; it's sized for a concretely stated next step.

## Data model

Additive migration on the existing `donations` table:

| Column | Type | Purpose |
|---|---|---|
| `payment_status` | string, default `pending` | `pending` \| `paid` \| `failed` \| `expired` — one-way transition to a final state |
| `payment_gateway` | string, default `midtrans` | future gateways (Core API) can use a different value without a schema change |
| `gateway_order_id` | string, unique | our generated order code (`SD-` + random), sent to Midtrans as `order_id`, set at row creation |
| `gateway_transaction_id` | string, nullable | Midtrans's own `transaction_id`, filled by the webhook |
| `payment_type` | string, nullable | e.g. `qris`, `gopay`, `bank_transfer` — from the webhook, reporting only |
| `paid_at` | timestamp, nullable | set when `payment_status` transitions to `paid` |
| `gateway_payload` | json, nullable | last raw webhook payload, kept for audit/dispute debugging |

### Status mapping (from Midtrans `transaction_status` + `fraud_status`)

| Midtrans status | fraud_status | → `payment_status` |
|---|---|---|
| `capture` or `settlement` | `accept` (or n/a for non-card) | `paid` |
| `capture` | `challenge` | `failed` (no fraud-review queue in v1) |
| `pending` | — | stays `pending` |
| `deny`, `cancel` | — | `failed` |
| `expire` | — | `expired` |

Once `payment_status` is `paid`/`failed`/`expired`, it is final. A duplicate webhook delivery for an already-final donation must be a no-op — checked inside a locked transaction before any state change — so a donation can never be double-credited (double alert fire, double milestone/subathon increment) by a retried notification.

## Components

```
app/Services/Payment/
  PaymentGatewayContract.php    # interface: createTransaction(Donation), handleNotification(array $payload)
  MidtransSnapGateway.php       # implements it — wraps Snap::getSnapToken() + SHA512 signature verification
  DonationFinalizer.php         # shared "mark paid + fire alert" logic — the ONLY entry point that credits a donation

app/Http/Controllers/
  DonationController.php        # store() creates a *pending* Donation + returns a Snap token, nothing else
  PaymentWebhookController.php  # new — POST /webhooks/midtrans, verifies signature, updates status, calls DonationFinalizer

app/Jobs/
  PaymentCleanupJob.php         # new — scheduled, expires stale pending donations + deletes their orphaned media

config/midtrans.php             # new — server_key, client_key, is_production from env
```

`PaymentGatewayContract` is bound to `MidtransSnapGateway` in a service provider.

### Request flow

1. `DonationController::store` keeps all existing validation, profanity filtering, and media upload/duration-tier logic as-is. Instead of finalizing immediately, it creates the `Donation` with `payment_status=pending` + a generated `gateway_order_id`, calls `PaymentGatewayContract::createTransaction($donation)` (sets a custom 60-minute expiry, not Midtrans's 24h default), and returns `{success, snap_token}` to the frontend. No alert, no milestone/subathon update, no thank-you message at this point.
2. If `createTransaction()` throws (Midtrans API down/timeout), the same `Donation` row is immediately marked `failed` (not left dangling `pending`) and the donor gets an error asking them to retry — a retry is a fresh `Donation` row + fresh Snap token.
3. Frontend: `donate.show` loads the Snap.js script; the existing AJAX submit flow is unchanged except the success handler calls `window.snap.pay(token, {onSuccess, onPending, onError, onClose})` instead of showing the thank-you message directly. These callbacks are **UI-only** ("waiting for payment confirmation…") and never credit a donation themselves — a donor's browser can be closed, spoofed, or lie, so only the server-to-server webhook is trusted.
4. `PaymentWebhookController` (public, CSRF-exempt — Midtrans posts server-to-server with no session/token, no rate limiter either since the signature check is the real gate) verifies the signature first, before trusting any field in the payload. On mismatch: log + respond `403`, do nothing else. On a valid signature: look up `Donation` by `gateway_order_id` inside `DB::transaction` + `lockForUpdate()`; if the `order_id` doesn't match any donation, log + respond `404`; if the donation is already in a final state, respond `200` (idempotent no-op, this is the expected shape of a Midtrans retry); otherwise apply the status mapping, and only on `paid` call `DonationFinalizer`.
5. `DonationFinalizer` is exactly the logic currently inline in `DonationController::store` (milestone `addAmount`, subathon `addSubathonTime`, `ProcessDonationJob::dispatchSync` + delayed-queue fallback, `ActivityLog::log('donation.create', ...)`) — extracted so there is exactly one code path that ever credits a donation, called only from the webhook once payment is confirmed.

### Cleanup

`PaymentCleanupJob`, scheduled every 15–30 minutes: finds `payment_status=pending` rows past their Snap expiry window, marks them `expired`, and deletes any attached `media_path` file from storage. Kept as its own job (not folded into the existing `CleanupOrphanedFilesJob`) to avoid two jobs racing on deleting the same file.

### Visibility

Pending donations never appear anywhere — not streamer dashboard, not admin dashboard, not leaderboard/milestone/subathon/stats — until `payment_status=paid`. `StreamerStatsService`'s existing SQL aggregations should be checked to ensure they filter on `payment_status='paid'` (today they implicitly assume every row is a real donation).

## Error handling summary

| Failure | Handling |
|---|---|
| Snap token creation fails at submit time | Donation marked `failed` immediately, donor told to retry |
| Webhook signature invalid | `403`, no state change, logged |
| Webhook `order_id` unknown | `404`, logged |
| Webhook for already-final donation | `200` no-op (expected Midtrans retry behavior) |
| Donor abandons Snap popup / payment times out | `PaymentCleanupJob` marks `expired` + deletes orphaned media after the expiry window |
| Card payment flagged `challenge` (fraud review) | Treated as `failed` in v1 — no manual review queue |

## Testing plan

- `PaymentGatewayContract` bound to a fake in tests — no real Midtrans calls from the test suite.
- `MidtransSnapGateway` unit test: signature verification math (`SHA512(order_id+status_code+gross_amount+ServerKey)`) — easy to get subtly wrong (field order, missing field).
- `DonationController::store` feature tests: valid submission → `pending` `Donation` + `snap_token` returned, no `AlertQueue` row, no milestone/subathon change.
- `PaymentWebhookController` feature tests (via the fake gateway):
  - valid `settlement` → `paid`, `DonationFinalizer` runs exactly once
  - same notification delivered twice → finalize runs only once (no duplicate alert/increment)
  - `deny`/`cancel`/`expire` → `failed`/`expired`, finalize never runs
  - bad signature → rejected, donation untouched
  - unknown `order_id` → `404`, nothing touched
- `PaymentCleanupJob` test: old `pending` donation past expiry + attached fake media file → run job → `expired` + file deleted; fresh `pending` donation → untouched.

## Follow-ups (not this design)

- Streamer payout/settlement — tracked in `BACKLOG.md`.
- Manual fraud-review queue for card `challenge` status.
- Core API integration (custom payment-method UI) — the `PaymentGatewayContract` interface exists specifically to make this swap contained.
