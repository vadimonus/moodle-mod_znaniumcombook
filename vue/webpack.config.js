const path = require('path');
const webpack = require('webpack');
const { VueLoaderPlugin } = require('vue-loader');
const TerserPlugin = require('terser-webpack-plugin');

module.exports = (env, options) => {

    exports = {
        mode: 'development',
        entry: {
            'bookpicker': './book_picker.js',
        },
        output: {
            path: path.resolve(__dirname, '../amd/src'),
            filename: 'bookpicker-lazy.js',
            libraryTarget: 'amd',
        },
        module: {
            rules: [
                {
                    test: /\.vue$/,
                    loader: 'vue-loader',
                    options: {
                        loaders: {},
                        // Other vue-loader options go here
                    },
                },
                {
                    test: /\.js$/,
                    loader: 'babel-loader',
                    exclude: /node_modules/,
                },
            ],
        },
        resolve: {
            alias: {
                'vue$': 'vue/dist/vue.esm-browser.js',
            },
            extensions: ['.js', '.vue'],
        },
        devtool: false,
        plugins: [
            new VueLoaderPlugin(),
        ],
        watchOptions: {
            ignored: /node_modules/,
        },
        externals: {
            'core/ajax': {
                amd: 'core/ajax',
            },
            'core/modal_factory': {
                amd: 'core/modal_factory',
            },
            'core/modal_events': {
                amd: 'core/modal_events',
            },
            'core/localstorage': {
                amd: 'core/localstorage',
            },
            'core/notification': {
                amd: 'core/notification',
            },
            'jquery': {
                amd: 'jquery',
            },
        },
    };

    if (options.mode === 'production') {
        exports.mode = 'production';
        exports.output = {
            path: path.resolve(__dirname, '../amd/build'),
            filename: 'bookpicker-lazy.min.js',
            libraryTarget: 'amd',
        };
        exports.devtool = false;
        exports.plugins = (exports.plugins || []).concat([
            new webpack.LoaderOptionsPlugin({
                minimize: true,
            }),
        ]);
        exports.optimization = {
            minimizer: [
                new TerserPlugin({
                    parallel: true,
                    terserOptions: {
                        output: {
                            comments: false,
                        },
                    },
                }),
            ],
        };
        exports.resolve.alias.vue$ = 'vue/dist/vue.esm-browser.prod.js';
    }

    return exports;
};
