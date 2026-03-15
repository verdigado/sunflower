<?php
/**
 * Show related posts for given categories.
 *
 * @package Sunflower 26
 */

/**
 * Find related posts.
 *
 * @param int   $exclude Exclude posts with the given id.
 * @param array $cats Categories to find posts with.
 */
function sunflower_related_posts( $exclude = false, $cats = false ) {
	$query = new WP_Query(
		array(
			'post_type'      => 'post',
			'posts_per_page' => 2,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'post__not_in'   => array( $exclude ),
			'category__in'   => (array) $cats,
		)
	);

	if ( ! $query->have_posts() ) {
		$query = new WP_Query(
			array(
				'post_type'      => 'post',
				'posts_per_page' => 2,
				'orderby'        => 'date',
				'order'          => 'DESC',
				'post__not_in'   => array( $exclude ),
			)
		);
	}

	return $query;
}

/**
 * Find latest posts (for event single pages).
 *
 * @param int $count Number of posts to return.
 */
function sunflower_latest_posts( $count = 2 ) {
	return new WP_Query(
		array(
			'post_type'      => 'post',
			'posts_per_page' => $count,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
}
