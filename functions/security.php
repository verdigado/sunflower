<?php
// Remove generator meta-tag
remove_action( 'wp_head', 'wp_generator' );

add_filter(
	'login_errors',
	function () {
		return __( 'Login error. Did you mistype s.th.?', 'sunflower' );
	}
);
