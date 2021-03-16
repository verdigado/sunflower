<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package sunflower
 */

get_header();

$layout_width = @get_post_meta( $post->ID, '_sunflower_styled_layout')[0] ? '' : 'container-narrow';
$metadata = @get_post_meta( $post->ID, '_sunflower_metadata')[0] ?: false;
$styled_layout = @get_post_meta( $post->ID, '_sunflower_styled_layout')[0] ? 'styled-layout' : '';

?>
	<div id="content" class="container <?php echo "$layout_width $styled_layout"; ?>">
		<div class="row">
			<div class="col-12">
				<main id="primary" class="site-main">

					<?php
					while ( have_posts() ) :
						the_post();
	
						get_template_part( 'template-parts/content', get_post_type(), ['metadata' => $metadata, 'class' => 'display-single'] );

						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;

					endwhile; // End of the loop.
					?>

				</main><!-- #main -->
			</div>
		</div>
			<div class="row sunflower-post-navigation mb-3">
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
					printf('<div class="col-12 col-md-6 mb-2 mb-md-0">%s</div>',
						$previous
					);
				}

				if( $next ){
					printf('<div class="col-12 col-md-6">%s</div>',
						$next
					);
				}
		
				?>
				
	</div>


	<?php
		if( get_sunflower_setting('sunflower_show_related_posts') ) {
			get_template_part( 'template-parts/related-posts', '' );
		}
	?>

</div>
<?php
get_sidebar();
get_footer();