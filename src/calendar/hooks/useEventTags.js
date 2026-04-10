import { useEntityRecords } from '@wordpress/core-data';

import { EMPTY_ARRAY, ENTITY_CONFIG } from '../constants';

const EVENT_TAG_QUERY = {
	per_page: ENTITY_CONFIG.PER_PAGE_ALL,
	context: ENTITY_CONFIG.CONTEXT,
};

/**
 * Custom hook for managing event tags
 *
 * @param {Array} selectedTags Currently selected tag slugs
 * @return {Object} Tag management object
 */
export default function useEventTags( selectedTags ) {
	const { records: allTags, hasResolved } = useEntityRecords(
		ENTITY_CONFIG.TAXONOMY,
		ENTITY_CONFIG.EVENT_TAG,
		EVENT_TAG_QUERY
	);

	const availableTags = allTags || EMPTY_ARRAY;
	const normalizedSelectedTags = Array.isArray( selectedTags )
		? selectedTags
		: EMPTY_ARRAY;
	const tagNamesBySlug = new Map(
		availableTags.map( ( tag ) => [ tag.slug, tag.name ] )
	);
	const tagSlugsByName = new Map(
		availableTags.map( ( tag ) => [ tag.name, tag.slug ] )
	);
	const selectedTagNames = normalizedSelectedTags
		.map( ( selectedTag ) => {
			if ( tagNamesBySlug.has( selectedTag ) ) {
				return tagNamesBySlug.get( selectedTag );
			}

			const selectedTagObject = availableTags.find(
				( tag ) =>
					tag.id === selectedTag || String( tag.id ) === selectedTag
			);

			return selectedTagObject?.name;
		} )
		.filter( Boolean );

	const convertNamesToSlugs = ( tagNames ) => {
		return tagNames
			.map( ( tagName ) => tagSlugsByName.get( tagName ) )
			.filter( Boolean );
	};

	return {
		tagSuggestions: availableTags.map( ( tag ) => tag.name ),
		selectedTagNames,
		hasResolved,
		convertNamesToSlugs,
		allTags: availableTags,
	};
}
