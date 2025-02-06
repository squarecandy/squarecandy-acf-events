<?php
// Add ACF Fields and Functions for Event

if ( function_exists( 'acf_add_local_field_group' ) ) :

	function squarecandy_events_add_fields() {
		$eventfields = array();

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
				'width' => '40',
			),
			'display_format'    => 'F j, Y',
			'return_format'     => 'F j, Y',
			'first_day'         => 0,
		);
		$eventfields['all_day']    = array(
			'key'           => 'field_5616bcdfb642d',
			'label'         => 'All Day',
			'name'          => 'all_day',
			'type'          => 'true_false',
			'wrapper'       => array(
				'width' => 10,
			),
			'default_value' => 0,
		);
		$eventfields['multi_day']  = array(
			'key'           => 'field_5616bd4ca2b0f',
			'label'         => 'Show End Date/Time',
			'name'          => 'multi_day',
			'type'          => 'true_false',
			'wrapper'       => array(
				'width' => '20',
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
				'width' => '30',
			),
			'display_format'    => 'g:i a',
			'return_format'     => 'g:i a',
		);
		$eventfields['end_date']   = array(
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
		$eventfields['end_time']   = array(
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
		$eventfields['venue']      = array(
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
		$eventfields['address']    = array(
			'key'   => 'field_address226474957',
			'label' => 'Address',
			'name'  => 'address',
			'type'  => 'text',
		);
		$eventfields['city']       = array(
			'key'     => 'field_city585d8171a157e',
			'label'   => 'City',
			'name'    => 'city',
			'type'    => 'text',
			'wrapper' => array(
				'width' => 50,
			),
		);
		$eventfields['state']      = array(
			'key'     => 'field_state94823hf873',
			'label'   => 'State/Province',
			'name'    => 'state',
			'type'    => 'text',
			'wrapper' => array(
				'width' => 25,
			),
		);
		$eventfields['zip']        = array(
			'key'     => 'field_zipfj8392y38r9',
			'label'   => 'Postal Code',
			'name'    => 'zip',
			'type'    => 'text',
			'wrapper' => array(
				'width' => 25,
			),
		);
		$eventfields['country']    = array(
			'key'   => 'field_country1749283947',
			'label' => 'Country',
			'name'  => 'country',
			'type'  => 'text',
		);
		if ( class_exists( 'AcfCountry' ) ) {
			$eventfields['country'] = array(
				'key'           => 'field_country1749283947',
				'label'         => 'Country',
				'name'          => 'country',
				'type'          => 'country',
				'default_value' => array(
					0 => 'United States',
				),
				'allow_null'    => 1,
				'multiple'      => 0,
				'ui'            => 1,
				'return_format' => 'name',
				'placeholder'   => 'Select a country',
			);
		}

		$instructions = '';
		if ( ! get_option( 'options_google_maps_api_key' ) ) {
			$instructions = 'Looks like you don\'t have a Google Maps API key yet. First,
			<a href="https://developers.google.com/maps/documentation/javascript/get-api-key"
			target="_blank">get your API key here</a>. Then enter it on the
			<a href="/wp-admin/edit.php?post_type=event&page=acf-options-event-settings">Events
			Settings page here</a>.';
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
				'instructions' => $instructions,
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

		if ( sqcdy_is_views2( 'events' ) ) :
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
				'sub_fields'                    => array(
					array(
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
					),
					array(
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
					),
					array(
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
					),
				),
			);
		endif;

		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		if (
			// if we're not in views 2 mode
			( ! sqcdy_is_views2( 'events' ) ) ||
			// or if we're on the front end anytime
			( ! is_admin() ) ||
			// or if we're on the edit screen for an event and the current event has a value for more_info_link
			( is_admin() && isset( $_GET['post'] ) && isset( $_GET['action'] ) && 'edit' === $_GET['action'] && 'event' === get_post_type( $_GET['post'] ) && get_field( 'more_info_link', $_GET['post'] ) ) ||
			// or if we're on the edit screen for an event and the current event has a value for facebook_link
			( is_admin() && isset( $_GET['post'] ) && isset( $_GET['action'] ) && 'edit' === $_GET['action'] && 'event' === get_post_type( $_GET['post'] ) && get_field( 'facebook_link', $_GET['post'] ) ) ||
			// or if we're in the admin and not editing an event (needed for saving legacy values, etc)
			( is_admin() && ! isset( $_GET['post'] ) && ! isset( $_GET['post_type'] ) )
		) :
			$label = sqcdy_is_views2( 'events' ) ? 'More Info Link (Legacy)' : 'More Info Link';

			$eventfields['more_info_link'] = array(
				'key'     => 'field_5616befced0ab',
				'label'   => $label,
				'name'    => 'more_info_link',
				'type'    => 'url',
				'wrapper' => array(
					'width' => 50,
				),
			);

			$label = sqcdy_is_views2( 'events' ) ? 'Facebook Event Link (Legacy)' : 'Facebook Event Link';

			$eventfields['facebook_link'] = array(
				'key'     => 'field_facebooklink7293484',
				'label'   => $label,
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

			function remove_default_event_category_metabox() {
				remove_meta_box( 'tagsdiv-events-category', 'event', 'side' );
			}
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
	add_action( 'after_setup_theme', 'squarecandy_events_add_fields' );


	$date_formats = array(
		'l, F j, Y' => 'Saturday, October 26, 1985',
		'F j, Y'    => 'October 26, 1985',
		'l, F j'    => 'Saturday, October 26',
		'F j'       => 'October 26',
		'D, M j, Y' => 'Sat, Oct 26, 1985',
		'M j, Y'    => 'Oct 26, 1985',
		'D, M j'    => 'Sat, Oct 26',
		'M j'       => 'Oct 26',
		'm/d/Y'     => '10/26/1985',
		'm/d'       => '10/26',
		'Y-m-d'     => '1985-10-26',
		'l, j F, Y' => 'Saturday, 26 October, 1985',
		'j F, Y'    => '26 October, 1985',
		'l, j F'    => 'Saturday, 26 October',
		'j F'       => '26 October',
		'D, j M, Y' => 'Sat, 26 Oct, 1985',
		'j M, Y'    => '26 Oct, 1985',
		'D, j M'    => 'Sat, 26 Oct',
		'j M'       => '26 Oct',
		'd/m/Y'     => '26/10/1985',
		'd/m'       => '26/10',
	);

	$default_date_formats = squarecandy_events_default_date_formats();

	$event_settings_fields = array(
		array(
			'key'               => 'field_5a71126cf1def',
			'label'             => 'Date Formats',
			'name'              => 'date_formats',
			'type'              => 'group',
			'instructions'      => 'Choose the date formats you want to use throughout the events system.',
			'required'          => 0,
			'conditional_logic' => 0,
			'wrapper'           => array(
				'width' => '',
				'class' => '',
				'id'    => '',
			),
			'layout'            => 'block',
			'sub_fields'        => array(
				array(
					'key'           => 'field_5a71125ff1dee',
					'label'         => 'Main Date Format',
					'name'          => 'date_format',
					'type'          => 'select',
					'choices'       => $date_formats,
					'default_value' => array(
						0 => $default_date_formats['date_format'],
					),
					'allow_null'    => 0,
					'return_format' => 'value',
					'wrapper'       => array(
						'width' => '33',
					),
				),
				array(
					'key'           => 'field_5a7119ab98e68',
					'label'         => 'Multi-Day Start Date',
					'name'          => 'date_format_multi_start',
					'type'          => 'select',
					'choices'       => $date_formats,
					'default_value' => array(
						0 => $default_date_formats['date_format_multi_start'],
					),
					'allow_null'    => 0,
					'return_format' => 'value',
					'wrapper'       => array(
						'width' => '33',
					),
				),
				array(
					'key'           => 'field_5a7119af98e69',
					'label'         => 'Multi-Day End Date',
					'name'          => 'date_format_multi_end',
					'type'          => 'select',
					'choices'       => $date_formats,
					'default_value' => array(
						0 => $default_date_formats['date_format_multi_end'],
					),
					'allow_null'    => 0,
					'return_format' => 'value',
					'wrapper'       => array(
						'width' => '33',
					),
				),
				array(
					'key'           => 'field_5a7119b198e6a',
					'label'         => 'Compact Date Format',
					'name'          => 'date_format_compact',
					'type'          => 'select',
					'choices'       => $date_formats,
					'default_value' => array(
						0 => $default_date_formats['date_format_compact'],
					),
					'allow_null'    => 0,
					'return_format' => 'value',
					'wrapper'       => array(
						'width' => '33',
					),
				),
				array(
					'key'           => 'field_5a711a6b98e6b',
					'label'         => 'Compact Multi-Day Start Date',
					'name'          => 'date_format_compact_multi_start',
					'type'          => 'select',
					'choices'       => $date_formats,
					'default_value' => array(
						0 => $default_date_formats['date_format_compact_multi_start'],
					),
					'allow_null'    => 0,
					'return_format' => 'value',
					'wrapper'       => array(
						'width' => '33',
					),
				),
				array(
					'key'           => 'field_5a711a6d98e6c',
					'label'         => 'Compact Multi-Day End Date',
					'name'          => 'date_format_compact_multi_end',
					'type'          => 'select',
					'choices'       => $date_formats,
					'default_value' => array(
						0 => $default_date_formats['date_format_compact_multi_end'],
					),
					'allow_null'    => 0,
					'return_format' => 'value',
					'wrapper'       => array(
						'width' => '33',
					),
				),
				array(
					'key'           => 'field_5a711add98e6d',
					'label'         => 'Time Format',
					'name'          => 'time_format',
					'type'          => 'select',
					'choices'       => array(
						'g:ia'  => '1:21pm',
						'g:i a' => '1:21 pm',
						'g:iA'  => '1:21PM',
						'H:i'   => '13:21',
					),
					'default_value' => array(
						0 => $default_date_formats['time_format'],
					),
					'allow_null'    => 0,
					'return_format' => 'value',
					'wrapper'       => array(
						'width' => '25',
					),
				),
				array(
					'key'           => 'field_timedatesep73847927483',
					'label'         => 'Date/Time Separator',
					'name'          => 'datetime_sep',
					'type'          => 'text',
					'default_value' => $default_date_formats['datetime_sep'],
					'wrapper'       => array(
						'width' => '25',
					),
				),
				array(
					'key'           => 'field_timedatesep2_729385610',
					'label'         => 'Date/Time Separator 2',
					'name'          => 'datetime_sep2',
					'type'          => 'text',
					'default_value' => $default_date_formats['datetime_sep2'],
					'wrapper'       => array(
						'width' => '25',
					),
				),
				array(
					'key'           => 'field_timedaterange733636483',
					'label'         => 'Date/Time Range Character',
					'name'          => 'datetime_range',
					'type'          => 'text',
					'default_value' => $default_date_formats['datetime_range'],
					'wrapper'       => array(
						'width' => '25',
					),
				),
			),

		),
		array(
			'key'          => 'field_5a6e820093be4',
			'label'        => 'Google Maps API Key',
			'name'         => 'google_maps_api_key',
			'type'         => 'text',
			'instructions' => '<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">Get Your API Key Here</a>',
			'wrapper'      => array(
				'width' => '50',
			),
		),
		array(
			'key'           => 'field_5a711b7d7ee7b',
			'label'         => 'Map Link',
			'name'          => 'map_link',
			'type'          => 'true_false',
			'message'       => 'Display Map Link Buttons',
			'default_value' => 1,
			'wrapper'       => array(
				'width' => '50',
			),
		),
		array(
			'key'           => 'field_5a71494e83bcb',
			'label'         => 'Show Map',
			'name'          => 'show_map_on_detail_page',
			'type'          => 'true_false',
			'message'       => 'Show a Google map with pin on the individual event page',
			'default_value' => 0,
			'wrapper'       => array(
				'width' => '50',
			),
		),
		array(
			'key'           => 'field_5a8123e9241f6',
			'label'         => 'Default Map Zoom Level',
			'name'          => 'default_zoom_level',
			'type'          => 'range',
			'instructions'  => 'select how far zoomed in the maps appear by default',
			'default_value' => 15,
			'min'           => 8,
			'max'           => 21,
			'step'          => 1,
			'prepend'       => '-',
			'append'        => '+',
			'wrapper'       => array(
				'width' => '50',
			),
		),
		array(
			'key'          => 'field_mapjson738474635',
			'label'        => 'Google Maps JSON',
			'name'         => 'google_maps_json',
			'type'         => 'textarea',
			'instructions' => 'Generate JSON map stype code or edit existing code here: <a href="https://mapstyle.withgoogle.com/">https://mapstyle.withgoogle.com</a>',
		),
		array(
			'key'           => 'field_5a711b987ee7c',
			'label'         => 'Add to Calendar Links',
			'name'          => 'add_to_gcal',
			'type'          => 'select',
			'choices'       => array(
				1        => 'Show',
				0        => 'Hide',
				'future' => 'Show on future events only',
			),
			'default_value' => 1,
			'wrapper'       => array(
				'width' => '52',
			),
		),
		array(
			'key'           => 'field_showticketslink7479',
			'label'         => 'Show Ticket Links',
			'name'          => 'show_tickets_link',
			'type'          => 'select',
			'choices'       => array(
				0        => 'Always Show (if link exists)',
				'future' => 'Show on future events only',
			),
			'default_value' => 0,
			'wrapper'       => array(
				'width' => '48',
			),
		),
		array(
			'key'           => 'field_homecountry17593483',
			'label'         => 'Home Country',
			'name'          => 'home_country',
			'type'          => 'text',
			'instructions'  => 'If the majority of your events are in one country, enter
		your home country here to override some location displays. Hides the home
		country name in most places. Provides <strong>City, State/Province</strong>
		for the short version of your home country and <strong>City, Country</strong>
		short version display for others.',
			'default_value' => 'United States',
		),
		array(
			'key'           => 'field_5a711c1103fad',
			'label'         => 'Archive by Year',
			'name'          => 'archive_by_year',
			'type'          => 'true_false',
			'message'       => 'Group Past Events by Year',
			'default_value' => 1,
			'wrapper'       => array(
				'width' => '52',
				'class' => 'before-conditional-half',
			),
		),
		array(
			'key'               => 'field_5a71e021fe462',
			'label'             => 'Collapse/Expand',
			'name'              => 'accordion',
			'type'              => 'true_false',
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_5a711c1103fad',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
			'message'           => 'Collapse/Expand Animation for Event Archives by Year',
			'default_value'     => 1,
			'wrapper'           => array(
				'width' => '48',
			),
		),
		array(
			'key'          => 'field_archiveyears829304',
			'label'        => 'Full Yearly Archive',
			'instructions' => 'Use the full WP archive with default urls for upcoming events (<code>/events/</code>) and past year archives (<code>/events/1984/</code>, <code>/events/1985/</code>, etc). You do not need to use a page with shortcodes if you use this option.',
			'name'         => 'yearly_archive',
			'type'         => 'true_false',
			'ui'           => 0,
			'message'      => 'Use full yearly archive system',
		),
		array(
			'key'     => 'field_loadmore83942',
			'label'   => 'Load More',
			'name'    => 'events_ajax_load_more',
			'type'    => 'true_false',
			'message' => 'use AJAX load more buttons in archives',
			'ui'      => 0,
			'wrapper' => array(
				'width' => '52',
				'class' => 'before-conditional-half',
			),
		),
		array(
			'key'               => 'field_eventspostsperpage789',
			'label'             => 'Load More Posts Per Page',
			'name'              => 'events_posts_per_page',
			'type'              => 'number',
			'min'               => 3,
			'max'               => 99,
			'default_value'     => 20,
			'wrapper'           => array(
				'width' => '48',
			),
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_loadmore83942',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
		),
		array(
			'key'           => 'field_5a711c6203fae',
			'label'         => 'More Link (Compact View)',
			'name'          => 'more_link',
			'type'          => 'link',
			'return_format' => 'array',
			'wrapper'       => array(
				'width' => '52',
			),
		),
		array(
			'key'           => 'field_5a7120c836833',
			'label'         => 'Number of Upcoming',
			'name'          => 'number_of_upcoming',
			'type'          => 'number',
			'instructions'  => 'How Many Upcoming Events to Show in Compact View',
			'default_value' => 3,
			'min'           => 1,
			'max'           => 99,
			'step'          => 1,
			'wrapper'       => array(
				'width' => '48',
			),
		),
		array(
			'key'           => 'field_5a71218b78f41',
			'label'         => 'Show Description',
			'name'          => 'show_description',
			'type'          => 'true_false',
			'message'       => 'Show Short Description in Compact View',
			'default_value' => 1,
			'wrapper'       => array(
				'width' => '52',
			),
		),
		array(
			'key'           => 'field_5a716c3632b88',
			'label'         => 'Show Title',
			'name'          => 'show_title',
			'type'          => 'true_false',
			'message'       => 'Show Title Field in Compact View',
			'default_value' => 1,
			'wrapper'       => array(
				'width' => '48',
			),
		),
		array(
			'key'           => 'field_showimage834j20f',
			'label'         => 'Show Image',
			'name'          => 'event_show_image',
			'type'          => 'true_false',
			'message'       => 'Show Image in Compact View',
			'default_value' => 1,
			'wrapper'       => array(
				'width' => '52',
			),
		),
		array(
			'key'           => 'field_imagesize8290348',
			'label'         => 'Image Size',
			'name'          => 'event_image_preview_size',
			'default_value' => 'thumbnail',
			'type'          => 'text', // @TODO - make the image size option a select that automatically gets all the sizes
			'wrapper'       => array(
				'width' => '48',
			),
		),
		array(
			'key'           => 'field_eventcat7283947892',
			'label'         => 'Events Categories',
			'name'          => 'enable_categories',
			'type'          => 'true_false',
			'message'       => 'Enable Events Categories System',
			'default_value' => 1,
			'wrapper'       => array(
				'width' => '52', //52/48 widths are so line wrapping stays the same when conditional fields are hidden
				'class' => 'before-conditional-half',
			),
		),
		array(
			'key'               => 'field_eventcattype39264951',
			'label'             => 'Events Categories Page - Display Type',
			'name'              => 'event_categories_type',
			'type'              => 'true_false',
			'message'           => 'Show only upcoming events on Event Category Pages',
			'default_value'     => 0,
			'wrapper'           => array(
				'width' => '48',
			),
			'conditional_logic' => array(
				array(
					array(
						'field'    => 'field_eventcat7283947892',
						'operator' => '==',
						'value'    => '1',
					),
				),
			),
		),
	);

	// if squarecandy-acf-works is present, add checkbox to sync work categories to events
	if ( taxonomy_exists( 'works-category' ) && post_type_exists( 'works' ) ) {
		$event_settings_fields[] = array(
			'key'           => 'field_eventworkcats39194651',
			'label'         => 'Sync Work Categories to Associated Events',
			'name'          => 'sync_event_work_categories',
			'type'          => 'true_false',
			'message'       => 'Store work categories on associated events',
			'default_value' => 0,
			'wrapper'       => array(
				'width' => '48',
			),
		);
	}

	// if views 2, add checkbox to put the title first in event preview
	if ( sqcdy_is_views2( 'events' ) ) {
		$event_settings_fields[] = array(
			'key'           => 'field_eventtitlefirst39264951',
			'label'         => 'Event Preview Title First',
			'name'          => 'event_preview_title_first',
			'type'          => 'true_false',
			'message'       => 'Show the event title above the date in event previews',
			'default_value' => 0,
			'wrapper'       => array(
				'width' => '52',
			),
		);

		$event_settings_fields[] = array(
			'key'           => 'field_eventsingledatefirst64951',
			'label'         => 'Event Single View: Date First',
			'name'          => 'event_single_date_first',
			'type'          => 'true_false',
			'message'       => 'Show the event date above the title in event single view',
			'default_value' => 0,
			'wrapper'       => array(
				'width' => '48',
			),
		);

		$event_settings_fields[] = array(
			'key'           => 'field_eventscpttitleheader55354',
			'label'         => 'Event Single View: Use CPT Title Header',
			'name'          => 'event_single_header_title',
			'type'          => 'true_false',
			'message'       => 'Show "Events" above the title in event single view',
			'default_value' => 0,
			'wrapper'       => array(
				'width' => '52',
			),
		);
	}

	$event_settings_fields[] = array(
		'key'           => 'field_5a712237b7a78',
		'label'         => 'No Events Text',
		'name'          => 'no_events_text',
		'type'          => 'wysiwyg',
		'default_value' => '<h2>There are currently no upcoming events.</h2><p>Please join the email list and we will keep you posted when new events get added.</p>',
		'tabs'          => 'all',
		'toolbar'       => 'basic',
		'media_upload'  => 0,
		'delay'         => 0,
	);

	//add ACF fields on 'Events Settings' page
	acf_add_local_field_group(
		array(
			'key'                   => 'group_5a6e81f786514',
			'title'                 => 'Event Options',
			'fields'                => $event_settings_fields,
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

	// If using events categories, add order checkbox to taxonomy edit page
	if ( get_option( 'options_enable_categories' ) ) :

		acf_add_local_field_group(
			array(
				'key'                   => 'group_621d5fd1e6476',
				'title'                 => 'Events category fields',
				'fields'                => array(
					array(
						'key'               => 'field_621d600d49e9e',
						'label'             => 'Event Order',
						'name'              => 'events_order',
						'type'              => 'radio',
						'instructions'      => 'Choose the order of the events (default newest first)',
						'required'          => 0,
						'conditional_logic' => 0,
						'wrapper'           => array(
							'width' => '',
							'class' => '',
							'id'    => '',
						),
						'choices'           => array(
							'DESC' => 'Newest first',
							'ASC'  => 'Oldest first',
						),
						'allow_null'        => 0,
						'other_choice'      => 0,
						'default_value'     => 'desc',
						'layout'            => 'vertical',
						'return_format'     => 'value',
						'save_other_choice' => 0,
					),
				),
				'location'              => array(
					array(
						array(
							'param'    => 'taxonomy',
							'operator' => '==',
							'value'    => 'events-category',
						),
					),
				),
				'menu_order'            => 0,
				'position'              => 'normal',
				'style'                 => 'default',
				'label_placement'       => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen'        => '',
				'active'                => true,
				'description'           => '',
			)
		);

	endif;

endif;
