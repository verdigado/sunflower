/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @param {Object} props React
 * @return {Element} Element to render.
 */
export default function Save( props ) {
	const blockProps = useBlockProps.save();
	const { url, icon } = props.attributes;

	return (
        <div { ...blockProps }>
            { url ? (
                <a href={ url } target="_blank" rel="noopener noreferrer"
                   className="sunflower-meta-data__link">
                    <i className={ icon }></i>
                </a>
            ) : (
                <i className={`${icon} is-empty`}></i>
            ) }
        </div>
    );
}
