<?php
// Square Candy ACF Events Preview/Listing Post Template
// $compact & $moreinfo_post_link are set in squarecandy_events_func() (?)

$event_id = empty( $event_id ) ? get_the_ID() : (int) $event_id;
$event    = get_fields( $event_id );

// start date is required, bail if it's not set
if ( empty( $event['start_date'] ) ) {
	return;
}

$event['ID']           = $event_id; // DO WE USE THIS?
$event['archive_date'] = get_field( 'archive_date', $event_id ); // DO WE USE THIS?
if ( empty( $event['archive_date'] ) ) {
	$event['archive_date'] = get_field( 'end_date', $event_id ) . ' ' . get_field( 'end_time', $event_id );
}
if ( empty( $event['archive_date'] ) ) {
	$event['archive_date'] = get_field( 'start_date', $event_id ) . ' 23:59:59';
}

$event_link  = get_permalink( $event_id );
$event_title = get_the_title( $event_id );
$classes     = array( 'events-preview' );

$is_views2 = sqcdy_is_views2( 'events' );

$show_image   = get_option( 'options_event_show_image' );
$image_html   = '';
$image_output = '';

if ( $show_image ) :
	// get options
	$image_size     = get_option( 'options_event_image_preview_size' );
	$image_position = get_option( 'options_event_image_preview_position' );
	$image_position = $image_position ? $image_position : 'bottom';
	// get thumbnail
	$image_html     = get_the_post_thumbnail( $event_id, $image_size );
	// @TODO - add bottom/left/top/right options and css
	$image_output .= '<div class="event-image-' . $image_position . ' event-image">';
	if ( $image_html ) :
		$classes[] = 'has-image has-image-' . $image_position;
		// don't add link if no image, but leave outer element in place to avoid screwing up legacy layouts?
		$image_output .= '<a href="' . $event_link . '" tabindex="-1">';
		$image_output .= $image_html;
		$image_output .= '</a>';
	endif;
	$image_output .= '</div>';
endif;

if ( $is_views2 ) {
	$classes[] = 'event';
	$classes[] = 'event-' . $event_id;
	$classes[] = 'type-event';
}

$class = implode( ' ', $classes );

$date_tag = $is_views2 ? 'span' : 'h1';

$date_container  = '<' . $date_tag . ' class="event-date-time" itemprop="startDate" content="' . date_i18n( 'Y-m-d', strtotime( $event['start_date'] ) ) . '">';
$date_container .= ! $is_views2 ? '<a href="' . $event_link . '">' : ''; // we're going to wrap the data and title together in views2
$date_container .= get_squarecandy_acf_events_date_display( $event, $compact );
$date_container .= ! $is_views2 ? '</a>' : '';
$date_container .= '</' . $date_tag . '> ';

$title_container = '';
if ( ! $compact || ( $compact && get_field( 'show_title', 'option' ) ) ) {
	$title_tag        = $is_views2 ? 'span' : 'h2';
	$title_container  = '<' . $title_tag . ' class="entry-title" itemprop="name">';
	$title_container .= ! $is_views2 ? '<a href="' . $event_link . '">' : ''; // we're going to wrap the data and title together in views2
	$title_container .= $event_title;
	$title_container .= ! $is_views2 ? '</a>' : '';
	$title_container .= '</' . $title_tag . '> ';
}

$show_post_link_button = $compact || $moreinfo_post_link ? $event_link : false; // @TODO check if $moreinfo_post_link is passed in properly here


// Begin output
$output .= '<article id="post-' . $event_id . '" class="' . $class . '" itemscope="" itemtype="http://schema.org/MusicEvent">';

if ( $is_views2 ) {
	// if views2 show image at top
	$output .= $image_output;

	// if views2 open content div
	$output .= '<div class="event-content">';
}

// Add date and title
if ( ! $is_views2 ) {
	// legacy title/date layout
	$output .= $date_container;
	$output  = apply_filters( 'squarecandy_events_preview_before_title', $output, $event_id );
	$output .= $title_container;
} else {
	// views2 title/date layout
	$output = apply_filters( 'squarecandy_events_preview_before_title', $output, $event_id );

	$h_level = empty( $h_level ) ? 'h2' : $h_level;

	$output .= '<' . $h_level . ' class="event-date-title">';
	$output .= '<a href="' . $event_link . '">';
	if ( get_option( 'options_event_preview_title_first' ) ) {
		$output .= $title_container;
		$output .= $date_container;
	} else {
		$output .= $date_container;
		$output .= $title_container;
	}
	$output .= '</a>';
	$output .= '</' . $h_level . '>';
}

$output = apply_filters( 'squarecandy_events_preview_before_address', $output, $event_id );

// Add address
if ( ! empty( $event['venue'] ) || ! empty( $event['address'] ) || ! empty( $event['city'] ) ) :
	if ( $compact ) {
		$output .= get_squarecandy_acf_events_address_display( $event, 'citystate', true );
	} else {
		$output .= get_squarecandy_acf_events_address_display( $event, '2line', true );
	}
endif;

// Add description
if ( ! empty( $event['short_description'] ) ) {
	$show_description = get_field( 'show_description', 'option' );
	if ( ! $compact || ( $compact && $show_description ) ) {
		$output .= '<div class="short-description" itemprop="description">' . $event['short_description'] . '</div>';
	}
}

if ( ! $is_views2 ) {
	// if NOT views2 add image here
	$output .= $image_output;
}

// Add buttons
$output .= '<div class="more-info-buttons">';
$output .= squarecandy_events_generate_buttons( $event, $show_post_link_button, false );
$output .= '</div>';

if ( ! empty( $event['end_date'] ) ) {
	$meta_end_date = date_i18n( 'Y-m-d', strtotime( $event['end_date'] ) );
} else {
	$meta_end_date = date_i18n( 'Y-m-d', strtotime( $event['start_date'] ) );
}

// Add meta
$output .= '<meta itemprop="endDate" content="' . $meta_end_date . '">';
$output .= '<meta itemprop="url" content="' . $event_link . '">';

if ( $is_views2 ) {
	$output .= '</div><!-- .event-content -->';
}

$output .= '</article>';
