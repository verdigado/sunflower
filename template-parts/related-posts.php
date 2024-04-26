<?php
/**
 * Show related posts
 *
 * @package sunflower
 */

?>
<div class="full-width bg-lightgreen mt-5">
	<div class="container related-posts">
		<div class="row">
			<div class="col-12 text-center p-5">
				<h2>
					<?php
						echo esc_attr__( 'Related posts', 'sunflower' );
					?>
				</h2>
			</div>

			<?php
				$sunflower_related_posts = sunflower_related_posts( get_the_ID(), wp_get_post_categories( get_the_ID() ) );
			while ( $sunflower_related_posts->have_posts() ) {
				$sunflower_related_posts->the_post();

				echo '<div class="col-12 col-md-6">';
				get_template_part( 'template-parts/content', 'archive' );
				echo '</div>';
			}
			?>
		</div>
	</div>
</div>
