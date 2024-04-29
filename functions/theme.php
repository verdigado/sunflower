<?php

add_theme_support( 'custom-logo' );

function the_sunflower_theme() {
	echo defined( 'SUNFLOWER_THEME' ) ? 'theme--' . SUNFLOWER_THEME : 'theme--default';
}

function sunflower_get_site_icon_url_defaults( $url, $size, $blog_id ) {
	// Update the $url for the /favicon.ico file
	return sunflower_parent_or_child( 'assets/img/favicon.ico' );
}

$options = get_option( 'sunflower_first_steps_options' );
if ( ( $options['sunflower_terms_of_use'] ?? false ) === 'checked' ) {
	// add the filter
	add_filter( 'get_site_icon_url', 'sunflower_get_site_icon_url_defaults', 10, 3 );
}
