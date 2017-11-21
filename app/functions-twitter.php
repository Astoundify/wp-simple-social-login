<?php
/**
 * Twitter Functions.
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
 * Register Twitter as Provider
 *
 * @since 1.0.0
 *
 * @param array $providers Service providers.
 * @return array
 */
function astoundify_simple_social_login_twitter_add_provider( $providers ) {
	$providers['twitter'] = '\Astoundify\Simple_Social_Login\Provider_Twitter';
	return $providers;
}
add_filter( 'astoundify_simple_social_login_providers', 'astoundify_simple_social_login_twitter_add_provider' );

/**
 * Process Button Action Request.
 *
 * @since 1.0.0
 *
 * @param string $action Request action.
 * @param string $referer URL.
 */
function astoundify_simple_social_login_twitter_process_action( $action, $referer ) {
	// Bail if not active.
	$twitter = astoundify_simple_social_login_get_provider( 'twitter' );
	if ( ! $twitter || ! $twitter->is_active() ) {
		wp_safe_redirect( esc_url_raw( urldecode( $referer ) ) );
		exit;
	}

	// Separate each action.
	switch ( $action ) {
		case 'login_register':
			if ( is_user_logged_in() ) {
				$twitter->redirect( urldecode( $referer ), 'already_log_in' );
			}

			$tw = $twitter->api_init();

			// Process URL.
			$process_url = add_query_arg( array(
				'astoundify_simple_social_login' => 'twitter',
				'action'                         => '_login_register',
				'_nonce'                         => wp_create_nonce( 'astoundify_simple_social_login_twitter' ),
				'_referer'                       => $referer,
			), home_url() );

			// Get tokens: oauth_token, oauth_token_secret, oauth_callback_confirmed.
			$tokens = $tw->oauth( 'oauth/request_token', array(
				'oauth_callback' => esc_url_raw( $process_url ),
			) );

			// Error.
			if( $tw->getLastHttpCode() !== 200 ) {
				$facebook->redirect( urldecode( $referer ), 'api_error' );
			}

			// Store tokens in session.
			$_SESSION['astoundify_simple_social_login_twitter_oauth_token'] = $tokens['oauth_token'];
			$_SESSION['astoundify_simple_social_login_twitter_oauth_token_secret'] = $tokens['oauth_token_secret'];

			// Generate the URL to make request to authorize our application
			$tw_url = $tw->url( 'oauth/authenticate', array(
				'oauth_token' => $tokens['oauth_token'],
			) );

			// Send request to Twitter.
			wp_redirect( esc_url_raw( $tw_url ) );
			exit;

			break;
		case '_login_register':
			if ( is_user_logged_in() ) {
				$twitter->redirect( urldecode( $referer ), 'already_log_in' );
			}

			// Success URL:
			$url = urldecode( $referer );
			if ( false !== strpos( $url, '/wp-login.php' ) ) {
				$url = home_url();
			}

			// Get twitter data.
			$data = $twitter->api_get_data( $referer );
			if ( ! $data || ! isset( $data['id'] ) ) {
				$twitter->redirect( urldecode( $referer ), 'api_error' );
			}

			// Get connected user ID.
			$user_id = $twitter->get_connected_user_id( $data['id'] );

			// User found. Log them in.
			if ( $user_id ) {
				astoundify_simple_social_login_log_user_in( $user_id );
				$twitter->redirect( urldecode( $url ) );
			}

			// If registration disabled. bail.
			if ( ! astoundify_simple_social_login_is_registration_enabled() ) {
				$twitter->redirect( urldecode( $referer ), 'connected_user_not_found' );
			}

			// Register user.
			$user_id = $twitter->insert_user( $data, $referer );
			if ( ! $user_id ) {
				$twitter->redirect( urldecode( $referer ), 'registration_fail' );
			}

			// Log them in.
			astoundify_simple_social_login_log_user_in( $user_id );

			// Redirect to home, if in login page.
			$twitter->redirect( $url );

			break;
		case 'link':
			if ( ! is_user_logged_in() ) {
				$twitter->redirect( urldecode( $referer ), 'already_log_in' );
			}

			$is_connected = $twitter->is_user_connected( get_current_user_id() );
			if ( $is_connected ) {
				$twitter->redirect( urldecode( $referer ), 'already_connected' );
			}

			$tw = $twitter->api_init();

			// Process URL.
			$process_url = add_query_arg( array(
				'astoundify_simple_social_login' => 'twitter',
				'action'                         => '_link',
				'_nonce'                         => wp_create_nonce( 'astoundify_simple_social_login_twitter' ),
				'_referer'                       => $referer,
			), home_url() );

			// Get tokens: oauth_token, oauth_token_secret, oauth_callback_confirmed.
			$tokens = $tw->oauth( 'oauth/request_token', array(
				'oauth_callback' => esc_url_raw( $process_url ),
			) );

			// Error.
			if( $tw->getLastHttpCode() !== 200 ) {
				$facebook->redirect( urldecode( $referer ), 'api_error' );
			}

			// Store tokens in session.
			$_SESSION['astoundify_simple_social_login_twitter_oauth_token'] = $tokens['oauth_token'];
			$_SESSION['astoundify_simple_social_login_twitter_oauth_token_secret'] = $tokens['oauth_token_secret'];

			// Generate the URL to make request to authorize our application
			$tw_url = $tw->url( 'oauth/authenticate', array(
				'oauth_token' => $tokens['oauth_token'],
			) );

			// Send request to Twitter.
			wp_redirect( esc_url_raw( $tw_url ) );
			exit;

			break;
		case '_link':
			if ( ! is_user_logged_in() ) {
				wp_safe_redirect( esc_url_raw( urldecode( $referer ) ) );
				exit;
			}

			// Get twitter data.
			$data = $twitter->api_get_data( $referer );
			if ( ! $data || ! isset( $data['id'] ) ) {
				$twitter->redirect( urldecode( $referer ), 'api_error' );
			}

			// Link user.
			$link = $twitter->link_user( array(
				'id' => $data['id'],
			) );

			if ( ! $link ) {
				$twitter->redirect( urldecode( $referer ), 'link_fail' );
			}

			$twitter->redirect( urldecode( $referer ) );

			break;
		case 'unlink':
			if ( ! is_user_logged_in() ) {
				wp_safe_redirect( esc_url_raw( urldecode( $referer ) ) );
				exit;
			}

			$twitter->unlink_user( get_current_user_id() );
			$twitter->redirect( urldecode( $referer ) );

			break;
		default:
			$twitter->redirect( urldecode( $referer ), 'unknown_action' );
	}
}
add_action( 'astoundify_simple_social_login_process_twitter', 'astoundify_simple_social_login_twitter_process_action', 10, 2 );
