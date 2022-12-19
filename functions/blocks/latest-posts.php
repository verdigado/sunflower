<?php

function sunflower_latest_posts_render( $block_attributes, $content ) {
	 $wp_query_args = array(
		 'post_type' => 'post',
		 'order'     => 'DESC',
	 );

	 $url_category_name = '';
	 $link              = false;

	 if ( isset( $block_attributes['categories'] ) and $block_attributes['categories'] != '' ) {
		 $wp_query_args['category_name'] = $block_attributes['categories'];
		 $url_category_name              = '&category_name=' . $block_attributes['categories'];

		 $categories = explode( ',', $block_attributes['categories'] );
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

	 if ( isset( $block_attributes['count'] ) and $block_attributes['count'] != '' ) {
		 $wp_query_args['posts_per_page'] = (int) $block_attributes['count'];
	 } else {
		 $wp_query_args['posts_per_page'] = 4;
	 }

	 if ( isset( $block_attributes['title'] ) ) {
		 $title = sprintf( '<h2 class="text-center h1">%s</h2>', $block_attributes['title'] );
	 } else {
		 $title = '';
	 }

		$posts = new WP_Query( $wp_query_args );

		$classes = ( isset( $block_attributes['className'] ) ) ? $block_attributes['className'] : '';

		$return = sprintf(
			' <div class="wp-block-group has-background latest-posts %s">
                        <div class="wp-block-group__inner-container">
                            %s
                        <div class="row">',
			$classes,
			$title
		);

		$columns = array( '', '' );
		$i       = 0;
	while ( $posts->have_posts() ) {
		$posts->the_post();
		ob_start();
		get_template_part( 'template-parts/content', 'archive' );

		$article        = ob_get_clean();
		$columns[ $i ] .= $article;

		// add articles to the first columns as well, but hide them on big screens
		if ( $i === 1 ) {
			$columns[0] .= sprintf( '<div class="d-md-none">%s</div>', $article );
		}

		$i = ( $i + 1 ) % 2;
	}

	if ( $posts->post_count > 0 ) {
		$button = sprintf(
			'
                    <a class="text-white no-link d-block d-md-none bg-primary has-green-550-hover-background-color border-radius" href="%1$s" rel="">
                        <div class="p-45 row ">
                        <span class="continue-reading text-white text-center pt-0">%2$s</span>
                        </div>
                    </a>
                ',
			$link,
			__( 'to archive', 'sunflower' )
		);

		$return .= sprintf(
			'<div class="col-12 col-md-6">%s%s</div><div class="d-none d-md-block col-md-6">%s',
			$columns[0],
			$button,
			$columns[1]
		);
	} else {
		$return .= sprintf( '<div class="col-12 text-center pb-4">%s</div><div class="col-12">', __( 'No posts found', 'sunflower' ) );
	}

		$return .= sprintf(
			'
            <a class="text-white no-link d-block bg-primary has-green-550-hover-background-color border-radius" href="%1$s" rel="">
                <div class="p-45 row ">
                <span class="continue-reading text-white text-center pt-0">%2$s</span>
                </div>
            </a>
        ',
			$link,
			__( 'to archive', 'sunflower' )
		);

		$return .= '</div></div></div></div>';

		return $return;
}
