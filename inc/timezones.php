<?php

/**
 * Format & sort raw timezone identifiers
 *    Modeled on wp_timezone_choice() in wp-includes/functions.php
 *
 * @param array $tz_identifiers - raw timezone codes as output by timezone_identifiers_list()
 * @param bool|string $single_continent - if not false, will group all timezones into one "continent" and if a string, use the param value as the name of the continent
 * @param array $overwrite_continent - array of original continet names and their replacements, e.g. array( 'America' => 'Americas' )
 *
 * @return array
 */
function squarecandy_build_timezone_options( $tz_identifiers, $single_continent = false, $overwrite_continent = array() ) {

	$continents        = array( 'America', 'Europe', 'Africa', 'Asia', 'Atlantic', 'Australia', 'Indian', 'Pacific', 'Antarctica', 'Arctic' );
	$timezone_array    = array();
	$select2_options   = array();
	$output            = array();
	$us_main_timezones = array(
		'America/New_York'    => 'Eastern',
		'America/Chicago'     => 'Central',
		'America/Denver'      => 'Mountain',
		'America/Phoenix'     => 'Mountain no DST',
		'America/Los_Angeles' => 'Pacific',
		'America/Anchorage'   => 'Alaska',
		'America/Adak'        => 'Hawaii',
		'Pacific/Honolulu'    => 'Hawaii no DST',
	);

	// get all abbreviations
	$all_abbreviations   = timezone_abbreviations_list();
	$abbreviation_lookup = array();
	foreach ( $all_abbreviations as $abbreviation => $timezones_for_abbrev ) {
		foreach ( $timezones_for_abbrev as $at ) {
			$atz_id = $at['timezone_id'];
			$abbrev = $abbreviation;
			// only use non daytime savings abbrevations
			if ( ! $at['dst'] ) {
				if ( isset( $abbreviation_lookup[ $atz_id ] ) ) {
					$abbreviation_lookup[ $atz_id ][] = $abbrev;
				} else {
					$abbreviation_lookup[ $atz_id ] = array( $abbrev );
				}
			}
		}
	}

	// loop through and calculate country & display fields
	foreach ( $tz_identifiers as $tzone ) {

		$zone = explode( '/', str_replace( '_', ' ', $tzone ) );

		if ( ! in_array( $zone[0], $continents, true ) || ! $zone[0] || ! isset( $zone[1] ) ) {
			continue; // UTC
		}

		$zone_info = array(
			'timezone'  => $tzone,
			'continent' => ( isset( $zone[0] ) && $zone[0] ? $zone[0] : '' ),
			'city'      => ( isset( $zone[1] ) && $zone[1] ? $zone[1] : '' ),
			'subcity'   => ( isset( $zone[2] ) && $zone[2] ? $zone[2] : '' ),
		);

		// get country & abbreviation
		$tz                   = new DateTimeZone( $tzone );
		$location             = $tz->getLocation();
		$zone_info['country'] = class_exists( 'Locale' )
			? Locale::getDisplayRegion( '-' . $location['country_code'], 'en' )
			: $location['country_code'];
		$dt                   = new DateTime( 'now', $tz );
		$abbreviation         = $dt->format( 'T' );
		// for non-numeric (i.e. offset) abbreviations, use the first matching non-DST abbreviation
		if ( ! is_numeric( $abbreviation ) && isset( $abbreviation_lookup[ $tzone ][0] ) ) {
			$abbreviation = strtoupper( $abbreviation_lookup[ $tzone ][0] );
		}

		// Allow overwriting the names of the continents
		if ( $single_continent && is_string( $single_continent ) ) {
			$zone_info['continent'] = $single_continent;
		} elseif ( in_array( $zone_info['continent'], array_keys( $overwrite_continent ), true ) ) {
			$zone_info['continent'] = $overwrite_continent[ $zone_info['continent'] ];
		}

		// set up the display
		$zone_info['display'] = $zone_info['city'];

		if ( ! empty( $zone_info['subcity'] ) ) {
			// maybe add subcity to the display.
			$zone_info['display'] .= ' - ' . $zone_info['subcity'];
		}

		// maybe add country to the display.
		// phpcs:ignore PHPCompatibility.FunctionUse.NewFunctions.str_containsFound
		if ( ! $single_continent && $zone_info['country'] && ! str_contains( $zone_info['display'], $zone_info['country'] ) && ! str_contains( $zone_info['continent'], $zone_info['country'] ) ) {
			$zone_info['display'] .= ', ' . $zone_info['country'];
		} elseif ( $single_continent ) {

			$pretty_timezone_name = '';

			if ( isset( $us_main_timezones[ $tzone ] ) ) {
				$pretty_timezone_name = $us_main_timezones[ $tzone ];
			} else {
				$exploded_comments    = explode( ' ', $location['comments'] );
				$pretty_timezone_name = $exploded_comments[0] && $exploded_comments[0] !== $abbreviation ? $exploded_comments[0] : '';
			}

			if ( $pretty_timezone_name ) {
				$zone_info['display'] .= ' / ' . $pretty_timezone_name;
			}
		}

		// add abbreviation
		$zone_info['display'] .= ' (' . $abbreviation . ')';

		$timezone_array[ $zone_info['continent'] ][ $tzone ] = $zone_info;
	}

	// loop through again, maybe sort based on country, build options array
	foreach ( $timezone_array as $continent => $timezones ) {

		// sort by country or if USA, sort main timezones to top
		if ( ! $single_continent ) {

			$timezones = wp_list_sort( $timezones, 'country' );

		} elseif ( 'USA' === $continent ) {

			$first_timezones = array();
			foreach ( $us_main_timezones as $us_main_timezone => $info ) {
				if ( isset( $timezones[ $us_main_timezone ] ) ) {
					$first_timezones[ $us_main_timezone ] = $timezones[ $us_main_timezone ];
					unset( $timezones[ $us_main_timezone ] );
				}
			}
			$timezones = array_merge( $first_timezones, $timezones );
		}

		foreach ( $timezones as $zone_info ) {

			$display   = esc_html( $zone_info['display'] );
			$continent = $zone_info['continent'];
			// if there's not already an optgroup for this continent, add it
			if ( ! isset( $select2_options[ $continent ] ) ) {
				$select2_options[ $continent ] = array();
			}
			$select2_options[ $continent ][ $zone_info['timezone'] ] = $display;
		}
	}

	// if it's more than one continent, build it so each group is added in the order of continents specified
	if ( count( $select2_options ) > 1 ) {

		foreach ( $continents as $cont ) {
			// allowing us to rename continents
			if ( in_array( $cont, array_keys( $overwrite_continent ), true ) ) {
				$cont = $overwrite_continent[ $cont ];
			}
			$output[ $cont ] = $select2_options[ $cont ];
		}
	} else {
		$output = $select2_options;
	}
	return $output;
}


