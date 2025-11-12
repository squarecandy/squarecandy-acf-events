<?php

add_action(
	'acf/include_fields',
	function() {

		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

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
				'key'        => 'field_timezonestring20d63sn49',
				'label'      => 'Site Timezone',
				'name'       => 'timezone_string',
				'type'       => 'select',
				'choices'    => squarecandy_timezone_choice(),
				'allow_null' => 1,
				'multiple'   => 0,
				'ui'         => 1,
				'wrapper'    => array(
					'width' => '50',
				),
			),
			array(
				'key'           => 'field_showtimezonesn683dsn39',
				'label'         => 'Show Timezone on every Event',
				'name'          => 'show_timezones',
				'type'          => 'true_false',
				'default_value' => '0',
				'wrapper'       => array(
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
					'width' => '100',
				),
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
				'label'         => 'Show Preview Image',
				'name'          => 'event_show_image',
				'type'          => 'true_false',
				'message'       => 'Show Image in Preview View',
				'default_value' => 1,
				'wrapper'       => array(
					'width' => '33',
				),
			),
			array(
				'key'           => 'field_showimagesingle34j20f',
				'label'         => 'Show Single Image',
				'name'          => 'event_show_image_single',
				'type'          => 'true_false',
				'message'       => 'Show Image in Single View',
				'default_value' => 1,
				'wrapper'       => array(
					'width' => '33',
				),
			),
			array(
				'key'           => 'field_imagesize8290348',
				'label'         => 'Image Size',
				'name'          => 'event_image_preview_size',
				'default_value' => 'thumbnail',
				'type'          => 'text', // @TODO - make the image size option a select that automatically gets all the sizes
				'wrapper'       => array(
					'width' => '33',
				),
			),
			array(
				'key'           => 'field_eventpreviewimagepos524234',
				'label'         => 'Preview Image Position',
				'name'          => 'event_image_preview_position',
				'type'          => 'select',
				'choices'       => array(
					'left'   => 'Left',
					'right'  => 'Right',
					'top'    => 'Top',
					'bottom' => 'Bottom',
				),
				'default_value' => 'right',
				'wrapper'       => array(
					'width' => '52',
				),
			),
			array(
				'key'           => 'field_eventsingleimagepos524234',
				'label'         => 'Single Image Position',
				'name'          => 'event_image_single_position',
				'type'          => 'select',
				'choices'       => array(
					'top'    => 'Top',
					'middle' => 'Middle',
					'bottom' => 'Bottom',
				),
				'default_value' => 'top',
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

		// Keep these fields for legacy sites.
		// But once you delete the google maps api key, or if it never gets entered, don't show the fields or allow the feature to be used.
		if ( get_option( 'options_google_maps_api_key' ) ) :

			$event_settings_fields[] = array(
				'key'          => 'field_5a6e820093be4',
				'label'        => 'Google Maps API Key',
				'name'         => 'google_maps_api_key',
				'type'         => 'text',
				'instructions' => '<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">Get Your API Key Here</a>',
				'wrapper'      => array(
					'width' => '50',
				),
			);

			$event_settings_fields[] = array(
				'key'           => 'field_5a71494e83bcb',
				'label'         => 'Show Map',
				'name'          => 'show_map_on_detail_page',
				'type'          => 'true_false',
				'message'       => 'Show a Google map with pin on the individual event page',
				'default_value' => 0,
				'wrapper'       => array(
					'width' => '50',
				),
			);

			$event_settings_fields[] = array(
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
			);

			$event_settings_fields[] = array(
				'key'          => 'field_mapjson738474635',
				'label'        => 'Google Maps JSON',
				'name'         => 'google_maps_json',
				'type'         => 'textarea',
				'instructions' => 'Generate JSON map stype code or edit existing code here: <a href="https://mapstyle.withgoogle.com/">https://mapstyle.withgoogle.com</a>',
			);

		endif; // 'options_google_maps_api_key'

		$home_country_basics = array(
			'key'           => 'field_homecountry17593483',
			'label'         => 'Home Country',
			'name'          => 'home_country',
			'instructions'  => 'If the majority of your events are in one country, enter
			your home country here to override some location displays. Hides the home
			country name in most places. Provides <strong>City, State/Province</strong>
			for the short version of your home country and <strong>City, Country</strong>
			short version display for others.',
			'default_value' => 'United States',
		);

		// override with our own custom countries list if available
		// use define('SQCDY_EVENTS_LEGACY_COUNTRY_FIELD', true); in wp-config.php to force use of the legacy text field instead
		if ( function_exists( 'squarecandy_get_countries' ) && ! defined( 'SQCDY_EVENTS_LEGACY_COUNTRY_FIELD' ) ) {

			$event_settings_fields[] = array(
				'key'           => $home_country_basics['key'],
				'label'         => $home_country_basics['label'],
				'name'          => $home_country_basics['name'],
				'instructions'  => $home_country_basics['instructions'],
				'type'          => 'select',
				'choices'       => squarecandy_get_countries(),
				'default_value' => 'United States',
				'allow_null'    => 1,
				'multiple'      => 0,
				'ui'            => 1,
				'ajax'          => 0,
				'return_format' => 'value',
			);
		} else {

			$event_settings_fields[] = array(
				'key'           => $home_country_basics['key'],
				'label'         => $home_country_basics['label'],
				'name'          => $home_country_basics['name'],
				'instructions'  => $home_country_basics['instructions'],
				'type'          => 'text',
				'default_value' => 'United States',
			);
		}

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

	}
);

// when loading the 'update_option_options_timezone_string' option, use WP 'timezone_string' option value
add_filter( 'acf/load_value', 'squarecandy_events_load_timezone', 100, 3 );
function squarecandy_events_load_timezone( $value, $post_id, $field ) {
	if ( 'field_timezonestring20d63sn49' === $field['key'] ) {
		$value = get_option( 'timezone_string' );
	}
	return $value;
}

// when saving the 'update_option_options_timezone_string' option, write it also to the WP 'timezone_string' option
add_action( 'update_option_options_timezone_string', 'squarecandy_events_save_timezone', 10, 3 );
function squarecandy_events_save_timezone( $old_value, $value, $option ) {
	if ( $value ) {
		update_option( 'timezone_string', $value );
	}
}
