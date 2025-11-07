<?php
// functions for data cleanup

/**
 * Calculate the archive date (archive/take down datetime), sort date,
 * and magic sort date (can be used for sorting by date descending, time ascending) based on start date and time
 * Produces an array of date strings
 *
 * @param array|int $event An event post ID, or an event array that includes id, start_date, start_time and all_day
 * @return array Array of date strings in YYYY-MM-DD HH:MM:SS
 */
function squarecandy_calculate_event_date_values( $event ) {

	// if it's a post ID, grab the fields
	if ( is_int( $event ) ) {
		$event_id    = $event;
		$event       = get_fields( $event );
		$event['id'] = $event_id;
	}

	$output_dates = array(
		'archive_date'    => false,
		'sort_date'       => false,
		'magic_sort_date' => false,
	);

	// bailout if we don't have the data we need
	if ( ! is_array( $event ) || ! isset( $event['start_date'] ) ) {
		return $output_dates;
	}

	// get the values
	$start_date = $event['start_date'];
	$end_date   = $event['end_date'] ?? false;
	$start_time = $event['start_time'] ?? false;
	$end_time   = $event['end_time'] ?? false;
	$all_day    = $event['all_day'] ?? false;
	$timezone   = $event['timezone'] ?? false;

	$system_timezone  = wp_timezone_string();
	$has_timezone     = $timezone && $timezone !== $system_timezone;
	$create_timezone  = $timezone ? new DateTimeZone( $timezone ) : new DateTimeZone( $system_timezone );
	$default_timezone = $has_timezone ? new DateTimeZone( $system_timezone ) : false;

	// get the raw, not acf formatted start_time
	$start_time_meta = isset( $event['start_time_meta'] ) ? $event['start_time_meta'] : get_post_meta( $event['id'], 'start_time', true );

	// calculate archive_date

	$archive_date = false;

	if ( $end_date && $end_time ) {
		// there is an end date and end time set
		$archive_date = $end_date . ' ' . $end_time;
	} elseif ( ! $end_date && $end_time ) {
		// there is no end date set, but there is an end time
		$archive_date = $start_date . ' ' . $end_time;
	} elseif ( ! $end_date && ! $end_time && $start_time ) {
		// there is no end time and no end date and there is a start time specified
		$archive_date = $start_date . ' ' . $start_time;
	} elseif ( $end_date ) {
		// this is all day and there is both start date and end date (multi day, no times)
		// or the unusual circumstance where a start date and time are set, end date but no end time
		// or really - if we didn't hit one of the options above and there is an end_date, this is good.
		$archive_date = $end_date . ' 11:59pm';
	} else {
		// at this point the only option left should be a single day all day event
		// end of the day on the start date is also the final fallback
		// as start date is the only required date/time field
		$archive_date = $start_date . ' 11:59pm';
	}

	$output_dates['archive_date'] = $archive_date;

	// calculate sort_date

	$sort_start_time = $start_time ?? '00:00:00';
	$sort_date       = $start_date . ' ' . $sort_start_time;

	$output_dates['sort_date'] = $sort_date;

	// calculate magic_sort_date

	// check if we have the extra data we need
	if ( $start_time_meta ) {

		//date is in 'F j, Y H:i:s' - make a DateTime, maybe convert to local timezone, then get just the time string from that
		$magic_start_time = $start_time_meta ? $start_time_meta : '00:00:01';
		$magic_sort_date  = squarecandy_create_date_time( "$start_date $magic_start_time", false, $create_timezone, $default_timezone );
		$magic_start_time = $magic_sort_date->format( 'H:i:s' );

		// get just the number of seconds since 12am the day of the event, then *reverse* that so earliest events will belatest etc.
		$seconds_calc    = squarecandy_calculate_seconds_from_time( $magic_start_time );
		$seconds         = $seconds_calc < 1 ? 1 : $seconds_calc;
		$reverse_seconds = 24 * 60 * 60 - $seconds;

		// figure out how that differs from the original so we can adjust the DateTime
		$seconds_interval = $reverse_seconds - $seconds;
		$magic_sort_date->add( DateInterval::createFromDateString( $seconds_interval . ' seconds' ) );
	}

	$output_dates['magic_sort_date'] = $magic_sort_date;

	foreach ( $output_dates as $key => $date ) {
		if ( $date ) {
			if ( 'magic_sort_date' !== $key ) {
				// these are still 'F j, Y g:i a' date strings, not DateTime, convert & maybe apply timezone
				$date = squarecandy_create_date_time( $date, false, $create_timezone, $default_timezone );
			}
			// convert to ISO format for database
			$output_dates[ $key ] = $date->format( 'Y-m-d H:i:s' );
		}
	}

	return $output_dates;

}

