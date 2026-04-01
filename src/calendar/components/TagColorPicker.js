import { __ } from '@wordpress/i18n';
import { BaseControl, ColorPalette } from '@wordpress/components';

import { TAG_COLOR_PALETTE, CALENDAR_COLORS } from '../constants';

const TAG_COLOR_PICKER_ID = 'sunflower-calendar-tag-colors';

function getUpdatedTagColors( currentTagColors, tagSlug, color ) {
	const nextTagColors = { ...currentTagColors };

	if ( color ) {
		nextTagColors[ tagSlug ] = color;
		return nextTagColors;
	}

	delete nextTagColors[ tagSlug ];

	return nextTagColors;
}

/**
 * @param {Object}   props
 * @param {Array}    props.allTags     All available event tags
 * @param {Object}   props.tagColors   Current tag color mappings (slug => color)
 * @param {Function} props.onChange    Callback when tag color changes
 * @param {boolean}  props.hasResolved Whether tags have loaded
 * @return {Element} TagColorPicker component
 */
function TagColorPicker( { allTags, tagColors, onChange, hasResolved } ) {
	const availableTags = Array.isArray( allTags ) ? allTags : [];
	const currentTagColors =
		tagColors && 'object' === typeof tagColors ? tagColors : {};

	if ( ! hasResolved ) {
		return (
			<BaseControl
				id={ `${ TAG_COLOR_PICKER_ID }-loading` }
				label={ __( 'Tag-Farben', 'sunflower-calendar-events' ) }
				help={ __( 'Lade Event-Tags…', 'sunflower-calendar-events' ) }
			>
				<p style={ { fontStyle: 'italic', color: '#666' } }>
					{ __( 'Laden…', 'sunflower-calendar-events' ) }
				</p>
			</BaseControl>
		);
	}

	if ( 0 === availableTags.length ) {
		return (
			<BaseControl
				id={ `${ TAG_COLOR_PICKER_ID }-empty` }
				label={ __( 'Tag-Farben', 'sunflower-calendar-events' ) }
				help={ __(
					'Lege zuerst Event-Tags an, um eigene Farben zuzuweisen.',
					'sunflower-calendar-events'
				) }
			/>
		);
	}

	return (
		<div className="sunflower-tag-color-picker">
			<BaseControl
				id={ TAG_COLOR_PICKER_ID }
				label={ __( 'Tag-Farben', 'sunflower-calendar-events' ) }
				help={ __(
					'Weise jedem Tag eine Farbe zu. Events mit diesem Tag werden in der gewählten Farbe angezeigt.',
					'sunflower-calendar-events'
				) }
			>
				<div className="tag-color-list">
					{ availableTags.map( ( tag ) => (
						<TagColorItem
							key={ tag.id }
							tag={ tag }
							color={ currentTagColors[ tag.slug ] }
							onColorChange={ ( color ) =>
								onChange(
									getUpdatedTagColors(
										currentTagColors,
										tag.slug,
										color
									)
								)
							}
						/>
					) ) }
				</div>
			</BaseControl>
		</div>
	);
}

/**
 * @param {Object}   props
 * @param {Object}   props.tag           Tag object
 * @param {string}   props.color         Current color for this tag
 * @param {Function} props.onColorChange Callback when color changes
 * @return {Element} TagColorItem component
 */
function TagColorItem( { tag, color, onColorChange } ) {
	const currentColor = color || CALENDAR_COLORS.PRIMARY;

	return (
		<div className="tag-color-item">
			<div className="tag-color-header">
				<div
					className="tag-color-indicator"
					style={ { backgroundColor: currentColor } }
				/>
				<strong>{ tag.name }</strong>
			</div>

			<ColorPalette
				colors={ TAG_COLOR_PALETTE }
				value={ color }
				onChange={ onColorChange }
				clearable
			/>
		</div>
	);
}

export default TagColorPicker;
