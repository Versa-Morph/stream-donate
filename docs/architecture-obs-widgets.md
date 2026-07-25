# Architecture: Widget Studio, OBS Canvas & Widget Rendering

The widget system spans three separate concerns that are easy to conflate: **styling** (Widget Studio), **layout** (OBS Canvas), and **rendering** (per-widget OBS controllers). They read/write different config blobs on `Streamer` and can be reasoned about independently.

## The three layers

| Concern | Owned by | Stored in | Edited via |
|---|---|---|---|
| Styling (colors, fonts, presets, per-widget copy) | `Streamer::getWidgetSettings()` defaults | `streamer.widget_settings` (JSON) | Widget Studio — `resources/views/streamer/widgets.blade.php` + `StreamerDashboardController::saveWidgets/saveAlertSettings/saveMilestoneSettings/saveLeaderboardSettings` |
| Layout (position/size/on-off per widget on a single 1920×1080 canvas) | `Streamer::getCanvasConfig()` defaults | `streamer.canvas_config` (JSON) | OBS Canvas editor — `resources/views/streamer/obs_canvas.blade.php` + `ObsCanvasController::save` |
| Rendering (what OBS actually loads as a Browser Source) | Per-widget Blade views | reads both blobs above at render time | `ObsController` (individual widgets) or `ObsCanvasController::render` (all-in-one canvas) |

A streamer can either add each widget as a **separate Browser Source** (`/{slug}/obs/overlay`, `/{slug}/obs/leaderboard`, etc. — positioned via OBS's own transform) or use the **OBS Canvas** (`/{slug}/obs/canvas`) as a single Browser Source that lays every enabled widget out internally per `canvas_config`. Both paths read the same `widget_settings` for styling — changing a theme in Widget Studio affects both.

## Widget Studio (styling)

`StreamerDashboardController::widgets()` loads the page; each widget type has its own save endpoint rather than one big form:

- `saveWidgets` — generic per-widget styling blob (colors, radius, width, position, preset name, font, etc). Request shape is `{widget: 'alert'|'milestone'|'leaderboard'|'qr'|'subathon'|'running_text', data: {...}}`. Keys are normalized (`[^a-z0-9_]` stripped, lowercased) and values sanitized: anything with `color` in the key must match a hex pattern or gets reset to empty; anything with `duration`/`size`/`width`/`height` gets clamped to `config('alert.widget_validation.max_numeric_value')`. Only the single `$widget` key in the JSON blob is replaced — other widgets' settings are left untouched.
- `saveAlertSettings` — the alert-specific non-visual settings: sound theme (from a fixed `config('alert.sound_presets')` list), YouTube/media-channel toggles, media upload size/duration tiers, `alert_duration_tiers` (donation-amount-based alert duration, capped to `alert_max_duration`). These map to real `Streamer` columns via `fill()`, not the `widget_settings` JSON blob.
- `saveMilestoneSettings` / `saveLeaderboardSettings` — same pattern, real columns (`milestone_title`, `milestone_target`, `leaderboard_title`, `leaderboard_count`, ...) rather than the JSON blob.

So `widget_settings` holds **presentational** config; `alert_duration_tiers`, `milestone_*`, `leaderboard_*` etc are **plain columns** even though they're donation/widget-adjacent. When adding a new widget setting, decide which bucket it belongs in: pure visual styling → `widget_settings[widget]`; anything that needs SQL-level querying/validation constraints → a real column.

Defaults for every widget's styling (including the preset names `default`/`neon`/`fire`/`ice`/`minimal`/`custom` referenced in the README) live in `Streamer::getWidgetSettings()` — see `docs/gotchas.md` re: the deep-merge-over-defaults pattern used there and in `getCanvasConfig()`.

## OBS Canvas (layout)

`ObsCanvasController` is a separate, simpler CRUD: `editor()` shows the drag/drop UI, `save()` validates and writes the full `{width, height, widgets: {notification, leaderboard, milestone, qrcode, ...: {active, x, y, w, h}}}` shape to `canvas_config`, `render()` is the **public, no-auth** endpoint OBS actually loads (`/{slug}/obs/canvas`), which reads `canvas_config` + `widget_settings` + generates a QR SVG via `QrCodeGenerator` and renders everything positioned absolutely inside the 1920×1080 canvas view.

Note `render()` takes `?key=` but — like the individual widget endpoints — does not validate it against `hash_equals`; see the security note in `docs/gotchas.md`.

## Individual widget rendering

`ObsController` (`overlay`, `leaderboard`, `milestone`, `subathon`, `runningText`) is what a streamer points a standalone OBS Browser Source at. Each action just resolves the `Streamer` by slug, pulls the relevant slice of `getWidgetSettings()` if needed, and renders a Blade view — no business logic lives here. Real-time data (new donations, updated stats) is pushed to these views client-side over the SSE connection described in the root `CLAUDE.md` donation pipeline section, not by this controller. `subathon` and `runningText` additionally pass server-rendered initial state (current widget settings / recent messages) so the widget has something to show before the first SSE event arrives.

## Files at a glance

```
app/Http/Controllers/
  StreamerDashboardController.php   # Widget Studio: styling + non-visual widget settings (auth)
  ObsCanvasController.php           # Canvas layout editor + public canvas render
  ObsController.php                 # Public individual widget renders

app/Models/Streamer.php             # getWidgetSettings() / getCanvasConfig() default+merge logic

resources/views/streamer/
  widgets.blade.php                 # Widget Studio UI (large — per-widget style editors + live preview)
  obs_canvas.blade.php              # Drag/drop canvas editor UI

resources/views/obs/
  overlay.blade.php, leaderboard.blade.php, milestone.blade.php,
  subathon.blade.php, running_text.blade.php   # Individual OBS Browser Source targets
  canvas.blade.php                             # All-in-one OBS Canvas Browser Source target
```
