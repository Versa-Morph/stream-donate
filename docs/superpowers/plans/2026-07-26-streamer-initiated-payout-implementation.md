# Streamer-Initiated Payout Request Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** let a streamer trigger creation of their own payout, while keeping admin's existing manual-creation path working identically via shared logic.

**Architecture:** extract `AdminPayoutController::create()`'s transaction body into `PayoutCreationService::createFor()`; both admin's controller and a new streamer endpoint call it. New `POST /streamer/payouts/request` route, always scoped to `auth()->user()->streamer` — never accepts a streamer ID.

**Tech Stack:** Laravel 12, PHPUnit, existing `PayoutGatewayInterface`/`ManualPayoutGateway`/fake gateway pattern.

## Global Constraints

- Streamer endpoint must never accept a streamer ID param — always resolves the authenticated user's own streamer.
- Admin's existing `create()` behavior must be provably unchanged: `PayoutCreationDisbursementTest` (existing, admin-flow) must pass without modification after the extraction.
- New rate limiter for the streamer endpoint, matching this app's "every mutating route gets a named limiter" convention (see `settings-update` for the closest precedent: `Limit::perMinute(10)->by($request->user()?->id ?? $request->ip())`).

---

### Task 1: Extract `PayoutCreationService`, refactor `AdminPayoutController::create()`

**Files:**
- Create: `app/Services/Payout/PayoutCreationService.php`
- Modify: `app/Http/Controllers/AdminPayoutController.php`
- Test: `tests/Feature/PayoutDisbursement/PayoutCreationDisbursementTest.php` (existing — must pass unchanged, proves the refactor preserved behavior)

**Interfaces:**
- Consumes: `PayoutGatewayInterface` (existing).
- Produces: `PayoutCreationService::createFor(Streamer $streamer, ?int $createdByUserId): Payout` — throws `\InvalidArgumentException` on ineligibility (below minimum, missing bank info, failed bank validation). Both `AdminPayoutController` (Task 1) and `StreamerDashboardController::requestPayout()` (Task 2) call this.

- [ ] **Step 1: Run the existing admin-flow test to confirm it's currently green (baseline before touching anything)**

Run: `./vendor/bin/phpunit tests/Feature/PayoutDisbursement/PayoutCreationDisbursementTest.php`
Expected: PASS (6 tests) — this is the safety net for the extraction; it must stay green after every step below.

- [ ] **Step 2: Create `PayoutCreationService` with the exact transaction body currently in `AdminPayoutController::create()`**

```php
<?php
// app/Services/Payout/PayoutCreationService.php
namespace App\Services\Payout;

use App\Models\Payout;
use App\Models\Streamer;
use Illuminate\Support\Facades\DB;

class PayoutCreationService
{
    public function __construct(
        private readonly PayoutGatewayInterface $payoutGateway
    ) {}

    /**
     * @throws \InvalidArgumentException if the streamer isn't eligible right now
     */
    public function createFor(Streamer $streamer, ?int $createdByUserId): Payout
    {
        return DB::transaction(function () use ($streamer, $createdByUserId) {
            $donations = $streamer->unpaidOutDonations()->lockForUpdate()->get();
            $gross = (int) $donations->sum('amount');

            if ($gross < config('payout.minimum_amount', 50000)) {
                throw new \InvalidArgumentException(
                    'Saldo belum mencapai minimum payout (Rp ' . number_format(config('payout.minimum_amount', 50000), 0, ',', '.') . ').'
                );
            }

            if (!$streamer->bank_account_number) {
                throw new \InvalidArgumentException('Streamer belum melengkapi info rekening bank.');
            }

            $transientPayout = new Payout([
                'bank_name' => $streamer->bank_name,
                'bank_account_number' => $streamer->bank_account_number,
                'bank_account_holder' => $streamer->bank_account_holder,
            ]);

            if (!$this->payoutGateway->validateBankAccount($transientPayout)) {
                throw new \InvalidArgumentException('Info rekening bank streamer tidak valid (gagal validasi Midtrans).');
            }

            $feePercent = config('payout.platform_fee_percent', 10);
            $fee = (int) round($gross * $feePercent / 100);
            $net = $gross - $fee;

            $payout = Payout::create([
                'streamer_id' => $streamer->id,
                'gross_amount' => $gross,
                'platform_fee_amount' => $fee,
                'net_amount' => $net,
                'status' => 'pending',
                'bank_name' => $streamer->bank_name,
                'bank_account_number' => $streamer->bank_account_number,
                'bank_account_holder' => $streamer->bank_account_holder,
                'created_by' => $createdByUserId,
            ]);

            $donations->each(fn ($d) => $d->update(['payout_id' => $payout->id]));

            $disbursement = $this->payoutGateway->disburse($payout);

            if ($disbursement->status === 'processing') {
                $payout->update(['status' => 'processing', 'reference' => $disbursement->reference]);
            } elseif ($disbursement->status === 'failed') {
                $donations->each(fn ($d) => $d->update(['payout_id' => null]));
                $payout->update(['status' => 'failed']);
            }
            // status === 'pending' (manual gateway): no change, payout stays pending as created.

            return $payout;
        });
    }
}
```

