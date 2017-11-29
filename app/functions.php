<?php
/**
 * Helper functions.
 *
 * @since 1.0.0
 *
 * @package Functions
 * @category Functions
 * @author Astoundify
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
	$providers = array(
		'facebook' => '\Astoundify\Simple_Social_Login\Provider_Facebook',
		'twitter'  => '\Astoundify\Simple_Social_Login\Provider_Twitter',
		'google'   => '\Astoundify\Simple_Social_Login\Provider_Google',
	);
	return apply_filters( 'astoundify_simple_social_login_providers', $providers );
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
		return array();
	}

	// Get active providers.
	$providers = array();
	foreach ( $_providers as $id => $v ) {
		$provider = astoundify_simple_social_login_get_provider( $id );
		if ( $provider && $provider->is_active() ) {
			$providers[ $id ] = $provider;
		}
	}
	return $providers;
}

/**
 * Get Provider.
 *
 * @since 1.0.0
 *
 * @param string $id Provider ID.
 * @return Astoundify\Simple_Social_Login\Provider|false
 */
function astoundify_simple_social_login_get_provider( $id ) {
	$providers = astoundify_simple_social_login_get_providers();
	return isset( $providers[ $id ] ) && class_exists( $providers[ $id ] ) ? new $providers[ $id ] : false;
}

/**
 * Get active display location.
 *
 * @since 1.0.0
 *
 * @return array
 */
function astoundify_simple_social_login_get_display_locations() {
	$options = get_option( 'astoundify_simple_social_login', array() );
	$display = isset( $options['display'] ) && is_array( $options['display'] ) ? $options['display'] : array();
	return $display;
}

/**
 * Is location selected.
 *
 * @since 1.0.0
 *
 * @param string $location Display location.
 * @return array
 */
function astoundify_simple_social_login_is_display_location_selected( $location ) {
	$locations = astoundify_simple_social_login_get_display_locations();
	return in_array( $location, $locations, true );
}

/**
 * Login/Register Buttons
 *
 * @since 1.0.0
 *
 * @return string
 */
function astoundify_simple_social_login_get_login_register_buttons() {
	$providers = astoundify_simple_social_login_get_active_providers();
	if ( ! $providers || ! is_array( $providers ) ) {
		return false;
	}

	ob_start();

	astoundify_simple_social_login_get_template(
		'login-register-buttons.php', array(
			'providers' => $providers,
		)
	);

	return apply_filters( 'astoundify_simple_social_login_login_register_buttons', ob_get_clean() );
}

/**
 * Link/Unlink Buttons.
 *
 * @since 1.0.0
 *
 * @return string
 */
function astoundify_simple_social_login_get_link_unlink_buttons() {
	$providers = astoundify_simple_social_login_get_active_providers();
	if ( ! $providers || ! is_array( $providers ) ) {
		return false;
	}

	ob_start();

	astoundify_simple_social_login_get_template(
		'link-unlink-buttons.php', array(
			'providers' => $providers,
		)
	);

	return apply_filters( 'astoundify_simple_social_login_link_unlink_buttons', ob_get_clean() );
}

/**
 * Add Query Vars
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_add_query_vars( $vars ) {
	$vars[] = 'astoundify_simple_social_login';
	return $vars;
}
add_filter( 'query_vars', 'astoundify_simple_social_login_add_query_vars', 1 );

/**
 * Register Custom Template When visiting Query Vars.
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_template_include( $template ) {
	$get = get_query_var( 'astoundify_simple_social_login' );
	if ( $get ) {
		$template = ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_TEMPLATE_PATH . 'process.php';
	}
	return $template;
}
add_filter( 'template_include', 'astoundify_simple_social_login_template_include' );

/**
 * Done Process. Endpoint For HybridAuth.
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_process_done() {
	// Bail if no active provider.
	$providers = astoundify_simple_social_login_get_active_providers();
	if ( ! $providers || ! is_array( $providers ) ) {
		wp_redirect( home_url() );
		exit;
	}

	// Load HybridAuth Library.
	require_once( ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . 'vendor/hybridauth/hybridauth/hybridauth/Hybrid/Auth.php' );

	// Process social account data.
	try {
		\Hybrid_Endpoint::process();
	} catch ( \Exception $e ) {
		wp_die();
		exit;
	}
	wp_die();
	exit;
}
add_action( 'astoundify_simple_social_login_process_done', 'astoundify_simple_social_login_process_done' );

/**
 * Action Process
 *
 * @since 1.0.0
 *
 * @param string $provider Login provider.
 * @param string $action   Request action.
 * @param string $referer  URL.
 */
