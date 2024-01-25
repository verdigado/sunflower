import { registerBlockType } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { withSelect } from '@wordpress/data';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import {
    FormTokenField,
    PanelBody,
    TextControl
} from '@wordpress/components';

registerBlockType( 'sunflower/next-events', {
    apiVersion: 2,
    title: __('Next events', 'sunflower') ,
    description: __('show next events', 'sunflower') ,
    icon: 'calendar-alt',
    category: 'sunflower-blocks',
    attributes: {
        tag: {
            type: 'array',
            default: [],
        },
        count: {
            type: 'int',
            default: 3,
        }
    },

    edit: withSelect( ( select, { attributes } ) => {
        return {
            tags: select('core').getEntityRecords('taxonomy', 'sunflower_event_tag', { per_page: -1}),
            posts: select( 'core' ).getEntityRecords( 'postType', 'sunflower_event', { sunflower_event_tag: attributes.tag } ),
        };
    } )( ( { posts, tags, attributes, setAttributes } ) => {
        const blockProps = useBlockProps();

        tags = (tags || []);
        const tagFormSuggestions = tags.map(tag => tag.name);
        const tagFormValue = tags.filter(tag => attributes.tag.includes(tag.id)).map(tag => tag.name);
        const onTagChange = (formTags) => {
            setAttributes({
                tag: formTags.map(tagName => tags.filter(tag => tag.name === tagName).map(tag => tag.id)[0])
            });
        }

        const onCountChange = ( newCount ) => {
            setAttributes({
              count: newCount === undefined ? '3' : newCount
            });
        }

        return (
            <div { ...blockProps }>
                { ! posts && __('Loading', 'sunflower') }
                { posts && posts.length === 0 && __('No Events', 'sunflower') }
                { posts && posts.length > 0 && (
                    <span> { __('Next events', 'sunflower') }
                        <br/>
                            <small>{ __('Maximum events shown', 'sunflower') }: {attributes.count}</small>
                        <ol>
                            { posts.map( ( post, i ) => {
                                if( i >= attributes.count ){
                                    return;
                                }
                                return <li key={ i }>{ post.title.rendered }</li> })
                            }
                        </ol>
                    </span>
                ) }

                <InspectorControls>
                    <PanelBody title={ __('Filter') } initialOpen={ true }>
                        <FormTokenField
                            label={ __('Tags') }
                            value={ tagFormValue }
                            onChange={ onTagChange }
                            suggestions={ tagFormSuggestions }
                        />

                        <TextControl
                            label={ __('Count', 'sunflower') }
                            value={ attributes.count }
                            onChange={ onCountChange }
                        />
                    </PanelBody>
                </InspectorControls>
            </div>
        )

    } ),
} );
