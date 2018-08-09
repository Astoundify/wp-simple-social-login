<?php
/**
 * Social Login Provider: Facebook
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
 * Facebook Social Login Provider
 *
 * @since 1.0.0
 */
class Provider_Facebook extends Provider {

	/**
	 * Provider ID.
	 *
	 * @since 1.0.0
	 * @var string $id Provider ID.
	 */
	public $id = 'facebook';

	/**
	 * Provider label.
	 *
	 * @since 2.0.0
	 * @var string $label Provider label.
	 */
	public $label = 'Facebook';

	/**
	 * Option Name
	 *
	 * @since 1.0.0
	 * @var string $option_name Option Name.
	 */
	public $option_name = 'astoundify_simple_social_login_facebook';

	/**
	 * Config
	 *
	 * @since 2.0.0
	 * @var array $config Provider configuration.
	 */
	public $config = [
		'scope' => 'default, email',
	];

}
