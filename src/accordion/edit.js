/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import {
	useBlockProps,
	RichText,
	InspectorControls,
	BlockControls,
	HeadingLevelDropdown,
} from '@wordpress/block-editor';

import { PanelBody, SelectControl, TextControl } from '@wordpress/components';
import { useEffect } from '@wordpress/element';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @param {Object}   props               React props.
 * @param {Object}   props.attributes
 * @param {Function} props.setAttributes
 * @param {string}   props.clientId
 * @return {Element} Element to render.
 */
export default function Edit( { attributes, setAttributes, clientId } ) {
	const blockProps = useBlockProps( {
		className: 'accordion',
	} );

	useEffect( () => {
		setAttributes( { blockId: clientId } );
	}, [ clientId, setAttributes ] );

	const onChangeContent = ( newContent ) => {
		setAttributes( { content: newContent } );
	};

	const onChangeHeadline = ( newHeadline ) => {
		setAttributes( { headline: newHeadline } );
	};

	const HeadingTag = `h${ attributes.headingLevel }`;

	return (
		<div { ...blockProps }>
			<BlockControls group="block">
				<HeadingLevelDropdown
					value={ attributes.headingLevel }
					options={ [ 2, 3, 4, 5, 6 ] }
					onChange={ ( level ) =>
						setAttributes( { headingLevel: level } )
					}
				/>
			</BlockControls>
			<InspectorControls>
				<PanelBody title={ __( 'Settings', 'sunflower-accordion' ) }>
					<SelectControl
						__nextHasNoMarginBottom
						label={ __( 'Heading level', 'sunflower-accordion' ) }
						help={ __(
							'All items of one accordion should use the same level.',
							'sunflower-accordion'
						) }
						value={ String( attributes.headingLevel ) }
						options={ [ 2, 3, 4, 5, 6 ].map( ( level ) => ( {
							label: `H${ level }`,
							value: String( level ),
						} ) ) }
						onChange={ ( value ) =>
							setAttributes( {
								headingLevel: parseInt( value, 10 ),
							} )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<div className="accordion-item alignfull">
				<HeadingTag className="accordion-header">
					<TextControl
						className="accordion-button"
						hideLabelFromVision="true"
						label={ __(
							'Clickable text opening the selection',
							'sunflower-accordion'
						) }
						onChange={ onChangeHeadline }
						value={ attributes.headline }
						placeholder={ __(
							'Titel of the accordion item',
							'sunflower-accordion'
						) }
					/>
				</HeadingTag>
				<div className="accordion-body">
					<RichText
						onChange={ onChangeContent }
						value={ attributes.content }
						placeholder={ __(
							'Text content of the openend accordion item.',
							'sunflower-accordion'
						) }
					/>
				</div>
			</div>
		</div>
	);
}
