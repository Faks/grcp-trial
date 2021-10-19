const path = require('path');
const { VueLoaderPlugin } = require('vue-loader')

module.exports = {
  entry: './resources/index.js',
  output: {
    filename: 'index.js',
    path: path.resolve(__dirname, 'public'),
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'resources/'),
      '@c': path.resolve(__dirname, 'resources/components'),
      vue: 'vue/dist/vue.js'
    },
  },
  module: {
    rules: [
      {
        test: /\.vue$/,
        loader: 'vue-loader',
      },
    ],
  },
  plugins: [
    new VueLoaderPlugin(),
  ],
};