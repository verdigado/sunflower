<?php
/**
 * The template for displaying all single events
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package sunflower
 */

$sunflower_ics_link = wp_nonce_url( home_url() . '/?sunflower_event=' . $post->post_name . '&format=ics', 'event_ics_' . $post->ID, 'sunflower_nonce' );

if ( isset( $_GET['format'] ) && 'ics' === $_GET['format'] ) {
	check_admin_referer( 'event_ics_' . $post->ID, 'sunflower_nonce' );
	include_once __DIR__ . '/functions/ical.php';
	die();
}

$sunflower_event_location_name   = get_post_meta( $post->ID, '_sunflower_event_location_name', true ) ?? false;
$sunflower_event_location_street = get_post_meta( $post->ID, '_sunflower_event_location_street', true ) ?? false;
$sunflower_event_location_city   = get_post_meta( $post->ID, '_sunflower_event_location_city', true ) ?? false;
$sunflower_event_webinar         = get_post_meta( $post->ID, '_sunflower_event_webinar', true ) ?? false;
$sunflower_event_organizer       = get_post_meta( $post->ID, '_sunflower_event_organizer', true ) ?? false;
$sunflower_event_organizer_url   = get_post_meta( $post->ID, '_sunflower_event_organizer_url', true ) ?? false;

$sunflower_event_lon  = get_post_meta( $post->ID, '_sunflower_event_lon', true ) ?? false;
$sunflower_event_lat  = get_post_meta( $post->ID, '_sunflower_event_lat', true ) ?? false;
$sunflower_event_zoom = get_post_meta( $post->ID, '_sunflower_event_zoom', true ) ?? false;
$sunflower_zoom       = sunflower_get_setting( 'sunflower_zoom' ) ? sunflower_get_setting( 'sunflower_zoom' ) : 11;
if ( ! $sunflower_event_zoom ) {
	$sunflower_event_zoom = $sunflower_zoom;
}

get_header();

[$sunflower_weekday, $sunflower_days, $sunflower_time] = sunflower_prepare_event_time_data( $post );

get_template_part( 'template-parts/event', 'json-ld' );

$sunflower_metadata  = '';
$sunflower_metadata .= sprintf(
	'<div class="text-uppercase weekday">%s</div>',
	$sunflower_weekday
);

$sunflower_metadata .= sprintf(
	'<div class="date mb-2">%s</div>',
	$sunflower_days
);

// Show time only if not whole day.
if ( $sunflower_time ) {
	$sunflower_metadata .= sprintf(
		'<div class="time mt-2 mb-2">%s %s</div>',
		$sunflower_time,
		__( "o'clock", 'sunflower' )
	);
}

$sunflower_location = array();
if ( $sunflower_event_location_name ) {
	$sunflower_location[] = $sunflower_event_location_name;
}

if ( $sunflower_event_location_street ) {
	$sunflower_location[] = $sunflower_event_location_street;
}

if ( $sunflower_event_location_city ) {
	$sunflower_location[] = $sunflower_event_location_city;
}

if ( is_array( $sunflower_location ) ) {
	$sunflower_metadata .= sprintf(
		'<div class="mt-2 mb-2">%s</div>',
		implode(
			'<br>',
			array_map(
				static function ( $locline ) {
					if ( filter_var( $locline, FILTER_VALIDATE_URL ) ) {
						return sprintf(
							'<i class="fa-solid fa-location-dot"></i><a href="%s" class="location" target="_blank">%s</a>',
							$locline,
							__( 'Location Link', 'sunflower' )
						);
					}

					return $locline;
				},
				$sunflower_location
			)
		)
	);
}

if ( $sunflower_event_webinar ) {
	$sunflower_metadata .= sprintf(
		'<div class="mt-1 mb-1"><a href="%s" target="_blank">%s</a></div>',
		$sunflower_event_webinar,
		__( 'Link to webinar', 'sunflower' )
	);
}

if ( $sunflower_event_organizer ) {
	if ( $sunflower_event_organizer_url ) {
		$sunflower_metadata .= sprintf( '<div class="mt-1 mb-1">%s <a href="%s" target="_blank">%s</a></div>', __( 'organized by', 'sunflower' ), $sunflower_event_organizer_url, $sunflower_event_organizer );
	} else {
		$sunflower_metadata .= sprintf( '<div class="mt-1 mb-1">%s %s</div>', __( 'organized by', 'sunflower' ), $sunflower_event_organizer );
	}
}

$sunflower_metadata .= sprintf(
	'<div><a href="%s" class="text-white">%s</a></div>',
	$sunflower_ics_link,
	__( 'Download as ics', 'sunflower' )
);

?>
	<div id="content" class="container container-narrow">
		<div class="row">
			<div class="col-12">
				<main id="primary" class="site-main">
					<?php

					while ( have_posts() ) :
						the_post();

						get_template_part(
							'template-parts/content',
							'',
							array(
								'metadata' => $sunflower_metadata,
								'class'    => 'display-single',
							)
						);

						?>

						<?php if ( $sunflower_event_lat && $sunflower_event_lon ) { ?>
						<div id="leaflet" class="d-flex flex-column justify-content-center align-items-center bg-lightgreen border-0">
							<div class="before-loading text-center">
								<i class="fas fa-map-marker-alt mb-3"></i>
								<div class="h5 mb-3">
							<?php esc_html_e( 'Show event location on map', 'sunflower' ); ?>
								</div>
								<div class="mb-3">
							<?php echo wp_kses_post( __( 'If you click the button, the content will be downloaded from openstreetmap.', 'sunflower' ) ); ?>
								</div>

								<button class="wp-block-button__link no-border-radius show-leaflet"
									data-lat="<?php echo esc_attr( $sunflower_event_lat ); ?>"
									data-lon="<?php echo esc_attr( $sunflower_event_lon ); ?>"
									data-zoom="<?php echo esc_attr( $sunflower_event_zoom ); ?>"
								>
							<?php esc_html_e( 'Show map', 'sunflower' ); ?>
								</button>
							</div>
						</div>
							<?php
						}
						?>
						<?php

										// If comments are open or we have at least one comment, load up the comment template.
						if ( comments_open() || get_comments_number() ) :
							comments_template();
										endif;

					endwhile;

					// End of the loop.
					?>

				</main><!-- #main -->
			</div>
	</div>
</div>
<?php
get_sidebar();
get_footer();
