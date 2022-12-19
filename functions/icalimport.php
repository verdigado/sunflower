<?php
// phpcs:disable Generic.Arrays.DisallowLongArraySyntax

require_once WP_CONTENT_DIR . '/themes/sunflower/assets/vndr/johngrogg/ics-parser/src/ICal/Event.php';
require_once WP_CONTENT_DIR . '/themes/sunflower/assets/vndr/johngrogg/ics-parser/src/ICal/ICal.php';

use ICal\ICal;

function sunflower_icalimport( $url = false, $auto_categories = false ) {
	try {
		$ical = new ICal(
			'ICal.ics',
			array(
				'defaultSpan'                 => 1,     // Default value
				'defaultTimeZone'             => 'Europe/Berlin',
				'defaultWeekStart'            => 'MO',  // Default value
				'disableCharacterReplacement' => false, // Default value
				'filterDaysAfter'             => null,  // Default value
				'filterDaysBefore'            => null,  // Default value
				'skipRecurrence'              => false, // Default value
			)
		);

		$ical->initUrl( $url, $username = null, $password = null, $userAgent = null );
	} catch ( \Exception $e ) {
		return false;
	}

	$time_range_history   = sunflower_get_constant( 'SUNFLOWER_EVENT_TIME_RANGE_BACK' ) ?: '0 months';
	$time_range_future    = sunflower_get_constant( 'SUNFLOWER_EVENT_TIME_RANGE' ) ?: '6 months';
	$recurring_events_max = (int) sunflower_get_constant( 'SUNFLOWER_EVENT_RECURRING_EVENTS' ) ?: 10;

	// $events = $ical->eventsFromInterval($time_range_future);
	$events = $ical->eventsFromRange(
		strftime( '%F', strtotime( '-' . $time_range_history ) ),
		strftime( '%F', strtotime( $time_range_future ) )
	);

	$updated_events         = 0;
	$ids_from_remote        = array();
	$count_recurring_events = array();

	foreach ( $events as $event ) {
		$uid = $event->uid;

		if ( isset( $event->rrule ) ) {
			if ( isset( $count_recurring_events[ $uid ] ) ) {
				$count_recurring_events[ $uid ]++;
			} else {
				$count_recurring_events[ $uid ] = 1;
			}

			if ( $count_recurring_events[ $uid ] > $recurring_events_max ) {
				continue;
			}
			$uid .= '_' . $event->dtstart;
		}

		// is this event already imported
		$is_imported = sunflower_get_event_by_uid( $uid );
		$wp_id       = 0;
		if ( $is_imported->have_posts() ) {
			$is_imported->the_post();
			$wp_id = get_the_ID();
			$updated_events++;
		}

		$post = array(
			'ID'           => $wp_id,
			'post_type'    => 'sunflower_event',
			'post_title'   => $event->summary,
			'post_content' => sprintf( '<!-- wp:paragraph -->%s<!-- /wp:paragraph -->', nl2br( $event->description ) ),
			'post_status'  => 'publish',

		);
		$id = wp_insert_post( (array) $post, true );
		if ( ! is_int( $id ) ) {
			echo 'Could not copy post';
			return false;
		}

		$ids_from_remote[] = $id;

		// use original date if no hours are given, else timezoned
		$startdate = ( strlen( $event->dtstart ) == 8 ) ? $event->dtstart : $event->dtstart_tz;
		$enddate   = ( strlen( $event->dtend ) == 8 ) ? $event->dtend : $event->dtend_tz;

		if ( get_sunflower_setting( 'sunflower_fix_time_zone_error' ) ) {
			$startdate .= 'Z';
			$enddate   .= 'Z';
		}

		update_post_meta( $id, '_sunflower_event_from', date( 'Y-m-d H:i', $ical->iCalDateToUnixTimestamp( $startdate ) ) );
		update_post_meta( $id, '_sunflower_event_until', date( 'Y-m-d H:i', $ical->iCalDateToUnixTimestamp( $enddate ) ) );
		update_post_meta( $id, '_sunflower_event_uid', $uid );

		if ( isset( $event->location ) ) {
			update_post_meta( $id, '_sunflower_event_location_name', $event->location );
			$coordinates = sunflower_geocode( $event->location );
			if ( $coordinates ) {
				list($lon, $lat) = $coordinates;
				update_post_meta( $id, '_sunflower_event_lat', $lat );
				update_post_meta( $id, '_sunflower_event_lon', $lon );
				$zoom = sunflower_get_constant( 'SUNFLOWER_EVENT_IMPORTED_ZOOM' ) ?: 12;
				update_post_meta( $id, '_sunflower_event_zoom', $zoom );
			}
		}

		$categories  = ( isset( $event->categories ) ) ? $event->categories : '';
		$categories .= ( $auto_categories ) ? ',' . $auto_categories : '';

		if ( $categories ) {
			wp_set_post_terms( $id, $categories, 'sunflower_event_tag' );
		}
	}

	return array( $ids_from_remote, count( $events ) - $updated_events, $updated_events );

}

