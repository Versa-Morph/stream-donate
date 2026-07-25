# Admin: Per-Streamer Drill-Down Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Let an admin click a streamer in the dashboard leaderboard and see that streamer's stats, recent donations, and recent activity on their own page.

**Architecture:** One new read-only method on the existing `AdminController` (no new dedicated controller — this has no lifecycle/multiple actions, unlike `AdminPayoutController`/`AdminAlertFailureController`), reusing `Streamer::buildStats()`, `Streamer::unpaidOutDonations()`, and the already-existing `?streamer_id=` filter on `AdminController::donations()` rather than rebuilding any of them.

**Tech Stack:** Laravel 12, PHP 8.2+, PHPUnit 11 (existing suite conventions).

## Global Constraints

- Design source of truth: `docs/superpowers/specs/2026-07-26-admin-streamer-drilldown-design.md`.
- User-facing strings are Indonesian, matching the rest of the codebase.
- Rupiah amounts format as `number_format($x, 0, ',', '.')` (Indonesian locale) — confirmed the hard way multiple times already in this codebase's recent history.
- This is a single, cohesive read-only feature — one task, not split further (unlike the Payout work, which had a real multi-stage lifecycle to split across).

---

### Task 1: Streamer drill-down page + leaderboard link

**Files:**
- Modify: `app/Http/Controllers/AdminController.php`
- Modify: `routes/web.php`
- Create: `resources/views/admin/streamer-show.blade.php`
- Modify: `resources/views/admin/dashboard.blade.php`
- Test: `tests/Feature/AdminStreamerDrilldown/AdminStreamerShowTest.php`

**Interfaces:**
- Consumes: `Streamer::buildStats()`, `Streamer::unpaidOutDonations()`, `Streamer::donations()`, `ActivityLog` (all existing, unchanged).
- Produces: `GET /admin/streamers/{streamer}` → `admin.streamers.show` named route.

- [ ] **Step 1: Write the failing tests**

