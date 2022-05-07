const webpack = require("webpack")
const path = require('path')
const env = process.env.NODE_ENV;
const MiniCssExtractPlugin = require("mini-css-extract-plugin")
let srcPath = './src/assets/src/';

module.exports = {
  target: "web",
  mode: "production",
  devtool: false,
  infrastructureLogging: {
    colors: true,
    level: "verbose",
  },

  entry: {
    triggers: srcPath + "triggers.js",
    edittrigger: srcPath + "edittrigger.js",
  },

  output: {
    clean: true,
    path: path.resolve(__dirname, 'src/assets/dist'),
    filename: "[name].js",
    chunkFilename: "[name].js",
    environment: { module: false }
  },

  resolve: {
    modules: [ "node_modules" ]
  },

  plugins: [
    new MiniCssExtractPlugin({
      filename: "[name].css"
    })
  ],

  optimization: {
    splitChunks: {
      maxSize: 200000
    }
  },

  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /(node_modules)/,
        use: {
          loader: "babel-loader",
          options: {
            presets: [
              ["@babel/preset-env", {
                useBuiltIns: false,
              }],
            ],
            plugins: [
              ["@babel/plugin-proposal-class-properties"],
              ["@babel/plugin-transform-runtime", { "corejs": 3 }],
              ["@babel/plugin-syntax-dynamic-import"]
            ],
          },
        }
      },
      {
        test: /\.(scss|css)$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
            options: {
              emit: true
            }
          },
          {
            loader: "css-loader",
            options: {
              importLoaders: 2,
            }
          },
          {
            loader: 'postcss-loader',
            options: {
              postcssOptions: {
                plugins: function () {
                  return [
                    require('autoprefixer')
                  ];
                }
              }
            }
          },
          { loader: "svg-transform-loader/encode-query" },
          {
            loader: "sass-loader",
            options: {
              sourceMap: true,
              implementation: require("sass"),
            }
          }
        ]
      }
    ]
  }
}
