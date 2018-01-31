<?php
if ( !function_exists('squarecandy_add_to_gcal') ):

function squarecandy_add_to_gcal(
  $name,
  $startdate,
  $enddate = false,
  $description = false,
  $location = false,
  $allday = false,
  $linktext = 'Add to gCal',
  $classes = array('gcal-button, button')
) {

  // calculate the start and end dates, convert to ISO format
  if ($allday) {
    $startdate = date('Ymd',strtotime($startdate));
  }
  else {
    $startdate = date('Ymd\THis',strtotime($startdate));
  }

  if ($enddate && !empty($enddate) && strlen($enddate) > 2) {
    if ($allday) {
      $enddate = date('Ymd',strtotime($enddate . ' + 1 day'));
    }
    else {
      $enddate = date('Ymd\THis',strtotime($enddate));
    }
  }
  else {
    if ($allday) {
      $enddate = date('Ymd',strtotime($startdate . ' + 1 day'));
    }
    else {
      $enddate = date('Ymd\THis',strtotime($startdate . ' + 2 hours'));
    }
  }

  // build the url
  $url = 'http://www.google.com/calendar/event?action=TEMPLATE';
  $url .= '&text=' . rawurlencode($name);
  $url .= '&dates=' . $startdate . '/' . $enddate;
  if ($description) {
    $url .= '&details=' . rawurlencode($description);
  }
  if ($location) {
    $url .= '&location=' . rawurlencode($location);
  }

  // build the link output
  $output = '<a href="' . $url . '" class="' . implode(' ',$classes) . '">'.$linktext.'</a>';

  return $output;
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
 */
?>
