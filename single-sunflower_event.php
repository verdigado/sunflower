<?php
/**
 * The template for displaying all single events
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package sunflower
 */

get_header();

$show_sidebar = @get_post_meta( $post->ID, '_sunflower_show_sidebar')[0] ? true : false;
$_sunflower_event_from = @get_post_meta( $post->ID, '_sunflower_event_from')[0] ?: false;
$_sunflower_event_until = @get_post_meta( $post->ID, '_sunflower_event_from')[0] ?: false;

$_sunflower_event_location_name = @get_post_meta( $post->ID, '_sunflower_event_location_name')[0] ?: false;
$_sunflower_event_location_street = @get_post_meta( $post->ID, '_sunflower_event_location_street')[0] ?: false;
$_sunflower_event_location_city = @get_post_meta( $post->ID, '_sunflower_event_location_city')[0] ?: false;


?>
	<div id="content" class="container">
		<div class="row">
			<div class="col-12 <?php if ( $show_sidebar ) echo 'col-md-8'; ?>">
				<main id="primary" class="site-main">
					<?php
					echo 'Von:' . $_sunflower_event_from;
					echo 'bis' . $_sunflower_event_until;

					printf('<div>%s, %s, %s</div>',
						$_sunflower_event_location_name,
						$_sunflower_event_location_street,
						$_sunflower_event_location_city
					);

					while ( have_posts() ) :
						the_post();

						get_template_part( 'template-parts/content', 'post' );

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
	<?php
		if ( $show_sidebar ) {
			get_sidebar();
		}
	?>
	</div>
</div>
<?php
get_footer();