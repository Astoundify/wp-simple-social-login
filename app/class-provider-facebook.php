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
	 *
	 * @var string $id Provider ID.
	 */
	public $id = 'facebook';

	/**
	 * Option Name
	 *
	 * @since 1.0.0
	 *
	 * @var string $option_name Option Name.
	 */
	public $option_name = 'astoundify_simple_social_login_facebook';

	/**
	 * Label
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Facebook', 'astoundify-simple-social-login' );
	}

	/**
	 * Login Register Button Text Default
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_login_register_button_text_default() {
		return esc_html__( 'Log in with Facebook', 'astoundify-simple-social-login' );
	}

	/**
	 * Link Button Text.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_link_button_text_default() {
		return esc_html__( 'Link your account to Facebook', 'astoundify-simple-social-login' );
	}

	/**
	 * Connected Info Text
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_connected_info_text_default() {
		return esc_html__( 'Your account is connected to Facebook. {{unlink}}.', 'astoundify-simple-social-login' );
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
				'scope'           => 'email',
				'access_type'     => 'offline',
				'approval_prompt' => 'force',
			],
			parent::get_config()
		);
	}

	/**
	 * Get API Data
	 *
	 * @since 2.0.0
	 *
	 * @param $adapter Provider HyrbidAuth provider.
	 * @return array
	 */
	public function get_profile_data( $adapter ) {
		$profile = $adapter->getUserProfile();

		$data = [
			'id'           => property_exists( $profile, 'identifier' ) ? $profile->identifier : '',
			'user_email'   => property_exists( $profile, 'emailVerified' ) ? $profile->emailVerified : ( property_exists( $profile, 'email' ) ? $profile->email : '' ),
			'display_name' => property_exists( $profile, 'displayName' ) ? $profile->displayName : '',
			'nickname'     => property_exists( $profile, 'displayName' ) ? $profile->displayName : '',
			'first_name'   => property_exists( $profile, 'firstName' ) ? $profile->firstName : '',
			'last_name'    => property_exists( $profile, 'lastName' ) ? $profile->lastName : '',
		];

		if ( ! $data['id'] ) {
			return false;
		}

		return $data;
	}

}
