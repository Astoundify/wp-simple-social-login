<?php
/**
 * Social Login WooCommerce Settings Functions.
 *
 * @since 1.0.0
 *
 * @package Admin
 * @category Functions
 * @author Astoundify
 */

// Do not access this file directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add Display Choices
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_admin_woocommerce_add_display_choice( $choices ) {
	$choices['woocommerce'] = esc_html( 'WooCommerce', 'astoundify-simple-social-login' );
	return $choices;
}
add_action( 'astoundify_simple_social_login_display_choices', 'astoundify_simple_social_login_admin_woocommerce_add_display_choice' );
