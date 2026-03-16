<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#070709">
    <title>{{ $title ?? config('app.name', 'StreamDonate') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js" defer></script>
    <style>
        :root {
            --bg:#070709;--bg-1:#0d0d12;
            --surface:rgba(20,20,26,0.85);
            --surface-2:#1a1a22;--surface-3:#1f1f28;
            --border:rgba(255,255,255,.08);--border-2:rgba(255,255,255,.14);
            --brand:#7c6cfc;--brand-light:#a99dff;--brand-glow:rgba(124,108,252,.22);
            --brand-deep:#6356e8;
            --success:#22d3a0;--success-glow:rgba(34,211,160,.15);
            --danger:#f43f5e;--danger-glow:rgba(244,63,94,.15);
            --warning:#f59e0b;
            --text:#f1f1f6;--text-2:#a0a0b4;--text-3:#606078;
            --radius:12px;--radius-lg:20px;--radius-xl:24px;
        }
        *,*::before,*::after{margin:0;padding:0;box-sizing:border-box}

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        /* ========== ANIMATED BACKGROUND ========== */
        .auth-bg {
            position: fixed; inset: 0;
            pointer-events: none; z-index: 0; overflow: hidden;
        }
        /* Radial gradient base */
        .auth-bg::before {
            content: '';
            position: absolute; inset: 0;
            background:
                radial-gradient(ellipse 80% 50% at 20% 0%, rgba(124,108,252,.12) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 80% 100%, rgba(168,85,247,.1) 0%, transparent 55%),
                radial-gradient(ellipse 40% 30% at 50% 50%, rgba(99,86,232,.06) 0%, transparent 70%);
        }
        /* Grid overlay */
        .auth-bg::after {
            content: '';
            position: absolute; inset: 0;
            background-image:
                linear-gradient(rgba(124,108,252,.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(124,108,252,.03) 1px, transparent 1px);
            background-size: 48px 48px;
            mask-image: radial-gradient(ellipse 80% 80% at 50% 50%, black 30%, transparent 100%);
        }
        .auth-blob {
            position: absolute; border-radius: 50%;
            filter: blur(100px); opacity: .14;
            will-change: transform;
        }
        .auth-blob-1 {
            width: 520px; height: 520px;
            background: radial-gradient(circle, var(--brand), var(--brand-deep));
            top: -180px; left: -180px;
            animation: blobDrift1 18s ease-in-out infinite;
        }
        .auth-blob-2 {
            width: 400px; height: 400px;
            background: radial-gradient(circle, #a855f7, #7c3aed);
            bottom: -140px; right: -140px;
            animation: blobDrift2 22s ease-in-out infinite;
        }
        .auth-blob-3 {
            width: 280px; height: 280px;
            background: radial-gradient(circle, #6366f1, #4f46e5);
            top: 45%; left: 48%;
            transform: translate(-50%,-50%);
            animation: blobDrift3 14s ease-in-out infinite 3s;
        }
        @keyframes blobDrift1 {
            0%,100%{transform:translate(0,0) scale(1)}
            33%{transform:translate(30px,40px) scale(1.06)}
            66%{transform:translate(-20px,20px) scale(0.96)}
        }
        @keyframes blobDrift2 {
            0%,100%{transform:translate(0,0) scale(1)}
            40%{transform:translate(-35px,-30px) scale(1.08)}
            70%{transform:translate(15px,-20px) scale(0.97)}
        }
        @keyframes blobDrift3 {
            0%,100%{transform:translate(-50%,-50%) scale(1)}
            50%{transform:translate(calc(-50% + 25px),calc(-50% + 35px)) scale(1.1)}
        }

        /* ========== LAYOUT ========== */
        .auth-wrap {
            width: 100%; max-width: 420px;
            padding: 20px 16px 32px;
            position: relative; z-index: 1;
        }

        /* ========== LOGO ========== */
        .auth-logo {
            text-align: center; margin-bottom: 28px;
            animation: slideDown .55s cubic-bezier(.22,.68,0,1.2) both;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px) }
            to   { opacity: 1; transform: translateY(0) }
        }
        .auth-logo-icon {
            width: 56px; height: 56px; border-radius: 18px;
            background: linear-gradient(135deg, var(--brand), #a855f7);
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 28px; margin-bottom: 12px;
            box-shadow: 0 0 0 1px rgba(255,255,255,.1), 0 0 40px var(--brand-glow), 0 8px 32px rgba(0,0,0,.3);
            transition: transform .3s cubic-bezier(.22,.68,0,1.2), box-shadow .3s;
            cursor: default;
        }
        .auth-logo-icon:hover {
            transform: scale(1.1) rotate(-6deg);
            box-shadow: 0 0 0 1px rgba(255,255,255,.12), 0 0 60px var(--brand-glow), 0 12px 40px rgba(0,0,0,.35);
        }
        .auth-logo-icon .iconify { color: #fff; font-size: 28px; width: 28px; height: 28px; }
        .auth-logo-text {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 23px; font-weight: 800; letter-spacing: -.6px;
        }
        .auth-logo-text span { color: var(--brand-light); }
        .auth-logo-tagline {
            font-size: 11px; color: var(--text-3); margin-top: 3px;
            letter-spacing: .03em;
        }

        /* ========== CARD ========== */
        .auth-card {
            background: rgba(16,16,22,.82);
            backdrop-filter: blur(24px) saturate(1.4);
            -webkit-backdrop-filter: blur(24px) saturate(1.4);
            border: 1px solid rgba(255,255,255,.09);
            border-radius: var(--radius-xl);
            padding: 32px 28px;
            box-shadow:
                0 0 0 1px rgba(124,108,252,.08),
                0 4px 6px rgba(0,0,0,.1),
                0 16px 48px rgba(0,0,0,.45),
                0 40px 80px rgba(0,0,0,.25),
                inset 0 1px 0 rgba(255,255,255,.05);
            animation: cardSlideUp .5s cubic-bezier(.22,.68,0,1.2) .08s both;
            position: relative; overflow: hidden;
        }
        .auth-card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(124,108,252,.4), transparent);
        }
        @keyframes cardSlideUp {
            from { opacity: 0; transform: translateY(24px) scale(.98) }
            to   { opacity: 1; transform: translateY(0) scale(1) }
        }

        /* ========== TYPOGRAPHY ========== */
        .auth-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 20px; font-weight: 700; letter-spacing: -.3px;
            margin-bottom: 4px;
        }
        .auth-sub { font-size: 13px; color: var(--text-3); margin-bottom: 28px; line-height: 1.5; }

        /* ========== FORM ELEMENTS ========== */
        .form-group { margin-bottom: 18px; }

        label {
            display: flex; align-items: center; gap: 5px;
            font-size: 12px; font-weight: 600; color: var(--text-2);
            margin-bottom: 7px; letter-spacing: .01em;
        }
        label .iconify { font-size: 13px; color: var(--text-3); }

        .input-wrap { position: relative; }
        .input-wrap input { padding-right: 44px !important; }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%; padding: 11px 14px;
            background: var(--surface-2);
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            font-size: 14px; outline: none;
            transition: border-color .15s, box-shadow .15s, background .15s;
        }
        input:hover:not(:focus) { border-color: var(--border-2); }
        input:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px var(--brand-glow);
            background: var(--surface-3);
        }
        input.is-valid {
            border-color: rgba(34,211,160,.4);
            box-shadow: 0 0 0 3px var(--success-glow);
        }
        input.is-invalid {
            border-color: rgba(244,63,94,.4);
            box-shadow: 0 0 0 3px var(--danger-glow);
        }
        input::placeholder { color: var(--text-3); }
        input:-webkit-autofill {
            -webkit-box-shadow: 0 0 0 100px var(--surface-3) inset;
            -webkit-text-fill-color: var(--text);
            transition: background-color 9999s;
        }

        /* Eye button */
        .eye-btn {
            position: absolute; right: 0; top: 0; bottom: 0; width: 42px;
            background: transparent; border: none; cursor: pointer;
            color: var(--text-3);
            display: flex; align-items: center; justify-content: center;
            transition: color .2s; border-radius: 0 var(--radius) var(--radius) 0;
        }
        .eye-btn:hover { color: var(--brand-light); }
        .eye-btn .iconify { font-size: 16px; width: 16px; height: 16px; }

        /* ========== BUTTONS ========== */
        .btn-submit {
            width: 100%; padding: 13px; border: none;
            border-radius: var(--radius); cursor: pointer;
            background: linear-gradient(135deg, var(--brand) 0%, #a855f7 50%, var(--brand-deep) 100%);
            background-size: 200% auto;
            color: #fff;
            font-family: 'Inter', sans-serif; font-size: 14px; font-weight: 700;
            transition: background-position .4s, transform .2s, box-shadow .2s;
            box-shadow: 0 4px 20px var(--brand-glow), 0 1px 0 rgba(255,255,255,.1) inset;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            position: relative; overflow: hidden;
        }
        .btn-submit::after {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(to bottom, rgba(255,255,255,.07), transparent);
            pointer-events: none;
        }
        .btn-submit:hover {
            background-position: right center;
            transform: translateY(-1px);
            box-shadow: 0 8px 28px var(--brand-glow), 0 1px 0 rgba(255,255,255,.1) inset;
        }
        .btn-submit:active { transform: translateY(0); box-shadow: 0 4px 16px var(--brand-glow); }
        .btn-submit:disabled { opacity: .65; cursor: not-allowed; transform: none; }
        .btn-submit .iconify { font-size: 17px; width: 17px; height: 17px; }

        /* ========== FOOTER / MISC ========== */
        .auth-footer {
            text-align: center; margin-top: 20px;
            font-size: 13px; color: var(--text-3);
        }
        .auth-footer a { color: var(--brand-light); text-decoration: none; font-weight: 600; }
        .auth-footer a:hover { text-decoration: underline; }

        .error-msg {
            font-size: 11px; color: var(--danger); margin-top: 6px;
            display: flex; align-items: center; gap: 4px;
        }

        .alert-success {
            background: rgba(34,211,160,.08);
            border: 1px solid rgba(34,211,160,.25);
            color: var(--success);
            padding: 10px 14px; border-radius: 10px;
            font-size: 13px; margin-bottom: 18px;
            display: flex; align-items: center; gap: 8px;
        }

        .checkbox-row {
            display: flex; align-items: center; gap: 10px;
            font-size: 13px; color: var(--text-2);
        }
        input[type="checkbox"] {
            width: 16px; height: 16px;
            accent-color: var(--brand);
            border-radius: 4px;
        }

        /* ========== SPIN ANIMATION ========== */
        @keyframes spin { from { transform: rotate(0deg) } to { transform: rotate(360deg) } }
        .spin { animation: spin .8s linear infinite; display: inline-block; }

        /* ========== SCROLLBAR ========== */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border-2); border-radius: 99px; }
    </style>
</head>
<body>
<div class="auth-bg">
    <div class="auth-blob auth-blob-1"></div>
    <div class="auth-blob auth-blob-2"></div>
    <div class="auth-blob auth-blob-3"></div>
</div>
<div class="auth-wrap">
    <div class="auth-logo">
        <div class="auth-logo-icon">
            <span class="iconify" data-icon="solar:gamepad-bold-duotone"></span>
        </div>
        <div class="auth-logo-text">Stream<span>Donate</span></div>
        <div class="auth-logo-tagline">Platform donasi untuk para streamer</div>
    </div>
    <div class="auth-card">
        {{ $slot }}
    </div>
    <div style="margin-top:20px;text-align:center;font-size:12px;color:var(--text-3)">
        <a href="{{ route('policies.index') }}" style="color:var(--text-3);text-decoration:none;margin:0 8px">Policies</a>
        <span style="opacity:.5">|</span>
        <a href="mailto:support@streamdonate.com" style="color:var(--text-3);text-decoration:none;margin:0 8px">Support</a>
    </div>
</div>
</body>
</html>
