/* global FullCalendar */

/**
 * Sunflower Calendar Block — Frontend JavaScript
 *
 * Initialises FullCalendar instances and fetches events via AJAX.
 *
 * Colours are controlled via CSS custom properties in style.scss and are
 * scoped to each calendar instance instead of the document root.
 */

import CalendarTooltip from './utils/CalendarTooltip';
import {
	FULLCALENDAR_DEFAULTS,
	CALENDAR_COLORS,
	DATA_ATTRIBUTES,
	AJAX_CONFIG,
	CSS_CLASSES,
	ERROR_MESSAGES,
} from './constants';
import {
	getLocaleCandidates,
	getLocaleFirstDay,
	getPreferredLocale,
} from './utils/locale';

const localeLoaders = new Map();

function hasFullCalendarLocale( localeCode ) {
	return Boolean(
		Array.isArray( FullCalendar?.globalLocales ) &&
			FullCalendar.globalLocales.some(
				( locale ) => locale.code === localeCode
			)
	);
}

function loadFullCalendarLocale( localesUrl, localeCode ) {
	if (
		! localesUrl ||
		! localeCode ||
		'en' === localeCode ||
		hasFullCalendarLocale( localeCode )
	) {
		return Promise.resolve( hasFullCalendarLocale( localeCode ) );
	}

	if ( localeLoaders.has( localeCode ) ) {
		return localeLoaders.get( localeCode );
	}

	const localeLoader = new Promise( ( resolve ) => {
		const script = document.createElement( 'script' );
		script.src = `${ localesUrl }${ localeCode }.global.min.js`;
		script.onload = () => resolve( hasFullCalendarLocale( localeCode ) );
		script.onerror = () => resolve( false );
		document.head.appendChild( script );
	} );

	localeLoaders.set( localeCode, localeLoader );

	return localeLoader;
}

/**
 * Manages a single FullCalendar instance.
 */
class CalendarManager {
	/**
	 * @param {HTMLElement} element Calendar container element
	 */
	constructor( element ) {
		this.element = element;
		this.calendar = null;
		this.ajaxUrl = element.getAttribute( DATA_ATTRIBUTES.AJAX_URL );
		this.localesUrl = element.getAttribute( DATA_ATTRIBUTES.LOCALES_URL );
		this.nonce = element.getAttribute( DATA_ATTRIBUTES.NONCE );
		this.tags = element.getAttribute( DATA_ATTRIBUTES.TAGS );
		this.tagColors = this.parseTagColors(
			element.getAttribute( DATA_ATTRIBUTES.TAG_COLORS )
		);
	}

	parseTagColors( json ) {
		try {
			return json ? JSON.parse( json ) : {};
		} catch ( e ) {
			this.logError( 'Error parsing tag colors:', e );
			return {};
		}
	}

	logError( message, error ) {
		// eslint-disable-next-line no-console
		console.error( `[Calendar] ${ message }`, error );
	}

	showError( message ) {
		this.hideLoading();
		this.element.classList.add( CSS_CLASSES.ERROR );
		const errorEl = document.createElement( 'div' );
		errorEl.className = 'calendar-error-message';
		errorEl.textContent = message;
		this.element.replaceChildren( errorEl );
	}

	showLoading() {
		this.element.classList.add( CSS_CLASSES.LOADING );
	}

	hideLoading() {
		this.element.classList.remove( CSS_CLASSES.LOADING );
	}

	handleLoading( isLoading ) {
		if ( isLoading ) {
			this.showLoading();
			return;
		}

		this.hideLoading();
	}

	/**
	 * Read theme colours from CSS custom properties, falling back to JS constants.
	 *
	 * @return {Object} Calendar colour values for the current instance.
	 */
	getColors() {
		const elementStyle = window.getComputedStyle( this.element );
		const rootStyle = window.getComputedStyle( document.documentElement );
		const getColor = ( propertyName, fallback ) =>
			elementStyle.getPropertyValue( propertyName ).trim() ||
			rootStyle.getPropertyValue( propertyName ).trim() ||
			fallback;

		return {
			PRIMARY: getColor(
				'--calendar-color-primary',
				CALENDAR_COLORS.PRIMARY
			),
			TEXT: getColor( '--calendar-color-text', CALENDAR_COLORS.TEXT ),
		};
	}

	buildRequestParams( fetchInfo ) {
		const params = new URLSearchParams( {
			action: AJAX_CONFIG.ACTION,
			start: fetchInfo.startStr,
			end: fetchInfo.endStr,
			nonce: this.nonce,
		} );

		if ( this.tags ) {
			params.append( 'tags', this.tags );
		}

		return params;
	}

	getEventColor( extendedProps ) {
		if ( ! extendedProps?.tags || ! this.tagColors ) {
			return null;
		}

		for ( const tagSlug of extendedProps.tags ) {
			if ( this.tagColors[ tagSlug ] ) {
				return this.tagColors[ tagSlug ];
			}
		}

		return null;
	}

	applyEventColors( event, colors ) {
		const tagColor = this.getEventColor( event.extendedProps );
		const backgroundColor = tagColor || colors.PRIMARY;

		return {
			...event,
			backgroundColor,
			borderColor: backgroundColor,
			textColor: colors.TEXT,
		};
	}

