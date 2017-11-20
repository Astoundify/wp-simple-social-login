<?php
/**
 * Social Login Provider Abstarct Class
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
abstract class Provider {

	/**
	 * Provider ID.
	 *
	 * @since 1.0.0
	 *
	 * @var string $id Provider ID.
	 */
	public $id = '';

	/**
	 * Option Name
	 *
	 * @since 1.0.0
	 *
	 * @var string $option_name Option Name.
	 */
	public $option_name = '';

	/**
	 * Is Active?
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_active() {
		// Is selected as active provider?
		$options = get_option( 'astoundify_simple_social_login', array() );
		$options = is_array( $options ) ? $options : array();

		$providers = isset( $options['providers'] ) && is_array( $options['providers'] ) ? $options['providers'] : array();

		if ( ! in_array( $this->id, $providers, true ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Is user connected?
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_user_connected( $user_id = null ) {
		$user = $user_id ? get_userdata( $user_id ) : wp_get_current_user();

		// Bail if user not set.
		if ( ! $user ) {
			return false;
		}

		// Is connected status.
		$is_connected = get_user_meta( $user->ID, "_astoundify_simple_social_login_{$this->id}_connected" , true );

		return $is_connected ? true : false;
	}

	/**
	 * Login Register Button Text
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_login_register_button_text() {
		$option = get_option( $option_name, array() );
		return isset( $option['login_button_text'] ) && $option['login_button_text'] ? esc_attr( $option['login_button_text'] ) : '';
	}

	/**
	 * Link Button Text.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_link_button_text() {
		$option = get_option( $option_name, array() );
		return isset( $option['link_button_text'] ) && $option['link_button_text'] ? esc_attr( $option['link_button_text'] ) : '';
	}

	/**
	 * Get Last Connected Time
	 *
	 * @since 1.0.0
	 */
	public function get_last_connected_time_text( $user_id = null ) {
		$user = null !== $user_id ? get_userdata( intval( $user_id ) ) : wp_get_current_user();
		if ( ! $user ) {
			return '';
		}

		$time = '';
		$timestamp = get_user_meta( $user->ID, "_astoundify_simple_social_login_{$this->id}_timestamp", true );
		if ( $timestamp ) {
			$time = sprintf( esc_html__( 'Last connected: %1$s @ %2$s', 'astoundify-simple-social-login' ), date_i18n( astoundify_simple_social_login_get_date_format(), $timestamp ), date_i18n( astoundify_simple_social_login_get_time_format(), $timestamp ) );
		}

		return $time;
	}

	/**
	 * Get URL
	 *
	 * @since 1.0.0
	 */
	public function get_action_url( $args ) {
		$defaults = array(
			'astoundify_simple_social_login' => $this->id,
			'action'                         => '',
			'redirect_to'                    => astoundify_simple_social_login_get_redirect_url( $action ),
			'_nonce'                         => wp_create_nonce( "astoundify_simple_social_login_{$action}" ),
			'_referer'                       => urldecode( esc_url( $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) ),
		);
		$args = wp_parse_args( $args, $defaults );

		$url = add_query_arg( $args, home_url() );

		return esc_url( $url );
	}

	/**
	 * Unlink Button
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_unlink_button() {
		$text = apply_filters( 'astoundify_simple_social_login_unlink_link_text', esc_html__( 'Unlink' ) );
		$title = $this->get_last_connected_time_text();
		$url = $this->get_action_url( 'unlink' );
		return "<a href='{$url}' title='{$title}'>{$text}</a>";
	}

	/**
	 * Connected Info Text
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_connected_info_text() {
		$option = get_option( $option_name, array() );
		$text = isset( $option['connected_info'] ) && $option['connected_info'] ? esc_attr( $option['connected_info'] ) : '';
		return $text;
	}

	/**
	 * Login/Register Button
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_login_register_button() {
		// Not needed if user already logged in.
		if ( is_user_logged_in() ) {
			return '';
		}

		$text = $this->get_login_register_button_text();
		$url = $this->get_action_url( 'login_register' );
		$classes = array(
			'button',
			'astoundify-simple-social-login-register-button',
			'astoundify-simple-social-login-register-button-' . $this->id,
		);
		$classes = implode( ' ', array_map( 'sanitize_html_class', $classes ) );

		$html = "<p><a class='{$classes}' href='{$url}'>{$text}</a></p>";

		return $html;
	}







	

}

































