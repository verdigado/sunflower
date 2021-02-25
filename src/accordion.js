import { registerBlockType } from '@wordpress/blocks';
import {
    useBlockProps,
    RichText,
    BlockControls,
} from '@wordpress/block-editor';
import {
	CheckboxControl,
	RadioControl,
	TextControl,
	ToggleControl,
	SelectControl
} from '@wordpress/components';


 
registerBlockType( 'sunflower/accordion', {
    apiVersion: 2,
    title: 'Akkordion',
    icon: 'menu-alt3',
    category: 'sunflower-blocks',
    attributes: {
        content: {
            type: 'array',
            source: 'children',
            selector: 'p',
            default: 'Text, der versteckt wird',
        },
        headline: {
            type: 'string',
            default: 'Titel zum Klicken',
        },
        blockId: {
            type: 'string',
        }
    },
    example: {
        attributes: {
            content: 'Hello World',
        },
    },
    edit: ( props ) => {
        const {
            attributes: {
                content,
                headline,
                blockId
            },
            setAttributes,
            clientId,
        } = props;
 
        const blockProps = useBlockProps();

        props.setAttributes( { blockId: clientId } );

        const onChangeContent = ( newContent ) => {
            props.setAttributes( { content: newContent } );

        };

        const onChangeHeadline = ( newHeadline ) => {
            props.setAttributes( { headline: newHeadline } );
        };
 
        const { InspectorControls } = wp.blockEditor;
        const { PanelBody } = wp.components;
  
        return (
            <div {...blockProps}>
        
                <TextControl
                    tagName="p"
                    label="Klickbarer Text, der die Sektion öffnet"
                    onChange={ onChangeHeadline }
                    value={ headline }
                />
                <RichText
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
                <div class="accordion-item">

                    <h4 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target={'.sacc' + props.attributes.blockId} aria-expanded="false" aria-controls={'.sacc' + props.attributes.blockId}>
                        { props.attributes.headline }
                        </button>
                    </h4>

                    <div class={'accordion-collapse collapse sacc' + props.attributes.blockId} aria-labelledby={'.sacc' + props.attributes.blockId}>
                        <div class="accordion-body">
                            <RichText.Content
                                className={ `sunflower-accordion` }
                                tagName="p"
                                value={ props.attributes.content }
                            />
                        </div>
                    </div>
                </div>
            </div>
        );
    },
} )