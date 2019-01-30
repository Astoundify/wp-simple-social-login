/**
 * Astoundify Simple Social Login JS
 *
 * @since 1.0.0
 */

/**
 * Internal dependencies.
 */
import 'css/buttons.css';
import 'css/woocommerce.css';
import 'css/wp-login.css';

(function( window, undefined ){

	window.wp = window.wp || {};
	var document = window.document;
	var $ = window.jQuery;


	/**
	 * Bind items to to the DOM.
	 *
	 * @since 1.0.0
	 */
	$( function(){

		$( '#astoundify-simple-social-login-nav-tabs .nav-tab' ).click( function(e) {
			e.preventDefault();

			// Tab active state.
			$( this ).addClass( 'nav-tab-active' );
			$( this ).siblings( '.nav-tab' ).removeClass( 'nav-tab-active' );

			// Show/hide panel.
			var panel = $( this ).attr( 'href' );
			$( panel ).show();
			$( panel ).siblings( '.astoundify-simple-social-login-panel' ).hide();
		});
	});

})( window );
