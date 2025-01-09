<?php

/**
 * Elements:
 *
 * Added "shadow" 'event-works-category' taxonomy to store 'works-category' on event posts (and be filtered by ACP)
 * 'featured_works_cats' postmeta on event post to keep track of which works cause which categories to be attached
 * Checkbox to enable sync
 * Bulk update to populate the taxonomy etc on first run
 * acf save post actions for events and works edit screen
 * pre_post_update for works inline edit
 * edit taxonomy action for 'works-category'
 * utility functions
 */

class SQC_Sync_Work_Categories {

	const ORIGINAL_TAX_SLUG  = 'works-category';
	const ORIGINAL_POST_TYPE = 'works';
	const WORKS_CAT_FIELD    = 'field_5839d8ee566b2'; // work category
	const TARGET_TAX_SLUG    = 'event-works-category';
	const TARGET_POST_TYPE   = 'event';
	const OPTIONS_KEY        = 'sync_event_work_categories';

	public function __construct() {

		add_action( 'init', array( $this, 'init' ), 100 );

	}

	// register works-category taxonomy for event post type
	public function init() {

		if ( self::works_plugin_active() ) :

			$this->add_option_page_settings();

			if ( self::sync_active() ) :

				$this->register_taxonomy();

				add_action( 'admin_init', array( $this, 'bulk_sync_categories' ) );

				// when we're changing the name or slug of the original category
				add_action( 'edited_' . self::ORIGINAL_TAX_SLUG, array( $this, 'edited_original_category' ), 10, 2 );

				// when we're editing an event check if we added or removed a work
				add_action( 'acf/save_post', array( $this, 'save_post_event' ), 5 ); // 5 so pre-save

				// when we're editing a work check if we added or removed a work category
				add_action( 'acf/save_post', array( $this, 'save_post_work' ), 5 );

				// when we're inline editing a work check if we added or removed a work category
				add_action( 'pre_post_update', array( $this, 'pre_post_update_work' ), 10, 2 );

				// handle changes when bulk editing using Admin Columns Pro
				add_filter( 'acp/editing/save_value', array( $this, 'acp_bulk_edit' ), 10, 3 );

			endif;

		endif;

	}

	public static function works_plugin_active() {
		return taxonomy_exists( self::ORIGINAL_TAX_SLUG ) && post_type_exists( self::ORIGINAL_POST_TYPE );
	}

	public static function sync_active() {
		return get_option( 'options_' . self::OPTIONS_KEY );
	}

	public function add_option_page_settings() {

		if ( function_exists( 'acf_add_local_field_group' ) ) :

			//add ACF fields on 'Events Settings' page
			acf_add_local_field_group(
				array(
					'key'                   => 'group_5sn691f762044',
					'title'                 => 'Sync Work Categories',
					'fields'                => array(
						array(
							'key'           => 'field_eventworkcats39194651',
							'label'         => '',
							'name'          => self::OPTIONS_KEY,
							'type'          => 'true_false',
							'message'       => 'Store work categories on associated events',
							'default_value' => 0,
							'wrapper'       => array(
								'width' => '48',
							),
						),
					),
					'location'              => array(
						array(
							array(
								'param'    => 'options_page',
								'operator' => '==',
								'value'    => 'acf-options-event-settings',
							),
						),
					),
					'menu_order'            => 0,
					'position'              => 'normal',
					'style'                 => 'default',
					'label_placement'       => 'top',
					'instruction_placement' => 'label',
					'hide_on_screen'        => '',
					'active'                => 1,
					'description'           => '',
				)
			);

		endif;
	}

