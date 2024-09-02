<?php
/**
 * Sunflower Event handling
 *
 * @package sunflower
 */

/**
 * Get the event metadata.
 */
function sunflower_get_event_fields() {
	return array(
		'_sunflower_event_from'            => array( __( 'Startdate', 'sunflower' ), 'datetimepicker' ),
		'_sunflower_event_until'           => array( __( 'Enddate', 'sunflower' ), 'datetimepicker' ),
		'_sunflower_event_whole_day'       => array( __( 'Whole day', 'sunflower' ), null, 'checkbox' ),
		'_sunflower_event_location_name'   => array( __( 'Location name', 'sunflower' ) ),
		'_sunflower_event_location_street' => array( __( 'Street', 'sunflower' ) ),
		'_sunflower_event_location_city'   => array( __( 'City', 'sunflower' ) ),
		'_sunflower_event_webinar'         => array( __( 'Webinar', 'sunflower' ) ),
		'_sunflower_event_organizer'       => array( __( 'Organizer', 'sunflower' ) ),
		'_sunflower_event_organizer_url'   => array( __( 'Organizer URL', 'sunflower' ) ),
		'_sunflower_event_lat'             => array( 'Latitude', null, 'hidden' ),
		'_sunflower_event_lon'             => array( 'Longitude', null, 'hidden' ),
		'_sunflower_event_zoom'            => array( 'Zoom', null, 'hidden' ),
	);
}

/**
 * Create sunflower event post type.
 */
