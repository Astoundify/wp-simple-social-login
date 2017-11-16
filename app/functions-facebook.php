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
 * Is Facebook Active Provider
 *
 * @since 1.0.0
 *
 * @return bool
 */
function astoundify_simple_social_login_facebook_is_active() {
	// Is selected as active provider?
	$options = get_option( 'astoundify_simple_social_login', array() );
	$options = is_array( $options ) ? $options : array();
	$providers = isset( $options['providers'] ) && is_array( $options['providers'] ) ? $options['providers'] : array();
	if ( ! in_array( 'facebook', $providers, true ) ) {
		return false;
	}

	// Check API requirements.
	$app_id = astoundify_simple_social_login_facebook_get_app_id();
	$app_secret = astoundify_simple_social_login_facebook_get_app_secret();
	if ( ! $app_id || ! $app_secret ) {
		return false;
	}

	return true;
}

/**
 * Facebook App ID.
 *
 * @since 1.0.0
 * @return string
 */
function astoundify_simple_social_login_facebook_get_app_id() {
	$option = get_option( 'astoundify_simple_social_login_facebook', array() );
	return isset( $option['app_id'] ) ? esc_attr( trim( $option['app_id'] ) ) : '';
}

/**
 * Facebook App Secret.
 *
 * @since 1.0.0
 * @return string
 */
function astoundify_simple_social_login_facebook_get_app_secret() {
	$option = get_option( 'astoundify_simple_social_login_facebook', array() );
	return isset( $option['app_secret'] ) ? esc_attr( trim( $option['app_secret'] ) ) : '';
}

/**
 * Facebook Login Button Text.
 *
 * @since 1.0.0
 * @return string
 */
function astoundify_simple_social_login_facebook_get_login_button_text() {
	$option = get_option( 'astoundify_simple_social_login_facebook', array() );
	return isset( $option['login_button_text'] ) && $option['login_button_text'] ? esc_attr( $option['login_button_text'] ) : esc_html__( 'Log in with Facebook', 'astoundify-simple-social-login' );
}

/**
 * Facebook Link Button Text.
 *
 * @since 1.0.0
 * @return string
 */
function astoundify_simple_social_login_facebook_get_link_button_text() {
	$option = get_option( 'astoundify_simple_social_login_facebook', array() );
	return isset( $option['link_button_text'] ) && $option['link_button_text'] ? esc_attr( $option['link_button_text'] ) : esc_html__( 'Link your account to Facebook', 'astoundify-simple-social-login' );
}

/**
 * Connect via Facebook API
 *
 * @since 1.0.0
 *
 * @return Facebook\Facebook|false
 */
function astoundify_simple_social_login_facebook_api() {
	if ( ! astoundify_simple_social_login_facebook_is_active() ) {
		return false;
	}

	$config = array(
		'app_id'                => astoundify_simple_social_login_facebook_get_app_id(),
		'app_secret'            => astoundify_simple_social_login_facebook_get_app_secret(),
		'default_graph_version' => 'v2.8',
	);

	return new Facebook\Facebook( $config );
}

/**
 * Facebook Login/Register Button URL.
 *
 * @since 1.0.0
 *
 * @return string
 */
function astoundify_simple_social_login_facebook_get_login_url() {
	if ( ! astoundify_simple_social_login_facebook_is_active() ) {
		return '';
	}

	$url = add_query_arg( array(
		'astoundify_simple_social_login' => 'facebook',
		'action'                         => 'login', // Options: login, link, unlink.
	), home_url() );

	return esc_url( $url );
}

/**
 * Facebook Login/Register Button.
 *
 * @since 1.0.0
 *
 * @return string
 */
function astoundify_simple_social_login_facebook_get_login_button() {
	$text = astoundify_simple_social_login_facebook_get_login_button_text();
	$url = astoundify_simple_social_login_facebook_get_login_url();
	$classes = array(
		'button',
		'astoundify-simple-social-login-button',
		'astoundify-simple-social-login-button-facebook',
	);
	$classes = implode( ' ', array_map( 'sanitize_html_class', $classes ) );
	$html = "<a class='{$classes}' href='{$url}'>{$text}</a>";
	return apply_filters( 'astoundify_simple_social_login_facebook_login_button', $html );
}

/**
 * Facebook Link/Connect Button.
 *
 * @since 1.0.0
 *
 * @return string
 */
function astoundify_simple_social_login_facebook_get_link_button() {
	$text = astoundify_simple_social_login_facebook_get_link_button_text();
	$url = astoundify_simple_social_login_facebook_get_login_url();
	$classes = array(
		'button',
		'astoundify-simple-social-link-button',
		'astoundify-simple-social-link-button-facebook',
	);
	$classes = implode( ' ', array_map( 'sanitize_html_class', $classes ) );
	$html = "<a class='{$classes}' href='{$url}'>{$text}</a>";
	return apply_filters( 'astoundify_simple_social_login_facebook_link_button', $html );
}

