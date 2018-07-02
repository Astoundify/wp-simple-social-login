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
	 * Provider label.
	 *
	 * @since 2.0.0
	 * @var string $label Provider label.
	 */
	public $label = 'Google';

	/**
	 * Option Name
	 *
	 * @since 1.0.0
	 *
	 * @var string $option_name Option Name.
	 */
	public $option_name = 'astoundify_simple_social_login_google';

	/**
	 * Config
	 *
	 * @since 2.0.0
	 * @var array $config Provider configuration.
	 */
	public $config = [
		'scope' => 'https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/plus.me https://www.googleapis.com/auth/userinfo.email',
	];

}
