/* global jQuery */
/* global sunflower */
jQuery( function ( $ ) {
	// Verify some fields sent by ajax.
	$( document ).ajaxSend( function ( event, jqXHR, ajaxOptions ) {
		const data = ajaxOptions.data || '';

		if ( /action=save-attachment/.test( data ) ) {
			$( 'form.compat-item' ).each( function () {
				const $form = $( this );

				// Check media_creator field
				const $creator = $form.find(
					'textarea[name$="[media_creator]"]'
				);
				const creatorRequired = $creator.prop( 'required' );
				const creatorEmpty = ! $creator.val().trim();

				if ( creatorRequired && creatorEmpty ) {
					jqXHR.abort();

					$creator.addClass( 'error' );
					if ( ! $form.find( '.creator-error' ).length ) {
						$creator.after(
							'<p class="notice notice-error">' +
								sunflower.texts.creatorFieldEmpty +
								'</p>'
						);
					}
					return;
				}
				$creator.removeClass( 'error' );
				$form.find( '.creator-error' ).remove();

				// Check alt-text and show warning if missing.
				const $altField = $(
					'#attachment-details-two-column-alt-text'
				);
				const $altDescription = $( '#alt-text-description' );
				if ( $altField.length && ! $altField.val().trim() ) {
					if ( $altDescription.prev( '.alt-warning' ).length === 0 ) {
						$(
							'<p class="clear notice notice-warning">' +
								sunflower.texts.emptyAltText +
								'</p>'
						).insertBefore( $altDescription );
					}
				} else {
					$altDescription.prev( '.alt-warning' ).remove();
				}
			} );
		}
	} );
} );
