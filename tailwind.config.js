import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Space Grotesk', 'Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
                display: ['Space Grotesk', 'Inter', ...defaultTheme.fontFamily.sans],
                body: ['Plus Jakarta Sans', 'Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Dark mode base colors
                dark: {
                    50: '#f8f9fa',
                    100: '#e9ecef',
                    200: '#dee2e6',
                    300: '#ced4da',
                    400: '#adb5bd',
                    500: '#6c757d',
                    600: '#495057',
                    700: '#343a40',
                    800: '#212529',
                    900: '#141419',
                    950: '#070709',
                },
                // Brand purple with variations
                brand: {
                    50: '#f5f3ff',
                    100: '#ede9fe',
                    200: '#ddd6fe',
                    300: '#c4b5fd',
                    400: '#a78bfa',
                    500: '#7c6cfc',
                    600: '#6d5aed',
                    700: '#5b47d1',
                    800: '#4a38b0',
                    900: '#3d2e8f',
                },
                // Accent colors
                neon: {
                    purple: '#7c6cfc',
                    pink: '#ff6ac7',
                    blue: '#4facfe',
                    cyan: '#00f2fe',
                    green: '#22d3a0',
                    orange: '#f97316',
                    red: '#ff3860',
                    yellow: '#ffd93d',
                },
                // Glass effect colors
                glass: {
                    white: 'rgba(255, 255, 255, 0.1)',
                    dark: 'rgba(0, 0, 0, 0.2)',
                    purple: 'rgba(124, 108, 252, 0.1)',
                },
            },
            backdropBlur: {
                xs: '2px',
            },
            boxShadow: {
                'glass': '0 8px 32px 0 rgba(0, 0, 0, 0.37)',
                'glass-lg': '0 12px 48px 0 rgba(0, 0, 0, 0.5)',
                'glow-sm': '0 0 10px rgba(124, 108, 252, 0.5)',
                'glow': '0 0 20px rgba(124, 108, 252, 0.6)',
                'glow-lg': '0 0 30px rgba(124, 108, 252, 0.7)',
                'neon-purple': '0 0 20px rgba(124, 108, 252, 0.8)',
                'neon-pink': '0 0 20px rgba(255, 106, 199, 0.8)',
                'neon-blue': '0 0 20px rgba(79, 172, 254, 0.8)',
                'neon-green': '0 0 20px rgba(34, 211, 160, 0.8)',
            },
            animation: {
                'fade-in': 'fadeIn 0.3s ease-in-out',
                'fade-in-up': 'fadeInUp 0.5s ease-out',
                'slide-in-right': 'slideInRight 0.3s ease-out',
                'slide-in-left': 'slideInLeft 0.3s ease-out',
                'scale-in': 'scaleIn 0.2s ease-out',
                'glow-pulse': 'glowPulse 2s ease-in-out infinite',
                'float': 'float 3s ease-in-out infinite',
                'gradient-shift': 'gradientShift 3s ease infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideInRight: {
                    '0%': { transform: 'translateX(100%)' },
                    '100%': { transform: 'translateX(0)' },
                },
                slideInLeft: {
                    '0%': { transform: 'translateX(-100%)' },
                    '100%': { transform: 'translateX(0)' },
                },
                scaleIn: {
                    '0%': { transform: 'scale(0.9)', opacity: '0' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
                glowPulse: {
                    '0%, 100%': { boxShadow: '0 0 20px rgba(124, 108, 252, 0.6)' },
                    '50%': { boxShadow: '0 0 30px rgba(124, 108, 252, 0.9)' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
                gradientShift: {
                    '0%, 100%': { backgroundPosition: '0% 50%' },
                    '50%': { backgroundPosition: '100% 50%' },
                },
            },
            backgroundImage: {
                'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                'gradient-conic': 'conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))',
                'gradient-purple': 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                'gradient-brand': 'linear-gradient(135deg, #7c6cfc 0%, #6d5aed 100%)',
                'gradient-neon': 'linear-gradient(135deg, #7c6cfc 0%, #ff6ac7 100%)',
            },
            borderRadius: {
                '4xl': '2rem',
            },
        },
    },

    plugins: [forms],
};
