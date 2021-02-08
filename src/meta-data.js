import { registerBlockType } from '@wordpress/blocks';
import {
    useBlockProps,
    RichText,
    AlignmentToolbar,
    BlockControls,
} from '@wordpress/block-editor';
import {
	CheckboxControl,
	RadioControl,
	TextControl,
	ToggleControl,
	SelectControl
} from '@wordpress/components';


 
registerBlockType( 'sunflower/meta-data', {
    apiVersion: 2,
    title: 'Metadaten',
    icon: 'info-outline',
    category: 'sunflower-blocks',
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
        icon: {
			type: 'string',
			default: 'fas fa-clock'
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
                icon,
                iconSelect
            },
        } = props;
 
        const blockProps = useBlockProps();
 
        const onChangeContent = ( newContent ) => {
            props.setAttributes( { content: newContent } );
        };
 
        const onChangeAlignment = ( newAlignment ) => {
            props.setAttributes( { alignment: newAlignment === undefined ? 'none' : newAlignment } );
        };

        const onChangeIcon = ( newIcon ) => {
            props.setAttributes( { icon: newIcon === undefined ? 'none' : newIcon } );
        };

        const onChangeIconSelect = ( newIcon ) => {
            props.setAttributes( { icon: newIcon === undefined ? 'none' : newIcon } );
        };

        const { InspectorControls } = wp.blockEditor;
        const { PanelBody } = wp.components;
 
        
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
                {
                    <InspectorControls>

                    <PanelBody title={ 'Einstellungen' }>
                        <SelectControl
                            label="Iconauswahl"
                            value={ iconSelect }
                            options={
                                [
                                    { value: 'none', label: 'Bitte wählen' },
                                    { value: 'fas fa-clock', label: 'Uhr' },
                                    { value: 'fas fa-tasks', label: 'Fortschritt' },
                                    { value: 'far fa-calendar-alt', label: 'Kalender' },
                                    { value: 'far fa-thumbs-up', label: 'Daumen hoch' },
                                    { value: 'far fa-thumbs-down', label: 'Daumen runter' },
                                ]
                            }
                            onChange={ onChangeIconSelect }
                        />

                        <TextControl
                            label="gesetztes Icon"
                            help="Alle Icons unter https://fontawesome.com/icons?d=gallery&m=free"
                            value={ icon }
                            onChange={ onChangeIcon }
                        />
                    </PanelBody>
                 
                 </InspectorControls>
                }
        
                <i class={ props.attributes.icon }></i>
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
                <i class={ props.attributes.icon }></i>
                <RichText.Content
                    className={ `sunflower-meta-data-${ props.attributes.alignment }` }
                    tagName="p"
                    value={ props.attributes.content }
                />
            </div>
        );
    },
} )