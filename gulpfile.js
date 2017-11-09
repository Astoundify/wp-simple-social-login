var gulp            = require( 'gulp' );
var template        = require( 'gulp-template' );
var fs              = require( 'fs' );
var concat          = require( 'gulp-concat' );
var pump            = require( 'pump' );
var pkg             = JSON.parse( fs.readFileSync( './package.json' ) );

var cleanCss        = require( 'gulp-clean-css' );
var scsslint        = require( 'gulp-csslint' );

var wpPot           = require( 'gulp-wp-pot' );
var checktextdomain = require( 'gulp-checktextdomain' );

var zip             = require( 'gulp-zip' );
var clean           = require( 'gulp-clean' );

var uglify          = require( 'gulp-uglify' );
var jshint          = require( 'gulp-jshint' );

var sourcemaps      = require( 'gulp-sourcemaps' );

var watch           = require( 'gulp-watch' );

var phpcs           = require( 'gulp-phpcs' );

/**
 * JS Hint
 * 
 * @since 1.0.0
 */
gulp.task( 'js:hint', function( cb ) {
	pump( [
		gulp.src( [
			'resources/assets/**/*.js',
		]),
		jshint( '.jshintrc' ),
		jshint.reporter( 'default' ),
		jshint.reporter( 'fail' )
	], cb );
} );

/**
 * JS Minify
 * 
 * @since 1.0.0
 */
gulp.task( 'js:minify', function() {
	gulp.src( 'resources/assets/js/wc-simple-social-login.js' )
		.pipe( sourcemaps.init() )
		.pipe( concat( 'wc-simple-social-login.min.js' ) )
		.pipe( uglify() )
		.pipe( sourcemaps.mapSources( function( sourcePath, file ) {
			return 'resources/assets/js/' + sourcePath;
		}))
		.pipe( sourcemaps.write( './' ) )
		.pipe( gulp.dest( 'public/js' ) );
} );

/**
 * CSS Lint
 *
 * @since 1.0.0
 */
gulp.task( 'css:lint', function( cb ) {
	pump( [
		gulp.src( [
			'resources/assets/css/*.css',
			'resources/assets/css/**/*.css'
		] ),
		scsslint( {
			'maxBuffer': 10007200,
			'reporterOutputFormat': 'Stats'
		} )
	], cb );
} );

/**
 * CSS Minify
 * 
 * @since 1.0.0
 */
gulp.task( 'css:minify', function() {
	gulp.src( 'resources/assets/css/wc-simple-social-login.css' )
		.pipe( sourcemaps.init() )
		.pipe( concat( 'wc-simple-social-login.min.css' ) )
		.pipe( cleanCss() )
		.pipe( sourcemaps.mapSources( function( sourcePath, file ) {
			return 'resources/assets/css/' + sourcePath;
		}))
		.pipe( sourcemaps.write( './' ) )
		.pipe( gulp.dest( 'public/css' ) );
} );

/* Minify */
gulp.task( 'minify', [ 'css:minify', 'js:minify'] );

/** Assets */
gulp.task( 'assets', [ 'minify' ] );

/**
 * Watch
 * 
 * @since 1.0.0
 */
gulp.task( 'watch', function () {
	// watch CSS
	gulp.watch( 'resources/assets/css/*.css', [ 'css:minify' ] );
});

/**
 * Check Textdomain
 * 
 * @since 1.0.0
 */
gulp.task( 'checktextdomain', function() {
	gulp.src( [ 
		'*.php', 
		'app/**/**.php', 
		'resources/**/**.php'
	] )
		.pipe( checktextdomain( {
			text_domain: 'astoundify-wc-simple-social-login',
			correct_domain: true,
			force: true,
			keywords: [
				'__:1,2d',
				'_e:1,2d',
				'_x:1,2c,3d',
				'esc_html__:1,2d',
				'esc_html_e:1,2d',
				'esc_html_x:1,2c,3d',
				'esc_attr__:1,2d',
				'esc_attr_e:1,2d',
				'esc_attr_x:1,2c,3d',
				'_ex:1,2c,3d',
				'_n:1,2,4d',
				'_nx:1,2,4c,5d',
				'_n_noop:1,2,3d',
				'_nx_noop:1,2,3c,4d'
			],
		} ) );
} );

/**
 * Make POT
 * 
 * @since 1.0.0
 */
gulp.task( 'makepot', function() {
	gulp.src( [ 
		'*.php', 
		'app/**/**.php', 
		'resources/**/**.php'
	] )
		.pipe( wpPot( {
			domain: 'astoundify-wc-simple-social-login',
		} ))
		.pipe( gulp.dest( 'resources/languages/astoundify-wc-simple-social-login.pot' ) );
} );

/* i18n */
gulp.task( 'i18n', [ 'checktextdomain', 'makepot' ] );

/**
 * PHP Code Sniffer
 *
 * @since 1.0.0
 */
gulp.task( 'php', function() {
	gulp.src( [
		'app/*.php',
		'app/**/*.php',
		'bootstrap/*.php',
		'resources/*.php',
		'resources/**.php'
	] )
		.pipe( phpcs({
			'standard': './phpcs.ruleset.xml'
		}) )
		.pipe( phpcs.reporter( 'log' ) )
} );

/**
 * Clean build files.
 *
 * @since 1.0.0
 */
gulp.task( 'clean', function() {
	gulp.src( [ './astoundify-wc-simple-social-login', '*.zip' ], {
		read: false
	} )
		.pipe( clean() );
} );

/**
 * Move distribution files to a /dist directory.
 *
 * @since 1.0.0
 */
gulp.task( 'bundle', [ 'clean', 'assets', 'makepot' ], function( cb ) {
	gulp.src( [
		'astoundify-wc-simple-social-login.php',
		'app/*',
		'app/**',
		'bootstrap/*',
		'resources/*',
		'resources/**',
		'public/*',
		'public/**',
		'vendor/autoload.php',
		'vendor/composer/*',
		'vendor/composer/**',
		'vendor/astoundify/*',
		'vendor/astoundify/**',
		'LICENSE',
		'readme.txt'
	], {
		base: './'
	} )
		.pipe( gulp.dest( 'astoundify-wc-simple-social-login' ) );

	cb();
} );

/**
 * ZIP
 *
 * @since 1.0.0
 */
gulp.task( 'zip', function() {
	gulp.src( [ '<%= slug %>/**' ], {
		base: './'
 	} )
		.pipe( zip( 'astoundify-wc-simple-social-login-' + pkg.version + '.zip' ) )
		.pipe( gulp.dest( '' ) );
} );
