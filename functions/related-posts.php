<?php

function sunflower_related_posts( $exclude = false, $cats = false ) {
	return new WP_Query(
		array(
			'post_type'      => 'post',
			'posts_per_page' => 2,
			'order'          => 'DESC',
			'post__not_in'   => array( $exclude ),
			'cat'            => $cats,
		)
	);
}
