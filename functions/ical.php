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
	$sunflower_event_tags = array();
	if ( isset( $_GET['sunflower_tag'] ) && ! empty( $_GET['sunflower_tag'] ) ) { // phpcs:ignore
		$sunflower_event_tags = explode( ',', $_GET['sunflower_tag'] ); // phpcs:ignore
	}
	$sunflower_event_tags_str = implode( ',', $sunflower_event_tags );
	$sunflower_filename       = preg_replace( '/[^a-zA-Z0-9]/', '-', $sunflower_prodid . '_events' . ( ( $sunflower_event_tags_str ) ? '-' . $sunflower_event_tags_str : '' ) ) . '.ics';
	$sunflower_posts          = sunflower_get_next_events( -1, $sunflower_event_tags );
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
header( 'Content-Type: text/calendar; charset=utf-8' );
header( sprintf( 'Content-Disposition: attachment; filename="%s";', $sunflower_filename ) );
header( 'Content-Description: File Transfer' );
header( 'Content-Transfer-Encoding: binary' );

// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
echo $sunflower_ical;
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

	$_sunflower_event_url = get_post_meta( $post->ID, '_sunflower_event_url', true ) ?? false;

	$from  = sunflower_get_ical_date( $_sunflower_event_from, ! $_sunflower_event_whole_day );
	$until = ( $_sunflower_event_until ) ? sunflower_get_ical_date( $_sunflower_event_until, ! $_sunflower_event_whole_day ) : sunflower_get_ical_date( 3600 + $_sunflower_event_from, ! $_sunflower_event_whole_day );

	$now              = sunflower_get_ical_date( strToTime( 'now' ), true );
	$summary          = sunflower_textfold( 'SUMMARY:' . html_entity_decode( (string) get_the_title() ) );
	$sunflower_prodid = wp_parse_url( (string) get_bloginfo( 'url' ), PHP_URL_HOST );
	$uid              = md5( uniqid( wp_rand(), true ) ) . '@' . $sunflower_prodid;

	$html = get_the_content( null, false, $post );
	$html = strip_shortcodes( $html );
	$html = do_blocks( $html );
	$html = wp_kses_post( $html );

	$altrep = rawurlencode( $html );

	$plain = wp_strip_all_tags( $html );
	$plain = html_entity_decode( $plain, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	$plain = sunflower_replace( $plain );

	$description = sunflower_textfold(
		'DESCRIPTION;ALTREP="data:text/html,' . $altrep . '":' . $plain
	);
	$location    = sunflower_textfold(
		'LOCATION:' . implode(
			', ',
			array_diff(
				array( $_sunflower_event_location_name, $_sunflower_event_location_street, $_sunflower_event_location_city ),
				array( false )
			)
		)
	);
	if ( $_sunflower_event_url ) {
		$permalink = $_sunflower_event_url;
	} else {
		$permalink = get_permalink( $post->ID );
	}
	$url = '';
	if ( filter_var( $permalink, FILTER_VALIDATE_URL ) ) {
		$url = sunflower_textfold( 'URL;VALUE=URI:' . $permalink );
	}

	return <<<"ICALEVENT"
BEGIN:VEVENT\r
UID:{$uid}\r
{$url}\r
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
function sunflower_replace( $text ) {
	// Replace ",", ";" and "\".
	$searchandreplace = array(
		','    => '\,',
		';'    => '\;',
		'\\'   => '\\\\',
		'\r\n' => '\\n',
		'\n'   => '\\n',
		'\r'   => '\\n',
	);

	$text = strtr( $text, $searchandreplace );

	// Remove empty lines and replace all new lines with "\n\n".
	$text = preg_replace( '/\s*(\n)/', '\\n\\n', $text );

	return $text;
}

/**
 * This folds the text line for SUMMARY and DESCRIPTION
 *
 * Reference: https://www.kanzaki.com/docs/ical/text.html
 *
 * @param string $text The text to fold.
 * @param int    $max_bytes Maximum bytes per line.
 * @return string The folded text.
 */
function sunflower_textfold( string $text, int $max_bytes = 73 ): string {
	$result = '';
	$line   = '';

	$len = mb_strlen( $text, 'UTF-8' );

	for ( $i = 0; $i < $len; $i++ ) {
		$char = mb_substr( $text, $i, 1, 'UTF-8' );

		// Count bytes, not characters.
		if ( strlen( $line . $char ) > $max_bytes ) {
			$result .= $line . "\r\n ";
			$line    = $char;
		} else {
			$line .= $char;
		}
	}

	return $result . $line;
}
