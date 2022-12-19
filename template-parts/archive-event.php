<?php
/**
 * Template part for displaying events
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package sunflower
 */

?>

<?php
	$tags     = wp_get_object_terms( get_the_ID(), 'sunflower_event_tag' );
	$tag_list = array();
foreach ( $tags as $tag ) {
	$tag_list[ $tag->slug ] = $tag->name;
}
?>

<a href="<?php echo esc_url( get_permalink() ); ?>" class="event-card <?php echo join( ' ', array_keys( $tag_list ) ); ?>" rel="bookmark">
<article id="post-<?php the_ID(); ?>" <?php post_class( 'event has-shadow' ); ?>>
	<div class="p-4">
		<?php
		 list($weekday, $days, $time ) = sunflower_prepare_event_time_data( $post );

			$from      = strToTime( get_post_meta( $post->ID, '_sunflower_event_from' )[0] );
			$attribute = date( 'Y-m-d', $from );

		?>
		<div class="event-archive-meta">
			<div class="text-uppercase small"><?php echo $weekday; ?></div>
			<div class="date">
				<time datetime="<?php echo $attribute; ?>">
					<?php echo $days; ?>
				</time>
			</div>
			<div class="small">
				<?php
					echo join( ' | ', $tag_list );
				?>
			</div>
		</div>

		<div class="mt-2">
			<header class="entry-header pt-2 pb-2">
				<?php
					the_title( '<strong class="h5">', '</strong>' );
				?>
			</header><!-- .entry-header -->

		
			<div class="entry-content">
				<?php
				the_excerpt(
					sprintf(
						wp_kses(
							/* translators: %s: Name of current post. Only visible to screen readers */
							__( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'sunflower' ),
							array(
								'span' => array(
									'class' => array(),
								),
							)
						),
						wp_kses_post( get_the_title() )
					)
				);
				?>
			</div><!-- .entry-content -->

		</div>
		
	</div>
</article><!-- #post-<?php the_ID(); ?> -->
</a>
