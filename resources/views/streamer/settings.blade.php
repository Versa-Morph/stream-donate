<x-app-layout>
@push('styles')
<style>
/* ── Layout ── */
.settings-wrap{padding:28px 32px;max-width:1160px;margin:0 auto}
.settings-header{margin-bottom:24px;display:flex;align-items:flex-start;justify-content:space-between;gap:16px;flex-wrap:wrap}
.settings-title{font-family:'Space Grotesk',sans-serif;font-size:22px;font-weight:700;letter-spacing:-.5px;margin-bottom:3px}
.settings-sub{font-size:13px;color:var(--text-3)}

/* ── Tab shell ── */
.settings-shell{display:grid;grid-template-columns:220px 1fr;gap:20px;align-items:start}

/* ── Sidebar nav ── */
.settings-nav{
    background:var(--surface);border:1px solid var(--border);
    border-radius:var(--radius-lg);overflow:hidden;
    position:sticky;top:80px;
}
.nav-section-label{
    font-size:10px;font-weight:700;letter-spacing:.8px;
    text-transform:uppercase;color:var(--text-3);
    padding:14px 16px 6px;
}
.nav-item{
    display:flex;align-items:center;gap:10px;
    padding:10px 16px;font-size:13px;font-weight:500;
    color:var(--text-2);cursor:pointer;
    transition:all .15s;border-left:3px solid transparent;
    user-select:none;
}
.nav-item:hover{color:var(--text);background:rgba(255,255,255,.04)}
.nav-item.active{
    color:var(--brand-light);background:rgba(124,108,252,.08);
    border-left-color:var(--brand);font-weight:600;
}
.nav-item .iconify{width:16px;height:16px;flex-shrink:0}
.nav-divider{height:1px;background:var(--border);margin:6px 0}
.nav-item.danger-nav:hover{color:var(--red);background:rgba(244,63,94,.06)}
.nav-item.danger-nav.active{color:var(--red);background:rgba(244,63,94,.08);border-left-color:var(--red)}

/* ── Tab panels ── */
.settings-panel{display:none}
.settings-panel.active{display:block}

/* ── Panel card ── */
.panel-card{
    background:var(--surface);border:1px solid var(--border);
    border-radius:var(--radius-lg);padding:28px;
    margin-bottom:16px;
}
.panel-card:last-child{margin-bottom:0}
.panel-card-head{
    display:flex;align-items:center;gap:12px;
    margin-bottom:22px;padding-bottom:18px;
    border-bottom:1px solid var(--border);
}
.panel-card-icon{
    width:36px;height:36px;border-radius:10px;
    display:flex;align-items:center;justify-content:center;
    flex-shrink:0;
}
.panel-card-icon .iconify{width:18px;height:18px}
.panel-card-title{font-family:'Space Grotesk',sans-serif;font-size:15px;font-weight:700;letter-spacing:-.2px}
.panel-card-sub{font-size:12px;color:var(--text-3);margin-top:2px}

/* ── Form groups ── */
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.form-row.triple{grid-template-columns:1fr 1fr 1fr}

/* ── Toggle row ── */
.toggle-row{display:flex;align-items:center;justify-content:space-between;
    padding:14px 0;border-bottom:1px solid var(--border);gap:16px}
.toggle-row:last-child{border-bottom:none;padding-bottom:0}
.toggle-row:first-of-type{padding-top:0}
.toggle-label{font-size:13px;font-weight:500;color:var(--text)}
.toggle-sub{font-size:11px;color:var(--text-3);margin-top:2px}

/* ── Toggle switch ── */
label.toggle-switch{
    display:inline-flex !important;
    width:40px;height:22px;border-radius:11px;
    background:var(--surface-3);border:1px solid var(--border);
    position:relative;cursor:pointer;
    transition:background .2s,border-color .2s;
    flex-shrink:0;margin-bottom:0 !important;
    font-size:0;color:transparent;
}
label.toggle-switch.on{background:var(--brand);border-color:var(--brand)}
label.toggle-switch::after{
    content:'';position:absolute;top:2px;left:2px;
    width:16px;height:16px;border-radius:50%;background:#fff;
    transition:transform .2s cubic-bezier(.34,1.4,.64,1);
    box-shadow:0 1px 4px rgba(0,0,0,.3);
}
label.toggle-switch.on::after{transform:translateX(18px)}

/* ── Thank-you preview ── */
.thankyou-preview{
    margin-top:8px;padding:10px 14px;
    background:rgba(124,108,252,.06);border:1px solid rgba(124,108,252,.2);
    border-radius:var(--radius);font-size:13px;color:var(--brand-light);
    line-height:1.6;min-height:42px;white-space:pre-wrap;word-break:break-word;
}
.preview-label{font-size:10px;font-weight:700;letter-spacing:.5px;color:var(--text-3);
    margin-bottom:5px;display:flex;align-items:center;gap:5px;text-transform:uppercase}
.preview-label .iconify{width:12px;height:12px}

