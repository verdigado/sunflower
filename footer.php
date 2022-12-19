<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 * php version 7.4.11
 *
 * @category Sunflower
 * @package  Sunflower
 * @author   Tom Rose <mail@tom-rose.de>
 * @license  GNU General Public License 3 https://www.gnu.org/licenses/gpl-3.0.html
 * @link     https://github.com/codeispoetry/sunflower
 */

?>
<?php
$sunflower_social_media_profiles = get_sunflower_social_media_profiles();
?>

	<footer id="colophon" class="site-footer">
		<div class="container site-info">
			<?php
			if ( has_nav_menu( 'footer1' ) || has_nav_menu( 'footer2' ) || $sunflower_social_media_profiles ) {
				?>
				<div class="row">
					<div class="col-12 col-md-4 d-flex justify-content-center justify-content-md-start">

						<nav class="navbar navbar-top navbar-expand-md ">
							<div class="text-center ">
							<?php
								wp_nav_menu(
									array(
										'theme_location' => 'footer1',
										'menu_id'        => 'footer1',
										'depth'          => 1, // 1 = no dropdowns, 2 = with dropdowns.
										'container'      => false,
										'menu_class'     => 'navbar-nav small',
										'fallback_cb'    => 'WP_Bootstrap_Navwalker::fallback',
										'walker'         => new WP_Bootstrap_Navwalker(),
									)
								);
							?>
							</div>
						</nav>
					</div>
					<div class="col-12 col-md-4 p-2 justify-content-center d-flex">
				<?php
				echo $sunflower_social_media_profiles;
				?>
					</div>
					<div class="col-12 col-md-4 d-flex justify-content-center justify-content-md-end">
						<nav class="navbar navbar-top navbar-expand-md">
							<div class="text-center">
							<?php
								wp_nav_menu(
									array(
										'theme_location' => 'footer2',
										'menu_id'        => 'footer2',
										'depth'          => 1, // 1 = no dropdowns, 2 = with dropdowns.
										'container'      => false,
										'menu_class'     => 'navbar-nav small',
										'fallback_cb'    => 'WP_Bootstrap_Navwalker::fallback',
										'walker'         => new WP_Bootstrap_Navwalker(),
									)
								);
							?>
							</div>
						</nav>
					</div>
				</div>
			
				<div class="row">
					<div class="col-12 mt-4 mb-4">
						<hr>
					</div>    
				</div>

				<?php
			}
			?>
				
			
			<div class="row d-block d-lg-none mb-5">
				<div class="col-12">
					<nav class=" navbar navbar-top navbar-expand-md d-flex justify-content-center">
						<div class="text-center">
							<?php
								wp_nav_menu(
									array(
										'theme_location' => 'topmenu',
										'menu_id'        => 'topmenu-footer',
										'depth'          => 1, // 1 = no dropdowns, 2 = with dropdowns.
										'container'      => false,
										'menu_class'     => 'navbar-nav small',
										'fallback_cb'    => 'WP_Bootstrap_Navwalker::fallback',
										'walker'         => new WP_Bootstrap_Navwalker(),
									)
								);
								?>
						</div>
					</nav>
				</div>
			</div>

			
			<div class="row">
				<div class="col-8 col-md-10">
					<p class="small">
						<?php bloginfo( 'name' ); ?> benutzt das<br>freie 
						grüne Theme <a href="https://sunflower-theme.de" target="_blank">sunflower</a> &dash; ein 
						Angebot der <a href="https://verdigado.com/" target="_blank">verdigado eG</a>.
					</p>
				</div>
				<div class="col-4 col-md-2">
				
					<?php
					$logo = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' );

					if ( has_custom_logo() ) {
						printf( '<img src="%s" class="img-fluid" alt="Logo %s">', esc_url( $logo[0] ), get_bloginfo( 'name' ) );
					} else {
						printf( '<img src="%s" class="img-fluid" alt="Logo Bündnis 90/Die Grünen">', sunflower_parent_or_child( 'assets/img/logo-diegruenen.svg' ) );
					}
					?>
				</div>
			</div>

			
			
		</div><!-- .site-info -->
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
