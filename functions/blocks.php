<?php

function sunflower_block_enqueue_backend() {
	// $asset_file = include get_template_directory() . '/build/example-static/index.asset.php';

	// wp_enqueue_script(
	// 	'sunflower-example-static',
	// 	get_template_directory_uri() . '/build/example-static/index.js',
	// 	$asset_file['dependencies'],
	// 	$asset_file['version']
	// );

	$asset_file = include get_template_directory() . '/build/latest-posts/latest-posts.asset.php';

	wp_enqueue_script(
		'sunflower-latest-posts',
		get_template_directory_uri() . '/build/latest-posts/latest-posts.js',
		$asset_file['dependencies'],
		$asset_file['version']
	);

}

function sunflower_block_enqueue() {
	// register_block_type(
	// 	'sunflower/next-events',
	// 	array(
	// 		'apiVersion'      => 2,
	// 		'editor_script'   => 'sunflower-blocks',
	// 		'render_callback' => 'sunflower_next_events_render',
	// 	)
	// );

    register_block_type(
        get_template_directory() . '/build/example-static'
    );
	register_block_type(
        get_template_directory() . '/build/latest-posts'
        // __DIR__ . './../build/latest-posts'
        // ,
        // array(
        //     'render_callback' => 'sunflower_latest_posts_render',
        // )
	);

    // $DA = __DIR__ . './../build/latest-posts';

    // register_block_type( __DIR__ . './build' );


}
add_action( 'init', 'sunflower_block_enqueue' );
// add_action( 'enqueue_block_editor_assets', 'sunflower_block_enqueue_backend' );

// add_action( 'admin_enqueue_scripts', 'sunflower_block_enqueue_backend' );


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
