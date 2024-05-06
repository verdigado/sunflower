<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
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
					while ( have_posts() ) :
						the_post();

						get_template_part(
							'template-parts/content',
							get_post_type(),
							array(
								'metadata' => $sunflower_metadata,
								'class'    => 'display-single',
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
			<div class="row sunflower-post-navigation mb-3">
				<?php

				if ( ! sunflower_get_setting( 'sunflower_hide_prev_next' ) ) {
					$sunflower_previous = get_previous_post_link(
						'<div class="">%link </div>',
						'%title <div class="announce">' . __( 'previous', 'sunflower' ) . '</div>'
					);

					$sunflower_next = get_next_post_link(
						'<div class="">%link </div>',
						'%title <div class="announce">' . __( 'next', 'sunflower' ) . '</div>'
					);

					if ( $sunflower_previous ) {
						printf(
							'<div class="col-12 col-md-6 mb-2 mb-md-0">%s</div>',
							wp_kses_post( $sunflower_previous )
						);
					}

					if ( $sunflower_next ) {
						printf(
							'<div class="col-12 col-md-6">%s</div>',
							wp_kses_post( $sunflower_next )
						);
					}
				}

				?>

	</div>


	<?php
	if ( sunflower_get_setting( 'sunflower_show_related_posts' ) ) {
		get_template_part( 'template-parts/related-posts', '' );
	}
	?>

</div>
<?php
get_sidebar();
get_footer();
