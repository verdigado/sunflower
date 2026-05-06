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
 */

/**
 * SVG-MIME-Typen für Uploads erlauben.
 *
 * @param array<string, string> $mimes Vorhandene MIME-Typen.
 * @return array<string, string> Angepasste MIME-Typen.
 */
function sunflower_allow_svg_uploads( $mimes ) {
	$mimes['svg']  = 'image/svg+xml';
	$mimes['svgz'] = 'image/svg+xml';

	return $mimes;
}
add_filter( 'upload_mimes', 'sunflower_allow_svg_uploads' );

/**
 * Fehlerhafte SVG-MIME-Typen beim Upload korrigieren.
 *
 * @param array<string, mixed> $data     Dateidaten und MIME-Infos.
 * @param string               $file     Dateipfad.
 * @param string               $filename Dateiname.
 * @return array<string, mixed> Angepasste Dateidaten.
 */
function sunflower_fix_svg_mime_type( $data, $file, $filename ) {

	$ext = strtolower( pathinfo( $filename, PATHINFO_EXTENSION ) );

	if ( 'svg' === $ext ) {
		$data['ext']  = 'svg';
		$data['type'] = 'image/svg+xml';
	}

	return $data;
}
add_filter( 'wp_check_filetype_and_ext', 'sunflower_fix_svg_mime_type', 10, 3 );

/**
 * SVG-Code bereinigen (Skripte, Event-Handler usw. entfernen).
 *
 * @param array<string, mixed> $file Daten der hochgeladenen Datei.
 * @return array<string, mixed> Bereinigte Dateidaten.
 */
function sunflower_sanitize_svg( $file ) {

	if ( isset( $file['type'] ) && 'image/svg+xml' === $file['type'] ) {
		global $wp_filesystem;

		if ( ! $wp_filesystem ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
			WP_Filesystem();
		}

		if ( $wp_filesystem ) {
			$contents = $wp_filesystem->get_contents( $file['tmp_name'] );

			if ( false !== $contents ) {
				// Entfernt Skripte, Event-Handler, iframes usw.
				$contents = preg_replace( '/<script.*?<\/script>/is', '', $contents );
				$contents = preg_replace( '/on\w+="[^"]*"/i', '', $contents );
				$contents = preg_replace( '/<iframe.*?<\/iframe>/is', '', $contents );

				$wp_filesystem->put_contents( $file['tmp_name'], $contents );
			}
		}
	}

	return $file;
}
add_filter( 'wp_handle_upload_prefilter', 'sunflower_sanitize_svg' );



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
