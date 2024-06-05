<?php
/**
 * Methods for the ical export.
 *
 * @package sunflower
 */

$sunflower_prodid   = wp_parse_url( (string) get_bloginfo( 'url' ), PHP_URL_HOST );
$sunflower_filename = preg_replace( '/[^a-zA-Z0-9]/', '-', (string) get_the_title() ) . '.ics';

$sunflower_event = '';
if ( ! defined( 'SUNFLOWER_ICAL_ALL_EVENTS' ) ) {
	$sunflower_event = sunflower_get_event_in_ics( $post ) . "\n";
} else {
	$sunflower_filename = preg_replace( '/[^a-zA-Z0-9]/', '-', $sunflower_prodid . '_events' ) . '.ics';
	$sunflower_posts    = sunflower_get_next_events();
	while ( $sunflower_posts->have_posts() ) {
		$sunflower_posts->the_post();
		$sunflower_event .= sunflower_get_event_in_ics( $post ) . "\n";
	}
}

$sunflower_ical = <<<"ICAL"
BEGIN:VCALENDAR\r
VERSION:2.0\r
PRODID:{$sunflower_prodid}\r
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
{$sunflower_event}END:VCALENDAR\r
ICAL;

header( 'Pragma: public' );
header( 'Expires: 0' );
header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
header( 'Cache-Control: private', false );
header( 'Content-Type: application/force-download' );
header( 'Content-Type: application/octet-stream' );
header( 'Content-Type: application/download' );
header( sprintf( 'Content-Disposition: attachment; filename="%s";', $sunflower_filename ) );
header( 'Content-Description: File Transfer' );
header( 'Content-Transfer-Encoding: binary' );

echo wp_kses_post( $sunflower_ical );
die();

/**
 * Get the date string for ical.
 *
 * @param int     $timestamp The unix timestamp.
 * @param boolean $with_time Show time or not.
 */
function sunflower_get_ical_date( $timestamp, $with_time = false ) {
	return gmdate( 'Ymd' . ( $with_time ? '\THis' : '' ), $timestamp );
}

/**
 * Get the ics metadata from sunflower event.
 *
 * @param WP_Post $post The post object.
 */
function sunflower_get_event_in_ics( $post ) {
	$_sunflower_event_from = get_post_meta( $post->ID, '_sunflower_event_from', true ) ?? false;
	$_sunflower_event_from = strToTime( (string) $_sunflower_event_from );

	$_sunflower_event_until = get_post_meta( $post->ID, '_sunflower_event_until', true ) ?? false;
	$_sunflower_event_until = strToTime( (string) $_sunflower_event_until );

	$_sunflower_event_whole_day       = get_post_meta( $post->ID, '_sunflower_event_whole_day', true ) ?? false;
	$_sunflower_event_location_name   = get_post_meta( $post->ID, '_sunflower_event_location_name', true ) ?? false;
	$_sunflower_event_location_street = get_post_meta( $post->ID, '_sunflower_event_location_street', true ) ?? false;
	$_sunflower_event_location_city   = get_post_meta( $post->ID, '_sunflower_event_location_city', true ) ?? false;

	$from  = sunflower_get_ical_date( $_sunflower_event_from, ! $_sunflower_event_whole_day );
	$until = ( $_sunflower_event_until ) ? sunflower_get_ical_date( $_sunflower_event_until, ! $_sunflower_event_whole_day ) : sunflower_get_ical_date( 3600 + $_sunflower_event_from, ! $_sunflower_event_whole_day );

	$now              = sunflower_get_ical_date( strToTime( 'now' ), true );
	$summary          = sunflower_textfold( 'SUMMARY:' . html_entity_decode( (string) get_the_title() ) );
	$sunflower_prodid = wp_parse_url( (string) get_bloginfo( 'url' ), PHP_URL_HOST );
	$uid              = md5( uniqid( wp_rand(), true ) ) . '@' . $sunflower_prodid;
	$description      = sunflower_textfold( 'DESCRIPTION:' . wp_strip_all_tags( get_the_content() ) );
	$location         = sunflower_textfold(
		'LOCATION:' . implode(
			', ',
			array_diff(
				array( $_sunflower_event_location_name, $_sunflower_event_location_street, $_sunflower_event_location_city ),
				array( false )
			)
		)
	);

	return <<<"ICALEVENT"
BEGIN:VEVENT\r
UID:{$uid}\r
{$location}\r
{$summary}\r
{$description}\r
CLASS:PUBLIC\r
DTSTART;TZID=Europe/Berlin:{$from}\r
DTEND;TZID=Europe/Berlin:{$until}\r
DTSTAMP:{$now}\r
END:VEVENT\r
ICALEVENT;
}

/**
 * This folds the text line for SUMMARY and DESCRIPTION
 *
 * Reference: https://www.kanzaki.com/docs/ical/text.html
 *
 * @param string $text The text to fold.
 */
function sunflower_textfold( $text ) {
	// Replace ",", ";" and "\".
	$searchandreplace = array(
		','  => '\,',
		';'  => '\;',
		'\\' => '\\\\',
	);
	$text             = strtr( $text, $searchandreplace );
	// Remove empty lines and replace all new lines with "\n\n".
	$text = preg_replace( '/\s*(\n)/', '\n\n', $text );
	// Fold all lines after 75 signs.
	return rtrim( chunk_split( (string) $text, 74, "\r\n " ), "\r\n " );
}