This is a direct extraction — same logic, same order of checks, only `Auth::id()` becomes the passed-in `$createdByUserId` parameter (since the service itself doesn't know who's calling: admin or streamer).

- [ ] **Step 3: Refactor `AdminPayoutController::create()` to delegate to the service**

Replace the `use` statements' `PayoutGatewayInterface` import and constructor with `PayoutCreationService`, and replace the transaction body with a call to the service:

```php
use App\Services\Payout\PayoutCreationService;
```

```php
public function __construct(
    private readonly PayoutCreationService $payoutCreationService
) {}
```

```php
public function create(Streamer $streamer): RedirectResponse
{
    try {
        $payout = $this->payoutCreationService->createFor($streamer, Auth::id());
    } catch (\InvalidArgumentException $e) {
        return back()->withErrors(['payout' => $e->getMessage()]);
    }

    ActivityLog::log(
        action: 'payout.created',
        description: "Payout Rp " . number_format($payout->net_amount, 0, ',', '.') . " dibuat untuk {$streamer->display_name}",
        userId: Auth::id(),
        streamerId: $streamer->id,
        payload: ['payout_id' => $payout->id],
    );

    return back()->with('success', 'Payout berhasil dibuat.');
}
```

`void()` still uses `DB::transaction`, so keep the `Illuminate\Support\Facades\DB` import — only remove the now-unused `PayoutGatewayInterface` import/constructor property, since nothing else in this controller uses it.

- [ ] **Step 4: Run the existing admin-flow test to verify the extraction preserved behavior**

Run: `./vendor/bin/phpunit tests/Feature/PayoutDisbursement/PayoutCreationDisbursementTest.php`
Expected: PASS (6 tests) — unchanged from Step 1's baseline. If anything fails here, the extraction introduced a behavior difference — fix it before proceeding, don't move to Task 2.

- [ ] **Step 5: Run the full suite to check for regressions elsewhere**

Run: `./vendor/bin/phpunit`

- [ ] **Step 6: Commit**

```bash
git add app/Services/Payout/PayoutCreationService.php app/Http/Controllers/AdminPayoutController.php
git commit -m "refactor: extract PayoutCreationService from AdminPayoutController::create()"
```

---

### Task 2: Streamer-facing `requestPayout()` endpoint

**Files:**
- Modify: `app/Http/Controllers/StreamerDashboardController.php`
- Modify: `routes/web.php`
- Modify: `app/Providers/AppServiceProvider.php`
- Test: `tests/Feature/Payout/StreamerPayoutRequestTest.php`

**Interfaces:**
- Consumes: `PayoutCreationService::createFor()` (Task 1).

- [ ] **Step 1: Write the failing tests**

