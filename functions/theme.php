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
 * Filter custom_logo theme mod to verify the attachment still exists.
 * Returns 0 if the attachment was deleted or doesn't exist.
 *
 * @param mixed $value The custom_logo attachment ID.
 * @return mixed The attachment ID if valid, 0 otherwise.
 */
function sunflower_validate_custom_logo( $value ) {
	if ( empty( $value ) ) {
		return $value;
	}

	$attachment_id = (int) $value;

	// Check if the attachment exists and is an image.
	$attachment = get_post( $attachment_id );
	if ( ! $attachment || 'attachment' !== $attachment->post_type || ! wp_attachment_is_image( $attachment_id ) ) {
		return 0;
	}

	return $attachment_id;
}
add_filter( 'theme_mod_custom_logo', 'sunflower_validate_custom_logo', 10 );

/**
 * Clean up orphaned custom_logo theme mod when an attachment is deleted.
 *
 * @param int $post_id The deleted attachment ID.
 */
function sunflower_cleanup_deleted_logo( $post_id ) {
	$custom_logo_id = get_theme_mod( 'custom_logo' );

	if ( $custom_logo_id && (int) $custom_logo_id === (int) $post_id ) {
		remove_theme_mod( 'custom_logo' );
	}
}
add_action( 'delete_attachment', 'sunflower_cleanup_deleted_logo' );

/**
 * Clean up the site_icon option when its attachment is deleted.
 *
 * @param int $post_id The deleted attachment ID.
 */
function sunflower_cleanup_deleted_site_icon( $post_id ) {
	$site_icon_id = (int) get_option( 'site_icon' );

	if ( $site_icon_id && $site_icon_id === (int) $post_id ) {
		update_option( 'site_icon', 0 );
	}
}
add_action( 'delete_attachment', 'sunflower_cleanup_deleted_site_icon' );

/**
 * Echo CSS class depending on theme setting.
 */
function sunflower_theme_class() {
	echo defined( 'SUNFLOWER_THEME' ) ? 'theme--' . esc_attr( SUNFLOWER_THEME ) : 'theme--default';
}

/**
 * Change URL to site icon to sunflower as default.
 * Only applies to frontend - admin uses native WordPress behavior.
 *
 * @param string $url     Site icon URL.
 * @param string $size    Size in pixel.
 */
function sunflower_get_site_icon_url_defaults( $url, $size ) {

	// Don't modify in admin area - let WordPress handle it natively.
	if ( is_admin() ) {
		return $url;
	}

	$site_icon_id = (int) get_option( 'site_icon' );

	// If a custom site icon is set and the attachment exists, use it.
	if ( $site_icon_id ) {
		$attachment = get_post( $site_icon_id );
		if ( $attachment && 'attachment' === $attachment->post_type && wp_attachment_is_image( $site_icon_id ) ) {
			// Return the original URL from WordPress.
			return $url;
		}
	}

	// No valid custom icon set - use sunflower defaults.
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
