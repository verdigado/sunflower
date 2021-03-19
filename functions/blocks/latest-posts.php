<?php

function sunflower_latest_posts_render( $block_attributes, $content ) {

$posts_per_page = 4; 
$posts = new WP_Query(array(
    'post_type'     => 'post',
    'posts_per_page'=> $posts_per_page,
    'order'        => 'DESC'
));


$return = sprintf(' <div class="wp-block-group has-background latest-posts">
                    <div class="wp-block-group__inner-container">
                        <h2 class="text-center h1">%s</h2>
                    <div class="row">',
                    __('News', 'sunflower')
);


ob_start();
$i = 1;
echo '<div class="col-12 col-md-6">';
while ( $posts->have_posts() ) {
    $posts->the_post();
        get_template_part( 'template-parts/content', 'archive');
        if($i == floor( $posts_per_page / 2)){
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