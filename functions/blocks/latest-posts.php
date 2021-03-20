<?php

function sunflower_latest_posts_render( $block_attributes, $content ) {
    $wp_query_args = array(
        'post_type'         => 'post',
        'order'             => 'DESC',
    );

    if(isset($block_attributes['categories']) AND $block_attributes['categories'] != '' ){
        $wp_query_args['category_name'] = $block_attributes['categories'];
    }

    if(isset($block_attributes['count']) AND $block_attributes['count'] != '' ){
        $wp_query_args['posts_per_page'] = (int) $block_attributes['count'];
    }else{
        $wp_query_args['posts_per_page'] = 4;
    }

    if(isset($block_attributes['title']) ){
        $title = $block_attributes['title'];
    }else{
        $title =  __('News', 'sunflower');
    }

    $posts = new WP_Query($wp_query_args);

    $return = sprintf(' <div class="wp-block-group has-background latest-posts">
                        <div class="wp-block-group__inner-container">
                            <h2 class="text-center h1">%s</h2>
                        <div class="row">',
                        $title
    );


    ob_start();
    $i = 1;
    echo '<div class="col-12 col-md-6">';
    while ( $posts->have_posts() ) {
        $posts->the_post();
            get_template_part( 'template-parts/content', 'archive');
            if($i == floor( $posts->post_count / 2)){
                echo '</div><div class="col-12 col-md-6">';
            }
            $i++;
    }  
    echo '</div>';
    
        
    $return .= ob_get_contents();
    ob_end_clean();


    $return .= sprintf('<div class="col-12 col-md-6 offset-md-6">
            <a class="text-white no-link d-block bg-primary hover-bg-green-550 border-radius" href="%2$s?post_type=post" rel="">
                <div class="p-45 row ">
                <span class="h2 col-12 col-md-6">%1$s</span>
                <span class="continue-reading text-white col-12 col-md-6">%3$s</span>
                </div>
            </a>
            </div>
        ',
        __('more posts', 'sunflower'), 
        home_url(),
        __('to archive', 'sunflower')
    );

    $return .= '</div></div></div>';

    return $return;
}