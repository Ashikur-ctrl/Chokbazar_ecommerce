import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['DM Sans', ...defaultTheme.fontFamily.sans],
                serif: ['DM Serif Display', ...defaultTheme.fontFamily.serif],
                display: ['DM Serif Display', ...defaultTheme.fontFamily.serif],
                bengali: ['Noto Sans Bengali', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                brand: {
                    50: '#fdf2ee',
                    100: '#f8e0d5',
                    200: '#edc4b3',
                    300: '#d9947a',
                    400: '#c26f53',
                    500: '#b5502d',
                    600: '#8f3c1f',
                    700: '#6e2c15',
                    800: '#501f0e',
                    900: '#3a1408',
                    950: '#240c04',
                },
                accent: {
                    50: '#fffbf0',
                    100: '#fef0d0',
                    200: '#fde09e',
                    300: '#fbd06b',
                    400: '#f5ce6b',
                    500: '#e0b44a',
                    600: '#c4962e',
                    700: '#a3781d',
                    800: '#7d5b12',
                    900: '#5a400b',
                },
                /* Kinetic Precision surface tokens */
                surface: {
                    DEFAULT: '#f7f9fb',
                    dim: '#d8dadc',
                    bright: '#f7f9fb',
                    'container-lowest': '#ffffff',
                    'container-low': '#f2f4f6',
                    container: '#eceef0',
                    'container-high': '#e6e8ea',
                    'container-highest': '#e0e3e5',
                },
                'on-surface': {
                    DEFAULT: '#191c1e',
                    variant: '#45464d',
                },
                'border-subtle': 'rgba(15, 23, 42, 0.08)',
                'glass-bg': 'rgba(255, 255, 255, 0.7)',
            },
            boxShadow: {
                'card': '0 1px 3px 0 rgba(0, 0, 0, 0.06)',
                'card-hover': '0 4px 12px 0 rgba(0, 0, 0, 0.08)',
                'elevated': '0 8px 24px 0 rgba(0, 0, 0, 0.12)',
                'linear': '0 1px 2px rgba(0,0,0,0.05), 0 4px 12px rgba(0,0,0,0.05)',
                'glass': '0 1px 3px rgba(0,0,0,0.04), 0 8px 32px rgba(0,0,0,0.06)',
                'soft': '0 0 0 1px rgba(15, 23, 42, 0.04), 0 1px 3px rgba(15, 23, 42, 0.06)',
            },
            borderRadius: {
                'card': '0.75rem',
                '2xl': '1rem',
                '3xl': '1.5rem',
            },
            maxWidth: {
                'container': '1440px',
            },
            spacing: {
                'gutter': '24px',
                'section-gap': '80px',
            },
            transitionDuration: {
                'snappy': '150ms',
            },
            animation: {
                'skeleton': 'skeleton-pulse 2s ease-in-out infinite',
                'fade-in': 'fade-in 0.3s ease-out',
                'fade-in-up': 'fade-in-up 0.4s ease-out',
                'scale-in': 'scale-in 0.2s ease-out',
                'slide-down': 'slide-down 0.2s ease-out',
                'scale-up': 'scale-up 0.15s ease-out',
            },
            keyframes: {
                'skeleton-pulse': {
                    '0%, 100%': { opacity: '0.4' },
                    '50%': { opacity: '1' },
                },
                'fade-in': {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                'fade-in-up': {
                    '0%': { transform: 'translateY(12px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                'scale-in': {
                    '0%': { transform: 'scale(0.95)', opacity: '0' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
                'slide-down': {
                    '0%': { transform: 'translateY(-8px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                'scale-up': {
                    '0%': { transform: 'scale(0.97)', opacity: '0' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
            },
        },
    },

    plugins: [forms],
};
