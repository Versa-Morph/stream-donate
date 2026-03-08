<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#070709">
    <title>{{ $title ?? 'Oops — ' . config('app.name', 'StreamDonate') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        :root {
            --bg:          #070709;
            --bg-1:        #0d0d12;
            --surface:     #141419;
            --surface-2:   #1a1a22;
            --border:      rgba(255,255,255,.07);
            --brand:       #7c6cfc;
            --brand-light: #a99dff;
            --brand-glow:  rgba(124,108,252,.2);
            --orange:      #f97316;
            --orange-glow: rgba(249,115,22,.15);
            --red:         #f43f5e;
            --red-glow:    rgba(244,63,94,.15);
            --yellow:      #fbbf24;
            --text:        #f1f1f6;
            --text-2:      #a0a0b4;
            --text-3:      #606078;
        }
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            -webkit-font-smoothing: antialiased;
        }
        .err-wrap {
            text-align: center;
            max-width: 480px;
            width: 100%;
        }
        .err-code {
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(80px, 18vw, 140px);
            font-weight: 800;
            line-height: 1;
            letter-spacing: -4px;
            background: linear-gradient(135deg, var(--brand) 0%, var(--brand-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }
        .err-code.warn {
            background: linear-gradient(135deg, var(--orange) 0%, var(--yellow) 100%);
            -webkit-background-clip: text;
            background-clip: text;
        }
        .err-code.danger {
            background: linear-gradient(135deg, var(--red) 0%, var(--orange) 100%);
            -webkit-background-clip: text;
            background-clip: text;
        }
        .err-glow {
            width: 200px; height: 200px;
            border-radius: 50%;
            background: var(--brand-glow);
            filter: blur(60px);
            position: fixed;
            top: 50%; left: 50%;
            transform: translate(-50%, -60%);
            pointer-events: none;
            z-index: 0;
        }
        .err-glow.warn  { background: var(--orange-glow); }
        .err-glow.danger { background: var(--red-glow); }
        .err-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 12px;
        }
        .err-msg {
            font-size: 15px;
            color: var(--text-2);
            line-height: 1.6;
            margin-bottom: 32px;
        }
        .err-actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: opacity .15s, transform .15s;
        }
        .btn:hover { opacity: .85; transform: translateY(-1px); }
        .btn-primary {
            background: var(--brand);
            color: #fff;
        }
        .btn-ghost {
            background: var(--surface-2, #1a1a22);
            color: var(--text-2);
            border: 1px solid var(--border);
        }
        .logo {
            position: fixed;
            top: 20px; left: 50%;
            transform: translateX(-50%);
            font-family: 'Space Grotesk', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--text-2);
            text-decoration: none;
            z-index: 10;
        }
        .logo span { color: var(--brand-light); }
    </style>
</head>
<body>
    <a href="{{ url('/') }}" class="logo">Stream<span>Donate</span></a>
    @yield('content')
</body>
</html>
