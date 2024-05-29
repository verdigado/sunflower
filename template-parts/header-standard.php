<?php
/**
 * Standard Header
 *
 * @package sunflower
 */

?>
<header id="masthead" class="site-header">

		<nav class="navbar navbar-top d-none d-lg-block navbar-expand-lg navbar-dark p-0 topmenu">
			<div class="container">
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topmenu-container" aria-controls="topmenu-container" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse justify-content-between" id="topmenu-container">
					<?php
					$sunflower_social_media_icons = '';
					if ( sunflower_get_setting( 'sunflower_header_social_media' ) ) {
						$sunflower_social_media_icons = sunflower_get_social_media_profiles();
					}
					printf( '<div class="social-media-profiles d-none d-md-flex">%s</div>', wp_kses_post( $sunflower_social_media_icons ) );
					?>
						<div class="d-flex">
							<?php
								wp_nav_menu(
									array(
										'theme_location' => 'topmenu',
										'menu_id'        => 'topmenu',
										// 1 = no dropdowns, 2 = with dropdowns.
										'depth'          => 1,
										'container'      => false,
										'menu_class'     => 'navbar-nav small',
										'fallback_cb'    => 'WP_Bootstrap_Navwalker::fallback',
										'walker'         => new WP_Bootstrap_Navwalker(),
									)
								);
								?>
							<form class="form-inline my-2 my-md-0 search d-flex" action="<?php bloginfo( 'url' ); ?>">
								<input class="form-control form-control-sm topbar-search-q" name="s" type="text" placeholder="<?php esc_html_e( 'Search', 'sunflower' ); ?>" aria-label="<?php esc_html_e( 'Search', 'sunflower' ); ?>"
									value="<?php echo get_search_query(); ?>"
								>
								<input type="submit" class="d-none" value="<?php esc_html_e( 'Search', 'sunflower' ); ?>" aria-label="<?php esc_html_e( 'Search', 'sunflower' ); ?>">
							</form>
							<button class="show-search me-3" title="<?php esc_html_e( 'show search', 'sunflower' ); ?>" aria-label="<?php esc_html_e( 'show search', 'sunflower' ); ?>">
								<i class="fas fa-search"></i>
							</button>
							<button class="d-none show-contrast" title="<?php esc_html_e( 'increase contrast', 'sunflower' ); ?>" aria-label="<?php esc_html_e( 'increase contrast', 'sunflower' ); ?>">
								<i class="fab fa-accessible-icon"></i>
							</button>
						</div>
				</div>
			</div>
		</nav>

		<div class="container-fluid bloginfo bg-primary">
			<div class="container d-flex align-items-center">
				<div class="img-container
				<?php
				$sunflower_options = get_option( 'sunflower_first_steps_options' );
				if ( has_custom_logo() ) {
					echo 'custom-logo';
				} elseif ( ( $sunflower_options['sunflower_terms_of_use'] ?? false ) === 'checked' ) {
						echo 'sunflower-logo';
				}
				?>
				">
					<?php
					if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
						the_custom_logo();
					} else {
						echo '<a href="' . esc_url( get_home_url() ) . '" rel="home" aria-current="page" title="', esc_attr( get_bloginfo( 'name' ) ) . '">';
						if ( ( $sunflower_options['sunflower_terms_of_use'] ?? false ) === 'checked' ) {
							printf( '<img src="%s" class="" alt="Logo">', esc_attr( sunflower_parent_or_child( 'assets/img/sunflower.svg' ) ) );
						}

						echo '</a>';
					}
					?>
				</div>
				<div>
					<a href="<?php echo esc_url( get_home_url() ); ?>" class="d-block h5 text-white bloginfo-name no-link">
						<?php esc_attr( bloginfo( 'name' ) ); ?>
					</a>
					<a href="<?php echo esc_url( get_home_url() ); ?>" class="d-block text-white mb-0 bloginfo-description no-link">
						<?php esc_attr( bloginfo( 'description' ) ); ?>
					</a>
				</div>
			</div>
		</div>

	</header><!-- #masthead -->

	<?php /* must be outside of masthead for stickness */ ?>
	<div id="navbar-sticky-detector"></div>
	<nav class="navbar navbar-main navbar-expand-lg navbar-light bg-white">
		<div class="container">
			<?php
				$sunflower_options = get_option( 'sunflower_first_steps_options' );
			if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
				$sunflower_custom_logo_id = get_theme_mod( 'custom_logo' );
				$sunflower_custom_logo    = wp_get_attachment_image_src( $sunflower_custom_logo_id, 'thumbnail' );
				printf( '<a class="navbar-brand" href="%s">', esc_url( $sunflower_custom_logo[0] ) );
				printf( '<img src="%s" class="custom-logo" alt="Logo" title="%s"></a>', esc_url( $sunflower_custom_logo[0] ), esc_attr( get_bloginfo( 'name' ) ) );
			} elseif ( ( $sunflower_options['sunflower_terms_of_use'] ?? false ) === 'checked' ) {
				printf( '<a class="navbar-brand" href="%s">', esc_url( get_home_url() ) );
				printf( '<img src="%s" class="sunflower-logo" alt="Sonnenblume - Logo" title="%s"></a>', esc_attr( sunflower_parent_or_child( 'assets/img/sunflower.svg' ) ), esc_attr( get_bloginfo( 'name' ) ) );
			}
			?>
			<?php
			/**
			 * Check if a navigation highlight button exists.
			 */
			function sunflower_output_highlight_button_if_exists() {
				if ( has_nav_menu( 'mainmenu' ) ) {
					$menu_locations = get_nav_menu_locations();
					$menu_id        = $menu_locations['mainmenu'];
					$menu_items     = wp_get_nav_menu_items( $menu_id );

					foreach ( $menu_items as $menu_item ) {
						if ( in_array( 'button-highlight', $menu_item->classes, true ) ) {
							$classes_string = implode( ' ', $menu_item->classes );
							echo '
							<div class="button-highlight-stuck"><a  href="' . esc_url( $menu_item->url ) . '"><i class="' . esc_attr( $classes_string ) . '"></i> ' . esc_html( $menu_item->title ) . '</a></div>';
							return;
						}
					}
				}
			}
			?>

			<?php sunflower_output_highlight_button_if_exists(); ?>


			<button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mainmenu-container" aria-controls="mainmenu" aria-expanded="false" aria-label="Toggle navigation">
				<i class="fas fa-times close"></i>
				<i class="fas fa-bars open"></i>
			</button>

			<div class="collapse navbar-collapse" id="mainmenu-container">
				<?php
					wp_nav_menu(
						array(
							'theme_location' => 'mainmenu',
							'menu_id'        => 'mainmenu',
							// 1 = no dropdowns, 2 = with dropdowns.
							'depth'          => 4,
							// We opened the <div> container already.
							'container'      => false,
							'menu_class'     => 'navbar-nav mr-auto',
							'fallback_cb'    => 'WP_Bootstrap_Navwalker::fallback',
							'walker'         => new WP_Bootstrap_Navwalker(),
						)
					);
					?>

				<form class="form-inline my-2 mb-2 search d-block d-lg-none" action="<?php bloginfo( 'url' ); ?>">
					<input class="form-control form-control-sm topbar-search-q" name="s" type="text" placeholder="<?php esc_html_e( 'Search', 'sunflower' ); ?>" aria-label="<?php esc_html_e( 'Search', 'sunflower' ); ?>"
						value="<?php echo get_search_query(); ?>"
					>
					<input type="submit" class="d-none" value="<?php esc_html_e( 'Search', 'sunflower' ); ?>" aria-label="<?php esc_html_e( 'Search', 'sunflower' ); ?>">

				</form>
			</div>
		</div>
	</nav>
