// Generated using webpack-cli https://github.com/webpack/webpack-cli
const path = require('path');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const WorkboxWebpackPlugin = require('workbox-webpack-plugin');

const isProduction = process.env.NODE_ENV == 'production';

const config = {
    entry: './src/main.js',
    output: {
        filename: "bundle.js",
        path: path.resolve(__dirname, 'dist'),
        library: 'exportCosmosConfig',        
        libraryExport: "default" ,
        libraryTarget: 'umd'
    },
    devServer: {
        open: true,
        host: 'localhost',
    },
    plugins: [
        new HtmlWebpackPlugin({
            template: 'index.html',
        }),
    ],
    externals: {
      jquery: 'jQuery'
    },
    module: {
        rules: [
            {
                test: /\.(eot|svg|ttf|woff|woff2|png|jpg|gif)$/i,
                type: 'asset',
            },
        ],
    },
    resolve: {
      fallback: {
        buffer: false,
        crypto: false,
        events: false,
        path: false,
        stream: false,
        string_decoder: false,
      },
    },    
};

module.exports = () => {
    if (isProduction) {
        config.mode = 'production';      
        config.plugins.push(new WorkboxWebpackPlugin.GenerateSW());        
    } else {
        config.mode = 'development';
    }
    return config;
};
