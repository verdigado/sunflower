<?php
/**
 * Include all function files.
 *
 * @package Sunflower 26
 */

/**
 * Include all sub-function files.
 */
require_once __DIR__ . '/functions/s.php';
require_once __DIR__ . '/functions/options/class-sunflowerfirststepspage.php';
require_once __DIR__ . '/functions/options/class-sunflowersettingspage.php';
require_once __DIR__ . '/functions/options/class-sunflowersocialmediasettingspage.php';
require_once __DIR__ . '/functions/options/class-sunflowereventsettingspage.php';
require_once __DIR__ . '/functions/events.php';
require_once __DIR__ . '/functions/excerpts.php';
require_once __DIR__ . '/functions/admin.php';
require_once __DIR__ . '/functions/metaboxes.php';
require_once __DIR__ . '/functions/blocks.php';
require_once __DIR__ . '/functions/update.php';
require_once __DIR__ . '/functions/related-posts.php';
require_once __DIR__ . '/functions/demo-content.php';
require_once __DIR__ . '/functions/demo-setup.php';
require_once __DIR__ . '/functions/welcome-page.php';
require_once __DIR__ . '/functions/activation.php';
require_once __DIR__ . '/functions/comments.php';
require_once __DIR__ . '/functions/icalimport.php';
require_once __DIR__ . '/functions/emailscrambler.php';
require_once __DIR__ . '/functions/contact-form.php';
require_once __DIR__ . '/functions/api.php';
require_once __DIR__ . '/functions/childtheme.php';
require_once __DIR__ . '/functions/menu.php';
require_once __DIR__ . '/functions/security.php';
require_once __DIR__ . '/functions/theme.php';
require_once __DIR__ . '/functions/latest-posts.php';
require_once __DIR__ . '/functions/media.php';
require_once __DIR__ . '/functions/class-sunflower-contact-widget.php';
require_once __DIR__ . '/functions/widgets.php';


/**
 * Get value of the sunflower settings.
 *
 * @param string $option The option key to search for.
 */
function sunflower_get_setting( $option ) {
	$options = get_option( 'sunflower_options' );

	if ( ! is_array( $options ) ) {
		$options = array();
	}

	$sunflower_social_media_options = get_option( 'sunflower_social_media_options' );
	if ( is_array( $sunflower_social_media_options ) ) {
		$options = array_merge( $options, $sunflower_social_media_options );
	}

	$sunflower_events_options = get_option( 'sunflower_events_options' );
	if ( is_array( $sunflower_events_options ) ) {
		$options = array_merge( $options, $sunflower_events_options );
	}

	if ( ! isset( $options[ $option ] ) ) {
		return false;
	}

	if ( empty( $options[ $option ] ) ) {
		return 0;
	}

	return $options[ $option ];
}


/**
 * Get the linked social media icons.
 */
function sunflower_get_social_media_profiles() {
	$profiles = block_core_social_link_services();

	$return = '';

	$lines = explode( "\n", (string) sunflower_get_setting( 'sunflower_social_media_profiles' ) );
	foreach ( $lines as $line ) {
		$line = trim( $line );
		if ( str_starts_with( $line, '#' ) || empty( $line ) ) {
			continue;
		}
		$some_profile = explode( ';', $line );
		$class        = $some_profile[0] ?? false;
		$title        = $some_profile[1] ?? false;
		$url          = $some_profile[2] ?? false;

		if ( false === $url || empty( $url ) ) {
			continue;
		}

		$return .= sprintf(
			'<a href="%1$s" target="_blank" title="%3$s" class="social-media-profile" rel="me"><i class="%2$s"></i></a>',
			esc_url( $url ),
			esc_attr( $class ),
			esc_attr( $title )
		);
	}

	if ( ! empty( $return ) ) {
		$return = sprintf( '<div class="sunflower__socials">%s</div>', $return );
	}

	return $return;
}


/**
 * SVG Upload
 *
 * An SVG is a document that can carry scripts, so it is treated differently
 * from other images: only editors and administrators may upload one, and every
 * upload is rebuilt by an allowlist-based sanitizer. Files that cannot be
 * sanitized are rejected.
 */

/**
 * Whether the current user may upload SVG files.
 *
 * @return bool True for editors and administrators.
 */
function sunflower_user_can_upload_svg() {
	return current_user_can( 'edit_others_posts' );
}

