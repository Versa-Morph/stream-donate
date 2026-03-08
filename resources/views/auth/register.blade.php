<x-guest-layout>
    <div class="auth-title">Daftar</div>
    <div class="auth-sub">Buat akun streamer baru</div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="form-group">
            <label for="name">Nama</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}"
                   required autofocus autocomplete="name"
                   placeholder="Nama kamu">
            @error('name')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   required autocomplete="username"
                   placeholder="kamu@email.com">
            @error('email')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">Password</label>
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
                   placeholder="Ulangi password">
            @error('password_confirmation')
                <div class="error-msg">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn-submit">Daftar</button>

        <div class="auth-footer">
            Sudah punya akun? <a href="{{ route('login') }}">Masuk</a>
        </div>
    </form>
</x-guest-layout>
