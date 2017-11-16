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
 * - Redirect URL.
 * - Nonce.
 */
if ( ! isset( $_GET['astoundify_simple_social_login'], $_GET['action'], $_GET['redirect_to'], $_GET['_nonce'] ) || ! wp_verify_nonce( $_GET['_nonce'], "astoundify_simple_social_login_{$_GET['action']}" ) ) {
	wp_safe_redirect( esc_url_raw( urldecode( $_GET['redirect_to'] ) ) );
	exit;
}

do_action( 'astoundify_simple_social_login_process_' . $_GET['astoundify_simple_social_login'], $_GET['action'], $_GET['redirect_to'] );
exit;
