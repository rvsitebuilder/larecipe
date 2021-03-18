const fs = require('fs');
const mix = require('laravel-mix');
require('laravel-mix-merge-manifest');
var pjson = require('./package.json');

mix.setPublicPath('public');

// Ref: https://laravel-mix.com/docs/4.0/options
mix.options({
    extractVueStyles: false,
    processCssUrls: true,
    terser: {
        terserOptions: {
            compress: {
                drop_console: false,
                pure_funcs: ['console.log', 'console.error'],
            },
        },
    },
    purifyCss: false,
    postCss: [require('autoprefixer')],
    clearConsole: false,
    cssNano: {
        discardComments: { removeAll: true },
    },
    hmrOptions: {
        host: 'localhost',
        port: 9999,
    },
});

var pathsToClean = [];
// Do not remove public folder if run webpack for user and admin separately.
if (!fs.existsSync('webpack.mix.user.js')) {
    pathsToClean = ['public'];
}

const { CleanWebpackPlugin } = require('clean-webpack-plugin');
const webpack = require('webpack');
mix.webpackConfig(() => {
    return {
        plugins: [
            new CleanWebpackPlugin({
                cleanOnceBeforeBuildPatterns: pathsToClean,
            }),
            new webpack.IgnorePlugin({
                resourceRegExp: /^\.\/locale$/,
                contextRegExp: /(moment|uikit\/dist\/js\/components)$/,
            }),
        ],
        externals: {
            // require("jquery") is external and available on the global var jQuery
            jquery: 'jQuery',
        },
    };
});

mix.mergeManifest();
mix.version();
mix.disableNotifications();

// Set webpack comfig
if (!mix.inProduction()) {
    // Set browserSync watch files
    mix.browserSync({
        proxy: 'localhost',
        files: [
            './config/**/*',
            './resources/**/*',
            './routes/**/*',
            './src/**/*',
        ],
    });

    // Run bundleAnalyzer to better understannd output
    require('dotenv').config({ path: '../../../.env' });

    if (process.env.MIX_BUNDLE_ANALYZER == 'enable') {
        const BundleAnalyzerPlugin = require('webpack-bundle-analyzer')
            .BundleAnalyzerPlugin;
        mix.webpackConfig(() => {
            return {
                plugins: [
                    new BundleAnalyzerPlugin({
                        analyzerMode: 'static',
                        // Check Report at public/report.html
                        // analyzerHost: '127.0.0.1',
                        // analyzerPort: 'auto'
                    }),
                ],
            };
        });
    }

    // Publish files on develop mode, on production it is done by artisan vender:publish
    const fse = require('fs-extra');
    mix.then(function () {
        console.log('==> Webpack finishes building.');
        var packagename = pjson.name.replace('@', '');
        let path = '../../../public/vendor/' + packagename;
        fse.remove(path, err => {
            if (err) return console.error(err);
            console.log('===> Remove ' + path + ' Done!');
            fse.copy('public', path, err => {
                if (err) return console.error(err);
                console.log('===> Copy /public to ' + path + ' Done!');
            });
        });
    });
}

module.exports = {
    mix: mix,
};
