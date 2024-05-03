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

/**
 * Change read_more link text.
 */
function sunflower_read_more_link() {
	return '…';
}

add_filter( 'excerpt_more', 'sunflower_read_more_link', 910 );
