<?php
/**
 * Register Sunflower block patterns.
 *
 * @package sunflower
 */

if ( function_exists( 'register_block_pattern_category' ) ) {
	$sunflower_dirs = glob( get_template_directory() . '/functions/block-patterns/*', GLOB_ONLYDIR );

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
					'content'    => wp_remote_retrieve_body( wp_remote_get( $sunflower_file ) ),
				)
			);
		}
	}
}
