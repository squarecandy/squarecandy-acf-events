<?php
/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Square_Candy
 */
get_header();
$shortcode_args = array();
if ( get_query_var( 'archive_year' ) ) {
	$archive_year                   = (int) get_query_var( 'archive_year' );
	$page_title                     = $archive_year . ' ' . __( 'Events Archive', 'squarecandy-acf-events' );
	$shortcode_args['type']         = 'past';
	$shortcode_args['archive_year'] = $archive_year;
	$classes                        = 'events-archive-year events-archive-' . $archive_year;
} elseif ( is_tax( 'events-category' ) ) {
	$this_tax   = get_queried_object();
	$page_title = $this_tax->name;
	if ( ! get_option( 'options_event_categories_type' ) ) {
		$shortcode_args['type'] = 'all';
	}
	$shortcode_args['cat'] = $this_tax->slug;
	$tax_order             = get_term_meta( $this_tax->term_id, 'events_order', true );
	if ( $tax_order ) {
		$shortcode_args['order'] = $tax_order;
	}
	$classes = 'event-archive-category';
} else {
	$page_title = __( 'Upcoming Events', 'squarecandy-acf-events' );
	$classes    = 'event-archive-upcoming';
}
$shortcode_args = apply_filters( 'squarecandy_events_category_shortcode_args', $shortcode_args );
?>
	<div id="primary" class="content-area content-area-events <?php echo $classes; ?>">
		<main id="main" class="site-main" role="main">

			<header class="page-header">
				<h1 class="page-title"><?php echo apply_filters( 'squarecandy_events_archive_page_title', $page_title, $shortcode_args ); ?></h1>
				<?php do_action( 'squarecandy_after_events_archive_title', $shortcode_args ); ?>
			</header><!-- .page-header -->

			<?php echo squarecandy_events_func( $shortcode_args ); ?>

			<?php
			if ( function_exists( 'squarecandy_archive_year_nav' ) ) {
				squarecandy_archive_year_nav();
			}
			?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php
get_footer();
