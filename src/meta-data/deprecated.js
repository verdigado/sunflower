import { useBlockProps } from '@wordpress/block-editor';

import metadata from './block.json';

const { attributes } = metadata;

const v2 = {
	attributes,
	save( props ) {
		const blockProps = useBlockProps.save();
		const { url, icon } = props.attributes;
		return (
			<div { ...blockProps }>
				<a href={ url } target="_blank" rel="noopener noreferrer">
					<i className={ icon }></i>
				</a>
			</div>
		);
	},
};

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
export default [ v2, v1 ];
