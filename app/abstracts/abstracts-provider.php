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
	 * Get URL
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_action_url( $args ) {
		$defaults = array(
			'astoundify_simple_social_login' => $this->id,
			'action'                         => '',
			'_nonce'                         => wp_create_nonce( "astoundify_simple_social_login_{$this->id}" ),
			'_referer'                       => urlencode( esc_url( $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) ),
		);
		$args = wp_parse_args( $args, $defaults );

		$url = add_query_arg( $args, home_url() );

		return $url;
	}

	/* === LOGIN/REGISTER === */

	/**
	 * Login Register Button Text
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_login_register_button_text() {
		$option = get_option( $this->option_name, array() );
		return isset( $option['login_button_text'] ) && $option['login_button_text'] ? esc_attr( $option['login_button_text'] ) : '';
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
		$url = $this->get_action_url( array(
			'action' => 'login_register',
		) );
		$classes = array(
			'button',
			'astoundify-simple-social-login-register-button',
			'astoundify-simple-social-login-register-button-' . $this->id,
		);
		$classes = implode( ' ', array_map( 'sanitize_html_class', $classes ) );

		$html = "<p><a class='{$classes}' href='{$url}'>{$text}</a></p>";

		return $html;
	}

	/* === LINK/UNLINK === */

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
	 * Link Button Text.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_link_button_text() {
		$option = get_option( $this->option_name, array() );
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
		$text = apply_filters( 'astoundify_simple_social_login_unlink_link_text', esc_html__( 'Unlink' ) );
		$title = $this->get_last_connected_time_text();
		$url = $this->get_action_url( array(
			'action' => 'unlink',
		) );
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
		$option = get_option( $this->option_name, array() );
		$text = isset( $option['connected_info'] ) && $option['connected_info'] ? esc_attr( $option['connected_info'] ) : '';
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

			$text = $this->get_link_button_text();
			$url = $this->get_action_url( array(
				'action' => 'link',
			) );
			$classes = array(
				'button',
				'astoundify-simple-social-link-button',
				'astoundify-simple-social-link-button-' . $this->id,
			);
			$classes = esc_attr( implode( ' ', array_map( 'sanitize_html_class', $classes ) ) );

			$html = "<p><a class='{$classes}' href='{$url}'>{$text}</a></p>";
		} else { // Already connected, show connected info.
			$is_connected = true;

			$text = get_connected_info_text();
			$url = $this->get_action_url( array(
				'action' => 'unlink',
			) );
			$classes = array(
				'astoundify-simple-social-login-unlink-text',
				'astoundify-simple-social-login-unlink-text-' . $this->id,
			);
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
		$is_connected = get_user_meta( $user->ID, "_astoundify_simple_social_login_{$this->id}_connected" , true );

		return $is_connected ? true : false;
	}

	/**
	 * Insert User.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data API data.
	 * @return int|false
	 */
	public function insert_user( $data ) {
		if ( ! $this->is_active() ) {
			return false;
		}

		$defaults = array(
			'id'           => '',
			'user_login'   => '',
			'user_pass'    => wp_generate_password(),
			'user_email'   => '',
			'display_name' => '',
			'nickname'     => '',
			'first_name'   => '',
			'last_name'    => '',
		);
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
		$sitename = strtolower( $_SERVER['SERVER_NAME'] );
		if ( substr( $sitename, 0, 4 ) == 'www.' ) {
			$sitename = substr( $sitename, 4 );
		}
		if ( ! $data['user_email'] ) {
			$data['user_email'] = sanitize_email( $data['id'] . '@' . $sitename );
		}
		if ( email_exists( $data['user_email'] ) ) {
			$data['user_email'] = sanitize_email( $data['id'] . '_' . time() . '@' . $sitename );
		}

		$inserted = wp_insert_user( $data );
		$user_id = $inserted && is_wp_error( $inserted ) ? false : intval( $inserted );

		if ( ! $user_id ) {
			return false;
		}

		// Success. Add user meta.
		update_user_meta( $user_id, "_astoundify_simple_social_login_{$this->id}_id", esc_html( $data['id'] ) );
		update_user_meta( $user_id, "_astoundify_simple_social_login_{$this->id}_timestamp", current_time( 'timestamp' ) );
		update_user_meta( $user_id, "_astoundify_simple_social_login_{$this->id}_timestamp_gmt", time() );
		update_user_meta( $user_id, "_astoundify_simple_social_login_{$this->id}_connected", 1 );

		// Unset defaults data, save extra datas as user meta.
		foreach( $defaults as $k => $v ) {
			unset( $data[ $k ] );
		}
		foreach( $data as $k => $v ) {
			update_user_meta( $user_id, "_astoundify_simple_social_login_{$this->id}_{$k}", $v );
		}

		return $user_id;
	}

	/**
	 * Link User
	 *
	 * @since 1.0.0
	 *
	 * @param array $data API data.
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

		$defaults = array(
			'id' => time(),
		);
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
		foreach( $defaults as $k => $v ) {
			unset( $data[ $k ] );
		}
		foreach( $data as $k => $v ) {
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
		$args = array(
			'meta_key'    => "_astoundify_simple_social_login_{$this->id}_id",
			'meta_value'  => esc_html( $id ),
			'number'      => -1,
			'fields'      => 'ID',
		);
		$users = get_users( $args );

		// If user found, return it.
		if ( $users ) {
			if ( 1 === count( $users )  ) {
				return intval( $users[0] );
			} else {
				// More than one users connected to the same account ? Maybe notice admin.
				return false;
			}
		}
		return false;
	}

	/* === API === */

	/**
	 * Error Codes
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_error_codes() {
		$errors = array(
			'no_id'                    => esc_html__( 'Cannot retrieve social account ID.', 'astoundify-simple-social-login' ),
			'already_log_in'           => esc_html__( 'User already logged-in.', 'astoundify-simple-social-login' ),
			'api_error'                => esc_html__( 'API connection error.', 'astoundify-simple-social-login' ),
			'connected_user_not_found' => esc_html__( 'Login failed. You are not registered to this website.', 'astoundify-simple-social-login' ),
			'registration_fail'        => esc_html__( 'Fail to register user.', 'astoundify-simple-social-login' ),
			'already_connected'        => esc_html__( 'User already connected.', 'astoundify-simple-social-login' ),
			'link_fail'                => esc_html__( 'Fail to link account with social profile.', 'astoundify-simple-social-login' ),
			'unknown_action'           => esc_html__( 'Action unknown.', 'astoundify-simple-social-login' ),
		);
		return $errors;
	}

	/**
	 * Redirect/Error Redirect
	 *
	 * @since 1.0.0
	 *
	 * @param string $url Redirect URL.
	 * @param string $error_code Error code.
	 */
	public function redirect( $url, $error_code = false ) {
		$url = remove_query_arg( '_error', $url );
		if ( $error_code ) {
			$url = add_query_arg( '_error', $error_code, $url );
		}
		wp_safe_redirect( esc_url_raw( $url ) );
		exit;
	}
}
