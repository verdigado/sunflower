import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import {
	CheckboxControl,
	RadioControl,
	TextControl,
	ToggleControl,
	SelectControl
} from '@wordpress/components';
 
registerBlockType( 'sunflower/latest-posts', {
    apiVersion: 2,
    title: 'Neueste Beiträge (Sunflower)',
    icon: 'admin-post',
    category: 'sunflower-blocks',
    attributes: {
        categories: {
			type: 'string',
			default: ''
		},
        count: {
			type: 'string',
			default: ''
		},
        title: {
			type: 'string',
			default: ''
		},
    },
 
    edit: (props)  => {
        const blockProps = useBlockProps();

        const {
            attributes: {
                categories,
                count,
                title
            },
        } = props;

        const get_wp_categories = ( input ) => {
            let categories = new Array();
            wp.data.select('core').getEntityRecords('taxonomy', 'category').forEach(element => {
                categories.push( element.slug );
            });

            return categories;
        };
    
        const onChangeCategories = ( input ) => {
            props.setAttributes( { categories: input === undefined ? '' : input } );
        };

        const onChangeCount = ( input ) => {
            props.setAttributes( { count: input === undefined ? '' : input } );
        };

        const onChangeTitle = ( input ) => {
            props.setAttributes( { title: input === undefined ? '' : input } );
        };


        const { InspectorControls } = wp.blockEditor;
        const { PanelBody } = wp.components;


        return (
            <div { ...blockProps }>
                { 
                    <span> 
                        Zeige die neuesten Beiträge an. Titel, Kategorien und Anzahl kannst Du rechts einstellen. 
                        Hier klicken für eine Liste aller Kategorien. 
                        <ul>
                            {get_wp_categories().map((category, i) => {     
                                return(<li>{category}</li>);
                            })}
                        </ul>
                    </span>
                }
                {
                    <InspectorControls>

                    <PanelBody title={ 'Einstellungen' }>

                        <TextControl
                            label="Überschrift"
                            help="Titel der Sektion"
                            value={ title }
                            onChange={ onChangeTitle }
                        />

                        <TextControl
                            label="Kategorien"
                            help="Kategorie-Slug(URL) eintragen. Mehrere mit Komma trennen. Leer lassen für alle."
                            value={ categories }
                            onChange={ onChangeCategories }
                        />

                        <TextControl
                            label="Anzahl"
                            help="Wieviele Beiträge sollen angezeigt werden"
                            value={ count }
                            onChange={ onChangeCount }
                        />
                    </PanelBody>
                 
                 </InspectorControls>
                }
            </div>
        )
 
    },
} )