/**
 * Modeled on wp_timezone_choice() in wp-includes/functions.php
 */
function squarecandy_timezone_choice( $selected_zone = null ) {

	$use_transient = ! sqcdy_is_debug(); // don't use transient when debugging

	$select_options = $use_transient ? get_transient( 'squarecandy-events-timezone-options' ) : false;

	if ( ! $select_options ) :

		$all_tz_identifiers         = timezone_identifiers_list();
		$first_timezone_identifiers = apply_filters( 'squarecandy_timezone_options_first_timezones', array() );
		$sort_popular_first         = apply_filters( 'squarecandy_events_sort_popular_first', true );

		if ( $first_timezone_identifiers ) :
			$non_first_timezone_identifiers = array_diff( $all_tz_identifiers, $first_timezone_identifiers );
			$first_timezones_title          = apply_filters( 'squarecandy_timezone_options_first_timezones_title', 'USA' );
			// sort the timezones & put non_us after US
			$sorted_first_timezones     = squarecandy_build_timezone_options( $first_timezone_identifiers, $first_timezones_title );
			$sorted_non_first_timezones = squarecandy_build_timezone_options( $non_first_timezone_identifiers, false, array( 'America' => 'Americas' ) );
			$select_options             = array_merge( $sorted_first_timezones, $sorted_non_first_timezones );
		else :
			$select_options = squarecandy_build_timezone_options( $all_tz_identifiers, false, array( 'America' => 'Americas' ) );
		endif;

		// add UTC to the list
		$select_options['UTC'] = array( 'UTC' => 'UTC' );

		if ( $sort_popular_first ) :
			// get most used timezones from the db
			global $wpdb;
			$popular_timezones = $wpdb->get_col( "SELECT `meta_value` FROM $wpdb->postmeta WHERE `meta_key` = 'timezone' AND `meta_value` != '' GROUP BY `meta_value` ORDER BY count(`post_id`) DESC, `meta_value` ASC LIMIT 5" );
			$popular_options   = array();
			$popular_count     = count( $popular_timezones );
			$wp_timezone       = wp_timezone_string();
			$popular_timezones = $popular_count ? $popular_timezones : array( $wp_timezone );
			$popular_title     = 'Most Used';

			// get info for most used timezones
			foreach ( $select_options as $continent => $timezones ) {
				foreach ( $timezones as $tz => $tz_display ) {
					if ( in_array( $tz, $popular_timezones, true ) ) {
						$popular_options[ $tz ] = $tz_display;
						unset( $select_options[ $continent ][ $tz ] ); // remove from the main list, otherwise the selct gets confused
					}
				}
			}

			if ( $popular_count > 1 ) {
				// resort most used timezones to roder from sql query
				uksort(
					$popular_options,
					function( $n ) use ( $popular_timezones ) {
						return array_search( $n, $popular_timezones, true );
					}
				);
			} elseif ( $popular_timezones[0] === $wp_timezone ) {
				// if the only timezone in the list is the default, title it that way
				$popular_title = 'Default';
			}

			// recombine with rest of options
			$select_options = array_merge( array( $popular_title => $popular_options ), $select_options );
		endif;

		if ( $use_transient ) {
			set_transient( 'squarecandy-events-timezone-options', $select_options, 86400 ); // 24 hours
		}

	endif;

	return $select_options;
}


