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
 * Is Facebook Social Login Active.
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
function astoundify_simple_social_login_facebook_get_login_register_button_text() {
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
 * Facebook Connected Info Text.
 *
 * @since 1.0.0
 * @return string
 */
function astoundify_simple_social_login_facebook_get_connected_info_text() {
	$option = get_option( 'astoundify_simple_social_login_facebook', array() );

	// translators: Do not translate {{unlink}} text. It'a a placeholder for unconnect link.
	$connected_info = isset( $option['connected_info'] ) && $option['connected_info'] ? esc_attr( $option['connected_info'] ) : esc_html__( 'Your account is connected to Facebook. {{unlink}}.', 'astoundify-simple-social-login' );

	$unlink_link_text = apply_filters( 'astoundify_simple_social_login_unlink_link_text', esc_html__( 'Unlink' ) );
	$unlink_url = astoundify_simple_social_login_facebook_get_url( 'unlink' );
	$last_connected_time = esc_attr( astoundify_simple_social_login_get_last_connected_time_text( get_current_user_id(), 'facebook' ) );
	$unlink = "<a href='{$unlink_url}' title='{$last_connected_time}'>{$unlink_link_text}</a>";

	$connected_info = str_replace( '{{unlink}}', $unlink, $connected_info );

	return $connected_info;
}

/**
 * Get URL for Facebook buttons.
 *
 * @since 1.0.0
 *
 * @param string $action Action.
 * @return string
 */
function astoundify_simple_social_login_facebook_get_url( $action = 'login_register' ) {
	if ( ! astoundify_simple_social_login_facebook_is_active() ) {
		return '';
	}

	$url = add_query_arg( array(
		'astoundify_simple_social_login' => 'facebook',
		'action'                         => $action,
		'redirect_to'                    => astoundify_simple_social_login_get_redirect_url( $action ),
		'_nonce'                         => wp_create_nonce( "astoundify_simple_social_login_{$action}" ),
		'_referer'                       => urldecode( esc_url( $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) ),
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
function astoundify_simple_social_login_facebook_get_login_register_button() {
	if ( ! astoundify_simple_social_login_facebook_is_active() ) {
		return '';
	}

	// Not needed if user already logged in.
	if ( is_user_logged_in() ) {
		return '';
	}

	$text = astoundify_simple_social_login_facebook_get_login_register_button_text();
	$url = astoundify_simple_social_login_facebook_get_url( 'login_register' );
	$classes = array(
		'button',
		'astoundify-simple-social-login-register-button',
		'astoundify-simple-social-login-register-button-facebook',
	);
	$classes = implode( ' ', array_map( 'sanitize_html_class', $classes ) );

	$html = "<p><a class='{$classes}' href='{$url}'>{$text}</a></p>";

	return apply_filters( 'astoundify_simple_social_login_facebook_login_register_button_html', $html, $text, $url, $classes );
}

/**
 * Print Login Register Button.
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_facebook_login_register_button() {
	echo astoundify_simple_social_login_facebook_get_login_register_button();
}
add_action( 'astoundify_simple_social_login_facebook_login_register_button', 'astoundify_simple_social_login_facebook_login_register_button' );

/**
 * Facebook Link/Unlink Button.
 *
 * @since 1.0.0
 *
 * @return string
 */
function astoundify_simple_social_login_facebook_get_link_unlink_button() {
	if ( ! astoundify_simple_social_login_facebook_is_active() ) {
		return '';
	}

	if ( ! is_user_logged_in() ) {
		return '';
	}

	// User not connected to Facebook, show connect button.
	if ( ! astoundify_simple_social_login_facebook_is_user_connected() ) {
		$is_connected = false;

		$text = astoundify_simple_social_login_facebook_get_link_button_text();
		$url = astoundify_simple_social_login_facebook_get_url( 'link' );
		$classes = array(
			'button',
			'astoundify-simple-social-link-button',
			'astoundify-simple-social-link-button-facebook',
		);
		$classes = esc_attr( implode( ' ', array_map( 'sanitize_html_class', $classes ) ) );

		$html = "<a class='{$classes}' href='{$url}'>{$text}</a>";
	} else { // Already connected, show connected info + unlink.
		$is_connected = true;

		$text = astoundify_simple_social_login_facebook_get_connected_info_text();
		$classes = array(
			'astoundify-simple-social-login-unlink-text',
			'astoundify-simple-social-login-unlink-text-facebook',
		);
		$classes = esc_attr( implode( ' ', array_map( 'sanitize_html_class', $classes ) ) );
		$url = astoundify_simple_social_login_facebook_get_url( 'unlink' );
		$unlink_link_text = apply_filters( 'astoundify_simple_social_login_unlink_link_text', esc_html__( 'Unlink' ) );

		$html = "<p class='{$classes}'>{$text}</p>";
	}

	return apply_filters( 'astoundify_simple_social_login_facebook_link_unlink_button_html', $html, $is_connected );
}

/**
 * Print Link/Unlink Button.
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_facebook_link_unlink_button() {
	echo astoundify_simple_social_login_facebook_get_link_unlink_button();
}
add_action( 'astoundify_simple_social_login_facebook_link_unlink_button', 'astoundify_simple_social_login_facebook_link_unlink_button' );

/**
 * Is User Connected to Facebook.
 *
 * @since 1.0.0
 *
 * @param int $user_id User ID.
 * @return bool
 */
function astoundify_simple_social_login_facebook_is_user_connected( $user_id = null ) {
	$user = $user_id ? get_userdata( $user_id ) : wp_get_current_user();

	// Bail if user not set.
	if ( ! $user ) {
		return false;
	}

	// Is connected status.
	$is_connected = get_user_meta( $user->ID, '_astoundify_simple_social_login_facebook_connected' , true );

	return $is_connected ? true : false;
}


/**
 * Get Connected User
 *
 * @since 1.0.0
 *
 * @param string $facebook_id Facebook ID.
 * @return int|false User with facebook ID. False if not found.
 */
function astoundify_simple_social_login_get_connected_user( $facebook_id ) {
	$args = array(
		'meta_key'    => '_astoundify_simple_social_login_facebook_id',
		'meta_value'  => esc_html( $facebook_id ),
		'number'      => -1,
		'fields'      => 'ID',
	);
	$users = get_users( $args );

	// If user found, return it.
	if ( $users ) {
		if ( 1 === count( $users )  ) {
			return intval( $users[0] );
		} else {
			// More than one users connected to the same account ? Maybe notice admin.
			return false;
		}
	}
	return false;
}

/**
 * Process Button Action Request.
 *
 * @since 1.0.0
 *
 * @param string $action Request action.
 */
function astoundify_simple_social_login_facebook_process_action( $action, $redirect_to, $referer ) {
	// Bail if not active.
	if ( ! astoundify_simple_social_login_facebook_is_active() ) {
		wp_safe_redirect( esc_url_raw( urldecode( $referer ) ) );
		exit;
	}

	// Separate each action.
	switch ( $action ) {
		case 'login_register':
			astoundify_simple_social_login_facebook_process_action_login_register_request( $redirect_to, $referer );
			break;
		case '_login_register':
			astoundify_simple_social_login_facebook_process_action_login_register( $redirect_to, $referer );
			break;
		case 'link':
			astoundify_simple_social_login_facebook_process_action_link_request( $redirect_to, $referer );
			break;
		case '_link':
			astoundify_simple_social_login_facebook_process_action_link( $redirect_to, $referer );
			break;
		case 'unlink':
			astoundify_simple_social_login_facebook_process_action_unlink( $redirect_to, $referer );
			break;
		default:
			wp_safe_redirect( esc_url_raw( urldecode( $referer ) ) );
			exit;
	}
}
add_action( 'astoundify_simple_social_login_process_facebook', 'astoundify_simple_social_login_facebook_process_action', 10, 3 );

/**
 * Initial Process Log and Register User Action.
 * This just send request to Facebook.
 *
 * @since 1.0.0
 *
 * @param string $redirect_to Redirect URL.
 */
function astoundify_simple_social_login_facebook_process_action_login_register_request( $redirect_to, $referer ) {
	if ( is_user_logged_in() ) {
		wp_safe_redirect( esc_url_raw( urldecode( $redirect_to ) ) );
		exit;
	}

	$fb = astoundify_simple_social_login_facebook_api();
	$helper = $fb->getRedirectLoginHelper();

	$process_url = add_query_arg( array(
		'astoundify_simple_social_login' => 'facebook',
		'action'                         => '_login_register',
		'redirect_to'                    => $redirect_to,
		'_nonce'                         => wp_create_nonce( 'astoundify_simple_social_login__login_register' ),
		'_referer'                       => $referer,
	), home_url() );
	$scope = array( 'email' );

	$fb_url = $helper->getLoginUrl( $process_url, $scope );

	wp_redirect( esc_url_raw( $fb_url ) );
	exit;
}

/**
 * Process Facebook Data From Login Register Action
 *
 * @since 1.0.0
 *
 * @param string $redirect_to Redirect URL.
 */
function astoundify_simple_social_login_facebook_process_action_login_register( $redirect_to, $referer ) {
	if ( is_user_logged_in() ) {
		astoundify_simple_social_login_add_error( 'login_register_fail', 'User already logged-in.' );
		wp_redirect( esc_url_raw( urldecode( $referer ) ) );
		exit;
	}

	// Get data from Facebook.
	$data = astoundify_simple_social_login_facebook_api_get_data();

	// Bail, No data.
	if ( false === $data ) {
		wp_redirect( esc_url_raw( urldecode( $referer ) ) );
		exit;
	}

	// Check if a user already connected to facebook account.
	$user_id = astoundify_simple_social_login_get_connected_user( $data['facebook_id'] );

	// Found user, log them in.
	if ( $user_id ) {
		astoundify_simple_social_login_log_user_in( $user_id );
		wp_redirect( esc_url_raw( urldecode( $redirect_to ) ) );
		exit;
	}

	// No user, and registration disabled.
	if ( ! astoundify_simple_social_login_is_registration_enabled() ) {
		astoundify_simple_social_login_add_error( 'connected_user_not_found', 'Cannot find user with your Facebook account.' );
		wp_redirect( esc_url_raw( urldecode( $referer ) ) );
		exit;
	}

	// Register user.
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
	$user_id = is_wp_error( $inserted ) ? false : intval( $inserted );

	// Fail to register user.
	if ( ! $user_id ) {
		astoundify_simple_social_login_add_error( 'registration_fail', 'Fail to register user.' );
		wp_redirect( esc_url_raw( urldecode( $referer ) ) );
		exit;
	}

	// Success. Add user meta.
	update_user_meta( $user_id, '_astoundify_simple_social_login_facebook_id', esc_html( $data['facebook_id'] ) );
	update_user_meta( $user_id, '_astoundify_simple_social_login_facebook_access_token', esc_html( $data['facebook_access_token'] ) );
	update_user_meta( $user_id, '_astoundify_simple_social_login_facebook_connected', 1 );
	update_user_meta( $user_id, '_astoundify_simple_social_login_facebook_timestamp', current_time( 'timestamp' ) );
	update_user_meta( $user_id, '_astoundify_simple_social_login_facebook_timestamp_gmt', time() );

	// Log them in.
	astoundify_simple_social_login_log_user_in( $user_id );
	wp_redirect( esc_url_raw( urldecode( $redirect_to ) ) );
	exit;
}

/**
 * Process Link User Action.
 *
 * @since 1.0.0
 *
 * @param string $redirect_to Redirect URL.
 */
function astoundify_simple_social_login_facebook_process_action_link_request( $redirect_to, $referer ) {
	if ( ! is_user_logged_in() ) {
		astoundify_simple_social_login_add_error( 'facebook_link_fail', 'User is not logged-in.' );
		wp_safe_redirect( esc_url_raw( urldecode( $redirect_to ) ) );
		exit;
	}

	$fb = astoundify_simple_social_login_facebook_api();
	$helper = $fb->getRedirectLoginHelper();

	$process_url = add_query_arg( array(
		'astoundify_simple_social_login' => 'facebook',
		'action'                         => '_link',
		'redirect_to'                    => $redirect_to,
		'_nonce'                         => wp_create_nonce( 'astoundify_simple_social_login__link' ),
		'_referer'                       => $referer,
	), home_url() );
	$scope = array( 'email' );

	$fb_url = $helper->getLoginUrl( $process_url, $scope );

	wp_redirect( esc_url_raw( $fb_url ) );
	exit;
}

/**
 * Process Link User Action.
 *
 * @since 1.0.0
 *
 * @param string $redirect_to Redirect URL.
 */
function astoundify_simple_social_login_facebook_process_action_link( $redirect_to, $referer ) {
	if ( ! is_user_logged_in() ) {
		astoundify_simple_social_login_add_error( 'facebook_link_fail', 'Cannot connect to Facebook. User is not logged-in.' );
		wp_safe_redirect( esc_url_raw( urldecode( $redirect_to ) ) );
		exit;
	}

	// User already connected.
	if ( astoundify_simple_social_login_facebook_is_user_connected() ) {
		astoundify_simple_social_login_add_error( 'facebook_link_fail', 'User account already connected to Facebook.' );
		wp_safe_redirect( esc_url_raw( urldecode( $redirect_to ) ) );
		exit;
	}

	// Get data from Facebook.
	$data = astoundify_simple_social_login_facebook_api_get_data();

	// Bail, No data.
	if ( false === $data ) {
		wp_redirect( esc_url_raw( urldecode( $redirect_to ) ) );
		exit;
	}

	// Check if a user already connected to Facebook account. Cannot connect 1 Facebook account to multiple WP account.
	$connected_user_id = astoundify_simple_social_login_get_connected_user( $data['facebook_id'] );
	if ( $connected_user_id ) {
		astoundify_simple_social_login_add_error( 'facebook_link_fail', 'Another user already connected to this Facebook account.' );
		wp_safe_redirect( esc_url_raw( urldecode( $redirect_to ) ) );
		exit;
	}

	// Connect user.
	$user_id = get_current_user_id();
	update_user_meta( $user_id, '_astoundify_simple_social_login_facebook_id', esc_html( $data['facebook_id'] ) );
	update_user_meta( $user_id, '_astoundify_simple_social_login_facebook_access_token', esc_html( $data['facebook_access_token'] ) );
	update_user_meta( $user_id, '_astoundify_simple_social_login_facebook_connected', 1 );
	update_user_meta( $user_id, '_astoundify_simple_social_login_facebook_timestamp', current_time( 'timestamp' ) );
	update_user_meta( $user_id, '_astoundify_simple_social_login_facebook_timestamp_gmt', time() );

	// Redirect them back.
	wp_safe_redirect( esc_url_raw( urldecode( $redirect_to ) ) );
	exit;
}

/**
 * Process Unlink User Action.
 *
 * @since 1.0.0
 *
 * @param string $redirect_to Redirect URL.
 */
function astoundify_simple_social_login_facebook_process_action_unlink( $redirect_to, $referer ) {
	if ( ! is_user_logged_in() ) {
		astoundify_simple_social_login_add_error( 'facebook_unlink_fail', 'Unlink Facebook account fail. User is not logged-in.' );
		wp_safe_redirect( esc_url_raw( urldecode( $redirect_to ) ) );
		exit;
	}

	$user_id = get_current_user_id();
	delete_user_meta( $user_id, '_astoundify_simple_social_login_facebook_id' );
	delete_user_meta( $user_id, '_astoundify_simple_social_login_facebook_access_token' );
	delete_user_meta( $user_id, '_astoundify_simple_social_login_facebook_connected' );

	wp_safe_redirect( esc_url_raw( urldecode( $redirect_to ) ) );
	exit;
}
