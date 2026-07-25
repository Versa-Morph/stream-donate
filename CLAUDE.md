# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project

StreamDonate — Laravel 12 real-time donation platform for streamers. Public donors submit donations via a per-streamer public form; donations flow into an alert queue that OBS Browser Source widgets consume over Server-Sent Events (SSE), no third-party services or OBS plugins required.

## Commands

```bash
composer run dev          # server + queue:listen + pail (logs) + vite, all concurrently
php artisan serve         # server only
npm run dev                # vite only (hot reload)
npm run build               # build frontend assets

composer test               # config:clear + php artisan test
php artisan test --filter=TestName
php artisan test tests/Feature/Auth/AuthenticationTest.php

php artisan migrate
php artisan migrate:fresh   # destructive: wipes all data
php artisan optimize:clear  # clear all caches
php artisan pail            # tail logs in real time
```

Local `.env` uses `DB_CONNECTION=sqlite` and `QUEUE_CONNECTION=sync` (jobs run inline, no worker needed). Tests (`phpunit.xml`) force `DB_DATABASE=:memory:` and `QUEUE_CONNECTION=sync`.

## Architecture

### Donation → Payment → Alert pipeline (the core flow)

1. **`DonationController::store`** (public, no auth, rate-limited `throttle:donate` — 5/min per IP+slug) validates and persists a `Donation` with `status = 'pending'`. Optional donor-uploaded media (audio/video) is validated for duration via `getID3` against the streamer's `media_duration_tiers` (bigger donation → longer allowed clip). It then calls `PaymentGatewayInterface::createTransaction()` (Midtrans Snap) and returns a `snap_token` to the browser, which opens a Snap popup (`donate/show.blade.php`). **No alert, milestone, or subathon update happens here** — a `Donation` existing does not mean money was received.
2. **`PaymentWebhookController`** (`POST /webhooks/midtrans`, public, CSRF-exempt, signature-verified via `hash_equals` — same pattern as `api_key` checks elsewhere) is the *only* place that ever transitions a `Donation` to `paid` and credits it. It uses a conditional `UPDATE ... WHERE status='pending'` (checking affected-row count) as an idempotency gate, since Midtrans retries notifications — a duplicate/replayed notification is a no-op, not a double-credit.
3. Only on a fresh `pending → paid` transition does the webhook call `ProcessDonationJob::dispatchSync(...)` (with the same `dispatch(...)->delay(5s)` fallback onto the real queue — `$tries=3`, backoff `[5,30,60]` — if sync dispatch throws), plus `Milestone::addAmount()` and `Streamer::addSubathonTime()` if applicable.
4. **`ProcessDonationJob`** assigns the next `seq` for that streamer inside `DB::transaction` + `lockForUpdate()` (prevents two simultaneous donations from colliding on the same seq), builds the SSE payload, and inserts it into `AlertQueue` with a 15-minute `expires_at` (long TTL so an OBS browser source that was briefly offline can still replay it).
5. **`SseController::stream`** is a long-lived `StreamedResponse` per streamer (`/{slug}/sse`), polling `AlertQueue` every 1s for rows with `seq > $lastSeq`, and sending a `ping` + refreshed `stats` heartbeat every 20s. Resume position is resolved in priority order: `Last-Event-ID` header (native browser reconnect) → `?last_seq=` query param (manual JS reconnect after OBS scene switch) → `MAX(seq)` (fresh connect, skips history). Every stream is isolated by `streamer_id` — one streamer's widgets never see another's data.
6. **`ObsController`** renders the actual widget Blade views (`overlay`, `leaderboard`, `milestone`, `subathon`, `running-text`) that the browser source loads; they connect to the SSE endpoint client-side. See `docs/architecture-obs-widgets.md` for how widget styling (Widget Studio), layout (OBS Canvas), and rendering fit together.

When touching this flow, preserve the "donation is never lost" guarantee — DB persistence of the `Donation` and the alert-queue dispatch are treated as separable concerns with independent failure handling.

