# Admin: Surfacing Failed Donation Alerts Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Give admins a dedicated, actionable place to see donations whose alert permanently failed to queue, and retry them, instead of the failure being buried in the mixed activity-log list.

**Architecture:** A new `AlertFailureService` derives "unresolved" failures by comparing existing `donation.alert_failed` activity-log entries against `donation.alert_retried` entries (new, logged only on a successful retry) — no schema change. A new `AdminAlertFailureController` exposes a list + retry action; `AdminController::dashboard()` gains a one-line count for a conditional warning card.

**Tech Stack:** Laravel 12, PHP 8.2+, PHPUnit 11 (existing suite conventions).

## Global Constraints

- Design source of truth: `docs/superpowers/specs/2026-07-25-admin-alert-failures-design.md` — every task below implements one section of it.
- User-facing strings are Indonesian, matching the rest of the codebase.
- No new migration — this feature reuses the existing `activity_logs` table entirely.
- Admin views use `<x-app-layout>...</x-app-layout>` (a Blade component), never `@extends('layouts.app')`/`@section` — confirmed the hard way during the payout work; every existing admin view uses the component form.
- Rupiah amounts always format as `number_format($x, 0, ',', '.')` (Indonesian locale) — confirmed the hard way during the payout work; the bare default renders US-locale commas instead.

---

### Task 1: `AlertFailureService` — the "unresolved" detection logic

**Files:**
- Create: `app/Services/AlertFailureService.php`
- Test: `tests/Feature/AlertFailure/AlertFailureServiceTest.php`

**Interfaces:**
- Produces: `AlertFailureService::unresolved(): \Illuminate\Support\Collection` (of `ActivityLog` models, newest first), `AlertFailureService::unresolvedCount(): int`.

- [ ] **Step 1: Write the failing tests**

```php
<?php
// tests/Feature/AlertFailure/AlertFailureServiceTest.php
namespace Tests\Feature\AlertFailure;

use App\Models\ActivityLog;
use App\Services\AlertFailureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlertFailureServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_failure_with_no_retry_is_unresolved(): void
    {
        ActivityLog::log(
            action: 'donation.alert_failed',
            description: 'Alert donasi #1 gagal',
            payload: ['donation_id' => 1, 'error' => 'Queue timeout'],
        );

        $unresolved = app(AlertFailureService::class)->unresolved();

        $this->assertCount(1, $unresolved);
        $this->assertSame(1, app(AlertFailureService::class)->unresolvedCount());
    }

    public function test_failure_with_matching_retry_is_excluded(): void
    {
        ActivityLog::log(
            action: 'donation.alert_failed',
            description: 'Alert donasi #2 gagal',
            payload: ['donation_id' => 2, 'error' => 'DB error'],
        );
        ActivityLog::log(
            action: 'donation.alert_retried',
            description: 'Alert donasi #2 berhasil di-retry',
            payload: ['donation_id' => 2],
        );

        $unresolved = app(AlertFailureService::class)->unresolved();

        $this->assertCount(0, $unresolved);
        $this->assertSame(0, app(AlertFailureService::class)->unresolvedCount());
    }

    public function test_retry_for_a_different_donation_does_not_resolve_this_one(): void
    {
        ActivityLog::log(
            action: 'donation.alert_failed',
            description: 'Alert donasi #3 gagal',
            payload: ['donation_id' => 3, 'error' => 'Timeout'],
        );
        ActivityLog::log(
            action: 'donation.alert_retried',
            description: 'Alert donasi #4 berhasil di-retry',
            payload: ['donation_id' => 4],
        );

        $this->assertCount(1, app(AlertFailureService::class)->unresolved());
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `./vendor/bin/phpunit tests/Feature/AlertFailure/AlertFailureServiceTest.php`
Expected: FAIL — `AlertFailureService` doesn't exist yet.

- [ ] **Step 3: Create the service**

```php
<?php
// app/Services/AlertFailureService.php
namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Collection;

