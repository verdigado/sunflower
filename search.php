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
						$sunflower_list_items = '';
						while ( have_posts() ) {

							the_post();

							/*
							* Include the Post-Type-specific template for the content.
							* If you want to override this in a child theme, then include a file
							* called content-___.php (where ___ is the Post Type name) and that will be used instead.
							*/
							ob_start();
							get_template_part( 'template-parts/content', 'archive' );

							$sunflower_article = ob_get_clean();

							$sunflower_list_items .= sprintf(
								'<div class="col-md-6">%s</div>',
								$sunflower_article
							);

						}
						?>

						<div class="archive-loop row" data-masonry='{"percentPosition": true }'>
								<?php
								echo wp_kses(
									$sunflower_list_items,
									array_merge(
										wp_kses_allowed_html( 'post' ),
										array(
											'time' => array(
												'class'    => true,
												'datetime' => true,
											),
										)
									)
								);
								?>
						</div>

						<?php

						$sunflower_args = array(
							'prev_text' => __( 'previous', 'sunflower' ),
							'next_text' => __( 'next', 'sunflower' ),
						);

						printf(
							'<div class="d-flex justify-content-around mt-3 mb-5"><div class="sunflower-pagination">%s</div></div>',
							wp_kses_post( paginate_links( $sunflower_args ) )
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