/**
 * Is user connected to Facebook
 *
 * @since 1.0.0
 *
 * @param int $user_id User ID.
 * @return bool
 */
function astoundify_simple_social_login_facebook_is_user_connected( $user_id = null ) {
	$user = $user_id ? get_userdata( $user_id ) : wp_get_current_user();
	if ( ! $user ) { // Bail if user not set.
		return false;
	}

	// Is connected status.
	$is_connected = get_user_meta( $user->ID, '_astoundify_simple_social_login_facebook_is_connected' , true );

	return $is_connected ? true : false;
}

/**
 * Connect user data to facebook.
 *
 * @since 1.0.0
 *
 * @param int $user_id User ID.
 * @param array $data Facebook data.
 * @return bool True on success.
 */
function astoundify_simple_social_login_facebook_connect_user( $user_id = null, $data = array() ) {
	$user = $user_id ? get_userdata( $user_id ) : wp_get_current_user();
	if ( ! $user ) { // Bail if user not set.
		return false;
	}

	$defaults = array(
		'facebook_id'            => '',
		'facebook_access_token'  => '',
		'facebook_name'          => '',
		'facebook_first_name'    => '',
		'facebook_last_name'     => '',
		'facebook_email'         => '', // Not really needed here.
	);
	$data = wp_parse_args( $data, $defaults );

	if ( ! $data['facebook_id'] ) {
		return false;
	}

	// Update user meta.
	update_user_meta( $user->ID, '_astoundify_simple_social_login_facebook_id', esc_html( $data['facebook_id'] ) );
	update_user_meta( $user->ID, '_astoundify_simple_social_login_facebook_access_token', esc_html( $data['facebook_access_token'] ) );
	update_user_meta( $user->ID, '_astoundify_simple_social_login_facebook_connected', 1 );

	// Update user if needed.
	$update = array();
	if ( ! $user->display_name && $data['facebook_name'] ) {
		$update['display_name'] = esc_html( $data['facebook_name'] );
	}
	if ( ! $user->nickname && $data['facebook_name'] ) {
		$update['nickname'] = esc_html( $data['facebook_name'] );
	}
	if ( ! $user->first_name && $data['facebook_first_name'] ) {
		$update['first_name'] = esc_html( $data['facebook_first_name'] );
	}
	if ( ! $user->last_name && $data['facebook_last_name'] ) {
		$update['last_name'] = esc_html( $data['facebook_last_name'] );
	}
	if ( $update ) {
		wp_update_user( $update );
	}

	return true;
}

/**
 * Create user data from facebook.
 *
 * @since 1.0.0
 *
 * @param array $data Facebook data.
 * @return int|bool User ID on success. False if fail.
 */
function astoundify_simple_social_login_facebook_create_user( $data = array() ) {
	// If user already have account, bail.
	if ( is_user_logged_in() ) {
		return false;
	}

	if ( ! $data['facebook_id'] ) {
		return false;
	}

	$defaults = array(
		'facebook_id'            => '',
		'facebook_access_token'  => '',
		'facebook_name'          => '',
		'facebook_first_name'    => '',
		'facebook_last_name'     => '',
		'facebook_email'         => '', // Not really needed here.
	);
	$data = wp_parse_args( $data, $defaults );

	$userdata = array();
	if ( $data['facebook_name'] ) {
		$userdata['user_login'] = sanitize_title( $data['facebook_name'] );
	}
	if ( ! isset( $userdata['user_login'] ) || ! $userdata['user_login'] ) {
		$userdata['user_login'] = time();
	}
	if ( username_exists( $userdata['user_login'] ) ) {
		$userdata['user_login'] = $userdata['user_login'] . '_' . time();
	}

	if ( $data['facebook_email'] ) {
		$userdata['user_email'] = sanitize_email( $data['facebook_email'] );
	}
	if ( ! isset( $userdata['user_email'] ) || ! $userdata['user_email'] ) {
		$userdata['user_email'] = $data['facebook_id'] . '@fb.com';
	}
	if ( email_exists( $userdata['user_email'] ) ) {
		$userdata['user_email'] = $data['facebook_id'] . '_' . time() . '@fb.com';
	}

	$userdata['display_name'] = $data['facebook_name'];
	$userdata['nickname'] = $data['facebook_name'];
	$userdata['first_name'] = $data['facebook_first_name'];
	$userdata['last_name'] = $data['facebook_last_name'];

	$inserted = wp_insert_user( $userdata );

	return is_wp_error( $inserted ) ? false : intval( $inserted );
}

