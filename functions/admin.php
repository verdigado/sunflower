<?php 
function sunflower_admin_bar( $wp_admin_bar ) {
    $wp_admin_bar->remove_node( 'wp-logo' );
    
    $args = array(
		'id'    => 'sunflower',
		'title' => 'Sunflower',
		'href'  => home_url() . '/wp-admin/admin.php?page=sunflower_admin',
		'meta'  => array( 'class' => '' )
	);
	$wp_admin_bar->add_node( $args );
}
add_action( 'admin_bar_menu', 'sunflower_admin_bar', 999 );

function sunflower_notice() {
    ?>
    <div class="notice notice-info notice-large sunflower-plugins is-dismissible">
        <p><?php _e( "Thank you for using sunflower theme. <a href='admin.php?page=sunflower_admin'>More information ca be found on the theme's setting page</a>.", 'sunflower' ); ?></p>
    </div>
    <?php
}
if (empty( get_option( 'sunflower-plugins-dismissed' ) ) ){
    add_action( 'admin_notices', 'sunflower_notice' );
}

function sunflower_load_admin_scripts(){ 
    wp_enqueue_script('sunflower-admin',
        get_template_directory_uri() .'/assets/js/admin.js', 
        array('jquery'), 
        '1.0.0', 
        true
    ); 
    wp_localize_script( 'sunflower-admin', 'sunflower', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'title' => get_the_title()
    )
);
}
add_action( 'admin_enqueue_scripts', 'sunflower_load_admin_scripts' );


function sunflower_update_notice() {
	update_option( 'sunflower-plugins-dismissed', 1 );
}
add_action( 'wp_ajax_sunflower_plugins_dismiss', 'sunflower_update_notice' );

function sunflower_admin() {
	//delete_option( 'sunflower-plugins-dismissed' );
}
add_action( 'admin_init', 'sunflower_admin' );

function sunflower_admin_style() {
    wp_enqueue_style('sunflower-admin-fontawesome', get_template_directory_uri().'/assets/css/admin-fontawesome.css');
    wp_enqueue_style('sunflower-admin-styles', get_template_directory_uri().'/assets/css/admin.css');
 }
 
 add_action('admin_enqueue_scripts', 'sunflower_admin_style');