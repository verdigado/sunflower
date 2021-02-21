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
            },
        } = props;
 
        const blockProps = useBlockProps();
 
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
        const random = Math.floor(1000000 * Math.random());
        return (
            <div {...blockProps}>
                <div class="accordion-item">

                    <h4 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target={'.sacc' + random} aria-expanded="false" aria-controls={'.sacc' + random}>
                        { props.attributes.headline }
                        </button>
                    </h4>

                    <div class={'accordion-collapse collapse sacc' + random} aria-labelledby={'.sacc' + random}>
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