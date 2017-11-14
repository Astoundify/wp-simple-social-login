<?php
/**
 * Social Login Settings Functions.
 *
 * @since 1.0.0
 *
 * @package Admin
 * @category Functions
 * @author Astoundify
 */

// Do not access this file directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Settings
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_register_settings() {
	register_setting(
		$option_group      = 'astoundify_simple_social_login',
		$option_name       = 'astoundify_simple_social_login',
		$sanitize_callback = 'astoundify_simple_social_login_sanitize_settings'
	);
}
add_action( 'admin_init', 'astoundify_simple_social_login_register_settings' );

/**
 * Sanitize Options
 *
 * @since 1.0.0
 *
 * @param mixed $input Input.
 * @return array
 */
function astoundify_simple_social_login_sanitize_settings( $input ) {
	return $input;
}

/**
 * Add Admin Menu
 *
 * @since 1.0.0
 * @link https://codex.wordpress.org/Function_Reference/add_options_page
 */
function astoundify_simple_social_login_add_menu_page() {
	add_options_page(
		$page_title  = esc_html( 'Astoundify Simple Social Login', 'astoundify-simple-social-login' ),
		$menu_title  = esc_html( 'Simple Social Login', 'astoundify-simple-social-login' ),
		$capability  = 'manage_options',
		$menu_slug   = 'astoundify-simple-social-login',
		$function    = 'astoundify_simple_social_login_menu_page_html'
	);
}
add_action( 'admin_menu', 'astoundify_simple_social_login_add_menu_page' );

/**
 * Settings HTML
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_menu_page_html() {
?>
<div class="wrap">
	<h1><?php esc_html_e( 'Astoundify Simple Social Login', 'astoundify-simple-social-login' ); ?></h1>
</div>
<?php
}

/**
 * Settings Page Scripts
 *
 * @since 1.0.0
 *
 * @param string $hook_suffix Hook suffix.
 */
function astoundify_simple_social_login_admin_enqueue_scripts( $hook_suffix ) {
	// Do not load if not in settings page.
	if ( 'settings_page_astoundify-simple-social-login' !== $hook_suffix  ) {
		return;
	}

	// Script Vars.
	$debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? true : false;
	$version = $debug ? time() : ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_VERSION;

	// Settings CSS.
	$url = ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_URL . 'public/assets/css/settings.min.css';
	if ( $debug ) {
		$url = ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_URL . 'resources/assets/css/settings.css';
	}
	wp_enqueue_style( 'astoundify-simple-social-login', $url, array(), $version );

	// Settings JS.
	$url = ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_URL . 'public/assets/js/settings.min.js';
	if ( $debug ) {
		$url = ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_URL . 'resources/assets/js/settings.js';
	}
	wp_enqueue_script( 'astoundify-simple-social-login', $url, array( 'jquery' ), $version, true );
}
add_action( 'admin_enqueue_scripts', 'astoundify_simple_social_login_admin_enqueue_scripts' );


























