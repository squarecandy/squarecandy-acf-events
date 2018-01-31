<?php

function event_init() {
	register_post_type( 'event', array(
		'labels'            => array(
			'name'                => __( 'Events', 'squarecandy-votebuilder-events' ),
			'singular_name'       => __( 'Event', 'squarecandy-votebuilder-events' ),
			'all_items'           => __( 'All Events', 'squarecandy-votebuilder-events' ),
			'new_item'            => __( 'New Event', 'squarecandy-votebuilder-events' ),
			'add_new'             => __( 'Add New', 'squarecandy-votebuilder-events' ),
			'add_new_item'        => __( 'Add New Event', 'squarecandy-votebuilder-events' ),
			'edit_item'           => __( 'Edit Event', 'squarecandy-votebuilder-events' ),
			'view_item'           => __( 'View Event', 'squarecandy-votebuilder-events' ),
			'search_items'        => __( 'Search Events', 'squarecandy-votebuilder-events' ),
			'not_found'           => __( 'No Events found', 'squarecandy-votebuilder-events' ),
			'not_found_in_trash'  => __( 'No Events found in trash', 'squarecandy-votebuilder-events' ),
			'parent_item_colon'   => __( 'Parent Event', 'squarecandy-votebuilder-events' ),
			'menu_name'           => __( 'Events', 'squarecandy-votebuilder-events' ),
		),
		'public'            => true,
		'hierarchical'      => false,
		'show_ui'           => true,
		'show_in_nav_menus' => true,
		'supports'          => array( 'title', 'editor' ),
		'has_archive'       => true,
		'rewrite'           => true,
		'query_var'         => true,
		'menu_icon'         => 'dashicons-calendar-alt',
		'show_in_rest'      => true,
		'rest_base'         => 'event',
		'rest_controller_class' => 'WP_REST_Posts_Controller',
	) );

}
add_action( 'init', 'event_init' );

function event_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$messages['event'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => sprintf( __('Event updated. <a target="_blank" href="%s">View Event</a>', 'squarecandy-votebuilder-events'), esc_url( $permalink ) ),
		2 => __('Custom field updated.', 'squarecandy-votebuilder-events'),
		3 => __('Custom field deleted.', 'squarecandy-votebuilder-events'),
		4 => __('Event updated.', 'squarecandy-votebuilder-events'),
		/* translators: %s: date and time of the revision */
		5 => isset($_GET['revision']) ? sprintf( __('Event restored to revision from %s', 'squarecandy-votebuilder-events'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6 => sprintf( __('Event published. <a href="%s">View Event</a>', 'squarecandy-votebuilder-events'), esc_url( $permalink ) ),
		7 => __('Event saved.', 'squarecandy-votebuilder-events'),
		8 => sprintf( __('Event submitted. <a target="_blank" href="%s">Preview Event</a>', 'squarecandy-votebuilder-events'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		9 => sprintf( __('Event scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Event</a>', 'squarecandy-votebuilder-events'),
		// translators: Publish box date format, see https://secure.php.net/manual/en/function.date.php
		date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( $permalink ) ),
		10 => sprintf( __('Event draft updated. <a target="_blank" href="%s">Preview Event</a>', 'squarecandy-votebuilder-events'), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);

	return $messages;
}
add_filter( 'post_updated_messages', 'event_updated_messages' );
