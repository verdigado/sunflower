<?php
/**
 * Check for updates from Sunflower update server.
 *
 * @package Sunflower 26
 */

/**
 * Send update request to update server specified in $theme_data['UpdateURI'].
 *
 * @param Array  $transient The theme update data with the latest details. Default false.
 * @param Array  $theme_data Theme data array.
 * @param string $theme_slug  The theme slug - 'sunflower26' our case.
 */
function sunflower_update_theme( $transient, $theme_data, $theme_slug ) {

	if ( 'sunflower26' !== $theme_slug ) {
		return $transient;
	}

	// Include an unmodified $wp_version.
	require ABSPATH . WPINC . '/version.php';
	$php_version = PHP_VERSION;

	$sunflower_options = get_option( 'sunflower_first_steps_options' );
	$update_channel    = $sunflower_options['sunflower_update_channel'] ?? 'stable';
	$request           = array(
		'slug'           => $theme_slug,
		'version'        => $theme_data['Version'],
		'php'            => $php_version,
		'url'            => get_bloginfo( 'url' ),
		'update_channel' => $update_channel,
	);
	// Start checking for an update.
	$send_for_check = array(
		'body' => array(
			'request' => serialize( $request ), // phpcs:ignore
		),
	);
	$raw_response   = wp_remote_post( $theme_data['UpdateURI'], $send_for_check );

	if ( ! is_wp_error( $raw_response ) && ( 200 === $raw_response['response']['code'] ) ) {
		$response = unserialize( $raw_response['body'] ); // phpcs:ignore
	}

	// Feed the update data into WP updater.
	if ( ! empty( $response ) ) {
		$response['version'] = $response['new_version'] ?? $theme_data['Version'];
		return $response;
	}

	// No update is available.
	$item = array(
		'theme'       => $theme_slug,
		'version'     => $theme_data['Version'],
		'new_version' => $theme_data['Version'],
	);

	return $item;
}

add_filter( 'update_themes_sunflower-theme.de', 'sunflower_update_theme', 10, 3 );

/**
 * Run update tasks if the theme version has changed.
 *
 * @param WP_Upgrader $upgrader The upgrader instance.
 * @param array       $hook_extra Extra data about the update process.
 */
function sunflower_maybe_run_theme_update( $upgrader, $hook_extra ) {

	// Only consider theme updates, not plugin updates or core updates.
	if ( empty( $hook_extra['type'] ) || 'theme' !== $hook_extra['type'] ||
		empty( $hook_extra['action'] ) || 'update' !== $hook_extra['action'] ||
		empty( $hook_extra['themes'] ) ) {
		return;
	}

	$parent_theme_dir = get_template();

	// Return early if the updated theme is not our parent theme.
	if ( ! in_array( $parent_theme_dir, $hook_extra['themes'], true ) ) {
		return;
	}

	$parent_theme   = wp_get_theme( $parent_theme_dir );
	$parent_version = $parent_theme->get( 'Version' );
	$stored_version = get_option( 'sunflower_theme_version' );

	if ( $stored_version === $parent_version ) {
		return;
	}

	sunflower_run_update_tasks( $stored_version, $parent_version );

	update_option( 'sunflower_theme_version', $parent_version );
}
add_action( 'upgrader_process_complete', 'sunflower_maybe_run_theme_update', 10, 2 );


/**
 * Execute a callback for each site in a multisite, or just once for single site.
 *
 * @param callable $callback The function to execute per site.
 */
function sunflower_for_each_site( callable $callback ) {
	if ( is_multisite() ) {
		$sites = get_sites();
		foreach ( $sites as $site ) {
			switch_to_blog( $site->blog_id );
			$callback();
			restore_current_blog();
		}
	} else {
		$callback();
	}
}

/**
 * Run update tasks depending on the from and to version.
 *
 * @param string $from_version The previous version.
 */
function sunflower_run_update_tasks( $from_version = '' ) {

	if ( empty( $from_version ) ) {
		return;
	}

	// Option sunflower_events_enabled was added in 2.2.15.
	if ( version_compare( $from_version, '2.2.15', '<' ) ) {

		$is_sunflower_events_enabled = sunflower_get_setting( 'sunflower_events_enabled' );
		if ( $is_sunflower_events_enabled ) {
			return;
		}

		$options = get_option( 'sunflower_events_options', array() );
		if ( ! is_array( $options ) ) {
			$options = array();
		}

		$options['sunflower_events_enabled'] = 1;
		update_option( 'sunflower_events_options', $options );
		update_option( 'sunflower_flush_rewrite_rules', 1 );
	}

	// Set default post image format to 'flexible'.
	if ( version_compare( $from_version, '3.0.0', '<' ) ) {
		$sunflower_options = get_option( 'sunflower_options' );
		if ( ! is_array( $sunflower_options ) ) {
			$sunflower_options = array();
		}
		$sunflower_options['sunflower_post_image_format'] = 'flexible';
		update_option( 'sunflower_options', $sunflower_options );
	}

	// Comment out Twitter/X from social media profiles.
	if ( version_compare( $from_version, '3.0.2', '<' ) ) {
		sunflower_for_each_site( 'sunflower_comment_out_twitter_profiles' );
	}
}

/**
 * Comment out Twitter/X social media profiles for the current site.
 */
function sunflower_comment_out_twitter_profiles() {
	$options = get_option( 'sunflower_social_media_options' );
	if ( ! is_array( $options ) ) {
		$options = array();
	}

	$lines     = explode( "\n", (string) ( $options['sunflower_social_media_profiles'] ?? '' ) );
	$new_lines = array();

	foreach ( $lines as $line ) {
		$line         = trim( $line );
		$some_profile = explode( ';', $line );
		$class        = $some_profile[0] ?? '';
		$url          = $some_profile[2] ?? '';

		if ( ! str_starts_with( $line, '#' )
			&& ( str_contains( $class, 'twitter' )
				|| str_contains( $class, 'x-twitter' )
				|| str_contains( $url, 'twitter.com' )
				|| str_contains( $url, 'x.com' ) )
		) {
			$line = '# ' . $line;
		}

		$new_lines[] = $line;
	}

	$options['sunflower_social_media_profiles'] = implode( "\n", $new_lines );
	update_option( 'sunflower_social_media_options', $options );
}
