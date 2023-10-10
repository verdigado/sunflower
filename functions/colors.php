<?php

function sunflower_add_custom_gutenberg_color_palette() {
	add_theme_support(
		'editor-color-palette',
		array(
			array(
				'name'  => esc_html__( 'Himmel', 'sunflower' ),
				'slug'  => 'himmel',
				'color' => '#0BA1DD',
			),
			array(
				'name'  => esc_html__( 'Tanne', 'sunflower' ),
				'slug'  => 'tanne',
				'color' => '#005437',
			),
			array(
				'name'  => esc_html__( 'Klee', 'sunflower' ),
				'slug'  => 'klee',
				'color' => '#008939',
			),
			array(
				'name'  => esc_html__( 'Grashalm', 'sunflower' ),
				'slug'  => 'grashalm',
				'color' => '#8ABD24',
			),
			array(
				'name'  => esc_html__( 'White', 'sunflower' ),
				'slug'  => 'white',
				'color' => '#ffffff',
			),
			array(
				'name'  => esc_html__( 'Sonne', 'sunflower' ),
				'slug'  => 'sonne',
				'color' => '#FFF17A',
			),
			array(
				'name'  => esc_html__( 'Sand', 'sunflower' ),
				'slug'  => 'sand',
				'color' => '#F5F1E9',
			),
			array(
				'name'  => esc_html__( 'Gray 100', 'sunflower' ),
				'slug'  => 'gray-100',
				'color' => '#EFF2ED',
			),
			array(
				'name'  => esc_html__( 'Black 500', 'sunflower' ),
				'slug'  => 'black-500',
				'color' => '#201D1B',
			),

		)
	);
}
add_action( 'after_setup_theme', 'sunflower_add_custom_gutenberg_color_palette' );
