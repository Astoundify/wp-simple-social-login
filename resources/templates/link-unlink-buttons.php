<?php
/**
 * Link/Unlink Buttons Template.
 *
 * @since 1.0.0
 *
 * @var array $providers Active Providers.
 */

// Do not access this file directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<ul class="astoundify-simple-social-login--link-unlink-buttons">
	<?php foreach ( $providers as $provider_id => $provider ) : ?>

		<li><?php echo $provider->get_link_unlink_button(); ?></li>

	<?php endforeach; ?>
</ul><!-- .astoundify-simple-social-login--link-unlink-buttons -->
