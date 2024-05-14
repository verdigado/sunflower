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
 */
function sunflower_get_site_icon_url_defaults( $url ) {
	// A custom site icon seems to be set. Keep it untouched.
	if ( filter_var( $url, FILTER_VALIDATE_URL ) ) {
		return $url;
	}
	// Set default site icon to sunflower.
	return sunflower_parent_or_child( 'assets/img/favicon.ico' );
}

// Add filter only, of terms of use are accepted.
$sunflower_options = get_option( 'sunflower_first_steps_options' );
if ( ( $sunflower_options['sunflower_terms_of_use'] ?? false ) === 'checked' ) {
	add_filter( 'get_site_icon_url', 'sunflower_get_site_icon_url_defaults', 10, 3 );
}
