<?php

function sunflower_add_custom_gutenberg_color_palette() {
	add_theme_support(
		'editor-color-palette',
		[
			[
				'name'  => esc_html__( 'Magenta', 'sunflower' ),
				'slug'  => 'magenta',
				'color' => '#e6007e',
			],
			[
				'name'  => esc_html__( 'Green', 'sunflower' ),
				'slug'  => 'green',
				'color' => '#46962b',
            ],
            [
				'name'  => esc_html__( 'Lightgray', 'sunflower' ),
				'slug'  => 'lightgray',
				'color' => '#aaaaaa',
			],

		]
	);
}
add_action( 'after_setup_theme', 'sunflower_add_custom_gutenberg_color_palette' );