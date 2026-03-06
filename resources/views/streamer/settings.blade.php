<x-app-layout>
@push('styles')
<style>
.settings-wrap{padding:28px 32px;max-width:960px;margin:0 auto}
.settings-header{margin-bottom:28px}
.settings-title{font-family:'Space Grotesk',sans-serif;font-size:22px;font-weight:700;letter-spacing:-.5px;margin-bottom:4px}
.settings-sub{font-size:13px;color:var(--text-3)}

/* 2-col grid */
.settings-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.s-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:24px}
.s-card-head{display:flex;align-items:center;gap:10px;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid var(--border)}
.s-card-icon{
    width:34px;height:34px;border-radius:10px;
    display:flex;align-items:center;justify-content:center;
    background:var(--surface-2);border:1px solid var(--border);flex-shrink:0;
}
.s-card-icon .iconify{width:18px;height:18px}
.s-card-title{font-family:'Space Grotesk',sans-serif;font-size:14px;font-weight:700;letter-spacing:-.2px}

/* Toggle row */
.toggle-row{display:flex;align-items:center;justify-content:space-between;padding:12px 0;border-bottom:1px solid var(--border);gap:16px}
.toggle-row:last-child{border-bottom:none;padding-bottom:0}
.toggle-row:first-of-type{padding-top:0}
.toggle-label{display:block;font-size:13px;font-weight:500;color:var(--text);letter-spacing:0;margin-bottom:0}
.toggle-sub{font-size:11px;color:var(--text-3);margin-top:2px;font-weight:400}

/* Toggle switch */
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
    margin-top:8px;
    padding:10px 14px;
    background:rgba(124,108,252,.06);
    border:1px solid rgba(124,108,252,.2);
    border-radius:var(--radius);
    font-size:13px;color:var(--brand-light);
    line-height:1.6;min-height:42px;
    white-space:pre-wrap;word-break:break-word;
    transition:all .2s;
}
.thankyou-preview-label{font-size:10px;font-weight:700;letter-spacing:.5px;color:var(--text-3);margin-bottom:5px;display:flex;align-items:center;gap:5px;text-transform:uppercase}
.thankyou-preview-label .iconify{width:12px;height:12px}

