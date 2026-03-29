<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="theme-color" content="#070709" />
    <title>Donasi untuk {{ $streamer->display_name }} — Tiptipan</title>
    <link rel="icon" type="image/x-icon" id="favicon" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet" />
    <style>
        *,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
        html{scroll-behavior:smooth}
        body{
            font-family:'Inter',sans-serif;
            background:#09090b;
            color:#fff;
            min-height:100vh;
            -webkit-font-smoothing:antialiased;
        }

        :root {
            --brand:#7c6cfc;
            --brand-dark:#6356e8;
            --pink:#ec4899;
            --text:#fafafa;
            --text-dim:#71717a;
            --border:rgba(255,255,255,0.06);
            --card:rgba(255,255,255,0.03);
        }

        /* ── BACKGROUND ── */
        .bg{
            position:fixed;inset:0;z-index:0;
            background:
                radial-gradient(ellipse 100% 50% at 50% 0%, rgba(124,108,252,0.15) 0%, transparent 60%),
                radial-gradient(ellipse 60% 40% at 80% 100%, rgba(236,72,153,0.08) 0%, transparent 50%),
                #09090b;
        }
        .bg-grid{
            position:fixed;inset:0;z-index:0;
            background-image:linear-gradient(rgba(255,255,255,0.015) 1px, transparent 1px),linear-gradient(90deg, rgba(255,255,255,0.015) 1px, transparent 1px);
            background-size:80px 80px;
            pointer-events:none;
        }

        /* ── LAYOUT ── */
        .page{
            position:relative;z-index:1;
            min-height:100vh;
            display:grid;
            grid-template-columns:1fr 480px;
        }

        /* ── LEFT: STREAMER INFO ── */
        .streamer-side{
            padding:40px 32px;
            display:flex;
            flex-direction:column;
            gap:16px;
            overflow-y:auto;
            max-height:100vh;
        }
        
        /* Info Cards Row */
        .info-row{
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(160px, 1fr));
            gap:12px;
        }

        /* Profile + QR Row */
        .profile-qr-row{
            display:flex;
            gap:16px;
            align-items:stretch;
        }

        /* Profile Card */
        .profile-card{
            flex:1;
            background:var(--card);
            border:1px solid var(--border);
            border-radius:20px;
            padding:24px;
        }
        .profile-header{
            display:flex;align-items:center;gap:16px;
            margin-bottom:16px;
        }
        .avatar{
            width:64px;height:64px;border-radius:16px;
            background:linear-gradient(135deg,var(--brand),var(--brand-dark));
            display:flex;align-items:center;justify-content:center;
            font-family:'Space Grotesk',sans-serif;
            font-size:20px;font-weight:700;color:#fff;
            box-shadow:0 4px 20px rgba(124,108,252,0.25);
            overflow:hidden;
            flex-shrink:0;
        }
        .avatar img{width:100%;height:100%;object-fit:cover}
        .profile-name{
            font-family:'Space Grotesk',sans-serif;
            font-size:20px;font-weight:700;
            margin-bottom:4px;
        }
        .profile-bio{font-size:13px;color:var(--text-dim);line-height:1.5}

        /* Tags */
        .profile-info{
            padding-top:16px;
            border-top:1px solid var(--border);
        }
        .profile-info-tags{
            display:flex;
            flex-wrap:wrap;
            gap:6px;
        }
        .tag{
            display:inline-flex;align-items:center;gap:5px;
            padding:6px 10px;
            background:rgba(255,255,255,0.03);
            border:1px solid var(--border);
            border-radius:8px;
            font-size:11px;font-weight:500;color:#fff;
            transition:all 0.2s;
        }
        .tag svg{width:12px;height:12px}
        .tag.youtube{border-color:rgba(255,0,0,0.3);color:#ff6b6b}
        .tag.youtube svg{color:#ff0000}
        .tag.tiktok{border-color:rgba(0,242,234,0.3);color:#5cf2ec}
        .tag.tiktok svg{color:#00f2ea}
        .tag.twitch{border-color:rgba(145,70,255,0.3);color:#bf94ff}
        .tag.twitch svg{color:#9146ff}
        .tag.game{border-color:rgba(251,191,36,0.3);color:#fbbf24}
        .tag.game svg{color:#f59e0b}

        /* Info Cards */
        .info-card{
            background:var(--card);
            border:1px solid var(--border);
            border-radius:16px;
            padding:20px;
        }
        .info-header{
            display:flex;align-items:center;gap:10px;
            margin-bottom:12px;
        }
        .info-icon{
            width:36px;height:36px;border-radius:10px;
            display:flex;align-items:center;justify-content:center;
        }
        .info-icon svg{width:16px;height:16px;color:#fff}
        .info-icon.purple{background:rgba(124,108,252,0.2)}
        .info-icon.pink{background:rgba(236,72,153,0.2)}
        .info-icon.green{background:rgba(34,197,94,0.2)}
        .info-icon.orange{background:rgba(249,115,22,0.2)}
        .info-icon.cyan{background:rgba(6,182,212,0.2)}
        .info-icon.red{background:rgba(239,68,68,0.2)}
        .info-title{font-size:13px;font-weight:600}

        /* Info Stat */
        .info-stat{
            font-family:'Space Grotesk',sans-serif;
            font-size:28px;font-weight:700;
            margin-bottom:4px;
        }
        .info-stat-label{font-size:12px;color:var(--text-dim)}

        /* Info Progress */
        .progress-bar{
            height:6px;
            background:rgba(255,255,255,0.06);
            border-radius:100px;
            overflow:hidden;
            margin-top:12px;
        }
        .progress-fill{
            height:100%;
            background:linear-gradient(90deg,var(--brand),var(--pink));
            border-radius:100px;
        }

        /* QR Modal */
        .qr-modal{
            display:none;
            position:fixed;
            top:0;left:0;right:0;bottom:0;
            background:rgba(0,0,0,0.9);
            z-index:9999;
            align-items:center;
            justify-content:center;
            backdrop-filter:blur(10px);
            animation:fadeIn 0.3s ease;
        }
        .qr-modal.active{display:flex}
        .qr-modal-content{
            position:relative;
            background:var(--card);
            border:1px solid var(--border);
            border-radius:20px;
            padding:32px;
            text-align:center;
            animation:scaleIn 0.3s ease;
            max-width:90vw;
        }
        .qr-modal-close{
            position:absolute;
            top:16px;right:16px;
            width:32px;height:32px;
            background:rgba(255,255,255,0.1);
            border:1px solid var(--border);
            border-radius:8px;
            display:flex;align-items:center;justify-content:center;
            cursor:pointer;
            transition:all 0.2s;
        }
        .qr-modal-close:hover{
            background:rgba(255,255,255,0.15);
            transform:scale(1.05);
        }
        .qr-modal-close svg{width:16px;height:16px;color:#fff}
        .qr-modal-image{
            width:300px;height:300px;
            background:#fff;
            border-radius:16px;
            padding:20px;
            margin:0 auto 20px;
            position:relative;
            display:flex;
            align-items:center;
            justify-content:center;
        }
        .qr-modal-image img{width:100%;height:100%;display:block}
        .qr-modal-logo{
            position:absolute;
            width:60px;height:60px;
            background:#fff;
            border-radius:12px;
            padding:8px;
            box-shadow:0 4px 12px rgba(0,0,0,0.3);
            display:flex;
            align-items:center;
            justify-content:center;
            z-index:10;
        }
        .qr-modal-logo img{
            width:100%;
            height:100%;
            object-fit:contain;
        }
        .qr-modal-title{
            font-family:'Space Grotesk',sans-serif;
            font-size:20px;font-weight:700;
            margin-bottom:8px;
        }
        .qr-modal-subtitle{
            font-size:14px;color:var(--text-dim);
        }
        @keyframes fadeIn{
            from{opacity:0}
            to{opacity:1}
        }
        @keyframes scaleIn{
            from{opacity:0;transform:scale(0.9)}
            to{opacity:1;transform:scale(1)}
        }

        /* Multi Milestone */
        .milestone-card{
            background:var(--card);
            border:1px solid var(--border);
            border-radius:16px;
            padding:20px;
        }
        .milestone-header{
            display:flex;
            align-items:center;
            justify-content:space-between;
            margin-bottom:16px;
        }
        .milestone-header-left{
            display:flex;
            align-items:center;
            gap:10px;
        }
        .milestone-nav{
            display:flex;
            gap:6px;
        }
        .milestone-nav-btn{
            width:28px;height:28px;
            background:rgba(255,255,255,0.05);
            border:1px solid var(--border);
            border-radius:8px;
            color:var(--text-dim);
            cursor:pointer;
            display:flex;align-items:center;justify-content:center;
            transition:all 0.2s;
        }
        .milestone-nav-btn:hover{background:var(--brand);border-color:var(--brand);color:#fff}
        .milestone-nav-btn svg{width:14px;height:14px}
        .milestone-slider{
            overflow:hidden;
        }
        .milestone-track{
            display:flex;
            transition:transform 0.3s ease;
        }
        .milestone-item{
            min-width:100%;
            flex-shrink:0;
        }
        .milestone-title{
            font-size:14px;
            font-weight:600;
            margin-bottom:8px;
        }
        .milestone-progress{
            display:flex;
            align-items:center;
            gap:12px;
            margin-bottom:8px;
        }
        .milestone-percent{
            font-family:'Space Grotesk',sans-serif;
            font-size:24px;
            font-weight:700;
            color:var(--brand);
            min-width:60px;
        }
        .milestone-bar{
            flex:1;
            height:8px;
            background:rgba(255,255,255,0.06);
            border-radius:100px;
            overflow:hidden;
        }
        .milestone-bar-fill{
            height:100%;
            border-radius:100px;
            transition:width 0.3s ease;
        }
        .milestone-bar-fill.purple{background:linear-gradient(90deg,#7c6cfc,#a78bfa)}
        .milestone-bar-fill.pink{background:linear-gradient(90deg,#ec4899,#f472b6)}
        .milestone-bar-fill.cyan{background:linear-gradient(90deg,#06b6d4,#22d3ee)}
        .milestone-bar-fill.green{background:linear-gradient(90deg,#22c55e,#4ade80)}
        .milestone-info{
            display:flex;
            justify-content:space-between;
            font-size:12px;
            color:var(--text-dim);
        }
        .milestone-dots{
            display:flex;
            justify-content:center;
            gap:6px;
            margin-top:12px;
        }
        .milestone-dot{
            width:6px;height:6px;
            background:rgba(255,255,255,0.15);
            border-radius:50%;
            cursor:pointer;
            transition:all 0.2s;
        }
        .milestone-dot.active{background:var(--brand);width:18px;border-radius:3px}

        /* Info List */
        .info-list{display:flex;flex-direction:column;gap:8px}
        .info-item{
            display:flex;align-items:center;gap:10px;
            font-size:13px;
        }
        .info-item svg{width:14px;height:14px;color:var(--brand);flex-shrink:0}
        .info-item span{color:var(--text-dim)}
        .recent-emoji{
            font-size:16px;
            width:24px;
            text-align:center;
            flex-shrink:0;
        }
        .rank-badge{
            width:22px;height:22px;
            border-radius:6px;
            display:flex;align-items:center;justify-content:center;
            font-size:11px;font-weight:700;
            flex-shrink:0;
        }
        .rank-badge.gold{background:linear-gradient(135deg,#fbbf24,#f59e0b);color:#000}
        .rank-badge.silver{background:linear-gradient(135deg,#9ca3af,#6b7280);color:#fff}
        .rank-badge.bronze{background:linear-gradient(135deg,#d97706,#b45309);color:#fff}

        /* Info Grid */
        .info-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px}
        .info-grid-item{text-align:center;padding:12px;background:rgba(255,255,255,0.02);border-radius:10px}
        .info-grid-value{font-family:'Space Grotesk',sans-serif;font-size:18px;font-weight:700}
        .info-grid-label{font-size:11px;color:var(--text-dim);margin-top:4px}

        /* Social */
        .social-card{
            background:var(--card);
            border:1px solid var(--border);
            border-radius:16px;
            padding:20px;
        }
        .social-title{font-size:13px;font-weight:600;margin-bottom:14px}
        .social-links{display:flex;gap:10px;flex-wrap:wrap}
        .social-link{
            display:flex;
            align-items:center;
            gap:8px;
            padding:10px 14px;
            background:rgba(255,255,255,0.03);
            border:1px solid var(--border);
            border-radius:10px;
            color:#fff;
            text-decoration:none;
            font-size:12px;
            font-weight:500;
            transition:all 0.2s;
        }
        .social-link:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,0.3)}
        .social-link svg{width:18px;height:18px;flex-shrink:0}
        .social-link.youtube{border-color:rgba(255,0,0,0.3);background:rgba(255,0,0,0.08)}
        .social-link.youtube:hover{border-color:#ff0000;background:rgba(255,0,0,0.15)}
        .social-link.youtube svg{color:#ff0000}
        .social-link.tiktok{border-color:rgba(0,242,234,0.3);background:rgba(0,242,234,0.08)}
        .social-link.tiktok:hover{border-color:#00f2ea;background:rgba(0,242,234,0.15)}
        .social-link.tiktok svg{color:#00f2ea}
        .social-link.twitter{border-color:rgba(29,161,242,0.3);background:rgba(29,161,242,0.08)}
        .social-link.twitter:hover{border-color:#1da1f2;background:rgba(29,161,242,0.15)}
        .social-link.twitter svg{color:#1da1f2}
        .social-link.instagram{border-color:rgba(225,48,108,0.3);background:rgba(225,48,108,0.08)}
        .social-link.instagram:hover{border-color:#e1306c;background:rgba(225,48,108,0.15)}
        .social-link.instagram svg{color:#e1306c}
        .social-link.discord{border-color:rgba(88,101,242,0.3);background:rgba(88,101,242,0.08)}
        .social-link.discord:hover{border-color:#5865f2;background:rgba(88,101,242,0.15)}
        .social-link.discord svg{color:#5865f2}
        .social-link.facebook{border-color:rgba(24,119,242,0.3);background:rgba(24,119,242,0.08)}
        .social-link.facebook:hover{border-color:#1877f2;background:rgba(24,119,242,0.15)}
        .social-link.facebook svg{color:#1877f2}

        /* QR */
        .qr-card{
            background:var(--card);
            border:1px solid var(--border);
            border-radius:20px;
            padding:16px;
            display:flex;
            flex-direction:column;
            align-items:center;
            justify-content:center;
            gap:10px;
            flex-shrink:0;
            cursor:pointer;
            transition:all 0.2s;
        }
        .qr-card:hover{
            border-color:var(--brand);
            transform:translateY(-2px);
            box-shadow:0 8px 24px rgba(124,108,252,0.2);
        }
        .qr-wrap{
            width:120px;height:120px;
            background:#fff;
            border-radius:12px;
            overflow:hidden;
            display:flex;align-items:center;justify-content:center;
            padding:6px;
            position:relative;
        }
        .qr-wrap img{width:100%;height:100%}
        .qr-logo{
            position:absolute;
            width:28px;height:28px;
            background:#fff;
            border-radius:6px;
            padding:4px;
            box-shadow:0 2px 8px rgba(0,0,0,0.2);
            display:flex;
            align-items:center;
            justify-content:center;
            z-index:10;
        }
        .qr-logo img{
            width:100%;
            height:100%;
            object-fit:contain;
        }
        .qr-label{
            font-size:12px;
            font-weight:600;
            color:var(--text-dim);
            text-align:center;
        }

        /* Card positions removed - using flexbox flow instead */

        /* ── RIGHT: FORM ── */
        .form-side{
            position:sticky;
            top:0;
            height:100vh;
            padding:32px;
            background:rgba(0,0,0,0.4);
            border-left:1px solid var(--border);
            display:flex;
            flex-direction:column;
            justify-content:flex-start;
            padding-top:60px;
            overflow-y:auto;
        }
        .form-card{
            width:100%;
            max-width:420px;
            margin:0 auto;
        }

        /* Form Header */
        .form-header{
            margin-bottom:20px;
            text-align:center;
        }
        .form-title{
            font-family:'Space Grotesk',sans-serif;
            font-size:22px;font-weight:700;
            margin-bottom:4px;
        }
        .form-sub{
            font-size:14px;color:var(--text-dim);
        }

        /* Amount */
        .field{margin-bottom:16px}
        .field-label{
            font-size:12px;font-weight:600;
            color:var(--text-dim);
            margin-bottom:8px;
        }
        .amount-presets{
            display:grid;grid-template-columns:repeat(3,1fr);
            gap:8px;
            margin-bottom:10px;
        }
        .preset-btn{
            padding:10px 6px;
            background:var(--card);
            border:1px solid var(--border);
            border-radius:10px;
            color:var(--text-dim);
            font-size:13px;font-weight:600;
            cursor:pointer;
            transition:all 0.2s;
        }
        .preset-btn:hover{border-color:rgba(255,255,255,0.2);color:#fff;background:rgba(255,255,255,0.05)}
        .preset-btn.active{
            background:linear-gradient(135deg,var(--brand),var(--brand-dark));
            border-color:var(--brand);
            color:#fff;
            box-shadow:0 4px 16px rgba(124,108,252,0.3);
        }
        .amount-wrap{position:relative}
        .amount-prefix{
            position:absolute;left:14px;top:50%;transform:translateY(-50%);
            font-size:15px;font-weight:600;color:var(--text-dim);
        }
        .amount-input{
            width:100%;padding:12px 14px 12px 44px;
            background:var(--card);
            border:1px solid var(--border);
            border-radius:10px;
            color:#fff;font-size:16px;font-weight:600;
            outline:none;transition:all 0.2s;
        }
        .amount-input:focus{border-color:var(--brand);box-shadow:0 0 0 3px rgba(124,108,252,0.15)}

        /* Input */
        .text-input{
            width:100%;padding:10px 12px;
            background:var(--card);
            border:1px solid var(--border);
            border-radius:10px;
            color:#fff;font-size:13px;
            outline:none;transition:all 0.2s;
        }
        .text-input::placeholder{color:var(--text-dim)}
        .text-input:focus{border-color:var(--brand);box-shadow:0 0 0 3px rgba(124,108,252,0.15)}
        textarea.text-input{resize:vertical;min-height:60px;line-height:1.4}
        .field-header{
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:6px;
        }
        .field-header .field-label{margin-bottom:0}
        .char-counter{
            font-size:11px;
            color:var(--text-dim);
        }
        .char-counter.warn{color:#f59e0b}
        .char-counter.limit{color:#ef4444}

        /* Media Request */
        .media-card{
            padding:14px;
            background:var(--card);
            border:1px solid var(--border);
            border-radius:12px;
            margin-bottom:16px;
        }
        .media-tabs-container{
            position:relative;
            margin-bottom:10px;
        }
        .media-tabs{
            display:flex;
            gap:6px;
            overflow-x:auto;
            overflow-y:hidden;
            padding:4px 2px;
            scroll-behavior:smooth;
            -webkit-overflow-scrolling:touch;
            scrollbar-width:none;
        }
        .media-tabs::-webkit-scrollbar{display:none}
        .media-tab{
            padding:8px 14px;
            background:transparent;
            border:1px solid var(--border);
            border-radius:8px;
            color:var(--text-dim);
            font-size:11px;font-weight:500;
            cursor:pointer;
            transition:all 0.2s;
            display:flex;align-items:center;gap:6px;
            white-space:nowrap;
            flex-shrink:0;
        }
        .media-tab svg{width:14px;height:14px;flex-shrink:0}
        .media-tab:hover{border-color:rgba(255,255,255,0.2);color:#fff}
        .media-tab.active{
            background:linear-gradient(135deg,var(--brand),var(--brand-dark));
            border-color:var(--brand);
            color:#fff;
        }
        .media-tab.youtube.active{background:linear-gradient(135deg,#ff0000,#cc0000);border-color:#ff0000}
        .media-tab.tiktok.active{background:linear-gradient(135deg,#00f2ea,#ff0050);border-color:#00f2ea}
        .media-tab.instagram.active{background:linear-gradient(135deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888);border-color:#dc2743}
        .media-tab.twitter.active{background:linear-gradient(135deg,#1da1f2,#0d8ecf);border-color:#1da1f2}
        .media-tab.spotify.active{background:linear-gradient(135deg,#1db954,#1aa34a);border-color:#1db954}
        .media-nav{
            position:absolute;
            top:50%;transform:translateY(-50%);
            width:24px;height:24px;
            background:rgba(0,0,0,0.8);
            border:1px solid var(--border);
            border-radius:50%;
            color:#fff;
            cursor:pointer;
            display:flex;align-items:center;justify-content:center;
            z-index:2;
            transition:all 0.2s;
        }
        .media-nav:hover{background:var(--brand)}
        .media-nav.prev{left:-6px}
        .media-nav.next{right:-6px}
        .media-nav svg{width:12px;height:12px}
        .media-input{
            width:100%;padding:10px 12px;
            background:rgba(0,0,0,0.25);
            border:1px solid rgba(255,255,255,0.08);
            border-radius:8px;
            color:#fff;font-size:12px;
            outline:none;transition:all 0.2s;
        }
        .media-input::placeholder{color:var(--text-dim)}
        .media-input:focus{border-color:var(--brand);box-shadow:0 0 0 2px rgba(124,108,252,0.1)}
        .media-hint{
            font-size:10px;color:var(--text-dim);
            margin-top:6px;
        }
        .media-input-wrap{display:none}
        .media-input-wrap.active{display:block}

        /* Custom File Upload */
        .file-upload-wrap{
            position:relative;
        }
        .file-upload-input{
            position:absolute;
            width:100%;
            height:100%;
            top:0;
            left:0;
            opacity:0;
            cursor:pointer;
            z-index:2;
        }
        .file-upload-btn{
            display:flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            width:100%;
            padding:12px 16px;
            background:rgba(0,0,0,0.25);
            border:1px dashed rgba(255,255,255,0.15);
            border-radius:8px;
            color:var(--text-dim);
            font-size:12px;
            font-weight:500;
            cursor:pointer;
            transition:all 0.2s;
        }
        .file-upload-btn:hover{
            border-color:var(--brand);
            color:var(--brand);
            background:rgba(124,108,252,0.05);
        }
        .file-upload-btn svg{
            width:16px;
            height:16px;
        }
        .file-upload-btn.has-file{
            border-style:solid;
            border-color:var(--brand);
            color:#fff;
            background:rgba(124,108,252,0.1);
        }
        .file-upload-name{
            max-width:200px;
            overflow:hidden;
            text-overflow:ellipsis;
            white-space:nowrap;
        }
        .media-hint-sep{
            margin:0 6px;
            opacity:0.5;
        }
        .media-duration-note{
            display:flex;
            align-items:center;
            gap:6px;
            margin-top:8px;
            padding:8px 10px;
            background:rgba(124,108,252,0.1);
            border:1px solid rgba(124,108,252,0.2);
            border-radius:6px;
            font-size:11px;
            color:var(--brand);
        }
        .media-duration-note.warning{
            background:rgba(251,191,36,0.1);
            border-color:rgba(251,191,36,0.3);
            color:#fbbf24;
        }
        .media-duration-note.error{
            background:rgba(239,68,68,0.1);
            border-color:rgba(239,68,68,0.3);
            color:#f87171;
        }

        /* Milestone Selection Slider */
        .milestone-selection{
            background:rgba(0,0,0,0.25);
            border:1px solid rgba(255,255,255,0.08);
            border-radius:12px;
            padding:16px;
        }
        .milestone-selection-header{
            display:flex;
            align-items:center;
            justify-content:space-between;
            margin-bottom:14px;
        }
        .milestone-selection-header-left{
            display:flex;
            align-items:center;
            gap:10px;
        }
        .milestone-selection-title{
            font-size:13px;
            font-weight:600;
            color:var(--text-primary);
        }
        .milestone-selection-optional{
            font-size:10px;
            color:var(--text-dim);
            background:rgba(255,255,255,0.05);
            padding:2px 8px;
            border-radius:4px;
        }
        .milestone-selection-nav{
            display:flex;
            gap:6px;
        }
        .milestone-selection-nav-btn{
            width:26px;
            height:26px;
            background:rgba(255,255,255,0.05);
            border:1px solid var(--border);
            border-radius:8px;
            color:var(--text-dim);
            cursor:pointer;
            display:flex;
            align-items:center;
            justify-content:center;
            transition:all 0.2s;
        }
        .milestone-selection-nav-btn:hover{
            background:var(--brand);
            border-color:var(--brand);
            color:#fff;
        }
        .milestone-selection-nav-btn svg{
            width:12px;
            height:12px;
        }
        .milestone-selection-slider{
            overflow:hidden;
            border-radius:10px;
            min-height:110px;
        }
        .milestone-selection-track{
            display:flex;
            transition:transform 0.3s ease;
            align-items:stretch;
        }
        .milestone-selection-item{
            min-width:100%;
            flex-shrink:0;
            display:flex;
        }
        .milestone-option{
            background:rgba(0,0,0,0.3);
            border:2px solid rgba(255,255,255,0.08);
            border-radius:10px;
            padding:12px;
            cursor:pointer;
            transition:all 0.2s;
            width:100%;
            display:flex;
            flex-direction:column;
            min-height:110px;
        }
        .milestone-option:hover{
            border-color:rgba(124,108,252,0.3);
            transform:translateY(-1px);
        }
        .milestone-option.selected{
            border-color:var(--brand);
            background:rgba(124,108,252,0.12);
        }
        .milestone-option-top{
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            margin-bottom:8px;
            flex:1;
        }
        .milestone-option-title{
            font-size:13px;
            font-weight:600;
            color:var(--text-primary);
            margin-bottom:4px;
            white-space:nowrap;
            overflow:hidden;
            text-overflow:ellipsis;
            max-width:200px;
        }
        .milestone-option-check{
            width:18px;
            height:18px;
            border:2px solid rgba(255,255,255,0.2);
            border-radius:50%;
            flex-shrink:0;
            display:flex;
            align-items:center;
            justify-content:center;
            transition:all 0.2s;
        }
        .milestone-option.selected .milestone-option-check{
            background:var(--brand);
            border-color:var(--brand);
        }
        .milestone-option-check svg{
            width:10px;
            height:10px;
            opacity:0;
            transition:opacity 0.2s;
        }
        .milestone-option.selected .milestone-option-check svg{
            opacity:1;
        }
        .milestone-option-progress{
            margin-top:8px;
        }
        .milestone-option-progress-row{
            display:flex;
            align-items:center;
            gap:10px;
            margin-bottom:6px;
        }
        .milestone-option-percent{
            font-family:'Space Grotesk',sans-serif;
            font-size:18px;
            font-weight:700;
            color:var(--brand);
            min-width:50px;
        }
        .milestone-option-bar{
            flex:1;
            height:6px;
            background:rgba(0,0,0,0.4);
            border-radius:3px;
            overflow:hidden;
        }
        .milestone-option-bar-fill{
            height:100%;
            border-radius:3px;
            transition:width 0.3s;
        }
        .milestone-option-bar-fill.purple{background:linear-gradient(90deg,#7c6cfc,#a78bfa)}
        .milestone-option-bar-fill.pink{background:linear-gradient(90deg,#ec4899,#f472b6)}
        .milestone-option-bar-fill.cyan{background:linear-gradient(90deg,#06b6d4,#22d3ee)}
        .milestone-option-bar-fill.green{background:linear-gradient(90deg,#22c55e,#4ade80)}
        .milestone-option-bar-fill.orange{background:linear-gradient(90deg,#f97316,#fb923c)}
        .milestone-option-amount{
            font-size:10px;
            color:var(--text-dim);
        }
        .milestone-selection-dots{
            display:flex;
            justify-content:center;
            gap:6px;
            margin-top:12px;
        }
        .milestone-selection-dot{
            width:6px;
            height:6px;
            background:rgba(255,255,255,0.15);
            border-radius:50%;
            cursor:pointer;
            transition:all 0.2s;
        }
        .milestone-selection-dot.active{
            background:var(--brand);
            width:18px;
            border-radius:3px;
        }

        /* Submit */
        .submit-btn{
            width:100%;padding:12px;
            background:linear-gradient(135deg,var(--brand),var(--brand-dark));
            border:none;border-radius:12px;
            color:#fff;font-size:14px;font-weight:700;
            cursor:pointer;
            transition:all 0.3s;
            display:flex;align-items:center;justify-content:center;gap:8px;
            box-shadow:0 4px 20px rgba(124,108,252,0.35);
        }
        .submit-btn:hover{transform:translateY(-2px);box-shadow:0 6px 24px rgba(124,108,252,0.45)}
        .submit-btn:active{transform:translateY(0)}
        .submit-btn svg{width:16px;height:16px}
        .submit-btn:disabled{opacity:0.5;cursor:not-allowed;transform:none}

        /* Error */
        .error-box{
            padding:10px 12px;
            background:rgba(239,68,68,0.1);
            border:1px solid rgba(239,68,68,0.25);
            border-radius:10px;
            color:#f87171;font-size:13px;
            margin-bottom:14px;
            display:none;
        }
        .error-box.show{display:block}

        /* Success */
        .success-box{text-align:center;padding:32px 16px;display:none}
        .success-box.show{display:block}
        .success-emoji{font-size:48px;margin-bottom:14px}
        .success-title{
            font-family:'Space Grotesk',sans-serif;
            font-size:20px;font-weight:700;
            margin-bottom:12px;
        }
        .success-msg{
            font-size:13px;color:#a78bfa;
            background:rgba(124,108,252,0.1);
            border:1px solid rgba(124,108,252,0.2);
            border-radius:10px;
            padding:12px;
            margin:12px 0;
        }
        .success-countdown{font-size:11px;color:var(--text-dim);margin-bottom:14px}

        /* Status */
        #conn-status{
            display:inline-flex;align-items:center;gap:6px;
            padding:6px 12px;border-radius:100px;
            font-size:11px;font-weight:600;
            margin-bottom:16px;
        }
        #conn-status.ok{background:rgba(34,197,94,0.1);border:1px solid rgba(34,197,94,0.25);color:#22c55e}
        #conn-status.err{background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.25);color:#ef4444}
        #conn-status.wait{background:rgba(251,191,36,0.1);border:1px solid rgba(251,191,36,0.25);color:#f59e0b}
        .conn-dot{width:5px;height:5px;border-radius:50%;background:currentColor;animation:pulse 2s infinite}

        /* Footer */
        .form-footer{
            margin-top:20px;padding-top:16px;
            border-top:1px solid var(--border);
            text-align:center;
            font-size:11px;color:var(--text-dim);
        }
        .form-footer a{color:var(--brand);text-decoration:none;font-weight:500}
        .form-footer a:hover{text-decoration:underline}
        
        /* Hide Mobile Elements on Desktop */
        .mobile-streamer-header,
        .mobile-qr-section{
            display:none;
        }

        /* Responsive Medium Desktop */
        @media(max-width:1280px) and (min-width:1025px){
            .page{
                grid-template-columns:1fr 450px;
            }
        }

        /* Responsive Tablet */
        @media(max-width:1024px){
            .page{
                grid-template-columns:1fr;
            }
            .streamer-side{
                padding:24px 20px;
                max-height:none;
                order:2;
            }
            .form-side{
                position:relative;
                height:auto;
                border-left:none;
                border-bottom:1px solid var(--border);
                padding:28px 20px;
                order:1;
            }
            .info-row{
                grid-template-columns:repeat(2, 1fr);
            }
            .qr-wrap{
                width:100px;height:100px;
            }
        }
        
        /* Responsive Mobile - Compact Layout */
        @media(max-width:640px){
            body{
                font-size:14px;
            }
            .page{
                grid-template-columns:1fr;
                gap:0;
            }
            
            /* Mobile Streamer Header - Vertical Centered Layout */
            .mobile-streamer-header{
                display:flex;
                flex-direction:column;
                align-items:center;
                text-align:center;
                padding:20px 12px 16px;
                margin-bottom:20px;
                background:transparent;
                border:none;
            }
            .mobile-streamer-avatar{
                width:80px;
                height:80px;
                border-radius:50%;
                background:linear-gradient(135deg,var(--brand),var(--brand-dark));
                display:flex;
                align-items:center;
                justify-content:center;
                font-family:'Space Grotesk',sans-serif;
                font-size:28px;
                font-weight:700;
                color:#fff;
                box-shadow:0 4px 20px rgba(124,108,252,0.3);
                overflow:hidden;
                flex-shrink:0;
                margin-bottom:12px;
                position:relative;
            }
            .mobile-streamer-avatar img{
                width:100%;
                height:100%;
                object-fit:cover;
            }
            .mobile-streamer-info{
                width:100%;
            }
            .mobile-streamer-name-row{
                display:flex;
                align-items:center;
                justify-content:center;
                gap:8px;
                margin-bottom:8px;
            }
            .mobile-streamer-name{
                font-family:'Space Grotesk',sans-serif;
                font-size:18px;
                font-weight:700;
                color:#fff;
            }
            .mobile-online-status{
                width:8px;
                height:8px;
                border-radius:50%;
                background:#22c55e;
                box-shadow:0 0 0 2px rgba(34,197,94,0.2);
                animation:pulse-online 2s infinite;
            }
            @keyframes pulse-online{
                0%,100%{
                    opacity:1;
                    transform:scale(1);
                }
                50%{
                    opacity:0.7;
                    transform:scale(1.1);
                }
            }
            .mobile-streamer-bio{
                font-size:12px;
                color:var(--text-dim);
                line-height:1.5;
                margin-bottom:12px;
                max-width:280px;
                margin-left:auto;
                margin-right:auto;
            }
            .mobile-streamer-tags{
                display:flex;
                flex-wrap:wrap;
                gap:6px;
                justify-content:center;
            }
            .mobile-tag{
                display:inline-flex;
                align-items:center;
                gap:4px;
                padding:4px 8px;
                background:rgba(255,255,255,0.05);
                border:1px solid var(--border);
                border-radius:8px;
                font-size:10px;
                font-weight:600;
                color:#fff;
                transition:all 0.2s;
            }
            .mobile-tag:active{
                transform:scale(0.95);
            }
            .mobile-tag svg{
                width:10px;
                height:10px;
            }
            .mobile-tag.youtube{border-color:rgba(255,0,0,0.4);color:#ff6b6b;background:rgba(255,0,0,0.1)}
            .mobile-tag.youtube svg{color:#ff0000}
            .mobile-tag.tiktok{border-color:rgba(0,242,234,0.4);color:#5cf2ec;background:rgba(0,242,234,0.1)}
            .mobile-tag.tiktok svg{color:#00f2ea}
            .mobile-tag.twitch{border-color:rgba(145,70,255,0.4);color:#bf94ff;background:rgba(145,70,255,0.1)}
            .mobile-tag.twitch svg{color:#9146ff}
            .mobile-tag.game{border-color:rgba(251,191,36,0.4);color:#fbbf24;background:rgba(251,191,36,0.1)}
            .mobile-tag.game svg{color:#f59e0b}
            
            /* Mobile QR + Social Section (2 Columns) */
            .mobile-qr-section{
                display:block;
                margin-top:16px;
                padding-top:16px;
                border-top:1px solid var(--border);
            }
            .mobile-qr-grid{
                display:grid;
                grid-template-columns:auto 1fr;
                gap:16px;
                align-items:start;
            }
            .mobile-qr-col{
                display:flex;
                flex-direction:column;
                align-items:center;
                gap:6px;
            }
            .mobile-qr-wrap{
                width:80px;
                height:80px;
                background:#fff;
                border-radius:12px;
                padding:6px;
                display:flex;
                align-items:center;
                justify-content:center;
                position:relative;
                cursor:pointer;
                transition:all 0.2s;
                flex-shrink:0;
            }
            .mobile-qr-wrap:active{
                transform:scale(0.95);
            }
            .mobile-qr-wrap img{
                width:100%;
                height:100%;
            }
            .mobile-qr-logo{
                position:absolute;
                width:18px;
                height:18px;
                background:#fff;
                border-radius:4px;
                padding:2px;
                box-shadow:0 2px 8px rgba(0,0,0,0.2);
                display:flex;
                align-items:center;
                justify-content:center;
                z-index:10;
            }
            .mobile-qr-logo img{
                width:100%;
                height:100%;
                object-fit:contain;
            }
            .mobile-qr-label{
                font-size:10px;
                color:var(--text-dim);
                font-weight:600;
            }
            
            /* Mobile Social Column */
            .mobile-social-col{
                display:flex;
                flex-direction:column;
                gap:8px;
            }
            .mobile-social-label{
                font-size:11px;
                font-weight:600;
                color:var(--text-dim);
                text-align:left;
            }
            .mobile-social-links{
                display:flex;
                flex-wrap:nowrap;
                gap:6px;
            }
            .mobile-social-link{
                width:28px;
                height:28px;
                border-radius:8px;
                display:flex;
                align-items:center;
                justify-content:center;
                transition:all 0.2s;
                border:1px solid var(--border);
                flex-shrink:0;
            }
            .mobile-social-link:active{
                transform:scale(0.9);
            }
            .mobile-social-link svg{
                width:14px;
                height:14px;
            }
            .mobile-social-link.youtube{
                background:rgba(255,0,0,0.1);
                border-color:rgba(255,0,0,0.3);
                color:#ff0000;
            }
            .mobile-social-link.tiktok{
                background:rgba(0,242,234,0.1);
                border-color:rgba(0,242,234,0.3);
                color:#00f2ea;
            }
            .mobile-social-link.instagram{
                background:linear-gradient(135deg,rgba(249,115,22,0.1),rgba(236,72,153,0.1));
                border-color:rgba(236,72,153,0.3);
                color:#ec4899;
            }
            .mobile-social-link.twitter{
                background:rgba(29,161,242,0.1);
                border-color:rgba(29,161,242,0.3);
                color:#1da1f2;
            }
            .mobile-social-link.discord{
                background:rgba(88,101,242,0.1);
                border-color:rgba(88,101,242,0.3);
                color:#5865f2;
            }
            
            /* Hide Desktop Streamer Side */
            .streamer-side{
                display:none;
            }
            
            /* Hide Connection Status on Mobile */
            #conn-status{
                display:none !important;
            }
            
            /* Hide Form Header on Mobile (show on desktop/tablet) */
            .form-header{
                display:none;
            }
            
            /* Form Side - Sticky Top */
            .form-side{
                position:sticky;
                top:0;
                z-index:100;
                padding:16px 12px;
                height:auto;
                border-left:none;
                border-bottom:1px solid var(--border);
                order:1;
                background:rgba(0,0,0,0.95);
                backdrop-filter:blur(20px);
            }
            .form-card{
                max-width:100%;
                padding:16px;
                border-radius:16px;
            }
            .form-title{
                font-size:18px;
                margin-bottom:12px;
            }
            
            /* Compact Fields */
            .field{
                margin-bottom:12px;
            }
            .field-header{
                margin-bottom:6px;
            }
            .field-label{
                font-size:12px;
            }
            .char-counter{
                font-size:10px;
            }
            .input{
                padding:10px 12px;
                font-size:14px;
            }
            .textarea{
                min-height:60px;
                padding:10px 12px;
                font-size:14px;
            }
            
            /* Amount Compact */
            .amount-display{
                font-size:24px;
                padding:12px;
            }
            .amount-presets{
                grid-template-columns:repeat(3,1fr);
                gap:6px;
            }
            .preset-btn{
                padding:8px 12px;
                font-size:12px;
            }
            
            /* Emoji Picker - Single Row */
            .emoji-picker{
                padding:10px;
                gap:8px;
            }
            .emoji-grid{
                grid-template-rows:repeat(1,1fr);
                gap:6px;
            }
            .emoji-btn{
                width:32px;
                height:32px;
                font-size:18px;
            }
            .emoji-nav{
                display:none;
            }
            
            /* Media Tabs - Vertical Compact */
            .media-tabs{
                flex-direction:row;
                flex-wrap:wrap;
                gap:6px;
                overflow:visible;
            }
            .media-tab{
                flex:1;
                min-width:calc(33.333% - 4px);
                padding:8px 10px;
                font-size:11px;
                gap:4px;
                justify-content:center;
            }
            .media-tab svg{
                width:14px;
                height:14px;
            }
            .media-nav{
                display:none;
            }
            
            /* Submit Button */
            .submit-btn{
                padding:12px;
                font-size:14px;
            }
            
            /* Milestone Selection Slider Mobile */
            .milestone-selection{
                padding:12px;
                border-radius:10px;
            }
            .milestone-selection-slider{
                min-height:100px;
            }
            .milestone-option{
                padding:10px;
                border-radius:8px;
                min-height:100px;
            }
            .milestone-selection-title{
                font-size:12px;
            }
            .milestone-selection-optional{
                font-size:9px;
            }
            .milestone-selection-nav-btn{
                width:24px;
                height:24px;
            }
            .milestone-selection-nav-btn svg{
                width:10px;
                height:10px;
            }
            .milestone-option-title{
                font-size:12px;
                max-width:180px;
            }
            .milestone-option-check{
                width:16px;
                height:16px;
            }
            .milestone-option-check svg{
                width:9px;
                height:9px;
            }
            .milestone-option-percent{
                font-size:16px;
                min-width:45px;
            }
            .milestone-option-bar{
                height:5px;
            }
            .milestone-option-amount{
                font-size:9px;
            }
            
            /* Streamer Side */
            .streamer-side{
                padding:16px 12px;
                order:2;
            }
            
            /* Profile Compact */
            .profile-qr-row{
                flex-direction:column;
                gap:12px;
            }
            .profile-card{
                padding:16px;
                border-radius:16px;
            }
            .profile-header{
                flex-direction:row;
                text-align:left;
                margin-bottom:12px;
                gap:12px;
            }
            .avatar{
                width:56px;
                height:56px;
                font-size:18px;
            }
            .profile-name{
                font-size:16px;
            }
            .profile-bio{
                font-size:12px;
                line-height:1.4;
            }
            .profile-info{
                padding-top:12px;
            }
            .profile-info-tags{
                gap:4px;
            }
            .tag{
                padding:4px 8px;
                font-size:10px;
            }
            .tag svg{
                width:10px;
                height:10px;
            }
            
            /* QR Card - Horizontal Compact */
            .qr-card{
                flex-direction:row;
                padding:12px 16px;
                gap:12px;
                border-radius:16px;
            }
            .qr-wrap{
                width:64px;
                height:64px;
            }
            .qr-logo{
                width:16px;
                height:16px;
                padding:2px;
                border-radius:3px;
            }
            .qr-label{
                font-size:11px;
                align-self:center;
            }
            
            /* Info Cards Compact */
            .info-row{
                grid-template-columns:1fr;
                gap:10px;
            }
            .info-card{
                padding:14px;
                border-radius:14px;
            }
            .info-icon{
                width:32px;
                height:32px;
            }
            .info-icon svg{
                width:14px;
                height:14px;
            }
            .info-title{
                font-size:12px;
            }
            .info-stat{
                font-size:20px;
            }
            .info-stat-label{
                font-size:11px;
            }
            
            /* Milestone Compact */
            .milestone-card{
                padding:14px;
                border-radius:14px;
            }
            .milestone-header{
                margin-bottom:12px;
            }
            .milestone-nav-btn{
                width:24px;
                height:24px;
            }
            .milestone-nav-btn svg{
                width:10px;
                height:10px;
            }
            .milestone-item{
                padding:12px;
            }
            .milestone-title{
                font-size:13px;
            }
            .milestone-progress{
                font-size:20px;
            }
            .milestone-amount{
                font-size:11px;
            }
            
            /* Social Links Compact */
            .social-links{
                gap:8px;
            }
            .social-link{
                padding:10px 14px;
                font-size:12px;
                border-radius:10px;
            }
            .social-link svg{
                width:14px;
                height:14px;
            }
            
            /* Recent Donations Compact */
            .recent-item{
                padding:10px;
            }
            .recent-emoji{
                font-size:18px;
            }
            .recent-name{
                font-size:12px;
            }
            .recent-msg{
                font-size:11px;
            }
            .recent-amount{
                font-size:13px;
            }
            
            /* Top Donatur Compact */
            .top-item{
                padding:10px;
            }
            .rank-badge{
                width:20px;
                height:20px;
                font-size:10px;
            }
            .top-name{
                font-size:12px;
            }
            .top-total{
                font-size:13px;
            }
            
            /* QR Modal Mobile */
            .qr-modal-image{
                width:280px;
                height:280px;
                padding:20px;
            }
            .qr-modal-logo{
                width:56px;
                height:56px;
                padding:8px;
                border-radius:10px;
            }
            .qr-modal-content{
                padding:20px;
                border-radius:16px;
            }
            .qr-modal-title{
                font-size:18px;
            }
            .qr-modal-subtitle{
                font-size:13px;
            }
            
            /* Hide desktop elements */
            .form-footer{
                font-size:10px;
                margin-top:12px;
                padding-top:12px;
            }
        }
        
        @keyframes pulse{
            0%,100%{opacity:1}
            50%{opacity:0.4}
        }
    </style>
</head>
<body>

<!-- QR Modal -->
<div class="qr-modal" id="qrModal" onclick="closeQRModal(event)">
    <div class="qr-modal-content" onclick="event.stopPropagation()">
        <button class="qr-modal-close" onclick="closeQRModal()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
        <div class="qr-modal-image">
            <img src="{{ route('qr.show', $streamer->slug) }}" alt="QR Code" />
            <div class="qr-modal-logo">
                <svg viewBox="0 0 316 316" xmlns="http://www.w3.org/2000/svg">
                    <path fill="#FF2D20" d="M305.8 81.125C305.77 80.995 305.69 80.885 305.65 80.755C305.56 80.525 305.49 80.285 305.37 80.075C305.29 79.935 305.17 79.815 305.07 79.685C304.94 79.515 304.83 79.325 304.68 79.175C304.55 79.045 304.39 78.955 304.25 78.845C304.09 78.715 303.95 78.575 303.77 78.475L251.32 48.275C249.97 47.495 248.31 47.495 246.96 48.275L194.51 78.475C194.33 78.575 194.19 78.725 194.03 78.845C193.89 78.955 193.73 79.045 193.6 79.175C193.45 79.325 193.34 79.515 193.21 79.685C193.11 79.815 192.99 79.935 192.91 80.075C192.79 80.285 192.71 80.525 192.63 80.755C192.58 80.875 192.51 80.995 192.48 81.125C192.38 81.495 192.33 81.875 192.33 82.265V139.625L148.62 164.795V52.575C148.62 52.185 148.57 51.805 148.47 51.435C148.44 51.305 148.36 51.195 148.32 51.065C148.23 50.835 148.16 50.595 148.04 50.385C147.96 50.245 147.84 50.125 147.74 49.995C147.61 49.825 147.5 49.635 147.35 49.485C147.22 49.355 147.06 49.265 146.92 49.155C146.76 49.025 146.62 48.885 146.44 48.785L93.99 18.585C92.64 17.805 90.98 17.805 89.63 18.585L37.18 48.785C37 48.885 36.86 49.035 36.7 49.155C36.56 49.265 36.4 49.355 36.27 49.485C36.12 49.635 36.01 49.825 35.88 49.995C35.78 50.125 35.66 50.245 35.58 50.385C35.46 50.595 35.38 50.835 35.3 51.065C35.25 51.185 35.18 51.305 35.15 51.435C35.05 51.805 35 52.185 35 52.575V232.235C35 233.795 35.84 235.245 37.19 236.025L142.1 296.425C142.33 296.555 142.58 296.635 142.82 296.725C142.93 296.765 143.04 296.835 143.16 296.865C143.53 296.965 143.9 297.015 144.28 297.015C144.66 297.015 145.03 296.965 145.4 296.865C145.5 296.835 145.59 296.775 145.69 296.745C145.95 296.655 146.21 296.565 146.45 296.435L251.36 236.035C252.72 235.255 253.55 233.815 253.55 232.245V174.885L303.81 145.945C305.17 145.165 306 143.725 306 142.155V82.265C305.95 81.875 305.89 81.495 305.8 81.125ZM144.2 227.205L100.57 202.515L146.39 176.135L196.66 147.195L240.33 172.335L208.29 190.625L144.2 227.205ZM244.75 114.995V164.795L226.39 154.225L201.03 139.625V89.825L219.39 100.395L244.75 114.995ZM249.12 57.105L292.81 82.265L249.12 107.425L205.43 82.265L249.12 57.105ZM114.49 184.425L96.13 194.995V85.305L121.49 70.705L139.85 60.135V169.815L114.49 184.425ZM91.76 27.425L135.45 52.585L91.76 77.745L48.07 52.585L91.76 27.425ZM43.67 60.135L62.03 70.705L87.39 85.305V202.545L43.67 227.205V60.135ZM244.75 229.085L201.03 254.245V196.885L244.75 171.725V229.085Z"/>
                </svg>
            </div>
        </div>
        <div class="qr-modal-title">{{ $streamer->display_name }}</div>
        <div class="qr-modal-subtitle">Scan untuk donasi</div>
    </div>
</div>

<div class="bg"></div>
<div class="bg-grid"></div>

<div class="page">

    <!-- LEFT: Streamer Info -->
    <section class="streamer-side">

        <!-- Profile + QR Row -->
        <div class="profile-qr-row">
            <!-- Profile Card -->
            <div class="profile-card">
                <div class="profile-header">
                    <div class="avatar">
                        @if($streamer->avatar)
                            <img src="{{ asset('storage/' . $streamer->avatar) }}" alt="{{ $streamer->display_name }}" />
                        @else
                            {{ strtoupper(substr($streamer->display_name, 0, 2)) }}
                        @endif
                    </div>
                    <div>
                        <h1 class="profile-name">{{ $streamer->display_name }}</h1>
                        @if($streamer->bio)
                            <p class="profile-bio">{{ $streamer->bio }}</p>
                        @endif
                    </div>
                </div>
                <div class="profile-info">
                    <div class="profile-info-tags">
                        @if($streamer->yt_enabled)
                        <span class="tag youtube">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/></svg>
                            YouTube
                        </span>
                        @endif
                        <span class="tag tiktok">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/></svg>
                            TikTok
                        </span>
                        <span class="tag twitch">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 2H3v16h5v4l4-4h5l4-4V2zm-10 9V7m5 4V7"/></svg>
                            Twitch
                        </span>
                        <span class="tag game">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><line x1="6" y1="12" x2="10" y2="12"/><line x1="8" y1="10" x2="8" y2="14"/><circle cx="17" cy="12" r="1"/></svg>
                            Mobile Legends
                        </span>
                        <span class="tag game">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><line x1="6" y1="12" x2="10" y2="12"/><line x1="8" y1="10" x2="8" y2="14"/><circle cx="17" cy="12" r="1"/></svg>
                            Genshin Impact
                        </span>
                        <span class="tag game">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><line x1="6" y1="12" x2="10" y2="12"/><line x1="8" y1="10" x2="8" y2="14"/><circle cx="17" cy="12" r="1"/></svg>
                            Valorant
                        </span>
                    </div>
                </div>
            </div>

            <!-- QR Code -->
            <div class="qr-card" onclick="openQRModal()">
                <div class="qr-wrap">
                    <img src="{{ route('qr.show', $streamer->slug) }}" alt="QR Code" />
                    <div class="qr-logo">
                        <svg viewBox="0 0 316 316" xmlns="http://www.w3.org/2000/svg">
                            <path fill="#FF2D20" d="M305.8 81.125C305.77 80.995 305.69 80.885 305.65 80.755C305.56 80.525 305.49 80.285 305.37 80.075C305.29 79.935 305.17 79.815 305.07 79.685C304.94 79.515 304.83 79.325 304.68 79.175C304.55 79.045 304.39 78.955 304.25 78.845C304.09 78.715 303.95 78.575 303.77 78.475L251.32 48.275C249.97 47.495 248.31 47.495 246.96 48.275L194.51 78.475C194.33 78.575 194.19 78.725 194.03 78.845C193.89 78.955 193.73 79.045 193.6 79.175C193.45 79.325 193.34 79.515 193.21 79.685C193.11 79.815 192.99 79.935 192.91 80.075C192.79 80.285 192.71 80.525 192.63 80.755C192.58 80.875 192.51 80.995 192.48 81.125C192.38 81.495 192.33 81.875 192.33 82.265V139.625L148.62 164.795V52.575C148.62 52.185 148.57 51.805 148.47 51.435C148.44 51.305 148.36 51.195 148.32 51.065C148.23 50.835 148.16 50.595 148.04 50.385C147.96 50.245 147.84 50.125 147.74 49.995C147.61 49.825 147.5 49.635 147.35 49.485C147.22 49.355 147.06 49.265 146.92 49.155C146.76 49.025 146.62 48.885 146.44 48.785L93.99 18.585C92.64 17.805 90.98 17.805 89.63 18.585L37.18 48.785C37 48.885 36.86 49.035 36.7 49.155C36.56 49.265 36.4 49.355 36.27 49.485C36.12 49.635 36.01 49.825 35.88 49.995C35.78 50.125 35.66 50.245 35.58 50.385C35.46 50.595 35.38 50.835 35.3 51.065C35.25 51.185 35.18 51.305 35.15 51.435C35.05 51.805 35 52.185 35 52.575V232.235C35 233.795 35.84 235.245 37.19 236.025L142.1 296.425C142.33 296.555 142.58 296.635 142.82 296.725C142.93 296.765 143.04 296.835 143.16 296.865C143.53 296.965 143.9 297.015 144.28 297.015C144.66 297.015 145.03 296.965 145.4 296.865C145.5 296.835 145.59 296.775 145.69 296.745C145.95 296.655 146.21 296.565 146.45 296.435L251.36 236.035C252.72 235.255 253.55 233.815 253.55 232.245V174.885L303.81 145.945C305.17 145.165 306 143.725 306 142.155V82.265C305.95 81.875 305.89 81.495 305.8 81.125ZM144.2 227.205L100.57 202.515L146.39 176.135L196.66 147.195L240.33 172.335L208.29 190.625L144.2 227.205ZM244.75 114.995V164.795L226.39 154.225L201.03 139.625V89.825L219.39 100.395L244.75 114.995ZM249.12 57.105L292.81 82.265L249.12 107.425L205.43 82.265L249.12 57.105ZM114.49 184.425L96.13 194.995V85.305L121.49 70.705L139.85 60.135V169.815L114.49 184.425ZM91.76 27.425L135.45 52.585L91.76 77.745L48.07 52.585L91.76 27.425ZM43.67 60.135L62.03 70.705L87.39 85.305V202.545L43.67 227.205V60.135ZM244.75 229.085L201.03 254.245V196.885L244.75 171.725V229.085Z"/>
                        </svg>
                    </div>
                </div>
                <div class="qr-label">Scan QR</div>
            </div>
        </div>

        <!-- Row 1: Stats -->
        <div class="info-row">
            <!-- Jam Tayang -->
            <div class="info-card">
                <div class="info-header">
                    <div class="info-icon purple">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <span class="info-title">Jam Tayang</span>
                </div>
                <div class="info-stat">1,240</div>
                <div class="info-stat-label">Total jam streaming</div>
            </div>

            <!-- Pengikut -->
            <div class="info-card">
                <div class="info-header">
                    <div class="info-icon green">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <span class="info-title">Pengikut</span>
                </div>
                <div class="info-stat">85.2K</div>
                <div class="info-stat-label">Total followers</div>
            </div>
        </div>

        <!-- Row 2: Multi Milestone -->
        @if($milestones->count() > 0)
        <div class="milestone-card">
            <div class="milestone-header">
                <div class="milestone-header-left">
                    <div class="info-icon pink">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </div>
                    <span class="info-title">Target Milestone</span>
                </div>
                <div class="milestone-nav">
                    <button type="button" class="milestone-nav-btn" onclick="slideMilestone(-1)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    </button>
                    <button type="button" class="milestone-nav-btn" onclick="slideMilestone(1)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>
            </div>
            <div class="milestone-slider">
                <div class="milestone-track" id="milestone-track">
                    @foreach($milestones as $milestone)
                    <div class="milestone-item">
                        <div class="milestone-title">{{ $milestone->title }}</div>
                        <div class="milestone-progress">
                            <span class="milestone-percent">{{ $milestone->progress_percentage }}%</span>
                            <div class="milestone-bar">
                                <div class="milestone-bar-fill {{ $milestone->color }}" style="width:{{ $milestone->progress_percentage }}%"></div>
                            </div>
                        </div>
                        <div class="milestone-info">
                            <span>Rp {{ number_format($milestone->current_amount, 0, ',', '.') }} terkumpul</span>
                            <span>Target: Rp {{ number_format($milestone->target_amount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="milestone-dots" id="milestone-dots">
                @foreach($milestones as $index => $milestone)
                <span class="milestone-dot{{ $index === 0 ? ' active' : '' }}" onclick="goToMilestone({{ $index }})"></span>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Row 3: Social -->
        <div class="info-row">
            <!-- Media Sosial -->
            <div class="social-card">
                <div class="social-title">Ikuti Sosial Media</div>
                <div class="social-links">
                    <a href="#" class="social-link youtube" target="_blank">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/></svg>
                        YouTube
                    </a>
                    <a href="#" class="social-link tiktok" target="_blank">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/></svg>
                        TikTok
                    </a>
                    <a href="#" class="social-link instagram" target="_blank">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                        Instagram
                    </a>
                    <a href="#" class="social-link twitter" target="_blank">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
                        Twitter
                    </a>
                    <a href="#" class="social-link discord" target="_blank">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028c.462-.63.874-1.295 1.226-1.994a.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/></svg>
                        Discord
                    </a>
                </div>
            </div>
        </div>

        <!-- Row 5: Recent & Top Donatur -->
        <div class="info-row">
            <!-- Recent Donations -->
            <div class="info-card">
                <div class="info-header">
                    <div class="info-icon purple">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <span class="info-title">Donasi Terbaru</span>
                </div>
                <div class="info-list">
                    <div class="info-item">
                        <span class="recent-emoji">💝</span>
                        <span>GamerPro123</span>
                        <strong style="margin-left:auto">Rp 50K</strong>
                    </div>
                    <div class="info-item">
                        <span class="recent-emoji">🔥</span>
                        <span>Anonymous</span>
                        <strong style="margin-left:auto">Rp 25K</strong>
                    </div>
                    <div class="info-item">
                        <span class="recent-emoji">❤️</span>
                        <span>SiPalingGG</span>
                        <strong style="margin-left:auto">Rp 100K</strong>
                    </div>
                </div>
            </div>

            <!-- Top Donatur -->
            <div class="info-card">
                <div class="info-header">
                    <div class="info-icon orange">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    </div>
                    <span class="info-title">Top Donatur</span>
                </div>
                <div class="info-list">
                    <div class="info-item">
                        <span class="rank-badge gold">1</span>
                        <span>RajaGaming</span>
                        <strong style="margin-left:auto">Rp 2.5Jt</strong>
                    </div>
                    <div class="info-item">
                        <span class="rank-badge silver">2</span>
                        <span>ThunderX</span>
                        <strong style="margin-left:auto">Rp 1.8Jt</strong>
                    </div>
                    <div class="info-item">
                        <span class="rank-badge bronze">3</span>
                        <span>NightOwl99</span>
                        <strong style="margin-left:auto">Rp 1.2Jt</strong>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <!-- RIGHT: Form -->
    <section class="form-side">
        <div class="form-card">
            <!-- Mobile Streamer Header (Only visible on mobile) -->
            <div class="mobile-streamer-header">
                <div class="mobile-streamer-avatar">
                    @if($streamer->avatar)
                        <img src="{{ asset('storage/' . $streamer->avatar) }}" alt="{{ $streamer->display_name }}" />
                    @else
                        {{ strtoupper(substr($streamer->display_name, 0, 2)) }}
                    @endif
                </div>
                <div class="mobile-streamer-info">
                    <div class="mobile-streamer-name-row">
                        <div class="mobile-streamer-name">{{ $streamer->display_name }}</div>
                        <div class="mobile-online-status"></div>
                    </div>
                    @if($streamer->bio)
                        <div class="mobile-streamer-bio">{{ $streamer->bio }}</div>
                    @endif
                    <div class="mobile-streamer-tags">
                        <span class="mobile-tag youtube">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                            YouTube
                        </span>
                        <span class="mobile-tag tiktok">
                            <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>
                            TikTok
                        </span>
                        <span class="mobile-tag twitch">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 2H3v16h5v4l4-4h5l4-4V2zm-10 9V7m5 4V7"/></svg>
                            Twitch
                        </span>
                        <span class="mobile-tag game">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><line x1="6" y1="12" x2="10" y2="12"/><line x1="8" y1="10" x2="8" y2="14"/><circle cx="17" cy="12" r="1"/></svg>
                            ML
                        </span>
                        <span class="mobile-tag game">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><line x1="6" y1="12" x2="10" y2="12"/><line x1="8" y1="10" x2="8" y2="14"/><circle cx="17" cy="12" r="1"/></svg>
                            Genshin
                        </span>
                        <span class="mobile-tag game">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><line x1="6" y1="12" x2="10" y2="12"/><line x1="8" y1="10" x2="8" y2="14"/><circle cx="17" cy="12" r="1"/></svg>
                            Valorant
                        </span>
                    </div>
                </div>
            </div>

            <div id="conn-status" class="wait"><span class="conn-dot"></span> Memuat…</div>

            <div class="form-header">
                <h2 class="form-title">Kirim Donasi</h2>
                <p class="form-sub">Support <strong>{{ $streamer->display_name }}</strong></p>
            </div>

            <div id="error-box" class="error-box"></div>

            <div id="donate-form">
                <!-- Amount -->
                <div class="field">
                    <div class="field-label">Pilih Nominal</div>
                    <div class="amount-presets">
                        <button type="button" class="preset-btn" onclick="setPreset(this, 5000)">5K</button>
                        <button type="button" class="preset-btn" onclick="setPreset(this, 10000)">10K</button>
                        <button type="button" class="preset-btn active" onclick="setPreset(this, 25000)">25K</button>
                        <button type="button" class="preset-btn" onclick="setPreset(this, 50000)">50K</button>
                        <button type="button" class="preset-btn" onclick="setPreset(this, 100000)">100K</button>
                        <button type="button" class="preset-btn" onclick="setPreset(this, 200000)">200K</button>
                    </div>
                    <div class="amount-wrap">
                        <span class="amount-prefix">Rp</span>
                        <input type="text" id="donor-amount-display" class="amount-input" value="25.000" inputmode="numeric" />
                        <input type="hidden" id="donor-amount" value="25000" />
                    </div>
                </div>

                <!-- Milestone Selection (if any) -->
                @if($milestones->count() > 0)
                <div class="field">
                    <div class="milestone-selection">
                        <div class="milestone-selection-header">
                            <div class="milestone-selection-header-left">
                                <div class="milestone-selection-title">Pilih Milestone</div>
                                <div class="milestone-selection-optional">Opsional</div>
                            </div>
                            <div class="milestone-selection-nav">
                                <button type="button" class="milestone-selection-nav-btn" onclick="slideFormMilestone(-1)">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="15 18 9 12 15 6"/>
                                    </svg>
                                </button>
                                <button type="button" class="milestone-selection-nav-btn" onclick="slideFormMilestone(1)">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <polyline points="9 18 15 12 9 6"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="milestone-selection-slider">
                            <div class="milestone-selection-track" id="form-milestone-track">
                                <!-- First item: No milestone -->
                                <div class="milestone-selection-item">
                                    <div class="milestone-option selected" onclick="selectFormMilestone(null, 0)">
                                        <div class="milestone-option-top">
                                            <div>
                                                <div class="milestone-option-title">Tidak Pilih Milestone</div>
                                            </div>
                                            <div class="milestone-option-check">
                                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <!-- Empty progress placeholder for consistent height -->
                                        <div class="milestone-option-progress" style="visibility:hidden">
                                            <div class="milestone-option-progress-row">
                                                <div class="milestone-option-percent">0%</div>
                                                <div class="milestone-option-bar">
                                                    <div class="milestone-option-bar-fill"></div>
                                                </div>
                                            </div>
                                            <div class="milestone-option-amount">-</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Milestone items -->
                                @foreach($milestones as $index => $milestone)
                                <div class="milestone-selection-item">
                                    <div class="milestone-option" data-milestone-id="{{ $milestone->id }}" onclick="selectFormMilestone({{ $milestone->id }}, {{ $index + 1 }})">
                                        <div class="milestone-option-top">
                                            <div>
                                                <div class="milestone-option-title">{{ $milestone->title }}</div>
                                            </div>
                                            <div class="milestone-option-check">
                                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="milestone-option-progress">
                                            <div class="milestone-option-progress-row">
                                                <div class="milestone-option-percent">{{ $milestone->progress_percentage }}%</div>
                                                <div class="milestone-option-bar">
                                                    <div class="milestone-option-bar-fill {{ $milestone->color }}" style="width:{{ $milestone->progress_percentage }}%"></div>
                                                </div>
                                            </div>
                                            <div class="milestone-option-amount">
                                                Rp {{ number_format($milestone->current_amount, 0, ',', '.') }} / Rp {{ number_format($milestone->target_amount, 0, ',', '.') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Dots indicator -->
                        <div class="milestone-selection-dots" id="form-milestone-dots">
                            <span class="milestone-selection-dot active" onclick="goToFormMilestone(0)"></span>
                            @foreach($milestones as $index => $milestone)
                            <span class="milestone-selection-dot" onclick="goToFormMilestone({{ $index + 1 }})"></span>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Name -->
                <div class="field">
                    <div class="field-header">
                        <div class="field-label">Nama <span style="color:#ef4444">*</span></div>
                        <span class="char-counter" id="name-counter">0/20</span>
                    </div>
                    <input type="text" id="donor-name" class="text-input" placeholder="Nama atau username kamu" maxlength="20" required oninput="updateCounter(this, 'name-counter', 20)" />
                </div>

                <!-- Message -->
                <div class="field">
                    <div class="field-header">
                        <div class="field-label">Pesan</div>
                        <span class="char-counter" id="msg-counter">0/200</span>
                    </div>
                    <textarea id="donor-msg" class="text-input" placeholder="Tulis pesan untuk streamer..." maxlength="200" oninput="updateCounter(this, 'msg-counter', 200)"></textarea>
                </div>

                <!-- Media Request -->
                <div class="media-card">
                    <div class="media-tabs-container">
                        <button type="button" class="media-nav prev" onclick="scrollMedia(-1)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                        </button>
                        <div class="media-tabs" id="media-tabs">
                            @if($streamer->yt_enabled)
                            <button type="button" class="media-tab youtube active" data-type="youtube" onclick="switchMedia(this)">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/></svg>
                                YouTube
                            </button>
                            @endif
                            <button type="button" class="media-tab tiktok" data-type="tiktok" onclick="switchMedia(this)">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5"/></svg>
                                TikTok
                            </button>
                            <button type="button" class="media-tab instagram" data-type="instagram" onclick="switchMedia(this)">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                                Instagram
                            </button>
                            <button type="button" class="media-tab twitter" data-type="twitter" onclick="switchMedia(this)">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
                                Twitter
                            </button>
                            <button type="button" class="media-tab spotify" data-type="spotify" onclick="switchMedia(this)">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 15c2-1 4-1 6 0"/><path d="M7 12c2.5-1.5 6.5-1.5 9 0"/><path d="M6 9c3-2 8-2 11 0"/></svg>
                                Spotify
                            </button>
                            @if($mediaUploadEnabled)
                            <button type="button" class="media-tab upload" data-type="upload" onclick="switchMedia(this)">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                Upload
                            </button>
                            @endif
                        </div>
                        <button type="button" class="media-nav next" onclick="scrollMedia(1)">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
                    </div>
                    @if($streamer->yt_enabled)
                    <div class="media-input-wrap active" data-type="youtube">
                        <input type="text" id="media-youtube" class="media-input" placeholder="https://youtube.com/watch?v=..." />
                        <div class="media-hint">Request lagu atau video dari YouTube</div>
                    </div>
                    @endif
                    <div class="media-input-wrap" data-type="tiktok">
                        <input type="text" id="media-tiktok" class="media-input" placeholder="https://tiktok.com/@user/video/..." />
                        <div class="media-hint">Request video TikTok</div>
                    </div>
                    <div class="media-input-wrap" data-type="instagram">
                        <input type="text" id="media-instagram" class="media-input" placeholder="https://instagram.com/p/... atau /reel/..." />
                        <div class="media-hint">Request Reels atau post Instagram</div>
                    </div>
                    <div class="media-input-wrap" data-type="twitter">
                        <input type="text" id="media-twitter" class="media-input" placeholder="https://twitter.com/user/status/..." />
                        <div class="media-hint">Request video dari Twitter/X</div>
                    </div>
                    <div class="media-input-wrap" data-type="spotify">
                        <input type="text" id="media-spotify" class="media-input" placeholder="https://open.spotify.com/track/..." />
                        <div class="media-hint">Request lagu dari Spotify</div>
                    </div>
                    @if($mediaUploadEnabled)
                    <div class="media-input-wrap" data-type="upload">
                        <div class="file-upload-wrap">
                            <input type="file" id="media-upload" class="file-upload-input" accept="video/*,audio/*" onchange="updateFileName(this)" />
                            <div class="file-upload-btn" id="file-upload-btn">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="17 8 12 3 7 8"/>
                                    <line x1="12" y1="3" x2="12" y2="15"/>
                                </svg>
                                <span id="file-upload-text">Pilih File</span>
                            </div>
                        </div>
                        <div class="media-hint">
                            <span id="media-duration-info">Maks durasi: <strong id="max-duration-text">0 detik</strong></span>
                            <span class="media-hint-sep">•</span>
                            <span>Maks {{ $mediaMaxSizeMb }}MB</span>
                        </div>
                        <div class="media-duration-note" id="media-duration-note" style="display:none">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width:12px;height:12px">
                                <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <span id="duration-note-text"></span>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Submit -->
                <button class="submit-btn" id="submit-btn" onclick="submitDonation()">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                    Kirim Donasi
                </button>
            </div>

            <!-- Success -->
            <div id="success-box" class="success-box">
                <div class="success-emoji" id="ty-emoji">🎉</div>
                <h3 class="success-title">Donasi Terkirim!</h3>
                <div class="success-msg" id="ty-msg"></div>
                <div class="success-countdown" id="ty-countdown"></div>
                <button class="submit-btn" onclick="resetForm()">Donasi Lagi</button>
            </div>

            <!-- Mobile QR + Social Section (Only visible on mobile) -->
            <div class="mobile-qr-section">
                <div class="mobile-qr-grid">
                    <!-- Left: QR Code -->
                    <div class="mobile-qr-col">
                        <div class="mobile-qr-wrap" onclick="openQRModal()">
                            <img src="{{ route('qr.show', $streamer->slug) }}" alt="QR Code" />
                            <div class="mobile-qr-logo">
                                <svg viewBox="0 0 316 316" xmlns="http://www.w3.org/2000/svg">
                                    <path fill="#FF2D20" d="M305.8 81.125C305.77 80.995 305.69 80.885 305.65 80.755C305.56 80.525 305.49 80.285 305.37 80.075C305.29 79.935 305.17 79.815 305.07 79.685C304.94 79.515 304.83 79.325 304.68 79.175C304.55 79.045 304.39 78.955 304.25 78.845C304.09 78.715 303.95 78.575 303.77 78.475L251.32 48.275C249.97 47.495 248.31 47.495 246.96 48.275L194.51 78.475C194.33 78.575 194.19 78.725 194.03 78.845C193.89 78.955 193.73 79.045 193.6 79.175C193.45 79.325 193.34 79.515 193.21 79.685C193.11 79.815 192.99 79.935 192.91 80.075C192.79 80.285 192.71 80.525 192.63 80.755C192.58 80.875 192.51 80.995 192.48 81.125C192.38 81.495 192.33 81.875 192.33 82.265V139.625L148.62 164.795V52.575C148.62 52.185 148.57 51.805 148.47 51.435C148.44 51.305 148.36 51.195 148.32 51.065C148.23 50.835 148.16 50.595 148.04 50.385C147.96 50.245 147.84 50.125 147.74 49.995C147.61 49.825 147.5 49.635 147.35 49.485C147.22 49.355 147.06 49.265 146.92 49.155C146.76 49.025 146.62 48.885 146.44 48.785L93.99 18.585C92.64 17.805 90.98 17.805 89.63 18.585L37.18 48.785C37 48.885 36.86 49.035 36.7 49.155C36.56 49.265 36.4 49.355 36.27 49.485C36.12 49.635 36.01 49.825 35.88 49.995C35.78 50.125 35.66 50.245 35.58 50.385C35.46 50.595 35.38 50.835 35.3 51.065C35.25 51.185 35.18 51.305 35.15 51.435C35.05 51.805 35 52.185 35 52.575V232.235C35 233.795 35.84 235.245 37.19 236.025L142.1 296.425C142.33 296.555 142.58 296.635 142.82 296.725C142.93 296.765 143.04 296.835 143.16 296.865C143.53 296.965 143.9 297.015 144.28 297.015C144.66 297.015 145.03 296.965 145.4 296.865C145.5 296.835 145.59 296.775 145.69 296.745C145.95 296.655 146.21 296.565 146.45 296.435L251.36 236.035C252.72 235.255 253.55 233.815 253.55 232.245V174.885L303.81 145.945C305.17 145.165 306 143.725 306 142.155V82.265C305.95 81.875 305.89 81.495 305.8 81.125ZM144.2 227.205L100.57 202.515L146.39 176.135L196.66 147.195L240.33 172.335L208.29 190.625L144.2 227.205ZM244.75 114.995V164.795L226.39 154.225L201.03 139.625V89.825L219.39 100.395L244.75 114.995ZM249.12 57.105L292.81 82.265L249.12 107.425L205.43 82.265L249.12 57.105ZM114.49 184.425L96.13 194.995V85.305L121.49 70.705L139.85 60.135V169.815L114.49 184.425ZM91.76 27.425L135.45 52.585L91.76 77.745L48.07 52.585L91.76 27.425ZM43.67 60.135L62.03 70.705L87.39 85.305V202.545L43.67 227.205V60.135ZM244.75 229.085L201.03 254.245V196.885L244.75 171.725V229.085Z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="mobile-qr-label">Tap QR</div>
                    </div>
                    
                    <!-- Right: Social Media -->
                    <div class="mobile-social-col">
                        <div class="mobile-social-label">Ikuti Juga</div>
                        <div class="mobile-social-links">
                            <a href="https://youtube.com/@streamer" target="_blank" class="mobile-social-link youtube">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                            </a>
                            <a href="https://tiktok.com/@streamer" target="_blank" class="mobile-social-link tiktok">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z"/></svg>
                            </a>
                            <a href="https://instagram.com/streamer" target="_blank" class="mobile-social-link instagram">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                            </a>
                            <a href="https://twitter.com/streamer" target="_blank" class="mobile-social-link twitter">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                            </a>
                            <a href="https://discord.gg/streamer" target="_blank" class="mobile-social-link discord">
                                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M20.317 4.492c-1.53-.69-3.17-1.2-4.885-1.49a.075.075 0 0 0-.079.036c-.21.369-.444.85-.608 1.23a18.566 18.566 0 0 0-5.487 0 12.36 12.36 0 0 0-.617-1.23A.077.077 0 0 0 8.562 3c-1.714.29-3.354.8-4.885 1.491a.07.07 0 0 0-.032.027C.533 9.093-.32 13.555.099 17.961a.08.08 0 0 0 .031.055 20.03 20.03 0 0 0 5.993 2.98.078.078 0 0 0 .084-.026 13.83 13.83 0 0 0 1.226-1.963.074.074 0 0 0-.041-.104 13.201 13.201 0 0 1-1.872-.878.075.075 0 0 1-.008-.125c.126-.093.252-.19.372-.287a.075.075 0 0 1 .078-.01c3.927 1.764 8.18 1.764 12.061 0a.075.075 0 0 1 .079.009c.12.098.245.195.372.288a.075.075 0 0 1-.006.125c-.598.344-1.22.635-1.873.877a.075.075 0 0 0-.041.105c.36.687.772 1.341 1.225 1.962a.077.077 0 0 0 .084.028 19.963 19.963 0 0 0 6.002-2.981.076.076 0 0 0 .032-.054c.5-5.094-.838-9.52-3.549-13.442a.06.06 0 0 0-.031-.028zM8.02 15.278c-1.182 0-2.157-1.069-2.157-2.38 0-1.312.956-2.38 2.157-2.38 1.21 0 2.176 1.077 2.157 2.38 0 1.312-.956 2.38-2.157 2.38zm7.975 0c-1.183 0-2.157-1.069-2.157-2.38 0-1.312.955-2.38 2.157-2.38 1.21 0 2.176 1.077 2.157 2.38 0 1.312-.946 2.38-2.157 2.38z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-footer">
                Powered by <a href="{{ route('home') }}">Tiptipan</a>
            </div>
        </div>
    </section>
</div>

<script>
const SLUG = '{{ $streamer->slug }}';
const SSE_URL = '/{{ $streamer->slug }}/sse?key={{ $streamer->api_key }}';
const STORE_URL = '/{{ $streamer->slug }}/donate';
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
const MIN_AMT = {{ $streamer->min_donation }};

// Amount presets
function setPreset(btn, val) {
    document.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('donor-amount').value = val;
    document.getElementById('donor-amount-display').value = val.toLocaleString('id-ID');
    updateMediaDurationInfo();
}

// QR Modal
function openQRModal() {
    document.getElementById('qrModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeQRModal(event) {
    if (!event || event.target.id === 'qrModal' || event.target.closest('.qr-modal-close')) {
        document.getElementById('qrModal').classList.remove('active');
        document.body.style.overflow = '';
    }
}

// Form Milestone selection slider
let currentFormMilestoneIndex = 0;
let formMilestones = [];
let selectedMilestoneId = null;

// Initialize form milestone slider
document.addEventListener('DOMContentLoaded', function() {
    const track = document.getElementById('form-milestone-track');
    if (track) {
        formMilestones = track.querySelectorAll('.milestone-selection-item');
    }
});

function slideFormMilestone(direction) {
    if (formMilestones.length === 0) return;
    
    currentFormMilestoneIndex += direction;
    if (currentFormMilestoneIndex < 0) currentFormMilestoneIndex = formMilestones.length - 1;
    if (currentFormMilestoneIndex >= formMilestones.length) currentFormMilestoneIndex = 0;
    
    updateFormMilestoneSlider();
}

function goToFormMilestone(index) {
    currentFormMilestoneIndex = index;
    updateFormMilestoneSlider();
}

function updateFormMilestoneSlider() {
    const track = document.getElementById('form-milestone-track');
    const dots = document.querySelectorAll('.milestone-selection-dot');
    
    if (track) {
        track.style.transform = `translateX(-${currentFormMilestoneIndex * 100}%)`;
    }
    
    dots.forEach((dot, i) => {
        dot.classList.toggle('active', i === currentFormMilestoneIndex);
    });
}

function selectFormMilestone(milestoneId, slideIndex) {
    // Update selected milestone ID
    selectedMilestoneId = milestoneId;
    
    // Remove selected class from all options
    document.querySelectorAll('.milestone-option').forEach(el => {
        el.classList.remove('selected');
    });
    
    // Add selected class to current slide's option
    const currentItem = formMilestones[slideIndex];
    if (currentItem) {
        const option = currentItem.querySelector('.milestone-option');
        if (option) {
            option.classList.add('selected');
        }
    }
}

// Close modal with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeQRModal();
});

// Character counter
function updateCounter(input, counterId, max) {
    const counter = document.getElementById(counterId);
    const len = input.value.length;
    counter.textContent = len + '/' + max;
    counter.classList.remove('warn', 'limit');
    if (len >= max) {
        counter.classList.add('limit');
    } else if (len >= max * 0.8) {
        counter.classList.add('warn');
    }
}

// Media switcher
function switchMedia(btn) {
    const type = btn.dataset.type;
    document.querySelectorAll('.media-tab').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.media-input-wrap').forEach(w => w.classList.remove('active'));
    document.querySelector('.media-input-wrap[data-type="' + type + '"]')?.classList.add('active');
}

// Media duration tiers
const mediaDurationTiers = @json($mediaDurationTiers);
const mediaMaxSizeMb = {{ $mediaMaxSizeMb }};

function getMaxDurationForAmount(amount) {
    let maxDuration = 0;
    const sortedTiers = [...mediaDurationTiers].sort((a, b) => a.min_amount - b.min_amount);
    
    for (const tier of sortedTiers) {
        if (amount >= tier.min_amount) {
            maxDuration = tier.max_duration;
        }
    }
    return maxDuration;
}

function formatDuration(seconds) {
    if (seconds >= 60) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return secs > 0 ? `${mins} menit ${secs} detik` : `${mins} menit`;
    }
    return `${seconds} detik`;
}

function updateMediaDurationInfo() {
    const amountInput = document.getElementById('donor-amount');
    const amount = parseInt(amountInput.value) || 0;
    const maxDuration = getMaxDurationForAmount(amount);
    
    const durationText = document.getElementById('max-duration-text');
    const noteEl = document.getElementById('media-duration-note');
    const noteText = document.getElementById('duration-note-text');
    
    if (durationText) {
        if (maxDuration > 0) {
            durationText.textContent = formatDuration(maxDuration);
        } else {
            durationText.textContent = 'tidak tersedia';
        }
    }
    
    if (noteEl && noteText) {
        if (maxDuration === 0 && mediaDurationTiers.length > 0) {
            const minTier = mediaDurationTiers.reduce((min, t) => t.min_amount < min.min_amount ? t : min);
            noteEl.style.display = 'flex';
            noteEl.className = 'media-duration-note warning';
            noteText.textContent = `Donasi minimal Rp ${minTier.min_amount.toLocaleString('id-ID')} untuk upload media (${formatDuration(minTier.max_duration)})`;
        } else if (maxDuration > 0) {
            // Find next tier for upgrade hint
            const sortedTiers = [...mediaDurationTiers].sort((a, b) => a.min_amount - b.min_amount);
            const nextTier = sortedTiers.find(t => t.min_amount > amount);
            
            if (nextTier) {
                noteEl.style.display = 'flex';
                noteEl.className = 'media-duration-note';
                noteText.textContent = `Donasi Rp ${nextTier.min_amount.toLocaleString('id-ID')}+ untuk durasi ${formatDuration(nextTier.max_duration)}`;
            } else {
                noteEl.style.display = 'none';
            }
        } else {
            noteEl.style.display = 'none';
        }
    }
}

// Media tabs slider navigation
function scrollMedia(direction) {
    const tabs = document.getElementById('media-tabs');
    const scrollAmount = 150; // ~2 tabs width
    tabs.scrollBy({ left: direction * scrollAmount, behavior: 'smooth' });
}

// Milestone slider
let currentMilestone = 0;
const totalMilestones = {{ $milestones->count() }};

function slideMilestone(direction) {
    if (totalMilestones === 0) return;
    currentMilestone += direction;
    if (currentMilestone < 0) currentMilestone = totalMilestones - 1;
    if (currentMilestone >= totalMilestones) currentMilestone = 0;
    updateMilestoneSlider();
}

function goToMilestone(index) {
    if (totalMilestones === 0) return;
    currentMilestone = index;
    updateMilestoneSlider();
}

function updateMilestoneSlider() {
    const track = document.getElementById('milestone-track');
    const dots = document.querySelectorAll('.milestone-dot');
    track.style.transform = `translateX(-${currentMilestone * 100}%)`;
    dots.forEach((dot, i) => {
        dot.classList.toggle('active', i === currentMilestone);
    });
}

// Amount formatter
document.addEventListener('DOMContentLoaded', function () {
    const display = document.getElementById('donor-amount-display');
    const hidden = document.getElementById('donor-amount');
    display.addEventListener('input', function () {
        const raw = this.value.replace(/\D/g, '');
        const num = parseInt(raw, 10) || 0;
        this.value = num ? num.toLocaleString('id-ID') : '';
        hidden.value = num;
        document.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('active'));
        updateMediaDurationInfo();
    });
    
    // Initial update
    updateMediaDurationInfo();
});

// SSE
let currentEventSource = null;
function connectSSE() {
    const connEl = document.getElementById('conn-status');
    const mobileStatus = document.querySelector('.mobile-online-status');
    
    if (currentEventSource) currentEventSource.close();
    currentEventSource = new EventSource(SSE_URL);
    
    currentEventSource.onopen = () => {
        if (connEl) {
            connEl.className = 'ok';
            connEl.innerHTML = '<span class="conn-dot"></span> Terhubung';
        }
        // Update mobile status to green (online)
        if (mobileStatus) {
            mobileStatus.style.background = '#22c55e';
            mobileStatus.style.boxShadow = '0 0 0 2px rgba(34,197,94,0.2)';
        }
    };
    
    currentEventSource.onerror = () => {
        if (connEl) {
            connEl.className = 'err';
            connEl.innerHTML = '<span class="conn-dot"></span> Reconnecting…';
        }
        // Update mobile status to red (offline)
        if (mobileStatus) {
            mobileStatus.style.background = '#ef4444';
            mobileStatus.style.boxShadow = '0 0 0 2px rgba(239,68,68,0.2)';
        }
        currentEventSource.close();
        setTimeout(connectSSE, 4000);
    };
}
connectSSE();

// Submit
async function submitDonation() {
    const btn = document.getElementById('submit-btn');
    const errBox = document.getElementById('error-box');
    const amount = parseInt(document.getElementById('donor-amount').value) || 0;
    const name = document.getElementById('donor-name').value.trim();
    const msg = document.getElementById('donor-msg').value.trim();
    const emoji = document.getElementById('selected-emoji').value;
    const fileInput = document.getElementById('media-upload');

    errBox.classList.remove('show');
    if (!name) { showErr('Nama wajib diisi.'); return; }
    if (!amount || amount < MIN_AMT) { showErr('Minimum donasi Rp ' + MIN_AMT.toLocaleString('id-ID')); return; }
    
    // Check if file has validation error
    if (fileInput && fileInput.files[0] && fileInput.validationMessage) {
        showErr(fileInput.validationMessage);
        return;
    }

    btn.disabled = true;
    btn.innerHTML = '<span style="opacity:0.7">Mengirim…</span>';

    try {
        // Use FormData to support file upload
        const formData = new FormData();
        formData.append('name', name);
        formData.append('amount', amount);
        formData.append('msg', msg);
        formData.append('emoji', emoji);
        
        // Add milestone_id if selected
        if (selectedMilestoneId) {
            formData.append('milestone_id', selectedMilestoneId);
        }
        
        // Add media file if present
        if (fileInput && fileInput.files[0]) {
            formData.append('media_file', fileInput.files[0]);
        }
        
        const res = await fetch(STORE_URL, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF },
            body: formData
        });
        const data = await res.json();
        
        if (data.success) {
            showSuccess(data.message || 'Terima kasih!');
        } else if (data.errors) {
            // Handle validation errors
            const firstError = Object.values(data.errors)[0];
            showErr(Array.isArray(firstError) ? firstError[0] : firstError);
            btn.disabled = false;
            btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg> Kirim Donasi';
        } else {
            showErr(data.message || 'Terjadi kesalahan.');
            btn.disabled = false;
            btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg> Kirim Donasi';
        }
    } catch (e) {
        showErr('Gagal mengirim. Coba lagi.');
        btn.disabled = false;
        btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg> Kirim Donasi';
    }
}

function showErr(msg) {
    const errBox = document.getElementById('error-box');
    errBox.textContent = msg;
    errBox.classList.add('show');
}

function showSuccess(msg) {
    document.getElementById('donate-form').style.display = 'none';
    const box = document.getElementById('success-box');
    document.getElementById('ty-emoji').textContent = document.getElementById('selected-emoji').value;
    document.getElementById('ty-msg').textContent = msg || 'Terima kasih!';
    box.classList.add('show');
    let sec = 8;
    const cd = document.getElementById('ty-countdown');
    cd.textContent = 'Form reset dalam ' + sec + ' detik';
    const iv = setInterval(() => { sec--; cd.textContent = 'Form reset dalam ' + sec + ' detik'; if (sec <= 0) { clearInterval(iv); resetForm(); } }, 1000);
}

// Update file upload button text and validate duration
function updateFileName(input) {
    const btn = document.getElementById('file-upload-btn');
    const text = document.getElementById('file-upload-text');
    const noteEl = document.getElementById('media-duration-note');
    const noteText = document.getElementById('duration-note-text');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileName = file.name;
        text.innerHTML = '<span class="file-upload-name">' + fileName + '</span>';
        btn.classList.add('has-file');
        
        // Validate file duration
        validateMediaDuration(file, function(duration, error) {
            if (error) {
                noteEl.style.display = 'flex';
                noteEl.className = 'media-duration-note error';
                noteText.textContent = error;
                input.setCustomValidity(error);
            } else if (duration !== null) {
                const amount = parseInt(document.getElementById('donor-amount').value) || 0;
                const maxDuration = getMaxDurationForAmount(amount);
                
                if (duration > maxDuration) {
                    const errorMsg = 'Durasi file (' + formatDuration(Math.ceil(duration)) + ') melebihi batas (' + formatDuration(maxDuration) + ')';
                    noteEl.style.display = 'flex';
                    noteEl.className = 'media-duration-note error';
                    noteText.textContent = errorMsg;
                    input.setCustomValidity(errorMsg);
                } else {
                    noteEl.style.display = 'flex';
                    noteEl.className = 'media-duration-note';
                    noteText.textContent = 'Durasi: ' + formatDuration(Math.ceil(duration)) + ' (maks ' + formatDuration(maxDuration) + ')';
                    input.setCustomValidity('');
                }
            } else {
                // Could not read duration, let backend validate
                input.setCustomValidity('');
                updateMediaDurationInfo();
            }
        });
    } else {
        text.textContent = 'Pilih File';
        btn.classList.remove('has-file');
        input.setCustomValidity('');
        updateMediaDurationInfo();
    }
}

// Validate media file duration using HTML5 Media API
function validateMediaDuration(file, callback) {
    const url = URL.createObjectURL(file);
    const isVideo = file.type.startsWith('video/');
    const media = isVideo ? document.createElement('video') : document.createElement('audio');
    
    media.preload = 'metadata';
    
    media.onloadedmetadata = function() {
        URL.revokeObjectURL(url);
        if (isFinite(media.duration) && media.duration > 0) {
            callback(media.duration, null);
        } else {
            callback(null, null); // Let backend validate
        }
    };
    
    media.onerror = function() {
        URL.revokeObjectURL(url);
        callback(null, 'Tidak dapat membaca file. Pastikan format didukung.');
    };
    
    media.src = url;
}

function resetForm() {
    document.getElementById('donate-form').style.display = '';
    document.getElementById('success-box').classList.remove('show');
    document.getElementById('donor-name').value = '';
    document.getElementById('donor-msg').value = '';
    document.getElementById('donor-amount').value = '25000';
    document.getElementById('donor-amount-display').value = '25.000';
    document.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('active'));
    document.querySelector('.preset-btn:nth-child(3)').classList.add('active');
    
    // Reset milestone selection slider to first item (no milestone)
    selectedMilestoneId = null;
    currentFormMilestoneIndex = 0;
    
    // Remove all selected classes
    document.querySelectorAll('.milestone-option').forEach(el => el.classList.remove('selected'));
    
    // Select first item (no milestone)
    if (formMilestones.length > 0) {
        const firstOption = formMilestones[0].querySelector('.milestone-option');
        if (firstOption) firstOption.classList.add('selected');
        updateFormMilestoneSlider();
    }
    
    // Reset file upload
    const fileInput = document.getElementById('media-upload');
    if (fileInput) {
        fileInput.value = '';
        updateFileName(fileInput);
    }
    
    const btn = document.getElementById('submit-btn');
    btn.disabled = false;
    btn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg> Kirim Donasi';
}

// Animated Favicon
(function() {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    canvas.width = 64;
    canvas.height = 64;
    
    const favicon = document.getElementById('favicon');
    let angle = 0;
    
    function drawFavicon() {
        // Clear canvas
        ctx.clearRect(0, 0, 64, 64);
        
        // Background gradient
        const gradient = ctx.createLinearGradient(0, 0, 64, 64);
        gradient.addColorStop(0, '#7c6cfc');
        gradient.addColorStop(1, '#ec4899');
        ctx.fillStyle = gradient;
        ctx.fillRect(0, 0, 64, 64);
        
        // Rotating effect
        ctx.save();
        ctx.translate(32, 32);
        ctx.rotate(angle);
        ctx.translate(-32, -32);
        
        // Draw "TT" text
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 36px Arial';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText('TT', 32, 32);
        
        ctx.restore();
        
        // Pulse effect with sine wave
        const scale = 1 + Math.sin(angle * 3) * 0.1;
        ctx.save();
        ctx.translate(32, 32);
        ctx.scale(scale, scale);
        ctx.translate(-32, -32);
        
        // Outer glow
        ctx.shadowColor = 'rgba(255, 255, 255, 0.3)';
        ctx.shadowBlur = 10;
        ctx.fillStyle = 'rgba(255, 255, 255, 0.1)';
        ctx.fillRect(0, 0, 64, 64);
        
        ctx.restore();
        
        // Update favicon
        favicon.href = canvas.toDataURL('image/png');
        
        // Increment angle for rotation
        angle += 0.05;
        
        // Loop animation
        requestAnimationFrame(drawFavicon);
    }
    
    // Start animation
    drawFavicon();
})();

// Animated Scrolling Title (Marquee Effect)
(function() {
    const originalTitle = "Donasi untuk {{ $streamer->display_name }} — Tiptipan";
    const separator = " ✨ ";
    let titleText = originalTitle + separator;
    let position = 0;
    
    function animateTitle() {
        // Create scrolling effect by shifting characters
        document.title = titleText.substring(position) + titleText.substring(0, position);
        
        position++;
        
        // Reset position when reaching end
        if (position > titleText.length) {
            position = 0;
        }
    }
    
    // Update title every 300ms for smooth scrolling effect
    setInterval(animateTitle, 300);
})();
</script>

<x-site-footer />

</body>
</html>