### Payment (Midtrans)

`App\Services\Payment\PaymentGatewayInterface` (bound to `MidtransSnapGateway` in `AppServiceProvider`) wraps the official `midtrans/midtrans-php` SDK — Snap mode today, deliberately behind an interface since a move to Midtrans's Core API is a planned follow-up (see `docs/superpowers/plans/2026-07-25-payment-gateway-integration.md`). Tests bind `Tests\Support\FakePaymentGateway` as a **singleton** (not `bind()`) — feature tests need to mutate flags like `shouldThrowOnCreate` on the exact instance the controller resolves via constructor injection.

**A `Donation` is only "real" once `status = 'paid'`.** Every public/real-time/financial surface — `StreamerStatsService`, leaderboard, milestone/subathon progress, SSE `stats`, OBS `running-text`, admin dashboard totals, CSV/PDF reports, the heatmap — must query `Streamer::paidDonations()` or `Donation::paid()` (a scope), never the raw `donations()`/`Donation::` query. The two deliberate exceptions are the streamer's own donation history and the admin's full donations list — internal oversight views that show every status with a badge, since a streamer/admin plausibly wants to see a failed or abandoned payment attempt. When adding a new stat/aggregate, decide which bucket it belongs to before wiring it up.

`CleanupExpiredPendingDonationsJob` (scheduled every 15 min, `routes/console.php`) expires stale `pending` rows past Snap's configured expiry window and deletes their orphaned media file — it updates status rather than deleting the row, since a `Donation` is a durable record even unpaid.

### Payout

Manual, admin-executed for now — no gateway interface, since this path makes no external API call at all (see `docs/superpowers/specs/2026-07-25-payout-settlement-design.md`; automated Midtrans disbursement is tracked as a `BACKLOG.md` follow-up). A `paid` `Donation` counts toward its streamer's owed balance (`Streamer::unpaidOutDonations()`) until an admin creates a `Payout` for that streamer via `AdminPayoutController::create()`, which — inside a `DB::transaction()` + `lockForUpdate()` on the target donations, same concurrency pattern `ProcessDonationJob` uses for `AlertQueue.seq` — snapshots the gross/fee/net amounts and the streamer's bank info (`bank_name`/`bank_account_number`/`bank_account_holder`, self-reported on their Settings page) onto the `Payout` row, then assigns `payout_id` to every included donation so they're excluded from future owed-balance calculations.

A `Payout` moves `pending → paid` (via `markPaid()`, requires a `reference` string) or `pending → voided` (via `void()`, releases its donations' `payout_id` back to `null` so they're picked up by the next payout) — **a `paid` payout is immutable**, never voidable, since the money has already left the platform and releasing its donations would let them be paid out a second time. Platform fee (`config('payout.platform_fee_percent', 10)`) and minimum payout threshold (`config('payout.minimum_amount', 50000)`) are snapshotted per-payout at creation time, not recomputed later — a config change only affects payouts created after the change.

### Admin dashboard

Built up across several passes, each reusing existing pieces rather than duplicating them: `AlertFailureService::unresolved()` compares `donation.alert_failed` activity-log entries against `donation.alert_retried` ones (no schema change — a failure is "resolved" once a matching retry succeeds) for the dashboard's alert-failure count card + `AdminAlertFailureController`'s retry-able list; `TrendsService::donationTrend()` aggregates paid donations per day (same WIB-aware date grouping as the streamer heatmap) for the dashboard's Chart.js trend charts; `AdminController::showStreamer()` (linked from the leaderboard) reuses `Streamer::buildStats()` and `Streamer::unpaidOutDonations()` rather than building new aggregation; and `donations()`/`logs()` share the same `search`/`streamer_id`/`from`/`to` filter shape. When adding a new dashboard widget, check whether the data it needs already has a home in one of these before writing a new query.

### Auth / roles

