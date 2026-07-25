# Streamer-Initiated Payout Request — Design

**Goal:** let a streamer trigger creation of their own payout (instead of only an admin being able to), while keeping admin's existing manual-creation path as a backstop.

## Context

Today `AdminPayoutController::create(Streamer $streamer)` is the only way a `Payout` gets created — an admin visits `/admin/payouts`, sees which streamers have an owed balance above `config('payout.minimum_amount')` with bank info filled in, and clicks "Buat Payout". Streamers have no self-service option; `streamer/payouts.blade.php` is read-only history.

This flips the primary trigger to the streamer, while keeping admin's button as an override (e.g. a streamer inactive, or requesting help through another channel).

## Decisions

- **Direct creation, no approval step**: a streamer's request creates the `Payout` immediately (same eligibility rules as today's admin flow), not a separate "requested" state an admin has to approve first. Admin oversight still happens via the existing payout list, mark-paid/void actions, and the polling job for automated disbursement.
- **Keep admin's button**: `AdminPayoutController::create()` stays as-is, usable for any streamer regardless of who normally requests. Both paths must produce identical business behavior — enforced by extracting shared logic (below), not by duplicating it.
- **Streamer scoped to only their own streamer_id**: the new endpoint never accepts a streamer ID from the request — it always resolves `auth()->user()->streamer`, so there is no way for a streamer to trigger a payout for anyone else.

## Architecture

### Shared creation logic

Extract the transaction body of `AdminPayoutController::create()` into a new `App\Services\Payout\PayoutCreationService`:

```php
class PayoutCreationService
{
    public function __construct(private readonly PayoutGatewayInterface $payoutGateway) {}

    /**
     * @throws \InvalidArgumentException if the streamer isn't eligible (below
     *   minimum, missing bank info, or bank validation fails)
     */
    public function createFor(Streamer $streamer, ?int $createdByUserId): Payout
    {
        // same lockForUpdate + minimum-amount + bank-info checks, fee calc,
        // Payout::create(), donation payout_id assignment, and gateway
        // disburse() branching that AdminPayoutController::create() has today
    }
}
```

`AdminPayoutController::create()` becomes a thin wrapper: resolve the `$streamer` from the route, call the service with `Auth::id()`, catch `InvalidArgumentException` the same way it does now, log the same `ActivityLog` entry, redirect the same way. Behavior is unchanged for admin.

### New streamer-facing endpoint

`StreamerDashboardController::requestPayout(): RedirectResponse` — no route parameter. Resolves `auth()->user()->streamer` (404/redirect to `streamer.setup` if none, same guard pattern used elsewhere in this controller), calls `PayoutCreationService::createFor($streamer, Auth::id())`, same try/catch/redirect shape as admin's.

Route: `POST /streamer/payouts/request`, name `streamer.payouts.request`, inside the existing `auth`-gated streamer route group (same middleware as `streamer.payouts`).

New rate limiter `payout-request` in `AppServiceProvider::boot()`, keyed by user ID (matches the `settings-update` limiter's shape — an authenticated user mutating their own data).

### UI

`streamer/payouts.blade.php` gets a "Request Payout" button in the page header (mirroring the admin page's `page-header`/`page-header-left` structure), showing the streamer's own owed balance and bank status inline — same disabled+`title`-tooltip pattern just built for the admin page's "Buat Payout" (below minimum / bank info missing), computed from `$streamer->unpaidOutDonations()->sum('amount')` and `$streamer->bank_account_number`.

### Audit trail

`Payout::created_by` already exists and is already loaded via the `createdBy` relation in `AdminPayoutController::show()` — just not rendered. Add "Dibuat oleh: {name}" to `admin/payout-show.blade.php`'s page-subtitle, so admin can see whether a given payout was self-requested or admin-created. No schema change — `created_by` is always a `User`, whether admin or streamer; the label naturally reads correctly either way (the streamer's own account name, or the admin's).

## Error handling

Identical to today's admin flow: `InvalidArgumentException` (below minimum, missing bank info, or failed gateway bank validation) is caught and surfaced via `back()->withErrors(['payout' => ...])`, rendered in the existing global flash system — no new error-handling pattern needed.

## Testing

- `PayoutCreationServiceTest` — the extracted service's eligibility/fee/disbursement-branching logic, replacing (not duplicating) equivalent coverage currently implied by `PayoutCreationDisbursementTest`'s HTTP-level assertions.
- `StreamerPayoutRequestTest` — streamer can request when eligible; rejected when below minimum; rejected when bank info missing; a streamer can never affect another streamer's payout (no route parameter to tamper with, but assert the resolved streamer is always the authenticated one).
- Existing `PayoutCreationDisbursementTest` (admin flow) must still pass unchanged after the extraction — proves the refactor preserved behavior.
