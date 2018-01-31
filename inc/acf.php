<?php
// Add ACF Fields for Event

if ( function_exists('acf_add_local_field_group') ):

$eventfields = array();
$eventfields[] = array(
	'key' => 'field_5616bbe39fbec',
	'label' => '(Start) Date',
	'name' => 'start_date',
	'type' => 'date_picker',
	'instructions' => '',
	'required' => 1,
	'conditional_logic' => 0,
	'wrapper' => array(
		'width' => '40',
		'class' => '',
		'id' => '',
	),
	'display_format' => 'F j, Y',
	'return_format' => 'F j, Y',
	'first_day' => 0,
);
$eventfields[] = array(
	'key' => 'field_5616bcdfb642d',
	'label' => 'All Day',
	'name' => 'all_day',
	'type' => 'true_false',
	'instructions' => '',
	'required' => 0,
	'conditional_logic' => 0,
	'wrapper' => array(
		'width' => 10,
		'class' => '',
		'id' => '',
	),
	'message' => '',
	'default_value' => 0,
	'ui' => 0,
	'ui_on_text' => '',
	'ui_off_text' => '',
);
$eventfields[] = array(
	'key' => 'field_5616bd4ca2b0f',
	'label' => 'Show End Date/Time',
	'name' => 'multi_day',
	'type' => 'true_false',
	'instructions' => '',
	'required' => 0,
	'conditional_logic' => 0,
	'wrapper' => array(
		'width' => '20',
		'class' => '',
		'id' => '',
	),
	'message' => '',
	'default_value' => 0,
	'ui' => 0,
	'ui_on_text' => '',
	'ui_off_text' => '',
);
$eventfields[] = array(
	'key' => 'field_5616bc2b9fbed',
	'label' => '(Start) Time',
	'name' => 'start_time',
	'type' => 'time_picker',
	'instructions' => '',
	'required' => 1,
	'conditional_logic' => array(
		array(
			array(
				'field' => 'field_5616bcdfb642d',
				'operator' => '!=',
				'value' => '1',
			),
		),
	),
	'wrapper' => array(
		'width' => '30',
		'class' => '',
		'id' => '',
	),
	'display_format' => 'g:i a',
	'return_format' => 'g:i a',
);
$eventfields[] = array(
	'key' => 'field_5616bd75112ca',
	'label' => 'End Date',
	'name' => 'end_date',
	'type' => 'date_picker',
	'instructions' => '',
	'required' => 0,
	'conditional_logic' => array(
		array(
			array(
				'field' => 'field_5616bd4ca2b0f',
				'operator' => '==',
				'value' => '1',
			),
		),
	),
	'wrapper' => array(
		'width' => '70',
		'class' => '',
		'id' => '',
	),
	'display_format' => 'F j, Y',
	'return_format' => 'F j, Y',
	'first_day' => 0,
);
$eventfields[] = array(
	'key' => 'field_5616bd8e112cb',
	'label' => 'End Time',
	'name' => 'end_time',
	'type' => 'time_picker',
	'instructions' => '',
	'required' => 1,
	'conditional_logic' => array(
		array(
			array(
				'field' => 'field_5616bcdfb642d',
				'operator' => '!=',
				'value' => '1',
			),
			array(
				'field' => 'field_5616bd4ca2b0f',
				'operator' => '==',
				'value' => '1',
			),
		),
	),
	'wrapper' => array(
		'width' => '30',
		'class' => '',
		'id' => '',
	),
	'display_format' => 'g:i a',
	'return_format' => 'g:i a',
);
$eventfields[] = array(
	'key' => 'field_5616bedeed0a9',
	'label' => 'Venue Name',
	'name' => 'venue',
	'type' => 'text',
	'instructions' => '',
	'required' => 0,
	'conditional_logic' => 0,
	'wrapper' => array(
		'width' => 50,
		'class' => '',
		'id' => '',
	),
	'default_value' => '',
	'placeholder' => '',
	'prepend' => '',
	'append' => '',
	'maxlength' => '',
	'readonly' => 0,
	'disabled' => 0,
);
$eventfields[] = array(
	'key' => 'field_5616beefed0aa',
	'label' => 'Venue Link',
	'name' => 'venue_link',
	'type' => 'url',
	'instructions' => '',
	'required' => 0,
	'conditional_logic' => 0,
	'wrapper' => array(
		'width' => 50,
		'class' => '',
		'id' => '',
	),
	'default_value' => '',
	'placeholder' => '',
);
$eventfields[] = array(
	'key' => 'field_585d8171a157e',
	'label' => 'City, State',
	'name' => 'city_state',
	'type' => 'text',
	'instructions' => 'For US events, enter the city and state (example: Fort Worth, TX). For International, enter city and country (example: Paris, France).',
	'required' => 0,
	'conditional_logic' => 0,
	'wrapper' => array(
		'width' => '',
		'class' => '',
		'id' => '',
	),
	'default_value' => '',
	'maxlength' => '',
	'placeholder' => '',
	'prepend' => '',
	'append' => '',
);
$eventfields[] = array(
	'key' => 'field_5616c0e68be8f',
	'label' => 'Venue Location',
	'name' => 'venue_location',
	'type' => 'google_map',
	'instructions' => '',
	'required' => 0,
	'conditional_logic' => 0,
	'wrapper' => array(
		'width' => '',
		'class' => '',
		'id' => '',
	),
	'center_lat' => '',
	'center_lng' => '',
	'zoom' => '',
	'height' => '',
);
$eventfields[] = array(
	'key' => 'field_5616befced0ab',
	'label' => 'More Info Link',
	'name' => 'more_info_link',
	'type' => 'url',
	'instructions' => '',
	'required' => 0,
	'conditional_logic' => 0,
	'wrapper' => array(
		'width' => 50,
		'class' => '',
		'id' => '',
	),
	'default_value' => '',
	'placeholder' => '',
);
$eventfields[] = array(
	'key' => 'field_5616bf58ed0ac',
	'label' => 'Tickets Link',
	'name' => 'tickets_link',
	'type' => 'url',
	'instructions' => '',
	'required' => 0,
	'conditional_logic' => 0,
	'wrapper' => array(
		'width' => 50,
		'class' => '',
		'id' => '',
	),
	'default_value' => '',
	'placeholder' => '',
);
$eventfields[] = array(
	'key' => 'field_5616bf8eed0ad',
	'label' => 'Short Description',
	'name' => 'short_description',
	'type' => 'wysiwyg',
	'instructions' => 'A short text description for the event. Limit 210 characters.	You may repeat this text and elaborate further in the main body field below.',
	'required' => 0,
	'conditional_logic' => 0,
	'wrapper' => array(
		'width' => '',
		'class' => '',
		'id' => '',
	),
	'tabs' => 'all',
	'toolbar' => 'basic',
	'media_upload' => 0,
	'default_value' => '',
	'delay' => 0,
);

