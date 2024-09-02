<?php
/**
 * Render the Sunflower latest posts block.
 *
 * @package sunflower
 */

$sunflower_classnames   = array();
$sunflower_classnames[] = 'has-background';
$sunflower_classnames[] = 'latest-posts';

$sunflower_columns = 1;
if ( isset( $attributes['blockLayout'] ) && 'grid' === $attributes['blockLayout'] ) {
	if ( isset( $attributes['columns'] ) ) {
		$sunflower_columns = $attributes['columns'];
	}
}

$sunflower_count = isset( $attributes['count'] ) ? (int) $attributes['count'] : 6;

$sunflower_link = false;

$sunflower_categories = array();
if ( isset( $attributes['categories'] ) && ! empty( $attributes['categories'] ) ) {
	$sunflower_categories = $attributes['categories'];
	$sunflower_link       = get_category_link( get_category_by_slug( trim( (string) $sunflower_categories[0] ) ) );
}

$sunflower_excluded_categories = array();
if ( isset( $attributes['excludedCategories'] ) && ! empty( $attributes['excludedCategories'] ) ) {
	$sunflower_excluded_categories = $attributes['excludedCategories'];
}

if ( ! $sunflower_link || '' === $sunflower_link ) {
	$sunflower_page_for_posts = get_option( 'page_for_posts' );
	if ( $sunflower_page_for_posts ) {
		$sunflower_link = get_permalink( $sunflower_page_for_posts );
	} elseif ( 'page' === get_option( 'show_on_front' ) ) {
		$sunflower_link = home_url() . '?post_type=post';
	} else {
		$sunflower_link = home_url();
	}
}

// Fetch posts for given parameters.
$sunflower_posts = sunflower_get_latest_posts( $sunflower_count, $sunflower_categories, $sunflower_excluded_categories );

$sunflower_title = isset( $attributes['title'] ) ? sprintf( '<h2 class="text-center h1">%s</h2>', $attributes['title'] ) : '';

$sunflower_classes = get_block_wrapper_attributes(
	array(
		'class' => implode( ' ', $sunflower_classnames ),
	)
);

$sunflower_list_items = sprintf(
	'<div %s>
        <div class="wp-block-group__inner-container">
            %s
                <div class="row" data-masonry=\'{"percentPosition": true }\' >',
	$sunflower_classes,
	$sunflower_title
);

switch ( $sunflower_columns ) {
	case 1:
		$sunflower_css_col = 'col-12';
		break;
	case 2:
		$sunflower_css_col = 'col-md-6';
		break;
	case 3:
		$sunflower_css_col = 'col-md-4';
		break;
}

while ( $sunflower_posts->have_posts() ) {
	$sunflower_posts->the_post();
	ob_start();
	get_template_part( 'template-parts/content', 'archive' );

	$sunflower_article = ob_get_clean();

	$sunflower_list_items .= sprintf(
		'<div class="%1$s">%2$s</div>',
		$sunflower_css_col,
		$sunflower_article
	);
}

if ( 0 === $sunflower_posts->post_count ) {
	$sunflower_list_items = sprintf( '<div class="col-12 text-center pb-4">%s</div><div class="col-12">', __( 'No posts found', 'sunflower' ) );
}

$sunflower_list_items .= sprintf(
	'
    <a class="text-white no-link d-block bg-primary has-green-550-hover-background-color border-radius" href="%1$s" rel="">
        <div class="p-45 row">
        <span class="continue-reading text-white text-center pt-0">%2$s</span>
        </div>
    </a>
',
	$sunflower_link,
	( $attributes['archiveText'] ?? '' ) ? ( $attributes['archiveText'] ?? '' ) : __( 'to archive', 'sunflower-latest-posts' )
);

$sunflower_list_items .= '</div></div></div>';

echo wp_kses(
	$sunflower_list_items,
	array_merge(
		wp_kses_allowed_html( 'post' ),
		array(
			'time' => array(
				'class'    => true,
				'datetime' => true,
			),
		)
	)
);
