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
	Disabled,
	FormTokenField,
	PanelBody,
	RangeControl,
	TextControl,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { useState, useEffect } from '@wordpress/element';
import { useEntityRecords } from '@wordpress/core-data';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

const EMPTY_ARRAY = [];

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

	const { title, tag, count, eventTitleFilter } = attributes;

	const [ tagFormSuggestions, setTagFormSuggestions ] =
		useState( EMPTY_ARRAY );
	const [ tagFormValue, setTagFormValue ] = useState( EMPTY_ARRAY );

	const query = { per_page: -1, context: 'view' };
	const { records: allTags, hasResolved } = useEntityRecords(
		'taxonomy',
		'sunflower_event_tag',
		query
	);

	useEffect( () => {
		if ( ! hasResolved ) {
			return;
		}

		setTagFormSuggestions( allTags.map( ( atag ) => atag.name ) );
		// accept tags as ids (pre 2.1.0) and slugs
		setTagFormValue(
			allTags
				.filter(
					( atag ) =>
						attributes.tag?.includes( atag.id ) ||
						attributes.tag?.includes( atag.slug )
				)
				.map( ( atag ) => atag.name )
		);
	}, [ allTags, hasResolved, tag, attributes.tag ] );

	const onChangeTitle = ( input ) => {
		setAttributes( { title: input === undefined ? '' : input } );
	};

	const onChangeTag = ( formTags ) => {
		setAttributes( {
			tag: formTags.map(
				( tagName ) =>
					allTags.find( ( atag ) => atag.name === tagName )?.slug
			),
		} );
	};

	return (
		<div { ...blockProps }>
			<Disabled>
				<ServerSideRender
					block={ 'sunflower/next-events' }
					attributes={ { title, tag, count, eventTitleFilter } }
				/>
			</Disabled>
			<InspectorControls>
				<PanelBody
					title={ __( 'Settings', 'sunflower-next-events' ) }
					initialOpen
				>
					<TextControl
						label={ __( 'Block title', 'sunflower-next-events' ) }
						help={ __(
							'Heading displayed above the events list',
							'sunflower-next-events'
						) }
						value={ title }
						placeholder={ __(
							'Next events',
							'sunflower-next-events'
						) }
						onChange={ onChangeTitle }
					/>
					<RangeControl
						label={ __(
							'Number of items',
							'sunflower-next-events'
						) }
						help={ __(
							'Number of events to show',
							'sunflower-next-events'
						) }
						value={ count }
						onChange={ ( value ) =>
							setAttributes( { count: value } )
						}
						min={ 6 }
						max={ 18 }
						step={ 6 }
					/>
				</PanelBody>
				<PanelBody
					title={ __( 'Filter', 'sunflower-next-events' ) }
					initialOpen={ false }
				>
					<TextControl
						label={ __(
							'Filter by title',
							'sunflower-next-events'
						) }
						help={ __(
							'Only show events whose title contains this text (case-insensitive)',
							'sunflower-next-events'
						) }
						value={ eventTitleFilter }
						onChange={ ( value ) =>
							setAttributes( { eventTitleFilter: value } )
						}
					/>
					<FormTokenField
						hasResolved={ hasResolved }
						label={ __( 'Tags', 'sunflower-next-events' ) }
						value={ tagFormValue }
						onChange={ onChangeTag }
						suggestions={ tagFormSuggestions }
					/>
				</PanelBody>
			</InspectorControls>
		</div>
	);
}
