# Payment Gateway Integration (Midtrans Snap) Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make donations real — a `Donation` only credits an alert/milestone/subathon once Midtrans confirms payment via webhook, instead of trusting a donor-typed `amount` at form-submit time.

**Architecture:** A `PaymentGatewayInterface` wraps the official `midtrans/midtrans-php` SDK (Snap mode). `DonationController::store` creates a `pending` `Donation` and returns a Snap token; a new `PaymentWebhookController` verifies Midtrans's signed notification and is the *only* place that ever transitions a donation to `paid` and fires the alert/milestone/subathon side effects.

**Tech Stack:** Laravel 12, PHP 8.2+, `midtrans/midtrans-php` (Composer, official SDK), Midtrans Snap.js (frontend), PHPUnit 11 (existing suite, sqlite `:memory:`, `QUEUE_CONNECTION=sync`).

## Global Constraints

- Design source of truth: `docs/superpowers/specs/2026-07-25-payment-gateway-design.md` — every task below implements one section of it.
- User-facing strings are Indonesian, matching the rest of the codebase (see `CLAUDE.md`).
- `hash_equals()` (not `===`) for the signature comparison — this codebase already treats plain string comparison of secrets as a timing-attack bug (see `docs/gotchas.md`).
- No real HTTP calls to Midtrans anywhere in the test suite — every test binds a fake gateway.
- Donation persistence is never rolled back due to payment/alert-delivery problems — same "donation is never lost" guarantee `CLAUDE.md` already documents for the pre-existing alert pipeline, just extended to cover payment state too.
- Every new migration is additive (no dropped/renamed columns on `donations`).

---

### Task 1: Data model — `donations` payment columns, model scopes, test factories

**Files:**
- Create: `database/migrations/2026_07_25_150000_add_payment_fields_to_donations_table.php`
- Modify: `app/Models/Donation.php`
- Modify: `app/Models/Streamer.php`
- Create: `database/factories/StreamerFactory.php`
- Create: `database/factories/DonationFactory.php`
- Test: `tests/Feature/Donation/DonationPaymentStatusTest.php`

**Interfaces:**
- Produces: `Donation::scopePaid($query)` (usable as `Donation::paid()->...`), `Streamer::paidDonations(): HasMany`, `Donation` fillable/cast additions (`status`, `payment_reference`, `payment_type`, `paid_at`), `StreamerFactory`, `DonationFactory` (default state `status = 'pending'`).

No existing `Streamer`/`Donation` factories exist in this codebase yet (only `UserFactory`) — every later task's tests depend on these two, so they're built here first.

- [ ] **Step 1: Write the failing test**

```php
<?php
// tests/Feature/Donation/DonationPaymentStatusTest.php
namespace Tests\Feature\Donation;

use App\Models\Donation;
use App\Models\Streamer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationPaymentStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_donation_defaults_to_pending_status(): void
    {
        $donation = Donation::factory()->create();

        $this->assertSame('pending', $donation->status);
    }

    public function test_paid_scope_only_returns_paid_donations(): void
    {
        $streamer = Streamer::factory()->create();
        Donation::factory()->for($streamer)->create(['status' => 'pending']);
        $paid = Donation::factory()->for($streamer)->create(['status' => 'paid']);

        $result = Donation::paid()->get();

        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($paid));
    }

    public function test_streamer_paid_donations_relation_filters_by_status(): void
    {
        $streamer = Streamer::factory()->create();
        Donation::factory()->for($streamer)->create(['status' => 'failed']);
        $paid = Donation::factory()->for($streamer)->create(['status' => 'paid']);

        $result = $streamer->paidDonations()->get();

        $this->assertCount(1, $result);
        $this->assertTrue($result->first()->is($paid));
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=DonationPaymentStatusTest`
Expected: FAIL — `status` column/factories don't exist yet (`Class "Database\Factories\DonationFactory" not found` or missing column error).

- [ ] **Step 3: Create the migration**

```php
<?php
// database/migrations/2026_07_25_150000_add_payment_fields_to_donations_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->string('status', 20)->default('pending')->after('ip_address')
                ->comment('pending | paid | failed | expired');
            $table->string('payment_reference')->nullable()->unique()->after('status')
                ->comment('Midtrans order_id, format TRX-{donation_id}');
            $table->string('payment_type', 40)->nullable()->after('payment_reference')
                ->comment('qris, gopay, bank_transfer, dll — dari webhook');
            $table->timestamp('paid_at')->nullable()->after('payment_type');
        });
    }

    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn(['status', 'payment_reference', 'payment_type', 'paid_at']);
        });
    }
};
```

(`unique()` already creates an index on `payment_reference` — no separate `->index()` call needed.)

- [ ] **Step 4: Update the `Donation` model**

In `app/Models/Donation.php`, extend `$fillable` and casts, and add the scope:

```php
protected $fillable = [
    'streamer_id',
    'milestone_id',
    'name',
    'amount',
    'emoji',
    'message',
    'yt_url',
    'media_path',
    'ip_address',
    'status',
    'payment_reference',
    'payment_type',
    'paid_at',
];
```

```php
protected function casts(): array
{
    return [
        'amount' => 'integer',
        'paid_at' => 'datetime',
    ];
}
```

Add this method (anywhere among the other public methods):

```php
public function scopePaid($query)
{
    return $query->where('status', 'paid');
}
```

- [ ] **Step 5: Add `Streamer::paidDonations()`**

In `app/Models/Streamer.php`, directly after the existing `donations(): HasMany` method:

```php
/**
 * Sama seperti donations(), tapi hanya donasi yang sudah dibayar.
 * Gunakan ini (bukan donations()) untuk semua statistik publik/real-time —
 * lihat docs/gotchas.md soal kenapa donasi pending tidak boleh terlihat.
 */
public function paidDonations(): HasMany
{
    return $this->donations()->where('status', 'paid');
}
```

- [ ] **Step 6: Create `StreamerFactory`**

```php
<?php
// database/factories/StreamerFactory.php
namespace Database\Factories;

use App\Models\Streamer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Streamer>
 */
class StreamerFactory extends Factory
{
    protected $model = Streamer::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'slug' => fake()->unique()->slug(2),
            'display_name' => fake()->name(),
            'api_key' => Streamer::generateApiKey(),
            'min_donation' => 1000,
            'is_accepting_donation' => true,
            'thank_you_message' => 'Terima kasih atas donasi kamu!',
        ];
    }
}
```

- [ ] **Step 7: Create `DonationFactory`**

```php
<?php
// database/factories/DonationFactory.php
namespace Database\Factories;

use App\Models\Donation;
use App\Models\Streamer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Donation>
 */
class DonationFactory extends Factory
{
    protected $model = Donation::class;

    public function definition(): array
    {
        return [
            'streamer_id' => Streamer::factory(),
            'name' => fake()->firstName(),
            'amount' => fake()->randomElement([5000, 10000, 25000, 50000]),
            'emoji' => '💝',
            'status' => 'pending',
            'ip_address' => '127.0.0.1',
        ];
    }
}
```

- [ ] **Step 8: Run test to verify it passes**

Run: `php artisan test --filter=DonationPaymentStatusTest`
Expected: PASS (3 tests)

- [ ] **Step 9: Commit**

