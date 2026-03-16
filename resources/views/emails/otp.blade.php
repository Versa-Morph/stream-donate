<!DOCTYPE html>
<html lang="id" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="format-detection" content="telephone=no,address=no,email=no,date=no,url=no">
    <title>Kode OTP StreamDonate</title>
    <!--[if mso]>
    <noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript>
    <![endif]-->
    <style>
        /* ===== RESET ===== */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        #outlook a { padding: 0; }
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Inter', Arial, sans-serif;
            background-color: #080810;
            color: #e2e8f0;
            margin: 0; padding: 0;
            width: 100% !important;
            min-width: 100%;
        }

        /* ===== WRAPPER ===== */
        .email-wrapper {
            max-width: 560px;
            margin: 0 auto;
            padding: 40px 20px 48px;
        }

        /* ===== PREHEADER ===== */
        .preheader {
            display: none; max-height: 0; overflow: hidden;
            font-size: 1px; line-height: 1px; color: #080810;
            mso-hide: all;
        }

        /* ===== TOP LOGO BAR ===== */
        .logo-bar {
            text-align: center; margin-bottom: 28px;
        }
        .logo-badge {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 8px 16px; border-radius: 99px;
            background: rgba(124,108,252,.1);
            border: 1px solid rgba(124,108,252,.2);
        }
        .logo-icon { font-size: 20px; }
        .logo-name {
            font-size: 15px; font-weight: 800; letter-spacing: -.3px;
            color: #a99dff;
        }

        /* ===== CARD ===== */
        .card {
            background: #0f0f1a;
            border: 1px solid rgba(124,108,252,.15);
            border-radius: 20px;
            overflow: hidden;
        }

        /* ===== HERO HEADER ===== */
        .card-hero {
            background: linear-gradient(135deg, #4f35cc 0%, #7c6cfc 40%, #a855f7 100%);
            padding: 40px 32px 36px;
            text-align: center;
            position: relative;
        }
        .hero-icon-wrap {
            width: 64px; height: 64px; border-radius: 20px; margin: 0 auto 16px;
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.2);
            display: flex; align-items: center; justify-content: center;
            font-size: 32px; line-height: 1;
        }
        .hero-title {
            font-size: 24px; font-weight: 800; color: #fff;
            letter-spacing: -.5px; margin-bottom: 6px;
        }
        .hero-sub {
            font-size: 14px; color: rgba(255,255,255,.75);
        }

        /* ===== CARD BODY ===== */
        .card-body { padding: 36px 32px; }

        .greeting {
            font-size: 15px; color: #94a3b8;
            line-height: 1.7; margin-bottom: 28px;
        }
        .greeting strong { color: #e2e8f0; }
        .greeting .name-highlight {
            color: #a99dff; font-weight: 700;
        }

        /* ===== OTP BLOCK ===== */
        .otp-section-label {
            font-size: 11px; font-weight: 700; letter-spacing: .12em;
            text-transform: uppercase; color: #7c6cfc;
            display: flex; align-items: center; gap: 6px;
            margin-bottom: 14px;
        }
        .otp-block {
            background: linear-gradient(135deg, rgba(124,108,252,.06), rgba(168,85,247,.04));
            border: 1.5px solid rgba(124,108,252,.3);
            border-radius: 16px; padding: 28px 24px; text-align: center;
            margin-bottom: 8px;
            box-shadow: 0 0 40px rgba(124,108,252,.08), inset 0 1px 0 rgba(255,255,255,.04);
        }
        .otp-digits {
            display: inline-flex; gap: 8px; align-items: center; justify-content: center;
            margin-bottom: 12px;
        }
        .otp-digit-box {
            width: 46px; height: 54px;
            background: rgba(124,108,252,.12);
            border: 1.5px solid rgba(124,108,252,.3);
            border-radius: 10px;
            display: inline-flex; align-items: center; justify-content: center;
            font-size: 26px; font-weight: 900; color: #c4b8ff;
            font-family: 'Courier New', 'Courier', monospace;
            letter-spacing: 0;
        }
        .otp-digit-sep {
            font-size: 20px; color: rgba(124,108,252,.4); margin: 0 2px;
        }
        .otp-validity {
            display: flex; align-items: center; justify-content: center; gap: 5px;
            font-size: 12px; color: #606078; margin-top: 4px;
        }
        .otp-validity-icon { font-size: 13px; }
        .otp-validity strong { color: #a99dff; }

        /* Fallback plain code for email clients that don't support flex */
        .otp-plain {
            display: none;
            font-size: 38px; font-weight: 900;
            letter-spacing: 10px; color: #a99dff;
            font-family: 'Courier New', monospace;
            text-align: center;
        }

        /* ===== DIVIDER ===== */
        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(124,108,252,.15), transparent);
            margin: 28px 0;
        }

        /* ===== WARNING BOX ===== */
        .warning-box {
            background: rgba(245,158,11,.06);
            border: 1px solid rgba(245,158,11,.2);
            border-radius: 12px; padding: 16px 18px;
            display: flex; gap: 12px; align-items: flex-start;
        }
        .warning-icon {
            font-size: 18px; flex-shrink: 0; padding-top: 1px;
        }
        .warning-title {
            font-size: 13px; font-weight: 700; color: #fbbf24; margin-bottom: 4px;
        }
        .warning-text { font-size: 12px; color: #94a3b8; line-height: 1.6; }

        /* ===== HELP TEXT ===== */
        .help-text {
            font-size: 13px; color: #4a4a6a; line-height: 1.7; margin-top: 24px;
        }

        /* ===== CARD FOOTER ===== */
        .card-footer {
            padding: 20px 32px 24px;
            border-top: 1px solid rgba(255,255,255,.04);
            text-align: center;
            background: rgba(0,0,0,.15);
        }
        .footer-logo {
            font-size: 16px; font-weight: 800; color: #7c6cfc;
            letter-spacing: -.3px; margin-bottom: 6px;
        }
        .footer-links { margin-bottom: 12px; }
        .footer-links a {
            font-size: 12px; color: #4a4a6a; text-decoration: none; margin: 0 8px;
        }
        .footer-text { font-size: 11px; color: #3a3a5a; line-height: 1.7; }

        /* ===== RESPONSIVE ===== */
        @media only screen and (max-width: 560px) {
            .email-wrapper { padding: 20px 12px 32px; }
            .card-hero { padding: 28px 20px 24px; }
            .card-body { padding: 24px 20px; }
            .card-footer { padding: 16px 20px 20px; }
            .hero-title { font-size: 20px; }
            .otp-digit-box { width: 38px; height: 46px; font-size: 22px; }
            .otp-digits { gap: 5px; }
        }
    </style>
</head>
<body>
    {{-- Preheader (preview text in inbox) --}}
    <div class="preheader">
        Kode OTP kamu: {{ $code }} — Berlaku 10 menit. Jangan bagikan ke siapapun.
    </div>

    <div class="email-wrapper">

        {{-- Logo bar --}}
        <div class="logo-bar">
            <div class="logo-badge">
                <span class="logo-icon">🎮</span>
                <span class="logo-name">StreamDonate</span>
            </div>
        </div>

        <div class="card">
            {{-- Hero --}}
            <div class="card-hero">
                <div class="hero-icon-wrap">🔐</div>
                <div class="hero-title">Verifikasi Akun Kamu</div>
                <div class="hero-sub">Gunakan kode OTP berikut untuk menyelesaikan pendaftaran</div>
            </div>

            {{-- Body --}}
            <div class="card-body">
                <p class="greeting">
                    Hei, <span class="name-highlight">{{ $name }}</span>! 👋<br><br>
                    Terima kasih sudah mendaftar di <strong>StreamDonate</strong>. Kami senang kamu bergabung!
                    Masukkan kode OTP berikut di halaman verifikasi untuk mengaktifkan akun kamu.
                </p>

                {{-- OTP section --}}
                <div class="otp-section-label">
                    🔑 &nbsp;Kode Verifikasi OTP
                </div>

                <div class="otp-block">
                    {{-- Individual digit boxes --}}
                    <div class="otp-digits">
                        @php $digits = str_split($code); @endphp
                        @foreach ($digits as $i => $digit)
                            <div class="otp-digit-box">{{ $digit }}</div>
                            @if ($i === 2)
                                <div class="otp-digit-sep">·</div>
                            @endif
                        @endforeach
                    </div>

                    <div class="otp-validity">
                        <span class="otp-validity-icon">⏱</span>
                        Berlaku selama <strong>10 menit</strong> sejak email ini dikirim
                    </div>
                </div>

                <div class="divider"></div>

                {{-- Security warning --}}
                <div class="warning-box">
                    <span class="warning-icon">⚠️</span>
                    <div>
                        <div class="warning-title">Jangan bagikan kode ini!</div>
                        <div class="warning-text">
                            Kode OTP ini bersifat rahasia dan hanya untuk kamu gunakan. <strong style="color:#fbbf24;">StreamDonate tidak akan pernah meminta kode ini</strong> melalui telepon, chat, atau email lain. Jika kamu tidak merasa mendaftar, abaikan email ini — akun tidak akan dibuat.
                        </div>
                    </div>
                </div>

                <p class="help-text">
                    Jika kamu mengalami masalah atau tidak bisa mengakses halaman verifikasi, silakan kunjungi kembali halaman pendaftaran dan daftar ulang. Kode ini akan otomatis kadaluarsa dalam 10 menit.
                </p>
            </div>

            {{-- Footer --}}
            <div class="card-footer">
                <div class="footer-logo">🎮 StreamDonate</div>
                <div class="footer-links">
                    <a href="#">Bantuan</a>
                    <a href="#">Kebijakan Privasi</a>
                    <a href="#">Syarat & Ketentuan</a>
                </div>
                <div class="footer-text">
                    © {{ date('Y') }} StreamDonate by VersaMorph · Dibuat dengan ❤️ di Indonesia<br>
                    Email ini dikirim otomatis, mohon tidak membalas email ini.<br>
                    Dikirim ke: <strong style="color:#4a4a6a">{{ $code ? '(email terdaftar)' : '' }}</strong>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
