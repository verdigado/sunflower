<?php
/**
 * Template part for providing events in json-ld format
 *
 * @package Sunflower 26
 */

$sunflower_event_location_name   = get_post_meta( $post->ID, '_sunflower_event_location_name', true ) ?? false;
$sunflower_event_location_street = get_post_meta( $post->ID, '_sunflower_event_location_street', true ) ?? false;
$sunflower_event_location_city   = get_post_meta( $post->ID, '_sunflower_event_location_city', true ) ?? false;
$sunflower_event_webinar         = get_post_meta( $post->ID, '_sunflower_event_webinar', true ) ?? false;
$sunflower_event_from            = get_post_meta( $post->ID, '_sunflower_event_from', true ) ?? false;
$sunflower_event_until           = get_post_meta( $post->ID, '_sunflower_event_until', true ) ?? false;
$sunflower_event_organizer_name  = get_post_meta( $post->ID, '_sunflower_event_organizer', true ) ?? false;
$sunflower_event_organizer_url   = get_post_meta( $post->ID, '_sunflower_event_organizer_url', true ) ?? false;

if ( ! function_exists( 'sunflower_get_jsonld_date' ) ) {
	/**
	 * Get the given date in format for json-ld
	 *
	 * @param string $input The event date string.
	 */
	function sunflower_get_jsonld_date( $input ) {
		return preg_replace( '/ /', 'T', (string) $input );
	}
}

if ( ! function_exists( 'sunflower_get_jsonld_text' ) ) {
	/**
	 * Turn a value into plain text for json-ld: no markup, no HTML entities.
	 *
	 * @param string $input The text, possibly containing HTML.
	 */
	function sunflower_get_jsonld_text( $input ) {
		return trim( html_entity_decode( wp_strip_all_tags( (string) $input ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
	}
}

// Collect the data as an array and let wp_json_encode() do the quoting.
// Values must not be pasted into the JSON by hand: a quotation mark in a
// location name would produce an invalid document.
$sunflower_jsonld = array(
	'@context'    => 'https://schema.org',
	'@type'       => 'Event',
	'name'        => sunflower_get_jsonld_text( get_the_title() ),
	'description' => sunflower_get_jsonld_text( get_the_excerpt() ),
);

$sunflower_jsonld_image = get_the_post_thumbnail_url();
if ( $sunflower_jsonld_image ) {
	$sunflower_jsonld['image'] = esc_url_raw( $sunflower_jsonld_image );
}

$sunflower_jsonld['startDate'] = sunflower_get_jsonld_date( $sunflower_event_from );
if ( $sunflower_event_until ) {
	$sunflower_jsonld['endDate'] = sunflower_get_jsonld_date( $sunflower_event_until );
}

if ( $sunflower_event_organizer_name ) {
	$sunflower_jsonld['organizer'] = array(
		'@type' => 'Organization',
		'name'  => sunflower_get_jsonld_text( $sunflower_event_organizer_name ),
	);

	$sunflower_jsonld_organizer_url = esc_url_raw( (string) $sunflower_event_organizer_url );
	if ( $sunflower_jsonld_organizer_url ) {
		$sunflower_jsonld['organizer']['url'] = $sunflower_jsonld_organizer_url;
	}
}

// Location: a webinar link takes precedence over a physical place, as before.
$sunflower_jsonld_webinar_url = esc_url_raw( (string) $sunflower_event_webinar );
if ( $sunflower_jsonld_webinar_url ) {
	$sunflower_jsonld['eventAttendanceMode'] = 'https://schema.org/OnlineEventAttendanceMode';
	$sunflower_jsonld['location']            = array(
		'@type' => 'VirtualLocation',
		'url'   => $sunflower_jsonld_webinar_url,
	);
} elseif ( $sunflower_event_location_name ) {
	$sunflower_jsonld['eventAttendanceMode'] = 'https://schema.org/OfflineEventAttendanceMode';
	$sunflower_jsonld['location']            = array(
		'@type'   => 'Place',
		'name'    => sunflower_get_jsonld_text( $sunflower_event_location_name ),
		'address' => array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => sunflower_get_jsonld_text( $sunflower_event_location_street ),
			'addressLocality' => sunflower_get_jsonld_text( $sunflower_event_location_city ),
		),
	);
} else {
	$sunflower_jsonld['location'] = __( 'none', 'sunflower' );
}

// JSON_HEX_TAG keeps a literal "</script>" in any value from closing the element.
$sunflower_jsonld_flags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT;
?>

<script type="application/ld+json">
<?php echo wp_json_encode( $sunflower_jsonld, $sunflower_jsonld_flags ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

</script>
