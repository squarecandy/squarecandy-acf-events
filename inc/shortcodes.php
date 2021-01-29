<?php
// function to generate the shortcode [squarecandy_events]
function squarecandy_events_func( $atts = array() ) {

	$today = date_i18n( 'Ymd', strtotime( 'now' ) );

	// is this a "compact" display? [squarecandy_events style=compact]
	$compact = isset( $atts['style'] ) && 'compact' === $atts['style'] ? true : false;

	// also filter by category? [squarecandy_events cat=my-cat-slug]
	$cat = ! empty( $atts['cat'] ) ? $atts['cat'] : false;

	// filter out specific events by ID
	// @TODO - remove this - see https://10up.github.io/Engineering-Best-Practices/php/#performance
	$not_in = ! empty( $atts['not_in'] ) ? $atts['not_in'] : false;

	// override total posts returned
	// @TODO - remove this. too much power for users - see https://10up.github.io/Engineering-Best-Practices/php/#performance
	// $posts_per_page = ! empty( $atts['posts_per_page'] ) ? $atts['posts_per_page'] : false;

	$posts_per_page = 2500;
	if ( get_field( 'events_ajax_load_more', 'option' ) ) {
		$posts_per_page = get_field( 'events_posts_per_page', 'option' ) ?? 20;
	}

	// filter for featured posts only
	$only_featured = ! empty( $atts['only_featured'] ) ? true : false;

	// order featured posts at top
	$featured_at_top = ! empty( $atts['featured_at_top'] ) ? true : false;

	// filter out featured posts
	$exclude_featured = ! empty( $atts['exclude_featured'] ) ? true : false;

	// force more info button to link to post
	$moreinfo_post_link = ! empty( $atts['moreinfo_post_link'] ) ? true : false;

	$archive_by_year = get_field( 'archive_by_year', 'option' );

	$accordion = false;
	if ( $archive_by_year && get_field( 'accordion', 'option' ) ) {
		$accordion = true;
	}

	$archive_year = ! empty( $atts['archive_year'] ) ? (int) $atts['archive_year'] : false;

	// check if the archive year value makes sense. 1970-2050 could be too restrictive eventually.
	// check for years between 1000-3999 just to be extra generous.
	// this will still filter out nonsense values like 123 or 12345
	if ( $archive_year > 4000 || $archive_year < 1000 ) {
		$archive_year = false;
	}

	// Start the default $args array.
	$args = array(
		'post_type'      => 'event',
		'post_status'    => 'publish',
		'posts_per_page' => $posts_per_page,
		'orderby'        => array(
			'start_date' => 'ASC',
			'start_time' => 'ASC',
		),
		'meta_query'     => array(
			// the values below are only for compatibility with orderby an array of keys
			array(
				'relation'   => 'OR',
				'start_date' => array(
					'key'     => 'start_date',
					'type'    => 'DATE',
					'compare' => 'EXISTS',
				),
				'start_time' => array(
					'key'     => 'start_time',
					'value'   => 'this-is-a-sorting-hack',
					'type'    => 'TIME',
					'compare' => '!=',
				),
			),
		),
	);

	if ( ! empty( $atts['page'] ) && (int) $atts['page'] > 1 ) {
		$args['paged'] = (int) $atts['page'];
	}

	if ( $featured_at_top ) {
		$args['orderby'] = array( 'featured' => 'DESC' ) + $args['orderby'];
	}

	if ( isset( $atts['type'] ) && 'past' === $atts['type'] ) {
		// past events archive [squarecandy_events type=past]
		if ( ! $archive_by_year && ! $archive_year ) {
			$args['orderby'] = array(
				'start_date' => 'DESC',
				'start_time' => 'ASC',
			);
		}

		$args['meta_query']['relation']     = 'AND';
		$args['meta_query']['archive_date'] = array(
			'key'     => 'archive_date',
			'type'    => 'DATE',
			'value'   => $today,
			'compare' => '<',
		);

		if ( $archive_year ) {
			$args['meta_query']['archive_year'] = array(
				'key'     => 'start_date',
				'value'   => array( $archive_year . '0101', $archive_year . '1231' ),
				'compare' => 'BETWEEN',
				'type'    => 'NUMERIC',
			);
		}

		$past = true;

	} elseif ( isset( $atts['type'] ) && 'all' === $atts['type'] ) {
		// show all events, both past and present [squarecandy_events type=all]
		$args['orderby'] = array(
			'start_date' => 'DESC',
			'start_time' => 'ASC',
		);
		$past            = false;
	} else {
		// upcoming events - this is the default display
		$args['meta_query']['relation']     = 'AND';
		$args['meta_query']['archive_date'] = array(
			'key'     => 'archive_date',
			'type'    => 'DATE',
			'value'   => $today,
			'compare' => '>=',
		);

		if ( $exclude_featured ) {
			$args['meta_query']['exclude_featured_meta'] = array(
				'relation' => 'OR',
				array(
					'key'     => 'featured',
					'value'   => 0,
					'compare' => '=',
				),
				array(
					'key'     => 'featured',
					'compare' => 'NOT EXISTS',
				),
			);
		}

		// only include featured events
		if ( $featured_at_top ) {
			$args['meta_query']['featured_at_top_meta'] = array(
				'relation'  => 'OR',
				'featured'  => array(
					'key'     => 'featured',
					'type'    => 'NUMERIC',
					'compare' => 'EXISTS',
				),
				'featured2' => array(
					'key'     => 'featured',
					'compare' => 'NOT EXISTS',
				),
			);
		}

		// featured event at top of list
		if ( $only_featured ) {
			$args['meta_query']['only_featured_meta'] = array(
				'key'     => 'featured',
				'value'   => 1,
				'compare' => '=',
			);
		}

		$past = false;
	}

	if ( $compact ) {
		$args['posts_per_page'] = get_field( 'number_of_upcoming', 'option' );
	}

	if ( $cat ) {
		$args['tax_query'] = array(
			array(
				'taxonomy'         => 'events-category',
				'terms'            => $cat,
				'field'            => 'slug',
				'include_children' => true,
				'operator'         => 'IN',
			),
		);
	}

	if ( $not_in ) {
		$args['post__not_in'] = explode( ',', $not_in );
	}

	// query
	$the_query2 = new WP_Query( $args );

	$output = '';

	// is this an ajax call?
	$ajax = ! empty( $atts['ajax'] ) ? true : false;

	if ( $the_query2->have_posts() ) :

		if ( ! $ajax ) :
			$output .= '<section class="event-listing';
			if ( $compact ) {
				$output .= ' event-listing-compact';
			}
			if ( $past ) {
				$output .= ' event-listing-past';
			}
			if ( $past && $accordion ) {
				$output .= ' event-listing-past-accordion';
			}
			if ( $past && $archive_by_year ) {
				$output .= ' event-listing-past-by-year';
			}
			$output .= '">';
		endif;

		// if this is a past event type and is grouped by year
		if ( $past && $archive_by_year && ! $ajax ) {
			$pastevents = array();
			while ( $the_query2->have_posts() ) :
				$the_query2->the_post();
				$year                  = date_i18n( 'Y', strtotime( get_field( 'start_date' ) ) );
				$pastevents[ $year ][] = get_the_ID();
			endwhile;
			krsort( $pastevents );
			foreach ( $pastevents as $year => $items ) {
				$output .= '<h2 class="events-year-header';
				if ( $accordion ) {
					$output .= ' accordion-header';
				}
				$output .= '">' . $year . '</h2>';

				$output .= '<div class="event-year-content';
				if ( $accordion ) {
					$output .= ' accordion-content';
				}
				$output .= '">';

				$args['post__in'] = $items;
				$the_query3       = new WP_Query( $args );
				if ( $the_query3->have_posts() ) :
					while ( $the_query3->have_posts() ) :
						$the_query3->the_post();
						include ACF_EVENTS_DIR_PATH . 'templates/event-preview-return.php';
					endwhile;
				endif;
				$output .= '</div>';
			}
		} else {
			while ( $the_query2->have_posts() ) :
				$the_query2->the_post();
				include ACF_EVENTS_DIR_PATH . 'templates/event-preview-return.php';
			endwhile;
		}

		if ( ! $ajax ) :
			// for the "compact" view, show the link to the full events page if there are more events.
			$more_link = get_field( 'more_link', 'option' );
			if ( $compact && $the_query2->post_count >= $args['posts_per_page'] && $more_link ) {
				$output .= '<a class="events-more-link button" href="' . $more_link['url'] . '">' . $more_link['title'] . '</a>';
			}

			// if ajax load more pagination is being used, display the load more button
			if ( get_field( 'events_ajax_load_more', 'option' ) && $the_query2->max_num_pages > 1 ) :
				$type = $atts['type'] ?? 'upcoming';

				$output .= '<div class="more-container" data-current-page="1" ';
				$output .= 'data-max-num-pages="' . $the_query2->max_num_pages . '" ';
				$output .= 'data-posts-per-page="' . $posts_per_page . '" ';
				$output .= 'data-type="' . $type . '" ';
				if ( $archive_year ) {
					$output .= 'data-archive-year="' . $archive_year . '" ';
				}
				$output .= '><button class="load-more load-more-events" style="">Load More Events</button></div>';
			endif;

			$output .= '</section>';
		endif;

	else :
		if ( ! $ajax ) {
			$output .= get_field( 'no_events_text', 'option' );
		}
	endif;

	wp_reset_postdata(); // Restore global post data stomped by the_post().

	return $output;
}
add_shortcode( 'squarecandy_events', 'squarecandy_events_func' );
