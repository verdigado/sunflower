<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package sunflower
 */

$sunflower_styled_layout = (bool) get_post_meta( $post->ID, '_sunflower_styled_layout', true ) ?? false;
if ( 'sunflower_event' === get_post_type() ) {
	$sunflower_styled_layout = false;
}

$sunflower_show_post_thumbnail = has_post_thumbnail() && ! get_post_meta( $post->ID, '_sunflower_hide_feature_image', true );
$sunflower_metadata            = $args['metadata'] ?? '';
$sunflower_class               = $args['class'] ?? '';
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $sunflower_class ); ?>>
	<?php if ( ! $sunflower_styled_layout ) { ?>
	<header class="entry-header full-width <?php echo ( $sunflower_show_post_thumbnail ) ? 'has-post-thumbnail' : 'has-no-post-thumbnail'; ?>">
		<div class="container">
			<div class="row position-relative">
				<div class="col-12">
		<?php
		$sunflower_roofline = get_post_meta( $post->ID, '_sunflower_roofline', true );
		if ( $sunflower_roofline ) {
			printf( ' <div class="roofline roofline-single">%s</div>', esc_attr( $sunflower_roofline ) );
		}
		?>
		<?php
		if ( is_singular() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;

		if ( 'post' === get_post_type() ) :
			?>
						<div class="entry-meta mb-3">
						<?php
						sunflower_posted_on();

						if ( sunflower_get_setting( 'sunflower_show_author' ) ) {
							sunflower_posted_by();
						}
						?>
						</div><!-- .entry-meta -->
					<?php
					endif;
		?>
					</div>

			</div>
		</div>
	</header><!-- .entry-header -->
		<?php
	}
	?>

	<?php
	if ( $sunflower_show_post_thumbnail ) {
		sunflower_post_thumbnail( $sunflower_styled_layout, true );
	}
	?>

	<div class="row">
		<div class="order-1 <?php echo ( $sunflower_metadata ) ? 'col-md-9' : 'col-md-12'; ?>">
			<div class="entry-content accordion">
				<?php
				the_content(
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

				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'sunflower' ),
						'after'  => '</div>',
					)
				);
				?>
			</div><!-- .entry-content -->

		</div><!-- .col-md-9 -->
		<?php
		if ( $sunflower_metadata ) {
			printf(
				'<div class="col-md-3 order-md-1 metabox small">%s</div>',
				wp_kses_post( $sunflower_metadata )
			);
		}
		?>
	</div>

	<div class="row">
		<footer class="entry-footer mt-4">
			<?php sunflower_entry_footer( true ); ?>
		</footer><!-- .entry-footer -->
	</div>

</article><!-- #post-<?php the_ID(); ?> -->
