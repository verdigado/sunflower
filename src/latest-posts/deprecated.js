import metadata from './block.json';

const { attributes } = metadata;

// change type of count attribute from string -> number
const v1 = {
	attributes: {
		...attributes,
		count: {
			type: 'string',
		},
	},
	migrate: ( oldAttributes ) => {
		return {
			...oldAttributes,
			count: parseInt( oldAttributes.count, 10 ),
		};
	},
	isEligible: ( { count } ) => count && 'string' === typeof count,
	save: () => null,
};

// change typoe of categories attribute from string to array
const v2 = {
	attributes: {
		...attributes,
		categories: {
			type: 'string',
		},
	},
	migrate: ( oldAttributes ) => {
		return {
			...oldAttributes,
			categories: oldAttributes.categories
				.trim()
				.split( ',' )
				.map( ( item ) => item.trim() ),
		};
	},
	isEligible: ( { categories } ) =>
		categories && 'string' === typeof categories,
	save: () => null,
};

// blocks without blockLayout were created when default was "grid"; keep them on grid
const v3 = {
	attributes: {
		...attributes,
		blockLayout: {
			type: 'string',
		},
	},
	isEligible: ( { blockLayout } ) => blockLayout === undefined,
	migrate: ( oldAttributes ) => {
		return {
			...oldAttributes,
			blockLayout: 'grid',
		};
	},
	save: () => null,
};

/**
 * New deprecations need to be placed first
 * for them to have higher priority.
 *
 * Old deprecations may need to be updated as well.
 *
 * See block-deprecation.md
 */
export default [ v3, v2, v1 ];
