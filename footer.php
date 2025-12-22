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

			</div>

			<div class="d-flex w-100 justify-content-between site-footer__content--bottom">

				<div class="d-flex">
					<?php
					if ( $sunflower_social_media_profiles ) {
						echo wp_kses_post( $sunflower_social_media_profiles );
					} else {
						// Leerer Block, damit das Layout immer den gleichen Platz hat.
						echo '<div class="sunflower__socials"></div>';
					}
					?>
				</div>

				<div class="footerlogo">
					<?php
					sunflower_inline_svg( 'assets/img/concave.svg' );

					// 1. Custom Logo hat immer Priorität
					if ( has_custom_logo() ) {

						$sunflower_custom_logo = wp_get_attachment_image_src(
							get_theme_mod( 'custom_logo' ),
							'full'
						);

						if ( ! empty( $sunflower_custom_logo[0] ) ) {
							printf(
								'<img src="%s" class="img-fluid" alt="%s">',
								esc_url( $sunflower_custom_logo[0] ),
								esc_attr( 'Logo ' . get_bloginfo( 'name' ) )
							);
						}
					} else {

						$sunflower_options      = get_option( 'sunflower_options' );
						$sunflower_color_scheme = $sunflower_options['sunflower_color_scheme'] ?? 'green';

						if ( 'light' === $sunflower_color_scheme ) {
							$sunflower_logo_path = 'assets/img/logo-diegruenen.svg';
						} else {
							$sunflower_logo_path = 'assets/img/logo-diegruenen-auf-tanne.svg';
						}

						printf(
							'<img src="%s" class="img-fluid" alt="%s">',
							esc_url( sunflower_parent_or_child( $sunflower_logo_path ) ),
							esc_attr( 'Logo BÜNDNIS 90/DIE GRÜNEN' )
						);
					}

					sunflower_inline_svg( 'assets/img/concave.svg' );
					?>

			</div>

		</div>

	</div><!-- .site-info -->
</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
