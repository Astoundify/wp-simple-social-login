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

namespace Astoundify\Simple_Social_Login;

// Load helper functions.
require_once( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'app/functions.php' );
require_once( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'app/template-functions.php' );

/**
 * Initialize plugin.
 *
 * @since 1.0.0
 */
add_action( 'plugins_loaded', function() {

	// Load text domain.
	load_plugin_textdomain( dirname( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH ), false, dirname( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH ) . '/resources/languages/' );

	// Admin Settings
	require_once( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'app/admin/functions-settings.php' );
	require_once( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'app/admin/functions-settings-facebook.php' );
	require_once( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'app/admin/functions-settings-twitter.php' );

	// WooCommerce Settings.
	if ( class_exists( 'WooCommerce' ) ) {
		require_once( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'app/admin/functions-settings-woocommerce.php' );
	}

} );
