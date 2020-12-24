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

	// AJAX Load More

	/* global eventsdata */
	// load more items via ajax
	$( 'section.event-listing' ).on( 'click', '.load-more-events', function() {
		const button = $( this );
		const container = button.parent();
		let currentPage = container.data( 'current-page' );
		const maxNumPages = container.data( 'max-num-pages' );
		const eventType = container.data( 'type' );
		const archiveYear = container.data( 'archive-year' );

		$.ajax( {
			url: eventsdata.ajaxurl,
			type: 'post',
			data: {
				action: 'events_load_more',
				page: currentPage,
				eventType,
				archiveYear,
			},
			beforeSend() {
				button.text( 'Loading...' ); // change the button text, you can also add a preloader image
			},
			success( data ) {
				if ( data ) {
					button
						.text( 'Load More Events' )
						.parent()
						.before( data ); // insert new posts
					currentPage++;
					container.data( 'current-page', currentPage ); // increment current page
					if ( currentPage === maxNumPages ) {
						button.remove(); // if last page, remove the button
					}
				} else {
					button.remove(); // if no data, remove the button as well
				}
			},
			error( result ) {
				// eslint-disable-next-line no-console
				console.error( result );
			},
		} );
	} );
} );
