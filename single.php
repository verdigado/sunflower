<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Sunflower 26
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
			<div class="row sunflower-post-navigation">
				<?php

				if ( ! sunflower_get_setting( 'sunflower_hide_prev_next' ) ) {
					$sunflower_arrow_left       = '<img src="' . esc_url( get_template_directory_uri() . '/assets/img/arrow-left.svg' ) . '" alt="" class="icon-arrow icon-arrow--left" />';
					$sunflower_arrow_right      = '<img src="' . esc_url( get_template_directory_uri() . '/assets/img/arrow-right.svg' ) . '" alt="" class="icon-arrow icon-arrow--right" />';
					$sunflower_max_title_length = 30;

					$sunflower_prev_post = get_previous_post();
					if ( $sunflower_prev_post ) {
						$sunflower_prev_title = get_the_title( $sunflower_prev_post );
						if ( mb_strlen( $sunflower_prev_title ) > $sunflower_max_title_length ) {
							$sunflower_prev_title = mb_substr( $sunflower_prev_title, 0, $sunflower_max_title_length ) . '…';
						}
						printf(
							'<div class="sunflower-post-navigation__prev"><div class="sunflower-post-navigation__item sunflower-post-navigation__item--prev"><a href="%s">%s%s</a></div></div>',
							esc_url( get_permalink( $sunflower_prev_post ) ),
							wp_kses_post( $sunflower_arrow_left ),
							esc_html( $sunflower_prev_title )
						);
					}

					$sunflower_next_post = get_next_post();
					if ( $sunflower_next_post ) {
						$sunflower_next_title = get_the_title( $sunflower_next_post );
						if ( mb_strlen( $sunflower_next_title ) > $sunflower_max_title_length ) {
							$sunflower_next_title = mb_substr( $sunflower_next_title, 0, $sunflower_max_title_length ) . '…';
						}
						printf(
							'<div class="sunflower-post-navigation__next"><div class="sunflower-post-navigation__item sunflower-post-navigation__item--next"><a href="%s">%s%s</a></div></div>',
							esc_url( get_permalink( $sunflower_next_post ) ),
							esc_html( $sunflower_next_title ),
							wp_kses_post( $sunflower_arrow_right )
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
get_footer();
