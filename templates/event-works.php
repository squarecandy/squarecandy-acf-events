<?php
$featured_works = get_field( 'featured_works' );
if ( ! empty( $featured_works ) ) :
	$t = count( $featured_works );
	?>
	<div class="featuredwork">
		<span>Featured Work<?php echo $t > 1 ? 's' : ''; ?></span>
		<?php
		$works = array();
		foreach ( $featured_works as $work ) {
			$works[] = '<span itemscope itemprop="workPerformed" itemtype="http://schema.org/CreativeWork"><a itemprop="url" href="' . get_the_permalink( $work->ID ) . '"><span itemprop="name">' . $work->post_title . '</span></a></span>';
		}
		echo implode( ', ', $works );
		?>
	</div>
<?php endif; ?>