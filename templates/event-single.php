<?php
// Square Candy ACF Events Single Event Post Template
$event = get_fields();
get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main" role="main">
		<?php while ( have_posts() ) : the_post(); ?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(array('events-full')); ?> itemscope="" itemtype="http://schema.org/MusicEvent">
				<h1 class="entry-title event-title" itemprop="name"><?php the_title(); ?></h1>
				<h2 class="event-date-time" itemprop="startDate" content="<?php echo date('Y-m-d',strtotime($event['start_date'])); ?>">
					<?php squarecandy_acf_events_date_display($event); ?>
				</h2>

				<meta itemprop="url" content="<?php the_permalink(); ?>">
				<?php squarecandy_acf_events_address_display($event, '3line'); ?>

				<div class="more-info-buttons">

				<?php if ( !empty($event['tickets_link']) ) { ?>
					<span itemprop="offers" itemscope="" itemtype="http://schema.org/Offer">
						<a class="button button-bold" itemprop="url" href="<?php echo $event['tickets_link']; ?>">
							<i class="fa fa-ticket"></i> Tickets
						</a>
					</span>
				<?php } ?>

				<?php if ( !empty($event['more_info_link']) ) { ?>
					<a class="button button-bold" href="<?php echo $event['more_info_link']; ?>">
						<i class="fa fa-info-circle"></i> More Info
					</a>
				<?php } ?>

				<?php if ( !empty($event['facebook_link']) ) { ?>
					<a class="button button-bold button-facebook" href="<?php echo $event['facebook_link']; ?>">
						<i class="fa fa-facebook"></i> <?php _e('Facebook', 'squarecandy-acf-events'); ?>
					</a>
				<?php } ?>

				<?php
					if ( get_field('add_to_gcal', 'option') ) :
						$startdate = $event['start_date'];
						if ( isset($event['start_time']) ) $startdate .= ' ' . $event['start_time'];

						if ($event['multi_day']==1) {
							$enddate = $event['end_date'];
							if ( isset($event['end_time']) ) $enddate .= ' ' . $event['end_time'];
						}
						else {
							$enddate = false;
						}

						if ($event['multi_day']==1) {
							$enddate = $event['end_date'].' '.$event['end_time'];
						}
						else {
							$enddate = false;
						}
						$event_address = isset($event['venue_location']['address']) ? $event['venue_location']['address'] : null;
						echo squarecandy_add_to_gcal(
							get_the_title(),
							$startdate,
							$enddate,
							$event['short_description'],
							$event_address,
							$event['all_day'],
							$linktext = '<i class="fa fa-google"></i> add to gCal',
							$classes = array('gcal-button', 'button', 'button-bold')
						);
					endif;
				?>

				</div>

				<?php the_post_thumbnail(); // @TODO - make this better: custom banner size, allow for caption, etc.  ?>

				<?php
					$test_empty_content = get_the_content();
					$test_empty_content = strip_tags($test_empty_content);
					$test_empty_content = str_replace('&nbsp;', '', $test_empty_content);
					$test_empty_content = trim($test_empty_content);
				?>
				<?php if ( !empty( $test_empty_content ) ) { ?>
					<div class="post-content event-description" itemprop="description">
						<?php echo apply_filters( 'the_content', get_the_content() ); ?>
					</div>
				<?php } ?>

				<?php if ( !empty($event['featured_works']) ) :
					$t = count($event['featured_works']); ?>
					<div class="featuredwork">
						<span>Featured Work<?php if ($t > 1) echo 's'; ?></span>
						<?php $works = array();
							foreach ( $event['featured_works'] as $work ) {
								$works[] = '<span itemscope itemprop="workPerformed" itemtype="http://schema.org/CreativeWork">
									<a itemprop="url" href="'.get_the_permalink($work->ID).'">
										<span itemprop="name">'.$work->post_title.'</span>
									</a>
								</span>';
							}
							echo implode(', ', $works);
							$i++;
						?>
					</div>
				<?php endif; ?>

				<?php if ( get_field('show_map_on_detail_page', 'option') &&
					!empty($event['venue_location']) &&
					!empty($event['venue_location']['lat']) &&
					!empty($event['venue_location']['lng'])
				) : ?>
					<div id="map"></div>
				<?php endif; ?>
			</article><!-- #post-## -->
		<?php endwhile; // End of the loop. ?>
		</main><!-- #main -->
	</div><!-- #primary -->
<?php

get_sidebar();
get_footer();