// by default, list US timezones at the top of the timezone list.
add_filter( 'squarecandy_timezone_options_first_timezones', 'squarecandy_timezone_us_timezone_identifiers' );
function squarecandy_timezone_us_timezone_identifiers( $first_timezones ) {
	// can short circuit this
	if ( ! $first_timezones ) {
		$first_timezones = DateTimeZone::listIdentifiers( DateTimeZone::PER_COUNTRY, 'US' );
	}
	return $first_timezones;
}


/**
 * Avoid an edge issue in ACP that throws a warning when trying to set up column sorting, by disabling sort on the Timezone column if added
 * `PHP Warning:  Array to string conversion in .../wp-content/plugins/admin-columns-pro/addons/acf/classes/Sorting/ModelFactory.php on line 106`
 * `106:                 natcasesort($choices);`
 * Their code is expecting $choices will be a simple $array, but we're setting it up as a multidimensional array to get optgroups.
 * This causes errors like above in the logs, and also the value of the field doesn't display in the column.
 * If AC is active, unless we're on a single event edit screen, flatten the options so AC can process them.
 */
function squarecandy_events_timezone_field_choices( $field ) {
	if ( is_admin() && class_exists( 'ACP\AdminColumnsPro' ) ) :
		$screen               = get_current_screen();
		$is_edit_event_single = isset( $screen->base ) && 'post' === $screen->base && isset( $screen->post_type ) && 'event' === $screen->post_type;
		if ( ! $is_edit_event_single ) {
			$field['choices'] = call_user_func_array( 'array_merge', array_values( $field['choices'] ) );
		}
	endif;
	return $field;
}
add_filter( 'acf/load_field/name=timezone', 'squarecandy_events_timezone_field_choices' );
