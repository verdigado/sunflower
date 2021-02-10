<?php

if( function_exists( 'register_block_pattern_category') ){
    register_block_pattern_category(
        'sunflower',
        array( 'label' => esc_html__( 'Sunflower', 'sunflower' ) )
    );

    register_block_pattern(
        'sunflower/person',
        array(
            'title'       => __( 'Person', 'sunflower' ),
            'description' => _x( 'Describes a person.', 'Block pattern description', 'function' ),
            'categories'  => ['sunflower'],
            'content'     => file_get_contents( get_stylesheet_directory() . '/functions/block-patterns/person.html'),
        )
    );
}
