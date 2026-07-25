# Streamer Payout / Settlement Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Track how much each streamer is owed from confirmed donations, let an admin manually record a payout (bank transfer done outside the app) against that owed balance, and let streamers see their own payout history.

**Architecture:** Additive migrations (`streamers.bank_*`, new `payouts` table, `donations.payout_id`), a plain `Payout` Eloquent model (no gateway interface — manual payout makes no external API call), a new `AdminPayoutController` for creation/management, and a read-only view added to the existing streamer dashboard controller.

**Tech Stack:** Laravel 12, PHP 8.2+, PHPUnit 11 (existing suite conventions — `StreamerFactory`/`DonationFactory` already exist from the payment gateway work).

## Global Constraints

- Design source of truth: `docs/superpowers/specs/2026-07-25-payout-settlement-design.md` — every task below implements one section of it.
- User-facing strings are Indonesian, matching the rest of the codebase.
- Platform fee is `config('payout.platform_fee_percent', 10)`, minimum payout is `config('payout.minimum_amount', 50000)` — both env-overridable config values, not admin-editable UI settings.
- Every new migration is additive (no dropped/renamed columns).
- A `paid` `Payout` is immutable — no void, no edit, ever.
- Bank info fields (`bank_name`, `bank_account_number`, `bank_account_holder`) are all-or-nothing: if any is submitted, all three are required.

---

### Task 1: Data model — migrations, `Payout` model, `Streamer` additions, config, factory

**Files:**
- Create: `database/migrations/2026_07_25_160000_add_bank_fields_to_streamers_table.php`
- Create: `database/migrations/2026_07_25_160100_create_payouts_table.php`
- Create: `database/migrations/2026_07_25_160200_add_payout_id_to_donations_table.php`
- Create: `app/Models/Payout.php`
- Modify: `app/Models/Streamer.php`
- Modify: `app/Models/Donation.php`
- Create: `config/payout.php`
- Create: `database/factories/PayoutFactory.php`
- Test: `tests/Feature/Payout/UnpaidOutDonationsTest.php`

**Interfaces:**
- Produces: `Streamer::unpaidOutDonations(): HasMany`, `Streamer::payouts(): HasMany`, `Donation::payout(): BelongsTo`, `Payout` model with `streamer()`, `donations()`, `createdBy()` relations and `status` values `pending`|`paid`|`voided`.

- [ ] **Step 1: Write the failing test**

