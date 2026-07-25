# Backlog

Planned work, not yet built. Update/reorder freely as priorities shift — unlike `docs/`, this file is expected to go stale and get rewritten. Move an item to `README.md`/`docs/` (as shipped behavior) once it's actually implemented; delete it from here at that point, don't leave a stale duplicate.

## 1. Widget Studio — full customization coverage

Not every widget exposes all its visual settings in `resources/views/streamer/widgets.blade.php` yet — some fields only exist as defaults in `Streamer::getWidgetSettings()` with no UI control. Needs an audit: for each widget (`alert`, `milestone`, `leaderboard`, `qr`, `subathon`, `running_text`), diff the default keys in `getWidgetSettings()` against what the Widget Studio form actually lets a streamer edit, then add the missing controls.

## 2. Automated payout disbursement (Midtrans Payout/Iris API)

Manual, admin-executed payout tracking (owed-balance ledger, bank-info snapshot, mark-paid/void lifecycle) is shipped — see `CLAUDE.md`'s "Payout" section and `docs/superpowers/plans/2026-07-25-payout-settlement-implementation.md`. This item is the deliberately deferred automation piece: integrate Midtrans's Payout/Iris API so an admin can trigger the actual bank transfer from within the app instead of doing it externally. Needs a separate Midtrans product enabled on the merchant account plus real bank-account KYC. When built, introduces a `PayoutGatewayInterface` (mirroring `PaymentGatewayInterface`'s shape) — the existing manual flow stays as an always-available fallback, not replaced.

## 3. Admin dashboard — make it useful

`AdminController::dashboard` currently ships: platform totals, today's stats, recent donations list, recent activity log, top-25 streamer leaderboard by lifetime donation sum, an unresolved-alert-failures count card + dedicated retry-able list page (shipped — `docs/superpowers/specs/2026-07-25-admin-alert-failures-design.md`), and a 7/30/90-day donations trend chart (shipped — `docs/superpowers/specs/2026-07-26-admin-trends-chart-design.md`). Missing / worth adding:
- Filtering/search on the recent donations & activity log tables (currently fixed-limit lists, no date range or streamer filter)
- Per-streamer drill-down (click a streamer in the leaderboard → their own stats page)
