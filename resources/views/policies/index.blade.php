<x-landing-layout title="Kebijakan & Ketentuan">
    @push('styles')
    <style>
        /* ── Hero Section ── */
        .policies-hero {
            text-align: center;
            padding: 60px 0 40px;
            position: relative;
        }
        .policies-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 600px;
            height: 300px;
            background: radial-gradient(ellipse at center, rgba(124,108,252,0.15) 0%, transparent 70%);
            pointer-events: none;
            z-index: -1;
        }
        .policies-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(124,108,252,0.1);
            border: 1px solid rgba(124,108,252,0.2);
            border-radius: 100px;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 600;
            color: var(--brand-light);
            margin-bottom: 20px;
        }
        .policies-badge .iconify {
            width: 14px;
            height: 14px;
        }
        .policies-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 36px;
            font-weight: 800;
            letter-spacing: -1px;
            margin-bottom: 12px;
            background: linear-gradient(135deg, #fff 0%, var(--brand-light) 50%, #ec4899 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .policies-subtitle {
            font-size: 15px;
            color: var(--text-3);
            max-width: 500px;
            margin: 0 auto;
            line-height: 1.6;
        }

        /* ── Policy Categories ── */
        .policy-section {
            margin-bottom: 40px;
        }
        .policy-section-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 14px;
            font-weight: 700;
            color: var(--text-2);
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .policy-section-title .iconify {
            width: 18px;
            height: 18px;
            color: var(--brand-light);
        }
        .policy-section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, var(--border), transparent);
            margin-left: 12px;
        }

        /* ── Policy Grid ── */
        .policy-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }
        @media(max-width: 1024px) {
            .policy-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media(max-width: 640px) {
            .policy-grid { grid-template-columns: 1fr; }
            .policies-title { font-size: 28px; }
        }

        /* ── Policy Card ── */
        .policy-card {
            position: relative;
            display: flex;
            flex-direction: column;
            padding: 24px;
            background: var(--glass-bg);
            backdrop-filter: blur(12px) saturate(180%);
            -webkit-backdrop-filter: blur(12px) saturate(180%);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            text-decoration: none;
            transition: all .3s ease;
            overflow: hidden;
        }
        .policy-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--brand), var(--purple));
            opacity: 0;
            transition: opacity .3s;
        }
        .policy-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: radial-gradient(ellipse at top left, rgba(124,108,252,0.08) 0%, transparent 50%);
            pointer-events: none;
            opacity: 0;
            transition: opacity .3s;
        }
        .policy-card:hover {
            border-color: rgba(124,108,252,0.4);
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.3), 0 0 30px rgba(124,108,252,0.1);
        }
        .policy-card:hover::before {
            opacity: 1;
        }
        .policy-card:hover::after {
            opacity: 1;
        }

        .policy-card-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: rgba(124,108,252,0.1);
            border: 1px solid rgba(124,108,252,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            transition: all .3s;
        }
        .policy-card:hover .policy-card-icon {
            background: rgba(124,108,252,0.2);
            border-color: rgba(124,108,252,0.4);
            transform: scale(1.1);
        }
        .policy-card-icon .iconify {
            width: 22px;
            height: 22px;
            color: var(--brand-light);
        }

        .policy-card-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 8px;
            transition: color .3s;
        }
        .policy-card:hover .policy-card-title {
            color: #fff;
        }

        .policy-card-desc {
            font-size: 12px;
            color: var(--text-3);
            line-height: 1.6;
            flex: 1;
        }

        .policy-card-arrow {
            position: absolute;
            bottom: 20px;
            right: 20px;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(255,255,255,0.05);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .3s;
        }
        .policy-card:hover .policy-card-arrow {
            background: rgba(124,108,252,0.2);
        }
        .policy-card-arrow .iconify {
            width: 16px;
            height: 16px;
            color: var(--text-3);
            transition: all .3s;
        }
        .policy-card:hover .policy-card-arrow .iconify {
            color: var(--brand-light);
            transform: translateX(3px);
        }

        /* ── Need Help Section ── */
        .need-help {
            background: linear-gradient(135deg, rgba(124,108,252,0.08) 0%, rgba(168,85,247,0.04) 100%);
            border: 1px solid rgba(124,108,252,0.2);
            border-radius: var(--radius-xl);
            padding: 32px;
            text-align: center;
            margin-top: 20px;
        }
        .need-help-icon {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: rgba(124,108,252,0.15);
            border: 1px solid rgba(124,108,252,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }
        .need-help-icon .iconify {
            width: 28px;
            height: 28px;
            color: var(--brand-light);
        }
        .need-help-title {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 18px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 8px;
        }
        .need-help-desc {
            font-size: 13px;
            color: var(--text-3);
            margin-bottom: 20px;
        }
        .need-help-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: linear-gradient(135deg, var(--brand) 0%, var(--purple) 100%);
            border: none;
            border-radius: var(--radius);
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all .3s;
            box-shadow: 0 4px 20px rgba(124,108,252,0.3);
        }
        .need-help-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(124,108,252,0.4);
            color: #fff;
        }
        .need-help-btn .iconify {
            width: 18px;
            height: 18px;
        }

        /* ── Stats Row ── */
        .policies-stats {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin: 30px 0 50px;
        }
        .policies-stat {
            text-align: center;
        }
        .policies-stat-value {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 28px;
            font-weight: 800;
            color: var(--brand-light);
            line-height: 1;
        }
        .policies-stat-label {
            font-size: 11px;
            color: var(--text-3);
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        @media(max-width: 640px) {
            .policies-stats {
                gap: 24px;
            }
            .policies-stat-value {
                font-size: 22px;
            }
        }
    </style>
    @endpush

    <div class="page-container">
        <!-- Hero Section -->
        <div class="policies-hero">
            <div class="policies-badge">
                <span class="iconify" data-icon="solar:document-text-bold-duotone"></span>
                Legal & Compliance
            </div>
            <h1 class="policies-title">Kebijakan & Ketentuan</h1>
            <p class="policies-subtitle">
                Transparansi adalah prioritas kami. Pelajari bagaimana kami melindungi data dan hak Anda.
            </p>
        </div>

        <!-- Stats -->
        <div class="policies-stats">
            <div class="policies-stat">
                <div class="policies-stat-value">14</div>
                <div class="policies-stat-label">Dokumen</div>
            </div>
            <div class="policies-stat">
                <div class="policies-stat-value">100%</div>
                <div class="policies-stat-label">Transparan</div>
            </div>
            <div class="policies-stat">
                <div class="policies-stat-value">24/7</div>
                <div class="policies-stat-label">Support</div>
            </div>
        </div>

        <!-- Section 1: Keamanan & Privasi -->
        <div class="policy-section">
            <div class="policy-section-title">
                <span class="iconify" data-icon="solar:shield-check-bold-duotone"></span>
                Keamanan & Privasi
            </div>
            <div class="policy-grid">
                <a href="{{ route('policies.show', 'privacy-policy') }}" class="policy-card">
                    <div class="policy-card-icon">
                        <span class="iconify" data-icon="solar:lock-bold-duotone"></span>
                    </div>
                    <div class="policy-card-title">Kebijakan Privasi</div>
                    <div class="policy-card-desc">Bagaimana kami mengumpulkan, menggunakan, dan melindungi data pribadi Anda.</div>
                    <div class="policy-card-arrow">
                        <span class="iconify" data-icon="solar:alt-arrow-right-bold"></span>
                    </div>
                </a>
                <a href="{{ route('policies.show', 'security-policy') }}" class="policy-card">
                    <div class="policy-card-icon">
                        <span class="iconify" data-icon="solar:shield-bold-duotone"></span>
                    </div>
                    <div class="policy-card-title">Keamanan Data</div>
                    <div class="policy-card-desc">Standar enkripsi dan langkah keamanan yang kami terapkan.</div>
                    <div class="policy-card-arrow">
                        <span class="iconify" data-icon="solar:alt-arrow-right-bold"></span>
                    </div>
                </a>
                <a href="{{ route('policies.show', 'cookie-policy') }}" class="policy-card">
                    <div class="policy-card-icon">
                        <span class="iconify" data-icon="solar:cookie-bold-duotone"></span>
                    </div>
                    <div class="policy-card-title">Kebijakan Cookie</div>
                    <div class="policy-card-desc">Penggunaan cookie untuk meningkatkan pengalaman browsing Anda.</div>
                    <div class="policy-card-arrow">
                        <span class="iconify" data-icon="solar:alt-arrow-right-bold"></span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Section 2: Layanan & Transaksi -->
        <div class="policy-section">
            <div class="policy-section-title">
                <span class="iconify" data-icon="solar:dollar-banner-bold-duotone"></span>
                Layanan & Transaksi
            </div>
            <div class="policy-grid">
                <a href="{{ route('policies.show', 'terms-of-service') }}" class="policy-card">
                    <div class="policy-card-icon">
                        <span class="iconify" data-icon="solar:document-bold-duotone"></span>
                    </div>
                    <div class="policy-card-title">Ketentuan Layanan</div>
                    <div class="policy-card-desc">Syarat dan ketentuan penggunaan platform Tiptipan.</div>
                    <div class="policy-card-arrow">
                        <span class="iconify" data-icon="solar:alt-arrow-right-bold"></span>
                    </div>
                </a>
                <a href="{{ route('policies.show', 'donation-policy') }}" class="policy-card">
                    <div class="policy-card-icon">
                        <span class="iconify" data-icon="solar:heart-bold-duotone"></span>
                    </div>
                    <div class="policy-card-title">Kebijakan Donasi</div>
                    <div class="policy-card-desc">Aturan dan proses donasi di platform kami.</div>
                    <div class="policy-card-arrow">
                        <span class="iconify" data-icon="solar:alt-arrow-right-bold"></span>
                    </div>
                </a>
                <a href="{{ route('policies.show', 'payment-policy') }}" class="policy-card">
                    <div class="policy-card-icon">
                        <span class="iconify" data-icon="solar:credit-card-bold-duotone"></span>
                    </div>
                    <div class="policy-card-title">Kebijakan Pembayaran</div>
                    <div class="policy-card-desc">Metode pembayaran dan proses transaksi yang didukung.</div>
                    <div class="policy-card-arrow">
                        <span class="iconify" data-icon="solar:alt-arrow-right-bold"></span>
                    </div>
                </a>
                <a href="{{ route('policies.show', 'refund-policy') }}" class="policy-card">
                    <div class="policy-card-icon">
                        <span class="iconify" data-icon="solar:rewind-back-bold-duotone"></span>
                    </div>
                    <div class="policy-card-title">Pengembalian Dana</div>
                    <div class="policy-card-desc">Prosedur dan kebijakan refund donasi.</div>
                    <div class="policy-card-arrow">
                        <span class="iconify" data-icon="solar:alt-arrow-right-bold"></span>
                    </div>
                </a>
                <a href="{{ route('policies.show', 'fee-policy') }}" class="policy-card">
                    <div class="policy-card-icon">
                        <span class="iconify" data-icon="solar:chart-bar-bold-duotone"></span>
                    </div>
                    <div class="policy-card-title">Transparansi Biaya</div>
                    <div class="policy-card-desc">Detail komisi dan biaya platform secara transparan.</div>
                    <div class="policy-card-arrow">
                        <span class="iconify" data-icon="solar:alt-arrow-right-bold"></span>
                    </div>
                </a>
                <a href="{{ route('policies.show', 'product-policy') }}" class="policy-card">
                    <div class="policy-card-icon">
                        <span class="iconify" data-icon="solar:box-bold-duotone"></span>
                    </div>
                    <div class="policy-card-title">Produk Digital</div>
                    <div class="policy-card-desc">Kebijakan terkait produk digital dan layanan premium.</div>
                    <div class="policy-card-arrow">
                        <span class="iconify" data-icon="solar:alt-arrow-right-bold"></span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Section 3: Komunitas & Konten -->
        <div class="policy-section">
            <div class="policy-section-title">
                <span class="iconify" data-icon="solar:users-group-two-bold-duotone"></span>
                Komunitas & Konten
            </div>
            <div class="policy-grid">
                <a href="{{ route('policies.show', 'content-policy') }}" class="policy-card">
                    <div class="policy-card-icon">
                        <span class="iconify" data-icon="solar:film-bold-duotone"></span>
                    </div>
                    <div class="policy-card-title">Panduan Konten</div>
                    <div class="policy-card-desc">Standar konten yang diizinkan di platform kami.</div>
                    <div class="policy-card-arrow">
                        <span class="iconify" data-icon="solar:alt-arrow-right-bold"></span>
                    </div>
                </a>
                <a href="{{ route('policies.show', 'fraud-policy') }}" class="policy-card">
                    <div class="policy-card-icon">
                        <span class="iconify" data-icon="solar:shield-warning-bold-duotone"></span>
                    </div>
                    <div class="policy-card-title">Kebijakan Anti-Fraud</div>
                    <div class="policy-card-desc">Pencegahan dan penanganan aktivitas penipuan.</div>
                    <div class="policy-card-arrow">
                        <span class="iconify" data-icon="solar:alt-arrow-right-bold"></span>
                    </div>
                </a>
                <a href="{{ route('policies.show', 'ip-policy') }}" class="policy-card">
                    <div class="policy-card-icon">
                        <span class="iconify" data-icon="solar:copyright-bold-duotone"></span>
                    </div>
                    <div class="policy-card-title">Hak Kekayaan Intelektual</div>
                    <div class="policy-card-desc">Perlindungan hak cipta dan kekayaan intelektual.</div>
                    <div class="policy-card-arrow">
                        <span class="iconify" data-icon="solar:alt-arrow-right-bold"></span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Section 4: Lainnya -->
        <div class="policy-section">
            <div class="policy-section-title">
                <span class="iconify" data-icon="solar:folder-bold-duotone"></span>
                Lainnya
            </div>
            <div class="policy-grid">
                <a href="{{ route('policies.show', 'tax-policy') }}" class="policy-card">
                    <div class="policy-card-icon">
                        <span class="iconify" data-icon="solar:calculator-bold-duotone"></span>
                    </div>
                    <div class="policy-card-title">Kebijakan Pajak</div>
                    <div class="policy-card-desc">Informasi terkait perpajakan donasi dan pendapatan.</div>
                    <div class="policy-card-arrow">
                        <span class="iconify" data-icon="solar:alt-arrow-right-bold"></span>
                    </div>
                </a>
                <a href="{{ route('policies.show', 'age-policy') }}" class="policy-card">
                    <div class="policy-card-icon">
                        <span class="iconify" data-icon="solar:users-plus-bold-duotone"></span>
                    </div>
                    <div class="policy-card-title">Batasan Usia</div>
                    <div class="policy-card-desc">Persyaratan usia minimum untuk pengguna.</div>
                    <div class="policy-card-arrow">
                        <span class="iconify" data-icon="solar:alt-arrow-right-bold"></span>
                    </div>
                </a>
            </div>
        </div>

        <!-- Need Help Section -->
        <div class="need-help">
            <div class="need-help-icon">
                <span class="iconify" data-icon="solar:question-circle-bold-duotone"></span>
            </div>
            <div class="need-help-title">Butuh Bantuan?</div>
            <div class="need-help-desc">
                Tim support kami siap membantu 24/7 untuk setiap pertanyaan terkait kebijakan kami.
            </div>
            <a href="mailto:support@tiptipan.id" class="need-help-btn">
                <span class="iconify" data-icon="solar:mail-bold-duotone"></span>
                Hubungi Support
            </a>
        </div>
    </div>

    <x-site-footer />
</x-app-layout>
