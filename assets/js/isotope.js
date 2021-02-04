/* eslint-disable no-undef */
const $isotope = jQuery( '.event-list' ).isotope( {

    itemSelector: '.event-card'
} );

jQuery( '.isotope-button-group' ).on( 'click', 'button', function() {
    var filterValue = $(this).attr('data-filter');
    $isotope.isotope( { filter: filterValue } );
});
