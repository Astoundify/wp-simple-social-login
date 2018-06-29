<?php

/**
 * Get active display location.
 *
 * @since 1.0.0
 *
 * @return array
 */
function astoundify_simple_social_login_get_display_locations() {
	$options = get_option( 'astoundify_simple_social_login', [] );
	$display = isset( $options['display'] ) && is_array( $options['display'] ) ? $options['display'] : [];

	return $display;
}

/**
 * Is location selected.
 *
 * @since 1.0.0
 *
 * @param  string $location Display location.
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
		'login-register-buttons.php', [
			'providers' => $providers,
		]
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
		'link-unlink-buttons.php', [
			'providers' => $providers,
		]
	);

	return apply_filters( 'astoundify_simple_social_login_link_unlink_buttons', ob_get_clean() );
}

/**
 * Enqueue styles depending on page.
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
		wp_enqueue_style( 'astoundify-simple-social-login-buttons', ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_URL . 'resources/assets/css/buttons.css', [], $version );

		// Load supplemental styles if needed.
		if ( $page ) {
			wp_enqueue_style( 'astoundify-simple-social-login', ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_URL . 'resources/assets/css/' . $page . '.css', [], $version );
		}
	} else {
		wp_enqueue_style( 'astoundify-simple-social-login', ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_URL . 'public/css/wp-simple-social-login.min.css', [], $version );
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

   <span class="astoundify-simple-social-login-icon"><?php include $file; ?></span>

<?php
		return ob_get_clean();
	}
}
