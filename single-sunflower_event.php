<?php
/**
 * The template for displaying all single events
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package sunflower
 */


$icsLink = home_url() . '/?sunflower_event=' . $post->post_name . '&format=ics';

if ( isset( $_GET['format'] ) and $_GET['format'] === 'ics' ) {
	include_once 'functions/ical.php';
	die();
}

$_sunflower_event_location_name   = @get_post_meta( $post->ID, '_sunflower_event_location_name' )[0] ?: false;
$_sunflower_event_location_street = @get_post_meta( $post->ID, '_sunflower_event_location_street' )[0] ?: false;
$_sunflower_event_location_city   = @get_post_meta( $post->ID, '_sunflower_event_location_city' )[0] ?: false;
$_sunflower_event_webinar         = @get_post_meta( $post->ID, '_sunflower_event_webinar' )[0] ?: false;
$_sunflower_event_organizer       = @get_post_meta( $post->ID, '_sunflower_event_organizer' )[0] ?: false;
$_sunflower_event_organizer_url   = @get_post_meta( $post->ID, '_sunflower_event_organizer_url' )[0] ?: false;


$_sunflower_event_lon  = @get_post_meta( $post->ID, '_sunflower_event_lon' )[0] ?: false;
$_sunflower_event_lat  = @get_post_meta( $post->ID, '_sunflower_event_lat' )[0] ?: false;
$_sunflower_event_zoom = @get_post_meta( $post->ID, '_sunflower_event_zoom' )[0] ?: false;

get_header();

list($weekday, $days, $time ) = sunflower_prepare_event_time_data( $post );

get_template_part( 'template-parts/event', 'json-ld' );

$metadata  = '';
$metadata .= sprintf(
	'<div class="text-uppercase weekday">%s</div>',
	$weekday
);

$metadata .= sprintf(
	'<div class="date mb-2">%s</div>',
	$days
);

// show time only if not whole day
if ( $time ) {
	$metadata .= sprintf(
		'<div class="time mt-2 mb-2">%s %s</div>',
		$time,
		__( "o'clock", 'sunflower' )
	);
}

$location = array();
if ( $_sunflower_event_location_name ) {
	$location[] = $_sunflower_event_location_name;
}
if ( $_sunflower_event_location_street ) {
	$location[] = $_sunflower_event_location_street;
}
if ( $_sunflower_event_location_city ) {
	$location[] = $_sunflower_event_location_city;
}
if ( ! empty( $location ) ) {
	$metadata .= sprintf(
		'<div class="mt-2 mb-2">%s</div>',
		join( '<br>', $location )
	);
}


if ( $_sunflower_event_webinar ) {
	$metadata .= sprintf(
		'<div class="mt-1 mb-1"><a href="%s" target="_blank">%s</a></div>',
		$_sunflower_event_webinar,
		__( 'Link to webinar', 'sunflower' )
	);
}

if ( $_sunflower_event_organizer ) {
	if ( $_sunflower_event_organizer_url ) {
		$metadata .= sprintf( '<div class="mt-1 mb-1">%s <a href="%s" target="_blank">%s</a></div>', __( 'organized by', 'sunflower' ), $_sunflower_event_organizer_url, $_sunflower_event_organizer );
	} else {
		$metadata .= sprintf( '<div class="mt-1 mb-1">%s %s</div>', __( 'organized by', 'sunflower' ), $_sunflower_event_organizer );
	}
}

$metadata .= sprintf(
	'<div><a href="%s" class="text-white">%s</a></div>',
	$icsLink,
	__( 'Download as ics', 'sunflower' )
);

?>
	<div id="content" class="container">
		<div class="row">
			<div class="col-12 col-md-10 offset-md-1">
				<main id="primary" class="site-main">
					<?php

					while ( have_posts() ) :
						the_post();

						get_template_part(
							'template-parts/content',
							'',
							array(
								'metadata' => $metadata,
								'class'    => 'display-single',
							)
						);

						?>

						<?php if ( $_sunflower_event_lat and $_sunflower_event_lon ) { ?>
						<div id="leaflet" class="d-flex flex-column justify-content-center align-items-center bg-lightgreen border-0">
							<div class="before-loading text-center">
								<i class="fas fa-map-marker-alt mb-3"></i>
								<div class="h5 mb-3">
							<?php _e( 'Show event location on map', 'sunflower' ); ?>
								</div>
								<div class="mb-3">
							<?php _e( 'If you click the button, the content will be downloaded from openstreetmap.', 'sunflower' ); ?>
								</div>

								<button class="wp-block-button__link no-border-radius show-leaflet" 
									data-lat="<?php echo $_sunflower_event_lat; ?>"
									data-lon="<?php echo $_sunflower_event_lon; ?>" 
									data-zoom="<?php echo $_sunflower_event_zoom; ?>"
								>
							<?php _e( 'Show map', 'sunflower' ); ?>
								</button>
							</div>
						</div>
						<?php } ?>
						<?php

						// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) :
							comments_template();
						endif;

					endwhile; // End of the loop.
					?>

				</main><!-- #main -->
			</div>
	</div>
</div>
<?php
get_sidebar();
get_footer();
