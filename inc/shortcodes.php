<?php
/** function to generate the shortcode [squarecandy_events]
 *
 * @param array  $atts {
 *      Shortcode attributes. Optional.
 *
 *      $style Display style. values: 'compact'
 *      $cat Filter by category.
 *      $not_in Filter by id.
 *      $posts_per_page
 *      $only_featured
 *      $featured_at_top
 *      $exclude_featured
 *      $moreinfo_post_link
 *      $archive_year
 *      $page
 *      $type values: 'past', 'all', 'upcoming'
 *      $order - ASC or DESC, only read in case of type='all'
 * }
 */
function squarecandy_events_func( $atts = array() ) {

	$today = date_i18n( 'Y-m-d', strtotime( 'now' ) );

	// is this a "compact" display? [squarecandy_events style=compact]
	$compact = isset( $atts['style'] ) && 'compact' === $atts['style'] ? true : false;

	// also filter by category? [squarecandy_events cat=my-cat-slug]
	$cat = ! empty( $atts['cat'] ) ? $atts['cat'] : false;

	// filter out specific events by ID
	// @TODO - remove this - see https://10up.github.io/Engineering-Best-Practices/php/#performance
	$not_in = ! empty( $atts['not_in'] ) ? $atts['not_in'] : false;

	// override total posts returned if too large
	// see https://10up.github.io/Engineering-Best-Practices/php/#performance
	$max_posts_per_page = 2500;
	$posts_per_page     = ! empty( $atts['posts_per_page'] ) && $atts['posts_per_page'] <= $max_posts_per_page ? $atts['posts_per_page'] : $max_posts_per_page;

	// set total posts for ajax load more if not passed in
	$default_ajax_posts_per_page = 20;
	if ( empty( $atts['posts_per_page'] ) && get_option( 'options_events_ajax_load_more' ) ) {
		$posts_per_page = get_option( 'options_events_posts_per_page' ) ?? $default_ajax_posts_per_page;
	}

	// filter for featured posts only
	$only_featured = ! empty( $atts['only_featured'] ) ? true : false;

	// order featured posts at top
	$featured_at_top = ! empty( $atts['featured_at_top'] ) ? true : false;

	// filter out featured posts
	$exclude_featured = ! empty( $atts['exclude_featured'] ) ? true : false;

	// force more info button to link to post
	$moreinfo_post_link = ! empty( $atts['moreinfo_post_link'] ) ? true : false;

	$archive_by_year = get_option( 'options_archive_by_year' );

	$accordion = false;
	if ( $archive_by_year && get_option( 'options_accordion' ) ) {
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
		'meta_key'       => 'sort_date',
		'orderby'        => 'meta_value',
		'order'          => 'ASC',
	);

	if ( ! empty( $atts['page'] ) && (int) $atts['page'] > 1 ) {
		$args['paged'] = (int) $atts['page'];
	}

	if ( isset( $atts['type'] ) && 'past' === $atts['type'] ) {
		// past events archive [squarecandy_events type=past]
		if ( ! $archive_by_year && ! $archive_year ) {
			$args['meta_key'] = 'magic_sort_date';
			$args['order']    = 'DESC';
		}

		// to speed things up, don't query two meta_keys on archive_year searches
		if ( ! $archive_year ) {
			$args['meta_query']['relation']     = 'AND';
			$args['meta_query']['archive_date'] = array(
				'key'     => 'archive_date',
				'type'    => 'DATE',
				'value'   => $today,
				'compare' => '<',
			);
		}

		if ( $archive_year ) {
			// for the current year, use now as the later date barrier on start_date
			// this may give slightly different results to archive_date, but maybe worth it to speed things up
			if ( (int) gmdate( 'Y' ) === $archive_year ) {
				$cutoff                             = date_i18n( 'Ymd', strtotime( 'now' ) );
				$args['meta_query']['archive_year'] = array(
					'key'     => 'start_date',
					'value'   => array( $archive_year . '0101', $cutoff ),
					'compare' => 'BETWEEN',
					'type'    => 'NUMERIC',
				);
			} else {
				$args['meta_query']['archive_year'] = array(
					'key'     => 'start_date',
					'value'   => array( $archive_year . '0101', $archive_year . '1231' ),
					'compare' => 'BETWEEN',
					'type'    => 'NUMERIC',
				);
			}
			$args['meta_key'] = 'sort_date';
			$args['order']    = 'ASC';
		}

		$past = true;

	} elseif ( isset( $atts['type'] ) && 'all' === $atts['type'] ) {
		// show all events, both past and present [squarecandy_events type=all]
		$args['meta_key'] = 'magic_sort_date';
		if ( isset( $atts['order'] ) && in_array( $atts['order'], array( 'ASC', 'DESC' ), true ) ) {
			$args['order'] = $atts['order'];
		} else {
			$args['order'] = 'DESC';
		}
		$past = false;
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

		// featured event at top of list
		if ( $featured_at_top ) {

			$args['orderby'] = array(
				'featured'        => 'DESC',
				$args['meta_key'] => $args['order'],
			);

			$args['meta_query'] = array(
				'relation'        => 'OR',
				'featured'        => array(
					'key'     => 'featured',
					'type'    => 'NUMERIC',
					'compare' => 'EXISTS',
				),
				'featured2'       => array(
					'key'     => 'featured',
					'compare' => 'NOT EXISTS',
				),
				$args['meta_key'] => array(
					'key'     => $args['meta_key'],
					'compare' => 'EXISTS',
				),
			);

			unset( $args['meta_key'] );
			unset( $args['order'] );
		}

		// only include featured events
		if ( $only_featured ) {
			$args['meta_query']['relation'] = 'AND';
			$args['meta_query']             = array(
				'key'     => 'featured',
				'value'   => 1,
				'compare' => '=',
			);
		}

		$past = false;
	}

	if ( $compact ) {
		// this will always override the number set in $atts - seems counterintuituve
		$args['posts_per_page'] = get_option( 'options_number_of_upcoming' );
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
		// @TODO - remove this. Better to just filter them out of the retured array later.
		// see https://10up.github.io/Engineering-Best-Practices/php/#performance
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
				$start_date     = get_field( 'start_date' );
				$start_time     = get_field( 'start_time' );
				$year           = date_i18n( 'Y', strtotime( $start_date ) );
				$month_day_time = date_i18n( 'md', strtotime( $start_date ) );
				if ( ! empty( $start_time ) ) {
					$month_day_time .= date_i18n( 'Hi', strtotime( $start_time ) );
				}
				// add the post date and ID to the key to ensure unique keys
				$month_day_time                        .= get_the_date( 'YmdHi' );
				$month_day_time                        .= get_the_ID();
				$pastevents[ $year ][ $month_day_time ] = get_the_ID();

			endwhile;

			// sort the results

			// check for attr to determine year sort order
			if ( ! empty( $atts['year_sort'] ) && 'ASC' === strtoupper( $atts['year_sort'] ) ) {
				// sort the years in ascending order
				ksort( $pastevents );
			} else {
				// sort the years in descending order
				krsort( $pastevents );
			}

			// check for attr to determine subevent sort order
			if ( ! empty( $atts['subevent_sort'] ) && 'DESC' === strtoupper( $atts['subevent_sort'] ) ) {
				// sort in descending order
				foreach ( $pastevents as $year => $items ) {
					krsort( $pastevents[ $year ] );
				}
			} else {
				// sort in ascending order
				foreach ( $pastevents as $year => $items ) {
					ksort( $pastevents[ $year ] );
				}
			}

			foreach ( $pastevents as $year => $items ) {
				$output .= '<h2 class="events-year-header';
				if ( $accordion ) {
					$output .= ' accordion-header';
				}
				$output .= '" tabindex="0">' . $year . '</h2>';

				$output .= '<div class="event-year-content';
				if ( $accordion ) {
					$output .= ' accordion-content';
				}
				$output .= '">';

				foreach ( $items as $event_id ) {
					include ACF_EVENTS_DIR_PATH . 'templates/event-preview-return.php';
				}

				$output .= '</div>';
			}
		} else {
			while ( $the_query2->have_posts() ) :
				$the_query2->the_post();
				$event_id = get_the_ID();
				include ACF_EVENTS_DIR_PATH . 'templates/event-preview-return.php';
			endwhile;
		}

		if ( ! $ajax ) :
			// for the "compact" view, show the link to the full events page if there are more events.
			$more_link = get_option( 'options_more_link' );
			if ( $compact && $the_query2->post_count >= $args['posts_per_page'] && is_array( $more_link ) && isset( $more_link['url'] ) && isset( $more_link['title'] ) ) {
				$output .= '<a class="events-more-link button" href="' . $more_link['url'] . '">' . $more_link['title'] . '</a>';
			}

			// if ajax load more pagination is being used, display the load more button
			if ( get_option( 'options_events_ajax_load_more' ) && $the_query2->max_num_pages > 1 ) :
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
			$output .= '<section class="event-listing no-events">' . do_shortcode( get_option( 'options_no_events_text' ) ) . '</section>';
		}
	endif;

	wp_reset_postdata(); // Restore global post data stomped by the_post().

	return $output;
}
add_shortcode( 'squarecandy_events', 'squarecandy_events_func' );
