import { registerBlockType } from '@wordpress/blocks';
import { withSelect } from '@wordpress/data';
import { useBlockProps } from '@wordpress/block-editor';
 
registerBlockType( 'sunflower/contact-form', {
    apiVersion: 2,
    title: 'Kontaktformular',
    icon: 'feedback',
    category: 'sunflower-blocks',
 
    edit: ( props ) => {
        const { attributes: { content }, setAttributes, className } = props;
        const blockProps = useBlockProps();
        const onChangeContent = ( newContent ) => {
            setAttributes( { content: newContent } );
        };
        return (
            <div {...blockProps}>
                Kontaktformular. Es gibt keine Einstellungsmöglichkeiten. Das Formular wird an den*die
                Webseiten-Administrator*in gesendet.
            </div>
        );
    },
    save: ( props ) => {
        const blockProps = useBlockProps.save();
        return  (
            <div {...blockProps}>  
               <form class="sunflower-contact-form" method="post">
                   <input type="text" name="name"/>
                   <input type="submit" value="abschicken"/>
               </form>
            </div>
        );
    },
} );
