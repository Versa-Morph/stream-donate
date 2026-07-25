# Admin: Surfacing Failed Donation Alerts — Design Spec

Date: 2026-07-25
Status: approved, ready for implementation planning

## Problem

`ProcessDonationJob::failed()` already logs a `donation.alert_failed` `ActivityLog` entry whenever a donation's alert permanently fails to queue (all retries exhausted) — but today that's only visible buried in the mixed, fixed-limit "recent activity" list on the admin dashboard, or in log files. There's no dedicated, actionable place for an admin to find these and do something about them. This is one of four independent pieces bundled in `BACKLOG.md`'s "Admin dashboard — make it useful" item; the other three (trends chart, list filtering, per-streamer drill-down) are separate, later specs.

## Decisions

- **Retry action included**: admins can re-dispatch the alert for a failed donation directly from this view, not just view-only visibility.
- **Placement**: a small count card on the main admin dashboard ("N Alert Gagal → Lihat"), linking to a dedicated `/admin/alert-failures` page — not folded into the existing `/admin/logs` filter.
- **No new migration**: reuses the existing `activity_logs` table. "Unresolved" is determined by comparing `donation.alert_failed` log entries against `donation.alert_retried` log entries (a new action, logged only on a successful retry) for the same `donation_id` — not by checking `AlertQueue` existence, which would give a false "still broken" reading once the ephemeral `AlertQueue` row from a successful retry gets cleaned up by the existing `CleanupExpiredQueueJob` (15 minutes later).

## Architecture

### Detecting "unresolved" failures

New `App\Services\AlertFailureService` (mirrors the existing `StreamerStatsService` precedent — reusable stats logic extracted out of controllers, used by both the dashboard card and the dedicated page):

```php
public function unresolved(): Collection
{
    $retriedDonationIds = ActivityLog::where('action', 'donation.alert_retried')
        ->get()
        ->map(fn ($log) => $log->payload['donation_id'] ?? null)
        ->filter()
        ->all();

    return ActivityLog::where('action', 'donation.alert_failed')
        ->orderByDesc('created_at')
        ->get()
        ->reject(fn ($log) => in_array($log->payload['donation_id'] ?? null, $retriedDonationIds))
        ->values();
}

public function unresolvedCount(): int
{
    return $this->unresolved()->count();
}
```

