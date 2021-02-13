/* eslint-disable no-undef */

jQuery( '.filter-button-group' ).on( 'click', 'button', function() {
    const filterValue = $( this ).attr( 'data-filter' );

    jQuery( '.event-list>*' ).addClass( 'd-none' );
    jQuery( filterValue, '.event-list' ).parent().removeClass( 'd-none' );

    jQuery( '.filter-active' ).removeClass( 'filter-active' );
    jQuery( this ).addClass( 'filter-active' );
});
