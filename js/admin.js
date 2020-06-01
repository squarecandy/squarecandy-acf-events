jQuery( document ).ready( function( $ ) {
	// validate that the end date is same or after the start date
	$( 'body.post-type-event #publish' ).on( 'click', function( e ) {
		const startDate = $( 'div[data-name="start_date"] input[type=hidden]' ).val();
		const endDate = $( 'div[data-name="end_date"] input[type=hidden]' ).val();
		const multiDay = $( 'div[data-name="multi_day"] input[type=checkbox]' ).is( ':checked' );

		if ( multiDay && endDate < startDate ) {
			const errorDatesBackwards = 'The end date cannot be before the start date';
			$( '#squarecandy-error-dates-backwards' ).remove();
			$( 'div[data-name="end_date"] .acf-input' ).after(
				'<div class="error" id="squarecandy-error-dates-backwards">' + errorDatesBackwards + '</div>'
			);
			e.preventDefault();
		}
	} );
} );
