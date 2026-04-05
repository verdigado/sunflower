<?php
/**
 * Jobs doing on activation of the sunflower theme.
 *
 * @package Sunflower 26
 */

/**
 * Jobs run after activation of sunflower theme.
 */
function sunflower_activate_theme() {
	// Flush rewrite rules to avoid 404 errors after theme activation.
	flush_rewrite_rules();
}

add_action( 'after_switch_theme', 'sunflower_activate_theme', 10, 2 );
