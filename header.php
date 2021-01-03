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
	<link rel="shortcut icon" href="#">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="bg-white site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'sunflower' ); ?></a>

	<header id="masthead" class="site-header ptsans">
	
		<nav class="navbar navbar-top navbar-expand-lg navbar-dark p-1 topmenu">
			<div class="container">
				<div class="collapse navbar-collapse">
				<?php
					wp_nav_menu( array(
						'theme_location'  => 'topmenu',
						'menu_id'		  => 'topmenu',
						'depth'	          => 1, // 1 = no dropdowns, 2 = with dropdowns.
						'container'       => 'div',
						'container_class' => 'collapse navbar-collapse justify-content-end',
						'container_id'    => 'topmenu',
						'menu_class'      => 'navbar-nav small',
						'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
						'walker'          => new WP_Bootstrap_Navwalker(),
					) );
					?>
				<form class="form-inline my-2 my-md-0">
					<input class="form-control form-control-sm" name="s" type="text" placeholder="<?php _e('Search', 'sunflower'); ?>" aria-label="<?php _e('Search', 'sunflower'); ?>"
						value="<?php echo get_search_query(); ?>"
					>
				</form>
				</div>
			</div>
		</nav>

		<nav class="navbar navbar-main navbar-expand-lg bg-white">
			<div class="container">
				<a class="navbar-brand" href="<?php echo get_home_url(); ?>">
					<img src="<?php echo get_template_directory_uri(); ?>/assets/img/sunflower.svg" class="">
				</a>
				<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample07" aria-controls="navbarsExample07" aria-expanded="false" aria-label="Toggle navigation">
				<span class="navbar-toggler-icon"></span>
				</button>

				<div class="collapse navbar-collapse" id="navbarsExample07">
				<?php
					wp_nav_menu( array(
						'theme_location'  => 'mainmenu',
						'menu_id'		  => 'mainmenu',
						'depth'	          => 2, // 1 = no dropdowns, 2 = with dropdowns.
						'container'       => 'div',
						'container_class' => 'collapse navbar-collapse',
						'container_id'    => 'bs-example-navbar-collapse-1',
						'menu_class'      => 'navbar-nav mr-auto text-uppercase font-weight-bold',
						'fallback_cb'     => 'WP_Bootstrap_Navwalker::fallback',
						'walker'          => new WP_Bootstrap_Navwalker(),
					) );
					?>
				</div>
			</div>
		</nav>
	
	</header><!-- #masthead -->
	

