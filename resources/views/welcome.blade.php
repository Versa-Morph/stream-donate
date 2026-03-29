<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tiptipan — Platform Donasi untuk Streamer Indonesia</title>
    <meta name="description" content="Platform donasi terpercaya khusus untuk streamer Indonesia. Terima donasi realtime, alert kustom, dan kelola supporter Anda dengan mudah.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Space+Grotesk:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --purple: #7C3AED;
            --purple-light: #A78BFA;
            --purple-dark: #5B21B6;
            --pink: #EC4899;
            --cyan: #06B6D4;
            --dark: #0A0A14;
            --dark-2: #12121F;
            --dark-3: #1A1A2E;
            --card: #16162A;
            --border: rgba(255,255,255,0.08);
            --text: #E2E8F0;
            --muted: #94A3B8;
            --white: #FFFFFF;
            --accent: #F59E0B;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark);
            color: var(--text);
            line-height: 1.6;
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* ===================== BACKGROUND ===================== */
        .bg-gradient {
            position: fixed;
            inset: 0;
            z-index: 0;
            background: 
                radial-gradient(ellipse 80% 50% at 50% -20%, rgba(124,58,237,0.25) 0%, transparent 50%),
                radial-gradient(ellipse 60% 40% at 100% 100%, rgba(236,72,153,0.15) 0%, transparent 40%),
                radial-gradient(ellipse 50% 30% at 0% 80%, rgba(6,182,212,0.1) 0%, transparent 40%),
                var(--dark);
        }

        .bg-grid {
            position: fixed;
            inset: 0;
            z-index: 0;
            background-image: 
                linear-gradient(rgba(255,255,255,0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.02) 1px, transparent 1px);
            background-size: 60px 60px;
        }

        /* Hyperspace Starfield Canvas */
        .starfield-canvas {
            position: fixed;
            inset: 0;
            z-index: 0;
            pointer-events: none;
        }

        /* ===================== FLOATING NAV ===================== */
        .floating-nav {
            position: fixed;
            top: 1.5rem;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            padding: 0.6rem 1.25rem;
            background: rgba(10, 10, 20, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 100px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3), 0 0 0 1px rgba(255,255,255,0.05) inset;
        }

        .nav-logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .nav-logo-icon {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--purple), var(--pink));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        .nav-logo-text {
            font-weight: 800;
            font-size: 1.1rem;
            color: white;
            font-family: 'Space Grotesk', sans-serif;
        }

        .nav-logo-text span { color: var(--purple-light); }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            list-style: none;
        }

        .nav-links a {
            color: var(--muted);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .nav-links a:hover {
            color: white;
            background: rgba(255,255,255,0.05);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-nav {
            padding: 0.5rem 1.25rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-nav-ghost {
            color: var(--text);
            background: transparent;
        }

        .btn-nav-ghost:hover {
            background: rgba(255,255,255,0.05);
            color: white;
        }

        .btn-nav-primary {
            color: white;
            background: linear-gradient(135deg, var(--purple), var(--pink));
        }

        .btn-nav-primary:hover {
            box-shadow: 0 0 20px rgba(124,58,237,0.5);
        }

        /* ===================== HERO SECTION ===================== */
        .hero {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 8rem 2rem 4rem;
            position: relative;
            z-index: 1;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(124,58,237,0.15);
            border: 1px solid rgba(124,58,237,0.3);
            border-radius: 100px;
            padding: 0.5rem 1.25rem;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--purple-light);
            margin-bottom: 2rem;
        }

        .hero-badge-dot {
            width: 6px;
            height: 6px;
            background: var(--purple-light);
            border-radius: 50%;
            animation: pulse 1.5s ease infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.5); opacity: 0.5; }
        }

        .hero-title {
            font-size: clamp(2.5rem, 7vw, 5rem);
            font-weight: 900;
            line-height: 1.1;
            color: white;
            text-align: center;
            font-family: 'Space Grotesk', sans-serif;
            margin-bottom: 1.5rem;
            min-height: 2.2em;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        #typing-text {
            display: block;
            min-height: 2.2em;
        }

        #typing-text br {
            display: block;
            content: '';
            margin: 0.1em 0;
        }

        .hero-title .highlight {
            background: linear-gradient(135deg, var(--purple-light), var(--pink), var(--cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: clamp(1rem, 2vw, 1.25rem);
            color: var(--muted);
            text-align: center;
            max-width: 600px;
            margin-bottom: 2.5rem;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn-hero {
            padding: 0.875rem 2rem;
            border-radius: 14px;
            font-size: 1rem;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
        }

        .btn-hero-primary {
            background: linear-gradient(135deg, var(--purple), var(--pink));
            color: white;
            box-shadow: 0 0 40px rgba(124,58,237,0.4);
        }

        .btn-hero-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 0 60px rgba(124,58,237,0.6);
        }

        .btn-hero-outline {
            background: transparent;
            border: 1px solid rgba(255,255,255,0.15);
            color: var(--text);
        }

        .btn-hero-outline:hover {
            border-color: var(--purple);
            background: rgba(124,58,237,0.1);
            color: white;
        }

        /* ===================== STATS SECTION ===================== */
        .stats-section {
            padding: 4rem 2rem;
            position: relative;
            z-index: 1;
        }

        .stats-container {
            max-width: 1100px;
            margin: 0 auto;
        }

        .stats-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .stats-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: white;
            margin-top: 0.75rem;
            font-family: 'Space Grotesk', sans-serif;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
        }

        .stat-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 20px;
            padding: 2rem 1.5rem;
            text-align: center;
            transition: all 0.3s;
        }

        .stat-card:hover {
            background: rgba(124,58,237,0.08);
            border-color: rgba(124,58,237,0.2);
            transform: translateY(-4px);
        }

        .stat-icon {
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .stat-icon svg {
            width: 2rem;
            height: 2rem;
        }

        .stat-prefix {
            font-size: 0.75rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 0.25rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 800;
            color: white;
            font-family: 'Space Grotesk', sans-serif;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--purple-light), var(--pink));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-label {
            font-size: 0.85rem;
            color: var(--muted);
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .stat-number { font-size: 2rem; }
        }

        /* ===================== LIVE DEMO SECTION ===================== */
        .demo-section {
            padding: 6rem 2rem;
            position: relative;
            z-index: 1;
        }

        .demo-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .demo-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .demo-tag {
            display: inline-block;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: var(--pink);
            margin-bottom: 0.75rem;
        }

        .demo-title {
            font-size: clamp(1.75rem, 3.5vw, 2.5rem);
            font-weight: 800;
            color: white;
            font-family: 'Space Grotesk', sans-serif;
        }

        .demo-grid {
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            gap: 3rem;
            align-items: start;
        }

        .demo-features {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: 2rem;
        }

        .demo-feature {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1.25rem;
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 16px;
            transition: all 0.3s;
        }

        .demo-feature:hover {
            background: rgba(124,58,237,0.05);
            border-color: rgba(124,58,237,0.2);
            transform: translateX(8px);
        }

        .demo-feature-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--purple), var(--pink));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
        }

        .demo-feature-text strong {
            display: block;
            color: white;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .demo-feature-text span {
            font-size: 0.875rem;
            color: var(--muted);
        }

        /* Live Feed */
        .live-feed {
            background: var(--dark-2);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 25px 80px rgba(0,0,0,0.4);
        }

        .live-feed-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem;
            background: var(--dark-3);
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .live-feed-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 700;
            color: white;
            font-size: 0.95rem;
        }

        .live-indicator {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            color: #ef4444;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .live-dot {
            width: 8px;
            height: 8px;
            background: #ef4444;
            border-radius: 50%;
            animation: live-pulse 1s ease infinite;
        }

        @keyframes live-pulse {
            0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(239,68,68,0.5); }
            50% { opacity: 0.7; box-shadow: 0 0 0 8px rgba(239,68,68,0); }
        }

        .live-feed-stats {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .stat-item {
            text-align: right;
        }

        .stat-label {
            font-size: 0.7rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-value {
            font-size: 1rem;
            font-weight: 700;
            color: var(--purple-light);
        }

        .live-feed-body {
            padding: 1rem;
            height: 380px;
            overflow: hidden;
        }

        .live-feed-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.5rem;
            background: var(--dark-3);
            border-top: 1px solid rgba(255,255,255,0.08);
            font-size: 0.875rem;
            color: var(--muted);
        }

        .footer-total {
            font-weight: 700;
            color: var(--pink);
            font-size: 1rem;
        }

        .feed-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            height: 100%;
        }

        .feed-item {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            padding: 0.875rem 1rem;
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 14px;
            animation: feed-in 0.4s ease;
        }

        @keyframes feed-in {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .feed-item:hover {
            background: rgba(124,58,237,0.08);
            border-color: rgba(124,58,237,0.2);
        }

        .feed-avatar {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            flex-shrink: 0;
            position: relative;
        }

        .feed-badge {
            position: absolute;
            bottom: -4px;
            right: -4px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 2px solid var(--dark-2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.5rem;
        }

        .feed-content {
            flex: 1;
            min-width: 0;
        }

        .feed-name-row {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .feed-name {
            font-weight: 600;
            color: white;
            font-size: 0.9rem;
        }

        .feed-badge-type {
            font-size: 0.6rem;
            padding: 0.1rem 0.4rem;
            border-radius: 4px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .feed-amount {
            font-weight: 800;
            font-size: 1rem;
            color: var(--purple-light);
        }

        .feed-message {
            font-size: 0.75rem;
            color: var(--muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-top: 0.15rem;
        }

        .feed-time {
            font-size: 0.7rem;
            color: var(--muted);
            flex-shrink: 0;
        }

        /* ===================== FEATURES SECTION ===================== */
        .features-section {
            padding: 6rem 2rem;
            position: relative;
            z-index: 1;
        }

        .features-container {
            max-width: 1100px;
            margin: 0 auto;
        }

        .features-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .features-title {
            font-size: clamp(1.75rem, 3.5vw, 2.5rem);
            font-weight: 800;
            color: white;
            font-family: 'Space Grotesk', sans-serif;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
        }

        .feature-card {
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 20px;
            padding: 1.75rem;
            transition: all 0.3s;
            position: relative;
        }

        .feature-card:hover {
            background: rgba(124,58,237,0.08);
            border-color: rgba(124,58,237,0.25);
            transform: translateY(-4px);
        }

        .feature-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .feature-card h3 {
            font-size: 1.15rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.5rem;
            font-family: 'Space Grotesk', sans-serif;
        }

        .feature-card p {
            font-size: 0.875rem;
            color: var(--muted);
            line-height: 1.6;
        }

        .feature-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: linear-gradient(135deg, var(--purple), var(--pink));
            color: white;
            font-size: 0.6rem;
            font-weight: 700;
            padding: 0.25rem 0.5rem;
            border-radius: 6px;
            text-transform: uppercase;
        }

        @media (max-width: 900px) {
            .features-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            .features-grid {
                grid-template-columns: 1fr;
            }
        }

        /* ===================== HOW IT WORKS SECTION ===================== */
        .how-it-works-section {
            padding: 6rem 2rem;
            position: relative;
            z-index: 1;
            background: linear-gradient(180deg, transparent 0%, rgba(124,58,237,0.05) 50%, transparent 100%);
        }

        .how-container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .how-header {
            text-align: center;
            margin-bottom: 4rem;
        }

        .how-title {
            font-size: clamp(1.75rem, 3.5vw, 2.5rem);
            font-weight: 800;
            color: white;
            font-family: 'Space Grotesk', sans-serif;
        }

        .how-steps {
            display: flex;
            align-items: flex-start;
            justify-content: center;
            gap: 1rem;
        }

        .how-step {
            flex: 1;
            text-align: center;
            max-width: 280px;
            position: relative;
        }

        .step-number {
            font-size: 0.75rem;
            font-weight: 800;
            color: var(--purple-light);
            letter-spacing: 0.1em;
            margin-bottom: 1rem;
        }

        .step-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, rgba(124,58,237,0.2), rgba(236,72,153,0.1));
            border: 1px solid rgba(124,58,237,0.2);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.25rem;
            margin: 0 auto 1.25rem;
            transition: all 0.3s;
        }

        .how-step:hover .step-icon {
            transform: scale(1.1);
            border-color: var(--purple);
            box-shadow: 0 0 30px rgba(124,58,237,0.3);
        }

        .how-step h3 {
            font-size: 1.15rem;
            font-weight: 700;
            color: white;
            margin-bottom: 0.75rem;
            font-family: 'Space Grotesk', sans-serif;
        }

        .how-step p {
            font-size: 0.875rem;
            color: var(--muted);
            line-height: 1.6;
        }

        .how-connector {
            width: 60px;
            height: 2px;
            background: linear-gradient(90deg, var(--purple), var(--pink));
            opacity: 0.3;
            margin-top: 40px;
            flex-shrink: 0;
        }

        @media (max-width: 768px) {
            .how-steps {
                flex-direction: column;
                align-items: center;
            }
            .how-connector {
                width: 2px;
                height: 30px;
                margin: 0;
            }
            .how-step {
                max-width: 100%;
            }
        }

        /* ===================== TESTIMONIALS ===================== */
        .testimonials-section {
            padding: 6rem 0;
            position: relative;
            z-index: 1;
            overflow: hidden;
        }

        .testimonials-header {
            text-align: center;
            margin-bottom: 3rem;
            padding: 0 2rem;
        }

        .testimonials-title {
            font-size: clamp(1.75rem, 3.5vw, 2.5rem);
            font-weight: 800;
            color: white;
            font-family: 'Space Grotesk', sans-serif;
        }

        .testimonials-scroll {
            display: flex;
            gap: 1.25rem;
            animation: scroll 40s linear infinite;
            width: max-content;
        }

        .testimonials-scroll:hover {
            animation-play-state: paused;
        }

        @keyframes scroll {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }

        .testimonial-card {
            width: 340px;
            flex-shrink: 0;
            background: linear-gradient(180deg, rgba(124,58,237,0.1) 0%, rgba(22,22,42,0.6) 100%);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px;
            padding: 1.5rem;
            transition: all 0.3s;
        }

        .testimonial-card:hover {
            transform: translateY(-5px);
            border-color: rgba(124,58,237,0.3);
        }

        .testimonial-user {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            margin-bottom: 1rem;
        }

        .testimonial-avatar {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }

        .testimonial-info strong {
            display: block;
            color: white;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .testimonial-info span {
            font-size: 0.75rem;
            color: var(--muted);
        }

        .testimonial-stars {
            color: #FFD700;
            font-size: 0.85rem;
            margin-bottom: 0.75rem;
        }

        .testimonial-text {
            color: var(--muted);
            font-size: 0.875rem;
            line-height: 1.6;
        }

        /* ===================== CTA SECTION ===================== */
        .cta-section {
            padding: 8rem 2rem;
            position: relative;
            z-index: 1;
            text-align: center;
        }

        .cta-content {
            max-width: 700px;
            margin: 0 auto;
            padding: 4rem;
            background: linear-gradient(180deg, rgba(124,58,237,0.15) 0%, rgba(236,72,153,0.05) 100%);
            border: 1px solid rgba(124,58,237,0.2);
            border-radius: 32px;
            position: relative;
            overflow: hidden;
        }

        .cta-content::before {
            content: '';
            position: absolute;
            top: -100px;
            left: 50%;
            transform: translateX(-50%);
            width: 400px;
            height: 400px;
            background: var(--purple);
            border-radius: 50%;
            filter: blur(100px);
            opacity: 0.15;
        }

        .cta-title {
            font-size: clamp(1.75rem, 4vw, 2.75rem);
            font-weight: 800;
            color: white;
            font-family: 'Space Grotesk', sans-serif;
            margin-bottom: 1rem;
            position: relative;
        }

        .cta-title span {
            background: linear-gradient(135deg, var(--purple-light), var(--pink));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .cta-desc {
            color: var(--muted);
            font-size: 1.05rem;
            margin-bottom: 2rem;
            position: relative;
        }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            position: relative;
        }

        /* ===================== FAQ SECTION ===================== */
        .faq-section {
            padding: 6rem 2rem;
            position: relative;
            z-index: 1;
        }

        .faq-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .faq-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .faq-title {
            font-size: clamp(1.75rem, 3.5vw, 2.5rem);
            font-weight: 800;
            color: white;
            font-family: 'Space Grotesk', sans-serif;
        }

        .faq-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .faq-item {
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 16px;
            overflow: hidden;
            transition: all 0.3s;
        }

        .faq-item:hover {
            border-color: rgba(124,58,237,0.2);
        }

        .faq-item.active {
            border-color: rgba(124,58,237,0.4);
            background: rgba(124,58,237,0.05);
        }

        .faq-question {
            width: 100%;
            padding: 1.25rem 1.5rem;
            background: transparent;
            border: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            color: white;
            font-size: 1rem;
            font-weight: 600;
            text-align: left;
            font-family: 'Inter', sans-serif;
        }

        .faq-icon {
            font-size: 1.5rem;
            color: var(--purple-light);
            transition: all 0.3s;
            line-height: 1;
        }

        .faq-item.active .faq-icon {
            transform: rotate(45deg);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .faq-item.active .faq-answer {
            max-height: 200px;
        }

        .faq-answer p {
            padding: 0 1.5rem 1.25rem;
            color: var(--muted);
            font-size: 0.9rem;
            line-height: 1.7;
        }

        /* ===================== FOOTER ===================== */
        .footer {
            position: relative;
            z-index: 1;
            margin-top: 4rem;
            padding: 0 2rem 2rem;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            border-top: 1px solid rgba(255,255,255,0.06);
        }

        .footer-main {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 2.5rem 0;
            gap: 2rem;
        }

        .footer-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .footer-brand {
            font-size: 1.35rem;
            font-weight: 700;
            color: white;
            font-family: 'Space Grotesk', sans-serif;
        }

        .footer-brand .dot {
            color: var(--purple-light);
        }

        .footer-tagline {
            font-size: 0.85rem;
            color: var(--muted);
            margin: 0;
            padding-left: 1rem;
            border-left: 1px solid rgba(255,255,255,0.1);
        }

        .footer-links {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex: 1;
            justify-content: center;
            flex-wrap: wrap;
        }

        .footer-links a {
            font-size: 0.85rem;
            color: var(--muted);
            transition: color 0.3s;
            font-weight: 500;
            text-decoration: none;
            padding: 0.25rem 0.5rem;
        }

        .footer-links a:hover {
            color: white;
        }

        .footer-links .footer-separator {
            font-size: 0.7rem;
            color: var(--muted);
            opacity: 0.4;
        }

        .footer-social {
            display: flex;
            gap: 0.5rem;
        }

        .footer-social a {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--muted);
            transition: all 0.3s;
            border-radius: 8px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
        }

        .footer-social a:hover {
            color: white;
            background: rgba(124,58,237,0.2);
            border-color: var(--purple);
        }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 0 0;
            border-top: 1px solid rgba(255,255,255,0.06);
        }

        .footer-credits {
            font-size: 0.85rem;
            color: var(--muted);
        }

        .footer-badge {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            font-size: 0.8rem;
            color: var(--muted);
        }

        @media (max-width: 900px) {
            .footer-main {
                flex-direction: column;
                text-align: center;
            }
            .footer-left {
                flex-direction: column;
                gap: 0.5rem;
            }
            .footer-tagline {
                border-left: none;
                padding-left: 0;
            }
            .footer-links {
                order: 3;
            }
            .footer-bottom {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }
        }

        /* ===================== RESPONSIVE ===================== */
        @media (max-width: 968px) {
            .floating-nav {
                width: calc(100% - 2rem);
                justify-content: space-between;
                padding: 0.75rem 1rem;
            }

            .nav-links { display: none; }

            .nav-logo {
                padding-right: 0;
                margin-right: 0;
                border-right: none;
            }

            .demo-grid {
                grid-template-columns: 1fr;
            }

            .footer-inner {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 640px) {
            .hero { padding: 7rem 1.5rem 3rem; }
            .hero-title { font-size: 2.25rem; }
            
            .floating-nav {
                padding: 0.5rem 0.75rem;
            }
            
            .nav-logo-text { display: none; }
            
            .nav-actions { margin-left: 0; padding-left: 0; border-left: none; }
            
            .btn-nav { padding: 0.4rem 0.875rem; font-size: 0.8rem; }
            
            .testimonial-card { width: 280px; }
            
            .footer-inner { grid-template-columns: 1fr; }
        }

        /* ===================== PAGE TRANSITION - SIMPLE FADE ===================== */
        .page-content {
            transition: opacity 0.7s ease-out;
        }
        
        .page-content.fading {
            opacity: 0;
        }
    </style>
</head>
<body>
    <div class="bg-gradient"></div>
    <div class="bg-grid"></div>
    <canvas class="starfield-canvas" id="starfield"></canvas>

    <!-- Page Content Wrapper for Transition -->
    <div class="page-content" id="page-content">

    {{-- FLOATING NAV --}}
    <nav class="floating-nav">
        <a href="#" class="nav-logo">
            <div class="nav-logo-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2"/><line x1="6" y1="12" x2="10" y2="12"/><line x1="8" y1="10" x2="8" y2="14"/><circle cx="17" cy="10" r="1" fill="currentColor"/><circle cx="15" cy="12" r="1" fill="currentColor"/></svg>
            </div>
            <span class="nav-logo-text">Tip<span>tipan</span></span>
        </a>
        <ul class="nav-links">
            <li><a href="#stats">Jejak Kami</a></li>
            <li><a href="#demo">Lihat Demo</a></li>
            <li><a href="#features">Keunggulan</a></li>
            <li><a href="#how-it-works">Panduan Awal</a></li>
            <li><a href="#faq">Pusat Informasi</a></li>
        </ul>
        <div class="nav-actions">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn-nav btn-nav-primary">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn-nav btn-nav-ghost">Masuk</a>
                <a href="{{ route('register') }}" class="btn-nav btn-nav-primary">Daftar</a>
            @endauth
        </div>
    </nav>

    {{-- HERO --}}
    <section class="hero">
        <div class="hero-badge">
            <div class="hero-badge-dot"></div>
            Platform Donasi #1 Indonesia
        </div>
        
        <h1 class="hero-title">
            <span id="typing-text"></span>
        </h1>
        
        <p class="hero-subtitle">
            Platform donasi realtime dengan alert menarik. Setiap dukungan langsung terasa spesial di layar OBS-mu.
        </p>
        
        <div class="hero-buttons">
            @auth
                    <a href="{{ url('/dashboard') }}" class="btn-hero btn-hero-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                        Buka Dashboard
                    </a>
            @else
                    <a href="{{ route('register') }}" class="btn-hero btn-hero-primary">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/></svg>
                        Daftar Gratis Sekarang
                    </a>
                <a href="{{ route('login') }}" class="btn-hero btn-hero-outline">Login</a>
            @endauth
        </div>
    </section>

    {{-- STATS SECTION --}}
    <section class="stats-section" id="stats">
        <div class="stats-container">
            <div class="stats-header">
                <span class="demo-tag">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    Jejak Kami
                </span>
                <h2 class="stats-title">Bukti Nyata, Angka yang Berbicara</h2>
            </div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="6" width="20" height="12" rx="2"/><line x1="6" y1="12" x2="10" y2="12"/><line x1="8" y1="10" x2="8" y2="14"/><circle cx="17" cy="10" r="1" fill="currentColor"/><circle cx="15" cy="12" r="1" fill="currentColor"/></svg>
                    </div>
                    <div class="stat-number" data-target="15000" data-suffix="K+">0+</div>
                    <div class="stat-label">Streamer Aktif</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor" stroke="none"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                    </div>
                    <div class="stat-number" data-target="50000000" data-suffix="M+">0+</div>
                    <div class="stat-label">Total Donasi</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div class="stat-number" data-target="250000" data-suffix="K+">0+</div>
                    <div class="stat-label">Supporter Setia</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                    </div>
                    <div class="stat-number" data-target="99">0%</div>
                    <div class="stat-label">Uptime</div>
                </div>
            </div>
        </div>
    </section>

    {{-- LIVE DEMO --}}
    <section class="demo-section" id="demo">
        <div class="demo-container">
            <div class="demo-header">
                <span class="demo-tag">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                    Live Alert
                </span>
                <h2 class="demo-title">Notifikasi yang Bikin Hype</h2>
            </div>
            
            <div class="demo-grid">
                <div class="demo-features">
                    <div class="demo-feature">
                        <div class="demo-feature-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        </div>
                        <div class="demo-feature-text">
                            <strong>0 Detik Delay</strong>
                            <span>Teknologi SSE membuat alert muncul instan di OBS</span>
                        </div>
                    </div>
                    <div class="demo-feature">
                        <div class="demo-feature-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="13.5" cy="6.5" r=".5" fill="white"/><circle cx="17.5" cy="10.5" r=".5" fill="white"/><circle cx="8.5" cy="7.5" r=".5" fill="white"/><circle cx="6.5" cy="12.5" r=".5" fill="white"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.93 0 1.5-.67 1.5-1.5 0-.39-.14-.77-.38-1.06-.24-.28-.38-.65-.38-1.06 0-.83.67-1.5 1.5-1.5H16c3.31 0 6-2.69 6-6 0-4.96-4.48-9-10-9z"/></svg>
                        </div>
                        <div class="demo-feature-text">
                            <strong>100% Customizable</strong>
                            <span>Canvas editor drag & drop untuk desain alert</span>
                        </div>
                    </div>
                    <div class="demo-feature">
                        <div class="demo-feature-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/></svg>
                        </div>
                        <div class="demo-feature-text">
                            <strong>Suara Custom</strong>
                            <span>Upload sound effect sendiri per nominal donasi</span>
                        </div>
                    </div>
                    <div class="demo-feature">
                        <div class="demo-feature-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                        </div>
                        <div class="demo-feature-text">
                            <strong>Filter Cerdas</strong>
                            <span>Filter kata kasar otomatis untuk keamanan</span>
                        </div>
                    </div>
                </div>
                
                <div class="live-feed">
                    <div class="live-feed-header">
                        <div class="live-feed-title">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                            Donation Feed
                        </div>
                        <div class="live-indicator">
                            <div class="live-dot"></div>
                            LIVE
                        </div>
                    </div>
                    <div class="live-feed-body">
                        <div class="feed-list" id="feed-list"></div>
                    </div>
                    <div class="live-feed-footer">
                        <span>Total Donasi Hari Ini</span>
                        <span class="footer-total">Rp 3.615.000</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FEATURES SECTION --}}
    <section class="features-section" id="features">
        <div class="features-container">
            <div class="features-header">
                <span class="demo-tag">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    Fitur Unggulan
                </span>
                <h2 class="features-title">Semua yang Kamu Butuhkan</h2>
            </div>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="13.5" cy="6.5" r=".5" fill="currentColor"/><circle cx="17.5" cy="10.5" r=".5" fill="currentColor"/><circle cx="8.5" cy="7.5" r=".5" fill="currentColor"/><circle cx="6.5" cy="12.5" r=".5" fill="currentColor"/><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10c.93 0 1.5-.67 1.5-1.5 0-.39-.14-.77-.38-1.06-.24-.28-.38-.65-.38-1.06 0-.83.67-1.5 1.5-1.5H16c3.31 0 6-2.69 6-6 0-4.96-4.48-9-10-9z"/></svg>
                    </div>
                    <h3>Canvas Editor</h3>
                    <p>Desain alert sesukamu dengan drag & drop. Font, warna, animasi - semua bisa dikustom!</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/></svg>
                    </div>
                    <h3>Sound Pack</h3>
                    <p>Pilih suara alert dari koleksi kami atau upload sound effect custom-mu sendiri.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    </div>
                    <h3>Analytics</h3>
                    <p>Lihat statistik donasi, supporter ter aktif, dan tren bulanan.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <h3>Filter Cerdas</h3>
                    <span class="feature-badge">AI Powered</span>
                    <p>Filter kata kasar otomatis dengan AI. Stream tetap aman dan nyaman.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                    <h3>Penarikan Cepat</h3>
                    <p>Cairkan donasi ke rekening bank kapan saja. Proses cepat dan aman.</p>
                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                    </div>
                    <h3>Multi Platform</h3>
                    <p>Bisa digunakan di Twitch, YouTube, Facebook Gaming, dan platform streaming lainnya.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- HOW IT WORKS SECTION --}}
    <section class="how-it-works-section" id="how-it-works">
        <div class="how-container">
            <div class="how-header">
                <span class="demo-tag">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
                    Mulai Sekarang
                </span>
                <h2 class="how-title">Gampang Banget!</h2>
            </div>
            
            <div class="how-steps">
                <div class="how-step">
                    <div class="step-number">01</div>
                    <div class="step-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/></svg>
                    </div>
                    <h3>Daftar Gratis</h3>
                    <p>Buat akun dalam 30 detik. Tanpa kartu kredit, tanpa biaya awal.</p>
                </div>
                <div class="how-connector"></div>
                <div class="how-step">
                    <div class="step-number">02</div>
                    <div class="step-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    </div>
                    <h3>Atur Alert</h3>
                    <p>Pilih tema, atur nominal trigger, dan customize tampilan sesukamu.</p>
                </div>
                <div class="how-connector"></div>
                <div class="how-step">
                    <div class="step-number">03</div>
                    <div class="step-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="15" rx="2" ry="2"/><polyline points="17 2 12 7 7 2"/></svg>
                    </div>
                    <h3>Siap Streaming</h3>
                    <p>Salin URL widget dan tambahkan ke OBS. Done! Mulai Terima Donasi!</p>
                </div>
            </div>
        </div>
    </section>

    {{-- TESTIMONIALS --}}
    <section class="testimonials-section" id="testimonials">
        <div class="testimonials-header">
            <span class="demo-tag">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                Testimoni
            </span>
            <h2 class="testimonials-title">Dipercaya Ribuan Streamer</h2>
        </div>
        
        <div class="testimonials-scroll" id="testimonials-scroll"></div>
    </section>

    {{-- FAQ SECTION --}}
    <section class="faq-section" id="faq">
        <div class="faq-container">
            <div class="faq-header">
                <span class="demo-tag">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    Pertanyaan
                </span>
                <h2 class="faq-title">Yang Sering Ditanyakan</h2>
            </div>
            
            <div class="faq-list">
                <div class="faq-item">
                    <button class="faq-question">
                        <span>Apakah Tiptipan gratis digunakan?</span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <p>Ya! Tiptipan sepenuhnya gratis untuk streamer. Kami tidak memungut biaya bulanan atau tahunan. Kami hanya mengambil небольшой комиссии от setiap donasi yang masuk (sama seperti platform lain). Untuk supporter, tidak ada biaya tambahan.</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question">
                        <span>Berapa lama dana donasi cair ke rekening?</span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <p>Proses pencairan dilakukan setiap hari kerja. Jika kamu mengajukan penarikan hari ini, dana akan masuk ke rekening bank kamu dalam 1-2 hari kerja. Untuk pengguna dengan verifikasi lengkap, penarikan bisa diproses lebih cepat!</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question">
                        <span>Apakah bisa digunakan di OBS?</span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <p>Tentu! Tiptipan menyediakan widget URL yang bisa langsung kamu tambahkan ke OBS sebagai Browser Source. Alert akan muncul secara realtime tanpa delay!</p>
                    </div>
                </div>
                <div class="faq-item">
                    <button class="faq-question">
                        <span>Apakah data donatur aman?</span>
                        <span class="faq-icon">+</span>
                    </button>
                    <div class="faq-answer">
                        <p>100% aman! Kami menggunakan enkripsi tingkat bank untuk melindungi data donatur. Data pribadi supporter tidak akan pernah kami bagikan ke pihak ketiga.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="cta-section" id="cta">
        <div class="cta-content">
            <h2 class="cta-title">Siap <span>Upgrade</span> Stream-mu?</h2>
            <p class="cta-desc">Bergabung dengan ribuan streamer Indonesia yang sudah monetize stream-nya dengan Tiptipan.</p>
            <div class="cta-buttons">
                @auth
                <a href="{{ url('/dashboard') }}" class="btn-hero btn-hero-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Buka Dashboard
                </a>
                @else
                <a href="{{ route('register') }}" class="btn-hero btn-hero-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.937 15.5A2 2 0 0 0 8.5 14.063l-6.135-1.582a.5.5 0 0 1 0-.962L8.5 9.936A2 2 0 0 0 9.937 8.5l1.582-6.135a.5.5 0 0 1 .963 0L14.063 8.5A2 2 0 0 0 15.5 9.937l6.135 1.581a.5.5 0 0 1 0 .964L15.5 14.063a2 2 0 0 0-1.437 1.437l-1.582 6.135a.5.5 0 0 1-.963 0z"/></svg>
                    Daftar Gratis Sekarang
                </a>
                    <a href="{{ route('login') }}" class="btn-hero btn-hero-outline">Login</a>
                @endauth
            </div>
        </div>
    </section>

    {{-- FOOTER (Agency Style) --}}
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-main">
                <div class="footer-left">
                    <div class="footer-brand">Tiptipan<span class="dot">.</span></div>
                    <p class="footer-tagline">Platform Donasi Indonesia</p>
                </div>
                
                <div class="footer-links">
                    <a href="#stats">Jejak Kami</a>
                    <span class="footer-separator">•</span>
                    <a href="#features">Keunggulan</a>
                    <span class="footer-separator">•</span>
                    <a href="#how-it-works">Panduan Awal</a>
                    <span class="footer-separator">•</span>
                    <a href="#faq">Pusat Informasi</a>
                    <span class="footer-separator">•</span>
                    <a href="{{ route('policies.index') }}">Kebijakan & Ketentuan</a>
                </div>

                <div class="footer-social">
                    <a href="#" aria-label="Twitter">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    <a href="#" aria-label="Instagram">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                    <a href="#" aria-label="YouTube">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                    </a>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="footer-credits">
                    <span>© {{ date('Y') }} Tiptipan by VersaMorph</span>
                </div>
                <div class="footer-badge">
                    <span>Made with</span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="#ef4444"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                    <span>in Indonesia</span>
                </div>
            </div>
        </div>
    </footer>

    </div><!-- End .page-content -->

    <script>
        // ===================== TYPING EFFECT =====================
        (function() {
            const typingElement = document.getElementById('typing-text');
            const phrases = [
                [
                    { text: 'Terima Donasi,', delay: 80 },
                    { text: 'Mudah Tanpa Ribet!', delay: 100, highlight: true }
                ],
                [
                    { text: 'Alert Keren,', delay: 80 },
                    { text: 'Stream Makin Seru!', delay: 100, highlight: true }
                ],
                [
                    { text: 'Donasi Realtime,', delay: 80 },
                    { text: 'Langsung Meluncur!', delay: 100, highlight: true }
                ],
                [
                    { text: 'Custom Alert,', delay: 80 },
                    { text: 'Bikin Cantik & Unik!', delay: 100, highlight: true }
                ],
                [
                    { text: 'Keamanan Terjamin,', delay: 80 },
                    { text: 'Fokus Saja Streaming!', delay: 100, highlight: true }
                ],
                [
                    { text: 'Cair Cepat,', delay: 80 },
                    { text: 'Langsung ke Rekening!', delay: 100, highlight: true }
                ],
                [
                    { text: 'Gratis Daftar,', delay: 80 },
                    { text: 'Zero Biaya Admin!', delay: 100, highlight: true }
                ]
            ];
            
            let phraseIndex = 0;
            let lineIndex = 0;
            let charIndex = 0;
            
            function type() {
                const currentPhrase = phrases[phraseIndex];
                const currentLine = currentPhrase[lineIndex];
                
                const targetText = currentLine.text.substring(0, charIndex + 1);
                typingElement.innerHTML = '';
                
                // Build the full text up to current line
                for (let i = 0; i < lineIndex; i++) {
                    if (currentPhrase[i].highlight) {
                        typingElement.innerHTML += `<span class="highlight">${currentPhrase[i].text}</span><br>`;
                    } else {
                        typingElement.innerHTML += currentPhrase[i].text + '<br>';
                    }
                }
                
                // Add current line being typed
                if (currentLine.highlight) {
                    typingElement.innerHTML += `<span class="highlight">${targetText}</span>`;
                } else {
                    typingElement.innerHTML += targetText;
                }
                
                charIndex++;
                
                if (charIndex >= currentLine.text.length) {
                    lineIndex++;
                    charIndex = 0;
                    
                    if (lineIndex >= currentPhrase.length) {
                        // Phrase complete
                        setTimeout(() => {
                            phraseIndex = (phraseIndex + 1) % phrases.length;
                            lineIndex = 0;
                            charIndex = 0;
                            type();
                        }, 3000);
                    } else {
                        setTimeout(type, 400);
                    }
                    return;
                }
                
                setTimeout(type, currentLine.delay);
            }
            
            // Start typing
            setTimeout(type, 500);
        })();

        // ===================== HYPERSPACE STARFIELD =====================
        // Expose speed control for page transition with smooth interpolation
        window.starfieldSpeed = {
            normal: 1.5,
            fast: 3,
            current: 1.5,
            target: 1.5,
            // Smooth transition to target speed
            update: function() {
                const diff = this.target - this.current;
                this.current += diff * 0.08; // Easing factor - smooth interpolation
            },
            setTarget: function(newSpeed) {
                this.target = newSpeed;
            }
        };
        
        (function() {
            const canvas = document.getElementById('starfield');
            const ctx = canvas.getContext('2d');
            
            let width, height;
            let stars = [];
            const numStars = 400;
            const centerGap = 150; // Empty center area radius
            
            // Use dynamic speed from window object
            function getSpeed() {
                // Update speed with smooth interpolation each frame
                window.starfieldSpeed.update();
                return window.starfieldSpeed.current;
            }
            
            // Resize handler
            function resize() {
                width = canvas.width = window.innerWidth;
                height = canvas.height = window.innerHeight;
            }
            
            // Star class - starts from random position, moves outward in random direction
            class Star {
                constructor() {
                    // Initialize at random starting position to avoid initial jump
                    let angle = Math.random() * Math.PI * 2;
                    let radius = centerGap + Math.random() * Math.max(width, height);
                    
                    this.x = width / 2 + Math.cos(angle) * radius;
                    this.y = height / 2 + Math.sin(angle) * radius;
                    this.prevX = this.x;
                    this.prevY = this.y;
                    
                    // Direction pointing outward from center
                    this.dx = Math.cos(angle);
                    this.dy = Math.sin(angle);
                    
                    // Depth for perspective
                    this.z = 400 + Math.random() * 600;
                }
                
                reset() {
                    // Random starting position (avoid center)
                    let angle = Math.random() * Math.PI * 2;
                    let radius = centerGap + Math.random() * Math.max(width, height);
                    
                    this.x = width / 2 + Math.cos(angle) * radius;
                    this.y = height / 2 + Math.sin(angle) * radius;
                    
                    // Direction pointing outward from center
                    this.dx = Math.cos(angle);
                    this.dy = Math.sin(angle);
                    
                    // Velocity (speed increases as it gets closer to camera)
                    const speed = getSpeed();
                    this.vx = this.dx * speed;
                    this.vy = this.dy * speed;
                    
                    // Depth for perspective (varied for visual interest)
                    this.z = 400 + Math.random() * 600;
                    this.prevX = this.x;
                    this.prevY = this.y;
                }
                
                update() {
                    this.prevX = this.x;
                    this.prevY = this.y;
                    
                    // Move outward and decrease z (toward camera)
                    const speed = getSpeed();
                    this.vx = this.dx * speed;
                    this.vy = this.dy * speed;
                    this.x += this.vx * (1000 / this.z);
                    this.y += this.vy * (1000 / this.z);
                    this.z -= speed * 3;
                    
                    // Reset when out of bounds or too close
                    const margin = 200;
                    const outOfBounds = 
                        this.x < -margin || 
                        this.x > width + margin || 
                        this.y < -margin || 
                        this.y > height + margin;
                    
                    if (this.z <= 50 || outOfBounds) {
                        this.reset();
                    }
                }
                
                draw() {
                    const speed = getSpeed();
                    // Perspective scale
                    const scale = 800 / this.z;
                    
                    // Screen position
                    const screenX = this.x * scale + (width - width * scale) / 2;
                    const screenY = this.y * scale + (height - height * scale) / 2;
                    
                    // Previous position
                    const prevScale = 800 / (this.z + speed * 3);
                    const prevScreenX = this.prevX * prevScale + (width - width * prevScale) / 2;
                    const prevScreenY = this.prevY * prevScale + (height - height * prevScale) / 2;
                    
                    // Smaller star size
                    const size = Math.max(0.3, (1.5 - this.z / 1200));
                    
                    // Opacity - fade in when far, fade out when too close
                    let opacity = Math.min(1, (1200 - this.z) / 300) * Math.min(1, this.z / 100);
                    opacity = Math.max(0, Math.min(0.9, opacity));
                    
                    // Skip if in center gap area
                    const distFromCenter = Math.sqrt(
                        Math.pow(screenX - width/2, 2) + 
                        Math.pow(screenY - height/2, 2)
                    );
                    if (distFromCenter < centerGap * scale) return;
                    
                    // Also skip if previous position was in center (avoid lines through center)
                    const prevDistFromCenter = Math.sqrt(
                        Math.pow(prevScreenX - width/2, 2) + 
                        Math.pow(prevScreenY - height/2, 2)
                    );
                    if (prevDistFromCenter < centerGap * prevScale) return;
                    
                    // Draw longer trail
                    const dx = screenX - prevScreenX;
                    const dy = screenY - prevScreenY;
                    const trailLength = Math.sqrt(dx * dx + dy * dy);
                    
                    // Limit trail length to avoid weird artifacts during speed changes
                    const maxTrailLength = 150;
                    if (trailLength > 2 && trailLength < maxTrailLength) {
                        // Extend trail backward for longer effect
                        const extendFactor = 8;
                        const trailEndX = screenX - dx * extendFactor;
                        const trailEndY = screenY - dy * extendFactor;
                        
                        // Check if trail end point is too close to center
                        const trailEndDist = Math.sqrt(
                            Math.pow(trailEndX - width/2, 2) + 
                            Math.pow(trailEndY - height/2, 2)
                        );
                        if (trailEndDist < centerGap) return;
                        
                        const gradient = ctx.createLinearGradient(
                            trailEndX, trailEndY, screenX, screenY
                        );
                        gradient.addColorStop(0, 'rgba(255, 255, 255, 0)');
                        gradient.addColorStop(0.4, `rgba(255, 255, 255, ${opacity * 0.3})`);
                        gradient.addColorStop(1, `rgba(255, 255, 255, ${opacity})`);
                        
                        ctx.beginPath();
                        ctx.moveTo(trailEndX, trailEndY);
                        ctx.lineTo(screenX, screenY);
                        ctx.strokeStyle = gradient;
                        ctx.lineWidth = size;
                        ctx.lineCap = 'round';
                        ctx.stroke();
                    }
                    
                    // Draw star head (smaller)
                    ctx.beginPath();
                    ctx.arc(screenX, screenY, size, 0, Math.PI * 2);
                    ctx.fillStyle = `rgba(255, 255, 255, ${opacity})`;
                    ctx.fill();
                }
            }
            
            // Initialize stars
            function init() {
                resize();
                stars = [];
                for (let i = 0; i < numStars; i++) {
                    stars.push(new Star());
                }
                initUFOs();
            }
            
            // UFO color variants matching background
            const ufoColors = [
                { main: '#7C3AED', light: '#A78BFA', dark: '#5B21B6', accent: '#C4B5FD', name: 'purple' },
                { main: '#EC4899', light: '#F472B6', dark: '#BE185D', accent: '#FBCFE8', name: 'pink' },
                { main: '#06B6D4', light: '#22D3EE', dark: '#0891B2', accent: '#A5F3FC', name: 'cyan' },
                { main: '#A78BFA', light: '#C4B5FD', dark: '#7C3AED', accent: '#DDD6FE', name: 'violet' },
                { main: '#F43F5E', light: '#FB7185', dark: '#E11D48', accent: '#FECDD3', name: 'rose' }
            ];
            
            // UFO class - moves toward center (opposite to stars)
            class UFO {
                constructor(colorIndex = 0) {
                    this.colorIndex = colorIndex;
                    this.colors = ufoColors[colorIndex];
                    this.reset();
                }
                
                reset() {
                    const angle = Math.random() * Math.PI * 2;
                    const radius = Math.max(width, height) * 0.6 + Math.random() * 200;
                    
                    this.x = width / 2 + Math.cos(angle) * radius;
                    this.y = height / 2 + Math.sin(angle) * radius;
                    this.angle = angle;
                    this.speed = 0.6 + Math.random() * 0.4;
                    this.z = 600 + Math.random() * 500;
                    this.size = 18 + Math.random() * 12;
                    this.wobble = 0;
                    this.wobbleSpeed = 0.02 + Math.random() * 0.02;
                    this.glowPhase = Math.random() * Math.PI * 2;
                    this.trail = [];
                    this.maxTrailLength = 25;
                    this.rotationSpeed = (Math.random() - 0.5) * 0.02;
                }
                
                update() {
                    const dirX = -Math.cos(this.angle);
                    const dirY = -Math.sin(this.angle);
                    
                    this.trail.unshift({ x: this.x, y: this.y, z: this.z });
                    if (this.trail.length > this.maxTrailLength) {
                        this.trail.pop();
                    }
                    
                    this.x += dirX * this.speed * (this.z / 400);
                    this.y += dirY * this.speed * (this.z / 400);
                    this.z -= this.speed * 2;
                    this.wobble += this.wobbleSpeed;
                    this.glowPhase += 0.05;
                    
                    const distFromCenter = Math.sqrt(
                        Math.pow(this.x - width/2, 2) + 
                        Math.pow(this.y - height/2, 2)
                    );
                    
                    if (this.z <= 50 || distFromCenter < 100) {
                        this.colorIndex = Math.floor(Math.random() * ufoColors.length);
                        this.colors = ufoColors[this.colorIndex];
                        this.reset();
                    }
                }
                
                hexToRgba(hex, alpha) {
                    const r = parseInt(hex.slice(1, 3), 16);
                    const g = parseInt(hex.slice(3, 5), 16);
                    const b = parseInt(hex.slice(5, 7), 16);
                    return `rgba(${r}, ${g}, ${b}, ${alpha})`;
                }
                
                draw() {
                    const scale = 600 / this.z;
                    const screenX = this.x * scale + (width - width * scale) / 2;
                    const screenY = this.y * scale + (height - height * scale) / 2;
                    
                    if (screenX < -100 || screenX > width + 100 || 
                        screenY < -100 || screenY > height + 100) return;
                    
                    const size = this.size * scale;
                    const opacity = Math.min(0.9, Math.max(0.2, (1000 - this.z) / 500));
                    const c = this.colors;
                    
                    // Draw trail
                    this.trail.forEach((point, i) => {
                        const trailScale = 600 / point.z;
                        const trailX = point.x * trailScale + (width - width * trailScale) / 2;
                        const trailY = point.y * trailScale + (height - height * trailScale) / 2;
                        const trailOpacity = opacity * (1 - i / this.trail.length) * 0.25;
                        const trailSize = size * (1 - i / this.trail.length);
                        
                        ctx.beginPath();
                        ctx.arc(trailX, trailY, trailSize * 0.25, 0, Math.PI * 2);
                        ctx.fillStyle = this.hexToRgba(c.main, trailOpacity);
                        ctx.fill();
                    });
                    
                    // UFO glow
                    const glowSize = size * 2.5;
                    const gradient = ctx.createRadialGradient(screenX, screenY, 0, screenX, screenY, glowSize);
                    gradient.addColorStop(0, this.hexToRgba(c.main, opacity * 0.4));
                    gradient.addColorStop(0.5, this.hexToRgba(c.light, opacity * 0.2));
                    gradient.addColorStop(1, this.hexToRgba(c.main, 0));
                    ctx.beginPath();
                    ctx.arc(screenX, screenY, glowSize, 0, Math.PI * 2);
                    ctx.fillStyle = gradient;
                    ctx.fill();
                    
                    // UFO body (saucer shape)
                    ctx.save();
                    ctx.translate(screenX, screenY + Math.sin(this.wobble) * 2);
                    
                    // Bottom dome
                    ctx.beginPath();
                    ctx.ellipse(0, size * 0.15, size * 0.4, size * 0.15, 0, 0, Math.PI);
                    ctx.fillStyle = this.hexToRgba(c.light, opacity);
                    ctx.fill();
                    
                    // Main body
                    ctx.beginPath();
                    ctx.ellipse(0, 0, size * 0.5, size * 0.12, 0, 0, Math.PI * 2);
                    const bodyGradient = ctx.createLinearGradient(-size * 0.5, 0, size * 0.5, 0);
                    bodyGradient.addColorStop(0, this.hexToRgba(c.dark, opacity));
                    bodyGradient.addColorStop(0.5, this.hexToRgba(c.main, opacity));
                    bodyGradient.addColorStop(1, this.hexToRgba(c.dark, opacity));
                    ctx.fillStyle = bodyGradient;
                    ctx.fill();
                    ctx.strokeStyle = this.hexToRgba(c.light, opacity);
                    ctx.lineWidth = 1;
                    ctx.stroke();
                    
                    // Top dome
                    ctx.beginPath();
                    ctx.ellipse(0, -size * 0.08, size * 0.25, size * 0.2, 0, Math.PI, 0);
                    const domeGradient = ctx.createRadialGradient(0, -size * 0.15, 0, 0, -size * 0.1, size * 0.25);
                    domeGradient.addColorStop(0, this.hexToRgba(c.accent, opacity * 0.9));
                    domeGradient.addColorStop(1, this.hexToRgba(c.main, opacity * 0.6));
                    ctx.fillStyle = domeGradient;
                    ctx.fill();
                    
                    // Lights on the rim
                    const numLights = 8;
                    for (let i = 0; i < numLights; i++) {
                        const lightAngle = (i / numLights) * Math.PI * 2 + this.glowPhase * 0.5;
                        const lx = Math.cos(lightAngle) * size * 0.45;
                        const ly = Math.sin(lightAngle) * size * 0.08;
                        
                        const lightIntensity = 0.5 + Math.sin(this.glowPhase + i * 0.8) * 0.5;
                        
                        const lightGlow = ctx.createRadialGradient(lx, ly, 0, lx, ly, size * 0.15);
                        lightGlow.addColorStop(0, this.hexToRgba('#FFFFFF', opacity * lightIntensity));
                        lightGlow.addColorStop(0.5, this.hexToRgba(c.light, opacity * lightIntensity * 0.7));
                        lightGlow.addColorStop(1, this.hexToRgba(c.main, 0));
                        ctx.beginPath();
                        ctx.arc(lx, ly, size * 0.15, 0, Math.PI * 2);
                        ctx.fillStyle = lightGlow;
                        ctx.fill();
                    }
                    
                    ctx.restore();
                }
            }
            
            const ufos = [];
            const numUFOs = 5;
            
            // Initialize UFOs with different colors
            function initUFOs() {
                for (let i = 0; i < numUFOs; i++) {
                    const ufo = new UFO(i % ufoColors.length);
                    ufo.z = 150 + i * 250;
                    ufo.speed = 0.5 + (i % 3) * 0.2;
                    ufos.push(ufo);
                }
            }
            
            // Animation loop
            function animate() {
                ctx.clearRect(0, 0, width, height);
                
                // Draw stars
                stars.forEach(star => {
                    star.update();
                    star.draw();
                });
                
                // Draw UFOs
                ufos.forEach(ufo => {
                    ufo.update();
                    ufo.draw();
                });
                
                requestAnimationFrame(animate);
            }
            
            // Handle window resize
            window.addEventListener('resize', () => {
                resize();
            });
            
            // Start
            init();
            animate();
        })();

        // ===================== LIVE FEED =====================
        const feedData = [
            { name: 'RajaGaming', avatar: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M2 4l3 12h14l3-12-6 7-4-7-4 7-6-7zm3 16h14"/></svg>', amount: 'Rp 100.000', message: 'GG streamer, semangat terus!', badge: 'super', color: 'linear-gradient(135deg, #FFD700, #FFA500)', badgeText: '<svg width="10" height="10" viewBox="0 0 24 24" fill="white" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>' },
            { name: 'ThunderX', avatar: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>', amount: 'Rp 50.000', message: 'W keras!', badge: 'member', color: 'linear-gradient(135deg, #7C3AED, #5B21B6)', badgeText: '<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>' },
            { name: 'MoonChan', avatar: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>', amount: 'Rp 25.000', message: 'Selamat streaming!', badge: 'donate', color: 'linear-gradient(135deg, #EC4899, #F43F5E)', badgeText: '<svg width="10" height="10" viewBox="0 0 24 24" fill="white" stroke="none"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>' },
            { name: 'GamerPro99', avatar: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><line x1="6" y1="12" x2="10" y2="12"/><line x1="8" y1="10" x2="8" y2="14"/><circle cx="17" cy="10" r="1" fill="white"/><circle cx="15" cy="12" r="1" fill="white"/></svg>', amount: 'Rp 75.000', message: 'Main apa nih?', badge: 'super', color: 'linear-gradient(135deg, #FFD700, #FFA500)', badgeText: '<svg width="10" height="10" viewBox="0 0 24 24" fill="white" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>' },
            { name: 'SakuraStream', avatar: '<svg width="24" height="24" viewBox="0 0 24 24" fill="white" stroke="none"><circle cx="12" cy="12" r="3"/><circle cx="12" cy="5" r="2.5"/><circle cx="12" cy="19" r="2.5"/><circle cx="5" cy="12" r="2.5"/><circle cx="19" cy="12" r="2.5"/><circle cx="7" cy="7" r="2"/><circle cx="17" cy="7" r="2"/><circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/></svg>', amount: 'Rp 30.000', message: 'Cantik stream-nya!', badge: 'donate', color: 'linear-gradient(135deg, #EC4899, #F43F5E)', badgeText: '<svg width="10" height="10" viewBox="0 0 24 24" fill="white" stroke="none"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>' },
            { name: 'VoltCaster', avatar: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>', amount: 'Rp 150.000', message: 'TOP UP GAN!', badge: 'super', color: 'linear-gradient(135deg, #FFD700, #FFA500)', badgeText: '<svg width="10" height="10" viewBox="0 0 24 24" fill="white" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>' },
            { name: 'DragonSlayer', avatar: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>', amount: 'Rp 20.000', message: 'Nice play!', badge: 'donate', color: 'linear-gradient(135deg, #EC4899, #F43F5E)', badgeText: '<svg width="10" height="10" viewBox="0 0 24 24" fill="white" stroke="none"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>' },
            { name: 'NinjaGamer', avatar: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><circle cx="12" cy="10" r="7"/><path d="M12 17v4"/><path d="M8 21h8"/></svg>', amount: 'Rp 200.000', message: 'RAHASIA TERBONGKAR!', badge: 'super', color: 'linear-gradient(135deg, #FFD700, #FFA500)', badgeText: '<svg width="10" height="10" viewBox="0 0 24 24" fill="white" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>' },
        ];

        const feedList = document.getElementById('feed-list');
        let totalAmount = 2450000;

        function createFeedItem(item, isNew = false) {
            const div = document.createElement('div');
            div.className = 'feed-item';
            div.innerHTML = `
                <div class="feed-avatar" style="background: ${item.color}">
                    ${item.avatar}
                    <div class="feed-badge" style="background: ${item.badge === 'super' ? '#FFD700' : item.badge === 'member' ? '#7C3AED' : '#EC4899'}">${item.badgeText}</div>
                </div>
                <div class="feed-content">
                    <div class="feed-name-row">
                        <span class="feed-name">${item.name}</span>
                        ${item.badge === 'super' ? '<span class="feed-badge-type" style="background:#FFD700;color:#000">SUPER</span>' : ''}
                    </div>
                    <div class="feed-amount">${item.amount}</div>
                    <div class="feed-message">"${item.message}"</div>
                </div>
                <span class="feed-time">Baru</span>
            `;
            return div;
        }

        // Initial load - show 5 items
        feedData.slice(0, 5).forEach(item => {
            feedList.appendChild(createFeedItem(item));
        });

        // Add new item every 3 seconds
        function addNewFeedItem() {
            const randomItem = feedData[Math.floor(Math.random() * feedData.length)];
            const newItem = createFeedItem(randomItem, true);
            
            feedList.insertBefore(newItem, feedList.firstChild);
            
            // Update total
            const amount = parseInt(randomItem.amount.replace(/[^0-9]/g, ''));
            totalAmount += amount;
            document.getElementById('total-feed').textContent = 'Rp ' + totalAmount.toLocaleString('id-ID');
            
            // Keep only 6 items
            while (feedList.children.length > 6) {
                feedList.removeChild(feedList.lastChild);
            }
        }

        setInterval(addNewFeedItem, 3000);

        // ===================== TESTIMONIALS =====================
        const testimonials = [
            { avatar: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><rect x="2" y="6" width="20" height="12" rx="2"/><line x1="6" y1="12" x2="10" y2="12"/><line x1="8" y1="10" x2="8" y2="14"/><circle cx="17" cy="10" r="1" fill="white"/><circle cx="15" cy="12" r="1" fill="white"/></svg>', name: 'ZephyrGaming', handle: '@zephyrgaming', stars: '<svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>', text: 'Tiptipan benar-benar game changer! Alert-nya keren.', bg: 'rgba(124,58,237,0.25)' },
            { avatar: '<svg width="24" height="24" viewBox="0 0 24 24" fill="white" stroke="none"><circle cx="12" cy="12" r="3"/><circle cx="12" cy="5" r="2.5"/><circle cx="12" cy="19" r="2.5"/><circle cx="5" cy="12" r="2.5"/><circle cx="19" cy="12" r="2.5"/></svg>', name: 'SakuraStream', handle: '@sakurastream', stars: '<svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>', text: 'Dashboard-nya clean dan informatif. Recommend!', bg: 'rgba(236,72,153,0.25)' },
            { avatar: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>', name: 'VoltCaster', handle: '@voltcaster', stars: '<svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>', text: 'Filter kata kasarnya TOP! Stream jadi aman.', bg: 'rgba(6,182,212,0.25)' },
            { avatar: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>', name: 'DragonSlayer', handle: '@dragonslayer', stars: '<svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>', text: 'Export CSV buat laporan pajak mudah banget.', bg: 'rgba(16,185,129,0.25)' },
            { avatar: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><circle cx="12" cy="10" r="7"/><path d="M12 17v4"/><path d="M8 21h8"/></svg>', name: 'NinjaGamer', handle: '@ninjagamer', stars: '<svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>', text: 'OBS widget-nya stabil, nunca ada masalah.', bg: 'rgba(245,158,11,0.25)' },
            { avatar: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>', name: 'MoonChan', handle: '@moonchan', stars: '<svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>', text: 'Fitur subathon membantu buat marathon streaming!', bg: 'rgba(124,58,237,0.25)' },
            { avatar: '<svg width="24" height="24" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>', name: 'StarGazer', handle: '@stargazer', stars: '<svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>', text: 'Gratis tapi fiturnya lengkap. Worth it!', bg: 'rgba(236,72,153,0.25)' },
            { avatar: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>', name: 'CatLover', handle: '@catlover', stars: '<svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>', text: 'Support timnya responsif dan helpful.', bg: 'rgba(6,182,212,0.25)' },
            { avatar: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/></svg>', name: 'FireStream', handle: '@firestream', stars: '<svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>', text: 'Canvas editor-nya kreatif banget.', bg: 'rgba(16,185,129,0.25)' },
            { avatar: '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>', name: 'ProGamer', handle: '@progamer', stars: '<svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg><svg width="14" height="14" viewBox="0 0 24 24" fill="#FFD700" stroke="none"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>', text: 'Tiptipan terbaik untuk streamer Indo!', bg: 'rgba(245,158,11,0.25)' },
        ];

        const testimonialsTrack = document.getElementById('testimonials-scroll');
        
        // Duplicate for infinite scroll
        [...testimonials, ...testimonials].forEach(t => {
            const card = document.createElement('div');
            card.className = 'testimonial-card';
            card.innerHTML = `
                <div class="testimonial-user">
                    <div class="testimonial-avatar" style="background: ${t.bg}">${t.avatar}</div>
                    <div class="testimonial-info">
                        <strong>${t.name}</strong>
                        <span>${t.handle}</span>
                    </div>
                </div>
                <div class="testimonial-stars">${t.stars}</div>
                <p class="testimonial-text">"${t.text}"</p>
            `;
            testimonialsTrack.appendChild(card);
        });

        // ===================== MOUSE PARALLAX =====================
        document.addEventListener('mousemove', (e) => {
            const x = (e.clientX / window.innerWidth - 0.5) * 10;
            const y = (e.clientY / window.innerHeight - 0.5) * 10;
            document.querySelector('.bg-gradient').style.transform = `translate(${x}px, ${y}px)`;
        });

        // ===================== FAQ ACCORDION =====================
        document.querySelectorAll('.faq-question').forEach(btn => {
            btn.addEventListener('click', () => {
                const item = btn.parentElement;
                const isActive = item.classList.contains('active');
                
                // Close all
                document.querySelectorAll('.faq-item').forEach(i => {
                    i.classList.remove('active');
                });
                
                // Toggle clicked
                if (!isActive) {
                    item.classList.add('active');
                }
            });
        });

        // ===================== STATS COUNTER =====================
        const statsObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counters = entry.target.querySelectorAll('.stat-number');
                    counters.forEach(counter => {
                        const target = parseInt(counter.dataset.target);
                        const suffix = counter.dataset.suffix || '';
                        const duration = 2000;
                        const step = target / (duration / 16);
                        let current = 0;
                        
                        const formatNumber = (num) => {
                            if (num >= 1000000) {
                                return (num / 1000000).toFixed(0) + 'M';
                            } else if (num >= 1000) {
                                return (num / 1000).toFixed(0) + 'K';
                            }
                            return Math.floor(num).toLocaleString('id-ID');
                        };
                        
                        const updateCounter = () => {
                            current += step;
                            if (current < target) {
                                counter.textContent = formatNumber(current);
                                requestAnimationFrame(updateCounter);
                            } else {
                                counter.textContent = formatNumber(target);
                            }
                        };
                        
                        updateCounter();
                    });
                    statsObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.5 });

        const statsSection = document.querySelector('.stats-section');
        if (statsSection) {
            statsObserver.observe(statsSection);
        }

        // ===================== PAGE TRANSITION - SIMPLE =====================
        (function() {
            const pageContent = document.getElementById('page-content');
            let isTransitioning = false;
            
            function startTransition(targetUrl) {
                if (isTransitioning) return;
                isTransitioning = true;
                
                // Smoothly speed up stars
                window.starfieldSpeed.setTarget(window.starfieldSpeed.fast);
                
                // Fade out content
                pageContent.classList.add('fading');
                
                // Navigate after fade completes (give more time for smooth acceleration)
                setTimeout(() => {
                    window.location.href = targetUrl;
                }, 800);
            }
            
            // Intercept auth links
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a[href*="/login"], a[href*="/register"]');
                
                if (link) {
                    e.preventDefault();
                    const href = link.getAttribute('href');
                    startTransition(href);
                }
            });
        })();
    </script>
</body>
</html>
