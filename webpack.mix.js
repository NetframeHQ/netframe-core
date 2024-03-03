const mix = require('laravel-mix');
require('laravel-mix-svg');
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

mix.webpackConfig({
    entry: {
        style: ['./resources/sass/style.scss'],
        pink: ['./resources/sass/themes/pink.scss'],
        blue: ['./resources/sass/themes/blue.scss'],
        boarding: ['./resources/sass-boarding/main.scss']
    }
});

mix.options({
        processCssUrls: false
    })
    .sass('resources/sass/style.scss', 'public/css/style.css')
    .sass('resources/sass/themes/pink.scss', 'public/css/theme/pink/style.css')
    .sass('resources/sass/themes/blue.scss', 'public/css/theme/blue/style.css')
    .sass('resources/sass-boarding/main.scss', 'public/css/boarding.css')
    .sourceMaps(true, 'source-map')
    .copy('resources/sass/themes/previews/standard.png', 'public/css/theme/standard/preview.png')
    .copy('resources/sass/themes/previews/pink.png', 'public/css/theme/pink/preview.png')
    .copy('resources/sass/themes/previews/blue.png', 'public/css/theme/blue/preview.png')
    .version();


// collab server
mix.js('resources/js/app.js', 'public/js/');
mix.js('resources/js/collab.js', 'public/js/')
    .svg({
        assets: ['./resources/js/collab/svg/'], // a list of directories to search svg images
        output: './resources/js/collab/svg.js',
});

// firebase browser notifications
mix.js('resources/js/firebase/serviceWorker.js', 'public/firebase-messaging-sw.js');
mix.js('resources/js/firebase/app.js', 'public/packages/firebase/app.js');

// channels
mix.js('resources/js/channel.js', 'public/js/')

// autocomplete
//mix.copy(['node_modules/@tarekraafat/autocomplete.js/dist/autoComplete.js'], 'resources/js/autocomplete/');
mix.combine('resources/js/autocomplete/*.js', 'public/js/autocomplete.js')

// chartjs
mix.copy('node_modules/chart.js/dist/chart.js', 'public/packages/chart.js/chart.js');