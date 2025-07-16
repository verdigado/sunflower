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

		<a href="<?php echo esc_url(home_url('/')); ?>" class="brand-left d-flex flex-column align-items-center text-center">
			<div class="label-top fw-bold text-uppercase skew-box"><?php esc_attr(bloginfo('name')); ?></div>
			<div class="label-bottom fw-bold skew-box"><?php esc_attr(bloginfo('description')); ?></div>
		</a>

		<!-- Center: Logo + Menu -->
		<div class="right-bar nav-center d-flex align-items-center flex-wrap justify-content-center">

			<?php
			get_template_part('assets/img/concave');
			?>

			<div class="logo-background">
				<?php
				$sunflower_options = get_option('sunflower_first_steps_options');
				if (function_exists('the_custom_logo') && has_custom_logo()) {
					the_custom_logo();
				} elseif (($sunflower_options['sunflower_terms_of_use'] ?? false) === 'checked') {
					echo '<a href="' . esc_url(home_url()) . '" rel="home">';
					get_template_part( 'assets/img/sunflower' );
					echo '</a>';
				}
				?>
			</div>

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

			<?php
			get_template_part('assets/img/concave');
			?>

		</div>
	</div>
</header>

<?php wp_body_open(); ?>
