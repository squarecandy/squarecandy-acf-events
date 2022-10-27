# Changelog

All notable changes to this project will be documented in this file. See [standard-version](https://github.com/conventional-changelog/standard-version) for commit guidelines.

### [1.7.1](https://github.com/squarecandy/squarecandy-acf-events/compare/v1.7.0...v1.7.1) (2022-10-27)


### Bug Fixes

* add "back to" footer to events single display to match other plugins, but hide by default ([e2900a3](https://github.com/squarecandy/squarecandy-acf-events/commit/e2900a31b73e5eb26923bdc055a0a6b9e7bf40c1))
* add default date formats to avoid errors when plugin first activated ([f8aa0a1](https://github.com/squarecandy/squarecandy-acf-events/commit/f8aa0a175df1d7b52dc3528c79b44533f5e9e5b0))
* add sortable event start_date column to admin list view ([eeb6912](https://github.com/squarecandy/squarecandy-acf-events/commit/eeb6912f1711d6a0d9279569616a7562148efc27))
* don't make an empty map link ([cc3590d](https://github.com/squarecandy/squarecandy-acf-events/commit/cc3590d8357c498315fc5a7e1e92cf36f63cf90f))
* fix issue with fatal data cleanup error when start_time not set, convert start_time from timestamp to G:i:s ([6643a83](https://github.com/squarecandy/squarecandy-acf-events/commit/6643a8361ae936388b1a196e4a7d31502f966b17))
* use address fields to populate map link if google map field is empty ([b2f4274](https://github.com/squarecandy/squarecandy-acf-events/commit/b2f4274f9200e32a9a0fb3fac8586343889b9427))
* variable name issue ([73d9fd1](https://github.com/squarecandy/squarecandy-acf-events/commit/73d9fd1511334f1e79d6661427b816c789ed3a33))

## [1.7.0](https://github.com/squarecandy/squarecandy-acf-events/compare/v1.6.1...v1.7.0) (2022-05-20)


### Features

* sync work categories to events ([5c8eafa](https://github.com/squarecandy/squarecandy-acf-events/commit/5c8eafa4dcc503602df493ed702c47d106db9fe4))


### Bug Fixes

* add both squarecandy users ([26b5144](https://github.com/squarecandy/squarecandy-acf-events/commit/26b5144b0bb96f481a69d4ca50fdb058f8fa460e))
* sync categories on inline edit of works ([d815437](https://github.com/squarecandy/squarecandy-acf-events/commit/d815437ff3519cabd19ff3a7bd6ac823adc020eb))
* update version in only one place in functions.php ([fb9c11f](https://github.com/squarecandy/squarecandy-acf-events/commit/fb9c11f24032711af7e5012242bfbe58d961e131))

### [1.6.1](https://github.com/squarecandy/squarecandy-acf-events/compare/v1.6.0...v1.6.1) (2022-03-03)


### Bug Fixes

* add option to change event category order ([b48c1c0](https://github.com/squarecandy/squarecandy-acf-events/commit/b48c1c0a0655d7a1648bd1eebbd9f60e5cb34f5b))

## [1.6.0](https://github.com/squarecandy/squarecandy-acf-events/compare/v1.5.0...v1.6.0) (2022-01-14)


### Features

* add option to event catagory page to show all events or upcoming events ([9f6f528](https://github.com/squarecandy/squarecandy-acf-events/commit/9f6f52846f096c46723114938e8a84eeae21acf3))


### Bug Fixes

* add ability to reverse order on all events listing ([f0a51a2](https://github.com/squarecandy/squarecandy-acf-events/commit/f0a51a2c2a5172d4889934f97dc8ba1b756a2336))
* add label for category page display acf field ([8ed070f](https://github.com/squarecandy/squarecandy-acf-events/commit/8ed070ffcb7c4b7b3a9185b76a728f123ea787d2))
* make event category option conditional & tidy up events setting layout ([77beb11](https://github.com/squarecandy/squarecandy-acf-events/commit/77beb11cc39de7d4a56f00f3efcca8fe65eb977b))

## [1.5.0](https://github.com/squarecandy/squarecandy-acf-events/compare/v1.4.0...v1.5.0) (2021-12-17)


### Features

* add hook before event preview address ([a3866e4](https://github.com/squarecandy/squarecandy-acf-events/commit/a3866e453849a2a512b5af193d1cd1b3f4f745d5))
* support events categories ([3e8bb28](https://github.com/squarecandy/squarecandy-acf-events/commit/3e8bb285c56d2021cacbc16d22e44d4cb4be0001))


### Bug Fixes

* ACF 5.11.1 compatibility ([f74eb85](https://github.com/squarecandy/squarecandy-acf-events/commit/f74eb85bae190891f70c59fc4bea295b0d00e27e))
* ACF 5.11.1 compatibility ([a707f1c](https://github.com/squarecandy/squarecandy-acf-events/commit/a707f1cc636872ad64aa6f5f80b39c4df3779025))
* ACF 5.11.1 compatibility ([6ec03b6](https://github.com/squarecandy/squarecandy-acf-events/commit/6ec03b614a6dadf2f926dc496c3ef566d6a27515))
* add another filter hook preview_before_title ([a1c6860](https://github.com/squarecandy/squarecandy-acf-events/commit/a1c6860d2c418622eea794042b2d64ca76e4f9c1))
* add classes for upcoming and year archives ([796750c](https://github.com/squarecandy/squarecandy-acf-events/commit/796750ca5c2e1b1c12c73c963a0706080bad39ff))
* apply <section> around no events message ([a9843fe](https://github.com/squarecandy/squarecandy-acf-events/commit/a9843fe819b3eb535512994a1d1fc7b5c38147d5))
* avoid multiple date meta_query ([ab13316](https://github.com/squarecandy/squarecandy-acf-events/commit/ab13316897c838f30497e8dc412c21134e3855f6))
* display short_description if post_content is empty ([3f545fd](https://github.com/squarecandy/squarecandy-acf-events/commit/3f545fd112ff61d5889e71694855e628d67fa0d9))
* don't display comma in event location if only country is set ([a59a63e](https://github.com/squarecandy/squarecandy-acf-events/commit/a59a63eafa18a091927a9b564dd96c9d7be38708))
* reinstate posts_per_page att ([6469371](https://github.com/squarecandy/squarecandy-acf-events/commit/6469371968452d6b430e1482cf714208180df945))
* reinstate shortcodes in no_events_text ([4dae748](https://github.com/squarecandy/squarecandy-acf-events/commit/4dae748a483299ce3da6eebf08c51889e574602b))
* remove old style admin columns hard coded set ([040fd9f](https://github.com/squarecandy/squarecandy-acf-events/commit/040fd9f1ed89a61ca643e54f2ee80a1507b65186))

## [1.4.0](https://github.com/squarecandy/squarespace-event-import/compare/v1.3.0...v1.4.0) (2021-01-29)


### Features

* add yearly archive option ([8eca4ee](https://github.com/squarecandy/squarespace-event-import/commit/8eca4ee3ffaf2bb131b8977a60fa8846cf929953))
* option for ajax load more ([9fb2d78](https://github.com/squarecandy/squarespace-event-import/commit/9fb2d7854382644493401cf2fba6afbddbb25a50))


### Bug Fixes

* add active class to year nav ([d50caac](https://github.com/squarecandy/squarespace-event-import/commit/d50caac18c051199cbce78d1603511586d138312))
* add hook to single display ([1492c4e](https://github.com/squarecandy/squarespace-event-import/commit/1492c4e084cdd41765ba3a1616f8fd903d3a1c39))
* add nonce check to ajax ([d2f5559](https://github.com/squarecandy/squarespace-event-import/commit/d2f555958ed0b6bab6d2eab37302dfa85cdf9591))
* cleanup non-existing variable ([1138743](https://github.com/squarecandy/squarespace-event-import/commit/1138743d4918d2791cb52d92d98b17a0120fd6f2))
* create simpler sorting with hidden meta data ([6b5a4ab](https://github.com/squarecandy/squarespace-event-import/commit/6b5a4abebc0adee47a4be127b4d4e928c3055efb))
* force the database update again, with js cachebuster ([24eba8f](https://github.com/squarecandy/squarespace-event-import/commit/24eba8f3a842c570540896a0ea8f79921cdcabd4))
* put the year nav menu at bottom of archive ([0e3b46b](https://github.com/squarecandy/squarespace-event-import/commit/0e3b46b42ca1420eb766127644b3e1b0e29de8c7))
* remove debug data from bulk update progress bar ([7359311](https://github.com/squarecandy/squarespace-event-import/commit/735931134fb3919c4f3c34819e6ea8cf8ee780c0))
* separate transients for year list and bulk update ([214de85](https://github.com/squarecandy/squarespace-event-import/commit/214de854ee71cdb88e934f016e8d37178ac8d035))
* single view, highlight year in year nav ([0e71cdb](https://github.com/squarecandy/squarespace-event-import/commit/0e71cdb5d24853010d4f6ba91b1320ca16977dca))
* spacing in "Featured Works" ([68c239d](https://github.com/squarecandy/squarespace-event-import/commit/68c239d8f76dcdad44ca3fec0c6da954272fd126))
* use class_exists instead of custom is_plugin_active ([c5d5920](https://github.com/squarecandy/squarespace-event-import/commit/c5d59207175105bfbf858d17f8849de4bdb56be6))

## [1.3.0](https://github.com/squarecandy/squarespace-event-import/compare/v1.2.1...v1.3.0) (2020-08-31)


### Features

* add custom end date validation ([52de70c](https://github.com/squarecandy/squarespace-event-import/commit/52de70c18821d608b65a87795e1765519fd10a60))
* introduce a new archive_date hidden field ([20b17dd](https://github.com/squarecandy/squarespace-event-import/commit/20b17dd8190eeb3523b2a2d8b89efc1098631906))
* Revamp CI system and run all new linting in line with WordPress Standards


### Bug Fixes

* accordion archive fixes ([d21d4cb](https://github.com/squarecandy/squarespace-event-import/commit/d21d4cb84a118bab7c469b9cacf1cd0cebcddf66))
* active plugin check function broken ([adff3dc](https://github.com/squarecandy/squarespace-event-import/commit/adff3dca57b73f6a8aff708f240e7f1928fa7ee0))
* admin columns pro settings not loading ([c3509fe](https://github.com/squarecandy/squarespace-event-import/commit/c3509fec96f03afb96aea43de4296fce72b5b865))
* end date validation error appears repeatedly ([7731299](https://github.com/squarecandy/squarespace-event-import/commit/7731299e6776b374e276d3ea98da117f34eec4b8))
* style fix for event preview ([c48dd0a](https://github.com/squarecandy/squarespace-event-import/commit/c48dd0a9eb3c5a9b2fb16243905d6641b4fc3a65))


## v1.2.1

* add even more shortcode query options
* fix query ordering issues

## v1.2.0

* better ACF field filtering
* link works reference field if squarecandy-acf-works is enabled
* add filtering for post type supports parameter
* hide map fields if no Google API key is entered
* add featured events filter options
* fix bug where end date data persists if end date/time checkbox is off
* start code cleanup

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
