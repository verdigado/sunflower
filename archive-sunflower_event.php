<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package sunflower
 */

if ( isset( $_GET['format'] ) && 'ics' === $_GET['format'] ) { // phpcs:ignore
	define( 'SUNFLOWER_ICAL_ALL_EVENTS', true );
	include_once __DIR__ . '/functions/ical.php';
	die();
}

get_header();

$sunflower_is_event_archive = isset( $_GET['archive'] ) && ( 'true' === $_GET['archive'] ); // phpcs:ignore
?>
	<?php
	// Prepare map data.
	$sunflower_map = array();
	?>
	<script>
		const map = {};
		map.marker = [];
	</script>


	<div id="content" class="container">
		<div class="row">
			<div class="col-12">
				<main id="primary" class="site-main">
					<?php if ( have_posts() ) : ?>

						<header class="page-header text-center">
							<h1 class="page-title">
								<?php
								( $sunflower_is_event_archive ) ? esc_html_e( 'Events archive', 'sunflower' ) : esc_html_e( 'Events', 'sunflower' );
								?>
							</h1>
								<?php
								$sunflower_events_description = sunflower_get_setting( 'sunflower_events_description' ) ?? '';
								if ( $sunflower_events_description ) {
									printf( '<p>%s</p>', wp_kses_post( $sunflower_events_description ) );
								}
								?>
						</header><!-- .page-header -->

						<div class="filter-button-group mb-5 text-center">
						<?php
						if ( $sunflower_is_event_archive ) {
							printf( '<a href="?archive=false" class="eventlist" >%s</a>', esc_html__( 'to upcoming events', 'sunflower' ) );
						} else {
							printf( '<button class="filter filter-active" data-filter="*">%s</button>', esc_html__( 'all events', 'sunflower' ) );
							if ( sunflower_get_setting( 'sunflower_show_event_archive' ) ) {
								printf( '<a href="?archive=true" class="eventlist" >%s</a>', esc_html__( 'Archive', 'sunflower' ) );
							}
						}
						?>

						<?php if ( sunflower_get_setting( 'sunflower_show_overall_map' ) && ! $sunflower_is_event_archive ) { ?>
								<button class="filter" data-filter=".map"><?php esc_html_e( 'Map', 'sunflower' ); ?></button>
							<?php
						}
						?>


						<?php
							$sunflower_terms = get_terms(
								array(
									'taxonomy'   => 'sunflower_event_tag',
									'hide_empty' => true,
								)
							);

						if ( ! $sunflower_is_event_archive ) {
							foreach ( $sunflower_terms as $sunflower_term ) {
								printf( '<button class="filter" data-filter=".%s">%s</button>', esc_attr( $sunflower_term->slug ), esc_attr( $sunflower_term->name ) );
							}
						}
						?>
						</div>

						<div class="row event-list">
						<?php

						$sunflower_ordered_posts = ( $sunflower_is_event_archive ) ? sunflower_get_past_events() : sunflower_get_next_events();

						/* Start the Loop */
						while ( $sunflower_ordered_posts->have_posts() ) {
							$sunflower_ordered_posts->the_post();

							/*
							* Include the Post-Type-specific template for the content.
							* If you want to override this in a child theme, then include a file
							* called content-___.php (where ___ is the Post Type name) and that will be used instead.
							*/

							echo '<div class="col-12 col-md-6 col-lg-4 mb-3">';
							get_template_part( 'template-parts/archive', 'event' );

							$sunflower_event_lon                                   = get_post_meta( $post->ID, '_sunflower_event_lon', true ) ?? false;
							$sunflower_event_lat                                   = get_post_meta( $post->ID, '_sunflower_event_lat', true ) ?? false;
							$sunflower_event_location_name                         = get_post_meta( $post->ID, '_sunflower_event_location_name', true ) ?? false;
							$sunflower_event_location_city                         = get_post_meta( $post->ID, '_sunflower_event_location_city', true ) ?? false;
							[$sunflower_weekday, $sunflower_days, $sunflower_time] = sunflower_prepare_event_time_data( $post );
							$sunflower_location                                    = $sunflower_event_location_city;
							if ( $sunflower_event_location_city ) {
								$sunflower_location .= ', ' . $sunflower_event_location_city;
							}

							if ( $sunflower_location ) {
								$sunflower_location = ' | ' . $sunflower_location;
							}

							if ( $sunflower_event_lat && $sunflower_event_lon ) {
								$sunflower_map[] = (object) array(
									'lat'     => $sunflower_event_lat,
									'lon'     => $sunflower_event_lon,
									'content' => sprintf(
										'<div class="leaflet-marker"><strong>%s</strong><div>%s%s</div><div>%s</div><a href="%s">%s</a></div>',
										get_the_title(),
										$sunflower_days,
										$sunflower_location,
										get_the_excerpt(),
										get_permalink(),
										__( 'more info', 'sunflower' )
									),
								);
							}

							echo '</div>';
						}


