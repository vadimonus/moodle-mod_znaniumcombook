const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const RemovePlugin = require('remove-files-webpack-plugin');

module.exports = (env, options) => {

    exports = {
        mode: 'development',
        entry: './styles.scss',
        output: {
            path: path.resolve(__dirname, '..'),
        },
        plugins: [
            new MiniCssExtractPlugin({
                filename: 'styles.css',
            }),
            new RemovePlugin({
                after: {
                    root: '..',
                    include: [
                        'main.js'
                    ]
                }
            }),
        ],
        module: {
            rules: [
                {
                    test: /\.scss$/,
                    use: [
                        {
                            loader: MiniCssExtractPlugin.loader,
                            options: {
                                publicPath: path.resolve(__dirname, '..'),
                            },
                        },
                        'css-loader',
                        'sass-loader',
                    ],
                },
            ],
        },
        watchOptions: {
            ignored: /node_modules/,
        },
    };

    return exports;
};
