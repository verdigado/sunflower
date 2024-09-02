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

import {
	PanelBody,
	Disabled,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
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

	const { title, mailTo, requireMail, requirePhone, displayPhone, sendCopy } =
		attributes;

	const onChangeTitle = ( input ) => {
		setAttributes( { title: input === undefined ? '' : input } );
	};

	const onChangeMailTo = ( input ) => {
		setAttributes( { mailTo: input === undefined ? '' : input } );
	};

	function toggleAttribute( propName ) {
		return () => {
			const value = attributes[ propName ];

			setAttributes( { [ propName ]: ! value } );
		};
	}

	return (
		<div { ...blockProps }>
			{
				<>
					<Disabled>
						<ServerSideRender
							block={ 'sunflower/contact-form' }
							attributes={ {
								title,
								requireMail,
								requirePhone,
								displayPhone,
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
						<TextControl
							label={ __( 'Mail To', 'sunflower-contact-form' ) }
							help={ __(
								'Mail form to this address instead of default receiver.',
								'sunflower-contact-form'
							) }
							value={ mailTo }
							placeholder={ __(
								'default receiver',
								'sunflower-contact-form'
							) }
							type="email"
							onChange={ onChangeMailTo }
						/>
						<ToggleControl
							label={ __(
								'Require E-Mail',
								'sunflower-contact-form'
							) }
							checked={ requireMail }
							onChange={ toggleAttribute( 'requireMail' ) }
						/>
						{ requireMail && (
							<ToggleControl
								label={ __(
									'Send copy to sender',
									'sunflower-contact-form'
								) }
								checked={ sendCopy }
								onChange={ toggleAttribute( 'sendCopy' ) }
							/>
						) }
						<ToggleControl
							label={ __(
								'Display Phone Field',
								'sunflower-contact-form'
							) }
							checked={ displayPhone }
							onChange={ toggleAttribute( 'displayPhone' ) }
						/>
						{ displayPhone && (
							<ToggleControl
								label={ __(
									'Require Phone',
									'sunflower-contact-form'
								) }
								checked={ requirePhone }
								onChange={ toggleAttribute( 'requirePhone' ) }
							/>
						) }
					</PanelBody>
				</InspectorControls>
			}
		</div>
	);
}
