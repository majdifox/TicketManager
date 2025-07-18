import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],
    
    darkMode: 'class',

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'system-ui', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', 'Menlo', 'Monaco', 'Consolas', 'Liberation Mono', 'Courier New', 'monospace'],
            },
            
            colors: {
                // Custom dark theme colors
                dark: {
                    50: '#f8fafc',
                    100: '#f1f5f9',
                    200: '#e2e8f0',
                    300: '#cbd5e1',
                    400: '#94a3b8',
                    500: '#64748b',
                    600: '#475569',
                    700: '#334155',
                    800: '#1e293b',
                    900: '#0f172a',
                    950: '#020617',
                },
                
                // Enhanced brand colors
                primary: {
                    50: '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a',
                    950: '#172554',
                },
                
                secondary: {
                    50: '#faf5ff',
                    100: '#f3e8ff',
                    200: '#e9d5ff',
                    300: '#d8b4fe',
                    400: '#c084fc',
                    500: '#a855f7',
                    600: '#9333ea',
                    700: '#7c3aed',
                    800: '#6b21a8',
                    900: '#581c87',
                    950: '#3b0764',
                },
                
                // Success, warning, error with better dark theme support
                success: {
                    50: '#f0fdf4',
                    100: '#dcfce7',
                    200: '#bbf7d0',
                    300: '#86efac',
                    400: '#4ade80',
                    500: '#22c55e',
                    600: '#16a34a',
                    700: '#15803d',
                    800: '#166534',
                    900: '#14532d',
                    950: '#052e16',
                },
                
                warning: {
                    50: '#fffbeb',
                    100: '#fef3c7',
                    200: '#fde68a',
                    300: '#fcd34d',
                    400: '#fbbf24',
                    500: '#f59e0b',
                    600: '#d97706',
                    700: '#b45309',
                    800: '#92400e',
                    900: '#78350f',
                    950: '#451a03',
                },
                
                error: {
                    50: '#fef2f2',
                    100: '#fee2e2',
                    200: '#fecaca',
                    300: '#fca5a5',
                    400: '#f87171',
                    500: '#ef4444',
                    600: '#dc2626',
                    700: '#b91c1c',
                    800: '#991b1b',
                    900: '#7f1d1d',
                    950: '#450a0a',
                },
            },
            
            backgroundImage: {
                'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                'gradient-conic': 'conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))',
                'gradient-mesh': 'radial-gradient(at 40% 20%, hsla(228,100%,74%,1) 0px, transparent 50%), radial-gradient(at 80% 0%, hsla(189,100%,56%,1) 0px, transparent 50%), radial-gradient(at 0% 50%, hsla(355,100%,93%,1) 0px, transparent 50%), radial-gradient(at 80% 50%, hsla(340,100%,76%,1) 0px, transparent 50%), radial-gradient(at 0% 100%, hsla(22,100%,77%,1) 0px, transparent 50%), radial-gradient(at 80% 100%, hsla(242,100%,70%,1) 0px, transparent 50%), radial-gradient(at 0% 0%, hsla(343,100%,76%,1) 0px, transparent 50%)',
            },
            
            animation: {
                'fade-in': 'fadeIn 0.5s ease-out',
                'fade-in-up': 'fadeInUp 0.5s ease-out',
                'fade-in-down': 'fadeInDown 0.5s ease-out',
                'fade-in-left': 'fadeInLeft 0.5s ease-out',
                'fade-in-right': 'fadeInRight 0.5s ease-out',
                'slide-in-up': 'slideInUp 0.5s ease-out',
                'slide-in-down': 'slideInDown 0.5s ease-out',
                'slide-in-left': 'slideInLeft 0.5s ease-out',
                'slide-in-right': 'slideInRight 0.5s ease-out',
                'scale-in': 'scaleIn 0.5s ease-out',
                'float': 'float 6s ease-in-out infinite',
                'glow': 'glow 2s ease-in-out infinite alternate',
                'shimmer': 'shimmer 2.5s linear infinite',
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                'bounce-slow': 'bounce 2s infinite',
                'gradient': 'gradient 3s ease infinite',
            },
            
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeInUp: {
                    '0%': {
                        opacity: '0',
                        transform: 'translateY(30px)',
                    },
                    '100%': {
                        opacity: '1',
                        transform: 'translateY(0)',
                    },
                },
                fadeInDown: {
                    '0%': {
                        opacity: '0',
                        transform: 'translateY(-30px)',
                    },
                    '100%': {
                        opacity: '1',
                        transform: 'translateY(0)',
                    },
                },
                fadeInLeft: {
                    '0%': {
                        opacity: '0',
                        transform: 'translateX(-30px)',
                    },
                    '100%': {
                        opacity: '1',
                        transform: 'translateX(0)',
                    },
                },
                fadeInRight: {
                    '0%': {
                        opacity: '0',
                        transform: 'translateX(30px)',
                    },
                    '100%': {
                        opacity: '1',
                        transform: 'translateX(0)',
                    },
                },
                slideInUp: {
                    '0%': {
                        opacity: '0',
                        transform: 'translateY(100%)',
                    },
                    '100%': {
                        opacity: '1',
                        transform: 'translateY(0)',
                    },
                },
                slideInDown: {
                    '0%': {
                        opacity: '0',
                        transform: 'translateY(-100%)',
                    },
                    '100%': {
                        opacity: '1',
                        transform: 'translateY(0)',
                    },
                },
                slideInLeft: {
                    '0%': {
                        opacity: '0',
                        transform: 'translateX(-100%)',
                    },
                    '100%': {
                        opacity: '1',
                        transform: 'translateX(0)',
                    },
                },
                slideInRight: {
                    '0%': {
                        opacity: '0',
                        transform: 'translateX(100%)',
                    },
                    '100%': {
                        opacity: '1',
                        transform: 'translateX(0)',
                    },
                },
                scaleIn: {
                    '0%': {
                        opacity: '0',
                        transform: 'scale(0.9)',
                    },
                    '100%': {
                        opacity: '1',
                        transform: 'scale(1)',
                    },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%': { transform: 'translateY(-20px)' },
                },
                glow: {
                    '0%': { boxShadow: '0 0 20px rgba(59, 130, 246, 0.5)' },
                    '100%': { boxShadow: '0 0 30px rgba(59, 130, 246, 0.8)' },
                },
                shimmer: {
                    '0%': { transform: 'translateX(-100%)' },
                    '100%': { transform: 'translateX(100%)' },
                },
                gradient: {
                    '0%, 100%': { backgroundPosition: '0% 50%' },
                    '50%': { backgroundPosition: '100% 50%' },
                },
            },
            
            spacing: {
                '18': '4.5rem',
                '88': '22rem',
                '128': '32rem',
                '144': '36rem',
            },
            
            zIndex: {
                '60': '60',
                '70': '70',
                '80': '80',
                '90': '90',
                '100': '100',
            },
            
            borderRadius: {
                '4xl': '2rem',
                '5xl': '2.5rem',
                '6xl': '3rem',
            },
            
            backdropBlur: {
                xs: '2px',
                '3xl': '64px',
                '4xl': '128px',
            },
            
            boxShadow: {
                'glow': '0 0 20px rgba(59, 130, 246, 0.3)',
                'glow-lg': '0 0 30px rgba(59, 130, 246, 0.4)',
                'glow-xl': '0 0 40px rgba(59, 130, 246, 0.5)',
                'glow-primary': '0 0 20px rgba(59, 130, 246, 0.3)',
                'glow-secondary': '0 0 20px rgba(168, 85, 247, 0.3)',
                'glow-success': '0 0 20px rgba(34, 197, 94, 0.3)',
                'glow-warning': '0 0 20px rgba(245, 158, 11, 0.3)',
                'glow-error': '0 0 20px rgba(239, 68, 68, 0.3)',
                'inner-glow': 'inset 0 0 20px rgba(59, 130, 246, 0.2)',
                '3xl': '0 35px 60px -12px rgba(0, 0, 0, 0.5)',
                '4xl': '0 45px 80px -12px rgba(0, 0, 0, 0.6)',
            },
            
            screens: {
                'xs': '475px',
                '3xl': '1600px',
                '4xl': '1920px',
            },
            
            fontSize: {
                '2xs': ['0.625rem', { lineHeight: '0.75rem' }],
                '3xs': ['0.5rem', { lineHeight: '0.625rem' }],
            },
            
            blur: {
                '4xl': '72px',
                '5xl': '96px',
            },
            
            scale: {
                '102': '1.02',
                '103': '1.03',
            },
            
            transitionTimingFunction: {
                'bounce-in': 'cubic-bezier(0.68, -0.55, 0.265, 1.55)',
                'bounce-out': 'cubic-bezier(0.175, 0.885, 0.32, 1.275)',
            },
            
            transitionDuration: {
                '400': '400ms',
                '600': '600ms',
                '800': '800ms',
                '900': '900ms',
            },
        },
    },

    plugins: [
        forms({
            strategy: 'class',
        }),
        
        // Custom plugin for glassmorphism utilities
        function({ addUtilities, addComponents, theme }) {
            const newUtilities = {
                '.glassmorphism': {
                    backgroundColor: 'rgba(255, 255, 255, 0.05)',
                    backdropFilter: 'blur(20px)',
                    '-webkit-backdrop-filter': 'blur(20px)',
                    border: '1px solid rgba(255, 255, 255, 0.1)',
                },
                '.glassmorphism-strong': {
                    backgroundColor: 'rgba(0, 0, 0, 0.2)',
                    backdropFilter: 'blur(20px)',
                    '-webkit-backdrop-filter': 'blur(20px)',
                    border: '1px solid rgba(255, 255, 255, 0.1)',
                },
                '.text-shadow': {
                    textShadow: '0 2px 4px rgba(0, 0, 0, 0.5)',
                },
                '.text-shadow-lg': {
                    textShadow: '0 4px 8px rgba(0, 0, 0, 0.5)',
                },
                '.scrollbar-hide': {
                    '-ms-overflow-style': 'none',
                    'scrollbar-width': 'none',
                    '&::-webkit-scrollbar': {
                        display: 'none',
                    },
                },
                '.scrollbar-thin': {
                    'scrollbar-width': 'thin',
                    'scrollbar-color': 'rgba(255, 255, 255, 0.2) transparent',
                    '&::-webkit-scrollbar': {
                        width: '6px',
                    },
                    '&::-webkit-scrollbar-track': {
                        background: 'transparent',
                    },
                    '&::-webkit-scrollbar-thumb': {
                        backgroundColor: 'rgba(255, 255, 255, 0.2)',
                        borderRadius: '3px',
                    },
                    '&::-webkit-scrollbar-thumb:hover': {
                        backgroundColor: 'rgba(255, 255, 255, 0.3)',
                    },
                },
                '.perspective-1000': {
                    perspective: '1000px',
                },
                '.preserve-3d': {
                    transformStyle: 'preserve-3d',
                },
                '.backface-hidden': {
                    backfaceVisibility: 'hidden',
                },
                '.rotate-y-180': {
                    transform: 'rotateY(180deg)',
                },
            };
            
            const newComponents = {
                '.card-3d': {
                    transform: 'translateZ(0)',
                    transition: 'transform 0.3s ease',
                    '&:hover': {
                        transform: 'translateZ(10px) rotateX(2deg) rotateY(2deg)',
                    },
                },
                '.floating-action-button': {
                    position: 'fixed',
                    bottom: '2rem',
                    right: '2rem',
                    width: '3.5rem',
                    height: '3.5rem',
                    borderRadius: '50%',
                    backgroundColor: 'rgb(59 130 246)',
                    color: 'white',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    boxShadow: '0 10px 25px rgba(59, 130, 246, 0.3)',
                    transition: 'all 0.3s ease',
                    zIndex: '50',
                    '&:hover': {
                        transform: 'scale(1.1)',
                        boxShadow: '0 15px 35px rgba(59, 130, 246, 0.4)',
                    },
                },
                '.gradient-border': {
                    position: 'relative',
                    background: 'linear-gradient(90deg, transparent, transparent)',
                    '&::before': {
                        content: '""',
                        position: 'absolute',
                        inset: '0',
                        padding: '1px',
                        background: 'linear-gradient(90deg, rgb(59 130 246), rgb(147 51 234))',
                        borderRadius: 'inherit',
                        mask: 'linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0)',
                        maskComposite: 'xor',
                        '-webkit-mask-composite': 'xor',
                    },
                },
                '.loading-dots': {
                    display: 'inline-flex',
                    gap: '0.25rem',
                    '& > div': {
                        width: '0.5rem',
                        height: '0.5rem',
                        backgroundColor: 'currentColor',
                        borderRadius: '50%',
                        animation: 'loading-dots 1.4s ease-in-out infinite both',
                    },
                    '& > div:nth-child(1)': { animationDelay: '-0.32s' },
                    '& > div:nth-child(2)': { animationDelay: '-0.16s' },
                    '& > div:nth-child(3)': { animationDelay: '0s' },
                },
            };
            
            addUtilities(newUtilities);
            addComponents(newComponents);
            
            // Add loading dots keyframe
            addUtilities({
                '@keyframes loading-dots': {
                    '0%, 80%, 100%': {
                        transform: 'scale(0)',
                        opacity: '0.5',
                    },
                    '40%': {
                        transform: 'scale(1)',
                        opacity: '1',
                    },
                },
            });
        },
    ],
};