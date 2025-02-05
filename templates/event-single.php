<?php
// Square Candy ACF Events Single Event Post Template
$event_id              = get_the_ID();
$event                 = get_fields( $event_id );
$event['ID']           = $event_id;
$event['archive_date'] = get_field( 'archive_date', $event_id );
if ( empty( $event['archive_date'] ) ) {
	$event['archive_date'] = get_field( 'end_date', $event_id ) . ' ' . get_field( 'end_time', $event_id );
}
if ( empty( $event['archive_date'] ) ) {
	$event['archive_date'] = get_field( 'start_date', $event_id ) . ' 23:59:59';
}

$template = new SquareCandy_Events_Template_Loader();
get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
		<?php
		while ( have_posts() ) :
			the_post();

			if ( sqcdy_is_views2( 'events' ) && get_option( 'options_event_single_header_title' ) ) :
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
			<article id="post-<?php the_ID(); ?>" <?php post_class( array( 'events-full', 'events-single' ) ); ?> itemscope="" itemtype="http://schema.org/MusicEvent">
				<div class="event-single-content-wrapper">
					<?php if ( ! sqcdy_is_views2( 'events' ) ) : ?>
						<h1 class="entry-title event-title" itemprop="name"><?php the_title(); ?></h1>
						<?php do_action( 'squarecandy_after_events_single_title' ); ?>
						<h2 class="event-date-time" itemprop="startDate" content="<?php echo date_i18n( 'Y-m-d', strtotime( $event['start_date'] ) ); ?>">
							<?php squarecandy_acf_events_date_display( $event ); ?>
						</h2>
					<?php else : ?>
						<h1 class="event-date-title">
							<?php
							$date_first      = get_option( 'options_event_single_date_first' );
							$date_container  = '<span class="event-date-time" itemprop="startDate" content="' . date_i18n( 'Y-m-d', strtotime( $event['start_date'] ) ) . '">';
							$date_container .= get_squarecandy_acf_events_date_display( $event );
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
					$event_image_html = apply_filters( 'squarecandy_events_single_event_image', false );
					if ( $event_image_html ) :
						echo $event_image_html;
					else :
						the_post_thumbnail();
					endif;

					$test_empty_content = get_the_content();
					$test_empty_content = wp_strip_all_tags( $test_empty_content );
					$test_empty_content = str_replace( '&nbsp;', '', $test_empty_content );
					$test_empty_content = trim( $test_empty_content );

					if ( ! empty( $test_empty_content ) ) {
						?>
						<div class="post-content event-description" itemprop="description">
							<?php echo apply_filters( 'the_content', get_the_content() ); ?>
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
