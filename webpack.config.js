/**
 * External dependencies
 */
const webpack = require( 'webpack' );
const path = require( 'path' );
const ExtractTextPlugin = require( 'extract-text-webpack-plugin' );
const CopyWebpackPlugin = require( 'copy-webpack-plugin' );

const cssFiles = [
	'buttons',
	'woocommerce',
	'wp-login',
];

// Configuration for the ExtractTextPlugin.
const extractConfig = {
	use: [
		{
			loader: 'raw-loader',
		},
		{
			loader: 'postcss-loader',
			options: {
				plugins: [
					require( 'autoprefixer' ),
					require( 'postcss-focus-within' ),
				],
			},
		},
		{
			loader: 'sass-loader',
			query: {
				outputStyle: 'production' === process.env.NODE_ENV ? 'compressed' : 'nested',
			},
		},
	],
};

const config = {
	mode: process.env.NODE_ENV === 'production' ? 'production' : 'development',
	resolve: {
		modules: [
			`${ __dirname }/resources/assets`,
			'node_modules',
		],
	},
	entry: {
		settings: './resources/assets/js/settings.js',
		'wp-login': './resources/assets/js/wp-login.js',
	},
	output: {
		filename: 'public/js/[name].min.js',
		path: __dirname,
	},
	module: {
		rules: [
			{
				test: /.js$/,
				use: 'babel-loader',
				exclude: /node_modules/,
				include: /js/,
			},
		],
	},
	externals: {
		jquery: 'jQuery',
		$: 'jQuery',
	},
	plugins: [
		new CopyWebpackPlugin( [
			{
				from: 'resources/assets/images',
				to: 'public/images',
			},
		] ),
		new webpack.ProvidePlugin( {
			$: 'jquery',
			jQuery: 'jquery',
			'window.jQuery': 'jquery',
		} ),
	],
};

// Add CSS extraction.
cssFiles.forEach( ( name ) => {
	const plugin = new ExtractTextPlugin( {
		filename: `./public/css/${ name }.min.css`,
	} );

	const rule = {
		test: new RegExp( `${ name }\.css$` ),
		use: plugin.extract( extractConfig ),
		include: /css/,
	};

	config.plugins.push( plugin );
	config.module.rules.push( rule );
} );

if ( config.mode !== 'production' ) {
	config.devtool = process.env.SOURCEMAP || 'source-map';
}

module.exports = config;