```bash
git add database/migrations/2026_07_25_150000_add_payment_fields_to_donations_table.php \
        database/factories/StreamerFactory.php database/factories/DonationFactory.php \
        app/Models/Donation.php app/Models/Streamer.php \
        tests/Feature/Donation/DonationPaymentStatusTest.php
git commit -m "feat: add payment status columns to donations + paid-only scope/relation"
```

---

### Task 2: Payment gateway service layer — interface, DTOs, `MidtransSnapGateway`

**Files:**
- Modify: `composer.json` (via `composer require`)
- Create: `config/midtrans.php`
- Modify: `.env.example`
- Create: `app/Services/Payment/PaymentGatewayInterface.php`
- Create: `app/Services/Payment/PaymentTransaction.php`
- Create: `app/Services/Payment/PaymentNotification.php`
- Create: `app/Services/Payment/InvalidPaymentSignatureException.php`
- Create: `app/Services/Payment/MidtransSnapGateway.php`
- Test: `tests/Unit/Services/Payment/MidtransSnapGatewayTest.php`

**Interfaces:**
- Consumes: `App\Models\Donation` (Task 1).
- Produces: `PaymentGatewayInterface::createTransaction(Donation $donation): PaymentTransaction`, `PaymentGatewayInterface::verifyNotification(array $payload): PaymentNotification`. `PaymentTransaction` has public readonly `$token: string`, `$orderId: string`. `PaymentNotification` has public readonly `$orderId: string`, `$status: string` (`paid`|`failed`|`expired`|`pending`), `$paymentType: ?string`.

