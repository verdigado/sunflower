<?php /* must be outside of masthead for stickness */ ?>
	<div id="navbar-sticky-detector" class="header-personal"></div>
	<nav class="navbar navbar-main navbar-expand-lg navbar-light bg-white header-personal">
		<div class="container nowrap">
			<div class="d-flex w-100">
			<div class="container d-flex align-items-center bloginfo">
				<a class="img-container" href="<?php echo get_home_url(); ?>">
					<img src="<?php echo sunflower_parent_or_child( 'assets/img/sunflower-dark.svg' ); ?>" class="" alt="Sonnenblume - Logo">
				</a>
				<div>
					<div class="h5 bloginfo-name">
						<?php bloginfo( 'name' ); ?>
					</div>
					<div class="mb-0 bloginfo-description">
						<?php bloginfo( 'description' ); ?>
					</div>
				</div>
			</div>
			
			<button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mainmenu-container" aria-controls="mainmenu" aria-expanded="false" aria-label="Toggle navigation">
				<i class="fas fa-times close"></i>
				<i class="fas fa-bars open"></i>
			</button>

			</div>

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
			</div>

			<div class="social-media-profiles d-none d-md-flex">
				<?php
					echo get_sunflower_social_media_profiles();
				?>
			</div>
		</div>
	</nav>

	<script>
		jQuery(document).ready( function (){
			jQuery('.navbar-toggler').click(function(){
				if(jQuery('.navbar-toggler').hasClass('collapsed')){
					window.setTimeout(() => {
						jQuery('.navbar > .container').add('nowrap');
					}, 100);
				}else{
					jQuery('.navbar > .container').removeClass('nowrap');
				}
			})
		})
  </script>
