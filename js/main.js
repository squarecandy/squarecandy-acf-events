jQuery( document ).ready( function( $ ) {
	// accordions for the event archive grouped by year
	$( '.accordion-content' ).hide();
	$( '.accordion-header' ).on( 'click', function() {
		const accordionHeader = $( this );

		if ( accordionHeader.hasClass( 'accordion-open' ) ) {
			// if you clicked on the one that's already open, just close it.
			$( '.accordion-open' ).removeClass( 'accordion-open' );
			accordionHeader.next().slideUp( 500 );
		} else {
			// otherwise, we have to open the clicked one and close any other that is open.

			$( '.accordion-open' )
				.not( accordionHeader )
				.removeClass( 'accordion-open' );
			accordionHeader
				.addClass( 'accordion-open' )
				.next()
				.slideDown( 400, function() {
					const padding = 80; // some extra to account for spacing and fixed menus
					const destination = accordionHeader.offset().top - padding;
					$( 'html,body' )
						.not( ':animated' )
						.animate( { scrollTop: destination }, 500 );
				} );
			$( '.accordion-content' )
				.not( accordionHeader.next() )
				.slideUp( 350 );
		}
		return false;
	} );
} );
