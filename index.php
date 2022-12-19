<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package sunflower
 */

get_header();
?>
	<div id="content" class="container">
		<div class="row">
			<div class="col-12">
				<main id="primary" class="site-main archive">
					<?php if ( have_posts() ) : ?>

						<header class="page-header mb-5 text-center">
						<?php
						if ( is_home() ) {
							printf( '<h1 class="page-title">%s</h1>', get_bloginfo( 'name' ) );
							printf( '<div class="archive-description">%s</div>', get_bloginfo( 'description' ) );

						} else {
							the_archive_title( '<h1 class="page-title">', '</h1>' );
							the_archive_description( '<div class="archive-description">', '</div>' );
						}
						?>
						</header><!-- .page-header -->

						<?php
						/* Start the Loop */

						$columns = array( '', '' );
						$i       = 0;
						while ( have_posts() ) :
							the_post();

							/*
							* Include the Post-Type-specific template for the content.
							* If you want to override this in a child theme, then include a file
							* called content-___.php (where ___ is the Post Type name) and that will be used instead.
							*/
							ob_start();
							get_template_part( 'template-parts/content', 'archive' );

							$article        = ob_get_clean();
							$columns[ $i ] .= $article;

							// add articles to the first columns as well, but hide them on big screens.
							if ( 1 === $i ) {
								$columns[0] .= sprintf( '<div class="d-md-none">%s</div>', $article );
							}

							$i = ( $i + 1 ) % 2;

						endwhile;
						?>

						<div class="archive-loop row">
							<div class="col-12 col-md-6">
						<?php echo $columns[0]; ?>
							</div>
							<div class="d-none d-md-block col-md-6">
						<?php echo $columns[1]; ?>
							</div>
						</div>
						<?php

						$args = array(
							'prev_text' => __( 'previous', 'sunflower' ),
							'next_text' => __( 'next', 'sunflower' ),

						);

						printf(
							'<div class="d-flex justify-content-around mt-3 mb-5"><div class="sunflower-pagination">%s</div></div>',
							paginate_links( $args )
						);

					else :

						get_template_part( 'template-parts/content', 'none' );

					endif;
					?>

				</main><!-- #main -->
			</div>
		</div>
</div>
<?php
get_footer();
