# Payment Gateway Integration — Design Spec

Date: 2026-07-25
Status: approved, ready for implementation planning

## Problem

Today, `DonationController::store` accepts a donor-typed `amount` and treats it as fact — no payment processor is ever contacted. A "donation" is a trusted client claim, not verified money received. This spec makes donations real by integrating Midtrans as a payment gateway, without changing the platform to handle multi-streamer payout/settlement yet (explicitly deferred — see "Out of scope").

## Decisions

These were settled before design work started, and shape everything below:

- **Provider**: Midtrans, via the official `midtrans/midtrans-php` Composer package.
- **Integration mode**: Snap (Midtrans-hosted payment method UI) for now. The team plans to move to Midtrans's Core API later to build a fully custom payment method UI — the architecture below (Section "Service layer") is built so that swap only requires a new class, not touching call sites.
- **Donor UX**: Snap popup overlay (`window.snap.pay(token, ...)`), not a redirect — matches the current single-page AJAX donation form, no navigation away from the page.
- **Scope**: capture-only. Money lands in one Midtrans merchant account; how the platform pays out each streamer's share is a separate, later feature (tracked in `BACKLOG.md`).
- **Pending-donation visibility**: a donation that hasn't been paid yet is invisible on every public/real-time surface (widgets, leaderboard, milestone, subathon, SSE stats, admin/streamer revenue totals) — it only "exists" once payment is confirmed. It IS visible, with a status badge, in the streamer's own donation history and the admin's full donations list (internal oversight views), so a failed/abandoned attempt isn't invisible to the people who'd want to know about it.
- **Stale pending cleanup**: scheduled job, same pattern as the existing `CleanupExpiredQueueJob`/`CleanupOrphanedFilesJob`.

## Rejected approaches

- **Direct Midtrans SDK calls inline in controllers, no abstraction.** Contradicts the stated Core API migration plan — swapping later would mean rewriting every call site instead of one class.
- **Third-party Laravel-Midtrans wrapper package instead of the official SDK.** Extra dependency, lower reputation/maintenance guarantee than Midtrans's own first-party SDK, less control when moving to Core API.

Chosen instead: a thin `PaymentGateway` interface wrapping the official SDK (below).

## Architecture

### Data model

`donations` table gains (additive migration, no destructive changes):

```
status             VARCHAR   default 'pending'   -- pending | paid | failed | expired
payment_reference  VARCHAR   nullable, unique     -- Midtrans order_id, "TRX-{$donation->id}"
payment_type       VARCHAR   nullable             -- e.g. qris, bank_transfer, gopay, credit_card
paid_at            TIMESTAMP nullable
```

- `order_id` sent to Midtrans is `"TRX-{$donation->id}"` — 1:1 with the `Donation` row. No retry-with-new-order-id: if a donor abandons the popup, that row expires and a resubmit creates a fresh `Donation` (fresh order_id). Keeps webhook lookup a single indexed query.
- New index on `payment_reference`; existing `[streamer_id, created_at]` index unaffected.
- `Donation::$fillable` gains `status`, `payment_reference`, `payment_type`, `paid_at`; casts gains `paid_at => datetime`.
- `Donation::scopePaid($query)` local scope added, for static `Donation::` call sites that need explicit paid-only filtering.
- `Streamer::paidDonations(): HasMany` added — a filtered sibling of the existing `donations()` relation, mirroring the existing `activeMilestones()` vs `milestones()` convention already in `Streamer.php`. `donations()` itself stays unfiltered (needed by the webhook's `payment_reference` lookup and the cleanup job's `status = 'pending'` query, both of which must see non-paid rows).

### Service layer

`config/midtrans.php` (new, follows the existing `config/donation.php` style):
```php
'server_key'          => env('MIDTRANS_SERVER_KEY'),
'client_key'          => env('MIDTRANS_CLIENT_KEY'),
'is_production'       => env('MIDTRANS_IS_PRODUCTION', false),
'snap_expiry_minutes' => env('MIDTRANS_SNAP_EXPIRY_MINUTES', 60),
```

`App\Services\Payment\PaymentGatewayInterface`:
```php
interface PaymentGatewayInterface
{
    public function createTransaction(Donation $donation): PaymentTransaction;
    public function verifyNotification(array $payload): PaymentNotification;
}
```
`PaymentTransaction`/`PaymentNotification` are small readonly DTOs, not Eloquent models — keeps the interface provider-agnostic for the future Core API swap.

