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
 * Check Request.
 */
if ( ! isset( $_GET['astoundify_simple_social_login'], $_GET['action'] ) ) {
	return false;
}

do_action( 'astoundify_simple_social_login_process_' . $_GET['astoundify_simple_social_login'], $_GET['action'] );
exit;
