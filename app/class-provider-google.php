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
	function api_init( $redirect = false, $oauth_token = false, $oauth_token_secret = false ) {
		if ( ! $this->is_active() ) {
			return false;
		}

		
		
		
		
		
		
		
		
		
		
		
		
	}

	/**
	 * Get API Data
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function api_get_data( $user_token = false, $user_token_secret = false ) {
		$data = array();
		return $data;
	}

}





