`App\Services\Payment\MidtransSnapGateway implements PaymentGatewayInterface`:
- `createTransaction()`: configures `\Midtrans\Config` from `config('midtrans.*')`, calls `\Midtrans\Snap::getSnapToken([...])` with `transaction_details => ['order_id' => "TRX-{$donation->id}", 'gross_amount' => $donation->amount]`, `customer_details => ['first_name' => $donation->name]`, `expiry => ['unit' => 'minutes', 'value' => config('midtrans.snap_expiry_minutes')]`.
- `verifyNotification()`: computes `hash_equals(hash('sha512', $orderId.$statusCode.$grossAmount.$serverKey), $payload['signature_key'])` — same `hash_equals` convention already used for `api_key` checks elsewhere in this codebase (see `docs/gotchas.md`) — throws `InvalidPaymentSignatureException` on mismatch, otherwise maps Midtrans's `transaction_status`/`fraud_status` to a normalized `paid`/`failed`/`expired`/`pending` result.

Bound in `AppServiceProvider::register()`: `$this->app->bind(PaymentGatewayInterface::class, MidtransSnapGateway::class)`. Tests bind a `FakePaymentGateway` instead — no real HTTP calls to Midtrans in the test suite.

### Donation submission flow

`DonationController::store` — validation/sanitization/media-upload unchanged. Tail end changes:

1. `Donation::create([..., 'status' => 'pending'])`, then set `payment_reference = "TRX-{$donation->id}"` and save (needs the row's `id` first).
2. Milestone `addAmount()` / subathon `addSubathonTime()` calls **removed from here** — moved to the webhook handler. Only confirmed money should move a milestone bar or add subathon minutes.
3. `ProcessDonationJob::dispatchSync(...)` **removed from here** too — moved to the webhook handler. The alert must not fire before payment is confirmed.
4. Call `PaymentGateway::createTransaction($donation)`, persist the token/reference, return `{success: true, data: {donation_id, snap_token}}`.
5. If `createTransaction()` throws (Midtrans unreachable/API error): the `Donation` row stays `pending` with no usable token. Unlike the existing alert-dispatch fallback (which has a queue to retry onto), there's nothing to background-retry here — no token means the donor has no way to pay. Return a clear error to the donor ("gagal memulai pembayaran, coba lagi") rather than a fake success.

Frontend (`resources/views/donate/show.blade.php`): add Snap.js `<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}">` (URL and key swapped based on `is_production`). On the existing AJAX success callback, call `window.snap.pay(snapToken, { onSuccess, onPending, onError, onClose })` instead of immediately showing the thank-you message. These callbacks are **UX only** — never treated as payment confirmation; only the server-to-server webhook is authoritative, per Midtrans's own documented best practice.

### Webhook flow

Route: `POST /webhooks/midtrans` → `PaymentWebhookController::handle`. Added to `bootstrap/app.php`'s `validateCsrfTokens(except: [...])` list (Midtrans posts without a Laravel CSRF token). Given this is a public unauthenticated endpoint, add a `throttle:payment-webhook` rate limiter too (defense in depth — the real gate is signature verification, same "public but signature-protected" shape as other endpoints documented in `docs/gotchas.md`).

`PaymentWebhookController::handle`:
1. `PaymentGateway::verifyNotification($request->all())` — bad signature → log + `403`, stop. Nothing after this point runs on unverified input.
2. `Donation::where('payment_reference', $notification->orderId)->first()` — not found → `404` + log.
3. **Idempotency gate first, independent of what the mapped status is**: `Donation::where('id', $donation->id)->where('status', 'pending')->update(['status' => $notification->mappedStatus, 'payment_type' => $notification->paymentType])`, check affected-row count. `0` rows affected means the donation was already out of `pending` (a prior notification already finalized it — Midtrans retries notifications) — return `200` immediately, do nothing else. This check is purely "was this row still `pending`", not "did it become `paid`" — the `capture`+`challenge` case maps to `pending → pending` and must still count as "already handled, don't reprocess" on a second identical delivery, not be mistaken for a fresh transition. Reuses the atomic-update philosophy already established for `Streamer::addSubathonTime()`/`Milestone::addAmount()` rather than introducing a new locking mechanism.
4. Only if the update in step 3 affected a row **and** `$notification->mappedStatus === 'paid'`: fire what `store()` used to fire inline — `ProcessDonationJob::dispatchSync($donation)` (with the same `dispatch()->delay(5s)` fallback on failure that exists today, preserving the "alert delivery can fail independently of confirmed payment" guarantee), `$milestone?->addAmount($donation->amount)`, `$streamer->addSubathonTime($donation->amount)` if subathon enabled, `ActivityLog::log('donation.paid', ...)`.
5. If the update in step 3 affected a row but `mappedStatus` was `failed`/`expired`: activity log only, no alert/milestone/subathon side effects. If `mappedStatus` was `pending` (the `challenge` case): no side effects, no log — donation is simply still awaiting manual resolution, next notification (if any) will re-evaluate.
6. Respond `200` once verified+processed (or already-processed); `403`/`404` only for the two rejection cases above.

Status mapping (Midtrans `transaction_status`/`fraud_status` → normalized status): `settlement` → `paid`; `capture` + `fraud_status=accept` → `paid`; `capture` + `fraud_status=challenge` → stays `pending` (manual fraud review case, not automated further in this spec); `deny`/`cancel` → `failed`; `expire` → `expired`; `pending` → no-op.

### Cleanup job

`App\Jobs\CleanupExpiredPendingDonationsJob` (same shape as `CleanupExpiredQueueJob`/`CleanupOrphanedFilesJob`):

```php
public function handle(): void
{
    $stale = Donation::where('status', 'pending')
        ->where('created_at', '<', now()->subMinutes(config('midtrans.snap_expiry_minutes') + 10))
        ->get();

    foreach ($stale as $donation) {
        if ($donation->media_path) {
            Storage::disk('public')->delete($donation->media_path);
        }
        $donation->update(['status' => 'expired']);
    }

    Log::info("CleanupExpiredPendingDonationsJob: expired {$stale->count()} stale pending donations");
}
```

- Updates to `expired`, does **not** delete the row — unlike `CleanupExpiredQueueJob` (which deletes pure ephemeral cache rows), a `Donation` is a durable record even unpaid; deleting it would erase evidence of a payment attempt that support/dispute handling might need later. Only the uploaded media file (real disk cost) is deleted.
- `+10` minute buffer past Snap's own expiry avoids racing a slightly-late expire webhook.
- Scheduled in `routes/console.php`: `Schedule::job(new CleanupExpiredPendingDonationsJob)->everyFifteenMinutes();`.

### Error handling — paid-only call sites

Every existing read of `Donation`/`donations()` was audited (20 call sites). They split into two buckets:

**Bucket 1 — public-facing / real-time / financial totals → must filter to `status = 'paid'`:**
- `StreamerStatsService` (leaderboard, today/total stats, cache-TTL last-donation lookup) — feeds `buildStats()`, consumed by SSE `stats` events and dashboards
- `Streamer::getTotalDonationsAttribute()`, `getTodayDonationsAttribute()`
- `ObsController::runningText()` — public OBS widget
- `AdminController::dashboard()` totals (`totalDonations`, `totalAmount`, `todayAmount`, `todayCount`)
- `ReportController` (CSV/PDF exports) — financial reports
- Milestone `addAmount()` / subathon `addSubathonTime()` — only ever called from the webhook now (Section "Webhook flow")

**Bucket 2 — internal history/management views → show every status, with a visible badge, no filtering:**
- `StreamerDashboardController::index()`'s recent-donations table (streamer's own dashboard)
- `AdminController::donations()` — full admin donations list

