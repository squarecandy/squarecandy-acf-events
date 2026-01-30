<?php
if ( ! function_exists( 'squarecandy_add_to_gcal' ) && ! function_exists( 'squarecandy_add_to_gcal_url' ) ) :

	/**
	* Create a Google Calendar "add to calendar" link.
	*
	* This function is convienient because it does not require an API connection.
	* Note that this only allows for adding a single event.
	* The data does not have to exist already on any Google Calendar anywhere.
	* This just adds your event data to the end-users GCal one item at a time.
	*
	* @author Peter Wise / Square Candy Design
	* @link https://gist.github.com/petertwise/26611bbcfc63f9c7e3a7dc45e9145133
	*
	* @param string    $name          The main title of the event
	* @param string    $startdate     The start date and time in any format that strtotime can digest
	* @param string    $enddate       The end date and time in any format that strtotime can digest
	* @param string    $description   The longer description text of the event
	* @param string    $location      The event location - any text google maps can parse as a single point
	* @param bool      $allday        Is the event "All Day" with no times displayed?
	* @param string    $linktext      The text of the link. Defaults to 'Add to gCal'.
	* @param array     $classes       An array of classes to add to the link element.
	* @param string    $timezone      The timezone identifier, e.g. 'America/New_York'
	* @return string An HTML link to add the event
	*/
	function squarecandy_add_to_gcal(
		$name,
		$startdate,
		$enddate = false,
		$description = false,
		$location = false,
		$allday = false,
		$linktext = 'Add to gCal',
		$classes = array( 'gcal-button, button' ),
		$timezone = false
	) {

		$url = squarecandy_add_to_gcal_url(
			$name,
			$startdate,
			$enddate,
			$description,
			$location,
			$allday,
			$timezone
		);

		// build the link output
		$output = '<a href="' . $url . '" class="' . implode( ' ', $classes ) . '">' . $linktext . '</a>';

		return $output;
	}

	/**
	 * Create a Google Calendar "add to calendar" URL.
	 * @author Peter Wise / Square Candy Design
	 * @link https://gist.github.com/petertwise/26611bbcfc63f9c7e3a7dc45e9145133
	 * @param string    $name          The main title of the event
	 * @param string	$startdate     The start date and time in any format that strtotime can digest
	 * @param string	$enddate       The end date and time in any format that strtotime can digest
	 * @param string	$description   The longer description text of the event
	 * @param string	$location      The event location - any text google maps can parse as a single point
	 * @param bool		$allday        Is the event "All Day" with no times displayed?
	 * @param string	$timezone      The timezone identifier, e.g. 'America/New_York'
	 * @return string A URL to add the event
	 */
	function squarecandy_add_to_gcal_url(
		$name,
		$startdate,
		$enddate = false,
		$description = false,
		$location = false,
		$allday = false,
		$timezone = false
	) {
		// calculate the start and end dates, convert to ISO format
		if ( $allday ) {
			$startdate = date_i18n( 'Ymd', strtotime( $startdate ) );
		} else {
			$startdate = date_i18n( 'Ymd\THis', strtotime( $startdate ) );
		}

		if ( $enddate && ! empty( $enddate ) && strlen( $enddate ) > 2 ) {
			if ( $allday ) {
				$enddate = date_i18n( 'Ymd', strtotime( $enddate . ' + 1 day' ) );
			} else {
				$enddate = date_i18n( 'Ymd\THis', strtotime( $enddate ) );
			}
		} else {
			if ( $allday ) {
					$enddate = date_i18n( 'Ymd', strtotime( $startdate . ' + 1 day' ) );
			} else {
					$enddate = date_i18n( 'Ymd\THis', strtotime( $startdate . ' + 2 hours' ) );
			}
		}

		// build the url
		$url  = 'http://www.google.com/calendar/event?action=TEMPLATE';
		$url .= '&text=' . rawurlencode( $name );
		$url .= '&dates=' . $startdate . '/' . $enddate;
		if ( $description ) {
			$url .= '&details=' . rawurlencode( $description );
		}
		if ( $location ) {
			$url .= '&location=' . rawurlencode( $location );
		}

		if ( $timezone ) {
			$url .= '&ctz=' . rawurlencode( $timezone );
		}

		return $url;
	}

endif;

/********************
 *
 *  Example Usage:
 *
 *  echo squarecandy_add_to_gcal('Example Event', 'June 30, 2017 8:00pm');
 *  echo squarecandy_add_to_gcal('Example Event', 'June 30, 2017 8:00pm', 'July 2, 2017 10:00am', 'This is my detailed event description', '1600 Pennsylvania Ave NW, Washington, DC 20500');
 *  echo squarecandy_add_to_gcal('Example Event', 'June 30, 2017', 'July 2, 2017', 'This is my detailed event description', '1600 Pennsylvania Ave NW, Washington, DC 20500', true, 'gCal+', array('my-custom-class') );
 *
 * // with timezone
 * echo squarecandy_add_to_gcal('Example Event', 'June 30, 2017 8:00pm', 'July 2, 2017 10:00am', 'This is my detailed event description', '1600 Pennsylvania Ave NW, Washington, DC 20500', false, 'gCal+', array('my-custom-class'), 'America/New_York' );
 *
 * // URL only
 * echo squarecandy_add_to_gcal_url('Example Event', 'June 30, 2017 8:00pm', 'July 2, 2017 10:00am', 'This is my detailed event description', '1600 Pennsylvania Ave NW, Washington, DC 20500', false, 'America/New_York' );
 */

