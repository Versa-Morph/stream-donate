<x-guest-layout>

    {{-- ====================== TAB SWITCHER ====================== --}}
    <div class="auth-tabs" id="auth-tabs">
        <button class="auth-tab" id="tab-login" onclick="switchTab('login')" type="button">
            <span class="iconify" data-icon="solar:login-bold-duotone"></span>
            Masuk
        </button>
        <button class="auth-tab" id="tab-register" onclick="switchTab('register')" type="button">
            <span class="iconify" data-icon="solar:user-plus-bold-duotone"></span>
            Daftar
        </button>
    </div>

    {{-- ====================== PANEL LOGIN ====================== --}}
    <div id="panel-login" class="panel-section">

        <div class="auth-title">Selamat Datang</div>
        <div class="auth-sub">Masuk ke akun StreamDonate kamu</div>

        @if (session('status'))
            <div class="alert-success">
                <span class="iconify" data-icon="solar:check-circle-bold-duotone"></span>
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="form-login">
            @csrf

            <div class="form-group">
                <label for="login_email">
                    <span class="iconify" data-icon="solar:letter-bold-duotone"></span>
                    Alamat Email
                </label>
                <input id="login_email" type="email" name="email"
                       value="{{ old('email') }}" required autofocus autocomplete="username"
                       placeholder="kamu@email.com"
                       class="{{ $errors->has('email') && !($errors->has('name')) ? 'is-invalid' : '' }}">
                @error('email')
                    @if (!$errors->has('name'))
                        <div class="error-msg">
                            <span class="iconify" data-icon="solar:danger-circle-bold-duotone"></span>
                            {{ $message }}
                        </div>
                    @endif
                @enderror
            </div>

            <div class="form-group">
                <label for="login_password">
                    <span class="iconify" data-icon="solar:lock-password-bold-duotone"></span>
                    Password
                </label>
                <div class="input-wrap">
                    <input id="login_password" type="password" name="password"
                           required autocomplete="current-password"
                           placeholder="••••••••">
                    <button type="button" class="eye-btn" onclick="togglePass('login_password', this)" tabindex="-1" aria-label="Toggle password visibility">
                        <span class="iconify" data-icon="solar:eye-bold"></span>
                    </button>
                </div>
                @error('password')
                    @if (!$errors->has('name'))
                        <div class="error-msg">
                            <span class="iconify" data-icon="solar:danger-circle-bold-duotone"></span>
                            {{ $message }}
                        </div>
                    @endif
                @enderror
            </div>

            <div class="form-row-between">
                <label class="checkbox-row" style="margin-bottom:0;cursor:pointer;">
                    <input type="checkbox" name="remember"> Ingat saya
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>
                @endif
            </div>

            <button type="submit" class="btn-submit" id="btn-login" style="margin-top:20px;">
                <span class="iconify" data-icon="solar:login-bold-duotone"></span>
                Masuk ke Dashboard
            </button>
        </form>

        <div class="auth-footer">
            Belum punya akun?
            <a href="javascript:void(0)" onclick="switchTab('register')">Daftar sekarang</a>
        </div>
    </div>

    {{-- ====================== PANEL REGISTER ====================== --}}
    <div id="panel-register" class="panel-section" style="display:none;">

        <div class="auth-title">Buat Akun Baru</div>
        <div class="auth-sub">Bergabung dan mulai terima donasi dari penonton kamu</div>

        {{-- Step indicator --}}
        <div class="step-indicator">
            <div class="step-item active">
                <div class="step-dot">
                    <span class="iconify" data-icon="solar:user-bold-duotone"></span>
                </div>
                <span class="step-label">Data Akun</span>
            </div>
            <div class="step-line"></div>
            <div class="step-item">
                <div class="step-dot">
                    <span class="iconify" data-icon="solar:shield-check-bold-duotone"></span>
                </div>
                <span class="step-label">Verifikasi OTP</span>
            </div>
        </div>

        <form method="POST" action="{{ route('register') }}" id="form-register">
            @csrf

            <div class="form-group">
                <label for="reg_name">
                    <span class="iconify" data-icon="solar:user-bold-duotone"></span>
                    Nama Lengkap
                </label>
                <input id="reg_name" type="text" name="name"
                       value="{{ old('name') }}" required autocomplete="name"
                       placeholder="Nama kamu atau nama channel"
                       class="{{ $errors->has('name') ? 'is-invalid' : '' }}">
                @error('name')
                    <div class="error-msg">
                        <span class="iconify" data-icon="solar:danger-circle-bold-duotone"></span>
                        {{ $message }}
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <label for="reg_email">
                    <span class="iconify" data-icon="solar:letter-bold-duotone"></span>
                    Alamat Email
                </label>
                <div class="input-wrap">
                    <input id="reg_email" type="email" name="email"
                           value="{{ old('email') }}" required autocomplete="username"
                           placeholder="kamu@email.com"
                           class="{{ $errors->has('email') && $errors->has('name') ? 'is-invalid' : '' }}"
                           oninput="validateEmailField(this)">
                    <span class="input-suffix" id="email-check-icon" style="display:none;"></span>
                </div>
                @error('email')
                    @if ($errors->has('name'))
                        <div class="error-msg">
                            <span class="iconify" data-icon="solar:danger-circle-bold-duotone"></span>
                            {{ $message }}
                        </div>
                    @endif
                @enderror
            </div>

            <div class="form-group">
                <label for="reg_password">
                    <span class="iconify" data-icon="solar:lock-password-bold-duotone"></span>
                    Password
                </label>
                <div class="input-wrap">
                    <input id="reg_password" type="password" name="password"
                           required autocomplete="new-password"
                           placeholder="Min. 8 karakter"
                           class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                           oninput="updatePasswordStrength(this.value)">
                    <button type="button" class="eye-btn" onclick="togglePass('reg_password', this)" tabindex="-1">
                        <span class="iconify" data-icon="solar:eye-bold"></span>
                    </button>
                </div>
                @error('password')
                    <div class="error-msg">
                        <span class="iconify" data-icon="solar:danger-circle-bold-duotone"></span>
                        {{ $message }}
                    </div>
                @enderror
                {{-- Password strength meter --}}
                <div class="pw-strength-wrap" id="pw-strength-wrap" style="display:none;">
                    <div class="pw-strength-bar">
                        <div class="pw-strength-fill" id="pw-strength-fill"></div>
                    </div>
                    <span class="pw-strength-label" id="pw-strength-label"></span>
                </div>
                {{-- Password hints --}}
                <div class="pw-hints" id="pw-hints" style="display:none;">
                    <div class="pw-hint" id="hint-len">
                        <span class="iconify" data-icon="solar:close-circle-bold"></span> Min. 8 karakter
                    </div>
                    <div class="pw-hint" id="hint-upper">
                        <span class="iconify" data-icon="solar:close-circle-bold"></span> Huruf kapital (A-Z)
                    </div>
                    <div class="pw-hint" id="hint-num">
                        <span class="iconify" data-icon="solar:close-circle-bold"></span> Angka (0-9)
                    </div>
                    <div class="pw-hint" id="hint-sym">
                        <span class="iconify" data-icon="solar:close-circle-bold"></span> Simbol (!@#...)
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="reg_password_confirmation">
                    <span class="iconify" data-icon="solar:lock-keyhole-bold-duotone"></span>
                    Konfirmasi Password
                </label>
                <div class="input-wrap">
                    <input id="reg_password_confirmation" type="password" name="password_confirmation"
                           required autocomplete="new-password"
                           placeholder="Ulangi password"
                           oninput="checkPasswordMatch()">
                    <button type="button" class="eye-btn" onclick="togglePass('reg_password_confirmation', this)" tabindex="-1">
                        <span class="iconify" data-icon="solar:eye-bold"></span>
                    </button>
                </div>
                <div class="error-msg" id="confirm-mismatch" style="display:none;">
                    <span class="iconify" data-icon="solar:danger-circle-bold-duotone"></span>
                    Password tidak cocok
                </div>
            </div>

            {{-- OTP info notice --}}
            <div class="otp-notice">
                <div class="otp-notice-icon">
                    <span class="iconify" data-icon="solar:letter-bold-duotone"></span>
                </div>
                <div>
                    <div class="otp-notice-title">Verifikasi OTP via Email</div>
                    <div class="otp-notice-desc">Kode 6 digit akan dikirim ke email kamu untuk memverifikasi pendaftaran.</div>
                </div>
            </div>

            <button type="submit" class="btn-submit" id="btn-register" style="margin-top:4px;">
                <span class="iconify" data-icon="solar:letter-bold-duotone"></span>
                Kirim Kode OTP
            </button>
        </form>

        <div class="auth-footer">
            Sudah punya akun?
            <a href="javascript:void(0)" onclick="switchTab('login')">Masuk di sini</a>
        </div>
    </div>

    <style>
        /* ========== TABS ========== */
        .auth-tabs {
            display: flex; gap: 4px;
            background: rgba(0,0,0,.25);
            border: 1px solid var(--border);
            border-radius: 12px; padding: 4px;
            margin-bottom: 24px;
        }
        .auth-tab {
            flex: 1; padding: 9px 12px;
            border: none; border-radius: 8px;
            font-size: 13px; font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            background: transparent; color: var(--text-3);
            transition: all .2s cubic-bezier(.22,.68,0,1.2);
            display: flex; align-items: center; justify-content: center; gap: 6px;
        }
        .auth-tab .iconify { font-size: 15px; width: 15px; height: 15px; }
        .auth-tab.active {
            background: var(--surface-2);
            color: var(--brand-light);
            box-shadow: 0 2px 10px rgba(0,0,0,.25), 0 0 0 1px rgba(124,108,252,.15);
        }
        .auth-tab:not(.active):hover { color: var(--text-2); background: rgba(255,255,255,.03); }

        /* ========== PANEL TRANSITION ========== */
        .panel-section {
            animation: panelFadeIn .25s ease both;
        }
        @keyframes panelFadeIn {
            from { opacity: 0; transform: translateY(8px) }
            to   { opacity: 1; transform: translateY(0) }
        }

        /* ========== FORM ROW ========== */
        .form-row-between {
            display: flex; justify-content: space-between; align-items: center;
        }
        .forgot-link {
            font-size: 12px; color: var(--brand-light);
            text-decoration: none; font-weight: 500;
            transition: opacity .15s;
        }
        .forgot-link:hover { opacity: .75; text-decoration: underline; }

        /* ========== STEP INDICATOR ========== */
        .step-indicator {
            display: flex; align-items: center; gap: 0;
            margin-bottom: 24px; position: relative;
        }
        .step-item {
            display: flex; flex-direction: column; align-items: center; gap: 5px;
            flex: 0; min-width: 72px; position: relative; z-index: 1;
        }
        .step-dot {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--surface-2);
            border: 1.5px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; transition: all .3s;
        }
        .step-dot .iconify { color: var(--text-3); font-size: 15px; width: 15px; height: 15px; }
        .step-item.active .step-dot {
            background: linear-gradient(135deg, var(--brand), #a855f7);
            border-color: transparent;
            box-shadow: 0 0 16px var(--brand-glow);
        }
        .step-item.active .step-dot .iconify { color: #fff; }
        .step-label { font-size: 10px; font-weight: 600; color: var(--text-3); white-space: nowrap; }
        .step-item.active .step-label { color: var(--brand-light); }
        .step-line {
            flex: 1; height: 1.5px;
            background: var(--border); margin: 0 4px; margin-bottom: 20px;
        }

        /* ========== INPUT SUFFIX ICON ========== */
        .input-suffix {
            position: absolute; right: 42px; top: 50%; transform: translateY(-50%);
            font-size: 14px; width: 14px; height: 14px; pointer-events: none;
        }

        /* ========== PASSWORD STRENGTH ========== */
        .pw-strength-wrap {
            display: flex; align-items: center; gap: 10px; margin-top: 8px;
        }
        .pw-strength-bar {
            flex: 1; height: 4px;
            background: var(--border); border-radius: 99px; overflow: hidden;
        }
        .pw-strength-fill {
            height: 100%; width: 0%; border-radius: 99px;
            transition: width .35s ease, background .35s;
        }
        .pw-strength-label { font-size: 11px; font-weight: 600; white-space: nowrap; }

        .pw-hints {
            display: flex; flex-wrap: wrap; gap: 6px 12px; margin-top: 10px;
        }
        .pw-hint {
            font-size: 11px; color: var(--text-3);
            display: flex; align-items: center; gap: 4px;
            transition: color .2s;
        }
        .pw-hint .iconify { font-size: 12px; color: var(--text-3); transition: color .2s; }
        .pw-hint.ok { color: var(--success); }
        .pw-hint.ok .iconify { color: var(--success); }

        /* ========== OTP NOTICE ========== */
        .otp-notice {
            display: flex; align-items: flex-start; gap: 12px;
            background: linear-gradient(135deg, rgba(124,108,252,.07), rgba(168,85,247,.05));
            border: 1px solid rgba(124,108,252,.2);
            border-radius: 12px; padding: 14px 16px;
            margin-top: 4px; margin-bottom: 16px;
        }
        .otp-notice-icon {
            width: 32px; height: 32px; border-radius: 8px; flex-shrink: 0;
            background: rgba(124,108,252,.15);
            display: flex; align-items: center; justify-content: center;
        }
        .otp-notice-icon .iconify { color: var(--brand-light); font-size: 16px; width: 16px; height: 16px; }
        .otp-notice-title { font-size: 12px; font-weight: 700; color: var(--text); margin-bottom: 2px; }
        .otp-notice-desc { font-size: 11px; color: var(--text-3); line-height: 1.5; }
    </style>

    <script>
        // ========== TAB SWITCHING ==========
        const urlParams = new URLSearchParams(window.location.search);
        const hasRegisterErrors = {{ ($errors->has('name') || ($errors->has('email') && old('name')) || ($errors->has('password') && old('name'))) ? 'true' : 'false' }};
        const sessionTab = '{{ session('tab', '') }}';

        function switchTab(tab) {
            const panels = { login: 'panel-login', register: 'panel-register' };
            const tabs   = { login: 'tab-login',   register: 'tab-register' };

            Object.keys(panels).forEach(key => {
                const panel = document.getElementById(panels[key]);
                const tabEl = document.getElementById(tabs[key]);
                if (key === tab) {
                    panel.style.display = '';
                    panel.style.animation = 'none';
                    panel.offsetHeight; // reflow
                    panel.style.animation = '';
                    tabEl.classList.add('active');
                } else {
                    panel.style.display = 'none';
                    tabEl.classList.remove('active');
                }
            });
        }

        if (hasRegisterErrors || sessionTab === 'register') {
            switchTab('register');
        } else {
            switchTab('login');
        }

        // ========== PASSWORD TOGGLE ==========
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

        // ========== PASSWORD STRENGTH ==========
        function updatePasswordStrength(value) {
            const wrap   = document.getElementById('pw-strength-wrap');
            const fill   = document.getElementById('pw-strength-fill');
            const label  = document.getElementById('pw-strength-label');
            const hints  = document.getElementById('pw-hints');
            const input  = document.getElementById('reg_password');

            if (!value) {
                wrap.style.display  = 'none';
                hints.style.display = 'none';
                input.classList.remove('is-valid','is-invalid');
                return;
            }
            wrap.style.display  = 'flex';
            hints.style.display = 'flex';

            const checks = {
                len:   value.length >= 8,
                upper: /[A-Z]/.test(value),
                num:   /[0-9]/.test(value),
                sym:   /[^A-Za-z0-9]/.test(value),
            };

            // Update hint items
            const hintMap = { len: 'hint-len', upper: 'hint-upper', num: 'hint-num', sym: 'hint-sym' };
            Object.keys(checks).forEach(key => {
                const el = document.getElementById(hintMap[key]);
                if (checks[key]) {
                    el.classList.add('ok');
                    el.querySelector('.iconify').setAttribute('data-icon', 'solar:check-circle-bold');
                } else {
                    el.classList.remove('ok');
                    el.querySelector('.iconify').setAttribute('data-icon', 'solar:close-circle-bold');
                }
            });

            const score = Object.values(checks).filter(Boolean).length;
            const levels = [
                { pct: '15%', color: '#f43f5e', text: 'Sangat lemah', cls: 'is-invalid' },
                { pct: '35%', color: '#f59e0b', text: 'Lemah',        cls: 'is-invalid' },
                { pct: '60%', color: '#eab308', text: 'Sedang',       cls: '' },
                { pct: '80%', color: '#22c55e', text: 'Kuat',         cls: 'is-valid' },
                { pct:'100%', color: '#22d3a0', text: 'Sangat kuat',  cls: 'is-valid' },
            ];
            const lvl = levels[Math.min(score, 4)];
            fill.style.width      = lvl.pct;
            fill.style.background = lvl.color;
            label.textContent     = lvl.text;
            label.style.color     = lvl.color;

            input.classList.remove('is-valid','is-invalid');
            if (lvl.cls) input.classList.add(lvl.cls);

            checkPasswordMatch();
        }

        // ========== CONFIRM PASSWORD MATCH ==========
        function checkPasswordMatch() {
            const pw1   = document.getElementById('reg_password').value;
            const pw2   = document.getElementById('reg_password_confirmation');
            const msg   = document.getElementById('confirm-mismatch');
            if (!pw2.value) { msg.style.display='none'; pw2.classList.remove('is-valid','is-invalid'); return; }
            if (pw1 === pw2.value) {
                msg.style.display = 'none';
                pw2.classList.remove('is-invalid'); pw2.classList.add('is-valid');
            } else {
                msg.style.display = 'flex';
                pw2.classList.remove('is-valid'); pw2.classList.add('is-invalid');
            }
        }

        // ========== EMAIL VALIDATION ==========
        function validateEmailField(input) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const iconEl     = document.getElementById('email-check-icon');
            if (!input.value) {
                input.classList.remove('is-valid','is-invalid');
                iconEl.style.display = 'none';
                return;
            }
            iconEl.style.display = 'block';
            if (emailRegex.test(input.value)) {
                input.classList.remove('is-invalid'); input.classList.add('is-valid');
                iconEl.innerHTML = '<span class="iconify" data-icon="solar:check-circle-bold" style="color:var(--success);font-size:14px;width:14px;height:14px;"></span>';
            } else {
                input.classList.remove('is-valid'); input.classList.add('is-invalid');
                iconEl.innerHTML = '<span class="iconify" data-icon="solar:danger-circle-bold-duotone" style="color:var(--danger);font-size:14px;width:14px;height:14px;"></span>';
            }
        }

        // ========== REGISTER SUBMIT LOADING ==========
        document.getElementById('form-register')?.addEventListener('submit', function(e) {
            const pw1 = document.getElementById('reg_password').value;
            const pw2 = document.getElementById('reg_password_confirmation').value;
            if (pw1 !== pw2) {
                e.preventDefault();
                document.getElementById('confirm-mismatch').style.display = 'flex';
                document.getElementById('reg_password_confirmation').classList.add('is-invalid');
                document.getElementById('reg_password_confirmation').focus();
                return;
            }
            const btn = document.getElementById('btn-register');
            btn.disabled = true;
            btn.innerHTML = '<span class="iconify spin" data-icon="solar:spinner-bold-duotone"></span> Mengirim OTP...';
        });

        // ========== LOGIN SUBMIT LOADING ==========
        document.getElementById('form-login')?.addEventListener('submit', function() {
            const btn = document.getElementById('btn-login');
            btn.disabled = true;
            btn.innerHTML = '<span class="iconify spin" data-icon="solar:spinner-bold-duotone"></span> Masuk...';
        });
    </script>
</x-guest-layout>