function sunflower_create_event_post_type() {
	register_post_type(
		'sunflower_event',
		array(
			'labels'       => array(
				'name'          => __( 'Events', 'sunflower' ),
				'singular_name' => __( 'Event', 'sunflower' ),
			),
			'public'       => true,
			'menu_icon'    => 'dashicons-calendar',
			'has_archive'  => true,
			'rewrite'      => array(
				'slug' => 'termine',
			),
			'show_in_rest' => true,
			'supports'     => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
		)
	);

	$sunflower_labels = array(
		'name'                       => _x( 'Tags', 'taxonomy general name', 'default' ),
		'singular_name'              => _x( 'Tag', 'taxonomy singular name', 'default' ),
		'search_items'               => __( 'Search Tags', 'default' ),
		'popular_items'              => __( 'Popular Tags', 'default' ),
		'all_items'                  => __( 'All Tags', 'default' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Tag', 'default' ),
		'update_item'                => __( 'Update Tag', 'default' ),
		'add_new_item'               => __( 'Add New Tag', 'default' ),
		'new_item_name'              => __( 'New Tag Name', 'default' ),
		'separate_items_with_commas' => __( 'Separate tags with commas', 'default' ),
		'add_or_remove_items'        => __( 'Add or remove tags', 'default' ),
		'choose_from_most_used'      => __( 'Choose from the most used tags', 'default' ),
		'menu_name'                  => __( 'Tags', 'default' ),
	);

	register_taxonomy(
		'sunflower_event_tag',
		'sunflower_event',
		array(
			'hierarchical'      => false,
			'labels'            => $sunflower_labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
		)
	);
}

add_action( 'init', 'sunflower_create_event_post_type' );

/**
 * Add event meta box
 *
 * See https://developer.wordpress.org/reference/functions/add_meta_box for a full explanation of each property
 */
function sunflower_add_event_meta_boxes() {

	add_meta_box(
		'sunflower_event_meta_box', // div id containing rendered fields.
		__( 'Event', 'sunflower' ), // section heading displayed as text.
		'sunflower_event_meta_box', // callback function to render fields.
		'sunflower_event', // name of post type on which to render fields.
		'side', // location on the screen.
		'high' // placement priority.
	);
}

add_action( 'admin_init', 'sunflower_add_event_meta_boxes' );

/**
 * Save event meta data to database.
 */
function sunflower_save_event_meta_boxes() {
	global $post;

	// Do not save, if nonce is invalid.
	if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'update-post_' . $post->ID ) ) {
		return;
	}

	// Ignore other post types.
	if ( 'sunflower_event' !== $post->post_type ) {
		return;
	}

	$sunflower_event_fields = sunflower_get_event_fields();

	if ( ! isset( $post->ID ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( get_post_status( $post->ID ) === 'auto-draft' ) {
		return;
	}

	$into_transients = array( '_sunflower_event_lat', '_sunflower_event_lon', '_sunflower_event_zoom' );

	$is_all_day = $_POST['_sunflower_event_whole_day'] ?? '';

	foreach ( $sunflower_event_fields as $id => $config ) {

		// In case of all day events the events end on midnight, next day. So we have to add one day on save.
		if ( '_sunflower_event_until' === $id && 'checked' === $is_all_day ) {
			$value = gmdate( 'Y-m-d', strtotime( sunflower_german_date2int_date( $_POST[ $id ] ) ) + 86400 );
		} else {
			$value = ( 'datetimepicker' === $config[1] ) ? sunflower_german_date2int_date( $_POST[ $id ] ) : $_POST[ $id ];
		}

		update_post_meta( $post->ID, $id, sanitize_text_field( $value ) );
		if ( ! in_array( $id, $into_transients, true ) ) {
			continue;
		}

		if ( ! $value ) {
			continue;
		}

		set_transient( $id, $value );
	}
}

add_action( 'save_post', 'sunflower_save_event_meta_boxes', 10, 2 );

/**
 * Transform date from German format "DD.MM.YYYY HH:mm" to "YYYY-MM-DD HH:mm"
 *
 * @param string $german_date The date string in German format.
 */
function sunflower_german_date2int_date( $german_date ) {
	if ( ! $german_date ) {
		return '';
	}

	$date_array = preg_split( '/[^0-9]/', (string) $german_date );

	if ( count( $date_array ) === 5 ) {
		return sprintf( '%s-%s-%s %s:%s', $date_array[2], $date_array[1], $date_array[0], $date_array[3], $date_array[4] );
	} else {
		return sprintf( '%s-%s-%s', $date_array[2], $date_array[1], $date_array[0] );
	}
}

/**
 * Transform date format from "YYYY-MM-DD HH:mm" into German format "DD.MM.YYYY HH:mm"
 *
 * @param int $int_date The date in format international format.
 */
function sunflower_int_date2german_date( $int_date ) {
	if ( ! $int_date ) {
		return '';
	}

	$date_array = preg_split( '/[^0-9]/', (string) $int_date );

	if ( count( $date_array ) === 5 ) {
		return sprintf( '%s.%s.%s %s:%s', $date_array[2], $date_array[1], $date_array[0], $date_array[3], $date_array[4] );
	} else {
		return sprintf( '%s.%s.%s', $date_array[2], $date_array[1], $date_array[0] );
	}
}



/**
 * Render event meta box
 */
function sunflower_event_meta_box() {
	global $post;

	$sunflower_event_fields = sunflower_get_event_fields();

	$custom = get_post_custom( $post->ID );
	$uid    = $custom['_sunflower_event_uid'][0] ?? false;

	if ( $uid ) {
		printf( '<div style="color:red">%s</div>', esc_html__( 'This event will be imported by remote ical-calendar. All changes here will be overwritten.', 'sunflower' ) );
		?>
		<script>
			jQuery( document ).ready(function() {
				window.setTimeout(() => {
					jQuery('.popover-slot').prepend('<div class="sunflower-admin-hint">Dies ist ein importierter Termin.<br>Änderungen hier werden in Kürze automatisch überschrieben.</div>');
				}, 1000);

			});
		</script>
		<?php
		return;
	}

	$is_all_day = $custom['_sunflower_event_whole_day'][0] ?? '';

	foreach ( $sunflower_event_fields as $id => $config ) {
		$value = $custom[ $id ][0] ?? false;

		// In case of all day events the events end on midnight, next day. For the input, we show last day of event.
		if ( '_sunflower_event_until' === $id && 'checked' === $is_all_day ) {
			$value = gmdate( 'Y.m.d', strtotime( $value ) - 86400 );
		}
		sunflower_event_field( $id, $config, $value );
	}

	$lat  = $custom['_sunflower_event_lat'][0] ?? false;
	$lon  = $custom['_sunflower_event_lon'][0] ?? false;
	$zoom = $custom['_sunflower_event_zoom'][0] ?? false;

	if ( ! $lat || ! $lon || ! $zoom ) {
		$lat  = get_transient( '_sunflower_event_lat' );
		$lon  = get_transient( '_sunflower_event_lon' );
		$zoom = get_transient( '_sunflower_event_zoom' );
	}

	if ( ! $lat || ! $lon || ! $zoom ) {
		$lat  = 50.5;
		$lon  = 9.7;
		$zoom = 4;
	}

	printf(
		'%1$s
        <div class="components-flex components-h-stack">
            <button id="sunflowerShowMap" class="components-button is-primary" onClick="sunflowerShowLeaflet( %4$s, %5$s, %6$s, true );">%2$s</button>
            <br />
            <button id="sunflowerDeleteMap" class="components-button is-secondary is-destructive" >%3$s</button>
        </div>
        <div id="leaflet" style="height:270px"></div>',
		esc_html__( 'Map', 'sunflower' ),
		esc_html__( 'load map', 'sunflower' ),
		esc_html__( 'delete map', 'sunflower' ),
		esc_attr( $lat ),
		esc_attr( $lon ),
		esc_attr( $zoom )
	);
}

/**
 * Render Event metadata fields
 *
 * @param int    $id The form field id.
 * @param array  $config The field configuration.
 * @param string $value The field value.
 */
function sunflower_event_field( $id, $config, $value ) {
	$sunflower_label = $config[0];
	$sunflower_class = $config[1] ?? '';
	$sunflower_type  = $config[2] ?? false;

	if ( 'datetimepicker' === $sunflower_class ) {
		$value = sunflower_int_date2german_date( $value );
	}

	match ( $sunflower_type ) {
		'checkbox' => printf(
			'<div><span><input class="%4$s" type="checkbox" name="%1$s" id="%1$s" %3$s value="checked"></span><label for="%1$s">%2$s</label></div>',
			esc_attr( $id ),
			esc_attr( $sunflower_label ),
			esc_attr( $value ),
			esc_attr( $sunflower_class )
		),
		'hidden' => printf(
			'<input type="hidden" name="%1$s" id="%1$s" value="%2$s">',
			esc_attr( $id ),
			esc_attr( $value )
		),
		default => printf(
			'<div>%2$s<br><input class="%4$s" type="text" name="%1$s" placeholder="%2$s" autocomplete="off" value="%3$s"></div>',
			esc_attr( $id ),
			esc_attr( $sunflower_label ),
			esc_attr( $value ),
			esc_attr( $sunflower_class )
		),
	};
}

/**
 * Load the required JavaScript files.
 */
function sunflower_load_event_admin_scripts() {
	wp_enqueue_script(
		'sunflower-datetimepicker',
		get_template_directory_uri() . '/assets/vndr/jquery-datetimepicker/build/jquery.datetimepicker.full.min.js',
		array( 'jquery' ),
		'1.0.0',
		true
	);

	wp_enqueue_script(
		'sunflower-datetimepicker-custom',
		get_template_directory_uri() . '/assets/js/custom-jquery-date-time-picker.js',
		array( 'sunflower-datetimepicker' ),
		'1.0.0',
		true
	);

	wp_enqueue_style(
		'sunflower-datetimepicker',
		get_template_directory_uri() . '/assets/vndr/jquery-datetimepicker/build/jquery.datetimepicker.min.css',
		array(),
		'1.0.0'
	);

	wp_enqueue_script(
		'sunflower-leaflet',
		get_template_directory_uri() . '/assets/vndr/leaflet/dist/leaflet.js',
		null,
		'3.2.1',
		true
	);

	wp_enqueue_style(
		'sunflower-leaflet',
		get_template_directory_uri() . '/assets/vndr/leaflet/dist/leaflet.css',
		array(),
		'1.0.0'
	);

	wp_enqueue_style(
		'sunflower-editor-style',
		get_template_directory_uri() . '/assets/css/editor-style.css',
		array(),
		'1.0.0'
	);
}

add_action( 'admin_enqueue_scripts', 'sunflower_load_event_admin_scripts' );

/**
 * Helper function to check if array has only numeric values
 *
 * @param array $value Array of elements which are either numeric or strings.
 */
function sunflower_is_numeric_array( array $value ) {
	foreach ( $value as $a => $b ) {
		if ( ! is_numeric( $b ) ) {
			return false;
		}
	}

	return true;
}

/**
 * Get the next events.
 *
 * @param int                 $number The amount of events to fetch.
 * @param null|int[]|string[] $tag_ids Array of sunflower_event_tag IDs.
 *
 * @return WP_Query
 */
function sunflower_get_next_events( $number = -1, $tag_ids = null ) {
	$sunflower_tax_query = null;

	if ( $tag_ids ) {
		if ( sunflower_is_numeric_array( $tag_ids ) ) {
			$sunflower_tax_query = array(
				array(
					'taxonomy' => 'sunflower_event_tag',
					'field'    => 'id',
					'terms'    => $tag_ids,
				),
			);
		} else {
			$sunflower_tax_query = array(
				array(
					'taxonomy' => 'sunflower_event_tag',
					'field'    => 'slug',
					'terms'    => $tag_ids,
				),
			);
		}
	}

	return new WP_Query(
		array(
			'post_type'      => 'sunflower_event',
			'posts_per_page' => $number,
			'tax_query'      => $sunflower_tax_query,
			'meta_key'       => '_sunflower_event_from',
			'orderby'        => 'meta_value',
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => '_sunflower_event_until',
					'value'   => gmdate( 'Y-m-d H:i', strToTime( 'now + 1 hours' ) ),
					'compare' => '>',
				),
				array(
					'key'     => '_sunflower_event_from',
					'value'   => gmdate( 'Y-m-d H:i', strToTime( 'now - 6 hours' ) ),
					'compare' => '>',
				),
			),
			'order'          => 'ASC',
		)
	);
}

