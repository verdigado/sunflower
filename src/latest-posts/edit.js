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
	RangeControl,
	PanelBody,
	TextControl,
	ToolbarGroup,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { grid, list } from '@wordpress/icons';
import { useEntityRecords } from '@wordpress/core-data';
import { useState, useEffect } from '@wordpress/element';

const EMPTY_ARRAY = [];

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

	const { title, categories, count, archiveText, blockLayout } = attributes;

	const [ categoriesFormSuggestions, setCategoriesFormSuggestions ] =
		useState( EMPTY_ARRAY );
	const [ categoriesFormValue, setCategoriesFormValue ] =
		useState( EMPTY_ARRAY );

	const query = { per_page: -1, context: 'view' };

	const { records: allCategories, hasResolved } = useEntityRecords(
		'taxonomy',
		'category',
		query
	);

	useEffect( () => {
		if ( ! hasResolved ) return;

		setCategoriesFormSuggestions(
			allCategories.map( ( category ) => category.name )
		);
		setCategoriesFormValue(
			allCategories
				.filter( () =>
					attributes.categories?.includes( categories.slug )
				)
				.map( ( category ) => category.name )
		);
	}, [ allCategories, hasResolved, categories, attributes.categories ] );

	const onChangeCategories = ( formCategories ) => {
		setAttributes( {
			categories: formCategories.map(
				( categoryName ) =>
					allCategories
						.filter(
							( category ) => category.name === categoryName
						)
						.map( ( category ) => category.slug )[ 0 ]
			),
		} );
	};

	const onChangeCount = ( value ) => {
		setAttributes( { count: value } );
	};

	const onChangeTitle = ( input ) => {
		setAttributes( { title: input === undefined ? '' : input } );
	};

	const onChangeArchiveText = ( input ) => {
		setAttributes( { archiveText: input === undefined ? '' : input } );
	};

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

	return (
		<div { ...blockProps }>
			{
				<>
					<BlockControls>
						<ToolbarGroup controls={ toolbarControls } />
					</BlockControls>
					<Disabled>
						<ServerSideRender
							block={ 'sunflower/latest-posts' }
							attributes={ {
								title,
								blockLayout,
								categories,
								count,
								archiveText,
								categoriesFormValue,
							} }
						/>
					</Disabled>
				</>
			}
			{
				<InspectorControls>
					<PanelBody title={ __( 'Settings' ) }>
						<TextControl
							label={ __( 'Title' ) }
							help={ __(
								'Title of the block section',
								'sunflower-latest-posts'
							) }
							value={ title }
							onChange={ onChangeTitle }
						/>

						<FormTokenField
							hasResolved={ hasResolved }
							label={ __( 'Categories' ) }
							value={ categoriesFormValue }
							onChange={ onChangeCategories }
							suggestions={ categoriesFormSuggestions }
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

						<TextControl
							label={ __(
								'Archive Text',
								'sunflower-latest-posts'
							) }
							help={ __(
								'Link label of the archive link',
								'sunflower-latest-posts'
							) }
							placeholder={ __(
								'to archive',
								'sunflower-latest-posts'
							) }
							value={ archiveText }
							onChange={ onChangeArchiveText }
						/>
					</PanelBody>
				</InspectorControls>
			}
		</div>
	);
}
