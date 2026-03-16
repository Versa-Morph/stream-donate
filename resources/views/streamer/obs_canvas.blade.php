<x-app-layout>
@push('styles')
<style>
/* ── Layout ── */
.canvas-wrap { padding: 24px 28px; max-width: 1400px; margin: 0 auto; }
.canvas-header { display: flex; align-items: flex-start; justify-content: space-between; gap: 16px; margin-bottom: 24px; flex-wrap: wrap; }
.canvas-title { font-family: 'Space Grotesk', sans-serif; font-size: 22px; font-weight: 700; letter-spacing: -.5px; margin-bottom: 3px; }
.canvas-sub { font-size: 13px; color: var(--text-3); }

/* ── Main grid: sidebar kiri + canvas kanan ── */
.canvas-editor { display: grid; grid-template-columns: 280px 1fr; gap: 20px; align-items: start; }

/* ── Sidebar ── */
.canvas-sidebar { display: flex; flex-direction: column; gap: 14px; position: sticky; top: 80px; }
.sidebar-card {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius-lg); padding: 18px;
}
.sidebar-card-title {
    font-family: 'Space Grotesk', sans-serif; font-size: 13px; font-weight: 700;
    letter-spacing: -.2px; margin-bottom: 14px; color: var(--text);
    display: flex; align-items: center; gap: 8px;
}
.sidebar-card-title .iconify { width: 16px; height: 16px; color: var(--brand-light); }

