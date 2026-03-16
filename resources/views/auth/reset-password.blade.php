<x-guest-layout>
    <div style="text-align:center;margin-bottom:20px;">
        <div style="width:52px;height:52px;border-radius:16px;background:linear-gradient(135deg,rgba(124,108,252,.2),rgba(168,85,247,.15));border:1px solid rgba(124,108,252,.25);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;box-shadow:0 0 24px rgba(124,108,252,.12);">
            <span class="iconify" data-icon="solar:key-bold-duotone" style="font-size:24px;color:var(--brand-light);"></span>
        </div>
        <div class="auth-title" style="text-align:center;">Buat Password Baru</div>
        <div class="auth-sub" style="text-align:center;">Masukkan password baru kamu di bawah ini.</div>
    </div>

    <form method="POST" action="{{ route('password.store') }}" id="reset-form">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="form-group">
            <label for="email">
                <span class="iconify" data-icon="solar:letter-bold-duotone"></span>
                Alamat Email
            </label>
            <input id="email" type="email" name="email"
                   value="{{ old('email', $request->email) }}"
                   required autofocus autocomplete="username"
                   placeholder="kamu@email.com"
                   class="{{ $errors->has('email') ? 'is-invalid' : '' }}">
            @error('email')
                <div class="error-msg">
                    <span class="iconify" data-icon="solar:danger-circle-bold-duotone"></span>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">
                <span class="iconify" data-icon="solar:lock-password-bold-duotone"></span>
                Password Baru
            </label>
            <div class="input-wrap">
                <input id="password" type="password" name="password"
                       required autocomplete="new-password"
                       placeholder="Min. 8 karakter"
                       class="{{ $errors->has('password') ? 'is-invalid' : '' }}">
                <button type="button" class="eye-btn" onclick="togglePass('password', this)" tabindex="-1">
                    <span class="iconify" data-icon="solar:eye-bold"></span>
                </button>
            </div>
            @error('password')
                <div class="error-msg">
                    <span class="iconify" data-icon="solar:danger-circle-bold-duotone"></span>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">
                <span class="iconify" data-icon="solar:lock-keyhole-bold-duotone"></span>
                Konfirmasi Password
            </label>
            <div class="input-wrap">
                <input id="password_confirmation" type="password" name="password_confirmation"
                       required autocomplete="new-password"
                       placeholder="Ulangi password baru">
                <button type="button" class="eye-btn" onclick="togglePass('password_confirmation', this)" tabindex="-1">
                    <span class="iconify" data-icon="solar:eye-bold"></span>
                </button>
            </div>
            @error('password_confirmation')
                <div class="error-msg">
                    <span class="iconify" data-icon="solar:danger-circle-bold-duotone"></span>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button type="submit" class="btn-submit" id="btn-reset">
            <span class="iconify" data-icon="solar:shield-check-bold-duotone"></span>
            Reset Password
        </button>
    </form>

    <div class="auth-footer">
        <a href="{{ route('login') }}">
            <span class="iconify" style="font-size:12px;vertical-align:middle;margin-right:3px;" data-icon="solar:arrow-left-bold"></span>
            Kembali ke halaman masuk
        </a>
    </div>

    <script>
        function togglePass(id, btn) {
            const input = document.getElementById(id);
            const icon  = btn.querySelector('.iconify');
            if (input.type === 'password') {
                input.type = 'text';
                icon.setAttribute('data-icon', 'solar:eye-closed-bold');
            } else {
                input.type = 'password';
                icon.setAttribute('data-icon', 'solar:eye-bold');
            }
        }

        document.getElementById('reset-form')?.addEventListener('submit', function() {
            const btn = document.getElementById('btn-reset');
            btn.disabled = true;
            btn.innerHTML = '<span class="iconify spin" data-icon="solar:spinner-bold-duotone"></span> Menyimpan...';
        });
    </script>
</x-guest-layout>
