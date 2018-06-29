<?php
/**
 * Social Login Provider: Twitter
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
 * Twitter Social Login Provider
 *
 * @since 1.0.0
 */
class Provider_Twitter extends Provider {

	/**
	 * Provider ID.
	 *
	 * @since 1.0.0
	 * @var string $id Provider ID.
	 */
	public $id = 'twitter';

	/**
	 * Provider label.
	 *
	 * @since 2.0.0
	 * @var string $label Provider label.
	 */
	public $label = 'Twitter';

	/**
	 * Option Name
	 *
	 * @since 1.0.0
	 * @var string $option_name Option Name.
	 */
	public $option_name = 'astoundify_simple_social_login_twitter';

	/**
	 * Config
	 *
	 * @since 2.0.0
	 * @var array $config Provider configuration.
	 */
	public $config = [
		'includeEmail' => true,
	];

}
