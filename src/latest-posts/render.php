<?php

$classnames = array();
$classnames[] = 'has-background';
$classnames[] = 'latest-posts';

$is_grid = false;
if ( isset( $attributes['blockLayout'] ) && 'grid' === $attributes['blockLayout'] ) {
    $is_grid = true;
}

$wp_query_args = array(
    'post_type' => 'post',
    'order'     => 'DESC',
);

$url_category_name = '';
$link              = false;

if ( isset( $attributes['categories'] ) and $attributes['categories'] != '' ) {
    $wp_query_args['category_name'] = $attributes['categories'];
    $url_category_name              = '&category_name=' . $attributes['categories'];

    $categories = explode( ',', $attributes['categories'] );
    $link       = get_category_link( get_category_by_slug( trim( $categories[0] ) ) );
}

if ( ! $link or $link == '' ) {
    if ( $page_for_posts = get_option( 'page_for_posts' ) ) {
        $link = get_permalink( $page_for_posts );
    } else {
        if ( 'page' === get_option( 'show_on_front' ) ) {
            $link = home_url() . '?post_type=post';

        } else {
            $link = home_url();
        }
    }
}

// set maximum amount of posts per page
if ( isset( $attributes['count'] ) and $attributes['count'] != '' ) {
    $wp_query_args['posts_per_page'] = (int) $attributes['count'];
} else {
    $wp_query_args['posts_per_page'] = 6;
}

// fetch posts for given parameters
$posts = new WP_Query( $wp_query_args );

if ( isset( $attributes['title'] ) ) {
    $title = sprintf( '<h2 class="text-center h1">%s</h2>', $attributes['title'] );
} else {
    $title = '';
}

$classes = get_block_wrapper_attributes( array( 'class' => implode( ' ', $classnames ) ) );


$list_items = sprintf(
    '<div %s>
        <div class="wp-block-group__inner-container">
            %s
                <div class="row" data-masonry=\'{"percentPosition": true }\' >',
    $classes,
    $title
);

$columns = array( '', '' );
$i       = 0;
if ($is_grid == true) {
    $cssCol = 'col-md-6';
} else {
    $cssCol = 'col-12';
}

while ( $posts->have_posts() ) {
    $posts->the_post();
    ob_start();
    get_template_part( 'template-parts/content', 'archive' );

    $article        = ob_get_clean();

    $list_items .= sprintf(
        '<div class="%1$s">%2$s</div>',
        $cssCol,
        $article
    );
}

if ( $posts->post_count == 0 ) {
    $list_items = sprintf( '<div class="col-12 text-center pb-4">%s</div><div class="col-12">', __( 'No posts found', 'sunflower' ) );
}

$list_items .= sprintf(
    '
    <a class="text-white no-link d-block bg-primary has-green-550-hover-background-color border-radius" href="%1$s" rel="">
        <div class="p-45 row ">
        <span class="continue-reading text-white text-center pt-0">%2$s</span>
        </div>
    </a>
',
    $link,
    ($attributes['archiveText'] ?? '') ?:  __( 'to archive', 'sunflower-latest-posts' )
);

$list_items .= '</div></div></div></div>';

echo $list_items;
