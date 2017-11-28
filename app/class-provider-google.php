<?php
/**
 * Social Login Provider: Google
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
 * Google Social Login Provider
 *
 * @since 1.0.0
 */
class Provider_Google extends Provider {

	/**
	 * Provider ID.
	 *
	 * @since 1.0.0
	 *
	 * @var string $id Provider ID.
	 */
	public $id = 'google';

	/**
	 * Option Name
	 *
	 * @since 1.0.0
	 *
	 * @var string $option_name Option Name.
	 */
	public $option_name = 'astoundify_simple_social_login_google';

	/**
	 * Label
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Google', 'astoundify-simple-social-login' );
	}

	/**
	 * Client ID
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_client_id() {
		$option = get_option( $this->option_name, array() );
		return isset( $option['client_id'] ) ? esc_attr( trim( $option['client_id'] ) ) : '';
	}

	/**
	 * Client Secret
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_client_secret() {
		$option = get_option( $this->option_name, array() );
		return isset( $option['client_secret'] ) ? esc_attr( trim( $option['client_secret'] ) ) : '';
	}

	/**
	 * Endpoint URL
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_endpoint_url() {
		$args = array(
			'astoundify_simple_social_login' => 'done',
		);
		$url = add_query_arg( $args, home_url() );
		return $url;
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
		$consumer_key = $this->get_client_id();
		$consumer_secret = $this->get_client_secret();
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
		return $text ? esc_attr( $text ) : esc_html__( 'Log in with Google', 'astoundify-simple-social-login' );
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
		return $text ? esc_attr( $text ) : esc_html__( 'Link your account to Google', 'astoundify-simple-social-login' );
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
		$text = $text ? $text : esc_html__( 'Your account is connected to Google. {{unlink}}.' );

		$text = str_replace( '{{unlink}}', $this->get_unlink_button(), $text );

		return $text;
	}

	/**
	 * Connect via Google API
	 *
	 * @since 1.0.0
	 *
	 * @return object|false
	 */
	function api_init() {
		if ( ! $this->is_active() ) {
			return false;
		}

		// Load library.
		require_once( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'vendor/hybridauth/hybridauth/hybridauth/Hybrid/Auth.php' );

		$config = array(
			'base_url'  => $this->get_endpoint_url(),
			'providers' => array(
				'Google' => array(
					'enabled'         => true,
					'keys'            => array(
						'id'     => $this->get_client_id(),
						'secret' => $this->get_client_secret(),
					),
					'scope'           => implode( ' ', array(
						'https://www.googleapis.com/auth/userinfo.profile',
						'https://www.googleapis.com/auth/userinfo.email'
					) ),
					'access_type'     => 'offline',
					'approval_prompt' => 'force',
				),
			),
		);

		return new \Hybrid_Auth( $config );
	}

	/**
	 * Get API Data
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function api_get_data() {
		if ( ! $this->is_active() ) {
			return false;
		}
		$hybridauth = $this->api_init();
		$adapter = $hybridauth->authenticate( 'Google' );
		$profile = $adapter->getUserProfile();

		$data = array(
			'id'                 => property_exists( $profile, 'identifier' ) ? $profile->identifier : '',
			'user_email'         => property_exists( $profile, 'emailVerified' ) ? $profile->emailVerified : ( property_exists( $profile, 'email' ) ? $profile->email : '' ),
			'display_name'       => property_exists( $profile, 'displayName' ) ? $profile->displayName : '',
			'nickname'           => property_exists( $profile, 'displayName' ) ? $profile->displayName : '',
			'first_name'         => property_exists( $profile, 'firstName' ) ? $profile->firstName : '',
			'last_name'          => property_exists( $profile, 'lastName' ) ? $profile->lastName : '',
			'gmail'              => property_exists( $profile, 'emailVerified' ) ? $profile->emailVerified : ( property_exists( $profile, 'email' ) ? $profile->email : '' ), // Gmail.
		);

		return $data;
	}
}
