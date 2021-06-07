<?php

function sunflower_block_enqueue() {
    $asset_file = include( get_template_directory() . '/build/index.asset.php');

    wp_enqueue_script(
        'sunflower-blocks',
        get_template_directory_uri() .'/build/index.js',
        $asset_file['dependencies'],
        $asset_file['version']
    );

    register_block_type( 'sunflower/next-events', array(
        'apiVersion' => 2,
        'editor_script' => 'sunflower-blocks',
        'render_callback' => 'sunflower_next_events_render'
    ) );

    register_block_type( 'sunflower/latest-posts', array(
        'apiVersion' => 2,
        'editor_script' => 'sunflower-blocks',
        'render_callback' => 'sunflower_latest_posts_render'
    ) );

    wp_register_script('fullcalendar-main', get_template_directory_uri() . '/assets/vndr/fullcalendar/main.js');
    wp_register_script('fullcalendar-locale', get_template_directory_uri() . '/assets/vndr/fullcalendar/locales/de.js', array('fullcalendar-main'));
    wp_register_style('fullcalendar', get_template_directory_uri() . '/assets/vndr/fullcalendar/main.css');

    register_block_type('sunflower/events-calendar', array(
        'api_version' => 2,
		'editor_script' => 'sunflower-blocks',
        'script' => 'fullcalendar-locale',
        'style' => 'fullcalendar',
        'render_callback' => 'sunflower_events_calendar_render'
	) );

}
add_action( 'init', 'sunflower_block_enqueue' );


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
add_filter( 'block_categories', 'sunflower_block_category', 10, 2);
