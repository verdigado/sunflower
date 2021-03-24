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
    title: 'Metadaten-Zeile',
    icon: 'info-outline',
    category: 'sunflower-blocks',
    attributes: {
        alignment: {
            type: 'string',
            default: 'none',
        },
        icon: {
			type: 'string',
			default: 'fas fa-clock'
		},
        url: {
			type: 'string',
			default: '#'
		},
    },
    edit: ( props ) => {
        const {
            attributes: {
                alignment,
                icon,
                iconSelect,
                url
            },
        } = props;
 
        const blockProps = useBlockProps();
 
        const onChangeAlignment = ( newAlignment ) => {
            props.setAttributes( { alignment: newAlignment === undefined ? 'none' : newAlignment } );
        };

        const onChangeIcon = ( newIcon ) => {
            props.setAttributes( { icon: newIcon === undefined ? 'none' : newIcon } );
        };

        const onChangeIconSelect = ( newIcon ) => {
            props.setAttributes( { icon: newIcon === undefined ? 'none' : newIcon } );
        };

        const onChangeUrl = ( newUrl ) => {
            props.setAttributes( { url: newUrl === undefined ? '#' : newUrl } );
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
                                    { value: 'fab fa-twitter', label: 'Twitter' },
                                    { value: 'fab fa-instagram', label: 'Instragram' },
                                    { value: 'fab fa-facebook-f', label: 'Facebook' },
                                    { value: 'fab fa-youtube', label: 'YouTube' },
                                    { value: 'fas fa-envelope', label: 'E-Mail' },
                                    { value: 'fas fa-globe', label: 'Website' },   
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

                        <TextControl
                            label="URL"
                            help="URL, wohin verlinkt wird"
                            value={ url }
                            onChange={ onChangeUrl }
                        />
                    </PanelBody>
                 
                 </InspectorControls>
                }
        
                <i class={ props.attributes.icon }></i>
            </div>
        );
    },
    save: ( props ) => {
        const blockProps = useBlockProps.save();
 
        return (
            <div {...blockProps}>
                <a href={ props.attributes.url } target="_blank" rel="noopener">
                    <i class={ props.attributes.icon }></i>
                </a>
            </div>
        );
    },
} )