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
	return apply_filters( 'astoundify_simple_social_login_sanitize_settings', $input );
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
	$settings = array(
		'settings' => esc_html( 'Settings', 'astoundify-simple-social-login' ),
		'facebook' => esc_html( 'Facebook', 'astoundify-simple-social-login' ),
		'twitter'  => esc_html( 'Twitter', 'astoundify-simple-social-login' ),
	);
	$settings = apply_filters( 'astoundify_simple_social_login_settings', $settings );
?>
<div id="astoundify-simple-social-login-admin" class="wrap">

	<h2 id="astoundify-simple-social-login-nav-tab" class="nav-tab-wrapper wp-clearfix">
		<?php
		$i = 0;
		foreach ( $settings as $id => $tab ) {
			$i++;
			echo '<a class="nav-tab ' . esc_attr( 1 === $i ? 'nav-tab-active' : '' ) . '" href="#astoundify-simple-social-login-' . esc_attr( $id ) . '">' . $tab . '</a>';
		};
		?>
	</h2><!-- #astoundify-simple-social-login-nav-tab -->

	<form method="post" action="options.php">
		<div id="astoundify-simple-social-login-panels">

			<?php
			$i = 0;
			foreach ( $settings as $id => $tab ) :
				$i++;
			?>

			<div id="astoundify-simple-social-login-<?php echo esc_attr( $id ); ?>" <?php echo ( 1 !== $i ? 'style="display:none"' : '' ); ?>>
				<?php do_action( 'astoundify_simple_social_login_settings_' . $id ); ?>
			</div>

			<?php endforeach; ?>

		</div><!-- #astoundify-simple-social-login-panels -->

		<?php do_settings_sections( 'astoundify-simple-social-login' ); ?>
		<?php settings_fields( 'astoundify_simple_social_login' ); ?>
		<?php submit_button(); ?> 
	</form>

</div><!-- #astoundify-simple-social-login-admin -->
<?php
}

/**
 * Settings Section Callback
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_settings_callback() {
	$options = get_option( 'astoundify_simple_social_login', array() );
	$options = is_array( $options ) ? $options : array();
?>

<h3><?php esc_html_e( 'Astoundify Simple Social Login Settings', 'astoundify-simple-social-login' ); ?></h3>

<table class="form-table">
	<tbody>
		<tr>
			<th scope="row"><?php esc_html_e( 'Enable in', 'astoundify-simple-social-login' ); ?></th>
			<td>

				<?php
				$location_options = isset( $options['location'] ) && is_array( $options['location'] ) ? $options['location'] : array();
				$locations = array(
					'wp_login'    => esc_html( 'WordPress Login Form', 'astoundify-simple-social-login' ),
					'wp_register' => esc_html( 'WordPress Register Form', 'astoundify-simple-social-login' ),
				);
				$locations = apply_filters( 'astoundify_simple_social_login_locations_choices', $locations );
				?>

				<?php foreach ( $locations as $key => $label ) : ?>
					<label><input <?php checked( 1, in_array( $key, $location_options ) ); ?> type="checkbox" name="astoundify_simple_social_login[location][]" value="<?php echo esc_attr( $key ); ?>"> <?php echo esc_html( $label ); ?></label><br/>
				<?php endforeach; ?>
			</td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e( 'Services', 'astoundify-simple-social-login' ); ?></th>
			<td>

				<?php
				$service_options = isset( $options['service'] ) && is_array( $options['service'] ) ? $options['service'] : array();
				$services = array(
					'facebook' => esc_html( 'Facebook', 'astoundify-simple-social-login' ),
					'twitter'  => esc_html( 'Twitter', 'astoundify-simple-social-login' ),
				);
				$services = apply_filters( 'astoundify_simple_social_login_services_choices', $services );
				?>

				<?php foreach ( $services as $key => $label ) : ?>
					<label><input <?php checked( 1, in_array( $key, $service_options ) ); ?> type="checkbox" name="astoundify_simple_social_login[service][]" value="<?php echo esc_attr( $key ); ?>"> <?php echo esc_html( $label ); ?></label><br/>
				<?php endforeach; ?>
			</td>
		</tr>
	</tbody>
</table>

<?php
}
add_action( 'astoundify_simple_social_login_settings_settings', 'astoundify_simple_social_login_settings_callback' );

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

