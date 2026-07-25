# Streamer Payout / Settlement — Design Spec

Date: 2026-07-25
Status: approved, ready for implementation planning

## Problem

Payment gateway integration (Midtrans Snap, capture-only — see `docs/superpowers/specs/2026-07-25-payment-gateway-design.md`) made donations real, but all collected money sits in one platform-level Midtrans merchant account. There is currently no mechanism at all for tracking how much each streamer is owed, or for recording that they were actually paid. This spec builds that mechanism.

## Decisions

Settled before design work started:

- **Payout mechanism**: manual, admin-executed for this pass. The app tracks amounts owed and records payout history; an admin transfers money outside the app (bank transfer) and marks the record paid. **Automated disbursement via Midtrans's Payout/Iris API is explicitly deferred** — it needs a separate Midtrans product enabled on the merchant account plus real bank-account KYC that can't be exercised the same way Snap's sandbox could. Tracked as a follow-up in `BACKLOG.md`.
- **No interface abstraction in this pass**: unlike the payment gateway (where `PaymentGatewayInterface` made sense because both Snap and Core API call Midtrans, just differently), manual payout makes **no external API call at all** — it's a DB record plus an admin doing a bank transfer by hand. An interface would have nothing real to abstract yet. When automated disbursement is built later, *that's* when `PayoutGatewayInterface` gets introduced, wrapping the real Midtrans Payout API call; manual stays as an always-available fallback path.
- **Platform fee**: 10% of each payout's gross amount, stored as `config('payout.platform_fee_percent', 10)` (env-overridable, matching the existing `config/donation.php`/`config/otp.php`/`config/midtrans.php` convention for business-rule constants in this codebase — not an admin-editable UI setting).
- **Payout trigger**: admin-initiated, not scheduled. Admin dashboard shows each streamer's current owed balance; admin clicks "Create Payout" for a streamer, which snapshots the current owed amount into a `Payout` record.
- **Minimum payout threshold**: `config('payout.minimum_amount', 50000)`, same config-file convention.
- **Bank account info**: collected via new fields on the streamer's own Settings page (self-reported), snapshotted into each `Payout` record at creation time (so a later bank-info change never retroactively affects an already-created payout).
- **Streamer visibility**: streamers can see their own payout history (gross/fee/net breakdown, status, date) in their dashboard — transparency, not just an admin-only ledger.

## Architecture

### Data model

