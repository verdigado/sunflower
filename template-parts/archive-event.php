<?php
/**
 * Template part for displaying events
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Sunflower 26
 */

?>

<?php
$sunflower_tags     = wp_get_object_terms( get_the_ID(), 'sunflower_event_tag' );
$sunflower_tag_list = array();
if ( ! is_wp_error( $sunflower_tags ) && is_array( $sunflower_tags ) ) {
	foreach ( $sunflower_tags as $sunflower_tag ) {
		$sunflower_slug = is_object( $sunflower_tag ) ? $sunflower_tag->slug : ( $sunflower_tag['slug'] ?? '' );
		$sunflower_name = is_object( $sunflower_tag ) ? $sunflower_tag->name : ( $sunflower_tag['name'] ?? '' );
		if ( '' !== $sunflower_slug ) {
			$sunflower_tag_list[ $sunflower_slug ] = $sunflower_name;
		}
	}
}

$sunflower_has_thumb = has_post_thumbnail();
?>

<a href="<?php echo esc_url( get_permalink() ); ?>"
	class="event-card <?php echo esc_attr( implode( ' ', array_keys( $sunflower_tag_list ) ) ); ?>" rel="bookmark">
	<article id="post-<?php the_ID(); ?>" <?php post_class( 'event has-shadow' ); ?>>

		<?php if ( $sunflower_has_thumb ) : ?>
			<figure class="event-card__media">
				<?php
				echo get_the_post_thumbnail();
				?>
			</figure>
		<?php endif; ?>

		<div class="p-4">
			<?php
			[$sunflower_weekday, $sunflower_days, $sunflower_time] = sunflower_prepare_event_time_data( $post );

			$sunflower_from      = strToTime( (string) get_post_meta( $post->ID, '_sunflower_event_from', true ) );
			$sunflower_attribute = gmdate( 'Y-m-d H:i', $sunflower_from );

			?>

			<div>
				<div class="event-archive-meta">
					<div class="weekday text-uppercase small">
						<?php echo esc_html( $sunflower_weekday ); ?>
					</div>
					<div class="date">
						<time datetime="<?php echo esc_attr( $sunflower_attribute ); ?>">
							<?php
							echo esc_html( $sunflower_days );

							if ( ! empty( $sunflower_time ) ) {
								echo '<br>' . esc_html( $sunflower_time ) . ' ' . esc_html__( "o'clock", 'sunflower' );
							}
							?>
						</time>
					</div>
				</div>

				<div class="mt-2">
					<header class="entry-header pt-2 pb-2">
						<?php
						the_title( '<span class="h2">', '</span>' );
						?>
					</header><!-- .entry-header -->

				</div>
			</div>

			<div class="fst-italic small">
				<?php
				echo esc_html( implode( ' | ', $sunflower_tag_list ) );
				?>
			</div>


		</div>

		<div class="event-card__icon--green">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/arrow-right.svg' ); ?>" alt="" width="20" height="20">
		</div>

		<div class="event-card__icon--white">
			<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/arrow-right_white.svg' ); ?>" alt="" width="20" height="20">
		</div>

	</article><!-- #post-<?php the_ID(); ?> -->
</a>
