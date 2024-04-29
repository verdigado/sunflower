<?php
/**
 * Template part for providing events in json-ld format
 *
 * @package sunflower
 */

$sunflower_event_location_name   = get_post_meta( $post->ID, '_sunflower_event_location_name', true ) ?? false;
$sunflower_event_location_street = get_post_meta( $post->ID, '_sunflower_event_location_street', true ) ?? false;
$sunflower_event_location_city   = get_post_meta( $post->ID, '_sunflower_event_location_city', true ) ?? false;
$sunflower_event_webinar         = get_post_meta( $post->ID, '_sunflower_event_webinar', true ) ?? false;
$sunflower_event_from            = get_post_meta( $post->ID, '_sunflower_event_from', true ) ?? false;
$sunflower_event_until           = get_post_meta( $post->ID, '_sunflower_event_until', true ) ?? false;
$sunflower_event_organizer_name  = get_post_meta( $post->ID, '_sunflower_event_organizer', true ) ?? false;
$sunflower_event_organizer_url   = get_post_meta( $post->ID, '_sunflower_event_organizer_url', true ) ?? false;

$sunflower_jsonld          = array();
$sunflower_jsonld['image'] = get_the_post_thumbnail_url() ?? false;

$sunflower_location = sprintf( '"location": "%s"', __( 'none', 'sunflower' ) );
if ( $sunflower_event_location_name ) {
	$sunflower_location  = '"eventAttendanceMode": "https://schema.org/OfflineEventAttendanceMode",';
	$sunflower_location .= sprintf(
		'"location": {
		"@type": "Place",
		"name": "%s",
		"address": {
		  "@type": "PostalAddress",
		  "streetAddress": "%s",
		  "addressLocality": "%s"
		}
	  }',
		$sunflower_event_location_name,
		$sunflower_event_location_street,
		$sunflower_event_location_city
	);
}

if ( $sunflower_event_webinar ) {
	$sunflower_location  = '"eventAttendanceMode": "https://schema.org/OnlineEventAttendanceMode",';
	$sunflower_location .= sprintf(
		'"location": {
		"@type": "VirtualLocation",
		"url": "%s"
	  }',
		$sunflower_event_webinar
	);
}

/**
 * Get the given date in format for json-ld
 *
 * @param string $input The event date string.
 */
function sunflower_get_jsonld_date( $input ) {
	return preg_replace( '/ /', 'T', (string) $input );
}
?>


<script type="application/ld+json">
{
	"@context": "https://schema.org",
	"@type": "Event",
	"name": "<?php echo esc_attr( get_the_title() ); ?>",
	"description": "<?php echo esc_attr( get_the_excerpt() ); ?>",
	<?php
	if ( $sunflower_jsonld['image'] ) {
		printf( '"image": "%s",', esc_url( $sunflower_jsonld['image'] ) );
	}
	?>
	"startDate": "<?php echo esc_attr( sunflower_get_jsonld_date( $sunflower_event_from ) ); ?>",
	<?php
	if ( $sunflower_event_until ) {
		printf( '"endDate": "%s",', esc_attr( sunflower_get_jsonld_date( $sunflower_event_until ) ) );
	}

	if ( $sunflower_event_organizer_name ) {
		printf(
			'"organizer": {
		"@type": "Organization",
		"name": "%s",
		"url": "%s"
	},',
			esc_attr( $sunflower_event_organizer_name ),
			esc_attr( $sunflower_event_organizer_url )
		);
	}

	echo wp_kses_post( $sunflower_location );
	?>


}
</script>
