<?php
/**
 * Social Login Provider : Facebook
 *
 * @since 1.0.0
 *
 * @package Abstracts
 * @category Core
 * @author Astoundify
 */

namespace Astoundify\Simple_Social_Login;

// Do not access this file directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provider
 *
 * @since 1.0.0
 */
class Provider_Facebook extends Provider {

	/**
	 * Provider ID.
	 *
	 * @since 1.0.0
	 *
	 * @var string $id Provider ID.
	 */
	public $id = 'facebook';

	/**
	 * Option Name
	 *
	 * @since 1.0.0
	 *
	 * @var string $option_name Option Name.
	 */
	public $option_name = 'astoundify_simple_social_login_facebook';

	/**
	 * Label
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Facebook', 'astoundify-simple-social-login' );
	}

	/**
	 * App ID.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_app_id() {
		$option = get_option( 'astoundify_simple_social_login_facebook', array() );
		return isset( $option['app_id'] ) ? esc_attr( trim( $option['app_id'] ) ) : '';
	}

	/**
	 * App Secret.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_app_secret() {
		$option = get_option( 'astoundify_simple_social_login_facebook', array() );
		return isset( $option['app_secret'] ) ? esc_attr( trim( $option['app_secret'] ) ) : '';
	}

	/**
	 * Is Active.
	 *
	 * @since 1.0.0
	 */
	public function is_active() {
		// Check if selected.
		$is_active = parent::is_active();

		if ( ! $is_active ) {
			return false;
		}

		// Check API requirements.
		$app_id = $this->get_app_id();
		$app_secret = $this->get_app_secret();
		if ( ! $app_id || ! $app_secret ) {
			return false;
		}

		return true;
	}

	/**
	 * Login Register Button Text
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_login_register_button_text() {
		$text = parent::get_login_register_button_text();
		return $text ? esc_attr( $text ) : esc_html__( 'Log in with Facebook', 'astoundify-simple-social-login' );
	}

	/**
	 * Link Button Text.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_link_button_text() {
		$text = parent::get_link_button_text();
		return $text ? esc_attr( $text ) : esc_html__( 'Link your account to Facebook', 'astoundify-simple-social-login' );
	}

	/**
	 * Connected Info Text
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_connected_info_text() {
		$text = parent::get_connected_info_text();
		// translators: {{unlink}} is a placeholder for unlink account link. Do not translate.
		$text = $text ? $text : esc_html__( 'Your account is connected to Facebook. {{unlink}}.' );

		$text = str_replace( '{{unlink}}', $this->get_unlink_button(), $text );

		return $text;
	}

	/**
	 * Connect via Facebook API
	 *
	 * @since 1.0.0
	 *
	 * @return Facebook\Facebook|false
	 */
	function api_init() {
		if ( ! $this->is_active() ) {
			return false;
		}

		// Load Facebook SDK.
		require_once( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'vendor/facebook/graph-sdk/src/Facebook/autoload.php' );

		$config = array(
			'app_id'                => $this->get_app_id(),
			'app_secret'            => $this->get_app_secret(),
			'default_graph_version' => 'v2.8',
		);

		return new \Facebook\Facebook( $config );
	}

	/**
	 * Add Error Codes.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_error_codes() {
		$errors = parent::get_error_codes();
		$errors['facebook_state_missing'] = esc_html__( 'Facebook SDK returned an error: Cross-site request forgery validation failed. Required param "state" missing from persistent data.', 'astoundify-simple-social-login' );
		$errors['facebook_token_graph_error'] = esc_html__( 'Facebook Graph returned an error.', 'astoundify-simple-social-login' );
		$errors['facebook_sdk_error'] = esc_html__( 'Facebook SDK returned an error.', 'astoundify-simple-social-login' );
		return $errors;
	}

	/**
	 * Get API Data
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function api_get_data( $referer ) {
		if ( ! $this->is_active() ) {
			return false;
		}

		// @link https://stackoverflow.com/questions/32029116
		if ( ! isset( $_GET['state'] ) ) {
			$this->redirect( urldecode( $referer ), 'facebook_state_missing' );
		}

		$fb = $this->api_init();
		$helper = $fb->getRedirectLoginHelper();
		$_SESSION['FBRLH_state'] = $_GET['state'];

		// Default data.
		$data = array(
			'access_token'  => '',
			'id'            => '',
			'email'         => '',
			'display_name'  => '',
			'nickname'      => '',
			'first_name'    => '',
			'last_name'     => '',
		);

		// Get access token.
		try {
			$access_token = $helper->getAccessToken();
		} catch( Facebook\Exceptions\FacebookResponseException $e ) {
			$this->redirect( urldecode( $referer ), 'facebook_token_graph_error' );
		} catch( Facebook\Exceptions\FacebookSDKException $e ) {
			$this->redirect( urldecode( $referer ), 'facebook_sdk_error' );
		}

		// Add access token to data array.
		$data['access_token'] = $access_token->getValue();

		// Process token.
		$fb->setDefaultAccessToken( $access_token->getValue() ) ;

		// Get Facebook user data using token.
		try {
			$profile_request = $fb->get( '/me?fields=name,first_name,last_name,email' );
			$profile = $profile_request->getGraphUser();

			if ( ! $profile->getProperty( 'id' ) ) {
				$this->redirect( urldecode( $referer ), 'no_id' );
			}

			$data['id']            = $profile->getProperty( 'id' );
			$data['email']         = $profile->getProperty( 'email' );
			$data['display_name']  = $profile->getProperty( 'name' );
			$data['nickname']      = $profile->getProperty( 'name' );
			$data['first_name']    = $profile->getProperty( 'first_name' );
			$data['last_name']     = $profile->getProperty( 'last_name' );

		} catch( Facebook\Exceptions\FacebookResponseException $e ) {
			$this->redirect( urldecode( $referer ), 'facebook_token_graph_error' );
		}

		if ( ! $data['id'] ) {
			$this->redirect( urldecode( $referer ), 'no_id' );
		}

		return $data;
	}

}