/* ── Alert theme preview ── */
.theme-select-wrap{position:relative}
.theme-previews{display:flex;gap:8px;margin-top:10px;flex-wrap:wrap}
.theme-preview{
    flex:1;min-width:0;padding:10px 12px;border-radius:var(--radius-sm);
    border:2px solid transparent;cursor:pointer;
    font-size:11px;font-weight:700;text-align:center;
    transition:all .15s;user-select:none;
}
.theme-preview.active{transform:scale(1.03)}
.theme-preview[data-theme="default"]{
    background:linear-gradient(135deg,#0d0d12,#1a1a2e);
    border-color:rgba(124,108,252,.4);color:var(--brand-light);
}
.theme-preview[data-theme="default"].active{border-color:var(--brand);box-shadow:0 0 12px var(--brand-glow)}
.theme-preview[data-theme="minimal"]{
    background:rgba(20,20,25,.8);
    border-color:rgba(255,255,255,.1);color:var(--text-2);
}
.theme-preview[data-theme="minimal"].active{border-color:var(--text-2);box-shadow:0 0 8px rgba(255,255,255,.1)}
.theme-preview[data-theme="neon"]{
    background:linear-gradient(135deg,#050517,#0a0a1f);
    border-color:rgba(0,255,200,.4);color:#00ffc8;
}
.theme-preview[data-theme="neon"].active{border-color:#00ffc8;box-shadow:0 0 12px rgba(0,255,200,.3)}
.theme-preview-dot{width:6px;height:6px;border-radius:50%;background:currentColor;margin:0 auto 5px;opacity:.8}

/* API key */
.api-section{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:24px;margin-top:16px}
.api-section-head{display:flex;align-items:center;gap:8px;margin-bottom:14px;padding-bottom:14px;border-bottom:1px solid var(--border)}
.api-section-head .iconify{width:18px;height:18px;color:var(--brand-light)}
.api-section-title{font-family:'Space Grotesk',sans-serif;font-size:14px;font-weight:700}
.api-key-row{display:flex;gap:8px}
.api-key-input{flex:1;font-size:12px;padding:10px 12px;background:var(--surface-2);border:1px solid var(--border);border-radius:var(--radius-sm);color:var(--text-3);font-family:monospace;outline:none;min-width:0}
.copy-btn{padding:10px 14px;background:var(--surface-3);border:1px solid var(--border);border-radius:var(--radius-sm);color:var(--text-2);cursor:pointer;font-size:12px;font-weight:700;transition:all .15s;white-space:nowrap;flex-shrink:0;display:inline-flex;align-items:center;gap:5px}
.copy-btn:hover{background:var(--brand);border-color:var(--brand);color:#fff}
.copy-btn .iconify{width:13px;height:13px}

/* Danger */
.danger-section{margin-top:16px;background:rgba(244,63,94,.04);border:1px solid rgba(244,63,94,.15);border-radius:var(--radius-lg);padding:20px}
.danger-head{display:flex;align-items:center;gap:8px;margin-bottom:8px}
.danger-head .iconify{width:18px;height:18px;color:var(--red)}
.danger-title{font-size:14px;font-weight:700;color:var(--red);font-family:'Space Grotesk',sans-serif}
.danger-desc{font-size:12px;color:var(--text-3);line-height:1.7;margin-bottom:14px}
.danger-btn{display:inline-flex;align-items:center;gap:8px;padding:9px 18px;border:1px solid rgba(244,63,94,.3);border-radius:var(--radius-sm);cursor:pointer;background:rgba(244,63,94,.08);color:var(--red);font-family:'Inter',sans-serif;font-size:13px;font-weight:600;transition:all .15s;text-decoration:none}
.danger-btn:hover{background:rgba(244,63,94,.15);border-color:rgba(244,63,94,.5);color:var(--red)}
.danger-btn .iconify{width:15px;height:15px}

/* Hint */
.hint{font-size:11px;color:var(--text-3);margin-top:5px;line-height:1.6;display:flex;align-items:flex-start;gap:5px}
.hint .iconify{width:12px;height:12px;flex-shrink:0;margin-top:1px;color:var(--text-3)}

/* ── Avatar upload ── */
.avatar-upload-wrap{display:flex;align-items:center;gap:16px;margin-bottom:16px}
.avatar-preview{width:64px;height:64px;border-radius:16px;background:linear-gradient(135deg,var(--brand),var(--purple));display:flex;align-items:center;justify-content:center;font-family:'Space Grotesk',sans-serif;font-size:22px;font-weight:700;color:#fff;flex-shrink:0;overflow:hidden;border:2px solid var(--border-2)}
.avatar-preview img{width:100%;height:100%;object-fit:cover;display:block}
.avatar-upload-btn{display:inline-flex;align-items:center;gap:7px;padding:8px 14px;border:1px solid var(--border);background:var(--surface-2);border-radius:var(--radius-sm);color:var(--text-2);font-size:12px;font-weight:600;cursor:pointer;transition:all .15s}
.avatar-upload-btn:hover{border-color:var(--brand);color:var(--brand-light)}
.avatar-upload-btn .iconify{width:14px;height:14px}
input[type="file"].file-input-hidden{display:none}

/* ── Sound selector ── */
.sound-presets{display:flex;gap:8px;margin-top:10px;flex-wrap:wrap}
.sound-preset-btn{
    display:inline-flex;align-items:center;gap:6px;
    padding:8px 14px;border-radius:var(--radius-sm);
    border:2px solid var(--border);background:var(--surface-2);
    color:var(--text-2);font-size:12px;font-weight:600;cursor:pointer;
    transition:all .15s;user-select:none;
}
.sound-preset-btn:hover{border-color:var(--border-2);color:var(--text)}
.sound-preset-btn.active{border-color:var(--brand);background:rgba(124,108,252,.12);color:var(--brand-light)}
.sound-preset-btn .iconify{width:14px;height:14px}
.sound-file-row{margin-top:12px;display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.sound-file-label{display:inline-flex;align-items:center;gap:7px;padding:8px 14px;border:1px solid var(--border);background:var(--surface-2);border-radius:var(--radius-sm);color:var(--text-2);font-size:12px;font-weight:600;cursor:pointer;transition:all .15s;flex-shrink:0}
.sound-file-label:hover{border-color:var(--brand-light);color:var(--brand-light)}
.sound-file-label .iconify{width:14px;height:14px}
.sound-file-name{font-size:11px;color:var(--text-3);min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.sound-play-btn{display:inline-flex;align-items:center;gap:5px;padding:8px 12px;border:1px solid rgba(34,211,160,.25);background:rgba(34,211,160,.06);border-radius:var(--radius-sm);color:var(--green);font-size:12px;font-weight:600;cursor:pointer;transition:all .15s;flex-shrink:0}
.sound-play-btn:hover{background:rgba(34,211,160,.12);border-color:var(--green)}
.sound-play-btn .iconify{width:13px;height:13px}
/* custom badge wrapper */
.sound-custom-badge{display:inline-flex;align-items:center;gap:0;position:relative}
.sound-custom-badge .sound-preset-btn{border-radius:var(--radius-sm) 0 0 var(--radius-sm);border-right:none}
.sound-custom-badge .sound-preset-btn.active{border-right:none}
.sound-custom-delete{display:inline-flex;align-items:center;justify-content:center;padding:0 8px;height:100%;border:2px solid var(--border);border-left:none;border-radius:0 var(--radius-sm) var(--radius-sm) 0;background:var(--surface-2);color:var(--text-3);cursor:pointer;transition:all .15s;flex-shrink:0}
.sound-custom-delete:hover{background:rgba(244,63,94,.12);border-color:rgba(244,63,94,.4);color:#f43f5e}
.sound-custom-badge.active .sound-custom-delete{border-color:var(--brand);background:rgba(124,108,252,.12)}
.sound-custom-badge.active .sound-custom-delete:hover{background:rgba(244,63,94,.15);border-color:rgba(244,63,94,.5);color:#f43f5e}
.sound-custom-delete .iconify{width:14px;height:14px}

/* Extend theme previews */

.theme-preview[data-theme="fire"]{
    background:linear-gradient(135deg,#0c0402,#1a0800);
    border-color:rgba(249,115,22,.5);color:#f97316;
}
.theme-preview[data-theme="fire"].active{border-color:#f97316;box-shadow:0 0 14px rgba(249,115,22,.4)}
.theme-preview[data-theme="ice"]{
    background:linear-gradient(135deg,#02060e,#050e1e);
    border-color:rgba(56,189,248,.4);color:#38bdf8;
}
.theme-preview[data-theme="ice"].active{border-color:#38bdf8;box-shadow:0 0 14px rgba(56,189,248,.3)}

/* OBS Widgets Preview */
.obs-section{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:24px;margin-top:16px}
.obs-section-head{display:flex;align-items:center;gap:8px;margin-bottom:4px;padding-bottom:14px;border-bottom:1px solid var(--border)}
.obs-section-head .iconify{width:18px;height:18px;color:var(--purple)}
.obs-section-title{font-family:'Space Grotesk',sans-serif;font-size:14px;font-weight:700}
.obs-widgets-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-top:16px}
.obs-widget-card{background:var(--surface-2);border:1px solid var(--border);border-radius:var(--radius);padding:16px;transition:border-color .2s}
.obs-widget-card:hover{border-color:var(--border-2)}
.obs-widget-icon{width:32px;height:32px;border-radius:8px;display:flex;align-items:center;justify-content:center;margin-bottom:10px;border:1px solid transparent}
.obs-widget-icon .iconify{width:18px;height:18px}
.obs-widget-name{font-size:12px;font-weight:700;margin-bottom:3px;letter-spacing:-.2px}
.obs-widget-desc{font-size:10px;color:var(--text-3);margin-bottom:10px;line-height:1.6}
.obs-widget-url-row{display:flex;gap:5px}
.obs-widget-url-input{flex:1;font-size:10px;padding:7px 9px;background:var(--surface-3);border:1px solid var(--border);border-radius:6px;color:var(--text-3);font-family:monospace;outline:none;min-width:0}
.obs-widget-actions{display:flex;gap:5px;margin-top:6px}
.obs-copy-btn{flex:1;padding:7px 0;background:var(--surface-3);border:1px solid var(--border);border-radius:6px;color:var(--text-2);cursor:pointer;font-size:10px;font-weight:700;transition:all .15s;display:inline-flex;align-items:center;justify-content:center;gap:4px}
.obs-copy-btn:hover{background:var(--brand);border-color:var(--brand);color:#fff}
.obs-copy-btn .iconify{width:11px;height:11px}
.obs-open-btn{flex:1;padding:7px 0;background:transparent;border:1px solid var(--border);border-radius:6px;color:var(--text-3);cursor:pointer;font-size:10px;font-weight:700;transition:all .15s;display:inline-flex;align-items:center;justify-content:center;gap:4px;text-decoration:none}
.obs-open-btn:hover{border-color:var(--border-2);color:var(--text)}
.obs-open-btn .iconify{width:11px;height:11px}
.obs-hint{font-size:11px;color:var(--text-3);margin-top:12px;display:flex;align-items:flex-start;gap:6px;line-height:1.6}
.obs-hint .iconify{width:13px;height:13px;flex-shrink:0;margin-top:1px;color:var(--text-3)}
@media(max-width:860px){.obs-widgets-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:500px){.obs-widgets-grid{grid-template-columns:1fr}}

@media(max-width:720px){.settings-grid{grid-template-columns:1fr}}
</style>
@endpush

<div class="settings-wrap">

    <div class="settings-header">
        <div class="settings-title">Settings</div>
        <div class="settings-sub">Konfigurasi profil, alert, milestone, dan leaderboard streamer kamu</div>
    </div>

    <form method="POST" action="{{ route('streamer.settings.update') }}" enctype="multipart/form-data">
        @csrf

        @if($errors->any())
            <div style="background:rgba(244,63,94,.08);border:1px solid rgba(244,63,94,.2);border-radius:var(--radius);padding:12px 16px;margin-bottom:20px;font-size:13px;color:#f43f5e;line-height:1.8">
                @foreach($errors->all() as $err)<div>• {{ $err }}</div>@endforeach
            </div>
        @endif

        <div class="settings-grid">

            {{-- Profil --}}
            <div class="s-card">
                <div class="s-card-head">
                    <div class="s-card-icon" style="background:rgba(124,108,252,.1);border-color:rgba(124,108,252,.2)">
                        <span class="iconify" data-icon="solar:user-bold-duotone" style="color:var(--brand-light)"></span>
                    </div>
                    <div class="s-card-title">Profil</div>
                </div>
                <div class="form-group">
                    <label>Nama Tampilan</label>
                    <input type="text" name="display_name"
                           value="{{ old('display_name', $streamer->display_name) }}"
                           required maxlength="60" placeholder="Nama streamer kamu" />
                </div>
                <div class="form-group">
                    <label>Bio</label>
                    <textarea name="bio" maxlength="200" placeholder="Deskripsi singkat tentang kamu...">{{ old('bio', $streamer->bio) }}</textarea>
                </div>
                <div class="form-group">
                    <label>Foto Profil</label>
                    <div class="avatar-upload-wrap">
                        <div class="avatar-preview" id="avatar-preview">
                            @if($streamer->avatar)
                                <img src="{{ asset('storage/' . $streamer->avatar) }}" alt="Avatar" id="avatar-img" />
                            @else
                                <span id="avatar-initials">{{ strtoupper(substr($streamer->display_name, 0, 2)) }}</span>
                            @endif
                        </div>
                        <div>
                            <label class="avatar-upload-btn" for="avatar-file-input">
                                <span class="iconify" data-icon="solar:camera-bold-duotone"></span>
                                Ganti Foto
                            </label>
                            <input type="file" id="avatar-file-input" name="avatar" accept="image/*" class="file-input-hidden" />
                            <div class="hint" style="margin-top:6px">
                                <span class="iconify" data-icon="solar:info-circle-bold"></span>
                                JPG/PNG/GIF, maks 2MB
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Minimum Donasi (Rp)</label>
                    <input type="number" name="min_donation"
                           value="{{ old('min_donation', $streamer->min_donation) }}"
                           min="100" required placeholder="1000" />
                </div>
                <div class="form-group">
                    <label>Pesan Terima Kasih</label>
                    <textarea name="thank_you_message" id="thank_you_message" maxlength="200"
                              placeholder="Terima kasih atas donasinya!">{{ old('thank_you_message', $streamer->thank_you_message) }}</textarea>
                    <div class="thankyou-preview-label">
                        <span class="iconify" data-icon="solar:eye-bold-duotone"></span>
                        Preview
                    </div>
                    <div class="thankyou-preview" id="thankyou-preview">{{ old('thank_you_message', $streamer->thank_you_message) }}</div>
                </div>
            </div>

            {{-- Alert --}}
            <div class="s-card">
                <div class="s-card-head">
                    <div class="s-card-icon" style="background:rgba(249,115,22,.1);border-color:rgba(249,115,22,.2)">
                        <span class="iconify" data-icon="solar:bell-bold-duotone" style="color:var(--orange)"></span>
                    </div>
                    <div class="s-card-title">Alert</div>
                </div>
                <div class="form-group">
                    <label>Durasi Alert (ms)</label>
                    <input type="number" name="alert_duration"
                           value="{{ old('alert_duration', $streamer->alert_duration) }}"
                           min="3000" max="30000" step="1000" required />
                    <div class="hint">
                        <span class="iconify" data-icon="solar:info-circle-bold"></span>
                        Berapa lama alert ditampilkan. 1000 ms = 1 detik. Rekomendasi: 8000
                    </div>
                </div>
                <div class="form-group">
                    <label>Tema Alert</label>
                    <div class="theme-select-wrap">
                        <select name="alert_theme" id="alert_theme" onchange="updateThemePreview(this.value)">
                            <option value="default"  {{ old('alert_theme', $streamer->alert_theme) === 'default'  ? 'selected' : '' }}>Default (Dark Purple)</option>
                            <option value="minimal"  {{ old('alert_theme', $streamer->alert_theme) === 'minimal'  ? 'selected' : '' }}>Minimal</option>
                            <option value="neon"     {{ old('alert_theme', $streamer->alert_theme) === 'neon'     ? 'selected' : '' }}>Neon</option>
                            <option value="fire"     {{ old('alert_theme', $streamer->alert_theme) === 'fire'     ? 'selected' : '' }}>Fire</option>
                            <option value="ice"      {{ old('alert_theme', $streamer->alert_theme) === 'ice'      ? 'selected' : '' }}>Ice</option>
                        </select>
                        <div class="theme-previews">
                            <div class="theme-preview {{ old('alert_theme', $streamer->alert_theme) === 'default' ? 'active' : '' }}" data-theme="default" onclick="selectTheme('default')">
                                <div class="theme-preview-dot"></div>Default
                            </div>
                            <div class="theme-preview {{ old('alert_theme', $streamer->alert_theme) === 'minimal' ? 'active' : '' }}" data-theme="minimal" onclick="selectTheme('minimal')">
                                <div class="theme-preview-dot"></div>Minimal
                            </div>
                            <div class="theme-preview {{ old('alert_theme', $streamer->alert_theme) === 'neon' ? 'active' : '' }}" data-theme="neon" onclick="selectTheme('neon')">
                                <div class="theme-preview-dot"></div>Neon
                            </div>
                            <div class="theme-preview {{ old('alert_theme', $streamer->alert_theme) === 'fire' ? 'active' : '' }}" data-theme="fire" onclick="selectTheme('fire')">
                                <div class="theme-preview-dot"></div>Fire
                            </div>
                            <div class="theme-preview {{ old('alert_theme', $streamer->alert_theme) === 'ice' ? 'active' : '' }}" data-theme="ice" onclick="selectTheme('ice')">
                                <div class="theme-preview-dot"></div>Ice
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Toggles --}}
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

                {{-- Sound section --}}
                <div class="form-group">
                    <label>Suara Alert</label>
                    <div class="toggle-row" style="border-bottom:none;padding:0 0 12px">
                        <div>
                            <div class="toggle-label">Aktifkan suara notifikasi</div>
                            <div class="toggle-sub">Mainkan suara saat donasi masuk di OBS</div>
                        </div>
                        <label class="toggle-switch {{ old('sound_enabled', $streamer->sound_enabled) ? 'on' : '' }}"
                               onclick="toggleSwitch(this,'sound_enabled')">
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
                        <button type="button" class="sound-preset-btn {{ $activePreset === 'ding' ? 'active' : '' }}" data-preset="ding" onclick="selectSoundPreset(this,'ding')">
                            <span class="iconify" data-icon="solar:bell-bold-duotone"></span>Ding
                        </button>
                        <button type="button" class="sound-preset-btn {{ $activePreset === 'coin' ? 'active' : '' }}" data-preset="coin" onclick="selectSoundPreset(this,'coin')">
                            <span class="iconify" data-icon="solar:star-bold-duotone"></span>Coin
                        </button>
                        <button type="button" class="sound-preset-btn {{ $activePreset === 'whoosh' ? 'active' : '' }}" data-preset="whoosh" onclick="selectSoundPreset(this,'whoosh')">
                            <span class="iconify" data-icon="solar:wind-bold-duotone"></span>Whoosh
                        </button>
                        {{-- Custom badge: always in DOM, hidden when no custom file --}}
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
                            @if($hasCustom)
                                {{ $customFileName }}
                            @else
                                Tidak ada file custom
                            @endif
                        </span>
                    </div>
                    <div class="hint" style="margin-top:8px">
                        <span class="iconify" data-icon="solar:info-circle-bold"></span>
                        Upload file audio custom untuk mengganti preset. Maks 5MB.
                    </div>
                </div>
            </div>

            {{-- Milestone --}}
            <div class="s-card">
                <div class="s-card-head">
                    <div class="s-card-icon" style="background:rgba(168,85,247,.1);border-color:rgba(168,85,247,.2)">
                        <span class="iconify" data-icon="solar:target-bold-duotone" style="color:var(--purple)"></span>
                    </div>
                    <div class="s-card-title">Milestone</div>
                </div>
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
                <div class="toggle-row">
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

            {{-- Leaderboard --}}
            <div class="s-card">
                <div class="s-card-head">
                    <div class="s-card-icon" style="background:rgba(251,191,36,.1);border-color:rgba(251,191,36,.2)">
                        <span class="iconify" data-icon="solar:ranking-bold-duotone" style="color:var(--yellow)"></span>
                    </div>
                    <div class="s-card-title">Leaderboard</div>
                </div>
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
                        Berapa donatur yang ditampilkan di widget leaderboard OBS.
                    </div>
                </div>
            </div>

        </div>

        <div style="margin-top:20px">
            <button type="submit" class="btn-primary">
                <span class="iconify" data-icon="solar:floppy-disk-bold-duotone" style="width:16px;height:16px"></span>
                Simpan Settings
            </button>
        </div>

    </form>

    {{-- API Key --}}
    <div class="api-section">
        <div class="api-section-head">
            <span class="iconify" data-icon="solar:key-bold-duotone"></span>
            <div class="api-section-title">API Key</div>
        </div>
        <div class="form-group">
            <label>API Key (digunakan di URL widget OBS)</label>
            <div class="api-key-row">
                <input class="api-key-input" readonly value="{{ $streamer->api_key }}" id="api-key-input" />
                <button type="button" class="copy-btn"
                        onclick="copyText(document.getElementById('api-key-input').value,'API Key')">
                    <span class="iconify" data-icon="solar:copy-bold-duotone"></span>Copy
                </button>
            </div>
        </div>
        <form method="POST" action="{{ route('streamer.regenerate-key') }}"
              onsubmit="return confirm('Yakin? URL widget OBS lama akan berhenti bekerja dan harus diupdate.')">
            @csrf
            <button type="submit" class="danger-btn">
                <span class="iconify" data-icon="solar:refresh-bold-duotone"></span>
                Regenerate API Key
            </button>
        </form>
        <div class="hint" style="margin-top:10px">
            <span class="iconify" data-icon="solar:shield-warning-bold-duotone" style="color:var(--yellow)"></span>
            Setelah regenerate, update semua URL di OBS Browser Source kamu dengan API key yang baru.
        </div>
    </div>

    {{-- OBS Widgets Preview --}}
    @if($streamer)
    <div class="obs-section">
        <div class="obs-section-head">
            <span class="iconify" data-icon="solar:monitor-bold-duotone"></span>
            <div class="obs-section-title">Widget OBS Browser Source</div>
        </div>
        <div style="font-size:12px;color:var(--text-3)">Copy URL di bawah lalu paste sebagai <strong style="color:var(--text-2)">Browser Source</strong> di OBS Studio.</div>
        @php
            $baseUrl  = config('app.url');
            $key      = $streamer->api_key;
            $slug     = $streamer->slug;
            $widgets  = [
                [
                    'id'    => 'obs-url-overlay',
                    'icon'  => 'solar:bell-bold-duotone',
                    'color' => 'var(--brand-light)',
                    'bg'    => 'rgba(124,108,252,.1)',
                    'bc'    => 'rgba(124,108,252,.2)',
                    'name'  => 'Alert Donasi',
                    'desc'  => 'Popup alert saat ada donasi masuk. Rekomen ukuran: 600×200px.',
                    'url'   => $baseUrl.'/'.$slug.'/obs/overlay?key='.$key,
                    'open'  => $baseUrl.'/'.$slug.'/obs/overlay?key='.$key,
                ],
                [
                    'id'    => 'obs-url-leaderboard',
                    'icon'  => 'solar:ranking-bold-duotone',
                    'color' => 'var(--yellow)',
                    'bg'    => 'rgba(251,191,36,.1)',
                    'bc'    => 'rgba(251,191,36,.2)',
                    'name'  => 'Leaderboard',
                    'desc'  => 'Panel top donatur real-time. Rekomen ukuran: 320×480px.',
                    'url'   => $baseUrl.'/'.$slug.'/obs/leaderboard?key='.$key,
                    'open'  => $baseUrl.'/'.$slug.'/obs/leaderboard?key='.$key,
                ],
                [
                    'id'    => 'obs-url-milestone',
                    'icon'  => 'solar:target-bold-duotone',
                    'color' => 'var(--green)',
                    'bg'    => 'rgba(34,211,160,.1)',
                    'bc'    => 'rgba(34,211,160,.2)',
                    'name'  => 'Milestone Bar',
                    'desc'  => 'Progress bar target donasi. Rekomen ukuran: 800×80px.',
                    'url'   => $baseUrl.'/'.$slug.'/obs/milestone?key='.$key,
                    'open'  => $baseUrl.'/'.$slug.'/obs/milestone?key='.$key,
                ],
                [
                    'id'    => 'obs-url-qr',
                    'icon'  => 'solar:qr-code-bold-duotone',
                    'color' => 'var(--purple)',
                    'bg'    => 'rgba(168,85,247,.1)',
                    'bc'    => 'rgba(168,85,247,.2)',
                    'name'  => 'QR Donasi',
                    'desc'  => 'QR code untuk di-scan penonton. Rekomen ukuran: 200×200px.',
                    'url'   => $baseUrl.'/'.$slug.'/obs/qr',
                    'open'  => $baseUrl.'/'.$slug.'/qr',
                ],
            ];
        @endphp
        <div class="obs-widgets-grid">
            @foreach($widgets as $w)
            <div class="obs-widget-card">
                <div class="obs-widget-icon" style="background:{{ $w['bg'] }};border-color:{{ $w['bc'] }}">
                    <span class="iconify" data-icon="{{ $w['icon'] }}" style="color:{{ $w['color'] }}"></span>
                </div>
                <div class="obs-widget-name">{{ $w['name'] }}</div>
                <div class="obs-widget-desc">{{ $w['desc'] }}</div>
                <input class="obs-widget-url-input" readonly value="{{ $w['url'] }}" id="{{ $w['id'] }}" />
                <div class="obs-widget-actions">
                    <button type="button" class="obs-copy-btn"
                            onclick="copyText(document.getElementById('{{ $w['id'] }}').value,'URL {{ $w['name'] }}')">
                        <span class="iconify" data-icon="solar:copy-bold-duotone"></span>Copy URL
                    </button>
                    <a href="{{ $w['open'] }}" target="_blank" class="obs-open-btn">
                        <span class="iconify" data-icon="solar:eye-bold-duotone"></span>Preview
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        <div class="obs-hint">
            <span class="iconify" data-icon="solar:info-circle-bold"></span>
            Jika kamu regenerate API Key, semua URL Alert, Leaderboard, dan Milestone di atas akan berubah — update ulang di OBS.
        </div>
    </div>
    @endif

    {{-- Danger Zone --}}
    <div class="danger-section">
        <div class="danger-head">
            <span class="iconify" data-icon="solar:shield-warning-bold-duotone"></span>
            <div class="danger-title">Danger Zone</div>
        </div>
        <div class="danger-desc">
            Aksi di bawah ini tidak bisa dibatalkan. Pastikan kamu yakin sebelum melanjutkan.
        </div>
        <a href="{{ route('profile.edit') }}" class="danger-btn">
            <span class="iconify" data-icon="solar:trash-bin-trash-bold-duotone"></span>
            Hapus Akun
        </a>
    </div>

</div>

@push('scripts')
<script>
function toggleSwitch(label, fieldName) {
    label.classList.toggle('on');
    const input = document.getElementById(fieldName);
    input.value = label.classList.contains('on') ? '1' : '0';
}

// ── Thank-you message live preview ──
document.addEventListener('DOMContentLoaded', function () {
    const textarea = document.getElementById('thank_you_message');
    const preview  = document.getElementById('thankyou-preview');
    if (textarea && preview) {
        textarea.addEventListener('input', function () {
            preview.textContent = this.value || '';
        });
    }
});

// ── Alert theme visual selector ──
function selectTheme(theme) {
    document.getElementById('alert_theme').value = theme;
    updateThemePreview(theme);
}

function updateThemePreview(theme) {
    document.querySelectorAll('.theme-preview').forEach(function (el) {
        el.classList.toggle('active', el.dataset.theme === theme);
    });
}

// ── Sound: state ──
var _customAudioCtx   = null;
var _customFileObject = null;   // File object dari <input type="file"> jika baru dipilih
var _customStoredUrl  = {!! $hasCustom ? json_encode($customSoundUrl) : 'null' !!}; // URL dari server

function _getAudioCtx() {
    if (!_customAudioCtx) _customAudioCtx = new (window.AudioContext || window.webkitAudioContext)();
    // Resume jika suspended (diperlukan di beberapa browser setelah idle)
    if (_customAudioCtx.state === 'suspended') _customAudioCtx.resume();
    return _customAudioCtx;
}

// ── Sound preset selector ──
function selectSoundPreset(btn, preset) {
    document.querySelectorAll('.sound-preset-btn').forEach(function (b) {
        b.classList.remove('active');
    });
    btn.classList.add('active');
    document.getElementById('notification_sound_preset').value = preset;

    var badgeWrap = document.getElementById('custom-sound-badge');
    if (badgeWrap) {
        if (preset === '__custom__') {
            badgeWrap.classList.add('active');
        } else {
            badgeWrap.classList.remove('active');
        }
    }
}

// ── Sound preview — IDENTIK dengan yang diputar di overlay OBS ──
// Supaya apa yang didengar di sini = apa yang muncul saat donasi masuk.
function previewSound() {
    var preset = document.getElementById('notification_sound_preset').value;
    if (preset === '__custom__') {
        _previewCustomSound();
        return;
    }
    var ctx = _getAudioCtx();
    if (preset === 'ding') {
        // Identik dengan playDing() di overlay
        var osc = ctx.createOscillator();
        var gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.type = 'sine';
        osc.frequency.setValueAtTime(880, ctx.currentTime);
        osc.frequency.exponentialRampToValueAtTime(1320, ctx.currentTime + 0.05);
        osc.frequency.exponentialRampToValueAtTime(660, ctx.currentTime + 0.4);
        gain.gain.setValueAtTime(0.6, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.6);
        osc.start(ctx.currentTime);
        osc.stop(ctx.currentTime + 0.6);
    } else if (preset === 'coin') {
        // Identik dengan playCoin() di overlay
        [0, 0.12].forEach(function(delay) {
            var osc = ctx.createOscillator();
            var gain = ctx.createGain();
            osc.connect(gain); gain.connect(ctx.destination);
            osc.type = 'square';
            osc.frequency.setValueAtTime(988, ctx.currentTime + delay);
            osc.frequency.setValueAtTime(1319, ctx.currentTime + delay + 0.07);
            gain.gain.setValueAtTime(0.35, ctx.currentTime + delay);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + delay + 0.3);
            osc.start(ctx.currentTime + delay);
            osc.stop(ctx.currentTime + delay + 0.3);
        });
    } else if (preset === 'whoosh') {
        // Identik dengan playWhoosh() di overlay
        var bufSize = ctx.sampleRate * 0.6;
        var buf = ctx.createBuffer(1, bufSize, ctx.sampleRate);
        var data = buf.getChannelData(0);
        for (var i = 0; i < bufSize; i++) data[i] = (Math.random() * 2 - 1);
        var src = ctx.createBufferSource();
        src.buffer = buf;
        var filter = ctx.createBiquadFilter();
        filter.type = 'bandpass';
        filter.frequency.setValueAtTime(400, ctx.currentTime);
        filter.frequency.exponentialRampToValueAtTime(2000, ctx.currentTime + 0.2);
        filter.frequency.exponentialRampToValueAtTime(200, ctx.currentTime + 0.6);
        filter.Q.value = 0.8;
        var g = ctx.createGain();
        g.gain.setValueAtTime(0, ctx.currentTime);
        g.gain.linearRampToValueAtTime(0.5, ctx.currentTime + 0.05);
        g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.6);
        src.connect(filter); filter.connect(g); g.connect(ctx.destination);
        src.start(ctx.currentTime);
        src.stop(ctx.currentTime + 0.6);
    }
}

function _previewCustomSound() {
    var ctx = _getAudioCtx();
    function _playBuf(decoded) {
        var src  = ctx.createBufferSource();
        var gain = ctx.createGain();
        src.buffer = decoded;
        src.connect(gain); gain.connect(ctx.destination);
        gain.gain.setValueAtTime(0.9, ctx.currentTime);
        src.start(ctx.currentTime);
    }
    if (_customFileObject) {
        var reader = new FileReader();
        reader.onload = function (e) {
            ctx.decodeAudioData(e.target.result, _playBuf, function() {
                alert('Format file tidak didukung.');
            });
        };
        reader.readAsArrayBuffer(_customFileObject);
    } else if (_customStoredUrl) {
        fetch(_customStoredUrl)
            .then(function (r) { return r.arrayBuffer(); })
            .then(function (buf) { return ctx.decodeAudioData(buf); })
            .then(_playBuf)
            .catch(function () { alert('Gagal memutar file custom.'); });
    }
}

// ── Delete custom sound ──
function deleteCustomSound() {
    if (!confirm('Hapus file audio custom? Suara akan kembali ke preset Ding.')) return;

    // Reset UI
    var badgeWrap = document.getElementById('custom-sound-badge');
    if (badgeWrap) badgeWrap.style.display = 'none';

    document.getElementById('sound-file-name').textContent = 'Tidak ada file custom';
    document.getElementById('delete_sound').value = '1';
    document.getElementById('notification_sound_preset').value = 'ding';

    // Activate ding badge
    document.querySelectorAll('.sound-preset-btn').forEach(function (b) { b.classList.remove('active'); });
    var dingBtn = document.querySelector('.sound-preset-btn[data-preset="ding"]');
    if (dingBtn) dingBtn.classList.add('active');

    // Clear file input + state
    document.getElementById('sound-file-input').value = '';
    _customFileObject = null;
    _customStoredUrl  = null;
}

// ── Avatar file preview ──
document.addEventListener('DOMContentLoaded', function () {
    var avatarInput = document.getElementById('avatar-file-input');
    if (avatarInput) {
        avatarInput.addEventListener('change', function () {
            if (!this.files || !this.files[0]) return;
            var reader = new FileReader();
            reader.onload = function (e) {
                var preview = document.getElementById('avatar-preview');
                preview.innerHTML = '<img src="' + e.target.result + '" alt="Avatar" style="width:100%;height:100%;object-fit:cover;border-radius:inherit" />';
            };
            reader.readAsDataURL(this.files[0]);
        });
    }

    // ── Sound file name display + custom badge ──
    var soundInput = document.getElementById('sound-file-input');
    if (soundInput) {
        soundInput.addEventListener('change', function () {
            var nameEl     = document.getElementById('sound-file-name');
            var badgeWrap  = document.getElementById('custom-sound-badge');
            var badgeLabel = document.getElementById('custom-badge-label');
            var badgeBtn   = document.getElementById('custom-badge-btn');
            if (this.files && this.files[0]) {
                var fileName = this.files[0].name;
                _customFileObject = this.files[0];
                nameEl.textContent = fileName;
                if (badgeLabel) badgeLabel.textContent = fileName;
                // Show badge wrapper and activate it
                if (badgeWrap) {
                    badgeWrap.style.display = '';
                    badgeWrap.classList.add('active');
                }
                // Activate custom badge btn, deactivate presets
                document.querySelectorAll('.sound-preset-btn').forEach(function (b) { b.classList.remove('active'); });
                if (badgeBtn) badgeBtn.classList.add('active');
                document.getElementById('notification_sound_preset').value = '__custom__';
                // Cancel any pending delete
                document.getElementById('delete_sound').value = '0';
            } else {
                _customFileObject = null;
                nameEl.textContent = 'Tidak ada file custom';
            }
        });
    }
});
</script>
@endpush
</x-app-layout>
