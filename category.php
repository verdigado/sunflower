<?php
/**
 * The template for displaying Category pages
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
					<?php if ( have_posts() ) { ?>

						<header class="page-header text-center">
						<?php
						the_archive_title( '<h1 class="page-title">', '</h1>' );
						the_archive_description( '<div class="archive-description">', '</div>' );
						?>
						</header><!-- .page-header -->

						<?php
						/* Show used categories below title and description depending on settings. */
						$sunflower_categories_archive_setting = sunflower_get_setting( 'sunflower_categories_archive' ) ? sunflower_get_setting( 'sunflower_categories_archive' ) : 'main-categories';

						$sunflower_args = array(
							'echo'             => false,
							'hierarchical'     => false,
							'parent'           => 0,
							'orderby'          => 'name',
							'hide_empty'       => true,
							'show_option_none' => '',
							'title_li'         => '',
						);

						if ( 'only-subcategories' === $sunflower_categories_archive_setting ) {
							$sunflower_args['parent'] = $cat;
						}

						if ( 'no' !== $sunflower_categories_archive_setting ) {
							$sunflower_categories_archive = wp_list_categories( $sunflower_args );

							if ( $sunflower_categories_archive ) {
								echo '<div class="filter-button-group mb-5 text-center sunflower-categories"><ul class="wp-block-categories-list wp-block-categories">';
									echo wp_kses_post( $sunflower_categories_archive );
								echo '</ul></div>';
							}
						}

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

						$sunflower_paginated_links = paginate_links( $sunflower_args );

						if ( $sunflower_paginated_links ) {
							printf(
								'<div class="d-flex justify-content-around mt-3 mb-5"><div class="sunflower-pagination">%s</div></div>',
								wp_kses_post( $sunflower_paginated_links )
							);
						}
					} else {
						get_template_part( 'template-parts/content', 'none' );

					}

					?>

				</main><!-- #main -->
			</div>
		</div>
</div>
<?php
get_sidebar();
get_footer();
