import { useBlockProps } from '@wordpress/block-editor';

import './editor.scss';

import CalendarPreview from './components/CalendarPreview';
import InspectorPanel from './components/InspectorPanel';
import useEventTags from './hooks/useEventTags';

/**
 * @param {Object}   props
 * @param {Object}   props.attributes
 * @param {Function} props.setAttributes
 * @return {Element} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {
	const blockProps = useBlockProps( {
		className: 'row',
	} );

	const selectedTags = attributes.tag;
	const currentTagColors = attributes.tagColors;

	const {
		tagSuggestions,
		selectedTagNames,
		hasResolved,
		convertNamesToSlugs,
		allTags,
	} = useEventTags( selectedTags );

	const handleSelectedTagsChange = ( formTags ) => {
		setAttributes( { tag: convertNamesToSlugs( formTags ) } );
	};

	const handleTagColorsChange = ( newTagColors ) => {
		setAttributes( { tagColors: newTagColors } );
	};

	return (
		<div { ...blockProps }>
			<CalendarPreview selectedTagNames={ selectedTagNames } />

			<InspectorPanel
				tagSuggestions={ tagSuggestions }
				selectedTagNames={ selectedTagNames }
				onTagChange={ handleSelectedTagsChange }
				hasResolved={ hasResolved }
				allTags={ allTags }
				tagColors={ currentTagColors }
				onTagColorsChange={ handleTagColorsChange }
			/>
		</div>
	);
}
