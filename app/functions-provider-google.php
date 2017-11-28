<?php
/**
 * Google Functions.
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
 * Register Google as Provider
 *
 * @since 1.0.0
 *
 * @param array $providers Service providers.
 * @return array
 */
function astoundify_simple_social_login_google_add_provider( $providers ) {
	$providers['google'] = '\Astoundify\Simple_Social_Login\Provider_Google';
	return $providers;
}
add_filter( 'astoundify_simple_social_login_providers', 'astoundify_simple_social_login_google_add_provider' );

/**
 * Process Button Action Request.
 *
 * @since 1.0.0
 *
 * @param string $action Request action.
 * @param string $referer URL.
 */
function astoundify_simple_social_login_google_process_action( $action, $referer ) {
	// Bail if not active.
	$provider = astoundify_simple_social_login_get_provider( 'google' );
	if ( ! $provider || ! $provider->is_active() ) {
		wp_safe_redirect( esc_url_raw( urldecode( $referer ) ) );
		exit;
	}

	// Separate each action.
	switch ( $action ) {
		case 'login_register':
			if ( is_user_logged_in() ) {
				$provider->error_redirect( 'already_logged_in' );
			}

			$data = $provider->api_get_data();
			if ( ! $data || ! isset( $data['id'] ) ) {
				$provider->error_redirect( 'api_error' );
			}

			// Get connected user ID.
			$user_id = $provider->get_connected_user_id( $data['id'] );

			// User found. Log them in.
			if ( $user_id ) {
				astoundify_simple_social_login_log_user_in( $user_id );
				$provider->success_redirect( urldecode( $referer ) );
			}

			// If registration disabled. bail.
			if ( ! astoundify_simple_social_login_is_registration_enabled() ) {
				$provider->error_redirect( 'connected_user_not_found' );
			}

			// Register user.
			$user_id = $provider->insert_user( $data, $referer );
			if ( ! $user_id ) {
				$provider->error_redirect( 'registration_fail' );
			}

			// Log them in.
			astoundify_simple_social_login_log_user_in( $user_id );

			// Redirect to home, if in login page.
			$provider->success_redirect( urldecode( $referer ) );

			break;
		case 'link':
			if ( ! is_user_logged_in() ) {
				$provider->error_redirect( 'not_logged_in', urldecode( $referer ) );
			}

			$is_connected = $provider->is_user_connected( get_current_user_id() );
			if ( $is_connected ) {
				$provider->error_redirect( 'already_connected', urldecode( $referer ) );
			}

			$data = $provider->api_get_data();
			if ( ! $data || ! isset( $data['id'] ) ) {
				$provider->error_redirect( 'api_error' );
			}

			// Get connected user ID.
			$user_id = $provider->get_connected_user_id( $data['id'] );
			if ( $user_id ) {
				$provider->error_redirect( 'another_already_connected', urldecode( $referer ) );
			}

			// Link user.
			$link = $provider->link_user( array(
				'id'    => $data['id'],
				'gmail' => $data['gmail'],
			) );

			if ( ! $link ) {
				$provider->error_redirect( 'link_fail', urldecode( $referer ) );
			}

			$provider->success_redirect( urldecode( $referer ) );

			break;
		case 'unlink':
			if ( ! is_user_logged_in() ) {
				$provider->error_redirect( 'not_logged_in' );
			}

			$provider->unlink_user( get_current_user_id() );
			$provider->success_redirect( urldecode( $referer ) );

			break;
		default:
			$provider->error_redirect( 'unknown_action' );
	}
}
add_action( 'astoundify_simple_social_login_process_google', 'astoundify_simple_social_login_google_process_action', 10, 2 );
