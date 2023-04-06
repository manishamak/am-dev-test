/* global mmApiData */

( function ( document, window, $ ) {
	/*
	 *  Refresh button Ajax call
	 */
	$( document ).on( 'click', '.mm-api-data-refresh-btn', function ( e ) {
		e.preventDefault();

		const $btn = $( this );

		if ( $btn.hasClass( 'disabled' ) || $btn.hasClass( 'loading' ) ) {
			return false;
		}

		$btn.addClass( 'loading disabled' );
		$btn.text( mmApiData.loading );

		// Setup ajax POST data.
		const data = {
			action: 'mm_api_data_refresh_ajax',
			nonce: mmApiData.nonce,
		};

		$.post( mmApiData.ajax_url, data, function ( res ) {
			$( '.wp-mail-smtp-page-content' ).html( res );
		} ).fail( function ( xhr ) {
			console.log( xhr.responseText );
		} );
	} );
} )( document, window, jQuery );
