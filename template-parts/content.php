<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package sunflower
 */

$styled_layout = @get_post_meta( $post->ID, '_sunflower_styled_layout' )[0] ? true : false;
if ( 'sunflower_event' === get_post_type() ) {
	$styled_layout = false;
}

$show_post_thumbnail = has_post_thumbnail() && ! @get_post_meta( $post->ID, '_sunflower_hide_feature_image' )[0];

$metadata = $class = false;
extract( $args );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $class ); ?>>
	<?php if ( ! $styled_layout ) { ?>
	<header class="entry-header full-width <?php echo ( $show_post_thumbnail ) ? 'has-post-thumbnail' : 'has-no-post-thumbnail'; ?>">
		<div class="container">
			<div class="row position-relative">
				<div class="col-12 offset-md-1 <?php echo ( $metadata ) ? 'text-left col-md-8' : 'col-md-10'; ?>">
		<?php
		$roofline = @get_post_meta( $post->ID, '_sunflower_roofline' )[0] ?: false;
		if ( $roofline ) {
			printf( ' <div class="roofline roofline-single">%s</div>', $roofline );
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

						if ( get_sunflower_setting( 'sunflower_show_author' ) ) {
							sunflower_posted_by();
						}
						?>
						</div><!-- .entry-meta -->
					<?php endif; ?>
					</div>

		<?php
		if ( $metadata ) {
			printf(
				'<div class="col-md-3 metabox small">%s</div>',
				$metadata
			);
		}
		?>
				
			</div>
		</div>
	</header><!-- .entry-header -->
		<?php
	}
	?>

	<?php
	if ( $show_post_thumbnail ) {
		sunflower_post_thumbnail( $styled_layout, true );
	}
	?>

	<div class="col-12 col-md-10 offset-md-1">
		<div class="entry-content">
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

		<footer class="entry-footer mt-5">
			<?php sunflower_entry_footer( true ); ?>
		</footer><!-- .entry-footer -->

		</div>    
</article><!-- #post-<?php the_ID(); ?> -->
