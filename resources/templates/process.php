<?php
/**
 * Template to Process Login Request.
 *
 * @since 1.0.0
 */

/**
 * Session is required. So, start if not yet initiated.
 */
if( ! session_id() ) {
	session_start();
}

/**
 * Check request, redirect back if not valid.
 * - Provider.
 * - Action.
 * - Nonce.
 */
if ( ! isset( $_GET['astoundify_simple_social_login'], $_GET['action'], $_GET['_nonce'], $_GET['_referer'] ) || ! wp_verify_nonce( $_GET['_nonce'], "astoundify_simple_social_login_{$_GET['astoundify_simple_social_login']}" ) ) {
	wp_die( 'batman111' );
	wp_safe_redirect( esc_url_raw( home_url() ) );
	exit;
}

do_action( 'astoundify_simple_social_login_process_' . $_GET['astoundify_simple_social_login'], $_GET['action'], $_GET['_referer'] );
exit;
