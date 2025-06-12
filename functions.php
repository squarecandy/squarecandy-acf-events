<?php
// main plugin file

define( 'ACF_EVENTS_DIR_PATH', plugin_dir_path( __FILE__ ) );
define( 'ACF_EVENTS_URL', plugin_dir_url( __FILE__ ) );
define( 'ACF_EVENTS_VERSION', 'version-1.10.0-dev.4' );

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

	if ( sqcdy_is_views2( 'events' ) ) {
		wp_enqueue_style( 'squarecandy-acf-events-css', ACF_EVENTS_URL . 'dist/css/views2.min.css', false, ACF_EVENTS_VERSION );
	} else {
		wp_enqueue_style( 'squarecandy-fontawesome', ACF_EVENTS_URL . 'dist/css/vendor/font-awesome/css/font-awesome.min.css', false, ACF_EVENTS_VERSION );
		wp_enqueue_style( 'squarecandy-acf-events-css', ACF_EVENTS_URL . 'dist/css/main.min.css', false, ACF_EVENTS_VERSION );
	}

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

// squarecandy-common files
require ACF_EVENTS_DIR_PATH . '/inc/sqcdy-common.php';
require ACF_EVENTS_DIR_PATH . '/inc/sqcdy-plugin.php';

// add ACF fields for events
require ACF_EVENTS_DIR_PATH . 'inc/acf.php';

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
	// @TODO implement Gamajo template loader to match other plugins
	if ( 'event' === get_post_type( $post_id ) && is_archive() ) {
		// Check if a custom template exists in the theme folder, if not, load the plugin template file
		if ( locate_template( array( 'archive-events.php' ) ) ) {
			$file = locate_template( array( 'archive-events.php' ) );
		} elseif ( locate_template( array( 'archive-event.php' ) ) ) {
			$file = locate_template( array( 'archive-event.php' ) );
		} else {
			$file = ACF_EVENTS_DIR_PATH . '/templates/archive-events.php';
		}

		// allow other plugins to override via the squarecandy_acf_events_archive_template filter
		$template = apply_filters( 'squarecandy_acf_events_archive_template', $file );

	}
	return $template;
}

/**
 * Take event (as array) and create formatted date string
 *
 * @param array $event [ 'multi_day', 'all_day', 'start_date', 'end_date', 'start_time', 'end_time' ]
 * @param string $compact
 *
 * @return string formatted date
 */
