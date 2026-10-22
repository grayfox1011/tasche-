const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

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
		'@divi/module': ['divi', 'module'],
		'@divi/module-library': ['divi', 'moduleLibrary'],
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
			{
				test: /\.s?css$/i,
				use: [
					{ loader: MiniCssExtractPlugin.loader },
					{ loader: 'css-loader', options: { url: false, importLoaders: 2 } },
					{ loader: 'sass-loader' },
				],
			},
		],
	},
	optimization: {
		splitChunks: {
			cacheGroups: {
				vb: {
					type: 'css/mini-extract',
					test: /[\\/]style(\.module)?\.(sc|sa|c)ss$/,
					chunks: 'all',
					enforce: true,
					name(_, chunks, cacheGroupKey) {
						const chunkName = chunks[0].name;
						return `${path.dirname(chunkName)}/${cacheGroupKey}-${path.basename(chunkName)}`;
					},
				},
				default: false,
			},
		},
	},
	plugins: [new MiniCssExtractPlugin({ filename: '../styles/[name].css' })],
	resolve: {
		extensions: ['.js', '.jsx', '.tsx', '.ts', '.json'],
	},
	output: {
		filename: '[name].js',
		path: path.resolve(__dirname, 'scripts'),
	},
	stats: { errorDetails: true },
};