function astoundify_simple_social_login_process( $provider, $action, $referer ) {
	// Bail if invalid or not active.
	$provider = astoundify_simple_social_login_get_provider( $provider );
	if ( ! $provider || ! $provider->is_active() ) {
		wp_safe_redirect( esc_url_raw( urldecode( $referer ) ) );
		exit;
	}

	// Process action.
	$provider->process_action( $action, $referer );
}
add_action( 'astoundify_simple_social_login_process', 'astoundify_simple_social_login_process', 10, 3 );

/**
 * Log User In.
 *
 * @since 1.0.0
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

	// User logged in, but no email.
	if ( is_user_logged_in() ) {
		$user = wp_get_current_user();
		if ( ! $user->user_email ) {
			wp_safe_redirect( esc_url_raw( astoundify_simple_social_login_get_setup_profile_url() ) );
			exit;
		}
	}
}

/**
 * Setup Profile URL
 * In case cannot capture email from social account. User will need to setup email in their user account.
 * As default it will use WordPress admin profile edit page.
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_get_setup_profile_url() {
	return apply_filters( 'astoundify_simple_social_login_setup_profile_url', admin_url( 'profile.php' ) );
}

/**
 * Is registration enabled.
 *
 * @since 1.0.0
 *
 * @return bool
 */
function astoundify_simple_social_login_is_registration_enabled() {
	$options = get_option( 'astoundify_simple_social_login', array() );
	$enable  = isset( $options['users_can_register'] ) && $options['users_can_register'] ? true : false;
	return apply_filters( 'astoundify_simple_social_login_registration_enabled', $enable );
}

/**
 * Date Format.
 *
 * @since 1.0.0
 *
 * @return string.
 */
function astoundify_simple_social_login_get_date_format() {
	return apply_filters( 'astoundify_simple_social_login_date_format', get_option( 'date_format' ) );
}

/**
 * Time Format.
 *
 * @since 1.0.0
 *
 * @return string.
 */
function astoundify_simple_social_login_get_time_format() {
	return apply_filters( 'astoundify_simple_social_login_time_format', get_option( 'time_format' ) );
}

/**
 * Is wp-login.php Page.
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
 * Utility. WordPress do not have conditional for this.
 *
 * @since 1.0.0
 *
 * @return bool
 */
function astoundify_simple_social_login_is_wp_register_page() {
	if ( isset( $GLOBALS['pagenow'], $_REQUEST['action'] ) && 'wp-login.php' === $GLOBALS['pagenow'] && 'register' === $_REQUEST['action'] ) {
		return true;
	}
	return false;
}

/**
 * Enqueu styles depending on page.
 *
 * @since 1.0.0
 *
 * @param string $page Optional page for specific styles in debug.
 */
function astoundify_simple_social_login_enqueue_styles( $page = false ) {
	$debug   = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? true : false;
	$version = $debug ? time() : ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_VERSION;

	if ( $debug ) {
		// If in debug load button base separately.
		wp_enqueue_style( 'astoundify-simple-social-login-buttons', ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_URL . 'resources/assets/css/buttons.css', array(), $version );

		// Load supplemental styles if needed.
		if ( $page ) {
			wp_enqueue_style( 'astoundify-simple-social-login', ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_URL . 'resources/assets/css/' . $page . '.css', array(), $version );
		}
	} else {
		wp_enqueue_style( 'astoundify-simple-social-login', ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_URL . 'public/css/wp-simple-social-login.min.css', array(), $version );
	}
}

/**
 * Get SVG
 *
 * @since 1.0.0
 *
 * @param string $icon Icon name.
 */
function astoundify_simple_social_login_get_svg( $icon ) {
	$debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? true : false;
	$file  = ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . "public/images/{$icon}.svg";

	if ( $debug ) {
		$file = ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_PATH . "resources/assets/images/{$icon}.svg";
	}

	$file = apply_filters( 'astoundify_simple_social_login_svg', $file, $icon );

	if ( file_exists( $file ) ) {
		ob_start();
?>

<span class="astoundify-simple-social-login-icon"><?php require( $file ); ?></span>

<?php
		return ob_get_clean();
	}
}
