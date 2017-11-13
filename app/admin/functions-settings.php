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
	
}
add_action( 'admin_init', 'astoundify_simple_social_login_register_settings' );

/**
 * Add Admin Menu
 *
 * @since 1.0.0
 */
function astoundify_simple_social_login_add_menu_page() {
	
}
add_action( 'admin_menu', 'astoundify_simple_social_login_add_menu_page' );

/**
 * Settings Page Scripts
 *
 * @since 1.0.0
 *
 * @param string $hook_suffix Hook suffix.
 */
function astoundify_simple_social_login_admin_enqueue_scripts( $hook_suffix ) {
	
}
add_action( 'admin_enqueue_scripts', 'astoundify_simple_social_login_admin_enqueue_scripts' );


























