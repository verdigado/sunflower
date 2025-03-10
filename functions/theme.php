<?php
/**
 * Theme related methods.
 *
 * @package sunflower
 */

/**
 * Register support for custom-logo
 */
add_theme_support( 'custom-logo' );

/**
 * Echo CSS class depending on theme setting.
 */
function sunflower_theme_class() {
	echo defined( 'SUNFLOWER_THEME' ) ? 'theme--' . esc_attr( SUNFLOWER_THEME ) : 'theme--default';
}

/**
 * Change URL to site icon to sunflower as default.
 *
 * @param string $url     Site icon URL.
 * @param string $size    Size in pixel.
 */
function sunflower_get_site_icon_url_defaults( $url, $size ) {

	$site_icon_id = (int) get_option( 'site_icon' );

	// A custom site icon seems to be set. Keep it untouched.
	if ( $site_icon_id && filter_var( $url, FILTER_VALIDATE_URL ) ) {
		return $url;
	}

	switch ( $size ) {
		case 32:
			$icon_url = sunflower_parent_or_child( 'assets/img/favicon.ico' );
			break;
		case 192:
			$icon_url = sunflower_parent_or_child( 'assets/img/sunflower-192.png' );
			break;
		case 180:
			$icon_url = sunflower_parent_or_child( 'assets/img/sunflower-180.png' );
			break;
		case 270:
			$icon_url = sunflower_parent_or_child( 'assets/img/sunflower-270.png' );
			break;
		default:
			$icon_url = sunflower_parent_or_child( 'assets/img/sunflower.png' );
			break;
	}
	// Set default site icon to sunflower.
	return $icon_url;
}

// Add filter only, if terms of use are accepted.
$sunflower_options = get_option( 'sunflower_first_steps_options' );
if ( ( $sunflower_options['sunflower_terms_of_use'] ?? false ) === 'checked' ) {
	add_filter( 'get_site_icon_url', 'sunflower_get_site_icon_url_defaults', 10, 3 );
}
