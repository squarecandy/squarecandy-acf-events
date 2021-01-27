<?php
// bulk update once on plugin activation or for existing users at version 1.3.0

// Run a cleanup on all events once
function squarecandy_cleanup_all_events() {

	check_ajax_referer( 'squarecandy_event_bulk_update' );
	$page = (int) $_POST['page'];

	$args = array(
		'post_type'              => 'event',
		'posts_per_page'         => 50,
		'paged'                  => $page,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
		'fields'                 => 'ids',
	);

	$events = new WP_Query( $args );

	foreach ( $events->posts as $post_id ) {
		squarecandy_cleanup_event_data( $post_id );
	}

	if ( 0 === $events->post_count || 0 === $events->found_posts || $page === (int) $events->max_num_pages ) {
		set_transient( 'squarecandy_event_cleanup_complete4', true );
		$return = array( 'status' => 'complete' );
	} else {
		$total_completed   = ( ( $page - 1 ) * 50 ) + $events->post_count;
		$percent_completed = floor( ( $total_completed / $events->found_posts ) * 100 );
		$return            = array(
			'status'            => 'success',
			'page'              => $page,
			'percent_completed' => $percent_completed,
			'progress_message'  => $percent_completed . '% complete (' . $total_completed . ' of ' . $events->found_posts . ')',
		);
	}
	echo wp_json_encode( $return );
	wp_die();
}
add_action( 'wp_ajax_squarecandy_cleanup_all_events', 'squarecandy_cleanup_all_events' );

function squarecandy_events_notice_bulk_update() {
	$class    = 'notice notice-warning squarecandy-events-bulk-update';
	$message  = __( 'A bulk update of your events is required. We are running it now. Please stay on this page until it completes.', 'squarecandy-acf-events' );
	$progress = '<div class="squarecandy-events-bulk-update-progress"><div class="progress-bar"></div></div><div class="squarecandy-events-bulk-update-progress-text"></div>';
	printf( '<div class="%1$s"><p>%2$s</p>' . $progress . '</div>', esc_attr( $class ), esc_html( $message ) );
}
add_action( 'admin_notices', 'squarecandy_events_notice_bulk_update' );
