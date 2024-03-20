<?php

/**
 * Template loader for PW Sample Plugin.
 *
 * Only need to specify class properties here.
 *
 */
class SquareCandy_Events_Template_Loader extends Gamajo_Template_Loader {

	// Prefix for filter names.
	protected $filter_prefix = 'squarecandy_events';

	// Directory name where custom templates for this plugin should be found in the theme.
	protected $theme_template_directory = 'squarecandy';

	// Reference to the root directory path of this plugin.
	protected $plugin_directory = ACF_EVENTS_DIR_PATH;

	// Directory name where templates are found in this plugin.
	protected $plugin_template_directory = 'templates';

	// https://stackoverflow.com/a/5819816/947370
	public function load_template_part( $template_name, $part_name = null ) {
		ob_start();
		$this->get_template_part( $template_name, $part_name );
		$var = ob_get_contents();
		ob_end_clean();
		return $var;
	}

}
