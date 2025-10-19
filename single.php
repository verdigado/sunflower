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
					$sunflower_arrow_left  = '<img src="' . esc_url( get_template_directory_uri() . '/assets/img/arrow-left.svg' ) . '" alt="" class="icon-arrow icon-arrow--left" />';
					$sunflower_arrow_right = '<img src="' . esc_url( get_template_directory_uri() . '/assets/img/arrow-right.svg' ) . '" alt="" class="icon-arrow icon-arrow--right" />';

					$sunflower_previous = get_previous_post_link(
						'<div class="sunflower-post-navigation__item sunflower-post-navigation__item--prev">%link</div>',
						$sunflower_arrow_left . '%title'
					);

					$sunflower_next = get_next_post_link(
						'<div class="sunflower-post-navigation__item sunflower-post-navigation__item--next">%link</div>',
						'%title ' . $sunflower_arrow_right
					);

					if ( $sunflower_previous ) {
						printf(
							'<div class="sunflower-post-navigation__prev">%s</div>',
							wp_kses_post( $sunflower_previous )
						);
					}

					if ( $sunflower_next ) {
						printf(
							'<div class="sunflower-post-navigation__next">%s</div>',
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
