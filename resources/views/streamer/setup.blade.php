<x-app-layout>
@push('styles')
<style>
.setup-wrap{padding:28px;max-width:600px;margin:0 auto}

/* header */
.setup-header{margin-bottom:32px;text-align:center;animation:slideDown .45s ease both}
.setup-icon{width:64px;height:64px;border-radius:20px;background:linear-gradient(135deg,var(--brand),var(--purple));display:flex;align-items:center;justify-content:center;font-size:28px;margin:0 auto 16px;box-shadow:0 0 32px var(--brand-glow)}
.setup-icon .iconify{font-size:30px;color:#fff}
.setup-title{font-family:'Space Grotesk',sans-serif;font-size:26px;font-weight:700;letter-spacing:-.5px;margin-bottom:6px}
.setup-sub{font-size:14px;color:var(--text-3);line-height:1.6}

/* step indicator */
.step-indicator{display:flex;align-items:center;justify-content:center;gap:0;margin-bottom:32px;animation:slideDown .5s .05s ease both;opacity:0;animation-fill-mode:forwards}
.step-item{display:flex;flex-direction:column;align-items:center;gap:6px;position:relative}
.step-dot{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;border:2px solid var(--border);background:var(--surface);color:var(--text-3);transition:all .3s}
.step-item.active .step-dot{background:var(--brand);border-color:var(--brand);color:#fff;box-shadow:0 0 12px var(--brand-glow)}
.step-item.done .step-dot{background:var(--green);border-color:var(--green);color:#fff}
.step-label{font-size:11px;color:var(--text-3);white-space:nowrap}
.step-item.active .step-label{color:var(--brand-light);font-weight:600}
.step-line{width:72px;height:2px;background:var(--border);margin:0 4px;margin-bottom:22px;flex-shrink:0}

/* card */
.setup-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:32px;animation:slideUp .45s .1s ease both;opacity:0;animation-fill-mode:forwards}

/* form */
.form-group{margin-bottom:20px}

.slug-preview{font-size:12px;color:var(--text-3);margin-top:8px}
.slug-preview span{color:var(--brand-light);font-weight:600}
.hint{font-size:11px;color:var(--text-3);margin-top:6px;line-height:1.6}

/* submit */
.setup-submit{width:100%;margin-top:8px;display:flex;align-items:center;justify-content:center;gap:8px}
.setup-submit .iconify{font-size:18px}
</style>
@endpush

<div class="setup-wrap">
    <div class="setup-header">
        <div class="setup-icon">
            <span class="iconify" data-icon="solar:gamepad-bold-duotone"></span>
        </div>
        <div class="setup-title">Setup Profil Streamer</div>
        <div class="setup-sub">Isi informasi dasar profil kamu untuk mulai menerima donasi</div>
    </div>

    <div class="step-indicator">
        <div class="step-item active">
            <div class="step-dot">1</div>
            <div class="step-label">Isi Data</div>
        </div>
        <div class="step-line"></div>
        <div class="step-item">
            <div class="step-dot">2</div>
            <div class="step-label">Review</div>
        </div>
        <div class="step-line"></div>
        <div class="step-item">
            <div class="step-dot">3</div>
            <div class="step-label">Selesai</div>
        </div>
    </div>

    <div class="setup-card">
        <form method="POST" action="{{ route('streamer.setup.store') }}">
            @csrf

            @if($errors->any())
                <div style="background:rgba(244,63,94,.08);border:1px solid rgba(244,63,94,.2);border-radius:var(--radius);padding:12px 16px;margin-bottom:20px;font-size:13px;color:#f43f5e">
                    @foreach($errors->all() as $err)
                        <div>• {{ $err }}</div>
                    @endforeach
                </div>
            @endif

            <div class="form-group">
                <label>Nama Tampilan</label>
                <div class="input-icon-wrap">
                    <span class="iconify" data-icon="solar:user-bold-duotone"></span>
                    <input type="text" name="display_name" value="{{ old('display_name') }}"
                        placeholder="Nama streamer kamu…" maxlength="60" required />
                </div>
                <div class="hint">Nama ini akan ditampilkan ke donatur di form donasi.</div>
            </div>

            <div class="form-group">
                <label>Slug (URL unik)</label>
                <div class="input-icon-wrap">
                    <span class="iconify" data-icon="solar:link-bold-duotone"></span>
                    <input type="text" name="slug" id="slug-input" value="{{ old('slug') }}"
                        placeholder="nama-streamer" maxlength="40" pattern="[a-z0-9\-]+"
                        oninput="updateSlugPreview()" required />
                </div>
                <div class="slug-preview">
                    URL form donasi: {{ config('app.url') }}/<span id="slug-preview">nama-streamer</span>
                </div>
                <div class="hint">Hanya huruf kecil, angka, dan tanda hubung (-). Tidak bisa diubah nanti.</div>
            </div>

            <div class="form-group">
                <label>Bio <span style="color:var(--text-3);font-weight:400">(opsional)</span></label>
                <div class="input-icon-wrap textarea-wrap">
                    <span class="iconify" data-icon="solar:document-text-bold-duotone"></span>
                    <textarea name="bio" placeholder="Ceritakan sedikit tentang kamu…" maxlength="200">{{ old('bio') }}</textarea>
                </div>
                <div class="hint">Max 200 karakter. Ditampilkan di halaman form donasi.</div>
            </div>

            <button type="submit" class="btn-primary setup-submit">
                <span class="iconify" data-icon="solar:rocket-bold-duotone"></span>
                Buat Profil Streamer
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function updateSlugPreview() {
    const raw = document.getElementById('slug-input').value;
    const clean = raw.toLowerCase().replace(/[^a-z0-9-]/g, '');
    // also keep input clean in real-time
    if (raw !== clean) {
        const pos = document.getElementById('slug-input').selectionStart;
        document.getElementById('slug-input').value = clean;
        document.getElementById('slug-input').setSelectionRange(pos, pos);
    }
    document.getElementById('slug-preview').textContent = clean || 'nama-streamer';
}
updateSlugPreview();
</script>
@endpush
</x-app-layout>
