<?php
/**
 * Social Login Provider Abstarct Class
 *
 * @since 1.0.0
 *
 * @package  Abstracts
 * @category Core
 * @author   Astoundify
 */

namespace Astoundify\Simple_Social_Login;

// Do not access this file directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provider
 *
 * Options should be stored as array using keys:
 * - login_button_text
 * - link_button_text
 * - connected_info
 *
 * User meta keys:
 * - "_astoundify_simple_social_login_{$this->id}_id"                (string)
 * - "_astoundify_simple_social_login_{$this->id}_connected"         (bool)
 * - "_astoundify_simple_social_login_{$this->id}_timestamp"         (int)
 * - "_astoundify_simple_social_login_{$this->id}_timestamp_gmt"     (int)
 * - "_astoundify_simple_social_login_{$this->id}_profile_image_url" (string)
 *
 * @since 1.0.0
 */
abstract class Provider {

	/**
	 * Provider ID.
	 *
	 * @since 1.0.0
	 * @var string $id Provider ID.
	 */
	public $id;

	/**
	 * Provider label.
	 *
	 * @since 2.0.0
	 * @var string $label Provider label.
	 */
	public $label;

	/**
	 * Option Name
	 *
	 * @since 1.0.0
	 * @var string $option_name Option Name.
	 */
	public $option_name;

	/**
	 * Config
	 *
	 * @since 2.0.0
	 * @var array $config Provider configuration.
	 */
	public $config;

	/**
	 * Return ID.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public function get_id() {
		return esc_html( $this->id );
	}

	/**
	 * Label
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html( $this->label );
	}

	/**
	 * Get base config.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_config() {
		return wp_parse_args(
			$this->config, [
				'callback'        => $this->get_endpoint_url(),
				'access_type'     => 'offline',
				'approval_prompt' => 'force',
				'keys'            => [
					'id'     => $this->get_app_id(),
					'secret' => $this->get_app_secret(),
				],
			]
		);
	}

	/**
	 * App ID.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_app_id() {
		$option = get_option( $this->option_name, [] );

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
		$option = get_option( $this->option_name, [] );

		return isset( $option['app_secret'] ) ? esc_attr( trim( $option['app_secret'] ) ) : '';
	}

	/**
	 * Normalize profile data.
	 *
	 * @since 2.0.0
	 *
	 * @param $adapter Provider HyrbidAuth provider.
	 * @return array
	 */
	public function get_profile_data( $adapter ) {
		$profile = $adapter->getUserProfile();

		$data = [
			'id'            => isset( $profile->identifier ) ? $profile->identifier : '',
			'user_email'    => ( isset( $profile->emailVerified ) && '' !== $profile->emailVerified ) ? $profile->emailVerified : ( isset( $profile->email ) ? $profile->email : '' ),
			'display_name'  => isset( $profile->displayName ) ? $profile->displayName : '',
			'nickname'      => isset( $profile->displayName ) ? $profile->displayName : '',
			'first_name'    => isset( $profile->firstName ) ? $profile->firstName : '',
			'last_name'     => isset( $profile->lastName ) ? $profile->lastName : '',
			'profile_image' => isset( $profile->photoURL ) ? $profile->photoURL : '',
		];

		if ( ! $data['id'] ) {
			return false;
		}

		return $data;
	}

