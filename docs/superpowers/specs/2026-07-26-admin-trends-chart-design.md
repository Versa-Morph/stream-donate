# Admin: Donations Trend Chart — Design Spec

Date: 2026-07-26
Status: approved, ready for implementation planning

## Problem

`AdminController::dashboard` only shows "today vs all-time" totals — no way to see how donations are trending day-over-day. This is the second of four independent pieces bundled in `BACKLOG.md`'s original "Admin dashboard — make it useful" item (the first, surfacing alert failures, already shipped); the other two (list filtering, per-streamer drill-down) are separate, later specs.

## Decisions

- **Chart.js**, not a hand-built SVG/CSS chart — this codebase currently has zero charting dependencies (the streamer "heatmap" is a custom CSS grid, not a JS chart), but a proper line chart with tooltips/crosshair/responsive resize isn't worth hand-rolling.
- **Two separate single-axis line charts (amount, count), not one dual-axis chart.** Chart.js makes a mixed bar+line dual-y-axis chart technically easy, but the dataviz skill is explicit that dual-axis (two y-scales) is the #1 chart mistake — two measures of different scale get two charts, not one chart with two scales.
- **Line chart, not bar** — per the dataviz skill's job→form table, "trend over time" maps to line (bar/column is for "compare magnitude"), single sequential hue.
- **Selectable range**: 7/30/90-day toggle buttons, not a fixed window.
- **Platform-wide only** — matches every other stat already on this dashboard (`totalAmount`, `todayAmount`, etc.); per-streamer scoping belongs to the separate "per-streamer drill-down" backlog item, not this one.
- **Colors come from this app's own existing design system**, not the dataviz skill's generic reference palette — `--brand` (`#7c6cfc`, already used throughout the admin/streamer UI) for the amount chart, `--orange` (`#f97316`) for the count chart. This is exactly what the skill's own "plug in a design system" section calls for: the method is invariant, the palette parameter is swapped for the target system's.
- **No dark/light mode work** — confirmed this admin layout has no `prefers-color-scheme`/`data-theme` support at all; single fixed theme throughout, so none of the skill's dark-mode-as-its-own-step requirement applies here.

## Architecture

### Data aggregation + refetch pattern

New `App\Services\TrendsService` (same reusable-service convention as `AlertFailureService`/`StreamerStatsService`):

```php
public function donationTrend(int $days): array
{
    $start = now()->subDays($days - 1)->startOfDay();

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
        $date = now()->subDays($days - 1 - $i);
        $row = $rows->get($date->format('Y-m-d'));
        $labels[] = $date->format('d/m');
        $amounts[] = $row ? (int) $row->total : 0;
        $counts[] = $row ? (int) $row->cnt : 0;
    }

    return compact('labels', 'amounts', 'counts');
}
```

- `AdminController::dashboard()` calls this once with the default range (30 days) for the initial render — mirrors `heatmapInitial` being passed straight into the streamer dashboard view.
- New `GET /admin/dashboard/trends?days=7|30|90` (JSON, validates `days` is one of exactly `[7, 30, 90]`) — called by the range-preset buttons to refetch without a full page reload, mirroring the existing `GET /streamer/heatmap-data` pattern in `StreamerDashboardController`. Per the dataviz interaction spec ("refetch keeps the frame — no skeleton, no layout jump"), the charts hold their previous render at reduced opacity while the fetch is in flight, then update in place.

### Chart rendering

Both charts: **2px line**, round join, single sequential hue, ~10% opacity area fill under the line (a wash, not a solid block), 8px-diameter point markers with a surface-color ring, hairline (1px) recessive gridlines, **no legend box** (single series — the card title already says what's plotted). Y-axis ticks rounded to clean numbers, thousands-comma'd (`Intl.NumberFormat('id-ID')`, matching this app's existing Rupiah formatting convention).

**Tooltip**: Chart.js `interaction: {mode: 'index', intersect: false}` gives the crosshair-that-snaps-to-nearest-X behavior the spec calls for. Tooltip callback formats the value as `Rp 1.234.567` for the amount chart (plain integer for the count chart) — value leads, visually stronger than the label, per "values lead, labels follow."

Colors read from the CSS custom properties already defined in `layouts/app.blade.php` via `getComputedStyle(document.documentElement).getPropertyValue('--brand')` / `--orange` rather than hardcoding hex in JS — stays in sync if those variables ever change.

### Time-range selector + accessible table view

**Range selector**: three toggle buttons ("7 Hari" / "30 Hari" / "90 Hari"), one row, above both charts — reusing this app's existing button-group/active-state styling (`.btn-xs` + an `.active` class) rather than the dataviz skill's fuller "preset-list-with-checkmark" dropdown spec, since a 3-option toggle group already achieves single-select clarity with this app's existing, simpler UI idiom. Clicking a button calls the trends endpoint and redraws both charts (`chart.data = ...; chart.update()`), holding the previous render at reduced opacity while the fetch is in flight.

**Table view** (the dataviz accessibility non-negotiable — "a table view exists"): a small "Lihat sebagai tabel" toggle link under each chart reveals a plain two-column table (Tanggal | Jumlah) built from the same data already in the DOM — no extra request, just a show/hide, consistent with this app's existing no-JS-framework, vanilla-JS convention.

### Testing strategy

- **`TrendsService::donationTrend()`** — seed paid donations across a few different days plus one `pending` donation, assert: pending is excluded, days with no donations zero-fill (not omitted — arrays stay a fixed length equal to `$days`), and a donation just outside the requested window doesn't leak in.
- **`AdminController::dashboard()`** — assert the view receives the initial trend data and the page renders the chart canvases + range buttons.
- **`GET /admin/dashboard/trends?days=7|30|90`** — assert each returns the correctly-shaped JSON (`labels`/`amounts`/`counts`, matching array lengths) and rejects an invalid `days` value (e.g. `days=45`) rather than silently accepting it.

Chart.js's actual rendering is client-side canvas — not something PHPUnit can assert on — so coverage stops at "the data reaching the browser is correct," the same boundary as the existing streamer heatmap's test coverage in this codebase.

## Out of scope (tracked separately)

- **List filtering (donations/activity-log tables)** and **per-streamer drill-down** — the remaining two pieces of `BACKLOG.md`'s original "admin dashboard" item, each its own future spec.
- **Custom date range** (only 7/30/90 presets, no calendar picker) — can be added later if actually wanted.
