# Automated Payout Disbursement (Midtrans Iris) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build the full architecture for automated Midtrans Iris payout disbursement — feature-flagged, default off — reusing the existing manual payout flow as one gateway implementation among two.

**Architecture:** `PayoutGatewayInterface` with `ManualPayoutGateway` (today's behavior) and `MidtransIrisGateway` (new, behind `config('payout.automated_disbursement_enabled')`) implementations; a bank-code dropdown replacing free-text bank names; new `processing`/`failed` `Payout` statuses with a polling job to resolve them.

**Tech Stack:** Laravel 12, PHP 8.2+, PHPUnit 11 (existing suite conventions).

## Global Constraints

- Design source of truth: `docs/superpowers/specs/2026-07-26-automated-payout-disbursement-design.md`.
- **`MidtransIrisGateway`'s three method bodies (`validateBankAccount`, `disburse`, `checkStatus`) are a deliberate, flagged exception to "no placeholders"** — the exact Iris JSON payload shape couldn't be confirmed through available docs this session (see the spec's "Important limitation" section, and Task 5 below). Every other task in this plan is real, complete, tested code.
- `config('payout.automated_disbursement_enabled', false)` — default off, the only safe state pre-KYC.
- User-facing strings are Indonesian, matching the rest of the codebase.
- No schema migration needed for `processing`/`failed` statuses — `Payout.status` is already a plain `string(20)` column.

---

### Task 1: `PayoutGatewayInterface`, DTOs, `ManualPayoutGateway`, fake, binding

**Files:**
- Create: `app/Services/Payout/PayoutGatewayInterface.php`
- Create: `app/Services/Payout/PayoutDisbursementResult.php`
- Create: `app/Services/Payout/PayoutStatusResult.php`
- Create: `app/Services/Payout/ManualPayoutGateway.php`
- Create: `tests/Support/FakePayoutGateway.php`
- Modify: `config/payout.php`
- Modify: `.env.example`
- Modify: `app/Providers/AppServiceProvider.php`
- Modify: `tests/TestCase.php`
- Test: `tests/Feature/PayoutDisbursement/ManualPayoutGatewayTest.php`

**Interfaces:**
- Produces: `PayoutGatewayInterface::validateBankAccount(Payout $payout): bool`, `::disburse(Payout $payout): PayoutDisbursementResult`, `::checkStatus(Payout $payout): PayoutStatusResult`. `PayoutDisbursementResult` has `status` (`'pending'`|`'processing'`|`'failed'`), `?reference`, `?errorMessage`. `PayoutStatusResult` has `status` (`'processing'`|`'paid'`|`'failed'`).

- [ ] **Step 1: Write the failing test**

```php
<?php
// tests/Feature/PayoutDisbursement/ManualPayoutGatewayTest.php
namespace Tests\Feature\PayoutDisbursement;

use App\Models\Payout;
use App\Services\Payout\ManualPayoutGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManualPayoutGatewayTest extends TestCase
{
    use RefreshDatabase;

    public function test_manual_gateway_validates_any_account_and_never_disburses(): void
    {
        $payout = Payout::factory()->create();
        $gateway = new ManualPayoutGateway();

        $this->assertTrue($gateway->validateBankAccount($payout));

        $result = $gateway->disburse($payout);
        $this->assertSame('pending', $result->status);
        $this->assertNull($result->reference);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `./vendor/bin/phpunit tests/Feature/PayoutDisbursement/ManualPayoutGatewayTest.php`
Expected: FAIL — none of these classes exist yet.

- [ ] **Step 3: Create the DTOs and interface**

```php
<?php
// app/Services/Payout/PayoutDisbursementResult.php
namespace App\Services\Payout;

final class PayoutDisbursementResult
{
    public function __construct(
        public readonly string $status, // 'pending' (manual, no-op) | 'processing' | 'failed'
        public readonly ?string $reference = null,
        public readonly ?string $errorMessage = null,
    ) {}
}
```

```php
<?php
// app/Services/Payout/PayoutStatusResult.php
namespace App\Services\Payout;

final class PayoutStatusResult
{
    public function __construct(
        public readonly string $status, // 'processing' | 'paid' | 'failed'
    ) {}
}
```

```php
<?php
// app/Services/Payout/PayoutGatewayInterface.php
namespace App\Services\Payout;

use App\Models\Payout;

interface PayoutGatewayInterface
{
    public function validateBankAccount(Payout $payout): bool;

    public function disburse(Payout $payout): PayoutDisbursementResult;

    public function checkStatus(Payout $payout): PayoutStatusResult;
}
```

- [ ] **Step 4: Create `ManualPayoutGateway`**

```php
<?php
// app/Services/Payout/ManualPayoutGateway.php
namespace App\Services\Payout;

use App\Models\Payout;

class ManualPayoutGateway implements PayoutGatewayInterface
{
    public function validateBankAccount(Payout $payout): bool
    {
        // Manual mode has no external validation to perform — the existing
        // "has bank_account_number" check in AdminPayoutController::create()
        // is all the validation manual payouts ever had.
        return true;
    }

    public function disburse(Payout $payout): PayoutDisbursementResult
    {
        // No-op: manual mode leaves the payout `pending` for an admin to
        // record the bank transfer and mark it paid by hand.
        return new PayoutDisbursementResult(status: 'pending');
    }

    public function checkStatus(Payout $payout): PayoutStatusResult
    {
        return new PayoutStatusResult(status: $payout->status);
    }
}
```

- [ ] **Step 5: Add config and env vars**

```php
// config/payout.php — add these two keys alongside the existing ones
'automated_disbursement_enabled' => env('PAYOUT_AUTOMATED_DISBURSEMENT_ENABLED', false),
'iris_api_key' => env('MIDTRANS_IRIS_API_KEY'),
```

Append to `.env.example`:
```
PAYOUT_AUTOMATED_DISBURSEMENT_ENABLED=false
MIDTRANS_IRIS_API_KEY=
```

- [ ] **Step 6: Bind the gateway based on the flag**

In `app/Providers/AppServiceProvider.php`, inside `register()`:

```php
$this->app->bind(\App\Services\Payout\PayoutGatewayInterface::class, function () {
    return config('payout.automated_disbursement_enabled')
        ? new \App\Services\Payout\MidtransIrisGateway()
        : new \App\Services\Payout\ManualPayoutGateway();
});
```

(`MidtransIrisGateway` is created in Task 5 — this binding is written now so Task 5 only needs to add the class, not touch this provider again.)

- [ ] **Step 7: Create `FakePayoutGateway` and bind it in `TestCase`**

```php
<?php
// tests/Support/FakePayoutGateway.php
namespace Tests\Support;

use App\Models\Payout;
use App\Services\Payout\PayoutDisbursementResult;
use App\Services\Payout\PayoutGatewayInterface;
use App\Services\Payout\PayoutStatusResult;

class FakePayoutGateway implements PayoutGatewayInterface
{
    public bool $bankAccountValid = true;
    public string $disburseStatus = 'processing'; // 'processing' | 'failed'
    public string $checkStatusResult = 'paid'; // 'paid' | 'failed' | 'processing'

    public function validateBankAccount(Payout $payout): bool
    {
        return $this->bankAccountValid;
    }

    public function disburse(Payout $payout): PayoutDisbursementResult
    {
        return new PayoutDisbursementResult(
            status: $this->disburseStatus,
            reference: $this->disburseStatus === 'processing' ? 'FAKE-IRIS-REF-' . $payout->id : null,
            errorMessage: $this->disburseStatus === 'failed' ? 'Simulated disbursement failure' : null,
        );
    }

    public function checkStatus(Payout $payout): PayoutStatusResult
    {
        return new PayoutStatusResult(status: $this->checkStatusResult);
    }
}
```

**No `TestCase.php` change for this interface** — unlike `PaymentGatewayInterface` (every donation always needs *some* payment gateway, so `FakePaymentGateway` is bound globally), `PayoutGatewayInterface`'s `ManualPayoutGateway` is itself a real, deterministic, no-external-calls implementation that's completely safe to exercise for real in tests. Leave `AppServiceProvider`'s conditional binding (Task 1 Step 6) as the only binding — tests that want the flag-off (manual) path get the real `ManualPayoutGateway` automatically; tests that want the flag-on (automated) path explicitly rebind to `FakePayoutGateway` for just that test method (Task 3's tests do this), the same per-test-override pattern already used by `test_invalid_signature_is_rejected_with_403` in the payment gateway work.

- [ ] **Step 8: Run test to verify it passes**

Run: `./vendor/bin/phpunit tests/Feature/PayoutDisbursement/ManualPayoutGatewayTest.php`
Expected: PASS

- [ ] **Step 9: Run the full suite to check for regressions**

Run: `./vendor/bin/phpunit`

- [ ] **Step 10: Commit**

```bash
git add app/Services/Payout/ tests/Support/FakePayoutGateway.php config/payout.php .env.example \
        app/Providers/AppServiceProvider.php tests/TestCase.php \
        tests/Feature/PayoutDisbursement/ManualPayoutGatewayTest.php
git commit -m "feat: add PayoutGatewayInterface, ManualPayoutGateway, feature flag"
```

---

### Task 2: Bank code dropdown

**Files:**
- Create: `config/banks.php`
- Create: `database/migrations/2026_07_26_140000_normalize_streamer_bank_names_to_codes.php`
- Modify: `app/Models/Streamer.php`
- Modify: `app/Http/Controllers/StreamerDashboardController.php`
- Modify: `resources/views/streamer/settings.blade.php`
- Test: `tests/Feature/PayoutDisbursement/BankCodeSettingsTest.php`

**Interfaces:**
- Produces: `Streamer::bankDisplayName(): string`, `config('banks')` — an associative array `['bca' => 'Bank Central Asia (BCA)', ...]`.

- [ ] **Step 1: Write the failing tests**

```php
<?php
// tests/Feature/PayoutDisbursement/BankCodeSettingsTest.php
namespace Tests\Feature\PayoutDisbursement;

use App\Models\Streamer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BankCodeSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_saving_a_valid_bank_code_succeeds(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'streamer'])->save();
        $streamer = Streamer::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post('/streamer/settings', [
            'display_name' => $streamer->display_name,
            'min_donation' => $streamer->min_donation,
            'thank_you_message' => $streamer->thank_you_message,
            'bank_name' => 'bca',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'Budi Santoso',
        ]);

        $response->assertSessionHasNoErrors();
        $streamer->refresh();
        $this->assertSame('bca', $streamer->bank_name);
    }

    public function test_saving_an_unknown_bank_code_is_rejected(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'streamer'])->save();
        $streamer = Streamer::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post('/streamer/settings', [
            'display_name' => $streamer->display_name,
            'min_donation' => $streamer->min_donation,
            'thank_you_message' => $streamer->thank_you_message,
            'bank_name' => 'not-a-real-bank',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'Budi Santoso',
        ]);

        $response->assertSessionHasErrors(['bank_name']);
    }

    public function test_bank_display_name_looks_up_the_friendly_name(): void
    {
        $streamer = Streamer::factory()->create(['bank_name' => 'bca']);

        $this->assertSame(config('banks')['bca'], $streamer->bankDisplayName());
    }

    public function test_bank_display_name_falls_back_gracefully_when_unset(): void
    {
        $streamer = Streamer::factory()->create(['bank_name' => null]);

        $this->assertSame('-', $streamer->bankDisplayName());
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `./vendor/bin/phpunit tests/Feature/PayoutDisbursement/BankCodeSettingsTest.php`
Expected: FAIL — `config('banks')` and `bankDisplayName()` don't exist yet; the current `bank_name` validation accepts any string.

- [ ] **Step 3: Create `config/banks.php`**

```php
<?php
// config/banks.php
return [
    'bca' => 'Bank Central Asia (BCA)',
    'bni' => 'Bank Negara Indonesia (BNI)',
    'bri' => 'Bank Rakyat Indonesia (BRI)',
    'mandiri' => 'Bank Mandiri',
    'cimb' => 'CIMB Niaga',
    'permata' => 'Bank Permata',
    'bsi' => 'Bank Syariah Indonesia (BSI)',
    'danamon' => 'Bank Danamon',
    'btn' => 'Bank Tabungan Negara (BTN)',
    'ocbc' => 'OCBC NISP',
];
```

- [ ] **Step 4: Add `Streamer::bankDisplayName()`**

Directly after `Streamer::paidDonations()`/`unpaidOutDonations()` (or any convenient spot among the other accessor-style methods):

```php
public function bankDisplayName(): string
{
    if (!$this->bank_name) {
        return '-';
    }

    return config('banks')[$this->bank_name] ?? $this->bank_name;
}
```

- [ ] **Step 5: Change the `bank_name` validation rule**

In `app/Http/Controllers/StreamerDashboardController.php::updateSettings`, change:

```php
'bank_name' => ['nullable', 'required_with:bank_account_number,bank_account_holder', 'string', 'max:100'],
```

to:

```php
'bank_name' => ['nullable', 'required_with:bank_account_number,bank_account_holder', Rule::in(array_keys(config('banks')))],
```

Add the import: `use Illuminate\Validation\Rule;`

- [ ] **Step 6: Change the Settings view field from text input to select**

In `resources/views/streamer/settings.blade.php`, replace:

```blade
<input type="text" name="bank_name" value="{{ old('bank_name', $streamer->bank_name) }}" placeholder="mis. Bank Central Asia">
```

with:

```blade
<select name="bank_name">
    <option value="">Pilih Bank</option>
    @foreach(config('banks') as $code => $name)
    <option value="{{ $code }}" {{ old('bank_name', $streamer->bank_name) === $code ? 'selected' : '' }}>
        {{ $name }}
    </option>
    @endforeach
</select>
```

- [ ] **Step 7: Create the data-fix migration**

```php
<?php
// database/migrations/2026_07_26_140000_normalize_streamer_bank_names_to_codes.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Existing bank_name values are free text (e.g. "Bank Central Asia") from
        // before this migration; they don't match the new coded vocabulary in
        // config/banks.php. Null them out — affected streamers re-select from
        // the dropdown next time they visit Settings. One-time, unavoidable cost
        // of moving from free text to a controlled vocabulary (see design spec).
        $validCodes = array_keys(config('banks'));

        DB::table('streamers')
            ->whereNotNull('bank_name')
            ->whereNotIn('bank_name', $validCodes)
            ->update(['bank_name' => null, 'bank_account_number' => null, 'bank_account_holder' => null]);
    }

    public function down(): void
    {
        // Irreversible by design — the original free-text values are gone.
    }
};
```

- [ ] **Step 8: Run test to verify it passes**

Run: `./vendor/bin/phpunit tests/Feature/PayoutDisbursement/BankCodeSettingsTest.php`
Expected: PASS (4 tests)

- [ ] **Step 9: Run the full suite to check for regressions**

Run: `./vendor/bin/phpunit`

Expect the existing `tests/Feature/Payout/StreamerBankSettingsTest.php` (from the original manual-payout work) to now fail on `test_saving_all_three_bank_fields_succeeds`, since it posts `'Bank Central Asia'` as free text — update that test's `bank_name` value to `'bca'` to match the new coded vocabulary. This is a deliberate, expected update to existing test data, not a regression to chase down.

- [ ] **Step 10: Commit**

```bash
git add config/banks.php app/Models/Streamer.php app/Http/Controllers/StreamerDashboardController.php \
        resources/views/streamer/settings.blade.php \
        database/migrations/2026_07_26_140000_normalize_streamer_bank_names_to_codes.php \
        tests/Feature/PayoutDisbursement/BankCodeSettingsTest.php \
        tests/Feature/Payout/StreamerBankSettingsTest.php
git commit -m "feat: change streamer bank field from free text to a coded dropdown"
```

---

### Task 3: Wire the gateway into `AdminPayoutController::create()`, lifecycle guard updates

**Files:**
- Modify: `app/Http/Controllers/AdminPayoutController.php`
- Test: `tests/Feature/PayoutDisbursement/PayoutCreationDisbursementTest.php`

**Interfaces:**
- Consumes: `PayoutGatewayInterface` (Task 1).

- [ ] **Step 1: Write the failing tests**

```php
<?php
// tests/Feature/PayoutDisbursement/PayoutCreationDisbursementTest.php
namespace Tests\Feature\PayoutDisbursement;

use App\Models\Donation;
use App\Models\Payout;
use App\Models\Streamer;
use App\Models\User;
use App\Services\Payout\PayoutGatewayInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\FakePayoutGateway;
use Tests\TestCase;

class PayoutCreationDisbursementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        return $admin;
    }

    private function streamerWithBankInfo(): Streamer
    {
        return Streamer::factory()->create([
            'bank_name' => 'bca',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'Budi Santoso',
        ]);
    }

    public function test_flag_off_creates_pending_payout_unchanged(): void
    {
        config(['payout.automated_disbursement_enabled' => false]);
        $admin = $this->admin();
        $streamer = $this->streamerWithBankInfo();
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 100000]);

        $this->actingAs($admin)->post("/admin/payouts/{$streamer->id}");

        $this->assertSame('pending', Payout::firstOrFail()->status);
    }

    /**
     * Binds FakePayoutGateway for just this test (not a global TestCase
     * override — see Task 1 Step 7's note on why ManualPayoutGateway stays
     * the real implementation by default).
     */
    private function fakeGateway(): FakePayoutGateway
    {
        $fake = new FakePayoutGateway();
        $this->app->singleton(PayoutGatewayInterface::class, fn () => $fake);
        return $fake;
    }

    public function test_flag_on_and_disburse_succeeds_moves_to_processing(): void
    {
        config(['payout.automated_disbursement_enabled' => true]);
        $gateway = $this->fakeGateway();
        $gateway->disburseStatus = 'processing';

        $admin = $this->admin();
        $streamer = $this->streamerWithBankInfo();
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 100000]);

        $this->actingAs($admin)->post("/admin/payouts/{$streamer->id}");

        $payout = Payout::firstOrFail();
        $this->assertSame('processing', $payout->status);
        $this->assertSame("FAKE-IRIS-REF-{$payout->id}", $payout->reference);
    }

    public function test_flag_on_and_disburse_fails_releases_donations(): void
    {
        config(['payout.automated_disbursement_enabled' => true]);
        $gateway = $this->fakeGateway();
        $gateway->disburseStatus = 'failed';

        $admin = $this->admin();
        $streamer = $this->streamerWithBankInfo();
        $donation = Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 100000]);

        $this->actingAs($admin)->post("/admin/payouts/{$streamer->id}");

        $payout = Payout::firstOrFail();
        $this->assertSame('failed', $payout->status);
        $donation->refresh();
        $this->assertNull($donation->payout_id);
    }

    public function test_flag_on_and_bank_account_invalid_rejects_before_creating_payout(): void
    {
        config(['payout.automated_disbursement_enabled' => true]);
        $gateway = $this->fakeGateway();
        $gateway->bankAccountValid = false;

        $admin = $this->admin();
        $streamer = $this->streamerWithBankInfo();
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 100000]);

        $response = $this->actingAs($admin)->post("/admin/payouts/{$streamer->id}");

        $response->assertSessionHasErrors();
        $this->assertSame(0, Payout::count());
    }

    public function test_mark_paid_accepts_processing_status(): void
    {
        $admin = $this->admin();
        $payout = Payout::factory()->create(['status' => 'processing']);

        $response = $this->actingAs($admin)->post("/admin/payouts/{$payout->id}/mark-paid", [
            'reference' => 'MANUAL-OVERRIDE-REF',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertSame('paid', $payout->fresh()->status);
    }

    public function test_void_rejects_processing_status(): void
    {
        $admin = $this->admin();
        $payout = Payout::factory()->create(['status' => 'processing']);

        $response = $this->actingAs($admin)->post("/admin/payouts/{$payout->id}/void");

        $response->assertSessionHasErrors();
        $this->assertSame('processing', $payout->fresh()->status);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `./vendor/bin/phpunit tests/Feature/PayoutDisbursement/PayoutCreationDisbursementTest.php`
Expected: FAIL — `create()` doesn't call the gateway yet; `markPaid()`/`void()` don't recognize `processing`.

- [ ] **Step 3: Update `AdminPayoutController::create()`**

Add the import:
```php
use App\Services\Payout\PayoutGatewayInterface;
```

Add constructor injection:
```php
public function __construct(
    private readonly PayoutGatewayInterface $payoutGateway
) {}
```

Inside the `DB::transaction()` closure in `create()`, after the existing bank-info check and before `Payout::create([...])`, add the pre-creation validation. `validateBankAccount()` takes a `Payout`, but no `Payout` row exists yet at this point — pass a transient, unsaved instance built from the streamer's bank fields (an unsaved Eloquent model works fine for plain attribute access; nothing here touches the database until `->save()`/`::create()` is called):

```php
$transientPayout = new Payout([
    'bank_name' => $streamer->bank_name,
    'bank_account_number' => $streamer->bank_account_number,
    'bank_account_holder' => $streamer->bank_account_holder,
]);

if (!$this->payoutGateway->validateBankAccount($transientPayout)) {
    throw new \InvalidArgumentException('Info rekening bank streamer tidak valid (gagal validasi Midtrans).');
}
```

After `$donations->each(fn ($d) => $d->update(['payout_id' => $payout->id]));`, inside the same transaction, add:

```php
$disbursement = $this->payoutGateway->disburse($payout);

if ($disbursement->status === 'processing') {
    $payout->update(['status' => 'processing', 'reference' => $disbursement->reference]);
} elseif ($disbursement->status === 'failed') {
    $donations->each(fn ($d) => $d->update(['payout_id' => null]));
    $payout->update(['status' => 'failed']);
}
// status === 'pending' (manual gateway): no change, payout stays pending as created.

return $payout;
```

- [ ] **Step 4: Update `markPaid()` and `void()` guards**

In `markPaid()`:
```php
if (!in_array($payout->status, ['pending', 'processing'], true)) {
    return back()->withErrors(['payout' => 'Payout ini sudah diproses sebelumnya.']);
}
```

`void()`'s existing `if ($payout->status !== 'pending')` guard is already correct as-is — no change needed there (confirms `processing` stays rejected).

- [ ] **Step 5: Run test to verify it passes**

Run: `./vendor/bin/phpunit tests/Feature/PayoutDisbursement/PayoutCreationDisbursementTest.php`
Expected: PASS (6 tests)

- [ ] **Step 6: Run the full suite to check for regressions**

Run: `./vendor/bin/phpunit`

- [ ] **Step 7: Commit**

```bash
git add app/Http/Controllers/AdminPayoutController.php \
        tests/Feature/PayoutDisbursement/PayoutCreationDisbursementTest.php
git commit -m "feat: wire PayoutGatewayInterface into AdminPayoutController::create(), update lifecycle guards"
```

---

### Task 4: `CheckPayoutDisbursementStatusJob`

**Files:**
- Create: `app/Jobs/CheckPayoutDisbursementStatusJob.php`
- Modify: `routes/console.php`
- Test: `tests/Unit/Jobs/CheckPayoutDisbursementStatusJobTest.php`

**Interfaces:**
- Consumes: `PayoutGatewayInterface::checkStatus()` (Task 1).

- [ ] **Step 1: Write the failing test**

```php
<?php
// tests/Unit/Jobs/CheckPayoutDisbursementStatusJobTest.php
namespace Tests\Unit\Jobs;

use App\Jobs\CheckPayoutDisbursementStatusJob;
use App\Models\Donation;
use App\Models\Payout;
use App\Models\Streamer;
use App\Services\Payout\PayoutGatewayInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\FakePayoutGateway;
use Tests\TestCase;

class CheckPayoutDisbursementStatusJobTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Binds FakePayoutGateway for just this test — the job resolves
     * PayoutGatewayInterface itself (not via constructor injection like the
     * controller), so this needs to be bound before the job runs regardless
     * of the automated_disbursement_enabled flag (this job only exists to
     * poll payouts that are already `processing`, which only happens when
     * automation was on at creation time — but the job itself doesn't
     * re-check the flag, it just polls whatever gateway is bound).
     */
    private function fakeGateway(): FakePayoutGateway
    {
        $fake = new FakePayoutGateway();
        $this->app->singleton(PayoutGatewayInterface::class, fn () => $fake);
        return $fake;
    }

    public function test_processing_payout_resolves_to_paid(): void
    {
        $gateway = $this->fakeGateway();
        $gateway->checkStatusResult = 'paid';

        $payout = Payout::factory()->create(['status' => 'processing']);

        (new CheckPayoutDisbursementStatusJob())->handle();

        $payout->refresh();
        $this->assertSame('paid', $payout->status);
        $this->assertNotNull($payout->paid_at);
    }

    public function test_processing_payout_resolves_to_failed_and_releases_donations(): void
    {
        $gateway = $this->fakeGateway();
        $gateway->checkStatusResult = 'failed';

        $streamer = Streamer::factory()->create();
        $payout = Payout::factory()->for($streamer)->create(['status' => 'processing']);
        $donation = Donation::factory()->for($streamer)->create(['status' => 'paid', 'payout_id' => $payout->id]);

        (new CheckPayoutDisbursementStatusJob())->handle();

        $payout->refresh();
        $this->assertSame('failed', $payout->status);
        $donation->refresh();
        $this->assertNull($donation->payout_id);
    }

    public function test_non_processing_payouts_are_untouched(): void
    {
        $gateway = $this->fakeGateway();
        $gateway->checkStatusResult = 'paid'; // would resolve to paid if (wrongly) picked up

        $pending = Payout::factory()->create(['status' => 'pending']);
        $originalPaidAt = now()->subDay();
        $alreadyPaid = Payout::factory()->create(['status' => 'paid', 'paid_at' => $originalPaidAt]);

        (new CheckPayoutDisbursementStatusJob())->handle();

        $this->assertSame('pending', $pending->fresh()->status);
        $this->assertSame('paid', $alreadyPaid->fresh()->status);
        $this->assertSame($originalPaidAt->toDateTimeString(), $alreadyPaid->fresh()->paid_at->toDateTimeString());
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `./vendor/bin/phpunit tests/Unit/Jobs/CheckPayoutDisbursementStatusJobTest.php`
Expected: FAIL — job doesn't exist yet.

- [ ] **Step 3: Create the job**

```php
<?php
// app/Jobs/CheckPayoutDisbursementStatusJob.php
namespace App\Jobs;

use App\Models\Donation;
use App\Models\Payout;
use App\Services\Payout\PayoutGatewayInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckPayoutDisbursementStatusJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function handle(): void
    {
        $gateway = app(PayoutGatewayInterface::class);
        $processing = Payout::where('status', 'processing')->get();

        foreach ($processing as $payout) {
            $result = $gateway->checkStatus($payout);

            if ($result->status === 'paid') {
                $payout->update(['status' => 'paid', 'paid_at' => now()]);
            } elseif ($result->status === 'failed') {
                DB::transaction(function () use ($payout) {
                    Donation::where('payout_id', $payout->id)->update(['payout_id' => null]);
                    $payout->update(['status' => 'failed']);
                });
            }
            // status === 'processing': still in flight, leave as-is until next run.
        }

        Log::info("CheckPayoutDisbursementStatusJob: checked {$processing->count()} processing payouts");
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('CheckPayoutDisbursementStatusJob: semua retry habis', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `./vendor/bin/phpunit tests/Unit/Jobs/CheckPayoutDisbursementStatusJobTest.php`
Expected: PASS (3 tests)

- [ ] **Step 5: Schedule the job**

In `routes/console.php`:

```php
use App\Jobs\CheckPayoutDisbursementStatusJob;
```

```php
// Poll Midtrans Iris for processing payout status every 15 minutes
Schedule::job(new CheckPayoutDisbursementStatusJob)->everyFifteenMinutes();
```

- [ ] **Step 6: Run the full suite to check for regressions**

Run: `./vendor/bin/phpunit`

- [ ] **Step 7: Commit**

```bash
git add app/Jobs/CheckPayoutDisbursementStatusJob.php routes/console.php \
        tests/Unit/Jobs/CheckPayoutDisbursementStatusJobTest.php
git commit -m "feat: add scheduled polling job for processing payout status"
```

---

### Task 5: `MidtransIrisGateway` — real implementation, payload TODO flagged

**Files:**
- Create: `app/Services/Payout/MidtransIrisGateway.php`
- Modify: `app/Providers/AppServiceProvider.php` (uncomment/finalize the binding from Task 1 Step 6 — it already references this class)

**Interfaces:**
- Produces: `MidtransIrisGateway implements PayoutGatewayInterface`.

This task's three methods are the flagged exception described in Global Constraints. No automated test exercises the real Iris HTTP calls (same boundary as `MidtransSnapGateway::createTransaction()` and the Chart.js rendering work — nothing here can hit a live sandbox without real credentials).

- [ ] **Step 1: Create the class with structurally-correct, payload-flagged methods**

```php
<?php
// app/Services/Payout/MidtransIrisGateway.php
namespace App\Services\Payout;

use App\Models\Payout;
use Illuminate\Support\Facades\Http;

/**
 * Wraps Midtrans Iris (disbursement/payout). Uses a separate Iris API key
 * (config('payout.iris_api_key')), NOT the Snap/Core API server key.
 *
 * IMPORTANT: the exact request/response JSON shape for CreatePayout,
 * ApprovePayout, and ValidateBankAccount could not be confirmed against
 * Midtrans's live Iris API reference in the session that wrote this class
 * (see docs/superpowers/specs/2026-07-26-automated-payout-disbursement-design.md,
 * "Important limitation"). CONFIRM the real field names against
 * https://docs.midtrans.com/reference/ before enabling
 * payout.automated_disbursement_enabled in any real environment — the
 * bodies below are structurally wired (auth header, base URL, method
 * shape) but the field names inside each request/response are marked and
 * MUST be verified, not trusted as-is.
 */
class MidtransIrisGateway implements PayoutGatewayInterface
{
    private function baseUrl(): string
    {
        return config('midtrans.is_production')
            ? 'https://app.midtrans.com/iris/api/v1'
            : 'https://app.sandbox.midtrans.com/iris/api/v1';
    }

    private function client()
    {
        return Http::withBasicAuth(config('payout.iris_api_key'), '')
            ->baseUrl($this->baseUrl())
            ->acceptJson();
    }

    public function validateBankAccount(Payout $payout): bool
    {
        // CONFIRM FIELD NAMES: this call signature (bank name + account number
        // → validity/account-holder-name response) is real per midtrans-go's
        // ValidateBankAccount method, but the exact query/body field names and
        // response shape are not verified here.
        $response = $this->client()->get('bank_account_validation', [
            'bank' => $payout->bank_name, // TODO: confirm param name against live API reference
            'account' => $payout->bank_account_number, // TODO: confirm param name
        ]);

        // TODO: confirm the actual success/validity field in the response body
        return $response->successful() && (bool) ($response->json('is_valid') ?? false);
    }

    public function disburse(Payout $payout): PayoutDisbursementResult
    {
        // CONFIRM FIELD NAMES: CreatePayout + ApprovePayout are real Iris
        // methods (per midtrans-go), but the exact request bodies below are
        // NOT verified — placeholders for the shape, not trusted values.
        $createResponse = $this->client()->post('payouts', [
            'payouts' => [[
                'beneficiary_name' => $payout->bank_account_holder, // TODO: confirm field name
                'beneficiary_account' => $payout->bank_account_number, // TODO: confirm field name
                'beneficiary_bank' => $payout->bank_name, // TODO: confirm field name
                'amount' => (string) $payout->net_amount, // TODO: confirm field name/type
                'notes' => "Payout #{$payout->id}", // TODO: confirm field name
            ]],
        ]);

        if (!$createResponse->successful()) {
            return new PayoutDisbursementResult(
                status: 'failed',
                errorMessage: 'CreatePayout gagal: ' . $createResponse->body(),
            );
        }

        // TODO: confirm the actual reference-number field in CreatePayout's response
        $referenceNo = $createResponse->json('payouts.0.reference_no');

        // Full automation per design decision: approve immediately, no
        // separate admin click.
        $approveResponse = $this->client()->post('payouts/approve', [
            'reference_nos' => [$referenceNo], // TODO: confirm field name
            'otp' => null, // TODO: confirm whether/how 2FA OTP applies for this account tier
        ]);

        if (!$approveResponse->successful()) {
            return new PayoutDisbursementResult(
                status: 'failed',
                reference: $referenceNo,
                errorMessage: 'ApprovePayout gagal: ' . $approveResponse->body(),
            );
        }

        return new PayoutDisbursementResult(status: 'processing', reference: $referenceNo);
    }

    public function checkStatus(Payout $payout): PayoutStatusResult
    {
        // CONFIRM FIELD NAMES: GetPayoutDetails is a real Iris method (per
        // midtrans-go), but the exact response status values/field names
        // below are NOT verified.
        $response = $this->client()->get("payouts/{$payout->reference}");

        // TODO: confirm the actual status field/values (this assumes something
        // like "approved"/"rejected"/"queued" — verify against the live API).
        $status = $response->json('status'); // TODO: confirm field name

        return new PayoutStatusResult(status: match ($status) {
            'completed', 'approved' => 'paid', // TODO: confirm actual terminal-success value
            'rejected', 'failed' => 'failed', // TODO: confirm actual terminal-failure value
            default => 'processing',
        });
    }
}
```

- [ ] **Step 2: Manual verification checklist (once real Iris credentials + KYC exist)**

This class cannot be exercised against a real sandbox in this environment. Before ever setting `PAYOUT_AUTOMATED_DISBURSEMENT_ENABLED=true` in a real environment:

1. Pull Midtrans's current Iris API reference (`https://docs.midtrans.com/reference/`) or a Postman collection.
2. Replace every `// TODO: confirm` field name/value in this file with the verified real ones.
3. Test `validateBankAccount()` against a real (sandbox) bank account number — confirm it returns `true`/`false` correctly.
4. Test `disburse()` against a small real sandbox payout — confirm `CreatePayout` + `ApprovePayout` both succeed and the returned reference matches what appears in the Midtrans Iris Portal.
5. Test `checkStatus()` polls that same payout and correctly reflects its Iris Portal status.
6. Only then flip the flag on in a real environment — and start with it off in production even after this, verifying one real payout manually before trusting it broadly.

- [ ] **Step 3: Run the full suite to check for regressions**

Run: `./vendor/bin/phpunit`
Expected: all pass — this class has no automated test coverage of its own (per the deliberate boundary above), so this step just confirms adding the file didn't break anything else (autoloading, container resolution).

- [ ] **Step 4: Commit**

```bash
git add app/Services/Payout/MidtransIrisGateway.php
git commit -m "feat: add MidtransIrisGateway skeleton — payload fields flagged pending live API confirmation"
```

---

## Post-plan note

Once merged, update `BACKLOG.md`'s "Automated payout disbursement" item to reflect the architecture is built but gated (`automated_disbursement_enabled` default off) and the `MidtransIrisGateway` payload fields are still pending confirmation against Midtrans's live Iris API reference before the flag can safely be turned on anywhere.
