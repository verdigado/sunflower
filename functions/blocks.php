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

}
add_action( 'init', 'sunflower_block_enqueue' );

function sunflower_next_events_render( $block_attributes, $content ) {
    $next_events = sunflower_get_next_events( 3 );

    $return = sprintf(' <div class="wp-block-group has-background next-events">
                        <div class="wp-block-group__inner-container">
                            <h2 class="text-center">%s</h2>
                            <div class="wp-block-button text-center mb-4"><a class="wp-block-button__link no-border-radius" href="%s?post_type=sunflower_event" rel="">%s</a></div>
                        <div class="row">',
                        __('Next events', 'sunflower'),
                        home_url(),
                        __('all events', 'sunflower')
    );

    ob_start();
    while ( $next_events->have_posts() ) {
        $next_events->the_post();

        echo '<div class="col-12 col-md-6 col-lg-4">';
            get_template_part( 'template-parts/archive', 'event');
        echo '</div>';

    }
  
           
    $return .= ob_get_contents();
    ob_end_clean();

    $return .= '</div></div></div>';

    return $return;
}

function sunflower_latest_posts_render( $block_attributes, $content ) {

    $posts = new WP_Query(array(
        'post_type'     => 'post',
        'posts_per_page'=> 4,
        'order'        => 'DESC'
    ));

   
    $return = sprintf(' <div class="wp-block-group has-background latest-posts">
                        <div class="wp-block-group__inner-container">
                            <h2 class="text-center">%s</h2>
                        <div class="row">',
                        __('News', 'sunflower')
    );

    ob_start();

    while ( $posts->have_posts() ) {
        $posts->the_post();
            echo '<div class="col-12 col-md-6">';
                get_template_part( 'template-parts/content', 'archive');
            echo '</div>';
    }  
     
           
    $return .= ob_get_contents();
    ob_end_clean();


    $return .= sprintf('<div class="col-12"></div><div class="col-12 col-md-6 offset-md-6 bg-primary p-5 d-flex justify-content-between">
           <span class="text-white h2">%s</span>
            <a class="btn btn-outline-light text-white" href="%s?post_type=post" rel="">%s</a>
            </div>
         ',
        __('more posts', 'sunflower'), 
        home_url(),
        __('to archive', 'sunflower')
);

    $return .= '</div></div></div>';

    return $return;
}

function sunflower_block_category( $categories, $post ) {
	return array_merge(
		array(
			array(
				'slug'  => 'sunflower-blocks',
				'title' => __( 'Sunflower', 'sunflower' ),
			),
		),
        $categories
	);
}
add_filter( 'block_categories', 'sunflower_block_category', 10, 2);