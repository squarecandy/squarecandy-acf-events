<?php

// [squarecandy_events]
function squarecandy_events_func($atts) {

	$today = date('Ymd', time());
	// pre_r($today);

	// is this a "compact" display? [squarecandy_events style=compact]
	$compact = ( isset($atts['style']) && $atts['style'] == 'compact' ) ? true : false;

	$archive_by_year = get_field('archive_by_year','option');
	$accordion = false;
	if ( $archive_by_year && get_field('accordion','option') ) $accordion = true;

	$orderby = array(
		'start_date' => 'ASC',
		'start_time' => 'ASC',
	);

	if ( isset($atts['type']) && $atts['type'] == 'past' ) {

		if ( !$archive_by_year ) {
			$orderby = array(
				'start_date' => 'DESC',
				'start_time' => 'ASC',
			);
		}

		// past events archive [squarecandy_events type=past]
		$args = array(
			'post_type' => 'event',
			'post_status' => 'publish',
			'posts_per_page' => -1, // show everything... @TODO consider limiting and paginating this
			'orderby' => $orderby,
			'meta_query' => array(
				'relation' => 'AND',
				array(
					'key' => 'start_date',
					'type' => 'DATE',
					'value' => $today,
					'compare' => '<='
				)
			),
		);
		$past = true;
	} else {
		// upcoming events (default)
		$args = array(
			'post_type' => 'event',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'orderby' => $orderby,
			'meta_query' => array(
				'relation' => 'OR',
				array(
					'key' => 'start_date',
					'type' => 'DATE',
					'value' => $today,
					'compare' => '>='
				),
				array(
					'key' => 'end_date',
					'type' => 'DATE',
					'value' => $today,
					'compare' => '>='
				)
			),
		);
		$past = false;
	}

	if ($compact) {
		$args['posts_per_page'] = get_field('number_of_upcoming', 'option');
	}

	// pre_r($args);

	// query
	$the_query2 = new WP_Query($args);

	$output = '';

	if ( $the_query2->have_posts() ):

		$output .= '<section class="event-listing';
		if ($compact) {
			$output .= ' event-listing-compact';
		}
		if ($past) {
			$output .= ' event-listing-past';
		}
		if ( $past && $accordion ) {
			$output .= ' event-listing-past-accordion';
		}
		if ( $past && $archive_by_year) {
			$output .= ' event-listing-past-by-year';
		}
		$output .= '">';

		// if this is a past event type and is grouped by year
		if ($past && $archive_by_year ) {
			// @TODO make grouping by year optional
			$pastevents = array();
			while ($the_query2->have_posts()) : $the_query2->the_post();
				$year = date('Y', strtotime(get_field('start_date')));
				$pastevents[$year][] = get_the_ID();
			endwhile;
			krsort($pastevents);
			foreach ($pastevents as $year => $items) {
				$output .= '<h2 class="events-year-header';
				if ($accordion) $output .= ' accordion-header';
				$output .= '">' . $year . '</h2>';

				$output .= '<div class="event-year-content';
				if ($accordion) $output .= ' accordion-content';
				$output .= '">';

				$args['post__in'] = $items;
				$the_query3 = new WP_Query($args);
				if ($the_query3->have_posts()):
					while ($the_query3->have_posts()) : $the_query3->the_post();
						include(ACF_EVENTS_DIR_PATH . 'templates/event-preview-return.php');
						// $output .= get_the_title();
					endwhile;
				endif;
				$output .= '</div>';
			}
		} else {
			while ( $the_query2->have_posts() ) : $the_query2->the_post();
				include(ACF_EVENTS_DIR_PATH . 'templates/event-preview-return.php');
			endwhile;
		}

		// for the "compact" view, show the link to the full events page if there are more events.
		if ( $compact && $the_query2->post_count >= $args['posts_per_page'] && $more_link = get_field('more_link', 'option') ) {
			$output .= '<a class="events-more-link button" href="' . $more_link['url'] . '">' . $more_link['title'] . '</a>';
		}
		$output .= '</section>';

	else:
		$output .= get_field('no_events_text','option');

	endif;

	wp_reset_query();   // Restore global post data stomped by the_post().

	return $output;
}
add_shortcode('squarecandy_events', 'squarecandy_events_func');
