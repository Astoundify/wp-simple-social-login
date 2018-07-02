<?php
/**
 * Plugin Name: Simple Social Login
 * Plugin URI: https://astoundify.com/products/simple-social-login/
 * Description: Simple Social Login by Astoundify for WooCommerce.
 * Version: 2.0.0-beta
 * Author: Astoundify
 * Author URI: https://astoundify.com/
 * Requires at least: 4.9.0
 * Tested up to: 4.9.6
 * Text Domain: astoundify-simple-social-login
 * Domain Path: resources/languages/
 *
 * @package  Plugin
 * @category Core
 * @author   Astoundify
 */

// Do not access this file directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Activation PHP Notice
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_php_notice() {
	// Translators: %1$s minimum PHP version, %2$s current PHP version.
	$notice = sprintf( __( 'Astoundify WC Simple Social Login plugin requires at least PHP %1$s. You are running PHP %2$s. Please upgrade and try again.', 'astoundify-simple-social-login' ), '<code>5.6.0</code>', '<code>' . PHP_VERSION . '</code>' );
?>

<div class="notice notice-error">
	<p><?php echo wp_kses_post( $notice, [ 'code' ] ); ?></p>
</div>

<?php
}

// Check for PHP version..
if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {
	add_action( 'admin_notices', 'astoundify_simple_social_login_php_notice' );

	return;
}

// Plugin can be loaded... define some constants.
define( 'ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_VERSION', '2.0.0-beta' );
define( 'ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_FILE', __FILE__ );
define( 'ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PLUGIN', plugin_basename( __FILE__ ) );
define( 'ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_TEMPLATE_PATH', trailingslashit( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'resources/templates' ) );

/**
 * Load auto loader.
 *
 * @since 1.0.0
 */
require_once ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'bootstrap/autoload.php';

/**
 * Start the application.
 *
 * @since 1.0.0
 */
require_once ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'bootstrap/app.php';
