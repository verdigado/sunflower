<?php

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

function sunflower_create_event_post_type() {
	register_post_type(
		'sunflower_event',
		// CPT Options
		array(
			'labels'       => array(
				'name'          => __( 'Events', 'sunflower' ),
				'singular_name' => __( 'Event', 'sunflower' ),
			),
			'public'       => true,
			'menu_icon'    => 'dashicons-calendar',
			'has_archive'  => true,
			'rewrite'      => array( 'slug' => 'termine' ),
			'show_in_rest' => true,
			'supports'     => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
		)
	);

	$labels = array(
		'name'                       => _x( 'Tags', 'taxonomy general name' ),
		'singular_name'              => _x( 'Tag', 'taxonomy singular name' ),
		'search_items'               => __( 'Search Tags' ),
		'popular_items'              => __( 'Popular Tags' ),
		'all_items'                  => __( 'All Tags' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Tag' ),
		'update_item'                => __( 'Update Tag' ),
		'add_new_item'               => __( 'Add New Tag' ),
		'new_item_name'              => __( 'New Tag Name' ),
		'separate_items_with_commas' => __( 'Separate tags with commas' ),
		'add_or_remove_items'        => __( 'Add or remove tags' ),
		'choose_from_most_used'      => __( 'Choose from the most used tags' ),
		'menu_name'                  => __( 'Tags' ),
	);

	register_taxonomy(
		'sunflower_event_tag',
		'sunflower_event',
		array(
			'hierarchical'      => false,
			'labels'            => $labels,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
		)
	);
}
add_action( 'init', 'sunflower_create_event_post_type' );

function sunflower_add_event_meta_boxes() {
	 // see https://developer.wordpress.org/reference/functions/add_meta_box for a full explanation of each property
	add_meta_box(
		'sunflower_event_meta_box', // div id containing rendered fields
		__( 'Event', 'sunflower' ), // section heading displayed as text
		'sunflower_event_meta_box', // callback function to render fields
		'sunflower_event', // name of post type on which to render fields
		'side', // location on the screen
		'high' // placement priority
	);
}
add_action( 'admin_init', 'sunflower_add_event_meta_boxes' );

function save_sunflower_event_meta_boxes() {
	global $post;

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

	$intoTransients = array( '_sunflower_event_lat', '_sunflower_event_lon', '_sunflower_event_zoom' );

	foreach ( $sunflower_event_fields as $id => $config ) {
		$value = ( @$config[1] === 'datetimepicker' ) ? germanDate2intDate( @$_POST[ $id ] ) : @$_POST[ $id ];

		update_post_meta( $post->ID, $id, sanitize_text_field( $value ) );

		if ( in_array( $id, $intoTransients ) and $value ) {
			set_transient( $id, $value );
		}
	}

}
add_action( 'save_post', 'save_sunflower_event_meta_boxes' );

function germanDate2intDate( $germanDate ) {
	if ( ! $germanDate ) {
		return '';
	}

	list($day, $month, $year, $hours, $minutes ) = preg_split( '/[^0-9]/', $germanDate );
	return "$year-$month-$day $hours:$minutes";
}

function intDate2germanDate( $intDate ) {
	if ( ! $intDate ) {
		return '';
	}
	list($year, $month, $day, $hours, $minutes ) = preg_split( '/[^0-9]/', $intDate );
	return "$day.$month.$year $hours:$minutes";
}

function sunflower_event_meta_box() {
	global $post;

	$sunflower_event_fields = sunflower_get_event_fields();

	$custom = get_post_custom( $post->ID );
	$uid    = @$custom['_sunflower_event_uid'][0];

	if ( $uid ) {
		printf( '<div style="color:red">%s</div>', __( 'This event will be imported by remote ical-calendar. All changes here will be overwritten.', 'sunflower' ) );
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

	foreach ( $sunflower_event_fields as $id => $config ) {
		$value = @$custom[ $id ][0];
		sunflower_event_field( $id, $config, $value );
	}

	$lat  = @$custom['_sunflower_event_lat'][0];
	$lon  = @$custom['_sunflower_event_lon'][0];
	$zoom = @$custom['_sunflower_event_zoom'][0];

	if ( ! $lat or ! $lon or ! $zoom ) {
		$lat  = get_transient( '_sunflower_event_lat' );
		$lon  = get_transient( '_sunflower_event_lon' );
		$zoom = get_transient( '_sunflower_event_zoom' );

	}

	if ( ! $lat or ! $lon or ! $zoom ) {
		$lat  = 50.5;
		$lon  = 9.7;
		$zoom = 4;
	}

	printf(
		'%1$s
        <div>
            <button id="sunflowerShowMap" onClick="sunflowerShowLeaflet( %4$s, %5$s, %6$s, true );">%2$s</button>
            <button id="sunflowerDeleteMap">%3$s</button>
        </div>
        <div id="leaflet" style="height:270px"></div>',
		__( 'Map', 'sunflower' ),
		__( 'load map', 'sunflower' ),
		__( 'delete map', 'sunflower' ),
		$lat,
		$lon,
		$zoom
	);

}

function sunflower_event_field( $id, $config, $value ) {
	$label = $config[0];
	$class = @$config[1] ?: '';
	$type  = @$config[2] ?: false;

	if ( $class === 'datetimepicker' ) {
		$value = intDate2germanDate( $value );
	}

	switch ( $type ) {
		case 'checkbox':
			printf(
				'%2$s<input class="%4$s" type="checkbox" name="%1$s" id="%1$s"  %3$s value="checked"><br>',
				$id,
				$label,
				( $value ) ?: '',
				$class
			);
			break;
		case 'hidden':
			printf(
				'<input type="hidden" name="%1$s" id="%1$s" value="%2$s">',
				$id,
				$value
			);
			break;
		default:
			printf(
				'<div>%2$s<br><input class="%4$s" type="text" name="%1$s" placeholder="%2$s" autocomplete="off" value="%3$s"></div>',
				$id,
				$label,
				$value,
				$class
			);
	}

}

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
		get_template_directory_uri() . '/assets/custom-jquery-date-time-picker.js',
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

}
add_action( 'admin_enqueue_scripts', 'sunflower_load_event_admin_scripts' );

/**
 * @param int        $number
 * @param null|int[] $tagIds Array of sunflower_event_tag IDs
 *
 * @return WP_Query
 */
function sunflower_get_next_events( $number = -1, $tagIds = null ) {
	$taxQuery = null;
	if ( $tagIds ) {
		$taxQuery = array(
			array(
				'taxonomy' => 'sunflower_event_tag',
				'field'    => 'id',
				'terms'    => $tagIds,
			),
		);
	}

	return new WP_Query(
		array(
			// 'paged'       => $paged,
			// 'nopaging'    => true,
			'post_type'      => 'sunflower_event',
			'posts_per_page' => $number,
			'tax_query'      => $taxQuery,
			'meta_key'       => '_sunflower_event_from',
			'orderby'        => 'meta_value',
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => '_sunflower_event_from',
					'value'   => date( 'Y-m-d H:i', strToTime( 'now - 6 hours' ) ),
					'compare' => '>',
				),
			),
			'order'          => 'ASC',
		)
	);
}

