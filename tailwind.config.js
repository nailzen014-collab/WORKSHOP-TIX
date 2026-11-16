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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                display: ['"Archivo"', ...defaultTheme.fontFamily.sans],
                serif: ['"Fraunces"', 'Georgia', 'serif'],
            },
            colors: {
                cream: {
                    50: '#FBFAF2',
                    100: '#F5F2E3',
                    200: '#ECE7D2',
                },
                forest: {
                    50: '#EEF3EC',
                    100: '#D6E2D2',
                    200: '#AEC9A6',
                    400: '#5C8A5A',
                    600: '#2F5E3B',
                    700: '#234B2E',
                    800: '#1B3A24',
                    900: '#12291A',
                },
                lime: {
                    300: '#DDF48A',
                    400: '#CFEE69',
                    500: '#BFE85A',
                    600: '#A7D63E',
                },
            },
            borderRadius: {
                '4xl': '2rem',
            },
            boxShadow: {
                card: '0 24px 48px -32px rgba(18, 41, 26, 0.45)',
            },
        },
    },

    plugins: [forms],
};
