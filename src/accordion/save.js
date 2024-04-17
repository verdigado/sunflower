/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, RichText } from '@wordpress/block-editor';

/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#save
 *
 * @param {Object} props            React props.
 * @param {Object} props.attributes
 * @return {Element} Element to render.
 */
export default function Save( { attributes } ) {
	const blockProps = useBlockProps.save();
	return (
		<div { ...blockProps }>
			<div className="accordion-item">
				<h4 className="accordion-header">
					<button
						className="accordion-button collapsed"
						type="button"
						data-bs-toggle="collapse"
						data-bs-target={ '#sacc-' + attributes.blockId }
						aria-expanded="false"
						aria-controls={ 'sacc-' + attributes.blockId }
						id={ 'saccid-' + attributes.blockId }
					>
						{ attributes.headline }
					</button>
				</h4>

				<div
					id={ 'sacc-' + attributes.blockId }
					role="region"
					className={ 'accordion-collapse collapse' }
					aria-labelledby={ 'saccid-' + attributes.blockId }
				>
					<div className="accordion-body">
						<RichText.Content
							className={ `sunflower-accordion` }
							tagName="p"
							value={ attributes.content }
						/>
					</div>
				</div>
			</div>
		</div>
	);
}
