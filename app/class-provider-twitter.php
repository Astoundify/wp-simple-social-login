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
	 * @return Facebook\Facebook|false
	 */
	function api_init() {

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

	}

}
