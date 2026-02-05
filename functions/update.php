<?php
/**
 * Check for updates from Sunflower update server.
 *
 * @package sunflower
 */

/**
 * Send update request to update server specified in $theme_data['UpdateURI'].
 *
 * @param Array  $transient The theme update data with the latest details. Default false.
 * @param Array  $theme_data Theme data array.
 * @param string $theme_slug  The theme slug - 'sunflower' our case.
 */
function sunflower_update_theme( $transient, $theme_data, $theme_slug ) {
	// Include an unmodified $wp_version.
	require ABSPATH . WPINC . '/version.php';
	$php_version = PHP_VERSION;

	$sunflower_options = get_option( 'sunflower_first_steps_options' );
	$update_channel    = $sunflower_options['sunflower_update_channel'] ?? 'stable';
	$request           = array(
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
 */
function sunflower_maybe_run_theme_update() {

	$theme   = wp_get_theme();
	$version = $theme->get( 'Version' );
	$stored  = get_option( 'sunflower_theme_version' );

	if ( $stored === $version ) {
		return;
	}

	sunflower_run_update_tasks( $stored, $version );

	update_option( 'sunflower_theme_version', $version );
}
add_action( 'init', 'sunflower_maybe_run_theme_update' );


/**
 * Run update tasks depending on the from and to version.
 *
 * @param string $from_version The previous version.
 */
function sunflower_run_update_tasks( $from_version ) {

	if ( version_compare( $from_version, '2.2.15', '<' ) ) {

		$is_sunflower_events_enabled = sunflower_get_setting( 'sunflower_events_enabled' );
		if ( $is_sunflower_events_enabled ) {
			return;
		}

		$options = get_option( 'sunflower_events_options', array() );

		$options['sunflower_events_enabled'] = 1;
		update_option( 'sunflower_events_options', $options );

		// Flush rewrite rules later on custom post registration.
		update_option( 'sunflower_flush_rewrite_rules', 1 );
	}
}
