<?php
/**
 * Facebook Functions.
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
 * Register Facebook as Provider
 *
 * @since 1.0.0
 *
 * @param array $providers Service providers.
 * @return array
 */
function astoundify_simple_social_login_facebook_add_provider( $providers ) {
	$providers['facebook'] = '\Astoundify\Simple_Social_Login\Provider_Facebook';
	return $providers;
}
add_filter( 'astoundify_simple_social_login_providers', 'astoundify_simple_social_login_facebook_add_provider' );

/**
 * Process Button Action Request.
 *
 * @since 1.0.0
 *
 * @param string $action Request action.
 * @param string $referer URL.
 */
function astoundify_simple_social_login_facebook_process_action( $action, $referer ) {
	// Bail if not active.
	$facebook = astoundify_simple_social_login_get_provider( 'facebook' );
	if ( ! $facebook || ! $facebook->is_active() ) {
		wp_safe_redirect( esc_url_raw( urldecode( $referer ) ) );
		exit;
	}

	// Separate each action.
	switch ( $action ) {
		case 'login_register':
			if ( is_user_logged_in() ) {
				$facebook->error_redirect( 'already_logged_in' );
			}

			$fb = $facebook->api_init();
			$helper = $fb->getRedirectLoginHelper();

			$process_url = add_query_arg( array(
				'astoundify_simple_social_login' => 'facebook',
				'action'                         => '_login_register',
				'_nonce'                         => wp_create_nonce( 'astoundify_simple_social_login_facebook' ),
				'_referer'                       => $referer,
			), home_url() );
			$scope = array( 'email' );

			$fb_url = $helper->getLoginUrl( $process_url, $scope );

			// Send request to facebook.
			wp_redirect( esc_url_raw( $fb_url ) );
			exit;

			break;
		case '_login_register':
			if ( is_user_logged_in() ) {
				$facebook->error_redirect( 'already_logged_in' );
			}

			// Get facebook data.
			$data = $facebook->api_get_data();
			if ( ! $data || ! isset( $data['id'] ) ) {
				$facebook->error_redirect( 'api_error' );
			}

			// Get connected user ID.
			$user_id = $facebook->get_connected_user_id( $data['id'] );

			// User found. Log them in.
			if ( $user_id ) {
				astoundify_simple_social_login_log_user_in( $user_id );
				$facebook->success_redirect();
			}

			// If registration disabled. bail.
			if ( ! astoundify_simple_social_login_is_registration_enabled() ) {
				$facebook->error_redirect( 'connected_user_not_found' );
			}

			// Register user.
			$user_id = $facebook->insert_user( $data, $referer );
			if ( ! $user_id ) {
				$facebook->error_redirect( 'registration_fail' );
			}

			// Log them in.
			astoundify_simple_social_login_log_user_in( $user_id );

			// Redirect to home, if in login page.
			$facebook->success_redirect();

			break;
		case 'link':
			if ( ! is_user_logged_in() ) {
				$facebook->error_redirect( 'not_logged_in' );
			}

			$is_connected = $facebook->is_user_connected( get_current_user_id() );
			if ( $is_connected ) {
				$facebook->error_redirect( 'already_connected' );
			}

			$fb = $facebook->api_init();
			$helper = $fb->getRedirectLoginHelper();

			$process_url = add_query_arg( array(
				'astoundify_simple_social_login' => 'facebook',
				'action'                         => '_link',
				'_nonce'                         => wp_create_nonce( 'astoundify_simple_social_login_facebook' ),
				'_referer'                       => $referer,
			), home_url() );
			$scope = array( 'email' );

			$fb_url = $helper->getLoginUrl( $process_url, $scope );

			// Send request to facebook.
			wp_redirect( esc_url_raw( $fb_url ) );
			exit;

			break;
		case '_link':
			if ( ! is_user_logged_in() ) {
				$facebook->error_redirect( 'not_logged_in' );
			}

			// Get facebook data.
			$data = $facebook->api_get_data();
			if ( ! $data || ! isset( $data['id'] ) ) {
				$facebook->error_redirect( 'api_error' );
			}

			// Get connected user ID.
			$user_id = $facebook->get_connected_user_id( $data['id'] );
			if ( $user_id ) {
				$facebook->error_redirect( 'another_already_connected' );
			}

			// Link user.
			$link = $facebook->link_user( array(
				'id' => $data['id'],
			) );

			if ( ! $link ) {
				$facebook->error_redirect( 'link_fail' );
			}

			$facebook->success_redirect();

			break;
		case 'unlink':
			if ( ! is_user_logged_in() ) {
				wp_safe_redirect( esc_url_raw( urldecode( $referer ) ) );
				exit;
			}

			$facebook->unlink_user( get_current_user_id() );
			$facebook->success_redirect();

			break;
		default:
			$facebook->error_redirect( 'unknown_action' );
	}
}
add_action( 'astoundify_simple_social_login_process_facebook', 'astoundify_simple_social_login_facebook_process_action', 10, 2 );
