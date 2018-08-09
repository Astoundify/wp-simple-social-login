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

	$class   = sprintf( 'Hybridauth\Provider\%s', ucfirst( $provider->get_id() ) );
	$adapter = new $class( $provider->get_config() );

	try {
		// Nonce is only set when leaving the site to start action.
		if ( isset( $_GET['_nonce'] ) ) {
			if ( ! wp_verify_nonce( $_GET['_nonce'], "astoundify_simple_social_login_{$provider->get_id()}" ) ) {
				throw new Exception( esc_html__( 'Invalid operation.', 'astoundify-simple-social-login' ) );
			}
		}

		switch ( $action ) {

			// Login/Register
			case 'authenticate':
				$adapter->authenticate( $provider );

				// Already logged in.
				if ( is_user_logged_in() ) {
					throw new Exception( esc_html__( 'You are already logged in to an account.', 'astoundify-simple-social-login' ) );
				}

				$provider_profile = $provider->get_profile_data( $adapter );

				// Try and find an existing user.
				$user_id = astoundify_simple_social_login_get_existing_user( $provider_profile['id'], $provider->get_id() );

				// If no account exists register one.
				if ( ! $user_id ) {
					$user_id = astoundify_simple_social_login_register_user( $provider_profile, $provider->get_id() );

					if ( ! $user_id ) {
						throw new Exception( esc_html__( 'Unable to create user account. Please contact the website administrator.', 'astoundify-simple-social-login' ) );
					}
				}

				// Log in if all is good.
				if ( $user_id ) {
					return astoundify_simple_social_login_log_user_in( $user_id, $provider->get_id() );
				}

				throw new Exception( esc_html__( 'Unable to authenticate. Please try again.', 'astoundify-simple-social-login' ) );

				break;

			// If a user is logged in they can link their account.
			case 'link':
				if ( ! is_user_logged_in() ) {
					throw new Exception( esc_html__( 'You are not logged in.', 'astoundify-simple-social-login' ) );
				}

				// Try and find an existing user.
				$user_id = astoundify_simple_social_login_get_existing_user( $provider_profile['id'], $provider->get_id() );

				if ( $user_id ) {
					throw new Exception( esc_html__( 'This social account is already linked to an existing user account.', 'astoundify-simple-social-login' ) );
				}

				$link = astoundify_simple_social_login_set_user_data( get_current_user_id(), $provider_profile, $provider->get_id() );

				if ( ! $link ) {
					throw new Exception( esc_html__( 'Unable to link account. Please try again.', 'astoundify-simple-social-login' ) );
				}

				break;

			// Remove an association.
			case 'unlink':
				if ( ! is_user_logged_in() ) {
					throw new Exception( esc_html__( 'You are not logged in.', 'astoundify-simple-social-login' ) );
				}

				astoundify_simple_social_login_unset_user_data( get_current_user_id(), $provider->get_id() );

				break;
		}
	} catch ( \Exception $e ) {
		wp_die( $e->getMessage() );
	}

	if ( isset( $_GET['_referrer'] ) ) {
		wp_safe_redirect( esc_url( $_GET['_referrer'] ) );
		exit();
	}
}
add_action( 'template_redirect', 'astoundify_simple_social_login_watch' );

/**
 * Determine if we can connect a social account to an existing WordPress user account.
 *
 * @since 1.0.0
 *
 * @param int    $provider_user_id User to search for based on social data.
 * @param string $provider Provider ID to search for.
 * @return int|false False if no existing user is found; otherwise the ID.
 */
