<?php
function sunflower_add_meta_boxes() {
    // see https://developer.wordpress.org/reference/functions/add_meta_box for a full explanation of each property
    add_meta_box(
        "sunflower_meta_box", // div id containing rendered fields
        __("Layout", 'sunflower'), // section heading displayed as text
        "sunflower_meta_box", // callback function to render fields
        array("post", "page","event"), // name of post type on which to render fields
        "side", // location on the screen
        "high" // placement priority
    );
}
add_action( "admin_init", "sunflower_add_meta_boxes" );

function save_sunflower_meta_boxes(){
    global $post;

    if ( !isset($post->ID ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( get_post_status( $post->ID ) === 'auto-draft' ) {
        return;
    }

    update_post_meta( $post->ID, "_sunflower_post_thumbnail_object_fit", sanitize_text_field( $_POST[ "_sunflower_post_thumbnail_object_fit" ] ) );
    update_post_meta( $post->ID, "_sunflower_show_sidebar", sanitize_text_field( $_POST[ "_sunflower_show_sidebar" ] ) );

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

    echo '<div class="components-panel__row">';
    _e('How should the thumbnail be displayed on archive pages?', 'sunflower');
    echo '<div><select name="' . $field .'" id="' . $field .'">';
        foreach($options AS $id => $label ) {
            $selected = (isset($custom[$field][0]) AND $id == $custom[$field][0] ) ? 'selected' : '';
            printf('<option value="%s" %s>%s</option>', $id, $selected, $label);
        }
    echo '</select></div></div>';

    printf('
        <div class="components-panel__row">
            <div class="components-base-control__field">
                <span class="components-checkbox-control__input-container">
                    <input name="_sunflower_show_sidebar" id="_sunflower_show_sidebar" class="" type="checkbox" value="1" %s>
                </span>
                <label class="components-checkbox-control__label" for="_sunflower_show_sidebar">%s</label>
            </div>
        </div>',
        ($custom['_sunflower_show_sidebar'][0]) ? 'checked': '',
        __('Show sidebar', 'sunflower')
    );

}