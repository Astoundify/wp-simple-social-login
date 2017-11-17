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
 * Is location display active.
 *
 * @since 1.0.0
 *
 * @param string $display_location Display location.
 * @return array
 */
function astoundify_simple_social_login_is_display_location_active( $display_location ) {
	$locations = astoundify_simple_social_login_get_display_locations();
	return in_array( $display_location, $locations, true );
}

/**
 * Get active providers.
 *
 * @since 1.0.0
 *
 * @return array
 */
function astoundify_simple_social_login_get_providers() {
	$option = get_option( 'astoundify_simple_social_login', array() );
	$providers = isset( $option['providers'] ) && is_array( $option['providers'] ) ? $option['providers'] : array();
	return $providers;
}

/**
 * Is Provider Active.
 *
 * @since 1.0.0
 *
 * @param string $provider The provider ID.
 * @return bool
 */
function astoundify_simple_social_login_is_provider_active( $provider ) {
	$providers = astoundify_simple_social_login_get_providers();
	return apply_filters( "astoundify_simple_social_login_is_{$provider}_active", in_array( $provider, $providers ) );
}

/**
 * Login/Register Buttons
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_get_login_register_buttons() {
	$providers = astoundify_simple_social_login_get_providers();
	if ( ! $providers || ! is_array( $providers ) ) {
		return '';
	}
	ob_start();
	?>

	<ul class="astoundify-simple-social-login-buttons" style="list-style:none;">
		<?php foreach ( $providers as $provider ) : ?>
			<li><?php do_action( "astoundify_simple_social_login_{$provider}_login_register_button" ); ?>
		<?php endforeach; ?>
	</ul>

	<?php
	return apply_filters( 'astoundify_simple_social_login_login_register_buttons', ob_get_clean() );
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
	$user        = get_userdata( $user_id );
	$user_login  = $user->user_login;

	// Enable remember me cookie.
	$remember_me = apply_filters( 'astoundify_simple_social_login_remember_me', true, $user_id );

	wp_set_auth_cookie( $user_id, $remember_me );
	wp_set_current_user( $user_id, $user_login );
	do_action( 'wp_login', $user_login, $user );
}

/**
 * Get Redirect URL.
 *
 * @since 1.0.0
 *
 * @param string $action Action: login, link, etc.
 * @return string
 */
function astoundify_simple_social_login_get_redirect_url( $action = 'login' ) {
	$url = is_singular() ? get_permalink( get_queried_object() ) : home_url();
	return esc_url_raw( apply_filters( 'astoundify_simple_social_login_redirect_url', $url, $action ) );
}

/**
 * Is registration enabled.
 *
 * @since 1.0.0
 *
 * @return bool
 */
function astoundify_simple_social_login_is_registration_enabled() {
	return apply_filters( 'astoundify_simple_social_login_registration_enabled', true );
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
 * Get Last Connected Time Info.
 *
 * @since 1.0.0
 *
 * @param int|null $user_id User ID.
 * @param string $provider Social login provider.
 * @return string
 */
function astoundify_simple_social_login_get_last_connected_time_text( $user_id = null, $provider ) {
	$user = null !== $user_id ? get_userdata( intval( $user_id ) ) : wp_get_current_user();

	// Bail if user not set.
	if ( ! $user ) {
		return '';
	}

	$time = '';
	$timestamp = get_user_meta( $user->ID, "_astoundify_simple_social_login_{$provider}_timestamp", true );
	if ( $timestamp ) {
		$time = sprintf( esc_html__( 'Last connected: %1$s @ %2$s', 'astoundify-simple-social-login' ), date_i18n( astoundify_simple_social_login_get_date_format(), $timestamp ), date_i18n( astoundify_simple_social_login_get_time_format(), $timestamp ) );
	}
	return apply_filters( 'astoundify_simple_social_login_last_connected_time_text', $time, $user, $provider );
}

/**
 * Add Error
 *
 * @since 1.0.0
 *
 * @param string $id Error ID.
 * @param string $error Error Message.
 */
function astoundify_simple_social_login_add_error( $id, $error ) {
	// Set if not yet set.
	global $_astoundify_simple_social_login_error;
	if ( ! isset( $_astoundify_simple_social_login_error ) || ! is_array( $_astoundify_simple_social_login_error ) ) {
		$_astoundify_simple_social_login_error = array();
	}

	// Add error.
	$_astoundify_simple_social_login_error[ $id ] = $error;
}

/**
 * Get Errors
 *
 * @since 1.0.0
 *
 * @return array
 */
function astoundify_simple_social_login_get_errors() {
	// Set if not yet set.
	global $_astoundify_simple_social_login_error;
	if ( ! isset( $_astoundify_simple_social_login_error ) || ! is_array( $_astoundify_simple_social_login_error ) ) {
		$_astoundify_simple_social_login_error = array();
	}

	return $_astoundify_simple_social_login_error;
}

/**
 * Is wp-login.php Page.
 * WordPress do not have conditional for this.
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
 * WordPress do not have conditional for this.
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







