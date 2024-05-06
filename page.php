<?php
/**
 * The template for displaying all pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package sunflower
 */

get_header();

$sunflower_layout_width  = get_post_meta( $post->ID, '_sunflower_styled_layout', true ) ? '' : 'container-narrow';
$sunflower_styled_layout = get_post_meta( $post->ID, '_sunflower_styled_layout', true ) ? 'styled-layout' : '';
$sunflower_metadata      = get_post_meta( $post->ID, '_sunflower_metadata', true ) ?? false;

?>
	<div id="content" class="container <?php printf( '%s %s', esc_attr( $sunflower_layout_width ), esc_attr( $sunflower_styled_layout ) ); ?>">
		<div class="row">
			<div class="col-12">
				<main id="primary" class="site-main">

					<?php
					$sunflower_display_single = ( is_front_page() ) ? '' : 'display-single';

					while ( have_posts() ) :
						the_post();

						get_template_part(
							'template-parts/content',
							'page',
							array(
								'metadata' => $sunflower_metadata,
								'class'    => $sunflower_display_single,
							)
						);

						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;

					endwhile;
					// End of the loop.
					?>

				</main><!-- #main -->
			</div>
	</div>
</div>
<?php
get_sidebar();
get_footer();
