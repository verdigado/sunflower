<?php
/**
 * Methods for importing ics calendar files.
 *
 * @package sunflower
 */

/**
 * Load class namespaces.
 */
require WP_CONTENT_DIR . '/themes/sunflower/lib/vendor/autoload.php';

use Sabre\VObject\ParseException;
use Sabre\VObject\Reader;

/**
 * The import function.
 *
 * @param string|boolean $url The URL to the ics-file.
 * @param boolean        $auto_categories Additional categories to add to every imported event.
 */
function sunflower_icalimport( $url = false, $auto_categories = false ) {
	try {
		$vcalendar = Reader::read(
			wp_remote_retrieve_body(
				wp_remote_get( ( $url ) ),
				Reader::OPTION_FORGIVING
			)
		);
	} catch ( ParseException $parse_exception ) {
		// Rethrow the exception to abstract the type.
		return $parse_exception->getMessage();
	}

	try {
		// If timezone_string contains a valid timezone string, we create a timezone object of it.
		$timezone = new \DateTimeZone( get_option( 'timezone_string' ) );
	} catch ( Exception $e ) {
		// Otherwise we fall back to 'Europe/Berlin'.
		$timezone = new \DateTimeZone( 'Europe/Berlin' );
	}

	$time_range_history   = sunflower_get_constant( 'SUNFLOWER_EVENT_TIME_RANGE_BACK' ) ? sunflower_get_constant( 'SUNFLOWER_EVENT_TIME_RANGE_BACK' ) : '0 months';
	$time_range_future    = sunflower_get_constant( 'SUNFLOWER_EVENT_TIME_RANGE' ) ? sunflower_get_constant( 'SUNFLOWER_EVENT_TIME_RANGE' ) : '6 months';
	$recurring_events_max = (int) sunflower_get_constant( 'SUNFLOWER_EVENT_RECURRING_EVENTS' ) ? (int) sunflower_get_constant( 'SUNFLOWER_EVENT_RECURRING_EVENTS' ) : 10;

	$time_range_start = new \DateTime();
	$time_range_start->setTimestamp( strtotime( '-' . $time_range_history ) );

	$time_range_stop = new \DateTime();
	$time_range_stop->setTimestamp( strtotime( (string) $time_range_future ) );

	$timezone_fix = null;
	if ( sunflower_get_setting( 'sunflower_fix_time_zone_error' ) ) {
		$timezone_fix = $timezone;
	}

	// expand RRULE events to new vCalendar which has all events in the given time range.
	$new_vcalendar = $vcalendar->expand( $time_range_start, $time_range_stop, $timezone_fix );

	$all_events = array();
	if ( is_iterable( $new_vcalendar->VEVENT ) ) { // phpcs:ignore
		foreach ( $new_vcalendar->VEVENT as $event ) { // phpcs:ignore
			// Limit to events in the given time range.
			if ( $event->isInTimeRange( $time_range_start, $time_range_stop ) ) {
				$all_events[] = $event;
			}
		}
	}

	$updated_events         = 0;
	$ids_from_remote        = array();
	$count_recurring_events = array();

	foreach ( $all_events as $event ) {
		$uid = $event->UID->getValue(); // phpcs:ignore

		// modified instances of a recurring event, RECURRENCE-ID is set but no RRULE.
		if ( isset( $event->RRULE ) || isset( $event->{'RECURRENCE-ID'} ) ) { // phpcs:ignore
			if ( isset( $count_recurring_events[ $uid ] ) ) {
				++$count_recurring_events[ $uid ];
			} else {
				$count_recurring_events[ $uid ] = 1;
			}

			if ( $count_recurring_events[ $uid ] > $recurring_events_max ) {
				continue;
			}

			$uid .= '_' . $event->DTSTART->getValue(); // phpcs:ignore
		}

		// Is this event already imported.
		$is_imported = sunflower_get_event_by_uid( $uid );
		$wp_id       = 0;
		if ( $is_imported->have_posts() ) {
			$is_imported->the_post();
			$wp_id = get_the_ID();
			++$updated_events;
		}

		$post_content = sprintf( '<!-- wp:paragraph --><p>%s</p><!-- /wp:paragraph -->', nl2br( (string) $event->DESCRIPTION ) ); // phpcs:ignore

		if ( isset( $event->URL ) && filter_var( (string) $event->URL, FILTER_VALIDATE_URL ) ) { // phpcs:ignore
			$post_content .= sprintf( '<!-- wp:paragraph --><p>%1$s: <a href="%1$s" target="_blank">%1$s</a></p><!-- /wp:paragraph -->', __( 'More Information', 'sunflower' ), (string) $event->URL ); // phpcs:ignore
		}

		$post = array(
			'ID'           => $wp_id,
			'post_type'    => 'sunflower_event',
			'post_title'   => $event->SUMMARY->getValue(), // phpcs:ignore
			'post_content' => $post_content,
			'post_status'  => 'publish',
		);
		$id   = wp_insert_post( (array) $post, true );
		if ( ! is_int( $id ) ) {
			echo 'Could not copy post';
			return false;
		}

		// Save all event post ids from imported ics ressources.
		$ids_from_remote[] = $id;

		// Write start and end time to event post metadata.
		if ( $event->DTSTART instanceof \Sabre\VObject\Property\ICalendar\Date || // phpcs:ignore
			( $event->DTSTART->getDateTime()->format( 'H:i' ) == '00:00' && $event->DTEND->getDateTime()->format( 'H:i' ) == '00:00' ) // phpcs:ignore
		) {
			update_post_meta( $id, '_sunflower_event_whole_day', 'checked' );
			update_post_meta( $id, '_sunflower_event_from', $event->DTSTART->getDateTime()->format( 'Y-m-d' ) ); // phpcs:ignore
			update_post_meta( $id, '_sunflower_event_until', $event->DTEND?->getDateTime()->format( 'Y-m-d' ) ); // phpcs:ignore
		} else {
			update_post_meta( $id, '_sunflower_event_from', $event->DTSTART->getDateTime( $timezone_fix )->setTimezone( $timezone )->format( 'Y-m-d H:i' ) ); // phpcs:ignore
			update_post_meta( $id, '_sunflower_event_until', $event->DTEND?->getDateTime( $timezone_fix )->setTimezone( $timezone )->format( 'Y-m-d H:i' ) ); // phpcs:ignore
		}

		update_post_meta( $id, '_sunflower_event_uid', $uid );

		if ( isset( $event->LOCATION ) ) { // phpcs:ignore
			update_post_meta( $id, '_sunflower_event_location_name', (string) $event->LOCATION ); // phpcs:ignore

			if ( ! filter_var( (string) $event->LOCATION, FILTER_VALIDATE_URL ) ) { // phpcs:ignore
				$coordinates = sunflower_geocode( (string) $event->LOCATION ); // phpcs:ignore
				if ( $coordinates ) {
					[$lon, $lat] = $coordinates;
					update_post_meta( $id, '_sunflower_event_lat', $lat );
					update_post_meta( $id, '_sunflower_event_lon', $lon );
					$zoom = sunflower_get_constant( 'SUNFLOWER_EVENT_IMPORTED_ZOOM' ) ? sunflower_get_constant( 'SUNFLOWER_EVENT_IMPORTED_ZOOM' ) : 12;
					update_post_meta( $id, '_sunflower_event_zoom', $zoom );
				}
			}
		}

		$categories  = $event->CATEGORIES ?? ''; // phpcs:ignore
		$categories .= ( $auto_categories ) ? ',' . $auto_categories : '';
		if ( '' === $categories ) {
			continue;
		}

		if ( '0' === $categories ) {
			continue;
		}

		wp_set_post_terms( $id, $categories, 'sunflower_event_tag' );
	}

	return array( $ids_from_remote, count( $all_events ) - $updated_events, $updated_events );
}

