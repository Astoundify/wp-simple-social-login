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
	 * App ID.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function app_id() {
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
	public function app_id() {
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
		$app_id = astoundify_simple_social_login_facebook_get_app_id();
		$app_secret = astoundify_simple_social_login_facebook_get_app_secret();
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
	 */
	public function get_connected_info_text() {
		$text = parent::get_connected_info_text();
		// translators: {{unlink}} is a placeholder for unlink account link. Do not translate.
		$text = $text ? $text : esc_html__( 'Your account is connected to Facebook. {{unlink}}.' );

		$text = str_replace( '{{unlink}}', $this->get_unlink_button(), $text );

		return $text;
	}












}