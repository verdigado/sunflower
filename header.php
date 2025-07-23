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
<html <?php language_attributes(); ?> class="<?php sunflower_theme_class(); ?>">

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">

	<script>document.documentElement.classList.add('preload');</script>

	<?php
	get_template_part( 'template-parts/open-graph' );
	?>
	<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<div id="page" class="site">
		<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'sunflower' ); ?></a>

		<?php
		$sunflower_header_layout = sunflower_get_setting( 'sunflower_header_layout' ) ? sunflower_get_setting( 'sunflower_header_layout' ) : 'standard';

		get_template_part( 'template-parts/header', $sunflower_header_layout );
		?>
