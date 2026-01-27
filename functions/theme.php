<?php
/**
 * Theme related methods.
 *
 * @package Sunflower 26
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

/**
 * Input-Icon-Script
 */
function sunflower_load_input_icon_script() {
	wp_enqueue_script(
		'input-icons',
		get_template_directory_uri() . '/assets/js/input-icons.js',
		array(),
		filemtime( get_template_directory() . '/assets/js/input-icons.js' ),
		true
	);
}
add_action( 'wp_enqueue_scripts', 'sunflower_load_input_icon_script' );


/**
 * Load file content and echo
 *
 * @param string $file     The file name with full path.
 */
function sunflower_inline_svg( $file ) {
	$filepath = get_template_directory() . '/' . $file;
	if ( file_exists( $filepath ) ) {
		$wpfsd = new WP_Filesystem_Direct( false );
		$svg   = $wpfsd->get_contents( $filepath );
		// We do not escape this, because only theme svg may be imported.
		echo $svg; // phpcs:ignore
	} else {
		echo '<!-- SVG not found: ' . esc_html( $filename ) . ' -->';
	}
}

/**
 * Get body classes depending on theme options
 *
 * @return array Array of body classes.
 */
function sunflower_get_body_classes() {

	$options = get_option( 'sunflower_options' );

	if ( ! empty( $options['sunflower_form_style'] ) ) {
		$classes[] = 'formstyle-' . sanitize_html_class( $options['sunflower_form_style'] );
	}

	if ( ! empty( $options['sunflower_color_scheme'] ) ) {
		$classes[] = 'colorscheme-' . sanitize_html_class( $options['sunflower_color_scheme'] );
	}

	if ( ! empty( $options['sunflower_footer_layout'] ) ) {
		$classes[] = 'footer-' . sanitize_html_class( $options['sunflower_footer_layout'] );
	}

	return $classes;
}

/**
 * Add body classes to the theme options
 *
 * @param array $classes Array containing all set body classes.
 */
function sunflower_add_body_classes( $classes ) {

	$classes = array_merge( $classes, sunflower_get_body_classes() );

	return $classes;
}
add_filter( 'body_class', 'sunflower_add_body_classes' );

/**
 * Add the styled_layout class in backend editor if set.
 *
 * @param string $classes The editor body classes.
 * @return string The modified classes
 */
function sunflower_admin_classes_layout( $classes ) {

	global $post;

	if ( $post ) {
		$sunflower_styled_layout = get_post_meta( $post->ID, '_sunflower_styled_layout', true ) ? 'styled-layout' : '';
		$classes                .= ' ' . $sunflower_styled_layout;
	}

	$classes .= ' ' . implode( ' ', sunflower_get_body_classes() );

	return trim( $classes );
}
add_filter( 'admin_body_class', 'sunflower_admin_classes_layout' );
