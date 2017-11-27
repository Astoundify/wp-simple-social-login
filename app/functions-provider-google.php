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
 * Done Process. Endpoint For HybridAuth.
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_google_process_done() {
	require_once( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . "vendor/hybridauth/hybridauth/hybridauth/Hybrid/Auth.php" );
	Hybrid_Endpoint::process();
	wp_die();
	exit;
}
add_action( 'astoundify_simple_social_login_process_done', 'astoundify_simple_social_login_google_process_done' );

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
	$google = astoundify_simple_social_login_get_provider( 'google' );
	if ( ! $google || ! $google->is_active() ) {
		wp_safe_redirect( esc_url_raw( urldecode( $referer ) ) );
		exit;
	}

	// Separate each action.
	switch ( $action ) {
		case 'login_register':
			if ( is_user_logged_in() ) {
				$google->error_redirect( 'already_logged_in' );
			}

			$data = $google->api_get_data();
			if ( ! $data || ! isset( $data['id'] ) ) {
				$google->error_redirect( 'api_error' );
			}

			// Get connected user ID.
			$user_id = $google->get_connected_user_id( $data['id'] );

			// User found. Log them in.
			if ( $user_id ) {
				astoundify_simple_social_login_log_user_in( $user_id );
				$google->success_redirect( urldecode( $referer ) );
			}

			// If registration disabled. bail.
			if ( ! astoundify_simple_social_login_is_registration_enabled() ) {
				$google->error_redirect( 'connected_user_not_found' );
			}

			// Register user.
			$user_id = $google->insert_user( $data, $referer );
			if ( ! $user_id ) {
				$google->error_redirect( 'registration_fail' );
			}

			// Log them in.
			astoundify_simple_social_login_log_user_in( $user_id );

			// Redirect to home, if in login page.
			$google->success_redirect( urldecode( $referer ) );

			break;

			break;
		case 'link':
			if ( ! is_user_logged_in() ) {
				$google->error_redirect( 'not_logged_in', urldecode( $referer ) );
			}

			$is_connected = $google->is_user_connected( get_current_user_id() );
			if ( $is_connected ) {
				$google->error_redirect( 'already_connected', urldecode( $referer ) );
			}

			$data = $google->api_get_data();
			if ( ! $data || ! isset( $data['id'] ) ) {
				$google->error_redirect( 'api_error' );
			}

			// Get connected user ID.
			$user_id = $google->get_connected_user_id( $data['id'] );
			if ( $user_id ) {
				$google->error_redirect( 'another_already_connected', urldecode( $referer ) );
			}

			// Link user.
			$link = $google->link_user( array(
				'id'    => $data['id'],
				'gmail' => $data['gmail'],
			) );

			if ( ! $link ) {
				$google->error_redirect( 'link_fail', urldecode( $referer ) );
			}

			$google->success_redirect( urldecode( $referer ) );


			break;
		case 'unlink':
			if ( ! is_user_logged_in() ) {
				$google->error_redirect( 'not_logged_in' );
			}

			$google->unlink_user( get_current_user_id() );
			$google->success_redirect( urldecode( $referer ) );

			break;
		default:
			$google->error_redirect( 'unknown_action' );
	}
}
add_action( 'astoundify_simple_social_login_process_google', 'astoundify_simple_social_login_google_process_action', 10, 2 );
