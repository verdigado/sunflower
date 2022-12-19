<?php

add_theme_support( 'custom-logo' );

function the_sunflower_theme() {
	echo defined( 'SUNFLOWER_THEME' ) ? 'theme--' . SUNFLOWER_THEME : 'theme--default';
}
