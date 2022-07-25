<?php

add_filter(
	'rest_endpoints',
	function ( $endpoints ) {
		if ( is_user_logged_in() ) {
			return $endpoints;
		}

		if ( isset( $endpoints['/wp/v2/users'] ) && ! get_sunflower_setting( 'sunflower_show_author' ) ) {
			unset( $endpoints['/wp/v2/users'] );
		}
		if ( isset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] ) && ! get_sunflower_setting( 'sunflower_show_author' ) ) {
			unset( $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
		}
		return $endpoints;
	}
);

add_action(
	'rest_api_init',
	function () {
		if ( is_user_logged_in() ) {
			return;
		}

		if ( get_sunflower_setting( 'sunflower_show_author' ) ) {
			return;
		}

		$args = array(
			'get_callback' => function () {
				return -1;
			},
		);
		register_rest_field( 'post', 'author', $args );
	}
);
