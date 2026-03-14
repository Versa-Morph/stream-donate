<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#070709">
    <title>{{ $title ?? config('app.name', 'StreamDonate') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js" defer></script>
    <style>
        :root {
            --bg:#070709;--bg-1:#0d0d12;--surface:#141419;--surface-2:#1a1a22;--surface-3:#1f1f28;
            --border:rgba(255,255,255,.07);--border-2:rgba(255,255,255,.12);
            --brand:#7c6cfc;--brand-light:#a99dff;--brand-glow:rgba(124,108,252,.2);
            --text:#f1f1f6;--text-2:#a0a0b4;--text-3:#606078;
            --radius:12px;--radius-lg:18px;
        }
        *,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex;align-items:center;justify-content:center;-webkit-font-smoothing:antialiased;overflow:hidden}

        /* Background blobs */
        .auth-bg{position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden}
        .auth-bg-blob{position:absolute;border-radius:50%;filter:blur(80px);opacity:.18}
        .auth-bg-blob-1{width:480px;height:480px;background:var(--brand);top:-120px;left:-160px;animation:blobFloat 12s ease-in-out infinite}
        .auth-bg-blob-2{width:360px;height:360px;background:#a855f7;bottom:-100px;right:-120px;animation:blobFloat 16s ease-in-out infinite reverse}
        .auth-bg-blob-3{width:240px;height:240px;background:#6356e8;top:50%;left:50%;transform:translate(-50%,-50%);animation:blobFloat 10s ease-in-out infinite 4s}
        @keyframes blobFloat{0%,100%{transform:translate(0,0) scale(1)}50%{transform:translate(20px,30px) scale(1.05)}}
        .auth-bg-blob-3{animation:blobFloat3 10s ease-in-out infinite 4s}
        @keyframes blobFloat3{0%,100%{transform:translate(-50%,-50%) scale(1)}50%{transform:translate(calc(-50% + 20px),calc(-50% + 30px)) scale(1.05)}}

        .auth-wrap{width:100%;max-width:400px;padding:24px;position:relative;z-index:1}

        /* Logo */
        .auth-logo{text-align:center;margin-bottom:32px;animation:slideDown .5s cubic-bezier(.22,.68,0,1.2) both}
        @keyframes slideDown{from{opacity:0;transform:translateY(-16px)}to{opacity:1;transform:translateY(0)}}
        .auth-logo-icon{width:52px;height:52px;border-radius:16px;background:linear-gradient(135deg,var(--brand),#a855f7);display:inline-flex;align-items:center;justify-content:center;font-size:26px;box-shadow:0 0 40px var(--brand-glow),0 0 80px rgba(124,108,252,.12);margin-bottom:12px;transition:transform .3s}
        .auth-logo-icon:hover{transform:scale(1.08) rotate(-4deg)}
        .auth-logo-icon .iconify{color:#fff;font-size:26px;width:26px;height:26px}
        .auth-logo-text{font-family:'Space Grotesk',sans-serif;font-size:22px;font-weight:700;letter-spacing:-.5px}
        .auth-logo-text span{color:var(--brand-light)}

        /* Card */
        .auth-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--radius-lg);padding:32px;animation:slideUp .45s cubic-bezier(.22,.68,0,1.2) .1s both;box-shadow:0 24px 64px rgba(0,0,0,.4),0 0 0 1px var(--border)}
        @keyframes slideUp{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}}

        .auth-title{font-family:'Space Grotesk',sans-serif;font-size:20px;font-weight:700;letter-spacing:-.3px;margin-bottom:4px}
        .auth-sub{font-size:13px;color:var(--text-3);margin-bottom:28px}
        .form-group{margin-bottom:18px}
        label{display:block;font-size:12px;font-weight:600;color:var(--text-2);margin-bottom:7px}
        input[type="text"],input[type="email"],input[type="password"]{width:100%;padding:11px 14px;background:var(--surface-2);border:1px solid var(--border);border-radius:var(--radius);color:var(--text);font-family:'Inter',sans-serif;font-size:14px;outline:none;transition:border-color .15s,box-shadow .15s}
        input:focus{border-color:var(--brand);box-shadow:0 0 0 3px var(--brand-glow);background:var(--surface-3)}
        input::placeholder{color:var(--text-3)}
        .btn-submit{width:100%;padding:13px;border:none;border-radius:var(--radius);cursor:pointer;background:linear-gradient(135deg,var(--brand),#6356e8);color:#fff;font-family:'Inter',sans-serif;font-size:14px;font-weight:700;transition:all .2s;box-shadow:0 4px 20px var(--brand-glow);display:flex;align-items:center;justify-content:center;gap:8px}
        .btn-submit:hover{transform:translateY(-1px);box-shadow:0 8px 28px var(--brand-glow)}
        .btn-submit:active{transform:translateY(0);box-shadow:0 4px 16px var(--brand-glow)}
        .btn-submit .iconify{font-size:17px;width:17px;height:17px}
        .auth-footer{text-align:center;margin-top:20px;font-size:13px;color:var(--text-3)}
        .auth-footer a{color:var(--brand-light);text-decoration:none}
        .auth-footer a:hover{text-decoration:underline}
        .error-msg{font-size:11px;color:#f43f5e;margin-top:6px}
        .checkbox-row{display:flex;align-items:center;gap:10px;font-size:13px;color:var(--text-2)}
        input[type="checkbox"]{width:16px;height:16px;accent-color:var(--brand)}
    </style>
</head>
<body>
<div class="auth-bg">
    <div class="auth-bg-blob auth-bg-blob-1"></div>
    <div class="auth-bg-blob auth-bg-blob-2"></div>
    <div class="auth-bg-blob auth-bg-blob-3"></div>
</div>
<div class="auth-wrap">
    <div class="auth-logo">
        <div class="auth-logo-icon">
            <span class="iconify" data-icon="solar:gamepad-bold-duotone"></span>
        </div>
        <div class="auth-logo-text">Stream<span>Donate</span></div>
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
