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
	sunflower_import_widgets();
	sunflower_import_events();
}

add_action( 'after_switch_theme', 'sunflower_activate_theme', 10, 2 );