/**
 * Get post type "sunflower_event" for given uid.
 *
 * @param int $uid The uid of the sunflower event.
 */
function sunflower_get_event_by_uid( $uid ) {
	return new WP_Query(
		array(
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

/**
 * Get all sunflower events with an uid set.
 */
function sunflower_get_events_having_uid() {
	$events_with_uid = new WP_Query(
		array(
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

/**
 * Run the import job.
 *
 * @param boolean $force Force the import even if transient time is not past.
 */
function sunflower_import_icals( $force = false ) {
	if ( ! $force && get_transient( 'sunflower_ical_imported' ) ) {
		return false;
	}

	if ( ! sunflower_get_setting( 'sunflower_ical_urls' ) ) {
		return false;
	}

	$import_every_n_hour = sunflower_get_constant( 'SUNFLOWER_EVENT_IMPORT_EVERY_N_HOUR' ) ? sunflower_get_constant( 'SUNFLOWER_EVENT_IMPORT_EVERY_N_HOUR' ) : 3;
	set_transient( 'sunflower_ical_imported', 1, $import_every_n_hour * 3600 );

	$lines = explode( "\n", (string) sunflower_get_setting( 'sunflower_ical_urls' ) );

	$ids_from_remote = array();
	foreach ( $lines as $line ) {
		$info = explode( ';', $line );

		$url             = trim( $info[0] );
		$auto_categories = $info[1] ?? false;

		if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
			continue;
		}

		$response = sunflower_icalimport( $url, $auto_categories );
		if ( ! empty( $response ) && is_array( $response ) && is_array( $response[0] ) ) {
			$ids_from_remote = array_merge( $ids_from_remote, $response[0] );
		}
	}

	$deleted_on_remote = array_diff( sunflower_get_events_having_uid(), $ids_from_remote );

	foreach ( $deleted_on_remote as $to_be_deleted ) {
		wp_delete_post( $to_be_deleted );
	}
}

add_action( 'init', 'sunflower_import_icals' );


/**
 * Make georeferencing via nominatim for unknown locations.
 *
 * @param string $location The location as human readable string.
 */
function sunflower_geocode( $location ) {
	static $i  = 0;
	$transient = sprintf( 'sunflower_geocache_%s', $location );

	$cached = get_transient( $transient );
	if ( $cached ) {
		return $cached;
	}

	if ( $i > 3 ) {
		// Download 3 geodata per import.
		return false;
	}

	$url     = sprintf( 'https://nominatim.openstreetmap.org/search?q=%s&format=geocodejson', rawurlencode( (string) $location ) );
	$opts    = array(
		'http' => array(
			'method' => 'GET',
			'header' => "Accept-language: en\r\n" .
							"user-agent: Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.96 Safari/537.36\r\n",
		),
	);
	$context = stream_context_create( $opts );

	$json = json_decode( wp_remote_retrieve_body( wp_remote_get( $url, false, $context ) ) );

	$lonlat = isset( $json->features[0] ) ? $json->features[0]->geometry->coordinates : false;

	++$i;

	set_transient( $transient, $lonlat );

	return $lonlat;
}
