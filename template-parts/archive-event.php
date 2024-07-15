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
$sunflower_tags     = wp_get_object_terms( get_the_ID(), 'sunflower_event_tag' );
$sunflower_tag_list = array();
foreach ( $sunflower_tags as $sunflower_tag ) {
	$sunflower_tag_list[ $sunflower_tag->slug ] = $sunflower_tag->name;
}
?>

<a href="<?php echo esc_url( get_permalink() ); ?>" class="event-card <?php echo esc_attr( implode( ' ', array_keys( $sunflower_tag_list ) ) ); ?>" rel="bookmark">
<article id="post-<?php the_ID(); ?>" <?php post_class( 'event has-shadow' ); ?>>
	<div class="p-4">
		<?php
		[$sunflower_weekday, $sunflower_days, $sunflower_time] = sunflower_prepare_event_time_data( $post );

		$sunflower_from      = strToTime( (string) get_post_meta( $post->ID, '_sunflower_event_from', true ) );
		$sunflower_attribute = gmdate( 'Y-m-d H:i', $sunflower_from );

		?>
		<div class="event-archive-meta">
			<div class="text-uppercase small"><?php echo esc_html( $sunflower_weekday ); ?></div>
			<div class="date">
				<time datetime="<?php echo esc_html( $sunflower_attribute ); ?>">
					<?php echo esc_html( $sunflower_days ) . wp_kses_post( empty( $sunflower_time ) ? '' : ' <span class="small">' . $sunflower_time . ' ' . __( "o'clock", 'sunflower' ) . '</span>' ); ?>
				</time>
			</div>
			<div class="fst-italic small">
				<?php
				echo esc_html( implode( ' | ', $sunflower_tag_list ) );
				?>
			</div>
		</div>

		<div class="mt-2">
			<header class="entry-header pt-2 pb-2">
				<?php
				the_title( '<strong class="h2">', '</strong>' );
				?>
			</header><!-- .entry-header -->


			<div class="entry-content">
				<?php
				the_excerpt();
				?>
			</div><!-- .entry-content -->

		</div>

	</div>
</article><!-- #post-<?php the_ID(); ?> -->
</a>
