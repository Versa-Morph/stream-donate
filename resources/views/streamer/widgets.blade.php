<x-app-layout>
@push('styles')
<style>
/* ══════════════════════════════════════════════
   WIDGET STUDIO — Phase 1
   Layout · Tab Nav · OBS URL Cards
══════════════════════════════════════════════ */

/* ── Page wrap ── */
.ws-wrap {
    padding: 28px 32px;
    max-width: 1200px;
    margin: 0 auto;
}

/* ── Page header ── */
.ws-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 16px;
    flex-wrap: wrap;
    margin-bottom: 28px;
}
.ws-header-left {}
.ws-title {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 22px;
    font-weight: 700;
    letter-spacing: -.5px;
    margin-bottom: 4px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.ws-title-icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    background: linear-gradient(135deg, var(--brand), var(--purple));
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 0 20px var(--brand-glow);
    flex-shrink: 0;
}
.ws-title-icon .iconify { width: 20px; height: 20px; color: #fff; }
.ws-sub {
    font-size: 13px;
    color: var(--text-3);
    padding-left: 46px;
}
.ws-header-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

/* ── Main layout: tabs (left) + panel (right) ── */
.ws-body {
    display: grid;
    grid-template-columns: 220px 1fr;
    gap: 20px;
    align-items: start;
}

/* ── Tab sidebar ── */
.ws-sidebar {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    overflow: hidden;
    position: sticky;
    top: 80px;
}
.ws-nav-label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .8px;
    text-transform: uppercase;
    color: var(--text-3);
    padding: 14px 16px 6px;
}
.ws-nav-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 11px 16px;
    font-size: 13px;
    font-weight: 500;
    color: var(--text-2);
    cursor: pointer;
    border-left: 3px solid transparent;
    transition: all .15s;
    user-select: none;
    position: relative;
}
.ws-nav-item:hover {
    color: var(--text);
    background: rgba(255,255,255,.04);
}
.ws-nav-item.active {
    color: var(--brand-light);
    background: rgba(124,108,252,.08);
    border-left-color: var(--brand);
    font-weight: 600;
}
.ws-nav-item .iconify {
    width: 16px; height: 16px;
    flex-shrink: 0;
}
.ws-nav-divider {
    height: 1px;
    background: var(--border);
    margin: 6px 0;
}

/* Widget type badge */
.ws-nav-badge {
    margin-left: auto;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .5px;
    padding: 2px 6px;
    border-radius: 10px;
    background: rgba(124,108,252,.12);
    color: var(--brand-light);
    border: 1px solid rgba(124,108,252,.2);
}
.ws-nav-badge.live {
    background: rgba(34,211,160,.1);
    color: var(--green);
    border-color: rgba(34,211,160,.2);
}

/* ── Tab panel ── */
.ws-panel { display: none; }
.ws-panel.active { display: block; }

/* ── Panel section card ── */
.ws-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 24px 28px;
    margin-bottom: 16px;
}
.ws-card:last-child { margin-bottom: 0; }

.ws-card-head {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 1px solid var(--border);
}
.ws-card-icon {
    width: 36px; height: 36px;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.ws-card-icon .iconify { width: 18px; height: 18px; }
.ws-card-title {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 15px;
    font-weight: 700;
    letter-spacing: -.2px;
}
.ws-card-sub {
    font-size: 12px;
    color: var(--text-3);
    margin-top: 2px;
}
.ws-card-head-right { margin-left: auto; }

/* ── OBS URL grid ── */
.obs-url-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 12px;
}

/* Single OBS URL row */
.obs-url-row {
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 14px 16px;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: border-color .2s;
}
.obs-url-row:hover { border-color: var(--border-2); }

.obs-url-icon {
    width: 32px; height: 32px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.obs-url-icon .iconify { width: 16px; height: 16px; }

.obs-url-info { flex: 1; min-width: 0; }
.obs-url-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--text-2);
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.obs-url-label .iconify { width: 12px; height: 12px; opacity: .6; }
.obs-url-input {
    width: 100%;
    font-size: 11px;
    padding: 7px 10px;
    background: var(--surface-3);
    border: 1px solid var(--border);
    border-radius: 6px;
    color: var(--text-3);
    font-family: monospace;
    outline: none;
    box-sizing: border-box;
    transition: border-color .15s;
}
.obs-url-input:focus {
    border-color: var(--brand);
    color: var(--text-2);
}

.obs-url-actions {
    display: flex;
    flex-direction: column;
    gap: 6px;
    flex-shrink: 0;
}
.obs-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 7px 12px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    transition: all .15s;
    border: 1px solid var(--border);
    background: var(--surface-3);
    color: var(--text-2);
    white-space: nowrap;
    text-decoration: none;
}
.obs-btn:hover {
    border-color: var(--brand);
    background: rgba(124,108,252,.1);
    color: var(--brand-light);
}
.obs-btn .iconify { width: 12px; height: 12px; }
.obs-btn.primary {
    border-color: rgba(124,108,252,.3);
    background: rgba(124,108,252,.08);
    color: var(--brand-light);
}
.obs-btn.primary:hover {
    background: rgba(124,108,252,.18);
    border-color: var(--brand);
}

/* ── Status indicator (live dot) ── */
.status-live {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .5px;
    color: var(--green);
}
.status-live-dot {
    width: 6px; height: 6px;
    border-radius: 50%;
    background: var(--green);
    animation: livePulse 2s infinite;
}
@keyframes livePulse {
    0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(34,211,160,.4); }
    50% { opacity: .6; box-shadow: 0 0 0 4px rgba(34,211,160,0); }
}

/* ── Info box ── */
.ws-info-box {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 12px 16px;
    background: rgba(124,108,252,.06);
    border: 1px solid rgba(124,108,252,.15);
    border-radius: var(--radius);
    font-size: 12px;
    color: var(--text-2);
    line-height: 1.6;
    margin-bottom: 16px;
}
.ws-info-box .iconify {
    width: 16px; height: 16px;
    color: var(--brand-light);
    flex-shrink: 0;
    margin-top: 1px;
}
.ws-info-box.warning {
    background: rgba(251,191,36,.05);
    border-color: rgba(251,191,36,.15);
}
.ws-info-box.warning .iconify { color: var(--yellow); }

/* ── Subathon Timer Styles ── */
.ws-tier-rows { margin-top: 12px; }
.ws-tier-row {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 0;
    border-bottom: 1px solid var(--border);
    font-size: 13px;
    color: var(--text-2);
}
.ws-tier-row:last-child { border-bottom: none; }
.ws-tier-row input {
    width: 90px;
    padding: 6px 10px;
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: 6px;
    color: var(--text);
    font-size: 12px;
}
.ws-tier-row input:focus {
    border-color: var(--brand);
    outline: none;
}
.ws-tier-remove {
    background: none;
    border: none;
    color: var(--text-3);
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
}
.ws-tier-remove:hover { color: var(--red); background: rgba(244,63,94,.1); }
.ws-tier-add {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 12px;
    background: none;
    border: 1px dashed var(--border);
    border-radius: 6px;
    color: var(--text-3);
    font-size: 12px;
    cursor: pointer;
    margin-top: 8px;
    transition: all .15s;
}
.ws-tier-add:hover { border-color: var(--brand); color: var(--brand); }
.ws-tier-add .iconify { width: 14px; height: 14px; }

.ws-timer-display {
    background: linear-gradient(135deg, var(--surface), var(--surface-2));
    border: 1px solid var(--border);
    border-radius: var(--radius-lg);
    padding: 28px;
    text-align: center;
    margin-bottom: 20px;
}
.ws-timer-value {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 48px;
    font-weight: 700;
    letter-spacing: -1px;
    color: var(--text);
    line-height: 1;
    margin-bottom: 6px;
}
.ws-timer-label {
    font-size: 12px;
    color: var(--text-3);
    text-transform: uppercase;
    letter-spacing: 1px;
}
.ws-timer-actions { display: flex; gap: 10px; justify-content: center; }
.ws-timer-btn {
    padding: 10px 18px;
    border-radius: var(--radius);
    border: 1px solid var(--border);
    background: var(--surface-2);
    color: var(--text-2);
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: all .15s;
    display: flex;
    align-items: center;
    gap: 6px;
}
.ws-timer-btn:hover { border-color: var(--border-2); color: var(--text); }
.ws-timer-btn.primary { background: var(--brand); border-color: var(--brand); color: #fff; }
.ws-timer-btn.primary:hover { opacity: .9; }
.ws-timer-btn .iconify { width: 14px; height: 14px; }

.ws-opt-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 0;
    border-bottom: 1px solid var(--border);
}
.ws-opt-row:first-of-type { padding-top: 0; }
.ws-opt-row:last-of-type { border-bottom: none; }
.ws-opt-label { flex: 1; }
.ws-opt-label > div:first-child { font-size: 13px; font-weight: 500; color: var(--text); }
.ws-opt-sub { font-size: 11px; color: var(--text-3); margin-top: 2px; }
.ws-opt-input { display: flex; align-items: center; gap: 6px; }
.ws-opt-input input {
    width: 80px;
    padding: 8px 10px;
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: 6px;
    color: var(--text);
    font-size: 13px;
}
.ws-opt-unit { font-size: 12px; color: var(--text-3); }

.ws-toggle { position: relative; width: 44px; height: 24px; flex-shrink: 0; }
.ws-toggle input { opacity: 0; width: 0; height: 0; }
.ws-toggle-slider {
    position: absolute;
    cursor: pointer;
    inset: 0;
    background: var(--surface-3);
    border-radius: 24px;
    transition: .25s;
}
.ws-toggle-slider::before {
    content: '';
    position: absolute;
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background: #fff;
    border-radius: 50%;
    transition: .25s;
}
.ws-toggle input:checked + .ws-toggle-slider { background: var(--brand); }
.ws-toggle input:checked + .ws-toggle-slider::before { transform: translateX(20px); }

.ws-save-row { display: flex; align-items: center; gap: 12px; margin-top: 20px; }
.ws-save-btn {
    padding: 10px 20px;
    background: linear-gradient(135deg, var(--brand), #6356e8);
    border: none;
    border-radius: var(--radius);
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all .2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}
.ws-save-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 16px var(--brand-glow); }
.ws-save-btn:disabled { opacity: .55; cursor: not-allowed; }
.ws-save-btn .iconify { width: 14px; height: 14px; }
.ws-save-msg { color: var(--green); font-size: 12px; opacity: 0; transition: opacity .3s; }
.ws-save-msg.show { opacity: 1; }

.ws-url-box {
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 14px;
    margin-bottom: 20px;
}

/* ── Widget meta tags ── */
.ws-tags {
    display: flex;
    align-items: center;
    gap: 6px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}
.ws-tag {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .3px;
    padding: 3px 8px;
    border-radius: 6px;
    border: 1px solid;
}
.ws-tag.blue   { background: rgba(124,108,252,.08); color: var(--brand-light); border-color: rgba(124,108,252,.2); }
.ws-tag.green  { background: rgba(34,211,160,.08);  color: var(--green);       border-color: rgba(34,211,160,.2); }
.ws-tag.orange { background: rgba(249,115,22,.08);  color: var(--orange);      border-color: rgba(249,115,22,.2); }
.ws-tag.yellow { background: rgba(251,191,36,.08);  color: var(--yellow);      border-color: rgba(251,191,36,.2); }
.ws-tag.purple { background: rgba(168,85,247,.08);  color: var(--purple);      border-color: rgba(168,85,247,.2); }
.ws-tag .iconify { width: 11px; height: 11px; }

/* ── Section divider label ── */
.ws-section-label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: .8px;
    text-transform: uppercase;
    color: var(--text-3);
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.ws-section-label::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border);
    margin-left: 4px;
}

/* ── Placeholder coming soon ── */
.ws-coming-soon {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-3);
}
.ws-coming-soon .iconify {
    width: 48px; height: 48px;
    opacity: .3;
    margin-bottom: 12px;
    display: block;
    margin-left: auto;
    margin-right: auto;
}
.ws-coming-soon-title {
    font-size: 15px;
    font-weight: 600;
    color: var(--text-2);
    margin-bottom: 6px;
}
.ws-coming-soon-sub {
    font-size: 12px;
    line-height: 1.6;
}

/* ── Widget size hint chips ── */
.ws-size-chips {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.ws-size-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 11px;
    font-weight: 600;
    color: var(--text-3);
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 4px 10px;
}
.ws-size-chip .iconify { width: 13px; height: 13px; }

/* ══════════════════════════════════════════════
   WIDGET STUDIO — Phase 2
   UI Customizer: Preset Picker + Custom Controls
══════════════════════════════════════════════ */

/* ── Preset picker grid ── */
.wc-presets {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
    gap: 10px;
    margin-bottom: 20px;
}
.wc-preset-card {
    border: 2px solid var(--border);
    border-radius: 10px;
    padding: 10px 8px;
    cursor: pointer;
    text-align: center;
    transition: all .18s;
    background: var(--surface-2);
    position: relative;
    overflow: hidden;
}
.wc-preset-card:hover {
    border-color: var(--border-2);
    background: var(--surface-3);
}
.wc-preset-card.active {
    border-color: var(--brand);
    background: rgba(124,108,252,.08);
    box-shadow: 0 0 0 1px rgba(124,108,252,.2);
}
.wc-preset-swatch {
    width: 100%;
    height: 32px;
    border-radius: 6px;
    margin-bottom: 8px;
    border: 1px solid rgba(255,255,255,.06);
}
.wc-preset-name {
    font-size: 11px;
    font-weight: 700;
    color: var(--text-2);
    letter-spacing: .2px;
}
.wc-preset-card.active .wc-preset-name {
    color: var(--brand-light);
}
.wc-preset-check {
    position: absolute;
    top: 5px; right: 5px;
    width: 16px; height: 16px;
    border-radius: 50%;
    background: var(--brand);
    display: none;
    align-items: center; justify-content: center;
}
.wc-preset-check .iconify { width: 10px; height: 10px; color: #fff; }
.wc-preset-card.active .wc-preset-check { display: flex; }

/* ── Custom controls panel ── */
.wc-custom-panel {
    display: none;
    flex-direction: column;
    gap: 0;
}
.wc-custom-panel.visible { display: flex; }

/* Control rows */
.wc-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 0;
    border-bottom: 1px solid var(--border);
}
.wc-row:last-child { border-bottom: none; }

.wc-row-label {
    flex: 0 0 140px;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-2);
    display: flex;
    align-items: center;
    gap: 6px;
}
.wc-row-label .iconify { width: 13px; height: 13px; opacity: .7; }

.wc-row-ctrl { flex: 1; display: flex; align-items: center; gap: 10px; }

/* Color picker swatch button */
.wc-color-wrap {
    position: relative;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.wc-color-swatch {
    width: 32px; height: 32px;
    border-radius: 8px;
    border: 2px solid var(--border-2);
    cursor: pointer;
    transition: border-color .15s, transform .1s;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0,0,0,.3);
}
.wc-color-swatch:hover { border-color: var(--brand); transform: scale(1.05); }
.wc-color-input {
    position: absolute;
    width: 32px; height: 32px;
    opacity: 0;
    cursor: pointer;
    top: 0; left: 0;
}
/* Opacity row (color picker + opacity slider for rgba fields) */
.wc-opacity-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
}
.wc-opacity-label {
    font-size: 10px;
    font-weight: 700;
    color: var(--text-3);
    letter-spacing: .5px;
    text-transform: uppercase;
    white-space: nowrap;
}
.wc-opacity-val {
    font-size: 11px;
    font-weight: 700;
    color: var(--text-3);
    width: 32px;
    text-align: right;
    font-family: monospace;
}

/* Gradient stop pickers */
.wc-grad-stops {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
}
.wc-grad-stop-label {
    font-size: 10px;
    font-weight: 700;
    color: var(--text-3);
    letter-spacing: .4px;
    text-transform: uppercase;
    white-space: nowrap;
}

/* Opacity + slider */
.wc-slider-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
}
.wc-slider {
    flex: 1;
    -webkit-appearance: none;
    height: 4px;
    border-radius: 2px;
    background: var(--border-2);
    outline: none;
    cursor: pointer;
}
.wc-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 14px; height: 14px;
    border-radius: 50%;
    background: var(--brand);
    border: 2px solid rgba(255,255,255,.2);
    cursor: pointer;
    transition: transform .1s;
}
.wc-slider::-webkit-slider-thumb:hover { transform: scale(1.2); }
.wc-slider-val {
    font-size: 11px;
    font-weight: 700;
    color: var(--text-3);
    width: 36px;
    text-align: right;
    font-family: monospace;
}

/* Number input (radius, width) */
.wc-number {
    width: 72px;
    background: var(--surface-3);
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 6px 8px;
    font-size: 12px;
    color: var(--text-2);
    outline: none;
    transition: border-color .15s;
    text-align: center;
}
.wc-number:focus { border-color: var(--brand); }
.wc-unit {
    font-size: 11px;
    color: var(--text-3);
    font-weight: 600;
}

/* Select (position, preset) */
.wc-select {
    background: var(--surface-3);
    border: 1px solid var(--border);
    border-radius: 6px;
    padding: 6px 10px;
    font-size: 12px;
    color: var(--text-2);
    outline: none;
    cursor: pointer;
    transition: border-color .15s;
}
.wc-select:focus { border-color: var(--brand); }

.wc-gradient-preview {
    width: 48px; height: 24px;
    border-radius: 5px;
    border: 1px solid rgba(255,255,255,.08);
    flex-shrink: 0;
}

/* Save button */
.wc-save-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 22px;
    background: linear-gradient(135deg, var(--brand), #6356e8);
    border: none;
    border-radius: var(--radius);
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all .18s;
    box-shadow: 0 4px 16px var(--brand-glow);
    margin-top: 18px;
    letter-spacing: -.1px;
}
.wc-save-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 24px var(--brand-glow);
}
.wc-save-btn:active { transform: translateY(0); }
.wc-save-btn .iconify { width: 15px; height: 15px; }
.wc-save-btn.saving { opacity: .7; pointer-events: none; }

/* Reset link */
.wc-reset-link {
    font-size: 11px;
    color: var(--text-3);
    cursor: pointer;
    text-decoration: underline;
    background: none;
    border: none;
    padding: 0;
    margin-left: 12px;
    margin-top: 18px;
    align-self: flex-end;
    transition: color .15s;
}
.wc-reset-link:hover { color: var(--text-2); }

/* Save row */
.wc-save-row {
    display: flex;
    align-items: center;
    gap: 0;
    flex-wrap: wrap;
}

/* ── Multi-step wizard nav ── */
.wc-steps {
    display: flex;
    align-items: center;
    gap: 0;
    margin-bottom: 20px;
    border-bottom: 1px solid var(--border);
    padding-bottom: 0;
    overflow-x: auto;
}
.wc-step-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 10px 14px;
    font-size: 12px;
    font-weight: 600;
    color: var(--text-3);
    background: none;
    border: none;
    border-bottom: 2px solid transparent;
    cursor: pointer;
    white-space: nowrap;
    transition: all .15s;
    margin-bottom: -1px;
}
.wc-step-btn:hover { color: var(--text-2); }
.wc-step-btn.active { color: var(--brand-light); border-bottom-color: var(--brand); }
.wc-step-btn .iconify { width: 14px; height: 14px; flex-shrink: 0; }
.wc-step-num {
    width: 18px; height: 18px;
    border-radius: 50%;
    background: var(--surface-3);
    border: 1px solid var(--border-2);
    font-size: 10px;
    font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    transition: all .15s;
    color: var(--text-3);
}
.wc-step-btn.active .wc-step-num {
    background: var(--brand);
    border-color: var(--brand);
    color: #fff;
}
.wc-step-panel { display: none; }
.wc-step-panel.active { display: block; }

