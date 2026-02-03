import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Manrope', 'Inter', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Charte LDM v2 - Couleurs principales
                primary: {
                    DEFAULT: '#0EA5E9',
                    dark: '#0F172A',
                },
                accent: '#0F172A',
                'accent-secondary': '#22C55E',
                // Couleurs neutres
                neutral: {
                    900: '#0F172A',
                    600: '#6B7280',
                    100: '#F3F4F6',
                },
                // Couleurs d'état
                success: '#22C55E',
                warning: '#F59E0B',
                danger: '#EF4444',
                info: '#0EA5E9',
                // Couleurs système
                border: '#E5E7EB',
            },
            backgroundColor: {
                'app': '#F3F4F6',
                'card': '#FFFFFF',
            },
            textColor: {
                'primary': '#0F172A',
                'secondary': '#6B7280',
            },
            borderColor: {
                'default': '#E5E7EB',
            },
        },
    },

    plugins: [forms, typography],
};
