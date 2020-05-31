<?php

function event_init() {

	$supports = array( 'title', 'editor', 'author', 'thumbnail' );
	$supports = apply_filters( 'squarecandy_filter_events_supports', $supports );
	register_post_type(
		'event',
		array(
			'labels'                => array(
				'name'               => __( 'Events', 'squarecandy-acf-events' ),
				'singular_name'      => __( 'Event', 'squarecandy-acf-events' ),
				'all_items'          => __( 'All Events', 'squarecandy-acf-events' ),
				'new_item'           => __( 'New Event', 'squarecandy-acf-events' ),
				'add_new'            => __( 'Add New', 'squarecandy-acf-events' ),
				'add_new_item'       => __( 'Add New Event', 'squarecandy-acf-events' ),
				'edit_item'          => __( 'Edit Event', 'squarecandy-acf-events' ),
				'view_item'          => __( 'View Event', 'squarecandy-acf-events' ),
				'search_items'       => __( 'Search Events', 'squarecandy-acf-events' ),
				'not_found'          => __( 'No Events found', 'squarecandy-acf-events' ),
				'not_found_in_trash' => __( 'No Events found in trash', 'squarecandy-acf-events' ),
				'parent_item_colon'  => __( 'Parent Event', 'squarecandy-acf-events' ),
				'menu_name'          => __( 'Events', 'squarecandy-acf-events' ),
			),
			'public'                => true,
			'hierarchical'          => false,
			'show_ui'               => true,
			'show_in_nav_menus'     => true,
			'supports'              => $supports,
			'has_archive'           => true,
			'rewrite'               => true,
			'query_var'             => true,
			'menu_icon'             => 'dashicons-calendar-alt',
			'show_in_rest'          => true,
			'rest_base'             => 'event',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
		)
	);

}
add_action( 'init', 'event_init' );

function event_updated_messages( $messages ) {
	global $post;

	$permalink = get_permalink( $post );

	$revision = filter_input( INPUT_GET, 'revision', FILTER_SANITIZE_NUMBER_INT );

	$messages['event'] = array(
		0  => '', // Unused. Messages start at index 1.
		/* translators: %s: permalink */
		1  => sprintf( __( 'Event updated. <a href="%s">View Event</a>', 'squarecandy-acf-events' ), esc_url( $permalink ) ),
		2  => __( 'Custom field updated.', 'squarecandy-acf-events' ),
		3  => __( 'Custom field deleted.', 'squarecandy-acf-events' ),
		4  => __( 'Event updated.', 'squarecandy-acf-events' ),
		/* translators: %s: date and time of the revision */
		5  => isset( $revision ) ? sprintf( __( 'Event restored to revision from %s', 'squarecandy-acf-events' ), wp_post_revision_title( $revision, false ) ) : false,
		/* translators: %s: permalink */
		6  => sprintf( __( 'Event published. <a href="%s">View Event</a>', 'squarecandy-acf-events' ), esc_url( $permalink ) ),
		7  => __( 'Event saved.', 'squarecandy-acf-events' ),
		/* translators: %s: permalink */
		8  => sprintf( __( 'Event submitted. <a href="%s">Preview Event</a>', 'squarecandy-acf-events' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
		9  => sprintf(
			// translators: Publish box date format, see https://secure.php.net/manual/en/function.date.php
			__( 'Event scheduled for: <strong>%1$s</strong>. <a href="%2$s">Preview Event</a>', 'squarecandy-acf-events' ),
			date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ),
			esc_url( $permalink )
		),
		/* translators: %s: post permalink */
		10 => sprintf( __( 'Event draft updated. <a href="%s">Preview Event</a>', 'squarecandy-acf-events' ), esc_url( add_query_arg( 'preview', 'true', $permalink ) ) ),
	);

	return $messages;
}
add_filter( 'post_updated_messages', 'event_updated_messages' );
