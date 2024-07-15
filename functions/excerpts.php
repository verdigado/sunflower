<?php
/**
 * Excerpt related functions.
 *
 * @package sunflower
 */

/**
 * Get the length of the excerpt from settings.
 */
function sunflower_excerpt_length() {
	return sunflower_get_setting( 'excerpt_length' ) ? sunflower_get_setting( 'excerpt_length' ) : 30;
}

add_filter( 'excerpt_length', 'sunflower_excerpt_length', 900 );
