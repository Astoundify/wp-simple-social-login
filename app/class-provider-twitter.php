<?php
/**
 * Social Login Provider: Twitter
 *
 * @since 1.0.0
 *
 * @package Abstracts
 * @category Core
 * @author Astoundify
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
	 *
	 * @var string $id Provider ID.
	 */
	public $id = 'twitter';

	/**
	 * Option Name
	 *
	 * @since 1.0.0
	 *
	 * @var string $option_name Option Name.
	 */
	public $option_name = 'astoundify_simple_social_login_twitter';

	/**
	 * Label
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_label() {
		return esc_html__( 'Twitter', 'astoundify-simple-social-login' );
	}

	/**
	 * Login Register Button Text Default
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_login_register_button_text_default() {
		return esc_html__( 'Log in with Twitter', 'astoundify-simple-social-login' );
	}

	/**
	 * Link Button Text Default
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_link_button_text_default() {
		return esc_html__( 'Link your account to Twitter', 'astoundify-simple-social-login' );
	}

	/**
	 * Connected Info Text Default
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_connected_info_text_default() {
		return esc_html__( 'Your account is connected to Twitter. {{unlink}}.', 'astoundify-simple-social-login' );
	}

	/**
	 * Get API Data
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function api_get_data() {
		if ( ! $this->is_active() ) {
			return false;
		}

		$config = array(
			'base_url'  => $this->get_endpoint_url(),
			'providers' => array(
				'Twitter' => array(
					'enabled'         => true,
					'keys'            => array(
						'key'    => $this->get_app_id(),
						'secret' => $this->get_app_secret(),
					),
					'includeEmail'    => true,
					'access_type'     => 'offline',
					'approval_prompt' => 'force',
				),
			),
		);

		$hybridauth = $this->api_init( $config );
		$adapter    = $hybridauth->authenticate( 'Twitter' );
		$profile    = $adapter->getUserProfile();

		$data = array(
			'id'           => property_exists( $profile, 'identifier' ) ? $profile->identifier : '',
			'user_email'   => property_exists( $profile, 'emailVerified' ) ? $profile->emailVerified : ( property_exists( $profile, 'email' ) ? $profile->email : '' ),
			'display_name' => property_exists( $profile, 'displayName' ) ? $profile->displayName : '',
			'nickname'     => property_exists( $profile, 'displayName' ) ? $profile->displayName : '',
			'first_name'   => property_exists( $profile, 'firstName' ) ? $profile->firstName : '',
			'last_name'    => property_exists( $profile, 'lastName' ) ? $profile->lastName : '',
			'screen_name'  => property_exists( $profile, 'displayName' ) ? $profile->displayName : '', // Twitter username.
		);

		if ( ! $data['id'] ) {
			return false;
		}

		return $data;
	}

	/**
	 * Link Data
	 *
	 * @since 1.0.0
	 *
	 * @param array $data Full social data.
	 * @return array
	 */
	public function get_link_data( $data ) {
		$selected_data = array(
			'id'          => $data['id'],
			'screen_name' => $data['screen_name'],
		);
		return $selected_data;
	}

}