else :

	get_template_part( 'template-parts/content', 'no-events' );

endif;
?>

					<script>

						<?php
						$sunflower_lower_lat = 90;
						$sunflower_upper_lat = 0;
						$sunflower_lower_lon = 90;
						$sunflower_upper_lon = 0;

						foreach ( $sunflower_map as $sunflower_marker ) {
							printf(
								"map.marker.push( { 'lat' : %s, 'lon': %s, 'content': '%s'} );",
								esc_attr( $sunflower_marker->lat ),
								esc_attr( $sunflower_marker->lon ),
								esc_attr( $sunflower_marker->content )
							);

							$sunflower_lower_lat = min( $sunflower_lower_lat, $sunflower_marker->lat );
							$sunflower_upper_lat = max( $sunflower_upper_lat, $sunflower_marker->lat );
							$sunflower_lower_lon = min( $sunflower_lower_lon, $sunflower_marker->lon );
							$sunflower_upper_lon = max( $sunflower_upper_lon, $sunflower_marker->lon );
						}

						$sunflower_center_lat = ( $sunflower_lower_lat + $sunflower_upper_lat ) / 2;
						$sunflower_center_lon = ( $sunflower_lower_lon + $sunflower_upper_lon ) / 2;
						$sunflower_zoom       = sunflower_get_setting( 'sunflower_zoom' ) ? sunflower_get_setting( 'sunflower_zoom' ) : 11;
						printf(
							"map.center = { 'lat': %s, 'lon': %s, 'zoom': %s };",
							esc_attr( $sunflower_center_lat ),
							esc_attr( $sunflower_center_lon ),
							esc_attr( $sunflower_zoom )
						);

						?>

					</script>

					<div class="col-12 d-none">
						<div id="leaflet" style="height:500px" class="map d-flex flex-column justify-content-center align-items-center bg-lightgreen border-0">
							<div class="before-loading text-center">
								<i class="fas fa-map-marker-alt mb-3"></i>
								<div class="h5 mb-3">
									<?php esc_html_e( 'Show event location on map', 'sunflower' ); ?>
								</div>
								<div class="mb-3">
									<?php echo wp_kses_post( __( 'If you click the button, the content will be downloaded from openstreetmap.', 'sunflower' ) ); ?>
								</div>

								<button class="wp-block-button__link no-border-radius show-leaflet-all">
									<?php esc_html_e( 'Show map', 'sunflower' ); ?>
								</button>
							</div>
						</div>
					</div>

					</div> <!-- event-list -->

					<?php
					if ( ! $sunflower_is_event_archive ) {
						printf( '<div class="row"><div class="col-12 text-end"><a href="?format=ics" class="small calendar-download">%s</a></div></div>', esc_html__( 'calendar in ics-format', 'sunflower' ) );
					}
					?>

				</main><!-- #main -->
			</div>
		</div>
</div>

<?php
	wp_enqueue_script(
		'filter-custom',
		get_template_directory_uri() . '/assets/js/event-filter.js',
		null,
		SUNFLOWER_VERSION,
		true
	);
	?>

<?php
get_sidebar();
get_footer();
