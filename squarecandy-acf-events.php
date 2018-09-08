<?php
/*
Plugin Name: Square Candy ACF Events
Plugin URI:  https://github.com/squarecandy/squarecandy-acf-events
Description: A custom events plugin that using Advanced Custom Fields
Version:	 1.0.1
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

// Allow updating from the github repository
add_action( 'init', 'github_plugin_updater_test_init' );
function github_plugin_updater_test_init() {

	include_once 'updater.php';

	define( 'WP_GITHUB_FORCE_UPDATE', true );

	if ( is_admin() ) { // note the use of is_admin() to double check that this is happening in the admin

		$config = array(
			'slug' => plugin_basename( __FILE__ ),
			'proper_folder_name' => 'squarecandy-acf-events',
			'api_url' => 'https://api.github.com/repos/squarecandy/squarecandy-acf-events',
			'raw_url' => 'https://raw.github.com/squarecandy/squarecandy-acf-events/master',
			'github_url' => 'https://github.com/squarecandy/squarecandy-acf-events',
			'zip_url' => 'https://github.com/squarecandy/squarecandy-acf-events/archive/master.zip',
			'sslverify' => true,
			'requires' => '4.0',
			'tested' => '4.9.8',
			'readme' => 'README.md',
			// 'access_token' => '', // only needed for private repository
		);

		new WP_GitHub_Updater( $config );

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
		// gather data to localize in the google maps script
		$data['location'] = get_field('venue_location');
		$data['mapjson'] = get_field('google_maps_json', 'option');
		$event = get_fields();
		$data['infowindow'] = get_squarecandy_acf_events_address_display($event, 'infowindow', false);
		if ( !isset($event['zoom_level']) ) {
			if ( get_field('default_zoom_level', 'option') ) $event['zoom_level'] = get_field('default_zoom_level', 'option');
			else $event['zoom_level'] = 15;
		}
		$data['zoomlevel'] = $event['zoom_level'];
		wp_localize_script( 'squarecandy-acf-events-maps', 'DATA', $data);

		// wp_localize_script( 'squarecandy-acf-events-maps', 'MAPJSON', $mapjson);

		// wp_localize_script( 'squarecandy-acf-events-maps', 'INFOWINDOW', $infowindow);
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
		if (
			// all day is checked, start and end date are different, and end date is not empty
			( $event['all_day'] == 1 && $event['start_date'] != $event['end_date'] && !empty($event['end_date']) ) ||
			// all day not checked, start and end date are different, and end date is not empty, both start and end times are empty
			( $event['all_day'] != 1 && $event['start_date'] != $event['end_date'] && !empty($event['end_date']) && empty($event['start_time']) && empty($event['end_time']) )
		) {
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
		elseif ( $event['all_day'] != 1 && $event['start_date'] != $event['end_date'] && !empty($event['end_date']) && !empty($event['start_time']) && empty($event['end_time']) ) {
			// fringe case: different start and end date, only start time is set
			$output .= date( $formats['date_format_multi_start'], strtotime($event['start_date']) );
			$output .= $range;
			$output .= date( $formats['date_format_multi_end'], strtotime($event['end_date']) );
			$output .= $sep . '<span class="time">' . date( $formats['time_format'], strtotime($event['start_time']) ) . '</span>';
		}
		elseif (
			// start and end date are set the same, all day not checked
			( $event['all_day'] != 1 && $event['start_date'] == $event['end_date'] ) ||
			// OR start and end time are both set, no end date specified
			( $event['all_day'] != 1 && !empty($event['start_date']) && empty($event['end_date']) && !empty($event['start_time']) && !empty($event['end_time']) )
		) {
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

// Function to get an address block output from the individual location fields
// $style options are: 1line, 2line, 3line, infowindow, citystate
function get_squarecandy_acf_events_address_display($event, $style = '2line', $maplink = true) {

	$home_country = get_field('home_country','option');

	$output = '<div class="venue venue-' . $style . '" itemprop="location" itemscope="" itemtype="http://schema.org/MusicVenue">';
	if ( !empty($event['venue']) ) {
		// link the venue name to the venue website, unless this is the map popup
		if ( !empty($event['venue_link']) && 'infowindow' != $style ) {
			$output .= '<a href="' . $event['venue_link'] .'" itemprop="url">';
		}
		// link the venue name to the full version of google maps in the map popup style
		if ( !empty($event['venue_location']) && !empty($event['venue_location']['address']) && 'infowindow' == $style ) {
			$output .= '<a href="https://www.google.com/maps/search/' . urlencode($event['venue_location']['address']) .'"><strong>';
		}

			$output .= '<span itemprop="name">' . $event['venue'] .'</span> ';

		if ( !empty($event['venue_link']) && 'infowindow' != $style ) {
			$output .= '</a> ';
		}
		if ( !empty($event['venue_location']) && !empty($event['venue_location']['address']) && 'infowindow' == $style ) {
			$output .= '</strong></a><br> ';
		}

		if ( '1line' == $style ) $output .= ', ';
		if ( '2line' == $style || '3line' == $style ) $output .= '<br>';
	}
	switch ($style) {
		case '1line':
		case '2line':
		case '3line':
			if ( !empty($event['address']) ) {
				$output .= '<span class="address">' . $event['address'] . '</span>';
			}
			if ( !empty($event['address']) && !empty($event['city']) ) {
				if ( '1line' == $style || '2line' == $style ) $output .= ', ';
				if ( '3line' == $style ) $output .= '<br>';
			}
			if ( !empty($event['city']) ) {
				$output .= '<span class="city">' . $event['city'] . '</span>';
			}
			if ( !empty($event['city']) && !empty($event['state']) ) {
				$output .= ', ';
			}
			if ( !empty($event['state']) ) {
				$output .= '<span class="state">' . $event['state'] . '</span>';
			}
			if ( !empty($event['zip']) ) {
				$output .= ' <span class="zip">' . $event['zip'] . '</span>';
			}
			if ( !empty($event['country']) && $home_country != $event['country'] ) {
				$output .= ', <span class="country">' . $event['country'] . '</span>';
			}
			if ( !empty($event['venue_location']) && !empty($event['venue_location']['address']) ) {
				if ( $maplink && get_field('map_link', 'option') ) {
					$output .= '<a class="button small button-gray button-map" href="https://www.google.com/maps/search/' . urlencode($event['venue_location']['address']) .'">';
					$output .= '<i class="fa fa-map"></i> ' . __('map', 'squarecandy-acf-events');
					$output .= '</a>';
				}
				$output .= '<meta itemprop="address" content="' .$event['venue_location']['address'] .'">';
			}
			break;

		case 'infowindow':
			// for use with google maps
			if ( !empty($event['address']) ) {
				$output .= '<span class="address">' . $event['address'] . '</span><br>';
			}
			if ( !empty($event['city']) ) {
				$output .= '<span class="city">' . $event['city'] . '</span>';
			}
			if ( !empty($event['city']) && !empty($event['state']) ) {
				$output .= ', ';
			}
			if ( !empty($event['state']) ) {
				$output .= '<span class="state">' . $event['state'] . '</span>';
			}
			if ( !empty($event['zip']) ) {
				$output .= ' <span class="zip">' . $event['zip'] . '</span>';
			}
			if ( !empty($event['country']) && $home_country != $event['country'] ) {
				$output .= ', <span class="country">' . $event['country'] . '</span>';
			}
			break;

		case 'citystate':
		default:
			// for short display
			if ( !empty($event['city']) ) {
				$output .= '<span class="city">' . $event['city'] . '</span>';
			}
			if ( $home_country && $event['country'] != $home_country ) {
				if ( !empty($event['city']) ) $output .= ', ';
				$output .= '<span class="country">' . $event['country'] . '</span>';
			}
			elseif ( !empty($event['state']) ) {
				if ( !empty($event['city']) ) $output .= ', ';
				$output .= '<span class="state">' . $event['state'] . '</span>';
			}
			break;

	}
	$output .= '</div>';
	return $output;
	}
function squarecandy_acf_events_address_display($event, $style = '2line', $maplink = true) {
	echo get_squarecandy_acf_events_address_display($event, $style, $maplink);
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


// Add nice Admin Columns Pro setup if the plugin is enabled.
if ( function_exists( 'ac_register_columns') ) :
function ac_custom_column_settings_8980a6ec() {

	ac_register_columns( 'event', array(
		array(
			'columns' => array(
				'5b92f1ec692b4' => array(
					'type' => 'column-featured_image',
					'label' => '',
					'width' => '60',
					'width_unit' => 'px',
					'image_size' => 'cpac-custom',
					'image_size_w' => '60',
					'image_size_h' => '60',
					'edit' => 'off',
					'sort' => 'off',
					'filter' => 'off',
					'filter_label' => '',
					'name' => '5b92f1ec692b4'
				),
				'title' => array(
					'type' => 'title',
					'label' => 'Title',
					'width' => '',
					'width_unit' => '%',
					'edit' => 'off',
					'sort' => 'on',
					'name' => 'title'
				),
				'5b92f1ec85d6b' => array(
					'type' => 'column-acf_field',
					'label' => 'Start Date',
					'width' => '',
					'width_unit' => '%',
					'field' => 'field_5616bbe39fbec',
					'date_format' => 'm/d/Y',
					'edit' => 'off',
					'sort' => 'on',
					'filter' => 'on',
					'filter_label' => '',
					'filter_format' => 'range',
					'name' => '5b92f1ec85d6b'
				),
				'5b92f1ec88345' => array(
					'type' => 'column-acf_field',
					'label' => 'End Date',
					'width' => '',
					'width_unit' => '%',
					'field' => 'field_5616bd75112ca',
					'date_format' => 'm/d/Y',
					'edit' => 'off',
					'sort' => 'off',
					'filter' => 'off',
					'filter_label' => '',
					'filter_format' => '',
					'name' => '5b92f1ec88345'
				),
				'5b92f1ec884a6' => array(
					'type' => 'column-acf_field',
					'label' => 'Start Time',
					'width' => '',
					'width_unit' => '%',
					'field' => 'field_5616bc2b9fbed',
					'date_format' => 'acf',
					'edit' => 'off',
					'sort' => 'off',
					'name' => '5b92f1ec884a6'
				),
				'5b92f1ec894da' => array(
					'type' => 'column-acf_field',
					'label' => 'End Time',
					'width' => '',
					'width_unit' => '%',
					'field' => 'field_5616bd8e112cb',
					'date_format' => 'acf',
					'edit' => 'off',
					'sort' => 'off',
					'name' => '5b92f1ec894da'
				),
				'5b92f1ec8961e' => array(
					'type' => 'column-acf_field',
					'label' => 'City',
					'width' => '',
					'width_unit' => '%',
					'field' => 'field_city585d8171a157e',
					'character_limit' => '20',
					'edit' => 'off',
					'sort' => 'on',
					'filter' => 'on',
					'filter_label' => '',
					'name' => '5b92f1ec8961e'
				)
			),

		)
	) );
}
add_action( 'ac/ready', 'ac_custom_column_settings_8980a6ec' );
endif;


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
