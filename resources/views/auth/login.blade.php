<x-guest-layout>

    {{-- ====================== TAB SWITCHER ====================== --}}
    <div class="auth-tabs" id="auth-tabs">
        <button class="auth-tab active" id="tab-login" onclick="switchTab('login')" type="button">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
            <span>Masuk</span>
        </button>
        <button class="auth-tab" id="tab-register" onclick="switchTab('register')" type="button">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
            <span>Daftar</span>
        </button>
        <div class="tab-slider" id="tab-slider"></div>
    </div>

    {{-- ====================== PANEL LOGIN ====================== --}}
    <div id="panel-login" class="panel-section">

        <div class="auth-header">
            <div class="auth-title">Selamat Datang</div>
            <div class="auth-sub">Masuk ke akun Tiptipan kamu</div>
        </div>

        @if (session('status'))
            <div class="alert-success animate-bounce-in">
                <div class="alert-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" id="form-login" class="auth-form">
            @csrf

            <div class="form-group animate-field" style="--delay: 0.1s">
                <label for="login_email" class="input-label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    Alamat Email
                </label>
                <div class="input-container">
                    <input id="login_email" type="email" name="email"
                           value="{{ old('email') }}" required autofocus autocomplete="username"
                           placeholder="kamu@email.com"
                           class="fancy-input {{ $errors->has('email') && !($errors->has('name')) ? 'is-invalid' : '' }}">
                    <div class="input-glow"></div>
                </div>
                @error('email')
                    @if (!$errors->has('name'))
                        <div class="error-msg animate-shake">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            {{ $message }}
                        </div>
                    @endif
                @enderror
            </div>

            <div class="form-group animate-field" style="--delay: 0.2s">
                <label for="login_password" class="input-label">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    Password
                </label>
                <div class="input-container">
                    <input id="login_password" type="password" name="password"
                           required autocomplete="current-password"
                           placeholder="Masukkan password"
                           class="fancy-input">
                    <div class="input-glow"></div>
                    <button type="button" class="eye-btn" onclick="togglePass('login_password', this)" tabindex="-1" aria-label="Toggle password visibility">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </button>
                </div>
                @error('password')
                    @if (!$errors->has('name'))
                        <div class="error-msg animate-shake">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            {{ $message }}
                        </div>
                    @endif
                @enderror
            </div>

            <div class="form-row-between animate-field" style="--delay: 0.3s">
                <label class="checkbox-container">
                    <input type="checkbox" name="remember">
                    <span class="checkmark"></span>
                    <span class="checkbox-label">Ingat saya</span>
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="forgot-link">
                        <span>Lupa password?</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </a>
                @endif
            </div>

            <button type="submit" class="btn-submit animate-field" style="--delay: 0.4s" id="btn-login">
                <span class="btn-content">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                    <span>Masuk ke Dashboard</span>
                </span>
                <div class="btn-bg"></div>
                <div class="btn-glow"></div>
            </button>
        </form>

        <div class="auth-footer animate-field" style="--delay: 0.5s">
            <span>Belum punya akun?</span>
            <a href="javascript:void(0)" onclick="switchTab('register')" class="footer-link">
                <span>Daftar sekarang</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
        </div>
    </div>

    {{-- ====================== PANEL REGISTER ====================== --}}
    <div id="panel-register" class="panel-section" style="display:none;">

        <div class="auth-header">
            <div class="auth-title">Buat Akun Baru</div>
            <div class="auth-sub">Bergabung dan mulai terima donasi dari penonton kamu</div>
        </div>

        {{-- Step Indicator - 3 Steps --}}
        <div class="step-indicator animate-field" style="--delay: 0.1s">
            <div class="step-item active" data-step="1">
                <div class="step-dot">
                    <div class="step-dot-inner">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <div class="step-ring"></div>
                    <div class="step-pulse"></div>
                </div>
                <span class="step-label">Data Diri</span>
            </div>
            <div class="step-connector" id="connector-1-2">
                <div class="step-connector-fill"></div>
            </div>
            <div class="step-item" data-step="2">
                <div class="step-dot">
                    <div class="step-dot-inner">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                    </div>
                    <div class="step-ring"></div>
                    <div class="step-pulse"></div>
                </div>
                <span class="step-label">Verifikasi Email</span>
            </div>
            <div class="step-connector" id="connector-2-3">
                <div class="step-connector-fill"></div>
            </div>
            <div class="step-item" data-step="3">
                <div class="step-dot">
                    <div class="step-dot-inner">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <div class="step-ring"></div>
                </div>
                <span class="step-label">Password</span>
            </div>
        </div>

        <form method="POST" action="{{ route('register') }}" id="form-register" class="auth-form">
            @csrf

            {{-- ==================== STEP 1: Data Diri ==================== --}}
            <div id="register-step-1" class="register-step active">
                <div class="form-group animate-field" style="--delay: 0.15s">
                    <label for="reg_name" class="input-label">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        Nama Lengkap
                    </label>
                    <div class="input-container">
                        <input id="reg_name" type="text" name="name"
                               value="{{ old('name') }}" required autocomplete="name"
                               placeholder="Nama lengkap kamu"
                               class="fancy-input {{ $errors->has('name') ? 'is-invalid' : '' }}">
                        <div class="input-glow"></div>
                    </div>
                    @error('name')
                        <div class="error-msg animate-shake">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="form-group animate-field" style="--delay: 0.2s">
                    <label for="reg_username" class="input-label">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M16 8v5a3 3 0 0 0 6 0v-1a10 10 0 1 0-3.92 7.94"/></svg>
                        Username Streamer
                    </label>
                    <div class="input-container">
                        <span class="input-prefix">tiptipan.com/</span>
                        <input id="reg_username" type="text" name="username"
                               value="{{ old('username') }}" required autocomplete="username"
                               placeholder="namastreamer"
                               class="fancy-input with-prefix {{ $errors->has('username') ? 'is-invalid' : '' }}"
                               oninput="validateUsername(this)">
                        <div class="input-glow"></div>
                        <span class="input-status" id="username-check-icon"></span>
                    </div>
                    <div class="input-hint">Huruf kecil, angka, underscore. Min. 3 karakter.</div>
                    @error('username')
                        <div class="error-msg animate-shake">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <button type="button" class="btn-submit animate-field" style="--delay: 0.25s" onclick="goToStep(2)">
                    <span class="btn-content">
                        <span>Lanjutkan</span>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    </span>
                    <div class="btn-bg"></div>
                    <div class="btn-glow"></div>
                </button>
            </div>

            {{-- ==================== STEP 2: Verifikasi Email ==================== --}}
            <div id="register-step-2" class="register-step" style="display:none;">
                <div class="form-group animate-field" style="--delay: 0.15s">
                    <label for="reg_email" class="input-label">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                        Alamat Email
                    </label>
                    <div class="input-container">
                        <input id="reg_email" type="email" name="email"
                               value="{{ old('email') }}" required autocomplete="email"
                               placeholder="kamu@email.com"
                               class="fancy-input {{ $errors->has('email') ? 'is-invalid' : '' }}"
                               oninput="validateEmailField(this)">
                        <div class="input-glow"></div>
                        <span class="input-status" id="email-check-icon"></span>
                    </div>
                    @error('email')
                        <div class="error-msg animate-shake">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                <div class="otp-section animate-field" style="--delay: 0.2s">
                    <button type="button" class="btn-send-otp" id="btn-send-otp" onclick="sendOTP()">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 2 11 13"/><path d="m22 2-7 20-4-9-9-4 20-7z"/></svg>
                        <span id="otp-btn-text">Kirim Kode OTP</span>
                    </button>
                    <div class="otp-timer" id="otp-timer" style="display:none;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <span>Kirim ulang dalam <strong id="otp-countdown">60</strong>s</span>
                    </div>
                </div>

                <div class="form-group animate-field" style="--delay: 0.25s; display:none;" id="otp-input-group">
                    <label class="input-label">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Masukkan Kode OTP
                    </label>
                    <div class="otp-boxes">
                        <input type="text" maxlength="1" class="otp-box" data-index="0" oninput="handleOTPInput(this)" onkeydown="handleOTPKeydown(event, this)" autofocus>
                        <input type="text" maxlength="1" class="otp-box" data-index="1" oninput="handleOTPInput(this)" onkeydown="handleOTPKeydown(event, this)">
                        <input type="text" maxlength="1" class="otp-box" data-index="2" oninput="handleOTPInput(this)" onkeydown="handleOTPKeydown(event, this)">
                        <input type="text" maxlength="1" class="otp-box" data-index="3" oninput="handleOTPInput(this)" onkeydown="handleOTPKeydown(event, this)">
                        <input type="text" maxlength="1" class="otp-box" data-index="4" oninput="handleOTPInput(this)" onkeydown="handleOTPKeydown(event, this)">
                        <input type="text" maxlength="1" class="otp-box" data-index="5" oninput="handleOTPInput(this)" onkeydown="handleOTPKeydown(event, this)">
                    </div>
                    <input type="hidden" name="otp_code" id="otp_code_hidden">
                    <div class="otp-status" id="otp-status"></div>
                </div>

                <div class="step-nav animate-field" style="--delay: 0.3s">
                    <button type="button" class="btn-back" onclick="goToStep(1)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                        <span>Kembali</span>
                    </button>
                    <button type="button" class="btn-submit btn-next" id="btn-verify-otp" onclick="verifyOTPAndContinue()" disabled>
                        <span class="btn-content">
                            <span>Verifikasi & Lanjutkan</span>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                        </span>
                        <div class="btn-bg"></div>
                        <div class="btn-glow"></div>
                    </button>
                </div>
            </div>

            {{-- ==================== STEP 3: Password ==================== --}}
            <div id="register-step-3" class="register-step" style="display:none;">
                <div class="form-group animate-field" style="--delay: 0.15s">
                    <label for="reg_password" class="input-label">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Password
                    </label>
                    <div class="input-container">
                        <input id="reg_password" type="password" name="password"
                               required autocomplete="new-password"
                               placeholder="Min. 8 karakter"
                               class="fancy-input {{ $errors->has('password') ? 'is-invalid' : '' }}"
                               oninput="updatePasswordStrength(this.value)">
                        <div class="input-glow"></div>
                        <button type="button" class="eye-btn" onclick="togglePass('reg_password', this)" tabindex="-1">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    @error('password')
                        <div class="error-msg animate-shake">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            {{ $message }}
                        </div>
                    @enderror
                    
                    <div class="pw-strength-container" id="pw-strength-wrap" style="display:none;">
                        <div class="pw-strength-bar">
                            <div class="pw-strength-fill" id="pw-strength-fill"></div>
                        </div>
                        <span class="pw-strength-label" id="pw-strength-label"></span>
                    </div>
                    
                    <div class="pw-hints" id="pw-hints" style="display:none;">
                        <div class="pw-hint" id="hint-len">
                            <span class="hint-icon">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            </span>
                            <span>Min. 8 karakter</span>
                        </div>
                        <div class="pw-hint" id="hint-upper">
                            <span class="hint-icon">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            </span>
                            <span>Huruf kapital (A-Z)</span>
                        </div>
                        <div class="pw-hint" id="hint-num">
                            <span class="hint-icon">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            </span>
                            <span>Angka (0-9)</span>
                        </div>
                        <div class="pw-hint" id="hint-sym">
                            <span class="hint-icon">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            </span>
                            <span>Simbol (!@#...)</span>
                        </div>
                    </div>
                </div>

                <div class="form-group animate-field" style="--delay: 0.2s">
                    <label for="reg_password_confirmation" class="input-label">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>
                        Konfirmasi Password
                    </label>
                    <div class="input-container">
                        <input id="reg_password_confirmation" type="password" name="password_confirmation"
                               required autocomplete="new-password"
                               placeholder="Ulangi password"
                               class="fancy-input"
                               oninput="checkPasswordMatch()">
                        <div class="input-glow"></div>
                        <button type="button" class="eye-btn" onclick="togglePass('reg_password_confirmation', this)" tabindex="-1">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                        </button>
                    </div>
                    <div class="error-msg animate-shake" id="confirm-mismatch" style="display:none;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        Password tidak cocok
                    </div>
                </div>

                <div class="step-nav animate-field" style="--delay: 0.25s">
                    <button type="button" class="btn-back" onclick="goToStep(2)">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m12 19-7-7 7-7"/><path d="M19 12H5"/></svg>
                        <span>Kembali</span>
                    </button>
                    <button type="submit" class="btn-submit btn-next" id="btn-register">
                        <span class="btn-content">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                            <span>Buat Akun</span>
                        </span>
                        <div class="btn-bg"></div>
                        <div class="btn-glow"></div>
                    </button>
                </div>
            </div>
        </form>

        <div class="auth-footer animate-field" style="--delay: 0.45s">
            <span>Sudah punya akun?</span>
            <a href="javascript:void(0)" onclick="switchTab('login')" class="footer-link">
                <span>Masuk di sini</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
            </a>
        </div>
    </div>

    <style>
        /* ====================== TAB SWITCHER ====================== */
        .auth-tabs {
            display: flex; gap: 4px;
            background: rgba(0,0,0,.3);
            border: 1px solid var(--border);
            border-radius: 16px; padding: 5px;
            margin-bottom: 28px;
            position: relative;
            overflow: hidden;
        }
        .auth-tab {
            flex: 1; padding: 12px 16px;
            border: none; border-radius: 12px;
            font-size: 13px; font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            background: transparent; color: var(--text-3);
            transition: all .3s ease;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            position: relative; z-index: 2;
        }
        .auth-tab svg { transition: all .3s; }
        .auth-tab span { position: relative; z-index: 1; }
        .auth-tab.active { color: #fff; }
        .auth-tab.active svg { transform: scale(1.1); }
        .auth-tab:not(.active):hover { 
            color: var(--text); 
            background: rgba(255,255,255,.03);
        }
        .tab-slider {
            position: absolute;
            top: 5px; left: 5px;
            width: calc(50% - 7px); height: calc(100% - 10px);
            background: linear-gradient(135deg, var(--brand), #a855f7);
            border-radius: 12px;
            transition: transform .3s ease;
            z-index: 1;
            box-shadow: 0 4px 20px var(--brand-glow);
        }
        .tab-slider.right { transform: translateX(calc(100% + 4px)); }

        /* ====================== ANIMATIONS ====================== */
        .animate-field {
            opacity: 0;
            transform: translateY(15px);
            animation: fieldSlideIn .4s ease forwards;
            animation-delay: var(--delay, 0s);
        }
        @keyframes fieldSlideIn {
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-bounce-in {
            animation: bounceIn .5s ease;
        }
        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(.9); }
            100% { opacity: 1; transform: scale(1); }
        }
        
        .animate-shake {
            animation: shakeError .4s ease;
        }
        @keyframes shakeError {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        /* ====================== HEADER ====================== */
        .auth-header { margin-bottom: 24px; text-align: center; }
        .auth-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 24px; font-weight: 700;
            background: linear-gradient(135deg, #fff, var(--brand-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }
        .auth-sub { 
            font-size: 13px; color: var(--text-3); line-height: 1.5;
        }

        /* ====================== PANEL ====================== */
        .panel-section {
            animation: panelReveal .3s ease both;
        }
        @keyframes panelReveal {
            from { opacity: 0; transform: translateX(10px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* ====================== INPUT LABEL ====================== */
        .input-label {
            display: flex; align-items: center; gap: 8px;
            font-size: 12px; font-weight: 600; color: var(--text-2);
            margin-bottom: 8px;
            transition: color .2s;
        }
        .input-label svg { color: var(--brand-light); }

        /* ====================== FANCY INPUT ====================== */
        .form-group { margin-bottom: 18px; }
        .input-container {
            position: relative;
        }
        .fancy-input {
            width: 100%; padding: 14px 16px;
            background: rgba(26, 26, 34, 0.6);
            border: 2px solid var(--border);
            border-radius: 12px;
            color: var(--text);
            font-family: 'Inter', sans-serif;
            font-size: 14px; outline: none;
            transition: all .2s ease;
        }
        .fancy-input:hover { 
            border-color: var(--border-2); 
            background: rgba(26, 26, 34, 0.8);
        }
        .fancy-input:focus {
            border-color: var(--brand);
            background: rgba(30, 30, 40, 0.9);
            box-shadow: 0 0 0 4px var(--brand-glow);
        }
        .fancy-input:focus ~ .input-glow {
            opacity: 1;
        }
        .fancy-input::placeholder { color: var(--text-3); }
        
        .input-glow {
            position: absolute; inset: -15px;
            background: radial-gradient(circle at center, var(--brand-glow), transparent 70%);
            opacity: 0;
            z-index: -1;
            transition: opacity .2s;
            filter: blur(15px);
            pointer-events: none;
        }
        
        .fancy-input.is-valid { border-color: var(--success); }
        .fancy-input.is-valid:focus { box-shadow: 0 0 0 4px var(--success-glow); }
        .fancy-input.is-invalid { border-color: var(--danger); }
        .fancy-input.is-invalid:focus { box-shadow: 0 0 0 4px var(--danger-glow); }
        
        /* Input with eye button padding */
        .input-container:has(.eye-btn) .fancy-input { padding-right: 50px; }

        /* Eye button */
        .eye-btn {
            position: absolute; right: 4px; top: 50%; transform: translateY(-50%);
            width: 40px; height: 40px;
            background: rgba(124,108,252,.1);
            border: none; border-radius: 10px;
            cursor: pointer;
            color: var(--text-3);
            display: flex; align-items: center; justify-content: center;
            transition: all .2s ease;
        }
        .eye-btn:hover { 
            color: var(--brand-light); 
            background: rgba(124,108,252,.2);
        }
        
        /* Input status icon */
        .input-status {
            position: absolute; right: 14px; top: 50%; transform: translateY(-50%);
            opacity: 0;
            transition: opacity .2s;
        }
        .input-status.show { opacity: 1; }

        /* ====================== CHECKBOX ====================== */
        .form-row-between {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 8px;
        }
        .checkbox-container {
            display: flex; align-items: center; gap: 10px;
            cursor: pointer; user-select: none;
            font-size: 13px; color: var(--text-2);
        }
        .checkbox-container input { display: none; }
        .checkmark {
            width: 20px; height: 20px;
            border: 2px solid var(--border);
            border-radius: 6px;
            background: rgba(26, 26, 34, 0.6);
            position: relative;
            transition: all .2s ease;
        }
        .checkmark::after {
            content: '';
            position: absolute;
            left: 6px; top: 2px;
            width: 5px; height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg) scale(0);
            transition: transform .2s ease;
        }
        .checkbox-container:hover .checkmark {
            border-color: var(--brand-light);
        }
        .checkbox-container input:checked ~ .checkmark {
            background: linear-gradient(135deg, var(--brand), #a855f7);
            border-color: transparent;
        }
        .checkbox-container input:checked ~ .checkmark::after {
            transform: rotate(45deg) scale(1);
        }

        /* Forgot link */
        .forgot-link {
            font-size: 13px; color: var(--brand-light);
            text-decoration: none; font-weight: 500;
            display: flex; align-items: center; gap: 4px;
            transition: all .2s;
        }
        .forgot-link svg { 
            transition: transform .2s; 
            opacity: 0;
        }
        .forgot-link:hover { color: #fff; }
        .forgot-link:hover svg { 
            opacity: 1;
            transform: translateX(3px); 
        }

        /* ====================== STEP INDICATOR ====================== */
        .step-indicator {
            display: flex; align-items: flex-start; 
            margin-bottom: 24px;
            padding: 0 10px;
        }
        .step-item {
            display: flex; flex-direction: column; align-items: center; gap: 10px;
            position: relative; z-index: 1;
        }
        .step-dot {
            width: 48px; height: 48px;
            position: relative;
            display: flex; align-items: center; justify-content: center;
        }
        .step-dot-inner {
            width: 40px; height: 40px;
            border-radius: 50%;
            background: var(--surface-2);
            border: 2px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            transition: all .3s ease;
            position: relative; z-index: 2;
        }
        .step-dot-inner svg { color: var(--text-3); transition: all .3s; }
        .step-ring {
            position: absolute; inset: 0;
            border: 2px solid transparent;
            border-radius: 50%;
            transition: all .3s;
        }
        .step-pulse {
            position: absolute; inset: -4px;
            border-radius: 50%;
            background: var(--brand);
            opacity: 0;
            animation: stepPulseAnim 2s ease-in-out infinite;
        }
        @keyframes stepPulseAnim {
            0%, 100% { transform: scale(0.8); opacity: 0; }
            50% { transform: scale(1.1); opacity: 0.3; }
        }
        .step-item.active .step-dot-inner {
            background: linear-gradient(135deg, var(--brand), #a855f7);
            border-color: transparent;
            box-shadow: 0 0 20px var(--brand-glow);
        }
        .step-item.active .step-dot-inner svg { color: #fff; }
        .step-item.active .step-ring { border-color: var(--brand); }
        .step-label { 
            font-size: 11px; font-weight: 600; color: var(--text-3); 
            white-space: nowrap;
            transition: all .3s;
        }
        .step-item.active .step-label { color: var(--brand-light); }
        .step-connector {
            flex: 1; height: 3px;
            background: var(--border);
            margin: 23px 12px 0;
            border-radius: 3px;
            overflow: hidden;
        }
        .step-connector-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--brand), #a855f7);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform .5s ease;
        }

        /* ====================== PASSWORD STRENGTH ====================== */
        .pw-strength-container {
            display: flex; align-items: center; gap: 12px; 
            margin-top: 12px;
            padding: 10px 12px;
            background: rgba(0,0,0,.2);
            border-radius: 10px;
            border: 1px solid var(--border);
        }
        .pw-strength-bar {
            flex: 1; height: 6px;
            background: var(--border); border-radius: 99px; 
            overflow: hidden;
        }
        .pw-strength-fill {
            height: 100%; width: 0%; border-radius: 99px;
            transition: width .3s ease, background .3s;
        }
        .pw-strength-label { 
            font-size: 11px; font-weight: 700; white-space: nowrap;
            min-width: 80px; text-align: right;
        }

        .pw-hints {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 8px; margin-top: 12px;
        }
        .pw-hint {
            font-size: 11px; color: var(--text-3);
            display: flex; align-items: center; gap: 6px;
            padding: 6px 10px;
            background: rgba(0,0,0,.15);
            border-radius: 8px;
            border: 1px solid transparent;
            transition: all .2s ease;
        }
        .hint-icon {
            display: flex; align-items: center; justify-content: center;
        }
        .pw-hint.ok { 
            color: var(--success); 
            border-color: rgba(34,211,160,.2);
            background: rgba(34,211,160,.05);
        }

        /* ====================== OTP NOTICE ====================== */
        .otp-notice {
            display: flex; align-items: center; gap: 14px;
            background: linear-gradient(135deg, rgba(124,108,252,.1), rgba(168,85,247,.05));
            border: 1px solid rgba(124,108,252,.25);
            border-radius: 14px; padding: 16px 18px;
            margin-top: 8px; margin-bottom: 8px;
        }
        .otp-notice-icon {
            width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0;
            background: rgba(124,108,252,.2);
            display: flex; align-items: center; justify-content: center;
        }
        .otp-notice-icon svg { color: var(--brand-light); }
        .otp-notice-content { flex: 1; }
        .otp-notice-title { font-size: 13px; font-weight: 700; color: var(--text); margin-bottom: 4px; }
        .otp-notice-desc { font-size: 12px; color: var(--text-3); line-height: 1.5; }

        /* ====================== SUBMIT BUTTON ====================== */
        .btn-submit {
            width: 100%; padding: 16px 24px; border: none;
            border-radius: 14px; cursor: pointer;
            background: transparent;
            color: #fff;
            font-family: 'Inter', sans-serif; font-size: 15px; font-weight: 700;
            position: relative; overflow: hidden;
            margin-top: 20px;
            transition: transform .2s ease, box-shadow .2s;
        }
        .btn-content {
            position: relative; z-index: 2;
            display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .btn-content svg { transition: transform .2s; }
        .btn-bg {
            position: absolute; inset: 0;
            background: linear-gradient(135deg, var(--brand), #a855f7);
            z-index: 1;
        }
        .btn-glow {
            position: absolute; inset: -2px;
            background: linear-gradient(135deg, var(--brand), #a855f7);
            border-radius: 16px;
            filter: blur(12px);
            opacity: 0.4;
            z-index: 0;
            transition: opacity .2s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px var(--brand-glow);
        }
        .btn-submit:hover .btn-glow { opacity: 0.6; }
        .btn-submit:hover .btn-content svg { transform: translateX(3px); }
        .btn-submit:active { transform: translateY(0); }
        .btn-submit:disabled {
            opacity: 0.6; cursor: not-allowed;
            transform: none;
        }

        /* ====================== FOOTER ====================== */
        .auth-footer {
            text-align: center; margin-top: 24px;
            font-size: 13px; color: var(--text-3);
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .footer-link {
            color: var(--brand-light); text-decoration: none; font-weight: 600;
            display: inline-flex; align-items: center; gap: 4px;
            transition: all .2s;
        }
        .footer-link svg {
            transition: transform .2s;
            opacity: 0;
        }
        .footer-link:hover { color: #fff; }
        .footer-link:hover svg {
            opacity: 1;
            transform: translateX(3px);
        }

        /* ====================== ALERTS ====================== */
        .alert-success {
            background: linear-gradient(135deg, rgba(34,211,160,.1), rgba(34,211,160,.05));
            border: 1px solid rgba(34,211,160,.3);
            color: var(--success);
            padding: 14px 18px; border-radius: 12px;
            font-size: 13px; margin-bottom: 20px;
            display: flex; align-items: center; gap: 12px;
        }
        .alert-icon {
            width: 36px; height: 36px;
            background: rgba(34,211,160,.15);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .error-msg {
            font-size: 11px; color: var(--danger); margin-top: 8px;
            display: flex; align-items: center; gap: 6px;
            padding: 6px 10px;
            background: rgba(244,63,94,.05);
            border-radius: 8px;
            border: 1px solid rgba(244,63,94,.15);
        }

        /* ====================== MULTI-STEP REGISTER ====================== */
        .register-step {
            animation: stepFadeIn .3s ease both;
        }
        .register-step[style*="display:none"] {
            animation: none;
        }
        @keyframes stepFadeIn {
            from { opacity: 0; transform: translateX(20px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* Input with prefix (username) */
        .input-container:has(.input-prefix) {
            position: relative;
        }
        .input-prefix {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            font-size: 13px; color: var(--text-3); font-weight: 500;
            pointer-events: none;
            z-index: 2;
        }
        .fancy-input.with-prefix {
            padding-left: 115px;
        }
        .input-container:has(.input-prefix) .input-status {
            right: 14px;
        }

        /* Input hint text */
        .input-hint {
            font-size: 11px; color: var(--text-3); margin-top: 6px;
            padding-left: 2px;
        }

        /* OTP Section */
        .otp-section {
            display: flex; align-items: center; gap: 12px;
            margin-bottom: 20px;
        }
        .btn-send-otp {
            flex: 1; padding: 14px 20px;
            background: linear-gradient(135deg, rgba(124,108,252,.15), rgba(168,85,247,.1));
            border: 1px solid rgba(124,108,252,.3);
            border-radius: 12px;
            color: var(--brand-light);
            font-family: 'Inter', sans-serif; font-size: 13px; font-weight: 600;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            transition: all .2s ease;
        }
        .btn-send-otp:hover {
            background: linear-gradient(135deg, rgba(124,108,252,.25), rgba(168,85,247,.15));
            border-color: var(--brand);
            transform: translateY(-1px);
        }
        .btn-send-otp:disabled {
            opacity: 0.5; cursor: not-allowed; transform: none;
        }
        .otp-timer {
            display: flex; align-items: center; gap: 6px;
            font-size: 12px; color: var(--text-3);
            padding: 10px 14px;
            background: rgba(0,0,0,.2);
            border-radius: 10px;
            border: 1px solid var(--border);
        }
        .otp-timer strong { color: var(--brand-light); }

        /* OTP Input Boxes */
        .otp-boxes {
            display: flex; gap: 8px;
            justify-content: center;
            margin-top: 12px;
        }
        .otp-box {
            width: 48px; height: 56px;
            background: rgba(26, 26, 34, 0.6);
            border: 2px solid var(--border);
            border-radius: 12px;
            color: var(--text);
            font-family: 'Space Grotesk', sans-serif;
            font-size: 24px; font-weight: 700;
            text-align: center;
            outline: none;
            transition: all .2s ease;
        }
        .otp-box:hover { border-color: var(--border-2); }
        .otp-box:focus {
            border-color: var(--brand);
            background: rgba(30, 30, 40, 0.9);
            box-shadow: 0 0 0 4px var(--brand-glow);
        }
        .otp-box.filled {
            border-color: var(--success);
            background: rgba(34,211,160,.05);
        }
        .otp-box.error {
            border-color: var(--danger);
            background: rgba(244,63,94,.05);
        }

        /* OTP Status */
        .otp-status {
            text-align: center; margin-top: 12px;
            font-size: 12px; font-weight: 500;
            min-height: 20px;
        }
        .otp-status.success { color: var(--success); }
        .otp-status.error { color: var(--danger); }
        .otp-status.loading { color: var(--text-3); }

        /* Step Navigation */
        .step-nav {
            display: flex; gap: 12px; margin-top: 24px;
            align-items: stretch;
        }
        .btn-back {
            padding: 16px 20px;
            background: rgba(255,255,255,.05);
            border: 1px solid var(--border);
            border-radius: 14px;
            color: var(--text-2);
            font-family: 'Inter', sans-serif; font-size: 14px; font-weight: 600;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 6px;
            transition: all .2s ease;
        }
        .btn-back:hover {
            background: rgba(255,255,255,.08);
            border-color: var(--border-2);
            color: var(--text);
        }
        .btn-back svg { transition: transform .2s; }
        .btn-back:hover svg { transform: translateX(-3px); }
        
        .step-nav .btn-submit {
            flex: 1;
            margin-top: 0;
        }

        /* Step completed state */
        .step-item.completed .step-dot-inner {
            background: var(--success);
            border-color: transparent;
        }
        .step-item.completed .step-dot-inner svg { color: #fff; }
        .step-item.completed .step-ring { border-color: var(--success); }
        .step-item.completed .step-label { color: var(--success); }

        /* ====================== RESPONSIVE ====================== */
        @media (max-width: 480px) {
            .pw-hints { grid-template-columns: 1fr; }
            .step-indicator { padding: 0; gap: 4px; }
            .step-label { font-size: 9px; }
            .step-dot { width: 40px; height: 40px; }
            .step-dot-inner { width: 32px; height: 32px; }
            .step-dot-inner svg { width: 14px; height: 14px; }
            .step-connector { margin: 18px 6px 0; }
            
            .otp-boxes { gap: 6px; }
            .otp-box { width: 42px; height: 50px; font-size: 20px; }
            
            .step-nav { flex-direction: column; gap: 10px; }
            .btn-back { width: 100%; justify-content: center; }
            
            .input-prefix { font-size: 11px; left: 10px; }
            .fancy-input.with-prefix { padding-left: 100px; }
            
            .otp-section { flex-direction: column; }
            .btn-send-otp { width: 100%; }
        }
    </style>

    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const hasRegisterErrors = {{ ($errors->has('name') || ($errors->has('username') && old('name')) || ($errors->has('email') && old('name')) || ($errors->has('password') && old('name'))) ? 'true' : 'false' }};
        const sessionTab = '{{ session('tab', '') }}';
        
        // Current registration step
        let currentStep = 1;
        let otpVerified = false;
        let otpTimer = null;

        // ====================== TAB SWITCHING ======================
        function switchTab(tab) {
            const panels = { login: 'panel-login', register: 'panel-register' };
            const tabs   = { login: 'tab-login',   register: 'tab-register' };
            const slider = document.getElementById('tab-slider');

            Object.keys(panels).forEach(key => {
                const panel = document.getElementById(panels[key]);
                const tabEl = document.getElementById(tabs[key]);
                if (key === tab) {
                    panel.style.display = '';
                    panel.style.animation = 'none';
                    panel.offsetHeight;
                    panel.style.animation = '';
                    tabEl.classList.add('active');
                    
                    // Reinitialize field animations
                    panel.querySelectorAll('.animate-field').forEach(el => {
                        el.style.animation = 'none';
                        el.offsetHeight;
                        el.style.animation = '';
                    });
                } else {
                    panel.style.display = 'none';
                    tabEl.classList.remove('active');
                }
            });
            
            // Move slider
            if (tab === 'register') {
                slider.classList.add('right');
            } else {
                slider.classList.remove('right');
            }
        }

        if (hasRegisterErrors || sessionTab === 'register') {
            switchTab('register');
        } else {
            switchTab('login');
        }

        // ====================== MULTI-STEP NAVIGATION ======================
        function goToStep(step) {
            // Validate current step before proceeding
            if (step > currentStep) {
                if (!validateStep(currentStep)) return;
            }

            // Hide all steps
            document.querySelectorAll('.register-step').forEach(s => {
                s.style.display = 'none';
            });

            // Show target step
            const targetStep = document.getElementById(`register-step-${step}`);
            if (targetStep) {
                targetStep.style.display = '';
                targetStep.style.animation = 'none';
                targetStep.offsetHeight;
                targetStep.style.animation = '';
                
                // Reinitialize animations
                targetStep.querySelectorAll('.animate-field').forEach(el => {
                    el.style.animation = 'none';
                    el.offsetHeight;
                    el.style.animation = '';
                });
            }

            // Update step indicator
            updateStepIndicator(step);
            currentStep = step;
        }

        function validateStep(step) {
            if (step === 1) {
                const name = document.getElementById('reg_name').value.trim();
                const username = document.getElementById('reg_username').value.trim();
                
                if (!name) {
                    document.getElementById('reg_name').classList.add('is-invalid');
                    document.getElementById('reg_name').focus();
                    return false;
                }
                if (!username || username.length < 3) {
                    document.getElementById('reg_username').classList.add('is-invalid');
                    document.getElementById('reg_username').focus();
                    return false;
                }
                if (!/^[a-z0-9_]+$/.test(username)) {
                    document.getElementById('reg_username').classList.add('is-invalid');
                    document.getElementById('reg_username').focus();
                    return false;
                }
                return true;
            }
            if (step === 2) {
                const email = document.getElementById('reg_email').value.trim();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                
                if (!email || !emailRegex.test(email)) {
                    document.getElementById('reg_email').classList.add('is-invalid');
                    document.getElementById('reg_email').focus();
                    return false;
                }
                
                // For now, we'll skip OTP verification requirement for development
                // In production, you would check: if (!otpVerified) { ... }
                return true;
            }
            return true;
        }

        function updateStepIndicator(step) {
            const stepItems = document.querySelectorAll('.step-item');
            const connectors = [
                document.getElementById('connector-1-2'),
                document.getElementById('connector-2-3')
            ];

            stepItems.forEach((item, index) => {
                const stepNum = index + 1;
                item.classList.remove('active', 'completed');
                
                if (stepNum < step) {
                    item.classList.add('completed');
                } else if (stepNum === step) {
                    item.classList.add('active');
                }
            });

            // Update connector fills
            connectors.forEach((conn, index) => {
                if (conn) {
                    const fill = conn.querySelector('.step-connector-fill');
                    if (fill) {
                        fill.style.transform = (index + 1) < step ? 'scaleX(1)' : 'scaleX(0)';
                    }
                }
            });
        }

        // ====================== USERNAME VALIDATION ======================
        function validateUsername(input) {
            const value = input.value.toLowerCase().replace(/[^a-z0-9_]/g, '');
            input.value = value;
            
            const iconEl = document.getElementById('username-check-icon');
            if (!value) {
                input.classList.remove('is-valid', 'is-invalid');
                iconEl.classList.remove('show');
                iconEl.innerHTML = '';
                return;
            }
            
            iconEl.classList.add('show');
            if (value.length >= 3 && /^[a-z0-9_]+$/.test(value)) {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                iconEl.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>';
            } else {
                input.classList.remove('is-valid');
                input.classList.add('is-invalid');
                iconEl.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--danger)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>';
            }
        }

        // ====================== OTP FUNCTIONS ======================
        function sendOTP() {
            const email = document.getElementById('reg_email').value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (!email || !emailRegex.test(email)) {
                document.getElementById('reg_email').classList.add('is-invalid');
                document.getElementById('reg_email').focus();
                return;
            }

            const btn = document.getElementById('btn-send-otp');
            const btnText = document.getElementById('otp-btn-text');
            const timer = document.getElementById('otp-timer');
            const otpGroup = document.getElementById('otp-input-group');
            
            // Disable button and show loading
            btn.disabled = true;
            btnText.textContent = 'Mengirim...';
            
            // Simulate OTP send (replace with actual API call)
            setTimeout(() => {
                // Show OTP input
                otpGroup.style.display = '';
                document.querySelector('.otp-box').focus();
                
                // Hide button, show timer
                btn.style.display = 'none';
                timer.style.display = 'flex';
                
                // Start countdown
                startOTPTimer();
                
                // Show success status
                const status = document.getElementById('otp-status');
                status.className = 'otp-status success';
                status.textContent = 'Kode OTP telah dikirim ke email kamu';
            }, 1500);
        }

        function startOTPTimer() {
            let seconds = 60;
            const countdownEl = document.getElementById('otp-countdown');
            const timer = document.getElementById('otp-timer');
            const btn = document.getElementById('btn-send-otp');
            const btnText = document.getElementById('otp-btn-text');
            
            if (otpTimer) clearInterval(otpTimer);
            
            otpTimer = setInterval(() => {
                seconds--;
                countdownEl.textContent = seconds;
                
                if (seconds <= 0) {
                    clearInterval(otpTimer);
                    timer.style.display = 'none';
                    btn.style.display = 'flex';
                    btn.disabled = false;
                    btnText.textContent = 'Kirim Ulang OTP';
                }
            }, 1000);
        }

        function handleOTPInput(input) {
            // Only allow numbers
            input.value = input.value.replace(/[^0-9]/g, '');
            
            if (input.value) {
                input.classList.add('filled');
                // Move to next input
                const nextIndex = parseInt(input.dataset.index) + 1;
                const nextInput = document.querySelector(`.otp-box[data-index="${nextIndex}"]`);
                if (nextInput) {
                    nextInput.focus();
                }
            } else {
                input.classList.remove('filled');
            }
            
            // Check if all boxes are filled
            checkOTPComplete();
        }

        function handleOTPKeydown(event, input) {
            // Handle backspace
            if (event.key === 'Backspace' && !input.value) {
                const prevIndex = parseInt(input.dataset.index) - 1;
                const prevInput = document.querySelector(`.otp-box[data-index="${prevIndex}"]`);
                if (prevInput) {
                    prevInput.focus();
                    prevInput.value = '';
                    prevInput.classList.remove('filled');
                }
            }
        }

        function checkOTPComplete() {
            const boxes = document.querySelectorAll('.otp-box');
            let otp = '';
            let allFilled = true;
            
            boxes.forEach(box => {
                if (!box.value) allFilled = false;
                otp += box.value;
            });
            
            // Store OTP in hidden field
            document.getElementById('otp_code_hidden').value = otp;
            
            // Enable/disable verify button
            const verifyBtn = document.getElementById('btn-verify-otp');
            verifyBtn.disabled = !allFilled;
        }

        function verifyOTPAndContinue() {
            const otp = document.getElementById('otp_code_hidden').value;
            const status = document.getElementById('otp-status');
            const boxes = document.querySelectorAll('.otp-box');
            
            // Show loading
            status.className = 'otp-status loading';
            status.textContent = 'Memverifikasi...';
            
            // Simulate OTP verification (replace with actual API call)
            setTimeout(() => {
                // For development, accept any 6-digit OTP
                if (otp.length === 6) {
                    status.className = 'otp-status success';
                    status.textContent = 'OTP terverifikasi!';
                    boxes.forEach(box => box.classList.add('filled'));
                    otpVerified = true;
                    
                    // Proceed to next step after short delay
                    setTimeout(() => goToStep(3), 500);
                } else {
                    status.className = 'otp-status error';
                    status.textContent = 'Kode OTP tidak valid';
                    boxes.forEach(box => {
                        box.classList.remove('filled');
                        box.classList.add('error');
                    });
                    setTimeout(() => {
                        boxes.forEach(box => box.classList.remove('error'));
                    }, 1000);
                }
            }, 1000);
        }

        // ====================== PASSWORD FUNCTIONS ======================
        function togglePass(id, btn) {
            const input = document.getElementById(id);
            if (input.type === 'password') {
                input.type = 'text';
                btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>';
            } else {
                input.type = 'password';
                btn.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
            }
        }

        function updatePasswordStrength(value) {
            const wrap  = document.getElementById('pw-strength-wrap');
            const fill  = document.getElementById('pw-strength-fill');
            const label = document.getElementById('pw-strength-label');
            const hints = document.getElementById('pw-hints');

            if (!value) {
                wrap.style.display  = 'none';
                hints.style.display = 'none';
                return;
            }
            wrap.style.display  = 'flex';
            hints.style.display = 'grid';

            const checks = {
                len:   value.length >= 8,
                upper: /[A-Z]/.test(value),
                num:   /[0-9]/.test(value),
                sym:   /[^A-Za-z0-9]/.test(value),
            };

            const okIcon = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>';
            const noIcon = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>';

            const hintMap = { len: 'hint-len', upper: 'hint-upper', num: 'hint-num', sym: 'hint-sym' };

            Object.keys(checks).forEach(key => {
                const el = document.getElementById(hintMap[key]);
                const iconEl = el.querySelector('.hint-icon');
                iconEl.innerHTML = checks[key] ? okIcon : noIcon;
                if (checks[key]) {
                    el.classList.add('ok');
                } else {
                    el.classList.remove('ok');
                }
            });

            const score = Object.values(checks).filter(Boolean).length;
            const levels = [
                { pct: '15%', color: '#f43f5e', text: 'Sangat lemah' },
                { pct: '35%', color: '#f59e0b', text: 'Lemah' },
                { pct: '60%', color: '#eab308', text: 'Sedang' },
                { pct: '80%', color: '#22c55e', text: 'Kuat' },
                { pct: '100%', color: '#22d3a0', text: 'Sangat kuat' },
            ];
            const lvl = levels[Math.min(score, 4)];
            fill.style.width      = lvl.pct;
            fill.style.background = lvl.color;
            label.textContent     = lvl.text;
            label.style.color     = lvl.color;

            checkPasswordMatch();
        }

        function checkPasswordMatch() {
            const pw1 = document.getElementById('reg_password').value;
            const pw2 = document.getElementById('reg_password_confirmation');
            const msg = document.getElementById('confirm-mismatch');
            if (!pw2.value) { 
                msg.style.display='none'; 
                pw2.classList.remove('is-valid','is-invalid'); 
                return; 
            }
            if (pw1 === pw2.value) {
                msg.style.display = 'none';
                pw2.classList.remove('is-invalid'); 
                pw2.classList.add('is-valid');
            } else {
                msg.style.display = 'flex';
                pw2.classList.remove('is-valid'); 
                pw2.classList.add('is-invalid');
            }
        }

        // ====================== EMAIL VALIDATION ======================
        function validateEmailField(input) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const iconEl = document.getElementById('email-check-icon');
            if (!input.value) {
                input.classList.remove('is-valid','is-invalid');
                iconEl.classList.remove('show');
                iconEl.innerHTML = '';
                return;
            }
            iconEl.classList.add('show');
            if (emailRegex.test(input.value)) {
                input.classList.remove('is-invalid'); 
                input.classList.add('is-valid');
                iconEl.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>';
            } else {
                input.classList.remove('is-valid'); 
                input.classList.add('is-invalid');
                iconEl.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--danger)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>';
            }
        }

        // ====================== FORM SUBMISSIONS ======================
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
            document.getElementById('btn-register').disabled = true;
        });

        document.getElementById('form-login')?.addEventListener('submit', function() {
            document.getElementById('btn-login').disabled = true;
        });

        // Initialize - make sure step 1 is visible
        document.addEventListener('DOMContentLoaded', function() {
            goToStep(1);
        });
    </script>
</x-guest-layout>
