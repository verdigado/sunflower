<?php
/**
 * The template for displaying search results pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Sunflower 26
 */

get_header();
?>
	<div id="content" class="container">
		<div class="row">
			<div class="col-12">
				<main id="primary" class="site-main archive">

					<?php if ( have_posts() ) { ?>

						<header class="page-header">
							<h1 class="page-title">
						<?php
						/* translators: %s: search query. */
						printf( esc_html__( 'Search Results for: %s', 'sunflower' ), '<span>' . get_search_query() . '</span>' );
						?>
							</h1>
						</header><!-- .page-header -->

						<?php
						/* Sort filter, same look as the category filter on archive pages. */
						$sunflower_search_orderby = get_query_var( 'orderby' );
						$sunflower_search_order   = strtoupper( (string) get_query_var( 'order' ) );

						if ( 'date' === $sunflower_search_orderby ) {
							$sunflower_search_sorting = ( 'ASC' === $sunflower_search_order ) ? 'oldest' : 'newest';
						} else {
							$sunflower_search_sorting = 'relevance';
						}

						$sunflower_search_base = get_search_link();

						$sunflower_search_sortings = array(
							'relevance' => array( __( 'Relevance', 'sunflower' ), $sunflower_search_base ),
							'newest'    => array(
								__( 'Newest', 'sunflower' ),
								add_query_arg(
									array(
										'orderby' => 'date',
										'order'   => 'desc',
									),
									$sunflower_search_base
								),
							),
							'oldest'    => array(
								__( 'Oldest', 'sunflower' ),
								add_query_arg(
									array(
										'orderby' => 'date',
										'order'   => 'asc',
									),
									$sunflower_search_base
								),
							),
						);

						echo '<div class="filter-button-group mb-5 text-center"><ul class="wp-block-categories-list">';
						foreach ( $sunflower_search_sortings as $sunflower_sorting_key => $sunflower_sorting ) {
							printf(
								'<li%s><a href="%s">%s</a></li>',
								( $sunflower_sorting_key === $sunflower_search_sorting ) ? ' class="current-cat"' : '',
								esc_url( $sunflower_sorting[1] ),
								esc_html( $sunflower_sorting[0] )
							);
						}
						echo '</ul></div>';

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

						<div class="archive-loop alignwide row" <?php echo sunflower_get_masonry_attr(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
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
get_footer();
