# Square Candy ACF Events

~Current Version:v1.1.1~

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

Show Upcoming Events in the standard format

`[squarecandy_events]`  
or  
`echo squarecandy_events_func();`

**Archive:**

Show Past Events in the standard format

`[squarecandy_events type=past]`  
or  
`echo squarecandy_events_func( array('type'=>'past') );`

**All:**

Show All Events (past, present and future) in the standard format

`[squarecandy_events type=all]`  
or  
`echo squarecandy_events_func( array('type'=>'all') );`

**Compact:**

Show the compact events block. This is good for sidebars, a homepage block, or other place where a compact preview of upcoming events is needed.

`[squarecandy_events style=compact]`  
or  
`echo squarecandy_events_func( array('style'=>'compact') );`

**Category Filter:**

Filter the list by Event Category. The example below assumes you have already created an event category called "My Example Category" and applied that category to some event items.

`[squarecandy_events cat=my-example-category]`  
or  
`echo squarecandy_events_func( array('cat'=>'my-example-category') );`

**Combinations:**

Combine any of the above as needed...

`[squarecandy_events type=past style=compact cat=my-example-category]`

## History

### v1.1.2

* "All" type no longer grouped by Year
* Add option for images in preview

### v1.1.1

* Add "all" display type
* Update examples

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

## Roadmap

* make options for both past and all to be grouped by year independently
* Support more preview image positions (left, right, top)
