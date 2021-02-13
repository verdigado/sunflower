<?php
function sunflower_add_meta_boxes() {
    // see https://developer.wordpress.org/reference/functions/add_meta_box for a full explanation of each property
    add_meta_box(
        "sunflower_meta_box_layout", // div id containing rendered fields
        __("Layout", 'sunflower'), // section heading displayed as text
        "sunflower_meta_box_layout", // callback function to render fields
        array("post", "page","event"), // name of post type on which to render fields
        "side", // location on the screen
        "high" // placement priority
    );

    add_meta_box(
        "sunflower_meta_box_metadata", // div id containing rendered fields
        __("Metadata", 'sunflower'), // section heading displayed as text
        "sunflower_meta_box_metadata", // callback function to render fields
        array("post", "page"), // name of post type on which to render fields
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

    update_post_meta( $post->ID, "_sunflower_post_thumbnail_object_fit", sanitize_text_field( @$_POST[ "_sunflower_post_thumbnail_object_fit" ] ) );
    update_post_meta( $post->ID, "_sunflower_show_title", sanitize_text_field( @$_POST[ "_sunflower_show_title" ] ) );
    update_post_meta( $post->ID, "_sunflower_roofline", sanitize_text_field( @$_POST[ "_sunflower_roofline" ] ) );


}
add_action( 'save_post', 'save_sunflower_meta_boxes' );

function sunflower_meta_box_layout(){
    global $post;
    $custom = get_post_custom( $post->ID );

    if( isset( $custom['_sunflower_show_title'][0]) ){
        $checked = ($custom['_sunflower_show_title'][0]) ? 'checked': '';
    } else {
        $checked = 'checked';
    }
    printf('
    <div class="components-panel__row">
        <div class="components-base-control__field">
            <span class="components-checkbox-control__input-container">
                <input name="_sunflower_show_title" id="_sunflower_show_title" class="" type="checkbox" value="1" %s>
            </span>
            <label class="components-checkbox-control__label" for="_sunflower_show_title">%s</label>
        </div>
    </div>',
    $checked,
    __('Show title', 'sunflower')
    );

}

function sunflower_meta_box_metadata(){
    global $post;
    $custom = get_post_custom( $post->ID );

    echo '<div class="">';
    _e('Roofline', 'sunflower');
    echo '<div><input name="_sunflower_roofline" value="' . @$custom['_sunflower_roofline'][0] .'" class="components-text-control__input">';
    echo '</div></div>';

}

add_filter( 'get_the_archive_title', function ($title) {    
    if ( is_category() ) {    
            $title = single_cat_title( '', false );    
        } elseif ( is_tag() ) {    
            $title = single_tag_title( '', false );    
        } elseif ( is_author() ) {    
            $title = '<span class="vcard">' . get_the_author() . '</span>' ;    
        } elseif ( is_tax() ) { //for custom post types
            $title = sprintf( __( '%1$s' ), single_term_title( '', false ) );
        } elseif (is_post_type_archive()) {
            $title = post_type_archive_title( '', false );
        }
    return $title;    
});