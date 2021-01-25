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
				<main id="primary" class="site-main">
					<?php if ( have_posts() ) : ?>

						<header class="page-header">
                            <h1 class="page-title">
                                <?php
                                    _e('Events', 'sunflower');
                                ?>
                            </h1>
						</header><!-- .page-header -->

						<?php

						$paged = (get_query_var('paged')) ? get_query_var('paged') : 1; 
						$ordered_posts = new WP_Query(array(
							'paged' => $paged,
							'post_type' => 'sunflower_event',
						
							'meta_query' => array(
									'relation' => 'OR',
									array(
										'key' => '_sunflower_event_from',
										'value' => date('Y-m-d H:i:s'),
										'compare' => '>'
									),
								),
							'order' => 'ASC',
						));


						/* Start the Loop */
						while ( $ordered_posts->have_posts() ) :
							$ordered_posts->the_post();

							/*
							* Include the Post-Type-specific template for the content.
							* If you want to override this in a child theme, then include a file
							* called content-___.php (where ___ is the Post Type name) and that will be used instead.
							*/
							get_template_part( 'template-parts/archive', 'event');

						endwhile;

						the_posts_navigation();

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