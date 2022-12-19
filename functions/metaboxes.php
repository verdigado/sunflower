<?php
function sunflower_add_meta_boxes() {
	// see https://developer.wordpress.org/reference/functions/add_meta_box for a full explanation of each property
	add_meta_box(
		'sunflower_meta_box_layout', // div id containing rendered fields
		__( 'Layout', 'sunflower' ), // section heading displayed as text
		'sunflower_meta_box_layout', // callback function to render fields
		array( 'post', 'page', 'event' ), // name of post type on which to render fields
		'side', // location on the screen
		'high' // placement priority
	);

	add_meta_box(
		'sunflower_meta_box_metadata', // div id containing rendered fields
		__( 'Metadata', 'sunflower' ), // section heading displayed as text
		'sunflower_meta_box_metadata', // callback function to render fields
		array( 'post', 'page' ), // name of post type on which to render fields
		'side', // location on the screen
		'high' // placement priority
	);
}
add_action( 'admin_init', 'sunflower_add_meta_boxes' );

function save_sunflower_meta_boxes() {
	global $post;

	if ( ! isset( $post->ID ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( get_post_status( $post->ID ) === 'auto-draft' ) {
		return;
	}

	update_post_meta( $post->ID, '_sunflower_styled_layout', sanitize_text_field( @$_POST['_sunflower_styled_layout'] ) );
	update_post_meta( $post->ID, '_sunflower_hide_feature_image', sanitize_text_field( @$_POST['_sunflower_hide_feature_image'] ) );
	update_post_meta( $post->ID, '_sunflower_roofline', sanitize_text_field( @$_POST['_sunflower_roofline'] ) );
	update_post_meta( $post->ID, '_sunflower_metadata', @$_POST['sunflower-meta-data'] );

}
add_action( 'save_post', 'save_sunflower_meta_boxes' );

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
		$checked,
		__( 'Styled layout', 'sunflower' ),
		__( 'do not show title, add and remove margins. e.g. for the homepage', 'sunflower' )
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
		$checked,
		__( 'Hide feature image on single', 'sunflower' ),
		__( 'Hide feature image on single view, but show it on lists.', 'sunflower' )
	);

}

function sunflower_meta_box_metadata() {
	global $post;
	$custom = get_post_custom( $post->ID );

	echo '<div class="">';
	printf( '<h3>%s</h3>', __( 'Roofline', 'sunflower' ) );

	echo '<div><input name="_sunflower_roofline" value="' . @$custom['_sunflower_roofline'][0] . '" class="components-text-control__input">';
	echo '</div></div>';

	echo '<div class="">';
	printf( '<h3>%s</h3>', __( 'Metadata', 'sunflower' ) );
	printf( '<p>%s</p>', __( 'use Shift + Enter to prevent line between', 'sunflower' ) );

	wp_editor(
		@$custom['_sunflower_metadata'][0],
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
	function ( $title ) {
		if ( is_category() ) {
			$title = single_cat_title( '', false );
		} elseif ( is_tag() ) {
			$title = single_tag_title( '', false );
		} elseif ( is_author() ) {
			$title = '<span class="vcard">' . get_the_author() . '</span>';
		} elseif ( is_tax() ) { // for custom post types
			$title = sprintf( __( '%1$s' ), single_term_title( '', false ) );
		} elseif ( is_post_type_archive() ) {
			$title = post_type_archive_title( '', false );
		}
		return $title;
	}
);
