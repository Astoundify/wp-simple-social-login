<?php
/**
 * Plugin Name: Astoundify WC Simple Social Login
 * Plugin URI: https://astoundify.com/
 * Description: Simple social login for WooCommerce.
 * Version: 1.0.0
 * Author: Astoundify
 * Author URI: https://astoundify.com/
 * Requires at least: 4.8.0
 * Tested up to: 4.8
 * Text Domain: astoundify-plugin-scaffold
 * Domain Path: resources/languages/
 *
 *    Copyright: 2017 Astoundify
 *    License: GNU General Public License v3.0
 *    License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package Plugin
 * @category Core
 * @author Astoundify
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
function astoundify_wc_simple_social_login_php_notice() {
	// Translators: %1$s minimum PHP version, %2$s current PHP version.
	$notice = sprintf( __( 'Astoundify WC Simple Social Login plugin requires at least PHP %1$s. You are running PHP %2$s. Please upgrade and try again.', 'astoundify-wc-simple-social-login' ), '<code>5.4.0</code>', '<code>' . PHP_VERSION . '</code>' );
?>

<div class="notice notice-error">
	<p><?php echo wp_kses_post( $notice, array( 'code' ) ); ?></p>
</div>

<?php
}

// Check for PHP version..
if ( version_compare( PHP_VERSION, '5.4', '<' ) ) {
	add_action( 'admin_notices', 'astoundify_wc_simple_social_login_php_notice' );

	return;
}

// Plugin can be loaded... define some constants.
define( 'ASTOUNDIFY_WC_SIMPLE_SOCIAL_LOGIN_VERSION', '1.0.0' );
define( 'ASTOUNDIFY_WC_SIMPLE_SOCIAL_LOGIN_FILE', __FILE__ );
define( 'ASTOUNDIFY_WC_SIMPLE_SOCIAL_LOGIN_PLUGIN', plugin_basename( __FILE__ ) );
define( 'ASTOUNDIFY_WC_SIMPLE_SOCIAL_LOGIN_PATH', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'ASTOUNDIFY_WC_SIMPLE_SOCIAL_LOGIN_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'ASTOUNDIFY_WC_SIMPLE_SOCIAL_LOGIN_TEMPLATE_PATH', trailingslashit( ASTOUNDIFY_WC_SIMPLE_SOCIAL_LOGIN_PATH . 'resources/templates' ) );

/**
 * Plugin Updater.
 *
 * @since 1.0.0
 */
function astoundify_wc_simple_social_login_updater() {
	//require_once( ASTOUNDIFY_WC_SIMPLE_SOCIAL_LOGIN_PATH . 'vendor/astoundify/plugin-updater/astoundify-pluginupdater.php' );

	//new Astoundify_PluginUpdater( __FILE__ );
}
add_action( 'admin_init', 'astoundify_wc_simple_social_login_updater', 9 );

/**
 * Load auto loader.
 *
 * @since 1.0.0
 */
require_once( ASTOUNDIFY_WC_SIMPLE_SOCIAL_LOGIN_PATH . 'bootstrap/autoload.php' );

/**
 * Start the application.
 *
 * @since 1.0.0
 */
require_once( ASTOUNDIFY_WC_SIMPLE_SOCIAL_LOGIN_PATH . 'bootstrap/app.php' );
