const mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.copyDirectory('resources/backend', 'storage/app/public/themes/admin/backend');
//mix.copyDirectory('resources/frontend', 'public/frontend');

mix.js('resources/js/app.js', 'storage/themes/admin/backend/js');
//mix.js('resources/js/app1.js', 'public/js').version();
//    .sass('resources/sass/app.scss', 'public/css');