function astoundify_simple_social_login_get_existing_user( $provider_user_id, $provider ) {
	$args = [
		'meta_key'   => "_astoundify_simple_social_login_{$provider}_id",
		'meta_value' => esc_html( $provider_user_id ),
		'number'     => -1,
		'fields'     => 'ID',
	];

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
 * @param array  $provider_data Provider APi data.
 * @param string $provider Provider ID.
 * @return int|false
 */
function astoundify_simple_social_login_register_user( $provider_data, $provider ) {
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

	astoundify_simple_social_login_set_user_data( $user_id, $provider_data, $provider );

	return $user_id;
}

/**
 * Set a user's provider meta data.
 *
 * @since 1.0.0
 *
 * @param int    $user_id User ID.
 * @param array  $provider_profile Provider profile information.
 * @param string $provider Provider ID.
 * @return bool
 */
function astoundify_simple_social_login_set_user_data( $user_id, $provider_profile, $provider ) {
	if ( astoundify_simple_social_login_get_existing_user( $provider_data['id'], $provider ) ) {
		return false;
	}

	update_user_meta( $user_id, "_astoundify_simple_social_login_{$provider}_id", esc_html( $provider_profile['id'] ) );
	update_user_meta( $user_id, "_astoundify_simple_social_login_{$provider}_timestamp", current_time( 'timestamp' ) );
	update_user_meta( $user_id, "_astoundify_simple_social_login_{$provider}_timestamp_gmt", time() );
	update_user_meta( $user_id, "_astoundify_simple_social_login_{$provider}_connected", 1 );
	update_user_meta( $user_id, '_astoundify_simple_social_login_profile_image', esc_url( $provider_profile['profile_image'] ) );

	do_action( 'astoundify_simple_social_login_set_user_data', $user_id, $provider_profile, $provider );

	return true;
}

/**
 * Unset a user's provider meta data.
 *
 * @since 1.0.0
 *
 * @param int    $user_id User ID.
 * @param string $provider Provider ID.
 */
function astoundify_simple_social_login_unset_user_data( $user_id, $provider ) {
	delete_user_meta( $user_id, "_astoundify_simple_social_login_{$provider}_id" );
	delete_user_meta( $user_id, "_astoundify_simple_social_login_{$provider}_timestamp" );
	delete_user_meta( $user_id, "_astoundify_simple_social_login_{$provider}_timestamp_gmt" );
	delete_user_meta( $user_id, "_astoundify_simple_social_login_{$provider}_connected" );
	delete_user_meta( $user_id, '_astoundify_simple_social_login_profile_image' );

	do_action( 'astoundify_simple_social_login_unset_user_data', $user_id, $provider_profile, $provider );
}

/**
 * Use the connected profile avatar URL if available.
 *
 * @since 1.0.0
 *
 * @param string $avatar Avatar HTML
 * @param mixed  $id_or_email Identifier.
 * @return string
 */
function astoundify_simple_social_login_get_avatar( $avatar, $id_or_email ) {

	if ( is_admin() ) {
		$screen = get_current_screen();

		if ( is_object( $screen ) && 'options-discussion' === $screen->id ) {
			return $avatar;
		}
	}

	$user_id = 0;

	if ( is_numeric( $id_or_email ) ) {
		$user_id = (int) $id_or_email;
	} elseif ( is_object( $id_or_email ) ) {
		if ( ! empty( $id_or_email->user_id ) ) {
			$user_id = (int) $id_or_email->user_id;
		}
	} else {
		$user = get_user_by( 'email', $id_or_email );

		if ( $user ) {
			$user_id = $user->ID;
		}
	}

	$image = get_user_meta( $user_id, '_astoundify_simple_social_login_profile_image', true );

	if ( $user_id && $image && '' !== $image ) {
		$avatar = preg_replace( "/src='(.*?)'/i", "src='" . $image . "'", $avatar );
		$avatar = preg_replace( "/srcset='(.*?)'/i", "srcset='" . $image . " 2x'", $avatar );
	}

	return $avatar;
}
add_filter( 'get_avatar', 'astoundify_simple_social_login_get_avatar', 10, 2 );

/**
 * Is user connected to a provider?
 *
 * @since 1.0.0
 *
 * @param int    $user_id User ID.
 * @param string $provider Provider ID.
 * @return bool
 */
function astoundify_simple_social_login_is_user_connected_to_provider( $user_id, $provider ) {
	return get_user_meta( $user_id, "_astoundify_simple_social_login_{$provider}_connected", true );
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
