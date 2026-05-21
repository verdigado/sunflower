import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { Fragment, cloneElement } from '@wordpress/element';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import './styles.scss';

const ICON_OPTIONS = [
	{ label: '— Kein Icon —', value: '' },
	{ label: '👍 Thumbs Up', value: 'fa-solid fa-thumbs-up' },
	{ label: '❤️  Heart', value: 'fa-solid fa-heart' },
	{ label: '✔️  Check', value: 'fa-solid fa-check' },
	{ label: '💬 Comment', value: 'fa-solid fa-comment' },
	{ label: '👤 User', value: 'fa-solid fa-user' },
	{ label: '✉️  Envelope', value: 'fa-solid fa-envelope' },
	{ label: '⭐ Star', value: 'fa-solid fa-star' },
	{ label: 'Download', value: 'fa-solid fa-download' },
	{ label: 'Upload', value: 'fa-solid fa-upload' },
	{ label: '➡️  Right', value: 'fa-solid fa-arrow-right' },
	{ label: '⬅️  Left', value: 'fa-solid fa-arrow-left' },
	{ label: '⬇️  Down', value: 'fa-solid fa-arrow-down' },
	{ label: '⬆️  Up', value: 'fa-solid fa-arrow-up' },
];

/* -----------------------------------------------
 * 1) Attribut registrieren
 * --------------------------------------------- */
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

/* -----------------------------------------------
 * 2) Inspector-Control (Dropdown)
 * --------------------------------------------- */
const withIconControl = createHigherOrderComponent( ( BlockEdit ) => {
	return ( props ) => {
		const { name, attributes, setAttributes } = props;

		if ( name !== 'core/button' ) {
			return <BlockEdit { ...props } />;
		}

		return (
			<Fragment>
				<BlockEdit { ...props } />
				<InspectorControls>
					<PanelBody
						title={ __( 'Icon auswählen', 'sunflower' ) }
						initialOpen={ false }
					>
						<SelectControl
							label={ __( 'Font Awesome Icon', 'sunflower' ) }
							value={ attributes.myIcon }
							options={ ICON_OPTIONS }
							onChange={ ( val ) =>
								setAttributes( { myIcon: val } )
							}
						/>
					</PanelBody>
				</InspectorControls>
			</Fragment>
		);
	};
}, 'withIconControl' );

addFilter(
	'editor.BlockEdit',
	'sunflower/button-icon-control',
	withIconControl
);

/* -----------------------------------------------
 * 3) Save: Icon-Klasse auf das <a>-Tag verschieben
 *    (sauber mit cloneElement statt Mutation)
 * --------------------------------------------- */
addFilter(
	'blocks.getSaveElement',
	'sunflower/button-icon-save',
	( element, blockType, attributes ) => {
		if ( blockType.name !== 'core/button' ) {
			return element;
		}

		const { myIcon } = attributes;
		if ( ! myIcon ) {
			return element;
		}

		// Struktur: <div class="wp-block-button"><a class="…">Text</a></div>
		const linkElement = element.props?.children;

		// Sicherheits-Check
		if (
			! linkElement?.props ||
			typeof linkElement.props.className !== 'string'
		) {
			return element;
		}

		// Neues <a> mit Icon-Klasse
		const newLink = cloneElement( linkElement, {
			className: linkElement.props.className + ' ' + myIcon,
		} );

		// Neues <div> – FA-Klassen entfernen falls vorhanden
		const cleanWrapperClass = ( element.props.className || '' )
			.split( ' ' )
			.filter(
				( c ) =>
					! c.startsWith( 'fa-' ) &&
					c !== 'fas' &&
					c !== 'far' &&
					c !== 'fab'
			)
			.join( ' ' );

		return cloneElement(
			element,
			{ className: cleanWrapperClass },
			newLink
		);
	}
);

/* -----------------------------------------------
 * 4) Editor-Preview: Wrapper bekommt die FA-Klasse
 * --------------------------------------------- */
addFilter(
	'editor.BlockListBlock',
	'sunflower/button-icon-editor-preview',
	( BlockListBlock ) => {
		return ( props ) => {
			if ( props.name !== 'core/button' ) {
				return <BlockListBlock { ...props } />;
			}

			const { myIcon } = props.attributes;
			if ( ! myIcon ) {
				return <BlockListBlock { ...props } />;
			}

			// Icon-Klasse(n) als data-Attribut UND als Klasse setzen
			const newWrapperProps = {
				...props.wrapperProps,
				'data-icon': myIcon,
				className: [
					props.wrapperProps?.className,
					'has-fa-icon',
					myIcon,
				]
					.filter( Boolean )
					.join( ' ' ),
			};

			return (
				<BlockListBlock { ...props } wrapperProps={ newWrapperProps } />
			);
		};
	}
);
