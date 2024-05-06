<?php
/**
 * Method for registring Sunflower blocks.
 *
 * @package sunflower
 */

/**
 * Add Sunflower block category.
 *
 * @param array $categories The block categories.
 */
function sunflower_block_category( $categories ) {
	return array_merge(
		$categories,
		array(
			array(
				'slug'  => 'sunflower-blocks',
				'title' => __( 'Sunflower', 'sunflower' ),
			),
		)
	);
}

add_filter( 'block_categories_all', 'sunflower_block_category', 10, 2 );

/**
 * Registers the block using the metadata loaded from the `block.json` file.
 * Behind the scenes, it registers also all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://developer.wordpress.org/reference/functions/register_block_type/
 */
function sunflower_blocks_init() {
	register_block_type( get_template_directory() . '/build/accordion' );
	wp_set_script_translations(
		'sunflower-accordion-editor-script',
		'sunflower-accordion',
		get_template_directory() . '/languages'
	);

	register_block_type( get_template_directory() . '/build/latest-posts' );
	wp_set_script_translations(
		'sunflower-latest-posts-editor-script',
		'sunflower-latest-posts',
		get_template_directory() . '/languages'
	);

	register_block_type( get_template_directory() . '/build/meta-data' );
	wp_set_script_translations(
		'sunflower-meta-data-editor-script',
		'sunflower-meta-data',
		get_template_directory() . '/languages'
	);

	register_block_type( get_template_directory() . '/build/next-events' );
	wp_set_script_translations(
		'sunflower-next-events-editor-script',
		'sunflower-next-events',
		get_template_directory() . '/languages'
	);

	register_block_type( get_template_directory() . '/build/contact-form' );
	wp_set_script_translations(
		'sunflower-contact-form-editor-script',
		'sunflower-contact-form',
		get_template_directory() . '/languages'
	);
}

add_action( 'init', 'sunflower_blocks_init' );

/**
 * Add the block language files.
 */
function sunflower_blocks_load_textdomain() {
	load_textdomain( 'sunflower-accordion', get_template_directory() . '/languages/sunflower-accordion-de_DE.mo' );
	load_theme_textdomain( 'sunflower-accordion', get_template_directory() . '/languages' );

	load_textdomain( 'sunflower-latest-posts', get_template_directory() . '/languages/sunflower-latest-posts-de_DE.mo' );
	load_theme_textdomain( 'sunflower-latest-posts', get_template_directory() . '/languages' );

	load_textdomain( 'sunflower-next-events', get_template_directory() . '/languages/sunflower-next-events-de_DE.mo' );
	load_theme_textdomain( 'sunflower-next-events', get_template_directory() . '/languages' );

	load_textdomain( 'sunflower-contact-form', get_template_directory() . '/languages/sunflower-contact-form-de_DE.mo' );
	load_theme_textdomain( 'sunflower-contact-form', get_template_directory() . '/languages' );

	load_textdomain( 'sunflower-meta-data', get_template_directory() . '/languages/sunflower-meta-data-de_DE.mo' );
	load_theme_textdomain( 'sunflower-meta-data', get_template_directory() . '/languages' );
}

add_action( 'after_setup_theme', 'sunflower_blocks_load_textdomain' );
