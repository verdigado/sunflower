<?php 
function sunflower_admin_bar( $wp_admin_bar ) {
    $wp_admin_bar->remove_node( 'wp-logo' );
    
    $args = array(
		'id'    => 'sunflower',
		'title' => 'Sunflower',
		'href'  => home_url() . '/wp-admin/admin.php?page=sunflower_settings',
		'meta'  => array( 'class' => '' )
	);
	$wp_admin_bar->add_node( $args );
}
add_action( 'admin_bar_menu', 'sunflower_admin_bar', 999 );

function sunflower_notice() {
    ?>
    <div class="notice notice-info notice-large sunflower-plugins is-dismissible">
        <p><?php _e( 'Hello world', 'sunflower' ); ?></p>
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

