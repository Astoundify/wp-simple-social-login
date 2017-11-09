<?php
/**
 * Template functions.
 *
 * @since 1.0.0
 *
 * @package Plugin Scaffold
 * @category Functions
 * @author Astoundify
 */

/**
 * Locate and load a template file.
 *
 * @since 1.0.0
 *
 * @param string $template_name Nam of template file.
 * @param array  $args          (default: array()) Pass data to template.
 * @param string $template_path (default: '') Load from a different area.
 * @param string $default_path  (default: '') Default path.
 */
function astoundify_wc_simple_social_login_get_template( $template_name, $args = array(), $template_path = '', $default_path = '' ) {
	// Extract variable to use in template file.
	if ( ! empty( $args ) && is_array( $args ) ) {
		extract( $args ); // WPCS: ok.
	}

	// Get template file.
	$located = astoundify_wc_simple_social_login_locate_template( $template_name, $template_path, $default_path );

	// File not exists, display error notice.
	if ( ! file_exists( $located ) ) {
		// Translators: %s Attempted template file.
		_doing_it_wrong( __FUNCTION__, esc_attr( sprintf( __( '%s does not exist.', 'astoundify-wc-simple-social-login' ), '<code>' . $located . '</code>' ), $located ), esc_attr( ASTOUNDIFY_WC_SIMPLE_SOCIAL_LOGIN_VERSION ) );
		return;
	}

	include( $located );
}

/**
 * Locate a template and return the path for inclusion.
 *
 * This is the load order:
 *
 *    yourtheme       /   $template_path   /   $template_name
 *    yourtheme       /   $template_name
 *    $default_path   /   $template_name
 *
 * @since 1.0.0
 *
 * @param string $template_name Name of template file.
 * @param string $template_path (default: '') Load from a different area.
 * @param string $default_path  (default: '') Default path.
 * @return string
 */
function astoundify_wc_simple_social_login_locate_template( $template_name, $template_path = '', $default_path = '' ) {
	// Set theme path if not set.
	if ( ! $template_path ) {
		$template_path = 'astoundify-wc-simple-social-login';
	}

	// Set default template path if not set.
	if ( ! $default_path ) {
		$default_path = ASTOUNDIFY_WC_SIMPLE_SOCIAL_LOGIN_TEMPLATE_PATH;
	}

	// Look within passed path within the theme - this is priority.
	$template = locate_template( trailingslashit( $template_path ) . $template_name );

	// Get default template if theme template file not found.
	if ( ! $template ) {
		$template = trailingslashit( $default_path ) . $template_name;
	}

	// Return what we found.
	return apply_filters( 'astoundify_wc_simple_social_login_locate_template', $template, $template_name, $template_path );
}
