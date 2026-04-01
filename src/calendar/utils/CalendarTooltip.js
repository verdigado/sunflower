/**
 * CalendarTooltip
 *
 * Singleton tooltip manager for calendar events.
 * Uses a single shared DOM element for better performance.
 * Tooltip content is built from DOM nodes (not innerHTML) to prevent XSS.
 */

import { getPreferredLocale } from './locale';

const TOOLTIP_CONFIG = {
	CLASS_NAME: 'sunflower-calendar-tooltip',
	BELOW_CLASS: 'is-below',
	CSS_VAR_ARROW: '--tooltip-arrow-color',
	DEFAULT_BG_COLOR: 'rgba(0, 0, 0, 0.9)',
	DEFAULT_TEXT_COLOR: '#ffffff',
	OFFSET: 10,
	EDGE_PADDING: 10,
	TIME_FORMAT: {
		hour: '2-digit',
		minute: '2-digit',
	},
};

class CalendarTooltip {
	constructor() {
		this.element = null;
	}

	init() {
		if ( this.element ) {
			return;
		}

		this.element = document.createElement( 'div' );
		this.element.className = TOOLTIP_CONFIG.CLASS_NAME;
		this.element.style.display = 'none';
		document.body.appendChild( this.element );
	}

	formatTime( date ) {
		return date.toLocaleTimeString(
			getPreferredLocale(),
			TOOLTIP_CONFIG.TIME_FORMAT
		);
	}

	buildTimeRange( startDate, endDate ) {
		const startTime = this.formatTime( startDate );
		const endTime = endDate ? this.formatTime( endDate ) : '';

		return endTime ? `${ startTime } – ${ endTime }` : startTime;
	}

	/**
	 * Build tooltip content using DOM nodes (avoids innerHTML / XSS risk).
	 *
	 * @param {Object} event FullCalendar event object
	 * @return {DocumentFragment} Tooltip content
	 */
	buildContent( event ) {
		const fragment = document.createDocumentFragment();

		const titleEl = document.createElement( 'div' );
		titleEl.className = 'tooltip-title';
		titleEl.textContent = event.title;
		fragment.appendChild( titleEl );

		if ( ! event.allDay && event.start ) {
			const timeEl = document.createElement( 'div' );
			timeEl.className = 'tooltip-time';
			timeEl.textContent = this.buildTimeRange( event.start, event.end );
			fragment.appendChild( timeEl );
		}

		return fragment;
	}

	applyColors( event ) {
		const bgColor =
			event.backgroundColor || TOOLTIP_CONFIG.DEFAULT_BG_COLOR;
		const textColor = event.textColor || TOOLTIP_CONFIG.DEFAULT_TEXT_COLOR;

		this.element.style.backgroundColor = bgColor;
		this.element.style.color = textColor;
		this.element.style.setProperty( TOOLTIP_CONFIG.CSS_VAR_ARROW, bgColor );
	}

	positionTooltip( el ) {
		const rect = el.getBoundingClientRect();
		const tooltipRect = this.element.getBoundingClientRect();
		const vw = window.innerWidth;
		const vh = window.innerHeight;
		const clamp = ( value, min, max ) =>
			Math.min( Math.max( value, min ), max );

		let left = rect.left + rect.width / 2;
		const halfWidth = tooltipRect.width / 2;
		const spaceAbove = rect.top - TOOLTIP_CONFIG.EDGE_PADDING;
		const spaceBelow = vh - rect.bottom - TOOLTIP_CONFIG.EDGE_PADDING;
		const placeBelow =
			tooltipRect.height + TOOLTIP_CONFIG.OFFSET > spaceAbove &&
			spaceBelow > spaceAbove;
		let top = placeBelow
			? rect.bottom + TOOLTIP_CONFIG.OFFSET
			: rect.top - TOOLTIP_CONFIG.OFFSET;

		this.element.classList.toggle( TOOLTIP_CONFIG.BELOW_CLASS, placeBelow );

		const minLeft = TOOLTIP_CONFIG.EDGE_PADDING;
		const maxLeft = Math.max(
			minLeft,
			vw - TOOLTIP_CONFIG.EDGE_PADDING - tooltipRect.width
		);
		const actualLeft = clamp( left - halfWidth, minLeft, maxLeft );
		left = actualLeft + halfWidth;

		const minTop = TOOLTIP_CONFIG.EDGE_PADDING;
		const maxTop = Math.max(
			minTop,
			vh - TOOLTIP_CONFIG.EDGE_PADDING - tooltipRect.height
		);
		const actualTop = clamp(
			placeBelow ? top : top - tooltipRect.height,
			minTop,
			maxTop
		);
		top = placeBelow ? actualTop : actualTop + tooltipRect.height;

		this.element.style.left = left + 'px';
		this.element.style.top = top + 'px';
	}

	show( event, el ) {
		if ( ! this.element ) {
			this.init();
		}

		this.element.textContent = '';
		this.element.appendChild( this.buildContent( event ) );

		this.applyColors( event );
		this.element.style.display = 'block';
		this.element.style.visibility = 'hidden';
		this.positionTooltip( el );
		this.element.style.visibility = 'visible';
	}

	hide() {
		if ( this.element ) {
			this.element.style.display = 'none';
			this.element.style.visibility = 'hidden';
			this.element.classList.remove( TOOLTIP_CONFIG.BELOW_CLASS );
		}
	}

	destroy() {
		if ( this.element ) {
			this.element.remove();
			this.element = null;
		}
	}
}

export default new CalendarTooltip();
