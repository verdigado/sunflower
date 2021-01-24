/* eslint-disable no-undef */
jQuery( document ).on( 'click', '.sunflower-plugins .notice-dismiss', function() {
	jQuery.ajax( {
		url: ajaxurl,
		data: {
			action: 'sunflower_plugins_dismiss',
		},
	} );
} );

jQuery( document ).ready( function() {
    jQuery( '#createHomepage' ).click( sunflowerCreateHomepage );
} );

function sunflowerCreateHomepage() {
	jQuery.ajax( {
		type: 'POST',
		url: sunflower.ajaxurl,
		data: {
			action: 'sunflowerCreateHomepage',
			title: sunflower.title,
		},
		success( json, textStatus, XMLHttpRequest ) {
			const data = JSON.parse( json );

			jQuery( '#createHomepage' ).hide();
			jQuery( '#createHomepageResponseLink' ).attr( 'href', `post.php?post=${ data.id }&action=edit` );
			jQuery( '#createHomepageResponse' ).show();
		},
		error( XMLHttpRequest, textStatus, errorThrown ) {
			//console.error( errorThrown );
		},
	} );
}
