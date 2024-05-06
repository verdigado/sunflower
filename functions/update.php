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

	$request = array(
		'version' => $theme_data['Version'],
		'php'     => $php_version,
		'url'     => get_bloginfo( 'url' ),
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