```php
<?php
// tests/Feature/Payout/UnpaidOutDonationsTest.php
namespace Tests\Feature\Payout;

use App\Models\Donation;
use App\Models\Payout;
use App\Models\Streamer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnpaidOutDonationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_unpaid_out_donations_only_counts_paid_and_unassigned(): void
    {
        $streamer = Streamer::factory()->create();
        Donation::factory()->for($streamer)->create(['status' => 'pending', 'amount' => 999999]);
        Donation::factory()->for($streamer)->create(['status' => 'failed', 'amount' => 999999]);

        $payout = Payout::factory()->for($streamer)->create();
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 999999, 'payout_id' => $payout->id]);

        $unassignedPaid = Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 15000]);

        $result = $streamer->unpaidOutDonations()->get();

        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($unassignedPaid));
        $this->assertSame(15000, $streamer->unpaidOutDonations()->sum('amount'));
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `./vendor/bin/phpunit tests/Feature/Payout/UnpaidOutDonationsTest.php`
Expected: FAIL — `Payout` model/factory, `payout_id` column, and `unpaidOutDonations()` don't exist yet.

- [ ] **Step 3: Create the migrations**

```php
<?php
// database/migrations/2026_07_25_160000_add_bank_fields_to_streamers_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('streamers', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('thank_you_message');
            $table->string('bank_account_number')->nullable()->after('bank_name');
            $table->string('bank_account_holder')->nullable()->after('bank_account_number');
        });
    }

    public function down(): void
    {
        Schema::table('streamers', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'bank_account_number', 'bank_account_holder']);
        });
    }
};
```

```php
<?php
// database/migrations/2026_07_25_160100_create_payouts_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('streamer_id')->constrained()->onDelete('cascade');
            $table->bigInteger('gross_amount')->comment('Total donasi yang termasuk dalam payout ini');
            $table->bigInteger('platform_fee_amount')->comment('Snapshot fee saat payout dibuat, tidak dihitung ulang');
            $table->bigInteger('net_amount')->comment('gross_amount - platform_fee_amount, diterima streamer');
            $table->string('status', 20)->default('pending')->comment('pending | paid | voided');
            $table->string('bank_name')->comment('Snapshot info bank streamer saat payout dibuat');
            $table->string('bank_account_number');
            $table->string('bank_account_holder');
            $table->string('reference')->nullable()->comment('Referensi transfer bank, diisi saat mark-paid');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->comment('Admin yang membuat payout ini');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['streamer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};
```

```php
<?php
// database/migrations/2026_07_25_160200_add_payout_id_to_donations_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->foreignId('payout_id')->nullable()->after('paid_at')
                ->constrained()->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropForeign(['payout_id']);
            $table->dropColumn('payout_id');
        });
    }
};
```

- [ ] **Step 4: Create `config/payout.php`**

```php
<?php
// config/payout.php
return [
    'platform_fee_percent' => (int) env('PAYOUT_PLATFORM_FEE_PERCENT', 10),
    'minimum_amount' => (int) env('PAYOUT_MINIMUM_AMOUNT', 50000),
];
```

Append to `.env.example`:
```
PAYOUT_PLATFORM_FEE_PERCENT=10
PAYOUT_MINIMUM_AMOUNT=50000
```

- [ ] **Step 5: Create the `Payout` model**

```php
<?php
// app/Models/Payout.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payout extends Model
{
    use HasFactory;

    protected $fillable = [
        'streamer_id',
        'gross_amount',
        'platform_fee_amount',
        'net_amount',
        'status',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'reference',
        'notes',
        'created_by',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'gross_amount' => 'integer',
            'platform_fee_amount' => 'integer',
            'net_amount' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    public function streamer(): BelongsTo
    {
        return $this->belongsTo(Streamer::class);
    }

    public function donations(): HasMany
    {
        return $this->hasMany(Donation::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFormattedNetAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->net_amount, 0, ',', '.');
    }
}
```

- [ ] **Step 6: Add to `Streamer` model**

Directly after the existing `paidDonations()` method in `app/Models/Streamer.php`:

```php
/**
 * Donasi yang sudah dibayar tapi belum masuk payout manapun —
 * inilah saldo "owed" streamer yang ditampilkan di admin/payouts.
 */
public function unpaidOutDonations(): HasMany
{
    return $this->paidDonations()->whereNull('payout_id');
}

public function payouts(): HasMany
{
    return $this->hasMany(Payout::class);
}
```

Also add `bank_name`, `bank_account_number`, `bank_account_holder` to `Streamer::$fillable`.

- [ ] **Step 7: Add to `Donation` model**

```php
public function payout(): BelongsTo
{
    return $this->belongsTo(Payout::class);
}
```

Add `payout_id` to `Donation::$fillable`.

- [ ] **Step 8: Create `PayoutFactory`**

```php
<?php
// database/factories/PayoutFactory.php
namespace Database\Factories;

use App\Models\Payout;
use App\Models\Streamer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payout>
 */
class PayoutFactory extends Factory
{
    protected $model = Payout::class;

    public function definition(): array
    {
        return [
            'streamer_id' => Streamer::factory(),
            'gross_amount' => 100000,
            'platform_fee_amount' => 10000,
            'net_amount' => 90000,
            'status' => 'pending',
            'bank_name' => 'Bank Central Asia',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => fake()->name(),
            'created_by' => User::factory(),
        ];
    }
}
```

- [ ] **Step 9: Run test to verify it passes**

Run: `./vendor/bin/phpunit tests/Feature/Payout/UnpaidOutDonationsTest.php`
Expected: PASS

- [ ] **Step 10: Run the full suite to check for regressions**

Run: `./vendor/bin/phpunit`

- [ ] **Step 11: Commit**

```bash
git add database/migrations/2026_07_25_160000_add_bank_fields_to_streamers_table.php \
        database/migrations/2026_07_25_160100_create_payouts_table.php \
        database/migrations/2026_07_25_160200_add_payout_id_to_donations_table.php \
        app/Models/Payout.php app/Models/Streamer.php app/Models/Donation.php \
        config/payout.php .env.example database/factories/PayoutFactory.php \
        tests/Feature/Payout/UnpaidOutDonationsTest.php
git commit -m "feat: add payout data model — Payout model, bank fields, payout_id FK"
```

---

### Task 2: Streamer Settings — bank account fields

**Files:**
- Modify: `app/Http/Controllers/StreamerDashboardController.php`
- Modify: `resources/views/streamer/settings.blade.php`
- Test: `tests/Feature/Payout/StreamerBankSettingsTest.php`

**Interfaces:**
- Consumes: `Streamer::$fillable` bank fields (Task 1).

- [ ] **Step 1: Write the failing tests**

```php
<?php
// tests/Feature/Payout/StreamerBankSettingsTest.php
namespace Tests\Feature\Payout;

use App\Models\Streamer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StreamerBankSettingsTest extends TestCase
{
    use RefreshDatabase;

    private function streamerUser(): array
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'streamer'])->save();
        $streamer = Streamer::factory()->create(['user_id' => $user->id]);

        return [$user, $streamer];
    }

    public function test_saving_all_three_bank_fields_succeeds(): void
    {
        [$user, $streamer] = $this->streamerUser();

        $response = $this->actingAs($user)->post('/streamer/settings', [
            'display_name' => $streamer->display_name,
            'min_donation' => $streamer->min_donation,
            'thank_you_message' => $streamer->thank_you_message,
            'bank_name' => 'Bank Central Asia',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'Budi Santoso',
        ]);

        $response->assertSessionHasNoErrors();
        $streamer->refresh();
        $this->assertSame('Bank Central Asia', $streamer->bank_name);
        $this->assertSame('1234567890', $streamer->bank_account_number);
        $this->assertSame('Budi Santoso', $streamer->bank_account_holder);
    }

    public function test_saving_only_one_bank_field_is_rejected(): void
    {
        [$user, $streamer] = $this->streamerUser();

        $response = $this->actingAs($user)->post('/streamer/settings', [
            'display_name' => $streamer->display_name,
            'min_donation' => $streamer->min_donation,
            'thank_you_message' => $streamer->thank_you_message,
            'bank_account_number' => '1234567890',
        ]);

        $response->assertSessionHasErrors(['bank_name', 'bank_account_holder']);
        $streamer->refresh();
        $this->assertNull($streamer->bank_account_number);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `./vendor/bin/phpunit tests/Feature/Payout/StreamerBankSettingsTest.php`
Expected: FAIL — validation doesn't recognize these fields, first test fails because nothing gets saved (fields not in `$validated`/`fill()`).

- [ ] **Step 3: Add validation and save logic**

In `app/Http/Controllers/StreamerDashboardController.php::updateSettings`, add to the `$request->validate([...])` array:

```php
'bank_name' => ['nullable', 'required_with:bank_account_number,bank_account_holder', 'string', 'max:100'],
'bank_account_number' => ['nullable', 'required_with:bank_name,bank_account_holder', 'string', 'max:50'],
'bank_account_holder' => ['nullable', 'required_with:bank_name,bank_account_number', 'string', 'max:100'],
```

Then include them in whatever `$streamer->fill([...])`/update call already persists `display_name`/`min_donation`/etc — add:
```php
'bank_name' => $validated['bank_name'] ?? $streamer->bank_name,
'bank_account_number' => $validated['bank_account_number'] ?? $streamer->bank_account_number,
'bank_account_holder' => $validated['bank_account_holder'] ?? $streamer->bank_account_holder,
```

- [ ] **Step 4: Add fields to the settings view**

In `resources/views/streamer/settings.blade.php`, directly after the existing `form-row` containing "Nama Tampilan"/"Minimum Donasi" (around line 403-416), add:

```blade
<div class="form-row">
    <div class="form-group">
        <label>Nama Bank</label>
        <input type="text" name="bank_name" value="{{ old('bank_name', $streamer->bank_name) }}" placeholder="mis. Bank Central Asia">
    </div>
    <div class="form-group">
        <label>Nomor Rekening</label>
        <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $streamer->bank_account_number) }}">
    </div>
</div>
<div class="form-group">
    <label>Nama Pemilik Rekening</label>
    <input type="text" name="bank_account_holder" value="{{ old('bank_account_holder', $streamer->bank_account_holder) }}">
</div>
```

- [ ] **Step 5: Run test to verify it passes**

Run: `./vendor/bin/phpunit tests/Feature/Payout/StreamerBankSettingsTest.php`
Expected: PASS

- [ ] **Step 6: Run the full suite to check for regressions**

Run: `./vendor/bin/phpunit`

- [ ] **Step 7: Commit**

```bash
git add app/Http/Controllers/StreamerDashboardController.php resources/views/streamer/settings.blade.php \
        tests/Feature/Payout/StreamerBankSettingsTest.php
git commit -m "feat: add bank account fields to streamer settings"
```

---

### Task 3: `AdminPayoutController::create()` — the core payout-creation logic

**Files:**
- Create: `app/Http/Controllers/AdminPayoutController.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Payout/PayoutCreationTest.php`

**Interfaces:**
- Consumes: `Streamer::unpaidOutDonations()` (Task 1), `config('payout.*')` (Task 1).
- Produces: `POST /admin/payouts/{streamer}` → redirects back with a new `Payout` created (or a validation error).

- [ ] **Step 1: Write the failing tests**

```php
<?php
// tests/Feature/Payout/PayoutCreationTest.php
namespace Tests\Feature\Payout;

use App\Models\Donation;
use App\Models\Payout;
use App\Models\Streamer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayoutCreationTest extends TestCase
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
            'bank_name' => 'Bank Central Asia',
            'bank_account_number' => '1234567890',
            'bank_account_holder' => 'Budi Santoso',
        ]);
    }

    public function test_creates_payout_with_correct_amounts_and_assigns_donations(): void
    {
        $admin = $this->admin();
        $streamer = $this->streamerWithBankInfo();
        $d1 = Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 60000]);
        $d2 = Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 40000]);

        $response = $this->actingAs($admin)->post("/admin/payouts/{$streamer->id}");

        $response->assertSessionHasNoErrors();
        $payout = Payout::firstOrFail();
        $this->assertSame(100000, $payout->gross_amount);
        $this->assertSame(10000, $payout->platform_fee_amount);
        $this->assertSame(90000, $payout->net_amount);
        $this->assertSame('Bank Central Asia', $payout->bank_name);

        $d1->refresh();
        $d2->refresh();
        $this->assertSame($payout->id, $d1->payout_id);
        $this->assertSame($payout->id, $d2->payout_id);
    }

    public function test_below_minimum_amount_is_rejected(): void
    {
        $admin = $this->admin();
        $streamer = $this->streamerWithBankInfo();
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 10000]);

        $response = $this->actingAs($admin)->post("/admin/payouts/{$streamer->id}");

        $response->assertSessionHasErrors();
        $this->assertSame(0, Payout::count());
    }

    public function test_missing_bank_info_is_rejected(): void
    {
        $admin = $this->admin();
        $streamer = Streamer::factory()->create(); // no bank info
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 100000]);

        $response = $this->actingAs($admin)->post("/admin/payouts/{$streamer->id}");

        $response->assertSessionHasErrors();
        $this->assertSame(0, Payout::count());
    }

    public function test_already_assigned_donation_is_excluded_from_a_new_payout(): void
    {
        $admin = $this->admin();
        $streamer = $this->streamerWithBankInfo();
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 100000]);

        $this->actingAs($admin)->post("/admin/payouts/{$streamer->id}");
        $firstPayout = Payout::firstOrFail();

        $newDonation = Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 60000]);
        $this->actingAs($admin)->post("/admin/payouts/{$streamer->id}");

        $this->assertSame(2, Payout::count());
        $secondPayout = Payout::where('id', '!=', $firstPayout->id)->firstOrFail();
        $this->assertSame(60000, $secondPayout->gross_amount);
        $newDonation->refresh();
        $this->assertSame($secondPayout->id, $newDonation->payout_id);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `./vendor/bin/phpunit tests/Feature/Payout/PayoutCreationTest.php`
Expected: FAIL — route/controller don't exist yet (404s).

- [ ] **Step 3: Create `AdminPayoutController` with `create()`**

```php
<?php
// app/Http/Controllers/AdminPayoutController.php
namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Payout;
use App\Models\Streamer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminPayoutController extends Controller
{
    public function create(Streamer $streamer): RedirectResponse
    {
        try {
            $payout = DB::transaction(function () use ($streamer) {
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
                    'created_by' => Auth::id(),
                ]);

                $donations->each(fn ($d) => $d->update(['payout_id' => $payout->id]));

                return $payout;
            });
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
}
```

- [ ] **Step 4: Register the route**

In `routes/web.php`, add the import and route inside the existing `admin`-gated group (alongside the `donations`/`users` routes):

```php
use App\Http\Controllers\AdminPayoutController;
```

```php
// Payouts
Route::post('/payouts/{streamer}', [AdminPayoutController::class, 'create'])
    ->middleware('throttle:admin-actions')
    ->name('payouts.create');
```

- [ ] **Step 5: Run test to verify it passes**

Run: `./vendor/bin/phpunit tests/Feature/Payout/PayoutCreationTest.php`
Expected: PASS (4 tests)

- [ ] **Step 6: Run the full suite to check for regressions**

Run: `./vendor/bin/phpunit`

- [ ] **Step 7: Commit**

```bash
git add app/Http/Controllers/AdminPayoutController.php routes/web.php \
        tests/Feature/Payout/PayoutCreationTest.php
git commit -m "feat: add AdminPayoutController::create() — computes and locks in a payout"
```

---

### Task 4: `AdminPayoutController::markPaid()` and `void()`

**Files:**
- Modify: `app/Http/Controllers/AdminPayoutController.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Payout/PayoutLifecycleTest.php`

**Interfaces:**
- Consumes: `Payout` model (Task 1), `Payout::donations()` relation.

- [ ] **Step 1: Write the failing tests**

```php
<?php
// tests/Feature/Payout/PayoutLifecycleTest.php
namespace Tests\Feature\Payout;

use App\Models\Donation;
use App\Models\Payout;
use App\Models\Streamer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayoutLifecycleTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        return $admin;
    }

    public function test_mark_paid_sets_status_and_reference(): void
    {
        $admin = $this->admin();
        $payout = Payout::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($admin)->post("/admin/payouts/{$payout->id}/mark-paid", [
            'reference' => 'TRF-20260725-001',
        ]);

        $response->assertSessionHasNoErrors();
        $payout->refresh();
        $this->assertSame('paid', $payout->status);
        $this->assertSame('TRF-20260725-001', $payout->reference);
        $this->assertNotNull($payout->paid_at);
    }

    public function test_mark_paid_on_already_paid_payout_is_rejected(): void
    {
        $admin = $this->admin();
        $payout = Payout::factory()->create(['status' => 'paid', 'paid_at' => now(), 'reference' => 'OLD-REF']);

        $response = $this->actingAs($admin)->post("/admin/payouts/{$payout->id}/mark-paid", [
            'reference' => 'NEW-REF',
        ]);

        $response->assertSessionHasErrors();
        $payout->refresh();
        $this->assertSame('OLD-REF', $payout->reference);
    }

    public function test_void_releases_donations_back_to_unpaid_out(): void
    {
        $admin = $this->admin();
        $streamer = Streamer::factory()->create();
        $payout = Payout::factory()->for($streamer)->create(['status' => 'pending']);
        $donation = Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 50000, 'payout_id' => $payout->id]);

        $response = $this->actingAs($admin)->post("/admin/payouts/{$payout->id}/void");

        $response->assertSessionHasNoErrors();
        $payout->refresh();
        $this->assertSame('voided', $payout->status);

        $donation->refresh();
        $this->assertNull($donation->payout_id);
        $this->assertSame(50000, $streamer->unpaidOutDonations()->sum('amount'));
    }

    public function test_void_on_paid_payout_is_rejected(): void
    {
        $admin = $this->admin();
        $payout = Payout::factory()->create(['status' => 'paid', 'paid_at' => now(), 'reference' => 'REF']);

        $response = $this->actingAs($admin)->post("/admin/payouts/{$payout->id}/void");

        $response->assertSessionHasErrors();
        $payout->refresh();
        $this->assertSame('paid', $payout->status);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `./vendor/bin/phpunit tests/Feature/Payout/PayoutLifecycleTest.php`
Expected: FAIL — routes/methods don't exist yet.

- [ ] **Step 3: Add `markPaid()` and `void()` to `AdminPayoutController`**

```php
use App\Models\Donation;
use Illuminate\Http\Request;
```

```php
public function markPaid(Payout $payout, Request $request): RedirectResponse
{
    if ($payout->status !== 'pending') {
        return back()->withErrors(['payout' => 'Payout ini sudah diproses sebelumnya.']);
    }

    $validated = $request->validate([
        'reference' => ['required', 'string', 'max:100'],
    ]);

    $payout->update([
        'status' => 'paid',
        'reference' => $validated['reference'],
        'paid_at' => now(),
    ]);

    ActivityLog::log(
        action: 'payout.paid',
        description: "Payout #{$payout->id} ditandai sudah dibayar (ref: {$validated['reference']})",
        userId: Auth::id(),
        streamerId: $payout->streamer_id,
        payload: ['payout_id' => $payout->id],
    );

    return back()->with('success', 'Payout ditandai sudah dibayar.');
}

public function void(Payout $payout): RedirectResponse
{
    if ($payout->status !== 'pending') {
        return back()->withErrors(['payout' => 'Hanya payout berstatus pending yang bisa dibatalkan.']);
    }

    DB::transaction(function () use ($payout) {
        Donation::where('payout_id', $payout->id)->update(['payout_id' => null]);
        $payout->update(['status' => 'voided']);
    });

    ActivityLog::log(
        action: 'payout.voided',
        description: "Payout #{$payout->id} dibatalkan",
        userId: Auth::id(),
        streamerId: $payout->streamer_id,
        payload: ['payout_id' => $payout->id],
    );

    return back()->with('success', 'Payout dibatalkan, donasi dikembalikan ke saldo.');
}
```

- [ ] **Step 4: Register the routes**

In `routes/web.php`, directly after the `payouts.create` route added in Task 3:

```php
Route::post('/payouts/{payout}/mark-paid', [AdminPayoutController::class, 'markPaid'])
    ->middleware('throttle:admin-actions')
    ->name('payouts.mark-paid');
Route::post('/payouts/{payout}/void', [AdminPayoutController::class, 'void'])
    ->middleware('throttle:admin-actions')
    ->name('payouts.void');
```

- [ ] **Step 5: Run test to verify it passes**

Run: `./vendor/bin/phpunit tests/Feature/Payout/PayoutLifecycleTest.php`
Expected: PASS (4 tests)

- [ ] **Step 6: Run the full suite to check for regressions**

Run: `./vendor/bin/phpunit`

- [ ] **Step 7: Commit**

```bash
git add app/Http/Controllers/AdminPayoutController.php routes/web.php \
        tests/Feature/Payout/PayoutLifecycleTest.php
git commit -m "feat: add AdminPayoutController::markPaid()/void() — payout lifecycle"
```

---

### Task 5: Admin payout views (`index()`, `show()`)

**Files:**
- Modify: `app/Http/Controllers/AdminPayoutController.php`
- Modify: `routes/web.php`
- Create: `resources/views/admin/payouts.blade.php`
- Create: `resources/views/admin/payout-show.blade.php`
- Modify: `resources/views/layouts/app.blade.php` (nav link)
- Test: `tests/Feature/Payout/AdminPayoutViewsTest.php`

**Interfaces:**
- Consumes: `Payout` model + `Streamer` bank fields (Task 1), `AdminPayoutController::create/markPaid/void` (Tasks 3-4).

- [ ] **Step 1: Write the failing tests**

```php
<?php
// tests/Feature/Payout/AdminPayoutViewsTest.php
namespace Tests\Feature\Payout;

use App\Models\Donation;
use App\Models\Payout;
use App\Models\Streamer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPayoutViewsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        return $admin;
    }

    public function test_index_shows_streamer_owed_balance_and_existing_payouts(): void
    {
        $admin = $this->admin();
        $streamer = Streamer::factory()->create(['display_name' => 'Budi Streamer']);
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 75000]);
        $payout = Payout::factory()->for($streamer)->create(['net_amount' => 90000]);

        $response = $this->actingAs($admin)->get('/admin/payouts');

        $response->assertOk();
        $response->assertSee('Budi Streamer');
        $response->assertSee('75.000'); // owed balance
        $response->assertSee(route('admin.payouts.show', $payout));
    }

    public function test_show_displays_payout_detail_and_included_donations(): void
    {
        $admin = $this->admin();
        $streamer = Streamer::factory()->create();
        $payout = Payout::factory()->for($streamer)->create();
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'name' => 'Andi', 'payout_id' => $payout->id]);

        $response = $this->actingAs($admin)->get("/admin/payouts/{$payout->id}");

        $response->assertOk();
        $response->assertSee('Andi');
        $response->assertSee($payout->bank_account_number);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `./vendor/bin/phpunit tests/Feature/Payout/AdminPayoutViewsTest.php`
Expected: FAIL — `index()`/`show()` don't exist, routes 404.

- [ ] **Step 3: Add `index()` and `show()` to `AdminPayoutController`**

```php
use Illuminate\View\View;
```

```php
public function index(): View
{
    // SQL-level aggregation via withSum, not a per-streamer loop — matches the
    // existing convention in StreamerStatsService/AdminController::dashboard
    // (see CLAUDE.md: "all aggregation done in SQL, no in-memory get()").
    $streamers = Streamer::withSum(
        ['donations as owed_amount' => fn ($q) => $q->where('status', 'paid')->whereNull('payout_id')],
        'amount'
    )
        // whereHas (EXISTS), not having() on the withSum alias — SQLite rejects
        // a HAVING clause without a GROUP BY, which withSum's correlated
        // subquery column doesn't provide. Equivalent since amounts are always
        // positive: "at least one matching donation exists" == "sum > 0".
        ->whereHas('donations', fn ($q) => $q->where('status', 'paid')->whereNull('payout_id'))
        ->orderByDesc('owed_amount')
        ->get();

    $payouts = Payout::with('streamer')
        ->orderByDesc('created_at')
        ->limit(config('pagination.admin_payouts', 50))
        ->get();

    return view('admin.payouts', compact('streamers', 'payouts'));
}

public function show(Payout $payout): View
{
    $payout->load('streamer', 'donations', 'createdBy');

    return view('admin.payout-show', compact('payout'));
}
```

- [ ] **Step 4: Register the routes**

In `routes/web.php`, add `index`/`show` alongside the mutating routes from Tasks 3-4 (GET routes don't need the `admin-actions` throttle — matches the existing `donations`/`users` index pattern):

```php
Route::get('/payouts', [AdminPayoutController::class, 'index'])->name('payouts.index');
Route::get('/payouts/{payout}', [AdminPayoutController::class, 'show'])->name('payouts.show');
```

- [ ] **Step 5: Create `resources/views/admin/payouts.blade.php`**

Follow the existing `resources/views/admin/donations.blade.php` structure (`.page-container`/`.page-header`/`.table-card`/`table` — see that file for the exact classes already in use). Two sections:

```blade
@extends('layouts.app')
@section('content')
<div class="page-container">
    <div class="page-header">
        <div class="page-header-left">
            <h1 class="page-title">Payout Streamer</h1>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <div class="table-card">
        <h2 class="section-title">Saldo Belum Dicairkan</h2>
        <table>
            <thead>
                <tr>
                    <th>Streamer</th>
                    <th>Saldo Owed</th>
                    <th>Info Bank</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($streamers as $streamer)
                <tr>
                    <td>{{ $streamer->display_name }}</td>
                    <td class="amount-cell">Rp {{ number_format($streamer->owed_amount) }}</td>
                    <td>
                        @if($streamer->bank_account_number)
                            {{ $streamer->bank_name }} — {{ $streamer->bank_account_number }}
                        @else
                            <span style="color:var(--text-3)">Belum diisi</span>
                        @endif
                    </td>
                    <td>
                        @if($streamer->owed_amount >= config('payout.minimum_amount') && $streamer->bank_account_number)
                            <form method="POST" action="{{ route('admin.payouts.create', $streamer) }}"
                                onsubmit="return confirm('Buat payout Rp {{ number_format($streamer->owed_amount) }} untuk {{ addslashes($streamer->display_name) }}?')">
                                @csrf
                                <button type="submit" class="btn-xs">Buat Payout</button>
                            </form>
                        @else
                            <span style="color:var(--text-3); font-size:11px">Belum memenuhi syarat</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="empty-cell">Tidak ada saldo owed saat ini</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="table-card">
        <h2 class="section-title">Riwayat Payout</h2>
        <table>
            <thead>
                <tr>
                    <th>Streamer</th>
                    <th>Net Amount</th>
                    <th>Status</th>
                    <th>Dibuat</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($payouts as $p)
                <tr>
                    <td>{{ $p->streamer->display_name }}</td>
                    <td class="amount-cell">{{ $p->formatted_net_amount }}</td>
                    <td>{{ ucfirst($p->status) }}</td>
                    <td style="font-size:11px; color:var(--text-3)">{{ $p->created_at->format('d/m/Y H:i') }}</td>
                    <td><a href="{{ route('admin.payouts.show', $p) }}" class="btn-xs">Detail</a></td>
                </tr>
                @empty
                <tr><td colspan="5" class="empty-cell">Belum ada payout</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
```

- [ ] **Step 6: Create `resources/views/admin/payout-show.blade.php`**

```blade
@extends('layouts.app')
@section('content')
<div class="page-container">
    <div class="page-header">
        <h1 class="page-title">Payout #{{ $payout->id }} — {{ $payout->streamer->display_name }}</h1>
    </div>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert-error">{{ $errors->first() }}</div>
    @endif

    <div class="table-card">
        <p>Gross: Rp {{ number_format($payout->gross_amount) }}</p>
        <p>Fee ({{ config('payout.platform_fee_percent') }}%): Rp {{ number_format($payout->platform_fee_amount) }}</p>
        <p>Net: {{ $payout->formatted_net_amount }}</p>
        <p>Bank: {{ $payout->bank_name }} — {{ $payout->bank_account_number }} a.n. {{ $payout->bank_account_holder }}</p>
        <p>Status: {{ ucfirst($payout->status) }}</p>
        @if($payout->reference)
            <p>Referensi: {{ $payout->reference }}</p>
        @endif

        @if($payout->status === 'pending')
            <form method="POST" action="{{ route('admin.payouts.mark-paid', $payout) }}" style="margin-top:12px">
                @csrf
                <input type="text" name="reference" placeholder="Referensi transfer bank" required>
                <button type="submit" class="btn-xs">Tandai Sudah Dibayar</button>
            </form>
            <form method="POST" action="{{ route('admin.payouts.void', $payout) }}"
                onsubmit="return confirm('Batalkan payout ini? Donasi akan dikembalikan ke saldo owed.')" style="margin-top:8px">
                @csrf
                <button type="submit" class="btn-xs">Batalkan</button>
            </form>
        @endif
    </div>

    <div class="table-card">
        <h2 class="section-title">Donasi Termasuk</h2>
        <table>
            <thead><tr><th>Donatur</th><th>Nominal</th><th>Waktu</th></tr></thead>
            <tbody>
                @foreach($payout->donations as $d)
                <tr>
                    <td>{{ $d->name }}</td>
                    <td class="amount-cell">Rp {{ number_format($d->amount) }}</td>
                    <td style="font-size:11px; color:var(--text-3)">{{ $d->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
```

- [ ] **Step 7: Add a "Payouts" nav link**

The admin nav is inline in `resources/views/layouts/app.blade.php` (no separate partial — the `partials/` directory that exists is under `resources/views/streamer/`, unrelated to nav). In the `@if(auth()->user()->isAdmin())` block (around line 1502-1514), directly after the existing "Logs" `<a>` link:

```blade
<a href="{{ route('admin.payouts.index') }}" class="nav-link {{ request()->routeIs('admin.payouts*') ? 'active' : '' }}">
    <span class="iconify" data-icon="solar:banknote-2-bold-duotone"></span>Payout
</a>
```

- [ ] **Step 8: Run test to verify it passes**

Run: `./vendor/bin/phpunit tests/Feature/Payout/AdminPayoutViewsTest.php`
Expected: PASS (2 tests)

- [ ] **Step 9: Run the full suite to check for regressions**

Run: `./vendor/bin/phpunit`

- [ ] **Step 10: Commit**

```bash
git add app/Http/Controllers/AdminPayoutController.php routes/web.php \
        resources/views/admin/payouts.blade.php resources/views/admin/payout-show.blade.php \
        resources/views/admin/partials/ \
        tests/Feature/Payout/AdminPayoutViewsTest.php
git commit -m "feat: add admin payout list/detail views"
```

---

### Task 6: Streamer-facing payout history

**Files:**
- Modify: `app/Http/Controllers/StreamerDashboardController.php`
- Modify: `routes/web.php`
- Modify: `resources/views/layouts/app.blade.php` (nav link)
- Create: `resources/views/streamer/payouts.blade.php`
- Test: `tests/Feature/Payout/StreamerPayoutHistoryTest.php`

**Interfaces:**
- Consumes: `Streamer::payouts()` (Task 1).

- [ ] **Step 1: Write the failing test**

```php
<?php
// tests/Feature/Payout/StreamerPayoutHistoryTest.php
namespace Tests\Feature\Payout;

use App\Models\Payout;
use App\Models\Streamer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StreamerPayoutHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_streamer_only_sees_their_own_payouts(): void
    {
        $userA = User::factory()->create();
        $userA->forceFill(['role' => 'streamer'])->save();
        $streamerA = Streamer::factory()->create(['user_id' => $userA->id]);
        Payout::factory()->for($streamerA)->create(['net_amount' => 90000]);

        $streamerB = Streamer::factory()->create();
        Payout::factory()->for($streamerB)->create(['net_amount' => 500000]);

        $response = $this->actingAs($userA)->get('/streamer/payouts');

        $response->assertOk();
        $response->assertSee('90.000', false);
        $response->assertDontSee('500.000', false);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `./vendor/bin/phpunit tests/Feature/Payout/StreamerPayoutHistoryTest.php`
Expected: FAIL — route doesn't exist, 404.

- [ ] **Step 3: Add `payouts()` to `StreamerDashboardController`**

```php
public function payouts(): View|RedirectResponse
{
    $user = auth()->user();

    if (!$user->streamer) {
        return redirect()->route('streamer.setup');
    }

    $streamer = $user->streamer;
    $payouts = $streamer->payouts()->orderByDesc('created_at')->get();

    return view('streamer.payouts', compact('streamer', 'payouts'));
}
```

- [ ] **Step 4: Register the route**

In `routes/web.php`, inside the existing `streamer`-gated group (alongside `settings`/`reports`):

```php
Route::get('/payouts', [StreamerDashboardController::class, 'payouts'])->name('payouts');
```

- [ ] **Step 5: Create `resources/views/streamer/payouts.blade.php`**

```blade
@extends('layouts.app')
@section('content')
<div class="page-container">
    <div class="page-header">
        <h1 class="page-title">Riwayat Payout</h1>
    </div>

    <div class="table-card">
        <table>
            <thead><tr><th>Tanggal</th><th>Gross</th><th>Fee</th><th>Net</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($payouts as $p)
                <tr>
                    <td>{{ $p->created_at->format('d/m/Y') }}</td>
                    <td class="amount-cell">Rp {{ number_format($p->gross_amount) }}</td>
                    <td class="amount-cell">Rp {{ number_format($p->platform_fee_amount) }}</td>
                    <td class="amount-cell">{{ $p->formatted_net_amount }}</td>
                    <td>{{ ucfirst($p->status) }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="empty-cell">Belum ada payout</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
```

- [ ] **Step 6: Add a "Payout" nav link**

In `resources/views/layouts/app.blade.php`, in the `@elseif(auth()->user()->isStreamer())` block (around line 1515-1536), directly after the existing "Laporan" `<a>` link:

```blade
<a href="{{ route('streamer.payouts') }}" class="nav-link {{ request()->routeIs('streamer.payouts*') ? 'active' : '' }}">
    <span class="iconify" data-icon="solar:banknote-2-bold-duotone"></span>Payout
</a>
```

- [ ] **Step 7: Run test to verify it passes**

Run: `./vendor/bin/phpunit tests/Feature/Payout/StreamerPayoutHistoryTest.php`
Expected: PASS

- [ ] **Step 8: Run the full suite to check for regressions**

Run: `./vendor/bin/phpunit`

- [ ] **Step 9: Commit**

```bash
git add app/Http/Controllers/StreamerDashboardController.php routes/web.php \
        resources/views/layouts/app.blade.php resources/views/streamer/payouts.blade.php \
        tests/Feature/Payout/StreamerPayoutHistoryTest.php
git commit -m "feat: add streamer-facing payout history view"
```

---

## Post-plan note

Once merged, update `BACKLOG.md` item 2 ("Streamer payout / settlement") to reflect it's shipped — add a "Payout" subsection to `CLAUDE.md`'s architecture section (mirroring the "Payment" subsection added for the payment gateway), and add a new backlog item for "Automated Midtrans disbursement" per the spec's "Out of scope" section (introduces `PayoutGatewayInterface`, mirrors `PaymentGatewayInterface`'s shape).
