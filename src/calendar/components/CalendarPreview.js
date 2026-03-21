/**
 * CalendarPreview Component
 *
 * Displays a static calendar preview in the block editor.
 * Shows current month with mock events for visualization.
 */

import { __ } from '@wordpress/i18n';

import { PREVIEW_CONFIG } from '../constants';
import {
	getLocalizedMonthName,
	getLocalizedTodayLabel,
	getLocalizedWeekdayLabels,
} from '../utils/locale';

/**
 * @param {Object} props
 * @param {Array}  props.selectedTagNames Selected event tag names
 * @return {Element} CalendarPreview component
 */
function CalendarPreview( { selectedTagNames } ) {
	const now = new Date();
	const currentMonth = getLocalizedMonthName( now );
	const currentYear = now.getFullYear();

	return (
		<div className="sunflower-calendar-container">
			<div className="sunflower-calendar-preview">
				<CalendarHeader month={ currentMonth } year={ currentYear } />
				<CalendarGrid />
				<PreviewNotice selectedTagNames={ selectedTagNames } />
			</div>
		</div>
	);
}

/**
 * @param {Object} props
 * @param {string} props.month Current month name
 * @param {number} props.year  Current year
 * @return {Element} CalendarHeader component
 */
function CalendarHeader( { month, year } ) {
	return (
		<div className="calendar-header">
			<div className="calendar-nav-left">
				<button className="calendar-btn" disabled>
					{ '\u2039' }
				</button>
				<button className="calendar-btn" disabled>
					{ '\u203A' }
				</button>
				<button className="calendar-btn" disabled>
					{ getLocalizedTodayLabel() }
				</button>
			</div>

			<h2 className="calendar-title">
				{ month } { year }
			</h2>
		</div>
	);
}

/**
 * @return {Element} CalendarGrid component
 */
function CalendarGrid() {
	const weekdayLabels = getLocalizedWeekdayLabels();

	return (
		<div className="calendar-grid">
			{ weekdayLabels.map( ( day ) => (
				<div key={ day } className="calendar-day-header">
					{ day }
				</div>
			) ) }

			{ Array.from( { length: PREVIEW_CONFIG.GRID_SIZE }, ( _, i ) => {
				const isToday = i === PREVIEW_CONFIG.TODAY_INDEX;
				const hasEvent = PREVIEW_CONFIG.MOCK_EVENT_DAYS.includes( i );
				const dayNumber = ( i % PREVIEW_CONFIG.DAY_CYCLE ) + 1;

				return (
					<CalendarDay
						key={ i }
						dayNumber={ dayNumber }
						isToday={ isToday }
						hasEvent={ hasEvent }
					/>
				);
			} ) }
		</div>
	);
}

/**
 * @param {Object}  props
 * @param {number}  props.dayNumber Day number to display
 * @param {boolean} props.isToday   Whether this is today's date
 * @param {boolean} props.hasEvent  Whether this day has an event
 * @return {Element} CalendarDay component
 */
function CalendarDay( { dayNumber, isToday, hasEvent } ) {
	const className = `calendar-day${ isToday ? ' is-today' : '' }`;

	return (
		<div className={ className }>
			<div className="calendar-day-number">{ dayNumber }</div>
			{ hasEvent && (
				<div className="calendar-event-mock">
					{ __( 'Termin', 'sunflower-calendar-events' ) }
				</div>
			) }
		</div>
	);
}

/**
 * @param {Object} props
 * @param {Array}  props.selectedTagNames Selected event tag names
 * @return {Element} PreviewNotice component
 */
function PreviewNotice( { selectedTagNames } ) {
	return (
		<div className="calendar-preview-notice">
			<p className="notice-main">
				{ __(
					'Editor-Vorschau. Tatsächliche Termine werden auf der Seite angezeigt.',
					'sunflower-calendar-events'
				) }
			</p>

			{ selectedTagNames && selectedTagNames.length > 0 && (
				<p className="notice-filter">
					{ __(
						'Gefiltert nach Tags:',
						'sunflower-calendar-events'
					) }{ ' ' }
					<strong>{ selectedTagNames.join( ', ' ) }</strong>
				</p>
			) }
		</div>
	);
}

export default CalendarPreview;
