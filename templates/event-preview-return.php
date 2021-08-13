<?php
// Square Candy ACF Events Preview/Listing Post Template
$event = get_fields();

$output .= '<article id="post-' . get_the_ID() . '" class="events-preview" itemscope="" itemtype="http://schema.org/MusicEvent">';

$output .= '<h1 class="event-date-time" itemprop="startDate" content="' . date_i18n( 'Y-m-d', strtotime( $event['start_date'] ) ) . '">';
$output .= '<a href="' . get_permalink() . '">' . get_squarecandy_acf_events_date_display( $event, $compact ) . '</a>';
$output .= '</h1>';

if ( ! empty( $event['end_date'] ) ) {
	$meta_end_date = date_i18n( 'Y-m-d', strtotime( $event['end_date'] ) );
} else {
	$meta_end_date = date_i18n( 'Y-m-d', strtotime( $event['start_date'] ) );
}

$output .= '<meta itemprop="endDate" content="' . $meta_end_date . '">';
if ( ! $compact || ( $compact && get_field( 'show_title', 'option' ) ) ) {
	$output .= '<h2 class="entry-title" itemprop="name"><a href="' . get_permalink() . '">' . get_the_title() . '</a></h2>';
}
$output .= '<meta itemprop="url" content="' . get_permalink() . '">';

$output = apply_filters( 'squarecandy_events_preview_before_address', $output, get_the_ID() );

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

if ( get_field( 'event_show_image', 'option' ) ) {
	// @TODO - add bottom/left/top/right options and css
	$output .= '<div class="event-image-bottom event-image">';
	$output .= '<a href="' . get_permalink() . '">';
	$size    = get_field( 'event_image_preview_size', 'option' );
	$output .= get_the_post_thumbnail( null, $size );
	$output .= '</a>';
	$output .= '</div>';
}

$output .= '<div class="more-info-buttons">';
if ( ! empty( $event['tickets_link'] ) ) {
	$output .= '<span itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
			<a class="button button-bold button-tickets" itemprop="url" href="' . $event['tickets_link'] . '">
				<i class="fa fa-ticket"></i> ' . __( 'Tickets', 'squarecandy-acf-events' ) . '
			</a>
		</span> ';
}

if ( $compact || $moreinfo_post_link ) {
	$moreinfo_post_link_text = __( 'More Info', 'squarecandy-acf-events' );
	$moreinfo_post_link_text = apply_filters( 'squarecandy_filter_events_moreinfo_post_link_text', $moreinfo_post_link_text );
	$output                 .= '<a class="button button-bold button-more-info" href="' . get_permalink() . '">
			<i class="fa fa-info-circle"></i> ' . $moreinfo_post_link_text . '
		</a> ';
} elseif ( ! empty( $event['more_info_link'] ) && ! $compact ) {
	$moreinfo_external_link_text = __( 'More Info', 'squarecandy-acf-events' );
	$moreinfo_external_link_text = apply_filters( 'squarecandy_filter_events_moreinfo_external_link_text', $moreinfo_external_link_text );
	$output                     .= '<a class="button button-bold button-more-info" href="' . $event['more_info_link'] . '">
			<i class="fa fa-info-circle"></i> ' . $moreinfo_external_link_text . '
		</a> ';
}


if ( ! empty( $event['facebook_link'] ) ) {
	$output .= '<a class="button button-bold button-facebook" href="' . $event['facebook_link'] . '">
			<i class="fa fa-facebook"></i>
			<span class="screen-reader-text">' . __( 'Facebook Event', 'squarecandy-acf-events' ) . '</span>
			</a> ';
}

if ( get_field( 'add_to_gcal', 'option' ) ) :

	$start_date = $event['start_date'];
	$end_date   = $event['end_date'] ?? false;
	$multi_day  = $event['muilti_day'] ?? false;

	if ( ! empty( $event['start_time'] ) ) {
		$start_date .= ' ' . $event['start_time'];
	}

	if ( $multi_day && $end_date && isset( $event['end_time'] ) ) {
		$end_date .= ' ' . $event['end_time'];
	}

	$event_address = $event['venue_location']['address'] ?? null;

	$output .= squarecandy_add_to_gcal(
		get_the_title(),
		$start_date,
		$end_date,
		$event['short_description'] ?? false,
		$event_address,
		$event['all_day'] ?? false,
		'<i class="fa fa-google-plus"></i><span class="screen-reader-text">' . __( 'add to google calendar', 'squarecandy-acf-events' ) . '</span>',
		array( 'gcal-button', 'button', 'button-bold' )
	);

endif;

$output .= '</div>
</article>';
