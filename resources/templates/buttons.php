<?php
/**
 * Buttons Template.
 *
 * @since 1.0.0
 *
 * @var array $providers Selected Providers.
 */

// Do not access this file directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<ul class="astoundify-simple-social-login-buttons">
	<?php foreach ( $providers as $provider_id ) :
		$data = astoundify_simple_social_login_provider_data( $provider_id );
		$provider = new $data['class'];
	?>

		<?php if ( $provider->is_active() ) : ?>
			<li><?php echo $provider->get_login_register_button(); ?></li>
		<?php endif; ?>

	<?php endforeach; ?>
</ul><!-- .astoundify-simple-social-login-buttons -->
