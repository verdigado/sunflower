<?php
/**
 * Get latest posts for sunflower.
 *
 * @package sunflower
 */

/**
 * Get the latest posts for given category ids.
 *
 * @param int                 $number The amount of posts to fetch. -1 for all.
 * @param null|int[]|string[] $category_ids Array of category IDs.
 * @param null|string[]       $exclude_ids Array of category IDs to exclude posts.
 *
 * @return WP_Query
 */
function sunflower_get_latest_posts( $number = -1, $category_ids = null, $exclude_ids = null ) {
	$tax_query = null;

	if ( $category_ids || $exclude_ids ) {
		if ( sunflower_is_numeric_array( $category_ids ) ) {
			$tax_query = array(
				array(
					'taxonomy' => 'category',
					'field'    => 'id',
					'terms'    => $category_ids,
				),
				array(
					'taxonomy' => 'category',
					'field'    => 'slug',
					'operator' => 'NOT IN',
					'terms'    => $exclude_ids,
				),
			);
		} else {
			$tax_query = array(
				array(
					'taxonomy' => 'category',
					'field'    => 'slug',
					'terms'    => $category_ids,
				),
				array(
					'taxonomy' => 'category',
					'field'    => 'slug',
					'operator' => 'NOT IN',
					'terms'    => $exclude_ids,
				),
			);
		}
	}

	return new WP_Query(
		array(
			'post_type'      => 'post',
			'posts_per_page' => $number,
			'tax_query'      => $tax_query,
			'order'          => 'DESC',
		)
	);
}
