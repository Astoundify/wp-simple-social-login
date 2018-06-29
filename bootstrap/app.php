<?php
/**
 * Load the application.
 *
 * @since 1.0.0
 *
 * @package  PluginScaffold
 * @category Bootstrap
 * @author   Astoundify
 */

namespace Astoundify\Simple_Social_Login;

// Load helper functions.
require_once ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'app/functions.php';
require_once ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'app/functions-display.php';
require_once ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'app/functions-template.php';

/**
 * Initialize plugin.
 *
 * @since 1.0.0
 */
add_action(
	'plugins_loaded', function () {

		// Load text domain.
		load_plugin_textdomain( dirname( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH ), false, dirname( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH ) . '/resources/languages/' );

		// Admin Settings
		include_once ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'app/admin/functions-settings.php';
		include_once ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'app/admin/functions-settings-facebook.php';
		include_once ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'app/admin/functions-settings-twitter.php';
		include_once ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'app/admin/functions-settings-google.php';

		// WordPress Integrations.
		include_once ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'app/functions-wordpress.php';

		// WooCommerce Integrations.
		if ( class_exists( 'WooCommerce' ) ) {
			include_once ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'app/functions-woocommerce.php';
			include_once ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'app/admin/functions-settings-woocommerce.php';
		}

	}
);