/* ── Layout picker cards (visual diagram) ── */
.wc-layout-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-bottom: 4px;
}
.wc-layout-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 14px 10px;
    border-radius: 10px;
    border: 2px solid var(--border);
    background: var(--surface-2);
    cursor: pointer;
    transition: all .15s;
    position: relative;
    text-align: center;
}
.wc-layout-card:hover { border-color: var(--brand); background: var(--surface-3); }
.wc-layout-card.active { border-color: var(--brand); background: rgba(124,108,252,.08); }
.wc-layout-check {
    position: absolute; top: 5px; right: 5px;
    width: 16px; height: 16px;
    border-radius: 50%;
    background: var(--brand);
    display: none;
    align-items: center; justify-content: center;
}
.wc-layout-check .iconify { width: 10px; height: 10px; color: #fff; }
.wc-layout-card.active .wc-layout-check { display: flex; }
.wc-layout-name {
    font-size: 11px;
    font-weight: 700;
    color: var(--text-2);
}
/* Layout diagram SVG-like boxes */
.wc-layout-diagram {
    width: 64px; height: 36px;
    border-radius: 6px;
    border: 1px solid var(--border-2);
    background: rgba(124,108,252,.08);
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 5px 6px;
    overflow: hidden;
}
.wc-layout-diagram.centered {
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 3px;
    padding: 4px;
}
.wc-layout-diagram.side {
    gap: 6px;
    padding: 4px 6px;
    border-radius: 4px;
    align-items: center;
}
.ld-icon  { width: 14px; height: 14px; border-radius: 4px; background: rgba(124,108,252,.3); flex-shrink: 0; }
.ld-lines { flex: 1; display: flex; flex-direction: column; gap: 3px; }
.ld-line  { height: 3px; border-radius: 2px; background: rgba(241,241,246,.25); }
.ld-line.accent { background: var(--brand); opacity: .7; width: 60%; }
.ld-line.small  { width: 80%; opacity: .6; }
.ld-cline { height: 3px; border-radius: 2px; background: rgba(241,241,246,.25); width: 70%; }
.ld-cline.accent { background: var(--brand); opacity: .7; width: 50%; }

/* ── Style picker cards ── */
.wc-style-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-bottom: 4px;
}
.wc-style-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 14px 10px;
    border-radius: 10px;
    border: 2px solid var(--border);
    background: var(--surface-2);
    cursor: pointer;
    transition: all .15s;
    position: relative;
    text-align: center;
}
.wc-style-card:hover { border-color: var(--brand); background: var(--surface-3); }
.wc-style-card.active { border-color: var(--brand); background: rgba(124,108,252,.08); }
.wc-style-check {
    position: absolute; top: 5px; right: 5px;
    width: 16px; height: 16px;
    border-radius: 50%;
    background: var(--brand);
    display: none;
    align-items: center; justify-content: center;
}
.wc-style-check .iconify { width: 10px; height: 10px; color: #fff; }
.wc-style-card.active .wc-style-check { display: flex; }
.wc-style-name {
    font-size: 11px;
    font-weight: 700;
    color: var(--text-2);
}
.wc-style-sub {
    font-size: 10px;
    color: var(--text-3);
    line-height: 1.3;
}
/* Style swatch mini preview */
.wc-style-swatch {
    width: 52px; height: 32px;
    border-radius: 6px;
    overflow: hidden;
    position: relative;
    flex-shrink: 0;
}
.wss-glass    { background: rgba(20,18,40,.8); border: 1px solid rgba(255,255,255,.15); box-shadow: inset 0 1px 0 rgba(255,255,255,.1); }
.wss-solid    { background: #0c0b18; border: 2px solid rgba(124,108,252,.5); }
.wss-neon     { background: #020412; border: 1px solid #00ffc8; box-shadow: 0 0 8px rgba(0,255,200,.4); }
.wss-minimal  { background: rgba(14,14,18,.9); border: 1px solid rgba(255,255,255,.08); }
.wss-retro    { background: #0a0818; border: 3px solid #7c6cfc; border-radius: 4px; }
.wss-frosted  { background: rgba(8,6,20,.06); border: 2px solid rgba(124,108,252,.8); }

/* ── Font family selector (visual button grid) ── */
.wc-font-grid {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    margin-bottom: 4px;
}
.wc-font-btn {
    padding: 7px 14px;
    border-radius: 8px;
    border: 2px solid var(--border);
    background: var(--surface-2);
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    color: var(--text-2);
    transition: all .15s;
    white-space: nowrap;
}
.wc-font-btn:hover { border-color: var(--brand); color: var(--text); }
.wc-font-btn.active { border-color: var(--brand); background: rgba(124,108,252,.1); color: var(--brand-light); }

/* ── Step footer navigation ── */
.wc-step-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-top: 14px;
    border-top: 1px solid var(--border);
    margin-top: 14px;
}
.wc-step-prev, .wc-step-next {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 8px;
    border: 1px solid var(--border-2);
    background: var(--surface-3);
    font-size: 12px;
    font-weight: 600;
    color: var(--text-2);
    cursor: pointer;
    transition: all .15s;
}
.wc-step-prev:hover, .wc-step-next:hover { border-color: var(--brand); color: var(--text); }
.wc-step-next.primary {
    background: linear-gradient(135deg, var(--brand), #6356e8);
    border-color: transparent;
    color: #fff;
    box-shadow: 0 4px 12px var(--brand-glow);
}
.wc-step-next.primary:hover { transform: translateY(-1px); box-shadow: 0 6px 18px var(--brand-glow); }
.wc-step-prev .iconify, .wc-step-next .iconify { width: 13px; height: 13px; }

/* Font size slider label override (no 'px' suffix for plain numbers) */
.wc-sliderval-plain { }



/* Toast notification */
#ws-toast {
    position: fixed;
    bottom: 28px;
    right: 28px;
    padding: 12px 18px;
    background: var(--surface);
    border: 1px solid var(--border-2);
    border-radius: var(--radius);
    font-size: 13px;
    font-weight: 600;
    color: var(--text);
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 8px 32px rgba(0,0,0,.5);
    z-index: 9999;
    transform: translateY(20px);
    opacity: 0;
    transition: all .25s cubic-bezier(.34,1.56,.64,1);
    pointer-events: none;
}
#ws-toast.show {
    transform: translateY(0);
    opacity: 1;
}
#ws-toast.success { border-color: rgba(34,211,160,.3); }
#ws-toast.success .iconify { color: var(--green); }
#ws-toast.error { border-color: rgba(239,68,68,.3); }
#ws-toast.error .iconify { color: #ef4444; }

/* ══════════════════════════════════════════════
   WIDGET STUDIO — Phase 3
   Live Preview: CSS-rendered mini widget mocks
══════════════════════════════════════════════ */

/* Preview card wrapper */
.wc-preview-card {}

/* Preview viewport — fixed height container, clips scaled mock */
.wc-preview-viewport {
    width: 100%;
    background: repeating-conic-gradient(rgba(255,255,255,.03) 0% 25%, transparent 0% 50%) 0 0 / 18px 18px;
    border-radius: 10px;
    border: 1px solid var(--border);
    overflow: hidden;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    /* Height is set per-widget via inline style on .wc-preview-stage wrapper */
}

/* Label overlay */
.wc-preview-label {
    position: absolute;
    top: 8px; left: 10px;
    font-size: 9px;
    font-weight: 700;
    letter-spacing: .8px;
    text-transform: uppercase;
    color: rgba(255,255,255,.25);
    pointer-events: none;
}
.wc-preview-label .wc-preview-dot {
    display: inline-block;
    width: 5px; height: 5px;
    border-radius: 50%;
    background: var(--green);
    margin-right: 4px;
    vertical-align: middle;
    animation: livePulse 2s infinite;
}

/* Scale stage — must be wrapped in .wc-preview-frame for correct dimensions */
.wc-preview-stage {
    transform-origin: top center;
    flex-shrink: 0;
    /* Width set per-widget via inline style */
}
/* Frame: provides the visible height after scaling */
.wc-preview-frame {
    overflow: hidden;
    flex-shrink: 0;
    /* height set via inline style */
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding: 16px 0;
}

/* ── ALERT preview mock ── */
.wc-prev-alert {
    --p-bg:             rgba(8,8,12,.96);
    --p-border:         rgba(255,255,255,.1);
    --p-accent:         #7c6cfc;
    --p-accent2:        #a99dff;
    --p-amount:         #f97316;
    --p-donor:          #f1f1f6;
    --p-top-line:       linear-gradient(90deg,#7c6cfc,#a855f7,#22d3a0);
    --p-prog-bar:       linear-gradient(90deg,#7c6cfc,#f97316);
    --p-radius:         16px;
    --p-msg-c:            rgba(241,241,246,.6);
    --p-font-size-title:  17px;
    --p-font-size-amount: 24px;
    --p-font-size-msg:    13px;
    --p-spacing:          18px;
    --p-blur:             12px;
    font-family: var(--p-font-family, 'Inter', sans-serif);
    width: 480px;
}
.wc-prev-alert.theme-neon {
    --p-bg:       rgba(2,4,18,.97);
    --p-border:   rgba(0,255,200,.22);
    --p-accent:   #00ffc8;
    --p-accent2:  #00e5ff;
    --p-amount:   #00e5ff;
    --p-donor:    #00ffc8;
    --p-msg-c:    rgba(0,255,200,.6);
    --p-top-line: linear-gradient(90deg,#00ffc8,#00c8ff,#7c6cfc);
    --p-prog-bar: linear-gradient(90deg,#00ffc8,#00c8ff);
    --p-radius:   14px;
}
.wc-prev-alert.theme-fire {
    --p-bg:       rgba(10,4,2,.97);
    --p-border:   rgba(249,115,22,.22);
    --p-accent:   #f97316;
    --p-accent2:  #fbbf24;
    --p-amount:   #fbbf24;
    --p-donor:    #fef3c7;
    --p-msg-c:    rgba(254,243,199,.5);
    --p-top-line: linear-gradient(90deg,#f97316,#ef4444,#fbbf24);
    --p-prog-bar: linear-gradient(90deg,#f97316,#ef4444,#fbbf24);
    --p-radius:   16px;
}
.wc-prev-alert.theme-ice {
    --p-bg:       rgba(2,8,22,.96);
    --p-border:   rgba(147,210,255,.18);
    --p-accent:   #38bdf8;
    --p-accent2:  #818cf8;
    --p-amount:   #38bdf8;
    --p-donor:    #e0f2fe;
    --p-msg-c:    rgba(224,242,254,.5);
    --p-top-line: linear-gradient(90deg,#38bdf8,#818cf8,#e0f2fe);
    --p-prog-bar: linear-gradient(90deg,#38bdf8,#818cf8);
    --p-radius:   18px;
}
.wc-prev-alert.theme-minimal {
    --p-bg:       rgba(12,12,16,.95);
    --p-border:   rgba(255,255,255,.14);
    --p-accent:   #e0e0f0;
    --p-accent2:  #ffffff;
    --p-amount:   #ffffff;
    --p-donor:    #ffffff;
    --p-msg-c:    rgba(241,241,246,.6);
    --p-top-line: linear-gradient(90deg,rgba(255,255,255,.5),rgba(255,255,255,.15));
    --p-prog-bar: linear-gradient(90deg,rgba(255,255,255,.7),rgba(255,255,255,.3));
    --p-radius:   12px;
}
.wc-prev-alert .alert-mock-box {
    background: var(--p-bg);
    border: 1px solid var(--p-border);
    border-radius: var(--p-radius);
    padding: 0;
    overflow: hidden;
    box-shadow: var(--p-shadow, 0 8px 40px rgba(0,0,0,.7), 0 0 0 1px rgba(255,255,255,.06));
    position: relative;
    opacity: var(--p-card-opacity, 1);
}
.wc-prev-alert.theme-neon  { --p-shadow: 0 8px 40px rgba(0,0,0,.85), 0 0 0 1px rgba(0,255,200,.12), 0 0 40px rgba(0,255,200,.1); }
.wc-prev-alert.theme-fire  { --p-shadow: 0 8px 40px rgba(0,0,0,.85), 0 0 0 1px rgba(249,115,22,.1), 0 0 50px rgba(239,68,68,.1); }
.wc-prev-alert.theme-ice   { --p-shadow: 0 8px 40px rgba(0,0,0,.85), 0 0 0 1px rgba(147,210,255,.08), 0 0 40px rgba(56,189,248,.08); }
.wc-prev-alert.theme-minimal { --p-shadow: 0 8px 32px rgba(0,0,0,.6), 0 0 0 1px rgba(255,255,255,.1); }
.wc-prev-alert .alert-mock-topline {
    height: 2px;
    background: var(--p-top-line);
}
.wc-prev-alert .alert-mock-inner {
    padding: var(--p-spacing) 20px 0;
}
.wc-prev-alert .alert-mock-header {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    margin-bottom: 14px;
}
.wc-prev-alert .alert-mock-names {
    flex: 1;
    min-width: 0;
}
.wc-prev-alert .alert-mock-donor {
    font-size: var(--p-font-size-title); font-weight: 700;
    color: var(--p-donor);
    letter-spacing: -.3px; line-height: 1.2;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.wc-prev-alert .alert-mock-amount {
    font-size: var(--p-font-size-amount); font-weight: 700;
    color: var(--p-amount);
    letter-spacing: -.6px; line-height: 1.1; margin-top: 2px;
}
.wc-prev-alert .alert-mock-msg {
    font-size: var(--p-font-size-msg);
    color: var(--p-msg-c);
    line-height: 1.65;
    padding: 12px 0 14px;
    word-break: break-word;
}
.wc-prev-alert .alert-mock-bar-wrap {
    height: 2px;
    background: rgba(255,255,255,.07);
    overflow: hidden;
}
.wc-prev-alert .alert-mock-bar {
    height: 100%;
    width: 72%;
    background: var(--p-prog-bar);
}

/* ── Alert mock: layout variants ── */
.wc-prev-alert .alert-mock-badge {
    flex-shrink: 0;
    font-size: 8px; font-weight: 800; letter-spacing: 1.4px;
    text-transform: uppercase;
    color: var(--p-accent2);
    opacity: .7;
    padding-top: 3px;
    white-space: nowrap;
}
.wc-prev-alert .alert-mock-divider {
    height: 1px;
    background: rgba(255,255,255,.06);
    margin: 0 -20px;
}
.wc-prev-alert.layout-centered .alert-mock-header {
    flex-direction: column;
    align-items: center;
    text-align: center;
}
.wc-prev-alert.layout-centered .alert-mock-names { text-align: center; }
.wc-prev-alert.layout-centered .alert-mock-msg   { text-align: center; }
.wc-prev-alert.layout-centered .alert-mock-badge { display: none; }

/* Side layout mock */
.wc-prev-alert.layout-side .alert-mock-inner    { display: none; }
.wc-prev-alert.layout-side .alert-mock-header  { display: none; }
.wc-prev-alert.layout-side .alert-mock-divider { display: none; }
.wc-prev-alert.layout-side .alert-mock-msg     { display: none; }
.wc-prev-alert.layout-side .alert-mock-side-body {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: var(--p-spacing) 20px;
}
.wc-prev-alert.layout-side .alert-mock-side-left {
    flex: 1;
    min-width: 0;
}
.wc-prev-alert.layout-side .alert-mock-side-donor {
    font-size: var(--p-font-size-title); font-weight: 700;
    color: var(--p-donor);
    letter-spacing: -.3px; line-height: 1.2;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.wc-prev-alert.layout-side .alert-mock-side-badge {
    font-size: 8px; font-weight: 800; letter-spacing: 1.4px;
    text-transform: uppercase;
    color: var(--p-accent2); opacity: .7;
    margin-top: 4px;
}
.wc-prev-alert.layout-side .alert-mock-side-msg {
    font-size: var(--p-font-size-msg);
    color: var(--p-msg-c);
    line-height: 1.55;
    margin-top: 6px;
    word-break: break-word;
    white-space: normal;
}
.wc-prev-alert.layout-side .alert-mock-side-right {
    flex-shrink: 0;
    text-align: right;
}
.wc-prev-alert.layout-side .alert-mock-side-amount {
    font-size: 32px; font-weight: 800;
    color: var(--p-amount);
    letter-spacing: -.8px; line-height: 1;
}
/* hide side-body by default, only show in layout-side */
.alert-mock-side-body { display: none; }

/* ── Alert mock: style variants ── */
.wc-prev-alert.style-glass .alert-mock-box {
    --p-bg: rgba(8,8,12,.82);
    backdrop-filter: blur(18px);
    -webkit-backdrop-filter: blur(18px);
}
.wc-prev-alert.style-solid .alert-mock-box {
    box-shadow: 0 4px 24px rgba(0,0,0,.75), 0 0 0 2px rgba(255,255,255,.1);
}
.wc-prev-alert.style-neon .alert-mock-box {
    box-shadow: 0 0 0 1px var(--p-accent), 0 0 32px var(--p-accent), 0 8px 40px rgba(0,0,0,.9);
}
.wc-prev-alert.style-neon .alert-mock-donor  { text-shadow: 0 0 18px var(--p-accent); }
.wc-prev-alert.style-neon .alert-mock-amount { text-shadow: 0 0 16px var(--p-accent2); }
.wc-prev-alert.style-minimal .alert-mock-box {
    box-shadow: 0 2px 10px rgba(0,0,0,.4);
}
.wc-prev-alert.style-minimal .alert-mock-topline { height: 1px; opacity: .5; }
.wc-prev-alert.style-retro .alert-mock-box {
    border-width: 3px;
    border-radius: calc(var(--p-radius) * .75);
    box-shadow: 3px 3px 0 var(--p-accent), 0 6px 20px rgba(0,0,0,.7);
}
.wc-prev-alert.style-frosted .alert-mock-box {
    background: rgba(8,6,20,.04);
    border: 2px solid var(--p-accent);
    box-shadow: 0 0 0 1px var(--p-accent), 0 4px 24px rgba(0,0,0,.6);
}
/* theme text-shadows */
.wc-prev-alert.theme-neon .alert-mock-donor  { text-shadow: 0 0 18px rgba(0,255,200,.5); }
.wc-prev-alert.theme-neon .alert-mock-amount { text-shadow: 0 0 16px rgba(0,229,255,.45); }
.wc-prev-alert.theme-fire .alert-mock-donor  { text-shadow: 0 0 24px rgba(251,191,36,.35); }
.wc-prev-alert.theme-fire .alert-mock-amount { text-shadow: 0 0 16px rgba(251,191,36,.4); }
.wc-prev-alert.theme-ice  .alert-mock-donor  { text-shadow: 0 0 18px rgba(56,189,248,.4); }
.wc-prev-alert.theme-ice  .alert-mock-amount { text-shadow: 0 0 14px rgba(56,189,248,.35); }



/* ── MILESTONE preview mock ── */
.wc-prev-milestone {
    --p-surface: rgba(8,8,12,.96);
    --p-border:  rgba(124,108,252,.2);
    --p-brand:   #7c6cfc;
    --p-brand2:  #a99dff;
    --p-orange:  #f97316;
    --p-green:   #22d3a0;
    --p-radius:  16px;
    width: 320px;
}
.wc-prev-milestone.theme-neon {
    --p-surface: rgba(2,4,18,.97);
    --p-border:  rgba(0,255,200,.22);
    --p-brand:   #00ffc8;
    --p-brand2:  #00e5ff;
    --p-orange:  #00e5ff;
    --p-green:   #00ffc8;
}
.wc-prev-milestone.theme-fire {
    --p-surface: rgba(10,4,2,.97);
    --p-border:  rgba(249,115,22,.22);
    --p-brand:   #f97316;
    --p-brand2:  #fbbf24;
    --p-orange:  #fbbf24;
    --p-green:   #f97316;
}
.wc-prev-milestone.theme-ice {
    --p-surface: rgba(2,8,22,.96);
    --p-border:  rgba(147,210,255,.18);
    --p-brand:   #38bdf8;
    --p-brand2:  #818cf8;
    --p-orange:  #38bdf8;
    --p-green:   #818cf8;
}
.wc-prev-milestone.theme-minimal {
    --p-surface: rgba(12,12,16,.95);
    --p-border:  rgba(255,255,255,.14);
    --p-brand:   #e0e0f0;
    --p-brand2:  #ffffff;
    --p-orange:  #e0e0f0;
    --p-green:   #ffffff;
}
.wc-prev-milestone .ms-mock-wrap {
    background: var(--p-surface);
    border: 1px solid var(--p-border);
    border-radius: var(--p-radius);
    overflow: hidden;
    box-shadow: 0 8px 40px rgba(0,0,0,.7), inset 0 0 0 1px rgba(255,255,255,.04);
    position: relative;
}
.wc-prev-milestone .ms-mock-topline {
    height: 3px;
    background: linear-gradient(90deg, var(--p-brand), #a855f7, var(--p-green));
}
.wc-prev-milestone .ms-mock-inner {
    padding: 14px 16px 16px;
}
.wc-prev-milestone .ms-mock-badge {
    display: inline-flex; align-items: center; gap: 4px;
    background: rgba(124,108,252,.1);
    border: 1px solid rgba(124,108,252,.22);
    border-radius: 20px;
    padding: 2px 8px;
    font-size: 8px; font-weight: 800; letter-spacing: 1.5px;
    color: var(--p-brand2); text-transform: uppercase;
    margin-bottom: 9px;
}
.wc-prev-milestone .ms-mock-title {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 14px; font-weight: 700;
    color: #f1f1f6; margin-bottom: 10px;
}
.wc-prev-milestone .ms-mock-amounts {
    display: flex; align-items: baseline; gap: 6px; margin-bottom: 10px;
}
.wc-prev-milestone .ms-mock-current {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 20px; font-weight: 700;
    color: var(--p-orange); line-height: 1;
}
.wc-prev-milestone .ms-mock-sep { font-size: 12px; color: rgba(241,241,246,.35); }
.wc-prev-milestone .ms-mock-target {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 14px; font-weight: 600;
    color: rgba(241,241,246,.55);
}
.wc-prev-milestone .ms-mock-pct {
    margin-left: auto;
    font-family: 'Space Grotesk', sans-serif;
    font-size: 13px; font-weight: 700;
    color: var(--p-brand2);
}
.wc-prev-milestone .ms-mock-track {
    height: 7px;
    background: rgba(255,255,255,.06);
    border-radius: 4px; overflow: hidden;
}
.wc-prev-milestone .ms-mock-bar {
    height: 100%; width: 58%;
    background: linear-gradient(90deg, var(--p-brand), #a855f7, var(--p-orange));
    border-radius: 4px;
}

/* ── SUBATHON preview mock ── */
.wc-prev-subathon {
    --p-surface: rgba(8,8,12,.96);
    --p-border:  rgba(124,108,252,.2);
    --p-brand:   #7c6cfc;
    --p-brand2:  #a855f7;
    --p-orange:  #f97316;
    --p-yellow:  #fbbf24;
    width: 280px;
}
.subathon-mock-wrap {
    width: 100%; height: 100%;
    background: rgba(8,8,12,.9);
    border-top: 2px solid transparent;
    border-image: linear-gradient(90deg,var(--p-brand),var(--p-brand2),#22d3a0) 1;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    padding: 16px; box-sizing: border-box;
}
.subathon-mock-timer {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 32px; font-weight: 700;
    color: #fff;
    line-height: 1;
    margin-bottom: 8px;
}
.subathon-mock-label {
    font-size: 10px; font-weight: 700;
    color: rgba(255,255,255,.5);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 12px;
}
.subathon-mock-bar {
    width: 80%;
    height: 4px;
    background: rgba(255,255,255,.1);
    border-radius: 2px;
    overflow: hidden;
}
.subathon-mock-bar-fill {
    height: 100%;
    width: 75%;
    background: linear-gradient(90deg, var(--p-brand), var(--p-brand2));
    border-radius: 2px;
}

/* ── LEADERBOARD preview mock ── */
.wc-prev-leaderboard {
    --p-surface: rgba(8,8,12,.96);
    --p-border:  rgba(124,108,252,.2);
    --p-brand:   #7c6cfc;
    --p-brand2:  #a99dff;
    --p-yellow:  #fbbf24;
    --p-green:   #22d3a0;
    --p-radius:  16px;
    width: 280px;
}
.wc-prev-leaderboard.theme-neon {
    --p-surface: rgba(2,4,18,.97);
    --p-border:  rgba(0,255,200,.22);
    --p-brand:   #00ffc8;
    --p-brand2:  #00e5ff;
    --p-yellow:  #00e5ff;
    --p-green:   #00ffc8;
}
.wc-prev-leaderboard.theme-fire {
    --p-surface: rgba(10,4,2,.97);
    --p-border:  rgba(249,115,22,.22);
    --p-brand:   #f97316;
    --p-brand2:  #fbbf24;
    --p-yellow:  #fbbf24;
    --p-green:   #f97316;
}
.wc-prev-leaderboard.theme-ice {
    --p-surface: rgba(2,8,22,.96);
    --p-border:  rgba(147,210,255,.18);
    --p-brand:   #38bdf8;
    --p-brand2:  #818cf8;
    --p-yellow:  #38bdf8;
    --p-green:   #818cf8;
}
.wc-prev-leaderboard.theme-minimal {
    --p-surface: rgba(12,12,16,.95);
    --p-border:  rgba(255,255,255,.14);
    --p-brand:   #e0e0f0;
    --p-brand2:  #ffffff;
    --p-yellow:  #e0e0f0;
    --p-green:   #ffffff;
}
.wc-prev-leaderboard .lb-mock-wrap {
    background: var(--p-surface);
    border: 1px solid var(--p-border);
    border-radius: var(--p-radius);
    overflow: hidden;
    box-shadow: 0 8px 40px rgba(0,0,0,.7), inset 0 0 0 1px rgba(255,255,255,.04);
    position: relative;
}
.wc-prev-leaderboard .lb-mock-topline {
    height: 3px;
    background: linear-gradient(90deg, var(--p-brand), #a855f7, var(--p-green));
}
.wc-prev-leaderboard .lb-mock-header {
    padding: 14px 14px 10px;
    border-bottom: 1px solid rgba(255,255,255,.06);
}
.wc-prev-leaderboard .lb-mock-badge {
    display: inline-flex; align-items: center; gap: 4px;
    background: rgba(124,108,252,.1);
    border: 1px solid rgba(124,108,252,.22);
    border-radius: 20px;
    padding: 2px 7px;
    font-size: 7px; font-weight: 800; letter-spacing: 1.2px;
    color: var(--p-brand2); text-transform: uppercase;
    margin-bottom: 7px;
}
.wc-prev-leaderboard .lb-mock-title {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 16px; font-weight: 700; color: #f1f1f6; line-height: 1;
}
.wc-prev-leaderboard .lb-mock-list {
    padding: 4px 0 6px;
}
.wc-prev-leaderboard .lb-mock-item {
    display: flex; align-items: center; gap: 8px;
    padding: 7px 12px;
}
.wc-prev-leaderboard .lb-mock-item + .lb-mock-item {
    border-top: 1px solid rgba(255,255,255,.04);
}
.wc-prev-leaderboard .lb-mock-item.rank-1 { background: rgba(251,191,36,.04); }
.wc-prev-leaderboard .lb-mock-rank { font-size: 14px; width: 22px; text-align: center; flex-shrink: 0; }
.wc-prev-leaderboard .lb-mock-avatar {
    width: 28px; height: 28px;
    border-radius: 50%;
    background: rgba(255,255,255,.06);
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; flex-shrink: 0;
}
.wc-prev-leaderboard .lb-mock-info { flex: 1; min-width: 0; }
.wc-prev-leaderboard .lb-mock-name {
    font-size: 12px; font-weight: 600; color: #f1f1f6;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.wc-prev-leaderboard .lb-mock-amount {
    font-size: 10px; color: rgba(241,241,246,.5);
}
.wc-prev-leaderboard .lb-mock-item.rank-1 .lb-mock-amount { color: var(--p-yellow); }

/* ── QR preview mock ── */
.wc-prev-qr {
    --p-surface: rgba(10,10,16,.93);
    --p-border:  rgba(124,108,252,.28);
    --p-brand:   #7c6cfc;
    --p-brand2:  #a99dff;
    --p-radius:  22px;
    width: 220px;
}
.wc-prev-qr.theme-neon {
    --p-surface: rgba(2,4,18,.97);
    --p-border:  rgba(0,255,200,.22);
    --p-brand:   #00ffc8;
    --p-brand2:  #00e5ff;
}
.wc-prev-qr.theme-fire {
    --p-surface: rgba(10,4,2,.97);
    --p-border:  rgba(249,115,22,.22);
    --p-brand:   #f97316;
    --p-brand2:  #fbbf24;
}
.wc-prev-qr.theme-ice {
    --p-surface: rgba(2,8,22,.96);
    --p-border:  rgba(147,210,255,.18);
    --p-brand:   #38bdf8;
    --p-brand2:  #818cf8;
}
.wc-prev-qr.theme-minimal {
    --p-surface: rgba(12,12,16,.95);
    --p-border:  rgba(255,255,255,.14);
    --p-brand:   #e0e0f0;
    --p-brand2:  #ffffff;
}
.wc-prev-qr .qr-mock-widget {
    background: var(--p-surface);
    border: 1px solid var(--p-border);
    border-radius: var(--p-radius);
    padding: 16px 16px 13px;
    display: flex; flex-direction: column; align-items: center;
    gap: 0;
    box-shadow: 0 0 0 1px rgba(124,108,252,.08), 0 24px 60px rgba(0,0,0,.7), 0 0 80px rgba(124,108,252,.12);
    position: relative;
    overflow: hidden;
}
.wc-prev-qr .qr-mock-topline {
    position: absolute;
    top: 0; left: 14px; right: 14px;
    height: 2px;
    border-radius: 2px;
    background: linear-gradient(90deg, var(--p-brand), var(--p-brand2));
}
.wc-prev-qr .qr-mock-header {
    display: flex; align-items: center; gap: 7px;
    margin-bottom: 12px; width: 100%;
}
.wc-prev-qr .qr-mock-logo {
    width: 22px; height: 22px;
    border-radius: 6px;
    background: linear-gradient(135deg, var(--p-brand), #6356e8);
    display: flex; align-items: center; justify-content: center;
    font-size: 8px; font-weight: 800; color: #fff; flex-shrink: 0;
}
.wc-prev-qr .qr-mock-name {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 10px; font-weight: 700; color: #f1f1f6;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    flex: 1;
}
.wc-prev-qr .qr-mock-img {
    width: 150px; height: 150px;
    border-radius: 10px;
    background: #141419;
    border: 1px solid rgba(255,255,255,.07);
    display: flex; align-items: center; justify-content: center;
    overflow: hidden;
}
.wc-prev-qr .qr-mock-img svg {
    width: 100%; height: 100%; display: block;
}
.wc-prev-qr .qr-mock-footer {
    margin-top: 10px;
    display: flex; align-items: center; gap: 5px;
}
.wc-prev-qr .qr-mock-dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: var(--p-brand2);
    animation: livePulse 2s infinite;
}
.wc-prev-qr .qr-mock-scan {
    font-size: 8px; font-weight: 600; color: #a0a0b4; letter-spacing: .3px;
}

/* ── Responsive ── */
@media (max-width: 900px) {
    .ws-wrap { padding: 20px 16px; }
    .ws-body {
        grid-template-columns: 1fr;
    }
    .ws-sidebar {
        position: static;
        display: flex;
        flex-wrap: wrap;
        overflow: visible;
        border-radius: var(--radius-lg);
    }
    .ws-nav-label { display: none; }
    .ws-nav-divider { display: none; }
    .ws-nav-item {
        border-left: none;
        border-bottom: 3px solid transparent;
        padding: 10px 14px;
        font-size: 12px;
    }
    .ws-nav-item.active {
        border-bottom-color: var(--brand);
        border-left: none;
        background: rgba(124,108,252,.08);
    }
    .ws-nav-badge { display: none; }
}

/* ══════════════════════════════════════════════
   WIDGET STUDIO — Pengaturan Alert Card
   Audio · Durasi Tier · Video
══════════════════════════════════════════════ */

/* ── Alert Settings Section Divider ── */
.was-section {
    margin-top: 22px;
}
.was-section:first-child { margin-top: 0; }
.was-section-head {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 14px;
    padding-bottom: 10px;
    border-bottom: 1px solid var(--border);
}
.was-section-icon {
    width: 28px; height: 28px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    font-size: 14px;
}
.was-section-title {
    font-size: 13px;
    font-weight: 600;
    color: var(--text);
}
.was-section-sub {
    font-size: 11px;
    color: var(--text-3);
    margin-left: auto;
}

/* ── Toggle row ── */
.was-toggle-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    padding: 10px 14px;
    background: rgba(255,255,255,.03);
    border: 1px solid var(--border);
    border-radius: 10px;
    margin-bottom: 12px;
}
.was-toggle-label {
    font-size: 13px;
    color: var(--text-2);
}
.was-toggle-label strong { color: var(--text); }

/* ── Toggle switch ── */
.was-toggle {
    position: relative;
    width: 38px; height: 22px;
    flex-shrink: 0;
    cursor: pointer;
}
.was-toggle input { opacity: 0; width: 0; height: 0; position: absolute; }
.was-toggle-track {
    position: absolute;
    inset: 0;
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.15);
    border-radius: 11px;
    transition: background .2s, border-color .2s;
}
.was-toggle-track::after {
    content: '';
    position: absolute;
    top: 3px; left: 3px;
    width: 14px; height: 14px;
    background: rgba(255,255,255,.5);
    border-radius: 50%;
    transition: transform .2s, background .2s;
}
.was-toggle input:checked ~ .was-toggle-track {
    background: rgba(124,108,252,.35);
    border-color: var(--brand);
}
.was-toggle input:checked ~ .was-toggle-track::after {
    transform: translateX(16px);
    background: var(--brand-light);
}

/* ── Sound preset grid ── */
.was-sound-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
    gap: 8px;
}
.was-sound-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    padding: 12px 8px 10px;
    background: rgba(255,255,255,.03);
    border: 1px solid var(--border);
    border-radius: 10px;
    cursor: pointer;
    transition: border-color .15s, background .15s;
    user-select: none;
    position: relative;
}
.was-sound-card:hover {
    background: rgba(255,255,255,.06);
    border-color: rgba(255,255,255,.2);
}
.was-sound-card.active {
    background: rgba(124,108,252,.12);
    border-color: var(--brand);
}
.was-sound-card.active .was-sound-name { color: var(--brand-light); }
.was-sound-icon {
    width: 32px; height: 32px;
    border-radius: 8px;
    background: rgba(255,255,255,.06);
    display: flex; align-items: center; justify-content: center;
    font-size: 16px;
    transition: background .15s;
    flex-shrink: 0;
}
.was-sound-card.active .was-sound-icon { background: rgba(124,108,252,.2); }
.was-sound-name {
    font-size: 11px;
    font-weight: 500;
    color: var(--text-2);
    text-align: center;
    line-height: 1.3;
}
.was-sound-play {
    position: absolute;
    top: 6px; right: 6px;
    width: 18px; height: 18px;
    border-radius: 50%;
    background: rgba(255,255,255,.08);
    border: none;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    opacity: 0;
    transition: opacity .15s;
    padding: 0;
}
.was-sound-card:hover .was-sound-play { opacity: 1; }
.was-sound-play .iconify { width: 10px; height: 10px; color: var(--text-2); }

/* ── Duration tier table ── */
.was-tier-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 12px;
}
.was-tier-table th {
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .6px;
    color: var(--text-3);
    text-align: left;
    padding: 0 8px 8px;
}
.was-tier-table td {
    padding: 4px 8px;
    vertical-align: middle;
}
.was-tier-table tr:not(:last-child) td {
    border-bottom: 1px solid rgba(255,255,255,.05);
}
.was-tier-from-label {
    font-size: 11px;
    color: var(--text-3);
    white-space: nowrap;
}
.was-tier-badge {
    display: inline-block;
    padding: 2px 7px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 600;
    background: rgba(124,108,252,.12);
    border: 1px solid rgba(124,108,252,.25);
    color: var(--brand-light);
    white-space: nowrap;
}
.was-tier-input {
    width: 80px;
    background: rgba(255,255,255,.05);
    border: 1px solid var(--border);
    border-radius: 6px;
    color: var(--text);
    font-size: 12px;
    padding: 4px 8px;
    outline: none;
    transition: border-color .15s;
}
.was-tier-input:focus { border-color: var(--brand); }
.was-tier-dur-wrap {
    display: flex;
    align-items: center;
    gap: 6px;
}
.was-tier-dur-slider {
    flex: 1;
    -webkit-appearance: none;
    height: 4px;
    border-radius: 2px;
    background: rgba(255,255,255,.1);
    outline: none;
    cursor: pointer;
    min-width: 80px;
}
.was-tier-dur-slider::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 14px; height: 14px;
    border-radius: 50%;
    background: var(--brand-light);
    cursor: pointer;
    box-shadow: 0 0 6px var(--brand-glow);
}
.was-tier-dur-val {
    font-size: 11px;
    color: var(--text-2);
    min-width: 32px;
    text-align: right;
}
.was-max-dur-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    background: rgba(251,191,36,.05);
    border: 1px solid rgba(251,191,36,.15);
    border-radius: 8px;
    margin-top: 6px;
}
.was-max-dur-row .iconify { color: #fbbf24; width: 14px; height: 14px; flex-shrink: 0; }
.was-max-dur-label { font-size: 12px; color: var(--text-2); flex: 1; }
.was-max-dur-input {
    width: 64px;
    background: rgba(255,255,255,.05);
    border: 1px solid var(--border);
    border-radius: 6px;
    color: var(--text);
    font-size: 12px;
    padding: 4px 8px;
    outline: none;
    text-align: center;
    transition: border-color .15s;
}
.was-max-dur-input:focus { border-color: #fbbf24; }
.was-max-dur-unit { font-size: 11px; color: var(--text-3); }

/* ── Video cards ── */
.was-video-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}
.was-video-card {
    padding: 14px;
    background: rgba(255,255,255,.03);
    border: 1px solid var(--border);
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.was-video-card.disabled { opacity: .5; pointer-events: none; }
.was-video-head {
    display: flex;
    align-items: center;
    gap: 10px;
}
.was-video-icon {
    width: 32px; height: 32px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    font-size: 16px;
}
.was-video-title {
    font-size: 13px;
    font-weight: 600;
    color: var(--text);
    flex: 1;
}
.was-video-desc {
    font-size: 11px;
    color: var(--text-3);
    line-height: 1.5;
}
.was-coming-soon {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 600;
    background: rgba(251,191,36,.1);
    border: 1px solid rgba(251,191,36,.2);
    color: #fbbf24;
}

/* ── Save button row ── */
.was-save-row {
    display: flex;
    justify-content: flex-end;
    margin-top: 20px;
    padding-top: 16px;
    border-top: 1px solid var(--border);
}
.was-save-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 20px;
    border-radius: 8px;
    background: var(--brand);
    color: #fff;
    font-size: 13px;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: opacity .15s, transform .1s;
}
.was-save-btn:hover { opacity: .9; }
.was-save-btn:active { transform: scale(.98); }
.was-save-btn.saving { opacity: .6; pointer-events: none; }
</style>
@endpush

<div class="ws-wrap">

    {{-- ── Page Header ── --}}
    <div class="ws-header">
        <div class="ws-header-left">
            <div class="ws-title">
                <div class="ws-title-icon">
                    <span class="iconify" data-icon="solar:palette-bold-duotone"></span>
                </div>
                Widget Studio
            </div>
            <div class="ws-sub">Kelola tampilan &amp; URL setiap widget OBS kamu</div>
        </div>
        <div class="ws-header-actions">
            <a href="{{ route('streamer.settings') }}"
               style="display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-weight:600;color:var(--text-2);text-decoration:none;transition:all .15s;background:var(--surface-2)"
               onmouseover="this.style.borderColor='var(--border-2)';this.style.color='var(--text)'"
               onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-2)'">
                <span class="iconify" data-icon="solar:settings-bold-duotone" style="width:14px;height:14px"></span>
                Settings
            </a>
            <a href="{{ route('streamer.dashboard') }}"
               style="display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-weight:600;color:var(--text-2);text-decoration:none;transition:all .15s;background:var(--surface-2)"
               onmouseover="this.style.borderColor='var(--border-2)';this.style.color='var(--text)'"
               onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-2)'">
                <span class="iconify" data-icon="solar:widget-bold-duotone" style="width:14px;height:14px"></span>
                Dashboard
            </a>
        </div>
    </div>

    <div class="ws-body">

        {{-- ── Sidebar Tab Nav ── --}}
        <nav class="ws-sidebar" id="ws-sidebar">
            <div class="ws-nav-label">Widget OBS</div>

            <div class="ws-nav-item active" data-tab="alert" onclick="wsTab('alert',this)">
                <span class="iconify" data-icon="solar:bell-bold-duotone" style="color:var(--orange)"></span>
                Alert
                <span class="ws-nav-badge live">SSE</span>
            </div>

            <div class="ws-nav-item" data-tab="milestone" onclick="wsTab('milestone',this)">
                <span class="iconify" data-icon="solar:target-bold-duotone" style="color:var(--purple)"></span>
                Milestone
                <span class="ws-nav-badge live">SSE</span>
            </div>

            <div class="ws-nav-item" data-tab="leaderboard" onclick="wsTab('leaderboard',this)">
                <span class="iconify" data-icon="solar:ranking-bold-duotone" style="color:var(--yellow)"></span>
                Leaderboard
                <span class="ws-nav-badge live">SSE</span>
            </div>

            <div class="ws-nav-item" data-tab="qr" onclick="wsTab('qr',this)">
                <span class="iconify" data-icon="solar:qr-code-bold-duotone" style="color:var(--green)"></span>
                Barcode / QR
                <span class="ws-nav-badge">Static</span>
            </div>

            <div class="ws-nav-item" data-tab="subathon" onclick="wsTab('subathon',this)">
                <span class="iconify" data-icon="solar:timer-bold-duotone" style="color:var(--brand)"></span>
                Subathon
                <span class="ws-nav-badge live">SSE</span>
            </div>

            <div class="ws-nav-item" data-tab="running-text" onclick="wsTab('running-text',this)">
                <span class="iconify" data-icon="solar:text-bold-duotone" style="color:var(--brand)"></span>
                Running Text
            </div>

            <div class="ws-nav-divider"></div>
            <div class="ws-nav-label">Lanjutan</div>

            <div class="ws-nav-item" data-tab="canvas" onclick="wsTab('canvas',this)">
                <span class="iconify" data-icon="solar:monitor-bold-duotone" style="color:var(--brand-light)"></span>
                OBS Canvas
                <span class="ws-nav-badge">Editor</span>
            </div>
        </nav>

        {{-- ── Panels ── --}}
        <div id="ws-panels">

            {{-- ══ TAB: Alert ══ --}}
            <div class="ws-panel active" id="tab-alert">

                {{-- ── Phase 2+: 5-Step Customizer — Alert ── --}}
                @php
                    $ws_alert = $widgetSettings['alert'];
                    // Parse rgba → hex + opacity for bg/border
                    preg_match('/rgba\((\d+),\s*(\d+),\s*(\d+),\s*([\d.]+)\)/', $ws_alert['bg'], $bgM);
                    $alertBgHex     = isset($bgM[1]) ? sprintf('#%02x%02x%02x',$bgM[1],$bgM[2],$bgM[3]) : '#08080c';
                    $alertBgOpacity = isset($bgM[4]) ? round($bgM[4]*100) : 96;
                    preg_match('/rgba\((\d+),\s*(\d+),\s*(\d+),\s*([\d.]+)\)/', $ws_alert['border'], $bdM);
                    $alertBorderHex     = isset($bdM[1]) ? sprintf('#%02x%02x%02x',$bdM[1],$bdM[2],$bdM[3]) : '#ffffff';
                    $alertBorderOpacity = isset($bdM[4]) ? round($bdM[4]*100) : 10;
                    // Gradient stops
                    preg_match_all('/#[0-9a-fA-F]{6}/', $ws_alert['top_line'], $tlC);
                    $alertTlA = $tlC[0][0] ?? '#7c6cfc'; $alertTlB = $tlC[0][1] ?? '#a855f7';
                    preg_match_all('/#[0-9a-fA-F]{6}/', $ws_alert['prog_bar'], $pbC);
                    $alertPbA = $pbC[0][0] ?? '#7c6cfc'; $alertPbB = $pbC[0][1] ?? '#f97316';
                @endphp
                <div class="ws-card">
                    <div class="ws-card-head">
                        <div class="ws-card-icon" style="background:rgba(124,108,252,.1);border:1px solid rgba(124,108,252,.2)">
                            <span class="iconify" data-icon="solar:palette-bold-duotone" style="color:var(--brand-light)"></span>
                        </div>
                        <div>
                            <div class="ws-card-title">Kustomisasi Tampilan</div>
                            <div class="ws-card-sub">5 langkah untuk atur layout, gaya, warna, tipografi &amp; spasi</div>
                        </div>
                    </div>

                    {{-- ── Step navigation tabs ── --}}
                    <div class="wc-steps" id="alert-steps">
                        <button class="wc-step-btn active" onclick="wcStep('alert',1,this)">
                            <span class="wc-step-num">1</span>
                            <span class="iconify" data-icon="solar:widget-bold"></span> Layout
                        </button>
                        <button class="wc-step-btn" onclick="wcStep('alert',2,this)">
                            <span class="wc-step-num">2</span>
                            <span class="iconify" data-icon="solar:magic-stick-3-bold"></span> Gaya
                        </button>
                        <button class="wc-step-btn" onclick="wcStep('alert',3,this)">
                            <span class="wc-step-num">3</span>
                            <span class="iconify" data-icon="solar:palette-bold"></span> Warna
                        </button>
                        <button class="wc-step-btn" onclick="wcStep('alert',4,this)">
                            <span class="wc-step-num">4</span>
                            <span class="iconify" data-icon="solar:text-bold"></span> Tipografi
                        </button>

                    </div>

                    {{-- ══ STEP 1: Layout ══ --}}
                    <div class="wc-step-panel active" id="alert-step-1">
                        <div class="ws-section-label" style="margin-bottom:10px">
                            <span class="iconify" data-icon="solar:widget-bold" style="width:12px;height:12px"></span>
                            Pilih struktur kartu alert
                        </div>
                        <div class="wc-layout-grid" id="alert-layouts">
                            <div class="wc-layout-card {{ ($ws_alert['layout']??'classic')==='classic'?'active':'' }}" onclick="wcSelectLayout('alert','classic',this)">
                                <div class="wc-layout-diagram">
                                    <div class="ld-icon"></div>
                                    <div class="ld-lines">
                                        <div class="ld-line accent"></div>
                                        <div class="ld-line small"></div>
                                        <div class="ld-line small" style="width:50%"></div>
                                    </div>
                                </div>
                                <div class="wc-layout-name">Classic</div>
                                <div class="wc-layout-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                            </div>
                            <div class="wc-layout-card {{ ($ws_alert['layout']??'classic')==='centered'?'active':'' }}" onclick="wcSelectLayout('alert','centered',this)">
                                <div class="wc-layout-diagram centered">
                                    <div class="ld-icon" style="margin:0 auto"></div>
                                    <div class="ld-cline accent" style="margin:0 auto"></div>
                                    <div class="ld-cline" style="margin:0 auto"></div>
                                </div>
                                <div class="wc-layout-name">Centered</div>
                                <div class="wc-layout-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                            </div>
                            <div class="wc-layout-card {{ ($ws_alert['layout']??'classic')==='side'?'active':'' }}" onclick="wcSelectLayout('alert','side',this)">
                                <div class="wc-layout-diagram side">
                                    <div style="display:flex;gap:6px;align-items:center;width:100%">
                                        <div class="ld-lines" style="flex:1">
                                            <div class="ld-line accent" style="width:70%"></div>
                                            <div class="ld-line small"></div>
                                        </div>
                                        <div class="ld-line accent" style="width:22px;height:10px;border-radius:2px;flex-shrink:0"></div>
                                    </div>
                                </div>
                                <div class="wc-layout-name">Side</div>
                                <div class="wc-layout-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                            </div>
                        </div>
                        <div class="wc-step-footer">
                            <span></span>
                            <button class="wc-step-next primary" onclick="wcStep('alert',2,document.querySelector('#alert-steps .wc-step-btn:nth-child(2)'))">
                                Berikutnya <span class="iconify" data-icon="solar:arrow-right-bold"></span>
                            </button>
                        </div>
                    </div>{{-- /step-1 --}}

                    {{-- ══ STEP 2: Style ══ --}}
                    <div class="wc-step-panel" id="alert-step-2">
                        <div class="ws-section-label" style="margin-bottom:10px">
                            <span class="iconify" data-icon="solar:magic-stick-3-bold" style="width:12px;height:12px"></span>
                            Pilih estetika visual alert
                        </div>
                        <div class="wc-style-grid" id="alert-styles">
                            <div class="wc-style-card {{ ($ws_alert['style']??'glass')==='glass'?'active':'' }}" onclick="wcSelectStyle('alert','glass',this)">
                                <div class="wc-style-swatch wss-glass"></div>
                                <div class="wc-style-name">Glass</div>
                                <div class="wc-style-sub">Transparan halus</div>
                                <div class="wc-style-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                            </div>
                            <div class="wc-style-card {{ ($ws_alert['style']??'glass')==='solid'?'active':'' }}" onclick="wcSelectStyle('alert','solid',this)">
                                <div class="wc-style-swatch wss-solid"></div>
                                <div class="wc-style-name">Solid</div>
                                <div class="wc-style-sub">Tegas &amp; opak</div>
                                <div class="wc-style-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                            </div>
                            <div class="wc-style-card {{ ($ws_alert['style']??'glass')==='neon'?'active':'' }}" onclick="wcSelectStyle('alert','neon',this)">
                                <div class="wc-style-swatch wss-neon"></div>
                                <div class="wc-style-name">Neon Glow</div>
                                <div class="wc-style-sub">Border bercahaya</div>
                                <div class="wc-style-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                            </div>
                            <div class="wc-style-card {{ ($ws_alert['style']??'glass')==='minimal'?'active':'' }}" onclick="wcSelectStyle('alert','minimal',this)">
                                <div class="wc-style-swatch wss-minimal"></div>
                                <div class="wc-style-name">Minimal</div>
                                <div class="wc-style-sub">Bersih &amp; simpel</div>
                                <div class="wc-style-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                            </div>
                            <div class="wc-style-card {{ ($ws_alert['style']??'glass')==='retro'?'active':'' }}" onclick="wcSelectStyle('alert','retro',this)">
                                <div class="wc-style-swatch wss-retro"></div>
                                <div class="wc-style-name">Retro</div>
                                <div class="wc-style-sub">Bold &amp; tebal</div>
                                <div class="wc-style-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                            </div>
                            <div class="wc-style-card {{ ($ws_alert['style']??'glass')==='frosted'?'active':'' }}" onclick="wcSelectStyle('alert','frosted',this)">
                                <div class="wc-style-swatch wss-frosted"></div>
                                <div class="wc-style-name">Outlined</div>
                                <div class="wc-style-sub">Transparan &amp; tegas</div>
                                <div class="wc-style-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                            </div>
                        </div>
                        {{-- Opacity Card slider --}}
                        <div class="wc-row" style="margin-top:10px">
                            <div class="wc-row-label">
                                <span class="iconify" data-icon="solar:layers-bold"></span>
                                Opacity Kartu
                            </div>
                            <div class="wc-row-ctrl">
                                <div class="wc-slider-wrap">
                                    <input type="range" class="wc-slider" min="10" max="100"
                                        value="{{ $ws_alert['card_opacity'] ?? 96 }}"
                                        id="alert-slider-card_opacity"
                                        oninput="wcCardOpacityChange('alert',this)">
                                    <span class="wc-slider-val" id="alert-sliderval-card_opacity">{{ $ws_alert['card_opacity'] ?? 96 }}%</span>
                                </div>
                            </div>
                        </div>
                        <div class="wc-step-footer">
                            <button class="wc-step-prev" onclick="wcStep('alert',1,document.querySelector('#alert-steps .wc-step-btn:nth-child(1)'))">
                                <span class="iconify" data-icon="solar:arrow-left-bold"></span> Kembali
                            </button>
                            <button class="wc-step-next primary" onclick="wcStep('alert',3,document.querySelector('#alert-steps .wc-step-btn:nth-child(3)'))">
                                Berikutnya <span class="iconify" data-icon="solar:arrow-right-bold"></span>
                            </button>
                        </div>
                    </div>{{-- /step-2 --}}

                    {{-- ══ STEP 3: Color ══ --}}
                    <div class="wc-step-panel" id="alert-step-3">
                        <div class="ws-section-label" style="margin-bottom:4px">
                            <span class="iconify" data-icon="solar:magic-stick-bold" style="width:12px;height:12px"></span>
                            Preset Tema
                        </div>
                        <div class="wc-presets" id="alert-presets">
                            <div class="wc-preset-card {{ $ws_alert['preset']==='default'?'active':'' }}" data-preset="default" onclick="wcSelectPreset('alert','default',this)">
                                <div class="wc-preset-swatch" style="background:linear-gradient(135deg,rgba(8,8,12,.96),#1a1a2e);border-color:rgba(255,255,255,.1)"></div>
                                <div class="wc-preset-name">Default</div>
                                <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                            </div>
                            <div class="wc-preset-card {{ $ws_alert['preset']==='neon'?'active':'' }}" data-preset="neon" onclick="wcSelectPreset('alert','neon',this)">
                                <div class="wc-preset-swatch" style="background:linear-gradient(135deg,rgba(2,4,18,.97),#001a14);border-color:rgba(0,255,200,.22)"></div>
                                <div class="wc-preset-name">Neon</div>
                                <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                            </div>
                            <div class="wc-preset-card {{ $ws_alert['preset']==='fire'?'active':'' }}" data-preset="fire" onclick="wcSelectPreset('alert','fire',this)">
                                <div class="wc-preset-swatch" style="background:linear-gradient(135deg,rgba(10,4,2,.97),#2a0a00);border-color:rgba(249,115,22,.22)"></div>
                                <div class="wc-preset-name">Fire</div>
                                <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                            </div>
                            <div class="wc-preset-card {{ $ws_alert['preset']==='ice'?'active':'' }}" data-preset="ice" onclick="wcSelectPreset('alert','ice',this)">
                                <div class="wc-preset-swatch" style="background:linear-gradient(135deg,rgba(2,8,22,.96),#001a2e);border-color:rgba(147,210,255,.18)"></div>
                                <div class="wc-preset-name">Ice</div>
                                <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                            </div>
                            <div class="wc-preset-card {{ $ws_alert['preset']==='minimal'?'active':'' }}" data-preset="minimal" onclick="wcSelectPreset('alert','minimal',this)">
                                <div class="wc-preset-swatch" style="background:linear-gradient(135deg,rgba(12,12,16,.95),#18181c);border-color:rgba(255,255,255,.14)"></div>
                                <div class="wc-preset-name">Minimal</div>
                                <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                            </div>
                            <div class="wc-preset-card {{ $ws_alert['preset']==='custom'?'active':'' }}" data-preset="custom" onclick="wcSelectPreset('alert','custom',this)">
                                <div class="wc-preset-swatch" style="background:linear-gradient(135deg,var(--brand),var(--purple));border-color:rgba(124,108,252,.3)"></div>
                                <div class="wc-preset-name">Custom</div>
                                <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                            </div>
                        </div>

                        {{-- Custom color controls --}}
                        <div class="wc-custom-panel {{ $ws_alert['preset']==='custom'?'visible':'' }}" id="alert-custom" style="margin-top:12px">
                            {{-- Background --}}
                            <div class="wc-row">
                                <div class="wc-row-label"><span class="iconify" data-icon="solar:square-bold"></span> Background</div>
                                <div class="wc-row-ctrl">
                                    <div class="wc-color-wrap">
                                        <div class="wc-color-swatch" id="alert-swatch-bg" style="background:{{ $ws_alert['bg'] }}"></div>
                                        <input type="color" class="wc-color-input" id="alert-color-bg" value="{{ $alertBgHex }}" oninput="wcRgbaColorChange('alert','bg',this)">
                                    </div>
                                    <div class="wc-opacity-wrap">
                                        <span class="wc-opacity-label">Opacity</span>
                                        <input type="range" class="wc-slider" min="0" max="100" value="{{ $alertBgOpacity }}" id="alert-opacity-bg" oninput="wcOpacityChange('alert','bg',this)">
                                        <span class="wc-opacity-val" id="alert-opacityval-bg">{{ $alertBgOpacity }}%</span>
                                    </div>
                                </div>
                            </div>
                            {{-- Border --}}
                            <div class="wc-row">
                                <div class="wc-row-label"><span class="iconify" data-icon="solar:border-bold"></span> Border</div>
                                <div class="wc-row-ctrl">
                                    <div class="wc-color-wrap">
                                        <div class="wc-color-swatch" id="alert-swatch-border" style="background:{{ $ws_alert['border'] }}"></div>
                                        <input type="color" class="wc-color-input" id="alert-color-border" value="{{ $alertBorderHex }}" oninput="wcRgbaColorChange('alert','border',this)">
                                    </div>
                                    <div class="wc-opacity-wrap">
                                        <span class="wc-opacity-label">Opacity</span>
                                        <input type="range" class="wc-slider" min="0" max="100" value="{{ $alertBorderOpacity }}" id="alert-opacity-border" oninput="wcOpacityChange('alert','border',this)">
                                        <span class="wc-opacity-val" id="alert-opacityval-border">{{ $alertBorderOpacity }}%</span>
                                    </div>
                                </div>
                            </div>
                            {{-- Accent --}}
                            <div class="wc-row">
                                <div class="wc-row-label"><span class="iconify" data-icon="solar:star-bold"></span> Accent</div>
                                <div class="wc-row-ctrl">
                                    <div class="wc-color-wrap">
                                        <div class="wc-color-swatch" id="alert-swatch-accent" style="background:{{ $ws_alert['accent'] }}"></div>
                                        <input type="color" class="wc-color-input" id="alert-color-accent" value="{{ $ws_alert['accent'] }}" oninput="wcColorChange('alert','accent',this)">
                                    </div>
                                </div>
                            </div>
                            {{-- Accent2 --}}
                            <div class="wc-row">
                                <div class="wc-row-label"><span class="iconify" data-icon="solar:star-shine-bold"></span> Accent 2</div>
                                <div class="wc-row-ctrl">
                                    <div class="wc-color-wrap">
                                        <div class="wc-color-swatch" id="alert-swatch-accent2" style="background:{{ $ws_alert['accent2'] }}"></div>
                                        <input type="color" class="wc-color-input" id="alert-color-accent2" value="{{ $ws_alert['accent2'] }}" oninput="wcColorChange('alert','accent2',this)">
                                    </div>
                                </div>
                            </div>
                            {{-- Amount color --}}
                            <div class="wc-row">
                                <div class="wc-row-label"><span class="iconify" data-icon="solar:dollar-bold"></span> Warna Nominal</div>
                                <div class="wc-row-ctrl">
                                    <div class="wc-color-wrap">
                                        <div class="wc-color-swatch" id="alert-swatch-amount_color" style="background:{{ $ws_alert['amount_color'] }}"></div>
                                        <input type="color" class="wc-color-input" id="alert-color-amount_color" value="{{ $ws_alert['amount_color'] }}" oninput="wcColorChange('alert','amount_color',this)">
                                    </div>
                                </div>
                            </div>
                            {{-- Donor color --}}
                            <div class="wc-row">
                                <div class="wc-row-label"><span class="iconify" data-icon="solar:user-bold"></span> Warna Nama</div>
                                <div class="wc-row-ctrl">
                                    <div class="wc-color-wrap">
                                        <div class="wc-color-swatch" id="alert-swatch-donor_color" style="background:{{ $ws_alert['donor_color'] }}"></div>
                                        <input type="color" class="wc-color-input" id="alert-color-donor_color" value="{{ $ws_alert['donor_color'] }}" oninput="wcColorChange('alert','donor_color',this)">
                                    </div>
                                </div>
                            </div>
                            {{-- Top line gradient --}}
                            <div class="wc-row">
                                <div class="wc-row-label"><span class="iconify" data-icon="solar:line-duotone"></span> Garis Atas</div>
                                <div class="wc-row-ctrl">
                                    <div class="wc-gradient-preview" id="alert-grad-top_line" style="background:{{ $ws_alert['top_line'] }}"></div>
                                    <div class="wc-grad-stops">
                                        <span class="wc-grad-stop-label">A</span>
                                        <div class="wc-color-wrap">
                                            <div class="wc-color-swatch" id="alert-grad-swatch-top_line-a" style="background:{{ $alertTlA }}"></div>
                                            <input type="color" class="wc-color-input" id="alert-grad-color-top_line-a" value="{{ $alertTlA }}" oninput="wcGradientStopChange('alert','top_line',this,document.getElementById('alert-grad-color-top_line-b'))">
                                        </div>
                                        <span class="wc-grad-stop-label">B</span>
                                        <div class="wc-color-wrap">
                                            <div class="wc-color-swatch" id="alert-grad-swatch-top_line-b" style="background:{{ $alertTlB }}"></div>
                                            <input type="color" class="wc-color-input" id="alert-grad-color-top_line-b" value="{{ $alertTlB }}" oninput="wcGradientStopChange('alert','top_line',document.getElementById('alert-grad-color-top_line-a'),this)">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Progress bar gradient --}}
                            <div class="wc-row">
                                <div class="wc-row-label"><span class="iconify" data-icon="solar:chart-2-bold"></span> Progress Bar</div>
                                <div class="wc-row-ctrl">
                                    <div class="wc-gradient-preview" id="alert-grad-prog_bar" style="background:{{ $ws_alert['prog_bar'] }}"></div>
                                    <div class="wc-grad-stops">
                                        <span class="wc-grad-stop-label">A</span>
                                        <div class="wc-color-wrap">
                                            <div class="wc-color-swatch" id="alert-grad-swatch-prog_bar-a" style="background:{{ $alertPbA }}"></div>
                                            <input type="color" class="wc-color-input" id="alert-grad-color-prog_bar-a" value="{{ $alertPbA }}" oninput="wcGradientStopChange('alert','prog_bar',this,document.getElementById('alert-grad-color-prog_bar-b'))">
                                        </div>
                                        <span class="wc-grad-stop-label">B</span>
                                        <div class="wc-color-wrap">
                                            <div class="wc-color-swatch" id="alert-grad-swatch-prog_bar-b" style="background:{{ $alertPbB }}"></div>
                                            <input type="color" class="wc-color-input" id="alert-grad-color-prog_bar-b" value="{{ $alertPbB }}" oninput="wcGradientStopChange('alert','prog_bar',document.getElementById('alert-grad-color-prog_bar-a'),this)">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- Border radius --}}
                            <div class="wc-row">
                                <div class="wc-row-label"><span class="iconify" data-icon="solar:rounded-bold"></span> Border Radius</div>
                                <div class="wc-row-ctrl">
                                    <div class="wc-slider-wrap">
                                        <input type="range" class="wc-slider" min="0" max="32" value="{{ $ws_alert['radius'] }}" id="alert-slider-radius" oninput="wcSliderChange('alert','radius',this)">
                                        <span class="wc-slider-val" id="alert-sliderval-radius">{{ $ws_alert['radius'] }}px</span>
                                    </div>
                                </div>
                            </div>
                        </div>{{-- /alert-custom --}}

                        <div class="wc-step-footer">
                            <button class="wc-step-prev" onclick="wcStep('alert',2,document.querySelector('#alert-steps .wc-step-btn:nth-child(2)'))">
                                <span class="iconify" data-icon="solar:arrow-left-bold"></span> Kembali
                            </button>
                            <button class="wc-step-next primary" onclick="wcStep('alert',4,document.querySelector('#alert-steps .wc-step-btn:nth-child(4)'))">
                                Berikutnya <span class="iconify" data-icon="solar:arrow-right-bold"></span>
                            </button>
                        </div>
                    </div>{{-- /step-3 --}}

                    {{-- ══ STEP 4: Typography ══ --}}
                    <div class="wc-step-panel" id="alert-step-4">
                        <div class="ws-section-label" style="margin-bottom:10px">
                            <span class="iconify" data-icon="solar:text-bold" style="width:12px;height:12px"></span>
                            Font keluarga
                        </div>
                        <div class="wc-font-grid" id="alert-fonts">
                            @php $fontFamilyCurrent = $ws_alert['font_family'] ?? 'inter'; @endphp
                            <button class="wc-font-btn {{ $fontFamilyCurrent==='inter'?'active':'' }}" style="font-family:'Inter',sans-serif" onclick="wcSelectFont('alert','inter',this)">Inter</button>
                            <button class="wc-font-btn {{ $fontFamilyCurrent==='space-grotesk'?'active':'' }}" style="font-family:'Space Grotesk',sans-serif" onclick="wcSelectFont('alert','space-grotesk',this)">Space Grotesk</button>
                            <button class="wc-font-btn {{ $fontFamilyCurrent==='plus-jakarta'?'active':'' }}" style="font-family:'Plus Jakarta Sans',sans-serif" onclick="wcSelectFont('alert','plus-jakarta',this)">Plus Jakarta</button>
                            <button class="wc-font-btn {{ $fontFamilyCurrent==='poppins'?'active':'' }}" style="font-family:'Poppins',sans-serif" onclick="wcSelectFont('alert','poppins',this)">Poppins</button>
                            <button class="wc-font-btn {{ $fontFamilyCurrent==='nunito'?'active':'' }}" style="font-family:'Nunito',sans-serif" onclick="wcSelectFont('alert','nunito',this)">Nunito</button>
                        </div>

                        <div class="ws-section-label" style="margin-top:14px;margin-bottom:4px">
                            <span class="iconify" data-icon="solar:text-field-bold" style="width:12px;height:12px"></span>
                            Ukuran font per elemen
                        </div>
                        {{-- font_size_title --}}
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:user-bold"></span> Nama Donatur</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-slider-wrap">
                                    <input type="range" class="wc-slider" min="11" max="28" value="{{ $ws_alert['font_size_title'] ?? 17 }}" id="alert-slider-font_size_title" oninput="wcFontSizeChange('alert','font_size_title',this)">
                                    <span class="wc-slider-val" id="alert-sliderval-font_size_title">{{ $ws_alert['font_size_title'] ?? 17 }}px</span>
                                </div>
                            </div>
                        </div>
                        {{-- font_size_amount --}}
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:dollar-bold"></span> Nominal</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-slider-wrap">
                                    <input type="range" class="wc-slider" min="14" max="36" value="{{ $ws_alert['font_size_amount'] ?? 24 }}" id="alert-slider-font_size_amount" oninput="wcFontSizeChange('alert','font_size_amount',this)">
                                    <span class="wc-slider-val" id="alert-sliderval-font_size_amount">{{ $ws_alert['font_size_amount'] ?? 24 }}px</span>
                                </div>
                            </div>
                        </div>
                        {{-- font_size_msg --}}
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:chat-round-bold"></span> Pesan</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-slider-wrap">
                                    <input type="range" class="wc-slider" min="10" max="20" value="{{ $ws_alert['font_size_msg'] ?? 13 }}" id="alert-slider-font_size_msg" oninput="wcFontSizeChange('alert','font_size_msg',this)">
                                    <span class="wc-slider-val" id="alert-sliderval-font_size_msg">{{ $ws_alert['font_size_msg'] ?? 13 }}px</span>
                                </div>
                            </div>
                        </div>
                        <div class="wc-step-footer">
                            <button class="wc-step-prev" onclick="wcStep('alert',3,document.querySelector('#alert-steps .wc-step-btn:nth-child(3)'))">
                                <span class="iconify" data-icon="solar:arrow-left-bold"></span> Kembali
                            </button>
                            <div></div>
                        </div>
                        <div class="wc-save-row" style="margin-top:8px">
                            <button class="wc-save-btn" id="alert-save-btn" onclick="wcSave('alert')">
                                <span class="iconify" data-icon="solar:floppy-disk-bold"></span>
                                Simpan Tampilan
                            </button>
                            <button class="wc-reset-link" onclick="wcReset('alert')">Reset ke default</button>
                        </div>
                    </div>{{-- /step-4 --}}

                </div>{{-- /customizer alert --}}

                {{-- ── Phase 3: Live Preview — Alert ── --}}
                <div class="ws-card wc-preview-card">
                    <div class="ws-card-head">
                        <div class="ws-card-icon" style="background:rgba(34,211,160,.1);border:1px solid rgba(34,211,160,.2)">
                            <span class="iconify" data-icon="solar:eye-bold-duotone" style="color:var(--green)"></span>
                        </div>
                        <div>
                            <div class="ws-card-title">Live Preview</div>
                            <div class="ws-card-sub">Tampilan Alert sesuai pengaturan kamu — update otomatis saat kamu mengubah warna</div>
                        </div>
                    </div>
                    <div class="wc-preview-viewport" id="preview-alert-viewport">
                        <span class="wc-preview-label">
                            <span class="wc-preview-dot"></span>PREVIEW
                        </span>
                        <div class="wc-preview-frame" style="width:100%;height:180px">
                            <div class="wc-preview-stage" id="preview-alert-stage" style="transform:scale(.55);transform-origin:top center;width:480px">
                                 <div class="wc-prev-alert" id="preview-alert">
                                 <div class="alert-mock-box">
                                     <div class="alert-mock-topline"></div>
                                     <div class="alert-mock-inner">
                                         <div class="alert-mock-header">
                                             <div class="alert-mock-names">
                                                 <div class="alert-mock-donor">Budi Santoso</div>
                                                 <div class="alert-mock-amount">Rp 50.000</div>
                                             </div>
                                             <div class="alert-mock-badge">DONASI MASUK</div>
                                         </div>
                                         <div class="alert-mock-divider"></div>
                                         <div class="alert-mock-msg">"Semangat terus streamnya, mantap kali!"</div>
                                     </div>
                                     <div class="alert-mock-side-body">
                                         <div class="alert-mock-side-left">
                                             <div class="alert-mock-side-donor">Budi Santoso</div>
                                             <div class="alert-mock-side-badge">DONASI MASUK</div>
                                             <div class="alert-mock-side-msg">"Semangat terus streamnya!"</div>
                                         </div>
                                         <div class="alert-mock-side-right">
                                             <div class="alert-mock-side-amount">Rp 50.000</div>
                                         </div>
                                     </div>
                                     <div class="alert-mock-bar-wrap">
                                         <div class="alert-mock-bar"></div>
                                     </div>
                                 </div>
                             </div>
                             </div>{{-- /wc-preview-frame --}}
                     </div>
                 </div>
             </div>{{-- /wc-preview-card --}}

                {{-- ══ Pengaturan Alert ══ --}}
                <div class="ws-card" id="alert-settings-card">
                    <div class="ws-card-head">
                        <div class="ws-card-icon" style="background:rgba(249,115,22,.1);border:1px solid rgba(249,115,22,.2)">
                            <span class="iconify" data-icon="solar:settings-bold-duotone" style="color:var(--orange)"></span>
                        </div>
                        <div>
                            <div class="ws-card-title">Pengaturan Alert</div>
                            <div class="ws-card-sub">Audio, durasi per donasi, dan pengaturan video</div>
                        </div>
                    </div>

                    {{-- ── Section 1: Audio ── --}}
                    <div class="was-section">
                        <div class="was-section-head">
                            <div class="was-section-icon" style="background:rgba(34,211,160,.1)">
                                <span class="iconify" data-icon="solar:volume-loud-bold-duotone" style="color:#22d3a0"></span>
                            </div>
                            <div class="was-section-title">Suara Notifikasi</div>
                            <div class="was-section-sub">Pilih suara yang muncul saat ada donasi</div>
                        </div>

                        {{-- Toggle aktif/nonaktif --}}
                        <div class="was-toggle-row">
                            <div class="was-toggle-label">
                                <strong>Aktifkan suara</strong> — putar notifikasi audio saat alert muncul
                            </div>
                            <label class="was-toggle" id="was-sound-toggle-label">
                                <input type="checkbox" id="was-sound-enabled"
                                       {{ $streamer->sound_enabled ? 'checked' : '' }}
                                       onchange="wasSoundToggle(this)">
                                <span class="was-toggle-track"></span>
                            </label>
                        </div>

                        {{-- Sound preset grid --}}
                        @php
                            $wasCurrentSound = $streamer->notification_sound ?? 'ding';
                            $wasSounds = [
                                ['key' => 'ding',       'label' => 'Ding',        'icon' => '🔔'],
                                ['key' => 'coin',       'label' => 'Coin',        'icon' => '🪙'],
                                ['key' => 'whoosh',     'label' => 'Whoosh',      'icon' => '💨'],
                                ['key' => 'chime',      'label' => 'Chime',       'icon' => '🎵'],
                                ['key' => 'pop',        'label' => 'Pop',         'icon' => '🫧'],
                                ['key' => 'tada',       'label' => 'Tada',        'icon' => '🎉'],
                                ['key' => 'woosh_light','label' => 'Woosh Soft',  'icon' => '🌬️'],
                                ['key' => 'blip',       'label' => 'Blip',        'icon' => '📡'],
                                ['key' => 'sparkle',    'label' => 'Sparkle',     'icon' => '✨'],
                                ['key' => 'fanfare',    'label' => 'Fanfare',     'icon' => '🎺'],
                            ];
                        @endphp
                        <div class="was-sound-grid" id="was-sound-grid">
                            @foreach($wasSounds as $ws)
                                <div class="was-sound-card {{ $wasCurrentSound === $ws['key'] ? 'active' : '' }}"
                                     data-sound="{{ $ws['key'] }}"
                                     onclick="wasSelectSound('{{ $ws['key'] }}', this)">
                                    <button class="was-sound-play"
                                            title="Preview suara"
                                            onclick="event.stopPropagation(); wasPreviewSound('{{ $ws['key'] }}')">
                                        <span class="iconify" data-icon="solar:play-bold"></span>
                                    </button>
                                    <div class="was-sound-icon">{{ $ws['icon'] }}</div>
                                    <div class="was-sound-name">{{ $ws['label'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ── Section 2: Durasi per Tier Donasi ── --}}
                    <div class="was-section">
                        <div class="was-section-head">
                            <div class="was-section-icon" style="background:rgba(124,108,252,.1)">
                                <span class="iconify" data-icon="solar:clock-circle-bold-duotone" style="color:var(--brand-light)"></span>
                            </div>
                            <div class="was-section-title">Durasi Alert per Tier Donasi</div>
                            <div class="was-section-sub">Alert lebih lama untuk donasi lebih besar</div>
                        </div>

                        <table class="was-tier-table" id="was-tier-table">
                            <thead>
                                <tr>
                                    <th>Tier</th>
                                    <th>Minimal Donasi (Rp)</th>
                                    <th colspan="2">Durasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $tierLabels = ['Kecil', 'Sedang', 'Besar', 'Super'];
                                    $tierColors = ['#22d3a0', '#7c6cfc', '#f97316', '#f59e0b'];
                                    $maxDurJs   = $alertMaxDur;
                                @endphp
                                @foreach($alertTiers as $ti => $tier)
                                <tr>
                                    <td>
                                        <span class="was-tier-badge" style="background:rgba(0,0,0,.2);border-color:{{ $tierColors[$ti] }}20;color:{{ $tierColors[$ti] }}">
                                            {{ $tierLabels[$ti] ?? 'Tier '.($ti+1) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($ti === 0)
                                            <span class="was-tier-from-label">0 (semua)</span>
                                            <input type="hidden" class="was-tier-from" data-tier="{{ $ti }}" value="0">
                                        @else
                                            <input type="number" class="was-tier-input was-tier-from"
                                                   data-tier="{{ $ti }}"
                                                   value="{{ $tier['from'] }}"
                                                   min="1" max="99999999"
                                                   oninput="wasTierFromChange({{ $ti }}, this)">
                                        @endif
                                    </td>
                                    <td>
                                        <div class="was-tier-dur-wrap">
                                            <input type="range" class="was-tier-dur-slider"
                                                   id="was-tier-slider-{{ $ti }}"
                                                   data-tier="{{ $ti }}"
                                                   min="1" max="{{ $alertMaxDur }}"
                                                   value="{{ min($tier['duration'], $alertMaxDur) }}"
                                                   oninput="wasTierDurChange({{ $ti }}, this)">
                                            <span class="was-tier-dur-val" id="was-tier-durval-{{ $ti }}">
                                                {{ min($tier['duration'], $alertMaxDur) }}s
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- Max duration control --}}
                        <div class="was-max-dur-row">
                            <span class="iconify" data-icon="solar:stopwatch-bold-duotone"></span>
                            <span class="was-max-dur-label">Durasi maksimum yang diizinkan (batas semua tier)</span>
                            <input type="number" class="was-max-dur-input" id="was-max-dur"
                                   value="{{ $alertMaxDur }}"
                                   min="5" max="120"
                                   oninput="wasMaxDurChange(this)">
                            <span class="was-max-dur-unit">detik (maks 120)</span>
                        </div>
                    </div>

                    {{-- ── Section 3: Video ── --}}
                    <div class="was-section">
                        <div class="was-section-head">
                            <div class="was-section-icon" style="background:rgba(239,68,68,.1)">
                                <span class="iconify" data-icon="solar:play-circle-bold-duotone" style="color:#ef4444"></span>
                            </div>
                            <div class="was-section-title">Permintaan Video</div>
                            <div class="was-section-sub">Donatur bisa minta putar video saat donasi</div>
                        </div>

                        <div class="was-video-grid">
                            {{-- YouTube --}}
                            <div class="was-video-card">
                                <div class="was-video-head">
                                    <div class="was-video-icon" style="background:rgba(239,68,68,.12)">
                                        <span class="iconify" data-icon="solar:youtube-bold-duotone" style="color:#ef4444;width:20px;height:20px"></span>
                                    </div>
                                    <div class="was-video-title">YouTube</div>
                                    <label class="was-toggle">
                                        <input type="checkbox" id="was-yt-enabled"
                                               {{ $streamer->yt_enabled ? 'checked' : '' }}
                                               onchange="wasYtToggle(this)">
                                        <span class="was-toggle-track"></span>
                                    </label>
                                </div>
                                <div class="was-video-desc">
                                    Saat aktif, donatur bisa menyertakan link YouTube saat donasi. Video akan otomatis diputar di alert.
                                </div>
                            </div>

                            {{-- TikTok — Coming Soon --}}
                            <div class="was-video-card disabled">
                                <div class="was-video-head">
                                    <div class="was-video-icon" style="background:rgba(255,255,255,.06)">
                                        <span class="iconify" data-icon="solar:tiktok-bold-duotone" style="color:var(--text-3);width:20px;height:20px"></span>
                                    </div>
                                    <div class="was-video-title">TikTok</div>
                                    <span class="was-coming-soon">
                                        <span class="iconify" data-icon="solar:hourglass-bold" style="width:10px;height:10px"></span>
                                        Coming Soon
                                    </span>
                                </div>
                                <div class="was-video-desc">
                                    Dukungan untuk request video TikTok akan hadir di update berikutnya.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Save button ── --}}
                    <div class="was-save-row">
                        <button class="was-save-btn" id="was-save-btn" onclick="wasSave()">
                            <span class="iconify" data-icon="solar:floppy-disk-bold"></span>
                            Simpan Pengaturan
                        </button>
                    </div>
                </div>{{-- /alert-settings-card --}}

            </div>{{-- /tab-alert --}}

            {{-- ══ TAB: Milestone ══ --}}
            <div class="ws-panel" id="tab-milestone">

                <div class="ws-card">
                    <div class="ws-card-head">
                        <div class="ws-card-icon" style="background:rgba(168,85,247,.1);border:1px solid rgba(168,85,247,.2)">
                            <span class="iconify" data-icon="solar:target-bold-duotone" style="color:var(--purple)"></span>
                        </div>
                        <div>
                            <div class="ws-card-title">Milestone Widget</div>
                            <div class="ws-card-sub">Progress bar target donasi real-time</div>
                        </div>
                        <div class="ws-card-head-right">
                            <div class="status-live">
                                <span class="status-live-dot"></span>
                                Real-time via SSE
                            </div>
                        </div>
                    </div>

                    <div class="ws-tags">
                        <span class="ws-tag purple">
                            <span class="iconify" data-icon="solar:target-bold"></span>
                            Progress Bar
                        </span>
                        <span class="ws-tag green">
                            <span class="iconify" data-icon="solar:refresh-bold"></span>
                            Auto Update
                        </span>
                        <span class="ws-tag blue">
                            <span class="iconify" data-icon="solar:star-bold"></span>
                            Auto Reset: {{ $streamer->milestone_reset ? 'ON' : 'OFF' }}
                        </span>
                    </div>

                    <div class="ws-size-chips">
                        <span class="ws-size-chip">
                            <span class="iconify" data-icon="solar:ruler-bold"></span>
                            Default 340 × ~130 px
                        </span>
                        <span class="ws-size-chip">
                            <span class="iconify" data-icon="solar:monitor-bold"></span>
                            Canvas 1920 × 1080
                        </span>
                        <span class="ws-size-chip">
                            <span class="iconify" data-icon="solar:target-bold"></span>
                            Target: Rp {{ number_format($streamer->milestone_target, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="ws-info-box">
                        <span class="iconify" data-icon="solar:info-circle-bold-duotone"></span>
                        <span>Widget ini tampil permanen di layar. Posisi default: <strong>bawah kiri</strong> canvas. Ubah judul &amp; target di menu <a href="{{ route('streamer.settings') }}#milestone" style="color:var(--brand-light)">Settings → Milestone</a>.</span>
                    </div>

                    <div class="ws-section-label">
                        <span class="iconify" data-icon="solar:link-bold" style="width:12px;height:12px"></span>
                        URL Browser Source OBS
                    </div>

                    <div class="obs-url-grid">
                        <div class="obs-url-row">
                            <div class="obs-url-icon" style="background:rgba(168,85,247,.1);border:1px solid rgba(168,85,247,.2)">
                                <span class="iconify" data-icon="solar:target-bold-duotone" style="color:var(--purple)"></span>
                            </div>
                            <div class="obs-url-info">
                                <div class="obs-url-label">
                                    Milestone Widget
                                    <code style="font-size:9px;background:rgba(255,255,255,.06);padding:1px 5px;border-radius:4px">?key=</code>
                                    sudah termasuk
                                </div>
                                @php $msUrl = route('obs.milestone', $streamer->slug) . '?key=' . $streamer->api_key; @endphp
                                <input class="obs-url-input" readonly value="{{ $msUrl }}" id="url-milestone" />
                            </div>
                            <div class="obs-url-actions">
                                <button class="obs-btn primary" onclick="copyText('{{ $msUrl }}', 'URL Milestone')">
                                    <span class="iconify" data-icon="solar:copy-bold-duotone"></span>Copy URL
                                </button>
                                <a class="obs-btn" href="{{ $msUrl }}" target="_blank">
                                    <span class="iconify" data-icon="solar:eye-bold-duotone"></span>Buka
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Phase 2: Customizer — Milestone ── --}}
                @php $ws_ms = $widgetSettings['milestone']; @endphp
                <div class="ws-card">
                    <div class="ws-card-head">
                        <div class="ws-card-icon" style="background:rgba(124,108,252,.1);border:1px solid rgba(124,108,252,.2)">
                            <span class="iconify" data-icon="solar:palette-bold-duotone" style="color:var(--brand-light)"></span>
                        </div>
                        <div>
                            <div class="ws-card-title">Kustomisasi Tampilan</div>
                            <div class="ws-card-sub">Pilih preset atau atur warna custom untuk Milestone widget</div>
                        </div>
                    </div>

                    <div class="ws-section-label">
                        <span class="iconify" data-icon="solar:magic-stick-bold" style="width:12px;height:12px"></span>
                        Preset Tema
                    </div>

                    <div class="wc-presets" id="milestone-presets">
                        <div class="wc-preset-card {{ $ws_ms['preset']==='default'?'active':'' }}" data-preset="default" onclick="wcSelectPreset('milestone','default',this)">
                            <div class="wc-preset-swatch" style="background:linear-gradient(135deg,rgba(8,8,12,.96),#1a1a2e);border-color:rgba(124,108,252,.2)"></div>
                            <div class="wc-preset-name">Default</div>
                            <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                        </div>
                        <div class="wc-preset-card {{ $ws_ms['preset']==='neon'?'active':'' }}" data-preset="neon" onclick="wcSelectPreset('milestone','neon',this)">
                            <div class="wc-preset-swatch" style="background:linear-gradient(135deg,rgba(2,4,18,.97),#001a14);border-color:rgba(0,255,200,.22)"></div>
                            <div class="wc-preset-name">Neon</div>
                            <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                        </div>
                         <div class="wc-preset-card {{ $ws_ms['preset']==='fire'?'active':'' }}" data-preset="fire" onclick="wcSelectPreset('milestone','fire',this)">
                             <div class="wc-preset-swatch" style="background:linear-gradient(135deg,rgba(10,4,2,.97),#2a0a00);border-color:rgba(249,115,22,.22)"></div>
                             <div class="wc-preset-name">Fire</div>
                             <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                         </div>
                         <div class="wc-preset-card {{ $ws_ms['preset']==='ice'?'active':'' }}" data-preset="ice" onclick="wcSelectPreset('milestone','ice',this)">
                             <div class="wc-preset-swatch" style="background:linear-gradient(135deg,rgba(2,8,22,.96),#001830);border-color:rgba(147,210,255,.18)"></div>
                             <div class="wc-preset-name">Ice</div>
                             <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                         </div>
                         <div class="wc-preset-card {{ $ws_ms['preset']==='minimal'?'active':'' }}" data-preset="minimal" onclick="wcSelectPreset('milestone','minimal',this)">
                             <div class="wc-preset-swatch" style="background:linear-gradient(135deg,rgba(12,12,16,.95),#1a1a1e);border-color:rgba(255,255,255,.14)"></div>
                             <div class="wc-preset-name">Minimal</div>
                             <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                         </div>
                         <div class="wc-preset-card {{ $ws_ms['preset']==='custom'?'active':'' }}" data-preset="custom" onclick="wcSelectPreset('milestone','custom',this)">
                            <div class="wc-preset-swatch" style="background:linear-gradient(135deg,var(--brand),var(--purple));border-color:rgba(124,108,252,.3)"></div>
                            <div class="wc-preset-name">Custom</div>
                            <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                        </div>
                    </div>

                    @php
                        preg_match('/rgba\((\d+),\s*(\d+),\s*(\d+),\s*([\d.]+)\)/', $ws_ms['surface'], $msM);
                        $msSurfaceHex = isset($msM[1]) ? sprintf('#%02x%02x%02x',$msM[1],$msM[2],$msM[3]) : (preg_match('/^#[0-9a-fA-F]{6}/',$ws_ms['surface'],$hM)?$hM[0]:'#08080c');
                        $msSurfaceOpacity = isset($msM[4]) ? round($msM[4]*100) : 96;

                        preg_match('/rgba\((\d+),\s*(\d+),\s*(\d+),\s*([\d.]+)\)/', $ws_ms['border'], $msBdM);
                        $msBorderHex = isset($msBdM[1]) ? sprintf('#%02x%02x%02x',$msBdM[1],$msBdM[2],$msBdM[3]) : (preg_match('/^#[0-9a-fA-F]{6}/',$ws_ms['border'],$hM)?$hM[0]:'#7c6cfc');
                        $msBorderOpacity = isset($msBdM[4]) ? round($msBdM[4]*100) : 20;
                    @endphp
                    <div class="wc-custom-panel {{ $ws_ms['preset']==='custom'?'visible':'' }}" id="milestone-custom">
                        <div class="ws-section-label" style="margin-bottom:4px">
                            <span class="iconify" data-icon="solar:pen-bold" style="width:12px;height:12px"></span>
                            Warna &amp; Gaya Custom
                        </div>

                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:square-bold"></span>Surface</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-color-wrap">
                                    <div class="wc-color-swatch" id="milestone-swatch-surface" style="background:{{ $ws_ms['surface'] }}"></div>
                                    <input type="color" class="wc-color-input" id="milestone-color-surface" value="{{ $msSurfaceHex }}" oninput="wcRgbaColorChange('milestone','surface',this)">
                                </div>
                                <div class="wc-opacity-wrap">
                                    <span class="wc-opacity-label">Opacity</span>
                                    <input type="range" class="wc-slider" min="0" max="100" value="{{ $msSurfaceOpacity }}" id="milestone-opacity-surface" oninput="wcOpacityChange('milestone','surface',this)">
                                    <span class="wc-opacity-val" id="milestone-opacityval-surface">{{ $msSurfaceOpacity }}%</span>
                                </div>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:border-bold"></span>Border</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-color-wrap">
                                    <div class="wc-color-swatch" id="milestone-swatch-border" style="background:{{ $ws_ms['border'] }}"></div>
                                    <input type="color" class="wc-color-input" id="milestone-color-border" value="{{ $msBorderHex }}" oninput="wcRgbaColorChange('milestone','border',this)">
                                </div>
                                <div class="wc-opacity-wrap">
                                    <span class="wc-opacity-label">Opacity</span>
                                    <input type="range" class="wc-slider" min="0" max="100" value="{{ $msBorderOpacity }}" id="milestone-opacity-border" oninput="wcOpacityChange('milestone','border',this)">
                                    <span class="wc-opacity-val" id="milestone-opacityval-border">{{ $msBorderOpacity }}%</span>
                                </div>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:star-bold"></span>Brand</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-color-wrap">
                                    <div class="wc-color-swatch" id="milestone-swatch-brand" style="background:{{ $ws_ms['brand'] }}"></div>
                                    <input type="color" class="wc-color-input" id="milestone-color-brand" value="{{ $ws_ms['brand'] }}" oninput="wcColorChange('milestone','brand',this)">
                                </div>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:star-shine-bold"></span>Brand 2</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-color-wrap">
                                    <div class="wc-color-swatch" id="milestone-swatch-brand2" style="background:{{ $ws_ms['brand2'] }}"></div>
                                    <input type="color" class="wc-color-input" id="milestone-color-brand2" value="{{ $ws_ms['brand2'] }}" oninput="wcColorChange('milestone','brand2',this)">
                                </div>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:dollar-bold"></span>Warna Nominal</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-color-wrap">
                                    <div class="wc-color-swatch" id="milestone-swatch-orange" style="background:{{ $ws_ms['orange'] }}"></div>
                                    <input type="color" class="wc-color-input" id="milestone-color-orange" value="{{ $ws_ms['orange'] }}" oninput="wcColorChange('milestone','orange',this)">
                                </div>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:check-circle-bold"></span>Warna Tercapai</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-color-wrap">
                                    <div class="wc-color-swatch" id="milestone-swatch-green" style="background:{{ $ws_ms['green'] }}"></div>
                                    <input type="color" class="wc-color-input" id="milestone-color-green" value="{{ $ws_ms['green'] }}" oninput="wcColorChange('milestone','green',this)">
                                </div>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:rounded-bold"></span>Border Radius</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-slider-wrap">
                                    <input type="range" class="wc-slider" min="0" max="32" value="{{ $ws_ms['radius'] }}" id="milestone-slider-radius" oninput="wcSliderChange('milestone','radius',this)">
                                    <span class="wc-slider-val" id="milestone-sliderval-radius">{{ $ws_ms['radius'] }}px</span>
                                </div>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:ruler-cross-pen-bold"></span>Lebar Widget</div>
                            <div class="wc-row-ctrl">
                                <input type="number" class="wc-number" min="200" max="600" value="{{ $ws_ms['width'] }}" id="milestone-num-width" oninput="wcNumChange('milestone','width',this)">
                                <span class="wc-unit">px</span>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:map-point-bold"></span>Posisi</div>
                            <div class="wc-row-ctrl">
                                <select class="wc-select" id="milestone-sel-position" onchange="wcSelectChange('milestone','position',this)">
                                    <option value="top-left"     {{ $ws_ms['position']==='top-left'     ?'selected':'' }}>Atas Kiri</option>
                                    <option value="top-right"    {{ $ws_ms['position']==='top-right'    ?'selected':'' }}>Atas Kanan</option>
                                    <option value="bottom-left"  {{ $ws_ms['position']==='bottom-left'  ?'selected':'' }}>Bawah Kiri</option>
                                    <option value="bottom-right" {{ $ws_ms['position']==='bottom-right' ?'selected':'' }}>Bawah Kanan</option>
                                </select>
                            </div>
                        </div>
                    </div>{{-- /milestone-custom --}}

                    <div class="wc-save-row">
                        <button class="wc-save-btn" id="milestone-save-btn" onclick="wcSave('milestone')">
                            <span class="iconify" data-icon="solar:floppy-disk-bold"></span>
                            Simpan Tampilan
                        </button>
                        <button class="wc-reset-link" onclick="wcReset('milestone')">Reset ke default</button>
                    </div>
                </div>{{-- /customizer milestone --}}

                {{-- ── Phase 3: Live Preview — Milestone ── --}}
                <div class="ws-card wc-preview-card">
                    <div class="ws-card-head">
                        <div class="ws-card-icon" style="background:rgba(34,211,160,.1);border:1px solid rgba(34,211,160,.2)">
                            <span class="iconify" data-icon="solar:eye-bold-duotone" style="color:var(--green)"></span>
                        </div>
                        <div>
                            <div class="ws-card-title">Live Preview</div>
                            <div class="ws-card-sub">Tampilan Milestone sesuai pengaturan kamu</div>
                        </div>
                    </div>
                    <div class="wc-preview-viewport" id="preview-milestone-viewport">
                        <span class="wc-preview-label">
                            <span class="wc-preview-dot"></span>PREVIEW
                        </span>
                        <div class="wc-preview-frame" style="width:100%;height:160px">
                            <div class="wc-preview-stage" id="preview-milestone-stage" style="transform:scale(.65);transform-origin:top center;width:320px">
                                <div class="wc-prev-milestone" id="preview-milestone">
                                    <div class="ms-mock-wrap">
                                        <div class="ms-mock-topline"></div>
                                        <div class="ms-mock-inner">
                                            <div class="ms-mock-badge">MILESTONE</div>
                                            <div class="ms-mock-title">Target Donasi Stream ini</div>
                                            <div class="ms-mock-amounts">
                                                <div class="ms-mock-current">Rp 145.000</div>
                                                <div class="ms-mock-sep">/</div>
                                                <div class="ms-mock-target">Rp 250.000</div>
                                                <div class="ms-mock-pct">58%</div>
                                            </div>
                                            <div class="ms-mock-track">
                                                <div class="ms-mock-bar"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>{{-- /wc-preview-frame --}}
                    </div>
                </div>

            </div>{{-- /tab-milestone --}}

            {{-- ══ TAB: Leaderboard ══ --}}
            <div class="ws-panel" id="tab-leaderboard">

                <div class="ws-card">
                    <div class="ws-card-head">
                        <div class="ws-card-icon" style="background:rgba(251,191,36,.1);border:1px solid rgba(251,191,36,.2)">
                            <span class="iconify" data-icon="solar:ranking-bold-duotone" style="color:var(--yellow)"></span>
                        </div>
                        <div>
                            <div class="ws-card-title">Leaderboard Widget</div>
                            <div class="ws-card-sub">Panel top donatur real-time</div>
                        </div>
                        <div class="ws-card-head-right">
                            <div class="status-live">
                                <span class="status-live-dot"></span>
                                Real-time via SSE
                            </div>
                        </div>
                    </div>

                    <div class="ws-tags">
                        <span class="ws-tag yellow">
                            <span class="iconify" data-icon="solar:ranking-bold"></span>
                            Top {{ $streamer->leaderboard_count }} Donatur
                        </span>
                        <span class="ws-tag green">
                            <span class="iconify" data-icon="solar:refresh-bold"></span>
                            Auto Update
                        </span>
                        <span class="ws-tag blue">
                            <span class="iconify" data-icon="solar:medal-ribbons-star-bold"></span>
                            Medali Otomatis
                        </span>
                    </div>

                    <div class="ws-size-chips">
                        <span class="ws-size-chip">
                            <span class="iconify" data-icon="solar:ruler-bold"></span>
                            Default 300 × auto px
                        </span>
                        <span class="ws-size-chip">
                            <span class="iconify" data-icon="solar:monitor-bold"></span>
                            Canvas 1920 × 1080
                        </span>
                        <span class="ws-size-chip">
                            <span class="iconify" data-icon="solar:users-group-rounded-bold"></span>
                            Menampilkan: {{ $streamer->leaderboard_count }} donatur
                        </span>
                    </div>

                    <div class="ws-info-box">
                        <span class="iconify" data-icon="solar:info-circle-bold-duotone"></span>
                        <span>Widget ini tampil permanen di layar. Posisi default: <strong>atas kiri</strong> canvas. Ubah judul &amp; jumlah tampil di menu <a href="{{ route('streamer.settings') }}#leaderboard" style="color:var(--brand-light)">Settings → Leaderboard</a>.</span>
                    </div>

                    <div class="ws-section-label">
                        <span class="iconify" data-icon="solar:link-bold" style="width:12px;height:12px"></span>
                        URL Browser Source OBS
                    </div>

                    <div class="obs-url-grid">
                        <div class="obs-url-row">
                            <div class="obs-url-icon" style="background:rgba(251,191,36,.1);border:1px solid rgba(251,191,36,.2)">
                                <span class="iconify" data-icon="solar:ranking-bold-duotone" style="color:var(--yellow)"></span>
                            </div>
                            <div class="obs-url-info">
                                <div class="obs-url-label">
                                    Leaderboard Widget
                                    <code style="font-size:9px;background:rgba(255,255,255,.06);padding:1px 5px;border-radius:4px">?key=</code>
                                    sudah termasuk
                                </div>
                                @php $lbUrl = route('obs.leaderboard', $streamer->slug) . '?key=' . $streamer->api_key; @endphp
                                <input class="obs-url-input" readonly value="{{ $lbUrl }}" id="url-leaderboard" />
                            </div>
                            <div class="obs-url-actions">
                                <button class="obs-btn primary" onclick="copyText('{{ $lbUrl }}', 'URL Leaderboard')">
                                    <span class="iconify" data-icon="solar:copy-bold-duotone"></span>Copy URL
                                </button>
                                <a class="obs-btn" href="{{ $lbUrl }}" target="_blank">
                                    <span class="iconify" data-icon="solar:eye-bold-duotone"></span>Buka
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Phase 2: Customizer — Leaderboard ── --}}
                @php $ws_lb = $widgetSettings['leaderboard']; @endphp
                <div class="ws-card">
                    <div class="ws-card-head">
                        <div class="ws-card-icon" style="background:rgba(124,108,252,.1);border:1px solid rgba(124,108,252,.2)">
                            <span class="iconify" data-icon="solar:palette-bold-duotone" style="color:var(--brand-light)"></span>
                        </div>
                        <div>
                            <div class="ws-card-title">Kustomisasi Tampilan</div>
                            <div class="ws-card-sub">Pilih preset atau atur warna custom untuk Leaderboard widget</div>
                        </div>
                    </div>

                    <div class="ws-section-label">
                        <span class="iconify" data-icon="solar:magic-stick-bold" style="width:12px;height:12px"></span>
                        Preset Tema
                    </div>

                    <div class="wc-presets" id="leaderboard-presets">
                        <div class="wc-preset-card {{ $ws_lb['preset']==='default'?'active':'' }}" data-preset="default" onclick="wcSelectPreset('leaderboard','default',this)">
                            <div class="wc-preset-swatch" style="background:linear-gradient(135deg,rgba(8,8,12,.96),#1a1a2e);border-color:rgba(124,108,252,.2)"></div>
                            <div class="wc-preset-name">Default</div>
                            <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                        </div>
                        <div class="wc-preset-card {{ $ws_lb['preset']==='neon'?'active':'' }}" data-preset="neon" onclick="wcSelectPreset('leaderboard','neon',this)">
                            <div class="wc-preset-swatch" style="background:linear-gradient(135deg,rgba(2,4,18,.97),#001a14);border-color:rgba(0,255,200,.22)"></div>
                            <div class="wc-preset-name">Neon</div>
                            <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                        </div>
                         <div class="wc-preset-card {{ $ws_lb['preset']==='fire'?'active':'' }}" data-preset="fire" onclick="wcSelectPreset('leaderboard','fire',this)">
                             <div class="wc-preset-swatch" style="background:linear-gradient(135deg,rgba(10,4,2,.97),#2a0a00);border-color:rgba(249,115,22,.22)"></div>
                             <div class="wc-preset-name">Fire</div>
                             <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                         </div>
                         <div class="wc-preset-card {{ $ws_lb['preset']==='ice'?'active':'' }}" data-preset="ice" onclick="wcSelectPreset('leaderboard','ice',this)">
                             <div class="wc-preset-swatch" style="background:linear-gradient(135deg,rgba(2,8,22,.96),#001830);border-color:rgba(147,210,255,.18)"></div>
                             <div class="wc-preset-name">Ice</div>
                             <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                         </div>
                         <div class="wc-preset-card {{ $ws_lb['preset']==='minimal'?'active':'' }}" data-preset="minimal" onclick="wcSelectPreset('leaderboard','minimal',this)">
                             <div class="wc-preset-swatch" style="background:linear-gradient(135deg,rgba(12,12,16,.95),#1a1a1e);border-color:rgba(255,255,255,.14)"></div>
                             <div class="wc-preset-name">Minimal</div>
                             <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                         </div>
                         <div class="wc-preset-card {{ $ws_lb['preset']==='custom'?'active':'' }}" data-preset="custom" onclick="wcSelectPreset('leaderboard','custom',this)">
                            <div class="wc-preset-swatch" style="background:linear-gradient(135deg,var(--brand),var(--purple));border-color:rgba(124,108,252,.3)"></div>
                            <div class="wc-preset-name">Custom</div>
                            <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                        </div>
                    </div>

                    @php
                        preg_match('/rgba\((\d+),\s*(\d+),\s*(\d+),\s*([\d.]+)\)/', $ws_lb['surface'], $lbSM);
                        $lbSurfaceHex = isset($lbSM[1]) ? sprintf('#%02x%02x%02x',$lbSM[1],$lbSM[2],$lbSM[3]) : (preg_match('/^#[0-9a-fA-F]{6}/',$ws_lb['surface'],$hM)?$hM[0]:'#08080c');
                        $lbSurfaceOpacity = isset($lbSM[4]) ? round($lbSM[4]*100) : 96;

                        preg_match('/rgba\((\d+),\s*(\d+),\s*(\d+),\s*([\d.]+)\)/', $ws_lb['border'], $lbBM);
                        $lbBorderHex = isset($lbBM[1]) ? sprintf('#%02x%02x%02x',$lbBM[1],$lbBM[2],$lbBM[3]) : (preg_match('/^#[0-9a-fA-F]{6}/',$ws_lb['border'],$hM)?$hM[0]:'#7c6cfc');
                        $lbBorderOpacity = isset($lbBM[4]) ? round($lbBM[4]*100) : 20;
                    @endphp
                    <div class="wc-custom-panel {{ $ws_lb['preset']==='custom'?'visible':'' }}" id="leaderboard-custom">
                        <div class="ws-section-label" style="margin-bottom:4px">
                            <span class="iconify" data-icon="solar:pen-bold" style="width:12px;height:12px"></span>
                            Warna &amp; Gaya Custom
                        </div>

                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:square-bold"></span>Surface</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-color-wrap">
                                    <div class="wc-color-swatch" id="leaderboard-swatch-surface" style="background:{{ $ws_lb['surface'] }}"></div>
                                    <input type="color" class="wc-color-input" id="leaderboard-color-surface" value="{{ $lbSurfaceHex }}" oninput="wcRgbaColorChange('leaderboard','surface',this)">
                                </div>
                                <div class="wc-opacity-wrap">
                                    <span class="wc-opacity-label">Opacity</span>
                                    <input type="range" class="wc-slider" min="0" max="100" value="{{ $lbSurfaceOpacity }}" id="leaderboard-opacity-surface" oninput="wcOpacityChange('leaderboard','surface',this)">
                                    <span class="wc-opacity-val" id="leaderboard-opacityval-surface">{{ $lbSurfaceOpacity }}%</span>
                                </div>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:border-bold"></span>Border</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-color-wrap">
                                    <div class="wc-color-swatch" id="leaderboard-swatch-border" style="background:{{ $ws_lb['border'] }}"></div>
                                    <input type="color" class="wc-color-input" id="leaderboard-color-border" value="{{ $lbBorderHex }}" oninput="wcRgbaColorChange('leaderboard','border',this)">
                                </div>
                                <div class="wc-opacity-wrap">
                                    <span class="wc-opacity-label">Opacity</span>
                                    <input type="range" class="wc-slider" min="0" max="100" value="{{ $lbBorderOpacity }}" id="leaderboard-opacity-border" oninput="wcOpacityChange('leaderboard','border',this)">
                                    <span class="wc-opacity-val" id="leaderboard-opacityval-border">{{ $lbBorderOpacity }}%</span>
                                </div>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:star-bold"></span>Brand</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-color-wrap">
                                    <div class="wc-color-swatch" id="leaderboard-swatch-brand" style="background:{{ $ws_lb['brand'] }}"></div>
                                    <input type="color" class="wc-color-input" id="leaderboard-color-brand" value="{{ $ws_lb['brand'] }}" oninput="wcColorChange('leaderboard','brand',this)">
                                </div>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:star-shine-bold"></span>Brand 2</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-color-wrap">
                                    <div class="wc-color-swatch" id="leaderboard-swatch-brand2" style="background:{{ $ws_lb['brand2'] }}"></div>
                                    <input type="color" class="wc-color-input" id="leaderboard-color-brand2" value="{{ $ws_lb['brand2'] }}" oninput="wcColorChange('leaderboard','brand2',this)">
                                </div>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:medal-ribbons-star-bold"></span>Warna Top 1</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-color-wrap">
                                    <div class="wc-color-swatch" id="leaderboard-swatch-yellow" style="background:{{ $ws_lb['yellow'] }}"></div>
                                    <input type="color" class="wc-color-input" id="leaderboard-color-yellow" value="{{ $ws_lb['yellow'] }}" oninput="wcColorChange('leaderboard','yellow',this)">
                                </div>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:check-circle-bold"></span>Warna Aksen</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-color-wrap">
                                    <div class="wc-color-swatch" id="leaderboard-swatch-green" style="background:{{ $ws_lb['green'] }}"></div>
                                    <input type="color" class="wc-color-input" id="leaderboard-color-green" value="{{ $ws_lb['green'] }}" oninput="wcColorChange('leaderboard','green',this)">
                                </div>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:rounded-bold"></span>Border Radius</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-slider-wrap">
                                    <input type="range" class="wc-slider" min="0" max="32" value="{{ $ws_lb['radius'] }}" id="leaderboard-slider-radius" oninput="wcSliderChange('leaderboard','radius',this)">
                                    <span class="wc-slider-val" id="leaderboard-sliderval-radius">{{ $ws_lb['radius'] }}px</span>
                                </div>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:ruler-cross-pen-bold"></span>Lebar Widget</div>
                            <div class="wc-row-ctrl">
                                <input type="number" class="wc-number" min="200" max="500" value="{{ $ws_lb['width'] }}" id="leaderboard-num-width" oninput="wcNumChange('leaderboard','width',this)">
                                <span class="wc-unit">px</span>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:map-point-bold"></span>Posisi</div>
                            <div class="wc-row-ctrl">
                                <select class="wc-select" id="leaderboard-sel-position" onchange="wcSelectChange('leaderboard','position',this)">
                                    <option value="top-left"     {{ $ws_lb['position']==='top-left'     ?'selected':'' }}>Atas Kiri</option>
                                    <option value="top-right"    {{ $ws_lb['position']==='top-right'    ?'selected':'' }}>Atas Kanan</option>
                                    <option value="bottom-left"  {{ $ws_lb['position']==='bottom-left'  ?'selected':'' }}>Bawah Kiri</option>
                                    <option value="bottom-right" {{ $ws_lb['position']==='bottom-right' ?'selected':'' }}>Bawah Kanan</option>
                                </select>
                            </div>
                        </div>
                    </div>{{-- /leaderboard-custom --}}

                    <div class="wc-save-row">
                        <button class="wc-save-btn" id="leaderboard-save-btn" onclick="wcSave('leaderboard')">
                            <span class="iconify" data-icon="solar:floppy-disk-bold"></span>
                            Simpan Tampilan
                        </button>
                        <button class="wc-reset-link" onclick="wcReset('leaderboard')">Reset ke default</button>
                    </div>
                </div>{{-- /customizer leaderboard --}}

                {{-- ── Phase 3: Live Preview — Leaderboard ── --}}
                <div class="ws-card wc-preview-card">
                    <div class="ws-card-head">
                        <div class="ws-card-icon" style="background:rgba(34,211,160,.1);border:1px solid rgba(34,211,160,.2)">
                            <span class="iconify" data-icon="solar:eye-bold-duotone" style="color:var(--green)"></span>
                        </div>
                        <div>
                            <div class="ws-card-title">Live Preview</div>
                            <div class="ws-card-sub">Tampilan Leaderboard sesuai pengaturan kamu</div>
                        </div>
                    </div>
                    <div class="wc-preview-viewport" id="preview-leaderboard-viewport">
                        <span class="wc-preview-label">
                            <span class="wc-preview-dot"></span>PREVIEW
                        </span>
                        <div class="wc-preview-frame" style="width:100%;height:200px">
                            <div class="wc-preview-stage" id="preview-leaderboard-stage" style="transform:scale(.64);transform-origin:top center;width:280px">
                                <div class="wc-prev-leaderboard" id="preview-leaderboard">
                                    <div class="lb-mock-wrap">
                                        <div class="lb-mock-topline"></div>
                                        <div class="lb-mock-header">
                                            <div class="lb-mock-badge">● LIVE</div>
                                            <div class="lb-mock-title">Top Donatur</div>
                                        </div>
                                        <div class="lb-mock-list">
                                            <div class="lb-mock-item rank-1">
                                                <div class="lb-mock-rank">🥇</div>
                                                <div class="lb-mock-avatar">😎</div>
                                                <div class="lb-mock-info">
                                                    <div class="lb-mock-name">Budi Santoso</div>
                                                    <div class="lb-mock-amount">Rp 250.000</div>
                                                </div>
                                            </div>
                                            <div class="lb-mock-item">
                                                <div class="lb-mock-rank">🥈</div>
                                                <div class="lb-mock-avatar">🎮</div>
                                                <div class="lb-mock-info">
                                                    <div class="lb-mock-name">Andi Wijaya</div>
                                                    <div class="lb-mock-amount">Rp 150.000</div>
                                                </div>
                                            </div>
                                            <div class="lb-mock-item">
                                                <div class="lb-mock-rank">🥉</div>
                                                <div class="lb-mock-avatar">🚀</div>
                                                <div class="lb-mock-info">
                                                    <div class="lb-mock-name">Siti Rahma</div>
                                                    <div class="lb-mock-amount">Rp 75.000</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>{{-- /wc-preview-frame --}}
                    </div>
                </div>

            </div>{{-- /tab-leaderboard --}}

            {{-- ══ TAB: Barcode / QR ══ --}}
            <div class="ws-panel" id="tab-qr">

                <div class="ws-card">
                    <div class="ws-card-head">
                        <div class="ws-card-icon" style="background:rgba(34,211,160,.1);border:1px solid rgba(34,211,160,.2)">
                            <span class="iconify" data-icon="solar:qr-code-bold-duotone" style="color:var(--green)"></span>
                        </div>
                        <div>
                            <div class="ws-card-title">Barcode / QR Widget</div>
                            <div class="ws-card-sub">QR code link donasi untuk ditampilkan di OBS</div>
                        </div>
                        <div class="ws-card-head-right">
                            <span class="ws-tag green">
                                <span class="iconify" data-icon="solar:qr-code-bold" style="width:11px;height:11px"></span>
                                Static Widget
                            </span>
                        </div>
                    </div>

                    <div class="ws-tags">
                        <span class="ws-tag green">
                            <span class="iconify" data-icon="solar:qr-code-bold"></span>
                            QR Code
                        </span>
                        <span class="ws-tag blue">
                            <span class="iconify" data-icon="solar:smartphone-bold"></span>
                            Scan to Donate
                        </span>
                        <span class="ws-tag orange">
                            <span class="iconify" data-icon="solar:link-bold"></span>
                            {{ url('/' . $streamer->slug) }}
                        </span>
                    </div>

                    <div class="ws-size-chips">
                        <span class="ws-size-chip">
                            <span class="iconify" data-icon="solar:ruler-bold"></span>
                            Default 260 × 320 px
                        </span>
                        <span class="ws-size-chip">
                            <span class="iconify" data-icon="solar:monitor-bold"></span>
                            Canvas 1920 × 1080
                        </span>
                    </div>

                    <div class="ws-info-box">
                        <span class="iconify" data-icon="solar:info-circle-bold-duotone"></span>
                        <span>QR code ini mengarah ke halaman donasi publik kamu: <strong>{{ url('/' . $streamer->slug) }}</strong>. Widget bersifat statis — tidak membutuhkan koneksi SSE.</span>
                    </div>

                    <div class="ws-section-label">
                        <span class="iconify" data-icon="solar:link-bold" style="width:12px;height:12px"></span>
                        URL Browser Source OBS
                    </div>

                    <div class="obs-url-grid">
                        {{-- QR Widget OBS --}}
                        <div class="obs-url-row">
                            <div class="obs-url-icon" style="background:rgba(34,211,160,.1);border:1px solid rgba(34,211,160,.2)">
                                <span class="iconify" data-icon="solar:qr-code-bold-duotone" style="color:var(--green)"></span>
                            </div>
                            <div class="obs-url-info">
                                <div class="obs-url-label">
                                    QR Widget OBS (dengan frame)
                                    <code style="font-size:9px;background:rgba(255,255,255,.06);padding:1px 5px;border-radius:4px">?key=</code>
                                    sudah termasuk
                                </div>
                                @php $qrObsUrl = route('obs.qr', $streamer->slug) . '?key=' . $streamer->api_key; @endphp
                                <input class="obs-url-input" readonly value="{{ $qrObsUrl }}" id="url-qr-obs" />
                            </div>
                            <div class="obs-url-actions">
                                <button class="obs-btn primary" onclick="copyText('{{ $qrObsUrl }}', 'URL QR OBS')">
                                    <span class="iconify" data-icon="solar:copy-bold-duotone"></span>Copy URL
                                </button>
                                <a class="obs-btn" href="{{ $qrObsUrl }}" target="_blank">
                                    <span class="iconify" data-icon="solar:eye-bold-duotone"></span>Buka
                                </a>
                            </div>
                        </div>

                        {{-- QR Bare SVG --}}
                        <div class="obs-url-row">
                            <div class="obs-url-icon" style="background:rgba(34,211,160,.06);border:1px solid rgba(34,211,160,.15)">
                                <span class="iconify" data-icon="solar:code-bold-duotone" style="color:var(--green);opacity:.7"></span>
                            </div>
                            <div class="obs-url-info">
                                <div class="obs-url-label">
                                    QR SVG Bare (tanpa frame)
                                </div>
                                @php $qrSvgUrl = route('qr.show', $streamer->slug); @endphp
                                <input class="obs-url-input" readonly value="{{ $qrSvgUrl }}" id="url-qr-svg" />
                            </div>
                            <div class="obs-url-actions">
                                <button class="obs-btn" onclick="copyText('{{ $qrSvgUrl }}', 'URL QR SVG')">
                                    <span class="iconify" data-icon="solar:copy-bold-duotone"></span>Copy URL
                                </button>
                                <a class="obs-btn" href="{{ $qrSvgUrl }}" target="_blank">
                                    <span class="iconify" data-icon="solar:eye-bold-duotone"></span>Buka
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Phase 2: Customizer — QR ── --}}
                @php $ws_qr = $widgetSettings['qr']; @endphp
                <div class="ws-card">
                    <div class="ws-card-head">
                        <div class="ws-card-icon" style="background:rgba(124,108,252,.1);border:1px solid rgba(124,108,252,.2)">
                            <span class="iconify" data-icon="solar:palette-bold-duotone" style="color:var(--brand-light)"></span>
                        </div>
                        <div>
                            <div class="ws-card-title">Kustomisasi Tampilan</div>
                            <div class="ws-card-sub">Pilih preset atau atur warna custom untuk QR widget</div>
                        </div>
                    </div>

                    <div class="ws-section-label">
                        <span class="iconify" data-icon="solar:magic-stick-bold" style="width:12px;height:12px"></span>
                        Preset Tema
                    </div>

                    <div class="wc-presets" id="qr-presets">
                        <div class="wc-preset-card {{ $ws_qr['preset']==='default'?'active':'' }}" data-preset="default" onclick="wcSelectPreset('qr','default',this)">
                            <div class="wc-preset-swatch" style="background:linear-gradient(135deg,rgba(10,10,16,.93),#1a1a2e);border-color:rgba(124,108,252,.28)"></div>
                            <div class="wc-preset-name">Default</div>
                            <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                        </div>
                        <div class="wc-preset-card {{ $ws_qr['preset']==='neon'?'active':'' }}" data-preset="neon" onclick="wcSelectPreset('qr','neon',this)">
                            <div class="wc-preset-swatch" style="background:linear-gradient(135deg,rgba(2,4,18,.97),#001a14);border-color:rgba(0,255,200,.22)"></div>
                            <div class="wc-preset-name">Neon</div>
                            <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                        </div>
                         <div class="wc-preset-card {{ $ws_qr['preset']==='fire'?'active':'' }}" data-preset="fire" onclick="wcSelectPreset('qr','fire',this)">
                             <div class="wc-preset-swatch" style="background:linear-gradient(135deg,rgba(10,4,2,.97),#2a0a00);border-color:rgba(249,115,22,.22)"></div>
                             <div class="wc-preset-name">Fire</div>
                             <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                         </div>
                         <div class="wc-preset-card {{ $ws_qr['preset']==='ice'?'active':'' }}" data-preset="ice" onclick="wcSelectPreset('qr','ice',this)">
                             <div class="wc-preset-swatch" style="background:linear-gradient(135deg,rgba(2,8,22,.96),#001830);border-color:rgba(147,210,255,.18)"></div>
                             <div class="wc-preset-name">Ice</div>
                             <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                         </div>
                         <div class="wc-preset-card {{ $ws_qr['preset']==='minimal'?'active':'' }}" data-preset="minimal" onclick="wcSelectPreset('qr','minimal',this)">
                             <div class="wc-preset-swatch" style="background:linear-gradient(135deg,rgba(12,12,16,.95),#1a1a1e);border-color:rgba(255,255,255,.14)"></div>
                             <div class="wc-preset-name">Minimal</div>
                             <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                         </div>
                         <div class="wc-preset-card {{ $ws_qr['preset']==='custom'?'active':'' }}" data-preset="custom" onclick="wcSelectPreset('qr','custom',this)">
                            <div class="wc-preset-swatch" style="background:linear-gradient(135deg,var(--brand),var(--purple));border-color:rgba(124,108,252,.3)"></div>
                            <div class="wc-preset-name">Custom</div>
                            <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                        </div>
                    </div>

                    @php
                        preg_match('/rgba\((\d+),\s*(\d+),\s*(\d+),\s*([\d.]+)\)/', $ws_qr['surface'], $qrSM);
                        $qrSurfaceHex = isset($qrSM[1]) ? sprintf('#%02x%02x%02x',$qrSM[1],$qrSM[2],$qrSM[3]) : (preg_match('/^#[0-9a-fA-F]{6}/',$ws_qr['surface'],$hM)?$hM[0]:'#0a0a10');
                        $qrSurfaceOpacity = isset($qrSM[4]) ? round($qrSM[4]*100) : 93;

                        preg_match('/rgba\((\d+),\s*(\d+),\s*(\d+),\s*([\d.]+)\)/', $ws_qr['border'], $qrBM);
                        $qrBorderHex = isset($qrBM[1]) ? sprintf('#%02x%02x%02x',$qrBM[1],$qrBM[2],$qrBM[3]) : (preg_match('/^#[0-9a-fA-F]{6}/',$ws_qr['border'],$hM)?$hM[0]:'#7c6cfc');
                        $qrBorderOpacity = isset($qrBM[4]) ? round($qrBM[4]*100) : 28;
                    @endphp
                    <div class="wc-custom-panel {{ $ws_qr['preset']==='custom'?'visible':'' }}" id="qr-custom">
                        <div class="ws-section-label" style="margin-bottom:4px">
                            <span class="iconify" data-icon="solar:pen-bold" style="width:12px;height:12px"></span>
                            Warna &amp; Gaya Custom
                        </div>

                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:square-bold"></span>Surface</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-color-wrap">
                                    <div class="wc-color-swatch" id="qr-swatch-surface" style="background:{{ $ws_qr['surface'] }}"></div>
                                    <input type="color" class="wc-color-input" id="qr-color-surface" value="{{ $qrSurfaceHex }}" oninput="wcRgbaColorChange('qr','surface',this)">
                                </div>
                                <div class="wc-opacity-wrap">
                                    <span class="wc-opacity-label">Opacity</span>
                                    <input type="range" class="wc-slider" min="0" max="100" value="{{ $qrSurfaceOpacity }}" id="qr-opacity-surface" oninput="wcOpacityChange('qr','surface',this)">
                                    <span class="wc-opacity-val" id="qr-opacityval-surface">{{ $qrSurfaceOpacity }}%</span>
                                </div>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:border-bold"></span>Border</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-color-wrap">
                                    <div class="wc-color-swatch" id="qr-swatch-border" style="background:{{ $ws_qr['border'] }}"></div>
                                    <input type="color" class="wc-color-input" id="qr-color-border" value="{{ $qrBorderHex }}" oninput="wcRgbaColorChange('qr','border',this)">
                                </div>
                                <div class="wc-opacity-wrap">
                                    <span class="wc-opacity-label">Opacity</span>
                                    <input type="range" class="wc-slider" min="0" max="100" value="{{ $qrBorderOpacity }}" id="qr-opacity-border" oninput="wcOpacityChange('qr','border',this)">
                                    <span class="wc-opacity-val" id="qr-opacityval-border">{{ $qrBorderOpacity }}%</span>
                                </div>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:star-bold"></span>Brand</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-color-wrap">
                                    <div class="wc-color-swatch" id="qr-swatch-brand" style="background:{{ $ws_qr['brand'] }}"></div>
                                    <input type="color" class="wc-color-input" id="qr-color-brand" value="{{ $ws_qr['brand'] }}" oninput="wcColorChange('qr','brand',this)">
                                </div>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:star-shine-bold"></span>Brand 2</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-color-wrap">
                                    <div class="wc-color-swatch" id="qr-swatch-brand2" style="background:{{ $ws_qr['brand2'] }}"></div>
                                    <input type="color" class="wc-color-input" id="qr-color-brand2" value="{{ $ws_qr['brand2'] }}" oninput="wcColorChange('qr','brand2',this)">
                                </div>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:rounded-bold"></span>Border Radius</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-slider-wrap">
                                    <input type="range" class="wc-slider" min="0" max="36" value="{{ $ws_qr['radius'] }}" id="qr-slider-radius" oninput="wcSliderChange('qr','radius',this)">
                                    <span class="wc-slider-val" id="qr-sliderval-radius">{{ $ws_qr['radius'] }}px</span>
                                </div>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:ruler-cross-pen-bold"></span>Lebar Widget</div>
                            <div class="wc-row-ctrl">
                                <input type="number" class="wc-number" min="160" max="400" value="{{ $ws_qr['width'] }}" id="qr-num-width" oninput="wcNumChange('qr','width',this)">
                                <span class="wc-unit">px</span>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:map-point-bold"></span>Posisi</div>
                            <div class="wc-row-ctrl">
                                <select class="wc-select" id="qr-sel-position" onchange="wcSelectChange('qr','position',this)">
                                    <option value="top-left"     {{ $ws_qr['position']==='top-left'     ?'selected':'' }}>Atas Kiri</option>
                                    <option value="top-right"    {{ $ws_qr['position']==='top-right'    ?'selected':'' }}>Atas Kanan</option>
                                    <option value="bottom-left"  {{ $ws_qr['position']==='bottom-left'  ?'selected':'' }}>Bawah Kiri</option>
                                    <option value="bottom-right" {{ $ws_qr['position']==='bottom-right' ?'selected':'' }}>Bawah Kanan</option>
                                </select>
                            </div>
                        </div>
                    </div>{{-- /qr-custom --}}

                    <div class="wc-save-row">
                        <button class="wc-save-btn" id="qr-save-btn" onclick="wcSave('qr')">
                            <span class="iconify" data-icon="solar:floppy-disk-bold"></span>
                            Simpan Tampilan
                        </button>
                        <button class="wc-reset-link" onclick="wcReset('qr')">Reset ke default</button>
                    </div>
                </div>{{-- /customizer qr --}}

                {{-- ── Phase 3: Live Preview — QR ── --}}
                <div class="ws-card wc-preview-card">
                    <div class="ws-card-head">
                        <div class="ws-card-icon" style="background:rgba(34,211,160,.1);border:1px solid rgba(34,211,160,.2)">
                            <span class="iconify" data-icon="solar:eye-bold-duotone" style="color:var(--green)"></span>
                        </div>
                        <div>
                            <div class="ws-card-title">Live Preview</div>
                            <div class="ws-card-sub">Tampilan QR widget sesuai pengaturan kamu</div>
                        </div>
                    </div>
                    <div class="wc-preview-viewport" id="preview-qr-viewport">
                        <span class="wc-preview-label">
                            <span class="wc-preview-dot"></span>PREVIEW
                        </span>
                        <div class="wc-preview-frame" style="width:100%;height:235px">
                            <div class="wc-preview-stage" id="preview-qr-stage" style="transform:scale(.70);transform-origin:top center;width:220px">
                                <div class="wc-prev-qr" id="preview-qr">
                                    <div class="qr-mock-widget">
                                        <div class="qr-mock-topline"></div>
                                        <div class="qr-mock-header">
                                            <div class="qr-mock-logo">SD</div>
                                            <div class="qr-mock-name">{{ $streamer->display_name }}</div>
                                        </div>
                                        <div class="qr-mock-img">
                                            <img src="{{ route('qr.show', $streamer->slug) }}" alt="QR Code" style="width:100%;height:100%;display:block;object-fit:contain">
                                        </div>
                                        <div class="qr-mock-footer">
                                            <span class="qr-mock-dot"></span>
                                            <span class="qr-mock-scan">SCAN TO DONATE</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>{{-- /wc-preview-frame --}}
                    </div>
                </div>

            </div>{{-- /tab-qr --}}

            {{-- ══ TAB: Subathon ══ --}}
            <div class="ws-panel" id="tab-subathon">

                <div class="ws-card">
                    <div class="ws-card-head">
                        <div class="ws-card-icon" style="background:rgba(124,108,252,.1);border:1px solid rgba(124,108,252,.2)">
                            <span class="iconify" data-icon="solar:timer-bold-duotone" style="color:var(--brand-light)"></span>
                        </div>
                        <div>
                            <div class="ws-card-title">Subathon Timer</div>
                            <div class="ws-card-sub">Timer countdown yang bertambah saat ada donasi</div>
                        </div>
                        <div class="ws-card-head-right">
                            <div class="status-live">
                                <span class="status-live-dot"></span>
                                Real-time via SSE
                            </div>
                        </div>
                    </div>

                    <div class="ws-tags">
                        <span class="ws-tag orange">
                            <span class="iconify" data-icon="solar:timer-bold"></span>
                            Countdown Timer
                        </span>
                        <span class="ws-tag green">
                            <span class="iconify" data-icon="solar:refresh-bold"></span>
                            Auto Update
                        </span>
                        <span class="ws-tag blue">
                            <span class="iconify" data-icon="solar:money-bold"></span>
                            Tambah via Donasi
                        </span>
                    </div>

                    <div class="ws-size-chips">
                        <span class="ws-size-chip">
                            <span class="iconify" data-icon="solar:ruler-bold"></span>
                            Default 320 × 150 px
                        </span>
                        <span class="ws-size-chip">
                            <span class="iconify" data-icon="solar:monitor-bold"></span>
                            Canvas 1920 × 1080
                        </span>
                    </div>

                    <div class="ws-info-box">
                        <span class="iconify" data-icon="solar:info-circle-bold-duotone"></span>
                        <span>Widget ini tampil permanen di layar. Ubah pengaturan Subathon di menu <a href="{{ route('streamer.settings') }}#subathon" style="color:var(--brand-light)">Settings → Subathon</a>.</span>
                    </div>

                    <div class="ws-section-label">
                        <span class="iconify" data-icon="solar:link-bold" style="width:12px;height:12px"></span>
                        URL Browser Source OBS
                    </div>

                    <div class="obs-url-grid">
                        <div class="obs-url-row">
                            <div class="obs-url-icon" style="background:rgba(124,108,252,.1);border:1px solid rgba(124,108,252,.2)">
                                <span class="iconify" data-icon="solar:timer-bold-duotone" style="color:var(--brand-light)"></span>
                            </div>
                            <div class="obs-url-info">
                                <div class="obs-url-label">
                                    Subathon Timer
                                    <code style="font-size:9px;background:rgba(255,255,255,.06);padding:1px 5px;border-radius:4px">?key=</code>
                                    sudah termasuk
                                </div>
                                @php $subUrl = route('obs.subathon', $streamer->slug) . '?key=' . $streamer->api_key; @endphp
                                <input class="obs-url-input" readonly value="{{ $subUrl }}" id="url-subathon" />
                            </div>
                            <div class="obs-url-actions">
                                <button class="obs-btn primary" onclick="copyText('{{ $subUrl }}', 'URL Subathon')">
                                    <span class="iconify" data-icon="solar:copy-bold-duotone"></span>Copy URL
                                </button>
                                <a class="obs-btn" href="{{ $subUrl }}" target="_blank">
                                    <span class="iconify" data-icon="solar:eye-bold-duotone"></span>Buka
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Live Preview Subathon ── --}}
                <div class="ws-card wc-preview-card">
                    <div class="ws-card-head">
                        <div class="ws-card-icon" style="background:rgba(34,211,160,.1);border:1px solid rgba(34,211,160,.2)">
                            <span class="iconify" data-icon="solar:eye-bold-duotone" style="color:var(--green)"></span>
                        </div>
                        <div>
                            <div class="ws-card-title">Live Preview</div>
                            <div class="ws-card-sub">Tampilan Subathon sesuai pengaturan kamu</div>
                        </div>
                    </div>
                    <div class="wc-preview-viewport" id="preview-subathon-viewport">
                        <span class="wc-preview-label">
                            <span class="wc-preview-dot"></span>PREVIEW
                        </span>
                        <div class="wc-preview-frame" style="width:100%;height:160px">
                            <div class="wc-preview-stage" id="preview-subathon-stage" style="transform:scale(.5);transform-origin:top center;width:320px">
                                <div class="wc-prev-subathon subathon-mock-wrap">
                                    <div class="subathon-mock-timer">00:45:00</div>
                                    <div class="subathon-mock-label">SISA WAKTU</div>
                                    <div class="subathon-mock-bar">
                                        <div class="subathon-mock-bar-fill"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ── Pengaturan Subathon ── --}}
                <div class="ws-card">
                    <div class="ws-card-head">
                        <div class="ws-card-icon" style="background:rgba(124,108,252,.1);border:1px solid rgba(124,108,252,.2)">
                            <span class="iconify" data-icon="solar:settings-bold-duotone" style="color:var(--brand-light)"></span>
                        </div>
                        <div>
                            <div class="ws-card-title">Pengaturan Subathon</div>
                            <div class="ws-card-sub">Aktifkan, durasi, dan konversi donasi</div>
                        </div>
                    </div>

                    <div class="ws-opt-row">
                        <div class="ws-opt-label">
                            <div>Aktifkan Subathon</div>
                            <div class="ws-opt-sub">Timer akan muncul di widget OBS</div>
                        </div>
                        <label class="ws-toggle">
                            <input type="checkbox" id="subathon-enabled" {{ $streamer->subathon_enabled ? 'checked' : '' }}>
                            <span class="ws-toggle-slider"></span>
                        </label>
                    </div>

                    <div class="ws-opt-row">
                        <div class="ws-opt-label">
                            <div>Durasi Default</div>
                            <div class="ws-opt-sub">Timer dimulai dengan durasi ini saat di-reset</div>
                        </div>
                        <div class="ws-opt-input">
                            <input type="number" id="subathon-duration" value="{{ $streamer->subathon_duration_minutes ?? 60 }}" min="1" max="1440" style="width:100px">
                            <span class="ws-opt-unit">menit</span>
                        </div>
                    </div>

                    <div class="ws-opt-row" style="align-items:flex-start;padding-top:16px;border-top:1px solid var(--border);margin-top:8px">
                        <div class="ws-opt-label">
                            <div>Konversi Donasi → Waktu</div>
                            <div class="ws-opt-sub">Tentukan menit ditambahkan per nominal</div>
                        </div>
                    </div>

                    <div class="ws-tier-rows" id="subathon-tiers">
                        @php
                            $subathonValues = $streamer->subathon_additional_values ?? [['from' => 0, 'minutes' => 1], ['from' => 10000, 'minutes' => 2], ['from' => 50000, 'minutes' => 5], ['from' => 100000, 'minutes' => 10], ['from' => 500000, 'minutes' => 30]];
                        @endphp
                        @foreach($subathonValues as $i => $v)
                        <div class="ws-tier-row">
                            <span>Donasi Rp</span>
                            <input type="number" class="ws-tier-from" value="{{ $v['from'] }}" min="0" step="1000" data-idx="{{ $i }}">
                            <span>→ Tambah</span>
                            <input type="number" class="ws-tier-to" value="{{ $v['minutes'] }}" min="1" max="60" data-idx="{{ $i }}">
                            <span>menit</span>
                            @if($i > 0)
                            <button type="button" class="ws-tier-remove" onclick="removeSubathonTier(this)">
                                <span class="iconify" data-icon="solar:trash-bold"></span>
                            </button>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="ws-tier-add" onclick="addSubathonTier()">
                        <span class="iconify" data-icon="solar:plus-bold"></span>
                        Tambah Rule
                    </button>

                    <div class="ws-save-row" style="margin-top:20px">
                        <button class="ws-save-btn" onclick="saveSubathonSettings()">
                            <span class="iconify" data-icon="solar:floppy-disk-bold"></span>
                            Simpan Pengaturan
                        </button>
                        <span class="ws-save-msg" id="msg-subathon"></span>
                    </div>
                </div>

                {{-- ── Phase 2: Customizer — Subathon ── --}}
                @php $ws_sub = $widgetSettings['subathon'] ?? []; @endphp
                <div class="ws-card">
                    <div class="ws-card-head">
                        <div class="ws-card-icon" style="background:rgba(124,108,252,.1);border:1px solid rgba(124,108,252,.2)">
                            <span class="iconify" data-icon="solar:palette-bold-duotone" style="color:var(--brand-light)"></span>
                        </div>
                        <div>
                            <div class="ws-card-title">Kustomisasi Tampilan</div>
                            <div class="ws-card-sub">Pilih preset atau atur warna custom untuk Subathon widget</div>
                        </div>
                    </div>

                    <div class="ws-section-label">
                        <span class="iconify" data-icon="solar:magic-stick-bold" style="width:12px;height:12px"></span>
                        Preset Tema
                    </div>

                    <div class="wc-presets" id="subathon-presets">
                        <div class="wc-preset-card {{ ($ws_sub['preset'] ?? 'default') === 'default' ? 'active' : '' }}" data-preset="default" onclick="wcSelectPreset('subathon','default',this)">
                            <div class="wc-preset-swatch" style="background:linear-gradient(135deg,rgba(8,8,12,.96),#1a1a2e);border-color:rgba(124,108,252,.2)"></div>
                            <div class="wc-preset-name">Default</div>
                            <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                        </div>
                        <div class="wc-preset-card {{ ($ws_sub['preset'] ?? '') === 'neon' ? 'active' : '' }}" data-preset="neon" onclick="wcSelectPreset('subathon','neon',this)">
                            <div class="wc-preset-swatch" style="background:linear-gradient(135deg,rgba(2,4,18,.97),#001a14);border-color:rgba(0,255,200,.22)"></div>
                            <div class="wc-preset-name">Neon</div>
                            <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                        </div>
                        <div class="wc-preset-card {{ ($ws_sub['preset'] ?? '') === 'fire' ? 'active' : '' }}" data-preset="fire" onclick="wcSelectPreset('subathon','fire',this)">
                            <div class="wc-preset-swatch" style="background:linear-gradient(135deg,rgba(10,4,2,.97),#2a0a00);border-color:rgba(249,115,22,.22)"></div>
                            <div class="wc-preset-name">Fire</div>
                            <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                        </div>
                        <div class="wc-preset-card {{ ($ws_sub['preset'] ?? '') === 'ice' ? 'active' : '' }}" data-preset="ice" onclick="wcSelectPreset('subathon','ice',this)">
                            <div class="wc-preset-swatch" style="background:linear-gradient(135deg,rgba(2,8,22,.96),#001830);border-color:rgba(147,210,255,.18)"></div>
                            <div class="wc-preset-name">Ice</div>
                            <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                        </div>
                        <div class="wc-preset-card {{ ($ws_sub['preset'] ?? '') === 'minimal' ? 'active' : '' }}" data-preset="minimal" onclick="wcSelectPreset('subathon','minimal',this)">
                            <div class="wc-preset-swatch" style="background:linear-gradient(135deg,rgba(12,12,16,.95),#1a1a1e);border-color:rgba(255,255,255,.14)"></div>
                            <div class="wc-preset-name">Minimal</div>
                            <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                        </div>
                        <div class="wc-preset-card {{ ($ws_sub['preset'] ?? '') === 'custom' ? 'active' : '' }}" data-preset="custom" onclick="wcSelectPreset('subathon','custom',this)">
                            <div class="wc-preset-swatch" style="background:linear-gradient(135deg,var(--brand),var(--purple));border-color:rgba(124,108,252,.3)"></div>
                            <div class="wc-preset-name">Custom</div>
                            <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                        </div>
                    </div>

                    @php
                        $subSurfaceHex = '#08080c';
                        $subSurfaceOpacity = 95;
                        $subBorderHex = '#7c6cfc';
                        $subBorderOpacity = 25;
                    @endphp
                    <div class="wc-custom-panel {{ ($ws_sub['preset'] ?? 'default') === 'custom' ? 'visible' : '' }}" id="subathon-custom">
                        <div class="ws-section-label" style="margin-bottom:4px">
                            <span class="iconify" data-icon="solar:pen-bold" style="width:12px;height:12px"></span>
                            Warna &amp; Gaya Custom
                        </div>

                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:square-bold"></span>Surface</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-color-wrap">
                                    <div class="wc-color-swatch" id="subathon-swatch-surface" style="background:{{ $ws_sub['bg'] ?? 'rgba(8,8,12,0.95)' }}"></div>
                                    <input type="color" class="wc-color-input" id="subathon-color-surface" value="{{ $subSurfaceHex }}" oninput="wcRgbaColorChange('subathon','surface',this)">
                                </div>
                                <div class="wc-opacity-wrap">
                                    <span class="wc-opacity-label">Opacity</span>
                                    <input type="range" class="wc-slider" min="0" max="100" value="{{ $subSurfaceOpacity }}" id="subathon-opacity-surface" oninput="wcOpacityChange('subathon','surface',this)">
                                    <span class="wc-opacity-val" id="subathon-opacityval-surface">{{ $subSurfaceOpacity }}%</span>
                                </div>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:border-bold"></span>Border</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-color-wrap">
                                    <div class="wc-color-swatch" id="subathon-swatch-border" style="background:{{ $ws_sub['border'] ?? 'rgba(124,108,252,0.25)' }}"></div>
                                    <input type="color" class="wc-color-input" id="subathon-color-border" value="{{ $subBorderHex }}" oninput="wcRgbaColorChange('subathon','border',this)">
                                </div>
                                <div class="wc-opacity-wrap">
                                    <span class="wc-opacity-label">Opacity</span>
                                    <input type="range" class="wc-slider" min="0" max="100" value="{{ $subBorderOpacity }}" id="subathon-opacity-border" oninput="wcOpacityChange('subathon','border',this)">
                                    <span class="wc-opacity-val" id="subathon-opacityval-border">{{ $subBorderOpacity }}%</span>
                                </div>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:star-bold"></span>Brand</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-color-wrap">
                                    <div class="wc-color-swatch" id="subathon-swatch-brand" style="background:{{ $ws_sub['brand'] ?? '#7c6cfc' }}"></div>
                                    <input type="color" class="wc-color-input" id="subathon-color-brand" value="{{ $ws_sub['brand'] ?? '#7c6cfc' }}" oninput="wcColorChange('subathon','brand',this)">
                                </div>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:star-shine-bold"></span>Brand 2</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-color-wrap">
                                    <div class="wc-color-swatch" id="subathon-swatch-brand2" style="background:{{ $ws_sub['brand2'] ?? '#a99dff' }}"></div>
                                    <input type="color" class="wc-color-input" id="subathon-color-brand2" value="{{ $ws_sub['brand2'] ?? '#a99dff' }}" oninput="wcColorChange('subathon','brand2',this)">
                                </div>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:text-bold"></span>Warna Text</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-color-wrap">
                                    <div class="wc-color-swatch" id="subathon-swatch-text" style="background:{{ $ws_sub['text'] ?? '#f1f1f6' }}"></div>
                                    <input type="color" class="wc-color-input" id="subathon-color-text" value="{{ $ws_sub['text'] ?? '#f1f1f6' }}" oninput="wcColorChange('subathon','text',this)">
                                </div>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:text-bold"></span>Warna Text 2</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-color-wrap">
                                    <div class="wc-color-swatch" id="subathon-swatch-text2" style="background:{{ $ws_sub['text2'] ?? '#a0a0b4' }}"></div>
                                    <input type="color" class="wc-color-input" id="subathon-color-text2" value="{{ $ws_sub['text2'] ?? '#a0a0b4' }}" oninput="wcColorChange('subathon','text2',this)">
                                </div>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:rounded-bold"></span>Border Radius</div>
                            <div class="wc-row-ctrl">
                                <div class="wc-slider-wrap">
                                    <input type="range" class="wc-slider" min="0" max="32" value="{{ $ws_sub['radius'] ?? 16 }}" id="subathon-slider-radius" oninput="wcSliderChange('subathon','radius',this)">
                                    <span class="wc-slider-val" id="subathon-sliderval-radius">{{ $ws_sub['radius'] ?? 16 }}px</span>
                                </div>
                            </div>
                        </div>
                        <div class="wc-row">
                            <div class="wc-row-label"><span class="iconify" data-icon="solar:ruler-cross-pen-bold"></span>Lebar Widget</div>
                            <div class="wc-row-ctrl">
                                <input type="number" class="wc-number" min="200" max="600" value="{{ $ws_sub['width'] ?? 320 }}" id="subathon-num-width" oninput="wcNumChange('subathon','width',this)">
                                <span class="wc-unit">px</span>
                            </div>
                        </div>
                    </div>

                    <div class="wc-save-row">
                        <button class="wc-save-btn" id="subathon-save-btn" onclick="wcSave('subathon')">
                            <span class="iconify" data-icon="solar:floppy-disk-bold"></span>
                            Simpan Tampilan
                        </button>
                        <button class="wc-reset-link" onclick="wcReset('subathon')">Reset ke default</button>
                    </div>
                </div>

            </div>

            {{-- ══ TAB: Running Text ══ --}}
            @php $ws_rt = $widgetSettings['running_text'] ?? []; @endphp
            <div class="ws-panel" id="tab-running-text">
                <div class="ws-card">
                    <div class="ws-card-head">
                        <div class="ws-card-icon" style="background:rgba(124,108,252,.1);border:1px solid rgba(124,108,252,.2)">
                            <span class="iconify" data-icon="solar:text-bold-duotone" style="color:var(--brand-light)"></span>
                        </div>
                        <div>
                            <div class="ws-card-title">Running Text</div>
                            <div class="ws-card-sub">Teks berjalan horizontal di bawah layar</div>
                        </div>
                    </div>

                    <div class="ws-opt-row">
                        <div class="ws-opt-label">
                            <div>Aktifkan Running Text</div>
                            <div class="ws-opt-sub">Teks akan berjalan di layar</div>
                        </div>
                        <label class="ws-toggle">
                            <input type="checkbox" id="running-text-enabled" {{ ($ws_rt['enabled'] ?? false) ? 'checked' : '' }}>
                            <span class="ws-toggle-slider"></span>
                        </label>
                    </div>

                    <div class="ws-section-label" style="margin-top:16px">
                        <span class="iconify" data-icon="solar:text" style="width:12px;height:12px"></span>
                        Isi Teks
                    </div>
                    <div class="form-group">
                        <textarea id="running-text-content" rows="2" style="width:100%;padding:10px;background:var(--surface-2);border:1px solid var(--border);border-radius:var(--radius-sm);color:var(--text);font-size:13px">{{ $ws_rt['text'] ?? 'Terima kasih atas donasi Anda! Semangat terus streamnya!' }}</textarea>
                        <div class="hint">
                            <span class="iconify" data-icon="solar:info-circle-bold"></span>
                            Teks yang akan berjalan di layar
                        </div>
                    </div>

                    <div class="ws-section-label" style="margin-top:16px">
                        <span class="iconify" data-icon="solar:link-bold" style="width:12px;height:12px"></span>
                        URL Browser Source OBS
                    </div>

                    <div class="obs-url-grid">
                        <div class="obs-url-row">
                            <div class="obs-url-icon" style="background:rgba(124,108,252,.1);border:1px solid rgba(124,108,252,.2)">
                                <span class="iconify" data-icon="solar:text-bold-duotone" style="color:var(--brand-light)"></span>
                            </div>
                            <div class="obs-url-info">
                                <div class="obs-url-label">
                                    Running Text
                                    <code style="font-size:9px;background:rgba(255,255,255,.06);padding:1px 5px;border-radius:4px">?key=</code>
                                    sudah termasuk
                                </div>
                                @php $rtUrl = route('obs.running-text', $streamer->slug) . '?key=' . $streamer->api_key; @endphp
                                <input class="obs-url-input" readonly value="{{ $rtUrl }}" id="url-running-text" />
                            </div>
                            <div class="obs-url-actions">
                                <button class="obs-btn primary" onclick="copyText('{{ $rtUrl }}', 'URL Running Text')">
                                    <span class="iconify" data-icon="solar:copy-bold-duotone"></span>Copy URL
                                </button>
                                <a class="obs-btn" href="{{ $rtUrl }}" target="_blank">
                                    <span class="iconify" data-icon="solar:eye-bold-duotone"></span>Buka
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ws-card">
                    <div class="ws-card-head">
                        <div class="ws-card-icon" style="background:rgba(124,108,252,.1);border:1px solid rgba(124,108,252,.2)">
                            <span class="iconify" data-icon="solar:palette-bold-duotone" style="color:var(--brand-light)"></span>
                        </div>
                        <div>
                            <div class="ws-card-title">Kustomisasi Tampilan</div>
                            <div class="ws-card-sub">Atur kecepatan, warna, dan tampilan</div>
                        </div>
                    </div>

                    <div class="ws-section-label">
                        <span class="iconify" data-icon="solar:magic-stick-bold" style="width:12px;height:12px"></span>
                        Preset Tema
                    </div>

                    <div class="wc-presets" id="running-text-presets">
                        <div class="wc-preset-card {{ ($ws_rt['preset'] ?? 'default') === 'default' ? 'active' : '' }}" data-preset="default" onclick="wcSelectPreset('running-text','default',this)">
                            <div class="wc-preset-swatch" style="background:linear-gradient(90deg,rgba(8,8,12,0.9),#7c6cfc);border-color:rgba(124,108,252,.2)"></div>
                            <div class="wc-preset-name">Default</div>
                            <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                        </div>
                        <div class="wc-preset-card {{ ($ws_rt['preset'] ?? '') === 'neon' ? 'active' : '' }}" data-preset="neon" onclick="wcSelectPreset('running-text','neon',this)">
                            <div class="wc-preset-swatch" style="background:linear-gradient(90deg,rgba(0,0,0,0.95),#00ffc8);border-color:rgba(0,255,200,.3)"></div>
                            <div class="wc-preset-name">Neon</div>
                            <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                        </div>
                        <div class="wc-preset-card {{ ($ws_rt['preset'] ?? '') === 'fire' ? 'active' : '' }}" data-preset="fire" onclick="wcSelectPreset('running-text','fire',this)">
                            <div class="wc-preset-swatch" style="background:linear-gradient(90deg,rgba(20,5,0,0.95),#ff4400);border-color:rgba(255,68,0,.3)"></div>
                            <div class="wc-preset-name">Fire</div>
                            <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                        </div>
                        <div class="wc-preset-card {{ ($ws_rt['preset'] ?? '') === 'gold' ? 'active' : '' }}" data-preset="gold" onclick="wcSelectPreset('running-text','gold',this)">
                            <div class="wc-preset-swatch" style="background:linear-gradient(90deg,rgba(20,15,0,0.95),#ffd700);border-color:rgba(255,215,0,.3)"></div>
                            <div class="wc-preset-name">Gold</div>
                            <div class="wc-preset-check"><span class="iconify" data-icon="solar:check-bold"></span></div>
                        </div>
                    </div>

                    <div class="ws-section-label" style="margin-top:20px">
                        <span class="iconify" data-icon="solar:settings-bold" style="width:12px;height:12px"></span>
                        Pengaturan
                    </div>

                    <div class="ws-opt-row">
                        <div class="ws-opt-label">
                            <div>Kecepatan</div>
                            <div class="ws-opt-sub">Semakin kecil semakin cepat</div>
                        </div>
                        <div class="ws-opt-input">
                            <input type="number" id="running-text-speed" value="{{ $ws_rt['speed'] ?? 50 }}" min="10" max="200" style="width:80px">
                            <span class="ws-opt-unit">detik</span>
                        </div>
                    </div>

                    <div class="ws-opt-row">
                        <div class="ws-opt-label">
                            <div>Arah Pergerakan</div>
                        </div>
                        <select id="running-text-direction" style="padding:8px 12px;background:var(--surface-2);border:1px solid var(--border);border-radius:6px;color:var(--text)">
                            <option value="left" {{ ($ws_rt['direction'] ?? 'left') === 'left' ? 'selected' : '' }}>Kiri → Kanan</option>
                            <option value="right" {{ ($ws_rt['direction'] ?? '') === 'right' ? 'selected' : '' }}>Kanan → Kiri</option>
                        </select>
                    </div>

                    <div class="ws-opt-row">
                        <div class="ws-opt-label">
                            <div>Ukuran Font</div>
                        </div>
                        <div class="ws-opt-input">
                            <input type="number" id="running-text-font-size" value="{{ $ws_rt['font_size'] ?? 18 }}" min="12" max="32" style="width:80px">
                            <span class="ws-opt-unit">px</span>
                        </div>
                    </div>

                    <div class="ws-section-label" style="margin-top:16px">
                        <span class="iconify" data-icon="solar:palette-bold" style="width:12px;height:12px"></span>
                        Warna
                    </div>

                    <div class="form-row" style="margin-top:12px">
                        <div class="form-group">
                            <label>Warna Teks</label>
                            <div style="display:flex;align-items:center;gap:8px">
                                <input type="color" id="running-text-color-text" value="{{ $ws_rt['text'] ?? '#ffffff' }}" style="width:40px;height:32px;padding:0;border:none;border-radius:6px;cursor:pointer">
                                <input type="text" id="running-text-color-text-text" value="{{ $ws_rt['text'] ?? '#ffffff' }}" style="width:100px;font-size:12px;font-family:monospace">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Warna Background</label>
                            <div style="display:flex;align-items:center;gap:8px">
                                <input type="color" id="running-text-color-bg" value="{{ substr($ws_rt['bg'] ?? '#08080c', 0, 7) }}" style="width:40px;height:32px;padding:0;border:none;border-radius:6px;cursor:pointer">
                                <input type="text" id="running-text-color-bg-text" value="{{ substr($ws_rt['bg'] ?? '#08080c', 0, 7) }}" style="width:100px;font-size:12px;font-family:monospace">
                            </div>
                        </div>
                    </div>

                    <div class="ws-save-row" style="margin-top:20px">
                        <button class="ws-save-btn" onclick="saveRunningTextSettings()">
                            <span class="iconify" data-icon="solar:floppy-disk-bold"></span>
                            Simpan Pengaturan
                        </button>
                        <span class="ws-save-msg" id="msg-running-text"></span>
                    </div>
                </div>
            </div>

            {{-- ══ TAB: OBS Canvas ══ --}}
            <div class="ws-panel" id="tab-canvas">
                <div class="ws-card">
                    <div class="ws-card-head">
                        <div class="ws-card-icon" style="background:rgba(124,108,252,.1);border:1px solid rgba(124,108,252,.2)">
                            <span class="iconify" data-icon="solar:monitor-bold-duotone" style="color:var(--brand-light)"></span>
                        </div>
                        <div>
                            <div class="ws-card-title">OBS Canvas Editor</div>
                            <div class="ws-card-sub">Semua widget dalam satu Browser Source tunggal</div>
                        </div>
                    </div>

                    <div class="ws-info-box warning">
                        <span class="iconify" data-icon="solar:danger-triangle-bold-duotone"></span>
                        <span><strong>Mode Lanjutan</strong> — Canvas menggabungkan Alert + Milestone + Leaderboard + QR dalam satu URL. Cocok jika kamu hanya ingin satu Browser Source di OBS.</span>
                    </div>

                    <div class="ws-size-chips">
                        <span class="ws-size-chip">
                            <span class="iconify" data-icon="solar:monitor-bold"></span>
                            1920 × 1080
                        </span>
                        <span class="ws-size-chip">
                            <span class="iconify" data-icon="solar:layers-bold"></span>
                            All-in-one
                        </span>
                    </div>

                    <div class="obs-url-grid" style="margin-bottom:20px">
                        <div class="obs-url-row">
                            <div class="obs-url-icon" style="background:rgba(124,108,252,.1);border:1px solid rgba(124,108,252,.2)">
                                <span class="iconify" data-icon="solar:monitor-bold-duotone" style="color:var(--brand-light)"></span>
                            </div>
                            <div class="obs-url-info">
                                <div class="obs-url-label">Canvas All-in-one</div>
                                @php $canvasUrl = route('obs.canvas', $streamer->slug) . '?key=' . $streamer->api_key; @endphp
                                <input class="obs-url-input" readonly value="{{ $canvasUrl }}" id="url-canvas" />
                            </div>
                            <div class="obs-url-actions">
                                <button class="obs-btn primary" onclick="copyText('{{ $canvasUrl }}', 'URL Canvas')">
                                    <span class="iconify" data-icon="solar:copy-bold-duotone"></span>Copy URL
                                </button>
                                <a class="obs-btn" href="{{ $canvasUrl }}" target="_blank">
                                    <span class="iconify" data-icon="solar:eye-bold-duotone"></span>Buka
                                </a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('streamer.obs-canvas') }}"
                       style="display:inline-flex;align-items:center;gap:8px;padding:10px 20px;background:linear-gradient(135deg,var(--brand),#6356e8);border:none;border-radius:var(--radius);color:#fff;font-size:13px;font-weight:700;cursor:pointer;text-decoration:none;transition:all .2s;box-shadow:0 4px 20px var(--brand-glow)">
                        <span class="iconify" data-icon="solar:pen-bold-duotone" style="width:16px;height:16px"></span>
                        Buka Canvas Editor
                    </a>
                </div>
            </div>{{-- /tab-canvas --}}

        </div>{{-- /ws-panels --}}
    </div>{{-- /ws-body --}}

