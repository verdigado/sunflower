<?php

if( function_exists( 'register_block_pattern_category') ){
    $dirs = glob(get_template_directory() . '/functions/block-patterns/*', GLOB_ONLYDIR);

    foreach($dirs AS $dir){
        $basenameDir = basename($dir);

        register_block_pattern_category(
            'sunflower-' . $basenameDir,
            array( 'label' => esc_html__( 'Sunflower', 'sunflower' ) . '-' . ucfirst($basenameDir))
        );
   
        $files = glob( $dir . '/*.html' );
        foreach($files AS $file){
            $basenameFile = basename($file, '.html');

            register_block_pattern(
                'sunflower/' . $basenameFile,
                array(
                    'title'       => ucfirst($basenameFile),
                    'categories'  => ['sunflower-' . $basenameDir],
                    'content'     => file_get_contents( $file ),
                )
            );
        }
    }
}
