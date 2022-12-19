<?php

$proid    = parse_url( get_bloginfo( 'url' ), PHP_URL_HOST );
$filename = preg_replace( '/[^a-zA-Z0-9]/', '-', get_the_title() ) . '.ics';

$event = '';
if ( ! defined( 'SUNFLOWER_ICAL_ALL_EVENTS' ) ) {
	$event = sunflower_getEventInIcs( $post ) . "\n";
} else {
	$filename = preg_replace( '/[^a-zA-Z0-9]/', '-', $proid . '_events' ) . '.ics';
	$posts    = sunflower_get_next_events();
	while ( $posts->have_posts() ) {
		$posts->the_post();
		$event .= sunflower_getEventInIcs( $post ) . "\n";
	}
}

$ical = <<<"ICAL"
BEGIN:VCALENDAR\r
VERSION:2.0\r
PRODID:$proid\r
METHOD:PUBLISH\r
BEGIN:VTIMEZONE\r
TZID:Europe/Berlin\r
X-LIC-LOCATION:Europe/Berlin\r
BEGIN:DAYLIGHT\r
TZOFFSETFROM:+0100\r
TZOFFSETTO:+0200\r
TZNAME:CEST\r
DTSTART:19700329T020000\r
RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=-1SU;BYMONTH=3\r
END:DAYLIGHT\r
BEGIN:STANDARD\r
TZOFFSETFROM:+0200\r
TZOFFSETTO:+0100\r
TZNAME:CET\r
DTSTART:19701025T030000\r
RRULE:FREQ=YEARLY;INTERVAL=1;BYDAY=-1SU;BYMONTH=10\r
END:STANDARD\r
END:VTIMEZONE\r
{$event}END:VCALENDAR\r
ICAL;

header( 'Pragma: public' );
header( 'Expires: 0' );
header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
header( 'Cache-Control: private', false );
header( 'Content-Type: application/force-download' );
header( 'Content-Type: application/octet-stream' );
header( 'Content-Type: application/download' );
header( "Content-Disposition: attachment; filename=\"{$filename}\";" );
header( 'Content-Description: File Transfer' );
header( 'Content-Transfer-Encoding: binary' );

echo $ical;
die();

function sunflower_getIcalDate( $timestamp, $withTime = false ) {
	return date( 'Ymd' . ( $withTime ? '\THis' : '' ), $timestamp );
}

function sunflower_getEventInIcs( $post ) {
	 $_sunflower_event_from           = @get_post_meta( $post->ID, '_sunflower_event_from' )[0] ?: false;
	$_sunflower_event_from            = strToTime( $_sunflower_event_from );
	$_sunflower_event_until           = @get_post_meta( $post->ID, '_sunflower_event_until' )[0] ?: false;
	$_sunflower_event_until           = strToTime( $_sunflower_event_until );
	$_sunflower_event_whole_day       = @get_post_meta( $post->ID, '_sunflower_event_whole_day' )[0] ?: false;
	$_sunflower_event_location_name   = @get_post_meta( $post->ID, '_sunflower_event_location_name' )[0] ?: false;
	$_sunflower_event_location_street = @get_post_meta( $post->ID, '_sunflower_event_location_street' )[0] ?: false;
	$_sunflower_event_location_city   = @get_post_meta( $post->ID, '_sunflower_event_location_city' )[0] ?: false;

	$from  = sunflower_getIcalDate( $_sunflower_event_from, ! $_sunflower_event_whole_day );
	$until = ( $_sunflower_event_until ) ? sunflower_getIcalDate( $_sunflower_event_until, ! $_sunflower_event_whole_day ) : sunflower_getIcalDate( 3600 + $_sunflower_event_from, ! $_sunflower_event_whole_day );

	$now         = sunflower_getIcalDate( strToTime( 'now' ), true );
	$summary     = get_the_title();
	$proid       = parse_url( get_bloginfo( 'url' ), PHP_URL_HOST );
	$uid         = md5( uniqid( mt_rand(), true ) ) . '@' . $proid;
	$description = get_the_excerpt();
	$location    = join(
		', ',
		array_diff(
			array(
				$_sunflower_event_location_name,
				$_sunflower_event_location_street,
				$_sunflower_event_location_city,
			),
			array( false )
		)
	);

	$event = <<<"ICALEVENT"
BEGIN:VEVENT\r
UID:$uid\r
LOCATION:$location\r
SUMMARY:$summary\r
DESCRIPTION:$description\r
CLASS:PUBLIC\r
DTSTART:$from\r
DTEND:$until\r
DTSTAMP:$now\r
END:VEVENT\r
ICALEVENT;

	return $event;
}
