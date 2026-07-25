# Admin: Donations & Logs Date-Range/Streamer Filtering Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add date-range filtering to the admin donations and activity-log pages, plus a streamer filter to the logs page (donations already has one) — the final piece of `BACKLOG.md`'s original "admin dashboard" item.

**Architecture:** Extend the two existing paginated `AdminController` methods (`donations()`, `logs()`) with `whereDate` clauses following `ReportController`'s existing `from`/`to` pattern (unbounded by default, not defaulted to "this month"), and copy `donations()`'s existing streamer dropdown onto the logs page.

**Tech Stack:** Laravel 12, PHP 8.2+, PHPUnit 11 (existing suite conventions).

## Global Constraints

- Design source of truth: `docs/superpowers/specs/2026-07-26-admin-list-filtering-design.md`.
- `from`/`to` are unbounded by default (no filter applied unless explicitly set) — different from `ReportController`'s own `from`/`to`, which default to "this month." Don't copy that default here.
- Filters must persist across pagination — both queries already call `->withQueryString()`, don't remove it.
- This is one small, cohesive task — no lifecycle to split across, unlike the Payout work.

---

### Task 1: Date-range filter on both pages, streamer filter on logs

**Files:**
- Modify: `app/Http/Controllers/AdminController.php`
- Modify: `resources/views/admin/donations.blade.php`
- Modify: `resources/views/admin/logs.blade.php`
- Test: `tests/Feature/AdminListFiltering/AdminListFilteringTest.php`

**Interfaces:**
- No new methods — extends existing `AdminController::donations()`/`logs()` behavior. `logs()`'s view data gains a `streamers` key (`donations()` already has this).

- [ ] **Step 1: Write the failing tests**

