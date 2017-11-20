/**
 * WordPress Login Screen
 *
 * @since 1.0.0
 */

( function( window, undefined ){

	window.wp = window.wp || {};
	var document = window.document;
	var $ = window.jQuery;

	/**
	 * Bind items to to the DOM.
	 *
	 * @since 1.0.0
	 */
	$( function(){

		/**
		 * Use Regular Login.
		 *
		 * @since 1.0.0
		 */
		$( '.login-with-username a' ).click( function(e) {
			e.preventDefault();
			$( '#loginform' ).addClass( 'regular-login' );
			$( '#astoundify-simple-social-login-wordpress-back a' ).show();
		});

		/**
		 * Back to Social Login.
		 *
		 * @since 1.0.0
		 */
		$( '#astoundify-simple-social-login-wordpress-back a' ).click( function(e) {
			e.preventDefault();
			$( '#loginform' ).removeClass( 'regular-login' );
			$( this ).hide();
		});
	});

})( window );
