import { registerBlockType } from '@wordpress/blocks';
import {
    useBlockProps,
    RichText,
    AlignmentToolbar,
    BlockControls,
} from '@wordpress/block-editor';

registerBlockType( 'sunflower/test-block', {
    title: 'Person',
    icon: 'businessperson',
    category: 'text',
    edit: () => <div>Hallo Welt! (edit)</div>,
    save: () => <div>Hallo Welt!</div>,
} );