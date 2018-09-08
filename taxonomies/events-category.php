<?php

/**
 * Registers the `events_category` taxonomy,
 * for use with 'event'.
 */
function events_category_init() {
	register_taxonomy( 'events-category', array( 'event' ), array(
		'hierarchical'      => false,
		'public'            => true,
		'show_in_nav_menus' => true,
		'show_ui'           => true,
		'show_admin_column' => false,
		'query_var'         => true,
		'rewrite'           => true,
		'capabilities'      => array(
			'manage_terms'  => 'edit_posts',
			'edit_terms'    => 'edit_posts',
			'delete_terms'  => 'edit_posts',
			'assign_terms'  => 'edit_posts',
		),
		'labels'            => array(
			'name'                       => __( 'Events categories', 'squarecandy-acf-events' ),
			'singular_name'              => _x( 'Events category', 'taxonomy general name', 'squarecandy-acf-events' ),
			'search_items'               => __( 'Search Events categories', 'squarecandy-acf-events' ),
			'popular_items'              => __( 'Popular Events categories', 'squarecandy-acf-events' ),
			'all_items'                  => __( 'All Events categories', 'squarecandy-acf-events' ),
			'parent_item'                => __( 'Parent Events category', 'squarecandy-acf-events' ),
			'parent_item_colon'          => __( 'Parent Events category:', 'squarecandy-acf-events' ),
			'edit_item'                  => __( 'Edit Events category', 'squarecandy-acf-events' ),
			'update_item'                => __( 'Update Events category', 'squarecandy-acf-events' ),
			'view_item'                  => __( 'View Events category', 'squarecandy-acf-events' ),
			'add_new_item'               => __( 'New Events category', 'squarecandy-acf-events' ),
			'new_item_name'              => __( 'New Events category', 'squarecandy-acf-events' ),
			'separate_items_with_commas' => __( 'Separate events categories with commas', 'squarecandy-acf-events' ),
			'add_or_remove_items'        => __( 'Add or remove events categories', 'squarecandy-acf-events' ),
			'choose_from_most_used'      => __( 'Choose from the most used events categories', 'squarecandy-acf-events' ),
			'not_found'                  => __( 'No events categories found.', 'squarecandy-acf-events' ),
			'no_terms'                   => __( 'No events categories', 'squarecandy-acf-events' ),
			'menu_name'                  => __( 'Events categories', 'squarecandy-acf-events' ),
			'items_list_navigation'      => __( 'Events categories list navigation', 'squarecandy-acf-events' ),
			'items_list'                 => __( 'Events categories list', 'squarecandy-acf-events' ),
			'most_used'                  => _x( 'Most Used', 'events-category', 'squarecandy-acf-events' ),
			'back_to_items'              => __( '&larr; Back to Events categories', 'squarecandy-acf-events' ),
		),
		'show_in_rest'      => true,
		'rest_base'         => 'events-category',
		'rest_controller_class' => 'WP_REST_Terms_Controller',
	) );

}
add_action( 'init', 'events_category_init' );

/**
 * Sets the post updated messages for the `events_category` taxonomy.
 *
 * @param  array $messages Post updated messages.
 * @return array Messages for the `events_category` taxonomy.
 */
function events_category_updated_messages( $messages ) {

	$messages['events-category'] = array(
		0 => '', // Unused. Messages start at index 1.
		1 => __( 'Events category added.', 'squarecandy-acf-events' ),
		2 => __( 'Events category deleted.', 'squarecandy-acf-events' ),
		3 => __( 'Events category updated.', 'squarecandy-acf-events' ),
		4 => __( 'Events category not added.', 'squarecandy-acf-events' ),
		5 => __( 'Events category not updated.', 'squarecandy-acf-events' ),
		6 => __( 'Events categories deleted.', 'squarecandy-acf-events' ),
	);

	return $messages;
}
add_filter( 'term_updated_messages', 'events_category_updated_messages' );
