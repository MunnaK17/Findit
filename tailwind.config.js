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
                headline: ['Lexend', ...defaultTheme.fontFamily.sans],
                body: ['Inter', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: '#b9c7e4',
                'primary-fixed': '#d6e3ff',
                'primary-container': '#0a192f',
                'on-primary': '#233148',
                'on-primary-container': '#74829d',
                secondary: '#ffbd69',
                'secondary-container': '#f29b00',
                'on-secondary': '#472a00',
                'on-secondary-container': '#5e3900',
                surface: '#121414',
                'surface-bright': '#38393a',
                'surface-container': '#1e2020',
                'surface-container-low': '#1a1c1c',
                'surface-container-lowest': '#0d0f0f',
                'surface-container-high': '#282a2b',
                'surface-container-highest': '#333535',
                'surface-variant': '#333535',
                'surface-dim': '#121414',
                'on-surface': '#e2e2e2',
                'on-surface-variant': '#c5c6cd',
                'on-background': '#e2e2e2',
                tertiary: '#b6c6ed',
                'tertiary-container': '#061836',
                'on-tertiary': '#20304f',
                'on-tertiary-container': '#7282a5',
                error: '#ffb4ab',
                'error-container': '#93000a',
                'on-error': '#690005',
                'on-error-container': '#ffdad6',
                outline: '#8f9097',
                'outline-variant': '#44474d',
                inverse: {
                    surface: '#e2e2e2',
                    'on-surface': '#2f3131',
                    primary: '#515f78',
                },
            },
            spacing: {
                'gutter': '24px',
                'stack-lg': '48px',
                'stack-md': '24px',
                'stack-sm': '12px',
            },
            borderRadius: {
                'lg': '0.5rem',
                'xl': '0.75rem',
            },
        },
    },

    plugins: [forms],
};
