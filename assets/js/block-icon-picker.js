( function ( wp ) {
	const { addFilter } = wp.hooks;
	const { createElement } = wp.element;

	// 1. Attribute registrieren (falls noch nicht geschehen)
	addFilter(
		'blocks.registerBlockType',
		'sunflower/button-icon-attribute',
		( settings, name ) => {
			if ( name !== 'core/button' ) {
				return settings;
			}
			settings.attributes = {
				...settings.attributes,
				myIcon: {
					type: 'string',
					default: '',
				},
			};
			return settings;
		}
	);

	// 2. InspectorControl für Glyphenauswahl (wie vorher)
	const withIconControl = wp.compose.createHigherOrderComponent(
		( BlockEdit ) => ( props ) => {
			const { attributes, setAttributes, name } = props;
			if ( name !== 'core/button' ) {
				return createElement( BlockEdit, props );
			}

			const { InspectorControls } = wp.blockEditor;
			const { PanelBody, SelectControl } = wp.components;
			return createElement(
				wp.element.Fragment,
				{},
				createElement( BlockEdit, props ),
				createElement(
					InspectorControls,
					{},
					createElement(
						PanelBody,
						{ title: 'Icon auswählen', initialOpen: false },
						createElement( SelectControl, {
							label: 'Font Awesome Icon',
							value: attributes.myIcon,
							options: [
								{ label: '— Kein Icon —', value: '' },
								{
									label: '👍 Thumbs Up',
									value: 'fa-solid fa-thumbs-up',
								},
								{
									label: '❤️ Heart',
									value: 'fa-solid fa-heart',
								},
								{
									label: '✔️ Check',
									value: 'fa-solid fa-check',
								},
								{
									label: '💬​ Comment',
									value: 'fa-solid fa-comment',
								},
								{
									label: '👤​​ User',
									value: 'fa-solid fa-user',
								},
								{
									label: '✉️​ envelope',
									value: 'fa-solid fa-envelope',
								},
								{
									label: '⭐​​​ star',
									value: 'fa-solid fa-star',
								},
								{
									label: '➡️​​​ right',
									value: 'fa-solid fa-arrow-right',
								},
								{
									label: '⬅️​​​ left',
									value: 'fa-solid fa-arrow-left',
								},
								{
									label: '⬇️​​​ down',
									value: 'fa-solid fa-arrow-down',
								},
								{
									label: '⬆️​​​ up',
									value: 'fa-solid fa-arrow-up',
								},
							],
							onChange: ( val ) =>
								setAttributes( { myIcon: val } ),
						} )
					)
				)
			);
		},
		'withIconControl'
	);

	addFilter(
		'editor.BlockEdit',
		'sunflower/button-icon-control',
		withIconControl
	);

	// 3. Save-Vorgang anpassen – verschiebe Icon-Klasse auf <a>
	addFilter(
		'blocks.getSaveElement',
		'sunflower/button-icon-move',
		( element, blockType, attributes ) => {
			if ( blockType.name !== 'core/button' ) {
				return element;
			}
			const { myIcon } = attributes;
			if ( ! myIcon ) {
				return element;
			}

			// Nur fortfahren, wenn <a> Element vorhanden
			const wrapperClass = element.props.className || '';
			const linkElement = element.props.children;

			if (
				linkElement &&
				linkElement.props &&
				typeof linkElement.props.className === 'string'
			) {
				// Entferne FontAwesome-Klassen vom Wrapper
				element.props.className = wrapperClass
					.split( ' ' )
					.filter(
						( c ) =>
							! c.startsWith( 'fa-' ) && ! c.startsWith( 'fas' )
					)
					.join( ' ' );

				// Füge Icon-Klasse dem Link hinzu
				linkElement.props.className += ' ' + myIcon;
			}

			return element;
		}
	);
} )( window.wp );
