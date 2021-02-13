<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package sunflower
 */

get_header();

$metadata = @get_post_meta( $post->ID, '_sunflower_metadata')[0] ?: false;
?>
	<div id="content" class="container">
		<div class="row">
			<div class="col-12 col-md-10 offset-md-1">
				<main id="primary" class="site-main">

					<?php
					while ( have_posts() ) :
						the_post();
	
						get_template_part( 'template-parts/content', get_post_type(), ['metadata' => $metadata] );

						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;

					endwhile; // End of the loop.
					?>

				</main><!-- #main -->
			</div>

			<div class="row sunflower-post-navigation">
				<?php
				
				
				$previous = get_previous_post_link(
					'<div class="">%link </div>',
					'%title <div class="announce">' . __('previous', 'sunflower'). '</div>'
				);

				$next = get_next_post_link(
					'<div class="">%link </div>',
					'%title <div class="announce">' . __('next', 'sunflower'). '</div>'
				);

			
				if( $previous ){
					printf('<div class="col-6 ">%s</div>',
						$previous
					);
				}

				if( $next ){
					printf('<div class="col-6">%s</div>',
						$next
					);
				}
		
				?>
			</div>	
	</div>
</div>
<?php
get_sidebar();
get_footer();