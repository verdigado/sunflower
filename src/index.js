import { registerBlockType } from '@wordpress/blocks';
import {
    useBlockProps,
    RichText,
    AlignmentToolbar,
    BlockControls,
} from '@wordpress/block-editor';

registerBlockType( 'sunflower/test-block', {
    title: 'Person',
    icon: 'businessperson',
    category: 'text',
    edit: () => <div>Hallo Welt! (edit)</div>,
    save: () => <div>Hallo Welt!</div>,
} );

registerBlockType( 'gutenberg-examples/example-04-controls-esnext', {
    apiVersion: 2,
    title: 'Example: Controls (esnext)',
    icon: 'universal-access-alt',
    category: 'text',
    attributes: {
        content: {
            type: 'array',
            source: 'children',
            selector: 'p',
        },
        alignment: {
            type: 'string',
            default: 'none',
        },
    },
    example: {
        attributes: {
            content: 'Hello World',
            alignment: 'right',
        },
    },
    edit: ( props ) => {
        const {
            attributes: {
                content,
                alignment,
            },
        } = props;
 
        const blockProps = useBlockProps();
 
        const onChangeContent = ( newContent ) => {
            props.setAttributes( { content: newContent } );
        };
 
        const onChangeAlignment = ( newAlignment ) => {
            props.setAttributes( { alignment: newAlignment === undefined ? 'none' : newAlignment } );
        };
 
        return (
            <div {...blockProps}>
                {
                    <BlockControls>
                        <AlignmentToolbar
                            value={ alignment }
                            onChange={ onChangeAlignment }
                        />
                    </BlockControls>
                }
                <RichText
                   
                    style={ { textAlign: alignment } }
                    tagName="p"
                    onChange={ onChangeContent }
                    value={ content }
                />
            </div>
        );
    },
    save: ( props ) => {
        const blockProps = useBlockProps.save();
 
        return (
            <div {...blockProps}>
                <RichText.Content
                   
                    tagName="p"
                    value={ props.attributes.content }
                />
            </div>
        );
    },
} );