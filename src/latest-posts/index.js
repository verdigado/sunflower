import { __ } from '@wordpress/i18n';
import { registerBlockType, registerBlockVariation } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
// import { registerBlockExtension } from '@10up/block-components';

import {
	CheckboxControl,
	RadioControl,
	TextControl,
	ToggleControl,
	SelectControl
} from '@wordpress/components';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * All files containing `style` keyword are bundled together. The code used
 * gets applied both to the front of your site and to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './style.scss';

import Edit from './edit';
import metadata from './block.json';

registerBlockType( metadata.name,
    {
        attributes: metadata.attributes,
        edit: Edit,
    }
);
