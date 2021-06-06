/* eslint-disable no-undef */

jQuery( '.filter-button-group' ).on( 'click', 'button', function() {
    const filterValue = jQuery( this ).attr( 'data-filter' );

    jQuery( '.event-list>*' ).addClass( 'd-none' );
    jQuery( filterValue, '.event-list' ).parent().removeClass( 'd-none' );

    jQuery( '.filter-active' ).removeClass( 'filter-active' );
    jQuery( this ).addClass( 'filter-active' );

    window.location.hash = '#' + filterValue.substr(1);

    if( filterValue == '*'){
        jQuery('#leaflet').parent().addClass( 'd-none' );
    }
});

jQuery(document).ready( function (){
    const hash = window.location.hash.substr(1);

    if( hash !== '' ){
        jQuery('.filter[data-filter=".' + hash + '"]').click();
    }
});
