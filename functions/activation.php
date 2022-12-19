<?php
add_action( 'after_switch_theme', 'sunflower_activate_theme', 10, 2 );


function sunflower_activate_theme( $old_theme_name, $old_theme = false ) {
	sunflower_import_widgets();
	sunflower_import_events();
}

function sunflower_import_widgets() {
	// check for theme_mods_urwahl3000
	$options = get_option( 'theme_mods_urwahl3000' );
	if ( empty( $options['sidebars_widgets'] ) ) {
		return;
	}

	$sidebars_widgets = array_merge( (array) $options['sidebars_widgets']['data']['infospalte'], (array) $options['sidebars_widgets']['data']['fussleist'] );

	$option = get_option( 'sidebars_widgets' );
	if ( empty( $option['sidebar-1'] ) ) {
		$option['sidebar-1'] = $sidebars_widgets;
		update_option( 'sidebars_widgets', $option );
	}
}

function sunflower_import_events() {
	$events = new WP_Query(
		array(
			'post_type' => 'termine',
		)
	);

	foreach ( $events->posts as $post ) {
		$meta           = get_post_meta( $post->ID );
		$originalPostId = $post->ID;

		if ( isset( $meta['_sunflower_copied'][0] ) ) {
			continue;
		}

		$post->ID        = 0;
		$post->post_type = 'sunflower_event';

		if ( $meta['_secretevent'][0] ) {
			$post->post_status = 'draft';
		}

		$id = wp_insert_post( (array) $post, true );
		if ( ! is_int( $id ) ) {
			echo 'Could not copy post';
			return false;
		}

		update_post_meta( $originalPostId, '_sunflower_copied', true );

		update_post_meta( $id, '_sunflower_event_location_city', $meta['_geostadt'][0] );
		update_post_meta( $id, '_sunflower_event_location_name', $meta['_geoshow'][0] );
		update_post_meta( $id, '_sunflower_event_lat', $meta['_lat'][0] );
		update_post_meta( $id, '_sunflower_event_lon', $meta['_lon'][0] );
		update_post_meta( $id, '_sunflower_event_zoom', $meta['_zoom'][0] );
		update_post_meta( $id, '_sunflower_event_organizer', $meta['_veranstalter'][0] );
		update_post_meta( $id, '_sunflower_event_organizer_url', $meta['_veranstalterlnk'][0] );

		update_post_meta( $id, '_sunflower_event_from', germanDate2intDate( $meta['_wpcal_from'][0] ) );
		update_post_meta( $id, '_sunflower_event_until', germanDate2intDate( $meta['_bis'][0] ) );
	}
}
