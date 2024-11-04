/* eslint-disable no-undef */

// Send POST request to peristent dismiss sunflower notices in backend.
jQuery( function ( $ ) {
	$( document ).on(
		'click',
		'.sunflower-plugins .notice-dismiss',
		function () {
			const id = $( this ).parent().attr( 'id' );
			jQuery.ajax( {
				url: ajaxurl,
				method: 'POST',
				data: {
					id,
					_wpnonce: $( '#' + id + ' #_wpnonce' ).val(),
					action: 'sunflower_plugins_dismiss',
				},
			} );
		}
	);
} );

// Methods for edition the location of manual added events.
jQuery( function () {
	jQuery( '#_sunflower_event_show_map' ).on( 'click', function () {
		if ( jQuery( '#_sunflower_event_show_map' ).is( ':checked' ) ) {
			jQuery( '#sunflower-map-settings' ).show();
		} else {
			jQuery( '#sunflower-map-settings' ).hide();
		}
	} );
	jQuery( '#sunflower-fix-location-delete' ).on( 'click', function () {
		sunflowerFixLocationDelete();
	} );
	jQuery( '#sunflower-location' ).on( 'change', function () {
		sunflowerFixLocation();
	} );
} );

let marker;
let leaflet = null;
/* eslint-disable-next-line no-unused-vars */
function sunflowerShowLeaflet( lat, lon, zoom, showMarker ) {
	if ( lat === -1 && lon === -1 && zoom === -1 ) {
		const address = [];

		const street = jQuery(
			'input[name="_sunflower_event_location_street"]'
		).val();
		const city = jQuery(
			'input[name="_sunflower_event_location_city"]'
		).val();

		if ( street ) {
			address.push( street.trim() );
		}

		if ( city ) {
			address.push( city.trim() );
		}
		jQuery.ajax( {
			url: 'https://nominatim.openstreetmap.org/search?format=json',
			method: 'GET',
			data: {
				q: address.join( ', ' ),
			},
			async: false,
			success( response ) {
				lat = response[ 0 ].lat;
				lon = response[ 0 ].lon;
				zoom = 12;
			},
		} );
	}

	if ( leaflet ) {
		jQuery( '#leaflet div' ).show();
		// do not initialize again
		return;
	}
	leaflet = L.map( 'leaflet' ).setView( [ lat, lon ], zoom );
	L.tileLayer( 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
		maxZoom: 19,
		attribution:
			'&copy; <a href="https://openstreetmap.org/copyright">OpenStreetMap contributors</a>',
	} ).addTo( leaflet );

	marker = L.marker( [ lat, lon ] ).addTo( leaflet );

	if ( showMarker ) {
		sunflowerSetMarker( lat, lon, leaflet.getZoom(), marker, false );
	} else {
		jQuery( '#sunflower-location-row' ).show();
	}

	leaflet.addEventListener( 'click', function ( ev ) {
		sunflowerSetMarker(
			ev.latlng.lat,
			ev.latlng.lng,
			leaflet.getZoom(),
			marker,
			true
		);
	} );
	jQuery( '#sunflowerShowMap' ).hide();
}

function sunflowerSetMarker( lat, lon, zoom, setmarker, update ) {
	jQuery( '#_sunflower_event_lat' ).val( lat );
	jQuery( '#_sunflower_event_lon' ).val( lon );
	jQuery( '#_sunflower_event_zoom' ).val( zoom );

	setmarker.setLatLng( new L.LatLng( lat, lon ) );

	if ( update === true ) {
		const address = [];

		const street = jQuery(
			'input[name="_sunflower_event_location_street"]'
		).val();
		const city = jQuery(
			'input[name="_sunflower_event_location_city"]'
		).val();

		if ( street ) {
			address.push( street.trim() );
		}

		if ( city ) {
			address.push( city.trim() );
		}

		jQuery.ajax( {
			url: ajaxurl,
			method: 'POST',
			data: {
				action: 'sunflower_fix_event_location',
				_wpnonce: jQuery( '#_wpnonce-locationfix' ).val(),
				lat,
				lon,
				do: 'update',
				transient: address.join( ', ' ),
			},
		} );
	}
}

function sunflowerFixLocation() {
	const latlon = jQuery( '#sunflower-location' ).val().split( ';' );
	marker.setLatLng( new L.LatLng( latlon[ 0 ], latlon[ 1 ] ) );
}

function sunflowerFixLocationDelete() {
	const transient = jQuery( '#sunflower-location option:selected' ).text();
	const poiSelected = jQuery( '#sunflower-location option:selected' ).val();

	// remove von von current select list
	jQuery( '#sunflower-location' )
		.children( 'option[value="' + poiSelected + '"]' )
		.remove();
	jQuery.ajax( {
		url: ajaxurl,
		method: 'POST',
		data: {
			action: 'sunflower_fix_event_location',
			_wpnonce: jQuery( '#_wpnonce-locationfix' ).val(),
			transient,
			do: 'delete',
		},
	} );
}

// show / hide help text for terms of use
jQuery( function ( $ ) {
	// hide help text if checkbox is already checked
	if ( $( '#sunflower_terms_of_use' ).is( ':checked' ) ) {
		$( '#help-sunflower-terms-condition' ).hide();
	}

	// hide help text if checkbox get's checked
	$( '#sunflower_terms_of_use:checkbox' ).on( 'change', function () {
		if ( $( '#sunflower_terms_of_use' ).is( ':checked' ) ) {
			$( '#help-sunflower-terms-condition' ).hide();
		} else {
			$( '#help-sunflower-terms-condition' ).show();
		}
	} );
} );

/* eslint-enable no-undef */
