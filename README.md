# Square Candy ACF Events

~Current Version:1.1.0~

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

**Default:**  
Show Upcoming Events in the standard formats

`[squarecandy_events]`  
or  
`echo squarecandy_events_func();`

**Archive:**  
Show Past Events in the standard formats

`[squarecandy_events type=past]`  
or  
`echo squarecandy_events_func( array('type'=>'past') );`

**Compact:**
Show the compact events block. This is good for sidebars, a homepage block, or other place where a compact preview of upcoming events is needed.

`[squarecandy_events style=compact]`  
or  
`echo squarecandy_events_func( array('style'=>'compact') );`

## History

### v1.1.0

* Add Optional Events Categories
* Add better support for more unusual combos of start date / end date / start time / end time. (Helps with imported data that might not get the "All Day" or "Show End Date" context right)
* Clean up options screen
* Start Tagging Releases

### v1.0.1

* Testing if the upgrade system works in the WordPress UI

### v1.0.0

* Add updating capacity through the WordPress UI via GitHub

### v0.1.0

* Initial Plugin built out of several older events projects
