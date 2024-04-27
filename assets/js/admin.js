/* eslint-disable no-undef */
jQuery(function($) {
	$(document).on( 'click', '.sunflower-plugins .notice-dismiss', function() {
		jQuery.ajax( {
			url: ajaxurl,
			method: "POST",
			data: {
				id: $(this).parent().attr('id'),
				_wpnonce: $('#_wpnonce').val(),
				action: 'sunflower_plugins_dismiss',
			},
		} );
	} );
});


jQuery( document ).ready( function() {
	jQuery( '#sunflowerDeleteMap' ).click( () => sunflowerDeleteLeaflet( ) );
	jQuery( '#sunflower-fix-location-delete' ).click( () => sunflowerFixLocationDelete( ) );
	jQuery( '#sunflower-location' ).change( () => sunflowerFixLocation( ) );
} );


function sunflowerDeleteLeaflet() {
	jQuery( '#_sunflower_event_lat' ).val( '' );
	jQuery( '#_sunflower_event_lon' ).val( '' );
	jQuery( '#_sunflower_event_zoom' ).val( '' );
    jQuery( '#leaflet' ).hide();
    jQuery( '#sunflowerShowMap' ).show();
}

var marker;
var leaflet = null;
function sunflowerShowLeaflet( lat, lon, zoom, showMarker ) {
    if (leaflet) {
        jQuery( '#leaflet' ).show();
        // do not initalize again
        return;
    }
    leaflet = L.map( 'leaflet' ).setView( [ lat, lon ], zoom );
    L.tileLayer( 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
		maxZoom: 19,
		attribution: '&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap contributors</a>'
	} ).addTo( leaflet );

	marker = L.marker( [ lat, lon ] ).addTo( leaflet );

	if( showMarker ){
		sunflowerSetMarker( lat, lon, leaflet.getZoom(), marker );
	} else{
		jQuery( '#sunflower-location-row' ).show();
	}

	leaflet.addEventListener( 'click', function( ev ) {
		sunflowerSetMarker( ev.latlng.lat, ev.latlng.lng, leaflet.getZoom(), marker );
	} );

	jQuery( '#sunflowerShowMap' ).hide();
}

function sunflowerSetMarker( lat, lon, zoom, marker ) {
	jQuery( '#_sunflower_event_lat' ).val( lat );
	jQuery( '#_sunflower_event_lon' ).val( lon );
	jQuery( '#_sunflower_event_zoom' ).val( zoom );

	marker.setLatLng( new L.LatLng( lat, lon ) );

	// do this only for location fixing
	if(jQuery( "#sunflower-location").length == 0 ){
		return;
	}

	jQuery.ajax( {
		url: ajaxurl,
		method: "POST",
		data: {
			action: 'sunflower_fix_event_location',
			_wpnonce: jQuery('#_wpnonce').val(),
			lat: lat,
			lon: lon,
			transient: jQuery( "#sunflower-location option:selected" ).text(),
		},
	} );
}

function sunflowerFixLocation(){
	const latlon = jQuery('#sunflower-location').val().split(';');
	marker.setLatLng( new L.LatLng( latlon[0], latlon[1] ) );
}

function sunflowerFixLocationDelete(){
	jQuery.ajax( {
		url: ajaxurl,
		method: "POST",
		data: {
			action: 'sunflower_fix_event_location',
			lat: 0,
			lon: 0,
			transient: jQuery( "#sunflower-location option:selected" ).text(),
		},
	} );
}


// show / hide help text for terms of use
jQuery(function($) {
    // hide help text if checkbox is already checked
    if ($('#sunflower_terms_of_use').is(':checked')) {
        $('#help-sunflower-terms-condition').hide();
    }

    // hide help text if checkbox get's checked
    $('#sunflower_terms_of_use:checkbox').on("change", function() {
        if ($('#sunflower_terms_of_use').is(':checked')) {
            $('#help-sunflower-terms-condition').hide();
        } else {
            $('#help-sunflower-terms-condition').show();
        }
    });
});