**`streamers` table** gains (nullable — existing streamers won't have these yet):
```
bank_name             VARCHAR nullable
bank_account_number   VARCHAR nullable
bank_account_holder   VARCHAR nullable
```
Editable on the existing streamer Settings page. Validated all-or-nothing: if any of the three is submitted, all three are required — partial bank info must never pass the "streamer has bank info" check `AdminPayoutController::create()` relies on.

**New `payouts` table:**
```
id
streamer_id           FK -> streamers.id
gross_amount          BIGINT   -- sum of included donations' amounts
platform_fee_amount   BIGINT   -- snapshotted at creation time, never recomputed later
net_amount            BIGINT   -- gross_amount - platform_fee_amount, what the streamer actually receives
status                VARCHAR default 'pending'   -- pending | paid | voided
bank_name             VARCHAR  -- snapshot of Streamer's bank fields AT CREATION TIME
bank_account_number   VARCHAR
bank_account_holder   VARCHAR
reference             VARCHAR nullable   -- admin-entered bank transfer reference, filled when marking paid
notes                 TEXT nullable
created_by            FK -> users.id (admin who created it)
paid_at               TIMESTAMP nullable
created_at / updated_at
```

**`donations` table** gains one column: `payout_id` FK nullable — same pattern as the existing `milestone_id` FK on that table. A `paid` donation with `payout_id = null` counts toward its streamer's owed balance; once assigned to a `Payout`, it's excluded from future owed-balance calculations. Voiding a `pending` payout resets its donations' `payout_id` back to `null` so they're picked up by the next payout.

**`config/payout.php`** (new, matching existing config-file style):
```php
'platform_fee_percent' => (int) env('PAYOUT_PLATFORM_FEE_PERCENT', 10),
'minimum_amount'        => (int) env('PAYOUT_MINIMUM_AMOUNT', 50000),
```

**`Streamer::unpaidOutDonations(): HasMany`** (new, mirrors the existing `paidDonations()`): `$this->paidDonations()->whereNull('payout_id')` — this is the "owed" query used everywhere.

### Payout creation flow

New `AdminPayoutController`, routes under the existing `admin`-gated group, mutating actions reuse the existing `throttle:admin-actions` limiter:

1. **`index()`** (`GET /admin/payouts`) — two sections on one page: (a) every streamer with `unpaidOutDonations()->sum('amount')` as their owed balance, "Create Payout" only actionable when owed ≥ `config('payout.minimum_amount')` **and** the streamer has `bank_account_number` set; (b) a table of existing `Payout` records (all streamers, most recent first, filterable by status) — this is where an admin finds a `pending` payout to open (`show()`) and either `markPaid()` or `void()`, and where `paid`/`voided` history is reviewed.
2. **`create(Streamer $streamer)`** (`POST /admin/payouts/{streamer}`) — inside `DB::transaction()`, `lockForUpdate()`s that streamer's `unpaidOutDonations()` rows (same concurrency pattern `ProcessDonationJob` already uses for `AlertQueue.seq`, so a donation landing mid-creation can't be silently double-counted or dropped). Re-validates server-side (owed ≥ minimum, bank info present — never trust just a disabled button). Computes `gross_amount` = sum, `platform_fee_amount` = `round(gross * fee_percent / 100)`, `net_amount` = gross − fee. Creates the `Payout` row (snapshotting the streamer's current bank fields), assigns `payout_id` to every locked donation. Logs `payout.created` via `ActivityLog`.
3. **`show(Payout $payout)`** (`GET /admin/payouts/{payout}`) — detail view: amount breakdown, snapshotted bank info, list of included donations (audit trail).
4. **`markPaid(Payout $payout)`** (`POST .../mark-paid`) — only valid from `status = pending`; requires a `reference` string input (the bank transfer reference/proof); sets `status = paid`, `paid_at = now()`. Logs `payout.paid`. Attempting this on a non-`pending` payout is rejected, not silently re-processed.
5. **`void(Payout $payout)`** (`POST .../void`) — only valid from `status = pending` (a `paid` payout can never be voided — money already left the platform; releasing its donations would let them be paid out a second time, a real double-payment risk). Sets `status = voided`, and in the same transaction resets `payout_id = null` on all its donations. Logs `payout.voided`.

Multiple `pending` payouts per streamer are valid, not an error condition: since `create()` only ever claims currently-unassigned donations, a second `create()` call while an earlier payout is still `pending` just captures whatever new paid donations arrived since — no double-claiming is possible.

### Streamer-facing visibility

`StreamerDashboardController::payouts()` (`GET /streamer/payouts`, behind the existing `streamer` middleware group) — read-only list of the authenticated streamer's own `Payout` records: gross/fee/net breakdown, `status`, `paid_at`, `reference`. No admin actions here, just transparency into what they were paid and when. Money formatting reuses the existing `Rp ` + `number_format()` convention already used everywhere (`Donation::getFormattedAmountAttribute()`, dashboard, reports) — no new formatting helper.

### Error handling & edge cases

- **Config changes don't retroactively affect existing payouts** — `platform_fee_percent`/`minimum_amount` changes only affect payouts created after the change, since each `Payout` snapshots its own `platform_fee_amount` at creation (same "snapshot, don't recompute" reasoning as the bank-info snapshot).
- **A `paid` payout is immutable** — no void, no edit. If a paid payout turns out to be a mistake, that's a manual reconciliation/support issue outside this system's scope for v1, not an automated reversal flow.
- **Bank info validation is all-or-nothing** on the streamer Settings save — partial info (e.g. just an account number) is rejected rather than silently accepted and later breaking `create()`'s bank-info check.

### Testing strategy

- **`Streamer::unpaidOutDonations()`** — seed paid/pending/failed/already-assigned donations for a streamer, assert only unassigned-`paid` ones count toward the sum.
- **`AdminPayoutController::create()`**:
  - Happy path: streamer with 3 paid donations (Rp 100,000 total) → `Payout` created with `gross_amount=100000`, `platform_fee_amount=10000`, `net_amount=90000`; all 3 donations get `payout_id` set; bank fields snapshotted correctly.
  - Below `minimum_amount` → validation error, no `Payout` row created, donations untouched.
  - Streamer missing bank account info → validation error, no `Payout` created.
  - A donation already assigned to a prior `Payout` is excluded from a new one: create once, add a new paid donation, create again — assert the second payout only contains the new donation.
- **`markPaid()`** — happy path sets `status=paid`/`paid_at`/`reference`; attempting on an already-`paid` or `voided` payout is rejected.
- **`void()`** — releases all its donations (`payout_id` back to `null`, confirmed by checking they're included in the next `unpaidOutDonations()` sum); attempting to void a `paid` payout is rejected.
- **Streamer Settings bank fields** — saving all three together succeeds; saving only one of three is rejected.
- **`StreamerDashboardController::payouts()`** — streamer A only ever sees their own payouts, never streamer B's.

## Out of scope (tracked separately)

- **Automated disbursement via Midtrans's Payout/Iris API** — needs a separate Midtrans product enabled plus real bank-account KYC. When built, introduces `PayoutGatewayInterface` (mirroring `PaymentGatewayInterface`'s shape) with the manual flow remaining as a fallback path. Add to `BACKLOG.md` as a follow-up once this ships.
- **Reversing/editing an already-`paid` payout** — treated as a manual reconciliation/support matter, not an in-app flow, for v1.
- **Scheduled/automatic payout batch creation** — this pass is admin-initiated only; a cron-scheduled batch creator (like the existing cleanup jobs) is a possible later enhancement, not built here.
