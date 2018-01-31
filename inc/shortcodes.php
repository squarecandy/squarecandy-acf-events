<?php

// [squarecandy_events]
function squarecandy_events_func($atts)
{
	$today = get_the_date('Ymd');

	// is this a "compact" display? [squarecandy_events style=compact]
	$compact = ( isset($atts['style']) && $atts['style'] == 'compact' ) ? true : false;

	$orderby = array(
		'start_date' => 'ASC',
		'start_time' => 'ASC',
	);

	if ( isset($atts['type']) && $atts['type'] == 'past' ) {

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
		$args['posts_per_page'] = 3;
		$thisid = get_the_ID();
		// $args['post__not_in'] = array($thisid);
	}

	// query
	$the_query2 = new WP_Query($args);

	$output = '';

	if ($the_query2->have_posts()):
		$output .= '<section class="event-listing';
		if ($compact) {
			$output .= ' event-listing-compact';
		}
		if ($past) {
			$output .= ' event-listing-past';
		}
		$output .= '">';

		if ($past) {
			// $output .= '<h2 class="acf-events-title">' . __( 'Past Events', 'squarecandy-acf-events' ) . '</h2>';

			// @TODO make grouping by year optional
			$pastevents = array();
			while ($the_query2->have_posts()) : $the_query2->the_post();
				$year = date('Y', strtotime(get_field('start_date')));
				$pastevents[$year][] = get_the_ID();
			endwhile;
			krsort($pastevents);
			foreach ($pastevents as $year => $items) {
				$output .= '<h2 class="accordion-header">'.$year.'</h2><div class="accordion-content">';
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
			// $output .= '<h2 class="acf-events-title">' . __( 'Upcoming Events', 'squarecandy-acf-events' ) . '</h2>';
			while ($the_query2->have_posts()) : $the_query2->the_post();
				include(ACF_EVENTS_DIR_PATH . 'templates/event-preview-return.php');
				// $output .= get_the_title();
			endwhile;
		}

		$output .= '</section>';
	else:
		$output .= get_field('no_events_text','option');

	endif;

	return $output;

	wp_reset_query();   // Restore global post data stomped by the_post().
}
add_shortcode('squarecandy_events', 'squarecandy_events_func');
