<?php
// function to generate the shortcode [squarecandy_events]
function squarecandy_events_func( $atts = array() ) {

	$today = date( 'Ymd', current_time('timestamp') );

	// is this a "compact" display? [squarecandy_events style=compact]
	$compact = isset( $atts['style'] ) && 'compact' === $atts['style'] ? true : false;

	// also filter by category? [squarecandy_events cat=my-cat-slug]
	$cat = ! empty( $atts['cat'] ) ? $atts['cat'] : false;

	// filter out specific events by ID
	$not_in = ! empty( $atts['not_in'] ) ? $atts['not_in'] : false;

	// override total posts returned
	$posts_per_page = ! empty( $atts['posts_per_page'] ) ? $atts['posts_per_page'] : false;

	// filter for featured posts only
	$only_featured = ! empty( $atts['only_featured'] ) ? true : false;

	// filter out featured posts
	$exclude_featured = ! empty( $atts['exclude_featured'] ) ? true : false;

	$archive_by_year = get_field( 'archive_by_year', 'option' );
	$accordion       = false;
	if ( $archive_by_year && get_field( 'accordion', 'option' ) ) {
		$accordion = true;
	}

	$orderby = array(
		'start_date' => 'ASC',
		'start_time' => 'ASC',
	);

	if ( isset( $atts['type'] ) && 'past' === $atts['type'] ) {
		// past events archive [squarecandy_events type=past]
		if ( ! $archive_by_year ) {
			$orderby = array(
				'start_date' => 'DESC',
				'start_time' => 'ASC',
			);
		}
		$args = array(
			'post_type'      => 'event',
			'post_status'    => 'publish',
			'posts_per_page' => 2500, // @TODO consider limiting and paginating this
			'orderby'        => $orderby,
			'meta_key' => 'start_date',
			'meta_query'     => array(
				'relation' => 'AND',
				'start_date' => array(
					'key'     => 'start_date',
					'type'    => 'DATE',
					'value'   => $today,
					'compare' => '<=',
				),
				array(
					'relation' => 'OR',
					'start_time' => array(
						'key'     => 'start_time',
						'compare' => 'EXISTS',
					),
					'start_time2' => array(
						'key'     => 'start_time',
						'compare' => 'NOT EXISTS',
					),
				),
			),
		);
		$past = true;
	} elseif ( isset( $atts['type'] ) && 'all' === $atts['type'] ) {
		// show all events, both past and present [squarecandy_events type=all]
		$orderby = array(
			'start_date' => 'DESC',
			'start_time' => 'ASC',
		);

		$args    = array(
			'post_type'      => 'event',
			'post_status'    => 'publish',
			'posts_per_page' => 2500, //  @TODO consider limiting and paginating this
			'orderby'        => $orderby,
			'meta_key' => 'start_date',
			'meta_query'     => array( array(
				'relation' => 'OR',
				'start_date' => array(
					'key'     => 'start_date',
					'type'    => 'DATE',
					'compare' => 'EXISTS',
				),
				'start_time' => array(
					'key'     => 'start_time',
					'type'    => 'TIME',
					'compare' => 'EXISTS',
				),
				'start_time2' => array(
					'key'     => 'start_time',
					'type'    => 'TIME',
					'compare' => 'NOT EXISTS',
				),
			), ),
		);
		$past    = false;
	} else {
		// upcoming events - this is the default display
		$meta_query = array(
			'relation' => 'AND',
			// this is the real query that retuns the desired sub-set of items
			array(
				'relation' => 'OR',
				'start_date2' => array(
					'key'     => 'start_date',
					'type'    => 'DATE',
					'value'   => $today,
					'compare' => '>=',
				),
				'end_date2' => array(
					'key'     => 'end_date',
					'type'    => 'DATE',
					'value'   => $today,
					'compare' => '>=',
				),
			),
			// the values below are only for compatibility with orderby an array of keys
			array(
				'relation' => 'OR',
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
			'meta_key' => 'start_date',
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
		$args['post__not_in'] = explode(',', $not_in);
	}

	if ( $posts_per_page ) {
		$args['posts_per_page'] = $ppp;
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
				$year                  = date( 'Y', strtotime( get_field( 'start_date' ) ) );
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
