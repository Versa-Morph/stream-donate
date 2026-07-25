# Admin: Per-Streamer Drill-Down — Design Spec

Date: 2026-07-26
Status: approved, ready for implementation planning

## Problem

The admin dashboard's streamer leaderboard shows name, donation count, and lifetime total — but no way to click into a streamer for more detail. This is the third of four independent pieces bundled in `BACKLOG.md`'s original "Admin dashboard — make it useful" item (alert failures and the trends chart already shipped); the remaining piece (list filtering on the donations/activity-log tables) is a separate, later spec.

## Decisions

- **Read-only view, no new lifecycle** — stays a single method on the existing `AdminController` (matching `donations()`/`users()`/`logs()`), not a new dedicated controller like `AdminPayoutController`/`AdminAlertFailureController` got — those earned a dedicated controller because each has multiple actions and a real lifecycle; this doesn't.
- **Reuses existing pieces rather than rebuilding them**: `Streamer::buildStats()` (already delegates to `StreamerStatsService`, paid-only) for headline numbers, `Streamer::unpaidOutDonations()` for owed balance, and the **already-existing** `?streamer_id=` filter on `AdminController::donations()` for the "view all donations" link — no new filtered/paginated donations list gets built for this feature.
- **Entry point**: the leaderboard row's streamer name/slug becomes a link to the drill-down page; the existing "Login As" (impersonate) button stays exactly where it is in the row, unchanged.
- **Page scope**: stats + recent activity + links out. Explicitly not bundling in a read view of the streamer's settings/config blobs (widget theme, etc.) — out of scope for this pass.

## Architecture

### Route & data

`GET /admin/streamers/{streamer}` → `AdminController::showStreamer(Streamer $streamer)` (route model binding), inside the existing `admin`-gated route group — no new middleware.

Data pulled together, all reused from existing pieces:
- `$streamer->buildStats()` — total donations, today's donations, donor count (paid-only, same numbers the streamer's own dashboard/SSE already show).
- `$streamer->unpaidOutDonations()->sum('amount')` — owed balance, reusing the payout feature's existing relation.
- `$streamer->donations()->with(...)->orderByDesc('created_at')->limit(...)` — a fixed-limit recent list (not the full filtered/paginated view — that's what the "view all" link is for).
- `ActivityLog::where('streamer_id', $streamer->id)->orderByDesc('created_at')->limit(...)` — recent activity, that streamer only.

Quick links: `route('admin.donations', ['streamer_id' => $streamer->id])` (existing filter, reused as-is), `route('admin.payouts.index')` (generic — no per-streamer filter exists there yet, and building one isn't worth it just for this link), `route('donate.show', $streamer->slug)` (public donation page).

### Page layout & edge cases

Sections, `<x-app-layout>` (matching every other admin view):

1. **Header** — display name, slug, join date, accepting-donations status badge, link to public donation page.
2. **Stat cards** — total donations, today's donations, donor count, owed balance.
3. **Recent donations** (fixed-limit) with a "Lihat semua →" link to the filtered donations page.
4. **Recent activity** (fixed-limit, that streamer's rows only).
5. **Quick links** — filtered donations page, `/admin/payouts`, public donation page.

Each list uses the same `@forelse ... @empty ... @endforelse` empty-state pattern already used everywhere else in this codebase — a brand-new streamer with zero activity renders cleanly.

**Leaderboard change**: the streamer name/slug cell in `resources/views/admin/dashboard.blade.php`'s leaderboard table becomes an `<a href="{{ route('admin.streamers.show', $s) }}">`; the impersonate form/button in that row is untouched.

### Testing strategy

- **`AdminController::showStreamer()`** — seed two streamers with different donations/activity, assert the page shows only the requested streamer's stats/recent-donations/recent-activity (never the other streamer's — the classic drill-down bug is leaking cross-streamer data), and the correct owed-balance figure.
- **Empty state** — a freshly-created streamer with zero donations/activity renders without error, showing empty-state text for each list.
- **Leaderboard link** — the admin dashboard's streamer-leaderboard row links to `route('admin.streamers.show', $streamer)`.

No separate "non-admin gets 403" test, consistent with the Payout/AlertFailure work — that's the existing `admin` middleware's behavior, not re-tested per new route.

## Out of scope (tracked separately)

- **List filtering** on the donations/activity-log tables — the remaining piece of `BACKLOG.md`'s original "admin dashboard" item, its own future spec.
- **Streamer settings/config read view** (widget theme, canvas layout, etc.) on this page.
- **Per-streamer filter on the payouts page** — not worth building just for this feature's "quick links" section.
