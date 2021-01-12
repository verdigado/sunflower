<?php

function sunflower_block_enqueue() {
    $asset_file = include( get_template_directory() . '/build/index.asset.php');
 
    wp_enqueue_script(
        'sunflower-block',
        get_template_directory_uri() .'/build/index.js',
        $asset_file['dependencies'],
        $asset_file['version']
    );

}
add_action( 'enqueue_block_editor_assets', 'sunflower_block_enqueue' );
