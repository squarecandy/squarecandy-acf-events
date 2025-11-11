<?php
// Add ACF Fields and Functions for Event

add_action( 'acf/include_fields', 'squarecandy_events_add_fields' );
function squarecandy_events_add_fields() {

	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	$eventfields = array();
	$is_views2   = sqcdy_is_views2( 'events' );

	$eventfields['featured_image'] = array(
		'key'           => 'field_eventsfeaturedimage94124',
		'label'         => 'Featured Image',
		'name'          => '_thumbnail_id',
		'type'          => 'image',
		'instructions'  => '',
		'required'      => 0,
		'return_format' => 'array',
		'preview_size'  => 'medium',
		'library'       => 'all',
		'wrapper'       => array(
			'width' => '39',
			'class' => '',
			'id'    => '',
		),
	);

	$eventfields['featured'] = array(
		'key'               => 'field_5d2c47ed9268c',
		'label'             => 'Feature Event',
		'name'              => 'featured',
		'type'              => 'true_false',
		'instructions'      => '',
		'required'          => 0,
		'conditional_logic' => 0,
		'wrapper'           => array(
			'width' => '61',
			'class' => '',
			'id'    => '',
		),
		'message'           => '',
		'default_value'     => 0,
		'ui'                => 1,
		'ui_on_text'        => 'Yes',
		'ui_off_text'       => 'No',
	);

	$eventfields['start_date'] = array(
		'key'               => 'field_5616bbe39fbec',
		'label'             => '(Start) Date',
		'name'              => 'start_date',
		'type'              => 'date_picker',
		'required'          => 1,
		'conditional_logic' => 0,
		'wrapper'           => array(
			'width' => '25',
			'class' => 'start-date',
		),
		'display_format'    => 'F j, Y',
		'return_format'     => 'F j, Y',
		'first_day'         => 0,
	);

	$eventfields['all_day'] = array(
		'key'           => 'field_5616bcdfb642d',
		'label'         => 'All Day',
		'name'          => 'all_day',
		'type'          => 'true_false',
		'wrapper'       => array(
			'width' => 10,
			'class' => 'start-date',
		),
		'default_value' => 0,
	);

	$eventfields['multi_day'] = array(
		'key'           => 'field_5616bd4ca2b0f',
		'label'         => 'Show End Date/Time',
		'name'          => 'multi_day',
		'type'          => 'true_false',
		'wrapper'       => array(
			'width' => '15',
			'class' => 'start-date',
		),
		'default_value' => 0,
	);

	$eventfields['start_time'] = array(
		'key'               => 'field_5616bc2b9fbed',
		'label'             => '(Start) Time',
		'name'              => 'start_time',
		'type'              => 'time_picker',
		'required'          => 1,
		'conditional_logic' => array(
			array(
				array(
					'field'    => 'field_5616bcdfb642d',
					'operator' => '!=',
					'value'    => '1',
				),
			),
		),
		'wrapper'           => array(
			'width' => '20',
			'class' => 'start-date',
		),
		'display_format'    => 'g:i a',
		'return_format'     => 'g:i a',
	);

	$eventfields['timezone'] = array(
		'key'               => 'field_fn48bedm4dn49',
		'label'             => 'Timezone',
		'name'              => 'timezone',
		'type'              => 'select',
		'conditional_logic' => 0,
		'choices'           => squarecandy_timezone_choice(),
		'allow_null'        => 1,
		'multiple'          => 0,
		'ui'                => 1,
		'wrapper'           => array(
			'width' => '25',
		),
		'instructions'      => 'leave empty for default: ' . wp_timezone_string(),
	);

	$eventfields['end_date'] = array(
		'key'               => 'field_5616bd75112ca',
		'label'             => 'End Date',
		'name'              => 'end_date',
		'type'              => 'date_picker',
		'conditional_logic' => array(
			array(
				array(
					'field'    => 'field_5616bd4ca2b0f',
					'operator' => '==',
					'value'    => '1',
				),
			),
		),
		'wrapper'           => array(
			'width' => '70',
		),
		'display_format'    => 'F j, Y',
		'return_format'     => 'F j, Y',
		'first_day'         => 0,
	);

	$eventfields['end_time'] = array(
		'key'               => 'field_5616bd8e112cb',
		'label'             => 'End Time',
		'name'              => 'end_time',
		'type'              => 'time_picker',
		'required'          => 0,
		'conditional_logic' => array(
			array(
				array(
					'field'    => 'field_5616bcdfb642d',
					'operator' => '!=',
					'value'    => '1',
				),
				array(
					'field'    => 'field_5616bd4ca2b0f',
					'operator' => '==',
					'value'    => '1',
				),
			),
		),
		'wrapper'           => array(
			'width' => '30',
		),
		'display_format'    => 'g:i a',
		'return_format'     => 'g:i a',
	);

	$eventfields['venue'] = array(
		'key'               => 'field_5616bedeed0a9',
		'label'             => 'Venue Name',
		'name'              => 'venue',
		'type'              => 'text',
		'conditional_logic' => 0,
		'wrapper'           => array(
			'width' => 50,
		),
	);

	$eventfields['venue_link'] = array(
		'key'     => 'field_5616beefed0aa',
		'label'   => 'Venue Link',
		'name'    => 'venue_link',
		'type'    => 'url',
		'wrapper' => array(
			'width' => 50,
		),
	);

	$eventfields['address'] = array(
		'key'   => 'field_address226474957',
		'label' => 'Address',
		'name'  => 'address',
		'type'  => 'text',
	);

	$eventfields['city'] = array(
		'key'     => 'field_city585d8171a157e',
		'label'   => 'City',
		'name'    => 'city',
		'type'    => 'text',
		'wrapper' => array(
			'width' => 50,
		),
	);

	$eventfields['state'] = array(
		'key'     => 'field_state94823hf873',
		'label'   => 'State/Province',
		'name'    => 'state',
		'type'    => 'text',
		'wrapper' => array(
			'width' => 25,
		),
	);

	$eventfields['zip'] = array(
		'key'     => 'field_zipfj8392y38r9',
		'label'   => 'Postal Code',
		'name'    => 'zip',
		'type'    => 'text',
		'wrapper' => array(
			'width' => 25,
		),
	);

	$eventfields['country'] = array(
		'key'   => 'field_country1749283947',
		'label' => 'Country',
		'name'  => 'country',
		'type'  => 'text',
	);

	// override with our own custom countries list if available
	// use define('SQCDY_EVENTS_LEGACY_COUNTRY_FIELD', true); in wp-config.php to force use of the legacy text field instead
	if ( function_exists( 'squarecandy_get_countries' ) && ! defined( 'SQCDY_EVENTS_LEGACY_COUNTRY_FIELD' ) ) {
		$eventfields['country'] = array(
			'key'           => $eventfields['country']['key'],
			'label'         => $eventfields['country']['label'],
			'name'          => $eventfields['country']['name'],
			'type'          => 'select',
			'choices'       => squarecandy_get_countries(),
			'default_value' => get_option( 'options_home_country' ) ?? 'United States',
			'allow_null'    => 1,
			'multiple'      => 0,
			'ui'            => 1,
			'ajax'          => 0,
			'return_format' => 'value',
		);

		// add filter to handle instances where an already stored value isn't on the country list
		add_filter( 'acf/prepare_field/key=' . $eventfields['country']['key'], 'squarecandy_events_prepare_country_field' );
	}

	// only show map fields if an api key has been entered
	if ( get_option( 'options_google_maps_api_key' ) ) :
		$zoom_option  = get_option( 'options_default_zoom_level' );
		$default_zoom = $zoom_option ? $zoom_option : 15;

		$eventfields['venue_location'] = array(
			'key'          => 'field_5616c0e68be8f',
			'label'        => 'Venue Location',
			'name'         => 'venue_location',
			'type'         => 'google_map',
			'instructions' => '',
			'center_lat'   => '40.6976701',
			'center_lng'   => '-74.25987,10',
			'zoom'         => $default_zoom,
			'height'       => '280',
		);

		$eventfields['zoom_level'] = array(
			'key'           => 'field_mapzoom273489241f6',
			'label'         => 'Map Zoom Level',
			'name'          => 'zoom_level',
			'type'          => 'range',
			'instructions'  => 'select how far zoomed in this map appears (setting reflected on front-end event page only, not in the map box above)',
			'default_value' => $default_zoom,
			'min'           => 8,
			'max'           => 21,
			'step'          => 1,
			'prepend'       => '-',
			'append'        => '+',
		);

	endif;

	$eventfields['tickets_link'] = array(
		'key'   => 'field_5616bf58ed0ac',
		'label' => 'Tickets Link',
		'name'  => 'tickets_link',
		'type'  => 'url',
	);

	if ( $is_views2 ) :

		$subfields = array();

		if ( is_acf_fontawesome_plugin_active() ) {

			$subfields[] = array(
				'key'               => 'field_67a37d2867e36',
				'label'             => 'Icon',
				'name'              => 'icon',
				'aria-label'        => '',
				'type'              => 'font-awesome',
				'instructions'      => '',
				'required'          => 0,
				'conditional_logic' => 0,
				'wrapper'           => array(
					'width' => '',
					'class' => '',
					'id'    => '',
				),
				'icon_sets'         => array(
					0 => 'solid',
					1 => 'regular',
					2 => 'brands',
					// some additional options we can maybe add later: 'light', 'sharp_light', 'sharp_regular', 'sharp_solid'
				),
				'custom_icon_set'   => '',
				'default_label'     => '',
				'default_value'     => '',
				'save_format'       => 'element',
				'allow_null'        => 0,
				'show_preview'      => 0,
				'enqueue_fa'        => 0,
				'allow_in_bindings' => 0,
				'fa_live_preview'   => '',
				'choices'           => array(),
				'parent_repeater'   => 'field_67a37d0867e35',
			);
		}

		$subfields[] = array(
			'key'               => 'field_67a37ee267e38',
			'label'             => 'Button Text',
			'name'              => 'button_text',
			'aria-label'        => '',
			'type'              => 'text',
			'instructions'      => '',
			'required'          => 1,
			'conditional_logic' => 0,
			'wrapper'           => array(
				'width' => '',
				'class' => '',
				'id'    => '',
			),
			'default_value'     => '',
			'maxlength'         => 80,
			'allow_in_bindings' => 0,
			'placeholder'       => '',
			'prepend'           => '',
			'append'            => '',
			'parent_repeater'   => 'field_67a37d0867e35',
		);

		$subfields[] = array(
			'key'               => 'field_67a37e8d67e37',
			'label'             => 'Link',
			'name'              => 'link',
			'aria-label'        => '',
			'type'              => 'url',
			'instructions'      => '',
			'required'          => 1,
			'conditional_logic' => 0,
			'wrapper'           => array(
				'width' => '',
				'class' => '',
				'id'    => '',
			),
			'default_value'     => '',
			'allow_in_bindings' => 0,
			'placeholder'       => '',
			'parent_repeater'   => 'field_67a37d0867e35',
		);

		// new buttons repeater
		$eventfields['more_info_buttons'] = array(
			'key'                           => 'field_67a37d0867e35',
			'label'                         => 'More Info Buttons',
			'name'                          => 'more_info_buttons',
			'aria-label'                    => '',
			'type'                          => 'repeater',
			'instructions'                  => '',
			'required'                      => 0,
			'conditional_logic'             => 0,
			'wrapper'                       => array(
				'width' => '',
				'class' => '',
				'id'    => '',
			),
			'acfe_repeater_stylised_button' => 0,
			'layout'                        => 'table',
			'pagination'                    => 0,
			'min'                           => 0,
			'max'                           => 0,
			'collapsed'                     => 'field_67a37ee267e38',
			'button_label'                  => 'Add Button',
			'rows_per_page'                 => 20,
			'sub_fields'                    => $subfields,
		);
	endif;

	$on_edit_screen = is_admin() && isset( $_GET['post'] ) && isset( $_GET['action'] ) && 'edit' === $_GET['action'] && 'event' === get_post_type( $_GET['post'] );

	// phpcs:disable WordPress.Security.NonceVerification.Recommended
	if (
		// if we're not in views 2 mode
		( ! $is_views2 ) ||
		// or if we're on the front end anytime
		( ! is_admin() ) ||
		// or if we're on the edit screen for an event and the current event has a value for more_info_link
		( $on_edit_screen && get_field( 'more_info_link', $_GET['post'] ) ) ||
		// or if we're on the edit screen for an event and the current event has a value for facebook_link
		( $on_edit_screen && get_field( 'facebook_link', $_GET['post'] ) ) ||
		// or if we're in the admin and not editing an event (needed for saving legacy values, etc)
		( is_admin() && ! isset( $_GET['post'] ) && ! isset( $_GET['post_type'] ) )
	) :

		$eventfields['more_info_link'] = array(
			'key'     => 'field_5616befced0ab',
			'label'   => $is_views2 ? 'More Info Link (Legacy)' : 'More Info Link',
			'name'    => 'more_info_link',
			'type'    => 'url',
			'wrapper' => array(
				'width' => 50,
			),
		);

		$eventfields['facebook_link'] = array(
			'key'     => 'field_facebooklink7293484',
			'label'   => $is_views2 ? 'Facebook Event Link (Legacy)' : 'Facebook Event Link',
			'name'    => 'facebook_link',
			'type'    => 'url',
			'wrapper' => array(
				'width' => 50,
			),
		);
	endif;
	// phpcs:enable WordPress.Security.NonceVerification.Recommended

	$eventfields['short_description'] = array(
		'key'           => 'field_5616bf8eed0ad',
		'label'         => 'Short Description',
		'name'          => 'short_description',
		'type'          => 'wysiwyg',
		'instructions'  => 'A short text description for the event. Limit 210 characters.	You may repeat this text and elaborate further in the main body field below.',
		'wrapper'       => array(
			'width' => '',
			'class' => 'short_wysiwyg',
			'id'    => '',
		),
		'tabs'          => 'all',
		'toolbar'       => 'basic',
		'media_upload'  => 0,
		'default_value' => '',
		'delay'         => 0,
	);

	if ( get_option( 'options_enable_categories' ) ) :

		$eventfields['category'] = array(
			'key'           => 'field_5b9318c9d74e7',
			'label'         => 'Category',
			'name'          => 'category',
			'type'          => 'taxonomy',
			'taxonomy'      => 'events-category',
			'field_type'    => 'checkbox',
			'add_term'      => 1,
			'save_terms'    => 1,
			'load_terms'    => 1,
			'return_format' => 'object',
			'multiple'      => 1,
			'allow_null'    => 1,
		);

		add_action( 'admin_menu', 'remove_default_event_category_metabox' );

	endif;

	// allow linking to "works" if the Square Candy ACF Composer Works plugin is enabled
	if ( function_exists( 'is_plugin_active' ) && ( is_plugin_active( 'squarecandy-acf-works/squarecandy-acf-works.php' ) || is_plugin_active( 'squarecandy-acf-works/plugin.php' ) ) ) :
		$eventfields['featured_works'] = array(
			'key'                     => 'field_5841cdf6350d1',
			'label'                   => 'Featured Works',
			'name'                    => 'featured_works',
			'type'                    => 'post_object',
			'post_type'               => array(
				0 => 'works',
			),
			'allow_null'              => 1,
			'multiple'                => 1,
			'acf_relationship_create' => 1,
			'ui'                      => 1,
			'return_format'           => 'object',
		);
	endif;

	$eventfields = apply_filters( 'squarecandy_filter_events_fields', $eventfields );

	acf_add_local_field_group(
		array(
			'key'                   => 'group_5616bbdb43b9f',
			'title'                 => 'Event Fields',
			'fields'                => $eventfields,
			'location'              => array(
				array(
					array(
						'param'    => 'post_type',
						'operator' => '==',
						'value'    => 'event',
					),
				),
			),
			'menu_order'            => 0,
			'position'              => 'acf_after_title',
			'style'                 => 'seamless',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen'        => array(
				0 => 'excerpt',
				1 => 'custom_fields',
				2 => 'discussion',
				3 => 'comments',
				4 => 'format',
				5 => 'page_attributes',
				6 => 'categories',
				7 => 'tags',
				8 => 'send-trackbacks',
				9 => 'featured_image',
			),
			'active'                => 1,
			'description'           => '',
		)
	);
}

if ( ! function_exists( 'is_acf_fontawesome_plugin_active' ) ) {
	function is_acf_fontawesome_plugin_active() {
		if (
			class_exists( 'acf_field_font_awesome' ) ||
			in_array(
				'advanced-custom-fields-font-awesome/acf-font-awesome.php',
				(array) get_option( 'active_plugins', array() ),
				true
			)
		) {
			return true;
		}
		return false;
	}
}

// filter the country list to handle instances where the already stored value isn't on the list
function squarecandy_events_prepare_country_field( $field ) {

	// check if the current value is in the country list
	if ( $field['value'] && ! in_array( $field['value'], $field['choices'], true ) ) {
		// add the current value to the select options so the field can be saved
		$field['choices'][ $field['value'] ] = $field['value'];
		// add a prompt to select a preset country instead
		$field['instructions'] .= ' <em>The current value, "' . $field['value'] . '" is not on our country list. Would you like to choose a country from the list instead?</em>';
	}

	return $field;
}

function remove_default_event_category_metabox() {
	remove_meta_box( 'tagsdiv-events-category', 'event', 'side' );
}
