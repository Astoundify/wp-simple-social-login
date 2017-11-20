<?php
/**
 * WordPress Integrations Functions.
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
	if ( ! astoundify_simple_social_login_is_display_location_selected( 'wp_login' ) && astoundify_simple_social_login_is_wp_login_page() ) {
		return;
	}
	$buttons = astoundify_simple_social_login_get_login_register_buttons();
	if ( ! $buttons ) {
		return;
	}
?>

<div id="astoundify-simple-social-login-wordpress-wrap">
	<?php echo $buttons; ?>
	<p class="login-or"><span><?php _e( 'Or', 'astoundify-simple-social-login' ); ?></span></p>
	<p class="login-with-username"><a href="#"><?php _e( 'Log in with username and password', 'astoundify-simple-social-login' ); ?></a></p>
</div><!-- #astoundify-simple-social-login-wordpress-wrap -->

<?php

	// Add "back to social login" link in login footer.
	add_action( 'login_footer', function() {
	?>
	<p id="astoundify-simple-social-login-wordpress-back">
		<a style="display:none;" href="#"><?php _e( 'Login with social account?', 'astoundify-simple-social-login' );?></a>
	</p><!-- #astoundify-simple-social-login-wordpress-back -->
	<?php
	} );
}
add_action( 'login_form', 'astoundify_simple_social_login_wordpress_login_form' );

/**
 * Add Link/Unlink in wp-admin Your Profile Page.
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_wordpress_profile() {
	if ( ! astoundify_simple_social_login_is_display_location_selected( 'wp_login' ) ) {
		return;
	}
	$providers = astoundify_simple_social_login_get_active_providers();
	if ( ! $providers || ! is_array( $providers ) ) {
		return;
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
 * WP Login Errors.
 *
 * @since 1.0.0
 *
 * @param WP_Error $errors      Errors.
 * @param string   $redirect_to Redirect URL.
 * @return array
 */
function astoundify_simple_social_login_wordpress_login_add_errors( $errors, $redirect_to ) {
	// Bail if not active.
	if ( ! astoundify_simple_social_login_is_display_location_selected( 'wp_login' ) ) {
		return $errors;
	}
	$providers = astoundify_simple_social_login_get_active_providers();
	if ( ! $providers || ! is_array( $providers ) ) {
		return $errors;
	}

	if ( isset( $_GET['_error'], $_GET['_provider'] ) ) {
		$provider = astoundify_simple_social_login_get_provider( $_GET['_provider'] );
		if ( $provider ) {
			$errors->add( $_GET['_error'], $provider->get_error( $_GET['_error'] ), 'error' );
		}
	}
	return $errors;
}
add_filter( 'wp_login_errors', 'astoundify_simple_social_login_wordpress_login_add_errors', 10, 2 );

/**
 * Print WP-Admin Error Notices
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_wordpress_admin_add_error_notices() {
	// Bail if not active.
	if ( ! astoundify_simple_social_login_is_display_location_selected( 'wp_login' ) ) {
		return;
	}
	$providers = astoundify_simple_social_login_get_active_providers();
	if ( ! $providers || ! is_array( $providers ) ) {
		return;
	}

	if ( isset( $_GET['_error'], $_GET['_provider'] ) ) {
		$provider = astoundify_simple_social_login_get_provider( $_GET['_provider'] );
		if ( $provider ) {
		?>

		<div class="notice notice-error">
			<p><?php echo $provider->get_error( $_GET['_error'] ); ?></p>
		</div>

		<?php
		}
	}
}
add_action( 'admin_notices', 'astoundify_simple_social_login_wordpress_admin_add_error_notices' );

/**
 * Scritps in WordPress Login Page.
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_wordpress_scripts() {
	if ( ! astoundify_simple_social_login_is_display_location_selected( 'wp_login' ) && astoundify_simple_social_login_is_wp_login_page() ) {
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
