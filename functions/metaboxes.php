<?php
/**
 * Methods for the meta box block.
 *
 * @package sunflower
 */

/**
 * Meta data boxes in the backend editor.
 */
function sunflower_add_meta_boxes() {
	// see https://developer.wordpress.org/reference/functions/add_meta_box for a full explanation of each property.
	add_meta_box(
		'sunflower_meta_box_layout', // div id containing rendered fields.
		__( 'Layout', 'sunflower' ), // section heading displayed as text.
		'sunflower_meta_box_layout', // callback function to render fields.
		array( 'post', 'page', 'event' ), // name of post type on which to render fields.
		'side', // location on the screen.
		'high' // placement priority.
	);

	add_meta_box(
		'sunflower_meta_box_metadata',
		__( 'Metadata', 'sunflower' ),
		'sunflower_meta_box_metadata',
		array( 'post', 'page' ),
		'side',
		'high'
	);
}

add_action( 'admin_init', 'sunflower_add_meta_boxes' );

/**
 * Save meta boxes data.
 */
function sunflower_save_meta_boxes() {
	global $post;

	if ( ! isset( $post->ID ) || empty( $_POST ) ) {
		return;
	}

	// Do not save, if nonce is invalid.
	if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-post_' . $post->ID ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( get_post_status( $post->ID ) === 'auto-draft' ) {
		return;
	}

	update_post_meta( $post->ID, '_sunflower_styled_layout', sanitize_text_field( $_POST['_sunflower_styled_layout'] ?? '' ) );
	update_post_meta( $post->ID, '_sunflower_hide_feature_image', sanitize_text_field( $_POST['_sunflower_hide_feature_image'] ?? '' ) );
	update_post_meta( $post->ID, '_sunflower_roofline', sanitize_text_field( $_POST['_sunflower_roofline'] ?? '' ) );
	update_post_meta( $post->ID, '_sunflower_metadata', $_POST['sunflower-meta-data'] ?? '' );
}

add_action( 'save_post', 'sunflower_save_meta_boxes' );

/**
 * Get the rendered metabox for backend.
 */
function sunflower_meta_box_layout() {
	global $post;
	$custom = get_post_custom( $post->ID );

	if ( isset( $custom['_sunflower_styled_layout'][0] ) ) {
		$checked = ( $custom['_sunflower_styled_layout'][0] ) ? 'checked' : '';
	} else {
		$checked = '';
	}

	printf(
		'
    <div class="components-panel__row">
        <div class="components-base-control__field">
            <span class="components-checkbox-control__input-container">
                <input name="_sunflower_styled_layout" id="_sunflower_styled_layout" class="" type="checkbox" value="1" %s>
            </span>
            <label class="components-checkbox-control__label" for="_sunflower_styled_layout">%s</label>
            <div><small>%s</small></div>
        </div>
    </div>',
		esc_attr( $checked ),
		esc_attr__( 'Styled layout', 'sunflower' ),
		esc_attr__( 'do not show title, add and remove margins. e.g. for the homepage', 'sunflower' )
	);

	if ( isset( $custom['_sunflower_hide_feature_image'][0] ) ) {
		$checked = ( $custom['_sunflower_hide_feature_image'][0] ) ? 'checked' : '';
	} else {
		$checked = '';
	}

	printf(
		'
    <div class="components-panel__row">
        <div class="components-base-control__field">
            <span class="components-checkbox-control__input-container">
                <input name="_sunflower_hide_feature_image" id="_sunflower_hide_feature_image" class="" type="checkbox" value="1" %s>
            </span>
            <label class="components-checkbox-control__label" for="_sunflower_hide_feature_image">%s</label>
            <div><small>%s</small></div>
        </div>
    </div>',
		esc_attr( $checked ),
		esc_attr__( 'Hide feature image on single', 'sunflower' ),
		esc_attr__( 'Hide feature image on single view, but show it on lists.', 'sunflower' )
	);
}

/**
 * Add the styled_layout class in backend editor if set.
 *
 * @param string $classes The editor body classes.
 * @return string The modified classes
 */
function sunflower_admin_classes_layout( $classes ) {

	global $post;

	if ( $post ) {
		$sunflower_styled_layout = get_post_meta( $post->ID, '_sunflower_styled_layout', true ) ? 'styled-layout' : '';
		$classes                .= ' ' . $sunflower_styled_layout;
	}

	return trim( $classes );
}
add_filter( 'admin_body_class', 'sunflower_admin_classes_layout' );

/**
 * Get the rendered metabox for backend.
 */
function sunflower_meta_box_metadata() {
	global $post;
	$custom = get_post_custom( $post->ID );

	echo '<div class="">';
	printf( '<h3>%s</h3>', esc_attr__( 'Roofline', 'sunflower' ) );

	echo '<div><input name="_sunflower_roofline" value="' . ( esc_attr( $custom['_sunflower_roofline'][0] ?? '' ) ) . '" class="components-text-control__input">';
	echo '</div></div>';

	echo '<div class="">';
	printf( '<h3>%s</h3>', esc_attr__( 'Metadata', 'sunflower' ) );
	printf( '<p>%s</p>', esc_attr__( 'use Shift + Enter to prevent line between', 'sunflower' ) );

	wp_editor(
		$custom['_sunflower_metadata'][0] ?? '',
		'sunflower-meta-data',
		array(
			'textarea_rows' => '5',
			'media_buttons' => false,
		)
	);
	echo '</div>';
}

add_filter(
	'get_the_archive_title',
	static function ( $title ) {
		if ( is_category() ) {
			$title = single_cat_title( '', false );
		} elseif ( is_tag() ) {
			$title = single_tag_title( '', false );
		} elseif ( is_author() ) {
			$title = '<span class="vcard">' . get_the_author() . '</span>';
		} elseif ( is_post_type_archive() ) {
			$title = post_type_archive_title( '', false );
		}

		return $title;
	}
);
