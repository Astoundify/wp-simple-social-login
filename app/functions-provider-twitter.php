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
				$twitter->error_redirect( 'already_logged_in' );
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
				$twitter->error_redirect( 'api_error' );
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
				$twitter->error_redirect( 'already_logged_in' );
			}

			// Success URL:
			$url = urldecode( $referer );
			if ( false !== strpos( $url, '/wp-login.php' ) ) {
				$url = home_url();
			}

			// Get twitter data.
			$data = $twitter->api_get_data();
			if ( ! $data || ! isset( $data['id'] ) ) {
				$twitter->error_redirect( 'api_error' );
			}

			// Get connected user ID.
			$user_id = $twitter->get_connected_user_id( $data['id'] );

			// User found. Log them in.
			if ( $user_id ) {
				astoundify_simple_social_login_log_user_in( $user_id );
				$twitter->success_redirect();
			}

			// If registration disabled. bail.
			if ( ! astoundify_simple_social_login_is_registration_enabled() ) {
				$twitter->error_redirect( 'connected_user_not_found' );
			}

			// Register user.
			$user_id = $twitter->insert_user( $data, $referer );
			if ( ! $user_id ) {
				$twitter->error_redirect( 'registration_fail' );
			}

			// Log them in.
			astoundify_simple_social_login_log_user_in( $user_id );

			// Redirect to home, if in login page.
			$twitter->success_redirect();

			break;
		case 'link':
			if ( ! is_user_logged_in() ) {
				$twitter->error_redirect( 'not_logged_in' );
			}

			$is_connected = $twitter->is_user_connected( get_current_user_id() );
			if ( $is_connected ) {
				$twitter->error_redirect( 'already_connected' );
			}

			// Attempt to use stored oauth token if available.
			$user_oauth_token = get_user_meta( get_current_user_id(), '_astoundify_simple_social_login_twitter_oauth_token', true );
			$user_oauth_token_secret = get_user_meta( get_current_user_id(), '_astoundify_simple_social_login_twitter_oauth_token_secret', true );

			if ( $user_oauth_token && $user_oauth_token_secret ) {

				// Use token to get credentials.
				$tw = $twitter->api_init( $user_oauth_token, $user_oauth_token_secret );

				// Get credentials.
				$profile = $tw->get( 'account/verify_credentials', array(
					'include_email'    => 'true', // Need to use string. Weird.
					'include_entities' => false,
					'skip_status'      => true,
				) );

				// Success.
				if ( $profile && ! property_exists( $profile, 'errors' ) ) {

					// Format data.
					$data = array(
						'id'                 => property_exists( $profile, 'id' ) ? $profile->id : '',
						'user_email'         => property_exists( $profile, 'email' ) ? $profile->email : '',
						'display_name'       => property_exists( $profile, 'screen_name' ) ? $profile->screen_name : '',
						'nickname'           => property_exists( $profile, 'screen_name' ) ? $profile->screen_name : '',
						'first_name'         => property_exists( $profile, 'name' ) ? $profile->name : '',
						'last_name'          => '',
						'screen_name'        => property_exists( $profile, 'screen_name' ) ? $profile->screen_name : $tokens['screen_name'],
						'oauth_token'        => $tokens['oauth_token'],
						'oauth_token_secret' => $tokens['oauth_token_secret'],
					);

					if ( ! $data['id'] ) {
						$twitter->error_redirect( 'no_id' );
					}

					// Link user.
					$link = $twitter->link_user( array(
						'id' => $data['id'],
						'oauth_token' => $data['oauth_token'],
						'oauth_token_secret' => $data['oauth_token_secret'],
					) );

					if ( ! $link ) {
						$twitter->error_redirect( 'link_fail' );
					}

					$twitter->success_redirect();
				}
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
				$twitter->error_redirect( 'api_error' );
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
				$twitter->error_redirect( 'not_logged_in' );
			}

			// Get twitter data.
			$data = $twitter->api_get_data();
			if ( ! $data || ! isset( $data['id'] ) ) {
				$twitter->error_redirect( 'api_error' );
			}

			// Link user.
			$link = $twitter->link_user( array(
				'id' => $data['id'],
				'oauth_token' => $data['oauth_token'],
				'oauth_token_secret' => $data['oauth_token_secret'],
			) );

			if ( ! $link ) {
				$twitter->error_redirect( 'link_fail' );
			}

			$twitter->success_redirect();

			break;
		case 'unlink':
			if ( ! is_user_logged_in() ) {
				wp_safe_redirect( esc_url_raw( urldecode( $referer ) ) );
				exit;
			}

			$twitter->unlink_user( get_current_user_id() );
			$twitter->success_redirect();

			break;
		default:
			$twitter->error_redirect( 'unknown_action' );
	}
}
add_action( 'astoundify_simple_social_login_process_twitter', 'astoundify_simple_social_login_twitter_process_action', 10, 2 );