</div>{{-- /ws-wrap --}}

@push('scripts')
<script>
/* ══════════════════════════════════════════════
   WIDGET STUDIO — JavaScript
   Phase 1: Tab switching
   Phase 2: Customizer — preset picker + custom controls + save/reset
══════════════════════════════════════════════ */

// ── In-memory state: current field values per widget ──
var wcState = {
    alert:       @json($widgetSettings['alert']),
    milestone:   @json($widgetSettings['milestone']),
    leaderboard: @json($widgetSettings['leaderboard']),
    qr:          @json($widgetSettings['qr']),
    subathon:    @json($widgetSettings['subathon'] ?? []),
    running_text: @json($widgetSettings['running_text'] ?? []),
};

// ── Defaults (mirrors Streamer::getWidgetSettings) ──
var wcDefaults = {
    alert: {
        preset:'default', bg:'rgba(8,8,12,0.96)', border:'rgba(255,255,255,0.1)',
        accent:'#7c6cfc', accent2:'#a99dff', amount_color:'#f97316',
        donor_color:'#f1f1f6', top_line:'linear-gradient(90deg,#7c6cfc,#a855f7,#22d3a0)',
        prog_bar:'linear-gradient(90deg,#7c6cfc,#f97316)',
        radius:'16', width:'560', position_x:'center', position_y:'bottom',
        layout:'classic', style:'glass', font_family:'inter',
        font_size_title:'17', font_size_amount:'24', font_size_msg:'13',
        card_opacity:'96', spacing:'2',
    },
    milestone: {
        preset:'default', surface:'rgba(8,8,12,0.96)', border:'rgba(124,108,252,0.2)',
        brand:'#7c6cfc', brand2:'#a99dff', orange:'#f97316', green:'#22d3a0',
        radius:'16', width:'340', position:'bottom-left',
    },
    leaderboard: {
        preset:'default', surface:'rgba(8,8,12,0.96)', border:'rgba(124,108,252,0.2)',
        brand:'#7c6cfc', brand2:'#a99dff', yellow:'#fbbf24', green:'#22d3a0',
        radius:'16', width:'300', position:'top-left',
    },
    qr: {
        preset:'default', surface:'rgba(10,10,16,0.93)', border:'rgba(124,108,252,0.28)',
        brand:'#7c6cfc', brand2:'#a855f7',
        radius:'16', width:'320', position:'top-center',
    },
    subathon: {
        preset:'default', bg:'rgba(8,8,12,0.95)', border:'rgba(124,108,252,0.25)',
        brand:'#7c6cfc', brand2:'#a99dff', text:'#f1f1f6', text2:'#a0a0b4',
        radius:'16', width:'320',
    },
    running_text: {
        preset:'default', enabled:false,
        text:'Terima kasih atas donasi Anda! Semangat terus streamnya!',
        speed:'50', direction:'left',
        bg:'rgba(8,8,12,0.9)', border:'rgba(124,108,252,0.2)',
        brand:'#7c6cfc', text:'#ffffff',
        font_size:'18', font_family:'inter',
        radius:'0', opacity:'90',
    },
};

