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
	TextControl,
	PanelBody,
	ToolbarGroup,
	Disabled,
} from '@wordpress/components';
import ServerSideRender from '@wordpress/server-side-render';
import { grid, list } from '@wordpress/icons';

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

	const onChangeCategories = ( input ) => {
		setAttributes( { categories: input === undefined ? '' : input } );
	};

	const onChangeCount = ( input ) => {
		setAttributes( { count: input === undefined ? '' : input } );
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

	const getWpCategories = () => {
		const categorieSlugs = new Array();
		wp.data
			.select( 'core' )
			.getEntityRecords( 'taxonomy', 'category' )
			?.forEach( ( element ) => {
				categorieSlugs.push( String( element.slug ) );
			} );

		return categorieSlugs;
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
							block={ 'sunflower/latest-posts' }
							attributes={ {
								title,
								blockLayout,
								categories,
								count,
								archiveText,
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

						<TextControl
							label={ __( 'Categories' ) }
							help={ __(
								'Comma separated list of category slugs to be shown. Leave empty for all.',
								'sunflower-latest-posts'
							) }
							value={ categories }
							onChange={ onChangeCategories }
						/>

						<div className="small sunflower-sidebar-help">
							{ __(
								'The following category slugs are available:',
								'sunflower-latest-posts'
							) }
							<ul>
								{ getWpCategories().map( ( category ) => {
									return (
										<li key={ category }>{ category }</li>
									);
								} ) }
							</ul>
						</div>

						<TextControl
							label={ __( 'Number of items' ) }
							help={ __(
								'Number of posts to be shown',
								'sunflower-latest-posts'
							) }
							value={ count }
							onChange={ onChangeCount }
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
