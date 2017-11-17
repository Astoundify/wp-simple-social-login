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
require_once( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'app/functions-template.php' );

// Providers.
require_once( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'app/functions-facebook.php' );
require_once( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'app/functions-facebook-api.php' );


/**
 * Initialize plugin.
 *
 * @since 1.0.0
 */
add_action( 'plugins_loaded', function() {

	// Load Facebook SDK if facebook is selected.
	if ( astoundify_simple_social_login_is_provider_active( 'facebook' ) ) {
		require_once( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'vendor/facebook/graph-sdk/src/Facebook/autoload.php' );
	}

	// Load text domain.
	load_plugin_textdomain( dirname( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH ), false, dirname( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH ) . '/resources/languages/' );

	// Admin Settings
	require_once( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'app/admin/functions-settings.php' );
	require_once( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'app/admin/functions-settings-facebook.php' );
	require_once( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'app/admin/functions-settings-twitter.php' );

	// WordPress Integrations.
	require_once( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'app/functions-wordpress.php' );

	// WooCommerce Integrations.
	if ( class_exists( 'WooCommerce' ) ) {
		require_once( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'app/functions-woocommerce.php' );
		require_once( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'app/admin/functions-settings-woocommerce.php' );
	}

} );
