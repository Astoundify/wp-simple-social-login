<?php
/**
 * Helper functions.
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
 * Is Provider Active.
 *
 * @since 1.0.0
 *
 * @param string $provider The provider ID.
 * @return bool
 */
function astoundify_simple_social_login_is_provider_active( $provider ) {
	// Do not use sanitize functions, because this functions need to be loaded as early as possible.
	$option = (array)get_option( 'astoundify_simple_social_login', array() );
	$providers = isset( $option['providers'] ) && is_array( $option['providers'] ) ? $option['providers'] : array();

	return apply_filters( "astoundify_simple_social_login_is_{$provider}_active", in_array( $provider, $providers ) );
}

/**
 * Add Query Vars
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_add_query_vars( $vars ) {
	$vars[] = 'astoundify_simple_social_login';
	return $vars;
}
add_filter( 'query_vars', 'astoundify_simple_social_login_add_query_vars', 1 );

/**
 * Register Custom Template When visiting Query Vars.
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_template_include( $template ) {
	$get = get_query_var( 'astoundify_simple_social_login' );
	if ( $get ) {
		$template = ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_TEMPLATE_PATH . 'process.php';
	}
	return $template;
}
add_filter( 'template_include', 'astoundify_simple_social_login_template_include' );

/**
 * Log User In.
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_log_user_in( $user_id ) {
	// Bail if user already logged in.
	if ( is_user_logged_in() ) {
		return false;
	}

	// Get user data.
	$user        = get_userdata( $user_id );
	$user_login  = $user->user_login;

	// Enable remember me cookie.
	$remember_me = apply_filters( 'astoundify_simple_social_login_remember_me', true, $user_id );

	wp_set_auth_cookie( $user_id, $remember_me );
	wp_set_current_user( $user_id, $user_login );
	do_action( 'wp_login', $user_login, $user );
}

/**
 * Get Redirect URL.
 *
 * @since 1.0.0
 *
 * @param string $action Action: login, link, etc.
 * @return string
 */
function astoundify_simple_social_login_get_redirect_url( $action = 'login' ) {
	$url = is_singular() ? get_permalink( get_queried_object() ) : home_url();
	return esc_url( apply_filters( 'astoundify_simple_social_login_redirect_url', $url, $action ) );
}


















