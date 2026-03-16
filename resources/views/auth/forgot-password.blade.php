<x-guest-layout>
    <div style="text-align:center;margin-bottom:20px;">
        <div style="width:52px;height:52px;border-radius:16px;background:linear-gradient(135deg,rgba(124,108,252,.2),rgba(168,85,247,.15));border:1px solid rgba(124,108,252,.25);display:flex;align-items:center;justify-content:center;margin:0 auto 14px;box-shadow:0 0 24px rgba(124,108,252,.12);">
            <span class="iconify" data-icon="solar:lock-password-bold-duotone" style="font-size:24px;color:var(--brand-light);"></span>
        </div>
        <div class="auth-title" style="text-align:center;">Lupa Password?</div>
        <div class="auth-sub" style="text-align:center;">Masukkan email kamu dan kami akan mengirimkan link reset password.</div>
    </div>

    @if (session('status'))
        <div class="alert-success" style="margin-bottom:20px;">
            <span class="iconify" data-icon="solar:check-circle-bold-duotone"></span>
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" id="forgot-form">
        @csrf

        <div class="form-group">
            <label for="email">
                <span class="iconify" data-icon="solar:letter-bold-duotone"></span>
                Alamat Email
            </label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   required autofocus placeholder="kamu@email.com"
                   class="{{ $errors->has('email') ? 'is-invalid' : '' }}">
            @error('email')
                <div class="error-msg">
                    <span class="iconify" data-icon="solar:danger-circle-bold-duotone"></span>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button type="submit" class="btn-submit" id="btn-forgot">
            <span class="iconify" data-icon="solar:letter-bold-duotone"></span>
            Kirim Link Reset Password
        </button>
    </form>

    <div class="auth-footer">
        <a href="{{ route('login') }}">
            <span class="iconify" style="font-size:12px;vertical-align:middle;margin-right:3px;" data-icon="solar:arrow-left-bold"></span>
            Kembali ke halaman masuk
        </a>
    </div>

    <script>
        document.getElementById('forgot-form')?.addEventListener('submit', function() {
            const btn = document.getElementById('btn-forgot');
            btn.disabled = true;
            btn.innerHTML = '<span class="iconify spin" data-icon="solar:spinner-bold-duotone"></span> Mengirim...';
        });
    </script>
</x-guest-layout>
