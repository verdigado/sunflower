/* eslint-disable no-undef */

function sunflowerRelayoutEventList() {
	const eventList = document.querySelector( '.event-list' );

	if ( ! eventList || typeof Masonry === 'undefined' ) {
		return;
	}

	const masonry = Masonry.data( eventList );

	if ( masonry ) {
		masonry.options.itemSelector = '.event-list > .col-12:not(.d-none)';
		masonry.reloadItems();
		masonry.layout();
	}
}

// Filter events by tags.
jQuery( '.filter-button-group' ).on( 'click', 'button', function () {
	const filterValue = jQuery( this ).attr( 'data-filter' );

	const eventTag = filterValue.substring( 1 );

	jQuery( '.event-list>*' ).addClass( 'd-none' );
	jQuery( filterValue, '.event-list' ).parent().removeClass( 'd-none' );

	jQuery( '.filter-active' ).removeClass( 'filter-active' );
	jQuery( this ).addClass( 'filter-active' );

	// append filter tag to ics download link
	jQuery( ' .calendar-download' )
		.attr( 'href', function ( index, href ) {
			const params = new URLSearchParams( href.split( '?' )[ 1 ] );
			if ( eventTag ) {
				params.set( 'sunflower_tag', eventTag );
			} else {
				params.delete( 'sunflower_tag' );
			}
			return href.split( '?' )[ 0 ] + '?' + params.toString();
		} )
		.text( function () {
			if ( eventTag && eventTag !== 'map' ) {
				return (
					sunflower.texts.icscalendar +
					' (' +
					eventTag.toUpperCase() +
					')'
				);
			}
			return sunflower.texts.icscalendar;
		} );

	window.location.hash = '#' + filterValue.substring( 1 );

	if ( filterValue === '*' ) {
		jQuery( '#leaflet' ).parent().addClass( 'd-none' );
	}

	window.requestAnimationFrame( sunflowerRelayoutEventList );
} );

// Open given filter on initial page load.
jQuery( function () {
	const hash = window.location.hash.substring( 1 );

	if ( hash !== '' ) {
		jQuery( 'button.filter[data-filter=".' + hash + '"]' ).click();
	}
} );

/* eslint-enable no-undef */
