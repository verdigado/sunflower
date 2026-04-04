<?php
/**
 * Import demo images into the media library on theme activation.
 *
 * @package Sunflower 26
 */

/**
 * List of demo images bundled with the theme (relative to assets/img/).
 */
function sunflower_demo_images() {
	return array(
		'Wald.jpg',
		'Alpen.jpg',
		'Leuchtturm.jpg',
		'Fahrrad.jpg',
		'ICE.jpg',
		'Elektroauto.jpg',
		'Biene.jpg',
		'Duenen.jpg',
		'Kuppel.jpg',
		'TheSunflower.jpg',
		'Beispielbild_Kandidatin.jpg',
	);
}

/**
 * Import demo images from assets/img/ into the WordPress media library.
 * Runs only once; subsequent calls are skipped via a stored option.
 *
 * @return array Attachment IDs keyed by filename (without extension).
 */
function sunflower_import_demo_images() {
	if ( get_option( 'sunflower_demo_images_imported' ) ) {
		return (array) get_option( 'sunflower_demo_image_ids', array() );
	}

	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';

	$ids     = array();
	$img_dir = get_template_directory() . '/assets/img/';

	foreach ( sunflower_demo_images() as $filename ) {
		$filepath = $img_dir . $filename;

		if ( ! file_exists( $filepath ) ) {
			continue;
		}

		$file_contents = file_get_contents( $filepath ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( false === $file_contents ) {
			continue;
		}

		// Skip if an attachment with this title already exists.
		$existing = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'title'          => pathinfo( $filename, PATHINFO_FILENAME ),
				'posts_per_page' => 1,
				'fields'         => 'ids',
			)
		);
		if ( ! empty( $existing ) ) {
			$key         = pathinfo( $filename, PATHINFO_FILENAME );
			$ids[ $key ] = $existing[0];
			continue;
		}

		$upload = wp_upload_bits( $filename, null, $file_contents );
		if ( ! empty( $upload['error'] ) ) {
			continue;
		}

		$filetype  = wp_check_filetype( $filename );
		$attach_id = wp_insert_attachment(
			array(
				'post_title'     => pathinfo( $filename, PATHINFO_FILENAME ),
				'post_mime_type' => $filetype['type'],
				'post_status'    => 'inherit',
			),
			$upload['file']
		);

		if ( is_wp_error( $attach_id ) ) {
			continue;
		}

		$attach_meta = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
		wp_update_attachment_metadata( $attach_id, $attach_meta );

		$key         = pathinfo( $filename, PATHINFO_FILENAME );
		$ids[ $key ] = $attach_id;
	}

	update_option( 'sunflower_demo_images_imported', true );
	update_option( 'sunflower_demo_image_ids', $ids );

	return $ids;
}
