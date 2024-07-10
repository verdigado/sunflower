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
	SelectControl,
} from '@wordpress/components';

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

	const { url, icon } = attributes;

	const iconSelect = [];

	const onChangeIcon = ( newIcon ) => {
		setAttributes( { icon: newIcon === undefined ? 'none' : newIcon } );
	};

	const onChangeIconSelect = ( newIcon ) => {
		setAttributes( { icon: newIcon === undefined ? 'none' : newIcon } );
	};

	function validateEmail( email ) {
		const re = /^[^\s@\/]+@[^\s@]+\.[^\s@]{2,10}$/;
		return re.test( email );
	}

	const onChangeUrl = ( newUrl ) => {
		if (
			validateEmail( newUrl ) &&
			! ( newUrl.substring( 0, 7 ) === 'mailto:' )
		) {
			newUrl = 'mailto:' + newUrl;
		}
		setAttributes( { url: newUrl === undefined ? '' : newUrl } );
	};

	return (
		<div { ...blockProps }>
			{
				<>
					<Disabled>
						<a
							href={ url }
							target="_blank"
							className={
								url === '#' || url === ''
									? 'text-danger'
									: 'text-ok'
							}
							rel="noopener noreferrer"
						>
							<i className={ icon }></i>
						</a>
					</Disabled>
				</>
			}
			{
				<InspectorControls>
					<PanelBody title={ __( 'Settings' ) }>
						<SelectControl
							label={ __(
								'Predefined Icons',
								'sunflower-meta-data'
							) }
							value={ iconSelect }
							options={ [
								{ value: 'none', label: 'Bitte wählen' },
								{
									value: 'fab fa-x-twitter',
									label: 'X (Twitter)',
								},
								{
									value: 'fab fa-bluesky',
									label: 'Bluesky',
								},
								{ value: 'fab fa-twitter', label: 'Twitter' },
								{
									value: 'fab fa-instagram',
									label: 'Instragram',
								},
								{
									value: 'fab fa-facebook-f',
									label: 'Facebook',
								},
								{
									value: 'fab fa-whatsapp',
									label: 'WhatsApp',
								},
								{
									value: 'fab fa-threads',
									label: 'Threads',
								},
								{
									value: 'fab fa-tiktok',
									label: 'TikTok',
								},
								{ value: 'fab fa-mastodon', label: 'Mastodon' },
								{ value: 'fab fa-youtube', label: 'YouTube' },
								{ value: 'fas fa-envelope', label: 'E-Mail' },
								{ value: 'fas fa-globe', label: 'Website' },
							] }
							onChange={ onChangeIconSelect }
						/>

						<TextControl
							label={ __(
								'Selected Icon',
								'sunflower-meta-data'
							) }
							help={ __(
								'All icons can be found at https://fontawesome.com/icons?d=gallery&m=free',
								'sunflower-meta-data'
							) }
							value={ icon }
							onChange={ onChangeIcon }
						/>

						<TextControl
							label="URL"
							help={ __(
								'Target URL the Icon is linked to. For email addresses, use the format mailto:example@example.com.',
								'sunflower-meta-data'
							) }
							value={ url }
							placeholder={ 'https://gruene.social/@verdigado' }
							onChange={ onChangeUrl }
						/>
					</PanelBody>
				</InspectorControls>
			}
		</div>
	);
}
