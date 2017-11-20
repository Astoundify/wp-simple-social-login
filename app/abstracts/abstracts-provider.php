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
	 * Is Active.
	 *
	 * @since 1.0.0
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
	 * Unlink Button
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_unlink_button() {
		
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
		return isset( $option['connected_info'] ) && $option['connected_info'] ? esc_attr( $option['connected_info'] ) : '';
	}









	

}

