function sunflower_get_past_events( $number = -1 ) {
	return new WP_Query(
		array(
			// 'paged' => $paged,
			// 'nopaging'        => true,
			'post_type'      => 'sunflower_event',
			'posts_per_page' => $number,
			'meta_key'       => '_sunflower_event_from',
			'orderby'        => 'meta_value',
			'meta_query'     => array(
				'relation' => 'OR',
				array(
					'key'     => '_sunflower_event_from',
					'value'   => date( 'Y-m-d H:i', strToTime( 'now' ) ),
					'compare' => '<',
				),
			),
			'order'          => 'DESC',
		)
	);
}

function sunflower_prepare_event_time_data( $post ) {
	$_sunflower_event_from      = @get_post_meta( $post->ID, '_sunflower_event_from' )[0] ?: false;
	$_sunflower_event_from      = strToTime( $_sunflower_event_from );
	$_sunflower_event_until     = @get_post_meta( $post->ID, '_sunflower_event_until' )[0] ?: false;
	$_sunflower_event_until     = strToTime( $_sunflower_event_until );
	$_sunflower_event_whole_day = @get_post_meta( $post->ID, '_sunflower_event_whole_day' )[0] ?: false;

	$event_more_days = ( $_sunflower_event_until and date( 'jFY', $_sunflower_event_from ) !== date( 'jFY', $_sunflower_event_until ) );

	$weekday = sprintf(
		'%s%s',
		date_i18n( 'l', $_sunflower_event_from ),
		( $event_more_days ) ? ' &dash; ' . date_i18n( 'l', $_sunflower_event_until ) : ''
	);

	$untildate = $untiltime = '';
	$fromdate  = date_i18n( 'd.m.Y', $_sunflower_event_from );
	if ( $_sunflower_event_until ) {
		$untildate = ' &dash; ' . date_i18n( 'd.m.Y', $_sunflower_event_until );

		if ( date_i18n( 'd.m.Y', $_sunflower_event_from ) === date_i18n( 'd.m.Y', $_sunflower_event_until ) ) {
			// same day, no until day
			$untildate = '';
		} elseif ( date( 'H:i', $_sunflower_event_until ) == '00:00' ) {
			// days with time 00:00
			$datetime = new DateTime();
			$datetime->setTimestamp( $_sunflower_event_from );
			$datetime->modify( '+1 day' );

			if ( date( 'Y-m-d', $_sunflower_event_until ) == $datetime->format( 'Y-m-d' ) ) {
				// tommorrow
				$weekday   = date_i18n( 'l', $_sunflower_event_from );
				$untildate = '';
			} else {
				$weekday = sprintf(
					'%s%s',
					date_i18n( 'l', $_sunflower_event_from ),
					( $event_more_days ) ? ' &dash; ' . date_i18n( 'l', $_sunflower_event_until - 1 ) : ''
				);
				// the - 1 leads to 1 seconds before midnight, that means the day before
				$untildate = ' &dash; ' . date_i18n( 'd.m.Y', $_sunflower_event_until - 1 );
			}
		} elseif ( date_i18n( 'm', $_sunflower_event_from ) === date_i18n( 'm', $_sunflower_event_until ) ) {
			// same month
			$fromdate = date_i18n( 'd.', $_sunflower_event_from );
		} elseif ( date_i18n( 'Y', $_sunflower_event_from ) === date_i18n( 'Y', $_sunflower_event_until ) ) {
			// same year
			$fromdate = date_i18n( 'd.m.', $_sunflower_event_from );
		}

		$untiltime = '&dash; ' . date_i18n( ' H:i', $_sunflower_event_until );
	}

	$days = sprintf(
		'%s%s',
		$fromdate,
		$untildate
	);

	$time = false;
	if ( date( 'H:i', $_sunflower_event_from ) !== '00:00' and ! $_sunflower_event_whole_day ) {
		$time = sprintf(
			'%s %s',
			date_i18n( 'H:i', $_sunflower_event_from ),
			$untiltime
		);
	}

	return array( $weekday, $days, $time );
}

