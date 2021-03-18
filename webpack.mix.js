const { mix } = require('./webpack.base.js');

// Set final path for images, fonts and etc.
// While webpack building final CSS, it will change all url() to begin with this path.
mix.setResourceRoot('/vendor/larecipe/dashboard');
// ============================ //
// Automate artisan vender:publish while developing
//
// Images, fonts, and etc. that use artisan vender:publish to install which defined
// on your app's service provider, need to call copyDirectory here.
// Webpack.base.js (line 116) already set up to copy app's public folder over
// to site's public folder.
//
// Path relative to this webpack.mix.js
mix.copyDirectory('resources/images', 'public/images');

// ============================ //
mix.js('resources/js/user/app.js', 'public/js/user/app.js');
mix.sass('resources/css/user/app.scss', 'css/user/app.css');