```php
<?php
// tests/Feature/AdminListFiltering/AdminListFilteringTest.php
namespace Tests\Feature\AdminListFiltering;

use App\Models\ActivityLog;
use App\Models\Donation;
use App\Models\Streamer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminListFilteringTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        return $admin;
    }

    public function test_donations_date_range_narrows_results(): void
    {
        $admin = $this->admin();
        $streamer = Streamer::factory()->create();
        Donation::factory()->for($streamer)->create(['name' => 'Dalam Rentang', 'created_at' => '2026-07-15']);
        Donation::factory()->for($streamer)->create(['name' => 'Di Luar Rentang', 'created_at' => '2026-06-01']);

        $response = $this->actingAs($admin)->get('/admin/donations?from=2026-07-01&to=2026-07-31');

        $response->assertOk();
        $response->assertSee('Dalam Rentang');
        $response->assertDontSee('Di Luar Rentang');
    }

    public function test_donations_with_no_date_filter_returns_everything(): void
    {
        $admin = $this->admin();
        $streamer = Streamer::factory()->create();
        Donation::factory()->for($streamer)->create(['name' => 'Donasi Lama', 'created_at' => '2020-01-01']);

        $response = $this->actingAs($admin)->get('/admin/donations');

        $response->assertOk();
        $response->assertSee('Donasi Lama');
    }

    public function test_logs_date_range_and_streamer_filter_combine_with_action_filter(): void
    {
        $admin = $this->admin();
        $streamerA = Streamer::factory()->create();
        $streamerB = Streamer::factory()->create();

        ActivityLog::log(action: 'donation.paid', description: 'Match', streamerId: $streamerA->id);
        ActivityLog::query()->where('description', 'Match')->update(['created_at' => '2026-07-15']);

        ActivityLog::log(action: 'donation.paid', description: 'Streamer Salah', streamerId: $streamerB->id);
        ActivityLog::query()->where('description', 'Streamer Salah')->update(['created_at' => '2026-07-15']);

        ActivityLog::log(action: 'donation.paid', description: 'Tanggal Salah', streamerId: $streamerA->id);
        ActivityLog::query()->where('description', 'Tanggal Salah')->update(['created_at' => '2026-06-01']);

        $response = $this->actingAs($admin)->get(
            "/admin/logs?action=donation&from=2026-07-01&to=2026-07-31&streamer_id={$streamerA->id}"
        );

        $response->assertOk();
        $response->assertSee('Match');
        $response->assertDontSee('Streamer Salah');
        $response->assertDontSee('Tanggal Salah');
    }

    public function test_logs_page_shows_streamer_dropdown(): void
    {
        $admin = $this->admin();
        $streamer = Streamer::factory()->create(['display_name' => 'Streamer Pilihan']);

        $response = $this->actingAs($admin)->get('/admin/logs');

        $response->assertOk();
        $response->assertSee('Streamer Pilihan');
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `./vendor/bin/phpunit tests/Feature/AdminListFiltering/AdminListFilteringTest.php`
Expected: FAIL — `test_donations_date_range_narrows_results` and the two `logs()`-related filter tests fail (no `from`/`to`/`streamer_id` filtering on logs yet, no streamer dropdown on the logs page). `test_donations_with_no_date_filter_returns_everything` passes trivially (already true today) — that's fine, it's a regression guard for the next step, not meant to prove new behavior.

- [ ] **Step 3: Add the filters to `AdminController`**

In `donations()`, directly after the existing `streamer_id` filter block:

```php
if ($from = $request->input('from')) {
    $query->whereDate('created_at', '>=', $from);
}
if ($to = $request->input('to')) {
    $query->whereDate('created_at', '<=', $to);
}
```

In `logs()`, replace the method body with:

```php
public function logs(Request $request): View
{
    $query = ActivityLog::with(['user', 'streamer'])->orderBy('created_at', 'desc');

    if ($action = $request->input('action')) {
        // Escape LIKE wildcards to prevent slow query attacks
        $escapedAction = $this->escapeLikeWildcards($action);
        $query->where('action', 'like', "%{$escapedAction}%");
    }

    if ($streamerId = $request->input('streamer_id')) {
        $query->where('streamer_id', $streamerId);
    }

    if ($from = $request->input('from')) {
        $query->whereDate('created_at', '>=', $from);
    }
    if ($to = $request->input('to')) {
        $query->whereDate('created_at', '<=', $to);
    }

    $logs = $query->paginate(config('pagination.admin_logs', 30))->withQueryString();
    $streamers = Streamer::orderBy('display_name')->get(['id', 'display_name', 'slug']);

    return view('admin.logs', compact('logs', 'streamers'));
}
```

- [ ] **Step 4: Add date inputs to `admin/donations.blade.php`**

In the existing filter form (around line 52-69), directly after the `streamer_id` `<select>` and before the "Filter" button:

```blade
<input type="date" name="from" value="{{ request('from') }}" style="max-width:150px">
<input type="date" name="to" value="{{ request('to') }}" style="max-width:150px">
```

Update the reset-link condition to also account for the new fields:
```blade
@if(request('search') || request('streamer_id') || request('from') || request('to'))
```

- [ ] **Step 5: Add the streamer dropdown + date inputs to `admin/logs.blade.php`**

In the existing filter form (around line 46-55), directly after the `action` text input and before the "Filter" button — the streamer `<select>` copied verbatim from `donations.blade.php`'s existing one:

```blade
<select name="streamer_id" style="max-width:220px">
    <option value="">Semua Streamer</option>
    @foreach($streamers as $s)
    <option value="{{ $s->id }}" {{ request('streamer_id') == $s->id ? 'selected' : '' }}>
        {{ $s->display_name }}
    </option>
    @endforeach
</select>
<input type="date" name="from" value="{{ request('from') }}" style="max-width:150px">
<input type="date" name="to" value="{{ request('to') }}" style="max-width:150px">
```

Update the reset-link condition:
```blade
@if(request('action') || request('streamer_id') || request('from') || request('to'))
```

- [ ] **Step 6: Run test to verify it passes**

Run: `./vendor/bin/phpunit tests/Feature/AdminListFiltering/AdminListFilteringTest.php`
Expected: PASS (4 tests)

- [ ] **Step 7: Run the full suite to check for regressions**

Run: `./vendor/bin/phpunit`

- [ ] **Step 8: Commit**

```bash
git add app/Http/Controllers/AdminController.php resources/views/admin/donations.blade.php \
        resources/views/admin/logs.blade.php tests/Feature/AdminListFiltering/AdminListFilteringTest.php
git commit -m "feat: add date-range filter to admin donations/logs, streamer filter to logs"
```

---

## Post-plan note

Once merged, `BACKLOG.md`'s "Admin dashboard — make it useful" item is fully shipped — delete it entirely (all four pieces: alert failures, trends chart, streamer drill-down, list filtering are done), per that file's own "delete once implemented" rule.
