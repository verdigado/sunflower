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
<html <?php language_attributes(); ?> class="<?php the_sunflower_theme(); ?>">

<head>
	<meta charset="<?php bloginfo('charset'); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
    <?php
        $options = get_option('sunflower_first_steps_options');
        if (($options['sunflower_terms_of_use'] ?? false) == true) {
            printf('<link rel="icon" href="%s" type="image/svg+xml" sizes="any">', sunflower_parent_or_child('assets/img/sunflower.svg'));
            printf('<link rel="icon" href="%s" type="image/png" sizes="48x48">', sunflower_parent_or_child('assets/img/favicon-48x48.png'));
        }
    ?>
	<?php
    get_template_part('template-parts/open-graph');
?>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
	<?php wp_body_open(); ?>
	<div id="page" class="site">
		<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e('Skip to content', 'sunflower'); ?></a>

		<?php
    $header_layout = get_sunflower_setting('sunflower_header_layout') ?: 'standard';

get_template_part('template-parts/header', $header_layout);
?>
