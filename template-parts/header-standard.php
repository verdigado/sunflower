<header id="masthead" class="site-header">

		<nav class="navbar navbar-top d-none d-lg-block navbar-expand-lg navbar-dark p-0 topmenu">
			<div class="container">
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topmenu-container" aria-controls="topmenu-container" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse justify-content-between" id="topmenu-container">
					<?php
						printf( '<div class="some-profiles d-flex">%s</div>', '' /*get_sunflower_social_media_profiles()*/ );
					?>
						<div class="d-flex">
							<?php
								wp_nav_menu(
									array(
										'theme_location' => 'topmenu',
										'menu_id'        => 'topmenu',
										'depth'          => 1, // 1 = no dropdowns, 2 = with dropdowns.
										'container'      => false,
										'menu_class'     => 'navbar-nav small',
										'fallback_cb'    => 'WP_Bootstrap_Navwalker::fallback',
										'walker'         => new WP_Bootstrap_Navwalker(),
									)
								);
								?>
							<form class="form-inline my-2 my-md-0 search d-flex" action="<?php bloginfo( 'url' ); ?>">
								<input class="form-control form-control-sm topbar-search-q" name="s" type="text" placeholder="<?php _e( 'Search', 'sunflower' ); ?>" aria-label="<?php _e( 'Search', 'sunflower' ); ?>"
									value="<?php echo get_search_query(); ?>"
								>
								<input type="submit" class="d-none" value="<?php _e( 'Search', 'sunflower' ); ?>" aria-label="<?php _e( 'Search', 'sunflower' ); ?>">
							</form>
							<button class="show-search me-3" title="<?php _e( 'show search', 'sunflower' ); ?>" aria-label="<?php _e( 'show search', 'sunflower' ); ?>">
								<i class="fas fa-search"></i>
							</button>
							<button class="d-none show-contrast" title="<?php _e( 'increase contrast', 'sunflower' ); ?>" aria-label="<?php _e( 'increase contrast', 'sunflower' ); ?>">
								<i class="fab fa-accessible-icon"></i>
							</button>
						</div>
				</div>
			</div>
		</nav>

		<div class="container-fluid bloginfo bg-primary">
			<div class="container d-flex align-items-center">
				<div class="img-container">
					<?php
					if ( function_exists( 'the_custom_logo' ) && has_custom_logo() ) {
						the_custom_logo();
					} else {
						echo '<a href="' . get_home_url() . '">';
							printf( '<img src="%s" class="" alt="Logo">', sunflower_parent_or_child( 'assets/img/sunflower.svg' ) );
						echo '</a>';
					}
					?>
				</div>
				<div>
					<a href="<?php echo get_home_url(); ?>" class="d-block h5 text-white bloginfo-name no-link">
						<?php bloginfo( 'name' ); ?>
					</a>
					<a href="<?php echo get_home_url(); ?>" class="d-block text-white mb-0 bloginfo-description no-link">
						<?php bloginfo( 'description' ); ?>
					</a>
				</div>
			</div>
		</div>

	</header><!-- #masthead -->

	<?php /* must be outside of masthead for stickness */ ?>
	<div id="navbar-sticky-detector"></div>
	<nav class="navbar navbar-main navbar-expand-lg navbar-light bg-white">
		<div class="container">
			<a class="navbar-brand" href="<?php echo get_home_url(); ?>">
				<img src="<?php echo sunflower_parent_or_child( 'assets/img/sunflower.svg' ); ?>" alt="Sonnenblume - Logo">
			</a>
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
						'depth'          => 4, // 1 = no dropdowns, 2 = with dropdowns.
						'container'      => false,
						'menu_class'     => 'navbar-nav mr-auto',
						'fallback_cb'    => 'WP_Bootstrap_Navwalker::fallback',
						'walker'         => new WP_Bootstrap_Navwalker(),
					)
				);
				?>

				<form class="form-inline my-2 mb-2 search d-block d-lg-none" action="<?php bloginfo( 'url' ); ?>">
					<input class="form-control form-control-sm topbar-search-q" name="s" type="text" placeholder="<?php _e( 'Search', 'sunflower' ); ?>" aria-label="<?php _e( 'Search', 'sunflower' ); ?>"
						value="<?php echo get_search_query(); ?>"
					>
					<input type="submit" class="d-none" value="<?php _e( 'Search', 'sunflower' ); ?>" aria-label="<?php _e( 'Search', 'sunflower' ); ?>">

				</form>
			</div>
		</div>
	</nav>
