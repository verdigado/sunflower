<?php
function sunflower_add_meta_boxes() {
    // see https://developer.wordpress.org/reference/functions/add_meta_box for a full explanation of each property
    add_meta_box(
        "sunflower_meta_box", // div id containing rendered fields
        __("Post thumbail", 'sunflower'), // section heading displayed as text
        "sunflower_meta_box", // callback function to render fields
        "event", // name of post type on which to render fields
        "side", // location on the screen
        "high" // placement priority
    );
}
add_action( "admin_init", "sunflower_add_meta_boxes" );

function save_sunflower_meta_boxes(){
    global $post;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( get_post_status( $post->ID ) === 'auto-draft' ) {
        return;
    }
    update_post_meta( $post->ID, "_sunflower_event_from", sanitize_text_field( $_POST[ "_sunflower_event_from" ] ) );
    update_post_meta( $post->ID, "_sunflower_post_thumbnail_object_fit", sanitize_text_field( $_POST[ "_sunflower_post_thumbnail_object_fit" ] ) );

}
add_action( 'save_post', 'save_sunflower_meta_boxes' );

function sunflower_meta_box(){
    global $post;
    $custom = get_post_custom( $post->ID );

    $field = '_sunflower_post_thumbnail_object_fit';
    $options = array(
        '' => __('Theme default', 'sunflower'),
        'contain' => __('complete image', 'sunflower'),
        'cover'   => __('full area', 'sunflower'),

    );
    _e('How should the thumbnail be displayed on archive pages', 'sunflower');
    echo '<div><select name="' . $field .'" id="' . $field .'">';
        foreach($options AS $id => $label ) {
            $selected = (isset($custom[$field][0]) AND $id == $custom[$field][0] ) ? 'selected' : '';
            printf('<option value="%s" %s>%s</option>', $id, $selected, $label);
        }
    echo '</select></div>';

}