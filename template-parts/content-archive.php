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
					<?php sunflower_post_thumbnail( false, false, true ); ?>
				</a>
			</div>
			<?php
		}
		?>
		<div class="p-45">
			<header class="entry-header mb-2">
				<div class="entry-meta mb-3">
					<?php
					sunflower_posted_on();
					?>
					<?php
					$sunflower_roofline = get_post_meta( $post->ID, '_sunflower_roofline', true );
					if ( $sunflower_roofline ) {
						printf( ' <div class="roofline metainfo mb-1">%s</div>', esc_attr( $sunflower_roofline ) );
					}
					?>
				</div><!-- .entry-meta -->
				<?php

				the_title( '<h2 class="card-title h5 mb-3"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );

				if ( 'post' === get_post_type() ) :
					?>
					<?php
				endif;
				?>
			</header><!-- .entry-header -->

			<div class="entry-content has-small-font-size">
				<?php
				the_excerpt();
				?>
			</div><!-- .entry-content -->

			<footer class="entry-footer">

				<div class="d-flex flex-row-reverse">
					<a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark" class="h6 continue-reading">
					<span><?php esc_attr_e( 'Continue reading', 'sunflower' ); ?></span>
					<img src="<?php echo esc_url( get_template_directory_uri() . '/assets/img/arrow-right.svg' ); ?>" alt="" class="icon-arrow" />
				</a>
				</div>
			</footer><!-- .entry-footer -->
		</div>
	</div>
</article><!-- #post-<?php the_ID(); ?> -->
