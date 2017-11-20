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
		return $text ? esc_attr( $text ) : esc_html__( 'Log in with Facebook', 'astoundify-simple-social-login' );
	}


	/**
	 * Connected Info Text
	 *
	 * @since 1.0.0
	 */
	public function get_connected_info_text() {
		$text = parent::get_connected_info_text();
		// translators: Do not translate {{unlink}} text. It'a a placeholder to unlink account.
		$text = $text ? $text : esc_html__( 'Your account is connected to Facebook. {{unlink}}.' );

		$connected_info = isset( $option['connected_info'] ) && $option['connected_info'] ? esc_attr( $option['connected_info'] ) : esc_html__( 'Your account is connected to Facebook. {{unlink}}.', 'astoundify-simple-social-login' );

		$unlink_link_text = apply_filters( 'astoundify_simple_social_login_unlink_link_text', esc_html__( 'Unlink' ) );
		$unlink_url = astoundify_simple_social_login_facebook_get_url( 'unlink' );
		$last_connected_time = esc_attr( astoundify_simple_social_login_get_last_connected_time_text( get_current_user_id(), 'facebook' ) );
		$unlink = "<a href='{$unlink_url}' title='{$last_connected_time}'>{$unlink_link_text}</a>";

		$connected_info = str_replace( '{{unlink}}', $unlink, $connected_info );

		return $connected_info;
	}












}