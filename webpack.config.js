/* global process __dirname */
const DEV = 'production' !== process.env.NODE_ENV;

/**
 * Plugins
 */
const path = require( 'path' );
const CleanWebpackPlugin = require( 'clean-webpack-plugin' );
const UglifyJsPlugin = require( 'uglifyjs-webpack-plugin' );
const FriendlyErrorsPlugin = require( 'friendly-errors-webpack-plugin' );
const MiniCssExtractPlugin = require( 'mini-css-extract-plugin' );
const OptimizeCssAssetsPlugin = require( 'optimize-css-assets-webpack-plugin' );
const cssnano = require( 'cssnano' );
const StyleLintPlugin = require( 'stylelint-webpack-plugin' );

const BUILD_DIR = path.resolve( __dirname, 'assets/build' );
const JS_MODULES_DIR = path.resolve( __dirname, 'assets/src/js' );
const CSS_MODULES_DIR = path.resolve( __dirname, 'assets/src/css' );

const entry = {
	// Entry JS files.
	admin: path.resolve( JS_MODULES_DIR, 'Admin/admin.js' ),
};

const output = {
	path: BUILD_DIR,
	filename: 'js/[name].build.js',
};

/**
 * Note: argv.mode will return 'development' or 'production'.
 *
 * @param {Object} argv
 */
const plugins = () => [
	new CleanWebpackPlugin(),

	new FriendlyErrorsPlugin( {
		clearConsole: false,
	} ),

	new MiniCssExtractPlugin( {
		filename: 'css/[name].css',
	} ),

	new StyleLintPlugin( {
		extends: 'stylelint-config-wordpress/scss',
	} ),
];

const rules = [
	{
		enforce: 'pre',
		test: /\.(js|jsx)$/,
		exclude: /node_modules/,
		use: 'eslint-loader',
	},
	{
		test: /\.js$/,
		include: [ JS_MODULES_DIR ],
		use: 'babel-loader',
	},
	{
		test: /\.s(a|c)ss$/,
		include: [ CSS_MODULES_DIR ],
		use: [
			MiniCssExtractPlugin.loader,
			'css-loader',
			'postcss-loader',
			'sass-loader',
		],
	},
];

const optimization = [
	new OptimizeCssAssetsPlugin( {
		cssProcessor: cssnano,
	} ),

	new UglifyJsPlugin( {
		cache: false,
		parallel: true,
		sourceMap: false,
	} ),
];

module.exports = () => ( {
	entry: entry,
	output: output,
	plugins: plugins(),
	devtool: 'source-map',

	module: {
		rules,
	},

	optimization: {
		minimizer: optimization,
	},

	externals: {
		jquery: 'jQuery',
	},
	resolve: {
		alias: {
			css: CSS_MODULES_DIR,
		},
	},
} );
