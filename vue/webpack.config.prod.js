const path = require('path');
const webpack = require('webpack');
const VueLoaderPlugin = require('vue-loader/lib/plugin');
const TerserPlugin = require('terser-webpack-plugin');

module.exports = (env, options) => {

    exports = {
        mode: 'production',
        entry: {
            'bookpicker': './book_picker.js',
        },
        output: {
            path: path.resolve(__dirname, '../amd/build'),
            filename: 'bookpicker-lazy.min.js',
            libraryTarget: 'amd',
        },
        module: {
            rules: [
                {
                    test: /\.css$/,
                    use: [
                        'vue-style-loader',
                        'css-loader',
                    ],
                },
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
                'vue$': 'vue/dist/vue.esm.js',
            },
            extensions: ['*', '.js', '.vue', '.json'],
        },
        plugins: [
            new VueLoaderPlugin(),
            new webpack.LoaderOptionsPlugin({
                minimize: true,
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
        optimization: {
            minimizer: [
                new TerserPlugin({
                    cache: true,
                    parallel: true,
                    sourceMap: false,
                    terserOptions: {
                        output: {
                            comments: false,
                        },
                    },
                }),
            ],
        }
    };
    return exports;
};
