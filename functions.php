<?php
// main plugin file

define( 'ACF_EVENTS_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'ACF_EVENTS_URL', plugin_dir_url( __FILE__ ) );
define( 'ACF_EVENTS_VERSION', 'version-1.7.0' );

// don't let users activate w/o ACF
register_activation_hook( __FILE__, 'squarecandy_acf_events_activate' );
function squarecandy_acf_events_activate() {
	if ( ! function_exists( 'acf_add_options_page' ) || ! function_exists( 'get_field' ) ) {
		// check that ACF functions we need are available. Complain and bail out if they are not
		wp_die(
			'The Square Candy ACF Events Plugin requires ACF
			(<a href="https://www.advancedcustomfields.com">Advanced Custom Fields</a>).
			<br><br><button onclick="window.history.back()">&laquo; back</button>'
		);
	}
}


// Front End Scripts and Styles
function squarecandy_acf_events_enqueue_scripts() {

	wp_enqueue_style( 'squarecandy-fontawesome', ACF_EVENTS_URL . 'dist/css/vendor/font-awesome/css/font-awesome.min.css', false, ACF_EVENTS_VERSION );
	wp_enqueue_style( 'squarecandy-acf-events-css', ACF_EVENTS_URL . 'dist/css/main.min.css', false, ACF_EVENTS_VERSION );

	if (
		// if maps option is on
		get_option( 'options_show_map_on_detail_page' ) &&
		// and there's a google maps API key entered
		get_option( 'options_google_maps_api_key' ) &&
		// and it's a single page view
		is_single() &&
		// and it's an event
		'event' === get_post_type( get_the_ID() )
	) {
		$google_maps_api_key = get_option( 'options_google_maps_api_key' );
		wp_enqueue_script( 'squarecandy-acf-events-gmapapi', 'https://maps.googleapis.com/maps/api/js?key=' . $google_maps_api_key, array(), ACF_EVENTS_VERSION, true );
		wp_enqueue_script( 'squarecandy-acf-events-maps', ACF_EVENTS_URL . 'dist/js/googlemaps.min.js', array( 'jquery' ), ACF_EVENTS_VERSION, true );
		// gather data to localize in the google maps script
		$data['location']   = get_field( 'venue_location' );
		$data['mapjson']    = get_option( 'options_google_maps_json' );
		$event              = get_fields();
		$data['infowindow'] = get_squarecandy_acf_events_address_display( $event, 'infowindow', false );
		if ( ! isset( $event['zoom_level'] ) ) {
			if ( get_option( 'options_default_zoom_level' ) ) {
				$event['zoom_level'] = get_option( 'options_default_zoom_level' );
			} else {
				$event['zoom_level'] = 15;
			}
		}
		$data['zoomlevel'] = $event['zoom_level'];
		wp_localize_script( 'squarecandy-acf-events-maps', 'DATA', $data );
	}

	wp_enqueue_script( 'squarecandy-acf-events-js', ACF_EVENTS_URL . 'dist/js/main.min.js', array( 'jquery' ), ACF_EVENTS_VERSION, true );
	wp_localize_script(
		'squarecandy-acf-events-js',
		'eventsdata',
		array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'events-ajax-nonce' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'squarecandy_acf_events_enqueue_scripts' );

// Admin Scripts and Styles
function squarecandy_acf_events_admin_enqueue() {
	wp_enqueue_style( 'squarecandy-acf-events-admin-css', ACF_EVENTS_URL . 'dist/css/admin.min.css', false, ACF_EVENTS_VERSION );
	wp_enqueue_script( 'squarecandy-acf-events-admin-js', ACF_EVENTS_URL . 'dist/js/admin.min.js', array( 'jquery' ), ACF_EVENTS_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'squarecandy_acf_events_admin_enqueue' );

// add a new custom post type for events
require ACF_EVENTS_DIR_PATH . 'post-types/event.php';

if ( get_option( 'options_enable_categories' ) ) :
	// add a events category taxonomy
	include ACF_EVENTS_DIR_PATH . 'taxonomies/events-category.php';
endif;

// add ACF fields for events
require ACF_EVENTS_DIR_PATH . 'inc/acf.php';

// Add to Calendar Generation Links
require ACF_EVENTS_DIR_PATH . 'inc/addtogcal.php';

// Add Shortcodes to display upcoming/past/compact/etc
require ACF_EVENTS_DIR_PATH . 'inc/shortcodes.php';

// Add Year Archive pages
if ( get_option( 'options_yearly_archive' ) ) {
	require ACF_EVENTS_DIR_PATH . 'inc/year-archives.php';
}

if ( get_option( 'options_events_ajax_load_more' ) ) {
	require ACF_EVENTS_DIR_PATH . 'inc/ajax-load-more.php';
}

//require ACF_EVENTS_DIR_PATH . 'inc/sync-work-categories.php';
require ACF_EVENTS_DIR_PATH . 'inc/class-sqc-sync-work-categories.php';
$sqc_event_cat_sync = new SQC_Sync_Work_Categories();

// provide custom theming for individual event pages
// https://code.tutsplus.com/articles/plugin-templating-within-wordpress--wp-31088

add_filter( 'template_include', 'squarecandy_acf_events_template_chooser' );

function squarecandy_acf_events_template_chooser( $template ) {

	$post_id = get_the_ID();

	// If this is an event post type and is a single post display, show custom template
	if ( 'event' === get_post_type( $post_id ) && is_single() ) {

		// Check if a custom template exists in the theme folder, if not, load the plugin template file
		if ( locate_template( array( 'event-single.php' ) ) ) {
			$file = locate_template( array( 'event-single.php' ) );
		} else {
			$file = ACF_EVENTS_DIR_PATH . '/templates/event-single.php';
		}

		// allow other plugins to override via the squarecandy_acf_events_single_template filter
		$template = apply_filters( 'squarecandy_acf_events_single_template', $file );

	}

	// if this is the archive page, show custom archive template
	if ( 'event' === get_post_type( $post_id ) && is_archive() ) {
		// Check if a custom template exists in the theme folder, if not, load the plugin template file
		if ( locate_template( array( 'archive-events.php' ) ) ) {
			$file = locate_template( array( 'archive-events.php' ) );
		} else {
			$file = ACF_EVENTS_DIR_PATH . '/templates/archive-events.php';
		}

		// allow other plugins to override via the squarecandy_acf_events_archive_template filter
		$template = apply_filters( 'squarecandy_acf_events_archive_template', $file );

	}
	return $template;
}


function get_squarecandy_acf_events_date_display( $event, $compact = null ) {

	// timedate formats and separators
	$formats = get_field( 'date_formats', 'option' );
	$sep     = '<span class="datetime-sep">' . $formats['datetime_sep'] . '</span>';
	$sep2    = '<span class="datetime-sep">' . $formats['datetime_sep2'] . '</span>';
	$range   = '<span class="datetime-range">' . $formats['datetime_range'] . '</span>';

	// ensure all required fields have a value
	$multi_day  = $event['multi_day'] ?? false;
	$all_day    = $event['all_day'] ?? false;
	$start_date = $event['start_date'] ?? false;
	$end_date   = $event['end_date'] ?? false;
	$start_time = $event['start_time'] ?? false;
	$end_time   = $event['end_time'] ?? false;

	if ( $compact ) {
		$formats['date_format']             = $formats['date_format_compact'];
		$formats['date_format_multi_start'] = $formats['date_format_compact_multi_start'];
		$formats['date_format_multi_end']   = $formats['date_format_compact_multi_end'];
	}

	$output = '';

	if ( ! $multi_day ) {
		// single date
		// example: June 3, 2019
		$output .= date_i18n( $formats['date_format'], strtotime( $start_date ) );
		if ( ! $all_day ) {
			// with time
			// example: June 3, 2019 - 3:00pm
			$output .= $sep . '<span class="time">' . date_i18n( $formats['time_format'], strtotime( $start_time ) ) . '</span>';
		}
	} else {
		// multi day
		if (
			// all day is checked, start and end date are different, and end date is not empty
			(
				$all_day &&
				$start_date !== $end_date &&
				! empty( $end_date )
			) ||
			// OR all day not checked, start and end date are different,
			// and end date is not empty, both start and end times are empty
			(
				! $all_day &&
				$start_date !== $end_date &&
				! empty( $end_date ) &&
				empty( $start_time ) &&
				empty( $end_time )
			)
		) {
			// range of dates with no time (all day)
			// Example: June 20 — July 4, 2019
			$output .= date_i18n( $formats['date_format_multi_start'], strtotime( $start_date ) );
			$output .= $range;
			$output .= date_i18n( $formats['date_format_multi_end'], strtotime( $end_date ) );
		} elseif (
			$all_day &&
			$start_date === $end_date
		) {
			// fringe case: start and end date are set the same, no time (all day)
			// example: June 3, 2019
			$output .= date_i18n( $formats['date_format'], strtotime( $start_date ) );
		} elseif (
			! $all_day &&
			$start_date === $end_date &&
			$start_time === $end_time
		) {
			// fringe case: start and end time and date are set the same
			// example: June 3, 2019 - 3pm
			$output .= date_i18n( $formats['date_format'], strtotime( $start_date ) );
			$output .= $sep . '<span class="time">' . date_i18n( $formats['time_format'], strtotime( $start_time ) ) . '</span>';
		} elseif (
			! $all_day &&
			$start_date !== $end_date &&
			! empty( $end_date ) &&
			! empty( $start_time ) &&
			empty( $end_time )
		) {
			// fringe case: different start and end date, only start time is set
			// example: June 20 — July 4, 2019 - 3pm
			$output .= date_i18n( $formats['date_format_multi_start'], strtotime( $start_date ) );
			$output .= $range;
			$output .= date_i18n( $formats['date_format_multi_end'], strtotime( $end_date ) );
			$output .= $sep . '<span class="time">' . date_i18n( $formats['time_format'], strtotime( $start_time ) ) . '</span>';
		} elseif (
			// start and end date are set the same, all day not checked
			(
				! $all_day &&
				$start_date === $end_date
			) ||
			// OR start and end time are both set, no end date specified
			(
				! $all_day &&
				! empty( $start_date ) &&
				empty( $end_date ) &&
				! empty( $start_time ) &&
				! empty( $end_time )
			)
		) {
			// start and end date the same; range of times
			// example: July 4, 2019 - 3pm–5pm
			$output .= date_i18n( $formats['date_format_multi_start'], strtotime( $start_date ) );
			$output .= $sep;
			$output .= '<span class="time">' . date_i18n( $formats['time_format'], strtotime( $start_time ) );
			$output .= $range;
			$output .= date_i18n( $formats['time_format'], strtotime( $end_time ) ) . '</span>';
		} elseif (
			! $all_day &&
			$start_date !== $end_date
		) {
			// range of dates and times
			// example: // example: Jun 20 at 3pm — Jun 21 at 5pm
			$output .= date_i18n( $formats['date_format_multi_start'], strtotime( $start_date ) ) . $sep2 . '<span class="time">' . $start_time . '</span> ';
			$output .= $range . ' ' . date_i18n( $formats['date_format_multi_end'], strtotime( $end_date ) ) . $sep2 . '<span class="time">' . $end_time . '</span>';
		}
	}
	return $output;
}
function squarecandy_acf_events_date_display( $event ) {
	echo get_squarecandy_acf_events_date_display( $event );
}

// Function to get an address block output from the individual location fields
// $style options are: 1line, 2line, 3line, infowindow, citystate
function get_squarecandy_acf_events_address_display( $event, $style = '2line', $maplink = true ) {

	$home_country = get_option( 'options_home_country' );

	$output = '<div class="venue venue-' . $style . '" itemprop="location" itemscope="" itemtype="http://schema.org/MusicVenue">';
	if ( ! empty( $event['venue'] ) ) {
		// link the venue name to the venue website, unless this is the map popup
		if ( ! empty( $event['venue_link'] ) && 'infowindow' !== $style ) {
			$output .= '<a href="' . $event['venue_link'] . '" itemprop="url">';
		}
		// link the venue name to the full version of google maps in the map popup style
		if ( ! empty( $event['venue_location'] ) && ! empty( $event['venue_location']['address'] ) && 'infowindow' === $style ) {
			$output .= '<a href="https://www.google.com/maps/search/' . rawurlencode( $event['venue_location']['address'] ) . '"><strong>';
		}

			$output .= '<span itemprop="name">' . $event['venue'] . '</span> ';

		if ( ! empty( $event['venue_link'] ) && 'infowindow' !== $style ) {
			$output .= '</a> ';
		}
		if ( ! empty( $event['venue_location'] ) && ! empty( $event['venue_location']['address'] ) && 'infowindow' === $style ) {
			$output .= '</strong></a><br> ';
		}

		if ( '1line' === $style ) {
			$output .= ', ';
		}
		if ( '2line' === $style || '3line' === $style ) {
			$output .= '<br>';
		}
	}
	switch ( $style ) {
		case '1line':
		case '2line':
		case '3line':
			if ( ! empty( $event['address'] ) ) {
				$output .= '<span class="address">' . $event['address'] . '</span>';
			}
			if ( ! empty( $event['address'] ) && ! empty( $event['city'] ) ) {
				if ( '1line' === $style || '2line' === $style ) {
					$output .= ', ';
				}
				if ( '3line' === $style ) {
					$output .= '<br>';
				}
			}
			if ( ! empty( $event['city'] ) ) {
				$output .= '<span class="city">' . $event['city'] . '</span>';
			}
			if ( ! empty( $event['city'] ) && ! empty( $event['state'] ) ) {
				$output .= ', ';
			}
			if ( ! empty( $event['state'] ) ) {
				$output .= '<span class="state">' . $event['state'] . '</span>';
			}
			if ( ! empty( $event['zip'] ) ) {
				$output .= ' <span class="zip">' . $event['zip'] . '</span>';
			}
			if ( ! empty( $event['country'] ) && $home_country !== $event['country'] ) {
				if ( ! empty( $event['address'] ) || ! empty( $event['city'] ) || ! empty( $event['state'] ) || ! empty( $event['zip'] ) ) {
					$output .= ', ';
				}
				$output .= '<span class="country">' . $event['country'] . '</span>';
			}
			if ( ! empty( $event['venue_location'] ) && ! empty( $event['venue_location']['address'] ) ) {
				if ( $maplink && get_option( 'options_map_link' ) ) {
					$output .= '<a class="button small button-gray button-map" href="https://www.google.com/maps/search/' . rawurlencode( $event['venue_location']['address'] ) . '">';
					$output .= '<i class="fa fa-map"></i> ' . __( 'map', 'squarecandy-acf-events' );
					$output .= '</a>';
				}
				$output .= '<meta itemprop="address" content="' . $event['venue_location']['address'] . '">';
			}
			break;

		case 'infowindow':
			// for use with google maps
			if ( ! empty( $event['address'] ) ) {
				$output .= '<span class="address">' . $event['address'] . '</span><br>';
			}
			if ( ! empty( $event['city'] ) ) {
				$output .= '<span class="city">' . $event['city'] . '</span>';
			}
			if ( ! empty( $event['city'] ) && ! empty( $event['state'] ) ) {
				$output .= ', ';
			}
			if ( ! empty( $event['state'] ) ) {
				$output .= '<span class="state">' . $event['state'] . '</span>';
			}
			if ( ! empty( $event['zip'] ) ) {
				$output .= ' <span class="zip">' . $event['zip'] . '</span>';
			}
			if ( ! empty( $event['country'] ) && $home_country !== $event['country'] ) {
				$output .= ', <span class="country">' . $event['country'] . '</span>';
			}
			break;

		case 'citystate':
		default:
			// for short display
			if ( ! empty( $event['city'] ) ) {
				$output .= '<span class="city">' . $event['city'] . '</span>';
			}
			if ( $home_country && $event['country'] !== $home_country ) {
				if ( ! empty( $event['city'] ) ) {
					$output .= ', ';
				}
				$output .= '<span class="country">' . $event['country'] . '</span>';
			} elseif ( ! empty( $event['state'] ) ) {
				if ( ! empty( $event['city'] ) ) {
					$output .= ', ';
				}
				$output .= '<span class="state">' . $event['state'] . '</span>';
			}
			break;

	}
	$output .= '</div>';
	return $output;
}
function squarecandy_acf_events_address_display( $event, $style = '2line', $maplink = true ) {
	echo get_squarecandy_acf_events_address_display( $event, $style, $maplink );
}


if ( function_exists( 'acf_add_options_sub_page' ) ) {
	acf_add_options_sub_page(
		array(
			'title'      => 'Event Settings',
			'parent'     => 'edit.php?post_type=event',
			'capability' => 'manage_options',
		)
	);
}

// Make sure we have a valid google maps api key
add_filter(
	'acf/settings/google_api_key',
	function () {
		return get_option( 'options_google_maps_api_key' );
	}
);


// functions to cleanup date/time data on save, etc
require ACF_EVENTS_DIR_PATH . 'inc/data-cleanup.php';

// run the bulk update if it has not been done yet.
if ( ! get_transient( 'squarecandy_event_cleanup_complete5' ) ) {
	// Bulk Update Script
	function squarecandy_acf_events_bulk_update_enqueue() {
		wp_enqueue_script( 'squarecandy-acf-events-bluk-update-js', ACF_EVENTS_URL . 'dist/js/bulk-update.min.js', array( 'jquery' ), ACF_EVENTS_VERSION, true );
		$nonce = wp_create_nonce( 'squarecandy_event_bulk_update' );
		wp_localize_script(
			'squarecandy-acf-events-bluk-update-js',
			'sqcdy_event_bulk_update_ajax_obj',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => $nonce,
			)
		);
	}
	add_action( 'admin_enqueue_scripts', 'squarecandy_acf_events_bulk_update_enqueue' );
	require ACF_EVENTS_DIR_PATH . 'inc/bulk-update.php';
}



// for debugging
if ( ! function_exists( 'pre_r' ) ) :
	function pre_r( $array ) {
		if ( WP_DEBUG ) {
			print '<pre class="squarecandy-pre-r">';
			print_r( $array ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions
			print '</pre>';
		}
	}
endif;