/* ── Tab switching ── */
function wsTab(name, navEl) {
    document.querySelectorAll('.ws-panel').forEach(function(p) { p.classList.remove('active'); });
    document.querySelectorAll('.ws-nav-item').forEach(function(n) { n.classList.remove('active'); });
    var panel = document.getElementById('tab-' + name);
    if (panel) panel.classList.add('active');
    if (navEl) navEl.classList.add('active');
    history.replaceState(null, '', '#' + name);
    try { sessionStorage.setItem('ws_tab', name); } catch(e) {}
}

// Restore tab from hash or sessionStorage
document.addEventListener('DOMContentLoaded', function () {
    var tab = null;
    var hash = location.hash.replace('#', '');
    if (hash && document.querySelector('.ws-nav-item[data-tab="' + hash + '"]')) {
        tab = hash;
    } else {
        try { tab = sessionStorage.getItem('ws_tab'); } catch(e) {}
    }
    if (tab) {
        var navEl = document.querySelector('.ws-nav-item[data-tab="' + tab + '"]');
        if (navEl) wsTab(tab, navEl);
    }
});

/* ── Toast ── */
function wsToast(msg, type) {
    var t = document.getElementById('ws-toast');
    t.className = 'show ' + (type || 'success');
    t.querySelector('.ws-toast-msg').textContent = msg;
    t.querySelector('.iconify').setAttribute('data-icon',
        type === 'error' ? 'solar:close-circle-bold' : 'solar:check-circle-bold');
    clearTimeout(t._timer);
    t._timer = setTimeout(function() { t.className = t.className.replace('show','').trim(); }, 3000);
}

