<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package sunflower
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/sunflower.svg">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'sunflower' ); ?></a>

	<header id="masthead" class="site-header ptsans">
	
		<nav class="navbar navbar-top d-none d-lg-block navbar-expand-lg navbar-dark p-0 topmenu">
			<div class="container">
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topmenu-container" aria-controls="topmenu-container" aria-expanded="false" aria-label="Toggle navigation">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse justify-content-end" id="topmenu-container">
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
				<form class="form-inline my-2 my-md-0 search" action="<?php bloginfo('url'); ?>">
					<input class="form-control form-control-sm" name="s" type="text" placeholder="<?php _e('Search', 'sunflower'); ?>" aria-label="<?php _e('Search', 'sunflower'); ?>"
						value="<?php echo get_search_query(); ?>"
					>
				</form>
				</div>
			</div>
		</nav>

		<div class="container-fluid bloginfo bg-primary">
			<div class="container d-flex align-items-center">
				<a class="me-1" href="<?php echo get_home_url(); ?>">
					<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/sunflower.svg" class="" alt="Sonnenblume - Logo">
				</a>
				<div>
					<div class="h5 text-white arvogruen mb-0 bloginfo-name">
						<?php bloginfo('name'); ?>
					</div>
					<div class="text-white ptsans mb-0 bloginfo-description">
						<?php bloginfo('description'); ?>
					</div>
				</div>
			</div>
		</div>

	</header><!-- #masthead -->

	<?php /* must be outside of masthead for stickness */ ?>
	<div id="navbar-sticky-detector"></div>
	<nav class="navbar navbar-main navbar-expand-lg navbar-light bg-white">
		<div class="container">
			<a class="navbar-brand" href="<?php echo get_home_url(); ?>">
				<img src="<?php echo get_stylesheet_directory_uri(); ?>/assets/img/sunflower.svg" alt="Sonnenblume - Logo">
			</a>
			<button class="navbar-toggler collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#mainmenu-container" aria-controls="mainmenu" aria-expanded="false" aria-label="Toggle navigation">
				<i class="fas fa-times close text-danger"></i>
				<i class="fas fa-bars open"></i>
			</button>

			<div class="collapse navbar-collapse" id="mainmenu-container">
			<?php
				wp_nav_menu( array(
					'theme_location'  => 'mainmenu',
					'menu_id'		  => 'mainmenu',
					'depth'	          => 4, // 1 = no dropdowns, 2 = with dropdowns.
					'container'       => false,
					'menu_class'      => 'navbar-nav mr-auto',
					'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
					'walker'          => new WP_Bootstrap_Navwalker(),
				) );
				?>
			</div>
		</div>
	</nav>