/* ── Resolution selector ── */
.res-select-wrap { position: relative; }
.res-select {
    width: 100%; padding: 9px 32px 9px 11px;
    border-radius: var(--radius); border: 1px solid var(--border);
    background: var(--surface-2); color: var(--text);
    font-family: 'Space Grotesk', sans-serif; font-size: 12px; font-weight: 600;
    cursor: pointer; appearance: none; -webkit-appearance: none;
    transition: border-color .15s, background .15s;
    outline: none;
}
.res-select:hover  { border-color: var(--border-2); background: var(--surface-3); }
.res-select:focus  { border-color: var(--brand); background: var(--surface-3); }
.res-select option { background: #1a1a24; color: var(--text); }
.res-select-arrow {
    position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
    pointer-events: none; color: var(--text-3);
    width: 14px; height: 14px;
}

/* ── Custom resolution inputs ── */
.res-custom {
    display: none; margin-top: 10px;
    gap: 8px; align-items: center;
}
.res-custom.visible { display: flex; }
.res-custom-input {
    flex: 1; padding: 8px 10px;
    border-radius: var(--radius); border: 1px solid var(--border);
    background: var(--surface-2); color: var(--text);
    font-family: monospace; font-size: 13px; font-weight: 600;
    text-align: center; outline: none;
    transition: border-color .15s;
}
.res-custom-input:focus { border-color: var(--brand); background: var(--surface-3); }
.res-custom-sep { font-size: 14px; font-weight: 700; color: var(--text-3); flex-shrink: 0; }
.res-custom-apply {
    padding: 8px 12px; border-radius: var(--radius);
    border: 1px solid var(--brand); background: rgba(124,108,252,.12);
    color: var(--brand-light); font-size: 11px; font-weight: 700;
    cursor: pointer; white-space: nowrap; transition: all .15s; flex-shrink: 0;
}
.res-custom-apply:hover { background: rgba(124,108,252,.22); }

/* ── Screen detect info ── */
.cam-status {
    margin-top: 8px; padding: 7px 10px;
    border-radius: var(--radius); font-size: 11px;
    display: flex; align-items: center; gap: 8px;
    line-height: 1.4;
    background: rgba(34,211,160,.06); border: 1px solid rgba(34,211,160,.15);
    color: rgba(34,211,160,.8);
}
.cam-status .cam-dot {
    width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0;
    background: #22d3a0;
}

/* ── Widget toggles ── */
.widget-toggle-list { display: flex; flex-direction: column; gap: 10px; }
.widget-row {
    display: flex; align-items: center; gap: 12px;
    padding: 10px 12px; border-radius: var(--radius);
    border: 1px solid var(--border); background: var(--surface-2);
    transition: border-color .15s;
}
.widget-row.is-active { border-color: rgba(124,108,252,.3); background: rgba(124,108,252,.06); }
.widget-icon {
    width: 32px; height: 32px; border-radius: 9px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px;
}
.widget-icon.notif  { background: rgba(249,115,22,.12); border: 1px solid rgba(249,115,22,.2); }
.widget-icon.lb     { background: rgba(251,191,36,.1);  border: 1px solid rgba(251,191,36,.2); }
.widget-icon.ms     { background: rgba(34,211,160,.1);  border: 1px solid rgba(34,211,160,.2); }
.widget-icon.qr     { background: rgba(124,108,252,.12); border: 1px solid rgba(124,108,252,.2); }
.widget-icon.subathon { background: linear-gradient(135deg, rgba(249,115,22,.15), rgba(251,191,36,.1)); border: 1px solid rgba(249,115,22,.25); }
.widget-icon.rt     { background: rgba(124,108,252,.12); border: 1px solid rgba(124,108,252,.2); }
.widget-info { flex: 1; }
.widget-name { font-size: 12px; font-weight: 600; color: var(--text); }
.widget-size-display { font-size: 10px; color: var(--text-3); margin-top: 2px; font-family: monospace; }

/* ── Toggle switch ── */
.toggle-switch { position: relative; width: 34px; height: 20px; flex-shrink: 0; }
.toggle-switch input { opacity: 0; width: 0; height: 0; }
.toggle-slider {
    position: absolute; inset: 0; cursor: pointer;
    background: var(--surface-3); border-radius: 20px;
    border: 1px solid var(--border);
    transition: background .2s, border-color .2s;
}
.toggle-slider::before {
    content: ''; position: absolute;
    width: 14px; height: 14px; border-radius: 50%;
    left: 2px; top: 2px;
    background: var(--text-3);
    transition: transform .2s, background .2s;
}
.toggle-switch input:checked + .toggle-slider { background: rgba(124,108,252,.3); border-color: var(--brand); }
.toggle-switch input:checked + .toggle-slider::before { transform: translateX(14px); background: var(--brand-light); }

/* ── URL box ── */
.url-box {
    background: var(--surface-2); border: 1px solid var(--border);
    border-radius: var(--radius); padding: 10px 12px;
    font-size: 11px; font-family: monospace; color: var(--brand-light);
    word-break: break-all; line-height: 1.5; margin-bottom: 10px;
}
.btn-copy {
    width: 100%; padding: 9px; border-radius: var(--radius);
    border: 1px solid var(--border); background: var(--surface-2);
    color: var(--text-2); font-size: 12px; font-weight: 600; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 6px;
    transition: all .15s;
}
.btn-copy:hover { border-color: var(--brand); color: var(--brand-light); background: rgba(124,108,252,.08); }
.btn-copy .iconify { width: 14px; height: 14px; }

.btn-save {
    width: 100%; padding: 11px; border: none; border-radius: var(--radius);
    background: linear-gradient(135deg, var(--brand), #6356e8);
    color: #fff; font-family: 'Inter', sans-serif;
    font-size: 13px; font-weight: 700; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: all .2s; box-shadow: 0 4px 16px var(--brand-glow);
}
.btn-save:hover:not(:disabled) { transform: translateY(-1px); box-shadow: 0 6px 24px var(--brand-glow); }
.btn-save:disabled { opacity: .55; cursor: not-allowed; }
.btn-save .iconify { width: 16px; height: 16px; }

/* ── Canvas container ── */
.canvas-stage-wrap {
    background: var(--surface); border: 1px solid var(--border);
    border-radius: var(--radius-lg); padding: 20px; overflow: hidden;
}
.canvas-stage-label {
    font-size: 11px; font-weight: 600; color: var(--text-3); letter-spacing: .4px;
    text-transform: uppercase; margin-bottom: 14px;
    display: flex; align-items: center; gap: 8px;
}
.canvas-stage-label .res-chip {
    background: var(--surface-3); border: 1px solid var(--border);
    padding: 2px 8px; border-radius: 20px; font-size: 10px; font-weight: 700;
    color: var(--brand-light); font-family: monospace;
}

/* ── Canvas surface (scaled) ── */
.canvas-surface-outer {
    /* Lebar SELALU mengikuti card — tidak boleh melebar melebihi container */
    width: 100%;
    max-width: 100%;
    overflow: hidden;
    border-radius: 10px; border: 1px solid var(--border);
    background: repeating-linear-gradient(
        45deg,
        rgba(255,255,255,.015) 0px, rgba(255,255,255,.015) 1px,
        transparent 1px, transparent 10px
    ),
    rgba(0,0,0,.4);
    /* height di-set via JS sesuai aspect ratio */
    position: relative;
    box-sizing: border-box;
}
.canvas-surface {
    /* Ukuran asli resolusi, di-scale via transform — tidak mempengaruhi layout parent */
    position: absolute;
    top: 0; left: 0;
    transform-origin: top left;
    background: transparent;
    overflow: hidden;
    /* width & height di-set via JS */
}

/* ── Widget boxes (draggable/resizable) ── */
.wbox {
    position: absolute;
    border: 2px dashed rgba(255,255,255,.2);
    border-radius: 10px;
    cursor: move;
    user-select: none;
    display: flex; flex-direction: column;
    transition: border-color .15s, box-shadow .15s;
    box-sizing: border-box;
    overflow: hidden;
}
.wbox:hover { border-color: rgba(124,108,252,.5); }
.wbox.dragging { border-color: var(--brand); box-shadow: 0 0 0 1px var(--brand), 0 8px 32px rgba(124,108,252,.3); z-index: 100; cursor: grabbing; }
.wbox.resizing { border-color: var(--orange); box-shadow: 0 0 0 1px var(--orange), 0 8px 32px rgba(249,115,22,.25); z-index: 100; }

.wbox[data-inactive] {
    display: none;
}

/* Widget header bar */
.wbox-header {
    background: rgba(0,0,0,.55);
    padding: 4px 8px 4px 10px;
    font-size: 10px; font-weight: 700; letter-spacing: .5px;
    color: rgba(255,255,255,.7); text-transform: uppercase;
    display: flex; align-items: center; justify-content: space-between;
    flex-shrink: 0;
    backdrop-filter: blur(4px);
}
.wbox-header-label { display: flex; align-items: center; gap: 5px; }
.wbox-coords {
    font-family: monospace; font-size: 9px; color: rgba(255,255,255,.4);
    letter-spacing: 0;
}

/* Widget content area */
.wbox-content {
    flex: 1; overflow: hidden;
    display: flex; align-items: center; justify-content: center;
    position: relative;
}

/* Notification preview */
.preview-notif {
    width: 100%; height: 100%;
    background: rgba(8,8,12,.9);
    border-top: 2px solid transparent;
    border-image: linear-gradient(90deg,#7c6cfc,#a855f7,#22d3a0) 1;
    display: flex; flex-direction: column; gap: 6px; padding: 10px 12px; box-sizing: border-box;
}
.preview-notif-header { display: flex; align-items: center; gap: 8px; }
.preview-notif-avatar { width: 28px; height: 28px; border-radius: 7px; background: rgba(124,108,252,.15); border: 1px solid rgba(124,108,252,.2); display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; }
.preview-notif-meta { flex: 1; }
.preview-notif-name { font-size: 10px; font-weight: 700; color: #f1f1f6; }
.preview-notif-amount { font-family: 'Space Grotesk', sans-serif; font-size: 14px; font-weight: 700; color: #f97316; }
.preview-notif-msg { font-size: 9px; color: rgba(241,241,246,.5); padding-top: 4px; border-top: 1px solid rgba(255,255,255,.06); }
.preview-notif-bar { height: 2px; background: linear-gradient(90deg,#7c6cfc,#f97316); border-radius: 1px; margin-top: auto; }

/* Leaderboard preview */
.preview-lb {
    width: 100%; height: 100%;
    background: rgba(8,8,12,.9);
    display: flex; flex-direction: column;
    border-top: 2px solid transparent;
    border-image: linear-gradient(90deg,#7c6cfc,#a855f7,#22d3a0) 1;
}
.preview-lb-header { padding: 8px 10px 6px; border-bottom: 1px solid rgba(255,255,255,.07); }
.preview-lb-live { font-size: 8px; font-weight: 800; color: #a99dff; letter-spacing: 1.2px; margin-bottom: 3px; }
.preview-lb-title { font-family: 'Space Grotesk', sans-serif; font-size: 12px; font-weight: 700; color: #f1f1f6; }
.preview-lb-list { flex: 1; overflow: hidden; padding: 4px 0; }
.preview-lb-item { display: flex; align-items: center; gap: 6px; padding: 4px 10px; }
.preview-lb-rank { font-size: 12px; width: 16px; flex-shrink: 0; }
.preview-lb-name { font-size: 9px; font-weight: 600; color: #f1f1f6; flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.preview-lb-amt  { font-family: 'Space Grotesk', sans-serif; font-size: 9px; font-weight: 700; color: #fbbf24; }
.preview-lb-footer { padding: 5px 10px; border-top: 1px solid rgba(255,255,255,.07); display: flex; justify-content: space-between; }
.preview-lb-footer-label { font-size: 8px; color: rgba(241,241,246,.35); font-weight: 700; letter-spacing: .5px; text-transform: uppercase; }
.preview-lb-footer-val   { font-family: 'Space Grotesk', sans-serif; font-size: 11px; font-weight: 700; color: #22d3a0; }

/* Milestone preview */
.preview-ms {
    width: 100%; height: 100%;
    background: rgba(8,8,12,.9);
    padding: 10px 12px; box-sizing: border-box;
    display: flex; flex-direction: column; gap: 6px;
    border-top: 2px solid transparent;
    border-image: linear-gradient(90deg,#7c6cfc,#a855f7,#22d3a0) 1;
}
.preview-ms-badge { font-size: 8px; font-weight: 800; color: #a99dff; letter-spacing: 1px; }
.preview-ms-title { font-family: 'Space Grotesk', sans-serif; font-size: 11px; font-weight: 700; color: #f1f1f6; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.preview-ms-amounts { display: flex; align-items: baseline; gap: 4px; }
.preview-ms-curr { font-family: 'Space Grotesk', sans-serif; font-size: 16px; font-weight: 700; color: #f97316; }
.preview-ms-sep  { font-size: 10px; color: rgba(241,241,246,.35); }
.preview-ms-tgt  { font-family: 'Space Grotesk', sans-serif; font-size: 12px; font-weight: 600; color: rgba(241,241,246,.5); }
.preview-ms-pct  { margin-left: auto; font-family: 'Space Grotesk', sans-serif; font-size: 12px; font-weight: 700; color: #a99dff; }
.preview-ms-track { height: 6px; background: rgba(255,255,255,.07); border-radius: 3px; overflow: hidden; }
.preview-ms-bar { height: 100%; width: 45%; background: linear-gradient(90deg, #7c6cfc, #a855f7, #f97316); border-radius: 3px; }

/* QR preview */
.preview-qr {
    width: 100%; height: 100%;
    background: rgba(10,10,16,.93);
    border: 1px solid rgba(124,108,252,.28);
    border-radius: 14px; box-sizing: border-box;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    gap: 6px; padding: 10px;
}
.preview-qr-logo { width: 22px; height: 22px; border-radius: 6px; background: linear-gradient(135deg,#7c6cfc,#6356e8); display: flex; align-items: center; justify-content: center; font-size: 8px; font-weight: 800; color: #fff; }
.preview-qr-box { width: 60%; aspect-ratio: 1; background: #141419; border-radius: 6px; display: flex; align-items: center; justify-content: center; }
.preview-qr-box .iconify { width: 50%; height: 50%; color: rgba(124,108,252,.4); }
.preview-qr-scan { font-size: 8px; font-weight: 700; color: rgba(169,157,255,.7); letter-spacing: .5px; }

/* Subathon preview */
.preview-subathon {
    width: 100%; height: 100%;
    background: rgba(8,8,12,.9);
    border-top: 2px solid transparent;
    border-image: linear-gradient(90deg,#7c6cfc,#a855f7,#22d3a0) 1;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
    padding: 12px; box-sizing: border-box;
    position: relative;
}
.preview-subathon-timer {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 28px; font-weight: 700;
    color: #fff;
    line-height: 1;
    margin-bottom: 6px;
}
.preview-subathon-label {
    font-size: 10px; font-weight: 700;
    color: rgba(255,255,255,.5);
    text-transform: uppercase;
    letter-spacing: 1px;
}
.preview-subathon-bar {
    height: 3px;
    width: 70%;
    background: linear-gradient(90deg,#7c6cfc,#a855f7);
    border-radius: 2px;
    margin-top: auto;
    position: absolute;
    bottom: 12px;
}

/* Running Text preview */
.preview-running_text {
    width: 100%; height: 100%;
    background: rgba(8,8,12,.9);
    border-top: 2px solid transparent;
    border-image: linear-gradient(90deg,#7c6cfc,#a855f7,#22d3a0) 1;
    display: flex; align-items: center;
    overflow: hidden;
}
.preview-rt-track {
    display: flex;
    align-items: center;
    white-space: nowrap;
    animation: scroll-left 8s linear infinite;
}
.preview-rt-text {
    font-family: 'Inter', sans-serif;
    font-size: 12px;
    font-weight: 600;
    color: #fff;
    padding: 0 20px;
}

/* ── Resize handle ── */
.resize-handle {
    position: absolute; bottom: 0; right: 0;
    width: 18px; height: 18px;
    cursor: se-resize;
    z-index: 10;
    display: flex; align-items: flex-end; justify-content: flex-end;
    padding: 3px;
}
.resize-handle svg { width: 10px; height: 10px; opacity: .5; }
.wbox:hover .resize-handle svg, .wbox.resizing .resize-handle svg { opacity: 1; }

/* ── Info bar di bawah canvas ── */
.canvas-info-bar {
    margin-top: 10px; padding: 8px 12px;
    background: var(--surface-2); border: 1px solid var(--border); border-radius: var(--radius);
    font-size: 11px; color: var(--text-3); display: flex; align-items: center; gap: 8px;
    flex-wrap: wrap;
}
.canvas-info-bar .iconify { width: 13px; height: 13px; flex-shrink: 0; }

/* ── Saving state ── */
.save-spinner {
    display: inline-block; width: 14px; height: 14px;
    border: 2px solid rgba(255,255,255,.3); border-top-color: #fff;
    border-radius: 50%; animation: btnSpin .6s linear infinite;
}
</style>
@endpush

<div class="canvas-wrap">
    <div class="canvas-header">
        <div>
            <div class="canvas-title">OBS Canvas</div>
            <div class="canvas-sub">Atur posisi dan ukuran semua widget dalam satu overlay untuk OBS Browser Source</div>
        </div>
    </div>

    <div class="canvas-editor">

        {{-- ── SIDEBAR ── --}}
        <div class="canvas-sidebar">

            {{-- Resolusi --}}
            <div class="sidebar-card">
                <div class="sidebar-card-title">
                    <span class="iconify" data-icon="solar:monitor-bold-duotone"></span>
                    Ukuran Layar
                </div>

                {{-- Dropdown resolusi --}}
                <div class="res-select-wrap">
                    <select class="res-select" id="res-select" onchange="onResSelectChange(this)">
                        {{-- Opsi diisi via JS saat load --}}
                    </select>
                    <svg class="res-select-arrow" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z"/>
                    </svg>
                </div>

                {{-- Input custom (muncul saat pilih "Custom") --}}
                <div class="res-custom" id="res-custom">
                    <input class="res-custom-input" type="number" id="custom-w" placeholder="1920" min="320" max="7680">
                    <span class="res-custom-sep">×</span>
                    <input class="res-custom-input" type="number" id="custom-h" placeholder="1080" min="180" max="4320">
                    <button class="res-custom-apply" onclick="applyCustomResolution()">Terapkan</button>
                </div>

                {{-- Info layar terdeteksi --}}
                <div class="cam-status" id="screen-res-info" style="display:none">
                    <span class="cam-dot"></span>
                    <span id="screen-res-text"></span>
                </div>
            </div>

            {{-- Widget Toggles --}}
            <div class="sidebar-card">
                <div class="sidebar-card-title">
                    <span class="iconify" data-icon="solar:layers-bold-duotone"></span>
                    Widget Aktif
                </div>
                <div class="widget-toggle-list">

                    <div class="widget-row" id="row-notification">
                        <div class="widget-icon notif">🔔</div>
                        <div class="widget-info">
                            <div class="widget-name">Notifikasi</div>
                            <div class="widget-size-display" id="size-notification">–</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" id="toggle-notification" onchange="toggleWidget('notification', this.checked)">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="widget-row" id="row-leaderboard">
                        <div class="widget-icon lb">🏆</div>
                        <div class="widget-info">
                            <div class="widget-name">Leaderboard</div>
                            <div class="widget-size-display" id="size-leaderboard">–</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" id="toggle-leaderboard" onchange="toggleWidget('leaderboard', this.checked)">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="widget-row" id="row-milestone">
                        <div class="widget-icon ms">🎯</div>
                        <div class="widget-info">
                            <div class="widget-name">Milestone</div>
                            <div class="widget-size-display" id="size-milestone">–</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" id="toggle-milestone" onchange="toggleWidget('milestone', this.checked)">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="widget-row" id="row-qrcode">
                        <div class="widget-icon qr">📱</div>
                        <div class="widget-info">
                            <div class="widget-name">QR Code</div>
                            <div class="widget-size-display" id="size-qrcode">–</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" id="toggle-qrcode" onchange="toggleWidget('qrcode', this.checked)">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="widget-row" id="row-subathon">
                        <div class="widget-icon subathon">⏱️</div>
                        <div class="widget-info">
                            <div class="widget-name">Subathon</div>
                            <div class="widget-size-display" id="size-subathon">–</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" id="toggle-subathon" onchange="toggleWidget('subathon', this.checked)">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="widget-row" id="row-running_text">
                        <div class="widget-icon rt">📜</div>
                        <div class="widget-info">
                            <div class="widget-name">Running Text</div>
                            <div class="widget-size-display" id="size-running_text">–</div>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" id="toggle-running_text" onchange="toggleWidget('running_text', this.checked)">
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                </div>
            </div>

            {{-- Save --}}
            <div class="sidebar-card">
                <div class="sidebar-card-title">
                    <span class="iconify" data-icon="solar:diskette-bold-duotone"></span>
                    Simpan & URL OBS
                </div>
                <button class="btn-save" id="btn-save" onclick="saveCanvas()">
                    <span class="iconify" data-icon="solar:diskette-bold-duotone"></span>
                    Simpan Konfigurasi
                </button>
                <div style="margin-top:14px">
                    <div style="font-size:11px;color:var(--text-3);font-weight:600;margin-bottom:6px;letter-spacing:.3px;text-transform:uppercase">URL untuk OBS Browser Source</div>
                    <div class="url-box" id="canvas-url">{{ route('obs.canvas', $streamer->slug) }}?key={{ $apiKey }}</div>
                    <button class="btn-copy" onclick="copyText('{{ route('obs.canvas', $streamer->slug) }}?key={{ $apiKey }}', 'URL Canvas')">
                        <span class="iconify" data-icon="solar:copy-bold-duotone"></span>
                        Salin URL
                    </button>
                </div>
                <div style="margin-top:10px;padding:10px;background:rgba(124,108,252,.06);border:1px solid rgba(124,108,252,.15);border-radius:var(--radius);font-size:11px;color:var(--text-3);line-height:1.5">
                    <strong style="color:var(--brand-light)">Tips OBS:</strong> Tambahkan Browser Source baru di OBS, tempel URL di atas, dan set ukuran sesuai resolusi yang dipilih.
                </div>
            </div>

        </div>

        {{-- ── CANVAS STAGE ── --}}
        <div>
            <div class="canvas-stage-wrap" id="canvas-stage-wrap">
                <div class="canvas-stage-label">
                    Preview Canvas
                    <span class="res-chip" id="res-chip-label">1920 × 1080</span>
                </div>

                <div class="canvas-surface-outer" id="canvas-surface-outer">
                    <div class="canvas-surface" id="canvas-surface">

                        {{-- Widget: Notification --}}
                        <div class="wbox" id="wbox-notification" data-widget="notification">
                            <div class="wbox-header">
                                <span class="wbox-header-label">🔔 Notifikasi Alert</span>
                                <span class="wbox-coords" id="coords-notification"></span>
                            </div>
                            <div class="wbox-content">
                                <div class="preview-notif">
                                    <div class="preview-notif-header">
                                        <div class="preview-notif-avatar">🎉</div>
                                        <div class="preview-notif-meta">
                                            <div class="preview-notif-name">Budi Santoso</div>
                                            <div class="preview-notif-amount">Rp 50K</div>
                                        </div>
                                        <div style="font-size:8px;font-weight:800;color:rgba(169,157,255,.6);letter-spacing:1px">DONASI MASUK</div>
                                    </div>
                                    <div class="preview-notif-msg">Semangat streamnya kak! Tetap sehat selalu 🙏</div>
                                    <div class="preview-notif-bar"></div>
                                </div>
                            </div>
                            <div class="resize-handle" data-widget="notification">
                                <svg viewBox="0 0 10 10" fill="none"><path d="M2 9L9 2M5 9L9 5M8 9L9 8" stroke="white" stroke-width="1.5" stroke-linecap="round"/></svg>
                            </div>
                        </div>

                        {{-- Widget: Leaderboard --}}
                        <div class="wbox" id="wbox-leaderboard" data-widget="leaderboard">
                            <div class="wbox-header">
                                <span class="wbox-header-label">🏆 Leaderboard</span>
                                <span class="wbox-coords" id="coords-leaderboard"></span>
                            </div>
                            <div class="wbox-content">
                                <div class="preview-lb">
                                    <div class="preview-lb-header">
                                        <div class="preview-lb-live">● LIVE</div>
                                        <div class="preview-lb-title">Top Donatur</div>
                                    </div>
                                    <div class="preview-lb-list">
                                        <div class="preview-lb-item"><span class="preview-lb-rank">🥇</span><span class="preview-lb-name">Budi Santoso</span><span class="preview-lb-amt">Rp 150K</span></div>
                                        <div class="preview-lb-item"><span class="preview-lb-rank">🥈</span><span class="preview-lb-name">Ani Wijaya</span><span class="preview-lb-amt">Rp 100K</span></div>
                                        <div class="preview-lb-item"><span class="preview-lb-rank">🥉</span><span class="preview-lb-name">Citra Dewi</span><span class="preview-lb-amt">Rp 75K</span></div>
                                        <div class="preview-lb-item"><span class="preview-lb-rank" style="font-size:9px;color:rgba(241,241,246,.35);font-weight:700">4</span><span class="preview-lb-name">Doni Saputra</span><span class="preview-lb-amt">Rp 50K</span></div>
                                    </div>
                                    <div class="preview-lb-footer">
                                        <span class="preview-lb-footer-label">Total</span>
                                        <span class="preview-lb-footer-val">Rp 375K</span>
                                    </div>
                                </div>
                            </div>
                            <div class="resize-handle" data-widget="leaderboard">
                                <svg viewBox="0 0 10 10" fill="none"><path d="M2 9L9 2M5 9L9 5M8 9L9 8" stroke="white" stroke-width="1.5" stroke-linecap="round"/></svg>
                            </div>
                        </div>

                        {{-- Widget: Milestone --}}
                        <div class="wbox" id="wbox-milestone" data-widget="milestone">
                            <div class="wbox-header">
                                <span class="wbox-header-label">🎯 Milestone</span>
                                <span class="wbox-coords" id="coords-milestone"></span>
                            </div>
                            <div class="wbox-content">
                                <div class="preview-ms">
                                    <div class="preview-ms-badge">🎯 MILESTONE</div>
                                    <div class="preview-ms-title">{{ $streamer->milestone_title ?? 'Target Stream Hari Ini' }}</div>
                                    <div class="preview-ms-amounts">
                                        <span class="preview-ms-curr">Rp 375K</span>
                                        <span class="preview-ms-sep">/</span>
                                        <span class="preview-ms-tgt">Rp 1Jt</span>
                                        <span class="preview-ms-pct">38%</span>
                                    </div>
                                    <div class="preview-ms-track"><div class="preview-ms-bar"></div></div>
                                </div>
                            </div>
                            <div class="resize-handle" data-widget="milestone">
                                <svg viewBox="0 0 10 10" fill="none"><path d="M2 9L9 2M5 9L9 5M8 9L9 8" stroke="white" stroke-width="1.5" stroke-linecap="round"/></svg>
                            </div>
                        </div>

                        {{-- Widget: QR Code --}}
                        <div class="wbox" id="wbox-qrcode" data-widget="qrcode">
                            <div class="wbox-header">
                                <span class="wbox-header-label">📱 QR Code</span>
                                <span class="wbox-coords" id="coords-qrcode"></span>
                            </div>
                            <div class="wbox-content">
                                <div class="preview-qr">
                                    <div class="preview-qr-logo">SD</div>
                                    <div class="preview-qr-box">
                                        <span class="iconify" data-icon="solar:qr-code-bold-duotone"></span>
                                    </div>
                                    <div class="preview-qr-scan">SCAN TO DONATE</div>
                                </div>
                            </div>
                            <div class="resize-handle" data-widget="qrcode">
                                <svg viewBox="0 0 10 10" fill="none"><path d="M2 9L9 2M5 9L9 5M8 9L9 8" stroke="white" stroke-width="1.5" stroke-linecap="round"/></svg>
                            </div>
                        </div>

                        {{-- Widget: Subathon --}}
                        <div class="wbox" id="wbox-subathon" data-widget="subathon">
                            <div class="wbox-header">
                                <span class="wbox-header-label">⏱️ Subathon</span>
                                <span class="wbox-coords" id="coords-subathon"></span>
                            </div>
                            <div class="wbox-content">
                                <div class="preview-subathon">
                                    <div class="preview-subathon-timer" id="preview-subathon-timer">00:00:00</div>
                                    <div class="preview-subathon-label">SISA WAKTU</div>
                                    <div class="preview-subathon-bar"></div>
                                </div>
                            </div>
                            <div class="resize-handle" data-widget="subathon">
                                <svg viewBox="0 0 10 10" fill="none"><path d="M2 9L9 2M5 9L9 5M8 9L9 8" stroke="white" stroke-width="1.5" stroke-linecap="round"/></svg>
                            </div>
                        </div>

                        {{-- Widget: Running Text --}}
                        <div class="wbox" id="wbox-running_text" data-widget="running_text">
                            <div class="wbox-header">
                                <span class="wbox-header-label">📜 Running Text</span>
                                <span class="wbox-coords" id="coords-running_text"></span>
                            </div>
                            <div class="wbox-content">
                                <div class="preview-running_text">
                                    <div class="preview-rt-track">
                                        <div class="preview-rt-text">Terima kasih donasi! Semangat terus!</div>
                                    </div>
                                </div>
                            </div>
                            <div class="resize-handle" data-widget="running_text">
                                <svg viewBox="0 0 10 10" fill="none"><path d="M2 9L9 2M5 9L9 5M8 9L9 8" stroke="white" stroke-width="1.5" stroke-linecap="round"/></svg>
                            </div>
                        </div>

                    </div>{{-- /canvas-surface --}}
                </div>{{-- /canvas-surface-outer --}}
            </div>{{-- /canvas-stage-wrap --}}

            <div class="canvas-info-bar">
                <span class="iconify" data-icon="solar:info-circle-bold-duotone"></span>
                <span><strong style="color:var(--text-2)">Drag</strong> widget untuk memindahkan posisi &bull; <strong style="color:var(--text-2)">Drag sudut kanan-bawah</strong> untuk resize &bull; Koordinat tersimpan ke resolusi yang dipilih</span>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
// ── Initial config dari server ──
const INITIAL_CONFIG = @json($canvasConfig);
const SAVE_URL = '{{ route('streamer.obs-canvas.save') }}';
const CSRF = document.querySelector('meta[name="csrf-token"]').content;

// ── State ──
let cfg = JSON.parse(JSON.stringify(INITIAL_CONFIG)); // deep clone

// Widget min sizes (dalam koordinat asli/resolusi penuh)
const MIN_W = { notification: 300, leaderboard: 180, milestone: 200, qrcode: 140, subathon: 200, running_text: 400 };
const MIN_H = { notification: 100, leaderboard: 150, milestone:  80, qrcode: 140, subathon: 100, running_text: 40 };

// ── Canvas DOM refs ──
const surface   = document.getElementById('canvas-surface');
const outer     = document.getElementById('canvas-surface-outer');
const stageWrap = document.getElementById('canvas-stage-wrap');

// ── Scale factor (koordinat asli → pixel di layar) ──
let scale = 1;

// ── Init ──
function init() {
    // Set resolusi dari config yang tersimpan (dipakai sebelum kamera terdeteksi)
    const w = cfg.width  || 1920;
    const h = cfg.height || 1080;

    applyResolution(w, h);

    // Set toggle dan posisi widget dari config
    ['notification', 'leaderboard', 'milestone', 'qrcode', 'subathon', 'running_text'].forEach(function(key) {
        const wdata = cfg.widgets[key];
        const toggle = document.getElementById('toggle-' + key);
        if (toggle) toggle.checked = !!wdata.active;
        updateRowActiveStyle(key, !!wdata.active);
        applyWidgetBox(key, wdata);
        updateSizeDisplay(key, wdata.w, wdata.h);
    });
}

// ── Preset resolusi ──
const RES_PRESETS = [
    { w: 1920, h: 1080, label: 'Full HD (FHD)' },
    { w: 1280, h:  720, label: 'HD 720p' },
    { w: 1366, h:  768, label: 'Laptop 1366×768' },
    { w: 1440, h:  900, label: 'Laptop 1440×900' },
    { w: 2560, h: 1440, label: 'QHD 2K' },
    { w: 3840, h: 2160, label: '4K UHD' },
];

// ── Bangun opsi select dari layar terdeteksi + presets ──
async function buildResSelect() {
    const sel = document.getElementById('res-select');
    const infoEl   = document.getElementById('screen-res-info');
    const infoText = document.getElementById('screen-res-text');
    sel.innerHTML = '';

    // ── Grup: Layar terdeteksi ──
    const screens = await detectAllScreens();
    let grpScreen = null;
    if (screens.length) {
        grpScreen = document.createElement('optgroup');
        grpScreen.label = 'Layar Terdeteksi';
        sel.appendChild(grpScreen);
        screens.forEach(function(s) {
            const opt = document.createElement('option');
            opt.value = s.w + 'x' + s.h;
            opt.textContent = s.label + '  (' + s.w + ' × ' + s.h + ')';
            opt.dataset.w = s.w;
            opt.dataset.h = s.h;
            grpScreen.appendChild(opt);
        });

        infoEl.style.display = 'flex';
        infoText.textContent = screens.length + ' layar terdeteksi';
    }

    // ── Grup: Preset umum ──
    const grpPreset = document.createElement('optgroup');
    grpPreset.label = 'Preset Resolusi';
    sel.appendChild(grpPreset);
    RES_PRESETS.forEach(function(p) {
        // Jangan duplikat jika sudah ada di layar terdeteksi
        const dup = Array.from(sel.querySelectorAll('option'))
            .some(function(o) { return o.dataset.w == p.w && o.dataset.h == p.h; });
        if (!dup) {
            const opt = document.createElement('option');
            opt.value = p.w + 'x' + p.h;
            opt.textContent = p.label + '  (' + p.w + ' × ' + p.h + ')';
            opt.dataset.w = p.w;
            opt.dataset.h = p.h;
            grpPreset.appendChild(opt);
        }
    });

    // ── Opsi Custom ──
    const grpCustom = document.createElement('optgroup');
    grpCustom.label = 'Lainnya';
    sel.appendChild(grpCustom);
    const optCustom = document.createElement('option');
    optCustom.value = 'custom';
    optCustom.textContent = 'Custom — masukkan sendiri…';
    grpCustom.appendChild(optCustom);

    // Pilih opsi yang sesuai config tersimpan
    const saved = cfg.width + 'x' + cfg.height;
    const match = sel.querySelector('option[value="' + saved + '"]');
    if (match) {
        sel.value = saved;
    } else if (cfg.width && cfg.height) {
        // Resolusi tersimpan tidak ada di list — tambahkan sebagai opsi custom sementara
        const optSaved = document.createElement('option');
        optSaved.value = saved;
        optSaved.textContent = 'Tersimpan  (' + cfg.width + ' × ' + cfg.height + ')';
        optSaved.dataset.w = cfg.width;
        optSaved.dataset.h = cfg.height;
        grpScreen ? grpScreen.insertBefore(optSaved, grpScreen.firstChild) : sel.insertBefore(optSaved, sel.firstChild);
        sel.value = saved;
    }
}

// ── Deteksi semua layar ──
// Coba Window.getScreenDetails() (Chrome 111+ / Edge 111+, butuh permission window-management).
// Fallback ke screen.width/height jika tidak tersedia atau ditolak.
async function detectAllScreens() {
    const seen = new Set();
    const results = [];
    const add = function(w, h, label) {
        const key = w + 'x' + h;
        if (!seen.has(key) && w > 0 && h > 0) {
            seen.add(key);
            results.push({ w: w, h: h, label: label });
        }
    };

    // Coba API multi-monitor (Chrome 111+)
    if (window.getScreenDetails) {
        try {
            const sds = await window.getScreenDetails();
            sds.screens.forEach(function(s, i) {
                const label = s.isPrimary
                    ? 'Layar Utama'
                    : ('Layar ' + (i + 1));
                add(s.width, s.height, label);
            });
            if (results.length) return results;
        } catch (e) {
            // Permission ditolak atau error — lanjut ke fallback
        }
    }

    // Fallback: screen.width/height (layar utama saja)
    add(screen.width, screen.height, 'Layar Utama');
    return results;
}

// ── Handler select berubah ──
function onResSelectChange(sel) {
    const customEl = document.getElementById('res-custom');
    if (sel.value === 'custom') {
        customEl.classList.add('visible');
        // Pre-fill dengan resolusi aktif
        document.getElementById('custom-w').value = cfg.width  || 1920;
        document.getElementById('custom-h').value = cfg.height || 1080;
        return;
    }
    customEl.classList.remove('visible');
    const opt = sel.options[sel.selectedIndex];
    const w = parseInt(opt.dataset.w);
    const h = parseInt(opt.dataset.h);
    if (w && h) applyResolution(w, h);
}

// ── Terapkan resolusi custom ──
function applyCustomResolution() {
    const w = parseInt(document.getElementById('custom-w').value) || 0;
    const h = parseInt(document.getElementById('custom-h').value) || 0;
    if (w < 320 || h < 180 || w > 7680 || h > 4320) {
        showToast('Resolusi tidak valid (min 320×180, max 7680×4320)');
        return;
    }

    // Tambahkan opsi ke select jika belum ada
    const sel = document.getElementById('res-select');
    const val = w + 'x' + h;
    let opt = sel.querySelector('option[value="' + val + '"]');
    if (!opt) {
        opt = document.createElement('option');
        opt.value = val;
        opt.textContent = 'Custom  (' + w + ' × ' + h + ')';
        opt.dataset.w = w;
        opt.dataset.h = h;
        // Sisipkan sebelum grup Custom
        const customOptgroup = Array.from(sel.querySelectorAll('optgroup'))
            .find(function(g) { return g.label === 'Lainnya'; });
        if (customOptgroup) {
            sel.insertBefore(opt, customOptgroup);
        } else {
            sel.appendChild(opt);
        }
    }
    sel.value = val;
    document.getElementById('res-custom').classList.remove('visible');
    applyResolution(w, h);
}

// ── Hitung lebar container yang tersedia untuk canvas ──
function getAvailWidth() {
    // Baca dari stageWrap (card container) dikurangi padding kiri+kanan
    // stageWrap tidak terpengaruh transform child karena surface kini position:absolute
    const wrap = stageWrap || outer.parentElement;
    const style = window.getComputedStyle(wrap);
    const padL  = parseFloat(style.paddingLeft)  || 0;
    const padR  = parseFloat(style.paddingRight) || 0;
    return Math.max(200, wrap.clientWidth - padL - padR);
}

function applyResolution(w, h) {
    cfg.width  = w;
    cfg.height = h;

    document.getElementById('res-chip-label').textContent = w + ' × ' + h;

    // Hitung scale berdasarkan lebar container yang tersedia
    const availW = getAvailWidth();
    scale = availW / w;
    const scaledH = Math.round(h * scale);

    // outer: lebar tetap availW (tidak boleh melebihi card), tinggi mengikuti aspect ratio
    outer.style.width  = availW + 'px';
    outer.style.height = scaledH + 'px';

    // Surface ukuran asli resolusi, di-scale via CSS transform (position:absolute — tidak push layout)
    surface.style.width     = w + 'px';
    surface.style.height    = h + 'px';
    surface.style.transform = 'scale(' + scale + ')';

    // Re-apply semua widget agar tidak keluar batas setelah resolusi berubah
    ['notification', 'leaderboard', 'milestone', 'qrcode'].forEach(function(key) {
        applyWidgetBox(key, cfg.widgets[key]);
    });
}

function applyWidgetBox(key, wdata) {
    const box = document.getElementById('wbox-' + key);
    if (!box) return;

    // Klem agar tidak keluar batas resolusi
    const maxX = cfg.width  - wdata.w;
    const maxY = cfg.height - wdata.h;
    const x = Math.max(0, Math.min(wdata.x, maxX));
    const y = Math.max(0, Math.min(wdata.y, maxY));
    cfg.widgets[key].x = x;
    cfg.widgets[key].y = y;

    box.style.left   = x + 'px';
    box.style.top    = y + 'px';
    box.style.width  = wdata.w + 'px';
    box.style.height = wdata.h + 'px';

    if (wdata.active) {
        box.removeAttribute('data-inactive');
    } else {
        box.setAttribute('data-inactive', '1');
    }

    updateCoords(key, x, y, wdata.w, wdata.h);
    updateSizeDisplay(key, wdata.w, wdata.h);
}

function updateCoords(key, x, y, w, h) {
    const el = document.getElementById('coords-' + key);
    if (el) el.textContent = x + ',' + y + ' | ' + w + '×' + h;
}

function updateSizeDisplay(key, w, h) {
    const el = document.getElementById('size-' + key);
    if (el) el.textContent = w + ' × ' + h + 'px';
}

function updateRowActiveStyle(key, active) {
    const row = document.getElementById('row-' + key);
    if (row) {
        if (active) row.classList.add('is-active');
        else row.classList.remove('is-active');
    }
}

// ── Toggle widget aktif/nonaktif ──
function toggleWidget(key, active) {
    cfg.widgets[key].active = active;
    applyWidgetBox(key, cfg.widgets[key]);
    updateRowActiveStyle(key, active);
}

// ── Drag & Drop + Resize (single consolidated mousedown handler) ──
let dragState   = null;
let resizeState = null;

surface.addEventListener('mousedown', function(e) {
    // ── Resize: handle klik di resize handle ──
    const handle = e.target.closest('.resize-handle');
    if (handle) {
        const key = handle.dataset.widget;
        const box = document.getElementById('wbox-' + key);
        if (!box || box.hasAttribute('data-inactive')) return;

        const wdata = cfg.widgets[key];
        resizeState = {
            key, box,
            startX: e.clientX, startY: e.clientY,
            startW: wdata.w,   startH: wdata.h,
        };
        box.classList.add('resizing');
        e.preventDefault();
        return; // jangan lanjut ke drag
    }

    // ── Drag: handle klik di wbox ──
    const box = e.target.closest('.wbox');
    if (!box || box.hasAttribute('data-inactive')) return;

    const key = box.dataset.widget;
    const wdata = cfg.widgets[key];

    // Hitung offset klik relatif terhadap sudut kiri-atas widget
    // dalam koordinat ASLI (bukan scaled), agar drag konsisten di semua resolusi
    const surfRect = surface.getBoundingClientRect();
    const clickXInSurface = (e.clientX - surfRect.left) / scale;
    const clickYInSurface = (e.clientY - surfRect.top)  / scale;
    const offX = clickXInSurface - wdata.x;
    const offY = clickYInSurface - wdata.y;

    dragState = { key, box, offX, offY };
    box.classList.add('dragging');
    e.preventDefault();
});

document.addEventListener('mousemove', function(e) {
    // ── Resize move ──
    if (resizeState) {
        const { key, box, startX, startY, startW, startH } = resizeState;

        const dxScaled = e.clientX - startX;
        const dyScaled = e.clientY - startY;

        // Konversi delta ke koordinat asli
        const dx = Math.round(dxScaled / scale);
        const dy = Math.round(dyScaled / scale);

        let newW = Math.max(MIN_W[key] || 100, startW + dx);
        let newH = Math.max(MIN_H[key] || 60,  startH + dy);

        // Klem agar tidak keluar batas
        const wdata = cfg.widgets[key];
        newW = Math.min(newW, cfg.width  - wdata.x);
        newH = Math.min(newH, cfg.height - wdata.y);

        cfg.widgets[key].w = newW;
        cfg.widgets[key].h = newH;

        box.style.width  = newW + 'px';
        box.style.height = newH + 'px';
        updateCoords(key, wdata.x, wdata.y, newW, newH);
        updateSizeDisplay(key, newW, newH);
        return;
    }

    // ── Drag move ──
    if (!dragState) return;

    const { key, box, offX, offY } = dragState;
    const surfRect = surface.getBoundingClientRect();

    // Posisi mouse dalam koordinat ASLI (bagi scale terlebih dahulu)
    const mouseXInSurface = (e.clientX - surfRect.left) / scale;
    const mouseYInSurface = (e.clientY - surfRect.top)  / scale;

    // Posisi widget = posisi mouse dikurangi offset klik
    let x = Math.round(mouseXInSurface - offX);
    let y = Math.round(mouseYInSurface - offY);

    // Klem dalam batas resolusi
    const wdata = cfg.widgets[key];
    x = Math.max(0, Math.min(x, cfg.width  - wdata.w));
    y = Math.max(0, Math.min(y, cfg.height - wdata.h));

    cfg.widgets[key].x = x;
    cfg.widgets[key].y = y;

    box.style.left = x + 'px';
    box.style.top  = y + 'px';
    updateCoords(key, x, y, wdata.w, wdata.h);
});

document.addEventListener('mouseup', function() {
    if (resizeState) {
        resizeState.box.classList.remove('resizing');
        resizeState = null;
    }
    if (dragState) {
        dragState.box.classList.remove('dragging');
        dragState = null;
    }
});

// ── Save ──
async function saveCanvas() {
    const btn = document.getElementById('btn-save');
    btn.disabled = true;
    btn.innerHTML = '<span class="save-spinner"></span> Menyimpan…';

    try {
        const payload = {
            width:   cfg.width,
            height:  cfg.height,
            widgets: {}
        };

        ['notification', 'leaderboard', 'milestone', 'qrcode', 'subathon', 'running_text'].forEach(function(key) {
            const w = cfg.widgets[key];
            payload.widgets[key] = {
                active: w.active ? true : false,
                x: Math.round(w.x),
                y: Math.round(w.y),
                w: Math.round(w.w),
                h: Math.round(w.h),
            };
        });

        const res = await fetch(SAVE_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF,
            },
            body: JSON.stringify(payload),
        });

        const data = await res.json();

        if (res.ok && data.success) {
            showToast('Konfigurasi canvas disimpan!');
        } else {
            showToast('Gagal menyimpan: ' + (data.message || 'Error tidak diketahui'));
        }
    } catch(err) {
        showToast('Gagal menyimpan. Coba lagi.');
        console.error(err);
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<span class="iconify" data-icon="solar:diskette-bold-duotone"></span> Simpan Konfigurasi';
        if (window.Iconify) window.Iconify.scan(btn);
    }
}

// ── Recalculate scale on window resize ──
let resizeTimer = null;
window.addEventListener('resize', function() {
    clearTimeout(resizeTimer);
    resizeTimer = setTimeout(function() {
        applyResolution(cfg.width, cfg.height);
    }, 60);
});

// ── ResizeObserver: scale ulang ketika lebar stage container berubah ──
// (misalnya sidebar collapse/expand, browser zoom, dll)
if (window.ResizeObserver) {
    const ro = new ResizeObserver(function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            applyResolution(cfg.width, cfg.height);
        }, 60);
    });
    ro.observe(stageWrap); // observe card wrap, bukan outer
}

// ── Run ──
document.addEventListener('DOMContentLoaded', function() {
    // Jalankan setelah satu frame agar layout sudah settle (outer.offsetWidth akurat)
    requestAnimationFrame(function() {
        init();
        // buildResSelect async: minta permission getScreenDetails lalu populate select
        (async function() {
            await buildResSelect();
        })();
    });
});
</script>
@endpush
</x-app-layout>