/* ── Preset palettes — mirrors body.theme-* CSS vars in overlay.blade.php ── */
var wcPresetPalettes = {
    alert: {
        default: {
            bg:           'rgba(8,8,12,.96)',
            border:       'rgba(255,255,255,.1)',
            accent:       '#7c6cfc',
            accent2:      '#a99dff',
            amount_color: '#f97316',
            donor_color:  '#f1f1f6',
            top_line:     'linear-gradient(90deg,#7c6cfc,#a855f7,#22d3a0)',
            prog_bar:     'linear-gradient(90deg,#7c6cfc,#f97316)',
            radius:       '16',
        },
        neon: {
            bg:           'rgba(2,4,18,.97)',
            border:       'rgba(0,255,200,.22)',
            accent:       '#00ffc8',
            accent2:      '#00e5ff',
            amount_color: '#00e5ff',
            donor_color:  '#00ffc8',
            top_line:     'linear-gradient(90deg,#00ffc8,#00c8ff,#7c6cfc)',
            prog_bar:     'linear-gradient(90deg,#00ffc8,#00c8ff)',
            radius:       '14',
        },
        fire: {
            bg:           'rgba(10,4,2,.97)',
            border:       'rgba(249,115,22,.22)',
            accent:       '#f97316',
            accent2:      '#fbbf24',
            amount_color: '#fbbf24',
            donor_color:  '#fef3c7',
            top_line:     'linear-gradient(90deg,#f97316,#ef4444,#fbbf24)',
            prog_bar:     'linear-gradient(90deg,#f97316,#ef4444,#fbbf24)',
            radius:       '16',
        },
        ice: {
            bg:           'rgba(2,8,22,.96)',
            border:       'rgba(147,210,255,.18)',
            accent:       '#38bdf8',
            accent2:      '#818cf8',
            amount_color: '#38bdf8',
            donor_color:  '#e0f2fe',
            top_line:     'linear-gradient(90deg,#38bdf8,#818cf8,#e0f2fe)',
            prog_bar:     'linear-gradient(90deg,#38bdf8,#818cf8)',
            radius:       '18',
        },
        minimal: {
            bg:           'rgba(12,12,16,.95)',
            border:       'rgba(255,255,255,.14)',
            accent:       '#e0e0f0',
            accent2:      '#ffffff',
            amount_color: '#ffffff',
            donor_color:  '#ffffff',
            top_line:     'linear-gradient(90deg,rgba(255,255,255,.5),rgba(255,255,255,.15))',
            prog_bar:     'linear-gradient(90deg,rgba(255,255,255,.7),rgba(255,255,255,.3))',
            radius:       '12',
        },
        custom: null,
    },
    milestone: {
        default: {
            surface: 'rgba(8,8,12,.96)',
            border:  'rgba(124,108,252,.2)',
            brand:   '#7c6cfc',
            brand2:  '#a99dff',
            orange:  '#f97316',
            green:   '#22d3a0',
            radius:  '16',
        },
        neon: {
            surface: 'rgba(2,4,18,.97)',
            border:  'rgba(0,255,200,.22)',
            brand:   '#00ffc8',
            brand2:  '#00e5ff',
            orange:  '#00e5ff',
            green:   '#00ffc8',
            radius:  '14',
        },
        fire: {
            surface: 'rgba(10,4,2,.97)',
            border:  'rgba(249,115,22,.22)',
            brand:   '#f97316',
            brand2:  '#fbbf24',
            orange:  '#fbbf24',
            green:   '#ef4444',
            radius:  '16',
        },
        ice: {
            surface: 'rgba(2,8,22,.96)',
            border:  'rgba(147,210,255,.18)',
            brand:   '#38bdf8',
            brand2:  '#818cf8',
            orange:  '#38bdf8',
            green:   '#818cf8',
            radius:  '18',
        },
        minimal: {
            surface: 'rgba(12,12,16,.95)',
            border:  'rgba(255,255,255,.14)',
            brand:   '#e0e0f0',
            brand2:  '#ffffff',
            orange:  '#ffffff',
            green:   '#e0e0f0',
            radius:  '12',
        },
        custom: null,
    },
    leaderboard: {
        default: {
            surface: 'rgba(8,8,12,.96)',
            border:  'rgba(124,108,252,.2)',
            brand:   '#7c6cfc',
            brand2:  '#a99dff',
            yellow:  '#fbbf24',
            green:   '#22d3a0',
            radius:  '16',
        },
        neon: {
            surface: 'rgba(2,4,18,.97)',
            border:  'rgba(0,255,200,.22)',
            brand:   '#00ffc8',
            brand2:  '#00e5ff',
            yellow:  '#00e5ff',
            green:   '#00ffc8',
            radius:  '14',
        },
        fire: {
            surface: 'rgba(10,4,2,.97)',
            border:  'rgba(249,115,22,.22)',
            brand:   '#f97316',
            brand2:  '#fbbf24',
            yellow:  '#fbbf24',
            green:   '#ef4444',
            radius:  '16',
        },
        ice: {
            surface: 'rgba(2,8,22,.96)',
            border:  'rgba(147,210,255,.18)',
            brand:   '#38bdf8',
            brand2:  '#818cf8',
            yellow:  '#e0f2fe',
            green:   '#818cf8',
            radius:  '18',
        },
        minimal: {
            surface: 'rgba(12,12,16,.95)',
            border:  'rgba(255,255,255,.14)',
            brand:   '#e0e0f0',
            brand2:  '#ffffff',
            yellow:  '#ffffff',
            green:   '#e0e0f0',
            radius:  '12',
        },
        custom: null,
    },
    qr: {
        default: {
            surface: 'rgba(10,10,16,.93)',
            border:  'rgba(124,108,252,.28)',
            brand:   '#7c6cfc',
            brand2:  '#a99dff',
            radius:  '22',
        },
        neon: {
            surface: 'rgba(2,4,18,.97)',
            border:  'rgba(0,255,200,.22)',
            brand:   '#00ffc8',
            brand2:  '#00e5ff',
            radius:  '20',
        },
        fire: {
            surface: 'rgba(10,4,2,.97)',
            border:  'rgba(249,115,22,.22)',
            brand:   '#f97316',
            brand2:  '#fbbf24',
            radius:  '22',
        },
        ice: {
            surface: 'rgba(2,8,22,.96)',
            border:  'rgba(147,210,255,.18)',
            brand:   '#38bdf8',
            brand2:  '#818cf8',
            radius:  '24',
        },
        minimal: {
            surface: 'rgba(12,12,16,.95)',
            border:  'rgba(255,255,255,.14)',
            brand:   '#e0e0f0',
            brand2:  '#ffffff',
            radius:  '16',
        },
        custom: null,
    },
};