```php
<?php
// tests/Feature/Payout/StreamerPayoutRequestTest.php
namespace Tests\Feature\Payout;

use App\Models\Donation;
use App\Models\Payout;
use App\Models\Streamer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StreamerPayoutRequestTest extends TestCase
{
    use RefreshDatabase;

    private function streamerUser(array $streamerAttrs = []): array
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'streamer'])->save();
        $streamer = Streamer::factory()->create(array_merge([
            'user_id' => $user->id,
            'bank_name' => 'bca',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'Budi Santoso',
        ], $streamerAttrs));

        return [$user, $streamer];
    }

    public function test_eligible_streamer_can_request_own_payout(): void
    {
        [$user, $streamer] = $this->streamerUser();
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 100000]);

        $response = $this->actingAs($user)->post('/streamer/payouts/request');

        $response->assertSessionHasNoErrors();
        $payout = Payout::firstOrFail();
        $this->assertSame($streamer->id, $payout->streamer_id);
        $this->assertSame($user->id, $payout->created_by);
    }

    public function test_below_minimum_is_rejected(): void
    {
        [$user, $streamer] = $this->streamerUser();
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 1000]);

        $response = $this->actingAs($user)->post('/streamer/payouts/request');

        $response->assertSessionHasErrors();
        $this->assertSame(0, Payout::count());
    }

    public function test_missing_bank_info_is_rejected(): void
    {
        [$user, $streamer] = $this->streamerUser(['bank_name' => null, 'bank_account_number' => null, 'bank_account_holder' => null]);
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 100000]);

        $response = $this->actingAs($user)->post('/streamer/payouts/request');

        $response->assertSessionHasErrors();
        $this->assertSame(0, Payout::count());
    }

    public function test_streamer_cannot_affect_another_streamers_payout(): void
    {
        [$requester] = $this->streamerUser();
        [, $otherStreamer] = $this->streamerUser();
        Donation::factory()->for($otherStreamer)->create(['status' => 'paid', 'amount' => 100000]);

        // The route has no streamer-id parameter to tamper with — the request
        // always resolves auth()->user()->streamer. Since $requester's own
        // streamer has no paid donations, their request must fail, proving it
        // was evaluated against their own streamer, not $otherStreamer's.
        $response = $this->actingAs($requester)->post('/streamer/payouts/request');

        $response->assertSessionHasErrors();
        $this->assertSame(0, Payout::count());
    }
}
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `./vendor/bin/phpunit tests/Feature/Payout/StreamerPayoutRequestTest.php`
Expected: FAIL — route doesn't exist yet (404).

- [ ] **Step 3: Add the rate limiter**

In `app/Providers/AppServiceProvider.php`, add alongside the other `RateLimiter::for(...)` calls (near `settings-update`):

```php
// Rate-limit streamer self-service payout requests
// Max 10 per minute per user — matches settings-update's shape (an
// authenticated user mutating their own data)
RateLimiter::for('payout-request', function (Request $request) {
    return Limit::perMinute(10)->by($request->user()?->id ?? $request->ip());
});
```

- [ ] **Step 4: Add the controller method**

In `app/Http/Controllers/StreamerDashboardController.php`, add the import:

```php
use App\Services\Payout\PayoutCreationService;
```

Add to the constructor (create one if it doesn't exist yet — check first: this controller currently has no `__construct()`, so add one):

```php
public function __construct(
    private readonly PayoutCreationService $payoutCreationService
) {}
```

Add the method near `payouts()`:

```php
/**
 * Streamer meminta pencairan payout untuk saldo owed mereka sendiri.
 */
public function requestPayout(): RedirectResponse
{
    $user = auth()->user();

    if (!$user->streamer) {
        return redirect()->route('streamer.setup');
    }

    try {
        $payout = $this->payoutCreationService->createFor($user->streamer, $user->id);
    } catch (\InvalidArgumentException $e) {
        return back()->withErrors(['payout' => $e->getMessage()]);
    }

    ActivityLog::log(
        action: 'payout.created',
        description: "Payout Rp " . number_format($payout->net_amount, 0, ',', '.') . " diminta oleh {$user->streamer->display_name}",
        userId: $user->id,
        streamerId: $user->streamer->id,
        payload: ['payout_id' => $payout->id],
    );

    return back()->with('success', 'Permintaan payout berhasil dibuat.');
}
```

- [ ] **Step 5: Add the route**

In `routes/web.php`, inside the existing `streamer` middleware group (same group as the `/payouts` GET route), add directly after it:

```php
Route::post('/payouts/request', [StreamerDashboardController::class, 'requestPayout'])
    ->middleware('throttle:payout-request')
    ->name('payouts.request');
```

- [ ] **Step 6: Run tests to verify they pass**

Run: `./vendor/bin/phpunit tests/Feature/Payout/StreamerPayoutRequestTest.php`
Expected: PASS (4 tests)

- [ ] **Step 7: Run the full suite to check for regressions**

Run: `./vendor/bin/phpunit`

- [ ] **Step 8: Commit**

```bash
git add app/Http/Controllers/StreamerDashboardController.php routes/web.php app/Providers/AppServiceProvider.php \
        tests/Feature/Payout/StreamerPayoutRequestTest.php
