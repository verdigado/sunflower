<?php
/**
 * Comment related functions.
 *
 * @package sunflower
 */

/**
 * Close the right column of the comments form.
 *
 * @param array $fields The array of comments from fields.
 */
function sunflower_rearrange_comment_fields( $fields ) {
	$fields['closer'] = '</div>';

	return $fields;
}

add_filter( 'comment_form_fields', 'sunflower_rearrange_comment_fields' );
