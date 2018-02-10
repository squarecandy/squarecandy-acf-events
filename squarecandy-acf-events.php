<?php
/*
Plugin Name: Square Candy ACF Events
Plugin URI:  http://squarecandy.net
Description: A custom events plugin that using Advanced Custom Fields
Version:	 0.1
Author:	  Peter Wise
Author URI:  http://squarecandy.net
Text Domain: squarecandy-acf-events
*/

define( 'ACF_EVENTS_DIR_PATH', plugin_dir_path( __FILE__ ) );

// don't let users activate w/o ACF
register_activation_hook( __FILE__, 'squarecandy_acf_events_activate' );
function squarecandy_acf_events_activate(){
	if ( !function_exists('acf_add_options_page') || !function_exists('get_field') ) {
		// check that ACF functions we need are available. Complain and bail out if they are not
		wp_die('The Square Candy ACF Events Plugin requires ACF
			(<a href="https://www.advancedcustomfields.com">Advanced Custom Fields</a>).
			<br><br><button onclick="window.history.back()">&laquo; back</button>');
	}
}

// Front End Scripts and Styles
function squarecandy_acf_events_enqueue_scripts() {

	wp_enqueue_style('squarecandy-fontawesome', plugins_url('css/vendor/font-awesome/css/font-awesome.min.css', __FILE__));
	wp_enqueue_style('squarecandy-acf-events-css', plugins_url('css/squarecandy-acf-events.css', __FILE__));

	if (
		// if maps option is on
		get_field('show_map_on_detail_page', 'option') &&
		// and there's a google maps API key entered
		get_field('google_maps_api_key', 'option') &&
		// and it's a single page view
		is_single() &&
		// and it's an event
		'event' == get_post_type( get_the_ID() )
	) {
		$google_maps_api_key = get_field('google_maps_api_key', 'option');
		wp_enqueue_script( 'squarecandy-acf-events-gmapapi', 'https://maps.googleapis.com/maps/api/js?key=' . $google_maps_api_key, array(), '20180101', true );
		wp_enqueue_script('squarecandy-acf-events-maps', plugins_url('js/googlemaps.js', __FILE__), array('jquery'), false, true);
		$data = get_field('venue_location');
		wp_localize_script( 'squarecandy-acf-events-maps', 'LOCATION', $data);
		$mapjson = get_field('google_maps_json', 'option');
		wp_localize_script( 'squarecandy-acf-events-maps', 'MAPJSON', $mapjson);
	}

	wp_enqueue_script('squarecandy-acf-events-js', plugins_url('js/squarecandy-acf-events.js', __FILE__), array('jquery'), false, true);
}
add_action('wp_enqueue_scripts', 'squarecandy_acf_events_enqueue_scripts');

// Admin Scripts and Styles
function squarecandy_acf_events_admin_enqueue() {
	wp_enqueue_style( 'squarecandy-acf-events-admin-css',  plugins_url('css/squarecandy-acf-events-admin.css', __FILE__), false, '1.0.0' );
}
add_action( 'admin_enqueue_scripts', 'squarecandy_acf_events_admin_enqueue' );


// add a new custom post type for events
include( ACF_EVENTS_DIR_PATH . 'post-types/event.php');

// add ACF fields for events
include( ACF_EVENTS_DIR_PATH . 'inc/acf.php');

// Add to Calendar Generation Links
include( ACF_EVENTS_DIR_PATH . 'inc/addtogcal.php');

// Add Shortcodes to display upcoming/past/compact/etc
include( ACF_EVENTS_DIR_PATH . 'inc/shortcodes.php');

// provide custom theming for individual event pages
// https://code.tutsplus.com/articles/plugin-templating-within-wordpress--wp-31088

add_filter( 'template_include', 'squarecandy_acf_events_template_chooser');

function squarecandy_acf_events_template_chooser( $template ) {

	$post_id = get_the_ID();

	// If this is an event post type and is a single post display, show custom template
	if ( 'event' == get_post_type( $post_id ) && is_single() ) {

		// Check if a custom template exists in the theme folder, if not, load the plugin template file
		if ( $theme_file = locate_template( array( 'event-single.php' ) ) ) {
			$file = $theme_file;
		}
		else {
			$file = dirname( __FILE__ ) . '/templates/event-single.php';
		}

		// allow other plugins to override via the squarecandy_acf_events_single_template filter
		$template = apply_filters( 'squarecandy_acf_events_single_template', $file );

	}

	// if this is the archive page, show custom archive template
	if ( 'event' == get_post_type( $post_id ) && is_archive() ) {
		// Check if a custom template exists in the theme folder, if not, load the plugin template file
		if ( $theme_file = locate_template( array( 'archive-events.php' ) ) ) {
			$file = $theme_file;
		}
		else {
			$file = dirname( __FILE__ ) . '/templates/archive-events.php';
		}

		// allow other plugins to override via the squarecandy_acf_events_archive_template filter
		$template = apply_filters( 'squarecandy_acf_events_archive_template', $file );

	}
	return $template;
}


function get_squarecandy_acf_events_date_display($event, $compact = null) {
	$formats = get_field('date_formats','option');
	$sep = '<span class="datetime-sep">' . $formats['datetime_sep'] . '</span>';
	$sep2 = '<span class="datetime-sep">' . $formats['datetime_sep2'] . '</span>';
	$range = '<span class="datetime-range">' . $formats['datetime_range'] . '</span>';

	if ($compact) {
		$formats['date_format'] = $formats['date_format_compact'];
		$formats['date_format_multi_start'] = $formats['date_format_compact_multi_start'];
		$formats['date_format_multi_end'] = $formats['date_format_compact_multi_end'];
	}
	// pre_r($formats);
	$output = '';
	if ( $event['multi_day'] != 1 ) {
		// single date
		$output .= date( $formats['date_format'], strtotime($event['start_date']) );
		if ( $event['all_day'] != 1 ) {
			// with time
			$output .= $sep . '<span class="time">' . date( $formats['time_format'], strtotime($event['start_time']) ) . '</span>';
		}
	}
	else {
		// multi day
		if ( $event['all_day'] == 1 && $event['start_date'] != $event['end_date'] ) {
			// range of dates with no time (all day)
			$output .= date( $formats['date_format_multi_start'], strtotime($event['start_date']) );
			$output .= $range;
			$output .= date( $formats['date_format_multi_end'], strtotime($event['end_date']) );
		}
		elseif ( $event['all_day'] == 1 && $event['start_date'] == $event['end_date'] ) {
			// fringe case: start and end date are set the same, no time (all day)
			$output .= date( $formats['date_format'], strtotime($event['start_date']) );
		}
		elseif ( $event['all_day'] != 1 && $event['start_date'] == $event['end_date'] && $event['start_time'] == $event['end_time'] ) {
			// fringe case: start and end time and date are set the same
			$output .= date( $formats['date_format'], strtotime($event['start_date']) );
			$output .= $sep . '<span class="time">' . date( $formats['time_format'], strtotime($event['start_time']) ) . '</span>';
		}
		elseif ( $event['all_day'] != 1 && $event['start_date'] == $event['end_date'] ) {
			// start and end date the same; range of times
			$output .= date( $formats['date_format_multi_start'], strtotime($event['start_date']) );
			$output .= $sep;
			$output .= '<span class="time">' . date( $formats['time_format'], strtotime($event['start_time']) );
			$output .= $range;
			$output .= date( $formats['time_format'], strtotime($event['end_time']) ) . '</span>';
		}
		elseif ( $event['all_day'] != 1 && $event['start_date'] != $event['end_date'] ) {
			$output .= $event['start_date'] . $sep2 . '<span class="time">' . $event['start_time'] . '</span> ';
			$output .= $range . ' ' . $event['end_date'] . $sep2 . '<span class="time">' . $event['end_time'] . '</span>';
		}
	}
	return $output;
}
function squarecandy_acf_events_date_display($event) {
	echo get_squarecandy_acf_events_date_display($event);
}

if ( function_exists( 'acf_add_options_sub_page' ) ){
	acf_add_options_sub_page(array(
		'title'      => 'Event Settings',
		'parent'     => 'edit.php?post_type=event',
		'capability' => 'manage_options'
	));
}

// Make sure we have a valid google maps api key
add_filter('acf/settings/google_api_key', function () {
	return get_field('google_maps_api_key','option');
});
// @TODO - add warning if the key is blank



// for debugging
if ( ! function_exists( 'pre_r' ) ) :
	function pre_r( $array ) {
		if (WP_DEBUG) {
			print '<pre class="squarecandy-pre-r">';
			print_r($array);
			print '</pre>';
		}
	}
endif;
