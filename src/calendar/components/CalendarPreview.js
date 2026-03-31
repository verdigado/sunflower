/**
 * CalendarPreview Component
 *
 * Displays a static calendar preview in the block editor using the same
 * wrapper structure and key FullCalendar classes as the frontend block.
 */

import { __ } from '@wordpress/i18n';

import {
	getLocalizedMonthName,
	getLocalizedTodayLabel,
	getLocalizedWeekdayLabels,
} from '../utils/locale';
import { buildPreviewMonthData } from '../utils/preview';

/**
 * @param {Object} props
 * @param {Array}  props.selectedTagNames Selected event tag names
 * @return {Element} CalendarPreview component
 */
function CalendarPreview( { selectedTagNames } ) {
	const now = new Date();
	const currentMonth = getLocalizedMonthName( now );
	const currentYear = now.getFullYear();
	const previewWeeks = buildPreviewMonthData( now );

	return (
		<div className="wp-block-group__inner-container">
			<div className="sunflower-calendar-container">
				<div className="sunflower-calendar">
					<CalendarHeader
						month={ currentMonth }
						year={ currentYear }
					/>
					<CalendarGrid weeks={ previewWeeks } />
					<PreviewNotice selectedTagNames={ selectedTagNames } />
				</div>
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
		<div className="fc-header-toolbar fc-toolbar">
			<div className="fc-toolbar-chunk calendar-nav-left">
				<div className="fc-button-group">
					<button
						type="button"
						className="fc-prev-button fc-button fc-button-primary"
						disabled
					>
						{ '\u2039' }
					</button>
					<button
						type="button"
						className="fc-next-button fc-button fc-button-primary"
						disabled
					>
						{ '\u203A' }
					</button>
				</div>
				<button
					type="button"
					className="fc-today-button fc-button fc-button-primary"
					disabled
				>
					{ getLocalizedTodayLabel() }
				</button>
			</div>

			<div className="fc-toolbar-chunk">
				<h2 className="fc-toolbar-title">
					{ month } { year }
				</h2>
			</div>

			<div className="fc-toolbar-chunk" aria-hidden="true" />
		</div>
	);
}

/**
 * @param {Object}               props
 * @param {Array<Array<Object>>} props.weeks Preview weeks to render
 * @return {Element} CalendarGrid component
 */
function CalendarGrid( { weeks } ) {
	const weekdayLabels = getLocalizedWeekdayLabels();

	return (
		<div className="calendar-preview-grid" role="grid" aria-readonly="true">
			{ weekdayLabels.map( ( day ) => (
				<div
					key={ day }
					className="fc-col-header-cell"
					role="columnheader"
				>
					<span className="fc-col-header-cell-cushion">{ day }</span>
				</div>
			) ) }

			{ weeks.map( ( week ) =>
				week.map( ( day ) => (
					<CalendarDay key={ day.key } { ...day } />
				) )
			) }
		</div>
	);
}

/**
 * @param {Object}  props
 * @param {number}  props.dayNumber      Day number to display
 * @param {boolean} props.isCurrentMonth Whether the day belongs to the current month
 * @param {boolean} props.isToday        Whether this is today's date
 * @param {boolean} props.hasEvent       Whether this day has a mock event
 * @return {Element} CalendarDay component
 */
function CalendarDay( { dayNumber, isCurrentMonth, isToday, hasEvent } ) {
	const className = [
		'calendar-preview-day',
		'fc-daygrid-day',
		isToday ? 'fc-day-today' : '',
		! isCurrentMonth ? 'is-outside-month' : '',
	]
		.filter( Boolean )
		.join( ' ' );

	return (
		<div className={ className } role="gridcell" aria-selected="false">
			<span className="fc-daygrid-day-number">{ dayNumber }</span>

			{ hasEvent && (
				<div className="calendar-preview-event fc-event fc-daygrid-event fc-daygrid-block-event">
					<div className="fc-event-main">
						{ __( 'Termin', 'sunflower-calendar-events' ) }
					</div>
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
					'Editor-Vorschau mit Beispielterminen. Die echten Termine werden auf der Seite geladen.',
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
