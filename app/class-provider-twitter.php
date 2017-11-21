<?php
/**
 * Social Login Provider: Twitter
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
 * Twitter Social Login Provider
 *
 * @since 1.0.0
 */
class Provider_Twitter extends Provider {

	/**
	 * Provider ID.
	 *
	 * @since 1.0.0
	 *
	 * @var string $id Provider ID.
	 */
	public $id = 'twitter';

	/**
	 * Option Name
	 *
	 * @since 1.0.0
	 *
	 * @var string $option_name Option Name.
	 */
	public $option_name = 'astoundify_simple_social_login_twitter';

	/**
	 * Label
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Twitter', 'astoundify-simple-social-login' );
	}

	/**
	 * Consumer Key (API Key)
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_consumer_key() {
		$option = get_option( $this->option_name, array() );
		return isset( $option['consumer_key'] ) ? esc_attr( trim( $option['consumer_key'] ) ) : '';
	}

	/**
	 * Consumer Key (API Secret Key)
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_consumer_secret() {
		$option = get_option( $this->option_name, array() );
		return isset( $option['consumer_secret'] ) ? esc_attr( trim( $option['consumer_secret'] ) ) : '';
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
		$consumer_key = $this->get_consumer_key();
		$consumer_secret = $this->get_consumer_secret();
		if ( ! $consumer_key || ! $consumer_secret ) {
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
		return $text ? esc_attr( $text ) : esc_html__( 'Log in with Twitter', 'astoundify-simple-social-login' );
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
		return $text ? esc_attr( $text ) : esc_html__( 'Link your account to Twitter', 'astoundify-simple-social-login' );
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
		$text = $text ? $text : esc_html__( 'Your account is connected to Twitter. {{unlink}}.' );

		$text = str_replace( '{{unlink}}', $this->get_unlink_button(), $text );

		return $text;
	}

	/**
	 * Connect via Facebook API
	 *
	 * @since 1.0.0
	 *
	 * @return TwitterOAuth|false
	 */
	function api_init( $oauth_token = false, $oauth_token_secret = false ) {
		if ( ! $this->is_active() ) {
			return false;
		}

		// Load Twitter SDK.
		require_once( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'vendor/abraham/twitteroauth/autoload.php' );

		if ( ! $oauth_token ) {
			return new \Abraham\TwitterOAuth\TwitterOAuth( $this->get_consumer_key(), $this->get_consumer_secret() );
		} else {
			return new \Abraham\TwitterOAuth\TwitterOAuth( $this->get_consumer_key(), $this->get_consumer_secret(), $oauth_token, $oauth_token_secret );
		}
	}

	/**
	 * Add Additional Error Codes.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_error_codes() {
		$errors = parent::get_error_codes();
		$errors['twitter_oauth_unverified'] = esc_html__( 'Unable to verify Twitter oAuth request.', 'astoundify-simple-social-login' );
		$errors['twitter_oauth_not_match'] = esc_html__( 'Twitter oAuth token do not match.', 'astoundify-simple-social-login' );
		$errors['twitter_cannot_retrive_credentials'] = esc_html__( 'Cannot retrieve user credentials from Twitter profile.', 'astoundify-simple-social-login' );
		return $errors;
	}

	/**
	 * API Callback
	 *
	 * @since 1.0.0
	 */
	public function get_api_callback_url() {
		$url = add_query_arg( array(
			'astoundify_simple_social_login' => $this->id,
			'action' => 'done',
		), home_url() );
		return $url;
	}

	/**
	 * Get API Data
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function api_get_data( $referer ) {
		// Make sure all data available.
		if ( ! isset( $_GET['oauth_token'], $_GET['oauth_verifier'], $_SESSION['astoundify_simple_social_login_twitter_oauth_token'], $_SESSION['astoundify_simple_social_login_twitter_oauth_token_secret'] ) ) {
			$this->redirect( urldecode( $referer ), 'twitter_oauth_unverified' );
		}

		// Check if token matches with previous request.
		if ( $_GET['oauth_token'] !== $_SESSION['astoundify_simple_social_login_twitter_oauth_token'] ) {
			$this->redirect( urldecode( $referer ), 'twitter_oauth_not_match' );
		}

		// Initiate request.
		$tw = $this->api_init( $_SESSION['astoundify_simple_social_login_twitter_oauth_token'], $_SESSION['astoundify_simple_social_login_twitter_oauth_token_secret'] );

		// Get Access tokens: oauth_token, oauth_token_secret, user_id, screen_name, x_auth_expires
		$tokens = $tw->oauth( 'oauth/access_token', array(
			'oauth_verifier' => $_GET['oauth_verifier'],
		) );

		// Re-init using access token.
		$tw = $this->api_init( $tokens['oauth_token'], $tokens['oauth_token_secret'] );

		// Get credentials.
		$profile = $tw->get( 'account/verify_credentials', array(
			'include_email'    => 'true', // Need to use string. Weird.
			'include_entities' => false,
			'skip_status'      => true,
		) );

		if ( ! $profile ) {
			$this->redirect( urldecode( $referer ), 'twitter_cannot_retrive_credentials' );
		}

		// Format data.
		$data = array(
			'id'            => property_exists( $profile, 'id' ) ? $profile->id : '',
			'user_email'    => property_exists( $profile, 'email' ) ? $profile->email : '',
			'display_name'  => property_exists( $profile, 'screen_name' ) ? $profile->screen_name : '',
			'nickname'      => property_exists( $profile, 'screen_name' ) ? $profile->screen_name : '',
			'first_name'    => property_exists( $profile, 'name' ) ? $profile->name : '',
			'last_name'     => '',
			'screen_name'   => property_exists( $profile, 'screen_name' ) ? $profile->screen_name : '',
		);

		if ( ! $data['id'] ) {
			$this->redirect( urldecode( $referer ), 'no_id' );
		}

		return $data;
	}
}