// Add the custom columns to the book post type:
add_filter( 'manage_sunflower_event_posts_columns', 'set_custom_edit_book_columns' );
function set_custom_edit_book_columns( $columns ) {
	unset( $columns['date'] );
	$columns['sunflower_event_date']          = __( 'Event date', 'sunflower' );
	$columns['sunflower_event_location_name'] = __( 'Event location', 'sunflower' );

	return $columns;
}

// Add the data to the custom columns for the book post type:
add_action( 'manage_sunflower_event_posts_custom_column', 'custom_sunflower_event_column', 10, 2 );
function custom_sunflower_event_column( $column, $post_id ) {
	switch ( $column ) {

		case 'sunflower_event_date':
			$date = @get_post_meta( $post_id, '_sunflower_event_from' )[0] ?: false;

			if ( $date ) {
				echo intDate2germanDate( $date );
			}
			break;

		case 'sunflower_event_location_name':
			$location = @get_post_meta( $post_id, '_sunflower_event_location_name' )[0] ?: false;

			echo $location;
	}
}

// Make sunflower_event_date column-header clickable in backend
// to allow ordering by it.
add_filter(
	'manage_edit-sunflower_event_sortable_columns',
	function ( $columns ) {
		$columns['sunflower_event_date'] = 'sunflower_event_date';
		return $columns;
	}
);

add_action(
	'pre_get_posts',
	function ( $query ) {
		if ( ! is_admin() ) {
			return;
		}
		$orderby = $query->get( 'orderby' );
		if ( $orderby == 'sunflower_event_date' ) {
			$query->set( 'meta_key', '_sunflower_event_from' );
			$query->set( 'orderby', 'meta_value' );
		}
	}
);
