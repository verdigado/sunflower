<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package sunflower
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'bg-white mb-4 has-shadow' ); ?>>
	<div class="">
		<?php if ( has_post_thumbnail() ) { ?>
			<div class="">
				<a href="<?php echo esc_url( get_permalink() ); ?>" aria-label="Post Thumbnail" rel="bookmark">
					<?php sunflower_post_thumbnail(); ?>
				</a>
			</div>
		<?php } ?>
		<div class="p-45">
			<header class="entry-header mb-2">
				<?php
					$roofline = @get_post_meta( $post->ID, '_sunflower_roofline', true ) ?: false;
				if ( $roofline ) {
					printf( ' <div class="roofline mb-1">%s</div>', $roofline );
				}
				?>
				<?php

				the_title( '<h2 class="card-title h4 mb-3"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );


				if ( 'post' === get_post_type() ) :
					?>
					<div class="entry-meta mb-3">
						<?php
						sunflower_posted_on();
						// sunflower_posted_by();
						?>
					</div><!-- .entry-meta -->
				<?php endif; ?>
			</header><!-- .entry-header -->      

			<div class="entry-content">
				<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark">
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

				wp_link_pages(
					array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'sunflower' ),
						'after'  => '</div>',
					)
				);
				?>
				</a>
			</div><!-- .entry-content -->

			<footer class="entry-footer">
				<?php sunflower_entry_footer(); ?>

				<div class="d-flex flex-row-reverse">
					<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark" class="continue-reading">
					<?php
						_e( 'Continue reading', 'sunflower' );
					?>
				</a>
				</div>
			</footer><!-- .entry-footer -->
		</div>
	</div>
</article><!-- #post-<?php the_ID(); ?> -->
