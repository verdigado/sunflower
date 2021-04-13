<?php

function sunflower_latest_posts_render( $block_attributes, $content ) {
    $wp_query_args = array(
        'post_type'         => 'post',
        'order'             => 'DESC',
    );

    $url_category_name = '';
    
    if(isset($block_attributes['categories']) AND $block_attributes['categories'] != '' ){
        $wp_query_args['category_name'] = $block_attributes['categories'];
        $url_category_name = '&category_name=' . $block_attributes['categories'];
    }

    if(isset($block_attributes['count']) AND $block_attributes['count'] != '' ){
        $wp_query_args['posts_per_page'] = (int) $block_attributes['count'];
    }else{
        $wp_query_args['posts_per_page'] = 4;
    }

    if(isset($block_attributes['title']) ){
        $title = sprintf('<h2 class="text-center h1">%s</h2>', $block_attributes['title']);
    }else{
        $title = '';
    }

    $posts = new WP_Query($wp_query_args);
    
    $classes = (isset($block_attributes['className'])) ? $block_attributes['className'] : '';

    $return = sprintf(' <div class="wp-block-group has-background latest-posts %s">
                        <div class="wp-block-group__inner-container">
                            %s
                        <div class="row">',
                        $classes,
                        $title
    );


    ob_start();
    $i = 1;
    echo '<div class="col-12 col-md-6">';
    while ( $posts->have_posts() ) {
        $posts->the_post();
            get_template_part( 'template-parts/content', 'archive');
            if($i == ceil( $posts->post_count / 2)){
                echo '</div><div class="col-12 col-md-6">';
            }
            $i++;
    }  
    
    $return .= ob_get_contents();
    ob_end_clean();

    if( $posts->post_count == 0 ){
        $return .= sprintf('%s</div><div class="col-12">', __('No posts found', 'sunflower'));
    }
    
    $return .= sprintf('
            <a class="text-white no-link d-block bg-primary has-green-550-hover-background-color border-radius" href="%1$s?post_type=post%3$s" rel="">
                <div class="p-45 row ">
                <span class="continue-reading text-white text-center pt-0">%2$s</span>
                </div>
            </a>
           
        ',
        home_url(),
        __('to archive', 'sunflower'),
        $url_category_name
    );

    $return .= '</div></div></div></div>';

    return $return;
}