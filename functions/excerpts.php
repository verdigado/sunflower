<?php

function sunflower_excerpt_length( $length ) {
	return sunflower_get_setting( 'excerpt_length' ) ?: 30;
}

add_filter( 'excerpt_length', 'sunflower_excerpt_length', 900 );

function modify_read_more_link() {
	return '…';
}

add_filter( 'excerpt_more', 'modify_read_more_link', 910 );

function sunflower_excerpt( $excerpt ) {
	return preg_replace( '/\–(.*?)$/', '$1', (string) $excerpt );
}

add_filter( 'get_the_excerpt', 'sunflower_excerpt', 920 );
