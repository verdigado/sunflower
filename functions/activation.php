<?php
/**
 * Jobs doing on activation of the sunflower theme.
 *
 * @package Sunflower 26
 */

/**
 * Whether the site title is empty or a generic WordPress placeholder.
 *
 * @param string $title Current site title.
 */
function sunflower_is_placeholder_site_title( $title ) {
	$normalized = strtolower( trim( (string) $title ) );

	return '' === $normalized || 'wordpress' === $normalized;
}

/**
 * Whether the tagline is empty or a default WordPress tagline (EN/de_DE).
 *
 * @param string $tagline Current tagline.
 */
function sunflower_is_placeholder_site_tagline( $tagline ) {
	$normalized = strtolower( trim( (string) $tagline ) );

	$placeholders = array(
		'',
		'just another wordpress site',
		'ein weiterer wordpress-blog',
		'ein weiterer wordpress blog',
	);

	return in_array( $normalized, $placeholders, true );
}

/**
 * Set default site title and tagline when still generic or empty (first setup).
 */
function sunflower_set_default_site_identity_if_placeholders() {
	if ( sunflower_is_placeholder_site_title( (string) get_option( 'blogname', '' ) ) ) {
		update_option( 'blogname', __( 'BÜNDNIS 90/DIE GRÜNEN', 'sunflower' ) );
	}
	if ( sunflower_is_placeholder_site_tagline( (string) get_option( 'blogdescription', '' ) ) ) {
		update_option( 'blogdescription', __( 'Stadt, Kreis oder Bundesland', 'sunflower' ) );
	}
}

/**
 * Jobs run after activation of sunflower theme.
 */
function sunflower_activate_theme() {
	flush_rewrite_rules();
	sunflower_set_default_site_identity_if_placeholders();
	sunflower_import_demo_images();
}

add_action( 'after_switch_theme', 'sunflower_activate_theme', 10, 2 );
