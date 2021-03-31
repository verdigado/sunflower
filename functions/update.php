<?php
/**/
// TEMP: Enable update check on every request. Normally you don't need this! This is for testing only!
//set_site_transient('update_themes', null);

// NOTE: All variables and functions will need to be prefixed properly to allow multiple plugins to be updated

/******************Change this*******************/
$api_url = 'https://sunflower-theme.de/updateserver/?rand=' . rand();
/************************************************/



/***********************Parent Theme**************/
if(function_exists('wp_get_theme')){
    $theme_data = wp_get_theme(get_option('template'));
    $theme_version = $theme_data->Version;  
} else {
    $theme_data = get_theme_data( TEMPLATEPATH . '/style.css');
    $theme_version = $theme_data['Version'];
}    
$theme_base = get_option('template');
/**************************************************/


add_filter('pre_set_site_transient_update_themes', 'sunflower_check_for_update');

function sunflower_check_for_update($checked_data) {
	global $wp_version, $theme_version, $theme_base, $api_url;

	$request = array(
		'version' => $theme_version,
		'url'	  => get_bloginfo('url')
	);
	// Start checking for an update
	$send_for_check = array(
		'body' => array(
			'request' => serialize($request)
		),
	);
	$raw_response = wp_remote_post($api_url, $send_for_check);

	if (!is_wp_error($raw_response) && ($raw_response['response']['code'] == 200))
		$response = unserialize($raw_response['body']);

	// Feed the update data into WP updater
	if (!empty($response)) 
		$checked_data->response[$theme_base] = $response;

	return $checked_data;
}



if (is_admin())
	$current = get_transient('update_themes');
?>