/**
 * Get past events.
 *
 * @param int $number The number of events to fetch.
 */
function sunflower_get_past_events( $number = -1 ) {
	return new WP_Query(
		array(
			'post_type'      => 'sunflower_event',
			'posts_per_page' => $number,
			'meta_key'       => '_sunflower_event_from',
			'orderby'        => 'meta_value',
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => '_sunflower_event_from',
					'value'   => gmdate( 'Y-m-d H:i', strToTime( 'now' ) ),
					'compare' => '<',
				),
			),
			'order'          => 'DESC',
		)
	);
}

/**
 * Prepare the event times for output.
 *
 * @param \WP_post $post The post object.
 */
function sunflower_prepare_event_time_data( $post ) {
	$_sunflower_event_from = get_post_meta( $post->ID, '_sunflower_event_from', true ) ?? false;
	$_sunflower_event_from = strToTime( (string) $_sunflower_event_from );

	$_sunflower_event_until = get_post_meta( $post->ID, '_sunflower_event_until', true ) ?? false;
	$_sunflower_event_until = strToTime( (string) $_sunflower_event_until );

	$_sunflower_event_whole_day = get_post_meta( $post->ID, '_sunflower_event_whole_day', true ) ?? false;

	$event_more_days = ( $_sunflower_event_until && gmdate( 'jFY', $_sunflower_event_from ) !== gmdate( 'jFY', $_sunflower_event_until ) );

	$weekday   = sprintf(
		'%s%s',
		date_i18n( 'l', $_sunflower_event_from ),
		( $event_more_days ) ? ' - ' . date_i18n( 'l', $_sunflower_event_until ) : ''
	);
	$untildate = '';
	$untiltime = '';
	$fromdate  = date_i18n( 'd.m.Y', $_sunflower_event_from );
	if ( $_sunflower_event_until ) {
		$untildate = ' - ' . date_i18n( 'd.m.Y', $_sunflower_event_until );

		if ( date_i18n( 'd.m.Y', $_sunflower_event_from ) === date_i18n( 'd.m.Y', $_sunflower_event_until ) ) {
			// On same day there is no until day.
			$untildate = '';
		} elseif ( gmdate( 'H:i', $_sunflower_event_until ) === '00:00' ) {
			// Days with time 00:00.
			$datetime = new DateTime();
			$datetime->setTimestamp( $_sunflower_event_from );
			$datetime->modify( '+1 day' );

			if ( gmdate( 'Y-m-d', $_sunflower_event_until ) === $datetime->format( 'Y-m-d' ) ) {
				// Its Tommorrow.
				$weekday   = date_i18n( 'l', $_sunflower_event_from );
				$untildate = '';
			} else {
				$weekday = sprintf(
					'%s%s',
					date_i18n( 'l', $_sunflower_event_from ),
					( $event_more_days ) ? ' - ' . date_i18n( 'l', $_sunflower_event_until - 1 ) : ''
				);
				// The - 1 leads to 1 seconds before midnight, that means the day before.
				$untildate = ' - ' . date_i18n( 'd.m.Y', $_sunflower_event_until - 1 );
			}
		} elseif ( date_i18n( 'm', $_sunflower_event_from ) === date_i18n( 'm', $_sunflower_event_until ) ) {
			// The same month.
			$fromdate = date_i18n( 'd.', $_sunflower_event_from );
		} elseif ( date_i18n( 'Y', $_sunflower_event_from ) === date_i18n( 'Y', $_sunflower_event_until ) ) {
			// The same year.
			$fromdate = date_i18n( 'd.m.', $_sunflower_event_from );
		}

		$untiltime = '- ' . date_i18n( ' H:i', $_sunflower_event_until );
	}

	$days = sprintf(
		'%s%s',
		$fromdate,
		$untildate
	);

	$time = false;
	if ( gmdate( 'H:i', $_sunflower_event_from ) !== '00:00' && ! $_sunflower_event_whole_day ) {
		$time = sprintf(
			'%s %s',
			date_i18n( 'H:i', $_sunflower_event_from ),
			$untiltime
		);
	}

	return array( $weekday, $days, $time );
}