function sunflower_get_event_by_uid( $uid ) {
	return new WP_Query(
		array(
			// 'paged' => $paged,
			// 'nopaging'        => true,
			'post_type'  => 'sunflower_event',
			'meta_key'   => '_sunflower_event_uid',
			'orderby'    => 'meta_value',
			'meta_query' => array(
				array(
					'key'     => '_sunflower_event_uid',
					'value'   => $uid,
					'compare' => '=',
				),
			),
		)
	);
}

function sunflower_get_events_having_uid() {
	$events_with_uid = new WP_Query(
		array(
			// 'paged' => $paged,
			'nopaging'   => true,
			'post_type'  => 'sunflower_event',
			'meta_key'   => '_sunflower_event_uid',
			'orderby'    => 'meta_value',
			'meta_query' => array(
				array(
					'key'     => '_sunflower_event_uid',
					'compare' => 'EXISTS',
				),
			),
		)
	);

	$ids = array();
	while ( $events_with_uid->have_posts() ) {
		$events_with_uid->the_post();
		$ids[] = get_the_ID();
	}

	return $ids;
}

add_action( 'init', 'sunflower_import_icals' );
function sunflower_import_icals( $force = false ) {

	if ( ! $force && get_transient( 'sunflower_ical_imported' ) ) {
		return false;
	}

	if ( ! get_sunflower_setting( 'sunflower_ical_urls' ) ) {
		return false;
	}

	$import_every_n_hour = sunflower_get_constant( 'SUNFLOWER_EVENT_IMPORT_EVERY_N_HOUR' ) ?: 3;
	set_transient( 'sunflower_ical_imported', 1, $import_every_n_hour * 3600 );

	$lines = explode( "\n", get_sunflower_setting( 'sunflower_ical_urls' ) );

	$ids_from_remote = array();
	foreach ( $lines as $line ) {
		$info = explode( ';', $line );

		$url             = trim( $info[0] );
		$auto_categories = ( isset( $info[1] ) ) ? $info[1] : false;

		if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
			continue;
		}

		$response        = sunflower_icalimport( $url, $auto_categories );
		$ids_from_remote = array_merge( $ids_from_remote, $response[0] );
	}

	$deleted_on_remote = array_diff( sunflower_get_events_having_uid(), $ids_from_remote );

	foreach ( $deleted_on_remote as $to_be_deleted ) {
		wp_delete_post( $to_be_deleted );
	}
}


function sunflower_geocode( $location ) {
	static $i  = 0;
	$transient = sprintf( 'sunflower_geocache_%s', $location );

	if ( $cached = get_transient( $transient ) ) {
		return $cached;
	}

	if ( $i > 3 ) {
		// download 3 geodata per import
		return false;
	}

	$url     = sprintf( 'https://nominatim.openstreetmap.org/search?q=%s&format=geocodejson', urlencode( $location ) );
	$opts    = array(
		'http' => array(
			'method' => 'GET',
			'header' => "Accept-language: en\r\n" .
				"user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.96 Safari/537.36\r\n",
		),
	);
	$context = stream_context_create( $opts );

	$json = json_decode( file_get_contents( $url, false, $context ) );

	if ( isset( $json->features[0] ) ) {
		$lonlat = $json->features[0]->geometry->coordinates;
	} else {
		$lonlat = false;
	}

	$i++;

	set_transient( $transient, $lonlat );

	return $lonlat;
}
