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

( function ( wp ) {
	const { addFilter } = wp.hooks;
	const { createHigherOrderComponent } = wp.compose;
	const { Fragment, useState, useEffect } = wp.element;
	const { apiFetch } = wp;

	// return early if disabled
	if ( sunflower.options.mediaCreator === 'disabled' ) {
		return;
	}
	// check if media has creator meta field
	const checkCreator = ( id, setHasCreator ) => {
		if ( ! id ) {
			setHasCreator( true );
			return;
		}

		apiFetch( { path: `/wp/v2/media/${ id }` } )
			.then( ( media ) => {
				const has =
					media?.meta?._media_creator &&
					media.meta._media_creator.trim() !== '';
				setHasCreator( has );
			} )
			.catch( () => {
				setHasCreator( true );
			} );
	};

	addFilter(
		'editor.BlockEdit',
		'sunflower/no-creator-warning',
		createHigherOrderComponent( ( BlockEdit ) => {
			return ( props ) => {
				// supported Blocks
				const supportedBlocks = [ 'core/image', 'core/media-text' ];
				if ( ! supportedBlocks.includes( props.name ) ) {
					return wp.element.createElement( BlockEdit, props );
				}

				// ID depends on block type
				const mediaId =
					props.name === 'core/image'
						? props.attributes.id
						: props.attributes.mediaId;

				const [ hasCreator, setHasCreator ] = useState( null );

				useEffect( () => {
					checkCreator( mediaId, setHasCreator );
				}, [ mediaId ] );

				if ( hasCreator === null ) {
					return wp.element.createElement( BlockEdit, props );
				}

				if (
					! hasCreator &&
					sunflower.options.mediaCreator === 'strict'
				) {
					const current = props.className || '';
					if ( ! current.includes( 'no-creator' ) ) {
						props.className = current + ' no-creator';
					}
				}

				if (
					props.name === 'core/media-text' &&
					props.attributes.mediaId
				) {
					if (
						! hasCreator &&
						( sunflower.options.mediaCreator === 'strict' ||
							sunflower.options.mediaCreator === 'required' )
					) {
						const current = props.attributes.className || '';
						if ( ! current.includes( 'no-creator' ) ) {
							props.attributes.className = (
								current + ' no-creator'
							).trim();
						}
					} else {
						const current = props.attributes.className || '';
						if ( current.includes( 'no-creator' ) ) {
							props.setAttributes( {
								className: current
									.replace( /\bno-creator\b/, '' )
									.trim(),
							} );
						}
					}
				}

				return wp.element.createElement(
					Fragment,
					null,
					wp.element.createElement(
						'div',
						{
							style: {
								position: 'relative',
								display: 'block',
							},
						},
						wp.element.createElement( BlockEdit, props ),
						! hasCreator &&
							( sunflower.options.mediaCreator === 'strict' ||
								sunflower.options.mediaCreator ===
									'required' ) &&
							wp.element.createElement(
								'span',
								{
									style: {
										position: 'absolute',
										top: '50%',
										left: '50%',
										transform: 'translate(-50%, -50%)',
										background: '#f0c419',
										color: '#000',
										padding: '10px',
										borderRadius: '5px',
										fontSize: '20px',
										fontWeight: 'bold',
										opacity: 1,
										transition: 'opacity 0.3s ease',
										pointerEvents: 'none',
										zIndex: 10,
									},
									className: 'sunflower-creator-warning',
								},
								sunflower.texts.creatorFieldEmpty
							)
					)
				);
			};
		}, 'withNoCreatorWarning' )
	);
} )( window.wp );