	/**
	 * Get URL
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_action_url( $action ) {
		$args = [
			'astoundify_simple_social_login' => $action,
			'provider'                       => $this->id,
			'_nonce'                         => wp_create_nonce( "astoundify_simple_social_login_{$this->id}" ),
			'_referrer'                      => urlencode( strtok( esc_url( $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ), '?' ) ),
		];

		return add_query_arg( $args, home_url() );
	}

	/**
	 * Endpoint URL (HybridAuth Process)
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_endpoint_url() {
		return add_query_arg(
			[
				'astoundify_simple_social_login' => 'authenticate',
				'provider'                       => $this->id,
			],
			user_trailingslashit( home_url() )
		);
	}

	/**
	 * Is Active?
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_active() {
		// Is selected as active provider?
		$options = get_option( 'astoundify_simple_social_login', [] );
		$options = is_array( $options ) ? $options : [];

		$providers = isset( $options['providers'] ) && is_array( $options['providers'] ) ? $options['providers'] : [];

		if ( ! in_array( $this->id, $providers, true ) ) {
			return false;
		}

		// Check API requirements.
		$app_id     = $this->get_app_id();
		$app_secret = $this->get_app_secret();

		if ( ! $app_id || ! $app_secret ) {
			return false;
		}

		return true;
	}

	/**
	 * Login Register Button Text Default
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_login_register_button_text_default() {
		return sprintf( esc_html__( 'Log in with %s', 'astoundify-simple-social-login' ), $this->get_label() );
	}

	/**
	 * Link Button Text.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_link_button_text_default() {
		return sprintf( esc_html__( 'Link your account to %s', 'astoundify-simple-social-login' ), $this->get_label() );
	}

	/**
	 * Connected Info Text
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_connected_info_text_default() {
		return sprintf( esc_html__( 'Your account is connected to %s. {{unlink}}', 'astoundify-simple-social-login' ), $this->get_label() );
	}

	/**
	 * Get SVG Icon
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_icon() {
		return astoundify_simple_social_login_get_svg( $this->id );
	}

	/**
	 * Login Register Button Text
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_login_register_button_text() {
		$option = get_option( $this->option_name, [] );

		return isset( $option['login_button_text'] ) && $option['login_button_text'] ? esc_attr( $option['login_button_text'] ) : $this->get_login_register_button_text_default();
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
		if ( is_user_logged_in() || ! $this->is_active() ) {
			return '';
		}

		$text = $this->get_login_register_button_text();
		$url  = $this->get_action_url( 'authenticate' );

		$classes = [
			'astoundify-simple-social-login-button',
			'astoundify-simple-social-login-button-' . $this->id,
			'astoundify-simple-social-login-login-register-button',
			'astoundify-simple-social-login-login-register-button-' . $this->id,
		];

		$classes = implode( ' ', array_map( 'sanitize_html_class', $classes ) );
		$icon    = $this->get_icon();

		$html = "<a class='{$classes}' href='{$url}'>{$icon} {$text}</a>";

		return $html;
	}

	/**
	 * Link Button Text.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_link_button_text() {
		$option = get_option( $this->option_name, [] );

		return isset( $option['link_button_text'] ) && $option['link_button_text'] ? esc_attr( $option['link_button_text'] ) : $this->get_link_button_text_default();
	}

	/**
	 * Unlink Button
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_unlink_button() {
		$text = apply_filters( 'astoundify_simple_social_login_unlink_link_text', esc_html__( 'Unlink', 'astoundify-simple-social-login' ) );
		$url  = $this->get_action_url( 'unlink' );

		return sprintf( '<a href="%s">%s</a>', esc_url( $url ), $text );
	}

	/**
	 * Connected Info Text
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_connected_info_text() {
		$option = get_option( $this->option_name, [] );
		$text   = isset( $option['connected_info'] ) && $option['connected_info'] ? esc_attr( $option['connected_info'] ) : $this->get_connected_info_text_default();
		$text   = str_replace( '{{unlink}}', $this->get_unlink_button(), $text );

		return $text;
	}

	/**
	 * Link/Unlink Button
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_link_unlink_button() {
		if ( ! is_user_logged_in() || ! $this->is_active() ) {
			return '';
		}

		// User not connected, show connect button.
		if ( ! astoundify_simple_social_login_is_user_connected_to_provider( get_current_user_id(), $this->get_id() ) ) {
			$is_connected = false;

			$text = $this->get_link_button_text();
			$url  = $this->get_action_url( 'link' );

			$classes = [
				'astoundify-simple-social-login-button',
				'astoundify-simple-social-login-button-' . $this->id,
				'astoundify-simple-social-login-link-button',
				'astoundify-simple-social-login-link-button-' . $this->id,
			];
			$classes = esc_attr( implode( ' ', array_map( 'sanitize_html_class', $classes ) ) );
			$icon    = $this->get_icon();

			$html = "<p><a class='{$classes}' href='{$url}'>{$icon} {$text}</a></p>";
		} else { // Already connected, show connected info.
			$is_connected = true;

			$text    = $this->get_connected_info_text();
			$url     = $this->get_action_url( 'unlink' );
			$classes = [
				'astoundify-simple-social-login-unlink-text',
				'astoundify-simple-social-login-unlink-text-' . $this->id,
			];
			$classes = esc_attr( implode( ' ', array_map( 'sanitize_html_class', $classes ) ) );

			$html = "<p class='{$classes}'>{$text}</p>";
		}

		return $html;
	}

}
