<?php
/**
 * Get Countries (WordPress)
 *
 * Get translated Countries in WordPress. Runs output through a
 * filter before returning to allow for customization through third
 * party plugins and themes, or for select removal/modification/addition
 * of countries for whatever reason.
 *
 * Function Usage:
 * search and replace all instances of `squarecandy` and replace it with your
 * theme or plugin's textdomain (squarecandy:twentyseventeen). Then call as:
 * $countries = squarecandy_get_countries();
 *
 * Filter Usage:
 * function squarecandy_countries_filter( $countries ) {
 *      // add a translatable country:
 *      $countries['XX'] = __( 'Country Name', 'squarecandy' );
 *      // remove a country:
 *      unset( $countries['YY'] );
 *      // modify a country:
 *      $countries['US'] = __( 'America, F*ck Yeah.', 'squarecandy' );
 *      // always return countries!
 *      return $countries;
 * }
 * add_filter( 'squarecandy_countries_filters', 'squarecandy_countries_filter', 10 );
 *
 * Source: https://gist.github.com/DHS/1340150?permalink_comment_id=2161080#gistcomment-2161080
 * See also: https://en.wikipedia.org/wiki/ISO_3166-1
 *
 * @link https://developer.wordpress.org/themes/functionality/internationalization/
 * @link https://developer.wordpress.org/plugins/internationalization/how-to-internationalize-your-plugin/
 * @link https://developer.wordpress.org/reference/functions/__/
 * @link https://developer.wordpress.org/plugins/hooks/filters/
 * @link https://developer.wordpress.org/reference/functions/apply_filters/
 * @link https://developer.wordpress.org/reference/functions/add_filter/
 *
 * @return array Countries as Country code => Country name
 */

