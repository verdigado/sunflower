<?php
/**
 * @param int        $number
 * @param null|int[]|string[] $tagIds Array of sunflower_event_tag IDs
 *
 * @return WP_Query
 */
function sunflower_get_latest_posts($number = -1, $tagIds = null)
{
    $taxQuery = null;

    if ($tagIds) {
        if (isIntArray($tagIds)) {
            $taxQuery = [[
                'taxonomy' => 'category',
                'field' => 'id',
                'terms' => $tagIds,
            ]];
        } else {
            $taxQuery = [[
                'taxonomy' => 'category',
                'field' => 'slug',
                'terms' => $tagIds,
            ]];
        }
    }

    return new WP_Query(
        [
            'post_type' => 'post',
            'posts_per_page' => $number,
            'tax_query' => $taxQuery,
            'order' => 'DESC',
        ]
    );

}
