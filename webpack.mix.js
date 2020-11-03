const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */
// Extend Mix with the "i18n" method, that loads the vue-i18n-loader
// mix.extend('i18n', new class {
//         webpackRules() {
//             return [
//                 {
//                     resourceQuery: /blockType=i18n/,
//                     type: 'javascript/auto',
//                     loader: '@kazupon/vue-i18n-loader',
//                 },
//             ];
//         }
//     }());
// mix.i18n()
mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        require('postcss-import'),
        require('tailwindcss'),
    ]);
