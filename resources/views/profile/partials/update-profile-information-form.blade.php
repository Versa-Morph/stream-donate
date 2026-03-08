<form id="send-verification" method="post" action="{{ route('verification.send') }}">
    @csrf
</form>

<form method="post" action="{{ route('profile.update') }}" style="display:flex;flex-direction:column;gap:16px">
    @csrf
    @method('patch')

    <div class="form-group" style="margin-bottom:0">
        <label for="name">Nama</label>
        <div class="input-icon-wrap">
            <span class="iconify" data-icon="solar:user-bold-duotone"></span>
            <input id="name" name="name" type="text"
                value="{{ old('name', $user->name) }}"
                required autofocus autocomplete="name"
                placeholder="Nama kamu…" />
        </div>
        @error('name')
            <div style="font-size:12px;color:#f43f5e;margin-top:5px">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group" style="margin-bottom:0">
        <label for="email">Email</label>
        <div class="input-icon-wrap">
            <span class="iconify" data-icon="solar:letter-bold-duotone"></span>
            <input id="email" name="email" type="email"
                value="{{ old('email', $user->email) }}"
                required autocomplete="username"
                placeholder="email@kamu.com" />
        </div>
        @error('email')
            <div style="font-size:12px;color:#f43f5e;margin-top:5px">{{ $message }}</div>
        @enderror

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div style="margin-top:8px;font-size:12px;color:var(--text-3)">
                Email belum terverifikasi.
                <button form="send-verification" style="background:none;border:none;color:var(--brand-light);font-size:12px;cursor:pointer;padding:0;text-decoration:underline">
                    Kirim ulang email verifikasi
                </button>
                @if (session('status') === 'verification-link-sent')
                    <span style="color:var(--green);display:block;margin-top:4px">Link verifikasi berhasil dikirim.</span>
                @endif
            </div>
        @endif
    </div>

    <div style="display:flex;align-items:center;gap:12px;padding-top:4px">
        <button type="submit" class="btn-primary" style="display:flex;align-items:center;gap:6px;padding:10px 20px">
            <span class="iconify" data-icon="solar:floppy-disk-bold-duotone"></span>
            Simpan Perubahan
        </button>
        @if (session('status') === 'profile-updated')
            <span class="profile-saved" id="profile-saved-msg">
                <span class="iconify" data-icon="solar:check-circle-bold-duotone"></span>
                Tersimpan!
            </span>
            <script>setTimeout(()=>{const el=document.getElementById('profile-saved-msg');if(el)el.style.opacity='0'},2500)</script>
        @endif
    </div>
</form>
