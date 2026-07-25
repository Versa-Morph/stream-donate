# Admin: Donations Trend Chart Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Show admins a day-by-day donations trend (amount + count, 7/30/90-day toggle) on the dashboard, instead of only "today vs all-time."

**Architecture:** A new `TrendsService` aggregates paid donations per day (same WIB-aware date grouping as the existing streamer heatmap). `AdminController::dashboard()` renders the initial 30-day view; a new JSON endpoint serves range-toggle refetches. Two Chart.js line charts render client-side, colors read from this app's existing `--brand`/`--orange` CSS custom properties.

**Tech Stack:** Laravel 12, PHP 8.2+, PHPUnit 11, Chart.js 4 (new — via CDN, matching this codebase's existing CDN-script-tag convention for Iconify/Midtrans Snap.js; **not** Vite/npm — confirmed `layouts/app.blade.php` has no `@vite` directive at all, this admin/streamer UI is 100% inline CSS/vanilla-JS + CDN scripts, adding a Vite entry point here would be inconsistent with everything else in this layout).

## Global Constraints

- Design source of truth: `docs/superpowers/specs/2026-07-26-admin-trends-chart-design.md` — every task below implements one section of it.
- User-facing strings are Indonesian, matching the rest of the codebase.
- Rupiah amounts always format as `Intl.NumberFormat('id-ID')` client-side / `number_format($x, 0, ',', '.')` server-side (Indonesian locale) — confirmed the hard way twice already (payout work) that the bare JS/PHP default renders US-locale commas.
- Platform-wide only — no per-streamer scoping in this feature.
- Only 7, 30, or 90 as valid `days` values — anything else is a validation error, not a silent fallback (deliberately different from the existing `heatmapData()`'s forgiving-fallback behavior, per the design spec's testing section).

---

### Task 1: `TrendsService` — day-by-day aggregation

**Files:**
- Create: `app/Services/TrendsService.php`
- Test: `tests/Feature/Trends/TrendsServiceTest.php`

**Interfaces:**
- Produces: `TrendsService::donationTrend(int $days): array` returning `['labels' => string[], 'amounts' => int[], 'counts' => int[]]`, all three arrays exactly `$days` long, oldest first.

- [ ] **Step 1: Write the failing test**

```php
<?php
// tests/Feature/Trends/TrendsServiceTest.php
namespace Tests\Feature\Trends;

use App\Models\Donation;
use App\Models\Streamer;
use App\Services\TrendsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrendsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_donation_trend_zero_fills_days_with_no_donations(): void
    {
        $trend = app(TrendsService::class)->donationTrend(7);

        $this->assertCount(7, $trend['labels']);
        $this->assertCount(7, $trend['amounts']);
        $this->assertCount(7, $trend['counts']);
        $this->assertSame([0, 0, 0, 0, 0, 0, 0], $trend['amounts']);
        $this->assertSame([0, 0, 0, 0, 0, 0, 0], $trend['counts']);
    }

    public function test_donation_trend_sums_paid_donations_on_the_correct_day_and_excludes_pending(): void
    {
        $streamer = Streamer::factory()->create();
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 30000, 'created_at' => now()]);
        Donation::factory()->for($streamer)->create(['status' => 'paid', 'amount' => 20000, 'created_at' => now()]);
        Donation::factory()->for($streamer)->create(['status' => 'pending', 'amount' => 999999, 'created_at' => now()]);

        $trend = app(TrendsService::class)->donationTrend(7);

        $this->assertSame(50000, end($trend['amounts']));
        $this->assertSame(2, end($trend['counts']));
    }

    public function test_donation_trend_excludes_donations_outside_the_window(): void
    {
        $streamer = Streamer::factory()->create();
        Donation::factory()->for($streamer)->create([
            'status' => 'paid',
            'amount' => 99999,
            'created_at' => now()->subDays(10),
        ]);

        $trend = app(TrendsService::class)->donationTrend(7);

        $this->assertSame(0, array_sum($trend['amounts']));
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `./vendor/bin/phpunit tests/Feature/Trends/TrendsServiceTest.php`
Expected: FAIL — `TrendsService` doesn't exist yet.

- [ ] **Step 3: Create the service**

```php
<?php
// app/Services/TrendsService.php
namespace App\Services;

use App\Models\Donation;
use Illuminate\Support\Facades\DB;

class TrendsService
{
    public function donationTrend(int $days): array
    {
        // App timezone is UTC, but donations are grouped by WIB (Asia/Jakarta)
        // calendar day — reasoning in WIB here too keeps the PHP-side lookup
        // keys in agreement with the SQL-side day buckets below. Plain now()
        // (UTC) would mismatch the SQL bucketing for ~7 of every 24 hours
        // (whenever UTC time is 17:00-23:59 and WIB has already rolled to the
        // next calendar day) — caught by Step 4's test failing for real.
        $nowWib = now('Asia/Jakarta');
        $start = $nowWib->copy()->subDays($days - 1)->startOfDay()->setTimezone('UTC');

        // Same WIB-aware date grouping as StreamerDashboardController::buildMonthHeatmap
        $driver = DB::getDriverName();
        $dateExpr = $driver === 'sqlite'
            ? "DATE(datetime(created_at, '+7 hours'))"
            : "DATE(CONVERT_TZ(created_at, 'UTC', 'Asia/Jakarta'))";

        $rows = Donation::paid()
            ->where('created_at', '>=', $start)
            ->selectRaw("{$dateExpr} as day, SUM(amount) as total, COUNT(*) as cnt")
            ->groupBy('day')
            ->get()
            ->keyBy('day');

        $labels = $amounts = $counts = [];
        for ($i = 0; $i < $days; $i++) {
            $date = $nowWib->copy()->subDays($days - 1 - $i);
            $row = $rows->get($date->format('Y-m-d'));
            $labels[] = $date->format('d/m');
            $amounts[] = $row ? (int) $row->total : 0;
            $counts[] = $row ? (int) $row->cnt : 0;
        }

        return compact('labels', 'amounts', 'counts');
    }
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `./vendor/bin/phpunit tests/Feature/Trends/TrendsServiceTest.php`
Expected: PASS (3 tests)

- [ ] **Step 5: Run the full suite to check for regressions**

Run: `./vendor/bin/phpunit`

- [ ] **Step 6: Commit**

```bash
git add app/Services/TrendsService.php tests/Feature/Trends/TrendsServiceTest.php
git commit -m "feat: add TrendsService for day-by-day donation aggregation"
```

---

### Task 2: `GET /admin/dashboard/trends` JSON endpoint

**Files:**
- Modify: `app/Http/Controllers/AdminController.php`
- Modify: `routes/web.php`
- Test: `tests/Feature/Trends/TrendsEndpointTest.php`

**Interfaces:**
- Consumes: `TrendsService::donationTrend()` (Task 1).
- Produces: `GET /admin/dashboard/trends?days=7|30|90` → `{labels: [...], amounts: [...], counts: [...]}` JSON, or a validation error for any other `days` value.

- [ ] **Step 1: Write the failing tests**

```php
<?php
// tests/Feature/Trends/TrendsEndpointTest.php
namespace Tests\Feature\Trends;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrendsEndpointTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        return $admin;
    }

    public function test_returns_correctly_shaped_json_for_each_valid_range(): void
    {
        $admin = $this->admin();

        foreach ([7, 30, 90] as $days) {
            $response = $this->actingAs($admin)->getJson("/admin/dashboard/trends?days={$days}");

            $response->assertOk();
            $response->assertJsonStructure(['labels', 'amounts', 'counts']);
            $this->assertCount($days, $response->json('labels'));
        }
    }

    public function test_rejects_an_invalid_days_value(): void
    {
        $admin = $this->admin();

        $response = $this->actingAs($admin)->getJson('/admin/dashboard/trends?days=45');

        $response->assertStatus(422);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `./vendor/bin/phpunit tests/Feature/Trends/TrendsEndpointTest.php`
Expected: FAIL — route doesn't exist yet (404s).

- [ ] **Step 3: Add the controller method**

In `app/Http/Controllers/AdminController.php`, add the import:

```php
use App\Services\TrendsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
```

(Add each only if not already imported — `Request` is already imported.)

```php
public function trendsData(Request $request): JsonResponse
{
    $validated = Validator::make($request->query(), [
        'days' => ['required', 'integer', 'in:7,30,90'],
    ])->validate();

    $trend = app(TrendsService::class)->donationTrend((int) $validated['days']);

    return response()->json($trend);
}
```

- [ ] **Step 4: Register the route**

In `routes/web.php`, directly after the `Route::get('/dashboard', ...)` line inside the admin-gated group:

```php
Route::get('/dashboard/trends', [AdminController::class, 'trendsData'])->name('dashboard.trends');
```

- [ ] **Step 5: Run test to verify it passes**

Run: `./vendor/bin/phpunit tests/Feature/Trends/TrendsEndpointTest.php`
Expected: PASS (2 tests — the first covers all 3 ranges internally)

- [ ] **Step 6: Run the full suite to check for regressions**

Run: `./vendor/bin/phpunit`

- [ ] **Step 7: Commit**

```bash
git add app/Http/Controllers/AdminController.php routes/web.php \
        tests/Feature/Trends/TrendsEndpointTest.php
git commit -m "feat: add admin dashboard trends JSON endpoint"
```

---

### Task 3: Dashboard integration — charts, range toggle, table view

**Files:**
- Modify: `app/Http/Controllers/AdminController.php`
- Modify: `app/Http/Middleware/SecurityHeaders.php`
- Modify: `resources/views/admin/dashboard.blade.php`
- Test: `tests/Feature/Trends/DashboardTrendChartTest.php`

**Interfaces:**
- Consumes: `TrendsService::donationTrend()` (Task 1), `GET /admin/dashboard/trends` (Task 2).

- [ ] **Step 1: Write the failing test**

```php
<?php
// tests/Feature/Trends/DashboardTrendChartTest.php
namespace Tests\Feature\Trends;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTrendChartTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renders_chart_canvases_and_range_buttons(): void
    {
        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertOk();
        $response->assertSee('id="trend-amount-chart"', false);
        $response->assertSee('id="trend-count-chart"', false);
        $response->assertSee('cdn.jsdelivr.net/npm/chart.js', false);
        $response->assertSee('30 Hari');
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `./vendor/bin/phpunit tests/Feature/Trends/DashboardTrendChartTest.php`
Expected: FAIL — none of this markup exists yet.

- [ ] **Step 3: Add initial trend data to `AdminController::dashboard()`**

```php
$trend = app(TrendsService::class)->donationTrend(30);
```

Add `'trend'` to the `compact(...)` call in the `return view(...)` line.

- [ ] **Step 4: Allow the Chart.js CDN in the CSP**

In `app/Http/Middleware/SecurityHeaders.php`, add `https://cdn.jsdelivr.net` to `script-src` (same reasoning as the Midtrans Snap.js CSP update from the payment gateway work — without this the browser blocks the script outright):

```php
"script-src 'self' 'unsafe-inline' https://code.iconify.design https://api.iconify.design https://app.sandbox.midtrans.com https://app.midtrans.com https://cdn.jsdelivr.net",
```

- [ ] **Step 5: Add the chart markup + script to the dashboard view**

`layouts/app.blade.php` has `@stack('scripts')` (line 1730, right before `</body>`) — use it via `@push('scripts')` in `resources/views/admin/dashboard.blade.php`, directly after the existing `@push('styles')` block:

```blade
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
@endpush
```

Add the range-toggle + charts + table-view markup, directly after the alert-failure card added in the previous feature and before the stats grid:

```blade
<div class="table-card" style="margin-bottom:16px">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px">
        <h2 class="section-title">Tren Donasi</h2>
        <div>
            <button type="button" class="btn-xs range-btn" data-days="7">7 Hari</button>
            <button type="button" class="btn-xs range-btn active" data-days="30">30 Hari</button>
            <button type="button" class="btn-xs range-btn" data-days="90">90 Hari</button>
        </div>
    </div>

    <canvas id="trend-amount-chart" height="80"></canvas>
    <a href="#" id="toggle-amount-table" style="font-size:11px">Lihat sebagai tabel</a>
    <table id="amount-table" style="display:none; margin-top:8px">
        <thead><tr><th>Tanggal</th><th>Jumlah</th></tr></thead>
        <tbody></tbody>
    </table>

    <canvas id="trend-count-chart" height="80" style="margin-top:24px"></canvas>
    <a href="#" id="toggle-count-table" style="font-size:11px">Lihat sebagai tabel</a>
    <table id="count-table" style="display:none; margin-top:8px">
        <thead><tr><th>Tanggal</th><th>Jumlah Donasi</th></tr></thead>
        <tbody></tbody>
    </table>
</div>
```

The canvas/button/table HTML above goes inline in the page body (matching `streamer/dashboard.blade.php`'s heatmap markup placement). Its behavior script joins the same `@push('scripts')` block as the Chart.js CDN tag from the previous step — this codebase's established convention (confirmed via `streamer/dashboard.blade.php:542`) is `@push('scripts') <script>...</script> @endpush`, never a bare inline `<script>` tag:

```blade
@push('scripts')
<script>
(function () {
    const brand = getComputedStyle(document.documentElement).getPropertyValue('--brand').trim();
    const orange = getComputedStyle(document.documentElement).getPropertyValue('--orange').trim();
    const rupiah = (v) => 'Rp ' + new Intl.NumberFormat('id-ID').format(v);
    const number = (v) => new Intl.NumberFormat('id-ID').format(v);

    function hexToRgba(hex, alpha) {
        const r = parseInt(hex.slice(1, 3), 16);
        const g = parseInt(hex.slice(3, 5), 16);
        const b = parseInt(hex.slice(5, 7), 16);
        return `rgba(${r}, ${g}, ${b}, ${alpha})`;
    }

    function lineDataset(label, data, color) {
        return {
            label,
            data,
            borderColor: color,
            backgroundColor: hexToRgba(color, 0.1),
            borderWidth: 2,
            pointRadius: 4,
            pointBackgroundColor: color,
            pointBorderColor: '#1a1a19',
            pointBorderWidth: 2,
            fill: true,
            tension: 0.2,
        };
    }

    function baseOptions(valueFormatter) {
        return {
            responsive: true,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: { callbacks: { label: (ctx) => valueFormatter(ctx.parsed.y) } },
            },
            scales: {
                x: { grid: { display: false } },
                y: {
                    beginAtZero: true,
                    grid: { color: '#2c2c2a' },
                    ticks: { callback: (v) => valueFormatter(v) },
                },
            },
        };
    }

    let initial = @json($trend);

    const amountChart = new Chart(document.getElementById('trend-amount-chart'), {
        type: 'line',
        data: { labels: initial.labels, datasets: [lineDataset('Total Donasi', initial.amounts, brand)] },
        options: baseOptions(rupiah),
    });

    const countChart = new Chart(document.getElementById('trend-count-chart'), {
        type: 'line',
        data: { labels: initial.labels, datasets: [lineDataset('Jumlah Donasi', initial.counts, orange)] },
        options: baseOptions(number),
    });

    function renderTable(tableId, labels, values) {
        const tbody = document.querySelector(`#${tableId} tbody`);
        tbody.innerHTML = '';
        labels.forEach((label, i) => {
            const tr = document.createElement('tr');
            const tdLabel = document.createElement('td');
            tdLabel.textContent = label;
            const tdValue = document.createElement('td');
            tdValue.textContent = values[i];
            tr.append(tdLabel, tdValue);
            tbody.appendChild(tr);
        });
    }
    renderTable('amount-table', initial.labels, initial.amounts);
    renderTable('count-table', initial.labels, initial.counts);

    document.getElementById('toggle-amount-table').addEventListener('click', function (e) {
        e.preventDefault();
        const t = document.getElementById('amount-table');
        t.style.display = t.style.display === 'none' ? '' : 'none';
    });
    document.getElementById('toggle-count-table').addEventListener('click', function (e) {
        e.preventDefault();
        const t = document.getElementById('count-table');
        t.style.display = t.style.display === 'none' ? '' : 'none';
    });

    document.querySelectorAll('.range-btn').forEach((btn) => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.range-btn').forEach((b) => b.classList.remove('active'));
            btn.classList.add('active');

            [amountChart, countChart].forEach((c) => c.canvas.style.opacity = '0.5');

            fetch(`{{ route('admin.dashboard.trends') }}?days=${btn.dataset.days}`)
                .then((r) => r.json())
                .then((data) => {
                    amountChart.data.labels = data.labels;
                    amountChart.data.datasets[0].data = data.amounts;
                    amountChart.update();
                    countChart.data.labels = data.labels;
                    countChart.data.datasets[0].data = data.counts;
                    countChart.update();
                    renderTable('amount-table', data.labels, data.amounts);
                    renderTable('count-table', data.labels, data.counts);
                    [amountChart, countChart].forEach((c) => c.canvas.style.opacity = '1');
                });
        });
    });
})();
</script>
@endpush
```

- [ ] **Step 6: Run test to verify it passes**

Run: `./vendor/bin/phpunit tests/Feature/Trends/DashboardTrendChartTest.php`
Expected: PASS

- [ ] **Step 7: Run the full suite to check for regressions**

Run: `./vendor/bin/phpunit`

- [ ] **Step 8: Manual verification** (Chart.js rendering is client-side canvas, not something PHPUnit can assert on — same boundary as the existing streamer heatmap)

1. `composer run dev`, log in as an admin, open `/admin/dashboard`.
2. Confirm both charts render with real data (seed a few test donations first if the DB is empty).
3. Click each range button (7/30/90 Hari) — confirm both charts update, the active button highlight moves, and there's a brief opacity dip during the fetch (not a layout jump).
4. Click "Lihat sebagai tabel" under each chart — confirm the table appears with matching values, and toggles closed on a second click.
5. Hover over a chart — confirm the tooltip shows the Rupiah/count value formatted correctly and the crosshair follows the pointer.

- [ ] **Step 9: Commit**

```bash
git add app/Http/Controllers/AdminController.php app/Http/Middleware/SecurityHeaders.php \
        resources/views/admin/dashboard.blade.php tests/Feature/Trends/DashboardTrendChartTest.php
git commit -m "feat: add donations trend charts to admin dashboard"
```

---

## Post-plan note

Once merged, update `BACKLOG.md`'s "Admin dashboard — make it useful" item to remove this piece, keeping the remaining two (list filtering, per-streamer drill-down) as the item's scope.
