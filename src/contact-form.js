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
                <div class="comment-respond mb-5">
               <form id="sunflower-contact-form" method="post" class="row">
                  
                <div class="col-12 col-md-6">
                    <p class="comment-form-comment">
                        <label for="message">Nachricht <span class="required">*</span></label> 
                        <textarea id="message" name="comment" cols="45" rows="8" maxlength="65525" required="required"></textarea>
                    </p>
                    <p>Erforderliche Felder sind mit * markiert. 
                        Mit der Nutzung dieses Formulars erklären Sie sich mit der Speicherung und Verarbeitung 
                        Ihrer Daten durch diese Website einverstanden. 
                        Weiteres entnehmen Sie bitte unserer <a href="#" id="privacy_policy_url">Datenschutzerklärung</a>.
                    </p>
                </div>
                <div class="col-12 col-md-6"><p class="comment-form-author">
                    <label for="name">Name <span class="required">*</span></label> 
                    <input id="name" name="name" type="text" value="" size="30" maxlength="245" required="required"/>

                    </p>
                    <p class="comment-form-email">
                        <label for="mail">E-Mail <span class="required">*</span></label> 
                        <input id="mail" name="mail" type="email" value="" size="30" maxlength="100" required="required"/>
                     </p>
   
                </div>
                    <p class="form-submit">
                        <input name="submit" type="submit" id="submit" class="submit" value="abschicken"/> 
                    </p>
               </form>
               </div>
            </div>

        );
    },
} );