/**
 * SVG-MIME-Typ für Uploads erlauben.
 *
 * Compressed SVG (.svgz) is deliberately not allowed: it cannot be sanitized
 * without unpacking it first.
 *
 * @param array<string, string> $mimes Vorhandene MIME-Typen.
 * @return array<string, string> Angepasste MIME-Typen.
 */
function sunflower_allow_svg_uploads( $mimes ) {
	if ( sunflower_user_can_upload_svg() ) {
		$mimes['svg'] = 'image/svg+xml';
	}

	return $mimes;
}
add_filter( 'upload_mimes', 'sunflower_allow_svg_uploads' );

/**
 * Fehlerhafte SVG-MIME-Typen beim Upload korrigieren.
 *
 * Only for users who may upload SVG at all - otherwise this would hand a valid
 * type to a file that the list of allowed MIME types has just refused.
 *
 * @param array<string, mixed> $data     Dateidaten und MIME-Infos.
 * @param string               $file     Dateipfad.
 * @param string               $filename Dateiname.
 * @return array<string, mixed> Angepasste Dateidaten.
 */
function sunflower_fix_svg_mime_type( $data, $file, $filename ) {

	$ext = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );

	if ( 'svg' === $ext && sunflower_user_can_upload_svg() ) {
		$data['ext']  = 'svg';
		$data['type'] = 'image/svg+xml';
	}

	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'sunflower_fix_svg_mime_type', 10, 3 );

/**
 * SVG-Code bereinigen (Skripte, Event-Handler usw. entfernen).
 *
 * The decision is based on the file name: the MIME type in $file['type'] is
 * sent by the browser and must not be trusted.
 *
 * @param array<string, mixed> $file Daten der hochgeladenen Datei.
 * @return array<string, mixed> Bereinigte Dateidaten, or with 'error' set to reject the upload.
 */
function sunflower_sanitize_svg( $file ) {

	$ext = strtolower( pathinfo( (string) ( $file['name'] ?? '' ), PATHINFO_EXTENSION ) );

	if ( 'svgz' === $ext ) {
		$file['error'] = __( 'Compressed SVG files (.svgz) are not allowed. Please upload a plain .svg file.', 'sunflower' );
		return $file;
	}

	if ( 'svg' !== $ext ) {
		return $file;
	}

	if ( ! sunflower_user_can_upload_svg() ) {
		$file['error'] = __( 'Sorry, you are not allowed to upload SVG files.', 'sunflower' );
		return $file;
	}

	// Fail closed: without the sanitizer library no SVG gets through.
	if ( ! class_exists( '\enshrined\svgSanitize\Sanitizer' ) || empty( $file['tmp_name'] ) ) {
		$file['error'] = __( 'This SVG file could not be checked and was not uploaded.', 'sunflower' );
		return $file;
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- local temporary upload file.
	$contents = file_get_contents( $file['tmp_name'] );

	try {
		$sanitizer = new \enshrined\svgSanitize\Sanitizer();
		$sanitizer->removeRemoteReferences( true );
		$clean = is_string( $contents ) ? $sanitizer->sanitize( $contents ) : false;
	} catch ( \Throwable $e ) {
		// A sanitizer that fails must never let the file through.
		$clean = false;
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents -- local temporary upload file.
	if ( ! is_string( $clean ) || '' === $clean || false === file_put_contents( $file['tmp_name'], $clean ) ) {
		$file['error'] = __( 'This SVG file could not be sanitized and was not uploaded.', 'sunflower' );
		return $file;
	}

	$file['size'] = strlen( $clean );

	return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'sunflower_sanitize_svg' );
add_filter( 'wp_handle_sideload_prefilter', 'sunflower_sanitize_svg' );



if ( ! function_exists( 'sunflower_filter_excerpt_more' ) ) {
	/**
	 * Replace default excerpt more string.
	 *
	 * @param string $more Default excerpt more.
	 * @return string
	 */
	function sunflower_filter_excerpt_more( string $more ): string {
		// Use parameter (PHPCS) even though we override the output.
		if ( '' === $more ) {
			$more = '';
		}

		return '...';
	}

	add_filter( 'excerpt_more', 'sunflower_filter_excerpt_more' );
}

// Core Block Patterns deaktivieren.
add_action(
	'after_setup_theme',
	function () {
		remove_theme_support( 'core-block-patterns' );
	},
	20
);
