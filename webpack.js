const { merge } = require('webpack-merge')
const path = require('path')
const webpack = require('webpack')
const webpackConfig = require('@nextcloud/webpack-vue-config')

const config = {
	entry: {
		designer: path.resolve(path.join('src', 'designer.js')),
		app: path.resolve(path.join('src', 'app.js')),
	},
	module: {
		rules: [
			{
				test: /\.svg$/,
				use: 'url-loader',
			},
		],
	},
	plugins: [
		new webpack.IgnorePlugin(/^\.\/locale$/, /moment$/),
	],
}

module.exports = merge(config, webpackConfig)
