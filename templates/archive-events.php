<?php
/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Square_Candy
 */
get_header(); ?>
	<div id="primary" class="content-area content-area-events">
		<main id="main" class="site-main" role="main">

			<header class="page-header">
				<h1 class="page-title"><?php _e('Upcoming Events', 'squarecandy-acf-events'); ?></h1>
			</header><!-- .page-header -->

			<?php echo do_shortcode('[squarecandy_events]'); ?>

			<h1 class="past-events-title"><?php _e('Past Events', 'squarecandy-acf-events'); ?></h1>

			<?php echo do_shortcode('[squarecandy_events type=past]'); ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
