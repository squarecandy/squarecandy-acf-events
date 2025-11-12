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
		$zone_info['country'] = Locale::getDisplayRegion( '-' . $location['country_code'], 'en' );
		$dt                   = new DateTime( 'now', $tz );
		$abbreviation         = $dt->format( 'T' );

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

	$select_options = get_transient( 'squarecandy-events-timezone-options' );

	if ( ! $select_options ) :

		$sort_us_first      = apply_filters( 'squarecandy_events_sort_us_first', true );
		$all_tz_identifiers = timezone_identifiers_list();

		if ( $sort_us_first ) :
			$us_timezone_identifiers     = DateTimeZone::listIdentifiers( DateTimeZone::PER_COUNTRY, 'US' );
			$non_us_timezone_identifiers = array_diff( $all_tz_identifiers, $us_timezone_identifiers );

			// sort the timezones & put non_us after US
			$sorted_us_timezones     = squarecandy_build_timezone_options( $us_timezone_identifiers, 'USA' );
			$sorted_non_us_timezones = squarecandy_build_timezone_options( $non_us_timezone_identifiers, false, array( 'America' => 'Americas' ) );
			$select_options          = array_merge( $sorted_us_timezones, $sorted_non_us_timezones );
			$select_options['UTC']   = 'UTC';

		endif;

		set_transient( 'squarecandy-events-timezone-options', $select_options, 86400 ); // 24 hours

	endif;

	return $select_options;
}

add_filter( 'acf/load_field/name=tztest', 'squarecandy_tz_load_field' );
function squarecandy_tz_load_field( $field ) {
	$field['choices'] = squarecandy_timezone_choice();
	return $field;
}