	// add the new shadow taxonomy
	public function register_taxonomy() {

		$taxonomy_args = array(
			'hierarchical'          => false,
			'public'                => false,
			'show_in_nav_menus'     => false,
			'show_ui'               => false,
			'show_admin_column'     => false,
			'query_var'             => true,
			'rewrite'               => true,
			'capabilities'          => array(
				'manage_terms' => 'edit_posts',
				'edit_terms'   => 'edit_posts',
				'delete_terms' => 'edit_posts',
				'assign_terms' => 'edit_posts',
			),
			'labels'                => array(
				'name'                       => __( 'Works categories', 'squarecandy' ),
				'singular_name'              => _x( 'Works category', 'taxonomy general name', 'squarecandy' ),
				'search_items'               => __( 'Search works categories', 'squarecandy' ),
				'popular_items'              => __( 'Popular works categories', 'squarecandy' ),
				'all_items'                  => __( 'All works categories', 'squarecandy' ),
				'parent_item'                => __( 'Parent works category', 'squarecandy' ),
				'parent_item_colon'          => __( 'Parent works category:', 'squarecandy' ),
				'edit_item'                  => __( 'Edit works category', 'squarecandy' ),
				'update_item'                => __( 'Update works category', 'squarecandy' ),
				'add_new_item'               => __( 'New works category', 'squarecandy' ),
				'new_item_name'              => __( 'New works category', 'squarecandy' ),
				'separate_items_with_commas' => __( 'Separate works categories with commas', 'squarecandy' ),
				'add_or_remove_items'        => __( 'Add or remove works categories', 'squarecandy' ),
				'choose_from_most_used'      => __( 'Choose from the most used works categories', 'squarecandy' ),
				'not_found'                  => __( 'No works categories found.', 'squarecandy' ),
				'menu_name'                  => __( 'Work Categories', 'squarecandy' ),
			),
			'show_in_rest'          => true,
			'rest_base'             => self::TARGET_TAX_SLUG,
			'rest_controller_class' => 'WP_REST_Terms_Controller',
			'meta_box_cb'           => false,
		);

		if ( WP_DEBUG ) {
			$taxonomy_args['show_ui'] = true;
			unset( $taxonomy_args['meta_box_cb'] );
		}

		register_taxonomy( self::TARGET_TAX_SLUG, array( self::TARGET_POST_TYPE ), $taxonomy_args );

	}

	// on first run: populate the shadow taxonomy and write the terms for each event
	public function bulk_sync_categories() {

		// set a transient to make sure it only runs once
		$transient = 'squarecandy_bulk_sync_event_work_cats202205';
		$all_done  = true;

		// only user id 1 or 7 can run this update
		if ( ! in_array( get_current_user_id(), array( 1, 7 ), true ) || get_transient( $transient ) ) {
			return;
		}

		sqcdy_log( 'SQC_Sync_Work_Categories starting bulk sync' );

		$cat_args = array(
			'hide_empty' => false,
			'taxonomy'   => self::ORIGINAL_TAX_SLUG,
		);

		$work_cats = get_terms( $cat_args );
		sqcdy_log( $work_cats, 'Existing categories' );

		if ( $work_cats ) :

			foreach ( $work_cats as $work_cat ) :
				$new_cat = null;
				if ( ! get_term_by( 'slug', $work_cat->slug, self::TARGET_TAX_SLUG ) ) {
					$new_cat = wp_insert_term( $work_cat->name, self::TARGET_TAX_SLUG, array( 'slug' => $work_cat->slug ) );
				}
				if ( $new_cat && ! is_wp_error( $new_cat ) ) {
					// store bidirectional meta to look up the two taxonomies
					update_term_meta( $work_cat->term_id, 'event_work_cat_id', $new_cat['term_id'] );
					update_term_meta( $new_cat['term_id'], 'work_cat_id', $work_cat->term_id );
				}
			endforeach;

		endif;

		$args = array(
			'post_type'              => self::TARGET_POST_TYPE,
			'posts_per_page'         => -1,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'fields'                 => 'ids',
		);

		$events = new WP_Query( $args );

		foreach ( $events->posts as $event_id ) {

			$works = get_post_meta( $event_id, 'featured_works', true );
			$meta  = array();
			$terms = array();

			sqcdy_log( $works, 'Works associated with event ' . $event_id );

			if ( is_array( $works ) ) {
				foreach ( $works as $work_id ) {
					$work_cats        = self::get_work_cats_for_event( $event_id, $work_id );
					$meta[ $work_id ] = $work_cats;
					$terms            = array_unique( array_merge( $terms, $work_cats ) );
				}
			}

			sqcdy_log( $terms, 'Categories associated with event ' . $event_id );

			// set postmeta
			delete_post_meta( $event_id, 'featured_works_cats' );
			update_post_meta( $event_id, 'featured_works_cats', $meta );

			// store terms
			wp_set_post_terms( $event_id, $terms, self::TARGET_TAX_SLUG, false ); //overwrite

		}

		if ( $all_done ) {
			set_transient( $transient, 'locked', 0 ); // lock function forever until transients are cleared
			wp_die( 'Synced Work Categories to Events' );
		}
	}