Filtering happens in PHP after two plain queries, not a single JSON-path SQL query (`payload` is a JSON column) — deliberately simple since alert failures should be rare (a job permanently failing after exhausting all retries isn't a common event). If volume ever grows large enough for this to matter, it can move to a SQL-side JSON query then.

### Retry action

New `AdminAlertFailureController::retry(Donation $donation)`:

```php
public function retry(Donation $donation): RedirectResponse
{
    try {
        ProcessDonationJob::dispatchSync($donation);
    } catch (\Throwable $e) {
        return back()->withErrors(['retry' => 'Retry gagal: ' . $e->getMessage()]);
    }

    ActivityLog::log(
        action: 'donation.alert_retried',
        description: "Alert donasi #{$donation->id} berhasil di-retry oleh admin",
        userId: Auth::id(),
        streamerId: $donation->streamer_id,
        payload: ['donation_id' => $donation->id],
    );

    return back()->with('success', 'Alert berhasil di-retry.');
}
```

- **`dispatchSync()`, not `dispatch()`** — the admin clicking "Retry" wants an immediate result (success or fails-again), not a background job they have to come back and check on. This also means `SyncQueue` propagates a thrown exception back to this catch block after invoking the job's own `failed()` hook (which logs its own fresh `donation.alert_failed` entry) — expected, not a bug: a second failure is genuinely another failure occurrence.
- **`donation.alert_retried` is only logged on success** — a retry that fails again throws before reaching that log call, so the donation correctly stays in `unresolved()` for another attempt.
- **Safe against double-crediting**: `ProcessDonationJob::failed()` only fires when the job's entire `DB::transaction()` rolled back, meaning no `AlertQueue` row was ever created for that donation — so a retry can't create a *second* alert for a donation that actually succeeded the first time.
- **Known, accepted minor risk**: two rapid clicks on the same "Retry" button could both call `dispatchSync()` before either completes, producing two `AlertQueue` rows and the donor's alert playing twice on OBS. Not guarded against — same risk tolerance as this codebase's other admin actions (donation delete, user toggle, etc. have no double-submit locking either), and the failure mode is cosmetic, not a data-integrity or financial issue.

### Routes, dashboard card, dedicated page

Routes (inside the existing `admin`-gated group in `routes/web.php`):
```php
Route::get('/alert-failures', [AdminAlertFailureController::class, 'index'])->name('alert-failures.index');
Route::post('/alert-failures/{donation}/retry', [AdminAlertFailureController::class, 'retry'])
    ->middleware('throttle:admin-actions')
    ->name('alert-failures.retry');
```

`AdminController::dashboard()` gains one line: `$unresolvedAlertFailures = app(AlertFailureService::class)->unresolvedCount();`, passed to the view. The dashboard view gets a small card, only rendered when count > 0: "⚠ N Alert Gagal → Lihat", linking to `route('admin.alert-failures.index')` — matches the existing small-summary-card pattern already on that dashboard.

`AdminAlertFailureController::index()` fetches `AlertFailureService::unresolved()`, resolves each log's `donation_id` (from `payload`) into the actual `Donation` (with `streamer`) for display via `Donation::find()` (not `findOrFail`) — `AdminController::deleteDonation` already lets an admin delete a `Donation` row, so a log entry whose donation no longer exists is a real, reachable case, not a hypothetical. Rows whose donation was deleted render with a "Donasi telah dihapus" placeholder in place of the donor/amount/streamer columns, and no Retry button (nothing to retry). Table columns: donor name, amount, streamer, error message (`payload['error']`), failed-at time, Retry button per row. Uses `<x-app-layout>` + this codebase's existing `.page-container`/`.table-card` classes — a Blade component layout, not `@extends`/`@section` (a real mistake caught during the payout work; every admin view in this codebase uses the component form).

Nav link added to `resources/views/layouts/app.blade.php`'s admin block, alongside Payout/Logs.

### Testing strategy

- **`AlertFailureService::unresolved()`** — the core logic, fully testable without needing a real job failure: seed one `ActivityLog` with `action='donation.alert_failed'` and `payload={donation_id: 1}`, assert it's included; seed a second failed log for `donation_id=2` plus a matching `donation.alert_retried` log for `donation_id=2`, assert that one is excluded.
- **`AdminAlertFailureController::index()`** — seed an unresolved failure, assert the page shows the donor name/amount/error message and a working Retry form.
- **`index()` with a deleted donation** — seed an unresolved failure log whose `donation_id` doesn't correspond to any existing `Donation` (simulating `AdminController::deleteDonation` having removed it), assert the page renders without error and shows the "Donasi telah dihapus" placeholder instead of crashing on a null donation.
- **`AdminAlertFailureController::retry()` success path** — seed a `Donation` + matching unresolved failure log, call retry, assert: an `AlertQueue` row now exists for that donation, a `donation.alert_retried` log was created, and `AlertFailureService::unresolved()` no longer includes it.
- **Dashboard card** — seed one unresolved failure, assert the count card renders; assert it's absent when there are none.

**Deliberate gap**: no automated test for `retry()`'s catch branch (job throws again). Reliably forcing `ProcessDonationJob::handle()` to throw in a test requires either a null-`streamer` relation (blocked by `donations.streamer_id`'s FK + cascade-delete) or another DB-level violation with no natural trigger here. This matches the existing convention in this codebase — the analogous catch blocks in `DonationController`'s queue-dispatch fallback aren't unit-tested for their throw path either. The catch block stays as defensive code, verified by reading rather than an automated test.

## Out of scope (tracked separately)

- **Trends chart, list filtering, per-streamer drill-down** — the other three pieces of `BACKLOG.md`'s original "Admin dashboard — make it useful" item, each its own future spec.
- **Moving `unresolved()`'s filtering to a SQL-side JSON query** — only worth doing if failure volume grows large enough to matter.