/* ── Alert theme picker ── */
.theme-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:8px;margin-top:10px}
.theme-chip{
    padding:10px 8px;border-radius:var(--radius-sm);
    border:2px solid transparent;cursor:pointer;
    font-size:11px;font-weight:700;text-align:center;
    transition:all .15s;user-select:none;
}
.theme-chip.active{transform:scale(1.04)}
.theme-chip-dot{width:6px;height:6px;border-radius:50%;background:currentColor;margin:0 auto 5px;opacity:.8}
.theme-chip[data-theme="default"]{background:linear-gradient(135deg,#0d0d12,#1a1a2e);border-color:rgba(124,108,252,.4);color:var(--brand-light)}
.theme-chip[data-theme="default"].active{border-color:var(--brand);box-shadow:0 0 12px var(--brand-glow)}
.theme-chip[data-theme="minimal"]{background:rgba(20,20,25,.8);border-color:rgba(255,255,255,.1);color:var(--text-2)}
.theme-chip[data-theme="minimal"].active{border-color:var(--text-2);box-shadow:0 0 8px rgba(255,255,255,.1)}
.theme-chip[data-theme="neon"]{background:linear-gradient(135deg,#050517,#0a0a1f);border-color:rgba(0,255,200,.4);color:#00ffc8}
.theme-chip[data-theme="neon"].active{border-color:#00ffc8;box-shadow:0 0 12px rgba(0,255,200,.3)}
.theme-chip[data-theme="fire"]{background:linear-gradient(135deg,#0c0402,#1a0800);border-color:rgba(249,115,22,.5);color:#f97316}
.theme-chip[data-theme="fire"].active{border-color:#f97316;box-shadow:0 0 14px rgba(249,115,22,.4)}
.theme-chip[data-theme="ice"]{background:linear-gradient(135deg,#02060e,#050e1e);border-color:rgba(56,189,248,.4);color:#38bdf8}
.theme-chip[data-theme="ice"].active{border-color:#38bdf8;box-shadow:0 0 14px rgba(56,189,248,.3)}

/* ── Avatar ── */
.avatar-row{display:flex;align-items:center;gap:16px;margin-bottom:20px}
.avatar-preview{width:64px;height:64px;border-radius:16px;
    background:linear-gradient(135deg,var(--brand),var(--purple));
    display:flex;align-items:center;justify-content:center;
    font-family:'Space Grotesk',sans-serif;font-size:22px;font-weight:700;
    color:#fff;flex-shrink:0;overflow:hidden;border:2px solid var(--border-2)}
.avatar-preview img{width:100%;height:100%;object-fit:cover;display:block}
.avatar-upload-btn{display:inline-flex;align-items:center;gap:7px;padding:8px 14px;
    border:1px solid var(--border);background:var(--surface-2);border-radius:var(--radius-sm);
    color:var(--text-2);font-size:12px;font-weight:600;cursor:pointer;transition:all .15s}
.avatar-upload-btn:hover{border-color:var(--brand);color:var(--brand-light)}
.avatar-upload-btn .iconify{width:14px;height:14px}
input[type="file"].file-input-hidden{display:none}

/* ── Sound presets ── */
.sound-presets{display:flex;gap:8px;flex-wrap:wrap}
.sound-preset-btn{display:inline-flex;align-items:center;gap:6px;
    padding:8px 14px;border-radius:var(--radius-sm);
    border:2px solid var(--border);background:var(--surface-2);
    color:var(--text-2);font-size:12px;font-weight:600;cursor:pointer;
    transition:all .15s;user-select:none}
.sound-preset-btn:hover{border-color:var(--border-2);color:var(--text)}
.sound-preset-btn.active{border-color:var(--brand);background:rgba(124,108,252,.12);color:var(--brand-light)}
.sound-preset-btn .iconify{width:14px;height:14px}
.sound-play-btn{display:inline-flex;align-items:center;gap:5px;padding:8px 12px;
    border:1px solid rgba(34,211,160,.25);background:rgba(34,211,160,.06);
    border-radius:var(--radius-sm);color:var(--green);font-size:12px;font-weight:600;
    cursor:pointer;transition:all .15s;flex-shrink:0}
.sound-play-btn:hover{background:rgba(34,211,160,.12);border-color:var(--green)}
.sound-play-btn .iconify{width:13px;height:13px}
.sound-custom-badge{display:inline-flex;align-items:center;gap:0;position:relative}
.sound-custom-badge .sound-preset-btn{border-radius:var(--radius-sm) 0 0 var(--radius-sm);border-right:none}
.sound-custom-delete{display:inline-flex;align-items:center;justify-content:center;
    padding:0 8px;border:2px solid var(--border);border-left:none;
    border-radius:0 var(--radius-sm) var(--radius-sm) 0;background:var(--surface-2);
    color:var(--text-3);cursor:pointer;transition:all .15s;flex-shrink:0}
.sound-custom-delete:hover{background:rgba(244,63,94,.12);border-color:rgba(244,63,94,.4);color:#f43f5e}
.sound-custom-badge.active .sound-custom-delete{border-color:var(--brand);background:rgba(124,108,252,.12)}
.sound-custom-delete .iconify{width:14px;height:14px}
.sound-file-row{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-top:12px}
.sound-file-label{display:inline-flex;align-items:center;gap:7px;padding:8px 14px;
    border:1px solid var(--border);background:var(--surface-2);border-radius:var(--radius-sm);
    color:var(--text-2);font-size:12px;font-weight:600;cursor:pointer;transition:all .15s;flex-shrink:0}
.sound-file-label:hover{border-color:var(--brand-light);color:var(--brand-light)}
.sound-file-label .iconify{width:14px;height:14px}
.sound-file-name{font-size:11px;color:var(--text-3);overflow:hidden;text-overflow:ellipsis;white-space:nowrap}

/* ── API key ── */
.api-key-row{display:flex;gap:8px}
.api-key-input{flex:1;font-size:12px;padding:10px 12px;background:var(--surface-2);
    border:1px solid var(--border);border-radius:var(--radius-sm);
    color:var(--text-3);font-family:monospace;outline:none;min-width:0}
.copy-btn{padding:10px 14px;background:var(--surface-3);border:1px solid var(--border);
    border-radius:var(--radius-sm);color:var(--text-2);cursor:pointer;font-size:12px;
    font-weight:700;transition:all .15s;white-space:nowrap;flex-shrink:0;
    display:inline-flex;align-items:center;gap:5px}
.copy-btn:hover{background:var(--brand);border-color:var(--brand);color:#fff}
.copy-btn .iconify{width:13px;height:13px}

/* ── Danger ── */
.danger-zone{background:rgba(244,63,94,.04);border:1px solid rgba(244,63,94,.15);
    border-radius:var(--radius-lg);padding:22px}
.danger-zone-head{display:flex;align-items:center;gap:8px;margin-bottom:8px}
.danger-zone-head .iconify{width:18px;height:18px;color:var(--red)}
.danger-zone-title{font-size:14px;font-weight:700;color:var(--red);font-family:'Space Grotesk',sans-serif}
.danger-zone-desc{font-size:12px;color:var(--text-3);line-height:1.7;margin-bottom:16px}
.danger-btn{display:inline-flex;align-items:center;gap:8px;padding:9px 18px;
    border:1px solid rgba(244,63,94,.3);border-radius:var(--radius-sm);cursor:pointer;
    background:rgba(244,63,94,.08);color:var(--red);font-family:'Inter',sans-serif;
    font-size:13px;font-weight:600;transition:all .15s;text-decoration:none}
.danger-btn:hover{background:rgba(244,63,94,.15);border-color:rgba(244,63,94,.5)}
.danger-btn .iconify{width:15px;height:15px}

/* ── OBS widgets ── */
.obs-grid{display:grid;grid-template-columns:repeat(2,1fr);gap:12px}
.obs-card{background:var(--surface-2);border:1px solid var(--border);
    border-radius:var(--radius);padding:16px;transition:border-color .2s}
.obs-card:hover{border-color:var(--border-2)}
.obs-card-icon{width:32px;height:32px;border-radius:8px;
    display:flex;align-items:center;justify-content:center;margin-bottom:10px}
.obs-card-icon .iconify{width:18px;height:18px}
.obs-card-name{font-size:13px;font-weight:700;margin-bottom:3px}
.obs-card-desc{font-size:11px;color:var(--text-3);margin-bottom:10px;line-height:1.6}
.obs-url-input{width:100%;font-size:10px;padding:7px 9px;background:var(--surface-3);
    border:1px solid var(--border);border-radius:6px;color:var(--text-3);
    font-family:monospace;outline:none;box-sizing:border-box;margin-bottom:6px}
.obs-actions{display:flex;gap:6px}
.obs-copy-btn{flex:1;padding:7px 0;background:var(--surface-3);border:1px solid var(--border);
    border-radius:6px;color:var(--text-2);cursor:pointer;font-size:11px;font-weight:700;
    transition:all .15s;display:inline-flex;align-items:center;justify-content:center;gap:4px}
.obs-copy-btn:hover{background:var(--brand);border-color:var(--brand);color:#fff}
.obs-copy-btn .iconify{width:11px;height:11px}
.obs-open-btn{flex:1;padding:7px 0;background:transparent;border:1px solid var(--border);
    border-radius:6px;color:var(--text-3);font-size:11px;font-weight:700;
    transition:all .15s;display:inline-flex;align-items:center;justify-content:center;
    gap:4px;text-decoration:none;cursor:pointer}
.obs-open-btn:hover{border-color:var(--border-2);color:var(--text)}
.obs-open-btn .iconify{width:11px;height:11px}

/* ── Hint ── */
.hint{font-size:11px;color:var(--text-3);margin-top:5px;line-height:1.6;
    display:flex;align-items:flex-start;gap:5px}
.hint .iconify{width:12px;height:12px;flex-shrink:0;margin-top:1px}

/* ── Save bar ── */
.save-bar{
    position:sticky;bottom:20px;z-index:10;
    background:var(--surface);border:1px solid var(--border);
    border-radius:var(--radius-lg);padding:14px 20px;
    display:flex;align-items:center;justify-content:space-between;gap:12px;
    box-shadow:0 8px 32px rgba(0,0,0,.4);margin-top:20px;
}
.save-bar-hint{font-size:12px;color:var(--text-3)}

/* ── Banned words chip ── */
.bw-chip{display:inline-flex;align-items:center;gap:4px;
    background:rgba(124,108,252,.12);border:1px solid rgba(124,108,252,.25);
    border-radius:20px;padding:3px 10px;font-size:12px;color:var(--brand-light)}
.bw-chip-del{background:none;border:none;color:inherit;cursor:pointer;
    font-size:14px;line-height:1;padding:0 0 0 2px;opacity:.7}
.bw-chip-del:hover{opacity:1}
.bw-chip.global{background:rgba(249,115,22,.08);border-color:rgba(249,115,22,.2);color:var(--orange)}

@media(max-width:900px){
    .settings-shell{grid-template-columns:1fr}
    .settings-nav{position:static;display:flex;flex-wrap:wrap;overflow:visible;border-radius:var(--radius-lg)}
    .nav-section-label{display:none}
    .nav-divider{display:none}
    .nav-item{border-left:none;border-bottom:3px solid transparent;padding:10px 14px;font-size:12px}
    .nav-item.active{border-bottom-color:var(--brand);border-left:none}
    .obs-grid{grid-template-columns:1fr}
}
@media(max-width:640px){.form-row{grid-template-columns:1fr}.theme-grid{grid-template-columns:repeat(3,1fr)}}
</style>
@endpush

<div class="settings-wrap">

    <div class="settings-header">
        <div>
            <div class="settings-title">Settings</div>
            <div class="settings-sub">Konfigurasi profil, alert, dan widget OBS kamu</div>
        </div>
        <a href="{{ route('streamer.dashboard') }}" style="display:inline-flex;align-items:center;gap:7px;padding:9px 16px;border:1px solid var(--border);border-radius:var(--radius-sm);font-size:13px;font-weight:600;color:var(--text-2);text-decoration:none;transition:all .15s;background:var(--surface-2)" onmouseover="this.style.borderColor='var(--border-2)';this.style.color='var(--text)'" onmouseout="this.style.borderColor='var(--border)';this.style.color='var(--text-2)'">
            <span class="iconify" data-icon="solar:arrow-left-bold" style="width:14px;height:14px"></span>
            Dashboard
        </a>
    </div>

    @if($errors->any())
    <div style="background:rgba(244,63,94,.08);border:1px solid rgba(244,63,94,.2);border-radius:var(--radius);padding:12px 16px;margin-bottom:20px;font-size:13px;color:#f43f5e;line-height:1.8">
        @foreach($errors->all() as $err)<div>• {{ $err }}</div>@endforeach
    </div>
    @endif

    @if(session('success'))
    <div style="background:rgba(34,211,160,.08);border:1px solid rgba(34,211,160,.2);border-radius:var(--radius);padding:12px 16px;margin-bottom:20px;font-size:13px;color:var(--green);line-height:1.6;display:flex;align-items:center;gap:8px">
        <span class="iconify" data-icon="solar:check-circle-bold-duotone" style="width:16px;height:16px;flex-shrink:0"></span>
        {{ session('success') }}
    </div>
    @endif

    <div class="settings-shell">

        {{-- ── Sidebar Nav ── --}}
        <nav class="settings-nav" id="settings-nav">
            <div class="nav-section-label">Pengaturan</div>
            <div class="nav-item active" data-tab="profil" onclick="switchTab('profil',this)">
                <span class="iconify" data-icon="solar:user-bold-duotone"></span>Profil
            </div>
            <div class="nav-item" data-tab="alert" onclick="switchTab('alert',this)">
                <span class="iconify" data-icon="solar:bell-bold-duotone"></span>Alert
            </div>
            <div class="nav-item" data-tab="milestone" onclick="switchTab('milestone',this)">
                <span class="iconify" data-icon="solar:target-bold-duotone"></span>Milestone
            </div>
            <div class="nav-item" data-tab="leaderboard" onclick="switchTab('leaderboard',this)">
                <span class="iconify" data-icon="solar:ranking-bold-duotone"></span>Leaderboard
            </div>
            <div class="nav-divider"></div>
            <div class="nav-section-label">Fitur</div>
            <div class="nav-item" data-tab="banned-words" onclick="switchTab('banned-words',this)">
                <span class="iconify" data-icon="solar:shield-warning-bold-duotone"></span>Kata Terlarang
            </div>
            <div class="nav-divider"></div>
            <div class="nav-section-label">Lainnya</div>
            <div class="nav-item" data-tab="api" onclick="switchTab('api',this)">
                <span class="iconify" data-icon="solar:key-bold-duotone"></span>API Key
            </div>
            <div class="nav-item danger-nav" data-tab="danger" onclick="switchTab('danger',this)">
                <span class="iconify" data-icon="solar:danger-triangle-bold-duotone"></span>Danger Zone
            </div>
        </nav>

        {{-- ── Tab Panels ── --}}
        <div id="settings-panels">

            <form method="POST" action="{{ route('streamer.settings.update') }}" enctype="multipart/form-data" id="settings-form">
            @csrf

            {{-- ══ TAB: Profil ══ --}}
            <div class="settings-panel active" id="tab-profil">
                <div class="panel-card">
                    <div class="panel-card-head">
                        <div class="panel-card-icon" style="background:rgba(124,108,252,.1);border:1px solid rgba(124,108,252,.2)">
                            <span class="iconify" data-icon="solar:user-bold-duotone" style="color:var(--brand-light)"></span>
                        </div>
                        <div>
                            <div class="panel-card-title">Profil</div>
                            <div class="panel-card-sub">Informasi yang tampil di halaman donasi publik</div>
                        </div>
                    </div>

                    {{-- Avatar --}}
                    <div class="avatar-row">
                        <div class="avatar-preview" id="avatar-preview">
                            @if($streamer->avatar)
                                <img src="{{ asset('storage/' . $streamer->avatar) }}" alt="Avatar" id="avatar-img" />
                            @else
                                <span id="avatar-initials">{{ strtoupper(substr($streamer->display_name, 0, 2)) }}</span>
                            @endif
                        </div>
                        <div>
                            <label class="avatar-upload-btn" for="avatar-file-input">
                                <span class="iconify" data-icon="solar:camera-bold-duotone"></span>Ganti Foto
                            </label>
                            <input type="file" id="avatar-file-input" name="avatar" accept="image/*" class="file-input-hidden" />
                            <div class="hint" style="margin-top:6px">
                                <span class="iconify" data-icon="solar:info-circle-bold"></span>JPG/PNG/GIF, maks 2MB
                            </div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Nama Tampilan</label>
                            <input type="text" name="display_name"
                                   value="{{ old('display_name', $streamer->display_name) }}"
                                   required maxlength="60" placeholder="Nama streamer kamu" />
                        </div>
                        <div class="form-group">
                            <label>Minimum Donasi (Rp)</label>
                            <input type="number" name="min_donation"
                                   value="{{ old('min_donation', $streamer->min_donation) }}"
                                   min="100" required placeholder="1000" />
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Bio</label>
                        <textarea name="bio" maxlength="200" rows="3" placeholder="Deskripsi singkat tentang kamu...">{{ old('bio', $streamer->bio) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>Pesan Terima Kasih</label>
                        <textarea name="thank_you_message" id="thank_you_message" maxlength="200" rows="3"
                                  placeholder="Terima kasih atas donasinya!">{{ old('thank_you_message', $streamer->thank_you_message) }}</textarea>
                        <div class="preview-label" style="margin-top:10px">
                            <span class="iconify" data-icon="solar:eye-bold-duotone"></span>Preview
                        </div>
                        <div class="thankyou-preview" id="thankyou-preview">{{ old('thank_you_message', $streamer->thank_you_message) }}</div>
                    </div>
                </div>
                <div class="save-bar">
                    <span class="save-bar-hint">Perubahan pada Profil</span>
                    <button type="submit" class="btn-primary">
                        <span class="iconify" data-icon="solar:floppy-disk-bold-duotone" style="width:16px;height:16px"></span>
                        Simpan
                    </button>
                </div>
            </div>

            {{-- ══ TAB: Alert ══ --}}
            <div class="settings-panel" id="tab-alert">
                <div class="panel-card">
                    <div class="panel-card-head">
                        <div class="panel-card-icon" style="background:rgba(249,115,22,.1);border:1px solid rgba(249,115,22,.2)">
                            <span class="iconify" data-icon="solar:bell-bold-duotone" style="color:var(--orange)"></span>
                        </div>
                        <div>
                            <div class="panel-card-title">Pengaturan Alert</div>
                            <div class="panel-card-sub">Tampilan popup donasi di OBS overlay</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Durasi Alert (ms)</label>
                            <input type="number" name="alert_duration"
                                   value="{{ old('alert_duration', $streamer->alert_duration) }}"
                                   min="3000" max="30000" step="1000" required />
                            <div class="hint">
                                <span class="iconify" data-icon="solar:info-circle-bold"></span>
                                1000 ms = 1 detik. Rekomendasi: 8000
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Tema Alert</label>
                            <select name="alert_theme" id="alert_theme" onchange="updateThemePreview(this.value)" style="display:none">
                                <option value="default"  {{ old('alert_theme', $streamer->alert_theme) === 'default'  ? 'selected' : '' }}>Default</option>
                                <option value="minimal"  {{ old('alert_theme', $streamer->alert_theme) === 'minimal'  ? 'selected' : '' }}>Minimal</option>
                                <option value="neon"     {{ old('alert_theme', $streamer->alert_theme) === 'neon'     ? 'selected' : '' }}>Neon</option>
                                <option value="fire"     {{ old('alert_theme', $streamer->alert_theme) === 'fire'     ? 'selected' : '' }}>Fire</option>
                                <option value="ice"      {{ old('alert_theme', $streamer->alert_theme) === 'ice'      ? 'selected' : '' }}>Ice</option>
                            </select>
                            <div class="theme-grid">
                                @foreach(['default','minimal','neon','fire','ice'] as $th)
                                <div class="theme-chip {{ old('alert_theme', $streamer->alert_theme) === $th ? 'active' : '' }}"
                                     data-theme="{{ $th }}" onclick="selectTheme('{{ $th }}')">
                                    <div class="theme-chip-dot"></div>
                                    {{ ucfirst($th) }}
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div style="border-top:1px solid var(--border);padding-top:18px;margin-top:4px">
                        <div class="toggle-row">
                            <div>
                                <div class="toggle-label">Terima Donasi</div>
                                <div class="toggle-sub">Matikan untuk menghentikan sementara donasi masuk</div>
                            </div>
                            <label class="toggle-switch {{ old('is_accepting_donation', $streamer->is_accepting_donation) ? 'on' : '' }}"
                                   onclick="toggleSwitch(this,'is_accepting_donation')">
                                <input type="hidden" name="is_accepting_donation" id="is_accepting_donation"
                                       value="{{ old('is_accepting_donation', $streamer->is_accepting_donation) ? '1' : '0' }}" />
                            </label>
                        </div>
                        <div class="toggle-row">
                            <div>
                                <div class="toggle-label">Request YouTube</div>
                                <div class="toggle-sub">Izinkan donatur menyertakan link video YouTube</div>
                            </div>
                            <label class="toggle-switch {{ old('yt_enabled', $streamer->yt_enabled) ? 'on' : '' }}"
                                   onclick="toggleSwitch(this,'yt_enabled')">
                                <input type="hidden" name="yt_enabled" id="yt_enabled"
                                       value="{{ old('yt_enabled', $streamer->yt_enabled) ? '1' : '0' }}" />
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Sound --}}
                <div class="panel-card">
                    <div class="panel-card-head">
                        <div class="panel-card-icon" style="background:rgba(34,211,160,.1);border:1px solid rgba(34,211,160,.2)">
                            <span class="iconify" data-icon="solar:volume-loud-bold-duotone" style="color:var(--green)"></span>
                        </div>
                        <div>
                            <div class="panel-card-title">Suara Notifikasi</div>
                            <div class="panel-card-sub">Suara yang diputar saat donasi masuk di OBS</div>
                        </div>
                        <label class="toggle-switch {{ old('sound_enabled', $streamer->sound_enabled) ? 'on' : '' }}"
                               onclick="toggleSwitch(this,'sound_enabled')" style="margin-left:auto">
                            <input type="hidden" name="sound_enabled" id="sound_enabled"
                                   value="{{ old('sound_enabled', $streamer->sound_enabled) ? '1' : '0' }}" />
                        </label>
                    </div>

                    @php
                        $hasCustom      = $streamer->notification_sound && !in_array($streamer->notification_sound, ['ding','coin','whoosh']);
                        $activePreset   = old('notification_sound_preset', $hasCustom ? '__custom__' : ($streamer->notification_sound ?: 'ding'));
                        $customFileName = $hasCustom ? basename($streamer->notification_sound) : '';
                        $customSoundUrl = $hasCustom ? asset('storage/' . $streamer->notification_sound) : '';
                    @endphp

                    <div class="sound-presets" id="sound-presets">
                        <button type="button" class="sound-preset-btn {{ $activePreset === 'ding'   ? 'active' : '' }}" data-preset="ding"   onclick="selectSoundPreset(this,'ding')">
                            <span class="iconify" data-icon="solar:bell-bold-duotone"></span>Ding
                        </button>
                        <button type="button" class="sound-preset-btn {{ $activePreset === 'coin'   ? 'active' : '' }}" data-preset="coin"   onclick="selectSoundPreset(this,'coin')">
                            <span class="iconify" data-icon="solar:star-bold-duotone"></span>Coin
                        </button>
                        <button type="button" class="sound-preset-btn {{ $activePreset === 'whoosh' ? 'active' : '' }}" data-preset="whoosh" onclick="selectSoundPreset(this,'whoosh')">
                            <span class="iconify" data-icon="solar:wind-bold-duotone"></span>Whoosh
                        </button>
                        <div class="sound-custom-badge {{ $activePreset === '__custom__' ? 'active' : '' }}" id="custom-sound-badge" style="{{ $hasCustom ? '' : 'display:none' }}">
                            <button type="button" class="sound-preset-btn {{ $activePreset === '__custom__' ? 'active' : '' }}" id="custom-badge-btn" data-preset="__custom__" onclick="selectSoundPreset(this,'__custom__')">
                                <span class="iconify" data-icon="solar:music-note-bold-duotone"></span><span id="custom-badge-label">{{ $customFileName ?: 'Custom' }}</span>
                            </button>
                            <button type="button" class="sound-custom-delete" id="custom-badge-delete" onclick="deleteCustomSound()" title="Hapus file custom">
                                <span class="iconify" data-icon="solar:close-circle-bold"></span>
                            </button>
                        </div>
                        <button type="button" class="sound-play-btn" onclick="previewSound()">
                            <span class="iconify" data-icon="solar:play-bold-duotone"></span>Preview
                        </button>
                    </div>
                    <input type="hidden" name="notification_sound_preset" id="notification_sound_preset" value="{{ $activePreset }}" />
                    <input type="hidden" name="delete_sound" id="delete_sound" value="0" />
                    <div class="sound-file-row">
                        <label class="sound-file-label" for="sound-file-input">
                            <span class="iconify" data-icon="solar:upload-bold-duotone"></span>Custom MP3/WAV/OGG
                        </label>
                        <input type="file" id="sound-file-input" name="sound_file" accept=".mp3,.wav,.ogg" class="file-input-hidden" />
                        <span class="sound-file-name" id="sound-file-name">
                            @if($hasCustom) {{ $customFileName }} @else Tidak ada file custom @endif
                        </span>
                    </div>
                    <div class="hint" style="margin-top:8px">
                        <span class="iconify" data-icon="solar:info-circle-bold"></span>
                        Upload file audio custom untuk mengganti preset. Maks 5MB.
                    </div>
                </div>

                <div class="save-bar">
                    <span class="save-bar-hint">Perubahan pada Alert & Suara</span>
                    <button type="submit" class="btn-primary">
                        <span class="iconify" data-icon="solar:floppy-disk-bold-duotone" style="width:16px;height:16px"></span>
                        Simpan
                    </button>
                </div>
            </div>

            {{-- ══ TAB: Milestone ══ --}}
            <div class="settings-panel" id="tab-milestone">
                <div class="panel-card">
                    <div class="panel-card-head">
                        <div class="panel-card-icon" style="background:rgba(168,85,247,.1);border:1px solid rgba(168,85,247,.2)">
                            <span class="iconify" data-icon="solar:target-bold-duotone" style="color:var(--purple)"></span>
                        </div>
                        <div>
                            <div class="panel-card-title">Milestone</div>
                            <div class="panel-card-sub">Target donasi yang ditampilkan di widget progress bar</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Judul Milestone</label>
                            <input type="text" name="milestone_title"
                                   value="{{ old('milestone_title', $streamer->milestone_title) }}"
                                   maxlength="80" required placeholder="Target donasi malam ini!" />
                        </div>
                        <div class="form-group">
                            <label>Target (Rp)</label>
                            <input type="number" name="milestone_target"
                                   value="{{ old('milestone_target', $streamer->milestone_target) }}"
                                   min="1000" required placeholder="500000" />
                        </div>
                    </div>

                    <div class="toggle-row" style="border-top:1px solid var(--border);padding-top:18px;margin-top:4px;border-bottom:none">
                        <div>
                            <div class="toggle-label">Auto Reset setelah tercapai</div>
                            <div class="toggle-sub">Progress bar reset ke 0% ketika target terpenuhi</div>
                        </div>
                        <label class="toggle-switch {{ old('milestone_reset', $streamer->milestone_reset) ? 'on' : '' }}"
                               onclick="toggleSwitch(this,'milestone_reset')">
                            <input type="hidden" name="milestone_reset" id="milestone_reset"
                                   value="{{ old('milestone_reset', $streamer->milestone_reset) ? '1' : '0' }}" />
                        </label>
                    </div>
                </div>
                <div class="save-bar">
                    <span class="save-bar-hint">Perubahan pada Milestone</span>
                    <button type="submit" class="btn-primary">
                        <span class="iconify" data-icon="solar:floppy-disk-bold-duotone" style="width:16px;height:16px"></span>
                        Simpan
                    </button>
                </div>
            </div>

            {{-- ══ TAB: Leaderboard ══ --}}
            <div class="settings-panel" id="tab-leaderboard">
                <div class="panel-card">
                    <div class="panel-card-head">
                        <div class="panel-card-icon" style="background:rgba(251,191,36,.1);border:1px solid rgba(251,191,36,.2)">
                            <span class="iconify" data-icon="solar:ranking-bold-duotone" style="color:var(--yellow)"></span>
                        </div>
                        <div>
                            <div class="panel-card-title">Leaderboard</div>
                            <div class="panel-card-sub">Panel top donatur yang tampil di widget OBS</div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Judul Leaderboard</label>
                            <input type="text" name="leaderboard_title"
                                   value="{{ old('leaderboard_title', $streamer->leaderboard_title) }}"
                                   maxlength="80" required placeholder="Top Donatur" />
                        </div>
                        <div class="form-group">
                            <label>Jumlah Tampil (top N)</label>
                            <input type="number" name="leaderboard_count"
                                   value="{{ old('leaderboard_count', $streamer->leaderboard_count) }}"
                                   min="3" max="20" required />
                            <div class="hint">
                                <span class="iconify" data-icon="solar:info-circle-bold"></span>
                                Berapa donatur yang tampil di widget leaderboard OBS. (3–20)
                            </div>
                        </div>
                    </div>
                </div>
                <div class="save-bar">
                    <span class="save-bar-hint">Perubahan pada Leaderboard</span>
                    <button type="submit" class="btn-primary">
                        <span class="iconify" data-icon="solar:floppy-disk-bold-duotone" style="width:16px;height:16px"></span>
                        Simpan
                    </button>
                </div>
            </div>

            </form>{{-- end settings-form --}}

            {{-- ══ TAB: Kata Terlarang (luar form) ══ --}}
            <div class="settings-panel" id="tab-banned-words">
                <div class="panel-card">
                    <div class="panel-card-head">
                        <div class="panel-card-icon" style="background:rgba(249,115,22,.1);border:1px solid rgba(249,115,22,.2)">
                            <span class="iconify" data-icon="solar:shield-warning-bold-duotone" style="color:var(--orange)"></span>
                        </div>
                        <div>
                            <div class="panel-card-title">Kata Terlarang</div>
                            <div class="panel-card-sub">Kata-kata ini akan disensor otomatis menjadi <code style="background:rgba(124,108,252,.12);color:var(--brand-light);padding:1px 5px;border-radius:4px">***</code> pada donasi yang masuk</div>
                        </div>
                    </div>

                    <div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap">
                        <input type="text" id="bw-new-word" placeholder="Tambah kata terlarang..."
                               maxlength="100" autocomplete="off"
                               style="flex:1;min-width:180px"
                               onkeydown="if(event.key==='Enter'){event.preventDefault();addBannedWord();}" />
                        <button type="button" onclick="addBannedWord()" class="btn-primary" style="padding:9px 18px;font-size:13px">+ Tambah</button>
                    </div>
                    <div id="bw-feedback" style="font-size:12px;margin-bottom:12px;display:none"></div>

                    <div style="margin-bottom:20px">
                        <div style="font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--text-3);margin-bottom:10px">
                            Kata Global (dari Admin)
                        </div>
                        <div id="bw-global-list" style="display:flex;flex-wrap:wrap;gap:6px;min-height:28px">
                            <span style="color:var(--text-3);font-size:12px;font-style:italic">Memuat...</span>
                        </div>
                    </div>
                    <div>
                        <div style="font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--text-3);margin-bottom:10px">
                            Kata Kustom Saya
                        </div>
                        <div id="bw-custom-list" style="display:flex;flex-wrap:wrap;gap:6px;min-height:28px">
                            <span style="color:var(--text-3);font-size:12px;font-style:italic">Memuat...</span>
                        </div>
                    </div>
                    <div class="hint" style="margin-top:16px">
                        <span class="iconify" data-icon="solar:info-circle-bold"></span>
                        Kata global dari admin tidak dapat dihapus. Kamu hanya bisa mengelola kata kustom milikmu sendiri.
                    </div>
                </div>
            </div>

            {{-- ══ TAB: API Key ══ --}}
            <div class="settings-panel" id="tab-api">
                <div class="panel-card">
                    <div class="panel-card-head">
                        <div class="panel-card-icon" style="background:rgba(124,108,252,.1);border:1px solid rgba(124,108,252,.2)">
                            <span class="iconify" data-icon="solar:key-bold-duotone" style="color:var(--brand-light)"></span>
                        </div>
                        <div>
                            <div class="panel-card-title">API Key</div>
                            <div class="panel-card-sub">Digunakan di URL widget OBS sebagai parameter <code style="background:rgba(255,255,255,.06);padding:1px 5px;border-radius:4px;font-size:11px">?key=</code></div>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom:20px">
                        <label>API Key aktif</label>
                        <div class="api-key-row">
                            <input class="api-key-input" readonly value="{{ $streamer->api_key }}" id="api-key-input" />
                            <button type="button" class="copy-btn"
                                    onclick="copyText(document.getElementById('api-key-input').value,'API Key')">
                                <span class="iconify" data-icon="solar:copy-bold-duotone"></span>Copy
                            </button>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('streamer.regenerate-key') }}"
                          onsubmit="return confirm('Yakin regenerate? URL widget OBS lama akan berhenti bekerja dan harus diupdate.')">
                        @csrf
                        <button type="submit" class="danger-btn">
                            <span class="iconify" data-icon="solar:refresh-bold-duotone"></span>
                            Regenerate API Key
                        </button>
                    </form>
                    <div class="hint" style="margin-top:12px">
                        <span class="iconify" data-icon="solar:shield-warning-bold-duotone" style="color:var(--yellow)"></span>
                        Setelah regenerate, update semua URL Browser Source di OBS dengan API key yang baru.
                    </div>
                </div>
            </div>

            {{-- ══ TAB: Danger Zone ══ --}}
            <div class="settings-panel" id="tab-danger">
                <div class="danger-zone">
                    <div class="danger-zone-head">
                        <span class="iconify" data-icon="solar:danger-triangle-bold-duotone"></span>
                        <div class="danger-zone-title">Danger Zone</div>
                    </div>
                    <div class="danger-zone-desc">
                        Aksi di bawah ini bersifat permanen dan tidak bisa dibatalkan. Pastikan kamu benar-benar yakin sebelum melanjutkan.
                    </div>
                    <a href="{{ route('profile.edit') }}" class="danger-btn">
                        <span class="iconify" data-icon="solar:trash-bin-trash-bold-duotone"></span>
                        Hapus Akun
                    </a>
                </div>
            </div>

        </div>{{-- end settings-panels --}}
    </div>{{-- end settings-shell --}}

</div>

@push('scripts')
<script>
// ── Tab switching ──
function switchTab(name, navEl) {
    document.querySelectorAll('.settings-panel').forEach(function (p) { p.classList.remove('active'); });
    document.querySelectorAll('.nav-item').forEach(function (n) { n.classList.remove('active'); });
    var panel = document.getElementById('tab-' + name);
    if (panel) panel.classList.add('active');
    if (navEl) navEl.classList.add('active');
    // Persist in URL hash and sessionStorage (hash is not sent to server on redirect)
    history.replaceState(null, '', '#' + name);
    try { sessionStorage.setItem('settings_tab', name); } catch(e) {}
}

// Restore tab from sessionStorage (survives POST redirect) or URL hash
document.addEventListener('DOMContentLoaded', function () {
    var tab = null;
    // URL hash takes priority (e.g. direct link)
    var hash = location.hash.replace('#', '');
    if (hash && document.querySelector('.nav-item[data-tab="' + hash + '"]')) {
        tab = hash;
    } else {
        // Fall back to sessionStorage (restored after form save redirect)
        try { tab = sessionStorage.getItem('settings_tab'); } catch(e) {}
    }
    if (tab) {
        var navEl = document.querySelector('.nav-item[data-tab="' + tab + '"]');
        if (navEl) switchTab(tab, navEl);
    }
});

// ── Toggle switch ──
function toggleSwitch(label, fieldName) {
    label.classList.toggle('on');
    document.getElementById(fieldName).value = label.classList.contains('on') ? '1' : '0';
}

// ── Thank-you preview ──
document.addEventListener('DOMContentLoaded', function () {
    var ta = document.getElementById('thank_you_message');
    var pv = document.getElementById('thankyou-preview');
    if (ta && pv) ta.addEventListener('input', function () { pv.textContent = this.value || ''; });
});

// ── Alert theme ──
function selectTheme(theme) {
    document.getElementById('alert_theme').value = theme;
    updateThemePreview(theme);
}
function updateThemePreview(theme) {
    document.querySelectorAll('.theme-chip').forEach(function (el) {
        el.classList.toggle('active', el.dataset.theme === theme);
    });
}

// ── Sound ──
var _customAudioCtx   = null;
var _customFileObject = null;
var _customStoredUrl  = {!! $hasCustom ? json_encode($customSoundUrl) : 'null' !!};

function _getAudioCtx() {
    if (!_customAudioCtx) _customAudioCtx = new (window.AudioContext || window.webkitAudioContext)();
    if (_customAudioCtx.state === 'suspended') _customAudioCtx.resume();
    return _customAudioCtx;
}

function selectSoundPreset(btn, preset) {
    document.querySelectorAll('.sound-preset-btn').forEach(function (b) { b.classList.remove('active'); });
    btn.classList.add('active');
    document.getElementById('notification_sound_preset').value = preset;
    var bw = document.getElementById('custom-sound-badge');
    if (bw) bw.classList.toggle('active', preset === '__custom__');
}

function previewSound() {
    var preset = document.getElementById('notification_sound_preset').value;
    if (preset === '__custom__') { _previewCustomSound(); return; }
    var ctx = _getAudioCtx();
    if (preset === 'ding') {
        var osc = ctx.createOscillator(), gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.type = 'sine';
        osc.frequency.setValueAtTime(880, ctx.currentTime);
        osc.frequency.exponentialRampToValueAtTime(1320, ctx.currentTime + 0.05);
        osc.frequency.exponentialRampToValueAtTime(660, ctx.currentTime + 0.4);
        gain.gain.setValueAtTime(0.6, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.6);
        osc.start(ctx.currentTime); osc.stop(ctx.currentTime + 0.6);
    } else if (preset === 'coin') {
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
    } else if (preset === 'whoosh') {
        var bufSize = ctx.sampleRate * 0.6, buf = ctx.createBuffer(1, bufSize, ctx.sampleRate);
        var data = buf.getChannelData(0);
        for (var i = 0; i < bufSize; i++) data[i] = (Math.random() * 2 - 1);
        var src = ctx.createBufferSource(); src.buffer = buf;
        var filter = ctx.createBiquadFilter(); filter.type = 'bandpass';
        filter.frequency.setValueAtTime(400, ctx.currentTime);
        filter.frequency.exponentialRampToValueAtTime(2000, ctx.currentTime + 0.2);
        filter.frequency.exponentialRampToValueAtTime(200, ctx.currentTime + 0.6);
        filter.Q.value = 0.8;
        var g = ctx.createGain();
        g.gain.setValueAtTime(0, ctx.currentTime);
        g.gain.linearRampToValueAtTime(0.5, ctx.currentTime + 0.05);
        g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.6);
        src.connect(filter); filter.connect(g); g.connect(ctx.destination);
        src.start(ctx.currentTime); src.stop(ctx.currentTime + 0.6);
    }
}

function _previewCustomSound() {
    var ctx = _getAudioCtx();
    function _playBuf(decoded) {
        var src = ctx.createBufferSource(), gain = ctx.createGain();
        src.buffer = decoded; src.connect(gain); gain.connect(ctx.destination);
        gain.gain.setValueAtTime(0.9, ctx.currentTime); src.start(ctx.currentTime);
    }
    if (_customFileObject) {
        var reader = new FileReader();
        reader.onload = function (e) { ctx.decodeAudioData(e.target.result, _playBuf, function() { alert('Format tidak didukung.'); }); };
        reader.readAsArrayBuffer(_customFileObject);
    } else if (_customStoredUrl) {
        fetch(_customStoredUrl).then(function (r) { return r.arrayBuffer(); })
            .then(function (buf) { return ctx.decodeAudioData(buf); })
            .then(_playBuf).catch(function () { alert('Gagal memutar file custom.'); });
    }
}

function deleteCustomSound() {
    if (!confirm('Hapus file audio custom? Suara akan kembali ke preset Ding.')) return;
    var bw = document.getElementById('custom-sound-badge');
    if (bw) bw.style.display = 'none';
    document.getElementById('sound-file-name').textContent = 'Tidak ada file custom';
    document.getElementById('delete_sound').value = '1';
    document.getElementById('notification_sound_preset').value = 'ding';
    document.querySelectorAll('.sound-preset-btn').forEach(function (b) { b.classList.remove('active'); });
    var dingBtn = document.querySelector('.sound-preset-btn[data-preset="ding"]');
    if (dingBtn) dingBtn.classList.add('active');
    document.getElementById('sound-file-input').value = '';
    _customFileObject = null; _customStoredUrl = null;
}

// ── Avatar preview ──
document.addEventListener('DOMContentLoaded', function () {
    var ai = document.getElementById('avatar-file-input');
    if (ai) {
        ai.addEventListener('change', function () {
            if (!this.files || !this.files[0]) return;
            var reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('avatar-preview').innerHTML =
                    '<img src="' + e.target.result + '" alt="Avatar" style="width:100%;height:100%;object-fit:cover;border-radius:inherit" />';
            };
            reader.readAsDataURL(this.files[0]);
        });
    }
    var si = document.getElementById('sound-file-input');
    if (si) {
        si.addEventListener('change', function () {
            var nameEl = document.getElementById('sound-file-name');
            var bw     = document.getElementById('custom-sound-badge');
            var bl     = document.getElementById('custom-badge-label');
            var bb     = document.getElementById('custom-badge-btn');
            if (this.files && this.files[0]) {
                var fn = this.files[0].name;
                _customFileObject = this.files[0];
                nameEl.textContent = fn;
                if (bl) bl.textContent = fn;
                if (bw) { bw.style.display = ''; bw.classList.add('active'); }
                document.querySelectorAll('.sound-preset-btn').forEach(function (b) { b.classList.remove('active'); });
                if (bb) bb.classList.add('active');
                document.getElementById('notification_sound_preset').value = '__custom__';
                document.getElementById('delete_sound').value = '0';
            } else {
                _customFileObject = null;
                nameEl.textContent = 'Tidak ada file custom';
            }
        });
    }
});

// ── Banned Words AJAX ──
(function () {
    var INDEX_URL = '{{ route('streamer.banned-words.index') }}';
    var STORE_URL = '{{ route('streamer.banned-words.store') }}';
    var CSRF      = document.querySelector('meta[name="csrf-token"]').content;

    function destroyUrl(id) { return '{{ url('streamer/banned-words') }}/' + id; }

    function chip(text, id) {
        var span = document.createElement('span');
        span.className = 'bw-chip' + (id ? '' : ' global');
        span.textContent = text;
        if (id) {
            var btn = document.createElement('button');
            btn.type = 'button'; btn.textContent = '×'; btn.title = 'Hapus';
            btn.className = 'bw-chip-del';
            btn.addEventListener('click', function () { deleteBannedWord(id, text, span); });
            span.appendChild(btn);
        }
        return span;
    }

    function setFeedback(msg, ok) {
        var el = document.getElementById('bw-feedback');
        el.textContent = msg;
        el.style.color = ok ? 'var(--green)' : 'var(--red)';
        el.style.display = 'block';
        clearTimeout(el._timer);
        el._timer = setTimeout(function () { el.style.display = 'none'; }, 4000);
    }

    function loadWords() {
        fetch(INDEX_URL, { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                var gl = document.getElementById('bw-global-list');
                gl.innerHTML = '';
                if (data.global && data.global.length) {
                    data.global.forEach(function (w) { gl.appendChild(chip(w, null)); });
                } else {
                    gl.innerHTML = '<span style="color:var(--text-3);font-size:12px;font-style:italic">Tidak ada kata global.</span>';
                }
                var cl = document.getElementById('bw-custom-list');
                cl.innerHTML = '';
                if (data.custom && data.custom.length) {
                    data.custom.forEach(function (item) { cl.appendChild(chip(item.word, item.id)); });
                } else {
                    cl.innerHTML = '<span style="color:var(--text-3);font-size:12px;font-style:italic">Belum ada kata kustom.</span>';
                }
            })
            .catch(function () { setFeedback('Gagal memuat daftar kata.', false); });
    }

    window.addBannedWord = function () {
        var input = document.getElementById('bw-new-word');
        var word  = input.value.trim();
        if (!word) return;
        fetch(STORE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ word: word }),
        })
        .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
        .then(function (res) {
            if (res.ok && res.data.ok) {
                input.value = '';
                setFeedback('Kata "' + res.data.word + '" berhasil ditambahkan.', true);
                loadWords();
            } else {
                setFeedback(res.data.error || 'Gagal menambahkan kata.', false);
            }
        })
        .catch(function () { setFeedback('Gagal menghubungi server.', false); });
    };

    function deleteBannedWord(id, word, chipEl) {
        if (!confirm('Hapus kata "' + word + '"?')) return;
        fetch(destroyUrl(id), { method: 'DELETE', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (data.ok) {
                    chipEl.remove();
                    setFeedback('Kata "' + word + '" dihapus.', true);
                    var cl = document.getElementById('bw-custom-list');
                    if (cl.children.length === 0) {
                        cl.innerHTML = '<span style="color:var(--text-3);font-size:12px;font-style:italic">Belum ada kata kustom.</span>';
                    }
                } else {
                    setFeedback(data.error || 'Gagal menghapus kata.', false);
                }
            })
            .catch(function () { setFeedback('Gagal menghubungi server.', false); });
    }

    document.addEventListener('DOMContentLoaded', loadWords);
})();
</script>
@endpush
</x-app-layout>
