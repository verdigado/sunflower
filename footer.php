<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @package  sunflower
 */

?>
<?php
$sunflower_social_media_profiles = sunflower_get_social_media_profiles();
?>

<footer id="colophon" class="site-footer">

	<div class="container site-info">
		<?php
		if ( has_nav_menu( 'footer1' ) || has_nav_menu( 'footer2' ) || $sunflower_social_media_profiles ) {
			?>
		<div class="site-footer__content">

			<div class="d-flex justify-content-between w-100 site-footer__content--top">
				<div class="vendorflex row">
						<p class="small">
							<?php bloginfo( 'name' ); ?> benutzt das freie
							grüne Theme <a href="https://sunflower-theme.de" target="_blank">sunflower</a> &dash; ein
							Angebot der <a href="https://verdigado.com/" target="_blank">verdigado eG</a>.
						</p>
				</div>

				<nav class="navbar navbar-top navbar-expand-md footermenu-1">
					<div class="text-center ">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'footer2',
								'menu_id'        => 'footer2',
								'depth'          => 1,
								// 1 = no dropdowns, 2 = with dropdowns.
								'container'      => false,
								'menu_class'     => 'navbar-nav small',
								'fallback_cb'    => 'WP_Bootstrap_Navwalker::fallback',
								'walker'         => new WP_Bootstrap_Navwalker(),
							)
						);
						?>
					</div>
				</nav>


				<nav class="navbar navbar-top navbar-expand-md footermenu-2">
					<div class="text-center ">
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'footer1',
								'menu_id'        => 'footer1',
								'depth'          => 1,
								// 1 = no dropdowns, 2 = with dropdowns.
								'container'      => false,
								'menu_class'     => 'navbar-nav small',
								'fallback_cb'    => 'WP_Bootstrap_Navwalker::fallback',
								'walker'         => new WP_Bootstrap_Navwalker(),
							)
						);
						?>
					</div>
				</nav>


				<?php
		}
		?>

			</div>

			<div class="d-flex w-100 justify-content-between site-footer__content--bottom">

				<div class="d-flex">
					<?php
					echo wp_kses_post( $sunflower_social_media_profiles );
					?>
				</div>

				<div class="footerlogo">
					<?php
					sunflower_inline_svg( 'assets/img/concave.svg' );
					?>

					<?php
					$sunflower_logo = wp_get_attachment_image_src( get_theme_mod( 'custom_logo' ), 'full' );

					if ( has_custom_logo() ) {
						printf( '<img src="%s" class="img-fluid" alt="Logo %s">', esc_url( $sunflower_logo[0] ), esc_attr( get_bloginfo( 'name' ) ) );
					} else {
						$sunflower_options = get_option( 'sunflower_first_steps_options' );
						if ( ( $sunflower_options['sunflower_terms_of_use'] ?? false ) === 'checked' ) {
							printf( '<img src="%s" class="img-fluid" alt="Logo BÜNDNIS 90/DIE GRÜNEN">', esc_attr( sunflower_parent_or_child( 'assets/img/logo-diegruenen.png' ) ) );
						}
					}
					?>
					<?php
					sunflower_inline_svg( 'assets/img/concave.svg' );
					?>

				</div>

			</div>

		</div>


	</div><!-- .site-info -->
</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
