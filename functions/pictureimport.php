<?php

function sunflower_import_all_pictures() {
	// Gives us access to the download_url() and wp_handle_sideload() functions
	include_once ABSPATH . 'wp-admin/includes/file.php';

	$server = 'https://sunflower-theme.de/updateserver/';
	$images = json_decode( file_get_contents( $server . 'images.php' ) );

	$count = 0;
	foreach ( $images as $image ) {
		if ( sunflower_import_one_picture( $server . $image ) ) {
			$count++;
		};
	}

	return $count;
}

function sunflower_import_one_picture( $url ) {
	 // URL to the WordPress logo

	$timeout_seconds = 5;

	// Download file to temp dir
	$temp_file = download_url( $url, $timeout_seconds );

	if ( ! is_wp_error( $temp_file ) ) {

		// Array based on $_FILE as seen in PHP file uploads
		$file = array(
			'name'     => basename( $url ), // ex: wp-header-logo.png
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
			// Default is true
			'test_form' => false,

			// Setting this to false lets WordPress allow empty files, not recommended
			// Default is true
			'test_size' => true,
		);

		// Move the temporary file into the uploads directory
		$results = wp_handle_sideload( $file, $overrides );

		if ( ! empty( $results['error'] ) ) {
			// print_r($results);
			echo __( 'An error occurred. Could not import images', 'sunflower' );
			return false;
		} else {

			$filename  = $results['file']; // Full path to the file
			$local_url = $results['url'];  // URL to the file in the uploads dir
			$type      = $results['type']; // MIME type of the file

			$attachment = array(
				'post_mime_type' => $type,
				'post_title'     => basename( $filename ),
				'post_content'   => '',
				'post_status'    => 'inherit',
			);

			$attachment_id   = wp_insert_attachment( $attachment, $filename );
			$attachment_data = wp_generate_attachment_metadata( $attachment_id, $filename );
			wp_update_attachment_metadata( $attachment_id, $attachment_data );

			echo "<li>$url importiert</li>";
		}

		return true;
	} else {
		return false;
	}
}
