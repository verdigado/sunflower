<?php
function sunflower_create_event_post_type() {
 
    register_post_type( 'event',
    // CPT Options
        array(
            'labels' => array(
                'name' => __( 'Events', 'sunflower' ),
                'singular_name' => __( 'Event', 'sunflower' )
            ),
            'public' => true,
            'menu_icon' => 'dashicons-calendar',
            'has_archive' => true,
            'rewrite' => array('slug' => __( 'event', 'sunflower' )),
            'show_in_rest' => true,
            'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
        )
    );
}
add_action( 'init', 'sunflower_create_event_post_type' );

function sunflower_add_event_meta_boxes() {
    // see https://developer.wordpress.org/reference/functions/add_meta_box for a full explanation of each property
    add_meta_box(
        "sunflower_meta_box", // div id containing rendered fields
        __("Event", 'sunflower'), // section heading displayed as text
        "sunflower_meta_box", // callback function to render fields
        "event", // name of post type on which to render fields
        "side", // location on the screen
        "high" // placement priority
    );
}
add_action( "admin_init", "sunflower_add_event_meta_boxes" );

function save_post_meta_boxes(){
    global $post;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( get_post_status( $post->ID ) === 'auto-draft' ) {
        return;
    }
    update_post_meta( $post->ID, "_sunflower_event_from", sanitize_text_field( $_POST[ "_sunflower_event_from" ] ) );
}
add_action( 'save_post', 'save_post_meta_boxes' );

function sunflower_meta_box(){
    global $post;
    $custom = get_post_custom( $post->ID );

    $from = $custom[ "_sunflower_event_from" ][ 0 ];

    printf('<input class="datetimepicker" type="text" name="_sunflower_event_from" placeholder="%s" value="%s"', 
        __('Start date', 'sunflower'), 
        $from );

    /*
    $advertisingCategory = $custom[ "_post_advertising_category" ][ 0 ];
    $advertisingHtml = $custom[ "_post_advertising_html" ][ 0 ];
    wp_editor(
        htmlspecialchars_decode( $advertisingHtml ),
        '_post_advertising_html',
        $settings = array(
            'textarea_name' => '_post_advertising_html',
        )
    );
    switch ( $advertisingCategory ) {
        case 'internal':
            $internalSelected = "selected";
            break;
        case 'external':
            $externalSelected = "selected";
            break;
        case 'mixed':
            $mixedSelected = "selected";
            break;
    }
    echo "<br>";
    echo "<select name=\"_post_advertising_category\">";
    echo "    <option value=\"internal\" $internalSelected>Internal</option>";
    echo "    <option value=\"external\" $externalSelected>External</option>";
    echo "    <option value=\"mixed\" $mixedSelected>Mixed</option>";
    echo "</select>";
    */
}

function sunflower_load_event_admin_scripts(){ 
    wp_enqueue_script('sunflower-datetimepicker',
        get_template_directory_uri() .'/assets/vndr/datetimepicker/jquery.datetimepicker.full.min.js', 
        array('jquery'), 
        '1.0.0', 
        true
    ); 

    wp_enqueue_script('sunflower-datetimepicker-custom',
        get_template_directory_uri() .'/assets/vndr/datetimepicker/sunflower.js', 
        array('sunflower-datetimepicker'), 
        '1.0.0', 
        true
    ); 

    wp_enqueue_style( 'sunflower-datetimepicker', 
        get_template_directory_uri() .'/assets/vndr/datetimepicker/jquery.datetimepicker.css', 
        array(), 
        '1.0.0' );

}
add_action( 'admin_enqueue_scripts', 'sunflower_load_event_admin_scripts' );
