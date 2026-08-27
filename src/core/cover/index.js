/**
 * WordPress dependencies
 */
import { registerBlockStyle } from '@wordpress/blocks';
import { __ } from '@wordpress/i18n';
import { addFilter } from '@wordpress/hooks';
import { createHigherOrderComponent } from '@wordpress/compose';
import { createElement, Fragment } from '@wordpress/element';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, ToggleControl } from '@wordpress/components';

import './styles.scss';

/**
 * Register new block style variation for the core/cover block.
 *
 * name  → slug, becomes className `is-style-<slug>`
 * label → Label in the editor sidebar, tab "stiles"
 */

registerBlockStyle( 'core/cover', {
	name: 'sunflower-hero',
	label: __( 'Sunflower Hero', 'sunflower' ),
} );

/**
 * Inspector-Toggle für das Sunflower Hero Cover:
 * "Volle Bildschirmhöhe" (Default) vs. "Bild nicht zuschneiden".
 *
 * Der Toggle schreibt die Klasse `sunflower-hero-no-crop` in die
 * className des Blocks, dadurch landet sie automatisch im Editor-
 * Wrapper und im gespeicherten Markup.
 */
const NO_CROP_CLASS = 'sunflower-hero-no-crop';
const HERO_STYLE_CLASS = 'is-style-sunflower-hero';

const hasClass = ( className, token ) =>
	( className || '' ).split( /\s+/ ).includes( token );

const withHeroNoCropToggle = createHigherOrderComponent( ( BlockEdit ) => {
	return function HeroNoCropToggle( props ) {
		if ( props.name !== 'core/cover' ) {
			return createElement( BlockEdit, props );
		}

		const className = props.attributes.className || '';

		if ( ! hasClass( className, HERO_STYLE_CLASS ) ) {
			return createElement( BlockEdit, props );
		}

		const noCrop = hasClass( className, NO_CROP_CLASS );

		const setNoCrop = ( value ) => {
			const classes = className
				.split( /\s+/ )
				.filter( ( c ) => c && c !== NO_CROP_CLASS );
			if ( value ) {
				classes.push( NO_CROP_CLASS );
			}
			props.setAttributes( { className: classes.join( ' ' ) } );
		};

		return createElement(
			Fragment,
			null,
			createElement( BlockEdit, props ),
			createElement(
				InspectorControls,
				// In den "Stile"-Tab, direkt unter die Stil-Auswahl.
				{ group: 'styles' },
				createElement(
					PanelBody,
					{
						title: __( 'Sunflower Hero', 'sunflower' ),
						initialOpen: true,
						className: 'sunflower-hero-style-panel',
					},
					createElement( ToggleControl, {
						__nextHasNoMarginBottom: true,
						label: __( 'Bild nicht zuschneiden', 'sunflower' ),
						help: noCrop
							? __(
									'Das Bild wird in voller Breite ohne Zuschnitt angezeigt. Die Höhe ergibt sich aus dem Seitenverhältnis des Bildes.',
									'sunflower'
							  )
							: __(
									'Standard: Volle Bildschirmhöhe. Das Bild füllt den Viewport und wird an den Rändern zugeschnitten.',
									'sunflower'
							  ),
						checked: noCrop,
						onChange: setNoCrop,
					} )
				)
			)
		);
	};
}, 'withHeroNoCropToggle' );

addFilter(
	'editor.BlockEdit',
	'sunflower/hero-no-crop-toggle',
	withHeroNoCropToggle
);
