/**
 * WordPress dependencies
 */
import { useBlockProps, RichText } from '@wordpress/block-editor';

/**
 * Internal dependencies
 */
import metadata from './block.json';

const { attributes } = metadata;

/**
 * Before `headingLevel` existed the heading was hardcoded to `h4`.
 *
 * Existing blocks carry no `headingLevel` in their saved block comment, so with
 * the new default of 3 the current `save` would render `h3` and no longer match
 * the `h4` stored in `post_content` – every existing accordion would show up as
 * invalid. This deprecation reproduces the old markup so those blocks keep
 * validating, and `migrate` lifts them to 3 – the level core uses, and the
 * correct one in the common case of an accordion inside an H2 section.
 * Deliberate trade-off: the stored `h4` is only rewritten once an editor saves
 * the page again, so editor and front end differ until then. Visually nothing
 * changes, because `.accordion-button` carries the typography, not the
 * heading tag.
 *
 * Do not remove while unmigrated content may still exist: a migrated block is
 * only written back to `post_content` once its page is saved again.
 */
const v1 = {
	attributes: {
		...attributes,
		headingLevel: {
			type: 'number',
			default: 4,
		},
	},
	migrate: ( oldAttributes ) => {
		return {
			...oldAttributes,
			headingLevel: 3,
		};
	},
	save: ( { attributes: blockAttributes } ) => {
		const blockProps = useBlockProps.save();
		return (
			<div { ...blockProps }>
				<div className="accordion-item">
					<h4 className="accordion-header">
						<button
							className="accordion-button collapsed"
							type="button"
							data-bs-toggle="collapse"
							data-bs-target={
								'#sacc-' + blockAttributes.blockId
							}
							aria-expanded="false"
							aria-controls={ 'sacc-' + blockAttributes.blockId }
							id={ 'saccid-' + blockAttributes.blockId }
						>
							{ blockAttributes.headline }
						</button>
					</h4>

					<div
						id={ 'sacc-' + blockAttributes.blockId }
						role="region"
						className={ 'accordion-collapse collapse' }
						aria-labelledby={ 'saccid-' + blockAttributes.blockId }
					>
						<div className="accordion-body">
							<RichText.Content
								className={ `sunflower-accordion` }
								tagName="p"
								value={ blockAttributes.content }
							/>
						</div>
					</div>
				</div>
			</div>
		);
	},
};

/**
 * New deprecations need to be placed first
 * for them to have higher priority.
 *
 * See block-deprecation.md
 */
export default [ v1 ];