	// when we're changing the name or slug of the original category
	public function edited_original_category( $term_id, $tt_id ) {

		// get the id of the corresponding event work category
		$work_cat_term       = get_term( $term_id );
		$event_work_cat_id   = get_term_meta( $term_id, 'event_work_cat_id', true );
		$event_work_cat_term = get_term( $event_work_cat_id );
		$track_properties    = array( 'name', 'slug' );
		$changed_properties  = array();

		foreach ( $track_properties as $property ) {
			if ( $event_work_cat_term->$property !== $work_cat_term->$property ) {
				$changed_properties[ $property ] = $work_cat_term->$property;
			}
		}

		sqcdy_log( $changed_properties, 'Changed taxonomy properties for ' . $work_cat_term->slug );

		if ( $changed_properties ) {
			wp_update_term( $event_work_cat_id, self::TARGET_TAX_SLUG, $changed_properties );
		}

	}

	public function save_post_event( $post_id ) {

		$post = get_post( $post_id );

		// only run for post type work
		if ( self::TARGET_POST_TYPE !== $post->post_type ) {
			return;
		}

		if ( ! $this->verify_save_post( $post ) ) {
			return;
		}

		$event_id      = $post_id;
		$field_key     = 'field_5841cdf6350d1'; //featured_works
		$works         = get_post_meta( $post_id, 'featured_works', true );
		$updated_works = isset( $_POST['acf'][ $field_key ] ) ? $_POST['acf'][ $field_key ] : array(); //phpcs:ignore WordPress.Security.NonceVerification.Missing

		sqcdy_log( $works, 'Previous works for event ' . $post_id );
		sqcdy_log( $updated_works, 'Updated works for event ' . $post_id );

		// if work list has been changed
		if ( $updated_works !== $works ) {

			$all_work_cats   = array();
			$event_work_cats = array();
			$work_meta       = array();

			// could look at added/removed, but we might end up removing a category that should still be there
			// so we need to loop through all works
			foreach ( $updated_works as $work_id ) {

				$work_cats     = get_the_terms( $work_id, self::ORIGINAL_TAX_SLUG );
				$all_work_cats = array_merge( $all_work_cats, $work_cats );

				sqcdy_log( $work_cats, 'Categories for work ' . $work_id );

				foreach ( $work_cats as $work_cat_term ) {

					// look up corresponding event work cat
					$event_work_cat_id = get_term_meta( $work_cat_term->term_id, 'event_work_cat_id', true );

					// add to meta array
					$work_meta[ $work_id ][ $work_cat_term->term_id ] = $event_work_cat_id;

					// add to array of all cats that we'll use to update the terms
					$event_work_cats[] = (int) $event_work_cat_id;

				}
			}

			// get rid of duplicates
			$event_work_cats = array_unique( $event_work_cats );

			sqcdy_log( $event_work_cats, 'All categories for event ' . $post_id );
			sqcdy_log( $work_meta, 'New postmeta for event ' . $post_id );

			// update the meta
			delete_post_meta( $event_id, 'featured_works_cats' );
			update_post_meta( $event_id, 'featured_works_cats', $work_meta );

			// update the terms
			wp_set_post_terms( $post_id, $event_work_cats, self::TARGET_TAX_SLUG, false ); //overwrite, don't append
		}
	}

