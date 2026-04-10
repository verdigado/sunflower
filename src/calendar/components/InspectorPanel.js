import { __ } from '@wordpress/i18n';
import { InspectorControls } from '@wordpress/block-editor';
import { FormTokenField, PanelBody } from '@wordpress/components';

import TagColorPicker from './TagColorPicker';

/**
 * @param {Object}   props
 * @param {Array}    props.tagSuggestions    Available tag suggestions
 * @param {Array}    props.selectedTagNames  Current tag names
 * @param {Function} props.onTagChange       Tag change handler
 * @param {boolean}  props.hasResolved       Whether tags have loaded
 * @param {Array}    props.allTags           All available tags (full objects)
 * @param {Object}   props.tagColors         Current tag-color mapping
 * @param {Function} props.onTagColorsChange Tag color change handler
 * @return {Element} InspectorPanel component
 */
export default function InspectorPanel( {
	tagSuggestions,
	selectedTagNames,
	onTagChange,
	hasResolved,
	allTags,
	tagColors,
	onTagColorsChange,
} ) {
	return (
		<InspectorControls>
			<PanelBody
				title={ __( 'Filter', 'sunflower-calendar-events' ) }
				initialOpen
			>
				<FormTokenField
					label={ __( 'Tags', 'sunflower-calendar-events' ) }
					value={ selectedTagNames }
					onChange={ onTagChange }
					suggestions={ tagSuggestions }
					disabled={ ! hasResolved }
				/>
			</PanelBody>

			<PanelBody
				title={ __( 'Colors', 'sunflower-calendar-events' ) }
				initialOpen={ false }
			>
				<TagColorPicker
					allTags={ allTags }
					tagColors={ tagColors }
					onChange={ onTagColorsChange }
					hasResolved={ hasResolved }
				/>
			</PanelBody>
		</InspectorControls>
	);
}
