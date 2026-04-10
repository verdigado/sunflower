import { PREVIEW_CONFIG } from '../constants';
import { getLocaleFirstDay } from './locale';

function getDateKey( date ) {
	return `${ date.getFullYear() }-${ date.getMonth() }-${ date.getDate() }`;
}

/**
 * Build a static month grid for the editor preview.
 *
 * Mirrors the browser locale's first day of week so the preview layout stays
 * close to the real calendar, while still using deterministic mock events.
 *
 * @param {Date} referenceDate Date used for the preview month.
 * @return {Array<Array<Object>>} Calendar weeks with day metadata.
 */
export function buildPreviewMonthData( referenceDate = new Date() ) {
	const monthStart = new Date(
		referenceDate.getFullYear(),
		referenceDate.getMonth(),
		1
	);
	const today = new Date(
		referenceDate.getFullYear(),
		referenceDate.getMonth(),
		referenceDate.getDate()
	);
	const firstDayOfWeek = getLocaleFirstDay();
	const leadingDays = ( monthStart.getDay() - firstDayOfWeek + 7 ) % 7;
	const gridStart = new Date( monthStart );

	gridStart.setDate( monthStart.getDate() - leadingDays );

	const days = Array.from(
		{ length: PREVIEW_CONFIG.TOTAL_CELLS },
		( _, index ) => {
			const cellDate = new Date( gridStart );

			cellDate.setDate( gridStart.getDate() + index );

			const dayNumber = cellDate.getDate();
			const isCurrentMonth =
				cellDate.getMonth() === monthStart.getMonth();

			return {
				key: getDateKey( cellDate ),
				dayNumber,
				isCurrentMonth,
				isToday: getDateKey( cellDate ) === getDateKey( today ),
				hasEvent:
					isCurrentMonth &&
					PREVIEW_CONFIG.MOCK_EVENT_DAYS.includes( dayNumber ),
			};
		}
	);

	return Array.from( { length: days.length / 7 }, ( _, weekIndex ) =>
		days.slice( weekIndex * 7, weekIndex * 7 + 7 )
	);
}
