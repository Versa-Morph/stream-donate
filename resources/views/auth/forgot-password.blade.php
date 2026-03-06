<x-guest-layout>
    <div class="auth-title">Lupa Password</div>
    <div class="auth-sub">Masukkan email kamu dan kami akan mengirimkan link reset password.</div>

    @if (session('status'))
        <div style="background:rgba(34,211,160,.1);border:1px solid rgba(34,211,160,.3);color:#22d3a0;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:18px;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   required autofocus placeholder="kamu@email.com">
            @error('email')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-submit">Kirim Link Reset Password</button>

        <div class="auth-footer">
            <a href="{{ route('login') }}">Kembali ke login</a>
        </div>
    </form>
</x-guest-layout>
