<?php
/**
 * Facebook API Functions.
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
 * Connect via Facebook API
 *
 * @since 1.0.0
 *
 * @return Facebook\Facebook|false
 */
function astoundify_simple_social_login_facebook_api() {
	if ( ! astoundify_simple_social_login_facebook_is_active() ) {
		astoundify_simple_social_login_add_error( 'facebook_not_active', esc_html__( 'Facebook social login connect is not active.', 'astoundify-simple-social-login' ) );
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
 * Get user data from Facebook.
 * This function is loaded when Facebook sending data back to our endpoint.
 *
 * @since 1.0.0
 *
 * @return array|false
 */
function astoundify_simple_social_login_facebook_api_get_data() {
	if ( ! astoundify_simple_social_login_facebook_is_active() ) {
		astoundify_simple_social_login_add_error( 'facebook_not_active', esc_html__( 'Facebook social login connect is not active.', 'astoundify-simple-social-login' ) );
		return false;
	}

	// Start API.
	$fb = astoundify_simple_social_login_facebook_api();
	$helper = $fb->getRedirectLoginHelper();

	// @link https://stackoverflow.com/questions/32029116
	if ( ! isset( $_GET['state'] ) ) {
		astoundify_simple_social_login_add_error( 'facebook_state_missing', esc_html__( 'Facebook SDK returned an error: Cross-site request forgery validation failed. Required param "state" missing from persistent data.', 'astoundify-simple-social-login' ) );
	}
	$_SESSION['FBRLH_state'] = $_GET['state'];

	// Default data.
	$data = array(
		'facebook_access_token'  => '',
		'facebook_id'            => '',
		'facebook_name'          => '',
		'facebook_first_name'    => '',
		'facebook_last_name'     => '',
	);

	// Get access token.
	try {
		$access_token = $helper->getAccessToken();
	} catch( Facebook\Exceptions\FacebookResponseException $e ) {
		astoundify_simple_social_login_add_error( 'facebook_get_token_graph', 'Facebook Graph returned an error: ' . $e->getMessage() );
	} catch( Facebook\Exceptions\FacebookSDKException $e ) {
		astoundify_simple_social_login_add_error( 'facebook_get_token_sdk', 'Facebook SDK returned an error: ' . $e->getMessage() );
	}

	// Bail if error.
	if ( astoundify_simple_social_login_get_errors() ) {
		return false;
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
			astoundify_simple_social_login_add_error( 'facebook_get_id', 'Cannot retrieve Facebook profile ID.' );
		}

		$data['facebook_id']         = $profile->getProperty( 'id' );
		$data['facebook_name']       = $profile->getProperty( 'name' );
		$data['facebook_first_name'] = $profile->getProperty( 'first_name' );
		$data['facebook_last_name']  = $profile->getProperty( 'last_name' );
		$data['facebook_email']      = $profile->getProperty( 'email' );

	} catch( Facebook\Exceptions\FacebookResponseException $e ) {
		astoundify_simple_social_login_add_error( 'facebook_get_token_graph', 'Facebook Graph returned an error: ' . $e->getMessage() );
	}

	// Bail if error.
	if ( astoundify_simple_social_login_get_errors() ) {
		return false;
	}

	return $data;
}
