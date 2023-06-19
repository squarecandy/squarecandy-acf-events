<?php
function squarecandy_events_year_archive_rewrite( $wp_rewrite ) {

	$feed_rules = array(
		'events\/([0-9]+)\/?$' => 'index.php?post_type=event&archive_year=$matches[1]',
	);

	// ( array merge must be done this way, to ensure new rule comes first )
	$wp_rewrite->rules = $feed_rules + $wp_rewrite->rules;
}
// refresh/flush permalinks in the dashboard if this is changed in any way
add_filter( 'generate_rewrite_rules', 'squarecandy_events_year_archive_rewrite' );

add_filter( 'query_vars', 'quarecandy_events_year_archive_query_vars' );
function quarecandy_events_year_archive_query_vars( $vars ) {
	array_push( $vars, 'archive_year' );
	return $vars;
}

function squarecandy_events_override_type_args( $args, $post_type ) {
	if ( 'event' === $post_type ) {
		$args['rewrite'] = array(
			'slug'       => 'events',
			'with_front' => false,
		);
	}
	return $args;
}
add_filter( 'register_post_type_args', 'squarecandy_events_override_type_args', 20, 2 );

function get_squarecandy_events_year_nav() {
	// don't do anything unless the list needs updating once per year
	if ( wp_date( 'Y' ) === get_transient( 'squarecandy_events_year_archive_update' ) ) {
		return false;
	}

	// needs updating
	update_squarecandy_archive_year_list();

}
add_action( 'init', 'get_squarecandy_events_year_nav' );

function update_squarecandy_archive_year_list() {
	global $wpdb;
	$sql              = "SELECT DISTINCT YEAR(meta_value) FROM $wpdb->postmeta WHERE meta_key = 'start_date' AND DATE(meta_value) < DATE(NOW()) ORDER BY meta_value DESC";
	$all_unique_years = $wpdb->get_results( $sql, ARRAY_N ); // phpcs:ignore
	$all_unique_years = array_merge( array(), ...array_values( $all_unique_years ) ); // flatten the array
	$all_unique_years = array_filter( $all_unique_years );

	update_option( 'squarecandy_events_years', $all_unique_years );
	set_transient( 'squarecandy_events_year_archive_update', wp_date( 'Y' ) );

}
add_action( 'save_post_event', 'update_squarecandy_archive_year_list' );

function squarecandy_archive_year_nav( $archiveyear = '' ) {
	if ( get_query_var( 'archive_year' ) ) {
		$archiveyear = (int) get_query_var( 'archive_year' );
	}
	?>
	<nav class="event-nav-by-year">
		<?php do_action( 'squarecandy_before_events_year_nav' ); ?>
		<ul>
			<li class="upcoming">
				<a <?php echo $archiveyear ? '' : 'class="active"'; ?> href="/events/">
				<?php echo apply_filters( 'squarecandy_events_see_all', 'Upcoming Events', 'year_nav' ); ?>
				</a>
			</li>
			<?php
			$years = get_option( 'squarecandy_events_years' );
			foreach ( $years as $year ) {
				?>
				<li class="past past-<?php echo $year; ?>">
					<a <?php echo (int) $archiveyear === (int) $year ? 'class="active"' : ''; ?> href="/events/<?php echo $year; ?>/">
						<?php echo $year; ?>
					</a>
				</li>
				<?php
			}
			?>
		</ul>
	</nav>
	<?php
}
