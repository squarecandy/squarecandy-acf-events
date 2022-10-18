<?php
// functions for data cleanup


/**
 * Calculate the archive date based on start and end dates
 *
 * @param array|int $event An event post ID, or an event array that includes start_date, end_date, start_time, end_time, multi_date and all_day
 * @return string $date The archive/take down datetime in YYYY-MM-DD HH:MM:SS
 */
function squarecandy_calculate_event_archive_date( $event ) {
	// if it's a post ID, grab the fields
	if ( is_int( $event ) ) {
		$event = get_fields( $event );
	}
	// bailout if we don't have the data we need
	if ( ! is_array( $event ) || ! isset( $event['start_date'] ) ) {
		return false;
	}

	// get the values
	$start_date = $event['start_date'];
	$end_date   = $event['end_date'] ?? false;
	$start_time = $event['start_time'] ?? false;
	$end_time   = $event['end_time'] ?? false;
	$all_day    = $event['all_day'] ?? false;

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

	if ( $archive_date ) {
		// convert to ISO format for database
		$archive_date = date_i18n( 'Y-m-d H:i:s', strtotime( $archive_date ) );
	}

	return $archive_date;
}

/**
 * Calculate the sort date based on start date and time
 *
 * @param array|int $event An event post ID, or an event array that includes start_date, start_time and all_day
 * @return string $date The sort datetime in YYYY-MM-DD HH:MM:SS
 */
function squarecandy_calculate_event_sort_date( $event ) {
	// if it's a post ID, grab the fields
	if ( is_int( $event ) ) {
		$event = get_fields( $event );
	}
	// bailout if we don't have the data we need
	if ( ! is_array( $event ) || ! isset( $event['start_date'] ) ) {
		return false;
	}

	// get the values
	$start_date = $event['start_date'];
	$start_time = $event['start_time'] ?? '00:00:00';
	$sort_date  = $start_date . ' ' . $start_time;

	$sort_date = date_i18n( 'Y-m-d H:i:s', strtotime( $sort_date ) );

	return $sort_date;
}

/**
 * Calculate the magic sort date based on start date and time
 * Produces a single meta field that can be used for sorting by date decending, time ascending
 *
 * @param array|int $event An event post ID, or an event array that includes start_date, start_time and all_day
 * @return string $date The sort datetime in YYYY-MM-DD HH:MM:SS
 */
function squarecandy_calculate_event_magic_sort_date( $event ) {
	// if it's a post ID, grab the fields
	if ( is_int( $event ) ) {
		$event = get_fields( $event );
	}
	// bailout if we don't have the data we need
	if ( ! is_array( $event ) || ! isset( $event['start_date'] ) ) {
		return false;
	}

	// get the values
	$start_date      = $event['start_date'];
	$start_time      = get_post_meta( $post_id, 'start_time', true ) ?: '00:00:01'; // get raw, not acf formatted
	$seconds_calc    = date_create_from_format( 'Y-m-d h:i:s', "1970-01-01 $start_time", new DateTimeZone( 'UTC' ) ); //use create_from_format so we get false if date not valid
	$seconds         = $seconds_calc ? (int) $seconds_calc->getTimestamp() : 1; // avoid error if $seconds_calc is false
	$seconds         = $seconds < 1 ? 1 : $seconds;
	$magic_sort_date = date_i18n( 'Y-m-d H:i:s', strtotime( "$start_date +1 day -$seconds seconds" ) );
	return $magic_sort_date;
}


/**
 * Cleanup Date Fields on Save
 *
 * @param int $post_id - The Post ID.
 */
function squarecandy_acf_events_acf_save_post( $post_id ) {

	// return early if post being saved is not an event.
	if ( 'event' !== get_post_type( $post_id ) ) {
		return;
	}

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
	$start_time_meta      = get_post_meta( $post_id, 'start_time', true );
	$converted_start_time = squarecandy_convert_event_time( $start_time_meta );

	if ( $converted_start_time && $start_time !== $converted_start_time ) {
		update_post_meta( $post_id, 'start_time', $converted_start_time );
	}

	// set the archive date (will make queries much simpler)
	$archive_date = squarecandy_calculate_event_archive_date( $post_id );
	update_post_meta( $post_id, 'archive_date', $archive_date );

	$sort_date = squarecandy_calculate_event_sort_date( $post_id );
	update_post_meta( $post_id, 'sort_date', $sort_date );

	$magic_sort_date = squarecandy_calculate_event_magic_sort_date( $post_id );
	update_post_meta( $post_id, 'magic_sort_date', $magic_sort_date );

	// if the event is not multi day but there is an end date.
	if ( ! get_field( 'multi_day', $post_id ) && get_field( 'end_date', $post_id ) ) {
		update_field( 'end_date', '', $post_id );
	}

	// if all day checkbox is ticked
	if ( get_field( 'all_day', $post_id ) ) {

		// remove start_time value
		if ( get_field( 'start_time', $post_id ) ) {
			update_field( 'start_time', '', $post_id );
		}

		// remove end_time value
		if ( get_field( 'end_time', $post_id ) ) {
			update_field( 'end_time', '', $post_id );
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
