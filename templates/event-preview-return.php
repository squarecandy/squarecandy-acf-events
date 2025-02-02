<?php
// Square Candy ACF Events Preview/Listing Post Template
global $event_id;
$event_id = (int) $event_id;

$event_id = empty( $event_id ) ? get_the_ID() : $event_id;

$event = get_fields( $event_id );

// start date is required, bail if it's not set
if ( empty( $event['start_date'] ) ) {
	return;
}

$event_link  = get_permalink( $event_id );
$event_title = get_the_title( $event_id );
$show_image  = get_option( 'options_event_show_image' );
$classes     = array( 'events-preview' );
$image_html  = '';
if ( $show_image ) :
	$image_size = get_option( 'options_event_image_preview_size' );
	$image_html = get_the_post_thumbnail( $event_id, $image_size );
	if ( $image_html ) :
		$classes[] = 'has-image';
	endif;
endif;
$class = implode( ' ', $classes );

$output .= '<article id="post-' . $event_id . '" class="' . $class . '" itemscope="" itemtype="http://schema.org/MusicEvent">';

$output .= '<h1 class="event-date-time" itemprop="startDate" content="' . date_i18n( 'Y-m-d', strtotime( $event['start_date'] ) ) . '">';
$output .= '<a href="' . $event_link . '">' . get_squarecandy_acf_events_date_display( $event, $compact ) . '</a>';
$output .= '</h1>';

$output = apply_filters( 'squarecandy_events_preview_before_title', $output, $event_id );

if ( ! $compact || ( $compact && get_field( 'show_title', 'option' ) ) ) {
	$output .= '<h2 class="entry-title" itemprop="name"><a href="' . $event_link . '">' . $event_title . '</a></h2>';
}

$output = apply_filters( 'squarecandy_events_preview_before_address', $output, $event_id );

if ( ! empty( $event['venue'] ) || ! empty( $event['address'] ) || ! empty( $event['city'] ) ) :
	if ( $compact ) {
		$output .= get_squarecandy_acf_events_address_display( $event, 'citystate', true );
	} else {
		$output .= get_squarecandy_acf_events_address_display( $event, '2line', true );
	}
endif;

if ( ! empty( $event['short_description'] ) ) {
	$show_description = get_field( 'show_description', 'option' );
	if ( ! $compact || ( $compact && $show_description ) ) {
		$output .= '<div class="short-description" itemprop="description">' . $event['short_description'] . '</div>';
	}
}

if ( $show_image ) :
	// @TODO - add bottom/left/top/right options and css
	$output .= '<div class="event-image-bottom event-image">';
	// don't add link if no image, but leave outer element in place to avoid screwing up legacy layouts?
	if ( $image_html ) :
		$output .= '<a href="' . $event_link . '" tabindex="-1">';
		$output .= $image_html;
		$output .= '</a>';
	endif;
	$output .= '</div>';
endif;

$show_post_link_button = $compact || $moreinfo_post_link ? $event_link : false; // @TODO check if $moreinfo_post_link is passed in properly here

$output .= '<div class="more-info-buttons">';
$output .= squarecandy_events_generate_buttons( $event, $show_post_link_button, false );
$output .= '</div>';

if ( ! empty( $event['end_date'] ) ) {
	$meta_end_date = date_i18n( 'Y-m-d', strtotime( $event['end_date'] ) );
} else {
	$meta_end_date = date_i18n( 'Y-m-d', strtotime( $event['start_date'] ) );
}

$output .= '<meta itemprop="endDate" content="' . $meta_end_date . '">';
$output .= '<meta itemprop="url" content="' . $event_link . '">';

$output .= '</article>';