```php
<?php
// tests/Feature/AdminStreamerDrilldown/AdminStreamerShowTest.php
namespace Tests\Feature\AdminStreamerDrilldown;

use App\Models\ActivityLog;
use App\Models\Donation;
use App\Models\Streamer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStreamerShowTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        return $admin;
    }

    public function test_shows_only_the_requested_streamers_data(): void
    {
        $admin = $this->admin();

        $streamerA = Streamer::factory()->create(['display_name' => 'Streamer A']);
        Donation::factory()->for($streamerA)->create(['status' => 'paid', 'amount' => 25000, 'name' => 'Donatur A']);
        ActivityLog::log(action: 'donation.paid', description: 'Aktivitas A', streamerId: $streamerA->id);

        $streamerB = Streamer::factory()->create(['display_name' => 'Streamer B']);
        Donation::factory()->for($streamerB)->create(['status' => 'paid', 'amount' => 999999, 'name' => 'Donatur B']);
        ActivityLog::log(action: 'donation.paid', description: 'Aktivitas B', streamerId: $streamerB->id);

        $response = $this->actingAs($admin)->get("/admin/streamers/{$streamerA->id}");

        $response->assertOk();
        $response->assertSee('Streamer A');
        $response->assertSee('Donatur A');
        $response->assertSee('Aktivitas A');
        $response->assertDontSee('Donatur B');
        $response->assertDontSee('Aktivitas B');
    }

    public function test_owed_balance_reflects_unpaid_out_donations_only(): void
    {
        $admin = $this->admin();
        $streamer = Streamer::factory()->create();
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 40000]);
        $payout = \App\Models\Payout::factory()->for($streamer)->create();
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 999999, 'payout_id' => $payout->id]);

        $response = $this->actingAs($admin)->get("/admin/streamers/{$streamer->id}");

        $response->assertOk();
        $response->assertSee('40.000');
    }

    public function test_empty_state_renders_without_error(): void
    {
        $admin = $this->admin();
        $streamer = Streamer::factory()->create();

        $response = $this->actingAs($admin)->get("/admin/streamers/{$streamer->id}");

        $response->assertOk();
    }

    public function test_leaderboard_links_to_the_drilldown_page(): void
    {
        $admin = $this->admin();
        $streamer = Streamer::factory()->create();
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 10000]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertOk();
        $response->assertSee(route('admin.streamers.show', $streamer));
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `./vendor/bin/phpunit tests/Feature/AdminStreamerDrilldown/AdminStreamerShowTest.php`
Expected: FAIL — route doesn't exist yet (404s), leaderboard doesn't link anywhere.

- [ ] **Step 3: Add `AdminController::showStreamer()`**

No new imports needed — `Streamer`, `ActivityLog`, `View` are already imported in this controller (confirmed by reading its current `use` block).

```php
public function showStreamer(Streamer $streamer): View
{
    $stats = $streamer->buildStats();
    $owedBalance = $streamer->unpaidOutDonations()->sum('amount');

    $recentDonations = $streamer->donations()
        ->orderByDesc('created_at')
        ->limit(config('pagination.admin_streamer_recent_donations', 10))
        ->get();

    $recentActivity = ActivityLog::where('streamer_id', $streamer->id)
        ->orderByDesc('created_at')
        ->limit(config('pagination.admin_streamer_recent_activity', 10))
        ->get();

    return view('admin.streamer-show', compact('streamer', 'stats', 'owedBalance', 'recentDonations', 'recentActivity'));
}
```

- [ ] **Step 4: Register the route**

In `routes/web.php`, directly after the `Route::get('/logs', ...)` line inside the admin-gated group:

```php
Route::get('/streamers/{streamer}', [AdminController::class, 'showStreamer'])->name('streamers.show');
```

- [ ] **Step 5: Create the view**

```blade
{{-- resources/views/admin/streamer-show.blade.php --}}
<x-app-layout>
<div class="page-container">
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">{{ $streamer->display_name }}</h1>
            <p class="page-subtitle">
                {{ $streamer->slug }} · Bergabung {{ $streamer->created_at->format('d/m/Y') }} ·
                @if($streamer->is_accepting_donation)
                    <span style="color:var(--green)">Menerima Donasi</span>
                @else
                    <span style="color:var(--text-3)">Tidak Menerima Donasi</span>
                @endif
            </p>
        </div>
        <a href="{{ route('donate.show', $streamer->slug) }}" target="_blank" class="card-link">Lihat halaman donasi →</a>
    </div>

    <div class="stats-grid">
        <div class="stat-card c-brand">
            <div class="stat-label">Total Donasi</div>
            <div class="stat-value">Rp {{ number_format($stats['total'], 0, ',', '.') }}</div>
        </div>
        <div class="stat-card c-orange">
            <div class="stat-label">Jumlah Donasi</div>
            <div class="stat-value">{{ number_format($stats['count'], 0, ',', '.') }}</div>
        </div>
        <div class="stat-card c-green">
            <div class="stat-label">Donatur Unik</div>
            <div class="stat-value">{{ number_format($stats['donors'], 0, ',', '.') }}</div>
        </div>
        <div class="stat-card c-purple">
            <div class="stat-label">Saldo Owed</div>
            <div class="stat-value">Rp {{ number_format($owedBalance, 0, ',', '.') }}</div>
        </div>
    </div>

    <div style="margin-bottom:16px">
        <a href="{{ route('admin.donations', ['streamer_id' => $streamer->id]) }}" class="btn-xs">Semua Donasi</a>
        <a href="{{ route('admin.payouts.index') }}" class="btn-xs">Payout</a>
    </div>

    <div class="table-card" style="margin-bottom:16px">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px">
            <h2 class="section-title">Donasi Terbaru</h2>
            <a href="{{ route('admin.donations', ['streamer_id' => $streamer->id]) }}" class="card-link">Lihat semua →</a>
        </div>
        <table>
            <thead><tr><th>Donatur</th><th>Nominal</th><th>Status</th><th>Waktu</th></tr></thead>
            <tbody>
                @forelse($recentDonations as $d)
                <tr>
                    <td>{{ $d->name }}</td>
                    <td class="amount-cell">Rp {{ number_format($d->amount, 0, ',', '.') }}</td>
                    <td>{{ ucfirst($d->status) }}</td>
                    <td style="font-size:11px; color:var(--text-3)">{{ $d->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="4" class="empty-cell">Belum ada donasi</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="table-card">
        <h2 class="section-title">Aktivitas Terbaru</h2>
        <table>
            <thead><tr><th>Aksi</th><th>Deskripsi</th><th>Waktu</th></tr></thead>
            <tbody>
                @forelse($recentActivity as $log)
                <tr>
                    <td style="font-size:11px">{{ $log->action }}</td>
                    <td>{{ $log->description }}</td>
                    <td style="font-size:11px; color:var(--text-3)">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="empty-cell">Belum ada aktivitas</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-app-layout>
```

- [ ] **Step 6: Link the leaderboard row to the new page**

In `resources/views/admin/dashboard.blade.php`, the leaderboard row (around line 246, inside the `@forelse($streamerStats as $i => $s)` loop):

```blade
<td>
    <a href="{{ route('admin.streamers.show', $s) }}" style="text-decoration:none">
        <div style="font-size:13px; font-weight:600; color:var(--text)">{{ $s->display_name }}</div>
        <div style="font-size:11px; color:var(--text-3)">{{ $s->slug }}</div>
    </a>
</td>
```

(Replaces the existing non-linked `<div>`/`<div>` pair in that `<td>` — the impersonate `<form>`/button in the row's last `<td>` is untouched.)

- [ ] **Step 7: Run test to verify it passes**

Run: `./vendor/bin/phpunit tests/Feature/AdminStreamerDrilldown/AdminStreamerShowTest.php`
Expected: PASS (4 tests)

- [ ] **Step 8: Run the full suite to check for regressions**

Run: `./vendor/bin/phpunit`

- [ ] **Step 9: Commit**

```bash
git add app/Http/Controllers/AdminController.php routes/web.php \
        resources/views/admin/streamer-show.blade.php resources/views/admin/dashboard.blade.php \
        tests/Feature/AdminStreamerDrilldown/AdminStreamerShowTest.php
git commit -m "feat: add admin per-streamer drill-down page, link from leaderboard"
```

---

## Post-plan note

Once merged, update `BACKLOG.md`'s "Admin dashboard — make it useful" item to remove this piece, leaving list filtering as the item's sole remaining scope.
