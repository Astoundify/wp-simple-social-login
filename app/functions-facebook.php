<?php
/**
 * Facebook Functions.
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
 * Facebook App ID.
 *
 * @since 1.0.0
 * @return string
 */
function astoundify_simple_social_login_facebook_get_app_id() {
	$option = get_option( 'astoundify_simple_social_login_facebook', array() );
	return isset( $option['app_id'] ) ? esc_attr( trim( $option['app_id'] ) ) : '';
}

/**
 * Facebook App Secret.
 *
 * @since 1.0.0
 * @return string
 */
function astoundify_simple_social_login_facebook_get_app_secret() {
	$option = get_option( 'astoundify_simple_social_login_facebook', array() );
	return isset( $option['app_secret'] ) ? esc_attr( trim( $option['app_secret'] ) ) : '';
}

/**
 * Facebook Login Button Text.
 *
 * @since 1.0.0
 * @return string
 */
function astoundify_simple_social_login_facebook_get_login_button_text() {
	$option = get_option( 'astoundify_simple_social_login_facebook', array() );
	return isset( $option['login_button_text'] ) && $option['login_button_text'] ? esc_attr( $option['login_button_text'] ) : esc_html__( 'Log in with Facebook', 'astoundify-simple-social-login' );
}

/**
 * Facebook Link Button Text.
 *
 * @since 1.0.0
 * @return string
 */
function astoundify_simple_social_login_facebook_get_link_button_text() {
	$option = get_option( 'astoundify_simple_social_login_facebook', array() );
	return isset( $option['link_button_text'] ) && $option['link_button_text'] ? esc_attr( $option['link_button_text'] ) : esc_html__( 'Link your account to Facebook', 'astoundify-simple-social-login' );
}



/* ========================================= */
add_action( 'init', function() {

	add_shortcode( 'test', function() {
		ob_start();

		$config = array(
			'app_id'                => astoundify_simple_social_login_facebook_get_app_id(),
			'app_secret'            => astoundify_simple_social_login_facebook_get_app_secret(),
			'default_graph_version' => 'v2.8',
		);

		$fb = new Facebook\Facebook( $config );

		$helper = $fb->getRedirectLoginHelper();
		$scope = ['email', 'user_about_me', 'user_birthday', 'user_hometown', 'user_location', 'user_website', 'publish_actions', 'read_custom_friendlists'];

		$process_url = add_query_arg( 'astoundify_simple_social_login', 'facebook', home_url() );
		//$process_url = home_url();
		$login_url = $helper->getLoginUrl( $process_url, $scope );

		echo '<a href="' . esc_url( $login_url ) . '">' . astoundify_simple_social_login_facebook_get_login_button_text() . '</a>';

		return ob_get_clean();
	} );

} );

add_action( 'astoundify_simple_social_login_process_facebook', function() {
?>


<?php
} );













































