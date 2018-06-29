<?php
/**
 * Helper functions.
 *
 * @since 1.0.0
 *
 * @package  Functions
 * @category Functions
 * @author   Astoundify
 */

// Do not access this file directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get Providers.
 *
 * @since 1.0.0
 *
 * @return array
 */
function astoundify_simple_social_login_get_providers() {
	$providers = [
		'facebook' => '\Astoundify\Simple_Social_Login\Provider_Facebook',
		'twitter'  => '\Astoundify\Simple_Social_Login\Provider_Twitter',
		'google'   => '\Astoundify\Simple_Social_Login\Provider_Google',
	];
	return apply_filters( 'astoundify_simple_social_login_providers', $providers );
}

/**
 * Get Provider.
 *
 * @since 1.0.0
 *
 * @param  string $id Provider ID.
 * @return Astoundify\Simple_Social_Login\Provider|false
 */
function astoundify_simple_social_login_get_provider( $id ) {
	$providers = astoundify_simple_social_login_get_providers();

	return isset( $providers[ $id ] ) && class_exists( $providers[ $id ] ) ? new $providers[ $id ]() : false;
}

/**
 * Get Active Providers
 *
 * @since 1.0.0
 *
 * @return array
 */
function astoundify_simple_social_login_get_active_providers() {
	$_providers = astoundify_simple_social_login_get_providers();

	if ( ! $_providers || ! is_array( $_providers ) ) {
		return [];
	}

	// Get active providers.
	$providers = [];

	foreach ( $_providers as $id => $v ) {
		$provider = astoundify_simple_social_login_get_provider( $id );

		if ( $provider && $provider->is_active() ) {
			$providers[ $id ] = $provider;
		}
	}

	return $providers;
}

/**
 * Watch for provider actions.
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_watch() {
	$action   = isset( $_GET['astoundify_simple_social_login'] ) ? $_GET['astoundify_simple_social_login'] : false;
	$provider = isset( $_GET['provider'] ) ? $_GET['provider'] : false;

	if ( ! $action || ! $provider ) {
		return;
	}

	$provider = astoundify_simple_social_login_get_provider( $provider );

	$class    = sprintf( 'Hybridauth\Provider\%s', ucfirst( $provider->id ) );
	$adapter  = new $class( $provider->get_config() );

	try {
		switch ( $action ) {
			case 'authenticate':
				$adapter->authenticate( $provider );

				// Already logged in.
				if ( is_user_logged_in() ) {
					throw new Exception( esc_html__( 'You are already logged in to an account.', 'astoundify-simple-social-login' ) );
				}

				$provider_profile = $provider->get_profile_data( $adapter );
				$user_id          = astoundify_simple_social_login_get_existing_user( $provider_profile['id'], $provider->id );

				// If no account exists register one.
				if ( ! $user_id ) {
					$user_id = astoundify_simple_social_login_register_user( $provider_profile, $provider->id );
				}

				// Log in if all is good.
				if ( $user_id ) {
					return astoundify_simple_social_login_log_user_in( $user_id, $provider->id );
				}

				throw new Exception( esc_html__( 'Unable to authenticate. Please try again', 'astoundify-simple-social-login' ) );

				break;
			case 'process':
				var_dump( 'wat' );
				break;
		}
	} catch ( \Exception $e ) {
		wp_die( $e->getMessage() );
	}
}
add_action( 'template_redirect', 'astoundify_simple_social_login_watch' );

/**
 * Determine if we can connect a social account to an existing WordPress user account.
 *
 * @since 1.0.0
 *
 * @param int $provider_user_id User to search for based on social data.
 * @param string $provider Provider ID to search for.
 * @return int|false False if no existing user is found; otherwise the ID.
 */
function astoundify_simple_social_login_get_existing_user( $provider_user_id, $provider ) {
	$args  = array(
		'meta_key'   => "_astoundify_simple_social_login_{$provider}_id",
		'meta_value' => esc_html( $provider_user_id ),
		'number'     => -1,
		'fields'     => 'ID',
	);

	$users = get_users( $args );

	// If user found, return it.
	if ( ! $users ) {
		return false;
	}

	if ( 1 === count( $users ) ) {
		return intval( $users[0] );
	} else {
		// More than one users connected to the same account ? Maybe notify admin.
		return false;
	}

	return false;
}

