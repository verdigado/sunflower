/* eslint-disable no-undef */

// Filter events by tags.
jQuery( '.filter-button-group' ).on( 'click', 'button', function () {
	const filterValue = jQuery( this ).attr( 'data-filter' );

	jQuery( '.event-list>*' ).addClass( 'd-none' );
	jQuery( filterValue, '.event-list' ).parent().removeClass( 'd-none' );

	jQuery( '.filter-active' ).removeClass( 'filter-active' );
	jQuery( this ).addClass( 'filter-active' );

	window.location.hash = '#' + filterValue.substring( 1 );

	if ( filterValue === '*' ) {
		jQuery( '#leaflet' ).parent().addClass( 'd-none' );
	}
} );

// Open given filter on initial page load.
jQuery( function () {
	const hash = window.location.hash.substring( 1 );

	if ( hash !== '' ) {
		jQuery( '.filter[data-filter=".' + hash + '"]' ).on( 'click' );
	}
} );

/* eslint-enable no-undef */
