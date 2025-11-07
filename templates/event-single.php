<?php
// Square Candy ACF Events Single Event Post Template
$event_id    = get_the_ID();
$event       = get_fields( $event_id );
$event['ID'] = $event_id;
$is_views2   = sqcdy_is_views2( 'events' );
$template    = new SquareCandy_Events_Template_Loader();

// set up image properties
$event_image_html = '';
$show_image       = get_option( 'options_event_show_image_single' );
$image_size       = $is_views2 ? 'large' : 'post-thumbnail'; // fall back to previous default value
$image_size       = apply_filters( 'squarecandy_events_single_event_image_size', $image_size, $event_id );

$image_position = get_option( 'options_event_image_single_position' );
if ( empty( $image_position ) ) {
	$image_position = 'middle';
}

// if the checkbox is checked, or has never been set, show the image
if ( false === $show_image || ! empty( $show_image ) ) {

	// allow shortcircuiting of getting image html via filter, currently used in elenaruehr and sopercussion
	$event_image_html = apply_filters( 'squarecandy_events_single_event_image', false, $event_id );

	// if not filtered, get the post thumbnail
	if ( empty( $event_image_html ) ) {
		$event_image_html = get_the_post_thumbnail( $event_id, $image_size );
		$event_image_html = '<div class="event-image event-image-' . $image_position . '">' . $event_image_html . '</div>';
	}
}

$event_date_meta    = date_i18n( 'Y-m-d', strtotime( $event['start_date'] ) );
$event_date_display = get_squarecandy_acf_events_date_display( $event );

get_header();
?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
		<?php
		while ( have_posts() ) :
			the_post();

			if ( $is_views2 && get_option( 'options_event_single_header_title' ) ) :
				$cpt_object = get_post_type_object( 'event' );
				$cpt_plural = is_a( $cpt_object, 'WP_Post_Type' ) ? esc_html( $cpt_object->labels->name ) : 'Events';
				?>
				<header class="entry-header events-header template-header">
					<div class="entry-title squarecandy-title events-title">
						<?php echo $cpt_plural; ?>
					</div>
				</header>
				<?php
			endif;
			?>
			<article id="post-<?php echo $event_id; ?>" <?php post_class( array( 'events-full', 'events-single' ) ); ?> itemscope="" itemtype="http://schema.org/MusicEvent">
				<div class="event-single-content-wrapper">
					<?php echo 'top' === $image_position ? $event_image_html : ''; ?>
					<?php if ( ! $is_views2 ) : ?>
						<h1 class="entry-title event-title" itemprop="name"><?php the_title(); ?></h1>
						<?php do_action( 'squarecandy_after_events_single_title' ); ?>
						<h2 class="event-date-time" itemprop="startDate" content="<?php echo $event_date_meta; ?>">
							<?php echo $event_date_display; ?>
						</h2>
					<?php else : ?>
						<h1 class="event-date-title">
							<?php
							$date_first      = get_option( 'options_event_single_date_first' );
							$date_container  = '<span class="event-date-time" itemprop="startDate" content="' . $event_date_meta . '">';
							$date_container .= $event_date_display;
							$date_container .= '</span> ';
							$title_container = '<span class="entry-title" itemprop="name">' . get_the_title() . '</span> ';
							if ( $date_first ) {
								echo $date_container;
								echo $title_container;
							} else {
								echo $title_container;
								echo $date_container;
							}
							?>
						</h1>
						<?php do_action( 'squarecandy_after_events_single_title' ); ?>
					<?php endif; ?>

					<meta itemprop="url" content="<?php the_permalink(); ?>">
					<?php squarecandy_acf_events_address_display( $event, '3line', true ); // 3line ?>

					<div class="more-info-buttons"><?php squarecandy_events_generate_buttons( $event ); //don't put line breaks around this, we don't want extra spaces! ?></div>

					<?php
					// default/legacy image position
					echo 'middle' === $image_position ? $event_image_html : '';

					$event_content      = get_the_content();
					$test_empty_content = wp_strip_all_tags( $event_content );
					$test_empty_content = str_replace( '&nbsp;', '', $test_empty_content );
					$test_empty_content = trim( $test_empty_content );

					if ( ! empty( $test_empty_content ) ) {
						?>
						<div class="post-content event-description" itemprop="description">
							<?php echo apply_filters( 'the_content', $event_content ); ?>
						</div>
					<?php } elseif ( ! empty( $event['short_description'] ) ) { ?>
						<div class="post-content event-description" itemprop="description">
							<?php echo apply_filters( 'the_content', $event['short_description'] ); ?>
						</div>
						<?php
					}

					echo $template->load_template_part( 'event', 'works' );

					do_action( 'squarecandy_acf_event_after_featured_works', $event );

					if ( get_field( 'show_map_on_detail_page', 'option' ) &&
						! empty( $event['venue_location'] ) &&
						! empty( $event['venue_location']['lat'] ) &&
						! empty( $event['venue_location']['lng'] )
					) :
						?>
						<div id="map"></div>
					<?php endif; ?>

					<?php echo 'bottom' === $image_position ? $event_image_html : ''; ?>
				</div>
				<footer class="squarecandy-footer squarecandy-events-footer">
					<?php
					do_action( 'squarecandy_acf_event_before_footer' );
					$events_slug = apply_filters( 'squarecandy_events_slug', 'events' );
					$all_events  = apply_filters( 'squarecandy_events_see_all', 'See All Events' );
					?>
					<a class="back-to-list back-to-events" href="/<?php echo $events_slug; ?>/">
						<?php echo $all_events; ?>
					</a>
				</footer>
			</article><!-- #post-## -->
		<?php endwhile; // End of the loop. ?>

		<?php
		if ( function_exists( 'squarecandy_archive_year_nav' ) ) {
			squarecandy_archive_year_nav( wp_date( 'Y', strtotime( $event['start_date'] ) ) );
		}
		?>
		</main><!-- #main -->
	</div><!-- #primary -->
<?php

get_sidebar();
get_footer();
