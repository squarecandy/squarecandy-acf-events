<?php
/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Square_Candy
 */
get_header();
if ( get_query_var( 'archive_year' ) ) {
	$archive_year = (int) get_query_var( 'archive_year' );
	$page_title   = $archive_year . ' ' . __( 'Events Archive', 'squarecandy-acf-events' );
	$shortcode    = '[squarecandy_events type=past archive_year=' . $archive_year . ']';
} else {
	$page_title = __( 'Upcoming Events', 'squarecandy-acf-events' );
	$shortcode  = '[squarecandy_events]';
}
?>
	<div id="primary" class="content-area content-area-events">
		<main id="main" class="site-main" role="main">

			<header class="page-header">
				<h1 class="page-title"><?php echo $page_title; ?></h1>
			</header><!-- .page-header -->

			<?php echo do_shortcode( $shortcode ); ?>

			<?php
			if ( function_exists( 'squarecandy_archive_year_nav' ) ) {
				squarecandy_archive_year_nav();
			}
			?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
