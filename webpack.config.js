const path = require('path');

module.exports = {
	entry: {
		bundle: './src/index.ts',
	},
	externals: {
		jquery: 'jQuery',
		underscore: '_',
		react: ['vendor', 'React'],
		'react-dom': ['vendor', 'ReactDOM'],
		'@wordpress/hooks': ['vendor', 'wp', 'hooks'],
		'@divi/data': ['divi', 'data'],
		'@divi/dynamic-data': ['divi', 'dynamicData'],
		'@divi/global-data': ['divi', 'globalData'],
		'@divi/module': ['divi', 'module'],
		'@divi/module-library': ['divi', 'moduleLibrary'],
		'@divi/module-utils': ['divi', 'moduleUtils'],
	},
	module: {
		rules: [
			{
				test: /\.tsx?$/,
				use: {
					loader: 'ts-loader',
					options: { transpileOnly: true },
				},
				exclude: /node_modules/,
			},
		],
	},
	resolve: {
		extensions: ['.js', '.jsx', '.tsx', '.ts', '.json'],
	},
	output: {
		filename: '[name].js',
		path: path.resolve(__dirname, 'scripts'),
	},
	stats: { errorDetails: true },
};