	//when we're editing a work check if we added or removed a work category
	public function save_post_work( $post_id ) {

		$post = get_post( $post_id );

		// only run for post type work
		if ( self::ORIGINAL_POST_TYPE !== $post->post_type ) {
			return;
		}

		if ( ! $this->verify_save_post( $post ) ) {
			return;
		}

		$field_key    = self::WORKS_CAT_FIELD; // work category
		$cats         = get_the_terms( $post_id, self::ORIGINAL_TAX_SLUG );
		$cat_ids      = $cats ? wp_list_pluck( $cats, 'term_id' ) : array();
		$updated_cats = isset( $_POST['acf'][ $field_key ] ) ? $_POST['acf'][ $field_key ] : array(); //phpcs:ignore WordPress.Security.NonceVerification.Missing

		$this->handle_work_changes( $post_id, $cat_ids, $updated_cats );

	}

	//when we're inline editing a work check if we added or removed a work category
	public function pre_post_update_work( $post_id, $data ) {

		$post = get_post( $post_id );

		// only run for post type work
		if ( self::ORIGINAL_POST_TYPE !== $post->post_type ) {
			return;
		}

		if ( ! $this->verify_save_post( $post, 'inline' ) ) {
			return;
		}

		sqcdy_log( 'inline work save' );

		$cats         = get_the_terms( $post_id, self::ORIGINAL_TAX_SLUG );
		$cat_ids      = $cats ? wp_list_pluck( $cats, 'term_id' ) : array();
		$updated_cats = array();

		//phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( empty( $_POST['tax_input'][ self::ORIGINAL_TAX_SLUG ] ) ) {
			$term_names = array();
		} elseif ( is_array( $_POST['tax_input'][ self::ORIGINAL_TAX_SLUG ] ) ) {
			$term_names = $_POST['tax_input'][ self::ORIGINAL_TAX_SLUG ];
		} else {
			$term_names = explode( ', ', $_POST['tax_input'][ self::ORIGINAL_TAX_SLUG ] );
		}
		//phpcs:enable WordPress.Security.NonceVerification.Missing

		foreach ( $term_names as $term_name ) {
			$term = get_term_by( 'name', $term_name, self::ORIGINAL_TAX_SLUG );
			if ( $term ) {
				$updated_cats[] = $term->term_id;
			}
		}

