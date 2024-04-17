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
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';

import { PanelBody, Disabled, TextControl } from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';

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
 * @return {Element} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {
	const blockProps = useBlockProps( {
		className: 'row',
	} );

	const { title } = attributes;

	const onChangeTitle = ( input ) => {
		setAttributes( { title: input === undefined ? '' : input } );
	};

	return (
		<div { ...blockProps }>
			{
				<>
					<Disabled>
						<ServerSideRender
							block={ 'sunflower/contact-form' }
							attributes={ {
								title,
							} }
						/>
					</Disabled>
				</>
			}
			{
				<InspectorControls>
					<PanelBody title={ __( 'Settings' ) }>
						<div className="small">
							{ __(
								'The receiver address may be altered within the Sunflower theme settings. By default, emails are sent to the site administrator.',
								'sunflower-contact-form'
							) }
						</div>
						<br />
						<TextControl
							label={ __( 'Title' ) }
							help={ __(
								'Title of the form',
								'sunflower-contact-form'
							) }
							value={ title }
							placeholder={ __(
								'Contact Form',
								'sunflower-contact-form'
							) }
							onChange={ onChangeTitle }
						/>
					</PanelBody>
				</InspectorControls>
			}
		</div>
	);
}
