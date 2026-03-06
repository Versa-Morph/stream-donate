<x-app-layout>
    @push('styles')
    <style>
        .admin-wrap { max-width: 560px; margin: 0 auto; padding: 28px 28px 48px; }
        .page-header { margin-bottom: 24px; }
        .page-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 24px; font-weight: 700; letter-spacing: -.5px; color: var(--text);
        }
        .page-subtitle { font-size: 13px; color: var(--text-3); margin-top: 4px; }

        .create-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-xl);
            padding: 28px 32px;
        }

        .form-grid { display: grid; gap: 18px; }

        .error-msg {
            font-size: 11px; color: var(--red);
            margin-top: 5px; font-weight: 500;
        }

        .role-cards {
            display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
        }
        .role-card {
            border: 2px solid var(--border);
            border-radius: var(--radius);
            padding: 14px 16px;
            cursor: pointer;
            transition: all .15s;
            position: relative;
        }
        .role-card input[type="radio"] {
            position: absolute; opacity: 0; width: 0; height: 0;
        }
        .role-card:has(input:checked) {
            border-color: var(--brand);
            background: rgba(124,108,252,.06);
        }
        .role-card-title {
            font-weight: 700; font-size: 13px; color: var(--text);
        }
        .role-card-desc { font-size: 11px; color: var(--text-3); margin-top: 3px; }

        .form-footer {
            display: flex; gap: 10px; align-items: center;
            justify-content: flex-end; margin-top: 4px;
        }
    </style>
    @endpush

    <div class="admin-wrap">
        <!-- Breadcrumb -->
        <div style="margin-bottom:16px; font-size:12px; color:var(--text-3)">
            <a href="{{ route('admin.users') }}" style="color:var(--brand-light)">Users</a>
            <span style="margin:0 6px">/</span>
            <span>Tambah User Baru</span>
        </div>

        <div class="page-header">
            <h1 class="page-title">Tambah User Baru</h1>
            <p class="page-subtitle">Buat akun admin atau streamer baru</p>
        </div>

        <div class="create-card">
            <form method="POST" action="{{ route('admin.users.store') }}" class="form-grid">
                @csrf

                <!-- Nama -->
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <input type="text" id="name" name="name"
                        value="{{ old('name') }}"
                        placeholder="Nama user"
                        required autofocus>
                    @error('name')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                        value="{{ old('email') }}"
                        placeholder="email@contoh.com"
                        required>
                    @error('email')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password"
                        placeholder="Min. 8 karakter"
                        required minlength="8">
                    @error('password')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Role -->
                <div class="form-group">
                    <label>Role</label>
                    <div class="role-cards">
                        <label class="role-card">
                            <input type="radio" name="role" value="streamer"
                                {{ old('role', 'streamer') === 'streamer' ? 'checked' : '' }}>
                            <div class="role-card-title">Streamer</div>
                            <div class="role-card-desc">Dapat mengelola form donasi dan melihat laporan</div>
                        </label>
                        <label class="role-card">
                            <input type="radio" name="role" value="admin"
                                {{ old('role') === 'admin' ? 'checked' : '' }}>
                            <div class="role-card-title">Admin</div>
                            <div class="role-card-desc">Akses penuh ke semua fitur admin panel</div>
                        </label>
                    </div>
                    @error('role')
                        <div class="error-msg">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-footer">
                    <a href="{{ route('admin.users') }}" class="btn-sm">Batal</a>
                    <button type="submit" class="btn-primary" style="padding:10px 24px; font-size:14px">
                        Buat User
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
