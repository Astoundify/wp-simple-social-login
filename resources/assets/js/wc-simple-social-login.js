/**
 * Plugin...
 *
 * @since 1.0.0
 */
(function( window, undefined ) {

	window.wp = window.wp || {};

	var document = window.document;
	var $ = window.jQuery;

	/**
	 * @since 1.0.0
	 */
	var $document = $(document);

	/**
	 * Wait for DOM ready.
	 *
	 * @since 1.0.0
	 */
	$document.ready(function() {
		console.log( 'Testing Plugin Scaffold!' );
	});

}( window ));
