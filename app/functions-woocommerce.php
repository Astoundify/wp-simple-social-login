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
 * Change Login Error Redirect To MyAccount Page.
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_woocommerce_login_register_error_redirect_url( $url, $error_code, $redirect_url, $provider ) {
	// Only if not forced.
	if ( ! $redirect_url ) {
		if ( is_checkout() ) {
			$url = wc_get_checkout_url();
		} else {
			$url = wc_get_page_permalink( 'myaccount' );
		}
	}
	return $url;
}
apply_filters( 'astoundify_simple_social_login_error_redirect_url', 'astoundify_simple_social_login_woocommerce_login_register_error_redirect_url', 10, 4 );

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
</div><!-- #astoundify-simple-social-login-woocommerce-wrap -->
<?php
}
add_action( 'woocommerce_login_form_end', 'astoundify_simple_social_login_woocommerce_login_register_buttons' );


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
	<h2><?php esc_html_e( 'Connected Social Accounts', 'astoundify-simple-social-login' ); ?></h2>
	<p class="description"><?php esc_html_e( 'You can connect your account to the following social login providers:', 'astoundify-simple-social-login' )?></p>
	<?php echo $buttons; ?>
</div><!-- #astoundify-simple-social-login-woocommerce-profile-wrap -->
<?php
}
add_action( 'woocommerce_after_edit_account_form', 'astoundify_simple_social_login_woocommerce_link_unlink_buttons' );

/**
 * Add Error Notice for WooCommerce
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_woocommerce_add_error_notice() {
	// Bail if not active.
	if ( ! astoundify_simple_social_login_is_display_location_selected( 'wp_login' ) ) {
		return;
	}
	$providers = astoundify_simple_social_login_get_active_providers();
	if ( ! $providers || ! is_array( $providers ) ) {
		return;
	}

	// Display error
	if ( isset( $_GET['_error'], $_GET['_provider'] ) ) {
		$provider = astoundify_simple_social_login_get_provider( $_GET['_provider'] );
		if ( $provider ) {
			wc_add_notice( $provider->get_error( $_GET['_error'] ), 'error' );
		}
	}

	// Display error if user do not have email in their account.
	if ( is_user_logged_in() ) {
		$user = wp_get_current_user();
		if ( ! $user->user_email ) {

			// If is not edit account page, redirect.
			if ( ! is_edit_account_page() ) {
				wp_safe_redirect( esc_url_raw( wc_customer_edit_account_url() ) );
				exit;
			} else { // Edit account page, add notice.
				wc_add_notice( esc_html__( 'Please setup your email to activate your account.', 'astoundify-simple-social-login' ) . ' <a href="' . esc_url( wc_customer_edit_account_url() ) . '#account_email">' . esc_html__( 'Add Email', 'astoundify-simple-social-login' ) . '</a>', 'error' );
			}

		}
	}
}
add_action( 'template_redirect', 'astoundify_simple_social_login_woocommerce_add_error_notice' );

/**
 * Change Setup Profile/Account Page to use WooCommerce My Account Edit Profile Page.
 *
 * @since 1.0.0
 *
 * @param string $url Profile URL.
 * @return string
 */
function astoundidy_simple_social_login_woocommerce_setup_profile_url( $url ) {
	return wc_customer_edit_account_url();
}
add_filter( 'astoundify_simple_social_login_setup_profile_url', 'astoundidy_simple_social_login_woocommerce_setup_profile_url' );

/**
 * Disable Redirect to WP Admin Profile Edit to Setup Email
 *
 * @since 1.0.0
 *
 * @return false
 */
add_filter( 'astoundify_simple_social_login_wordpress_admin_email_setup_redirect', '__return_false' );

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

	// Script Vars.
	$debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? true : false;
	$version = $debug ? time() : ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_VERSION;

	// CSS.
	$url = ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_URL . 'public/css/woocommerce.min.css';
	if ( $debug ) {
		$url = ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_URL . 'resources/assets/css/woocommerce.css';
	}
	wp_enqueue_style( 'astoundify-simple-social-login-woocommerce', $url, array(), $version );
}
add_action( 'wp_enqueue_scripts', 'astoundify_simple_social_login_woocommerce_scripts' );
