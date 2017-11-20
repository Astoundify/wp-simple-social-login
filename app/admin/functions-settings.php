<?php
/**
 * Social Login Settings Functions.
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
 * Register Settings
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_register_settings() {
	register_setting(
		$option_group      = 'astoundify_simple_social_login',
		$option_name       = 'astoundify_simple_social_login',
		$sanitize_callback = 'astoundify_simple_social_login_sanitize_settings'
	);
}
add_action( 'admin_init', 'astoundify_simple_social_login_register_settings' );

/**
 * Sanitize Options
 *
 * @since 1.0.0
 *
 * @param mixed $input Input.
 * @return array
 */
function astoundify_simple_social_login_sanitize_settings( $input ) {
	$defaults = array(
		'display'             => array(),
		'providers'           => array(),
		'users_can_register'  => get_option( 'users_can_register', false ),
	);
	$output = wp_parse_args( (array)$input, $defaults );
	return apply_filters( 'astoundify_simple_social_login_sanitize_settings', $output );
}

/**
 * Add Admin Menu
 *
 * @since 1.0.0
 * @link https://codex.wordpress.org/Function_Reference/add_options_page
 */
function astoundify_simple_social_login_add_menu_page() {
	add_options_page(
		$page_title  = esc_html( 'Astoundify Simple Social Login', 'astoundify-simple-social-login' ),
		$menu_title  = esc_html( 'Simple Social Login', 'astoundify-simple-social-login' ),
		$capability  = 'manage_options',
		$menu_slug   = 'astoundify-simple-social-login',
		$function    = 'astoundify_simple_social_login_settings'
	);
}
add_action( 'admin_menu', 'astoundify_simple_social_login_add_menu_page' );

/**
 * Settings HTML
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_settings() {
	// Settings array.
	$tabs = array(
		'settings' => esc_html( 'Settings', 'astoundify-simple-social-login' ),
	);
	$tabs = apply_filters( 'astoundify_simple_social_login_settings_tabs', $tabs );
?>
<div id="astoundify-simple-social-login-settings" class="wrap">

	<h2 id="astoundify-simple-social-login-nav-tabs" class="nav-tab-wrapper wp-clearfix">
		<?php
		$i = 0;
		foreach ( $tabs as $id => $tab ) {
			$i++;
			echo '<a class="nav-tab ' . esc_attr( 1 === $i ? 'nav-tab-active' : '' ) . '" href="#astoundify-simple-social-login-panel-' . esc_attr( $id ) . '">' . $tab . '</a>';
		};
		?>
	</h2><!-- #astoundify-simple-social-login-nav-tab -->

	<form method="post" action="options.php">
		<div id="astoundify-simple-social-login-panels">

			<?php
			$i = 0;
			foreach ( $tabs as $id => $tab ) :
				$i++;
			?>

			<div id="astoundify-simple-social-login-panel-<?php echo esc_attr( $id ); ?>" <?php echo ( 1 !== $i ? 'style="display:none"' : '' ); ?> class="astoundify-simple-social-login-panel">
				<?php do_action( 'astoundify_simple_social_login_panel_' . $id ); ?>
			</div>

			<?php endforeach; ?>

		</div><!-- #astoundify-simple-social-login-panels -->

		<?php do_settings_sections( 'astoundify-simple-social-login' ); ?>
		<?php settings_fields( 'astoundify_simple_social_login' ); ?>
		<?php submit_button(); ?> 
	</form>

</div><!-- #astoundify-simple-social-login-settings -->
<?php
}

/**
 * Settings Panel
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_panel_settings() {
	$options = get_option( 'astoundify_simple_social_login', array() );
	$options = is_array( $options ) ? $options : array();
?>

<h3><?php esc_html_e( 'Astoundify Simple Social Login Settings', 'astoundify-simple-social-login' ); ?></h3>

<table class="form-table">
	<tbody>
		<tr>
			<th scope="row"><?php esc_html_e( 'Display Social Login Buttons', 'astoundify-simple-social-login' ); ?></th>
			<td>
				<?php
				$display_options = isset( $options['display'] ) && is_array( $options['display'] ) ? $options['display'] : array();
				$choices = array(
					'wp_login'    => esc_html( 'WordPress Login Form', 'astoundify-simple-social-login' ),
				);
				$choices = apply_filters( 'astoundify_simple_social_login_display_choices', $choices );
				?>

				<?php foreach ( $choices as $key => $label ) : ?>
					<label><input <?php checked( 1, in_array( $key, $display_options ) ); ?> type="checkbox" name="astoundify_simple_social_login[display][]" value="<?php echo esc_attr( $key ); ?>"> <?php echo esc_html( $label ); ?></label><br/>
				<?php endforeach; ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Providers', 'astoundify-simple-social-login' ); ?></th>
			<td>
				<?php
				$provider_options = isset( $options['providers'] ) && is_array( $options['providers'] ) ? $options['providers'] : array();
				$choices = array();
				$providers = astoundify_simple_social_login_get_providers();
				foreach( $providers as $id => $class ) {
					$provider = astoundify_simple_social_login_get_provider( $id );
					if ( $provider ) {
						$choices[ $id ] = $provider->get_label();
					}
				}
				?>

				<?php if ( $choices ) : ?>
					<?php foreach ( $choices as $key => $label ) : ?>
						<label><input <?php checked( 1, in_array( $key, $provider_options ) ); ?> type="checkbox" name="astoundify_simple_social_login[providers][]" value="<?php echo esc_attr( $key ); ?>"> <?php echo esc_html( $label ); ?></label><br/>
					<?php endforeach; ?>
				<?php else : ?>
					<p class="description"><?php esc_html_e( 'No provider available.', 'astoundify-simple-socia-login' ); ?></p>
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Enable Registration', 'astoundify-simple-social-login' ); ?></th>
			<td>
				<?php
				$registration_enabled = isset( $options['users_can_register'] ) && $options['users_can_register'] ? true : false;
				?>

				<label><input <?php checked( true, $registration_enabled ); ?> type="checkbox" name="astoundify_simple_social_login[users_can_register][]" value="1"> <?php echo esc_html_e( 'Register user if no users associated with social profile.', 'astoundify-simple-social-login' ); ?></label><br/>

			</td>
		</tr>
	</tbody>
</table>

<?php
}
add_action( 'astoundify_simple_social_login_panel_settings', 'astoundify_simple_social_login_panel_settings' );

/**
 * Settings Page Scripts
 *
 * @since 1.0.0
 *
 * @param string $hook_suffix Hook suffix.
 */
function astoundify_simple_social_login_admin_enqueue_scripts( $hook_suffix ) {
	// Do not load if not in settings page.
	if ( 'settings_page_astoundify-simple-social-login' !== $hook_suffix  ) {
		return;
	}

	// Script Vars.
	$debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? true : false;
	$version = $debug ? time() : ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_VERSION;

	// Settings CSS.
	$url = ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_URL . 'public/assets/css/settings.min.css';
	if ( $debug ) {
		$url = ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_URL . 'resources/assets/css/settings.css';
	}
	wp_enqueue_style( 'astoundify-simple-social-login', $url, array(), $version );

	// Settings JS.
	$url = ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_URL . 'public/assets/js/settings.min.js';
	if ( $debug ) {
		$url = ASTOUNDIFY_SIMPLE_SOCIAL_LOGIN_URL . 'resources/assets/js/settings.js';
	}
	wp_enqueue_script( 'astoundify-simple-social-login', $url, array( 'jquery' ), $version, true );
}
add_action( 'admin_enqueue_scripts', 'astoundify_simple_social_login_admin_enqueue_scripts' );

