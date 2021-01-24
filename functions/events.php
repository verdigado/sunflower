<?php

$sunflower_event_fields = [
    '_sunflower_event_from'            => [ 'Startdate', 'datetimepicker' ],
    '_sunflower_event_until'           => [ 'Enddate', 'datetimepicker' ],
    '_sunflower_event_whole_day'       => [ 'Whole day', null, 'checkbox' ],
    '_sunflower_event_location_name'   => [ 'Location name' ],
    '_sunflower_event_location_street' => [ 'Street'],
    '_sunflower_event_location_city'   => [ 'City' ]
];

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
        "sunflower_event_meta_box", // div id containing rendered fields
        __("Event", 'sunflower'), // section heading displayed as text
        "sunflower_event_meta_box", // callback function to render fields
        "event", // name of post type on which to render fields
        "side", // location on the screen
        "high" // placement priority
    );
}
add_action( "admin_init", "sunflower_add_event_meta_boxes" );

function save_sunflower_event_meta_boxes(){
    global $post, $sunflower_event_fields;

    if ( !isset($post->ID ) ) {
        return;
    }

    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( get_post_status( $post->ID ) === 'auto-draft' ) {
        return;
    }

    foreach($sunflower_event_fields AS $id => $config ){
        update_post_meta( $post->ID, $id, sanitize_text_field( $_POST[ $id ] ) );
    }
}
add_action( 'save_post', 'save_sunflower_event_meta_boxes' );

function sunflower_event_meta_box(){
    global $post, $sunflower_event_fields;;
    $custom = get_post_custom( $post->ID );

    foreach($sunflower_event_fields AS $id => $config ){
        $value = $custom[ $id ][ 0 ];
        sunflower_event_field( $id, $config, $value );
    }

}

function sunflower_event_field( $id, $config, $value ){
    $label = __($config[0], 'sunflower');
    $class = $config[1] ?: '';
    $type = $config[2] ?: false;

    switch($type){
        case 'checkbox':
            printf('%2$s<input class="%4$s" type="checkbox" name="%1$s" %3$s value="checked"><br>', 
                $id,
                $label,
                ($value) ?: '',
                $class );
            break;
        default:
            printf('%2$s<input class="%4$s" type="text" name="%1$s" placeholder="%2$s" value="%3$s">', 
                $id,
                $label,
                $value,
                $class );
    }
    

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
