<?php
/**
 * Get latest posts for sunflower.
 *
 * @package sunflower
 */

/**
 * Get the latest posts for given tag ids.
 *
 * @param int                 $number The amount of posts to fetch. -1 for all.
 * @param null|int[]|string[] $tag_ids Array of sunflower_event_tag IDs.
 *
 * @return WP_Query
 */
function sunflower_get_latest_posts( $number = -1, $tag_ids = null ) {
	$tax_query = null;

	if ( $tag_ids ) {
		if ( isIntArray( $tag_ids ) ) {
			$tax_query = array(
				array(
					'taxonomy' => 'category',
					'field'    => 'id',
					'terms'    => $tag_ids,
				),
			);
		} else {
			$tax_query = array(
				array(
					'taxonomy' => 'category',
					'field'    => 'slug',
					'terms'    => $tag_ids,
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
