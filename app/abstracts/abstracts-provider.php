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
	 * Label
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_label() {
		// Use ID as fallback.
		return $this->id;
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
	public function get_action_url( $args ) {
		$defaults = array(
			'astoundify_simple_social_login' => $this->id,
			'action'                         => '',
			'_nonce'                         => wp_create_nonce( "astoundify_simple_social_login_{$this->id}" ),
			'_referer'                       => urlencode( strtok( esc_url( $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ), '?'  ) ), // Remove all query vars from URL.
		);
		$args = wp_parse_args( $args, $defaults );

		$url = add_query_arg( $args, home_url() );

		return $url;
	}

	/* === APP CREDENTIALS === */

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
	 * Endpoint URL (HybridAuth Process)
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_endpoint_url() {
		return add_query_arg( 'astoundify_simple_social_login', 'done', user_trailingslashit( home_url() ) );
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
		$options = get_option( 'astoundify_simple_social_login', array() );
		$options = is_array( $options ) ? $options : array();

		$providers = isset( $options['providers'] ) && is_array( $options['providers'] ) ? $options['providers'] : array();

		if ( ! in_array( $this->id, $providers, true ) ) {
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

	/* === LOGIN/REGISTER === */

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
		$option = get_option( $this->option_name, array() );
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
		$url = $this->get_action_url( array(
			'action' => 'login_register',
		) );
		$classes = array(
			'astoundify-simple-social-login-button',
			'astoundify-simple-social-login-button-' . $this->id,
			'astoundify-simple-social-login-login-register-button',
			'astoundify-simple-social-login-login-register-button-' . $this->id,
		);
		$classes = implode( ' ', array_map( 'sanitize_html_class', $classes ) );
		$icon = $this->get_icon();

		$html = "<p><a class='{$classes}' href='{$url}'>{$icon} {$text}</a></p>";

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
		$option = get_option( $this->option_name, array() );
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
		$text = apply_filters( 'astoundify_simple_social_login_unlink_link_text', esc_html__( 'Unlink' ) );
		$title = $this->get_last_connected_time_text();
		$url = $this->get_action_url( array(
			'action' => 'unlink',
		) );
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
		$option = get_option( $this->option_name, array() );
		$text = isset( $option['connected_info'] ) && $option['connected_info'] ? esc_attr( $option['connected_info'] ) : $this->get_connected_info_text_default();
		$text = str_replace( '{{unlink}}', $this->get_unlink_button(), $text );
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
				'astoundify-simple-social-login-button',
				'astoundify-simple-social-login-button-' . $this->id,
				'astoundify-simple-social-login-link-button',
				'astoundify-simple-social-login-link-button-' . $this->id,
			);
			$classes = esc_attr( implode( ' ', array_map( 'sanitize_html_class', $classes ) ) );
			$icon = $this->get_icon();

			$html = "<p><a class='{$classes}' href='{$url}'>{$icon} {$text}</a></p>";
		} else { // Already connected, show connected info.
			$is_connected = true;

			$text = $this->get_connected_info_text();
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
		if ( $data['user_email'] && email_exists( $data['user_email'] ) ) {
			return false;
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
	 * Connect via HybridAuth
	 *
	 * @since 1.0.0
	 *
	 * @return Hybrid_Auth|false
	 */
	function api_init( $config ) {
		if ( ! $this->is_active() ) {
			return false;
		}

		require_once( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'vendor/hybridauth/hybridauth/hybridauth/Hybrid/Auth.php' );

		$hybridauth = false;

		try {
			$hybridauth = new \Hybrid_Auth( $config );
		} catch( \Exception $e ) {
			return false;
		}

		return $hybridauth;
	}

	/**
	 * Get Error
	 *
	 * @since 1.0.0
	 *
	 * @param string $code Error Code.
	 * @return string
	 */
	public function get_error( $code ) {
		$error_codes = $this->get_error_codes();
		return isset( $error_codes[ $code ] ) ? $error_codes[ $code ] : printf( esc_html__( 'Unknown Error: %s', 'astoundify-simple-social-login' ), $code );
	}

	/**
	 * Error Codes
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_error_codes() {
		$errors = array(
			'no_id'                     => esc_html__( 'Cannot retrieve social profile ID.', 'astoundify-simple-social-login' ),
			'already_logged_in'         => esc_html__( 'User already logged-in.', 'astoundify-simple-social-login' ),
			'not_logged_in'             => esc_html__( 'User need to login to connect.', 'astoundify-simple-social-login' ),
			'api_error'                 => esc_html__( 'API connection error.', 'astoundify-simple-social-login' ),
			'connected_user_not_found'  => esc_html__( 'Login failed. Your social profile is not registered to this website.', 'astoundify-simple-social-login' ),
			'registration_fail'         => esc_html__( 'Fail to register user.', 'astoundify-simple-social-login' ),
			'already_connected'         => esc_html__( 'User already connected.', 'astoundify-simple-social-login' ),
			'link_fail'                 => esc_html__( 'Fail to link account with social profile.', 'astoundify-simple-social-login' ),
			'unknown_action'            => esc_html__( 'Error. Action unknown.', 'astoundify-simple-social-login' ),
			'email_already_registered'  => esc_html__( 'Fail to register user. Email already registered.', 'astoundify-simple-social-login' ),
			'another_already_connected' => esc_html__( 'Another user already connected to social account.', 'astoundify-simple-social-login' ),
		);
		return $errors;
	}

	/**
	 * Error Redirect
	 *
	 * @since 1.0.0
	 *
	 * @param string $error_code   Error code.
	 * @param string $redirect_url Force redirect URL, optional.
	 */
	public function error_redirect( $error_code, $redirect_url = false ) {
		// Redirect URL.
		$url = $redirect_url ? $redirect_url : wp_login_url();

		// Error code.
		$url = remove_query_arg( '_error', $url );
		$url = add_query_arg( '_error', $error_code, $url );

		// Provider info to get error code string/content.
		$url = remove_query_arg( '_provider', $url );
		$url = add_query_arg( '_provider', $this->id, $url );

		// Add filter to modify url.
		$url = apply_filters( 'astoundify_simple_social_login_error_redirect_url', $url, $error_code, $redirect_url, $this );

		// Redirect with error code.
		wp_safe_redirect( esc_url_raw( $url ) );
		exit;
	}

	/**
	 * Success Redirect
	 *
	 * @since 1.0.0
	 *
	 * @param string $redirect_url Force redirect URL, optional.
	 */
	public function success_redirect( $redirect_url = false ) {
		$url = apply_filters( 'astoundify_simple_social_login_success_redirect_url', $redirect_url ? $redirect_url : home_url(), $redirect_url, $this );

		if ( false !== strpos( $url, 'wp-login.php' ) ) {
			$url = home_url();
		}
		wp_safe_redirect( esc_url_raw( add_query_arg( '_flush', time(), $url ) ) );
		exit;
	}

	/**
	 * Link Data: Only store social account ID on link process.
	 * Do not override account email, name, etc.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Full social data.
	 * @return array
	 */
	public function get_link_data( $data ) {
		$selected_data = array(
			'id' => $data['id'],
		);
		return $selected_data;
	}

	/**
	 * Process Action
	 *
	 * @since 1.0.0
	 *
	 * @param string $action Request action.
	 * @param string $referer URL.
	 */
	public function process_action( $action, $referer ) {

		// Separate each action.
		switch ( $action ) {
			case 'login_register':
				if ( is_user_logged_in() ) {
					$this->error_redirect( 'already_logged_in' );
				}

				$data = $this->api_get_data();
				if ( ! $data || ! isset( $data['id'] ) ) {
					$this->error_redirect( 'api_error' );
				}

				// Get connected user ID.
				$user_id = $this->get_connected_user_id( $data['id'] );

				// User found. Log them in.
				if ( $user_id ) {
					astoundify_simple_social_login_log_user_in( $user_id );
					$this->success_redirect( urldecode( $referer ) );
				}

				// If registration disabled. bail.
				if ( ! astoundify_simple_social_login_is_registration_enabled() ) {
					$this->error_redirect( 'connected_user_not_found' );
				}

				// Register user.
				$user_id = $this->insert_user( $data, $referer );
				if ( ! $user_id ) {
					$this->error_redirect( 'registration_fail' );
				}

				// Log them in.
				astoundify_simple_social_login_log_user_in( $user_id );

				// Redirect to home, if in login page.
				$this->success_redirect( urldecode( $referer ) );

				break;
			case 'link':
				if ( ! is_user_logged_in() ) {
					$this->error_redirect( 'not_logged_in', urldecode( $referer ) );
				}

				$is_connected = $this->is_user_connected( get_current_user_id() );
				if ( $is_connected ) {
					$this->error_redirect( 'already_connected', urldecode( $referer ) );
				}

				$data = $this->api_get_data();
				if ( ! $data || ! isset( $data['id'] ) ) {
					$this->error_redirect( 'api_error' );
				}

				// Get connected user ID.
				$user_id = $this->get_connected_user_id( $data['id'] );
				if ( $user_id ) {
					$this->error_redirect( 'another_already_connected', urldecode( $referer ) );
				}

				// Link user.
				$link_data = $this->get_link_data( $data );
				$link = $this->link_user( $link_data );

				if ( ! $link ) {
					$this->error_redirect( 'link_fail', urldecode( $referer ) );
				}

				$this->success_redirect( urldecode( $referer ) );

				break;
			case 'unlink':
				if ( ! is_user_logged_in() ) {
					$this->error_redirect( 'not_logged_in', urldecode( $referer ) );
				}

				$this->unlink_user( get_current_user_id() );
				$this->success_redirect( urldecode( $referer ) );

				break;
			default:
				$this->error_redirect( 'unknown_action' );
		}
	}
}
