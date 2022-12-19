<?php
/**
 * The template for displaying archive pages
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package sunflower
 */

if ( isset( $_GET['format'] ) and $_GET['format'] === 'ics' ) {
	define( 'SUNFLOWER_ICAL_ALL_EVENTS', true );
	include_once 'functions/ical.php';
	die();
}

get_header();

$is_event_archive = isset( $_GET['archive'] ) && ( $_GET['archive'] == 'true' );
?>
	<?php
	// Prepare map data
	$map = array();
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
								( $is_event_archive ) ? _e( 'Events archive', 'sunflower' ) : _e( 'Events', 'sunflower' );
								?>
							</h1>
								<?php
								if ( $sunflower_events_description = get_sunflower_setting( 'sunflower_events_description' ) ) {
									printf( '<p>%s</p>', $sunflower_events_description );
								}
								?>
						</header><!-- .page-header -->

						<div class="filter-button-group mb-5 text-center">
						<?php
						if ( $is_event_archive ) {
							printf( '<a href="?archive=false" class="eventlist" >%s</a>', __( 'to upcoming events', 'sunflower' ) );
						} else {
							printf( '<button class="filter filter-active" data-filter="*">%s</button>', __( 'all events', 'sunflower' ) );
							if ( get_sunflower_setting( 'sunflower_show_event_archive' ) ) {
								printf( '<a href="?archive=true" class="eventlist" >%s</a>', __( 'Archive', 'sunflower' ) );
							}
						}
						?>

						<?php if ( get_sunflower_setting( 'sunflower_show_overall_map' ) and ! $is_event_archive ) { ?>
								<button class="filter" data-filter=".map"><?php _e( 'Map', 'sunflower' ); ?></button>
						<?php } ?>

							
						<?php
						$terms = get_terms(
							array(
								'taxonomy'   => 'sunflower_event_tag',
								'hide_empty' => true,
							)
						);

						if ( ! $is_event_archive ) {
							foreach ( $terms as $term ) {
								printf( '<button class="filter" data-filter=".%s">%s</button>', $term->slug, $term->name );
							}
						}
						?>
						</div>

						<div class="row event-list">
						<?php

						// $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

						$ordered_posts = ( $is_event_archive ) ? sunflower_get_past_events() : sunflower_get_next_events();



						/* Start the Loop */
						while ( $ordered_posts->have_posts() ) :
							$ordered_posts->the_post();

							/*
							* Include the Post-Type-specific template for the content.
							* If you want to override this in a child theme, then include a file
							* called content-___.php (where ___ is the Post Type name) and that will be used instead.
							*/

							echo '<div class="col-12 col-md-6 col-lg-4 mb-3">';
							get_template_part( 'template-parts/archive', 'event' );

							$_sunflower_event_lon           = @get_post_meta( $post->ID, '_sunflower_event_lon' )[0] ?: false;
							$_sunflower_event_lat           = @get_post_meta( $post->ID, '_sunflower_event_lat' )[0] ?: false;
							$_sunflower_event_location_name = @get_post_meta( $post->ID, '_sunflower_event_location_name' )[0] ?: false;
							$_sunflower_event_location_city = @get_post_meta( $post->ID, '_sunflower_event_location_city' )[0] ?: false;
							list($weekday, $days, $time )   = sunflower_prepare_event_time_data( $post );
							$location                       = $_sunflower_event_location_city;
							if ( $_sunflower_event_location_city ) {
								$location .= ', ' . $_sunflower_event_location_city;
							}

							if ( $location ) {
								$location = ' | ' . $location;
							}

							if ( $_sunflower_event_lat and $_sunflower_event_lon ) {
								$map[] = (object) array(
									'lat'     => $_sunflower_event_lat,
									'lon'     => $_sunflower_event_lon,
									'content' => sprintf(
										'<div class="leaflet-marker"><strong>%s</strong><div>%s%s</div><div>%s</div><a href="%s">%s</a></div>',
										get_the_title(),
										$days,
										$location,
										get_the_excerpt(),
										get_permalink(),
										__( 'more info', 'sunflower' )
									),

								);
							}
							echo '</div>';

						endwhile;

					else :

						get_template_part( 'template-parts/content', 'no-events' );

					endif;
					?>
					
					<script>

						<?php
						$lowerLat = 90;
						$upperLat = 0;
						$lowerLon = 90;
						$upperLon = 0;

						foreach ( $map as $marker ) {
							printf(
								"map.marker.push( { 'lat' : %s, 'lon': %s, 'content': '%s'} );",
								$marker->lat,
								$marker->lon,
								$marker->content
							);

							$lowerLat = min( $lowerLat, $marker->lat );
							$upperLat = max( $upperLat, $marker->lat );
							$lowerLon = min( $lowerLon, $marker->lon );
							$upperLon = max( $upperLon, $marker->lon );

						}

						$centerLat = ( $lowerLat + $upperLat ) / 2;
						$centerLon = ( $lowerLon + $upperLon ) / 2;
						$zoom      = get_sunflower_setting( 'sunflower_zoom' ) ?: 6;
						printf(
							"map.center = { 'lat': %s, 'lon': %s, 'zoom': %s };",
							$centerLat,
							$centerLon,
							$zoom
						);

						?>

					</script>

					<div class="col-12 d-none">
						<div id="leaflet" style="height:500px" class="map d-flex flex-column justify-content-center align-items-center bg-lightgreen border-0">
							<div class="before-loading text-center">
								<i class="fas fa-map-marker-alt mb-3"></i>
								<div class="h5 mb-3">
									<?php _e( 'Show event location on map', 'sunflower' ); ?>
								</div>
								<div class="mb-3">
									<?php _e( 'If you click the button, the content will be downloaded from openstreetmap.', 'sunflower' ); ?>
								</div>

								<button class="wp-block-button__link no-border-radius show-leaflet-all">
									<?php _e( 'Show map', 'sunflower' ); ?>
								</button>
							</div>
						</div>
					</div>

					</div> <!-- event-list -->

					<?php
					if ( ! $is_event_archive ) {
						printf( '<div class="row"><div class="col-12 text-end"><a href="?format=ics" class="small">%s</a></div></div>', __( 'calendar in ics-format', 'sunflower' ) );
					}
					?>

				</main><!-- #main -->
			</div>
		</div>
</div>

<?php
	wp_enqueue_script(
		'filter-custom',
		get_template_directory_uri() . '/assets/js/filter.js',
		null,
		'3.2.1',
		true
	);
	?>

<?php
get_sidebar();
get_footer();