The signature-verification math is the one part of this task with real logic worth unit-testing (easy to get subtly wrong — field order, wrong hash algo). `createTransaction()` calls a real static SDK method (`Snap::getSnapToken`) that hits Midtrans's API — there is no way to unit-test it without a live sandbox call, so this task does **not** claim automated coverage for it; that's verified manually once Task 4 wires it into the donation form (see Task 4's manual note) using real Midtrans sandbox credentials.

- [ ] **Step 1: Install the SDK**

Run: `composer require midtrans/midtrans-php`
Expected: `composer.json` gains `"midtrans/midtrans-php": "*"` under `require` (matching the existing wildcard-version style already used for `barryvdh/laravel-dompdf`, `james-heinrich/getid3`, `simplesoftwareio/simple-qrcode` in this repo).

- [ ] **Step 2: Add config and env vars**

```php
<?php
// config/midtrans.php
return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),

    /*
    | Custom Snap transaction expiry. Kept short (default 60 min) rather than
    | Midtrans's own 24h default, since a stream donation is time-sensitive.
    */
    'snap_expiry_minutes' => env('MIDTRANS_SNAP_EXPIRY_MINUTES', 60),
];
```

Append to `.env.example`:
```
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_SNAP_EXPIRY_MINUTES=60
```

- [ ] **Step 3: Write the failing test**

```php
<?php
// tests/Unit/Services/Payment/MidtransSnapGatewayTest.php
namespace Tests\Unit\Services\Payment;

use App\Services\Payment\InvalidPaymentSignatureException;
use App\Services\Payment\MidtransSnapGateway;
use Tests\TestCase;

class MidtransSnapGatewayTest extends TestCase
{
    public function test_verify_notification_accepts_valid_signature_and_maps_settlement_to_paid(): void
    {
        config(['midtrans.server_key' => 'SB-Mid-server-test123']);
        $gateway = new MidtransSnapGateway();

        $orderId = 'TRX-1';
        $statusCode = '200';
        $grossAmount = '10000.00';
        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . 'SB-Mid-server-test123');

        $notification = $gateway->verifyNotification([
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signature,
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
        ]);

        $this->assertSame('paid', $notification->status);
        $this->assertSame($orderId, $notification->orderId);
        $this->assertSame('qris', $notification->paymentType);
    }

    public function test_verify_notification_rejects_tampered_signature(): void
    {
        config(['midtrans.server_key' => 'SB-Mid-server-test123']);
        $gateway = new MidtransSnapGateway();

        $this->expectException(InvalidPaymentSignatureException::class);

        $gateway->verifyNotification([
            'order_id' => 'TRX-1',
            'status_code' => '200',
            'gross_amount' => '10000.00',
            'signature_key' => 'not-the-real-signature',
            'transaction_status' => 'settlement',
        ]);
    }

    public function test_capture_with_challenge_fraud_status_stays_pending(): void
    {
        config(['midtrans.server_key' => 'SB-Mid-server-test123']);
        $gateway = new MidtransSnapGateway();

        $orderId = 'TRX-2';
        $statusCode = '200';
        $grossAmount = '10000.00';
        $signature = hash('sha512', $orderId . $statusCode . $grossAmount . 'SB-Mid-server-test123');

        $notification = $gateway->verifyNotification([
            'order_id' => $orderId,
            'status_code' => $statusCode,
            'gross_amount' => $grossAmount,
            'signature_key' => $signature,
            'transaction_status' => 'capture',
            'fraud_status' => 'challenge',
        ]);

        $this->assertSame('pending', $notification->status);
    }

    public function test_deny_maps_to_failed_and_expire_maps_to_expired(): void
    {
        config(['midtrans.server_key' => 'SB-Mid-server-test123']);
        $gateway = new MidtransSnapGateway();

        $sign = fn (string $orderId, string $statusCode, string $grossAmount) =>
            hash('sha512', $orderId . $statusCode . $grossAmount . 'SB-Mid-server-test123');

        $deny = $gateway->verifyNotification([
            'order_id' => 'TRX-3', 'status_code' => '202', 'gross_amount' => '10000.00',
            'signature_key' => $sign('TRX-3', '202', '10000.00'),
            'transaction_status' => 'deny',
        ]);
        $this->assertSame('failed', $deny->status);

        $expire = $gateway->verifyNotification([
            'order_id' => 'TRX-4', 'status_code' => '407', 'gross_amount' => '10000.00',
            'signature_key' => $sign('TRX-4', '407', '10000.00'),
            'transaction_status' => 'expire',
        ]);
        $this->assertSame('expired', $expire->status);
    }
}
```

- [ ] **Step 4: Run test to verify it fails**

Run: `php artisan test --filter=MidtransSnapGatewayTest`
Expected: FAIL — classes don't exist yet.

- [ ] **Step 5: Create the DTOs and exception**

```php
<?php
// app/Services/Payment/PaymentTransaction.php
namespace App\Services\Payment;

final class PaymentTransaction
{
    public function __construct(
        public readonly string $token,
        public readonly string $orderId,
    ) {}
}
```

```php
<?php
// app/Services/Payment/PaymentNotification.php
namespace App\Services\Payment;

final class PaymentNotification
{
    public function __construct(
        public readonly string $orderId,
        public readonly string $status,
        public readonly ?string $paymentType,
    ) {}
}
```

```php
<?php
// app/Services/Payment/InvalidPaymentSignatureException.php
namespace App\Services\Payment;

use RuntimeException;

class InvalidPaymentSignatureException extends RuntimeException
{
}
```

- [ ] **Step 6: Create the interface**

```php
<?php
// app/Services/Payment/PaymentGatewayInterface.php
namespace App\Services\Payment;

use App\Models\Donation;

interface PaymentGatewayInterface
{
    public function createTransaction(Donation $donation): PaymentTransaction;

    public function verifyNotification(array $payload): PaymentNotification;
}
```

- [ ] **Step 7: Implement `MidtransSnapGateway`**

```php
<?php
// app/Services/Payment/MidtransSnapGateway.php
namespace App\Services\Payment;

use App\Models\Donation;
use Midtrans\Config;
use Midtrans\Snap;

class MidtransSnapGateway implements PaymentGatewayInterface
{
    public function createTransaction(Donation $donation): PaymentTransaction
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = (bool) config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $orderId = "TRX-{$donation->id}";

        $token = Snap::getSnapToken([
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $donation->amount,
            ],
            'customer_details' => [
                'first_name' => $donation->name,
            ],
            'expiry' => [
                'unit' => 'minutes',
                'value' => (int) config('midtrans.snap_expiry_minutes', 60),
            ],
        ]);

        return new PaymentTransaction(token: $token, orderId: $orderId);
    }

    public function verifyNotification(array $payload): PaymentNotification
    {
        $orderId = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $receivedSignature = (string) ($payload['signature_key'] ?? '');

        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . config('midtrans.server_key'));

        if (!hash_equals($expectedSignature, $receivedSignature)) {
            throw new InvalidPaymentSignatureException('Signature Midtrans tidak valid.');
        }

        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        $status = match (true) {
            $transactionStatus === 'settlement' => 'paid',
            $transactionStatus === 'capture' && $fraudStatus === 'accept' => 'paid',
            $transactionStatus === 'capture' && $fraudStatus === 'challenge' => 'pending',
            in_array($transactionStatus, ['deny', 'cancel'], true) => 'failed',
            $transactionStatus === 'expire' => 'expired',
            default => 'pending',
        };

        return new PaymentNotification(
            orderId: $orderId,
            status: $status,
            paymentType: $payload['payment_type'] ?? null,
        );
    }
}
```

- [ ] **Step 8: Run test to verify it passes**

Run: `php artisan test --filter=MidtransSnapGatewayTest`
Expected: PASS (4 tests)

- [ ] **Step 9: Commit**

```bash
git add composer.json composer.lock config/midtrans.php .env.example \
        app/Services/Payment/ tests/Unit/Services/Payment/MidtransSnapGatewayTest.php
git commit -m "feat: add PaymentGatewayInterface + MidtransSnapGateway with signature verification"
```

---

### Task 3: Fake gateway for tests + service binding

**Files:**
- Create: `tests/Support/FakePaymentGateway.php`
- Modify: `app/Providers/AppServiceProvider.php`
- Modify: `tests/TestCase.php`
- Test: `tests/Unit/PaymentGatewayBindingTest.php`

**Interfaces:**
- Consumes: `PaymentGatewayInterface`, `PaymentTransaction`, `PaymentNotification` (Task 2).
- Produces: `FakePaymentGateway` with a public `$shouldThrowOnCreate: bool` toggle (used by Task 4's failure test) — `createTransaction()` returns a canned token, `verifyNotification()` trusts the payload's own `status`/`payment_type` fields directly (no signature math — that's already covered by the real gateway's unit tests in Task 2, so every later feature test can post plain `{order_id, status, payment_type}` payloads).

- [ ] **Step 1: Write the failing test**

```php
<?php
// tests/Unit/PaymentGatewayBindingTest.php
namespace Tests\Unit;

use App\Services\Payment\PaymentGatewayInterface;
use Tests\Support\FakePaymentGateway;
use Tests\TestCase;

class PaymentGatewayBindingTest extends TestCase
{
    public function test_fake_gateway_is_bound_during_tests(): void
    {
        $gateway = $this->app->make(PaymentGatewayInterface::class);

        $this->assertInstanceOf(FakePaymentGateway::class, $gateway);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=PaymentGatewayBindingTest`
Expected: FAIL — `FakePaymentGateway` class doesn't exist, nothing bound yet.

- [ ] **Step 3: Create `FakePaymentGateway`**

```php
<?php
// tests/Support/FakePaymentGateway.php
namespace Tests\Support;

use App\Models\Donation;
use App\Services\Payment\PaymentGatewayInterface;
use App\Services\Payment\PaymentNotification;
use App\Services\Payment\PaymentTransaction;
use RuntimeException;

class FakePaymentGateway implements PaymentGatewayInterface
{
    public bool $shouldThrowOnCreate = false;

    public function createTransaction(Donation $donation): PaymentTransaction
    {
        if ($this->shouldThrowOnCreate) {
            throw new RuntimeException('Fake gateway: simulated createTransaction failure.');
        }

        return new PaymentTransaction(
            token: "fake-snap-token-{$donation->id}",
            orderId: "TRX-{$donation->id}",
        );
    }

    public function verifyNotification(array $payload): PaymentNotification
    {
        return new PaymentNotification(
            orderId: (string) $payload['order_id'],
            status: (string) $payload['status'],
            paymentType: $payload['payment_type'] ?? null,
        );
    }
}
```

- [ ] **Step 4: Bind the real gateway in `AppServiceProvider`**

In `app/Providers/AppServiceProvider.php`, inside `register()`:

```php
public function register(): void
{
    $this->app->bind(
        \App\Services\Payment\PaymentGatewayInterface::class,
        \App\Services\Payment\MidtransSnapGateway::class
    );
}
```

- [ ] **Step 5: Override the binding in `TestCase`**

```php
<?php
// tests/TestCase.php
namespace Tests;

use App\Services\Payment\PaymentGatewayInterface;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Tests\Support\FakePaymentGateway;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // singleton, not bind() — Task 4's failure test needs to mutate the
        // SAME instance the controller later resolves via constructor injection.
        $this->app->singleton(PaymentGatewayInterface::class, FakePaymentGateway::class);
    }
}
```

- [ ] **Step 6: Run test to verify it passes**

Run: `php artisan test --filter=PaymentGatewayBindingTest`
Expected: PASS

- [ ] **Step 7: Commit**

```bash
git add tests/Support/FakePaymentGateway.php app/Providers/AppServiceProvider.php \
        tests/TestCase.php tests/Unit/PaymentGatewayBindingTest.php
git commit -m "test: bind fake payment gateway for the test suite"
```

---

### Task 4: `DonationController::store` — create pending donation, start Snap transaction

**Files:**
- Modify: `app/Http/Controllers/DonationController.php`
- Test: `tests/Feature/Donation/DonationStoreTest.php`

**Interfaces:**
- Consumes: `PaymentGatewayInterface` (Task 2/3), `Donation::$fillable` incl. `status`/`payment_reference` (Task 1).
- Produces: `POST /{slug}/donate` now returns `{success: true, data: {donation_id, snap_token}}` on success (previously `{success, message, data: {id}}`) — **this is a breaking response-shape change**, Task 6 updates the one frontend consumer of this response.

This task removes the `ProcessDonationJob::dispatchSync`, milestone `addAmount`, and subathon `addSubathonTime` calls from `store()` entirely — they move to `PaymentWebhookController` in Task 5. Until Task 5 lands, a real donation submitted through the running app will create a `pending` row and a Snap token but never actually fire an alert — that's expected mid-plan state, not a regression to "fix" here.

- [ ] **Step 1: Write the failing tests**

```php
<?php
// tests/Feature/Donation/DonationStoreTest.php
namespace Tests\Feature\Donation;

use App\Models\AlertQueue;
use App\Models\Donation;
use App\Models\Streamer;
use App\Services\Payment\PaymentGatewayInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationStoreTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_submission_creates_pending_donation_and_returns_snap_token(): void
    {
        $streamer = Streamer::factory()->create();

        $response = $this->postJson("/{$streamer->slug}/donate", [
            'name' => 'Budi',
            'amount' => 20000,
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $response->assertJsonStructure(['data' => ['donation_id', 'snap_token']]);

        $donation = Donation::firstOrFail();
        $this->assertSame('pending', $donation->status);
        $this->assertSame("TRX-{$donation->id}", $donation->payment_reference);
        $this->assertSame(0, AlertQueue::count());
    }

    public function test_gateway_failure_returns_error_and_leaves_donation_pending(): void
    {
        $streamer = Streamer::factory()->create();

        $fake = $this->app->make(PaymentGatewayInterface::class);
        $fake->shouldThrowOnCreate = true;

        $response = $this->postJson("/{$streamer->slug}/donate", [
            'name' => 'Budi',
            'amount' => 20000,
        ]);

        $response->assertStatus(502);
        $response->assertJson(['success' => false]);

        $donation = Donation::firstOrFail();
        $this->assertSame('pending', $donation->status);
    }
}
```

(Different `Streamer::factory()` per test gives each a unique `slug`, so the `throttle:donate` limiter — keyed by `ip + slug` — never trips between these two tests.)

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=DonationStoreTest`
Expected: FAIL — response still has the old shape (`data.id` not `data.donation_id`/`data.snap_token`), milestone/subathon/alert logic still runs inline.

- [ ] **Step 3: Rewrite the tail of `DonationController::store`**

In `app/Http/Controllers/DonationController.php`, add the import and constructor:

```php
use App\Services\Payment\PaymentGatewayInterface;
```

```php
class DonationController extends Controller
{
    public function __construct(
        private readonly PaymentGatewayInterface $paymentGateway
    ) {}

    // ... show() unchanged ...
```

Replace everything from `// ── Simpan donasi ke DB ──` through the final `return response()->json([...'success' => true...])` with:

```php
        // ── Simpan donasi (status pending) ke DB ──
        try {
            $donation = Donation::create([
                'streamer_id' => $streamer->id,
                'milestone_id' => $validated['milestone_id'] ?? null,
                'name'        => $name,
                'amount'      => (int) $validated['amount'],
                'emoji'       => $emoji,
                'message'     => $msg,
                'yt_url'      => $ytUrl,
                'media_path'  => $mediaPath,
                'ip_address'  => $request->ip(),
                'status'      => 'pending',
            ]);
        } catch (\Throwable $e) {
            if ($mediaPath) {
                Storage::disk('public')->delete($mediaPath);
            }

            Log::error('DonationController: gagal menyimpan donasi', [
                'streamer_id' => $streamer->id,
                'error'       => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses donasi saat ini. Mohon coba beberapa saat lagi.',
            ], 500);
        }

        // ── Mulai transaksi pembayaran via Midtrans Snap ──
        // Alert, milestone, dan subathon TIDAK diproses di sini — hanya setelah
        // PaymentWebhookController mengonfirmasi pembayaran berhasil.
        try {
            $transaction = $this->paymentGateway->createTransaction($donation);
            $donation->update(['payment_reference' => $transaction->orderId]);
        } catch (\Throwable $e) {
            Log::error('DonationController: gagal membuat transaksi pembayaran', [
                'donation_id' => $donation->id,
                'error'       => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memulai pembayaran. Mohon coba lagi.',
            ], 502);
        }

        ActivityLog::log(
            action: 'donation.pending',
            description: "{$name} memulai pembayaran Rp " . number_format($donation->amount, 0, ',', '.'),
            streamerId: $streamer->id,
            payload: ['donation_id' => $donation->id],
        );

        return response()->json([
            'success' => true,
            'data' => [
                'donation_id' => $donation->id,
                'snap_token'  => $transaction->token,
            ],
        ]);
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=DonationStoreTest`
Expected: PASS (2 tests)

- [ ] **Step 5: Run the full suite to check for regressions**

Run: `php artisan test`
Expected: all previously-passing tests still pass (no other test exercises `DonationController::store` yet).

- [ ] **Step 6: Commit**

```bash
git add app/Http/Controllers/DonationController.php tests/Feature/Donation/DonationStoreTest.php
git commit -m "feat: DonationController creates pending donation and starts Snap transaction"
```

---

### Task 5: `PaymentWebhookController` — verify, credit, idempotent

**Files:**
- Create: `app/Http/Controllers/PaymentWebhookController.php`
- Modify: `routes/web.php`
- Modify: `bootstrap/app.php`
- Modify: `app/Providers/AppServiceProvider.php`
- Test: `tests/Feature/Donation/PaymentWebhookTest.php`

**Interfaces:**
- Consumes: `PaymentGatewayInterface::verifyNotification()` (Task 2/3), `Donation::status`/`payment_reference` (Task 1), `Milestone::addAmount(int $amount): void`, `Streamer::addSubathonTime(int $amount): array`, `ProcessDonationJob::dispatchSync(Donation $donation)` (all pre-existing, unchanged).
- Produces: `POST /webhooks/midtrans` — `200` on processed/already-processed, `403` on bad signature, `404` on unknown `order_id`.

- [ ] **Step 1: Write the failing tests**

```php
<?php
// tests/Feature/Donation/PaymentWebhookTest.php
namespace Tests\Feature\Donation;

use App\Models\AlertQueue;
use App\Models\Donation;
use App\Models\Streamer;
use App\Services\Payment\MidtransSnapGateway;
use App\Services\Payment\PaymentGatewayInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_paid_notification_marks_donation_paid_and_queues_alert(): void
    {
        $streamer = Streamer::factory()->create();
        $donation = Donation::factory()->for($streamer)->create([
            'status' => 'pending',
            'payment_reference' => 'TRX-1',
        ]);

        $response = $this->postJson('/webhooks/midtrans', [
            'order_id' => 'TRX-1',
            'status' => 'paid',
            'payment_type' => 'qris',
        ]);

        $response->assertOk();
        $donation->refresh();
        $this->assertSame('paid', $donation->status);
        $this->assertSame('qris', $donation->payment_type);
        $this->assertNotNull($donation->paid_at);
        $this->assertSame(1, AlertQueue::where('donation_id', $donation->id)->count());
    }

    public function test_duplicate_paid_notification_does_not_double_credit(): void
    {
        $streamer = Streamer::factory()->create();
        Donation::factory()->for($streamer)->create([
            'status' => 'pending',
            'payment_reference' => 'TRX-2',
        ]);

        $payload = ['order_id' => 'TRX-2', 'status' => 'paid', 'payment_type' => 'qris'];

        $this->postJson('/webhooks/midtrans', $payload)->assertOk();
        $this->postJson('/webhooks/midtrans', $payload)->assertOk();

        $this->assertSame(1, AlertQueue::count());
    }

    public function test_failed_notification_marks_failed_without_alert(): void
    {
        $streamer = Streamer::factory()->create();
        $donation = Donation::factory()->for($streamer)->create([
            'status' => 'pending',
            'payment_reference' => 'TRX-3',
        ]);

        $this->postJson('/webhooks/midtrans', [
            'order_id' => 'TRX-3',
            'status' => 'failed',
        ])->assertOk();

        $donation->refresh();
        $this->assertSame('failed', $donation->status);
        $this->assertSame(0, AlertQueue::count());
    }

    public function test_unknown_order_id_returns_404(): void
    {
        $this->postJson('/webhooks/midtrans', [
            'order_id' => 'TRX-does-not-exist',
            'status' => 'paid',
        ])->assertStatus(404);
    }

    public function test_invalid_signature_is_rejected_with_403(): void
    {
        $this->app->bind(PaymentGatewayInterface::class, MidtransSnapGateway::class);
        config(['midtrans.server_key' => 'test-server-key']);

        $response = $this->postJson('/webhooks/midtrans', [
            'order_id' => 'TRX-999',
            'status_code' => '200',
            'gross_amount' => '10000.00',
            'signature_key' => 'not-a-real-signature',
            'transaction_status' => 'settlement',
        ]);

        $response->assertStatus(403);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=PaymentWebhookTest`
Expected: FAIL — route `webhooks/midtrans` doesn't exist (404 for every test, including the ones expecting 200/403).

- [ ] **Step 3: Create `PaymentWebhookController`**

```php
<?php
// app/Http/Controllers/PaymentWebhookController.php
namespace App\Http\Controllers;

use App\Jobs\ProcessDonationJob;
use App\Models\ActivityLog;
use App\Models\Donation;
use App\Models\Milestone;
use App\Services\Payment\InvalidPaymentSignatureException;
use App\Services\Payment\PaymentGatewayInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function __construct(
        private readonly PaymentGatewayInterface $paymentGateway
    ) {}

    public function handle(Request $request): JsonResponse
    {
        try {
            $notification = $this->paymentGateway->verifyNotification($request->all());
        } catch (InvalidPaymentSignatureException $e) {
            Log::warning('PaymentWebhookController: signature tidak valid', ['payload' => $request->all()]);

            return response()->json(['message' => 'Signature tidak valid.'], 403);
        }

        $donation = Donation::where('payment_reference', $notification->orderId)->first();

        if (!$donation) {
            Log::warning('PaymentWebhookController: order_id tidak ditemukan', [
                'order_id' => $notification->orderId,
            ]);

            return response()->json(['message' => 'Donasi tidak ditemukan.'], 404);
        }

        // Idempotency gate: only the first notification to see this row as
        // "pending" gets to act on it. A retried/duplicate notification (or one
        // arriving after the row already resolved) is a no-op.
        $affected = Donation::where('id', $donation->id)
            ->where('status', 'pending')
            ->update([
                'status'       => $notification->status,
                'payment_type' => $notification->paymentType,
                'paid_at'      => $notification->status === 'paid' ? now() : null,
            ]);

        if ($affected === 0) {
            return response()->json(['message' => 'Sudah diproses.'], 200);
        }

        $donation->refresh();

        if ($notification->status === 'paid') {
            $this->creditDonation($donation);
        } elseif (in_array($notification->status, ['failed', 'expired'], true)) {
            ActivityLog::log(
                action: "donation.{$notification->status}",
                description: "Pembayaran donasi #{$donation->id} berstatus {$notification->status}",
                streamerId: $donation->streamer_id,
                payload: ['donation_id' => $donation->id],
            );
        }
        // status === 'pending' (fraud-review "challenge" case): tidak ada aksi,
        // tunggu notifikasi Midtrans berikutnya.

        return response()->json(['message' => 'OK'], 200);
    }

    /**
     * Satu-satunya titik yang meng-kreditkan donasi setelah pembayaran
     * dikonfirmasi: milestone, subathon, alert queue, dan activity log.
     */
    private function creditDonation(Donation $donation): void
    {
        $streamer = $donation->streamer;

        if ($donation->milestone_id) {
            $milestone = Milestone::find($donation->milestone_id);
            if ($milestone && $milestone->streamer_id === $streamer->id) {
                $milestone->addAmount($donation->amount);
            }
        }

        if ($streamer->subathon_enabled) {
            $streamer->addSubathonTime($donation->amount);
        }

        $alertQueued = true;
        try {
            ProcessDonationJob::dispatchSync($donation);
        } catch (\Throwable $e) {
            $alertQueued = false;

            Log::error('PaymentWebhookController: ProcessDonationJob sync gagal, fallback ke queue', [
                'donation_id' => $donation->id,
                'error'       => $e->getMessage(),
            ]);

            try {
                ProcessDonationJob::dispatch($donation)->delay(now()->addSeconds(5));
            } catch (\Throwable $queueError) {
                Log::critical('PaymentWebhookController: fallback queue juga gagal', [
                    'donation_id' => $donation->id,
                    'error'       => $queueError->getMessage(),
                ]);
            }
        }

        ActivityLog::log(
            action: 'donation.paid',
            description: "{$donation->name} berhasil membayar donasi Rp " . number_format($donation->amount, 0, ',', '.'),
            streamerId: $streamer->id,
            payload: ['donation_id' => $donation->id, 'alert_queued' => $alertQueued],
        );
    }
}
```

- [ ] **Step 4: Register the route**

In `routes/web.php`, add a new section directly after the existing `Public Routes — Donasi` block (still before the `Public Slug Routes — HARUS di paling bawah` wildcard section):

```php
use App\Http\Controllers\PaymentWebhookController;
```

```php
/*
|--------------------------------------------------------------------------
| Payment Webhooks
|--------------------------------------------------------------------------
*/

Route::post('/webhooks/midtrans', [PaymentWebhookController::class, 'handle'])
    ->middleware('throttle:payment-webhook')
    ->name('webhooks.midtrans');
```

- [ ] **Step 5: Exempt the route from CSRF verification**

In `bootstrap/app.php`, inside the existing `->withMiddleware(function (Middleware $middleware): void { ... })` closure (after the `$middleware->web(append: [...])` call), add:

```php
$middleware->validateCsrfTokens(except: [
    'webhooks/midtrans',
]);
```

- [ ] **Step 6: Register the rate limiter**

In `app/Providers/AppServiceProvider.php`, inside `boot()`, alongside the other `RateLimiter::for(...)` registrations:

```php
// Rate-limit Midtrans payment webhook.
// The real protection is signature verification in PaymentWebhookController;
// this just caps abuse volume on a public unauthenticated endpoint.
RateLimiter::for('payment-webhook', function (Request $request) {
    return Limit::perMinute(120)->by($request->ip());
});
```

- [ ] **Step 7: Run test to verify it passes**

Run: `php artisan test --filter=PaymentWebhookTest`
Expected: PASS (5 tests)

- [ ] **Step 8: Run the full suite to check for regressions**

Run: `php artisan test`
Expected: all tests pass, including Task 4's `DonationStoreTest` (unaffected by this task).

- [ ] **Step 9: Commit**

```bash
git add app/Http/Controllers/PaymentWebhookController.php routes/web.php bootstrap/app.php \
        app/Providers/AppServiceProvider.php tests/Feature/Donation/PaymentWebhookTest.php
git commit -m "feat: add Midtrans payment webhook — verifies signature, credits donation idempotently"
```

---

### Task 6: Frontend — Snap.js popup on the donation form

**Files:**
- Modify: `resources/views/donate/show.blade.php`

**Interfaces:**
- Consumes: the new `POST /{slug}/donate` response shape from Task 4 (`data.snap_token`), `config('midtrans.client_key')`, `config('midtrans.is_production')`.

This project has no frontend test runner (`package.json` only has `vite`/`tailwindcss`, no Jest/Vitest/Dusk) — verification for this task is manual against a real Midtrans **sandbox** account, not an automated test. Don't invent a fake automated check; follow the manual checklist in Step 4 instead.

- [ ] **Step 1: Add the Snap.js script tag**

In `resources/views/donate/show.blade.php`, in the `<head>` section (near the existing meta/CSRF tags), add:

```blade
<script
    src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
    data-client-key="{{ config('midtrans.client_key') }}"></script>
```

- [ ] **Step 2: Replace the immediate success handler with the Snap popup**

Around line 2607 (`if (data.success) { showSuccess(...) }`), change to:

```javascript
if (data.success) {
    window.snap.pay(data.data.snap_token, {
        onSuccess: function () {
            showSuccess('Pembayaran berhasil! Terima kasih atas donasi kamu.');
        },
        onPending: function () {
            showSuccess('Pembayaran kamu sedang diproses. Alert akan muncul begitu dikonfirmasi.');
        },
        onError: function () {
            showErr('Pembayaran gagal. Silakan coba lagi.');
            btn.disabled = false;
            btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg> Kirim Donasi';
        },
        onClose: function () {
            btn.disabled = false;
            btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg> Kirim Donasi';
        },
    });
} else if (data.errors) {
```

(This is `onSuccess`/`onPending`/`onError`/`onClose` shown as **UX messaging only** — per the design spec, the webhook from Task 5 is what actually credits the donation; these callbacks never touch the database.)

- [ ] **Step 3: Set local `.env` sandbox credentials**

```
MIDTRANS_SERVER_KEY=SB-Mid-server-xxxxxxxxxxxxxxxxxxxxxxxx
MIDTRANS_CLIENT_KEY=SB-Mid-client-xxxxxxxxxxxxxxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=false
```
(Get real sandbox keys from the Midtrans dashboard — https://dashboard.sandbox.midtrans.com — under Settings → Access Keys.)

- [ ] **Step 4: Manual verification checklist**

1. `composer run dev`, open a streamer's donation page (`/{slug}`).
2. Submit a donation — confirm the Snap popup opens (not a page redirect), showing QRIS/e-wallet/VA/card options.
3. Pay using a Midtrans sandbox test method (e.g. their simulator VA number, or a test card `4811 1111 1111 1114`).
4. Confirm `onSuccess`/`onPending` fires the expected message, and the popup closes.
5. Because the local dev server isn't publicly reachable, Midtrans's webhook can't reach `POST /webhooks/midtrans` automatically in this environment — instead, manually trigger it via the Midtrans sandbox dashboard's "Resend Notification" button for that transaction, or `curl` the webhook URL directly with a payload matching what the transaction detail page shows. Confirm the `Donation` row flips to `paid` (check via `php artisan tinker` → `Donation::latest()->first()`) and an alert appears on the streamer's OBS overlay (`/{slug}/obs/overlay?key=...`).
6. Confirm closing the popup without paying (`onClose`) re-enables the submit button and leaves the `Donation` row `pending`.

- [ ] **Step 5: Commit**

```bash
git add resources/views/donate/show.blade.php .env.example
git commit -m "feat: donation form opens Midtrans Snap popup instead of showing success immediately"
```

---

### Task 7: Paid-only filtering — stats, leaderboard, milestone/subathon triggers

**Files:**
- Modify: `app/Services/StreamerStatsService.php`
- Modify: `app/Models/Streamer.php`
- Test: `tests/Feature/Donation/PaidOnlyStatsTest.php`

**Interfaces:**
- Consumes: `Streamer::paidDonations()` (Task 1).
- Produces: no new public interface — `computeStats()`, `calculateDynamicCacheTtl()`, `getTotalDonationsAttribute()`, `getTodayDonationsAttribute()` all now ignore non-paid donations.

- [ ] **Step 1: Write the failing test**

```php
<?php
// tests/Feature/Donation/PaidOnlyStatsTest.php
namespace Tests\Feature\Donation;

use App\Models\Donation;
use App\Models\Streamer;
use App\Services\StreamerStatsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaidOnlyStatsTest extends TestCase
{
    use RefreshDatabase;

    public function test_build_stats_ignores_pending_and_failed_donations(): void
    {
        $streamer = Streamer::factory()->create(['leaderboard_count' => 5]);
        Donation::factory()->for($streamer)->create(['status' => 'pending', 'amount' => 999999]);
        Donation::factory()->for($streamer)->create(['status' => 'failed', 'amount' => 999999]);
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 15000, 'name' => 'Budi']);

        $stats = app(StreamerStatsService::class)->computeStats($streamer);

        $this->assertSame(15000, $stats['total']);
        $this->assertSame(1, $stats['count']);
        $this->assertCount(1, $stats['leaderboard']);
        $this->assertSame('Budi', $stats['leaderboard'][0]['name']);
    }

    public function test_total_and_today_donation_attributes_are_paid_only(): void
    {
        $streamer = Streamer::factory()->create();
        Donation::factory()->for($streamer)->create(['status' => 'pending', 'amount' => 999999]);
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 10000]);

        $this->assertSame(10000, $streamer->total_donations);
        $this->assertSame(10000, $streamer->today_donations);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=PaidOnlyStatsTest`
Expected: FAIL — totals include the pending/failed amounts too.

- [ ] **Step 3: Update `StreamerStatsService::computeStats`**

In `app/Services/StreamerStatsService.php`, change the three `$streamer->donations()` call sites (and the local `$base` alias) to `$streamer->paidDonations()`:

```php
$base = $streamer->paidDonations();
```

```php
$leaderboard = $streamer->paidDonations()
    ->selectRaw('name, SUM(amount) as total, COUNT(*) as cnt')
    ->selectSub(
        Donation::selectRaw('emoji')
            ->whereColumn('name', 'donations.name')
            ->latest('created_at')
            ->limit(1),
        'emoji'
    )
    ->groupBy('name')
```
(rest of the leaderboard query block unchanged)

```php
$milestoneQuery = $streamer->milestone_reset
    ? $streamer->paidDonations()->whereDate('created_at', today())
    : $streamer->paidDonations();
```

- [ ] **Step 4: Update `calculateDynamicCacheTtl`**

```php
private function calculateDynamicCacheTtl(Streamer $streamer): int
{
    $lastDonationAt = $streamer->paidDonations()
        ->latest('created_at')
        ->value('created_at');
    // ... rest unchanged
```

- [ ] **Step 5: Update `Streamer` attribute accessors**

In `app/Models/Streamer.php`:

```php
public function getTotalDonationsAttribute(): int
{
    return $this->paidDonations()->sum('amount');
}

public function getTodayDonationsAttribute(): int
{
    return $this->paidDonations()->whereDate('created_at', today())->sum('amount');
}
```

(The deprecated, unused `Streamer::calculateDynamicCacheTtl()` private method — already marked `@deprecated` in favor of `StreamerStatsService` — is left as-is; it has no live call sites so it's out of scope here.)

- [ ] **Step 6: Run test to verify it passes**

Run: `php artisan test --filter=PaidOnlyStatsTest`
Expected: PASS (2 tests)

- [ ] **Step 7: Commit**

```bash
git add app/Services/StreamerStatsService.php app/Models/Streamer.php \
        tests/Feature/Donation/PaidOnlyStatsTest.php
git commit -m "fix: stats, leaderboard, and milestone/subathon totals only count paid donations"
```

---

### Task 8: Paid-only filtering — OBS running-text widget, admin totals, reports, heatmap

**Files:**
- Modify: `app/Http/Controllers/ObsController.php`
- Modify: `app/Http/Controllers/AdminController.php`
- Modify: `app/Http/Controllers/ReportController.php`
- Modify: `app/Http/Controllers/StreamerDashboardController.php`
- Test: `tests/Feature/Donation/PaidOnlyReportingTest.php`

**Interfaces:**
- Consumes: `Donation::scopePaid()` / `Streamer::paidDonations()` (Task 1).

- [ ] **Step 1: Write the failing test**

```php
<?php
// tests/Feature/Donation/PaidOnlyReportingTest.php
namespace Tests\Feature\Donation;

use App\Models\Donation;
use App\Models\Streamer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaidOnlyReportingTest extends TestCase
{
    use RefreshDatabase;

    public function test_obs_running_text_only_shows_paid_donation_messages(): void
    {
        $streamer = Streamer::factory()->create();
        Donation::factory()->for($streamer)->create(['status' => 'pending', 'message' => 'Pesan belum bayar']);
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'message' => 'Pesan sudah bayar']);

        $response = $this->get("/{$streamer->slug}/obs/running-text");

        $response->assertOk();
        $response->assertSee('Pesan sudah bayar');
        $response->assertDontSee('Pesan belum bayar');
    }

    public function test_admin_dashboard_totals_exclude_non_paid_donations(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $streamer = Streamer::factory()->create();
        Donation::factory()->for($streamer)->create(['status' => 'pending', 'amount' => 999999]);
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 10000]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertOk();
        $response->assertViewHas('totalAmount', 10000);
        $response->assertViewHas('totalDonations', 1);
    }

    public function test_heatmap_data_excludes_non_paid_donations(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'streamer'])->save();
        $streamer = Streamer::factory()->create(['user_id' => $user->id]);
        Donation::factory()->for($streamer)->create(['status' => 'pending', 'amount' => 999999]);
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 5000]);

        $response = $this->actingAs($user)->get('/streamer/heatmap-data?year=' . now()->year . '&month=' . now()->month);

        $response->assertOk();
        // heatmapData() returns {year, month, days: [{iso, total, count}, ...]} — one entry
        // per calendar day, not a flat total. Sum across all days rather than assert a
        // single day's bucket, so this doesn't depend on which WIB day the donation lands in.
        $monthTotal = array_sum(array_column($response->json('days'), 'total'));
        $this->assertSame(5000, $monthTotal);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=PaidOnlyReportingTest`
Expected: FAIL — all three surfaces currently include non-paid amounts/messages.

- [ ] **Step 3: Update `ObsController::runningText`**

In `app/Http/Controllers/ObsController.php`:

```php
$donations = $streamer->paidDonations()
    ->whereNotNull('message')
    ->where('message', '!=', '')
    ->orderBy('created_at', 'desc')
    ->limit(config('pagination.running_text_donations', 20))
    ->get();
```

- [ ] **Step 4: Update `AdminController::dashboard`**

In `app/Http/Controllers/AdminController.php`, change the totals block:

```php
$totalDonations  = Donation::paid()->count();
$totalAmount     = Donation::paid()->sum('amount');
$todayAmount     = Donation::paid()->whereDate('created_at', today())->sum('amount');
$todayCount      = Donation::paid()->whereDate('created_at', today())->count();
```

Leave `$recentDonations` and `$streamerStats` (the `withCount`/`withSum` leaderboard query) as-is for now — `$recentDonations` is Bucket 2 (Task 9 adds its status badge instead of filtering it), and `$streamerStats`'s per-streamer lifetime totals should also arguably be paid-only; fold that into Task 9 alongside the badge work since it touches the same view/query together.

- [ ] **Step 5: Update `ReportController`**

In `app/Http/Controllers/ReportController.php`, every `$streamer->donations()` call site in `index()`, `exportCsv()`, and `exportPdf()` becomes `$streamer->paidDonations()` — these are financial reports, they must never include unconfirmed money. There are 6 such call sites across the three methods (lines ~47, ~105, ~150, ~156, ~164 as of this plan being written); grep to confirm you've caught all of them:

Run: `grep -n '\$streamer->donations()' app/Http/Controllers/ReportController.php`
Expected after the edit: no matches.

- [ ] **Step 6: Update `StreamerDashboardController::heatmapData`**

The heatmap query uses the model directly, not the relation:

```php
$rows = \App\Models\Donation::where('streamer_id', $streamer->id)
    ->where('status', 'paid')
    ->whereBetween('created_at', [$start, $end])
    ->selectRaw("{$datExpr} as day, SUM(amount) as total, COUNT(*) as cnt")
    ->groupBy('day')
    ->get()
    ->keyBy('day');
```

- [ ] **Step 7: Run test to verify it passes**

Run: `php artisan test --filter=PaidOnlyReportingTest`
Expected: PASS (3 tests)

- [ ] **Step 8: Run the full suite to check for regressions**

Run: `php artisan test`

- [ ] **Step 9: Commit**

```bash
git add app/Http/Controllers/ObsController.php app/Http/Controllers/AdminController.php \
        app/Http/Controllers/ReportController.php app/Http/Controllers/StreamerDashboardController.php \
        tests/Feature/Donation/PaidOnlyReportingTest.php
git commit -m "fix: OBS running-text, admin totals, reports, and heatmap only count paid donations"
```

---

### Task 9: Status badges on internal history/management views (Bucket 2)

**Files:**
- Modify: `resources/views/admin/donations.blade.php`
- Modify: `resources/views/streamer/dashboard.blade.php`
- Modify: `app/Http/Controllers/AdminController.php` (the `$streamerStats` leaderboard query, deferred from Task 8)
- Test: `tests/Feature/Donation/DonationStatusBadgeTest.php`

**Interfaces:**
- Consumes: `Donation::status` (Task 1).

Per the design spec, these two views are the deliberate exception to "hidden until paid" — an admin/streamer should be able to see that a donation attempt failed or is still pending, not just successful ones. No controller change needed for the two views themselves (they already query the raw, unfiltered relation) — only the Blade templates gain a badge.

- [ ] **Step 1: Write the failing test**

```php
<?php
// tests/Feature/Donation/DonationStatusBadgeTest.php
namespace Tests\Feature\Donation;

use App\Models\Donation;
use App\Models\Streamer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationStatusBadgeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_donations_list_shows_status_badge(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $streamer = Streamer::factory()->create();
        Donation::factory()->for($streamer)->create(['status' => 'pending', 'name' => 'Andi']);
        Donation::factory()->for($streamer)->create(['status' => 'failed', 'name' => 'Sari']);

        $response = $this->actingAs($admin)->get('/admin/donations');

        $response->assertOk();
        $response->assertSee('Menunggu Pembayaran');
        $response->assertSee('Gagal');
    }

    public function test_streamer_dashboard_history_shows_status_badge(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'streamer'])->save();
        $streamer = Streamer::factory()->create(['user_id' => $user->id]);
        Donation::factory()->for($streamer)->create(['status' => 'expired', 'name' => 'Rudi']);

        $response = $this->actingAs($user)->get('/streamer/dashboard');

        $response->assertOk();
        $response->assertSee('Kedaluwarsa');
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=DonationStatusBadgeTest`
Expected: FAIL — no status text rendered anywhere yet.

- [ ] **Step 3: Add a status badge to `admin/donations.blade.php`**

Add a `<th>Status</th>` column header after the existing `<th>Waktu</th>` (around line 80), and a matching `<td>` in the row loop (around line 108, right after the "Waktu" `<td>`):

```blade
<td>
    @php
        $statusLabel = match($d->status) {
            'paid' => 'Berhasil',
            'pending' => 'Menunggu Pembayaran',
            'failed' => 'Gagal',
            'expired' => 'Kedaluwarsa',
            default => ucfirst($d->status),
        };
        $statusClass = match($d->status) {
            'paid' => 'badge-success',
            'pending' => 'badge-warning',
            'failed', 'expired' => 'badge-danger',
            default => '',
        };
    @endphp
    <span class="{{ $statusClass }}" style="font-size:11px;padding:2px 8px;border-radius:6px">{{ $statusLabel }}</span>
</td>
```

Update the `colspan="7"` on the empty-state row (around line 126) to `colspan="8"` to match the new column count.

- [ ] **Step 4: Add a status badge to `streamer/dashboard.blade.php`**

In the `history-item` loop (around line 442-460), add the badge next to the existing tier badge:

```blade
@if($d->status !== 'paid')
    @php
        $statusLabel = match($d->status) {
            'pending' => 'Menunggu Pembayaran',
            'failed' => 'Gagal',
            'expired' => 'Kedaluwarsa',
            default => ucfirst($d->status),
        };
    @endphp
    <span class="h-badge" style="background:var(--surface-3);color:var(--text-3)">{{ $statusLabel }}</span>
@endif
```

(Paid donations show no extra badge — the existing tier badge already covers the normal case; only non-paid statuses need calling out.)

- [ ] **Step 5: Make the admin per-streamer leaderboard paid-only (deferred from Task 8)**

In `app/Http/Controllers/AdminController.php::dashboard()`, the `$streamerStats` query:

```php
$streamerStats = Streamer::with('user')
    ->withCount(['donations as donations_count' => fn ($q) => $q->where('status', 'paid')])
    ->withSum(['donations as donations_sum_amount' => fn ($q) => $q->where('status', 'paid')], 'amount')
    ->orderByDesc('donations_sum_amount')
    ->limit(config('pagination.admin_streamer_stats', 25))
    ->get();
```

(Aliasing back to `donations_count`/`donations_sum_amount` keeps the existing Blade view's variable names working unchanged.)

- [ ] **Step 6: Run test to verify it passes**

Run: `php artisan test --filter=DonationStatusBadgeTest`
Expected: PASS (2 tests)

- [ ] **Step 7: Run the full suite to check for regressions**

Run: `php artisan test`

- [ ] **Step 8: Commit**

```bash
git add resources/views/admin/donations.blade.php resources/views/streamer/dashboard.blade.php \
        app/Http/Controllers/AdminController.php tests/Feature/Donation/DonationStatusBadgeTest.php
git commit -m "feat: show donation payment status badge on admin and streamer history views"
```

---

### Task 10: Scheduled cleanup of stale pending donations

**Files:**
- Create: `app/Jobs/CleanupExpiredPendingDonationsJob.php`
- Modify: `routes/console.php`
- Test: `tests/Unit/Jobs/CleanupExpiredPendingDonationsJobTest.php`

**Interfaces:**
- Consumes: `config('midtrans.snap_expiry_minutes')` (Task 2), `Donation::status`/`media_path` (Task 1).

- [ ] **Step 1: Write the failing test**

```php
<?php
// tests/Unit/Jobs/CleanupExpiredPendingDonationsJobTest.php
namespace Tests\Unit\Jobs;

use App\Jobs\CleanupExpiredPendingDonationsJob;
use App\Models\Donation;
use App\Models\Streamer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CleanupExpiredPendingDonationsJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_stale_pending_donation_is_expired_and_media_deleted(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('donations/media/old.mp3', 'fake-audio-content');

        $streamer = Streamer::factory()->create();
        $stale = Donation::factory()->for($streamer)->create([
            'status' => 'pending',
            'media_path' => 'donations/media/old.mp3',
            'created_at' => now()->subMinutes(config('midtrans.snap_expiry_minutes', 60) + 20),
        ]);

        (new CleanupExpiredPendingDonationsJob())->handle();

        $stale->refresh();
        $this->assertSame('expired', $stale->status);
        Storage::disk('public')->assertMissing('donations/media/old.mp3');
    }

    public function test_fresh_pending_donation_is_untouched(): void
    {
        $streamer = Streamer::factory()->create();
        $fresh = Donation::factory()->for($streamer)->create([
            'status' => 'pending',
            'created_at' => now()->subMinutes(5),
        ]);

        (new CleanupExpiredPendingDonationsJob())->handle();

        $fresh->refresh();
        $this->assertSame('pending', $fresh->status);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=CleanupExpiredPendingDonationsJobTest`
Expected: FAIL — job class doesn't exist.

- [ ] **Step 3: Create the job**

```php
<?php
// app/Jobs/CleanupExpiredPendingDonationsJob.php
namespace App\Jobs;

use App\Models\Donation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CleanupExpiredPendingDonationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function handle(): void
    {
        $bufferMinutes = (int) config('midtrans.snap_expiry_minutes', 60) + 10;

        $stale = Donation::where('status', 'pending')
            ->where('created_at', '<', now()->subMinutes($bufferMinutes))
            ->get();

        foreach ($stale as $donation) {
            if ($donation->media_path) {
                Storage::disk('public')->delete($donation->media_path);
            }
            $donation->update(['status' => 'expired']);
        }

        Log::info("CleanupExpiredPendingDonationsJob: expired {$stale->count()} stale pending donations");
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('CleanupExpiredPendingDonationsJob: semua retry habis, stale pending donations tidak dibersihkan', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=CleanupExpiredPendingDonationsJobTest`
Expected: PASS (2 tests)

- [ ] **Step 5: Schedule the job**

In `routes/console.php`, add the import and schedule entry alongside the existing two:

```php
use App\Jobs\CleanupExpiredPendingDonationsJob;
```

```php
// Cleanup stale pending donations (payment abandoned/expired) every 15 minutes
Schedule::job(new CleanupExpiredPendingDonationsJob)->everyFifteenMinutes();
```

- [ ] **Step 6: Run the full suite to check for regressions**

Run: `php artisan test`

- [ ] **Step 7: Commit**

```bash
git add app/Jobs/CleanupExpiredPendingDonationsJob.php routes/console.php \
        tests/Unit/Jobs/CleanupExpiredPendingDonationsJobTest.php
git commit -m "feat: scheduled cleanup of stale pending donations and their orphaned media"
```

---

## Post-plan note

Once all 10 tasks are merged, update `BACKLOG.md` item 2 ("Payment gateway integration — Midtrans Snap") to reflect it's shipped — move its one-line summary into `CLAUDE.md`'s architecture section (a new "Payment" subsection alongside "Donation → Alert pipeline") and delete it from `BACKLOG.md`, per that file's own stated convention. Item 2a (payout/settlement) stays in the backlog untouched.
