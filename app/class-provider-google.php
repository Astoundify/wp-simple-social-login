<?php
/**
 * Social Login Provider: Google
 *
 * @since 1.0.0
 *
 * @package  Abstracts
 * @category Core
 * @author   Astoundify
 */

namespace Astoundify\Simple_Social_Login;

// Do not access this file directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Google Social Login Provider
 *
 * @since 1.0.0
 */
class Provider_Google extends Provider {

	/**
	 * Provider ID.
	 *
	 * @since 1.0.0
	 *
	 * @var string $id Provider ID.
	 */
	public $id = 'google';

	/**
	 * Option Name
	 *
	 * @since 1.0.0
	 *
	 * @var string $option_name Option Name.
	 */
	public $option_name = 'astoundify_simple_social_login_google';

	/**
	 * Label
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Google', 'astoundify-simple-social-login' );
	}

	/**
	 * Get API config data.
	 *
	 * @since 2.0.0
	 *
	 * @return array
	 */
	public function get_config() {
		return wp_parse_args(
			[
				'scope' => implode(
					' ', [
						'https://www.googleapis.com/auth/userinfo.profile',
						'https://www.googleapis.com/auth/userinfo.email',
					]
				),
			],
			parent::get_config()
		);
	}

}
