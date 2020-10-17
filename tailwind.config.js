const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
    purge: [
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Nunito', ...defaultTheme.fontFamily.sans],
            },
            shadows: {
                default: '0 0 5px 0 rgba(0, 0, 0, 0.8)'
            },
            colors: {
                'gray-light': '#F5F6F9',
                'gray-description': 'rgba(0, 0, 0, 0.4)',
                'blue': '#47cdff',
                'blue-light': '#8ae2fe',
            },

        },
    },

    variants: {
        opacity: ['responsive', 'hover', 'focus', 'disabled'],
    },

    plugins: [require('@tailwindcss/ui')],
};
