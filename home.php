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
			<div class="col-12 col-md-8">
				<main id="primary" class="site-main">
					<?php if ( have_posts() ) : ?>
					
						<header class="page-header">
                            <h1 class="page-title"><?php bloginfo('blogname');?></h1>
							<div class="archive-description"><?php bloginfo('description'); ?></div>
						</header><!-- .page-header -->

						<?php
						/* Start the Loop */
						while ( have_posts() ) :
							the_post();

							/*
							* Include the Post-Type-specific template for the content.
							* If you want to override this in a child theme, then include a file
							* called content-___.php (where ___ is the Post Type name) and that will be used instead.
							*/
							get_template_part( 'template-parts/content', 'archive' );

						endwhile;

						the_posts_navigation();

					else :

						get_template_part( 'template-parts/content', 'none' );

					endif;
					?>

				</main><!-- #main -->
			</div>
			<?php
				get_sidebar();
			?>
		</div>
</div>
<?php
get_footer();