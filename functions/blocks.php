<?php

function sunflower_block_enqueue_backend() {
	$asset_file = include get_template_directory() . '/build/index.asset.php';

	wp_enqueue_script(
		'sunflower-blocks',
		get_template_directory_uri() . '/build/index.js',
		$asset_file['dependencies'],
		$asset_file['version']
	);
}

function sunflower_block_enqueue() {
	register_block_type(
		'sunflower/next-events',
		array(
			'apiVersion'      => 2,
			'editor_script'   => 'sunflower-blocks',
			'render_callback' => 'sunflower_next_events_render',
		)
	);

	register_block_type(
		'sunflower/latest-posts',
		array(
			'apiVersion'      => 2,
			'editor_script'   => 'sunflower-blocks',
			'render_callback' => 'sunflower_latest_posts_render',
		)
	);

}
add_action( 'init', 'sunflower_block_enqueue' );
add_action( 'admin_enqueue_scripts', 'sunflower_block_enqueue_backend' );




function sunflower_block_category( $categories, $post ) {
	return array_merge(
		$categories,
		array(
			array(
				'slug'  => 'sunflower-blocks',
				'title' => __( 'Sunflower', 'sunflower' ),
			),
		)
	);
}

if ( version_compare( $wp_version, '5.8', '>=' ) ) {
	add_filter( 'block_categories_all', 'sunflower_block_category', 10, 2 );
} else {
	add_filter( 'block_categories', 'sunflower_block_category', 10, 2 );
}