/**
 * Add the custom columns to the book post type
 *
 * @param array $columns Columns of the event post type.
 */
function sunflower_set_custom_edit_book_columns( $columns ) {
	unset( $columns['date'] );
	$columns['sunflower_event_date']          = __( 'Event date', 'sunflower' );
	$columns['sunflower_event_location_name'] = __( 'Event location', 'sunflower' );

	return $columns;
}

add_filter( 'manage_sunflower_event_posts_columns', 'sunflower_set_custom_edit_book_columns' );

/**
 * Add the data to the custom columns for the book post type
 *
 * @param string $column The event metadata field.
 * @param int    $post_id The post id.
 */
function sunflower_custom_event_column( $column, $post_id ) {
	switch ( $column ) {
		case 'sunflower_event_date':
			$date = get_post_meta( $post_id, '_sunflower_event_from', true ) ?? false;

			if ( $date ) {
				echo esc_attr( sunflower_int_date2german_date( $date ) );
			}

			break;

		case 'sunflower_event_location_name':
			$location = get_post_meta( $post_id, '_sunflower_event_location_name', true ) ?? false;

			echo esc_attr( $location );
	}
}

add_action( 'manage_sunflower_event_posts_custom_column', 'sunflower_custom_event_column', 10, 2 );

// Make sunflower_event_date column-header clickable in backend
// to allow ordering by it.
add_filter(
	'manage_edit-sunflower_event_sortable_columns',
	static function ( $columns ) {
		$columns['sunflower_event_date'] = 'sunflower_event_date';
		return $columns;
	}
);

add_action(
	'pre_get_posts',
	static function ( $query ) {
		if ( ! is_admin() ) {
			return;
		}

		$orderby = $query->get( 'orderby' );
		if ( 'sunflower_event_date' === $orderby ) {
			$query->set( 'meta_key', '_sunflower_event_from' );
			$query->set( 'orderby', 'meta_value' );
		}
	}
);