	async fetchEvents( fetchInfo ) {
		const params = this.buildRequestParams( fetchInfo );

		try {
			const response = await fetch(
				`${ this.ajaxUrl }?${ params.toString() }`
			);

			if ( ! response.ok ) {
				throw new Error( ERROR_MESSAGES.FETCH_FAILED );
			}

			let events;
			try {
				events = await response.json();
			} catch ( jsonError ) {
				this.logError( ERROR_MESSAGES.INVALID_RESPONSE, jsonError );
				throw new Error( ERROR_MESSAGES.INVALID_RESPONSE );
			}

			if ( ! Array.isArray( events ) ) {
				this.logError( ERROR_MESSAGES.INVALID_RESPONSE, events );
				throw new Error( ERROR_MESSAGES.INVALID_RESPONSE );
			}

			const colors = this.getColors();

			return events.map( ( event ) =>
				this.applyEventColors( event, colors )
			);
		} catch ( error ) {
			if (
				error.message === ERROR_MESSAGES.FETCH_FAILED ||
				error.message === ERROR_MESSAGES.INVALID_RESPONSE
			) {
				throw error;
			}

			this.logError( ERROR_MESSAGES.NETWORK_ERROR, error );
			throw new Error( ERROR_MESSAGES.NETWORK_ERROR );
		}
	}

	async getCalendarLocale() {
		const preferredLocale = getPreferredLocale();

		for ( const localeCode of getLocaleCandidates( preferredLocale ) ) {
			if ( 'en' === localeCode ) {
				return undefined;
			}

			if ( await loadFullCalendarLocale( this.localesUrl, localeCode ) ) {
				return localeCode;
			}
		}

		return undefined;
	}

	getConfig( locale ) {
		const colors = this.getColors();

		const config = {
			initialView: FULLCALENDAR_DEFAULTS.INITIAL_VIEW,
			height: FULLCALENDAR_DEFAULTS.HEIGHT,
			firstDay: getLocaleFirstDay(),
			headerToolbar: {
				left: 'prev,next today',
				center: 'title',
				right: '',
			},
			buttonText: {
				prev: '\u2039',
				next: '\u203A',
			},
			displayEventTime: FULLCALENDAR_DEFAULTS.DISPLAY_EVENT_TIME,
			events: ( fetchInfo, successCallback, failureCallback ) => {
				this.fetchEvents( fetchInfo )
					.then( ( data ) => successCallback( data ) )
					.catch( ( error ) => failureCallback( error ) );
			},
			loading: this.handleLoading.bind( this ),
			eventColor: colors.PRIMARY,
			eventBorderColor: colors.PRIMARY,
			eventTextColor: colors.TEXT,
			eventDisplay: 'auto',
			eventMouseEnter: this.handleEventMouseEnter.bind( this ),
			eventMouseLeave: this.handleEventMouseLeave.bind( this ),
		};

		if ( locale ) {
			config.locale = locale;
		}

		return config;
	}

	handleEventMouseEnter( info ) {
		CalendarTooltip.show( info.event, info.el );
	}

	handleEventMouseLeave() {
		CalendarTooltip.hide();
	}

	isValid() {
		if ( ! this.ajaxUrl || ! this.nonce ) {
			this.logError(
				ERROR_MESSAGES.MISSING_CONFIG,
				new Error( 'Missing data attributes' )
			);
			this.showError( ERROR_MESSAGES.MISSING_CONFIG );
			return false;
		}

		if ( typeof FullCalendar === 'undefined' ) {
			this.logError(
				ERROR_MESSAGES.LIBRARY_NOT_LOADED,
				new Error( 'FullCalendar undefined' )
			);
			this.showError( ERROR_MESSAGES.LIBRARY_NOT_LOADED );
			return false;
		}

		return true;
	}

	async init() {
		if ( ! this.isValid() ) {
			return;
		}

		try {
			this.showLoading();
			const locale = await this.getCalendarLocale();
			this.calendar = new FullCalendar.Calendar(
				this.element,
				this.getConfig( locale )
			);
			this.calendar.render();
		} catch ( error ) {
			this.logError( 'Failed to initialise calendar', error );
			this.showError( ERROR_MESSAGES.LIBRARY_NOT_LOADED );
			this.hideLoading();
		}
	}

	destroy() {
		if ( this.calendar ) {
			this.calendar.destroy();
			this.calendar = null;
		}
	}
}

const calendarInstances = new Set();

function initializeAllCalendars() {
	document
		.querySelectorAll( `.${ CSS_CLASSES.CALENDAR }` )
		.forEach( ( element ) => {
			const manager = new CalendarManager( element );
			calendarInstances.add( manager );
			void manager.init();
		} );
}

function destroyAllCalendars() {
	calendarInstances.forEach( ( manager ) => manager.destroy() );
	calendarInstances.clear();
	CalendarTooltip.destroy();
}

document.addEventListener( 'DOMContentLoaded', initializeAllCalendars );
window.addEventListener( 'beforeunload', destroyAllCalendars );

export { CalendarManager, initializeAllCalendars, destroyAllCalendars };
