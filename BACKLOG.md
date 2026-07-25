# Backlog

Planned work, not yet built. Update/reorder freely as priorities shift — unlike `docs/`, this file is expected to go stale and get rewritten. Move an item to `README.md`/`docs/` (as shipped behavior) once it's actually implemented; delete it from here at that point, don't leave a stale duplicate.

## 1. Widget Studio — full customization coverage

Not every widget exposes all its visual settings in `resources/views/streamer/widgets.blade.php` yet — some fields only exist as defaults in `Streamer::getWidgetSettings()` with no UI control. Needs an audit: for each widget (`alert`, `milestone`, `leaderboard`, `qr`, `subathon`, `running_text`), diff the default keys in `getWidgetSettings()` against what the Widget Studio form actually lets a streamer edit, then add the missing controls.

## 2. Payment gateway integration — Midtrans Snap

**Spec'd, ready for implementation planning**: `docs/superpowers/specs/2026-07-25-payment-gateway-design.md`. Provider: Midtrans (Snap, popup UX). Capture-only for now — see item 2a below for the deferred piece.

## 2a. Streamer payout / settlement

Explicitly deferred out of the payment gateway integration (item 2) — once donations flow through Midtrans, the money lands in one platform-level merchant account. This item is: how the platform then distributes each streamer's share to them (schedule, fees/cut if any, payout method, reconciliation/reporting). Not scoped or designed yet — do this after item 2 ships and real payment data exists to design against.

## 3. Admin dashboard — make it useful

`AdminController::dashboard` currently ships: platform totals, today's stats, recent donations list, recent activity log, top-25 streamer leaderboard by lifetime donation sum. Missing / worth adding:
- Trends over time (donations/day chart, not just today vs all-time)
- Filtering/search on the recent donations & activity log tables (currently fixed-limit lists, no date range or streamer filter)
- Per-streamer drill-down (click a streamer in the leaderboard → their own stats page)
- Surfacing `ProcessDonationJob` failures (`failed()` logs to `ActivityLog` as `donation.alert_failed`, see `docs/gotchas.md`) somewhere an admin can actually see and act on them, not just in logs
