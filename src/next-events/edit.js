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
	BlockControls,
	InspectorControls,
} from '@wordpress/block-editor';

import {
	Disabled,
	FormTokenField,
	PanelBody,
	RangeControl,
	TextControl,
	ToolbarGroup,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { grid, list } from '@wordpress/icons';
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

	const { title, tag, count, blockLayout } = attributes;

	const toolbarControls = [
		{
			icon: list,
			title: __( 'List view' ),
			onClick: () => setAttributes( { blockLayout: 'list' } ),
			isActive: blockLayout === 'list',
		},
		{
			icon: grid,
			title: __( 'Grid view' ),
			onClick: () => setAttributes( { blockLayout: 'grid' } ),
			isActive: blockLayout === 'grid',
		},
	];

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
					allTags
						.filter( ( atag ) => atag.name === tagName )
						.map( ( atag ) => atag.slug )[ 0 ]
			),
		} );
	};

	const onChangeCount = ( value ) => {
		setAttributes( { count: value } );
	};

	return (
		<div { ...blockProps }>
			{
				<>
					<BlockControls>
						<ToolbarGroup controls={ toolbarControls } />
					</BlockControls>
					<Disabled>
						<ServerSideRender
							block={ 'sunflower/next-events' }
							attributes={ {
								title,
								blockLayout,
								tag,
								count,
								tagFormValue,
							} }
						/>
					</Disabled>
				</>
			}
			{
				<InspectorControls>
					<PanelBody title={ __( 'Filter' ) } initialOpen>
						<TextControl
							label={ __( 'Title' ) }
							help={ __(
								'Title of the block section',
								'sunflower-latest-posts'
							) }
							value={ title }
							placeholder={ __(
								'Next events',
								'sunflower-next-events'
							) }
							onChange={ onChangeTitle }
						/>

						<FormTokenField
							hasResolved={ hasResolved }
							label={ __( 'Tags' ) }
							value={ tagFormValue }
							onChange={ onChangeTag }
							suggestions={ tagFormSuggestions }
						/>

						<RangeControl
							label={ __( 'Number of items' ) }
							help={ __(
								'Number of posts to be shown',
								'sunflower-latest-posts'
							) }
							value={ count }
							onChange={ onChangeCount }
							min={ 1 }
							max={ 20 }
						/>
					</PanelBody>
				</InspectorControls>
			}
		</div>
	);
}
