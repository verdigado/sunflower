<?php

function sunflower_parent_or_child( $path ) {

	$local_file = get_stylesheet_directory() . '/' . $path;
	if ( file_exists( $local_file ) ) {
		return get_stylesheet_directory_uri() . '/' . $path;
	}

	return get_template_directory_uri() . '/' . $path;
}
