// Webpack uses this to work with directories
const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const CssMinimizerPlugin = require('css-minimizer-webpack-plugin');
const isDevelopment = process.env.NODE_ENV === 'development';
const isProduction = process.env.NODE_ENV === 'production';


// This is the main configuration object.
// Here you write different options and tell Webpack what to do
module.exports = {

  // Path to your entry point. From this file Webpack will begin his work
  entry: './build/js/index.js',

  devtool: (isDevelopment) ? 'source-map' : false,

  // Path and filename of your result bundle.
  // Webpack will bundle all JavaScript into this file
  output: {
    path: path.resolve(__dirname, ''),
    filename: 'bundle.js'
  },

  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /(node_modules)/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env']
          }
        }
      },
      {
        // Apply rule for .sass, .scss or .css files
        test: /\.(sa|sc|c)ss$/,
  
        // Set loaders to transform files.
        // Loaders are applying from right to left(!)
        // The first loader will be applied after others
        use: [
              {
                // After all CSS loaders we use plugin to do his work.
                // It gets all transformed CSS and extracts it into separate
                // single bundled file
                loader: MiniCssExtractPlugin.loader
              }, 
               {
                 // This loader resolves url() and @imports inside CSS
                 loader: "css-loader",
                 options: { 
                   url: false
                 }
               },
               {
                 // Then we apply postCSS fixes like autoprefixer and minifying
                 loader: "postcss-loader"
               },
               {
                 // First we transform SASS to standard CSS
                 loader: "sass-loader",
                 options: {
                   implementation: require("sass")
                 }
               }
             ]
      }
    ]
  },

  optimization: {
    minimize: true,
    minimizer: [
      // For webpack@5 you can use the `...` syntax to extend existing minimizers (i.e. `terser-webpack-plugin`), uncomment the next line
      // `...`
      new CssMinimizerPlugin(),
    ],
  },

  plugins: [

    new MiniCssExtractPlugin({
      filename: "bundle.css"
    })
  
  ],

  // Default mode for Webpack is production.
  // Depending on mode Webpack will apply different things
  // on final bundle. For now we don't need production's JavaScript 
  // minifying and other thing so let's set mode to development
  mode: 'development'
};