// allow linking to "works" if the Square Candy ACF Composer Works plugin is enabled
if ( post_type_exists('works') ) :
	$eventfields[] = array(
		'key' => 'field_5841cdf6350d1',
		'label' => 'Featured Works',
		'name' => 'featured_works',
		'type' => 'relationship',
		'instructions' => '',
		'required' => 0,
		'conditional_logic' => 0,
		'wrapper' => array(
			'width' => '',
			'class' => '',
			'id' => '',
		),
		'post_type' => array(
			0 => 'works',
		),
		'taxonomy' => array(
		),
		'min' => '',
		'max' => '',
		'filters' => array(
			0 => 'search',
		),
		'elements' => '',
		'return_format' => 'object',
	);
endif;

acf_add_local_field_group(array(
	'key' => 'group_5616bbdb43b9f',
	'title' => 'Event Fields',
	'fields' => $eventfields,
	'location' => array(
		array(
			array(
				'param' => 'post_type',
				'operator' => '==',
				'value' => 'event',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'acf_after_title',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => array(
		0 => 'excerpt',
		1 => 'custom_fields',
		2 => 'discussion',
		3 => 'comments',
		4 => 'format',
		5 => 'page_attributes',
		6 => 'categories',
		7 => 'tags',
		8 => 'send-trackbacks',
	),
	'active' => 1,
	'description' => '',
));


/*

acf_add_local_field_group(array(
	'key' => 'group_5a6e81f786514',
	'title' => 'Event Options',
	'fields' => array(
		array(
			'key' => 'field_5a6e820093be4',
			'label' => 'Google Maps API Key',
			'name' => 'google_maps_api_key',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => '',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'options_page',
				'operator' => '==',
				'value' => 'acf-options-event-settings',
			),
		),
	),
	'menu_order' => 0,
	'position' => 'normal',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => 1,
	'description' => '',
));

*/

endif;
