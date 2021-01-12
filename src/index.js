import { registerBlockType } from '@wordpress/blocks';
 
registerBlockType( 'sunflower/test-block', {
    title: 'Person',
    icon: 'businessperson',
    category: 'text',
    edit: () => <div>Hallo Welt! (edit)</div>,
    save: () => <div>Hallo Welt!</div>,
} );
