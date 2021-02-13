<?php
/**
 * The template for displaying all single events
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package sunflower
 */
$_sunflower_event_from = @get_post_meta( $post->ID, '_sunflower_event_from')[0] ?: false;
$_sunflower_event_from = strToTime($_sunflower_event_from);
$_sunflower_event_until = @get_post_meta( $post->ID, '_sunflower_event_until')[0] ?: false;
$_sunflower_event_until = strToTime($_sunflower_event_until);
$_sunflower_event_whole_day = @get_post_meta( $post->ID, '_sunflower_event_whole_day')[0] ?: false;

$_sunflower_event_location_name = @get_post_meta( $post->ID, '_sunflower_event_location_name')[0] ?: false;
$_sunflower_event_location_street = @get_post_meta( $post->ID, '_sunflower_event_location_street')[0] ?: false;
$_sunflower_event_location_city = @get_post_meta( $post->ID, '_sunflower_event_location_city')[0] ?: false;
$_sunflower_event_webinar = @get_post_meta( $post->ID, '_sunflower_event_webinar')[0] ?: false;

$_sunflower_event_lon = @get_post_meta( $post->ID, '_sunflower_event_lon')[0] ?: false;
$_sunflower_event_lat = @get_post_meta( $post->ID, '_sunflower_event_lat')[0] ?: false;
$_sunflower_event_zoom = @get_post_meta( $post->ID, '_sunflower_event_zoom')[0] ?: false;

$icsLink = home_url() . '/?sunflower_event=' . $post->post_name . '&format=ics';


if( isset($_GET['format']) AND $_GET['format'] === 'ics' ){
	require_once('functions/ical.php');
	die();
}


get_header();

$metadata = '';

$event_more_days = ( $_sunflower_event_until AND date('jFY', $_sunflower_event_from) !== date('jFY', $_sunflower_event_until) );

$metadata .= sprintf('<div class="arvo text-uppercase weekday">%s %s</div>',
		($event_more_days) ? __('from', 'sunflower') : '',
		date_i18n('l',  $_sunflower_event_from)
	);

$untildate = $untiltime = '';
if($_sunflower_event_until){
	$untildate = '&dash; ' . date_i18n(' j.m.Y',  $_sunflower_event_until);
	$untiltime = '&dash; ' . date_i18n(' H:i',  $_sunflower_event_until);
}
$metadata .= sprintf('<div class="date mb-2">%s %s</div>',
	date_i18n('j.m.Y',  $_sunflower_event_from),
	$untildate
);

// show time only if not whole day
if( date('H:i', $_sunflower_event_from) !== '00:00' AND !$_sunflower_event_whole_day){
	$metadata .= sprintf('<div class="time mt-2 mb-2">%s %s %s</div>',
		date_i18n('H:i',  $_sunflower_event_from),
		$untiltime,
		__("o'clock", 'sunflower')

	);
}

$location = [];
if( $_sunflower_event_location_name ) $location[] = $_sunflower_event_location_name;
if( $_sunflower_event_location_street ) $location[] = $_sunflower_event_location_street;
if( $_sunflower_event_location_city) $location[] = $_sunflower_event_location_city;
if( !empty($location)){
	$metadata .= sprintf('<div class="mt-2 mb-2">%s</div>',
		join(',', $location)
	);
}


if( $_sunflower_event_webinar ){
	$metadata .= sprintf('<div class="mt-1 mb-1"><a href="%s" target="_blank"><i class="fas fa-desktop"></i> %s</a></div>',
		$_sunflower_event_webinar,
		__('Link to webinar', 'sunflower')
	);
}

$metadata .= sprintf('<div><a href="%s" class="text-white">%s</a></div>',
	$icsLink,
	__('Download as ics', 'sunflower')
);

?>
	<div id="content" class="container">
		<div class="row">
			<div class="col-12 col-md-10 offset-md-1">
				<main id="primary" class="site-main mt-5">
					<?php

					while ( have_posts() ) :
						the_post();

						get_template_part( 'template-parts/content', '', ['metadata' => $metadata] );

					?>

					<?php if($_sunflower_event_lat AND $_sunflower_event_lon) { ?>
						<div id="leaflet" class="d-flex flex-column justify-content-center align-items-center bg-lightgreen border-0">
							<div class="before-loading text-center">
								<i class="fas fa-map-marker-alt mb-3"></i>
								<div class="h5 mb-3">
									<?php _e('Show event location on map', 'sunflower'); ?>
								</div>
								<div class="mb-3">
									<?php _e('If you click the button, the content will be downloaded from openstreetmap.', 'sunflower'); ?>
								</div>

								<button class="wp-block-button__link no-border-radius show-leaflet" 
									data-lat="<?php echo $_sunflower_event_lat; ?>"
									data-lon="<?php echo $_sunflower_event_lon; ?>" 
									data-zoom="<?php echo $_sunflower_event_zoom; ?>"
								>
									<?php _e('Show map', 'sunflower'); ?>
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