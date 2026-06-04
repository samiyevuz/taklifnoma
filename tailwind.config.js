/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],
    theme: {
        extend: {
            colors: {
                cream: {
                    50: '#FDFCFA',
                    100: '#FAFAFA',
                    200: '#F5F2EB',
                    300: '#EDE8DC',
                },
                royal: {
                    950: '#08070F',
                    900: '#0F0E17',
                    800: '#1A1828',
                    700: '#252336',
                    600: '#35324A',
                    500: '#4A4563',
                },
                luxury: {
                    gold: '#C9A227',
                    'gold-light': '#E8D48B',
                    'gold-dark': '#9A7B1A',
                    purple: '#5B2D8E',
                    'purple-deep': '#3D1D5C',
                    'purple-light': '#8B5CB8',
                    emerald: '#0D6B5C',
                    'emerald-light': '#14A085',
                    'emerald-dark': '#084A40',
                },
            },
            fontFamily: {
                sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                serif: ['"Playfair Display"', 'ui-serif', 'Georgia', 'serif'],
            },
            fontSize: {
                'fluid-hero': [
                    'clamp(1.75rem, 4.5vw + 0.75rem, 4.5rem)',
                    { lineHeight: '1.1', letterSpacing: '-0.02em' },
                ],
                'fluid-subtitle': [
                    'clamp(1rem, 1.5vw + 0.5rem, 1.5rem)',
                    { lineHeight: '1.4' },
                ],
                'fluid-body': [
                    'clamp(0.875rem, 0.5vw + 0.75rem, 1.125rem)',
                    { lineHeight: '1.65' },
                ],
            },
            spacing: {
                'fluid-xs': 'clamp(0.5rem, 1vw, 0.75rem)',
                'fluid-sm': 'clamp(0.75rem, 2vw, 1rem)',
                'fluid-md': 'clamp(1rem, 3vw, 1.5rem)',
                'fluid-lg': 'clamp(1.5rem, 4vw, 2.5rem)',
                'fluid-xl': 'clamp(2rem, 5vw, 4rem)',
            },
            borderRadius: {
                glass: '1.25rem',
                'glass-lg': '1.75rem',
            },
            boxShadow: {
                glass: '0 8px 32px rgba(15, 14, 23, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.08)',
                'glass-lg': '0 16px 48px rgba(15, 14, 23, 0.18), inset 0 1px 0 rgba(255, 255, 255, 0.1)',
                'gold-glow': '0 0 40px rgba(201, 162, 39, 0.25)',
                'purple-glow': '0 0 40px rgba(91, 45, 142, 0.2)',
            },
            backdropBlur: {
                glass: '20px',
                'glass-heavy': '40px',
            },
            transitionTimingFunction: {
                premium: 'cubic-bezier(0.4, 0, 0.2, 1)',
            },
            transitionDuration: {
                premium: '300ms',
                'premium-slow': '500ms',
            },
            keyframes: {
                'fade-in': {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                'slide-up': {
                    '0%': { opacity: '0', transform: 'translateY(16px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                'scale-in': {
                    '0%': { opacity: '0', transform: 'scale(0.96)' },
                    '100%': { opacity: '1', transform: 'scale(1)' },
                },
                shimmer: {
                    '0%': { backgroundPosition: '-200% 0' },
                    '100%': { backgroundPosition: '200% 0' },
                },
                'ripple-expand': {
                    '0%': { transform: 'scale(0)', opacity: '0.5' },
                    '100%': { transform: 'scale(4)', opacity: '0' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0)' },
                    '50%': { transform: 'translateY(-6px)' },
                },
            },
            animation: {
                'fade-in': 'fade-in 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards',
                'slide-up': 'slide-up 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards',
                'scale-in': 'scale-in 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards',
                shimmer: 'shimmer 2.5s linear infinite',
                float: 'float 4s cubic-bezier(0.4, 0, 0.2, 1) infinite',
            },
        },
    },
    plugins: [],
};
