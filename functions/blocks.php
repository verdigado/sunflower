<?php
/**
 * Method for registring Sunflower blocks.
 *
 * @package Sunflower 26
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
	require_once __DIR__ . '/block-patterns.php';

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

/**
 * Add icon picker for WP blocks.
 */
function sunflower_enqueue_block_icon_picker() {
	wp_enqueue_script(
		'sunflower-button-icon-picker',
		get_template_directory_uri() . '/assets/js/block-icon-picker.js',
		array(
			'wp-blocks',
			'wp-element',
			'wp-editor',
			'wp-components',
			'wp-i18n',
			'wp-compose',
			'wp-data',
		),
		filemtime( get_template_directory() . '/assets/js/block-icon-picker.js' ),
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'sunflower_enqueue_block_icon_picker' );

/**
 * Enqueue script for setting --bg CSS variable on mark elements in editor.
 * This replicates the frontend.js behavior for skewed headlines in the block editor.
 * Uses enqueue_block_assets to ensure it runs inside the editor iframe (WP 5.9+).
 */
function sunflower_enqueue_editor_mark_bg() {
	// Nur im Admin/Editor laden, nicht im Frontend.
	if ( ! is_admin() ) {
		return;
	}

	wp_enqueue_script(
		'sunflower-editor-mark-bg',
		get_template_directory_uri() . '/assets/js/editor-mark-bg.js',
		array(),
		filemtime( get_template_directory() . '/assets/js/editor-mark-bg.js' ),
		true
	);
}
add_action( 'enqueue_block_assets', 'sunflower_enqueue_editor_mark_bg' );

/**
 * Block styles for Gutenberg.
 */
function sunflower_enqueue_block_core_assets() {

	wp_enqueue_script(
		'sunflower-core-list-variations',
		get_template_directory_uri() . '/build/core/cover/index.js',
		array(
			'wp-blocks',
			'wp-i18n',
			'wp-element',
		),
		SUNFLOWER_VERSION,
		true
	);

	wp_enqueue_script(
		'sunflower-core-list-variations',
		get_template_directory_uri() . '/build/core/list/index.js',
		array(
			'wp-blocks',
			'wp-i18n',
			'wp-element',
		),
		SUNFLOWER_VERSION,
		true
	);

	// Styles for the editor.
	wp_enqueue_style(
		'sunflower-core-list-variations',
		get_template_directory_uri() . '/build/core/cover/index.css',
		array(),
		SUNFLOWER_VERSION
	);
	wp_enqueue_style(
		'sunflower-core-list-variations',
		get_template_directory_uri() . '/build/core/list/index.css',
		array(),
		SUNFLOWER_VERSION
	);
}
add_action( 'enqueue_block_editor_assets', 'sunflower_enqueue_block_core_assets' );
