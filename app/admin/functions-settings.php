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

</div><!-- #astoundify-simple-social-login-admin -->
<?php
}

/**
 * Settings Section Callback
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_settings_callback() {
?>

<h3><?php esc_html_e( 'Astoundify Simple Social Login Settings', 'astoundify-simple-social-login' ); ?></h3>

<p>Lorem Ipsum..</p>

<table class="form-table">
	<tbody>
		<tr>
			<th scope="row">Text</th>
			<td>
				<input type="text" class="regular-text">
				<p class="description">Lorem ipsum.</p>
			</td>
		</tr>
	</tbody>
</table>

<h3><?php esc_html_e( 'WooCommerce Settings', 'astoundify-simple-social-login' ); ?></h3>

<p>Lorem Ipsum..</p>

<table class="form-table">
	<tbody>
		<tr>
			<th scope="row">Text</th>
			<td>
				<p><input type="text" class="regular-text"></p>
				<p class="description">Lorem ipsum.</p>
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

