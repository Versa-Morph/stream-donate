<!doctype html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>StreamDonate — QR Widget OBS</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet" />
    <style>
        /* ── TRANSPARAN — wajib untuk OBS Browser Source ── */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        html, body {
            width: 1920px;
            height: 1080px;
            background: transparent !important;
            overflow: hidden;
            font-family: 'Inter', sans-serif;
            -webkit-font-smoothing: antialiased;
        }

        :root {
            --brand:   #7c6cfc;
            --brand2:  #a99dff;
            --surface: rgba(10,10,16,.93);
            --border:  rgba(124,108,252,.28);
            --text:    #f1f1f6;
            --text-2:  #a0a0b4;
        }

        /* ── QR WIDGET CARD ── */
        .qr-widget {
            position: fixed;
            bottom: 40px;
            right: 48px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0;

            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 22px;
            padding: 20px 20px 16px;
            width: 260px;

            box-shadow:
                0 0 0 1px rgba(124,108,252,.08),
                0 24px 60px rgba(0,0,0,.7),
                0 0 80px rgba(124,108,252,.12);

            /* Animate in on load */
            animation: slideIn .6s cubic-bezier(.34,1.3,.64,1) both;
        }

        @keyframes slideIn {
            from { transform: translateY(40px) scale(.95); opacity: 0; }
            to   { transform: translateY(0)    scale(1);   opacity: 1; }
        }

        /* Top accent line */
        .qr-widget::before {
            content: '';
            position: absolute;
            top: 0; left: 16px; right: 16px;
            height: 2px;
            border-radius: 2px;
            background: linear-gradient(90deg, var(--brand), #a99dff);
        }

        /* Header row */
        .qr-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
            width: 100%;
        }
        .qr-logo-icon {
            width: 26px; height: 26px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--brand), #6356e8);
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 800; color: #fff;
            letter-spacing: -.5px; flex-shrink: 0;
        }
        .qr-header-text {
            flex: 1;
        }
        .qr-header-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 12px; font-weight: 700;
            color: var(--text); letter-spacing: -.2px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .qr-header-sub {
            font-size: 9px; color: var(--text-2);
            margin-top: 1px; letter-spacing: .2px;
        }

        /* QR image */
        .qr-image-wrap {
            width: 200px; height: 200px;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,.07);
            background: #141419;
            display: flex; align-items: center; justify-content: center;
        }
        .qr-image-wrap img,
        .qr-image-wrap svg {
            width: 100%;
            height: 100%;
            display: block;
        }

        /* Footer */
        .qr-footer {
            margin-top: 12px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .scan-pulse {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: var(--brand2);
            animation: pulse 2s infinite;
            flex-shrink: 0;
        }
        @keyframes pulse {
            0%,100% { opacity:1; transform:scale(1); }
            50%      { opacity:.4; transform:scale(.65); }
        }
        .scan-label {
            font-size: 10px; color: var(--text-2);
            font-weight: 600; letter-spacing: .3px;
        }
        .qr-url {
            font-size: 9px; color: rgba(169,157,255,.6);
            margin-top: 4px; font-family: monospace;
            word-break: break-all; text-align: center;
            line-height: 1.4;
        }
    </style>
</head>
<body>

<div class="qr-widget">
    <!-- Header -->
    <div class="qr-header">
        <div class="qr-logo-icon">SD</div>
        <div class="qr-header-text">
            <div class="qr-header-title">{{ $streamer->display_name }}</div>
            <div class="qr-header-sub">Scan untuk donasi</div>
        </div>
    </div>

    <!-- QR Code -->
    <div class="qr-image-wrap">
        {!! $qrSvg !!}
    </div>

    <!-- Footer -->
    <div class="qr-footer">
        <span class="scan-pulse"></span>
        <span class="scan-label">SCAN TO DONATE</span>
    </div>
    <div class="qr-url">{{ $donateUrl }}</div>
</div>

</body>
</html>
