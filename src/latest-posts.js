import { registerBlockType } from '@wordpress/blocks';
import { withSelect } from '@wordpress/data';
import { useBlockProps } from '@wordpress/block-editor';
 
registerBlockType( 'sunflower/latest-posts', {
    apiVersion: 2,
    title: 'Neueste Beiträge',
    icon: 'admin-post',
    category: 'sunflower-blocks',
 
    edit: withSelect( ( select ) => {
        return {
            posts: select( 'core' ).getEntityRecords( 'postType', 'post' ),
        };
    } )( ( { posts } ) => {
        const blockProps = useBlockProps();
 
        return (
            <div { ...blockProps }>
                { ! posts && 'Loading' }
                { posts && posts.length === 0 && 'No Posts' }
                { posts && posts.length > 0 && (
                    <span> Zeige die neuesten Beiträge an, derzeit  
                        <ol>
                            <li>{ posts[ 0 ].title.rendered }</li>
                            <li>{ posts[ 1 ].title.rendered }</li>
                            <li>{ posts[ 2 ].title.rendered }</li>
                            <li>...</li>
                        </ol>
                    </span>
                ) } 
            </div>
        )
 
    } ),
} );
