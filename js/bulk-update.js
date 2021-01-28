jQuery( document ).ready( function( $ ) {
	/* global sqcdy_event_bulk_update_ajax_obj */

	function bulkUpdatePaged( page ) {
		$.post(
			sqcdy_event_bulk_update_ajax_obj.ajax_url,
			{
				_ajax_nonce: sqcdy_event_bulk_update_ajax_obj.nonce,
				action: 'squarecandy_cleanup_all_events',
				page,
			},
			function( data ) {
				// callback
				data = JSON.parse( data );
				if ( data.status === 'success' ) {
					$( '.squarecandy-events-bulk-update-progress .progress-bar' ).css( 'width', data.percent_completed + '%' );
					$( '.squarecandy-events-bulk-update-progress-text' ).html( data.progress_message );
					bulkUpdatePaged( data.page + 1 );
				}

				if ( data.status === 'complete' ) {
					$( '.squarecandy-events-bulk-update-progress .progress-bar' ).css( 'width', '100%' );
					$( '.squarecandy-events-bulk-update-progress-text' ).html( 'Complete!' );
					$( '.squarecandy-events-bulk-update' )
						.delay( 2000 )
						.slideUp();
				}
			}
		);
	}

	bulkUpdatePaged( 1 );
} );
