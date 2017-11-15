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






