function get_squarecandy_acf_events_date_display( $event, $compact = null ) {

	// timedate formats and separators
	$formats         = get_field( 'date_formats', 'option' );
	$default_formats = squarecandy_events_default_date_formats();

	foreach ( array_keys( $default_formats ) as $format ) {
		if ( ! isset( $formats[ $format ] ) ) {
			$formats[ $format ] = $default_formats[ $format ];
		}
	}

	$sep   = '<span class="datetime-sep">' . $formats['datetime_sep'] . '</span>';
	$sep2  = '<span class="datetime-sep">' . $formats['datetime_sep2'] . '</span>';
	$range = '<span class="datetime-range">' . $formats['datetime_range'] . '</span>';

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
			$output .= date_i18n( $formats['date_format'], strtotime( $start_date ) );
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

/**
 * Function to get an address block output from the individual location fields
 * @param array $event
 * @param string $style
 * @param bool $maplink
 *
 * $style options are: '1line', '2line', '3line', 'infowindow', 'citystate'
 * $event properties:
 * 'venue', 'venue_link', 'venue_location', 'address', 'city', 'state', 'zip', 'country'
 */
function get_squarecandy_acf_events_address_display( $event, $style = '2line', $maplink = true ) {

	// set default (empty) values so we don't have to worry about array values not being set
	$event = wp_parse_args(
		$event,
		array(
			'venue'          => '',
			'venue_link'     => '',
			'venue_location' => array(
				'address' => '', // nested doesn't work if $event['venue_location'] is set but empty
			),
			'address'        => '',
			'city'           => '',
			'state'          => '',
			'zip'            => '',
			'country'        => '',
		)
	);

	$event['venue_location'] = $event['venue_location'] ? $event['venue_location'] : array( 'address' => '' );

	$home_country = get_option( 'options_home_country' );

	$output = '<div class="venue venue-' . $style . '" itemprop="location" itemscope="" itemtype="http://schema.org/MusicVenue">';
	if ( $event['venue'] ) {

		$is_map_popup = 'infowindow' === $style;

		// link the venue name to the venue website, unless this is the map popup
		// @TODO - strip out all 'infowindow' stuff if we fully kill the map feature
		if ( $event['venue_link'] && ! $is_map_popup ) {
			$output .= '<a href="' . $event['venue_link'] . '" itemprop="url">';
		}

		// link the venue name to the full version of google maps in the map popup style
		if ( $event['venue_location']['address'] && $is_map_popup ) {
			$output .= '<a href="https://www.google.com/maps/search/' . rawurlencode( $event['venue_location']['address'] ) . '"><strong>';
		}

		$output .= '<span itemprop="name">' . $event['venue'] . '</span> ';

		// close link for venue website
		if ( $event['venue_link'] && ! $is_map_popup ) {
			$output .= '</a> ';
		}

		// close link for full version of google maps
		if ( $event['venue_location']['address'] && $is_map_popup ) {
			$output .= '</strong></a><br> ';
		}

		if ( '1line' === $style || 'citystate' === $style ) {
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
			if ( $event['address'] ) {
				$output .= '<span class="address">' . $event['address'] . '</span>';
			}
			if ( $event['address'] && $event['city'] ) {
				if ( '1line' === $style || '2line' === $style ) {
					$output .= ', ';
				}
				if ( '3line' === $style ) {
					$output .= '<br>';
				}
			}
			if ( $event['city'] ) {
				$output .= '<span class="city">' . $event['city'] . '</span>';
			}
			if ( $event['city'] && $event['state'] ) {
				$output .= ', ';
			}
			if ( $event['state'] ) {
				$output .= '<span class="state">' . $event['state'] . '</span>';
			}
			if ( $event['zip'] ) {
				$output .= ' <span class="zip">' . $event['zip'] . '</span>';
			}
			if ( $event['country'] && $home_country !== $event['country'] ) {
				if ( ! empty( $event['address'] ) || ! empty( $event['city'] ) || ! empty( $event['state'] ) || ! empty( $event['zip'] ) ) {
					$output .= ', ';
				}
				$output .= '<span class="country">' . $event['country'] . '</span>';
			}

			$map_location = '';
			if ( $maplink && get_option( 'options_map_link' ) ) :
				// to get full adress: if google map field exists, use that, otherwise try to concatenate address fields
				if ( $event['venue_location']['address'] ) :
					$map_location = $event['venue_location']['address'];
				elseif ( $event['address'] && ( ( $event['city'] && $event['state'] ) || ( $event['city'] && $event['country'] ) || $event['zip'] ) ) :
					$address_fields = array( 'city', 'state', 'zip', 'country' );
					$map_location   = $event['address'];
					foreach ( $address_fields as $address_field ) {
						if ( $event[ $address_field ] ) :
							$map_location .= ' ' . $event[ $address_field ];
							$map_location .= 'city' === $address_field ? ',' : '';
						endif;
					}
				endif;
				$map_link = $map_location ? 'https://www.google.com/maps/search/' . rawurlencode( $map_location ) : '';
				$map_link = apply_filters( 'squarecandy_events_map_link', $map_link, $map_location );
				if ( $map_link ) :
					$output .= '<a class="button small button-gray button-map" href="' . $map_link . '">';
					$icon    = sqcdy_is_views2( 'events' ) ? '' : '<i class="fa fa-map"></i> ';
					$icon    = apply_filters( 'squarecandy_events_map_icon', $icon );
					$output .= $icon . __( 'map', 'squarecandy-acf-events' );
					$output .= '</a>';
				endif;
			endif;
			if ( $map_location ) :
				$output .= '<meta itemprop="address" content="' . $map_location . '">';
			endif;

			break;

		case 'infowindow':
			// for use with google maps
			if ( $event['address'] ) {
				$output .= '<span class="address">' . $event['address'] . '</span><br>';
			}
			if ( $event['city'] ) {
				$output .= '<span class="city">' . $event['city'] . '</span>';
			}
			if ( $event['city'] && $event['state'] ) {
				$output .= ', ';
			}
			if ( $event['state'] ) {
				$output .= '<span class="state">' . $event['state'] . '</span>';
			}
			if ( $event['zip'] ) {
				$output .= ' <span class="zip">' . $event['zip'] . '</span>';
			}
			if ( $event['country'] && $home_country !== $event['country'] ) {
				$output .= ', <span class="country">' . $event['country'] . '</span>';
			}
			break;

		case 'citystate':
		default:
			// for short display
			if ( $event['city'] ) {
				$output .= '<span class="city">' . $event['city'] . '</span>';
			}
			if ( $home_country && $event['country'] !== $home_country ) {
				if ( ! empty( $event['city'] ) ) {
					$output .= ', ';
				}
				$output .= '<span class="country">' . $event['country'] . '</span>';
			} elseif ( $event['state'] ) {
				if ( $event['city'] ) {
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


function squarecandy_events_default_date_formats() {
	$default_date_formats = array(
		'date_format'                     => 'l, F j, Y',
		'date_format_multi_start'         => 'F j',
		'date_format_multi_end'           => 'F j, Y',
		'date_format_compact'             => 'D, M j',
		'date_format_compact_multi_start' => 'M j',
		'date_format_compact_multi_end'   => 'M j, Y',
		'time_format'                     => 'g:ia',
		'datetime_sep'                    => ' – ',
		'datetime_sep2'                   => ', ',
		'datetime_range'                  => '–',
	);
	return $default_date_formats;
}

$subpage = array(
	'title'      => 'Event Settings',
	'parent'     => 'edit.php?post_type=event',
	'capability' => 'manage_options',
);
squarecandy_add_options_page( $subpage, true );


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
if ( ! get_transient( 'squarecandy_event_cleanup_complete5' ) && ! sqcdy_is_views2( 'events' ) ) {
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

/**
 * Add sortable start_date column to admin event list, sort descending by default
 */

add_filter( 'manage_event_posts_columns', 'squarecandy_events_filter_posts_columns' );
function squarecandy_events_filter_posts_columns( $columns ) {
	//add our column
	$columns['event_date'] = __( 'Event Date' );
	//rearrange so it's before author & date
	$move_columns = array( 'author', 'date' );
	foreach ( $move_columns as $key ) {
		if ( isset( $columns[ $key ] ) ) {
			$column = $columns[ $key ];
			unset( $columns[ $key ] );
			$columns[ $key ] = $column;
		}
	}
	return $columns;
}

add_action( 'manage_event_posts_custom_column', 'squarecandy_events_column', 10, 2 );
function squarecandy_events_column( $column, $post_id ) {
	if ( 'event_date' === $column ) {
		echo get_field( 'start_date', $post_id );
	}
}

add_filter( 'manage_edit-event_sortable_columns', 'squarecandy_events_sortable_columns' );
function squarecandy_events_sortable_columns( $columns ) {
	$columns['event_date'] = 'sort_event_date';
	return $columns;
}

add_action( 'pre_get_posts', 'squarecandy_events_edit_event_orderby' );
function squarecandy_events_edit_event_orderby( $query ) {
	if ( ! is_admin() || ! $query->is_main_query() ) {
		return;
	}
	$screen = get_current_screen();
	if ( isset( $screen->id ) && 'edit-event' === $screen->id ) :

		$orderby = $query->get( 'orderby' );

		if ( 'sort_event_date' === $orderby ) {

			// if we're manually sorting by the column:
			$query->set( 'orderby', 'meta_value' );
			$query->set( 'meta_key', 'start_date' );
			$query->set( 'meta_type', 'date' );

		} elseif ( ! $orderby ) {
			// if there's no manual sort set, sort by date descending:
			$query->set( 'orderby', 'meta_value' );
			$query->set( 'meta_key', 'start_date' );
			$query->set( 'meta_type', 'date' );
			$query->set( 'order', 'DESC' );
		}

	endif;
}

// WP All Import events import compatibility
// This runs the function that creates the values for sort_date magic_sort_date and archive_date for each event imported
//
function squarecandy_events_pmxi_saved_post( $post_id, $xml_node, $is_update ) {
	if ( 'event' === get_post_type( $post_id ) ) {
		if ( function_exists( 'squarecandy_cleanup_event_data' ) ) {
			$cleanup = squarecandy_cleanup_event_data( $post_id );
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				if ( $cleanup ) {
					error_log( 'squarecandy_cleanup_event_data ran successfully! ' . $post_id ); // phpcs:ignore
				} else {
					error_log( 'squarecandy_cleanup_event_data failed. ' . $post_id ); // phpcs:ignore
				}
			}
		}
	}
}
add_action( 'pmxi_saved_post', 'squarecandy_events_pmxi_saved_post', 999, 3 );

/**
 * Generate and echo/return html to display buttons for an event
 * @param array $event
 * @param bool $show_post_link_button
 * @param bool $echo
 * @return string $output OR echos string $output
 *
 * button types / $event properties:
 * 'tickets_link', 'more_info_link', 'facebook_link', 'add_to_gcal'
 */
function squarecandy_events_generate_buttons( $event, $show_post_link_button = false, $echo = true ) {

	$output = '';
	$single = $echo;

	$now       = date_i18n( 'Y-m-d H:i:s', strtotime( 'now' ) );
	$is_future = ! empty( $event['archive_date'] ) && $event['archive_date'] > $now;

	$show_tickets_link = get_option( 'options_show_tickets_link' );

	if (
		( ! empty( $event['tickets_link'] ) && 'future' === $show_tickets_link && $is_future ) ||
		( ! empty( $event['tickets_link'] ) && empty( $show_tickets_link ) )
	) :
		$output .= '<span itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">';
		$output .= '<a class="button button-bold button-tickets" itemprop="url" href="' . $event['tickets_link'] . '">';
		$icon    = sqcdy_is_views2( 'events' ) ? '' : '<i class="fa fa-ticket"></i> ';
		$icon    = apply_filters( 'squarecandy_events_ticket_icon', $icon );
		$output .= $icon . __( 'Tickets', 'squarecandy-acf-events' );
		$output .= '</a>';
		$output .= '</span>';
	endif;
	if ( $show_post_link_button ) :
		$moreinfo_post_link_text = __( 'More Info', 'squarecandy-acf-events' );
		$moreinfo_post_link_text = apply_filters( 'squarecandy_filter_events_moreinfo_post_link_text', $moreinfo_post_link_text );
		$output                 .= '<a class="button button-bold button-more-info" href="' . $show_post_link_button . '">
				<i class="fa fa-info-circle"></i> ' . $moreinfo_post_link_text . '
			</a> ';
	elseif ( ! empty( $event['more_info_link'] ) ) :
		$moreinfo_external_link_text = apply_filters( 'squarecandy_filter_events_moreinfo_external_link_text', __( 'More Info', 'squarecandy-acf-events' ) );
		$output                     .= '<a class="button button-bold button-more-info" href="' . $event['more_info_link'] . '">';
		$output                     .= '<i class="fa fa-info-circle"></i> ' . $moreinfo_external_link_text;
		$output                     .= '</a>';
	endif;
	if ( ! empty( $event['facebook_link'] ) ) :
		$output .= '<a class="button button-bold button-facebook" href="' . $event['facebook_link'] . '">';
		$output .= '<i class="fa fa-facebook"></i> ';
		$output .= ! $single ? '<span class="screen-reader-text">' : ''; //shortcode inconsistently wraps this text
		$output .= __( 'Facebook', 'squarecandy-acf-events' );
		$output .= ! $single ? '</span>' : '';
		$output .= '</a>';
	endif;

	if ( ! empty( $event['more_info_buttons'] ) && is_array( $event['more_info_buttons'] ) ) :
		foreach ( $event['more_info_buttons'] as $button ) :
			$button_text = $button['button_text'] ?? '';
			$button_link = $button['link'] ?? '';
			$button_icon = $button['icon'] ?? '';
			$button_icon = apply_filters( 'squarecandy_events_more_info_button_icon', $button_icon );
			if ( $button_text && $button_link ) :
				$output .= '<a class="button button-events-more-info" href="' . $button_link . '">';
				$output .= $button_icon ? '<span>' . $button_icon . '</span>' : '';
				$output .= '<span>' . $button_text . '</span>';
				$output .= '</a>';
			endif;
		endforeach;
	endif;

	$add_to_gcal = get_option( 'options_add_to_gcal' );
	if (
		( $add_to_gcal && 'future' === $add_to_gcal && $is_future ) ||
		( $add_to_gcal && 1 === $add_to_gcal )
	) :
		$output .= squarecandy_add_to_calendar( $event );
	endif;

	if ( $echo ) {
		echo $output;
	} else {
		return $output;
	}
}

function squarecandy_add_to_calendar( $event ) {

	if ( ! is_array( $event ) || empty( $event['start_date'] ) ) {
		return;
	}

	$start_date = $event['start_date'];
	$end_date   = $event['end_date'] ?? false;
	$multi_day  = $event['muilti_day'] ?? false;

	if ( ! empty( $event['start_time'] ) ) {
		$start_date .= ' ' . $event['start_time'];
	}

	if ( $multi_day && $end_date && isset( $event['end_time'] ) ) {
		$end_date .= ' ' . $event['end_time'];
	}

	$event_address = get_squarecandy_acf_events_address_display( $event, '1line', false );
	$event_address = wp_strip_all_tags( $event_address );

	$event_title = get_the_title();

	if ( ! sqcdy_is_views2( 'events' ) ) :

		// shortcode also wraps this text & uses different icon
		$linktext = $single ? '<i class="fa fa-google"></i> add to gCal' : '<i class="fa fa-google-plus"></i><span class="screen-reader-text">' . __( 'add to google calendar', 'squarecandy-acf-events' ) . '</span>';

		return squarecandy_add_to_gcal(
			$event_title,
			$start_date,
			$end_date,
			$event['short_description'] ?? '',
			$event_address,
			$event['all_day'] ?? false,
			$linktext,
			array( 'gcal-button', 'button', 'button-bold' )
		);

	else :

		// https://calndr.link/api-docs
		// additional valid values: 'yahoo', 'outlookcom'
		$services = array(
			'google'    => 'Google',
			'apple'     => 'Apple',
			'outlook'   => 'Outlook',
			'office365' => 'Office 365',
		);

		// filter services
		$services = apply_filters( 'squarecandy_add_to_calendar_services', $services );

		$output  = '<div class="squarecandy-add-to-calendar">';
		$output .= '<span class="label add-to-calendar-label">' . __( 'Add to Calendar:', 'squarecandy-acf-events' ) . '</span>';

		// timezone
		// use WordPress timezone
		// $timezone = get_option( 'timezone_string' );
		// @TODO - add per-event timezone option

		// convert start date to ISO format
		$start_date = date_i18n( 'c', strtotime( $start_date ) );

		// convert end date to ISO format
		if ( ! empty( $end_date ) && $multi_day ) {
			$end_date = date_i18n( 'c', strtotime( $end_date ) );
		}

		$url_params  = '&title=' . rawurlencode( $event_title );
		$url_params .= '&start=' . rawurlencode( $start_date );
		if ( ! empty( $end_date ) ) {
			$url_params .= '&end=' . rawurlencode( $end_date );
		}
		if ( ! empty( $event['short_description'] ) ) {
			$url_params .= '&description=' . rawurlencode( $event['short_description'] );
		}
		if ( ! empty( $event_address ) ) {
			$url_params .= '&location=' . rawurlencode( $event_address );
		}
		if ( ! empty( $event['all_day'] ) ) {
			$url_params .= '&all_day=true';
		}

		// @TODO add timezone support
		// $url_params .= '&timezone=' . rawurlencode( $timezone );

		foreach ( $services as $service => $name ) {
			$url     = 'https://calndr.link/d/event/?service=' . $service . $url_params;
			$output .= '<a href="' . esc_url( $url ) . '" class="add-to-calendar-link">' . esc_html( $name ) . '</a>';
		}

		$output .= '</div>';
		return $output;
	endif;
}

if ( ! class_exists( 'Gamajo_Template_Loader' ) ) {
	require ACF_EVENTS_DIR_PATH . 'inc/class-gamajo-template-loader.php';
}
require ACF_EVENTS_DIR_PATH . 'inc/class-squarecandy-events-template-loader.php';
