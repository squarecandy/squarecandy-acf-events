<?php
// Square Candy ACF Events Single Event Post Template
$event = get_fields();
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
					<h2 class="event-date-time" itemprop="startDate" content="<?php echo date_i18n( 'Y-m-d', strtotime( $event['start_date'] ) ); ?>">
						<?php squarecandy_acf_events_date_display( $event ); ?>
					</h2>

					<meta itemprop="url" content="<?php the_permalink(); ?>">
					<?php squarecandy_acf_events_address_display( $event, '3line' ); ?>

					<div class="more-info-buttons">

					<?php if ( ! empty( $event['tickets_link'] ) ) { ?>
						<span itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
							<a class="button button-bold button-tickets" itemprop="url" href="<?php echo $event['tickets_link']; ?>">
								<i class="fa fa-ticket"></i> Tickets
							</a>
						</span>
					<?php } ?>

					<?php
					if ( ! empty( $event['more_info_link'] ) ) :
						$moreinfo_external_link_text = apply_filters( 'squarecandy_filter_events_moreinfo_external_link_text', __( 'More Info', 'squarecandy-acf-events' ) );
						?>
						<a class="button button-bold button-more-info" href="<?php echo $event['more_info_link']; ?>">
							<i class="fa fa-info-circle"></i> <?php echo $moreinfo_external_link_text; ?>
						</a>
						<?php
					endif;
					?>

					<?php if ( ! empty( $event['facebook_link'] ) ) { ?>
						<a class="button button-bold button-facebook" href="<?php echo $event['facebook_link']; ?>">
							<i class="fa fa-facebook"></i> <?php _e( 'Facebook', 'squarecandy-acf-events' ); ?>
						</a>
					<?php } ?>

					<?php
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

						echo squarecandy_add_to_gcal(
							get_the_title(),
							$start_date,
							$end_date,
							$event['short_description'] ?? '',
							$event_address,
							$event['all_day'] ?? false,
							$linktext = '<i class="fa fa-google"></i> add to gCal',
							$classes  = array( 'gcal-button', 'button', 'button-bold' )
						);

					endif;
					?>

					</div>

					<?php
					the_post_thumbnail(); // @TODO - make this better: custom banner size, allow for caption, etc.

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
					if ( ! empty( $event['featured_works'] ) ) :
						$t = count( $event['featured_works'] );
						?>
						<div class="featuredwork">
							<span>Featured Work<?php echo $t > 1 ? 's' : ''; ?></span>
							<?php
							$works = array();
							foreach ( $event['featured_works'] as $work ) {
								$works[] = '<span itemscope itemprop="workPerformed" itemtype="http://schema.org/CreativeWork"><a itemprop="url" href="' . get_the_permalink( $work->ID ) . '"><span itemprop="name">' . $work->post_title . '</span></a></span>';
							}
							echo implode( ', ', $works );
							?>
						</div>
					<?php endif; ?>

					<?php do_action( 'squarecandy_acf_event_after_featured_works' ); ?>

					<?php
					if ( get_field( 'show_map_on_detail_page', 'option' ) &&
						! empty( $event['venue_location'] ) &&
						! empty( $event['venue_location']['lat'] ) &&
						! empty( $event['venue_location']['lng'] )
					) :
						?>
						<div id="map"></div>
					<?php endif; ?>
				</div>
					?>
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