/* ── Preset picker ── */
function wcSelectPreset(widget, preset, card) {
    // Update UI cards
    document.querySelectorAll('#' + widget + '-presets .wc-preset-card').forEach(function(c) {
        c.classList.remove('active');
    });
    card.classList.add('active');

    // Show/hide custom panel
    var panel = document.getElementById(widget + '-custom');
    if (panel) {
        panel.classList.toggle('visible', preset === 'custom');
    }

    // Sync wcState color fields to the selected preset's palette.
    // This ensures:
    //   1. Color pickers reflect the preset's actual colors.
    //   2. If user later switches to 'custom', they start from the preset's colors.
    //   3. The preview correctly shows the preset's palette.
    var palettes = wcPresetPalettes[widget];
    if (palettes && preset !== 'custom' && palettes[preset]) {
        var palette = palettes[preset];
        Object.keys(palette).forEach(function(field) {
            wcState[widget][field] = palette[field];
        });
    }

    // Update state
    wcState[widget].preset = preset;

    // Sync DOM controls (color pickers, sliders) to the new state, then update preview
    wcSyncDom(widget, wcState[widget]);
}

/* ── Step navigation ── */
function wcStep(widget, stepNum, btnEl) {
    document.querySelectorAll('#' + widget + '-steps .wc-step-btn').forEach(function(b) { b.classList.remove('active'); });
    if (btnEl) btnEl.classList.add('active');
    for (var i = 1; i <= 5; i++) {
        var panel = document.getElementById(widget + '-step-' + i);
        if (panel) panel.classList.toggle('active', i === stepNum);
    }
}

