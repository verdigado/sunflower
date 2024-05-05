<?php
/**
 * Security related methods.
 *
 * @package sunflower
 */

// Remove generator meta-tag.
remove_action( 'wp_head', 'wp_generator' );

add_filter(
	'login_errors',
	static fn () => __( 'Login error. Did you mistype s.th.?', 'sunflower' )
);