Single `User` model with a `role` column (`admin` | `streamer`), deliberately excluded from `$fillable`/mass-assignment (`$guarded = ['role', 'is_active', ...]`) to prevent privilege escalation — role must be set explicitly, never via user input. `EnsureAdmin`/`EnsureStreamer` middleware (aliased `admin`/`streamer` in `bootstrap/app.php`) gate route groups. A `User` may exist without a `Streamer` profile yet (fresh signup) — the `streamer.setup` route is intentionally reachable with only `auth,verified`, not the `streamer` middleware, to avoid a redirect loop for users completing onboarding.

### Streamer model config blobs

`Streamer` stores several JSON-cast config blobs (`widget_settings`, `canvas_config`, `alert_duration_tiers`, `media_duration_tiers`, `subathon_additional_values`) each with a getter (`getWidgetSettings()`, `getCanvasConfig()`, etc.) that deep-merges saved values over hardcoded defaults — this lets new default keys ship without a migration or breaking existing streamers' saved settings. Follow this merge pattern when adding new configurable widget/canvas options rather than requiring backfill migrations.

`api_key` is excluded from `$fillable` (regenerated only via `Streamer::generateApiKey()`) and from `$hidden`/serialization. Only the SSE endpoint authenticates it, via `hash_equals($streamer->api_key, $request->query('key'))` to prevent timing attacks — OBS Canvas render and individual widget endpoints deliberately do not (see `docs/gotchas.md`). Follow the `hash_equals` pattern for any new endpoint that reads non-public per-streamer data.

`Streamer::buildStats()` delegates to `StreamerStatsService` (all aggregation done in SQL, no in-memory `get()`), which is what both the dashboard and the SSE `stats` event consume.

### Routing structure (`routes/web.php`)

Route order matters: Breeze auth routes load first, then named static/prefixed routes, and the wildcard public routes (`/{slug}`, `/{slug}/donate`, `/{slug}/obs/*`, `/{slug}/sse`) are declared **last** since they'd otherwise swallow everything. Within the admin group, `/impersonate/stop` is declared before the `admin`-gated group (the currently-impersonating user has swapped identity and would fail the `admin` check), and before `/impersonate/{user}` (to avoid being captured by route-model binding).

Every mutating/public-facing route has a dedicated named rate limiter registered in `AppServiceProvider::boot()` (`donate`, `otp-verify`, `settings-update`, `admin-actions`, `sse`, `obs-widget`, `report-export`, etc.) — add a new limiter there rather than reusing an unrelated one when adding endpoints.

### Error handling (`bootstrap/app.php`)

Centralized exception rendering: `ThrottleRequestsException` → `errors.429` view, `ModelNotFoundException` → `errors.404`, generic `HttpException` → `errors.{status}` view (falls back to `errors.generic`) with Indonesian user-facing messages resolved via `resolveHttpMessage()`. All of these branch on `$request->expectsJson()` to return JSON instead for AJAX/API callers. Unhandled `Throwable`s are logged then rendered as `errors.500` in production, or left to Laravel's default debug page when `APP_DEBUG=true`.

### Content moderation

`ProfanityFilter` service sanitizes donor `name`/`message` server-side before storage (banned words are both a global admin-managed list and per-streamer custom lists via `BannedWord`) — filtering happens once, at write time, so all downstream consumers (OBS overlay, dashboard, exports) see already-clean text.

## Conventions

- User-facing strings, comments, and commit messages in this codebase are Indonesian; keep consistent with surrounding code unless told otherwise.
- Git workflow, branch model, and commit message format: see `README.md` ("Git Workflow" section) — not repeated here.

## Further docs

This file is a gateway, not the full picture. Deeper docs live in `docs/`:

- **`docs/gotchas.md`** — non-obvious/deliberate decisions (security tradeoffs, race-condition fixes, route-ordering constraints). Read before "fixing" anything that looks wrong.
- **`docs/architecture-obs-widgets.md`** — how Widget Studio (styling), OBS Canvas (layout), and OBS widget rendering fit together; which config blob owns what.

Add new docs here only when they capture **why**, not **what** — anything derivable by reading the code or `README.md` doesn't need a doc.
