<?php
$_sunflower_event_location_name   = @get_post_meta( $post->ID, '_sunflower_event_location_name' )[0] ?: false;
$_sunflower_event_location_street = @get_post_meta( $post->ID, '_sunflower_event_location_street' )[0] ?: false;
$_sunflower_event_location_city   = @get_post_meta( $post->ID, '_sunflower_event_location_city' )[0] ?: false;
$_sunflower_event_webinar         = @get_post_meta( $post->ID, '_sunflower_event_webinar' )[0] ?: false;
$_sunflower_event_from            = @get_post_meta( $post->ID, '_sunflower_event_from' )[0] ?: false;
$_sunflower_event_until           = @get_post_meta( $post->ID, '_sunflower_event_until' )[0] ?: false;
$_sunflower_event_organizer_name  = @get_post_meta( $post->ID, '_sunflower_event_organizer' )[0] ?: false;
$_sunflower_event_organizer_url   = @get_post_meta( $post->ID, '_sunflower_event_organizer_url' )[0] ?: false;



$jsonld          = array();
$jsonld['image'] = get_the_post_thumbnail_url() ?: false;

$location = sprintf( '"location": "%s"', __( 'none', 'sunflower' ) );
if ( $_sunflower_event_location_name ) {
	$location  = '"eventAttendanceMode": "https://schema.org/OfflineEventAttendanceMode",';
	$location .= sprintf(
		'"location": {		
        "@type": "Place",
        "name": "%s",
        "address": {
          "@type": "PostalAddress",
          "streetAddress": "%s",
          "addressLocality": "%s"
        }
      }',
		$_sunflower_event_location_name,
		$_sunflower_event_location_street,
		$_sunflower_event_location_city
	);
}


if ( $_sunflower_event_webinar ) {
	$location  = '"eventAttendanceMode": "https://schema.org/OnlineEventAttendanceMode",';
	$location .= sprintf(
		'"location": {
        "@type": "VirtualLocation",
        "url": "%s"
      }',
		$_sunflower_event_webinar
	);
}


function getJsonldDate( $input ) {
	$output = preg_replace( '/ /', 'T', $input );
	return $output;
}
?>


<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Event",
  "name": "<?php echo esc_attr( get_the_title() ); ?>",
  "description": "<?php echo esc_attr( get_the_excerpt() ); ?>",
  <?php
	if ( $jsonld['image'] ) {
		printf( '"image": "%s",', $jsonld['image'] );
	}
	?>
  "startDate": "<?php echo getJsonldDate( $_sunflower_event_from ); ?>",
  <?php
	if ( $_sunflower_event_until ) {
		printf( '"endDate": "%s",', getJsonldDate( $_sunflower_event_until ) );
	}

	if ( $_sunflower_event_organizer_name ) {
		printf(
			'"organizer": {		
	    "@type": "Organization",
	    "name": "%s",
	    "url": "%s"
	},',
			$_sunflower_event_organizer_name,
			$_sunflower_event_organizer_url
		);
	}

	echo $location;
	?>
  
 
}
</script>