if ( ! function_exists( 'squarecandy_get_countries' ) ) :
	function squarecandy_get_countries() {

		$countries = array(
			'Afghanistan'                                  => __( 'Afghanistan', 'squarecandy' ),
			'Åland Islands'                                => __( 'Åland Islands', 'squarecandy' ),
			'Albania'                                      => __( 'Albania', 'squarecandy' ),
			'Algeria'                                      => __( 'Algeria', 'squarecandy' ),
			'American Samoa'                               => __( 'American Samoa', 'squarecandy' ),
			'Andorra'                                      => __( 'Andorra', 'squarecandy' ),
			'Angola'                                       => __( 'Angola', 'squarecandy' ),
			'Anguilla'                                     => __( 'Anguilla', 'squarecandy' ),
			'Antarctica'                                   => __( 'Antarctica', 'squarecandy' ),
			'Antigua and Barbuda'                          => __( 'Antigua and Barbuda', 'squarecandy' ),
			'Argentina'                                    => __( 'Argentina', 'squarecandy' ),
			'Armenia'                                      => __( 'Armenia', 'squarecandy' ),
			'Aruba'                                        => __( 'Aruba', 'squarecandy' ),
			'Australia'                                    => __( 'Australia', 'squarecandy' ),
			'Austria'                                      => __( 'Austria', 'squarecandy' ),
			'Azerbaijan'                                   => __( 'Azerbaijan', 'squarecandy' ),
			'Bahamas'                                      => __( 'Bahamas', 'squarecandy' ),
			'Bahrain'                                      => __( 'Bahrain', 'squarecandy' ),
			'Bangladesh'                                   => __( 'Bangladesh', 'squarecandy' ),
			'Barbados'                                     => __( 'Barbados', 'squarecandy' ),
			'Belarus'                                      => __( 'Belarus', 'squarecandy' ),
			'Belgium'                                      => __( 'Belgium', 'squarecandy' ),
			'Belize'                                       => __( 'Belize', 'squarecandy' ),
			'Benin'                                        => __( 'Benin', 'squarecandy' ),
			'Bermuda'                                      => __( 'Bermuda', 'squarecandy' ),
			'Bhutan'                                       => __( 'Bhutan', 'squarecandy' ),
			'Bolivia'                                      => __( 'Bolivia', 'squarecandy' ),
			'Bonaire, Sint Eustatius and Saba'             => __( 'Bonaire, Sint Eustatius and Saba', 'squarecandy' ),
			'Bosnia and Herzegovina'                       => __( 'Bosnia and Herzegovina', 'squarecandy' ),
			'Botswana'                                     => __( 'Botswana', 'squarecandy' ),
			'Bouvet Island'                                => __( 'Bouvet Island', 'squarecandy' ),
			'Brazil'                                       => __( 'Brazil', 'squarecandy' ),
			'British Indian Ocean Territory'               => __( 'British Indian Ocean Territory', 'squarecandy' ),
			'British Virgin Islands'                       => __( 'British Virgin Islands', 'squarecandy' ),
			'Brunei'                                       => __( 'Brunei', 'squarecandy' ),
			'Bulgaria'                                     => __( 'Bulgaria', 'squarecandy' ),
			'Burkina Faso'                                 => __( 'Burkina Faso', 'squarecandy' ),
			'Burundi'                                      => __( 'Burundi', 'squarecandy' ),
			'Cambodia'                                     => __( 'Cambodia', 'squarecandy' ),
			'Cameroon'                                     => __( 'Cameroon', 'squarecandy' ),
			'Canada'                                       => __( 'Canada', 'squarecandy' ),
			'Cape Verde'                                   => __( 'Cape Verde', 'squarecandy' ),
			'Cayman Islands'                               => __( 'Cayman Islands', 'squarecandy' ),
			'Central African Republic'                     => __( 'Central African Republic', 'squarecandy' ),
			'Chad'                                         => __( 'Chad', 'squarecandy' ),
			'Chile'                                        => __( 'Chile', 'squarecandy' ),
			'China'                                        => __( 'China', 'squarecandy' ),
			'Christmas Island'                             => __( 'Christmas Island', 'squarecandy' ),
			'Cocos (Keeling) Islands'                      => __( 'Cocos (Keeling) Islands', 'squarecandy' ),
			'Colombia'                                     => __( 'Colombia', 'squarecandy' ),
			'Comoros'                                      => __( 'Comoros', 'squarecandy' ),
			'Congo'                                        => __( 'Congo', 'squarecandy' ),
			'Congo, Democratic Republic of the'            => __( 'Congo, Democratic Republic of the', 'squarecandy' ),
			'Cook Islands'                                 => __( 'Cook Islands', 'squarecandy' ),
			'Costa Rica'                                   => __( 'Costa Rica', 'squarecandy' ),
			'Côte d’Ivoire'                                => __( 'Côte d’Ivoire', 'squarecandy' ),
			'Croatia'                                      => __( 'Croatia', 'squarecandy' ),
			'Cuba'                                         => __( 'Cuba', 'squarecandy' ),
			'Cyprus'                                       => __( 'Cyprus', 'squarecandy' ),
			'Czech Republic'                               => __( 'Czech Republic', 'squarecandy' ), //Czechia
			'Denmark'                                      => __( 'Denmark', 'squarecandy' ),
			'Djibouti'                                     => __( 'Djibouti', 'squarecandy' ),
			'Dominica'                                     => __( 'Dominica', 'squarecandy' ),
			'Dominican Republic'                           => __( 'Dominican Republic', 'squarecandy' ),
			'Ecuador'                                      => __( 'Ecuador', 'squarecandy' ),
			'Egypt'                                        => __( 'Egypt', 'squarecandy' ),
			'El Salvador'                                  => __( 'El Salvador', 'squarecandy' ),
			'Equatorial Guinea'                            => __( 'Equatorial Guinea', 'squarecandy' ),
			'Eritrea'                                      => __( 'Eritrea', 'squarecandy' ),
			'Estonia'                                      => __( 'Estonia', 'squarecandy' ),
			'Eswatini'                                     => __( 'Eswatini', 'squarecandy' ),
			'Ethiopia'                                     => __( 'Ethiopia', 'squarecandy' ),
			'Falkland Islands'                             => __( 'Falkland Islands', 'squarecandy' ),
			'Faroe Islands'                                => __( 'Faroe Islands', 'squarecandy' ),
			'Fiji'                                         => __( 'Fiji', 'squarecandy' ),
			'Finland'                                      => __( 'Finland', 'squarecandy' ),
			'France'                                       => __( 'France', 'squarecandy' ),
			'French Guiana'                                => __( 'French Guiana', 'squarecandy' ),
			'French Polynesia'                             => __( 'French Polynesia', 'squarecandy' ),
			'French Southern and Antarctic Territories'    => __( 'French Southern and Antarctic Territories', 'squarecandy' ),
			'Gabon'                                        => __( 'Gabon', 'squarecandy' ),
			'Gambia'                                       => __( 'Gambia', 'squarecandy' ),
			'Georgia'                                      => __( 'Georgia', 'squarecandy' ),
			'Germany'                                      => __( 'Germany', 'squarecandy' ),
			'Ghana'                                        => __( 'Ghana', 'squarecandy' ),
			'Gibraltar'                                    => __( 'Gibraltar', 'squarecandy' ),
			'Greece'                                       => __( 'Greece', 'squarecandy' ),
			'Greenland'                                    => __( 'Greenland', 'squarecandy' ),
			'Grenada'                                      => __( 'Grenada', 'squarecandy' ),
			'Guadeloupe'                                   => __( 'Guadeloupe', 'squarecandy' ),
			'Guam'                                         => __( 'Guam', 'squarecandy' ),
			'Guatemala'                                    => __( 'Guatemala', 'squarecandy' ),
			'Guernsey'                                     => __( 'Guernsey', 'squarecandy' ),
			'Guinea'                                       => __( 'Guinea', 'squarecandy' ),
			'Guinea-Bissau'                                => __( 'Guinea-Bissau', 'squarecandy' ),
			'Guyana'                                       => __( 'Guyana', 'squarecandy' ),
			'Haiti'                                        => __( 'Haiti', 'squarecandy' ),
			'Heard Island and McDonald Islands'            => __( 'Heard Island and McDonald Islands', 'squarecandy' ),
			'Honduras'                                     => __( 'Honduras', 'squarecandy' ),
			'Hong Kong'                                    => __( 'Hong Kong', 'squarecandy' ),
			'Hungary'                                      => __( 'Hungary', 'squarecandy' ),
			'Iceland'                                      => __( 'Iceland', 'squarecandy' ),
			'India'                                        => __( 'India', 'squarecandy' ),
			'Indonesia'                                    => __( 'Indonesia', 'squarecandy' ),
			'Iran'                                         => __( 'Iran', 'squarecandy' ),
			'Iraq'                                         => __( 'Iraq', 'squarecandy' ),
			'Ireland'                                      => __( 'Ireland', 'squarecandy' ),
			'Isle of Man'                                  => __( 'Isle of Man', 'squarecandy' ),
			'Israel'                                       => __( 'Israel', 'squarecandy' ),
			'Italy'                                        => __( 'Italy', 'squarecandy' ),
			'Jamaica'                                      => __( 'Jamaica', 'squarecandy' ),
			'Japan'                                        => __( 'Japan', 'squarecandy' ),
			'Jersey'                                       => __( 'Jersey', 'squarecandy' ),
			'Jordan'                                       => __( 'Jordan', 'squarecandy' ),
			'Kazakhstan'                                   => __( 'Kazakhstan', 'squarecandy' ),
			'Kenya'                                        => __( 'Kenya', 'squarecandy' ),
			'Kiribati'                                     => __( 'Kiribati', 'squarecandy' ),
			'Kuwait'                                       => __( 'Kuwait', 'squarecandy' ),
			'Kyrgyzstan'                                   => __( 'Kyrgyzstan', 'squarecandy' ),
			'Laos'                                         => __( 'Laos', 'squarecandy' ),
			'Latvia'                                       => __( 'Latvia', 'squarecandy' ),
			'Lebanon'                                      => __( 'Lebanon', 'squarecandy' ),
			'Lesotho'                                      => __( 'Lesotho', 'squarecandy' ),
			'Liberia'                                      => __( 'Liberia', 'squarecandy' ),
			'Libya'                                        => __( 'Libya', 'squarecandy' ),
			'Liechtenstein'                                => __( 'Liechtenstein', 'squarecandy' ),
			'Lithuania'                                    => __( 'Lithuania', 'squarecandy' ),
			'Luxembourg'                                   => __( 'Luxembourg', 'squarecandy' ),
			'Macau'                                        => __( 'Macau', 'squarecandy' ),
			'Madagascar'                                   => __( 'Madagascar', 'squarecandy' ),
			'Malawi'                                       => __( 'Malawi', 'squarecandy' ),
			'Malaysia'                                     => __( 'Malaysia', 'squarecandy' ),
			'Maldives'                                     => __( 'Maldives', 'squarecandy' ),
			'Mali'                                         => __( 'Mali', 'squarecandy' ),
			'Malta'                                        => __( 'Malta', 'squarecandy' ),
			'Marshall Islands'                             => __( 'Marshall Islands', 'squarecandy' ),
			'Martinique'                                   => __( 'Martinique', 'squarecandy' ),
			'Mauritania'                                   => __( 'Mauritania', 'squarecandy' ),
			'Mauritius'                                    => __( 'Mauritius', 'squarecandy' ),
			'Mayotte'                                      => __( 'Mayotte', 'squarecandy' ),
			'Mexico'                                       => __( 'Mexico', 'squarecandy' ),
			'Micronesia'                                   => __( 'Micronesia', 'squarecandy' ),
			'Moldova'                                      => __( 'Moldova', 'squarecandy' ),
			'Monaco'                                       => __( 'Monaco', 'squarecandy' ),
			'Mongolia'                                     => __( 'Mongolia', 'squarecandy' ),
			'Montenegro'                                   => __( 'Montenegro', 'squarecandy' ),
			'Montserrat'                                   => __( 'Montserrat', 'squarecandy' ),
			'Morocco'                                      => __( 'Morocco', 'squarecandy' ),
			'Mozambique'                                   => __( 'Mozambique', 'squarecandy' ),
			'Myanmar [Burma]'                              => __( 'Myanmar [Burma]', 'squarecandy' ),
			'Namibia'                                      => __( 'Namibia', 'squarecandy' ),
			'Nauru'                                        => __( 'Nauru', 'squarecandy' ),
			'Nepal'                                        => __( 'Nepal', 'squarecandy' ),
			'Netherlands'                                  => __( 'Netherlands', 'squarecandy' ),
			'New Caledonia'                                => __( 'New Caledonia', 'squarecandy' ),
			'New Zealand'                                  => __( 'New Zealand', 'squarecandy' ),
			'Nicaragua'                                    => __( 'Nicaragua', 'squarecandy' ),
			'Niger'                                        => __( 'Niger', 'squarecandy' ),
			'Nigeria'                                      => __( 'Nigeria', 'squarecandy' ),
			'Niue'                                         => __( 'Niue', 'squarecandy' ),
			'Norfolk Island'                               => __( 'Norfolk Island', 'squarecandy' ),
			'North Korea'                                  => __( 'North Korea', 'squarecandy' ),
			'North Macedonia'                              => __( 'North Macedonia', 'squarecandy' ),
			'Northern Mariana Islands'                     => __( 'Northern Mariana Islands', 'squarecandy' ),
			'Norway'                                       => __( 'Norway', 'squarecandy' ),
			'Oman'                                         => __( 'Oman', 'squarecandy' ),
			'Pakistan'                                     => __( 'Pakistan', 'squarecandy' ),
			'Palau'                                        => __( 'Palau', 'squarecandy' ),
			'Palestinian Territories'                      => __( 'Palestinian Territories', 'squarecandy' ),
			'Panama'                                       => __( 'Panama', 'squarecandy' ),
			'Papua New Guinea'                             => __( 'Papua New Guinea', 'squarecandy' ),
			'Paraguay'                                     => __( 'Paraguay', 'squarecandy' ),
			'Peru'                                         => __( 'Peru', 'squarecandy' ),
			'Philippines'                                  => __( 'Philippines', 'squarecandy' ),
			'Pitcairn Islands'                             => __( 'Pitcairn Islands', 'squarecandy' ),
			'Poland'                                       => __( 'Poland', 'squarecandy' ),
			'Portugal'                                     => __( 'Portugal', 'squarecandy' ),
			'Puerto Rico'                                  => __( 'Puerto Rico', 'squarecandy' ),
			'Qatar'                                        => __( 'Qatar', 'squarecandy' ),
			'Romania'                                      => __( 'Romania', 'squarecandy' ),
			'Russia'                                       => __( 'Russia', 'squarecandy' ), //Russian Federation
			'Rwanda'                                       => __( 'Rwanda', 'squarecandy' ),
			'Saint Barthélemy'                             => __( 'Saint Barthélemy', 'squarecandy' ),
			'Saint Helena'                                 => __( 'Saint Helena', 'squarecandy' ),
			'Saint Kitts and Nevis'                        => __( 'Saint Kitts and Nevis', 'squarecandy' ),
			'Saint Lucia'                                  => __( 'Saint Lucia', 'squarecandy' ),
			'Saint Martin'                                 => __( 'Saint Martin', 'squarecandy' ),
			'Saint Pierre and Miquelon'                    => __( 'Saint Pierre and Miquelon', 'squarecandy' ),
			'Saint Vincent and the Grenadines'             => __( 'Saint Vincent and the Grenadines', 'squarecandy' ),
			'Samoa'                                        => __( 'Samoa', 'squarecandy' ),
			'San Marino'                                   => __( 'San Marino', 'squarecandy' ),
			'São Tomé and Príncipe'                        => __( 'São Tomé and Príncipe', 'squarecandy' ),
			'Saudi Arabia'                                 => __( 'Saudi Arabia', 'squarecandy' ),
			'Senegal'                                      => __( 'Senegal', 'squarecandy' ),
			'Serbia'                                       => __( 'Serbia', 'squarecandy' ),
			'Seychelles'                                   => __( 'Seychelles', 'squarecandy' ),
			'Sierra Leone'                                 => __( 'Sierra Leone', 'squarecandy' ),
			'Singapore'                                    => __( 'Singapore', 'squarecandy' ),
			'Slovakia'                                     => __( 'Slovakia', 'squarecandy' ),
			'Slovenia'                                     => __( 'Slovenia', 'squarecandy' ),
			'Solomon Islands'                              => __( 'Solomon Islands', 'squarecandy' ),
			'Somalia'                                      => __( 'Somalia', 'squarecandy' ),
			'South Africa'                                 => __( 'South Africa', 'squarecandy' ),
			'South Georgia and the South Sandwich Islands' => __( 'South Georgia and the South Sandwich Islands', 'squarecandy' ),
			'South Korea'                                  => __( 'South Korea', 'squarecandy' ),
			'Spain'                                        => __( 'Spain', 'squarecandy' ),
			'Sri Lanka'                                    => __( 'Sri Lanka', 'squarecandy' ),
			'Sudan'                                        => __( 'Sudan', 'squarecandy' ),
			'Suriname'                                     => __( 'Suriname', 'squarecandy' ),
			'Svalbard and Jan Mayen'                       => __( 'Svalbard and Jan Mayen', 'squarecandy' ),
			'Sweden'                                       => __( 'Sweden', 'squarecandy' ),
			'Switzerland'                                  => __( 'Switzerland', 'squarecandy' ),
			'Syria'                                        => __( 'Syria', 'squarecandy' ),
			'Taiwan'                                       => __( 'Taiwan', 'squarecandy' ),
			'Tajikistan'                                   => __( 'Tajikistan', 'squarecandy' ),
			'Tanzania'                                     => __( 'Tanzania', 'squarecandy' ),
			'Thailand'                                     => __( 'Thailand', 'squarecandy' ),
			'Timor-Leste'                                  => __( 'Timor-Leste', 'squarecandy' ),
			'Togo'                                         => __( 'Togo', 'squarecandy' ),
			'Tokelau'                                      => __( 'Tokelau', 'squarecandy' ),
			'Tonga'                                        => __( 'Tonga', 'squarecandy' ),
			'Trinidad and Tobago'                          => __( 'Trinidad and Tobago', 'squarecandy' ),
			'Tunisia'                                      => __( 'Tunisia', 'squarecandy' ),
			'Turkey'                                       => __( 'Turkey', 'squarecandy' ),
			'Turkmenistan'                                 => __( 'Turkmenistan', 'squarecandy' ),
			'Turks and Caicos Islands'                     => __( 'Turks and Caicos Islands', 'squarecandy' ),
			'Tuvalu'                                       => __( 'Tuvalu', 'squarecandy' ),
			'U.S. Minor Outlying Islands'                  => __( 'U.S. Minor Outlying Islands', 'squarecandy' ),
			'U.S. Virgin Islands'                          => __( 'U.S. Virgin Islands', 'squarecandy' ),
			'Uganda'                                       => __( 'Uganda', 'squarecandy' ),
			'Ukraine'                                      => __( 'Ukraine', 'squarecandy' ),
			'United Arab Emirates'                         => __( 'United Arab Emirates', 'squarecandy' ),
			'United Kingdom'                               => __( 'United Kingdom', 'squarecandy' ),
			'United States'                                => __( 'United States', 'squarecandy' ),
			'Uruguay'                                      => __( 'Uruguay', 'squarecandy' ),
			'Uzbekistan'                                   => __( 'Uzbekistan', 'squarecandy' ),
			'Vanuatu'                                      => __( 'Vanuatu', 'squarecandy' ),
			'Vatican City'                                 => __( 'Vatican City', 'squarecandy' ),
			'Venezuela'                                    => __( 'Venezuela', 'squarecandy' ),
			'Vietnam'                                      => __( 'Vietnam', 'squarecandy' ),
			'Wallis and Futuna'                            => __( 'Wallis and Futuna', 'squarecandy' ),
			'Western Sahara'                               => __( 'Western Sahara', 'squarecandy' ),
			'Yemen'                                        => __( 'Yemen', 'squarecandy' ),
			'Zambia'                                       => __( 'Zambia', 'squarecandy' ),
			'Zimbabwe'                                     => __( 'Zimbabwe', 'squarecandy' ),
		);

		/**
		 * Filter the countries before returning
		 *
		 * @param array $countries countries array for filtering
		 */
		$countries = apply_filters( 'squarecandy_countries_filters', $countries );

		/**
		 * Return the translated and filtered countries
		 */
		return $countries;
	}
endif;
