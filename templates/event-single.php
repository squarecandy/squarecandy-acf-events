<?php
// Square Candy ACF Events Single Event Post Template
$event = get_fields();
$template = new SquareCandy_Events_Template_Loader();
get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article id="post-<?php the_ID(); ?>" <?php post_class( array( 'events-full' ) ); ?> itemscope="" itemtype="http://schema.org/MusicEvent">
				<div class="event-single-content-wrapper">
					<h1 class="entry-title event-title" itemprop="name"><?php the_title(); ?></h1>
					<?php do_action( 'squarecandy_after_events_single_title' ); ?>
					<h2 class="event-date-time" itemprop="startDate" content="<?php echo date_i18n( 'Y-m-d', strtotime( $event['start_date'] ) ); ?>">
						<?php squarecandy_acf_events_date_display( $event ); ?>
					</h2>

					<meta itemprop="url" content="<?php the_permalink(); ?>">
					<?php squarecandy_acf_events_address_display( $event, '3line' ); ?>

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
