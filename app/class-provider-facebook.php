<?php
/**
 * Social Login Provider: Facebook
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
 * Facebook Social Login Provider
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
		$option = get_option( $this->option_name, array() );
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
		$option = get_option( $this->option_name, array() );
		return isset( $option['app_secret'] ) ? esc_attr( trim( $option['app_secret'] ) ) : '';
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
		$url = add_query_arg( $args, user_trailingslashit( home_url() ) );
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

		// Load library.
		require_once( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'vendor/hybridauth/hybridauth/hybridauth/Hybrid/Auth.php' );

		$config = array(
			'base_url'  => $this->get_endpoint_url(),
			'providers' => array(
				'Facebook' => array(
					'enabled'         => true,
					'keys'            => array(
						'id'     => $this->get_app_id(),
						'secret'  => $this->get_app_secret(),
					),
					'scope'           => 'email',
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
		$adapter = $hybridauth->authenticate( 'Facebook' );
		$profile = $adapter->getUserProfile();

		$data = array(
			'id'                 => property_exists( $profile, 'identifier' ) ? $profile->identifier : '',
			'user_email'         => property_exists( $profile, 'emailVerified' ) ? $profile->emailVerified : ( property_exists( $profile, 'email' ) ? $profile->email : '' ),
			'display_name'       => property_exists( $profile, 'displayName' ) ? $profile->displayName : '',
			'nickname'           => property_exists( $profile, 'displayName' ) ? $profile->displayName : '',
			'first_name'         => property_exists( $profile, 'firstName' ) ? $profile->firstName : '',
			'last_name'          => property_exists( $profile, 'lastName' ) ? $profile->lastName : '',
			'screen_name'        => property_exists( $profile, 'displayName' ) ? $profile->displayName : '', // Twitter username.
		);

		if ( ! $data['id'] ) {
			return false;
		}

		return $data;
	}

}
