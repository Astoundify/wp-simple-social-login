<?php
/**
 * Social Login Facebook Settings Functions.
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
 * Register Facebook Settings
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_facebook_register_settings() {
	register_setting(
		$option_group      = 'astoundify_simple_social_login',
		$option_name       = 'astoundify_simple_social_login_facebook',
		$sanitize_callback = 'astoundify_simple_social_login_facebook_sanitize_settings'
	);
}
add_action( 'admin_init', 'astoundify_simple_social_login_facebook_register_settings' );

/**
 * Sanitize Facebook Settings
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_facebook_sanitize_settings( $input ) {
	return $input;
}

/**
 * Add Settings
 *
 * @since 1.0.0
 *
 * @param array $settings Settings Section.
 * @return array
 */
function astoundify_simple_social_login_facebook_add_settings( $settings ) {
	$settings['facebook'] = esc_html( 'Facebook', 'astoundify-simple-social-login' );
	return $settings;
}
add_filter( 'astoundify_simple_social_login_settings_tabs', 'astoundify_simple_social_login_facebook_add_settings' );

/**
 * Facebook Settings Panel
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_panel_facebook() {
?>
<p>Lalalalaa...</p>
<?php
}
add_action( 'astoundify_simple_social_login_panel_facebook', 'astoundify_simple_social_login_panel_facebook' );


