		$this->handle_work_changes( $post_id, $cat_ids, $updated_cats );

	}

	/**
	 * handle changes when bulk editing using Admin Columns Pro
	 * @param array{value: array, method: string} $value
	 * @param AC\Column $column
	 * @param int|string $post_id
	 *
	 * @return array{value: array, method: string}
	 */

	private function acp_bulk_edit( $value, $column, $post_id ) {

		if ( $column->get_post_type() === self::ORIGINAL_POST_TYPE && $column->get_type() === self::WORKS_CAT_FIELD ) {

			sqcdy_log( $post_id, 'acp_bulk_inline_edit' );
			sqcdy_log( $value, 'Edit value' );

			$cats    = get_the_terms( $post_id, self::ORIGINAL_TAX_SLUG );
			$cat_ids = $cats ? wp_list_pluck( $cats, 'term_id' ) : array();

			switch ( $value['method'] ) {
				case 'add':
					$updated_cats = array_merge( $cat_ids, $value['value'] );
					break;
				case 'remove':
					$updated_cats = array_diff( $cat_ids, $value['value'] );
					break;
				case 'replace':
					$updated_cats = $value['value'];
					break;
			}

			if ( isset( $updated_cats ) ) {
				$this->handle_work_changes( $post_id, $cat_ids, $updated_cats );
			}
		}

		return $value;
	}

	/**
	 * When a work's categories are updated, populate those changes to the associated events.
	 *
	 * @param int|string $post_id work ID
	 * @param array $cat_ids Previous categories for work
	 * @param array $updated_cats Updated categories for work
	 */

	private function handle_work_changes( $post_id, $cat_ids, $updated_cats ) {

		sqcdy_log( $cat_ids, 'Previous categories for work ' . $post_id );
		sqcdy_log( $updated_cats, 'Updated categories for work ' . $post_id );

		// if category list has been changed
		if ( $updated_cats !== $cat_ids ) {

			// get added/removed categories
			$removed_cats = array_diff( $cat_ids, $updated_cats );

			// get associated events
			$events = self::get_events_for_work( $post_id );

			sqcdy_log( $removed_cats, 'Removed categories for work ' . $post_id );
			sqcdy_log( $events, 'Events for ' . $post_id );

			// loop through events to add/remove categories
			foreach ( $events as $event_id ) {

				$work_meta = get_post_meta( $event_id, 'featured_works_cats', true );
				$work_meta = is_array( $work_meta ) ? $work_meta : array();

				sqcdy_log( $work_meta, 'Stored category postmeta for event ' . $event_id );

				// clear the meta for this work
				unset( $work_meta[ $post_id ] );

				//check if we should keep the category bc it's attached to another work associated with this event
				foreach ( $removed_cats as $removed_cat_id ) {

					$keep = false;

					// loop through other works attached to this event
					foreach ( $work_meta as $work => $cats ) {

						if ( in_array( $removed_cat_id, array_keys( $cats ), true ) ) {
							$keep = true;
							break;
						}
					}

					if ( ! $keep ) {
						sqcdy_log( $removed_cat_id, 'Removing categories from event' );
						$event_work_cat_id = get_term_meta( $removed_cat_id, 'event_work_cat_id', true );
						wp_remove_object_terms( $event_id, array( (int) $event_work_cat_id ), self::TARGET_TAX_SLUG );
					}
				}

				// don't worry about checking for adding, wp won't create duplicate term relationships
				// re-add all categories on saved work just in case
				foreach ( $updated_cats as $added_cat_id ) {
					sqcdy_log( $added_cat_id, 'Adding categories to event' );
					$event_work_cat_id = get_term_meta( $added_cat_id, 'event_work_cat_id', true );
					wp_set_post_terms( $event_id, array( (int) $event_work_cat_id ), self::TARGET_TAX_SLUG, true );
					$work_meta[ $post_id ][ $added_cat_id ] = $event_work_cat_id;
				}

				// update meta
				update_post_meta( $event_id, 'featured_works_cats', $work_meta );
				sqcdy_log( $work_meta, 'New category postmeta for event ' . $event_id );

			}
		}

	}

	private function verify_save_post( $post, $type = 'main' ) {

		// if new post or autosave not saving meta so bail (?)
		if ( 'auto-draft' === $post->post_status || defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE || empty( $_POST ) ) {
			// phpcs:ignore Squiz.PHP.CommentedOutCode.Found
			//sqcdy_log( 'Save post aborted: auto draft / auto save / Broadcaster save' ); //logging this causes issues when DB_DEBIG or DEBUG_LOG are set
			return;
		}

		//check nonces
		if ( 'main' === $type ) {
			$nonce_key = '_wpnonce';
			$action    = 'update-post_' . $post->ID;
		} elseif ( 'inline' === $type ) {
			$nonce_key = '_inline_edit';
			$action    = 'inlineeditnonce';
		} else {
			return;
		}

		$nonce = isset( $_POST[ $nonce_key ] ) && wp_verify_nonce( $_POST[ $nonce_key ], $action );

		if ( ! $nonce ) {
			sqcdy_log( 'Save post aborted: incorrect nonce' );
			return false;
		}

		return true;

	}

	// Utility functions

	// returns array of post ids
	public static function get_events_for_work( $work_id ) {
		$args   = array(
			'posts_per_page' => -1,
			'post_type'      => self::TARGET_POST_TYPE,
			'meta_query'     => array(
				array(
					'key'     => 'featured_works',
					'value'   => '"' . $work_id . '"',
					'compare' => 'LIKE',
				),
			),
			'fields'         => 'ids',
		);
		$events = get_posts( $args );
		return $events;
	}

	// returns array of arrays: work category id => event work category id
	public static function get_work_cats_for_event( $event_id, $work_id ) {

		$work_cats = get_the_terms( $work_id, self::ORIGINAL_TAX_SLUG );
		$meta      = array();

		if ( is_array( $work_cats ) ) {

			foreach ( $work_cats as $work_cat ) {
				$event_work_cat_id          = get_term_meta( $work_cat->term_id, 'event_work_cat_id', true );
				$meta[ $work_cat->term_id ] = (int) $event_work_cat_id;
			}
		}

		return $meta;
	}
}
