<?php
/**
 * WooCommerce Functions.
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
 * WooCommerce My Account Link/Unlink Buttons
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_woocommerce_link_unlink_buttons() {
	if ( ! astoundify_simple_social_login_is_display_location_active( 'woocommerce' ) ) {
		return;
	}
	$providers = astoundify_simple_social_login_get_providers();
	if ( ! $providers || ! is_array( $providers ) ) {
		return '';
	}
?>
<div id="astoundify-simple-social-login-woocommerce-profile-wrap">
	<h2><?php esc_html_e( 'Connected Social Accounts', 'astoundify-simple-social-login' ); ?></h2>
	<p class="description"><?php esc_html_e( 'You can connect your account to the following social login providers:' )?></p>
	<?php echo astoundify_simple_social_login_get_link_unlink_buttons(); ?>
</div><!-- #astoundify-simple-social-login-woocommerce-profile-wrap -->
<?php
}
add_action( 'woocommerce_after_edit_account_form', 'astoundify_simple_social_login_woocommerce_link_unlink_buttons' );

/**
 * Print Login Button
 *
 *
 */
function astoundify_simple_social_login_woocommerce_login_register_buttons() {
?>
<div id="astoundify-simple-social-login-woocommerce-wrap">
	<p><?php esc_html_e( 'Use a social account for faster login or easy registration.', 'astoundify-simple-social-login' ); ?></p>
	<?php echo astoundify_simple_social_login_get_login_register_buttons(); ?>
</div><!-- #astoundify-simple-social-login-woocommerce-wrap -->
<?php
}
add_action( 'woocommerce_login_form_end', 'astoundify_simple_social_login_woocommerce_login_register_buttons' );


