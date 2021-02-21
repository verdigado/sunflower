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
				<div class="col-12 col-md-4">
					<nav class="navbar navbar-top navbar-expand-md justify-content-center justify-content-md-start">
						<div class="text-center ">
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
				</div>
				<div class="col-12 col-md-4 p-2 justify-content-center d-flex">
					<?php
						echo get_sunflower_social_media_profiles();	
					?>
				</div>
				<nav class="col-12 col-md-4 navbar navbar-top navbar-expand-md d-flex justify-content-center justify-content-md-end">
					<div class="text-center">
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

						<div class="d-block d-md-none">
							<?php
								wp_nav_menu( array(
									'theme_location'  => 'topmenu',
									'menu_id'		  => 'topmenu',
									'depth'	          => 1, // 1 = no dropdowns, 2 = with dropdowns.
									'container'       => false,
									'menu_class'      => 'navbar-nav small',
									'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
									'walker'          => new WP_Bootstrap_Navwalker(),
								) );
							?>
						</div>
					</div>
				</nav>
			</div>
		
			<div class="row">
				<div class="col-12 mt-4 mb-4">
					<hr>
				</div>	
			</div>
			<div class="row">
				<div class="col-10">
					<p class="small">
						<?php bloginfo('name'); ?> benutzt das<br>freie 
						grüne Theme <a href="https://sunflower-theme.de" target="_blank">sunflower</a>.
					</p>
				</div>
				<div class="col-2">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/logo-diegruenen.svg" class="img-fluid" alt="Logo Bündnis 90/Die Grünen">
				</div>
			</div>

			
			
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
