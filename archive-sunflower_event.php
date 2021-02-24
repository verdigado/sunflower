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

						<header class="page-header text-center">
                            <h1 class="page-title">
                                <?php
                                    _e('Events', 'sunflower');
                                ?>
                            </h1>
						</header><!-- .page-header -->


						<div class="filter-button-group mb-5 text-center">
							<button class="filter filter-active" data-filter="*"><?php _e('all events', 'sunflower'); ?></button>
							<?php
							$terms = get_terms([
								'taxonomy' => 'sunflower_event_tag',
								'hide_empty' => true,
							]);


							foreach($terms AS $term){
								printf('<button class="filter" data-filter=".%s">%s</button>', $term->slug, $term->name);
							}
							?>
						</div>

						<div class="row event-list">
						<?php

						//$paged = (get_query_var('paged')) ? get_query_var('paged') : 1; 
						$ordered_posts = sunflower_get_next_events();


						/* Start the Loop */
						while ( $ordered_posts->have_posts() ) :
							$ordered_posts->the_post();

							/*
							* Include the Post-Type-specific template for the content.
							* If you want to override this in a child theme, then include a file
							* called content-___.php (where ___ is the Post Type name) and that will be used instead.
							*/

							echo '<div class="col-12 col-md-6 col-lg-4 mb-3">';
								get_template_part( 'template-parts/archive', 'event');
							echo '</div>';

						endwhile;

						the_posts_navigation();

					else :

						get_template_part( 'template-parts/content', 'none' );

					endif;
					?>
					</div> <!-- event-list -->
				</main><!-- #main -->
			</div>
		</div>
</div>

<?php
	wp_enqueue_script(
        'filter-custom',
        get_template_directory_uri() . '/assets/js/filter.js',
		null,
        '3.2.1', 
        true
	);
?>

<?php
get_footer();