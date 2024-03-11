<?php
/**/
// TEMP: Enable update check on every request. Normally you don't need this! This is for testing only!
//set_site_transient('update_themes', null);

add_filter( 'update_themes_sunflower-theme.de', 'update_theme_sunflower', 10, 3 );

function update_theme_sunflower( $transient, $theme_data, $theme_slug )
{
	// Include an unmodified $wp_version.
	require ABSPATH . WPINC . '/version.php';
	$php_version = PHP_VERSION;

    $request = array(
        'version' => $theme_data['Version'],
		'php' => $php_version,
        'url' => get_bloginfo('url')
    );
    // Start checking for an update
    $send_for_check = array(
        'body' => array(
            'request' => serialize($request)
        ),
    );
    $raw_response = wp_remote_post( $theme_data['UpdateURI'], $send_for_check );

    if ( !is_wp_error($raw_response) && ($raw_response['response']['code'] == 200) ) {
        $response = unserialize($raw_response['body']);
    }

    // Feed the update data into WP updater
    if ( !empty( $response ) ) {
        $response['version'] = $response['new_version'] ?? $theme_data['Version'];
        $transient = $response;
    } else {
        // No update is available.
        $item = array(
            'theme'        => $theme_slug,
            'version'      => $theme_data['Version'],
            'new_version'  => $theme_data['Version'],
        );
        $transient = $item;
    }

    return $transient;
}
