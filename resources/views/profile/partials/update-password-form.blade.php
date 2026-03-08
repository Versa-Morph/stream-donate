<form method="post" action="{{ route('password.update') }}" style="display:flex;flex-direction:column;gap:16px">
    @csrf
    @method('put')

    <div class="form-group" style="margin-bottom:0">
        <label for="update_password_current_password">Password Saat Ini</label>
        <div class="input-icon-wrap">
            <span class="iconify" data-icon="solar:lock-password-bold-duotone"></span>
            <input id="update_password_current_password" name="current_password" type="password"
                autocomplete="current-password" placeholder="Password lama kamu…" />
        </div>
        @error('current_password', 'updatePassword')
            <div style="font-size:12px;color:#f43f5e;margin-top:5px">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group" style="margin-bottom:0">
        <label for="update_password_password">Password Baru</label>
        <div class="input-icon-wrap">
            <span class="iconify" data-icon="solar:key-bold-duotone"></span>
            <input id="update_password_password" name="password" type="password"
                autocomplete="new-password" placeholder="Password baru…" />
        </div>
        @error('password', 'updatePassword')
            <div style="font-size:12px;color:#f43f5e;margin-top:5px">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group" style="margin-bottom:0">
        <label for="update_password_password_confirmation">Konfirmasi Password</label>
        <div class="input-icon-wrap">
            <span class="iconify" data-icon="solar:key-bold-duotone"></span>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                autocomplete="new-password" placeholder="Ulangi password baru…" />
        </div>
        @error('password_confirmation', 'updatePassword')
            <div style="font-size:12px;color:#f43f5e;margin-top:5px">{{ $message }}</div>
        @enderror
    </div>

    <div style="display:flex;align-items:center;gap:12px;padding-top:4px">
        <button type="submit" class="btn-primary" style="display:flex;align-items:center;gap:6px;padding:10px 20px">
            <span class="iconify" data-icon="solar:floppy-disk-bold-duotone"></span>
            Simpan Password
        </button>
        @if (session('status') === 'password-updated')
            <span class="profile-saved" id="password-saved-msg">
                <span class="iconify" data-icon="solar:check-circle-bold-duotone"></span>
                Password diperbarui!
            </span>
            <script>setTimeout(()=>{const el=document.getElementById('password-saved-msg');if(el)el.style.opacity='0'},2500)</script>
        @endif
    </div>
</form>
