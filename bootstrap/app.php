<?php
/**
 * Load the application.
 *
 * @since 1.0.0
 *
 * @package PluginScaffold
 * @category Bootstrap
 * @author Astoundify
 */

namespace Astoundify\WC_Simple_Social_Login;

// Load helper functions.
require_once( ASTOUNDIFY_WC_SIMPLE_SOCIAL_LOGIN_PATH . 'app/functions.php' );
require_once( ASTOUNDIFY_WC_SIMPLE_SOCIAL_LOGIN_PATH . 'app/template-functions.php' );

/**
 * Initialize plugin.
 *
 * @since 1.0.0
 */
add_action( 'plugins_loaded', function() {

	// Load text domain.
	load_plugin_textdomain( dirname( ASTOUNDIFY_WC_SIMPLE_SOCIAL_LOGIN_PATH ), false, dirname( ASTOUNDIFY_WC_SIMPLE_SOCIAL_LOGIN_PATH ) . '/resources/languages/' );

	// Load scripts.
	add_action( 'wp_enqueue_scripts', function() {

		// Load CSS.
		wp_enqueue_style( 'astoundify-wc-simple-social-login', ASTOUNDIFY_WC_SIMPLE_SOCIAL_LOGIN_URL . 'public/css/wc-simple-social-login.min.css', array(), ASTOUNDIFY_WC_SIMPLE_SOCIAL_LOGIN_VERSION );

		// Load JS.
		wp_enqueue_script( 'astoundify-wc-simple-social-login', ASTOUNDIFY_WC_SIMPLE_SOCIAL_LOGIN_URL . 'public/js/wc-simple-social-login.min.js', array( 'jquery' ), ASTOUNDIFY_WC_SIMPLE_SOCIAL_LOGIN_VERSION, true );

	} );

} );
