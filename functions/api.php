<?php
/**
 * REST API related filters
 *
 * @package sunflower
 */

/**
 * Remove api endpoint for user requests, if author shouldn't be shown.
 */
add_filter(
	'rest_endpoints',
	static function ( $endpoints ) {
		if ( is_user_logged_in() ) {
			return $endpoints;
		}

		if ( isset( $endpoints['/wp/v2/users'] ) && ! sunflower_get_setting( 'sunflower_show_author' ) ) {
			unset( $endpoints['/wp/v2/users'] );
		}

		if ( isset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] ) && ! sunflower_get_setting( 'sunflower_show_author' ) ) {
			unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
		}

		return $endpoints;
	}
);

/**
 * Hide author in post api response, if author shouldn't be shown.
 *
 * Example call: https://example.com/wp-json/wp/v2/posts?per_page=1
 */
add_action(
	'rest_api_init',
	static function () {
		if ( is_user_logged_in() ) {
			return;
		}

		if ( sunflower_get_setting( 'sunflower_show_author' ) ) {
			return;
		}

		$args = array(
			'get_callback' => static fn () => -1,
		);
		register_rest_field( 'post', 'author', $args );
	}
);
