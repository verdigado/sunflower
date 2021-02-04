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
				<main id="primary" class="site-main mt-5">
					<?php if ( have_posts() ) : ?>

						<header class="page-header">
                            <h1 class="page-title">
                                <?php
                                    _e('Events', 'sunflower');
                                ?>
                            </h1>
						</header><!-- .page-header -->


						<div class="isotope-button-group mb-2">
							<button class="btn btn-sm btn-outline-primary me-2" data-filter="*"><?php _e('Show all', 'sunflower'); ?></button>
							<?php
							$terms = get_terms([
								'taxonomy' => 'sunflower_event_tag',
								'hide_empty' => true,
							]);


							foreach($terms AS $term){
								printf('<button class="btn btn-sm btn-outline-info me-1" data-filter=".%s">%s</button>', $term->slug, $term->name);
							}
							?>
						</div>

						<div class="event-list">
						<?php

						//$paged = (get_query_var('paged')) ? get_query_var('paged') : 1; 
						$ordered_posts = new WP_Query(array(
							//'paged' => $paged,
							'nopaging'		=> true,
							'post_type'     => 'sunflower_event',
							'meta_key' 	    => '_sunflower_event_from', 
							'orderby'       => 'meta_value',
							'meta_query'    => array(
									'relation' => 'OR',
									array(
										'key' => '_sunflower_event_from',
										'value' => date('Y-m-d H:i', strToTime('now - 6 hours')),
										'compare' => '>'
									),
								),
							'order'        => 'ASC',
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
					</div> <!-- event-list -->
				</main><!-- #main -->
			</div>
		</div>
</div>

<?php
	wp_enqueue_script(
        'isotope-module',
        get_template_directory_uri() . '/assets/vndr/isotope-layout/dist/isotope.pkgd.min.js',
       null,
        '3.2.1', 
        true
	);

	wp_enqueue_script(
        'isotope-custom',
        get_template_directory_uri() . '/assets/js/isotope.js',
       'isotope-module',
        '3.2.1', 
        true
	);
?>

<?php
get_footer();