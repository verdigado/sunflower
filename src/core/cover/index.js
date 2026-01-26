/**
 * WordPress dependencies
 */
import { registerBlockStyle } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';

import './styles.scss';

/**
 * Register new block style variation for the core/list block.
 *
 * name  → slug, becomes className `is-style-<slug>`
 * label → Label in the editor sidebar, tab "stiles"
 */

registerBlockStyle( 'core/cover', {
	name: 'sunflower-hero',
	label: __( 'Sunflower Hero', 'sunflower' ),
} );