/* ── Layout picker ── */
function wcSelectLayout(widget, layout, card) {
    document.querySelectorAll('#' + widget + '-layouts .wc-layout-card').forEach(function(c) { c.classList.remove('active'); });
    card.classList.add('active');
    wcState[widget].layout = layout;
    wcUpdatePreview(widget);
}

/* ── Style picker ── */
function wcSelectStyle(widget, style, card) {
    document.querySelectorAll('#' + widget + '-styles .wc-style-card').forEach(function(c) { c.classList.remove('active'); });
    card.classList.add('active');
    wcState[widget].style = style;
    wcUpdatePreview(widget);
}

/* ── Font family picker ── */
function wcSelectFont(widget, fontKey, btn) {
    document.querySelectorAll('#' + widget + '-fonts .wc-font-btn').forEach(function(b) { b.classList.remove('active'); });
    btn.classList.add('active');
    wcState[widget].font_family = fontKey;
    wcUpdatePreview(widget);
}

/* ── Font size slider ── */
function wcFontSizeChange(widget, field, input) {
    var val = input.value;
    var label = document.getElementById(widget + '-sliderval-' + field);
    if (label) label.textContent = val + 'px';
    wcState[widget][field] = val;
    wcUpdatePreview(widget);
}

/* ── Blur slider ── */
function wcBlurSliderChange(widget, field, input) {
    var val = input.value;
    var label = document.getElementById(widget + '-sliderval-' + field);
    if (label) label.textContent = val + 'px';
    wcState[widget][field] = val;
    wcUpdatePreview(widget);
}

/* ── Card opacity slider ── */
function wcCardOpacityChange(widget, input) {
    var val = parseInt(input.value, 10);
    var label = document.getElementById(widget + '-sliderval-card_opacity');
    if (label) label.textContent = val + '%';
    wcState[widget].card_opacity = String(val);
    wcUpdatePreview(widget);
}

/* ── Pure-hex color picker → swatch sync ── */
function wcColorChange(widget, field, input) {
    var hex = input.value;
    var swatch = document.getElementById(widget + '-swatch-' + field);
    if (swatch) swatch.style.background = hex;
    wcState[widget][field] = hex;
    wcUpdatePreview(widget);
}

/* ── rgba color picker: update hex component, rebuild rgba ── */
function wcRgbaColorChange(widget, field, input) {
    var hex = input.value;
    var swatch = document.getElementById(widget + '-swatch-' + field);
    // Get current opacity from companion slider
    var opacitySlider = document.getElementById(widget + '-opacity-' + field);
    var opacity = opacitySlider ? (parseInt(opacitySlider.value, 10) / 100) : 1;
    var rgba = wcHexOpacityToRgba(hex, opacity);
    if (swatch) swatch.style.background = rgba;
    wcState[widget][field] = rgba;
    wcUpdatePreview(widget);
}

/* ── Opacity slider: rebuild rgba from current color picker hex ── */
function wcOpacityChange(widget, field, input) {
    var opacity = parseInt(input.value, 10) / 100;
    var label = document.getElementById(widget + '-opacityval-' + field);
    if (label) label.textContent = input.value + '%';
    var colorPicker = document.getElementById(widget + '-color-' + field);
    var hex = colorPicker ? colorPicker.value : '#000000';
    var rgba = wcHexOpacityToRgba(hex, opacity);
    var swatch = document.getElementById(widget + '-swatch-' + field);
    if (swatch) swatch.style.background = rgba;
    wcState[widget][field] = rgba;
    wcUpdatePreview(widget);
}

/* ── Convert #rrggbb + opacity(0-1) → rgba(r,g,b,a) ── */
function wcHexOpacityToRgba(hex, opacity) {
    var r = parseInt(hex.slice(1,3), 16);
    var g = parseInt(hex.slice(3,5), 16);
    var b = parseInt(hex.slice(5,7), 16);
    var a = Math.round(opacity * 100) / 100;
    return 'rgba(' + r + ',' + g + ',' + b + ',' + a + ')';
}

/* ── Gradient stop color pickers → rebuild gradient ── */
function wcGradientStopChange(widget, field, inputA, inputB) {
    var colorA = inputA.value;
    var colorB = inputB.value;
    // Update stop swatches
    var swatchA = document.getElementById(widget + '-grad-swatch-' + field + '-a');
    var swatchB = document.getElementById(widget + '-grad-swatch-' + field + '-b');
    if (swatchA) swatchA.style.background = colorA;
    if (swatchB) swatchB.style.background = colorB;
    // Check if existing gradient has a middle stop — preserve it if so
    var existing  = wcState[widget][field] || '';
    var allStops  = existing.match(/#[0-9a-fA-F]{6}/g) || [];
    var grad;
    if (allStops.length >= 3) {
        // Keep the middle stop unchanged; only update first and last
        grad = 'linear-gradient(90deg,' + colorA + ',' + allStops[1] + ',' + colorB + ')';
    } else {
        grad = 'linear-gradient(90deg,' + colorA + ',' + colorB + ')';
    }
    var preview = document.getElementById(widget + '-grad-' + field);
    if (preview) preview.style.background = grad;
    wcState[widget][field] = grad;
    wcUpdatePreview(widget);
}

/* ── Range slider ── */
function wcSliderChange(widget, field, input) {
    var val = input.value;
    var label = document.getElementById(widget + '-sliderval-' + field);
    if (label) label.textContent = val + 'px';
    wcState[widget][field] = val;
    wcUpdatePreview(widget);
}

/* ── Number input ── */
function wcNumChange(widget, field, input) {
    wcState[widget][field] = input.value;
    wcUpdatePreview(widget);
}

/* ── Select ── */
function wcSelectChange(widget, field, select) {
    wcState[widget][field] = select.value;
    wcUpdatePreview(widget);
}

/* ── Save to server ── */
function wcSave(widget) {
    var btn = document.getElementById(widget + '-save-btn');
    if (btn) { btn.classList.add('saving'); btn.innerHTML = '<span class="iconify" data-icon="solar:loading-bold-duotone"></span> Menyimpan\u2026'; }

    var payload = Object.assign({}, wcState[widget]);

    fetch('{{ route("streamer.widgets.save") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ widget: widget, data: payload }),
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (res.ok) {
            wsToast('Tampilan ' + widget + ' berhasil disimpan!', 'success');
        } else {
            wsToast('Gagal menyimpan: ' + (res.error || 'unknown error'), 'error');
        }
    })
    .catch(function() { wsToast('Koneksi gagal. Coba lagi.', 'error'); })
    .finally(function() {
        if (btn) {
            btn.classList.remove('saving');
            btn.innerHTML = '<span class="iconify" data-icon="solar:floppy-disk-bold"></span> Simpan Tampilan';
        }
    });
}

