<?php
/**
 * Custom Header Template
 *
 * @package sunflower
 */

?>

<header class="top-bar">
	<div class="container-fluid d-flex align-items-center justify-content-between flex-wrap">

		<!-- Left: Label Block -->

		<a href="<?php echo esc_url(home_url('/')); ?>" class="brand-left branding d-flex flex-column text-center">
			<div class="label-top fw-bold text-uppercase skew-box"><?php esc_attr(bloginfo('name')); ?></div>
			<div class="label-bottom fw-bold skew-box"><?php esc_attr(bloginfo('description')); ?></div>
		</a>


		<div class="right-bar menubar nav-center d-flex align-items-center flex-wrap justify-content-center">

			<?php
			get_template_part('assets/img/concave');
			?>

			<div class="right-bar__content">

				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"
				   rel="home"
				   class="logo-background">
					<?php
					$sunflower_options = get_option('sunflower_first_steps_options');
					if (function_exists('the_custom_logo') && has_custom_logo()) {
						the_custom_logo();
					} elseif (($sunflower_options['sunflower_terms_of_use'] ?? false) === 'checked') {
						get_template_part('assets/img/sunflower');
					}
					?>
				</a>

				<nav class="main-menu">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'mainmenu',
							'menu_class' => 'nav',
							'container' => false,
							'fallback_cb' => false,
						)
					);
					?>

				</nav>
			</div>

			<?php
			get_template_part('assets/img/concave');
			?>

		</div>

		<nav class="hamburger">
			<?php
			get_template_part('assets/img/concave');
			?>

			<div class="hamburger__top"></div>
			<div class="hamburger__center"></div>
			<div class="hamburger__bottom"></div>

			<?php
			get_template_part('assets/img/concave');
			?>
		</nav>
	</div>
</header>

<?php wp_body_open(); ?>
