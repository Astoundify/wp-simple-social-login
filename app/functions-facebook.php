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
 * Process Button Action Request.
 *
 * @since 1.0.0
 *
 * @param string $action Request action.
 * @param string $referer URL.
 */
function astoundify_simple_social_login_facebook_process_action( $action, $referer ) {
	// Bail if not active.
	$facebook = new \Astoundify\Simple_Social_Login\Provider_Facebook;
	if ( ! $facebook->is_active() ) {
		wp_safe_redirect( esc_url_raw( urldecode( $referer ) ) );
		exit;
	}

	// Separate each action.
	switch ( $action ) {
		case 'login_register':
			if ( is_user_logged_in() ) {
				$facebook->redirect( urldecode( $referer ), 'already_log_in' );
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
				$facebook->redirect( urldecode( $referer ), 'already_log_in' );
			}

			// Success URL:
			$url = urldecode( $referer );
			if ( false !== strpos( $url, '/wp-login.php' ) ) {
				$url = home_url();
			}

			// Get facebook data.
			$data = $facebook->api_get_data( $referer );
			if ( ! $data || ! isset( $data['id'] ) ) {
				$facebook->redirect( urldecode( $referer ), 'api_error' );
			}

			// Get connected user ID.
			$user_id = $facebook->get_connected_user_id( $data['id'] );

			// User found. Log them in.
			if ( $user_id ) {
				astoundify_simple_social_login_log_user_in( $user_id );
				$facebook->redirect( urldecode( $url ) );
			}

			// If registration disabled. bail.
			if ( ! astoundify_simple_social_login_is_registration_enabled() ) {
				$facebook->redirect( urldecode( $referer ), 'connected_user_not_found' );
			}

			// Register user.
			$user_id = $facebook->insert_user( $data );
			if ( ! $user_id ) {
				$facebook->redirect( urldecode( $referer ), 'registration_fail' );
			}

			// Log them in.
			astoundify_simple_social_login_log_user_in( $user_id );

			// Redirect to home, if in login page.
			$facebook->redirect( $url );

			break;
		case 'link':
			if ( ! is_user_logged_in() ) {
				wp_safe_redirect( esc_url_raw( urldecode( $referer ) ) );
				exit;
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
				wp_safe_redirect( esc_url_raw( urldecode( $referer ) ) );
				exit;
			}

			$is_connected = $facebook->is_user_connected( get_current_user_id() );
			if ( $is_connected ) {
				$facebook->redirect( urldecode( $referer ), 'already_connected' );
			}

			// Get facebook data.
			$data = $facebook->api_get_data();
			if ( ! $data || ! isset( $data['id'] ) ) {
				$facebook->redirect( urldecode( $referer ), 'api_error' );
			}

			// Link user.
			$link = $facebook->link_user( array(
				'id' => $data['id'],
			) );

			if ( ! $link ) {
				$facebook->redirect( urldecode( $referer ), 'link_fail' );
			}

			$facebook->redirect( urldecode( $referer ) );

			break;
		case 'unlink':
			if ( ! is_user_logged_in() ) {
				wp_safe_redirect( esc_url_raw( urldecode( $referer ) ) );
				exit;
			}

			$facebook->unlink_user( get_current_user_id() );
			$facebook->redirect( urldecode( $referer ) );

			break;
		default:
			$facebook->redirect( urldecode( $referer ), 'unknown_action' );
	}
}
add_action( 'astoundify_simple_social_login_process_facebook', 'astoundify_simple_social_login_facebook_process_action', 10, 2 );
