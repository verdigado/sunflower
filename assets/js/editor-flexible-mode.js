/* globals wp */
/**
 * Flexible-Mode: Spalten-Control im WP Core "Neueste Beiträge"-Block deaktivieren.
 *
 * Wenn die Theme-Einstellung "Beitragsbilder" auf "Flexibel" gesetzt ist, zeigt
 * der Core-Block eine Info-Notice statt des Spalten-Sliders – da im Masonry-Modus
 * immer 2 Spalten verwendet werden.
 */
( function () {
	var addFilter          = wp.hooks.addFilter;
	var createHOC          = wp.compose.createHigherOrderComponent;
	var el                 = wp.element.createElement;
	var Fragment           = wp.element.Fragment;
	var useSelect          = wp.data.useSelect;
	var useEffect          = wp.element.useEffect;
	var InspectorControls  = wp.blockEditor.InspectorControls;
	var PanelBody          = wp.components.PanelBody;
	var Notice             = wp.components.Notice;

	var withFlexibleModeColumns = createHOC( function ( BlockEdit ) {
		return function ( props ) {
			if ( props.name !== 'core/latest-posts' ) {
				return el( BlockEdit, props );
			}

			var isFlexible = useSelect( function ( select ) {
				return (
					select( 'core/editor' ).getEditorSettings()
						.sunflowerPostImageMode === 'flexible'
				);
			}, [] );

			var isGrid =
				props.attributes.displayLayout &&
				props.attributes.displayLayout.type === 'grid';

			// Im Flexibel-Modus Spaltenanzahl auf 2 zurücksetzen, damit der
			// gespeicherte Wert mit dem Masonry-Layout übereinstimmt.
			useEffect(
				function () {
					if (
						isFlexible &&
						isGrid &&
						props.attributes.displayLayout.columns !== 2
					) {
						props.setAttributes( {
							displayLayout: { type: 'grid', columns: 2 },
						} );
					}
				},
				[ isFlexible, isGrid ] // eslint-disable-line react-hooks/exhaustive-deps
			);

			if ( ! isFlexible || ! isGrid ) {
				return el( BlockEdit, props );
			}

			return el(
				Fragment,
				null,
				el( BlockEdit, props ),
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: 'Beitragsbilder-Modus', initialOpen: true },
						el(
							Notice,
							{ status: 'warning', isDismissible: false },
							'Im \u201eFlexibel\u201c-Modus (Masonry-Grid) wird das Raster immer zweispaltig dargestellt. Die Spaltenanzahl kann nur im \u201eModern\u201c-Modus ge\u00e4ndert werden.'
						)
					)
				)
			);
		};
	}, 'withFlexibleModeColumns' );

	addFilter(
		'editor.BlockEdit',
		'sunflower/flexible-mode-columns',
		withFlexibleModeColumns
	);
} )();
