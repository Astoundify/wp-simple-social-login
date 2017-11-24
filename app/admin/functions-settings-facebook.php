<?php
/**
 * Social Login Facebook Settings Functions.
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
 * Register Facebook Settings
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_facebook_register_settings() {
	register_setting(
		$option_group      = 'astoundify_simple_social_login',
		$option_name       = 'astoundify_simple_social_login_facebook',
		$sanitize_callback = 'astoundify_simple_social_login_facebook_sanitize_settings'
	);
}
add_action( 'admin_init', 'astoundify_simple_social_login_facebook_register_settings' );

/**
 * Sanitize Facebook Settings
 *
 * @since 1.0.0
 *
 * @param mixed $input Data input.
 * @return array
 */
function astoundify_simple_social_login_facebook_sanitize_settings( $input ) {
	$output = array();

	$output['app_id'] = isset( $input['app_id'] ) ? esc_attr( trim( $input['app_id'] ) ) : '';

	$output['app_secret'] = isset( $input['app_secret'] ) ? esc_attr( trim( $input['app_secret'] ) ) : '';

	$output['login_button_text'] = isset( $input['login_button_text'] ) ? esc_attr( $input['login_button_text'] ) : '';

	$output['link_button_text'] = isset( $input['link_button_text'] ) ? esc_attr( $input['link_button_text'] ) : '';

	$output['connected_info'] = isset( $input['connected_info'] ) ? esc_attr( $input['connected_info'] ) : '';

	return apply_filters( 'astoundify_simple_social_login_facebook_sanitize_settings', $output );
}

/**
 * Add Facebook Settings Tab
 *
 * @since 1.0.0
 *
 * @param array $settings Settings Section.
 * @return array
 */
function astoundify_simple_social_login_facebook_add_settings_tab( $settings ) {
	$settings['facebook'] = esc_html( 'Facebook', 'astoundify-simple-social-login' );
	return $settings;
}
add_filter( 'astoundify_simple_social_login_settings_tabs', 'astoundify_simple_social_login_facebook_add_settings_tab' );

/**
 * Facebook Settings Panel
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_panel_facebook() {
	$options = get_option( 'astoundify_simple_social_login_facebook', array() );
	$options = astoundify_simple_social_login_facebook_sanitize_settings( $options );
?>

<p><?php esc_html_e( 'Need help setting up and configuring Facebook social login?', 'astoundify-simple-social-login' ); ?> <a href="https://astoundify.com/go/simple-social-login-fb-setup/" target="_blank"><?php esc_html_e( 'Read the docs', 'astoundify-simple-social-login' ); ?></a>.</p>

<table class="form-table">
	<tbody>
		<tr>
			<th scope="row"><label for="facebook-app-id"><?php esc_html_e( 'App ID', 'astoundify-simple-social-login' ); ?></label></th>
			<td>
				<input id="facebook-app-id" type="text" class="regular-text" name="astoundify_simple_social_login_facebook[app_id]" value="<?php echo esc_attr( $options['app_id'] ); ?>">
				<p class="description"><?php esc_html_e( 'Your app ID.', 'astoundify-simple-social-login' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="facebook-app-secret"><?php esc_html_e( 'App Secret', 'astoundify-simple-social-login' ); ?></label></th>
			<td>
				<input id="facebook-app-secret" type="<?php echo esc_attr( defined( 'WP_DEBUG' ) && WP_DEBUG ? 'text' : 'password' ); ?>" class="regular-text" name="astoundify_simple_social_login_facebook[app_secret]" value="<?php echo esc_attr( $options['app_secret'] ); ?>">
				<p class="description"><?php esc_html_e( 'Your app secret.', 'astoundify-simple-social-login' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="facebook-login-button-text"><?php esc_html_e( 'Login Button Text', 'astoundify-simple-social-login' ); ?></label></th>
			<td>
				<input placeholder="<?php esc_attr_e( 'Log in with Facebook', 'astoundify-simple-social-login' ); ?>" id="facebook-login-button-text" type="text" class="regular-text" name="astoundify_simple_social_login_facebook[login_button_text]" value="<?php echo esc_attr( $options['login_button_text'] ); ?>">
				<p class="description"><?php esc_html_e( 'Controls the text displayed on the login button.', 'astoundify-simple-social-login' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="facebook-link-button-text"><?php esc_html_e( 'Link Button Text', 'astoundify-simple-social-login' ); ?></label></th>
			<td>
				<input placeholder="<?php esc_attr_e( 'Link your account to Facebook', 'astoundify-simple-social-login' ); ?>" id="facebook-link-button-text" type="text" class="regular-text" name="astoundify_simple_social_login_facebook[link_button_text]" value="<?php echo esc_attr( $options['link_button_text'] ); ?>">
				<p class="description"><?php esc_html_e( 'Controls the text displayed on the link account button.', 'astoundify-simple-social-login' ); ?></p>
			</td>
		</tr>
		<tr>
			<th scope="row"><label for="facebook-connected-info"><?php esc_html_e( 'Connected Info', 'astoundify-simple-social-login' ); ?></label></th>
			<td>
				<?php // translators: Do not translate {{unlink}} text. It'a a placeholder for unconnect link. ?>
				<input placeholder="<?php esc_attr_e( 'Your account is connected to Facebook. {{unlink}}.', 'astoundify-simple-social-login' ); ?>" id="facebook-connected-info" type="text" class="regular-text" name="astoundify_simple_social_login_facebook[connected_info]" value="<?php echo esc_attr( $options['connected_info'] ); ?>">
				<p class="description"><?php echo wp_kses_post( 'Controls the text displayed on the account page if the user is already connected. Use {{unlink}} to display unlink link.', 'astoundify-simple-social-login' ); ?></p>
			</td>
		</tr>
	</tbody>
</table>

<?php
}
add_action( 'astoundify_simple_social_login_panel_facebook', 'astoundify_simple_social_login_panel_facebook' );
