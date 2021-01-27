<?php
// load more items via ajax
function squarecandy_events_load_more_ajax_handler() {

	$page = (int) esc_attr( $_POST['page'] ) + 1; // we need the next page to be loaded

	$attr   = array();
	$attr[] = 'page=' . $page;
	$attr[] = 'type=' . esc_attr( $_POST['eventType'] );
	if ( ! empty( $_POST['archiveYear'] ) ) {
		$attr[] = 'archive_year=' . esc_attr( $_POST['archiveYear'] );
	}

	$shortcode = '[squarecandy_events ajax=1 ' . implode( ' ', $attr ) . ']';
	echo do_shortcode( $shortcode );
	die;
}
add_action( 'wp_ajax_events_load_more', 'squarecandy_events_load_more_ajax_handler' );
add_action( 'wp_ajax_nopriv_events_load_more', 'squarecandy_events_load_more_ajax_handler' );
