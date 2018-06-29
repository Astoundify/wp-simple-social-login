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
 * - "_astoundify_simple_social_login_{$this->id}_id"            (string)
 * - "_astoundify_simple_social_login_{$this->id}_connected"     (bool)
 * - "_astoundify_simple_social_login_{$this->id}_timestamp"     (int)
 * - "_astoundify_simple_social_login_{$this->id}_timestamp_gmt" (int)
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
	 * Label
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_label() {
		return $this->id;
	}

	/**
	 * Get base config.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_config() {
		return [
			'callback'  => $this->get_endpoint_url(),
			'keys'      => [
				'id'     => $this->get_app_id(),
				'secret' => $this->get_app_secret(),
			],
		];
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
	 * Get URL
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_action_url( $action ) {
		$defaults = [
			'astoundify_simple_social_login' => $action,
			'provider'                       => $this->id,
			'_nonce'                         => wp_create_nonce( "astoundify_simple_social_login_{$this->id}" ),
			'_referer'                       => urlencode( strtok( esc_url( $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ), '?' ) ), // Remove all query vars from URL.
		];

		$args = wp_parse_args( $args, $defaults );

		return add_query_arg( $args, home_url() );
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
	 * Default Login Register Button Text.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_login_register_button_text_default() {
		return '';
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

		$text    = $this->get_login_register_button_text();
		$url     = $this->get_action_url( 'authenticate' );

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
	 * Link Button Text Default.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_link_button_text_default() {
		return '';
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
		$text  = apply_filters( 'astoundify_simple_social_login_unlink_link_text', esc_html__( 'Unlink', 'astoundify-simple-social-login' ) );
		$title = $this->get_last_connected_time_text();
		$url   = $this->get_action_url( 'unlink' );

		return "<a href='{$url}' title='{$title}'>{$text}</a>";
	}

	/**
	 * Connected Info Text Default
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_connected_info_text_default() {
		return '';
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
		if ( ! $this->is_user_connected() ) {
			$is_connected = false;

			$text    = $this->get_link_button_text();
			$url     = $this->get_action_url( 'link' );

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

	/* === USER === */

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
		$is_connected = get_user_meta( $user->ID, "_astoundify_simple_social_login_{$this->id}_connected", true );

		return $is_connected ? true : false;
	}

	/**
	 * Insert User.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $data API data.
	 * @return int|false
	 */
	public function insert_user( $data ) {
		if ( ! $this->is_active() ) {
			return false;
		}

		$defaults = [
			'id'           => '',
			'user_login'   => '',
			'user_pass'    => wp_generate_password(),
			'user_email'   => '',
			'display_name' => '',
			'nickname'     => '',
			'first_name'   => '',
			'last_name'    => '',
		];

		$data = wp_parse_args( $data, $defaults );

		// Bail if no ID.
		if ( ! $data['id'] || ! $data['display_name'] ) {
			return false;
		}

		// User Login.
		$data['user_login'] = sanitize_title( $data['display_name'] );

		if ( username_exists( $data['user_login'] ) ) {
			$data['user_login'] = $data['user_login'] . '_' . time();
		}

		// Email.
		if ( $data['user_email'] && email_exists( $data['user_email'] ) ) {
			return false;
		}

		$inserted = wp_insert_user( $data );
		$user_id  = $inserted && is_wp_error( $inserted ) ? false : intval( $inserted );

		if ( ! $user_id ) {
			return false;
		}

		// Success. Add user meta.
		update_user_meta( $user_id, "_astoundify_simple_social_login_{$this->id}_id", esc_html( $data['id'] ) );
		update_user_meta( $user_id, "_astoundify_simple_social_login_{$this->id}_timestamp", current_time( 'timestamp' ) );
		update_user_meta( $user_id, "_astoundify_simple_social_login_{$this->id}_timestamp_gmt", time() );
		update_user_meta( $user_id, "_astoundify_simple_social_login_{$this->id}_connected", 1 );

		// Unset defaults data, save extra datas as user meta.
		foreach ( $defaults as $k => $v ) {
			unset( $data[ $k ] );
		}

		foreach ( $data as $k => $v ) {
			update_user_meta( $user_id, "_astoundify_simple_social_login_{$this->id}_{$k}", $v );
		}

		return $user_id;
	}

	/**
	 * Link User
	 *
	 * @since 1.0.0
	 *
	 * @param  array $data API data.
	 * @return int|false
	 */
	public function link_user( $data ) {
		if ( ! $this->is_active() ) {
			return false;
		}

		$user = wp_get_current_user();
		if ( ! $user || ! $user->ID ) {
			return false;
		}

		$user_id = $user->ID;

		$defaults = [
			'id' => time(),
		];

		$data = wp_parse_args( $data, $defaults );

		// Bail if no ID.
		if ( ! $data['id'] ) {
			return false;
		}

		// Add user meta.
		update_user_meta( $user_id, "_astoundify_simple_social_login_{$this->id}_id", esc_html( $data['id'] ) );
		update_user_meta( $user_id, "_astoundify_simple_social_login_{$this->id}_timestamp", current_time( 'timestamp' ) );
		update_user_meta( $user_id, "_astoundify_simple_social_login_{$this->id}_timestamp_gmt", time() );
		update_user_meta( $user_id, "_astoundify_simple_social_login_{$this->id}_connected", 1 );

		// Unset defaults data, save extra datas as user meta.
		foreach ( $defaults as $k => $v ) {
			unset( $data[ $k ] );
		}

		foreach ( $data as $k => $v ) {
			update_user_meta( $user_id, "_astoundify_simple_social_login_{$this->id}_{$k}", $v );
		}

		return $user->ID;
	}

	/**
	 * Unlink User
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function unlink_user( $user_id ) {
		if ( ! $this->is_active() ) {
			return false;
		}

		$user = $user_id ? get_userdata( $user_id ) : wp_get_current_user();

		// Bail if user not set.
		if ( ! $user ) {
			return false;
		}

		$user_id = $user->ID;

		delete_user_meta( $user_id, "_astoundify_simple_social_login_{$this->id}_id" );
		delete_user_meta( $user_id, "_astoundify_simple_social_login_{$this->id}_connected" );

		return true;
	}

	/**
	 * Get Connected User ID
	 *
	 * @since 1.0.0
	 *
	 * @return false|int
	 */
	public function get_connected_user_id( $id ) {
		$args  = [
			'meta_key'   => "_astoundify_simple_social_login_{$this->id}_id",
			'meta_value' => esc_html( $id ),
			'number'     => -1,
			'fields'     => 'ID',
		];

		$users = get_users( $args );

		// If user found, return it.
		if ( $users ) {
			if ( 1 === count( $users ) ) {
				return intval( $users[0] );
			} else {
				// More than one users connected to the same account ? Maybe notice admin.
				return false;
			}
		}

		return false;
	}

}
