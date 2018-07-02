<?php
/**
 * WooCommerce Functions.
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
 * Print Login Button in Login Form.
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_woocommerce_login_register_buttons() {
	if ( ! astoundify_simple_social_login_is_display_location_selected( 'woocommerce' ) ) {
		return;
	}

	$buttons = astoundify_simple_social_login_get_login_register_buttons();

	if ( ! $buttons ) {
		return;
	}
?>

<div id="astoundify-simple-social-login-woocommerce-wrap">
	<p><?php esc_html_e( 'Use a social account for faster login or easy registration.', 'astoundify-simple-social-login' ); ?></p>

	<?php echo $buttons; ?>

	<p class="login-or"><span><?php _e( 'Or', 'astoundify-simple-social-login' ); ?></span></p>
</div><!-- #astoundify-simple-social-login-woocommerce-wrap -->

<?php
}
add_action( 'woocommerce_login_form_start', 'astoundify_simple_social_login_woocommerce_login_register_buttons' );


/**
 * WooCommerce My Account Link/Unlink Buttons
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_woocommerce_link_unlink_buttons() {
	if ( ! astoundify_simple_social_login_is_display_location_selected( 'woocommerce' ) ) {
		return;
	}

	$buttons = astoundify_simple_social_login_get_link_unlink_buttons();

	if ( ! $buttons ) {
		return;
	}
?>

<div id="astoundify-simple-social-login-woocommerce-profile-wrap">
	<h2><?php esc_html_e( 'Social Accounts', 'astoundify-simple-social-login' ); ?></h2>

	<?php echo $buttons; ?>
</div><!-- #astoundify-simple-social-login-woocommerce-profile-wrap -->

<?php
}
add_action( 'woocommerce_after_edit_account_form', 'astoundify_simple_social_login_woocommerce_link_unlink_buttons' );

/**
 * Scritps for WooCommerce Pages.
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_woocommerce_scripts() {
	if ( ! astoundify_simple_social_login_is_display_location_selected( 'woocommerce' ) ) {
		return;
	}

	$providers = astoundify_simple_social_login_get_active_providers();

	if ( ! $providers || ! is_array( $providers ) ) {
		return;
	}

	astoundify_simple_social_login_enqueue_styles( 'woocommerce' );
}
add_action( 'wp_enqueue_scripts', 'astoundify_simple_social_login_woocommerce_scripts' );
