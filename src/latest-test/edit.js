/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __, _x } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

import ServerSideRender from '@wordpress/server-side-render';

import {
	CheckboxControl,
	RadioControl,
	TextControl,
	ToggleControl,
	SelectControl
} from '@wordpress/components';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {Element} Element to render.
 */
export default function Edit(props) {

    const blockProps = useBlockProps();

    const {
        attributes: {
            categories,
            count,
            title,
            style
        },
    } = props;

    const get_wp_categories = ( input ) => {
        let categories = new Array();
        wp.data.select('core').getEntityRecords('taxonomy', 'category')?.forEach(element => {
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

    const onChangeStyle = ( style ) => {
        props.setAttributes( { style: input === undefined ? '' : style } );
    };

    const { InspectorControls } = wp.blockEditor;
    const { PanelBody } = wp.components;

    return (
        <div { ...blockProps }>
            {
                <>
                <ServerSideRender
                  block={ "sunflower/latest-test" }
                  attributes={ {
                    categories: categories,
                    count: count,
                    title: title,
                    style: style
                  } }
                />
                </>
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

                    <SelectControl
                        key="style"
                        label="Style"
                        help="Wie soll's aussehen?"
                        options={ [
                            {
                                label: _x( 'DESC', 'sunflower' ),
                                value: 'list',
                            },
                            {
                                label: __( 'ASC', 'sunflower' ),
                                value: 'kachel',
                            },
                        ] }
                        value={ style }
                        onChange={ onChangeStyle }
                    />
                </PanelBody>

             </InspectorControls>
            }
        </div>
    )
}
