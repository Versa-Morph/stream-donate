<x-guest-layout>
    <div class="auth-title">Reset Password</div>
    <div class="auth-sub">Masukkan password baru kamu</div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email"
                   value="{{ old('email', $request->email) }}"
                   required autofocus autocomplete="username"
                   placeholder="kamu@email.com">
            @error('email')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Password Baru</label>
            <input id="password" type="password" name="password"
                   required autocomplete="new-password"
                   placeholder="Min. 8 karakter">
            @error('password')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password_confirmation">Konfirmasi Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   required autocomplete="new-password"
                   placeholder="Ulangi password baru">
            @error('password_confirmation')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-submit">Reset Password</button>
    </form>
</x-guest-layout>
