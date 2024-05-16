<?php
/**
 * Jobs doing on activation of the sunflower theme.
 *
 * @package sunflower
 */

/**
 * Jobs run after activation of sunflower theme.
 */
function sunflower_activate_theme() {
	sunflower_import_widgets();
	sunflower_import_events();
}

add_action( 'after_switch_theme', 'sunflower_activate_theme', 10, 2 );

/**
 * Import widgets from urwahl3000 theme.
 */
function sunflower_import_widgets() {
	// Check for theme_mods_urwahl3000.
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

/**
 * Import events from wpcalendar
 */
function sunflower_import_events() {
	$events = new WP_Query(
		array(
			'post_type'      => 'termine',
			'posts_per_page' => '-1',
		)
	);

	foreach ( $events->posts as $post ) {
		$meta                       = get_post_meta( $post->ID );
		$sunflower_original_post_id = $post->ID;

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

		update_post_meta( $sunflower_original_post_id, '_sunflower_copied', true );

		update_post_meta( $id, '_sunflower_event_location_city', $meta['_geostadt'][0] );
		update_post_meta( $id, '_sunflower_event_location_name', $meta['_geoshow'][0] );
		update_post_meta( $id, '_sunflower_event_lat', $meta['_lat'][0] );
		update_post_meta( $id, '_sunflower_event_lon', $meta['_lon'][0] );
		update_post_meta( $id, '_sunflower_event_zoom', $meta['_zoom'][0] );
		update_post_meta( $id, '_sunflower_event_organizer', $meta['_veranstalter'][0] );
		update_post_meta( $id, '_sunflower_event_organizer_url', $meta['_veranstalterlnk'][0] );

		update_post_meta( $id, '_sunflower_event_from', sunflower_german_date2int_date( $meta['_wpcal_from'][0] ) );
		update_post_meta( $id, '_sunflower_event_until', sunflower_german_date2int_date( $meta['_bis'][0] ) );

		if ( $meta['_thumbnail_id'][0] ) {
			update_post_meta( $id, '_thumbnail_id', $meta['_thumbnail_id'][0] );
		}
	}
}
