import { useBlockProps } from '@wordpress/block-editor';

import metadata from './block.json';

const { attributes } = metadata;

// change type of count attribute from string -> number
const v1 = {
	attributes,
	save( props ) {
		const blockProps = useBlockProps.save();
		return (
			<div { ...blockProps }>
				{ /* eslint-disable-next-line react/jsx-no-target-blank */ }
				<a href={ props.attributes.url } target="_blank" rel="noopener">
					<i className={ props.attributes.icon }></i>
				</a>
			</div>
		);
	},
};

/**
 * New deprecations need to be placed first
 * for them to have higher priority.
 *
 * Old deprecations may need to be updated as well.
 *
 * See block-deprecation.md
 */
export default [ v1 ];
