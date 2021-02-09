<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package sunflower
 */

get_header();

$show_sidebar = @get_post_meta( $post->ID, '_sunflower_show_sidebar')[0] ? true : false;

?>
	<div id="content" class="container">
		<div class="row">
			<div class="col-12 col-md-9">
				<main id="primary" class="site-main mt-5">

					<?php
					while ( have_posts() ) :
						the_post();

						get_template_part( 'template-parts/content', get_post_type() );

						the_post_navigation(
							array(
								'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'sunflower' ) . '</span> <span class="nav-title">%title</span>',
								'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'sunflower' ) . '</span> <span class="nav-title">%title</span>',
							)
						);

						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;

					endwhile; // End of the loop.
					?>

				</main><!-- #main -->
			</div>
	</div>
</div>
<?php
if ( $show_sidebar ) {
	get_sidebar();
}
get_footer();