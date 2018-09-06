# Square Candy ACF Events

~Current Version:1.0~

A custom events plugin using Advanced Custom Fields

* Requires Advanced Custom Fields Pro 5.x
* Outputs HTML including schema.org structured data
* A basic, easy to use events system

## Settings

* See available settings at **Events > Event Settings**
	* Date formatting options
	* Google Maps API key integration
	* Additional Mapping options
	* Display options

## Shortcodes

Default: Show Upcoming Events in the standard formats
`[squarecandy_events]`
or
`echo squarecandy_events_func();`

Archive: Show Past Events in the standard formats
`[squarecandy_events type=past]`
or
`echo squarecandy_events_func( array('type'=>'past') );`

Show the compact events block
`[squarecandy_events style=compact]`
or
`echo squarecandy_events_func( array('style'=>'compact') );`
