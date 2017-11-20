<?php
/**
 * WordPress Functions.
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
 * Add Button to wp-login.php Login Form.
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_wordpress_login_form() {
	if ( ! astoundify_simple_social_login_is_display_location_active( 'wp_login' ) && astoundify_simple_social_login_is_wp_login_page() ) {
		return;
	}
	$providers = astoundify_simple_social_login_get_providers();
	if ( ! $providers || ! is_array( $providers ) ) {
		return '';
	}
	?>

	<div class="astoundify-simple-social-login-wordpress-wrap">
		<?php echo astoundify_simple_social_login_get_login_register_buttons(); ?>
		<p class="login-or"><span><?php _e( 'Or', 'astoundify-simple-social-login' ); ?></span></p>
		<p class="login-with-username"><a href="#"><?php _e( 'Log in with username and password', 'astoundify-simple-social-login' ); ?></a></p>
	</div>

	<?php

	// Add "back to social login" link in login footer.
	add_action( 'login_footer', function() {
		?>
		<p id="astoundify-simple-social-login-wordpress-back">
			<a style="display:none;" href="#"><?php _e( 'Login with social account?', 'astoundify-simple-social-login' );?></a>
		</p>
		<?php
	} );
	
}
add_action( 'login_form', 'astoundify_simple_social_login_wordpress_login_form' );

/**
 * Scritps in WordPress Login Page.
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_wordpress_scripts() {
	if ( ! astoundify_simple_social_login_is_display_location_active( 'wp_login' ) && astoundify_simple_social_login_is_wp_login_page() ) {
		return;
	}
	$providers = astoundify_simple_social_login_get_providers();
	if ( ! $providers || ! is_array( $providers ) ) {
		return '';
	}

	// Script Vars.
	$debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? true : false;
	$version = $debug ? time() : ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_VERSION;

	// CSS.
	$url = ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_URL . 'public/assets/css/wp-login.min.css';
	if ( $debug ) {
		$url = ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_URL . 'resources/assets/css/wp-login.css';
	}
	wp_enqueue_style( 'astoundify-simple-social-login-wordpress', $url, array(), $version );

	// JS.
	$url = ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_URL . 'public/assets/js/wp-login.min.js';
	if ( $debug ) {
		$url = ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_URL . 'resources/assets/js/wp-login.js';
	}
	wp_enqueue_script( 'astoundify-simple-social-login-wordpress', $url, array( 'jquery' ), $version );
}
add_action( 'login_enqueue_scripts', 'astoundify_simple_social_login_wordpress_scripts' );


/**
 * Add Link/Unlink in wp-admin Your Profile Page.
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_wordpress_profile() {
	if ( ! astoundify_simple_social_login_is_display_location_active( 'wp_login' ) ) {
		return;
	}
	$providers = astoundify_simple_social_login_get_providers();
	if ( ! $providers || ! is_array( $providers ) ) {
		return '';
	}
	?>
	<h2><?php esc_html_e( 'Connected Social Accounts', 'astoundify-simple-social-login' ); ?></h2>

	<table class="form-table">
		<tbody>
			<tr>
				<th>
					<label><?php esc_html_e( 'Social Profiles', 'astoundify-simple-social-login' ); ?></label>
				</th>
				<td>
					<p class="description"><?php esc_html_e( 'You can connect your account to the following social login providers:' )?></p>
					<?php echo astoundify_simple_social_login_get_link_unlink_buttons(); ?>
				</td>
			</tr>
		</tbody>
	</table>
	<?php
}
add_action( 'show_user_profile', 'astoundify_simple_social_login_wordpress_profile', 20 );

/**
 * Connected User Not Found.
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_wordpress_add_errors( $errors, $redirect_to ) {
	if ( isset( $_GET['_error_code'] ) ) {
		if ( 'connected_user_not_found' === $_GET['_error_code'] ) {
			$errors->add( 'connected_user_not_found', esc_html__( 'Cannot find user with your social account.', 'astoundify-simple-social-login' ), 'error' );
		}
	}
	return $errors;
}
add_filter( 'wp_login_errors', 'astoundify_simple_social_login_wordpress_add_errors', 10, 2 );