/**
 * Connect user data to facebook.
 *
 * @since 1.0.0
 *
 * @return bool True on success.
 */
function astoundify_simple_social_login_facebook_disconnect_user( $user_id = null, $data = array() ) {
	$user = $user_id ? get_userdata( $user_id ) : wp_get_current_user();
	if ( ! $user ) { // Bail if user not set.
		return false;
	}
	delete_user_meta( $user->ID, '_astoundify_simple_social_login_facebook_id' );
	delete_user_meta( $user->ID, '_astoundify_simple_social_login_facebook_connected' );
	return true;
}

/**
 * Process Login Request.
 *
 * @since 1.0.0
 *
 * @param string $action Request action.
 */
function astoundify_simple_social_login_facebook_process_action( $action ) {
	if ( ! astoundify_simple_social_login_facebook_is_active() ) {
		return '';
	}

	switch ( $_GET['action'] ) {
		case 'login':
			astoundify_simple_social_login_facebook_process_action_login();
			break;
		case 'connect':
			astoundify_simple_social_login_facebook_process_action_connect();
			break;
		default:
			wp_safe_redirect( esc_url_raw( home_url() ) );
			exit;
	}
}
add_action( 'astoundify_simple_social_login_process_facebook', 'astoundify_simple_social_login_facebook_process_action' );

/**
 * Process Login Action.
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_facebook_process_action_login() {
	$fb = astoundify_simple_social_login_facebook_api();
	$helper = $fb->getRedirectLoginHelper();

	$process_url = add_query_arg( array(
		'astoundify_simple_social_login' => 'facebook',
		'action'                         => 'connect',
	), home_url() );
	$scope = array( 'email' );
	$fb_url = $helper->getLoginUrl( $process_url, $scope );

	wp_redirect( esc_url_raw( $fb_url ) );
	exit;
}

/**
 * Connect.
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_facebook_process_action_connect() {
	// Start API.
	$fb = astoundify_simple_social_login_facebook_api();
	$helper = $fb->getRedirectLoginHelper();

	// @link https://stackoverflow.com/questions/32029116
	// Needed for : Facebook SDK returned an error: Cross-site request forgery validation failed. Required param "state" missing from persistent data.
	$_SESSION['FBRLH_state'] = $_GET['state'];

	// Default data.
	$data = array(
		'facebook_access_token'  => '',
		'facebook_id'            => '',
		'facebook_name'          => '',
		'facebook_first_name'    => '',
		'facebook_last_name'     => '',
	);

	// Error messages.
	$error = array();

	// Get access token.
	try {
		$access_token = $helper->getAccessToken();
	} catch( Facebook\Exceptions\FacebookResponseException $e ) {
		$error[] = 'Graph returned an error: ' . $e->getMessage();
	} catch( Facebook\Exceptions\FacebookSDKException $e ) {
		$error[] = 'Facebook SDK returned an error: ' . $e->getMessage();
	}

	// Error.
	if ( $error ) {
		wp_safe_redirect( esc_url_raw( home_url() ) );
		exit;
	}

	// Add access token.
	$data['facebook_access_token'] = $access_token->getValue();

	// Process token.
	$fb->setDefaultAccessToken( $access_token->getValue() ) ;

	// Get Facebook user data using token.
	try {
		$profile_request = $fb->get( '/me?fields=name,first_name,last_name,email' );
		$profile = $profile_request->getGraphUser();

		if ( ! $profile->getProperty( 'id' ) ) {
			$error[] = 'Cannot retrieve Facebook profile ID.';
		}

		$data['facebook_id']         = $profile->getProperty( 'id' );
		$data['facebook_name']       = $profile->getProperty( 'name' );
		$data['facebook_first_name'] = $profile->getProperty( 'first_name' );
		$data['facebook_last_name']  = $profile->getProperty( 'last_name' );
		$data['facebook_email']      = $profile->getProperty( 'email' );

	} catch( Facebook\Exceptions\FacebookResponseException $e ) {
		$error[] = 'Graph returned an error: ' . $e->getMessage();
	}

	// Error.
	if ( $error ) {
		wp_safe_redirect( esc_url_raw( home_url() ) );
		exit;
	}

	// Create user.
	$user_id = astoundify_simple_social_login_facebook_create_user( $data );

	// Log user in.
	astoundify_simple_social_login_log_user_in( $user_id );

	// Redirect back.
	wp_safe_redirect( esc_url_raw( home_url() ) );
	exit;
}


/* ========================================= */
add_action( 'init', function() {
	add_shortcode( 'test', function() {
		return astoundify_simple_social_login_facebook_get_link_button();
	} );
} );










































