<?php
/**
 * Methods for the sample image import into media.
 *
 * @package sunflower
 */

/**
 * Import all pictures from sunflower updateserver to local.
 */
function sunflower_import_all_pictures() {
	// Gives us access to the download_url() and wp_handle_sideload() functions.
	include_once ABSPATH . 'wp-admin/includes/file.php';

	$server = 'https://sunflower-theme.de/updateserver/';
	$images = json_decode( wp_remote_retrieve_body( wp_remote_get( $server . 'images.php' ) ) );

	$count = 0;
	if ( is_array( $images ) ) {
		foreach ( $images as $image ) {
			if ( sunflower_import_one_picture( $server . $image ) ) {
				++$count;
			}
		}
	}

	return $count;
}

/**
 * Import single picture from url
 *
 * @param string $url The file url.
 */
function sunflower_import_one_picture( $url ) {
	$timeout_seconds = 5;

	// Download the file to temp dir.
	$temp_file = download_url( $url, $timeout_seconds );

	if ( ! is_wp_error( $temp_file ) ) {
		// Array based on $_FILE as seen in PHP file uploads.
		$file = array(
			'name'     => basename( (string) $url ),
			'type'     => 'image/png',
			'tmp_name' => $temp_file,
			'error'    => 0,
			'size'     => filesize( $temp_file ),
		);

		$overrides = array(
			// Tells WordPress to not look for the POST form
			// fields that would normally be present as
			// we downloaded the file from a remote server, so there
			// will be no form fields
			// Default is true.
			'test_form' => false,
			// Setting this to false lets WordPress allow empty files, not recommended.
			// Default is true.
			'test_size' => true,
		);

		// Move the temporary file into the uploads directory.
		$results = wp_handle_sideload( $file, $overrides );

		if ( ! empty( $results['error'] ) ) {
			esc_attr_e( 'An error occurred. Could not import images', 'sunflower' );
			return false;
		}

		// Full path to the file.
		$filename = $results['file'];
		// URL to the file in the uploads dir.
		$local_url = $results['url'];
		// MIME type of the file.
		$type            = $results['type'];
		$attachment      = array(
			'post_mime_type' => $type,
			'post_title'     => basename( (string) $filename ),
			'post_content'   => '',
			'post_status'    => 'inherit',
		);
		$attachment_id   = wp_insert_attachment( $attachment, $filename );
		$attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
		wp_update_attachment_metadata( $attachment_id, $attachment_data );
		printf( '<li>%s importiert</li>', esc_url( $url ) );

		return true;
	}

	return false;
}
