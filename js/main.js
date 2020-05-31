jQuery( document ).ready( function( $ ) {
	// accordions - event archive, etc
	$( '.accordion-content' ).hide();
	$( '.accordion-header' ).on( 'click', function() {
		const accordionheader = $( this );
		if ( accordionheader.hasClass( 'accordion-open' ) ) {
			$( '.accordion-open' ).removeClass( 'accordion-open' );
			accordionheader.next().slideUp( 500 );
		} else {
			const openoffset = $( '.accordion-open' )
				.next()
				.outerHeight();
			const destination = accordionheader.offset().top;
			$( 'html,body' )
				.not( ':animated' )
				.animate( { scrollTop: destination - 110 - openoffset }, 500 );
			$( '.accordion-open' )
				.not( accordionheader )
				.removeClass( 'accordion-open' );
			accordionheader
				.addClass( 'accordion-open' )
				.next()
				.slideDown( 500 );
			$( '.accordion-content' )
				.not( accordionheader.next() )
				.slideUp( 500 );
		}
		return false;
	} );
} );