function squarecandy_create_date_time( $date_string, $format, $create_timezone, $default_timezone ) {

	if ( $format ) {
		$date_time = date_create_from_format( $format, $date_string, $create_timezone );
	} else {
		// if $format is falsey, try creating a DateTime but catch the exception if it's not a valid date string
		try {
			$date_time = new DateTime( $date_string, $create_timezone );
		} catch ( Exception $e ) {
			return false;
		}
	}
	if ( $default_timezone ) {
		$date_time->setTimeZone( $default_timezone );
	}
		return $date_time;
}

function squarecandy_calculate_seconds_from_time( $time_string ) {
	$split = explode( ':', $time_string );
	if ( count( $split ) !== 3 ) {
		return 0;
	}
	$output = $split[0] * 60 * 60 + $split[1] * 60 + $split[2];
	return $output;
}

/**
 * Cleanup Date Fields on Save
 *
 * @param int $post_id - The Post ID.
 */
function squarecandy_acf_events_acf_save_post( $post_id ) {
	// create the archive datetime and cleanup the other date data
	squarecandy_cleanup_event_data( $post_id );
}
add_action( 'acf/save_post', 'squarecandy_acf_events_acf_save_post', 15 );

/**
 * Clean up event data
 *
 * create the archive datetime and cleanup the other date data
 *
 * @param int $post_id - The Post ID.
 * @return bool $status - true if the process completed, false if this is not an event
 */
function squarecandy_cleanup_event_data( $post_id ) {

	// bail out if post being saved is not an event.
	if ( 'event' !== get_post_type( $post_id ) ) {
		return false;
	}

	// try converting the start_time (if is a timestamp)
	$start_time_meta      = get_post_meta( $post_id, 'start_time', true ); // unformatted
	$converted_start_time = squarecandy_convert_event_time( $start_time_meta );

	if ( $converted_start_time && $start_time_meta !== $converted_start_time ) {
		update_post_meta( $post_id, 'start_time', $converted_start_time );
	}

	$event                    = get_fields( $post_id );
	$event['id']              = $post_id;
	$event['start_time_meta'] = $start_time_meta;

	$date_values = squarecandy_calculate_event_date_values( $event );

	// set the archive date (will make queries much simpler)
	update_post_meta( $post_id, 'archive_date', $date_values['archive_date'] );
	update_post_meta( $post_id, 'sort_date', $date_values['sort_date'] );
	update_post_meta( $post_id, 'magic_sort_date', $date_values['magic_sort_date'] );

	// if the event is not multi day but there is an end date.
	if ( empty( $event['multi_day'] ) && ! empty( $event['end_date'] ) ) {
		update_post_meta( $post_id, 'end_date', '' );
	}

	// if all day checkbox is ticked
	if ( ! empty( $event['all_day'] ) ) {

		// remove start_time value
		if ( ! empty( $event['start_time'] ) ) {
			update_post_meta( $post_id, 'start_time', '' );
		}

		// remove end_time value
		if ( ! empty( $event['end_time'] ) ) {
			update_post_meta( $post_id, 'end_time', '' );
		}
	}
	return true;
}

/**
 * Convert start time from timestamp to G:i:s
 *
 * @param string $start_time - start_time postmeta
 * @return string - start_time (new value if converted, old value if not)
 */
function squarecandy_convert_event_time( $start_time ) {

	// is it in "g:i:s" format?
	preg_match( '/\d{2}:\d{2}:\d{2}/', $start_time, $matches );

	if ( ! count( $matches ) ) {

		//is it a timestamp maybe?
		$time = date_create_from_format( 'U', $start_time, new DateTimeZone( 'UTC' ) ); // should return false if not valid timestamp

		if ( $time ) {
			$start_time = $time->format( 'H:i:s' );
		}
	}

	return $start_time;
}
