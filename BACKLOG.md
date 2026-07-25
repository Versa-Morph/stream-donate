# Backlog

Planned work, not yet built. Update/reorder freely as priorities shift — unlike `docs/`, this file is expected to go stale and get rewritten. Move an item to `README.md`/`docs/` (as shipped behavior) once it's actually implemented; delete it from here at that point, don't leave a stale duplicate.

## 1. Widget Studio — full customization coverage

Not every widget exposes all its visual settings in `resources/views/streamer/widgets.blade.php` yet — some fields only exist as defaults in `Streamer::getWidgetSettings()` with no UI control. Needs an audit: for each widget (`alert`, `milestone`, `leaderboard`, `qr`, `subathon`, `running_text`), diff the default keys in `getWidgetSettings()` against what the Widget Studio form actually lets a streamer edit, then add the missing controls.

## 2. Streamer payout / settlement

Payment gateway integration (Midtrans Snap, capture-only) is shipped — see `CLAUDE.md`'s "Payment" section and `docs/superpowers/plans/2026-07-25-payment-gateway-integration.md`. This item is the deliberately deferred piece: once donations flow through Midtrans, the money lands in one platform-level merchant account. How the platform then distributes each streamer's share to them (schedule, fees/cut if any, payout method, reconciliation/reporting) is not scoped or designed yet — do this now that real payment data exists to design against.

## 3. Admin dashboard — make it useful

`AdminController::dashboard` currently ships: platform totals, today's stats, recent donations list, recent activity log, top-25 streamer leaderboard by lifetime donation sum. Missing / worth adding:
- Trends over time (donations/day chart, not just today vs all-time)
- Filtering/search on the recent donations & activity log tables (currently fixed-limit lists, no date range or streamer filter)
- Per-streamer drill-down (click a streamer in the leaderboard → their own stats page)
- Surfacing `ProcessDonationJob` failures (`failed()` logs to `ActivityLog` as `donation.alert_failed`, see `docs/gotchas.md`) somewhere an admin can actually see and act on them, not just in logs
