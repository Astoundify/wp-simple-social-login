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
	return esc_url_raw( apply_filters( 'astoundify_simple_social_login_redirect_url', $url, $action ) );
}

/**
 * Is registration enabled.
 *
 * @since 1.0.0
 *
 * @return bool
 */
function astoundify_simple_social_login_is_registration_enabled() {
	return apply_filters( 'astoundify_simple_social_login_registration_enabled', true );
}

/**
 * Add Error
 *
 * @since 1.0.0
 *
 * @param string $id Error ID.
 * @param string $error Error Message.
 */
function astoundify_simple_social_login_add_error( $id, $error ) {
	// Set if not yet set.
	global $_astoundify_simple_social_login_error;
	if ( ! isset( $_astoundify_simple_social_login_error ) || ! is_array( $_astoundify_simple_social_login_error ) ) {
		$_astoundify_simple_social_login_error = array();
	}

	// Add error.
	$_astoundify_simple_social_login_error[ $id ] = $error;
}

/**
 * Get Errors
 *
 * @since 1.0.0
 *
 * @return array
 */
function astoundify_simple_social_login_get_errors() {
	// Set if not yet set.
	global $_astoundify_simple_social_login_error;
	if ( ! isset( $_astoundify_simple_social_login_error ) || ! is_array( $_astoundify_simple_social_login_error ) ) {
		$_astoundify_simple_social_login_error = array();
	}

	return $_astoundify_simple_social_login_error;
}












