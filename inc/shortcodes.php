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
	$posts_per_page = ! empty( $atts['posts_per_page'] ) ? $atts['posts_per_page'] : false;

	// filter for featured posts only
	$only_featured = ! empty( $atts['only_featured'] ) ? true : false;

	// order featured posts at top
	$featured_at_top = ! empty( $atts['featured_at_top'] ) ? true : false;

	// filter out featured posts
	$exclude_featured = ! empty( $atts['exclude_featured'] ) ? true : false;

	// force more info button to link to post
	$moreinfo_post_link = ! empty( $atts['moreinfo_post_link'] ) ? true : false;

	$archive_by_year = get_field( 'archive_by_year', 'option' );

	$accordion       = false;
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

	$orderby = array(
		'start_date' => 'ASC',
		'start_time' => 'ASC',
	);

	if ( $featured_at_top ) {
		$orderby = array( 'featured' => 'DESC' ) + $orderby;
	}

	if ( isset( $atts['type'] ) && 'past' === $atts['type'] ) {
		// past events archive [squarecandy_events type=past]
		if ( ! $archive_by_year && ! $archive_year ) {
			$orderby = array(
				'start_date' => 'DESC',
				'start_time' => 'ASC',
			);
		}

		$meta_query = array(
			'relation'   => 'AND',
			'archive_date' => array(
				'key'     => 'archive_date',
				'type'    => 'DATE',
				'value'   => $today,
				'compare' => '<',
			),
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
		);

		if ( $archive_year ) {
			$meta_query['archive_year'] = array(
				'key'     => 'start_date',
				'value'   => array( $archive_year . '0101', $archive_year . '1231' ),
				'compare' => 'BETWEEN',
				'type'    => 'NUMERIC',
			);
		}

		$args = array(
			'post_type'      => 'event',
			'post_status'    => 'publish',
			'posts_per_page' => 2500, // @TODO consider limiting and paginating this
			'orderby'        => $orderby,
			'meta_query'     => $meta_query,
		);
		$past = true;
	} elseif ( isset( $atts['type'] ) && 'all' === $atts['type'] ) {
		// show all events, both past and present [squarecandy_events type=all]
		$orderby = array(
			'start_date' => 'DESC',
			'start_time' => 'ASC',
		);

		$args = array(
			'post_type'      => 'event',
			'post_status'    => 'publish',
			'posts_per_page' => 2500, // @TODO consider limiting and paginating this
			'orderby'        => $orderby,
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
		$past = false;
	} else {
		// upcoming events - this is the default display
		$meta_query = array(
			'relation' => 'AND',
			// this is the real query that retuns the desired sub-set of items
			array(
				'archive_date' => array(
					'key'     => 'archive_date',
					'type'    => 'DATE',
					'value'   => $today,
					'compare' => '>=',
				),
			),
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
		);

		if ( $exclude_featured ) {
			$exclude_featured_meta = array(
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
			array_push( $meta_query, $exclude_featured_meta );
		}

		// only include featured events
		if ( $featured_at_top ) {
			$featured_at_top_meta = array(
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
			array_push( $meta_query, $featured_at_top_meta );
		}

		// featured event at top of list
		if ( $only_featured ) {
			$only_featured_meta = array(
				'key'     => 'featured',
				'value'   => 1,
				'compare' => '=',
			);
			array_push( $meta_query, $only_featured_meta );
		}

		$args = array(
			'post_type'      => 'event',
			'post_status'    => 'publish',
			'posts_per_page' => 2500,
			'orderby'        => $orderby,
			'meta_key'       => 'start_date',
			'meta_query'     => $meta_query,
		);
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

	if ( $posts_per_page ) {
		$args['posts_per_page'] = $posts_per_page;
	}

	// query
	$the_query2 = new WP_Query( $args );

	$output = '';

	if ( $the_query2->have_posts() ) :

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

		// if this is a past event type and is grouped by year
		if ( $past && $archive_by_year ) {
			// @TODO make grouping by year optional
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

		// for the "compact" view, show the link to the full events page if there are more events.
		$more_link = get_field( 'more_link', 'option' );
		if ( $compact && $the_query2->post_count >= $args['posts_per_page'] && $more_link ) {
			$output .= '<a class="events-more-link button" href="' . $more_link['url'] . '">' . $more_link['title'] . '</a>';
		}
		$output .= '</section>';

	else :
		if ( ! $is_featured ) {
			$output .= get_field( 'no_events_text', 'option' );
		}
	endif;

	wp_reset_postdata();   // Restore global post data stomped by the_post().

	return $output;
}
add_shortcode( 'squarecandy_events', 'squarecandy_events_func' );
