<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
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

						<header class="page-header text-center">
							<h1 class="page-title">
						<?php
						/* translators: %s: search query. */
						printf( esc_html__( 'Search Results for: %s', 'sunflower' ), '<span>' . get_search_query() . '</span>' );
						?>
							</h1>
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

							// add articles to the first columns as well, but hide them on big screens
							if ( $i === 1 ) {
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
