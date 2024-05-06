<?php
/**
 * Register Sunflower block patterns.
 *
 * @package sunflower
 */

/**
 * Include required classes to access local files.
 */
require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

if ( function_exists( 'register_block_pattern_category' ) ) {
	$sunflower_dirs       = glob( get_template_directory() . '/functions/block-patterns/*', GLOB_ONLYDIR );
	$sunflower_filesystem = new WP_Filesystem_Direct( true );

	foreach ( $sunflower_dirs as $sunflower_dir ) {
		$sunflower_basename_dir = basename( $sunflower_dir );

		register_block_pattern_category(
			'sunflower-' . $sunflower_basename_dir,
			array(
				'label' => esc_html__( 'Sunflower', 'sunflower' ) . '-' . ucfirst( $sunflower_basename_dir ),
			)
		);

		$sunflower_files = glob( $sunflower_dir . '/*.html' );
		foreach ( $sunflower_files as $sunflower_file ) {
			$sunflower_basename_file = basename( $sunflower_file, '.html' );

			register_block_pattern(
				'sunflower/' . $sunflower_basename_file,
				array(
					'title'      => ucfirst( $sunflower_basename_file ),
					'categories' => array( 'sunflower-' . $sunflower_basename_dir ),
					'content'    => $sunflower_filesystem->get_contents( $sunflower_file ),
				)
			);
		}
	}
}