class AlertFailureService
{
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
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `./vendor/bin/phpunit tests/Feature/AlertFailure/AlertFailureServiceTest.php`
Expected: PASS (3 tests)

- [ ] **Step 5: Run the full suite to check for regressions**

Run: `./vendor/bin/phpunit`

- [ ] **Step 6: Commit**

```bash
git add app/Services/AlertFailureService.php tests/Feature/AlertFailure/AlertFailureServiceTest.php
git commit -m "feat: add AlertFailureService to detect unresolved donation alert failures"
```

---

### Task 2: `AdminAlertFailureController` — list + retry

**Files:**
- Create: `app/Http/Controllers/AdminAlertFailureController.php`
- Modify: `routes/web.php`
- Create: `resources/views/admin/alert-failures.blade.php`
- Modify: `resources/views/layouts/app.blade.php` (nav link)
- Test: `tests/Feature/AlertFailure/AdminAlertFailureControllerTest.php`

**Interfaces:**
- Consumes: `AlertFailureService::unresolved()` (Task 1), `ProcessDonationJob::dispatchSync()` (existing, unchanged).
- Produces: `GET /admin/alert-failures`, `POST /admin/alert-failures/{donation}/retry`.

- [ ] **Step 1: Write the failing tests**

```php
<?php
// tests/Feature/AlertFailure/AdminAlertFailureControllerTest.php
namespace Tests\Feature\AlertFailure;

use App\Models\ActivityLog;
use App\Models\AlertQueue;
use App\Models\Donation;
use App\Models\Streamer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAlertFailureControllerTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        return $admin;
    }

    public function test_index_shows_unresolved_failure_with_retry_form(): void
    {
        $admin = $this->admin();
        $streamer = Streamer::factory()->create();
        $donation = Donation::factory()->for($streamer)->create(['status' => 'paid', 'name' => 'Andi']);
        ActivityLog::log(
            action: 'donation.alert_failed',
            payload: ['donation_id' => $donation->id, 'error' => 'Queue timeout'],
        );

        $response = $this->actingAs($admin)->get('/admin/alert-failures');

        $response->assertOk();
        $response->assertSee('Andi');
        $response->assertSee('Queue timeout');
        $response->assertSee(route('admin.alert-failures.retry', $donation));
    }

    public function test_index_handles_deleted_donation_gracefully(): void
    {
        $admin = $this->admin();
        ActivityLog::log(
            action: 'donation.alert_failed',
            payload: ['donation_id' => 999999, 'error' => 'Queue timeout'],
        );

        $response = $this->actingAs($admin)->get('/admin/alert-failures');

        $response->assertOk();
        $response->assertSee('Donasi telah dihapus');
    }

    public function test_retry_creates_alert_queue_and_logs_success(): void
    {
        $admin = $this->admin();
        $streamer = Streamer::factory()->create();
        $donation = Donation::factory()->for($streamer)->create(['status' => 'paid']);
        ActivityLog::log(
            action: 'donation.alert_failed',
            payload: ['donation_id' => $donation->id, 'error' => 'Queue timeout'],
        );

        $response = $this->actingAs($admin)->post("/admin/alert-failures/{$donation->id}/retry");

        $response->assertSessionHasNoErrors();
        $this->assertSame(1, AlertQueue::where('donation_id', $donation->id)->count());
        $this->assertDatabaseHas('activity_logs', ['action' => 'donation.alert_retried']);

        $unresolved = app(\App\Services\AlertFailureService::class)->unresolved();
        $this->assertCount(0, $unresolved);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `./vendor/bin/phpunit tests/Feature/AlertFailure/AdminAlertFailureControllerTest.php`
Expected: FAIL — route doesn't exist yet (404s).

- [ ] **Step 3: Create `AdminAlertFailureController`**

```php
<?php
// app/Http/Controllers/AdminAlertFailureController.php
namespace App\Http\Controllers;

use App\Jobs\ProcessDonationJob;
use App\Models\ActivityLog;
use App\Models\Donation;
use App\Services\AlertFailureService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminAlertFailureController extends Controller
{
    public function __construct(
        private readonly AlertFailureService $alertFailureService
    ) {}

    public function index(): View
    {
        $failures = $this->alertFailureService->unresolved()->map(function ($log) {
            $donationId = $log->payload['donation_id'] ?? null;
            return [
                'log' => $log,
                'donation' => $donationId ? Donation::with('streamer')->find($donationId) : null,
                'error' => $log->payload['error'] ?? null,
            ];
        });

        return view('admin.alert-failures', compact('failures'));
    }

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
}
```

- [ ] **Step 4: Register the routes**

In `routes/web.php`, add the import and routes inside the existing `admin`-gated group, directly after the `Activity logs` route:

```php
use App\Http\Controllers\AdminAlertFailureController;
```

```php
// Activity logs
Route::get('/logs', [AdminController::class, 'logs'])->name('logs');

// Alert failures
Route::get('/alert-failures', [AdminAlertFailureController::class, 'index'])->name('alert-failures.index');
Route::post('/alert-failures/{donation}/retry', [AdminAlertFailureController::class, 'retry'])
    ->middleware('throttle:admin-actions')
    ->name('alert-failures.retry');
```

- [ ] **Step 5: Create the view**

```blade
{{-- resources/views/admin/alert-failures.blade.php --}}
<x-app-layout>
<div class="page-container">
    <div class="page-header">
        <h1 class="page-title">Alert Gagal</h1>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <div class="table-card">
        <table>
            <thead>
                <tr>
                    <th>Donatur</th>
                    <th>Nominal</th>
                    <th>Streamer</th>
                    <th>Error</th>
                    <th>Waktu</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($failures as $failure)
                <tr>
                    @if($failure['donation'])
                        <td>{{ $failure['donation']->name }}</td>
                        <td class="amount-cell">Rp {{ number_format($failure['donation']->amount, 0, ',', '.') }}</td>
                        <td>{{ $failure['donation']->streamer->display_name ?? '—' }}</td>
                    @else
                        <td colspan="3" style="color:var(--text-3)">Donasi telah dihapus</td>
                    @endif
                    <td style="font-size:11px">{{ $failure['error'] ?? '—' }}</td>
                    <td style="font-size:11px; color:var(--text-3)">{{ $failure['log']->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($failure['donation'])
                            <form method="POST" action="{{ route('admin.alert-failures.retry', $failure['donation']) }}">
                                @csrf
                                <button type="submit" class="btn-xs">Retry</button>
                            </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="empty-cell">Tidak ada alert gagal saat ini</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-app-layout>
```

- [ ] **Step 6: Add nav link**

In `resources/views/layouts/app.blade.php`, in the `@if(auth()->user()->isAdmin())` block, directly after the "Payout" link added in the payout-settlement work:

```blade
<a href="{{ route('admin.alert-failures.index') }}" class="nav-link {{ request()->routeIs('admin.alert-failures*') ? 'active' : '' }}">
    <span class="iconify" data-icon="solar:danger-triangle-bold-duotone"></span>Alert Gagal
</a>
```

- [ ] **Step 7: Run test to verify it passes**

Run: `./vendor/bin/phpunit tests/Feature/AlertFailure/AdminAlertFailureControllerTest.php`
Expected: PASS (3 tests)

- [ ] **Step 8: Run the full suite to check for regressions**

Run: `./vendor/bin/phpunit`

- [ ] **Step 9: Commit**

```bash
git add app/Http/Controllers/AdminAlertFailureController.php routes/web.php \
        resources/views/admin/alert-failures.blade.php resources/views/layouts/app.blade.php \
        tests/Feature/AlertFailure/AdminAlertFailureControllerTest.php
git commit -m "feat: add admin alert-failures list + retry action"
```

---

### Task 3: Dashboard count card

**Files:**
- Modify: `app/Http/Controllers/AdminController.php`
- Modify: `resources/views/admin/dashboard.blade.php`
- Test: `tests/Feature/AlertFailure/DashboardAlertFailureCardTest.php`

**Interfaces:**
- Consumes: `AlertFailureService::unresolvedCount()` (Task 1).

- [ ] **Step 1: Write the failing tests**

```php
<?php
// tests/Feature/AlertFailure/DashboardAlertFailureCardTest.php
namespace Tests\Feature\AlertFailure;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAlertFailureCardTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        return $admin;
    }

    public function test_card_renders_when_there_are_unresolved_failures(): void
    {
        $admin = $this->admin();
        ActivityLog::log(action: 'donation.alert_failed', payload: ['donation_id' => 1, 'error' => 'x']);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertOk();
        // "Alert Gagal" alone also matches the nav link (present on every admin
        // page regardless of this card) — the em dash is unique to the card copy.
        $response->assertSee('1 Alert Gagal —', false);
    }

    public function test_card_is_absent_when_there_are_no_unresolved_failures(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertOk();
        $response->assertDontSee('Alert Gagal —', false);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `./vendor/bin/phpunit tests/Feature/AlertFailure/DashboardAlertFailureCardTest.php`
Expected: FAIL — `AdminController::dashboard()` doesn't pass the count, view doesn't render the card.

- [ ] **Step 3: Add the count to `AdminController::dashboard()`**

Add the import:
```php
use App\Services\AlertFailureService;
```

Inside `dashboard()`, alongside the other summary queries:
```php
$unresolvedAlertFailures = app(AlertFailureService::class)->unresolvedCount();
```

Add `'unresolvedAlertFailures'` to the `compact(...)` call in the `return view(...)` line.

- [ ] **Step 4: Add the card to the dashboard view**

In `resources/views/admin/dashboard.blade.php`, near the top of the page content (before the stat-card grid), add:

```blade
@if($unresolvedAlertFailures > 0)
<div class="alert-error" style="margin-bottom:16px">
    ⚠ {{ $unresolvedAlertFailures }} Alert Gagal —
    <a href="{{ route('admin.alert-failures.index') }}" style="text-decoration:underline">Lihat →</a>
</div>
@endif
```

- [ ] **Step 5: Run test to verify it passes**

Run: `./vendor/bin/phpunit tests/Feature/AlertFailure/DashboardAlertFailureCardTest.php`
Expected: PASS (2 tests)

- [ ] **Step 6: Run the full suite to check for regressions**

Run: `./vendor/bin/phpunit`

- [ ] **Step 7: Commit**

```bash
git add app/Http/Controllers/AdminController.php resources/views/admin/dashboard.blade.php \
        tests/Feature/AlertFailure/DashboardAlertFailureCardTest.php
git commit -m "feat: add unresolved alert-failure count card to admin dashboard"
```

---

## Post-plan note

Once merged, update `BACKLOG.md`'s "Admin dashboard — make it useful" item to remove this piece and keep the other three (trends chart, list filtering, per-streamer drill-down) as the remaining scope.
