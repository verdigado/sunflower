<?php
/**
 * Custom Header Template
 *
 * @package Sunflower 26
 */

?>

<?php if ( has_nav_menu( 'topmenu' ) ) : ?>
	<div class="topmenu">
		<div class="container-fluid">
			<nav class="navbar navbar-expand-lg py-0" aria-label="<?php esc_attr_e( 'Top menu', 'sunflower' ); ?>">
				<div class="navbar-collapse">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'topmenu',
							'menu_class'     => 'navbar-nav nav flex-row',
							'container'      => false,
							'fallback_cb'    => false,
							'item_class'     => 'nav-item',
							'link_class'     => 'nav-link',
						)
					);
					?>
				</div>
			</nav>
		</div>
	</div>
<?php endif; ?>
<?php
	$sunflower_options = get_option( 'sunflower_options' );

if ( ( $sunflower_options['sunflower_design_switcher'] ?? false ) === 'checked' ) :
	?>

<button id="design-switcher-toggle"
		class="design-switcher-toggle"
		aria-label="Design‑Umschalter öffnen"
		aria-haspopup="dialog"
		aria-controls="design-switcher-panel">
		<i class="fas fa-brush"></i>
</button>

<div id="design-switcher-panel"
	class="design-switcher-panel"
	role="dialog"
	aria-modal="true"
	aria-labelledby="design-switcher-title"
	hidden>
	<header class="design-switcher-header">
		<h2 id="design-switcher-title"><?php esc_html_e( 'Design Switch', 'sunflower' ); ?></h2>
		<button id="design-switcher-close"
				class="design-switcher-close"
				aria-label="Schließen">
				<i class="fas fa-xmark"></i>
		</button>
	</header>

	<div id="design-switcher" class="design-switcher">
		<label><?php esc_html_e( 'Shape Style', 'sunflower' ); ?>
			<select id="formstyle-select">
				<option value="rounded"><?php esc_html_e( 'Rounded', 'sunflower' ); ?></option>
				<option value="sharp"><?php esc_html_e( 'Sharp', 'sunflower' ); ?></option>
			</select>
		</label>

		<label><?php esc_html_e( 'Color Mood', 'sunflower' ); ?>
			<select id="colorscheme-select">
				<option value="light"><?php esc_html_e( 'Light', 'sunflower' ); ?></option>
				<option value="green"><?php esc_html_e( 'Dark', 'sunflower' ); ?></option>
			</select>
		</label>

		<label><?php esc_html_e( 'Footer Color Variant', 'sunflower' ); ?>
			<select id="footer-select">
				<option value="sand"><?php esc_html_e( 'Sand', 'sunflower' ); ?></option>
				<option value="green"><?php esc_html_e( 'Light Green', 'sunflower' ); ?></option>
			</select>
		</label>
	</div>
</div>
<div id="design-switcher-backdrop" class="design-switcher-backdrop" hidden></div>
<?php endif; ?>
<header class="top-bar">
	<div class="container-fluid d-flex align-items-center justify-content-between flex-wrap">

		<!-- Left: Label Block -->

		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="brand-left branding d-flex flex-column text-center">
			<div class="label-top skew-box"><?php esc_attr( bloginfo( 'name' ) ); ?></div>
			<div class="label-bottom skew-box"><?php esc_attr( bloginfo( 'description' ) ); ?></div>
		</a>

		<?php if ( is_active_sidebar( 'header-after-brand' ) ) : ?>
		<div class="header-widget-area">
			<?php dynamic_sidebar( 'header-after-brand' ); ?>
		</div>
		<?php endif; ?>

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

	<button class="hamburger" type="button" aria-expanded="false" aria-label="<?php esc_attr_e( 'Menu', 'sunflower' ); ?>">
		<?php
		sunflower_inline_svg( 'assets/img/concave.svg' );
		?>

		<span class="hamburger__top"></span>
		<span class="hamburger__center"></span>
		<span class="hamburger__bottom"></span>

		<?php
		sunflower_inline_svg( 'assets/img/concave.svg' );
		?>
	</button>
	</div>

	<?php
	if ( is_front_page() ) {
		$sunflower_social_media_icons = '';
		if ( sunflower_get_setting( 'sunflower_header_social_media' ) ) {
			$sunflower_social_media_icons = sunflower_get_social_media_profiles();
		}
		printf( '<div class="social-media-profiles d-none d-md-flex">%s</div>', wp_kses_post( $sunflower_social_media_icons ) );
	}
	?>

</header>
