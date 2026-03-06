<x-guest-layout>
    <div class="auth-title">Masuk</div>
    <div class="auth-sub">Masuk ke akun StreamDonate kamu</div>

    @if (session('status'))
        <div style="background:rgba(34,211,160,.1);border:1px solid rgba(34,211,160,.3);color:#22d3a0;padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:18px;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   required autofocus autocomplete="username"
                   placeholder="kamu@email.com">
            @error('email')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input id="password" type="password" name="password"
                   required autocomplete="current-password"
                   placeholder="••••••••">
            @error('password')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="checkbox-row">
                <input type="checkbox" name="remember">
                Ingat saya
            </label>
        </div>

        <button type="submit" class="btn-submit">
            <span class="iconify" data-icon="solar:login-bold-duotone"></span>
            Masuk
        </button>

        <div class="auth-footer">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Lupa password?</a>
            @endif
        </div>
    </form>
</x-guest-layout>
