<?php
/**
 * Template to Process Login Request.
 *
 * @since 1.0.0
 */

/**
 * Session is required. So, start if not yet initiated.
 *
 * @since 1.0.0
 */
if ( ! session_id() ) {
	session_start();
}

/**
 * HybridAuth Endpoint.
 *
 * @since 1.0.0
 */
if ( isset( $_GET['astoundify_simple_social_login'] ) && 'done' === $_GET['astoundify_simple_social_login'] ) {
	do_action( 'astoundify_simple_social_login_process_done' );
	exit;
}

/**
 * Check request, redirect back if not valid.
 * - Provider.
 * - Action.
 * - Nonce.
 * - Referer.
 *
 * @since 1.0.0
 */
if ( ! isset( $_GET['astoundify_simple_social_login'], $_GET['action'], $_GET['_nonce'], $_GET['_referer'] ) || ! wp_verify_nonce( $_GET['_nonce'], "astoundify_simple_social_login_{$_GET['astoundify_simple_social_login']}" ) ) {
	wp_safe_redirect( esc_url_raw( home_url() ) );
	exit;
}

/**
 * Process Action.
 *
 * @since 1.0.0
 */
do_action( 'astoundify_simple_social_login_process', $_GET['astoundify_simple_social_login'], $_GET['action'], $_GET['_referer'] );
exit;
