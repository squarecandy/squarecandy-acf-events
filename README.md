# Square Candy ACF Events

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

**Advanced:**

More options are now available:

* `not_in=123,125,127` // filter out specific events by ID
* `posts_per_page=3` // override total posts returned
* `only_featured=true` // filter for featured posts only
* `featured_at_top=true` // order featured posts at top
* `exclude_featured=true` // filter out featured posts
* `more_info_post_link=true` // force the more info button to appear by default & link to the single post instead of to an external website

## Filters

Available filters:

* `squarecandy_filter_events_fields` - filter the ACF fields array
* `squarecandy_filter_events_supports` - filter the array of components the post type supports ( 'title', 'editor', 'author', 'thumbnail' )

## [Developer Guide](https://developers.squarecandy.net)

For more detailed information about coding standards, development philosophy, how to run linting, how to release a new version, and more, visit the [Square Candy Developer Guide](https://developers.squarecandy.net).

## Roadmap

* make options for both "past" and "all" to be grouped by year independently
* Support more preview image positions (left, right, top)
* throw error if end date is before start date
* create hidden takedown/archive date for smoother queries
* Either make an optional checkbox for loading archive years via AJAX - OR - just force this behavior if the past events query returns more than 250 items or something like that.
* add cancelled feature and corresponding schema https://schema.org/EventCancelled
* add online location feature https://schema.org/VirtualLocation https://schema.org/BroadcastEvent
