# Admin: Donations & Logs Date-Range/Streamer Filtering — Design Spec

Date: 2026-07-26
Status: approved, ready for implementation planning

## Problem

`BACKLOG.md`'s original "Admin dashboard — make it useful" item listed "filtering/search on the recent donations & activity log tables" as missing. On investigation, this was stale: `AdminController::donations()` already has `search` + `streamer_id` filtering (paginated), and `logs()` already has an `action` filter (paginated) — these were already built, likely before this round of admin-dashboard work started. This is the fourth and final piece of that original backlog item; the actual remaining gap is narrower: **date-range filtering on both pages, plus a streamer filter on `logs()`** (which `donations()` already has).

## Decisions

- **Unbounded by default** — `from`/`to` are opt-in narrowing filters, not defaulted to a window (unlike `ReportController`'s existing `from`/`to`, which default to "this month"). Omitting both keeps today's all-time behavior unchanged for admins who don't touch the filter.
- **`logs()` gains a `streamer_id` filter**, identical to the one `donations()` already has — lets an admin see just one streamer's activity log entries.
- **Follows `ReportController`'s existing `from`/`to` query-param pattern** for consistency, just without its default-to-this-month behavior.

## Architecture

### Filters

`AdminController::donations()` gains date-range:
```php
if ($from = $request->input('from')) {
    $query->whereDate('created_at', '>=', $from);
}
if ($to = $request->input('to')) {
    $query->whereDate('created_at', '<=', $to);
}
```

`AdminController::logs()` gains the same date-range treatment, plus:
```php
if ($streamerId = $request->input('streamer_id')) {
    $query->where('streamer_id', $streamerId);
}
```
`logs()` also passes `$streamers` to its view (currently doesn't — `donations()` already does, for its existing dropdown).

### Views

Both `resources/views/admin/donations.blade.php` and `admin/logs.blade.php` gain `<input type="date" name="from">`/`name="to">` in their existing `.filter-bar` forms, pre-filled from `request('from')`/`request('to')` — persisting across pagination via the existing `withQueryString()` already in place on both paginated queries. `logs.blade.php` also gains the streamer `<select>`, copied from `donations.blade.php`'s existing one.

### Testing strategy

- **`donations()` date range** — seed donations on different dates, assert `from`/`to` correctly include/exclude, and that omitting both still returns everything (unbounded default).
- **`logs()` date range + streamer filter** — seed activity logs across different dates/streamers, assert each filter narrows correctly, and that combining date range with the existing `action` filter still works together (AND logic, not one replacing the other).
- **View rendering** — both pages show the date inputs pre-filled with `request('from')`/`request('to')` after a filtered request (persists across pagination, not reset on page 2); `logs.blade.php` additionally shows the streamer dropdown with the selected streamer marked.

## Out of scope (tracked separately)

- **Widget Studio full customization coverage** and **automated payout disbursement** — the other two remaining `BACKLOG.md` items, unrelated to this one.