/* ── Reset to defaults ── */
function wcReset(widget) {
    if (!confirm('Reset tampilan ' + widget + ' ke default? Perubahan yang belum disimpan akan hilang.')) return;

    var def = wcDefaults[widget];
    wcState[widget] = Object.assign({}, def);

    // Reset preset picker
    document.querySelectorAll('#' + widget + '-presets .wc-preset-card').forEach(function(c) {
        c.classList.toggle('active', c.dataset.preset === 'default');
    });
    // Hide custom panel
    var panel = document.getElementById(widget + '-custom');
    if (panel) panel.classList.remove('visible');

    // Sync all DOM controls
    wcSyncDom(widget, def);

    // Reset step-specific UI for alert
    if (widget === 'alert') {
        var defaultLayout = (def.layout || 'classic');
        var defaultStyle  = (def.style  || 'glass');
        var defaultFont   = (def.font_family || 'inter');

        // Reset layout cards — use data from wcDefaults, not onclick string parsing
        document.querySelectorAll('#alert-layouts .wc-layout-card').forEach(function(c) {
            var m = (c.getAttribute('onclick') || '').match(/wcSelectLayout\s*\([^,]+,\s*'([^']+)'/);
            c.classList.toggle('active', m ? m[1] === defaultLayout : false);
        });
        // Reset style cards
        document.querySelectorAll('#alert-styles .wc-style-card').forEach(function(c) {
            var m = (c.getAttribute('onclick') || '').match(/wcSelectStyle\s*\([^,]+,\s*'([^']+)'/);
            c.classList.toggle('active', m ? m[1] === defaultStyle : false);
        });
        // Reset font buttons
        document.querySelectorAll('#alert-fonts .wc-font-btn').forEach(function(b) {
            var m = (b.getAttribute('onclick') || '').match(/wcSelectFont\s*\([^,]+,\s*'([^']+)'/);
            b.classList.toggle('active', m ? m[1] === defaultFont : false);
        });
        // Return to step 1
        wcStep('alert', 1, document.querySelector('#alert-steps .wc-step-btn:first-child'));
    }

    // Save immediately
    wcSave(widget);
}

/* ── Sync DOM controls from a state object ── */
function wcSyncDom(widget, state) {
    Object.keys(state).forEach(function(field) {
        var val = state[field];
        if (field === 'preset') return;

        // color swatch (always update to final value)
        var swatch = document.getElementById(widget + '-swatch-' + field);
        if (swatch) swatch.style.background = val;

        // Plain hex color picker
        var colorPicker = document.getElementById(widget + '-color-' + field);
        if (colorPicker) {
            if (/^#[0-9a-fA-F]{6}$/.test(val)) {
                colorPicker.value = val;
            } else {
                // rgba value — extract hex part
                var rgbaMatch = val.match(/rgba\((\d+),\s*(\d+),\s*(\d+)/);
                if (rgbaMatch) {
                    colorPicker.value = '#' +
                        ('0'+parseInt(rgbaMatch[1],10).toString(16)).slice(-2) +
                        ('0'+parseInt(rgbaMatch[2],10).toString(16)).slice(-2) +
                        ('0'+parseInt(rgbaMatch[3],10).toString(16)).slice(-2);
                }
            }
        }

        // Opacity slider (for rgba fields)
        var opacitySlider = document.getElementById(widget + '-opacity-' + field);
        var opacityVal    = document.getElementById(widget + '-opacityval-' + field);
        if (opacitySlider) {
            var aMatch = val.match(/rgba\(\d+,\s*\d+,\s*\d+,\s*([\d.]+)\)/);
            var pct = aMatch ? Math.round(parseFloat(aMatch[1]) * 100) : 100;
            opacitySlider.value = pct;
            if (opacityVal) opacityVal.textContent = pct + '%';
        }

        // Gradient preview + stop pickers
        var gradEl = document.getElementById(widget + '-grad-' + field);
        if (gradEl) {
            gradEl.style.background = val;
            // Extract hex stops and update stop pickers
            var stops = val.match(/#[0-9a-fA-F]{6}/g) || [];
            var stopA = document.getElementById(widget + '-grad-color-' + field + '-a');
            var stopB = document.getElementById(widget + '-grad-color-' + field + '-b');
            var swA   = document.getElementById(widget + '-grad-swatch-' + field + '-a');
            var swB   = document.getElementById(widget + '-grad-swatch-' + field + '-b');
            if (stopA && stops[0]) { stopA.value = stops[0]; if (swA) swA.style.background = stops[0]; }
            if (stopB && stops[1]) { stopB.value = stops[1]; if (swB) swB.style.background = stops[1]; }
        }

        // slider
        var slider = document.getElementById(widget + '-slider-' + field);
        if (slider) { slider.value = val; }
        var sliderVal = document.getElementById(widget + '-sliderval-' + field);
        if (sliderVal) {
            sliderVal.textContent = val + 'px';
        }

        // number
        var num = document.getElementById(widget + '-num-' + field);
        if (num) num.value = val;

        // select
        var sel = document.getElementById(widget + '-sel-' + field);
        if (sel) sel.value = val;
    });
    wcUpdatePreview(widget);
}

/* ══════════════════════════════════════════════
   WIDGET STUDIO — Phase 3
   Live Preview: wcUpdatePreview()
   Updates the CSS-rendered mock widget inside the
   preview card to reflect the current wcState.
══════════════════════════════════════════════ */

/* Preset → theme class mapping */
var wcPresetThemeMap = {
    alert:       { default:'', neon:'theme-neon', fire:'theme-fire', ice:'theme-ice', minimal:'theme-minimal', custom:'' },
    milestone:   { default:'', neon:'theme-neon', fire:'theme-fire', ice:'theme-ice', minimal:'theme-minimal', custom:'' },
    leaderboard: { default:'', neon:'theme-neon', fire:'theme-fire', ice:'theme-ice', minimal:'theme-minimal', custom:'' },
    qr:          { default:'', neon:'theme-neon', fire:'theme-fire', ice:'theme-ice', minimal:'theme-minimal', custom:'' },
};

function wcUpdatePreview(widget) {
    var el = document.getElementById('preview-' + widget);
    if (!el) return;

    var st      = wcState[widget];
    var preset  = st.preset || 'default';
    var isCustom = preset === 'custom';

    // -- Apply theme class on the preview wrapper --
    var themeMap = wcPresetThemeMap[widget] || {};
    var allThemes = ['theme-neon','theme-fire','theme-ice','theme-minimal'];
    allThemes.forEach(function(cls) { el.classList.remove(cls); });
    var cls = themeMap[preset] || '';
    if (cls) el.classList.add(cls);

    // -- Apply custom CSS vars only when preset === custom --
    if (widget === 'alert') {
        // --p-bg and --p-border must be set directly on .alert-mock-box because
        // style-variant rules (e.g. .style-glass .alert-mock-box { --p-bg: ... })
        // re-declare these vars on the child element, which would shadow any value
        // set on the parent wrapper via CSS variable inheritance.
        var mockBox = el.querySelector('.alert-mock-box');
        var bgTarget = mockBox || el;
        bgTarget.style.setProperty('--p-bg',     isCustom ? (st.bg     || '') : '');
        bgTarget.style.setProperty('--p-border', isCustom ? (st.border || '') : '');
        el.style.setProperty('--p-accent',   isCustom ? (st.accent   || '') : '');
        el.style.setProperty('--p-accent2',  isCustom ? (st.accent2  || '') : '');
        el.style.setProperty('--p-amount',   isCustom ? (st.amount_color || '') : '');
        el.style.setProperty('--p-donor',    isCustom ? (st.donor_color  || '') : '');
        el.style.setProperty('--p-top-line', isCustom ? (st.top_line || '') : '');
        el.style.setProperty('--p-prog-bar', isCustom ? (st.prog_bar || '') : '');
        if (isCustom && st.radius) el.style.setProperty('--p-radius', st.radius + 'px');
        else el.style.removeProperty('--p-radius');
        // Layout classes
        var allLayouts = ['layout-classic','layout-centered','layout-side'];
        allLayouts.forEach(function(c) { el.classList.remove(c); });
        el.classList.add('layout-' + (st.layout || 'classic'));
        // Style classes
        var allStyles = ['style-glass','style-solid','style-neon','style-minimal','style-retro','style-frosted'];
        allStyles.forEach(function(c) { el.classList.remove(c); });
        el.classList.add('style-' + (st.style || 'glass'));
        // Font family
        var fontFamilyMap = {
            'inter':         "'Inter', sans-serif",
            'space-grotesk': "'Space Grotesk', sans-serif",
            'plus-jakarta':  "'Plus Jakarta Sans', sans-serif",
            'poppins':       "'Poppins', sans-serif",
            'nunito':        "'Nunito', sans-serif",
        };
        el.style.setProperty('--p-font-family', fontFamilyMap[st.font_family || 'inter'] || "'Inter', sans-serif");
        // Font sizes
        el.style.setProperty('--p-font-size-title',  (st.font_size_title  || 17) + 'px');
        el.style.setProperty('--p-font-size-amount', (st.font_size_amount || 24) + 'px');
        el.style.setProperty('--p-font-size-msg',    (st.font_size_msg    || 13) + 'px');
        // Spacing (fallback 18px)
        var spacingPx = {'1':'12px','2':'18px','3':'26px'};
        el.style.setProperty('--p-spacing', spacingPx[String(st.spacing || '2')] || '18px');
        // Card opacity
        el.style.setProperty('--p-card-opacity', (parseInt(st.card_opacity || '96', 10) / 100).toFixed(2));
    } else if (widget === 'milestone') {
        el.style.setProperty('--p-surface', isCustom ? (st.surface || '') : '');
        el.style.setProperty('--p-border',  isCustom ? (st.border  || '') : '');
        el.style.setProperty('--p-brand',   isCustom ? (st.brand   || '') : '');
        el.style.setProperty('--p-brand2',  isCustom ? (st.brand2  || '') : '');
        el.style.setProperty('--p-orange',  isCustom ? (st.orange  || '') : '');
        el.style.setProperty('--p-green',   isCustom ? (st.green   || '') : '');
        if (isCustom && st.radius) el.style.setProperty('--p-radius', st.radius + 'px');
        else el.style.removeProperty('--p-radius');
    } else if (widget === 'leaderboard') {
        el.style.setProperty('--p-surface', isCustom ? (st.surface || '') : '');
        el.style.setProperty('--p-border',  isCustom ? (st.border  || '') : '');
        el.style.setProperty('--p-brand',   isCustom ? (st.brand   || '') : '');
        el.style.setProperty('--p-brand2',  isCustom ? (st.brand2  || '') : '');
        el.style.setProperty('--p-yellow',  isCustom ? (st.yellow  || '') : '');
        el.style.setProperty('--p-green',   isCustom ? (st.green   || '') : '');
        if (isCustom && st.radius) el.style.setProperty('--p-radius', st.radius + 'px');
        else el.style.removeProperty('--p-radius');
    } else if (widget === 'qr') {
        el.style.setProperty('--p-surface', isCustom ? (st.surface || '') : '');
        el.style.setProperty('--p-border',  isCustom ? (st.border  || '') : '');
        el.style.setProperty('--p-brand',   isCustom ? (st.brand   || '') : '');
        el.style.setProperty('--p-brand2',  isCustom ? (st.brand2  || '') : '');
        if (isCustom && st.radius) el.style.setProperty('--p-radius', st.radius + 'px');
        else el.style.removeProperty('--p-radius');
    }
}

/* Initialise all previews on page load */
document.addEventListener('DOMContentLoaded', function () {
    ['alert','milestone','leaderboard','qr'].forEach(function(w) {
        wcUpdatePreview(w);
    });
});

/* ══════════════════════════════════════════════
   ALERT SETTINGS — wasState + helpers
   Card #alert-settings-card
══════════════════════════════════════════════ */

var wasState = {
    sound_enabled:        {{ $streamer->sound_enabled ? 'true' : 'false' }},
    notification_sound:   {!! json_encode($streamer->notification_sound ?? 'ding') !!},
    yt_enabled:           {{ $streamer->yt_enabled ? 'true' : 'false' }},
    alert_max_duration:   {{ (int) min($alertMaxDur, 120) }},
    alert_duration_tiers: {!! json_encode($alertTiers) !!},
};

/* ── Audio Context for preview (shared with overlay pattern) ── */
var _wasAudioCtx = null;
function _wasGetAudioCtx() {
    if (!_wasAudioCtx) _wasAudioCtx = new (window.AudioContext || window.webkitAudioContext)();
    if (_wasAudioCtx.state === 'suspended') _wasAudioCtx.resume();
    return _wasAudioCtx;
}

/* ── Preview synthesized sound ── */
function wasPreviewSound(key) {
    try {
        switch (key) {
            case 'ding':       _wasPlayDing();       break;
            case 'coin':       _wasPlayCoin();       break;
            case 'whoosh':     _wasPlayWhoosh();     break;
            case 'chime':      _wasPlayChime();      break;
            case 'pop':        _wasPlayPop();        break;
            case 'tada':       _wasPlayTada();       break;
            case 'woosh_light':_wasPlayWooshLight(); break;
            case 'blip':       _wasPlayBlip();       break;
            case 'sparkle':    _wasPlaySparkle();    break;
            case 'fanfare':    _wasPlayFanfare();    break;
            default:           _wasPlayDing();
        }
    } catch(e) { console.warn('wasPreviewSound error', e); }
}

function _wasPlayDing() {
    var ctx = _wasGetAudioCtx();
    var osc = ctx.createOscillator(), gain = ctx.createGain();
    osc.connect(gain); gain.connect(ctx.destination);
    osc.type = 'sine';
    osc.frequency.setValueAtTime(880, ctx.currentTime);
    osc.frequency.exponentialRampToValueAtTime(1320, ctx.currentTime + 0.05);
    osc.frequency.exponentialRampToValueAtTime(660, ctx.currentTime + 0.4);
    gain.gain.setValueAtTime(0.6, ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.6);
    osc.start(ctx.currentTime); osc.stop(ctx.currentTime + 0.6);
}
function _wasPlayCoin() {
    var ctx = _wasGetAudioCtx();
    [0, 0.12].forEach(function(delay) {
        var osc = ctx.createOscillator(), gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.type = 'square';
        osc.frequency.setValueAtTime(988, ctx.currentTime + delay);
        osc.frequency.setValueAtTime(1319, ctx.currentTime + delay + 0.07);
        gain.gain.setValueAtTime(0.35, ctx.currentTime + delay);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + delay + 0.3);
        osc.start(ctx.currentTime + delay); osc.stop(ctx.currentTime + delay + 0.3);
    });
}
function _wasPlayWhoosh() {
    var ctx = _wasGetAudioCtx();
    var bufSize = ctx.sampleRate * 0.6;
    var buf = ctx.createBuffer(1, bufSize, ctx.sampleRate);
    var data = buf.getChannelData(0);
    for (var i = 0; i < bufSize; i++) data[i] = (Math.random() * 2 - 1);
    var source = ctx.createBufferSource(); source.buffer = buf;
    var filter = ctx.createBiquadFilter();
    filter.type = 'bandpass';
    filter.frequency.setValueAtTime(400, ctx.currentTime);
    filter.frequency.exponentialRampToValueAtTime(2000, ctx.currentTime + 0.2);
    filter.frequency.exponentialRampToValueAtTime(200, ctx.currentTime + 0.6);
    filter.Q.value = 0.8;
    var gain = ctx.createGain();
    gain.gain.setValueAtTime(0, ctx.currentTime);
    gain.gain.linearRampToValueAtTime(0.5, ctx.currentTime + 0.05);
    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.6);
    source.connect(filter); filter.connect(gain); gain.connect(ctx.destination);
    source.start(ctx.currentTime); source.stop(ctx.currentTime + 0.6);
}
function _wasPlayChime() {
    var ctx = _wasGetAudioCtx();
    var notes = [1047, 1319, 1568, 2093];
    notes.forEach(function(freq, i) {
        var delay = i * 0.12;
        var osc = ctx.createOscillator(), gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.type = 'sine';
        osc.frequency.setValueAtTime(freq, ctx.currentTime + delay);
        gain.gain.setValueAtTime(0.4, ctx.currentTime + delay);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + delay + 0.7);
        osc.start(ctx.currentTime + delay); osc.stop(ctx.currentTime + delay + 0.8);
    });
}
function _wasPlayPop() {
    var ctx = _wasGetAudioCtx();
    var osc = ctx.createOscillator(), gain = ctx.createGain();
    osc.connect(gain); gain.connect(ctx.destination);
    osc.type = 'sine';
    osc.frequency.setValueAtTime(400, ctx.currentTime);
    osc.frequency.exponentialRampToValueAtTime(80, ctx.currentTime + 0.08);
    gain.gain.setValueAtTime(0.7, ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.12);
    osc.start(ctx.currentTime); osc.stop(ctx.currentTime + 0.14);
}
function _wasPlayTada() {
    var ctx = _wasGetAudioCtx();
    var notes = [523, 659, 784, 1047, 1319];
    notes.forEach(function(freq, i) {
        var delay = i * 0.07;
        var osc = ctx.createOscillator(), gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.type = 'triangle';
        osc.frequency.setValueAtTime(freq, ctx.currentTime + delay);
        gain.gain.setValueAtTime(0.35, ctx.currentTime + delay);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + delay + 0.35);
        osc.start(ctx.currentTime + delay); osc.stop(ctx.currentTime + delay + 0.4);
    });
}
function _wasPlayWooshLight() {
    var ctx = _wasGetAudioCtx();
    var bufSize = ctx.sampleRate * 0.35;
    var buf = ctx.createBuffer(1, bufSize, ctx.sampleRate);
    var data = buf.getChannelData(0);
    for (var i = 0; i < bufSize; i++) data[i] = (Math.random() * 2 - 1);
    var source = ctx.createBufferSource(); source.buffer = buf;
    var filter = ctx.createBiquadFilter();
    filter.type = 'highpass';
    filter.frequency.setValueAtTime(1200, ctx.currentTime);
    filter.frequency.exponentialRampToValueAtTime(4000, ctx.currentTime + 0.15);
    filter.frequency.exponentialRampToValueAtTime(800, ctx.currentTime + 0.35);
    var gain = ctx.createGain();
    gain.gain.setValueAtTime(0, ctx.currentTime);
    gain.gain.linearRampToValueAtTime(0.3, ctx.currentTime + 0.04);
    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);
    source.connect(filter); filter.connect(gain); gain.connect(ctx.destination);
    source.start(ctx.currentTime); source.stop(ctx.currentTime + 0.4);
}
function _wasPlayBlip() {
    var ctx = _wasGetAudioCtx();
    var osc = ctx.createOscillator(), gain = ctx.createGain();
    osc.connect(gain); gain.connect(ctx.destination);
    osc.type = 'square';
    osc.frequency.setValueAtTime(1200, ctx.currentTime);
    osc.frequency.setValueAtTime(1600, ctx.currentTime + 0.05);
    gain.gain.setValueAtTime(0.25, ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.15);
    osc.start(ctx.currentTime); osc.stop(ctx.currentTime + 0.18);
}
function _wasPlaySparkle() {
    var ctx = _wasGetAudioCtx();
    for (var i = 0; i < 5; i++) {
        (function(i) {
            var delay = i * 0.06;
            var freq  = 1400 + Math.random() * 1200;
            var osc   = ctx.createOscillator(), gain = ctx.createGain();
            osc.connect(gain); gain.connect(ctx.destination);
            osc.type = 'sine';
            osc.frequency.setValueAtTime(freq, ctx.currentTime + delay);
            gain.gain.setValueAtTime(0.2, ctx.currentTime + delay);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + delay + 0.25);
            osc.start(ctx.currentTime + delay); osc.stop(ctx.currentTime + delay + 0.3);
        })(i);
    }
}
function _wasPlayFanfare() {
    var ctx = _wasGetAudioCtx();
    var sequence = [523, 659, 784, 659, 1047];
    var timing   = [0, 0.1, 0.2, 0.32, 0.44];
    sequence.forEach(function(freq, i) {
        var delay = timing[i];
        var osc   = ctx.createOscillator(), gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.type = 'sawtooth';
        osc.frequency.setValueAtTime(freq, ctx.currentTime + delay);
        gain.gain.setValueAtTime(0.28, ctx.currentTime + delay);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + delay + 0.3);
        osc.start(ctx.currentTime + delay); osc.stop(ctx.currentTime + delay + 0.35);
    });
}

/* ── Sound toggle ── */
function wasSoundToggle(input) {
    wasState.sound_enabled = input.checked;
}

/* ── Select a sound preset card ── */
function wasSelectSound(key, card) {
    document.querySelectorAll('#was-sound-grid .was-sound-card').forEach(function(c) {
        c.classList.remove('active');
    });
    card.classList.add('active');
    wasState.notification_sound = key;
}

/* ── YouTube toggle ── */
function wasYtToggle(input) {
    wasState.yt_enabled = input.checked;
}

/* ── Tier threshold input ── */
function wasTierFromChange(ti, input) {
    var val = parseInt(input.value, 10) || 0;
    wasState.alert_duration_tiers[ti].from = val;
}

/* ── Tier duration slider ── */
function wasTierDurChange(ti, input) {
    var val = Math.min(parseInt(input.value, 10) || 1, wasState.alert_max_duration);
    input.value = val;
    var label = document.getElementById('was-tier-durval-' + ti);
    if (label) label.textContent = val + 's';
    wasState.alert_duration_tiers[ti].duration = val;
}

/* ── Max duration input — clamp all tier sliders ── */
function wasMaxDurChange(input) {
    var val = Math.min(Math.max(parseInt(input.value, 10) || 5, 5), 120);
    wasState.alert_max_duration = val;
    // Re-clamp every tier slider
    wasState.alert_duration_tiers.forEach(function(tier, ti) {
        var slider = document.getElementById('was-tier-slider-' + ti);
        var label  = document.getElementById('was-tier-durval-' + ti);
        if (slider) {
            slider.max = val;
            var clamped = Math.min(parseInt(slider.value, 10) || 1, val);
            slider.value = clamped;
            wasState.alert_duration_tiers[ti].duration = clamped;
            if (label) label.textContent = clamped + 's';
        }
    });
}

/* ── Save to server ── */
function wasSave() {
    var btn = document.getElementById('was-save-btn');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="iconify" data-icon="solar:loading-bold-duotone"></span> Menyimpan\u2026';
    }

    fetch('{{ route("streamer.widgets.alert-settings") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            sound_enabled:        wasState.sound_enabled,
            notification_sound:   wasState.notification_sound,
            yt_enabled:           wasState.yt_enabled,
            alert_max_duration:   wasState.alert_max_duration,
            alert_duration_tiers: wasState.alert_duration_tiers,
        }),
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (res.ok) {
            wsToast('Pengaturan alert berhasil disimpan!', 'success');
        } else {
            wsToast('Gagal menyimpan: ' + (res.error || 'unknown error'), 'error');
        }
    })
    .catch(function() { wsToast('Koneksi gagal. Coba lagi.', 'error'); })
    .finally(function() {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<span class="iconify" data-icon="solar:floppy-disk-bold"></span> Simpan Pengaturan';
        }
    });
}

/* ── Subathon Timer Functions ── */
let subathonTierCount = {{ count($subathonValues) }};

function addSubathonTier() {
    var container = document.getElementById('subathon-tiers');
    var html = '<div class="ws-tier-row">' +
        '<span>Donasi Rp</span>' +
        '<input type="number" class="ws-tier-from" value="0" min="0" step="1000" data-idx="' + subathonTierCount + '">' +
        '<span>→ Tambah</span>' +
        '<input type="number" class="ws-tier-to" value="1" min="1" max="60" data-idx="' + subathonTierCount + '">' +
        '<span>menit</span>' +
        '<button type="button" class="ws-tier-remove" onclick="removeSubathonTier(this)">' +
        '<span class="iconify" data-icon="solar:trash-bold"></span></button></div>';
    container.insertAdjacentHTML('beforeend', html);
    subathonTierCount++;
}

function removeSubathonTier(btn) {
    var row = btn.closest('.ws-tier-row');
    if (document.querySelectorAll('#subathon-tiers .ws-tier-row').length > 1) {
        row.remove();
    }
}

function saveSubathonSettings() {
    var enabled = document.getElementById('subathon-enabled').checked;
    var duration = parseInt(document.getElementById('subathon-duration').value) || 60;
    
    var tiers = [];
    document.querySelectorAll('#subathon-tiers .ws-tier-row').forEach(function(row) {
        var from = parseInt(row.querySelector('.ws-tier-from').value) || 0;
        var minutes = parseInt(row.querySelector('.ws-tier-to').value) || 1;
        tiers.push({ from: from, minutes: minutes });
    });
    
    var btn = document.querySelector('#msg-subathon-settings').previousElementSibling;
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="iconify spin" data-icon="solar:spinner-bold-duotone"></span> Menyimpan...';
    }
    
    fetch('{{ route("streamer.subathon.save") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            subathon_enabled: enabled,
            subathon_duration_minutes: duration,
            subathon_additional_values: tiers
        })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (res.ok) {
            wsToast('Pengaturan Subathon berhasil disimpan!', 'success');
        } else {
            wsToast('Gagal menyimpan: ' + (res.error || 'unknown error'), 'error');
        }
    })
    .catch(function() { wsToast('Koneksi gagal. Coba lagi.', 'error'); })
    .finally(function() {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<span class="iconify" data-icon="solar:floppy-disk-bold"></span> Simpan Pengaturan';
        }
    });
}

function resetSubathonTimer() {
    if (!confirm('Reset timer ke durasi default?')) return;
    
    fetch('{{ route("streamer.subathon.reset-timer") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (res.ok) {
            document.getElementById('ws-timer-current').textContent = res.timer;
            wsToast('Timer di-reset!', 'success');
        }
    });
}

function addSubathonTimeManual() {
    var mins = prompt('Berapa menit yang ingin ditambahkan?', '10');
    if (!mins || isNaN(parseInt(mins))) return;
    
    fetch('{{ route("streamer.subathon.add-time") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ minutes: parseInt(mins) })
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (res.ok) {
            document.getElementById('ws-timer-current').textContent = res.timer;
            wsToast('+' + mins + ' menit ditambahkan!', 'success');
        }
    });
}

function saveRunningTextSettings() {
    var enabled = document.getElementById('running-text-enabled').checked;
    var text = document.getElementById('running-text-content').value;
    var speed = parseInt(document.getElementById('running-text-speed').value) || 50;
    var direction = document.getElementById('running-text-direction').value;
    var fontSize = parseInt(document.getElementById('running-text-font-size').value) || 18;
    var textColor = document.getElementById('running-text-color-text').value;
    var bgColor = document.getElementById('running-text-color-bg').value;
    
    var btn = document.querySelector('#msg-running-text').previousElementSibling;
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="iconify spin" data-icon="solar:spinner-bold-duotone"></span> Menyimpan...';
    }
    
    var payload = {
        enabled: enabled,
        text: text,
        speed: speed,
        direction: direction,
        font_size: fontSize,
        text: textColor,
        bg: bgColor,
        preset: 'default'
    };
    
    fetch('{{ route("streamer.widgets.save") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ widget: 'running_text', data: payload }),
    })
    .then(function(r) { return r.json(); })
    .then(function(res) {
        if (res.ok) {
            wsToast('Pengaturan Running Text berhasil disimpan!', 'success');
        } else {
            wsToast('Gagal menyimpan: ' + (res.error || 'unknown error'), 'error');
        }
    })
    .catch(function() { wsToast('Koneksi gagal. Coba lagi.', 'error'); })
    .finally(function() {
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<span class="iconify" data-icon="solar:floppy-disk-bold"></span> Simpan Pengaturan';
        }
    });
}
</script>

{{-- Toast element (shared) --}}
<div id="ws-toast" role="alert">
    <span class="iconify" data-icon="solar:check-circle-bold" style="width:16px;height:16px;flex-shrink:0"></span>
    <span class="ws-toast-msg"></span>
</div>
@endpush

</x-app-layout>
