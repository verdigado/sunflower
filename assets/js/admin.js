/* eslint-disable no-undef */
jQuery( document ).on( 'click', '.sunflower-plugins .notice-dismiss', function() {
	jQuery.ajax( {
		url: ajaxurl,
		data: {
			action: 'sunflower_plugins_dismiss',
		},
	} );
} );
