<?php
/**
 * Facebook WordPress.
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
	echo astoundify_simple_social_login_get_login_register_buttons();
}
add_action( 'login_form', 'astoundify_simple_social_login_wordpress_login_form' );

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










































