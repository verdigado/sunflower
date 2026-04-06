/* globals wp */
/**
 * Flexible-Mode: Spalten-Control deaktivieren
 */
( function () {
	const addFilter = wp.hooks.addFilter;
	const createHOC = wp.compose.createHigherOrderComponent;
	const el = wp.element.createElement;
	const Fragment = wp.element.Fragment;
	const useSelect = wp.data.useSelect;
	const useEffect = wp.element.useEffect;
	const InspectorControls = wp.blockEditor.InspectorControls;
	const PanelBody = wp.components.PanelBody;
	const Notice = wp.components.Notice;

	const withFlexibleModeColumns = createHOC( function ( BlockEdit ) {
		return function ( props ) {
			if ( props.name !== 'core/latest-posts' ) {
				return el( BlockEdit, props );
			}

			const isFlexible = useSelect( function ( select ) {
				return (
					select( 'core/editor' ).getEditorSettings()
						.sunflowerPostImageMode === 'flexible'
				);
			}, [] );

			const isGrid =
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