git commit -m "feat: add streamer self-service payout request endpoint"
```

---

### Task 3: UI — request button + admin audit trail

**Files:**
- Modify: `resources/views/streamer/payouts.blade.php`
- Modify: `resources/views/admin/payout-show.blade.php`
- Test: `tests/Feature/Payout/StreamerPayoutRequestTest.php` (extend with a UI-visibility assertion)

**Interfaces:**
- Consumes: `$streamer` (already passed to `streamer.payouts` view by `StreamerDashboardController::payouts()` — confirmed unused today, this task starts using it), `route('streamer.payouts.request')` (Task 2), `$payout->createdBy` (existing relation, already eager-loaded by `AdminPayoutController::show()` but not yet rendered).

- [ ] **Step 1: Write the failing test — button visibility**

Add to `tests/Feature/Payout/StreamerPayoutRequestTest.php`:

```php
public function test_request_button_disabled_when_ineligible(): void
{
    [$user, $streamer] = $this->streamerUser(['bank_name' => null, 'bank_account_number' => null, 'bank_account_holder' => null]);
    Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 100000]);

    $response = $this->actingAs($user)->get('/streamer/payouts');

    $response->assertSee('disabled', false);
}

public function test_request_button_enabled_when_eligible(): void
{
    [$user, $streamer] = $this->streamerUser();
    Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 100000]);

    $response = $this->actingAs($user)->get('/streamer/payouts');

    $response->assertDontSee('disabled', false);
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `./vendor/bin/phpunit tests/Feature/Payout/StreamerPayoutRequestTest.php`
Expected: FAIL — `test_request_button_disabled_when_ineligible` fails (no button/disabled attribute exists yet in the view).

- [ ] **Step 3: Add the request button to `streamer/payouts.blade.php`**

Replace the page header with one showing owed balance and the request button, matching the disabled+tooltip pattern already used on `admin/payouts.blade.php`:

```blade
<x-app-layout>
<div class="page-container">
    @php
        $ownedAmount = $streamer->unpaidOutDonations()->sum('amount');
        $hasBankInfo = (bool) $streamer->bank_account_number;
        $meetsMinimum = $ownedAmount >= config('payout.minimum_amount');
        $blockedReasons = [];
        if (!$hasBankInfo) {
            $blockedReasons[] = 'Info bank belum diisi (lengkapi di Settings)';
        }
        if (!$meetsMinimum) {
            $blockedReasons[] = 'Saldo belum mencapai minimum Rp ' . number_format(config('payout.minimum_amount'), 0, ',', '.');
        }
    @endphp
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">Riwayat Payout</h1>
            <p class="page-subtitle">{{ $payouts->count() }} payout tercatat · Saldo owed: Rp {{ number_format($ownedAmount, 0, ',', '.') }}</p>
        </div>
        <form method="POST" action="{{ route('streamer.payouts.request') }}"
            onsubmit="return confirm('Ajukan payout Rp {{ number_format($ownedAmount, 0, ',', '.') }}?')">
            @csrf
            <button type="submit" class="btn-xs" @if($blockedReasons) disabled title="{{ implode(' · ', $blockedReasons) }}" @endif>Request Payout</button>
        </form>
    </div>
```

(The rest of the file — the table — stays exactly as it already is from the earlier styling pass.)

- [ ] **Step 4: Run test to verify it passes**

Run: `./vendor/bin/phpunit tests/Feature/Payout/StreamerPayoutRequestTest.php`
Expected: PASS (all tests in this file, including the two new ones)

- [ ] **Step 5: Add "Dibuat oleh" to the admin payout detail page**

In `resources/views/admin/payout-show.blade.php`, in the `page-subtitle` block added during the earlier styling pass, add the creator name:

```blade
<p class="page-subtitle">
    <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
    @if($payout->reference)
        · Referensi: {{ $payout->reference }}
    @endif
    · Dibuat oleh: {{ $payout->createdBy->name ?? '—' }}
</p>
```

`$payout->createdBy` is already eager-loaded by `AdminPayoutController::show()`'s existing `$payout->load('streamer', 'donations', 'createdBy')` — no controller change needed. This works identically whether the creator was an admin or a streamer self-requesting — `createdBy` is always just a `User`.

- [ ] **Step 6: Run the full suite to check for regressions**

Run: `./vendor/bin/phpunit`

- [ ] **Step 7: Commit**

```bash
git add resources/views/streamer/payouts.blade.php resources/views/admin/payout-show.blade.php \
        tests/Feature/Payout/StreamerPayoutRequestTest.php
git commit -m "feat: add streamer payout request button and admin creator audit trail"
```

---

## Post-plan note

Once merged, no `BACKLOG.md` entry needed — this isn't a deferred/gated item, it's a complete replacement of the trigger-authorization model, live immediately.
