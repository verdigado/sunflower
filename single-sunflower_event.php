<?php
/**
 * The template for displaying all single events
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package sunflower
 */
$show_sidebar = @get_post_meta( $post->ID, '_sunflower_show_sidebar')[0] ? true : false;
$_sunflower_event_from = @get_post_meta( $post->ID, '_sunflower_event_from')[0] ?: false;
$_sunflower_event_until = @get_post_meta( $post->ID, '_sunflower_event_until')[0] ?: false;
$_sunflower_event_whole_day = @get_post_meta( $post->ID, '_sunflower_event_whole_day')[0] ?: false;

$_sunflower_event_location_name = @get_post_meta( $post->ID, '_sunflower_event_location_name')[0] ?: false;
$_sunflower_event_location_street = @get_post_meta( $post->ID, '_sunflower_event_location_street')[0] ?: false;
$_sunflower_event_location_city = @get_post_meta( $post->ID, '_sunflower_event_location_city')[0] ?: false;
$_sunflower_event_webinar = @get_post_meta( $post->ID, '_sunflower_event_webinar')[0] ?: false;

$_sunflower_event_lon = @get_post_meta( $post->ID, '_sunflower_event_lon')[0] ?: false;
$_sunflower_event_lat = @get_post_meta( $post->ID, '_sunflower_event_lat')[0] ?: false;
$_sunflower_event_zoom = @get_post_meta( $post->ID, '_sunflower_event_zoom')[0] ?: false;

$icsLink = home_url() . '/?sunflower_event=' . $post->post_name . '&format=ics';
$show_sidebar = @get_post_meta( $post->ID, '_sunflower_show_sidebar')[0] ? true : false;


if( isset($_GET['format']) AND $_GET['format'] === 'ics' ){
	require_once('functions/ical.php');
	die();
}


get_header();

function formatDay( $time, $whole_day ){
	global $post;
	static $day;

	if( !$time ){
		return '';
	}
	$timestamp = strToTime($time);


	$timecode = ( date('H:i', $timestamp) === '00:00' OR $whole_day) ? '' : ',  H<\s\u\p>i</\s\u\p> \U\h\r';
	
	if ($day AND $day === date('jFY', $timestamp)){
		$daycode = '';
		$timecode = ( date('H:i', $timestamp) === '00:00' OR $whole_day) ? '' : '  H<\s\u\p>i</\s\u\p> \U\h\r';
	}else{
		$day = date('jFY', $timestamp);
		$daycode = 'l, \d\e\n j. F Y';
	}

	return date_i18n( $daycode . $timecode, $timestamp);
}


?>
	<div id="content" class="container">
		<div class="row">
			<div class="col-12">
				<main id="primary" class="site-main mt-5">
					<?php
					$startdate = formatDay( $_sunflower_event_from, $_sunflower_event_whole_day );
					$enddate = formatDay( $_sunflower_event_until, $_sunflower_event_whole_day);
					printf('<div><i class="far fa-clock"></i> %s %s</div>',
						$startdate,
						($enddate) ? ' &dash; ' . $enddate : ''
					);

					$location = [];
					if( $_sunflower_event_location_name ) $location[] = $_sunflower_event_location_name;
					if( $_sunflower_event_location_street ) $location[] = $_sunflower_event_location_street;
					if( $_sunflower_event_location_city) $location[] = $_sunflower_event_location_city;
					if( !empty($location)){
						printf('<div><i class="fas fa-map-marker-alt"></i> %s</div>',
							join(',', $location)
						);
					}

					printf('<div><a href="%s"><i class="fas fa-download"></i> %s</a></div>',
						$icsLink,
						__('Download as ics', 'sunflower')
					);

					if( $_sunflower_event_webinar ){
						printf('<div><a href="%s" target="_blank"><i class="fas fa-desktop"></i> %s</a></div>',
							$_sunflower_event_webinar,
							__('Link to webinar', 'sunflower')
						);
					}

					while ( have_posts() ) :
						the_post();

						get_template_part( 'template-parts/content', 'post' );

					?>

					<?php if($_sunflower_event_lat AND $_sunflower_event_lon) { ?>
						<div id="leaflet" class="d-flex flex-column justify-content-center align-items-center">
							<p class="text-center ms-5 me-5 small">
								<?php _e('This is thirdparty content. If you click the button, the content will be downloaded from a thirdparty server.', 'sunflower'); ?>
							<p>
							<button class="btn btn-info btn-sm show-leaflet" 
								data-lat="<?php echo $_sunflower_event_lat; ?>"
								data-lon="<?php echo $_sunflower_event_lon; ?>" 
								data-zoom="<?php echo $_sunflower_event_zoom; ?>"
							>
								<?php _e('Show map', 'sunflower'); ?>
							</button>
						</div>
					<?php } ?>

					<?php

						the_post_navigation(
							array(
								'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous:', 'sunflower' ) . '</span> <span class="nav-title">%title</span>',
								'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'sunflower' ) . '</span> <span class="nav-title">%title</span>',
							)
						);

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
if ( $show_sidebar ) {
	get_sidebar();
}
get_footer();