Mechanics: `Streamer::paidDonations()` / `Donation::scopePaid()` (Section "Data model") are used at every Bucket 1 site. Bucket 2 sites are left querying the raw, unfiltered relation/model, with a status column/badge added to their views.

Other error handling: `store()`'s `createTransaction()` failure (Section "Donation submission flow"). Webhook signature failures logged + `403`, never silently swallowed. If `ProcessDonationJob::dispatchSync()` throws inside the webhook handler, it falls back exactly like `DonationController::store` does today (`dispatch()->delay(5s)` onto the real queue).

### Testing strategy

- `FakePaymentGateway implements PaymentGatewayInterface` (test support) — `createTransaction()` returns a canned token/order-id, `verifyNotification()` returns a controllable normalized result. Bound per-test or in `TestCase`. No real HTTP calls to Midtrans anywhere in the suite.
- Feature tests (`tests/Feature/Donation/...`):
  - `store()` creates a `pending` `Donation`, calls the fake gateway, returns a token; asserts no `AlertQueue` row and no milestone/subathon change yet.
  - Webhook with valid signature + `settlement` → `Donation` becomes `paid`, `AlertQueue` row created, milestone/subathon updated once.
  - Webhook replay (identical payload twice) → second call is a no-op (idempotency via the conditional update); side effects fire exactly once.
  - Webhook with tampered/invalid signature → `403`, `Donation` stays `pending`, no side effects.
  - Webhook for unknown `order_id` → `404`.
  - `deny`/`expire` statuses → `Donation` becomes `failed`/`expired`, no alert/milestone side effects.
- `CleanupExpiredPendingDonationsJob` unit test — seeds a stale `pending` donation with a `media_path`, asserts it becomes `expired` and the file is deleted from the faked `Storage::disk('public')`; asserts a fresh `pending` row (within the expiry window) is untouched.
- Existing `phpunit.xml` env is unaffected (`QUEUE_CONNECTION=sync`, in-memory sqlite) — no real Midtrans sandbox credentials needed for the suite, by construction of the interface.

## Out of scope (tracked separately)

- **Streamer payout/settlement** — how the platform distributes collected money to individual streamers. Explicitly deferred; added to `BACKLOG.md` as a follow-up item.
- Automating the `capture` + `fraud_status=challenge` case (manual fraud review) beyond leaving the donation `pending`.
- Any UI for admins to see/manage pending or failed donation attempts beyond the existing list views gaining a status badge (ties into the existing "admin dashboard — make it useful" backlog item, not built here).
