<?php

function sunflower_block_enqueue_backend() {
	// $asset_file = include get_template_directory() . '/build/example-static/index.asset.php';

	// wp_enqueue_script(
	// 	'sunflower-example-static',
	// 	get_template_directory_uri() . '/build/example-static/index.js',
	// 	$asset_file['dependencies'],
	// 	$asset_file['version']
	// );

	// $asset_file = include get_template_directory() . '/build/latest-posts/latest-posts.asset.php';

	// wp_enqueue_script(
	// 	'sunflower-latest-posts',
	// 	get_template_directory_uri() . '/build/latest-posts/latest-posts.js',
	// 	$asset_file['dependencies'],
	// 	$asset_file['version']
	// );

}

// /**
//  * Load all translations for our plugin from the MO file.
//  */
// function basic_block_3df23d___load_textdomain() {
// 	load_plugin_textdomain( 'sunflower', false, get_template_directory() .  '/languages' );
// }
// add_action( 'init', 'basic_block_3df23d___load_textdomain' );

function sunflower_block_enqueue() {
	// register_block_type(
	// 	'sunflower/next-events',
	// 	array(
	// 		'apiVersion'      => 2,
	// 		'editor_script'   => 'sunflower-blocks',
	// 		'render_callback' => 'sunflower_next_events_render',
	// 	)
	// );

    // register_block_type(
    //     get_template_directory() . '/build/example-static'
    // );


    wp_register_script(
        'latest-posts',
        get_template_directory() . '/build/latest-posts/',
        array( 'wp-blocks', 'react', 'wp-i18n', 'wp-block-editor' )
    );

    // register_block_type( get_template_directory() .  '/build' );

    register_block_type(
        get_template_directory() . '/build/latest-posts'
    );

            // // First, unload textdomain - Based on https://core.trac.wordpress.org/ticket/34213#comment:26
            // unload_textdomain('sunflower');

            // // Call the core translations from plugins languages/ folder
            // if (file_exists(get_template_directory() .  '/languages/' . get_locale() . '.mo')) {
            //     load_textdomain(
            //         'sunflower',
            //         get_template_directory() .  '/languages/' . get_locale() . '.mo'
            //     );
            // }

            // if (function_exists('wp_set_script_translations')) {
            //     wp_set_script_translations(
            //         'latest-posts',
            //         'sunflower',
            //         get_template_directory() .  '/languages'
            //     );
            // }




    // $hallo = get_template_directory() . '/languages';
    // wp_set_script_translations( 'latest-posts', 'sunflower', get_template_directory() . '/languages' );
    wp_set_script_translations( 'latest-posts', 'sunflower' );

	// register_block_type(
    //     get_template_directory() . '/build/latest-posts'
    //     // __DIR__ . './../build/latest-posts'
    //     // ,
    //     // array(
    //     //     'render_callback' => 'sunflower_latest_posts_render',
    //     // )
	// );

    // $DA = __DIR__ . './../build/latest-posts';

    // register_block_type( __DIR__ . './build' );


}
add_action( 'init', 'sunflower_block_enqueue' );

function sunflower_block_enqueue_test() {


    // Register the block by passing the location of block.json to register_block_type.
    // register_block_type( get_template_directory() . '/build' );


    wp_register_script(
        'latest-test',
        get_template_directory() . '/build/latest-test/',
        array( 'wp-blocks', 'react', 'wp-i18n', 'wp-block-editor' )
    );

    register_block_type( get_template_directory() .  '/build/latest-test' );

    // register_block_type(
    //     get_stylesheet_directory() . '/build/latest-test'
    // );

            // // First, unload textdomain - Based on https://core.trac.wordpress.org/ticket/34213#comment:26
            // unload_textdomain('sunflower');

            // // Call the core translations from plugins languages/ folder
            // if (file_exists(get_template_directory() .  '/languages/' . get_locale() . '.mo')) {
            //     load_textdomain(
            //         'sunflower',
            //         get_template_directory() .  '/languages/' . get_locale() . '.mo'
            //     );
            // }

            // if (function_exists('wp_set_script_translations')) {
            //     wp_set_script_translations(
            //         'latest-posts',
            //         'sunflower',
            //         get_template_directory() .  '/languages'
            //     );
            // }

    // $hallo = get_template_directory() . '/languages';
    wp_set_script_translations( 'latest-test', 'sunflower' );
    // wp_set_script_translations( 'latest-test', 'sunflower' );

	// register_block_type(
    //     get_template_directory() . '/build/latest-posts'
    //     // __DIR__ . './../build/latest-posts'
    //     // ,
    //     // array(
    //     //     'render_callback' => 'sunflower_latest_posts_render',
    //     // )
	// );

    // $DA = __DIR__ . './../build/latest-posts';

    // register_block_type( __DIR__ . './build' );


}
add_action( 'init', 'sunflower_block_enqueue_test' );
// add_action("after_setup_theme", function () {
//     load_theme_textdomain( 'sunflower', get_stylesheet_directory() . '/languages' );
// });
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
