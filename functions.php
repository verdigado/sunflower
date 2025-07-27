<?php
/**
 * Include all function files.
 *
 * @package sunflower
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


function aenias_fade_assets() {

	/* Inline‑Script ganz an den Anfang des Heads */
	add_action(
		'wp_head',
		fn() => print(
		"<script>document.documentElement.classList.add('preload');</script>"
		),
		0   // höchste Priorität
	);

	/* JS – im Head mit defer */
	wp_enqueue_script(
		'fade',
		get_stylesheet_directory_uri() . '/assets/js/fade.js',
		[],
		null,
		false
	);
	wp_script_add_data('fade', 'strategy', 'defer');
}
add_action('wp_enqueue_scripts', 'aenias_fade_assets');


/**
 * Add body classes to the theme options
 *
 * @param array $classes Array containing all set body classes.
 */
function sunflower_add_body_classes( $classes ) {
	$options = get_option( 'sunflower_options' );

	if ( ! empty( $options['sunflower_form_style'] ) ) {
		$classes[] = 'formstyle-' . sanitize_html_class( $options['sunflower_form_style'] );
	}

	if ( ! empty( $options['sunflower_color_scheme'] ) ) {
		$classes[] = 'colorscheme-' . sanitize_html_class( $options['sunflower_color_scheme'] );
	}

	if ( ! empty( $options['sunflower_header_layout'] ) ) {
		$classes[] = 'header-' . sanitize_html_class( $options['sunflower_header_layout'] );
	}

	if ( ! empty( $options['sunflower_footer_layout'] ) ) {
		$classes[] = 'footer-' . sanitize_html_class( $options['sunflower_footer_layout'] );
	}

	return $classes;
}
add_filter( 'body_class', 'sunflower_add_body_classes' );


/**
 * Add body classes to the theme options
 *
 * @param array $classes Array containing all set body classes.
 */
function sunflower_add_body_classes( $classes ) {
	$options = get_option( 'sunflower_options' );

	if ( ! empty( $options['sunflower_form_style'] ) ) {
		$classes[] = 'formstyle-' . sanitize_html_class( $options['sunflower_form_style'] );
	}

	if ( ! empty( $options['sunflower_color_scheme'] ) ) {
		$classes[] = 'colorscheme-' . sanitize_html_class( $options['sunflower_color_scheme'] );
	}

	if ( ! empty( $options['sunflower_header_layout'] ) ) {
		$classes[] = 'header-' . sanitize_html_class( $options['sunflower_header_layout'] );
	}

	if ( ! empty( $options['sunflower_footer_layout'] ) ) {
		$classes[] = 'footer-' . sanitize_html_class( $options['sunflower_footer_layout'] );
	}

	return $classes;
}
add_filter( 'body_class', 'sunflower_add_body_classes' );


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
		return false;
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
		$line         = trim( $line );
		$some_profile = explode( ';', $line );
		$class        = $some_profile[0] ?? false;
		$title        = $some_profile[1] ?? false;
		$url          = $some_profile[2] ?? false;

		if ( false === $url || empty( $url ) ) {
			continue;
		}

		$return .= sprintf(
			'<a href="%1$s" target="_blank" title="%3$s" class="social-media-profile" rel="me"><i class="%2$s"></i></a>',
			$url,
			$class,
			$title
		);
	}

	return $return;
}


