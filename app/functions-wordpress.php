<?php
/**
 * Facebook WordPress.
 *
 * @since 1.0.0
 *
 * @package Functions
 * @category Functions
 * @author Astoundify
 */

// Do not access this file directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add Button to wp-login.php Login Form.
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_wp_login() {
	if ( ! astoundify_simple_social_login_is_display_location_active( 'wp_login' ) && astoundify_simple_social_login_is_wp_login_page() ) {
		return;
	}
	$providers = astoundify_simple_social_login_get_providers();
	if ( ! $providers || ! is_array( $providers ) ) {
		return '';
	}
	echo astoundify_simple_social_login_get_login_register_buttons();
}
add_action( 'login_form', 'astoundify_simple_social_login_wp_login' );