/**
 * Authenticate a specific user.
 *
 * @since 1.0.0
 *
 * @param int $user_id User to authenticate.
 */
function astoundify_simple_social_login_log_user_in( $user_id ) {
	// Bail if user already logged in.
	if ( is_user_logged_in() ) {
		return false;
	}

	// Get user data.
	$user       = get_userdata( $user_id );
	$user_login = $user->user_login;

	// Enable remember me cookie.
	$remember_me = apply_filters( 'astoundify_simple_social_login_remember_me', true, $user_id );

	wp_set_auth_cookie( $user_id, $remember_me );
	wp_set_current_user( $user_id, $user_login );

	do_action( 'wp_login', $user_login, $user );

	// User logged in, but no email -- let things happen here (currently nothing).
	if ( is_user_logged_in() ) {
		$user = wp_get_current_user();

		if ( ! $user->user_email ) {
			do_action( 'astoundify_simple_social_login_incomplete_user_account', $user );
		}
	}
}

/**
 * Register a user based on provider data.
 *
 * @since 1.0.0
 *
 * @param array $provider_data Provider APi data.
 * @param string $provider Provider ID.
 * @return int|false
 */
function astoundify_simple_social_login_register_user( $provider_data, $provider ) {
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

	$provider_data = wp_parse_args( $provider_data, $defaults );

	// Bail if missing some data.
	if ( ! $provider_data['id'] || ! $provider_data['display_name'] || ! $provider_data['user_email'] ) {
		return false;
	}

	// User Login.
	$provider_data['user_login'] = sanitize_title( $provider_data['display_name'] );

	if ( username_exists( $provider_data['user_login'] ) ) {
		$provider_data['user_login'] = $provider_data['user_login'] . '_' . time();
	}

	// Email.
	if ( email_exists( $provider_data['user_email'] ) ) {
		return false;
	}

	$inserted = wp_insert_user( $provider_data );
	$user_id  = $inserted && is_wp_error( $inserted ) ? false : intval( $inserted );

	if ( ! $user_id ) {
		return false;
	}

	// Success. Add user meta.
	update_user_meta( $user_id, "_astoundify_simple_social_login_{$provider}_id", esc_html( $provider_data['id'] ) );
	update_user_meta( $user_id, "_astoundify_simple_social_login_{$provider}_timestamp", current_time( 'timestamp' ) );
	update_user_meta( $user_id, "_astoundify_simple_social_login_{$provider}_timestamp_gmt", time() );
	update_user_meta( $user_id, "_astoundify_simple_social_login_{$provider}_connected", 1 );

	// Unset defaults provider_data, save extra provider_datas as user meta.
	foreach ( $defaults as $k => $v ) {
		unset( $provider_data[ $k ] );
	}

	foreach ( $provider_data as $k => $v ) {
		update_user_meta( $user_id, "_astoundify_simple_social_login_{$provider}_{$k}", $v );
	}

	return $user_id;
}

/**
 * Is wp-login.php Page.
 *
 * Utility. WordPress do not have conditional for this.
 *
 * @since 1.0.0
 *
 * @return bool
 */
function astoundify_simple_social_login_is_wp_login_page() {
	// Always false if register page.
	if ( astoundify_simple_social_login_is_wp_register_page() ) {
		return false;
	}

	if ( isset( $GLOBALS['pagenow'] ) && 'wp-login.php' === $GLOBALS['pagenow'] ) {
		return true;
	}

	return false;
}

/**
 * Is wp-login.php Register Page.
 *
 * Utility. WordPress do not have conditional for this.
 *
 * @since 1.0.0
 *
 * @return bool
 */
function astoundify_simple_social_login_is_wp_register_page() {
	return ( isset( $GLOBALS['pagenow'], $_REQUEST['action'] ) && 'wp-login.php' === $GLOBALS['pagenow'] && 'register' === $_REQUEST['action'] );
}
