<?php
/**
 * Render the Sunflower Calendar Events block.
 *
 * Initialises a FullCalendar instance with AJAX event loading.
 *
 * Available filter hooks:
 *
 * sunflower_calendar_wrapper_classes – Customise wrapper CSS classes.
 *   add_filter( 'sunflower_calendar_wrapper_classes', function( $classes, $attributes ) {
 *       $classes[] = 'my-custom-class';
 *       return $classes;
 *   }, 10, 2 );
 *
 * sunflower_calendar_data – Customise data passed to JavaScript.
 *   add_filter( 'sunflower_calendar_data', function( $data, $attributes ) {
 *       $data['custom'] = 'value';
 *       return $data;
 *   }, 10, 2 );
 *
 * Block attributes available via $attributes:
 *   array $tag       Optional. Event tag slugs to filter by.
 *   array $tagColors Optional. Tag-slug → hex-colour mapping.
 *
 * @package Sunflower 26
 */

if ( ! defined( 'SUNFLOWER_FULLCALENDAR_VERSION' ) ) {
	define( 'SUNFLOWER_FULLCALENDAR_VERSION', '6.1.20' );
}

if ( ! defined( 'SUNFLOWER_CALENDAR_NONCE_ACTION' ) ) {
	define( 'SUNFLOWER_CALENDAR_NONCE_ACTION', 'sunflower_calendar_events' );
}

/**
 * Enqueue FullCalendar scripts (idempotent – safe to call multiple times per page).
 */
if ( ! function_exists( 'sunflower_enqueue_calendar_scripts' ) ) :
function sunflower_enqueue_calendar_scripts() {
	if ( wp_script_is( 'fullcalendar', 'enqueued' ) ) {
		return;
	}

	$template_uri = get_template_directory_uri();
	$version      = SUNFLOWER_FULLCALENDAR_VERSION;

	wp_enqueue_script(
		'fullcalendar',
		$template_uri . '/assets/vndr/fullcalendar/index.global.min.js',
		array(),
		$version,
		true
	);
}
endif;

/**
 * Convert the tag attribute to a comma-separated string for the data attribute.
 *
 * @param mixed $tags Tag value from block attributes.
 * @return string
 */
if ( ! function_exists( 'sunflower_prepare_calendar_tags' ) ) :
function sunflower_prepare_calendar_tags( $tags ) {
	if ( empty( $tags ) ) {
		return '';
	}

	$calendar_tags = is_array( $tags ) ? $tags : explode( ',', (string) $tags );
	$calendar_tags = array_filter(
		array_map( 'sanitize_title', array_map( 'trim', $calendar_tags ) )
	);

	return implode( ',', $calendar_tags );
}
endif;

/**
 * Sanitize tag color mappings before exposing them to the frontend.
 *
 * @param mixed $tag_colors Tag color map from block attributes.
 * @return array
 */
if ( ! function_exists( 'sunflower_prepare_calendar_tag_colors' ) ) :
function sunflower_prepare_calendar_tag_colors( $tag_colors ) {
	if ( ! is_array( $tag_colors ) ) {
		return array();
	}

	$prepared_tag_colors = array();

	foreach ( $tag_colors as $tag_slug => $color ) {
		$prepared_tag_slug = sanitize_title( (string) $tag_slug );
		$prepared_color    = sanitize_hex_color( (string) $color );

		if ( ! $prepared_tag_slug || ! $prepared_color ) {
			continue;
		}

		$prepared_tag_colors[ $prepared_tag_slug ] = $prepared_color;
	}

	return $prepared_tag_colors;
}
endif;

/**
 * Build the calendar configuration array passed to JavaScript via data attributes.
 *
 * @param array $attributes Block attributes.
 * @return array
 */
if ( ! function_exists( 'sunflower_build_calendar_data' ) ) :
function sunflower_build_calendar_data( $attributes ) {
	$calendar_tags = $attributes['tag'] ?? array();
	$tag_colors    = sunflower_prepare_calendar_tag_colors( $attributes['tagColors'] ?? array() );

	$data = array(
		'ajax_url'    => admin_url( 'admin-ajax.php' ),
		'locales_url' => trailingslashit( get_template_directory_uri() . '/assets/vndr/@fullcalendar/core/locales' ),
		'tags'        => sunflower_prepare_calendar_tags( $calendar_tags ),
		'nonce'       => wp_create_nonce( SUNFLOWER_CALENDAR_NONCE_ACTION ),
		'tag_colors'  => wp_json_encode( $tag_colors ),
	);

	/**
	 * Filters the calendar data passed to JavaScript.
	 *
	 * @param array $data       Calendar configuration data.
	 * @param array $attributes Block attributes.
	 */
	return apply_filters( 'sunflower_calendar_data', $data, $attributes );
}
endif;

/**
 * Build the wrapper CSS classes array.
 *
 * @param array $attributes Block attributes.
 * @return array
 */
if ( ! function_exists( 'sunflower_build_wrapper_classes' ) ) :
function sunflower_build_wrapper_classes( $attributes ) {
	$classes = array( 'calendar-events' );

	/**
	 * Filters the calendar wrapper CSS classes.
	 *
	 * @param array $classes    Array of CSS class names.
	 * @param array $attributes Block attributes.
	 */
	return apply_filters( 'sunflower_calendar_wrapper_classes', $classes, $attributes );
}
endif;

sunflower_enqueue_calendar_scripts();

$calendar_data      = sunflower_build_calendar_data( $attributes );
$wrapper_classes    = sunflower_build_wrapper_classes( $attributes );
$wrapper_attributes = get_block_wrapper_attributes(
	array( 'class' => implode( ' ', $wrapper_classes ) )
);
?>
<div <?php echo $wrapper_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by core ?>>
	<div class="wp-block-group__inner-container">
		<div class="sunflower-calendar-container">
			<div
				class="sunflower-calendar"
				data-ajax-url="<?php echo esc_url( $calendar_data['ajax_url'] ); ?>"
				data-locales-url="<?php echo esc_url( $calendar_data['locales_url'] ); ?>"
				data-tags="<?php echo esc_attr( $calendar_data['tags'] ); ?>"
				data-nonce="<?php echo esc_attr( $calendar_data['nonce'] ); ?>"
				data-tag-colors="<?php echo esc_attr( $calendar_data['tag_colors'] ); ?>">
			</div>
		</div>
	</div>
</div>
