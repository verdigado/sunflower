<?php
/**
 * Custom Header Template
 *
 * @package Sunflower 26
 */

?>

<header class="top-bar">
	<div class="container-fluid d-flex align-items-center justify-content-between flex-wrap">

		<!-- Left: Label Block -->

		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="brand-left branding d-flex flex-column text-center">
			<div class="label-top skew-box"><?php esc_attr( bloginfo( 'name' ) ); ?></div>
			<div class="label-bottom skew-box"><?php esc_attr( bloginfo( 'description' ) ); ?></div>
		</a>


		<div class="right-bar menubar nav-center d-flex align-items-center flex-wrap justify-content-center">

			<?php
			sunflower_inline_svg( 'assets/img/concave.svg' );
			?>

			<div class="right-bar__content">

				<?php
				$sunflower_options = get_option( 'sunflower_first_steps_options' );
				if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
					// Custom Logo wird direkt angezeigt (erzeugt eigenen Link).
					the_custom_logo();
				} elseif ( ( $sunflower_options['sunflower_terms_of_use'] ?? false ) === 'checked' ) {
					// Standard Sunflower Logo mit Hintergrund.
					?>
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>"
						rel="home"
						class="logo-background">
						<?php sunflower_inline_svg( 'assets/img/sunflower-3.0.svg' ); ?>
					</a>
					<?php
				}
				?>

				<nav class="main-menu">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'mainmenu',
							'menu_class'     => 'nav',
							'container'      => false,
							'fallback_cb'    => false,
						)
					);
					?>

				</nav>
			</div>

			<?php
			sunflower_inline_svg( 'assets/img/concave.svg' );
			?>

		</div>

		<nav class="hamburger">
			<?php
			sunflower_inline_svg( 'assets/img/concave.svg' );
			?>

			<div class="hamburger__top"></div>
			<div class="hamburger__center"></div>
			<div class="hamburger__bottom"></div>

			<?php
			sunflower_inline_svg( 'assets/img/concave.svg' );
			?>
		</nav>
	</div>

	<?php
	$sunflower_social_media_icons = '';
	if ( sunflower_get_setting( 'sunflower_header_social_media' ) ) {
		$sunflower_social_media_icons = sunflower_get_social_media_profiles();
	}
	printf( '<div class="social-media-profiles d-none d-md-flex">%s</div>', wp_kses_post( $sunflower_social_media_icons ) );
	?>

</header>

<?php wp_body_open(); ?>
