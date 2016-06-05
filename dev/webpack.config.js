var webpack = require('webpack')

module.exports = {
	entry: [
		'./dev/vue/datasets.js'
	],
	output: {
		path: '/public/js',
		publicPath: '/public/',
		filename: 'datasets.min.js'
	},
	//watch: true,
	module: {
		loaders: [{
			test: /\.js$/,
			exclude: /node_modules/,
			loader: 'babel'
		}, {
			test: /\.vue$/,
			loader: 'vue'
		}]
	},
	plugins: [
		new webpack.DefinePlugin({
			'process.env': {
				'NODE_ENV': '"production"'
			}
		}),
		new webpack.optimize.UglifyJsPlugin({
			compress: {
				warnings: false
			}
		})
	],
	babel: {
		presets: ['es2015'],
		plugins: ['transform-runtime']
	},
	resolve: {
		modulesDirectories: ['node_modules']
	}
}
