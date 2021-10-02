import { registerBlockType } from '@wordpress/blocks';
import { withSelect } from '@wordpress/data';
import { useBlockProps } from '@wordpress/block-editor';
 
registerBlockType( 'sunflower/next-events', {
    apiVersion: 2,
    title: 'Nächste Termine',
    icon: 'calendar-alt',
    category: 'sunflower-blocks',
 
    edit: withSelect( ( select ) => {
        return {
            posts: select( 'core' ).getEntityRecords( 'postType', 'sunflower_event' ),
        };
    } )( ( { posts } ) => {
        const blockProps = useBlockProps();
         
        return (
            <div { ...blockProps }>
                { ! posts && 'Loading' }
                { posts && posts.length === 0 && 'No Posts' }
                { posts && posts.length > 0 && (
                    <span> Zeige die nächsten drei Termine an, derzeit
                        <ol>
                            { posts.map( ( post, i ) => <li key={ i }>{ post.title.rendered }</li> ) }
                        </ol>
                    </span>
                ) } 
            </div>
        )
 
    } ),
} );
