const path = require('path');
const webpack = require('webpack');
const { VueLoaderPlugin } = require('vue-loader');
const TerserPlugin = require('terser-webpack-plugin');

module.exports = (env, options) => {
    let base = {
        mode: 'development',
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
        devtool: 'inline-source-map',
        plugins: [
            new VueLoaderPlugin(),
            new webpack.DefinePlugin({
                __VUE_PROD_DEVTOOLS__: JSON.stringify(false)
            }),
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
        base.mode = 'production';
        base.devtool = false;
        base.plugins = (base.plugins || []).concat([
            new webpack.LoaderOptionsPlugin({
                minimize: true,
            }),
        ]);
        base.optimization = {
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
        base.resolve.alias.vue$ = 'vue/dist/vue.esm-browser.prod.js';
    }

    let src = {
        ...base,
        entry: {
            'bookpicker': './book_picker.js',
        },
        output: {
            path: path.resolve(__dirname, '../amd/src'),
            filename: 'bookpicker-lazy.js',
            libraryTarget: 'amd',
        },
    };
    let build = {
        ...base,
        entry: {
            'bookpicker': './book_picker.js',
        },
        output: {
            path: path.resolve(__dirname, '../amd/build'),
            filename: 'bookpicker-lazy.min.js',
            libraryTarget: 'amd',
        },
    };

    return [src, build];
};
