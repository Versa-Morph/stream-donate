@props(['title' => 'Policy', 'slug' => ''])

<x-landing-layout :title="$title">
    @push('styles')
    <style>
        /* ── Hero Section ── */
        .policy-hero {
            padding: 50px 0 30px;
            text-align: center;
            position: relative;
        }
        .policy-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 500px;
            height: 250px;
            background: radial-gradient(ellipse at center, rgba(124,108,252,0.12) 0%, transparent 70%);
            pointer-events: none;
            z-index: -1;
        }
        .policy-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: var(--text-3);
            font-size: 13px;
            margin-bottom: 24px;
            text-decoration: none;
            transition: color .2s;
            padding: 8px 14px;
            background: var(--surface-2);
            border-radius: var(--radius);
            border: 1px solid var(--border);
        }
        .policy-back:hover {
            color: var(--brand-light);
            border-color: rgba(124,108,252,0.3);
            background: var(--surface-3);
        }
        .policy-back .iconify {
            width: 16px;
            height: 16px;
        }
        .policy-hero-icon {
            width: 64px;
            height: 64px;
            border-radius: 18px;
            background: linear-gradient(135deg, rgba(124,108,252,0.15), rgba(168,85,247,0.1));
            border: 1px solid rgba(124,108,252,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .policy-hero-icon .iconify {
            width: 32px;
            height: 32px;
            color: var(--brand-light);
        }
        .policy-hero-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 32px;
            font-weight: 800;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #fff 0%, var(--brand-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .policy-hero-meta {
            font-size: 13px;
            color: var(--text-3);
        }

        /* ── Policy Content Card ── */
        .policy-card {
            background: var(--glass-bg);
            backdrop-filter: blur(12px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            overflow: hidden;
            box-shadow: var(--glass-shadow);
            margin-bottom: 40px;
        }
        .policy-card-header {
            padding: 24px 28px;
            background: linear-gradient(135deg, rgba(124,108,252,0.08) 0%, rgba(168,85,247,0.04) 100%);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .policy-card-header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .policy-card-header-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: rgba(124,108,252,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .policy-card-header-icon .iconify {
            width: 20px;
            height: 20px;
            color: var(--brand-light);
        }
        .policy-card-header-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 14px;
            font-weight: 700;
            color: var(--text);
        }
        .policy-card-header-badge {
            font-size: 11px;
            color: var(--text-3);
            margin-top: 2px;
        }
        .policy-card-update {
            font-size: 12px;
            color: var(--text-3);
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .policy-card-update .iconify {
            width: 14px;
            height: 14px;
        }

        .policy-card-body {
            padding: 32px;
        }

        /* ── Policy Typography ── */
        .policy-content h2 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--text);
            margin: 32px 0 16px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .policy-content h2:first-child {
            margin-top: 0;
        }
        .policy-content h2 .iconify {
            width: 20px;
            height: 20px;
            color: var(--brand-light);
        }
        .policy-content h3 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
            margin: 24px 0 12px;
        }
        .policy-content p {
            font-size: 14px;
            color: var(--text-2);
            line-height: 1.8;
            margin-bottom: 16px;
        }
        .policy-content ul, .policy-content ol {
            margin: 12px 0 20px 20px;
            color: var(--text-2);
            font-size: 14px;
            line-height: 1.8;
        }
        .policy-content li {
            margin-bottom: 10px;
            padding-left: 8px;
        }
        .policy-content li::marker {
            color: var(--brand-light);
        }
        .policy-content strong {
            color: var(--text);
            font-weight: 600;
        }
        .policy-content a {
            color: var(--brand-light);
            text-decoration: none;
            border-bottom: 1px dashed rgba(124,108,252,0.4);
            transition: all .2s;
        }
        .policy-content a:hover {
            border-bottom-color: var(--brand-light);
        }

        /* ── Info Box ── */
        .policy-info-box {
            background: linear-gradient(135deg, rgba(124,108,252,0.08) 0%, rgba(6,182,212,0.04) 100%);
            border: 1px solid rgba(124,108,252,0.2);
            border-radius: var(--radius);
            padding: 16px 20px;
            margin: 20px 0;
            display: flex;
            gap: 14px;
        }
        .policy-info-box-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(124,108,252,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .policy-info-box-icon .iconify {
            width: 18px;
            height: 18px;
            color: var(--brand-light);
        }
        .policy-info-box-content {
            flex: 1;
        }
        .policy-info-box-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 4px;
        }
        .policy-info-box-text {
            font-size: 12px;
            color: var(--text-3);
            line-height: 1.6;
        }

        /* ── Warning Box ── */
        .policy-warning-box {
            background: rgba(251,191,36,0.08);
            border: 1px solid rgba(251,191,36,0.2);
            border-radius: var(--radius);
            padding: 16px 20px;
            margin: 20px 0;
            display: flex;
            gap: 14px;
        }
        .policy-warning-box-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: rgba(251,191,36,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .policy-warning-box-icon .iconify {
            width: 18px;
            height: 18px;
            color: var(--yellow);
        }
        .policy-warning-box-content {
            flex: 1;
        }
        .policy-warning-box-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--yellow);
            margin-bottom: 4px;
        }
        .policy-warning-box-text {
            font-size: 12px;
            color: var(--text-3);
            line-height: 1.6;
        }

        /* ── Table of Contents ── */
        .policy-toc {
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 20px 24px;
            margin-bottom: 28px;
        }
        .policy-toc-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .policy-toc-title .iconify {
            width: 16px;
            height: 16px;
            color: var(--brand-light);
        }
        .policy-toc-list {
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .policy-toc-list li {
            margin-bottom: 8px;
        }
        .policy-toc-list a {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--text-3);
            text-decoration: none;
            padding: 6px 10px;
            border-radius: var(--radius-sm);
            transition: all .15s;
        }
        .policy-toc-list a:hover {
            color: var(--brand-light);
            background: rgba(124,108,252,0.08);
        }
        .policy-toc-list .iconify {
            width: 14px;
            height: 14px;
        }

        /* ── Contact Section ── */
        .policy-contact {
            background: linear-gradient(135deg, rgba(34,211,160,0.08) 0%, rgba(6,182,212,0.04) 100%);
            border: 1px solid rgba(34,211,160,0.2);
            border-radius: var(--radius-lg);
            padding: 24px;
            margin-top: 32px;
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .policy-contact-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: rgba(34,211,160,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .policy-contact-icon .iconify {
            width: 26px;
            height: 26px;
            color: var(--green);
        }
        .policy-contact-content {
            flex: 1;
        }
        .policy-contact-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 4px;
        }
        .policy-contact-text {
            font-size: 13px;
            color: var(--text-3);
            margin-bottom: 12px;
        }
        .policy-contact-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 18px;
            background: var(--green);
            border: none;
            border-radius: var(--radius);
            color: #000;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: all .2s;
        }
        .policy-contact-btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
            color: #000;
        }
        .policy-contact-btn .iconify {
            width: 16px;
            height: 16px;
        }

        @media (max-width: 768px) {
            .policy-hero-title { font-size: 24px; }
            .policy-card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
            .policy-card-body { padding: 24px 20px; }
            .policy-contact {
                flex-direction: column;
                text-align: center;
            }
        }
    </style>
    @endpush

    <div class="page-container narrow">
        <!-- Hero Section -->
        <div class="policy-hero">
            <a href="{{ route('policies.index') }}" class="policy-back">
                <span class="iconify" data-icon="solar:alt-arrow-left-bold"></span>
                Kembali ke Kebijakan
            </a>
            <div class="policy-hero-icon">
                <span class="iconify" data-icon="solar:document-text-bold-duotone"></span>
            </div>
            <h1 class="policy-hero-title">{{ $title }}</h1>
            <p class="policy-hero-meta">Terakhir diperbarui: Maret 2026</p>
        </div>

        <!-- Policy Card -->
        <div class="policy-card">
            <div class="policy-card-header">
                <div class="policy-card-header-left">
                    <div class="policy-card-header-icon">
                        <span class="iconify" data-icon="solar:info-circle-bold-duotone"></span>
                    </div>
                    <div>
                        <div class="policy-card-header-title">{{ $title }}</div>
                        <div class="policy-card-header-badge">Dokumen Resmi Tiptipan</div>
                    </div>
                </div>
                <div class="policy-card-update">
                    <span class="iconify" data-icon="solar:calendar-bold-duotone"></span>
                    Maret 2026
                </div>
            </div>
            <div class="policy-card-body">
                <div class="policy-content">
                    @if($slug)
                        @include('policies.content.' . $slug)
                    @else
                        <p>Kebijakan tidak ditemukan.</p>
                    @endif
                </div>

                <!-- Contact Section -->
                <div class="policy-contact">
                    <div class="policy-contact-icon">
                        <span class="iconify" data-icon="solar:headphones-bold-duotone"></span>
                    </div>
                    <div class="policy-contact-content">
                        <div class="policy-contact-title">Ada pertanyaan?</div>
                        <div class="policy-contact-text">
                            Tim support kami siap membantu untuk setiap pertanyaan terkait kebijakan ini.
                        </div>
                        <a href="mailto:support@tiptipan.id" class="policy-contact-btn">
                            <span class="iconify" data-icon="solar:mail-bold-duotone"></span>
                            Hubungi Support
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-site-footer />
</x-app-layout>
