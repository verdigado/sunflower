<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package sunflower
 */

?>

	<footer id="colophon" class="site-footer">
		<div class="container site-info">
			<div class="row">
				<nav class="col-md-4 navbar navbar-top navbar-expand-md navbar-dark">
					<div class="container">
						<?php
							wp_nav_menu( array(
								'theme_location'  => 'footer1',
								'menu_id'		  => 'footer1',
								'depth'	          => 1, // 1 = no dropdowns, 2 = with dropdowns.
								'container'       => false,
								'menu_class'      => 'navbar-nav small',
								'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
								'walker'          => new WP_Bootstrap_Navwalker(),
							) );
						?>
					</div>
				</nav>
				<div class="col-md-4 p-2 justify-content-center d-flex">
					<?php
						echo get_sunflower_social_media_profiles();	
					?>
				</div>
				<nav class="col-md-4 navbar navbar-top navbar-expand-md navbar-dark d-flex justify-content-end">
					<div class="">
						<?php
							wp_nav_menu( array(
								'theme_location'  => 'footer2',
								'menu_id'		  => 'footer2',
								'depth'	          => 1, // 1 = no dropdowns, 2 = with dropdowns.
								'container'       => false,
								'menu_class'      => 'navbar-nav small',
								'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
								'walker'          => new WP_Bootstrap_Navwalker(),
							) );
						?>
					</div>
				</nav>
			</div>
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
