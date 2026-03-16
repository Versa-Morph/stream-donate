<x-guest-layout>

    {{-- ===== STEP PROGRESS BAR ===== --}}
    <div class="otp-progress">
        <div class="otp-progress-step done">
            <div class="otp-progress-dot">
                <span class="iconify" data-icon="solar:check-bold"></span>
            </div>
            <span class="otp-progress-lbl">Data Akun</span>
        </div>
        <div class="otp-progress-line done"></div>
        <div class="otp-progress-step active">
            <div class="otp-progress-dot">
                <span class="iconify" data-icon="solar:shield-check-bold-duotone"></span>
            </div>
            <span class="otp-progress-lbl">Verifikasi OTP</span>
        </div>
        <div class="otp-progress-line"></div>
        <div class="otp-progress-step">
            <div class="otp-progress-dot">
                <span class="iconify" data-icon="solar:confetti-bold-duotone"></span>
            </div>
            <span class="otp-progress-lbl">Selesai</span>
        </div>
    </div>

    {{-- ===== HEADER ===== --}}
    <div class="otp-header">
        <div class="otp-envelope-icon">
            <span class="iconify" data-icon="solar:letter-bold-duotone"></span>
        </div>
        <div class="auth-title" style="text-align:center;margin-top:14px;margin-bottom:4px;">
            Cek Email Kamu
        </div>
        <div class="auth-sub" style="text-align:center;margin-bottom:0;">
            Kode OTP 6 digit telah dikirim ke<br>
            <strong class="otp-email-highlight">{{ session('otp_register.email', 'email kamu') }}</strong>
        </div>
    </div>

    {{-- ===== STATUS ALERT ===== --}}
    @if (session('status'))
        <div class="alert-success" style="margin-top:20px;margin-bottom:0;">
            <span class="iconify" data-icon="solar:check-circle-bold-duotone"></span>
            {{ session('status') }}
        </div>
    @endif

    {{-- ===== OTP FORM ===== --}}
    <form method="POST" action="{{ route('otp.verify') }}" id="otp-form" style="margin-top:28px;">
        @csrf

        {{-- Digit inputs --}}
        <div class="otp-inputs-wrap">
            <div class="otp-inputs">
                @for ($i = 1; $i <= 6; $i++)
                <div class="otp-digit-wrap">
                    <input
                        type="text"
                        class="otp-digit"
                        id="otp_{{ $i }}"
                        maxlength="1"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        autocomplete="{{ $i === 1 ? 'one-time-code' : 'off' }}"
                        data-index="{{ $i }}"
                        spellcheck="false"
                    >
                </div>
                @if ($i === 3)
                    <div class="otp-sep">—</div>
                @endif
                @endfor
            </div>
        </div>
        <input type="hidden" name="otp" id="otp-hidden">

        {{-- Error message --}}
        @error('otp')
            <div class="otp-error-msg">
                <span class="iconify" data-icon="solar:danger-circle-bold-duotone"></span>
                {{ $message }}
            </div>
        @enderror

        {{-- Timer --}}
        <div class="otp-timer-wrap" id="otp-timer-wrap">
            <div class="otp-timer-ring">
                <svg viewBox="0 0 36 36" class="otp-ring-svg">
                    <circle class="otp-ring-bg" cx="18" cy="18" r="15.9"></circle>
                    <circle class="otp-ring-fill" id="otp-ring-fill" cx="18" cy="18" r="15.9"
                        stroke-dasharray="100 100" stroke-dashoffset="0"></circle>
                </svg>
            </div>
            <div class="otp-timer-text">
                <span id="countdown" class="otp-countdown">10:00</span>
                <span class="otp-timer-sub">tersisa</span>
            </div>
        </div>

        {{-- Submit button --}}
        <button type="submit" class="btn-submit" id="btn-verify" disabled>
            <span class="iconify" data-icon="solar:shield-check-bold-duotone"></span>
            Verifikasi OTP
        </button>

        {{-- Verify hint text --}}
        <p class="otp-hint-text" id="otp-hint-text">Masukkan semua 6 digit kode OTP</p>
    </form>

    {{-- ===== RESEND SECTION ===== --}}
    <div class="otp-resend-wrap">
        <p class="otp-resend-label">Tidak menerima kode?</p>
        <form method="POST" action="{{ route('otp.resend') }}" id="form-resend">
            @csrf
            <button type="submit" class="btn-resend" id="btn-resend">
                <span class="iconify" data-icon="solar:refresh-circle-bold-duotone"></span>
                <span id="resend-text">Kirim Ulang OTP</span>
            </button>
        </form>
    </div>

    <div class="auth-footer" style="margin-top:8px;">
        <a href="{{ route('login') }}">
            <span class="iconify" style="font-size:12px;vertical-align:middle;margin-right:3px;" data-icon="solar:arrow-left-bold"></span>
            Kembali ke halaman masuk
        </a>
    </div>

    <style>
        /* ===== PROGRESS STEPS ===== */
        .otp-progress {
            display: flex; align-items: center;
            margin-bottom: 28px; gap: 0;
        }
        .otp-progress-step {
            display: flex; flex-direction: column; align-items: center;
            gap: 5px; min-width: 72px; z-index: 1; flex: 0;
        }
        .otp-progress-dot {
            width: 34px; height: 34px; border-radius: 50%;
            background: var(--surface-2); border: 1.5px solid var(--border);
            display: flex; align-items: center; justify-content: center;
            transition: all .35s cubic-bezier(.22,.68,0,1.2);
        }
        .otp-progress-dot .iconify { color: var(--text-3); font-size: 14px; width: 14px; height: 14px; }
        .otp-progress-lbl { font-size: 10px; font-weight: 600; color: var(--text-3); white-space: nowrap; }
        .otp-progress-line {
            flex: 1; height: 1.5px;
            background: var(--border); margin: 0 4px; margin-bottom: 20px;
            transition: background .4s;
        }
        .otp-progress-step.done .otp-progress-dot {
            background: rgba(34,211,160,.15); border-color: rgba(34,211,160,.4);
        }
        .otp-progress-step.done .otp-progress-dot .iconify { color: var(--success); }
        .otp-progress-step.done .otp-progress-lbl { color: var(--success); }
        .otp-progress-line.done { background: rgba(34,211,160,.3); }
        .otp-progress-step.active .otp-progress-dot {
            background: linear-gradient(135deg, var(--brand), #a855f7);
            border-color: transparent;
            box-shadow: 0 0 16px var(--brand-glow);
        }
        .otp-progress-step.active .otp-progress-dot .iconify { color: #fff; }
        .otp-progress-step.active .otp-progress-lbl { color: var(--brand-light); }

        /* ===== OTP HEADER ===== */
        .otp-header { text-align: center; }
        .otp-envelope-icon {
            width: 56px; height: 56px; border-radius: 18px; margin: 0 auto;
            background: linear-gradient(135deg, rgba(124,108,252,.2), rgba(168,85,247,.15));
            border: 1px solid rgba(124,108,252,.25);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 0 30px rgba(124,108,252,.15);
            animation: pulseGlow 3s ease-in-out infinite;
        }
        .otp-envelope-icon .iconify { color: var(--brand-light); font-size: 26px; width: 26px; height: 26px; }
        @keyframes pulseGlow {
            0%,100% { box-shadow: 0 0 20px rgba(124,108,252,.12); }
            50%      { box-shadow: 0 0 35px rgba(124,108,252,.25); }
        }
        .otp-email-highlight {
            color: var(--brand-light); font-weight: 700;
        }

        /* ===== OTP INPUTS ===== */
        .otp-inputs-wrap { display: flex; justify-content: center; }
        .otp-inputs {
            display: flex; align-items: center; gap: 8px;
        }
        .otp-digit-wrap { position: relative; }
        .otp-digit {
            width: 48px !important; height: 58px;
            text-align: center;
            font-size: 22px; font-weight: 800;
            font-family: 'Space Grotesk', monospace;
            letter-spacing: 0;
            border-radius: 12px !important;
            background: var(--surface-2) !important;
            border: 1.5px solid var(--border) !important;
            color: var(--brand-light) !important;
            transition: border-color .15s, box-shadow .15s, background .15s, transform .1s;
            padding: 0 !important;
            caret-color: transparent;
            outline: none !important;
        }
        .otp-digit:focus {
            border-color: var(--brand) !important;
            box-shadow: 0 0 0 3px var(--brand-glow) !important;
            background: var(--surface-3) !important;
            transform: scale(1.05);
        }
        .otp-digit.filled {
            border-color: rgba(124,108,252,.45) !important;
            background: rgba(124,108,252,.06) !important;
        }
        .otp-digit.error-shake {
            border-color: rgba(244,63,94,.5) !important;
            box-shadow: 0 0 0 3px rgba(244,63,94,.12) !important;
            animation: shake .4s cubic-bezier(.36,.07,.19,.97);
        }
        @keyframes shake {
            10%,90%  { transform: translateX(-1px) }
            20%,80%  { transform: translateX(2px) }
            30%,50%,70% { transform: translateX(-3px) }
            40%,60%  { transform: translateX(3px) }
        }
        .otp-sep {
            font-size: 18px; color: var(--text-3); margin: 0 2px; line-height: 1;
            padding-bottom: 2px;
        }

        /* ===== ERROR MESSAGE ===== */
        .otp-error-msg {
            display: flex; align-items: center; gap: 6px; justify-content: center;
            font-size: 12px; color: var(--danger); margin-top: 12px;
        }
        .otp-error-msg .iconify { font-size: 14px; }

        /* ===== CIRCULAR TIMER ===== */
        .otp-timer-wrap {
            display: flex; align-items: center; justify-content: center;
            gap: 12px; margin: 20px 0 22px;
        }
        .otp-timer-ring {
            width: 48px; height: 48px; position: relative; flex-shrink: 0;
        }
        .otp-ring-svg { transform: rotate(-90deg); width: 100%; height: 100%; }
        .otp-ring-bg {
            fill: none; stroke: var(--border); stroke-width: 2.5;
        }
        .otp-ring-fill {
            fill: none; stroke: var(--brand);
            stroke-width: 2.5; stroke-linecap: round;
            transition: stroke-dashoffset .5s linear, stroke .5s;
        }
        .otp-timer-text { display: flex; flex-direction: column; align-items: flex-start; }
        .otp-countdown {
            font-family: 'Space Grotesk', monospace;
            font-size: 22px; font-weight: 700; color: var(--text);
            line-height: 1; letter-spacing: -.5px;
            transition: color .5s;
        }
        .otp-countdown.warning { color: var(--warning); }
        .otp-countdown.expired { color: var(--danger); }
        .otp-timer-sub { font-size: 11px; color: var(--text-3); margin-top: 2px; }

        /* ===== VERIFY HINT ===== */
        .otp-hint-text {
            text-align: center; font-size: 12px; color: var(--text-3);
            margin-top: 10px; margin-bottom: 0; transition: color .2s;
        }
        .otp-hint-text.ready { color: var(--success); }

        /* ===== RESEND ===== */
        .otp-resend-wrap {
            text-align: center; margin-top: 22px;
        }
        .otp-resend-label {
            font-size: 12px; color: var(--text-3); margin-bottom: 10px;
        }
        .btn-resend {
            background: transparent;
            border: 1.5px solid var(--border-2); border-radius: 10px;
            padding: 9px 18px; font-size: 12px; font-weight: 600;
            font-family: 'Inter', sans-serif;
            color: var(--text-2); cursor: pointer;
            transition: all .2s cubic-bezier(.22,.68,0,1.2);
            display: inline-flex; align-items: center; gap: 7px;
        }
        .btn-resend:hover:not(:disabled) {
            border-color: var(--brand); color: var(--brand-light);
            background: rgba(124,108,252,.06);
            transform: translateY(-1px);
        }
        .btn-resend:disabled { opacity: .4; cursor: not-allowed; transform: none; }
        .btn-resend .iconify { font-size: 15px; width: 15px; height: 15px; }
    </style>

    <script>
        // ===== OTP DIGIT LOGIC =====
        const digits    = document.querySelectorAll('.otp-digit');
        const hiddenInp = document.getElementById('otp-hidden');
        const btnVerify = document.getElementById('btn-verify');
        const hintText  = document.getElementById('otp-hint-text');

        // Auto-focus on first input
        digits[0].focus();

        digits.forEach((input, idx) => {
            input.addEventListener('focus', () => { input.select(); });

            input.addEventListener('input', e => {
                const val = e.target.value.replace(/[^0-9]/g, '');
                e.target.value = val ? val[val.length - 1] : '';

                if (val && idx < digits.length - 1) {
                    digits[idx + 1].focus();
                }
                syncOtp();
            });

            input.addEventListener('keydown', e => {
                if (e.key === 'Backspace') {
                    if (!e.target.value && idx > 0) {
                        digits[idx - 1].focus();
                        digits[idx - 1].value = '';
                        syncOtp();
                    } else if (e.target.value) {
                        e.target.value = '';
                        syncOtp();
                        e.preventDefault();
                    }
                }
                if (e.key === 'ArrowLeft' && idx > 0) { e.preventDefault(); digits[idx - 1].focus(); }
                if (e.key === 'ArrowRight' && idx < 5) { e.preventDefault(); digits[idx + 1].focus(); }
            });

            input.addEventListener('paste', e => {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData)
                    .getData('text').replace(/[^0-9]/g, '');
                digits.forEach((d, i) => { d.value = paste[i] || ''; });
                digits[Math.min(paste.length, 5)].focus();
                syncOtp();
            });
        });

        function syncOtp() {
            const code = [...digits].map(d => d.value).join('');
            hiddenInp.value = code;

            digits.forEach(d => {
                d.classList.toggle('filled', !!d.value);
                d.classList.remove('error-shake');
            });

            const allFilled = code.length === 6;
            btnVerify.disabled = !allFilled;

            if (allFilled) {
                hintText.textContent = 'Semua digit terisi — siap verifikasi!';
                hintText.classList.add('ready');
            } else {
                hintText.textContent = `Masukkan ${6 - code.length} digit lagi`;
                hintText.classList.remove('ready');
            }
        }

        // ===== FORM SUBMIT =====
        document.getElementById('otp-form').addEventListener('submit', function(e) {
            syncOtp();
            const code = hiddenInp.value;
            if (code.length < 6) {
                e.preventDefault();
                digits.forEach(d => {
                    if (!d.value) d.classList.add('error-shake');
                });
                return;
            }
            btnVerify.disabled = true;
            btnVerify.innerHTML = '<span class="iconify spin" data-icon="solar:spinner-bold-duotone"></span> Memverifikasi...';
        });

        // ===== CIRCULAR COUNTDOWN TIMER =====
        const TOTAL_SECONDS = 10 * 60;
        let secondsLeft = TOTAL_SECONDS;
        const countdownEl  = document.getElementById('countdown');
        const ringFill     = document.getElementById('otp-ring-fill');
        const circumference = 2 * Math.PI * 15.9; // ≈ 99.9

        function setRing(progress) {
            // progress: 1 = full, 0 = empty
            const offset = circumference * (1 - progress);
            ringFill.style.strokeDasharray  = `${circumference} ${circumference}`;
            ringFill.style.strokeDashoffset = offset;
        }

        setRing(1); // init full

        const timerInterval = setInterval(() => {
            secondsLeft--;

            const m  = Math.floor(secondsLeft / 60).toString().padStart(2, '0');
            const s  = (secondsLeft % 60).toString().padStart(2, '0');
            countdownEl.textContent = `${m}:${s}`;

            const progress = secondsLeft / TOTAL_SECONDS;
            setRing(progress);

            // Color transitions
            if (secondsLeft <= 60) {
                countdownEl.classList.remove('warning');
                countdownEl.classList.add('expired');
                ringFill.style.stroke = 'var(--danger)';
            } else if (secondsLeft <= 120) {
                countdownEl.classList.add('warning');
                ringFill.style.stroke = 'var(--warning)';
            }

            if (secondsLeft <= 0) {
                clearInterval(timerInterval);
                countdownEl.textContent = 'Kadaluarsa';
                setRing(0);
                btnVerify.disabled = true;
                btnVerify.innerHTML = '<span class="iconify" data-icon="solar:clock-circle-bold-duotone"></span> Kode Kadaluarsa';
                hintText.textContent = 'Kode telah kadaluarsa. Kirim ulang OTP.';
                hintText.classList.remove('ready');
                hintText.style.color = 'var(--danger)';
            }
        }, 1000);

        // ===== RESEND BUTTON THROTTLE =====
        const btnResend  = document.getElementById('btn-resend');
        const resendText = document.getElementById('resend-text');

        document.getElementById('form-resend').addEventListener('submit', function(e) {
            // Let server handle throttle, but disable button immediately
            btnResend.disabled = true;
            resendText.textContent = 'Mengirim...';
            btnResend.querySelector('.iconify').setAttribute('data-icon', 'solar:spinner-bold-duotone');
            btnResend.querySelector('.iconify').classList.add('spin');
        });

        // If page has errors (resend throttle), re-enable after a moment
        @if ($errors->has('otp') && str_contains($errors->first('otp'), 'Tunggu'))
        btnResend.disabled = true;
        let waitSec = 60;
        const waitInterval = setInterval(() => {
            waitSec--;
            if (waitSec <= 0) {
                clearInterval(waitInterval);
                btnResend.disabled = false;
                resendText.textContent = 'Kirim Ulang OTP';
                btnResend.querySelector('.iconify').setAttribute('data-icon', 'solar:refresh-circle-bold-duotone');
                btnResend.querySelector('.iconify').classList.remove('spin');
            } else {
                resendText.textContent = `Tunggu ${waitSec}d`;
            }
        }, 1000);
        @endif
    </script>
</x-guest-layout>
