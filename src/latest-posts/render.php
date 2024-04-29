<?php

$classnames   = array();
$classnames[] = 'has-background';
$classnames[] = 'latest-posts';

$is_grid = false;
if ( isset( $attributes['blockLayout'] ) && 'grid' === $attributes['blockLayout'] ) {
	$is_grid = true;
}

$count = isset( $attributes['count'] ) ? (int) $attributes['count'] : 6;

$url_category_name = '';
$link              = false;

$categories = array();
if ( isset( $attributes['categories'] ) && ! empty( $attributes['categories'] ) ) {
	$categories = $attributes['categories'];
	$link       = get_category_link( get_category_by_slug( trim( (string) $categories[0] ) ) );
}

if ( ! $link || $link == '' ) {
	if ( $page_for_posts = get_option( 'page_for_posts' ) ) {
		$link = get_permalink( $page_for_posts );
	} elseif ( 'page' === get_option( 'show_on_front' ) ) {
		$link = home_url() . '?post_type=post';
	} else {
		$link = home_url();
	}
}

// fetch posts for given parameters
$posts = sunflower_get_latest_posts( $count, $categories );

$title = isset( $attributes['title'] ) ? sprintf( '<h2 class="text-center h1">%s</h2>', $attributes['title'] ) : '';

$classes = get_block_wrapper_attributes(
	array(
		'class' => implode( ' ', $classnames ),
	)
);

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
$cssCol  = $is_grid == true ? 'col-md-6' : 'col-12';

while ( $posts->have_posts() ) {
	$posts->the_post();
	ob_start();
	get_template_part( 'template-parts/content', 'archive' );

	$article = ob_get_clean();

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
	( $attributes['archiveText'] ?? '' ) ?: __( 'to archive', 'sunflower-latest-posts' )
);

$list_items .= '</div></div></div></div>';

echo $list_items;
